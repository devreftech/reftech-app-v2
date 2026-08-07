<?php

namespace App\Http\Controllers;

use App\Models\ToolAudit;
use App\Models\ToolAuditItem;
use App\Services\ToolAssetPath;
use App\Services\ToolAuditPeriodGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Str;

class ToolAuditController extends Controller
{
    protected function guardTechnician()
    {
        if (Auth::user()->role != 'Technician') {
            abort(403, 'Halaman ini khusus untuk Technician.');
        }
    }

    public function index(ToolAuditPeriodGenerator $generator)
    {
        $this->guardTechnician();

        // Lazy trigger — jaga-jaga kalau cron server belum/tidak jalan.
        $generator->generateIfNeeded();

        $audits = ToolAudit::with('period')
            ->where('id_technician', Auth::id())
            ->orderByDesc('id')
            ->get();

        return view('pages.technician.tool-audit.index', compact('audits'));
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
                ? 'nullable|image|mimes:jpeg,jpg,png|max:4096'
                : 'required|image|mimes:jpeg,jpg,png|max:4096';

            $kondisi = $request->input("{$prefix}.kondisi");
            if ($kondisi == 'Rusak') {
                $rules["{$prefix}.alasan"] = 'required|string|max:255';
            } elseif ($kondisi == 'Hilang') {
                $rules["{$prefix}.metode_ganti"] = 'required|in:Beli Sendiri,Potong Bonus';
                $rules["{$prefix}.alasan"] = 'nullable|string|max:255';
            }
        }

        $messages['items.*.kondisi.required'] = 'Kondisi wajib dipilih untuk setiap tools.';
        $messages['items.*.foto_audit.required'] = 'Foto wajib diupload untuk setiap tools.';
        $messages['items.*.alasan.required'] = 'Alasan wajib diisi untuk tools yang Rusak.';
        $messages['items.*.metode_ganti.required'] = 'Metode ganti wajib dipilih untuk tools yang Hilang.';

        $this->validate($request, $rules, $messages);

        $technicianName = Auth::user()->name;
        $totalAda = 0;
        $totalRusak = 0;
        $totalHilang = 0;

        foreach ($audit->items as $item) {
            $data = $request->input("items.{$item->id}");

            $item->qty_actual = $data['qty_actual'];
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
        $ext = $foto->getClientOriginalExtension();
        $name = Str::random(10);
        $uploadDir = ToolAssetPath::to('asset/tools-audit/' . $technicianId);
        $relativePath = 'asset/tools-audit/' . $technicianId . '/' . $name . '.' . $ext;

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $img = Image::make($foto->path());
        $img->resize(1280, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $label = $technicianName . ' - ' . Carbon::now()->format('d M Y H:i');
        $barHeight = 26;

        $img->rectangle(0, $img->height() - $barHeight, $img->width(), $img->height(), function ($draw) {
            $draw->background('rgba(0,0,0,0.55)');
        });
        $img->text($label, 8, $img->height() - 8, function ($font) {
            $font->file(5);
            $font->color('#ffffff');
            $font->valign('bottom');
        });

        $img->save(ToolAssetPath::to($relativePath), 80);

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
            'foto_audit' => 'required|image|mimes:jpeg,jpg,png|max:4096'
        ], [
            'foto_audit.required' => 'Foto wajib diupload.',
            'foto_audit.image' => 'File harus berupa gambar.',
            'foto_audit.mimes' => 'Format gambar harus jpeg, jpg, atau png.',
            'foto_audit.max' => 'Ukuran gambar maksimal 4MB.'
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
            'foto_url' => asset($relativePath)
        ]);
    }
}

