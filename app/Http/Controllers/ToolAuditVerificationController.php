<?php

namespace App\Http\Controllers;

use App\Models\ToolAudit;
use App\Services\ToolAssetPath;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Str;

class ToolAuditVerificationController extends Controller
{
    protected function guardAdmin()
    {
        if (Auth::user()->role != 'Admin') {
            abort(403, 'Hanya Admin yang bisa mengakses Verifikasi Audit Tools.');
        }
    }

    public function index(Request $request)
    {
        $this->guardAdmin();

        $status = $request->get('status', 'Submitted');

        $audits = ToolAudit::with(['technician', 'period'])
            ->where('status_submit', $status)
            ->orderByDesc('submitted_at')
            ->get();

        return view('pages.admin.tool-audit-verification.index', compact('audits', 'status'));
    }

    public function show($id)
    {
        $this->guardAdmin();

        $audit = ToolAudit::with(['technician', 'period', 'items.fixedAsset.toolsMaster'])
            ->findOrFail($id);

        return view('pages.admin.tool-audit-verification.show', compact('audit'));
    }

    public function decide(Request $request, $id)
    {
        $this->guardAdmin();

        $audit = ToolAudit::with('items')->findOrFail($id);

        if ($audit->status_submit != 'Submitted') {
            abort(403, 'Audit ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'action' => 'required|in:verify,reject',
            'catatan_admin' => $request->action == 'reject' ? 'required|string' : 'nullable|string',
        ], [
            'catatan_admin.required' => 'Catatan wajib diisi kalau audit ditolak / minta perbaikan.',
        ]);

        foreach ($audit->items as $item) {
            $itemStatus = $request->input("item_status.{$item->id}");
            $itemNote = $request->input("item_note.{$item->id}");
            if ($itemStatus) {
                $item->status_verifikasi_item = $itemStatus;
                $item->catatan_admin_item = $itemNote;
                $item->save();
            }
        }

        $audit->catatan_admin = $request->catatan_admin;
        $audit->verified_by = Auth::id();
        $audit->verified_at = Carbon::now();
        $audit->status_submit = $request->action == 'verify' ? 'Verified' : 'Rejected';
        $audit->save();

        $message = $request->action == 'verify'
            ? 'Audit ' . $audit->no_audit . ' sudah diverifikasi.'
            : 'Audit ' . $audit->no_audit . ' dikembalikan ke teknisi untuk diperbaiki.';

        return redirect()->route('tool-audit-verification.index')->with('success', $message);
    }

    public function edit($id)
    {
        $this->guardAdmin();

        $audit = ToolAudit::with(['technician', 'period', 'items.fixedAsset.toolsMaster'])
            ->findOrFail($id);

        return view('pages.admin.tool-audit-verification.edit', compact('audit'));
    }

    public function update(Request $request, $id)
    {
        $this->guardAdmin();

        $audit = ToolAudit::with('items')->findOrFail($id);

        $rules = [];
        foreach ($audit->items as $item) {
            $prefix = "items.{$item->id}";
            $rules["{$prefix}.kondisi"] = 'required|in:Ada,Rusak,Hilang';
            $rules["{$prefix}.qty_actual"] = 'required|integer|min:0';
            $rules["{$prefix}.foto_audit"] = 'nullable|image|mimes:jpeg,jpg,png|max:4096';

            $kondisi = $request->input("{$prefix}.kondisi");
            if ($kondisi == 'Rusak') {
                $rules["{$prefix}.alasan"] = 'required|string|max:255';
            } elseif ($kondisi == 'Hilang') {
                $rules["{$prefix}.metode_ganti"] = 'required|in:Beli Sendiri,Potong Bonus';
                $rules["{$prefix}.alasan"] = 'nullable|string|max:255';
            }
        }

        $this->validate($request, $rules, [
            'items.*.kondisi.required' => 'Kondisi wajib dipilih untuk setiap tools.',
            'items.*.qty_actual.required' => 'Qty sekarang wajib diisi untuk setiap tools.',
            'items.*.alasan.required' => 'Alasan wajib diisi untuk tools yang Rusak.',
            'items.*.metode_ganti.required' => 'Metode ganti wajib dipilih untuk tools yang Hilang.',
        ]);

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
                    $audit->id_technician,
                    Auth::user()->name
                );
            }

            $item->save();

            if ($item->kondisi == 'Ada') $totalAda++;
            if ($item->kondisi == 'Rusak') $totalRusak++;
            if ($item->kondisi == 'Hilang') $totalHilang++;
        }

        $audit->total_tools = $audit->items->count();
        $audit->total_ada = $totalAda;
        $audit->total_rusak = $totalRusak;
        $audit->total_hilang = $totalHilang;
        $audit->save();

        return redirect()->route('tool-audit-verification.show', $audit->id)->with('success', 'Data self-audit ' . $audit->no_audit . ' berhasil diperbarui oleh Admin.');
    }

    protected function uploadFotoAudit($foto, $technicianId, $editorName)
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

        $label = $editorName . ' - ' . Carbon::now()->format('d M Y H:i');
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
}
