<?php

namespace App\Http\Controllers;

use App\Models\Bast;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BastSignController extends Controller
{
    /**
     * Tampilan publik portal tanda tangan BAST untuk Customer.
     * Menggunakan secure token unik sehingga customer tidak perlu login.
     */
    public function show($token)
    {
        $bast = Bast::with(['units', 'kanbanTask', 'quotation', 'creator'])
            ->where('sign_token', $token)
            ->first();

        // Fallback jika token berupa ID (untuk backward compatibility)
        if (!$bast && is_numeric($token)) {
            $bast = Bast::with(['units', 'kanbanTask', 'quotation', 'creator'])->find($token);
            if ($bast) {
                return redirect()->route('bast.customer.sign', $bast->sign_token);
            }
        }

        if (!$bast) {
            abort(404, 'Berita Acara Serah Terima (BAST) tidak ditemukan atau tautan sudah kedaluwarsa.');
        }

        return view('pages.customer.bast-sign', [
            'bast' => $bast,
        ]);
    }

    /**
     * Memproses tanda tangan digital customer (Canvas Base64 PNG).
     */
    public function sign(Request $request, $token)
    {
        $bast = Bast::where('sign_token', $token)->firstOrFail();

        if ($bast->isSignedByCustomer()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'BAST ini sudah ditandatangani sebelumnya.',
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
            'agreement.accepted'      => 'Anda harus menyetujui pernyataan konfirmasi serah terima.',
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

        $uploadDir = public_path('asset/bast/signatures/' . date('Y'));
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        $filename = 'sign_cust_' . $bast->id . '_' . Str::random(12) . '.png';
        $fullPath = $uploadDir . '/' . $filename;
        File::put($fullPath, $decodedSig);
        $sigRelativePath = 'asset/bast/signatures/' . date('Y') . '/' . $filename;

        // 2. Proses Stempel (Opsional)
        $stampRelativePath = null;
        if ($request->hasFile('stamp')) {
            $stampFile = $request->file('stamp');
            $stampFilename = 'stamp_cust_' . $bast->id . '_' . Str::random(12) . '.' . $stampFile->getClientOriginalExtension();
            $stampFile->move($uploadDir, $stampFilename);
            $stampRelativePath = 'asset/bast/signatures/' . date('Y') . '/' . $stampFilename;
        }

        // 3. Simpan data tanda tangan ke BAST
        $bast->customer_signature       = $sigRelativePath;
        $bast->customer_signer_name     = trim($request->input('signer_name'));
        $bast->customer_signer_position = $request->filled('signer_position') ? trim($request->input('signer_position')) : null;
        $bast->customer_signed_stamp    = $stampRelativePath;
        $bast->customer_ip              = $request->ip();
        $bast->customer_signed_at       = Carbon::now();
        $bast->save();

        return response()->json([
            'status'   => 'success',
            'message'  => 'Berita Acara Serah Terima (BAST) berhasil ditandatangani.',
            'redirect' => route('bast.customer.sign', $bast->sign_token),
        ]);
    }

    /**
     * Reset tanda tangan oleh customer pada halaman publik (jika ingin menandatangani ulang).
     */
    public function customerReset(Request $request, $token)
    {
        $bast = Bast::where('sign_token', $token)->firstOrFail();

        // Hapus file tanda tangan & stempel lama jika ada
        if ($bast->customer_signature && File::exists(public_path($bast->customer_signature))) {
            File::delete(public_path($bast->customer_signature));
        }
        if ($bast->customer_signed_stamp && File::exists(public_path($bast->customer_signed_stamp))) {
            File::delete(public_path($bast->customer_signed_stamp));
        }

        $bast->customer_signature       = null;
        $bast->customer_signer_name     = null;
        $bast->customer_signer_position = null;
        $bast->customer_signed_stamp    = null;
        $bast->customer_ip              = null;
        $bast->customer_signed_at       = null;
        $bast->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'   => 'success',
                'message'  => 'Tanda tangan berhasil dihapus. Silakan bubuhkan tanda tangan baru.',
                'redirect' => route('bast.customer.sign', $bast->sign_token),
            ]);
        }

        return redirect()->route('bast.customer.sign', $bast->sign_token)
            ->with('message', 'Tanda tangan berhasil dihapus. Silakan bubuhkan tanda tangan baru.');
    }

    /**
     * Reset tanda tangan customer dari sisi internal (Admin / Accounting).
     */
    public function resetSign($id)
    {
        $bast = Bast::findOrFail($id);

        if ($bast->customer_signature && File::exists(public_path($bast->customer_signature))) {
            File::delete(public_path($bast->customer_signature));
        }
        if ($bast->customer_signed_stamp && File::exists(public_path($bast->customer_signed_stamp))) {
            File::delete(public_path($bast->customer_signed_stamp));
        }

        $bast->customer_signature       = null;
        $bast->customer_signer_name     = null;
        $bast->customer_signer_position = null;
        $bast->customer_signed_stamp    = null;
        $bast->customer_ip              = null;
        $bast->customer_signed_at       = null;
        $bast->save();

        return redirect()->back()->with('success', 'Tanda tangan customer pada BAST berhasil direset. Customer dapat menandatangani ulang.');
    }
}

