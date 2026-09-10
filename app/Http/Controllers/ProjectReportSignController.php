<?php

namespace App\Http\Controllers;

use App\Models\ProjectReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProjectReportSignController extends Controller
{
    /**
     * Tampilan publik portal tanda tangan Project Report untuk Customer.
     * Menggunakan secure token unik sehingga customer tidak perlu login.
     */
    public function show($token)
    {
        $report = ProjectReport::with([
            'tasks',
            'materials',
            'equipments',
            'manpowers',
            'photos',
            'client',
            'kanbanTask.board',
            'creator'
        ])
            ->where('sign_token', $token)
            ->first();

        // Fallback jika token berupa ID (untuk backward compatibility)
        if (!$report && is_numeric($token)) {
            $report = ProjectReport::with([
                'tasks',
                'materials',
                'equipments',
                'manpowers',
                'photos',
                'client',
                'kanbanTask.board',
                'creator'
            ])->find($token);
            if ($report) {
                return redirect()->route('project-reports.customer.sign', $report->sign_token);
            }
        }

        if (!$report) {
            abort(404, 'Daily Project Report tidak ditemukan atau tautan sudah kedaluwarsa.');
        }

        return view('pages.customer.project-report-sign', [
            'report' => $report,
        ]);
    }

    /**
     * Memproses tanda tangan digital customer (Canvas Base64 PNG).
     */
    public function sign(Request $request, $token)
    {
        $report = ProjectReport::where('sign_token', $token)->firstOrFail();

        if ($report->isSignedByCustomer()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Laporan Proyek ini sudah ditandatangani sebelumnya.',
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
            'agreement.accepted'      => 'Anda harus menyetujui pernyataan konfirmasi laporan.',
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

        $uploadDir = public_path('asset/project-report/signatures/' . date('Y'));
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        $filename = 'sign_cust_' . $report->id . '_' . Str::random(12) . '.png';
        $fullPath = $uploadDir . '/' . $filename;
        File::put($fullPath, $decodedSig);
        $sigRelativePath = 'asset/project-report/signatures/' . date('Y') . '/' . $filename;

        // 2. Proses Stempel (Opsional)
        $stampRelativePath = null;
        if ($request->hasFile('stamp')) {
            $stampFile = $request->file('stamp');
            $stampFilename = 'stamp_cust_' . $report->id . '_' . Str::random(12) . '.' . $stampFile->getClientOriginalExtension();
            $stampFile->move($uploadDir, $stampFilename);
            $stampRelativePath = 'asset/project-report/signatures/' . date('Y') . '/' . $stampFilename;
        }

        // 3. Simpan data tanda tangan ke Project Report
        $report->customer_signature       = $sigRelativePath;
        $report->customer_signer_name     = trim($request->input('signer_name'));
        $report->customer_signer_position = $request->filled('signer_position') ? trim($request->input('signer_position')) : null;
        $report->customer_signed_stamp    = $stampRelativePath;
        $report->customer_ip              = $request->ip();
        $report->customer_signed_at       = Carbon::now();

        // Mirroring ke kolom lama agar kompatibel
        $report->client_sign              = $sigRelativePath;
        $report->client_pic_name          = trim($request->input('signer_name'));

        if ($report->status == 'draft' || empty($report->status)) {
            $report->status = 'approved';
        }

        $report->save();

        return response()->json([
            'status'   => 'success',
            'message'  => 'Daily Project Report berhasil ditandatangani.',
            'redirect' => route('project-reports.customer.sign', $report->sign_token),
        ]);
    }

    /**
     * Reset tanda tangan oleh customer pada halaman publik (jika ingin menandatangani ulang).
     */
    public function customerReset(Request $request, $token)
    {
        $report = ProjectReport::where('sign_token', $token)->firstOrFail();

        // Hapus file tanda tangan & stempel lama jika ada
        if ($report->customer_signature && File::exists(public_path($report->customer_signature))) {
            File::delete(public_path($report->customer_signature));
        }
        if ($report->customer_signed_stamp && File::exists(public_path($report->customer_signed_stamp))) {
            File::delete(public_path($report->customer_signed_stamp));
        }

        $report->customer_signature       = null;
        $report->customer_signer_name     = null;
        $report->customer_signer_position = null;
        $report->customer_signed_stamp    = null;
        $report->customer_ip              = null;
        $report->customer_signed_at       = null;
        $report->client_sign              = null;
        $report->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'   => 'success',
                'message'  => 'Tanda tangan berhasil dihapus. Silakan bubuhkan tanda tangan baru.',
                'redirect' => route('project-reports.customer.sign', $report->sign_token),
            ]);
        }

        return redirect()->route('project-reports.customer.sign', $report->sign_token)
            ->with('message', 'Tanda tangan berhasil dihapus. Silakan bubuhkan tanda tangan baru.');
    }

    /**
     * Reset tanda tangan customer dari sisi internal (Admin / Teknisi).
     */
    public function adminReset($id)
    {
        $report = ProjectReport::findOrFail($id);

        if ($report->customer_signature && File::exists(public_path($report->customer_signature))) {
            File::delete(public_path($report->customer_signature));
        }
        if ($report->customer_signed_stamp && File::exists(public_path($report->customer_signed_stamp))) {
            File::delete(public_path($report->customer_signed_stamp));
        }

        $report->customer_signature       = null;
        $report->customer_signer_name     = null;
        $report->customer_signer_position = null;
        $report->customer_signed_stamp    = null;
        $report->customer_ip              = null;
        $report->customer_signed_at       = null;
        $report->client_sign              = null;
        $report->save();

        return redirect()->back()->with('success', 'Tanda tangan client pada Project Report berhasil direset. Client dapat menandatangani ulang.');
    }
}
