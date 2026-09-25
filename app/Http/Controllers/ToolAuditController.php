<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\ToolAudit;
use App\Models\ToolAuditItem;
use App\Models\ToolTransfer;
use App\Models\User;
use App\Services\ToolAssetPath;
use App\Services\ToolAuditPeriodGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Str;

class ToolAuditController extends Controller
{
    protected function guardTechnician()
    {
        if (!in_array(Auth::user()->role, ['Technician', 'Coordinator', 'Support', 'ServiceM', 'Admin', 'Developer'])) {
            abort(403, 'Halaman ini khusus untuk pemegang tools.');
        }
    }

    public function index(ToolAuditPeriodGenerator $generator)
    {
        $this->guardTechnician();

        // Lazy trigger — jaga-jaga kalau cron server belum/tidak jalan.
        $generator->generateIfNeeded();

        $audits = ToolAudit::with(['period', 'items.fixedAsset.toolsMaster'])
            ->where('id_technician', Auth::id())
            ->orderByDesc('id')
            ->get();

        // Active / urgent audit needing action (Open period + Draft or Rejected)
        $activeAudit = $audits->first(function ($a) {
            return in_array($a->status_submit, ['Draft', 'Rejected']) && $a->period && $a->period->status === 'Open';
        });

        // Currently assigned tools to this technician with pending transfer status check
        $assignedTools = FixedAsset::where('type', 'Tools')
            ->where('id_pic', Auth::id())
            ->where(function ($q) {
                $q->whereNull('status_tools')
                  ->orWhere('status_tools', '!=', 'Disposed');
            })
            ->with(['toolsMaster', 'aktiva'])
            ->orderBy('id', 'desc')
            ->get();

        // Active incoming transfer requests to this technician (waiting my approval)
        $incomingTransfers = ToolTransfer::with(['fixedAsset.toolsMaster', 'fromUser'])
            ->where('id_to_user', Auth::id())
            ->where('status', 'Pending')
            ->latest('id')
            ->get();

        // Active outgoing transfer requests from this technician
        $outgoingTransfers = ToolTransfer::with(['fixedAsset.toolsMaster', 'toUser'])
            ->where('id_from_user', Auth::id())
            ->latest('id')
            ->take(15)
            ->get();

        // Transfer history (completed / rejected / approved for this technician)
        $transferHistories = ToolTransfer::with(['fixedAsset.toolsMaster', 'fromUser', 'toUser'])
            ->where(function ($q) {
                $q->where('id_from_user', Auth::id())
                  ->orWhere('id_to_user', Auth::id());
            })
            ->latest('id')
            ->take(20)
            ->get();

        // Other active technicians for destination dropdown
        $otherTechnicians = User::where('role', 'Technician')
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        // Summary statistics
        $totalAuditsCount = $audits->count();
        $verifiedAuditsCount = $audits->where('status_submit', 'Verified')->count();
        $draftAuditsCount = $audits->where('status_submit', 'Draft')->count();
        $rejectedAuditsCount = $audits->where('status_submit', 'Rejected')->count();
        $submittedAuditsCount = $audits->where('status_submit', 'Submitted')->count();
        $actionRequiredCount = $draftAuditsCount + $rejectedAuditsCount;

        return view('pages.technician.tool-audit.index', compact(
            'audits',
            'activeAudit',
            'assignedTools',
            'incomingTransfers',
            'outgoingTransfers',
            'transferHistories',
            'otherTechnicians',
            'totalAuditsCount',
            'verifiedAuditsCount',
            'draftAuditsCount',
            'rejectedAuditsCount',
            'submittedAuditsCount',
            'actionRequiredCount'
        ));
    }

    public function show($id)
    {
        $this->guardTechnician();

        $audit = ToolAudit::with(['items.fixedAsset.toolsMaster', 'period'])
            ->where('id_technician', Auth::id())
            ->findOrFail($id);

        $editable = in_array($audit->status_submit, ['Draft', 'Rejected']);

        return view('pages.technician.tool-audit.show', compact('audit', 'editable'));
    }

