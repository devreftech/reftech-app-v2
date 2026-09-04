<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\DetailQuotation;
use App\Models\Quotation;
use App\Models\SubtitleQuotation;
use App\Models\UnitQuotation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ContractSignController extends Controller
{
    /**
     * Tampilan publik portal tanda tangan kontrak untuk Customer.
     * Menggunakan secure token unik sehingga customer tidak perlu login.
     */
    public function show($token)
    {
        $contract = Contract::where('sign_token', $token)->first();

        // Fallback jika token berupa ID (untuk backward compatibility / fallback)
        if (!$contract && is_numeric($token)) {
            $contract = Contract::find($token);
            if ($contract) {
                // Auto-generate token dan redirect ke URL ber-token
                return redirect()->route('contract.customer.sign', $contract->sign_token);
            }
        }

        if (!$contract) {
            abort(404, 'Kontrak tidak ditemukan atau tautan sudah kedaluwarsa.');
        }

        // Siapkan data kontrak berdasarkan tipe (Unit Quotation vs Service Quotation)
        if ($contract->id_unit_quotation) {
            $unitQuote = UnitQuotation::with(['client', 'pic', 'details.unit', 'sales'])->find($contract->id_unit_quotation);
            if (!$unitQuote) {
                abort(404, 'Data Quotation Unit tidak ditemukan.');
            }

            return view('pages.customer.contract-sign', [
                'contract'  => $contract,
                'isUnit'    => true,
                'unitQuote' => $unitQuote,
                'quote'     => null,
                'dquote'    => null,
                'subQuote'  => null,
                'tax'       => null,
            ]);
        }

        $quote = Quotation::with(['pic.client', 'sales'])->find($contract->id_quotation);
        if (!$quote) {
            abort(404, 'Data Quotation tidak ditemukan.');
        }

        $subQuote = null;
        if ($quote->type !== 'Sparepart') {
            $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $quote->id)->get();
        }
        $tax    = ($quote->subtotal - $quote->diskon) * $quote->tax / 100;
        $dquote = DetailQuotation::where('id_quotation', $quote->id)->get();

        return view('pages.customer.contract-sign', [
            'contract'  => $contract,
            'isUnit'    => false,
            'unitQuote' => null,
            'quote'     => $quote,
            'dquote'    => $dquote,
            'subQuote'  => $subQuote,
            'tax'       => $tax,
        ]);
    }

    /**
     * Memproses tanda tangan digital customer (Canvas Base64 PNG).
     */
    public function sign(Request $request, $token)
    {
        $contract = Contract::where('sign_token', $token)->firstOrFail();

        if ($contract->isSignedByCustomer()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kontrak ini sudah ditandatangani sebelumnya.',
            ], 422);
        }

        $request->validate([
            'signature_data'  => 'required|string',
            'signer_name'     => 'required|string|max:255',
            'signer_position' => 'required|string|max:255',
            'agreement'       => 'accepted',
            'stamp'           => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
        ], [
            'signature_data.required'  => 'Goresan tanda tangan wajib dibubuhkan.',
            'signer_name.required'     => 'Nama lengkap penandatangan wajib diisi.',
            'signer_position.required' => 'Jabatan / posisi penandatangan wajib diisi.',
            'agreement.accepted'       => 'Anda harus menyetujui pernyataan wewenang penandatanganan.',
            'stamp.image'              => 'File stempel harus berupa gambar.',
        ]);

        // 1. Proses Gambar Tanda Tangan Canvas (Base64 PNG)
        $sigData = $request->input('signature_data');
        if (preg_match('/^data:image\/(\w+);base64,/', $sigData, $type)) {
            $sigData = substr($sigData, strpos($sigData, ',') + 1);
            $type = strtolower($type[1]); // png
            $decodedSig = base64_decode($sigData);

            if ($decodedSig === false) {
                return response()->json(['status' => 'error', 'message' => 'Gagal memproses tanda tangan.'], 422);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Format tanda tangan tidak valid.'], 422);
        }

        $uploadDir = public_path('asset/contract/signatures/' . date('Y'));
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        $filename = 'sign_' . $contract->id . '_' . Str::random(12) . '.png';
        $fullPath = $uploadDir . '/' . $filename;
        File::put($fullPath, $decodedSig);
        $sigRelativePath = 'asset/contract/signatures/' . date('Y') . '/' . $filename;

        // 2. Proses Stempel (Opsional)
        $stampRelativePath = null;
        if ($request->hasFile('stamp')) {
            $stampFile = $request->file('stamp');
            $stampFilename = 'stamp_' . $contract->id . '_' . Str::random(12) . '.' . $stampFile->getClientOriginalExtension();
            $stampFile->move($uploadDir, $stampFilename);
            $stampRelativePath = 'asset/contract/signatures/' . date('Y') . '/' . $stampFilename;
        }

        // 3. Simpan data tanda tangan ke kontrak
        $contract->customer_signature       = $sigRelativePath;
        $contract->customer_signer_name     = trim($request->input('signer_name'));
        $contract->customer_signer_position = trim($request->input('signer_position'));
        $contract->customer_signed_stamp    = $stampRelativePath;
        $contract->customer_ip              = $request->ip();
        $contract->signed_at                = Carbon::now();
        $contract->save();

        return response()->json([
            'status'   => 'success',
            'message'  => 'Kontrak berhasil ditandatangani.',
            'redirect' => route('contract.customer.sign', $contract->sign_token),
        ]);
    }

    /**
     * Reset tanda tangan customer (khusus internal admin/accounting).
     */
    public function resetSign(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);

        // Hapus file tanda tangan lama jika ada
        if ($contract->customer_signature && File::exists(public_path($contract->customer_signature))) {
            File::delete(public_path($contract->customer_signature));
        }
        if ($contract->customer_signed_stamp && File::exists(public_path($contract->customer_signed_stamp))) {
            File::delete(public_path($contract->customer_signed_stamp));
        }

        $contract->customer_signature       = null;
        $contract->customer_signer_name     = null;
        $contract->customer_signer_position = null;
        $contract->customer_signed_stamp    = null;
        $contract->customer_ip              = null;
        $contract->signed_at                = null;
        $contract->save();

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Tanda tangan customer berhasil di-reset.',
            ]);
        }

        return redirect()->back()->with('message', 'Tanda tangan customer berhasil di-reset.');
    }

    /**
     * Reset/Hapus tanda tangan oleh customer (jika customer salah mengisi/ingin tanda tangan ulang).
     */
    public function customerReset(Request $request, $token)
    {
        $contract = Contract::where('sign_token', $token)->firstOrFail();

        // Hapus file tanda tangan lama jika ada
        if ($contract->customer_signature && File::exists(public_path($contract->customer_signature))) {
            File::delete(public_path($contract->customer_signature));
        }
        if ($contract->customer_signed_stamp && File::exists(public_path($contract->customer_signed_stamp))) {
            File::delete(public_path($contract->customer_signed_stamp));
        }

        $contract->customer_signature       = null;
        $contract->customer_signer_name     = null;
        $contract->customer_signer_position = null;
        $contract->customer_signed_stamp    = null;
        $contract->customer_ip              = null;
        $contract->signed_at                = null;
        $contract->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Tanda tangan berhasil dihapus. Silakan bubuhkan tanda tangan baru.',
                'redirect' => route('contract.customer.sign', $contract->sign_token),
            ]);
        }

        return redirect()->route('contract.customer.sign', $contract->sign_token)
            ->with('message', 'Tanda tangan berhasil dihapus. Silakan bubuhkan tanda tangan baru.');
    }

    /**
     * Download/Lihat PDF kontrak resmi bagi customer.
     */
    public function downloadPdf($token)
    {
        $contract = Contract::where('sign_token', $token)->firstOrFail();
        $sellcon  = $contract;

        if ($contract->id_unit_quotation) {
            $unitQuote = UnitQuotation::with(['client', 'pic', 'details.unit', 'sales'])->find($contract->id_unit_quotation);
            return view('pages.accounting.contract.detail-print-unit', compact('sellcon', 'unitQuote'));
        }

        $quote = Quotation::where('id', $contract->id_quotation)->first();
        $subQuote = null;
        if ($quote->type !== 'Sparepart') {
            $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $quote->id)->get();
        }
        $tax    = ($quote->subtotal - $quote->diskon) * $quote->tax / 100;
        $dquote = DetailQuotation::where('id_quotation', $quote->id)->get();

        return view('pages.accounting.contract.detail-print', compact('subQuote', 'sellcon', 'quote', 'dquote', 'tax'));
    }
}
