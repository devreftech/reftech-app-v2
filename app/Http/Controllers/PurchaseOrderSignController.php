<?php

namespace App\Http\Controllers;

use App\Models\DetailPurchaseOrder;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PurchaseOrderSignController extends Controller
{
    /**
     * Tampilan publik portal tanda tangan Purchase Order untuk Vendor/Supplier.
     * Menggunakan secure token unik sehingga vendor tidak perlu login.
     */
    public function show($token)
    {
        $purchase = PurchaseOrder::with(['detail', 'supplier'])
            ->where('sign_token', $token)
            ->first();

        // Fallback jika token berupa ID (untuk backward compatibility)
        if (!$purchase && is_numeric($token)) {
            $purchase = PurchaseOrder::with(['detail', 'supplier'])->find($token);
            if ($purchase) {
                return redirect()->route('purchase.vendor.sign', $purchase->sign_token);
            }
        }

        if (!$purchase) {
            abort(404, 'Purchase Order tidak ditemukan atau tautan sudah kedaluwarsa.');
        }

        $dPurchase = DetailPurchaseOrder::where('id_purchase_order', $purchase->id)->get();
        $totalPph = $dPurchase->sum('pph');
        $hasDisc = $dPurchase->contains(fn($item) => $item->disc > 0);

        return view('pages.vendor.purchase-sign', [
            'purchase'  => $purchase,
            'dPurchase' => $dPurchase,
            'totalPph'  => $totalPph,
            'hasDisc'   => $hasDisc,
        ]);
    }

    /**
     * Memproses tanda tangan digital vendor (Canvas Base64 PNG).
     */
    public function sign(Request $request, $token)
    {
        $purchase = PurchaseOrder::where('sign_token', $token)->firstOrFail();

        if ($purchase->isSignedByVendor()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Purchase Order ini sudah ditandatangani sebelumnya.',
            ], 422);
        }

        $request->validate([
            'signature_data'  => 'required|string',
            'signer_name'     => 'required|string|max:255',
            'signer_position' => 'nullable|string|max:255',
            'agreement'       => 'accepted',
            'stamp'           => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
        ], [
            'signature_data.required' => 'Goresan tanda tangan wajib dibubuhkan.',
            'signer_name.required'    => 'Nama lengkap penandatangan wajib diisi.',
            'agreement.accepted'      => 'Anda harus menyetujui pernyataan persetujuan Purchase Order.',
            'stamp.image'             => 'File stempel harus berupa gambar (jpg, png).',
        ]);

        // 1. Proses Gambar Tanda Tangan Canvas (Base64 PNG)
        $sigData = $request->input('signature_data');
        if (preg_match('/^data:image\/(\w+);base64,/', $sigData, $type)) {
            $sigData = substr($sigData, strpos($sigData, ',') + 1);
            $decodedSig = base64_decode($sigData);

            if ($decodedSig === false) {
                return response()->json(['status' => 'error', 'message' => 'Gagal memproses tanda tangan.'], 422);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Format tanda tangan tidak valid.'], 422);
        }

        $uploadDir = public_path('asset/po/signatures/' . date('Y'));
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        $filename = 'sign_vendor_' . $purchase->id . '_' . Str::random(12) . '.png';
        $fullPath = $uploadDir . '/' . $filename;
        File::put($fullPath, $decodedSig);
        $sigRelativePath = 'asset/po/signatures/' . date('Y') . '/' . $filename;

        // 2. Proses Stempel (Opsional)
        $stampRelativePath = null;
        if ($request->hasFile('stamp')) {
            $stampFile = $request->file('stamp');
            $stampFilename = 'stamp_vendor_' . $purchase->id . '_' . Str::random(12) . '.' . $stampFile->getClientOriginalExtension();
            $stampFile->move($uploadDir, $stampFilename);
            $stampRelativePath = 'asset/po/signatures/' . date('Y') . '/' . $stampFilename;
        }

        // 3. Simpan data tanda tangan ke PO
        $purchase->vendor_signature       = $sigRelativePath;
        $purchase->vendor_signer_name     = trim($request->input('signer_name'));
        $purchase->vendor_signer_position = $request->filled('signer_position') ? trim($request->input('signer_position')) : null;
        $purchase->vendor_signed_stamp    = $stampRelativePath;
        $purchase->vendor_ip              = $request->ip();
        $purchase->vendor_signed_at       = Carbon::now();
        $purchase->save();

        return response()->json([
            'status'   => 'success',
            'message'  => 'Purchase Order berhasil disetujui dan ditandatangani.',
            'redirect' => route('purchase.vendor.sign', $purchase->sign_token),
        ]);
    }

    /**
     * Reset tanda tangan oleh vendor pada halaman publik (jika ingin menandatangani ulang).
     */
    public function vendorReset(Request $request, $token)
    {
        $purchase = PurchaseOrder::where('sign_token', $token)->firstOrFail();

        // Hapus file tanda tangan & stempel lama jika ada
        if ($purchase->vendor_signature && File::exists(public_path($purchase->vendor_signature))) {
            File::delete(public_path($purchase->vendor_signature));
        }
        if ($purchase->vendor_signed_stamp && File::exists(public_path($purchase->vendor_signed_stamp))) {
            File::delete(public_path($purchase->vendor_signed_stamp));
        }

        $purchase->vendor_signature       = null;
        $purchase->vendor_signer_name     = null;
        $purchase->vendor_signer_position = null;
        $purchase->vendor_signed_stamp    = null;
        $purchase->vendor_ip              = null;
        $purchase->vendor_signed_at       = null;
        $purchase->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'   => 'success',
                'message'  => 'Tanda tangan berhasil dihapus. Silakan bubuhkan tanda tangan baru.',
                'redirect' => route('purchase.vendor.sign', $purchase->sign_token),
            ]);
        }

        return redirect()->route('purchase.vendor.sign', $purchase->sign_token)
            ->with('message', 'Tanda tangan berhasil dihapus. Silakan bubuhkan tanda tangan baru.');
    }

    /**
     * Admin/Accounting Reset tanda tangan vendor dari halaman detail PO.
     */
    public function adminReset(Request $request, $id)
    {
        $purchase = PurchaseOrder::findOrFail($id);

        if ($purchase->vendor_signature && File::exists(public_path($purchase->vendor_signature))) {
            File::delete(public_path($purchase->vendor_signature));
        }
        if ($purchase->vendor_signed_stamp && File::exists(public_path($purchase->vendor_signed_stamp))) {
            File::delete(public_path($purchase->vendor_signed_stamp));
        }

        $purchase->vendor_signature       = null;
        $purchase->vendor_signer_name     = null;
        $purchase->vendor_signer_position = null;
        $purchase->vendor_signed_stamp    = null;
        $purchase->vendor_ip              = null;
        $purchase->vendor_signed_at       = null;
        $purchase->save();

        return redirect()->route('purchase.show', $purchase->id)
            ->with('message', 'Tanda tangan vendor berhasil dihapus / direset.');
    }
}