    /**
     * Simpan perubahan sebagai Draft (tidak dikirim ke admin).
     */
    public function saveDraft(Request $request, $id)
    {
        $this->guardTechnician();

        $audit = ToolAudit::with('items')
            ->where('id_technician', Auth::id())
            ->findOrFail($id);

        if (!in_array($audit->status_submit, ['Draft', 'Rejected'])) {
            abort(403, 'Audit ini sudah disubmit dan tidak bisa diubah lagi.');
        }

        $technicianName = Auth::user()->name;
        $totalAda = 0;
        $totalRusak = 0;
        $totalHilang = 0;

        if ($request->has('items') && is_array($request->items)) {
            foreach ($audit->items as $item) {
                if (isset($request->items[$item->id])) {
                    $data = $request->items[$item->id];
                    if (isset($data['qty_actual'])) {
                        $item->qty_actual = (int)$data['qty_actual'];
                    }
                    if (isset($data['kondisi'])) {
                        $item->kondisi = $data['kondisi'];
                        $item->alasan = in_array($data['kondisi'], ['Rusak', 'Hilang']) ? ($data['alasan'] ?? $item->alasan) : null;
                        $item->metode_ganti = $data['kondisi'] == 'Hilang' ? ($data['metode_ganti'] ?? $item->metode_ganti) : null;
                    }

                    if ($request->hasFile("items.{$item->id}.foto_audit")) {
                        $item->foto_audit = $this->uploadFotoAudit(
                            $request->file("items.{$item->id}.foto_audit"),
                            Auth::id(),
                            $technicianName
                        );
                    }

                    $item->save();
                }

                if ($item->kondisi == 'Ada') $totalAda++;
                if ($item->kondisi == 'Rusak') $totalRusak++;
                if ($item->kondisi == 'Hilang') $totalHilang++;
            }
        }

        $audit->total_tools = $audit->items->count();
        $audit->total_ada = $totalAda;
        $audit->total_rusak = $totalRusak;
        $audit->total_hilang = $totalHilang;
        $audit->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Draft audit berhasil disimpan ke database.',
                'totals' => [
                    'ada' => $totalAda,
                    'rusak' => $totalRusak,
                    'hilang' => $totalHilang,
                    'total' => $audit->total_tools,
                ]
            ]);
        }

        return redirect()->route('tool-audit.show', $audit->id)->with('success', 'Perubahan audit berhasil disimpan sebagai Draft.');
    }

    /**
     * Auto-save per item via AJAX secara real-time saat teknisi mengubah input / kondisi.
     */
    public function autoSaveItemAjax(Request $request, $itemId)
    {
        $this->guardTechnician();

        $item = ToolAuditItem::with('audit')
            ->whereHas('audit', function ($query) {
                $query->where('id_technician', Auth::id())
                    ->whereIn('status_submit', ['Draft', 'Rejected']);
            })
            ->findOrFail($itemId);

        if ($request->has('qty_actual')) {
            $item->qty_actual = max(0, (int)$request->input('qty_actual'));
        }

        if ($request->has('kondisi')) {
            $kondisi = $request->input('kondisi');
            if (in_array($kondisi, ['Ada', 'Rusak', 'Hilang'])) {
                $item->kondisi = $kondisi;
                $item->alasan = in_array($kondisi, ['Rusak', 'Hilang']) ? $request->input('alasan') : null;
                $item->metode_ganti = $kondisi == 'Hilang' ? $request->input('metode_ganti') : null;
            }
        }

        if ($request->has('alasan')) {
            $item->alasan = $request->input('alasan');
        }

        if ($request->has('metode_ganti')) {
            $item->metode_ganti = $request->input('metode_ganti');
        }

        $item->save();

        // Recalculate totals on parent audit without changing status
        $audit = $item->audit;
        $allItems = $audit->items;
        $audit->total_ada = $allItems->where('kondisi', 'Ada')->count();
        $audit->total_rusak = $allItems->where('kondisi', 'Rusak')->count();
        $audit->total_hilang = $allItems->where('kondisi', 'Hilang')->count();
        $audit->save();

        return response()->json([
            'success' => true,
            'message' => 'Tersimpan otomatis sebagai Draft.',
            'item_id' => $item->id,
            'totals' => [
                'ada' => $audit->total_ada,
                'rusak' => $audit->total_rusak,
                'hilang' => $audit->total_hilang,
                'total' => $audit->total_tools,
            ]
        ]);
    }

    public function submit(Request $request, $id)
    {
        $this->guardTechnician();

        $audit = ToolAudit::with('items')
            ->where('id_technician', Auth::id())
            ->findOrFail($id);

        if (!in_array($audit->status_submit, ['Draft', 'Rejected'])) {
            abort(403, 'Audit ini sudah disubmit dan tidak bisa diubah lagi.');
        }

        $rules = ['items' => 'required|array'];
        $messages = [];

        foreach ($audit->items as $item) {
            $prefix = "items.{$item->id}";
            $rules["{$prefix}.kondisi"] = 'required|in:Ada,Rusak,Hilang';
            $rules["{$prefix}.qty_actual"] = 'required|integer|min:0';
            $rules["{$prefix}.foto_audit"] = $item->foto_audit
                ? 'nullable|image|mimes:jpeg,jpg,png,webp,gif,heic,heif|max:12288'
                : 'required|image|mimes:jpeg,jpg,png,webp,gif,heic,heif|max:12288';

            $kondisi = $request->input("{$prefix}.kondisi");
            if ($kondisi == 'Rusak') {
                $rules["{$prefix}.alasan"] = 'required|string|max:500';
            } elseif ($kondisi == 'Hilang') {
                $rules["{$prefix}.metode_ganti"] = 'required|in:Beli Sendiri,Potong Bonus';
                $rules["{$prefix}.alasan"] = 'nullable|string|max:500';
            }
        }

        $messages['items.*.kondisi.required'] = 'Kondisi (Ada / Rusak / Hilang) wajib dipilih untuk setiap tools.';
        $messages['items.*.foto_audit.required'] = 'Foto fisik terbaru wajib diupload untuk setiap tools.';
        $messages['items.*.alasan.required'] = 'Keterangan/alasan kerusakan wajib diisi untuk tools yang Rusak.';
        $messages['items.*.metode_ganti.required'] = 'Metode pertanggungjawaban wajib dipilih untuk tools yang Hilang.';
        $messages['items.*.foto_audit.max'] = 'Ukuran foto maksimal 12MB.';
        $messages['items.*.foto_audit.image'] = 'File harus berupa foto/gambar.';

        $this->validate($request, $rules, $messages);

        $technicianName = Auth::user()->name;
        $totalAda = 0;
        $totalRusak = 0;
        $totalHilang = 0;

        foreach ($audit->items as $item) {
            $data = $request->input("items.{$item->id}");

            $item->qty_actual = isset($data['qty_actual']) ? (int)$data['qty_actual'] : $item->qty_actual;
            $item->kondisi = $data['kondisi'];
            $item->alasan = in_array($data['kondisi'], ['Rusak', 'Hilang']) ? ($data['alasan'] ?? null) : null;
            $item->metode_ganti = $data['kondisi'] == 'Hilang' ? ($data['metode_ganti'] ?? null) : null;

            if ($request->hasFile("items.{$item->id}.foto_audit")) {
                $item->foto_audit = $this->uploadFotoAudit(
                    $request->file("items.{$item->id}.foto_audit"),
                    Auth::id(),
                    $technicianName
                );
            }

            $item->save();

            if ($item->kondisi == 'Ada') $totalAda++;
            if ($item->kondisi == 'Rusak') $totalRusak++;
            if ($item->kondisi == 'Hilang') $totalHilang++;
        }

        $audit->status_submit = 'Submitted';
        $audit->submitted_at = Carbon::now();
        $audit->total_tools = $audit->items->count();
        $audit->total_ada = $totalAda;
        $audit->total_rusak = $totalRusak;
        $audit->total_hilang = $totalHilang;
        $audit->save();

        return redirect()->route('tool-audit.show', $audit->id)->with('success', 'Self-audit berhasil disubmit dan menunggu verifikasi Admin.');
    }

    protected function uploadFotoAudit($foto, $technicianId, $technicianName)
    {
        $ext = strtolower($foto->getClientOriginalExtension() ?: 'jpg');
        $name = Str::random(12);
        $uploadDir = ToolAssetPath::to('asset/tools-audit/' . $technicianId);
        $relativePath = 'asset/tools-audit/' . $technicianId . '/' . $name . '.' . $ext;

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $img = Image::make($foto->getRealPath());
        
        // Handle mobile EXIF rotation automatically
        if (method_exists($img, 'orientate')) {
            $img->orientate();
        }

        // Crop and fit into a high-res Square (1000px x 1000px)
        $img->fit(1000, 1000, function ($constraint) {
            $constraint->upsize();
        });

        $label = $technicianName . ' - ' . Carbon::now()->format('d M Y H:i');
        $barHeight = 32;

        $img->rectangle(0, $img->height() - $barHeight, $img->width(), $img->height(), function ($draw) {
            $draw->background('rgba(0,0,0,0.65)');
        });
        $img->text($label, 12, $img->height() - 10, function ($font) {
            $font->file(5);
            $font->color('#ffffff');
            $font->valign('bottom');
        });

        $img->save(ToolAssetPath::to($relativePath), 85);

        return $relativePath;
    }

    public function uploadPhotoAjax(Request $request, $itemId)
    {
        $this->guardTechnician();

        $item = ToolAuditItem::with('audit')
            ->whereHas('audit', function ($query) {
                $query->where('id_technician', Auth::id())
                    ->whereIn('status_submit', ['Draft', 'Rejected']);
            })
            ->findOrFail($itemId);

        $this->validate($request, [
            'foto_audit' => 'required|image|mimes:jpeg,jpg,png,webp,gif,heic,heif|max:12288'
        ], [
            'foto_audit.required' => 'Foto wajib diupload.',
            'foto_audit.image' => 'File harus berupa gambar.',
            'foto_audit.mimes' => 'Format gambar harus jpeg, jpg, png, webp, atau heic.',
            'foto_audit.max' => 'Ukuran gambar maksimal 12MB.'
        ]);

        $technicianName = Auth::user()->name;

        $relativePath = $this->uploadFotoAudit(
            $request->file('foto_audit'),
            Auth::id(),
            $technicianName
        );

        $item->foto_audit = $relativePath;
        $item->save();

        return response()->json([
            'success' => true,
            'foto_url' => asset($relativePath),
            'item_id' => $item->id
        ]);
    }

    /**
     * Ajukan transfer kepemilikan alat ke teknisi lain.
     */
    public function storeTransfer(Request $request)
    {
        $this->guardTechnician();

        $request->validate([
            'id_fixed_asset' => 'required|exists:fixed_asset,id',
            'id_to_user' => 'required|exists:users,id',
            'catatan_pengirim' => 'nullable|string|max:500',
            'foto_kondisi' => 'nullable|image|mimes:jpeg,jpg,png,webp,heic,heif|max:12288',
        ], [
            'id_fixed_asset.required' => 'Pilih alat yang akan ditransfer.',
            'id_to_user.required' => 'Pilih teknisi penerima transfer.',
            'foto_kondisi.image' => 'File foto harus berupa gambar.',
            'foto_kondisi.max' => 'Ukuran foto maksimal 12MB.',
        ]);

        $asset = FixedAsset::where('type', 'Tools')
            ->where('id_pic', Auth::id())
            ->findOrFail($request->id_fixed_asset);

        if ((int)$request->id_to_user === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mentransfer alat ke akun Anda sendiri.');
        }

        // Cek apakah alat ini sudah memiliki pengajuan transfer yang masih pending
        $existingPending = ToolTransfer::where('id_fixed_asset', $asset->id)
            ->where('status', 'Pending')
            ->first();

        if ($existingPending) {
            return back()->with('error', 'Alat ini sedang dalam proses transfer ke ' . ($existingPending->toUser?->name ?? 'teknisi lain') . '. Mohon tunggu respons atau batalkan pengajuan sebelumnya.');
        }

        $recipient = User::where('role', 'Technician')->findOrFail($request->id_to_user);

        $fotoPath = null;
        if ($request->hasFile('foto_kondisi')) {
            $fotoPath = $this->uploadFotoAudit(
                $request->file('foto_kondisi'),
                Auth::id(),
                Auth::user()->name . ' (Transfer)'
            );
        }

        $transferNumber = 'TRF-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        ToolTransfer::create([
            'transfer_number' => $transferNumber,
            'id_fixed_asset' => $asset->id,
            'id_from_user' => Auth::id(),
            'id_to_user' => $recipient->id,
            'status' => 'Pending',
            'catatan_pengirim' => $request->catatan_pengirim,
            'foto_kondisi' => $fotoPath,
            'requested_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Permintaan transfer kepemilikan alat berhasil dikirim ke ' . $recipient->name . '. Kepemilikan akan resmi berpindah setelah disetujui/diterima oleh teknisi tersebut.');
    }

    /**
     * Terima / Setujui transfer kepemilikan alat yang ditujukan ke akun saya.
     */
    public function acceptTransfer(Request $request, $id)
    {
        $this->guardTechnician();

        $transfer = ToolTransfer::with(['fixedAsset.toolsMaster', 'fromUser'])
            ->where('id_to_user', Auth::id())
            ->where('status', 'Pending')
            ->findOrFail($id);

        $asset = $transfer->fixedAsset;

        DB::transaction(function () use ($transfer, $asset, $request) {
            $transfer->status = 'Approved';
            $transfer->responded_at = Carbon::now();
            $transfer->catatan_penerima = $request->input('catatan_penerima');
            $transfer->save();

            // Pindahkan kepemilikan alat ke teknisi penerima
            $asset->id_pic = Auth::id();
            $asset->tanggal_serah_terima = Carbon::today()->toDateString();
            if ($transfer->foto_kondisi) {
                $asset->foto_awal = $transfer->foto_kondisi;
            }
            $asset->save();

            // Sinkronisasi dengan periode audit yang sedang OPEN jika ada
            $openPeriod = \App\Models\ToolAuditPeriod::where('status', 'Open')->latest('id')->first();
            if ($openPeriod) {
                // 1. Jika pengirim punya audit Draft di periode ini, hapus item alat ini dari audit pengirim
                $senderAudit = ToolAudit::with('items')
                    ->where('id_audit_period', $openPeriod->id)
                    ->where('id_technician', $transfer->id_from_user)
                    ->where('status_submit', 'Draft')
                    ->first();

                if ($senderAudit) {
                    $itemToRemove = $senderAudit->items->where('id_fixed_asset', $asset->id)->first();
                    if ($itemToRemove) {
                        $itemToRemove->delete();
                    }
                    $senderAudit->total_tools = $senderAudit->items()->count();
                    $senderAudit->total_ada = $senderAudit->items()->where('kondisi', 'Ada')->count();
                    $senderAudit->total_rusak = $senderAudit->items()->where('kondisi', 'Rusak')->count();
                    $senderAudit->total_hilang = $senderAudit->items()->where('kondisi', 'Hilang')->count();
                    $senderAudit->save();
                }

                // 2. Jika penerima (saya) punya audit Draft di periode ini, tambahkan item alat baru ini ke audit saya
                $recipientAudit = ToolAudit::with('items')
                    ->where('id_audit_period', $openPeriod->id)
                    ->where('id_technician', Auth::id())
                    ->where('status_submit', 'Draft')
                    ->first();

                if ($recipientAudit) {
                    $existsInRecipient = $recipientAudit->items->where('id_fixed_asset', $asset->id)->first();
                    if (!$existsInRecipient) {
                        ToolAuditItem::create([
                            'id_audit' => $recipientAudit->id,
                            'id_fixed_asset' => $asset->id,
                            'qty_actual' => $asset->qty ?: 1,
                            'kondisi' => null,
                        ]);
                    }
                    $recipientAudit->total_tools = $recipientAudit->items()->count();
                    $recipientAudit->save();
                }
            }
        });

        $toolName = $asset->toolsMaster?->nama_tools ?? $asset->desc ?? 'Alat';
        return back()->with('success', 'Transfer alat "' . $toolName . '" berhasil diterima! Alat kini resmi terdaftar atas nama Anda.');
    }

    /**
     * Tolak permintaan transfer kepemilikan alat.
     */
    public function rejectTransfer(Request $request, $id)
    {
        $this->guardTechnician();

        $transfer = ToolTransfer::with('fixedAsset.toolsMaster')
            ->where('id_to_user', Auth::id())
            ->where('status', 'Pending')
            ->findOrFail($id);

        $transfer->status = 'Rejected';
        $transfer->responded_at = Carbon::now();
        $transfer->catatan_penerima = $request->input('catatan_penerima');
        $transfer->save();

        $toolName = $transfer->fixedAsset?->toolsMaster?->nama_tools ?? 'Alat';
        return back()->with('success', 'Permintaan transfer alat "' . $toolName . '" telah ditolak.');
    }

    /**
     * Batalkan permintaan transfer yang diajukan oleh pengirim.
     */
    public function cancelTransfer(Request $request, $id)
    {
        $this->guardTechnician();

        $transfer = ToolTransfer::where('id_from_user', Auth::id())
            ->where('status', 'Pending')
            ->findOrFail($id);

        $transfer->status = 'Cancelled';
        $transfer->responded_at = Carbon::now();
        $transfer->save();

        return back()->with('success', 'Permintaan transfer kepemilikan alat berhasil dibatalkan.');
    }
}

