<?php

namespace App\Http\Controllers;

use App\Models\DetailPurchaseOrder;
use App\Models\PurchaseOrder;
use App\Models\RentalAccessory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RentalAccessoryReceiptController extends Controller
{
    /**
     * Show Goods Receipt form for Rental Accessories.
     */
    public function goodsReceiptForm($po)
    {
        $purchase = PurchaseOrder::with('supplier')->findOrFail($po);
        $detail = DetailPurchaseOrder::where('id_purchase_order', $po)
            ->with('rentalAccessory')
            ->get();

        $previewNoGr = $this->generateNoGr();
        $categories = RentalAccessory::categories();

        return view('pages.warehouse.rental-accessories.goods-receipt', compact('purchase', 'detail', 'previewNoGr', 'categories'));
    }

    /**
     * Store Goods Receipt for Rental Accessories.
     */
    public function storeGoodsReceipt(Request $request, $po)
    {
        $purchase = PurchaseOrder::findOrFail($po);

        $request->validate([
            'gr_date' => 'required|date',
            'detail_id' => 'required|array',
            'detail_id.*' => 'required|integer|exists:detail_purchase_order,id',
            'qty_received' => 'required|array',
            'qty_received.*' => 'required|integer|min:0',
            'category' => 'required|array',
            'category.*' => 'required|string|in:hose,header,reducer,cable,nipple',
            'condition' => 'required|array',
            'condition.*' => 'required|string|in:ok,fair,damaged',
        ], [
            'gr_date.required' => 'Tanggal Penerimaan Barang Wajib Diisi',
            'qty_received.*.required' => 'Jumlah diterima wajib diisi',
        ]);

        DB::transaction(function () use ($request, $purchase) {
            foreach ($request->detail_id as $key => $detailId) {
                $poDetail = DetailPurchaseOrder::findOrFail($detailId);
                $qtyReceived = (int) ($request->qty_received[$key] ?? 0);
                if ($qtyReceived <= 0) continue;

                $category = $request->category[$key] ?? 'hose';
                $condition = $request->condition[$key] ?? 'ok';
                $location = $request->location[$key] ?? null;
                $code = $request->code[$key] ?? null;
                $size = $request->size[$key] ?? null;
                $length = $request->length[$key] ?? null;
                $maxPressure = $request->max_pressure[$key] ?? null;
                $connection = $request->connection[$key] ?? null;
                $material = $request->material[$key] ?? null;
                $extraSpec = $request->extra_spec[$key] ?? null;

                // 1. Jika sudah ada referensi id_rental_accessory di PO Detail
                if ($poDetail->id_rental_accessory) {
                    $accessory = RentalAccessory::find($poDetail->id_rental_accessory);
                    if ($accessory) {
                        $accessory->stock += $qtyReceived;
                        $accessory->condition = $condition;
                        if ($location) $accessory->location = $location;
                        if (!$accessory->id_purchase_order) $accessory->id_purchase_order = $purchase->id;
                        $accessory->save();
                    }
                } else {
                    // 2. Cek apakah ada accessory dengan code/nama yang sama
                    $accessory = null;
                    if ($code) {
                        $accessory = RentalAccessory::where('code', $code)->first();
                    }

                    if ($accessory) {
                        $accessory->stock += $qtyReceived;
                        $accessory->condition = $condition;
                        if ($location) $accessory->location = $location;
                        if (!$accessory->id_purchase_order) $accessory->id_purchase_order = $purchase->id;
                        $accessory->save();

                        $poDetail->id_rental_accessory = $accessory->id;
                        $poDetail->save();
                    } else {
                        // 3. Buat entri baru di rental_accessories
                        $newAccessory = RentalAccessory::create([
                            'category' => $category,
                            'id_purchase_order' => $purchase->id,
                            'code' => $code,
                            'name' => $poDetail->product ?: ($categories[$category] ?? 'Aksesoris Rental'),
                            'size' => $size,
                            'length' => $length,
                            'max_pressure' => $maxPressure,
                            'connection' => $connection,
                            'material' => $material,
                            'extra_spec' => $extraSpec,
                            'stock' => $qtyReceived,
                            'condition' => $condition,
                            'rental_status' => 'available',
                            'location' => $location,
                            'notes' => 'Masuk via GR PO ' . $purchase->no_po . ' pada ' . date('d/m/Y', strtotime($request->gr_date)),
                        ]);

                        $poDetail->id_rental_accessory = $newAccessory->id;
                        $poDetail->save();
                    }
                }
            }

            // Simpan status / tanggal GR pada PO
            if (empty($purchase->no_gr)) {
                $purchase->no_gr = $this->generateNoGr();
            }
            if (empty($purchase->gr_sent_at)) {
                $purchase->gr_sent_at = now();
            }
            $purchase->save();
        });

        return redirect()->route('unit-acquisition.index')->with('success', 'Goods Receipt Aksesoris berhasil disimpan! Stok aksesoris rental telah diperbarui.');
    }

    private function generateNoGr(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $prefix = "GR-ACC/{$year}/{$month}/";
        $last = PurchaseOrder::where('no_gr', 'like', $prefix . '%')
            ->orderByDesc('no_gr')
            ->value('no_gr');

        $lastSeq = $last ? (int) substr($last, -4) : 0;
        $nextSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $nextSeq;
    }
}
