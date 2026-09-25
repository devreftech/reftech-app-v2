<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\ToolAssignmentTechnician;
use App\Models\ToolAudit;
use App\Models\ToolAuditPeriod;
use App\Models\ToolMaster;
use App\Models\User;
use App\Services\ToolAssetPath;
use App\Services\ToolAuditPeriodGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Str;

class ToolAssignmentController extends Controller
{
    protected function guardAdmin()
    {
        if (!in_array(Auth::user()->role, ['Admin', 'Developer'])) {
            abort(403, 'Hanya Admin yang bisa mengakses Management Tools.');
        }
    }

    public function index(Request $request)
    {
        $this->guardAdmin();

        // 1. Data teknisi
        $technicians = User::whereHas('toolAssignmentEntry')
            ->withCount(['toolsAssigned' => function ($q) {
                $q->where('status_tools', 'Aktif');
            }])
            ->with(['toolsAssigned' => function ($q) {
                $q->where('status_tools', 'Aktif')->with('toolsMaster');
            }])
            ->orderBy('name')
            ->get();

        $availableUsers = User::whereDoesntHave('toolAssignmentEntry')->orderBy('name')->get();

        // 2. Data Periode Audit
        $periods = ToolAuditPeriod::with(['audits.technician'])
            ->orderByDesc('tahun')
            ->orderByDesc('semester')
            ->get();

        // 3. Periode aktif saat ini
        $today = Carbon::today()->toDateString();
        $currentActivePeriod = $periods->first(function ($p) use ($today) {
            return $p->status == 'Open' && $p->tanggal_mulai <= $today && $p->tanggal_selesai >= $today;
        }) ?? $periods->where('status', 'Open')->first() ?? $periods->first();

        // 4. Statistik Ringkasan
        $totalActiveTools = FixedAsset::where('type', 'Tools')
            ->where('status_tools', 'Aktif')
            ->count();

        $totalTechHoldingTools = $technicians->filter(fn ($t) => $t->tools_assigned_count > 0)->count();

        $latestAudits = $currentActivePeriod ? $currentActivePeriod->audits : collect();
        $submittedCount = $latestAudits->whereIn('status_submit', ['Submitted', 'Verified'])->count();
        $verifiedCount = $latestAudits->where('status_submit', 'Verified')->count();
        $draftCount = $latestAudits->where('status_submit', 'Draft')->count();

        $stats = [
            'total_technicians' => $technicians->count(),
            'total_holding_tools' => $totalTechHoldingTools,
            'total_active_tools' => $totalActiveTools,
            'current_active_period' => $currentActivePeriod,
            'submitted_count' => $submittedCount,
            'verified_count' => $verifiedCount,
            'draft_count' => $draftCount,
            'total_audits_count' => $latestAudits->count(),
            'open_periods_count' => $periods->where('status', 'Open')->count(),
        ];

        $activeTab = $request->get('tab', 'technicians');

        return view('pages.technician.tool-assignment.index', compact(
            'technicians',
            'availableUsers',
            'periods',
            'currentActivePeriod',
            'stats',
            'activeTab'
        ));
    }

