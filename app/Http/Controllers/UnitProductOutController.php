<?php

namespace App\Http\Controllers;

use App\Models\DetailUnitProductOut;
use App\Models\FixedAsset;
use App\Models\UnitInventory;
use App\Models\UnitProductOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnitProductOutController extends Controller
{
    public function index()
    {
        $unitProductOuts = UnitProductOut::with('detail')->orderByDesc('id')->get();
        return view('pages.warehouse.unit-product-out.index', compact('unitProductOuts'));
    }

    public function create()
    {
        $unitInventories = UnitInventory::where('status', 'available')->with('unit')->get();
        $fixedAssets = FixedAsset::where('type', 'Mesin')
            ->where('is_disposed', false)
            ->where('qc_status', '!=', 'checking')
            ->with('unit')
            ->get();
        $nextNoTransaksi = $this->generateNoTransaksi();
        return view('pages.warehouse.unit-product-out.form', compact('unitInventories', 'fixedAssets', 'nextNoTransaksi'));
    }

    private function generateNoTransaksi(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $prefix = "UOUT/{$year}/{$month}/";

        $last = UnitProductOut::where('no_transaksi', 'like', $prefix . '%')
            ->orderByDesc('no_transaksi')
            ->value('no_transaksi');

        $lastSeq = $last ? (int) substr($last, -3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $nextSeq;
    }

    public function store(Request $request)
    {
        $rule = [
            'date' => 'required|date',
            'source_type' => 'required|array',
            'source_type.*' => 'required|in:unit_inventory,fixed_asset',
            'source_id' => 'required|array',
            'harga_jual' => 'required|array',
        ];
        $this->validate($request, $rule);

        $unitProductOut = DB::transaction(function () use ($request) {
            $unitProductOut = new UnitProductOut();
            $unitProductOut->no_transaksi = $this->generateNoTransaksi();
            $unitProductOut->date = $request->date;
            $unitProductOut->customer = $request->customer;
            $unitProductOut->note = $request->note;
            $unitProductOut->created_by = Auth::id();
            $unitProductOut->save();

            foreach ($request->source_type as $key => $sourceType) {
                $sourceId = $request->source_id[$key];
                $hargaJual = $request->harga_jual[$key];

                $detail = new DetailUnitProductOut();
                $detail->id_unit_product_out = $unitProductOut->id;
                $detail->source_type = $sourceType;
                $detail->harga_jual = $hargaJual;

                if ($sourceType == 'unit_inventory') {
                    $inventory = UnitInventory::findOrFail($sourceId);
                    $detail->id_unit_inventory = $inventory->id;
                    $detail->nilai_pokok = $inventory->total_modal;
                    $detail->selisih = $hargaJual - $inventory->total_modal;

                    $inventory->status = 'sold';
                    $inventory->save();
                } else {
                    $fixed = FixedAsset::findOrFail($sourceId);
                    $hitung = $fixed->hitungNilaiBuku();
                    $nilaiBuku = $hitung['nilai_buku'];

                    $detail->id_fixed_asset = $fixed->id;
                    $detail->nilai_pokok = $nilaiBuku;
                    $detail->selisih = $hargaJual - $nilaiBuku;

                    $fixed->is_disposed = true;
                    $fixed->tanggal_disposal = $request->date;
                    $fixed->nilai_buku_disposal = $nilaiBuku;
                    $fixed->harga_jual_final = $hargaJual;
                    $fixed->status_unit = 'Sold';
                    $fixed->save();
                }

                $detail->save();
            }

            return $unitProductOut;
        });

        return redirect('/unit-product-out')->with('success', 'Unit keluar berhasil disimpan: ' . $unitProductOut->no_transaksi);
    }
}
