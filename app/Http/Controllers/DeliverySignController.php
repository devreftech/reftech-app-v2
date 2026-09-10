<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DetailDelivery;
use App\Models\UnitQuotation;
use App\Models\Invoice;
use App\Models\Suo;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DeliverySignController extends Controller
{
    /**
     * Tampilan publik portal tanda tangan Surat Jalan (Delivery Order) untuk Customer / Penerima.
     * Menggunakan secure token unik sehingga customer tidak perlu login.
     */
    public function show($token)
    {
        $delivery = Delivery::with('detail')
            ->where('sign_token', $token)
            ->first();

        // Fallback jika token berupa ID (untuk backward compatibility)
        if (!$delivery && is_numeric($token)) {
            $delivery = Delivery::with('detail')->find($token);
            if ($delivery) {
                return redirect()->route('delivery.customer.sign', $delivery->sign_token);
            }
        }

        if (!$delivery) {
            abort(404, 'Surat Jalan (Delivery Order) tidak ditemukan atau tautan sudah kedaluwarsa.');
        }

        $unitQuote = null;
        $invoice   = null;
        $suo       = null;
        $client    = null;

        if ($delivery->id_unit_quotation) {
            $unitQuote = UnitQuotation::with(['client', 'pic', 'sales', 'details.unit', 'details.equivalent.product'])->find($delivery->id_unit_quotation);
            $client    = $unitQuote?->client;
            $invoice   = $delivery->id_invoice ? Invoice::find($delivery->id_invoice) : null;
        } elseif ($delivery->id_invoice) {
            $invoice   = Invoice::with(['quotation.client', 'quotation.pic'])->find($delivery->id_invoice);
            $client    = $invoice?->quotation?->client;
        } elseif ($delivery->id_suo) {
            $suo       = Suo::with(['detail', 'sales'])->find($delivery->id_suo);
            $client    = Client::where('company', $suo?->company)->first();
        }

        $address = $client ? ($delivery->destination == '1' ? $client->address : $client->subAddress) : '-';
        $doNumber = $invoice?->no_invoice ?? $unitQuote?->no_quote ?? ('SJ-' . $delivery->id);
        $isKojisha = ($client?->info === 'Kojisha');
        $entityFullName = $isKojisha ? 'PT Kojisha Innotiv Indonesia' : 'PT Reftech Jaya Optima';

        return view('pages.customer.delivery-sign', compact(
            'delivery', 'unitQuote', 'invoice', 'suo', 'client', 'address', 'doNumber', 'isKojisha', 'entityFullName'
        ));
    }

    /**
     * Memproses tanda tangan digital penerima / customer (Canvas Base64 PNG).
     */
    public function sign(Request $request, $token)
    {
        $delivery = Delivery::where('sign_token', $token)->firstOrFail();

        if ($delivery->isSignedByCustomer()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Surat Jalan ini sudah ditandatangani sebelumnya.',
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
            'agreement.accepted'      => 'Anda harus menyetujui konfirmasi penerimaan barang.',
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

        $uploadDir = public_path('asset/delivery/signatures/' . date('Y'));
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        $filename = 'sign_cust_' . $delivery->id . '_' . Str::random(12) . '.png';
        $fullPath = $uploadDir . '/' . $filename;
        File::put($fullPath, $decodedSig);
        $sigRelativePath = 'asset/delivery/signatures/' . date('Y') . '/' . $filename;

        // 2. Proses Stempel (Opsional)
        $stampRelativePath = null;
        if ($request->hasFile('stamp')) {
            $stampFile = $request->file('stamp');
            $stampFilename = 'stamp_cust_' . $delivery->id . '_' . Str::random(12) . '.' . $stampFile->getClientOriginalExtension();
            $stampFile->move($uploadDir, $stampFilename);
            $stampRelativePath = 'asset/delivery/signatures/' . date('Y') . '/' . $stampFilename;
        }

        // 3. Simpan data tanda tangan ke Delivery
        $delivery->customer_signature       = $sigRelativePath;
        $delivery->customer_signer_name     = trim($request->input('signer_name'));
        $delivery->customer_signer_position = $request->filled('signer_position') ? trim($request->input('signer_position')) : null;
        $delivery->customer_signed_stamp    = $stampRelativePath;
        $delivery->customer_ip              = $request->ip();
        $delivery->customer_signed_at       = Carbon::now();
        $delivery->save();

        return response()->json([
            'status'   => 'success',
            'message'  => 'Surat Jalan berhasil ditandatangani.',
            'redirect' => route('delivery.customer.sign', $delivery->sign_token),
        ]);
    }

    /**
     * Reset tanda tangan oleh customer pada halaman publik.
     */
    public function customerReset(Request $request, $token)
    {
        $delivery = Delivery::where('sign_token', $token)->firstOrFail();

        if ($delivery->customer_signature && File::exists(public_path($delivery->customer_signature))) {
            File::delete(public_path($delivery->customer_signature));
        }
        if ($delivery->customer_signed_stamp && File::exists(public_path($delivery->customer_signed_stamp))) {
            File::delete(public_path($delivery->customer_signed_stamp));
        }

        $delivery->customer_signature       = null;
        $delivery->customer_signer_name     = null;
        $delivery->customer_signer_position = null;
        $delivery->customer_signed_stamp    = null;
        $delivery->customer_ip              = null;
        $delivery->customer_signed_at       = null;
        $delivery->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'   => 'success',
                'message'  => 'Tanda tangan berhasil dihapus. Silakan bubuhkan tanda tangan baru.',
                'redirect' => route('delivery.customer.sign', $delivery->sign_token),
            ]);
        }

        return redirect()->route('delivery.customer.sign', $delivery->sign_token)
            ->with('message', 'Tanda tangan berhasil dihapus. Silakan bubuhkan tanda tangan baru.');
    }

    /**
     * Reset tanda tangan customer dari sisi internal (Admin / Accounting).
     */
    public function resetSign($id)
    {
        $delivery = Delivery::findOrFail($id);

        if ($delivery->customer_signature && File::exists(public_path($delivery->customer_signature))) {
            File::delete(public_path($delivery->customer_signature));
        }
        if ($delivery->customer_signed_stamp && File::exists(public_path($delivery->customer_signed_stamp))) {
            File::delete(public_path($delivery->customer_signed_stamp));
        }

        $delivery->customer_signature       = null;
        $delivery->customer_signer_name     = null;
        $delivery->customer_signer_position = null;
        $delivery->customer_signed_stamp    = null;
        $delivery->customer_ip              = null;
        $delivery->customer_signed_at       = null;
        $delivery->save();

        return redirect()->back()->with('success', 'Tanda tangan penerima pada Surat Jalan berhasil direset.');
    }
}
