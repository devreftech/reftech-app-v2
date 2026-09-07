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
                'sellcon'   => $contract,
                'isUnit'    => true,
                'unitQuote' => $unitQuote,
                'quote'     => null,
                'dquote'    => null,
                'subQuote'  => null,
                'tax'       => null,
            ]);
        }

        $quote = Quotation::with(['pic.client', 'sales', 'termncon'])->find($contract->id_quotation);
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
            'sellcon'   => $contract,
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
            'signer_position' => 'nullable|string|max:255',
            'agreement'       => 'accepted',
            'stamp'           => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
        ], [
            'signature_data.required'  => 'Goresan tanda tangan wajib dibubuhkan.',
            'signer_name.required'     => 'Nama lengkap penandatangan wajib diisi.',
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
        $contract->customer_signer_position = $request->filled('signer_position') ? trim($request->input('signer_position')) : null;
        $contract->customer_signed_stamp    = $stampRelativePath;
        $contract->customer_ip              = $request->ip();
        $contract->signed_at                = Carbon::now();
        $contract->save();

        // 4. Otomatisasi PO & Notifikasi saat Customer TTD Kontrak Resmi
        if ($contract->id_unit_quotation) {
            $quote = UnitQuotation::with(['client', 'pic', 'details'])->find($contract->id_unit_quotation);
            if ($quote) {
                $accUserIds = \App\Models\User::getAccountingRecipientsForSales($quote->id_sales, true);
                $allTargetUserIds = array_unique(array_merge($accUserIds, $quote->id_sales ? [$quote->id_sales] : []));
                foreach ($allTargetUserIds as $userId) {
                    \App\Models\UnitQuotationPaymentNotification::create([
                        'id_unit_quotation' => $quote->id,
                        'id_user' => $userId,
                        'type' => 'contract_signed',
                        'is_read' => false,
                    ]);
                }

                // Otomatisasi Upload PO jika quotation belum dalam status po_received
                if ($quote->status !== 'po_received') {
                    $parsed = self::parsePaymentTerm($quote->payment);
                    $contractDocNoun = $contract->type === 'Order' ? 'Confirm Order' : 'Selling Contract';

                    $quote->update([
                        'po_number'      => $contract->no_contract,
                        'po_file'        => route('contract.print', $contract->id),
                        'payment_method' => $parsed['payment_method'],
                        'status'         => 'po_received',
                        'po_received'    => $contract->signed_at ? $contract->signed_at->toDateString() : now()->toDateString(),
                        'type'           => 'Project',
                    ]);

                    $quote->statusHistory()->create([
                        'status' => 'po_received',
                        'note'   => "PO otomatis terbit dari {$contractDocNoun} ({$contract->no_contract}) yang telah ditandatangani oleh customer ({$contract->customer_signer_name}).",
                    ]);

                    // Update Client issue & status
                    $client = $quote->client;
                    if ($client && ($client->id_issues != "5" || $client->role !== 'Customers')) {
                        $client->id_issues = '5';
                        $client->role = 'Customers';
                        $client->save();

                        \App\Models\CrmStatus::create([
                            'id_client' => $client->id,
                            'status'    => 2,
                        ]);
                    }

                    // Buat Sales Order (Pending PO)
                    $unitQuotationController = app(\App\Http\Controllers\UnitQuotationController::class);
                    $pending = $unitQuotationController->createPendingPoForUnitQuotation($quote);

                    // Buat Invoice Request Pertama (DP atau Full Payment)
                    $invoice = \App\Models\Invoice::create([
                        'id_unit_quotation' => $quote->id,
                        'no_po'             => $quote->po_number,
                        'flag'              => (optional($quote->client)->info === 'Kojisha' || str_contains((string) $quote->no_quote, 'KII')) ? 'Kojisha' : 'Reftech',
                        'pph'               => 0,
                        'type'              => $parsed['invoice_type'],
                        'percent'           => $parsed['invoice_type'] === 'DP' ? floatval($parsed['dp_percent'] ?? 50) : 100,
                    ]);

                    foreach ($accUserIds as $userId) {
                        \App\Models\UnitQuotationPaymentNotification::create([
                            'id_invoice' => $invoice->id,
                            'id_unit_quotation' => $quote->id,
                            'id_user' => $userId,
                            'type' => 'invoice_requested',
                            'is_read' => false,
                        ]);
                    }
                }
            }
        }

        // Quotation Layanan/Parts Lama
        if ($contract->id_quotation) {
            $quote = Quotation::with(['pic.client', 'termncon'])->find($contract->id_quotation);
            if ($quote && $quote->status != '100') {
                $rawPayment = $quote->termncon[0]->payment ?? '';
                $parsed = self::parsePaymentTerm($rawPayment);

                $quote->status = '100';
                $quote->po_number = $contract->no_contract;
                $quote->po_file = route('contract.print', $contract->id);
                $quote->upload_date = Carbon::today();
                $quote->save();

                $existingInv = \App\Models\Invoice::where('id_quotation', $quote->id)->first();
                if (!$existingInv) {
                    $invoice = new \App\Models\Invoice();
                    $invoice->id_quotation = $quote->id;
                    $invoice->no_po = $contract->no_contract;
                    $invoice->flag = $quote->pic?->client?->info ?? 'Reftech';
                    $invoice->type = $parsed['invoice_type'];
                    $invoice->percent = $parsed['invoice_type'] === 'DP' ? floatval($parsed['dp_percent'] ?? 50) : 100;
                    $invoice->save();
                }

                $client = $quote->pic?->client;
                if ($client && ($client->id_issues != "5" || $client->role !== 'Customers')) {
                    $client->id_issues = '5';
                    $client->role = 'Customers';
                    $client->save();

                    \App\Models\CrmStatus::create([
                        'id_client' => $client->id,
                        'status'    => 2,
                    ]);
                }
            }
        }

        return response()->json([
            'status'   => 'success',
            'message'  => 'Kontrak berhasil ditandatangani dan PO otomatis diproses.',
            'redirect' => route('contract.customer.sign', $contract->sign_token),
        ]);
    }

    /**
     * Helper parsing syarat pembayaran (Payment Term) ke payment_method, invoice_type, dan dp_percent.
     */
    public static function parsePaymentTerm(?string $rawPayment): array
    {
        $raw = trim($rawPayment ?? '');
        if (empty($raw)) {
            return [
                'payment_method' => 'CBD',
                'invoice_type'   => 'CT',
                'dp_percent'     => null,
            ];
        }

        // DP X%
        if (preg_match('/DP\s*(\d+)%/i', $raw, $m)) {
            $dp = (int) $m[1];
            $pelunasan = 100 - $dp;
            return [
                'payment_method' => "DP {$dp}% & Pelunasan NET {$pelunasan}",
                'invoice_type'   => 'DP',
                'dp_percent'     => $dp,
            ];
        }

        // CBD
        if (stripos($raw, 'CBD') !== false || stripos($raw, 'Cash Before') !== false) {
            return [
                'payment_method' => 'CBD',
                'invoice_type'   => 'CT',
                'dp_percent'     => null,
            ];
        }

        // COD
        if (stripos($raw, 'COD') !== false || stripos($raw, 'Cash On') !== false) {
            return [
                'payment_method' => 'COD',
                'invoice_type'   => 'CT',
                'dp_percent'     => null,
            ];
        }

        // Tempo X Hari
        if (preg_match('/(?:Tempo|Net)\s*(\d+)\s*(?:Hari|Days)?/i', $raw, $m)) {
            $days = (int) $m[1];
            return [
                'payment_method' => "Tempo {$days} Hari",
                'invoice_type'   => 'CT',
                'dp_percent'     => null,
            ];
        }

        if (stripos($raw, 'Tempo') !== false) {
            return [
                'payment_method' => 'Tempo',
                'invoice_type'   => 'CT',
                'dp_percent'     => null,
            ];
        }

        return [
            'payment_method' => $raw ?: 'CBD',
            'invoice_type'   => 'CT',
            'dp_percent'     => null,
        ];
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
