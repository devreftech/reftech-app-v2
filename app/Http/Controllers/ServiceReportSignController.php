<?php

namespace App\Http\Controllers;

use App\Models\Reports;
use App\Models\ReportsPict;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ServiceReportSignController extends Controller
{
    /**
     * Tampilan publik portal tanda tangan Service Report untuk Customer / PIC.
     * Menggunakan secure token unik sehingga customer tidak perlu login.
     */
    public function show($token)
    {
        $service = Reports::with(['pic.client', 'machine', 'technician', 'picture'])
            ->where('sign_token', $token)
            ->first();

        // Fallback jika token berupa ID (untuk backward compatibility)
        if (!$service && is_numeric($token)) {
            $service = Reports::with(['pic.client', 'machine', 'technician', 'picture'])->find($token);
            if ($service) {
                return redirect()->route('service-report.customer.sign', $service->sign_token);
            }
        }

        if (!$service) {
            abort(404, 'Laporan Servis tidak ditemukan atau tautan sudah kedaluwarsa.');
        }

        $pict = ReportsPict::where('id_reports', $service->id)->get();

        return view('pages.customer.service-report-sign', [
            'service' => $service,
            'pict'    => $pict,
        ]);
    }

    /**
     * Memproses tanda tangan digital customer (Canvas Base64 PNG).
     */
    public function sign(Request $request, $token)
    {
        $service = Reports::where('sign_token', $token)->firstOrFail();

        if ($service->isSignedByCustomer()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Laporan Servis ini sudah ditandatangani sebelumnya.',
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
            'agreement.accepted'      => 'Anda harus menyetujui pernyataan konfirmasi pekerjaan servis.',
            'stamp.image'             => 'File stempel harus berupa gambar.',
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

        $uploadDir = public_path('asset/service-report/signatures/' . date('Y'));
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        $filename = 'sign_' . $service->id . '_' . Str::random(12) . '.png';
        $fullPath = $uploadDir . '/' . $filename;
        File::put($fullPath, $decodedSig);
        $sigRelativePath = 'asset/service-report/signatures/' . date('Y') . '/' . $filename;

        // 2. Proses Stempel (Opsional)
        $stampRelativePath = null;
        if ($request->hasFile('stamp')) {
            $stampFile = $request->file('stamp');
            $stampFilename = 'stamp_' . $service->id . '_' . Str::random(12) . '.' . $stampFile->getClientOriginalExtension();
            $stampFile->move($uploadDir, $stampFilename);
            $stampRelativePath = 'asset/service-report/signatures/' . date('Y') . '/' . $stampFilename;
        }

        // 3. Simpan data tanda tangan ke report
        $service->customer_signature       = $sigRelativePath;
        $service->sign_client              = $sigRelativePath; // backward-compatibility
        $service->customer_signer_name     = trim($request->input('signer_name'));
        $service->customer_signer_position = $request->filled('signer_position') ? trim($request->input('signer_position')) : null;
        $service->customer_signed_stamp    = $stampRelativePath;
        $service->customer_ip              = $request->ip();
        $service->signed_at                = Carbon::now();
        $service->save();

        return response()->json([
            'status'   => 'success',
            'message'  => 'Laporan Servis berhasil ditandatangani.',
            'redirect' => route('service-report.customer.sign', $service->sign_token),
        ]);
    }

    /**
     * Reset tanda tangan oleh customer (jika customer ingin tanda tangan ulang).
     */
    public function customerReset(Request $request, $token)
    {
        $service = Reports::where('sign_token', $token)->firstOrFail();

        // Hapus file tanda tangan lama jika ada
        if ($service->customer_signature && File::exists(public_path($service->customer_signature))) {
            File::delete(public_path($service->customer_signature));
        }
        if ($service->customer_signed_stamp && File::exists(public_path($service->customer_signed_stamp))) {
            File::delete(public_path($service->customer_signed_stamp));
        }

        $service->customer_signature       = null;
        $service->sign_client              = null;
        $service->customer_signer_name     = null;
        $service->customer_signer_position = null;
        $service->customer_signed_stamp    = null;
        $service->customer_ip              = null;
        $service->signed_at                = null;
        $service->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'   => 'success',
                'message'  => 'Tanda tangan berhasil dihapus. Silakan bubuhkan tanda tangan baru.',
                'redirect' => route('service-report.customer.sign', $service->sign_token),
            ]);
        }

        return redirect()->route('service-report.customer.sign', $service->sign_token)
            ->with('message', 'Tanda tangan berhasil dihapus. Silakan bubuhkan tanda tangan baru.');
    }

    /**
     * Reset tanda tangan customer (khusus internal admin/teknisi).
     */
    public function resetSign(Request $request, $id)
    {
        $service = Reports::findOrFail($id);

        if ($service->customer_signature && File::exists(public_path($service->customer_signature))) {
            File::delete(public_path($service->customer_signature));
        }
        if ($service->customer_signed_stamp && File::exists(public_path($service->customer_signed_stamp))) {
            File::delete(public_path($service->customer_signed_stamp));
        }

        $service->customer_signature       = null;
        $service->sign_client              = null;
        $service->customer_signer_name     = null;
        $service->customer_signer_position = null;
        $service->customer_signed_stamp    = null;
        $service->customer_ip              = null;
        $service->signed_at                = null;
        $service->save();

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Tanda tangan customer berhasil di-reset.',
            ]);
        }

        return redirect()->back()->with('message', 'Tanda tangan customer berhasil di-reset.');
    }

    /**
     * Download / Print PDF Laporan Servis resmi bagi customer.
     */
    public function downloadPdf($token)
    {
        $service = Reports::with(['pic.client', 'machine', 'technician', 'picture'])
            ->where('sign_token', $token)
            ->firstOrFail();

        $pict = ReportsPict::where('id_reports', $service->id)->get();

        return view('pages.technician.service-reports.detail-print', compact('service', 'pict'));
    }
}