    /**
     * Tambah / Simpan Periode Audit Baru (Mendukung Single Quarter maupun Generate 1 Tahun Penuh Sekaligus)
     */
    public function storePeriod(Request $request, ToolAuditPeriodGenerator $generator)
    {
        $this->guardAdmin();

        $mode = $request->input('mode', 'single'); // 'single' atau 'full_year'

        // JIKA MODE 1 TAHUN PENUH (SEMUA 4 KUARTAL SEKALIGUS)
        if ($mode == 'full_year' || $request->has('generate_all_quarters')) {
            $request->validate([
                'tahun' => 'required|integer|min:2020|max:2099',
                'quarters' => 'required|array',
                'quarters.*.tanggal_mulai' => 'required|date',
                'quarters.*.tanggal_selesai' => 'required|date|after_or_equal:quarters.*.tanggal_mulai',
                'quarters.*.status' => 'required|in:Open,Closed',
            ], [
                'quarters.*.tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            ]);

            $tahun = (int) $request->tahun;
            $createdCount = 0;
            $today = Carbon::today()->toDateString();
            $syncedTechs = 0;
            $syncedTools = 0;

            foreach ($request->quarters as $semester => $qData) {
                $period = ToolAuditPeriod::firstOrCreate(
                    ['tahun' => $tahun, 'semester' => (int) $semester],
                    [
                        'tanggal_mulai' => $qData['tanggal_mulai'],
                        'tanggal_selesai' => $qData['tanggal_selesai'],
                        'status' => $qData['status'],
                    ]
                );

                $period->tanggal_mulai = $qData['tanggal_mulai'];
                $period->tanggal_selesai = $qData['tanggal_selesai'];
                $period->status = $qData['status'];
                $period->save();

                $createdCount++;

                // Jika statusnya Open, langsung sinkronkan teknisi
                if ($period->status == 'Open') {
                    $res = $generator->generateForPeriod($period);
                    $syncedTechs = max($syncedTechs, $res['technicians']);
                    $syncedTools = max($syncedTools, $res['tools']);
                }
            }

            $msg = "Berhasil menjadwalkan 4 Kuartal (Q1, Q2, Q3, Q4) untuk Tahun {$tahun} sekaligus!";
            if ($syncedTechs > 0) {
                $msg .= " Sinkronisasi teknisi berhasil dijalankan ({$syncedTechs} teknisi, {$syncedTools} item tools).";
            }

            return redirect()->route('tool-assignment.index', ['tab' => 'periods'])->with('success', $msg);
        }

        // JIKA MODE KUARTAL TUNGGAL (SINGLE QUARTER)
        $request->validate([
            'tahun' => 'required|integer|min:2020|max:2099',
            'semester' => 'required|integer|between:1,4',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:Open,Closed',
            'trigger_now' => 'nullable|in:1,0',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $period = ToolAuditPeriod::firstOrCreate(
            ['tahun' => $request->tahun, 'semester' => $request->semester],
            [
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status' => $request->status,
            ]
        );

        $period->tanggal_mulai = $request->tanggal_mulai;
        $period->tanggal_selesai = $request->tanggal_selesai;
        $period->status = $request->status;
        $period->save();

        $msg = "Periode audit {$period->period_title} ({$period->semester_label}) berhasil disimpan.";

        if ($request->trigger_now == '1' || $request->status == 'Open') {
            $res = $generator->generateForPeriod($period);
            $msg = "Periode audit {$period->period_title} berhasil diaktifkan! Draft audit telah dibuat/disinkronkan untuk {$res['technicians']} teknisi ({$res['tools']} item tools).";
        }

        return redirect()->route('tool-assignment.index', ['tab' => 'periods'])->with('success', $msg);
    }

    /**
     * Update Tanggal / Status Periode Audit
     */
    public function updatePeriod(Request $request, $id, ToolAuditPeriodGenerator $generator)
    {
        $this->guardAdmin();

        $period = ToolAuditPeriod::findOrFail($id);

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:Open,Closed',
            'sync_technicians' => 'nullable|in:1,0',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $period->tanggal_mulai = $request->tanggal_mulai;
        $period->tanggal_selesai = $request->tanggal_selesai;
        $period->status = $request->status;
        $period->save();

        $msg = "Jadwal periode audit {$period->period_title} berhasil diperbarui.";

        if ($request->sync_technicians == '1' || $request->status == 'Open') {
            $res = $generator->generateForPeriod($period);
            $msg .= " Sinkronisasi tools teknisi selesai ({$res['technicians']} teknisi, {$res['tools']} tools).";
        }

        return redirect()->route('tool-assignment.index', ['tab' => 'periods'])->with('success', $msg);
    }

    /**
     * Trigger / Aktifkan Waktunya Audit Sekarang
     */
    public function triggerPeriod(Request $request, $id, ToolAuditPeriodGenerator $generator)
    {
        $this->guardAdmin();

        $period = ToolAuditPeriod::findOrFail($id);
        $period->status = 'Open';

        // Jika tanggal selesai sudah lewat dari hari ini, perpanjang otomatis 10 hari
        $today = Carbon::today()->toDateString();
        if ($period->tanggal_selesai < $today) {
            $period->tanggal_mulai = $today;
            $period->tanggal_selesai = Carbon::today()->addDays(9)->toDateString();
        }
        $period->save();

        $result = $generator->generateForPeriod($period);

        return redirect()->route('tool-assignment.index', ['tab' => 'periods'])->with(
            'success',
            "⚡ Waktu audit periode {$period->period_title} berhasil diaktifkan sekarang! Form self-audit telah dibuat & disinkronkan untuk {$result['technicians']} teknisi ({$result['tools']} item tools)."
        );
    }

    /**
     * Toggle Status Buka / Tutup Periode Audit
     */
    public function togglePeriodStatus($id)
    {
        $this->guardAdmin();

        $period = ToolAuditPeriod::findOrFail($id);
        $period->status = $period->status == 'Open' ? 'Closed' : 'Open';
        $period->save();

        return redirect()->route('tool-assignment.index', ['tab' => 'periods'])->with(
            'success',
            "Status periode audit {$period->period_title} sekarang: {$period->status}."
        );
    }

    /**
     * Hapus Periode Audit
     */
    public function deletePeriod($id)
    {
        $this->guardAdmin();

        $period = ToolAuditPeriod::with('audits')->findOrFail($id);

        $hasSubmitted = $period->audits->whereIn('status_submit', ['Submitted', 'Verified'])->count() > 0;
        if ($hasSubmitted) {
            return redirect()->route('tool-assignment.index', ['tab' => 'periods'])->with(
                'error',
                'Periode ini tidak dapat dihapus karena sudah ada data audit yang disubmit/diverifikasi oleh teknisi.'
            );
        }

        foreach ($period->audits as $audit) {
            $audit->items()->delete();
            $audit->delete();
        }

        $title = $period->period_title;
        $period->delete();

        return redirect()->route('tool-assignment.index', ['tab' => 'periods'])->with(
            'success',
            "Periode audit {$title} dan draft terkait berhasil dihapus."
        );
    }


    public function addTechnician(Request $request)
    {
        $this->guardAdmin();

        $request->validate([
            'user_id' => 'required|exists:users,id|unique:tool_assignment_technicians,user_id',
        ], [
            'user_id.unique' => 'User ini sudah ada di daftar teknisi Tool Assignment.',
        ]);

        $user = User::findOrFail($request->user_id);
        ToolAssignmentTechnician::create(['user_id' => $user->id]);

        return redirect()->back()->with('success', $user->name . ' berhasil ditambahkan ke daftar Tool Assignment.');
    }

    public function removeTechnician($userId)
    {
        $this->guardAdmin();

        $entry = ToolAssignmentTechnician::where('user_id', $userId)->firstOrFail();
        $entry->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus dari daftar Tool Assignment.');
    }

    public function show($technicianId)
    {
        $this->guardAdmin();

        $technician = User::whereHas('toolAssignmentEntry')->findOrFail($technicianId);
        $tools = FixedAsset::where('type', 'Tools')
            ->where('id_pic', $technicianId)
            ->with('toolsMaster')
            ->orderByDesc('tanggal_serah_terima')
            ->get();
        $toolMasters = ToolMaster::where('status_aktif', 1)->orderBy('nama_tools')->get();
        $otherTechnicians = User::whereHas('toolAssignmentEntry')->where('id', '!=', $technicianId)->orderBy('name')->get();

        return view('pages.technician.tool-assignment.show', compact('technician', 'tools', 'toolMasters', 'otherTechnicians'));
    }

    public function store(Request $request, $technicianId)
    {
        $this->guardAdmin();

        $technician = User::whereHas('toolAssignmentEntry')->findOrFail($technicianId);

        $request->validate([
            'id_tools_master' => 'required|exists:tool_master,id',
            'qty' => 'required|integer|min:1',
            'tanggal_serah_terima' => 'required|date',
            'foto_awal' => 'required|image|mimes:jpeg,jpg,png|max:4096',
        ], [
            'id_tools_master.required' => 'Pilih jenis tools dari master.',
            'foto_awal.required' => 'Foto serah-terima wajib diupload.',
        ]);

        $fixed = new FixedAsset();
        $fixed->type = 'Tools';
        $fixed->id_tools_master = $request->id_tools_master;
        $fixed->id_pic = $technician->id;
        $fixed->qty = $request->qty;
        $fixed->tanggal_serah_terima = $request->tanggal_serah_terima;
        $fixed->status_tools = 'Aktif';
        $fixed->status = 1;
        $fixed->desc = $request->desc;

        $fixed->foto_awal = $this->uploadFoto($request->file('foto_awal'), $technician->id);
        $fixed->save();

        return redirect()->back()->with('success', 'Tools berhasil ditambahkan ke ' . $technician->name . '.');
    }

    public function update(Request $request, $id)
    {
        $this->guardAdmin();

        $fixed = FixedAsset::where('type', 'Tools')->findOrFail($id);

        $request->validate([
            'qty'                  => 'required|integer|min:1',
            'tanggal_serah_terima' => 'required|date',
            'foto_awal'            => 'nullable|file|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $fixed->qty = $request->qty;
        $fixed->tanggal_serah_terima = $request->tanggal_serah_terima;
        $fixed->desc = $request->desc;

        if ($request->hasFile('foto_awal')) {
            $fixed->foto_awal = $this->uploadFoto($request->file('foto_awal'), $fixed->id_pic);
        }

        $fixed->save();

        return redirect()->back()->with('success', 'Data tools berhasil diperbarui.');
    }

    public function transfer(Request $request, $id)
    {
        $this->guardAdmin();

        $request->validate([
            'id_pic' => 'required|exists:users,id',
        ]);

        $fixed = FixedAsset::where('type', 'Tools')->findOrFail($id);
        $newTechnician = User::whereHas('toolAssignmentEntry')->findOrFail($request->id_pic);

        $fixed->id_pic = $newTechnician->id;
        $fixed->save();

        return redirect()->back()->with('success', 'Tools berhasil dipindah ke ' . $newTechnician->name . '.');
    }

    public function retire($id)
    {
        $this->guardAdmin();

        $fixed = FixedAsset::where('type', 'Tools')->findOrFail($id);
        $fixed->status_tools = $fixed->status_tools == 'Retired' ? 'Aktif' : 'Retired';
        $fixed->save();

        return redirect()->back()->with('success', $fixed->status_tools == 'Retired' ? 'Tools ditandai Retired.' : 'Tools diaktifkan kembali.');
    }

    protected function uploadFoto($foto, $technicianId)
    {
        $ext = $foto->getClientOriginalExtension();
        $name = Str::random(10);
        $uploadDir = ToolAssetPath::to('asset/tools-instance/' . $technicianId);
        $relativePath = 'asset/tools-instance/' . $technicianId . '/' . $name . '.' . $ext;

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $img = Image::make($foto->path());
        $img->fit(1000, 1000, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save(ToolAssetPath::to($relativePath));

        return $relativePath;
    }
}
