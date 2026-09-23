<?php

namespace App\Http\Controllers;

use App\Models\ChangeWarehouse;
use App\Models\DetailChangeWarehouse;
use App\Models\DetailProduct;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChangeWarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transfers = ChangeWarehouse::with([
            'sender',
            'reciever',
            'details.replacement.product'
        ])
        ->orderBy('date', 'desc')
        ->orderBy('id', 'desc')
        ->get();

        $totalTransfer = $transfers->count();
        $inTransitCount = $transfers->where('status', '!=', 2)->count();
        $receivedCount = $transfers->where('status', 2)->count();
        $bksToBdgCount = $transfers->where('from', 'BKS')->count();
        $bdgToBksCount = $transfers->where('from', 'BDG')->count();
        $totalQty = $transfers->sum(function ($transfer) {
            return $transfer->details->sum('qty');
        });

        return view('pages.warehouse.changing.index', compact(
            'transfers',
            'totalTransfer',
            'inTransitCount',
            'receivedCount',
            'bksToBdgCount',
            'bdgToBksCount',
            'totalQty'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $detProduct = DetailProduct::with('product')
            ->whereHas('product')
            ->orderBy('id', 'asc')
            ->get();
        return view('pages.warehouse.changing.form', compact('detProduct'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'info' => 'required|in:BDG,BKS',
            'replacement' => 'required|array',
            'qty' => 'required|array',
        ]);

        return DB::transaction(function () use ($request) {
            $changing = new ChangeWarehouse();
            $changing->id_sender = Auth::user()->id;
            $changing->date = Carbon::now();
            $changing->status = 1;
            $changing->title = $request->title;
            $changing->kurir = $request->kurir ?? 'Internal Staff';
            $changing->note = $request->note ?? '-';
            $changing->to = $request->info;
            $changing->from = $request->info == 'BDG' ? 'BKS' : 'BDG';
            $changing->save();

            if ($request->has('replacement')) {
                foreach ($request->replacement as $item => $value) {
                    if (empty($value)) continue;
                    $qty = isset($request->qty[$item]) ? (int) $request->qty[$item] : 1;
                    if ($qty <= 0) continue;

                    $detChanging = new DetailChangeWarehouse();
                    $detChanging->id_change_warehouse = $changing->id;
                    $detChanging->id_replacement = $value;
                    $detChanging->qty = $qty;
                    $detChanging->save();
                }
            }

            return redirect()->route('change-warehouse.index')->with('success', 'Transfer antar gudang #' . str_pad($changing->id, 4, '0', STR_PAD_LEFT) . ' berhasil dibuat.');
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $change = ChangeWarehouse::with(['sender', 'reciever'])->findOrFail($id);
        $detChange = DetailChangeWarehouse::with(['replacement.product'])
            ->where('id_change_warehouse', $id)
            ->get();

        return view('pages.warehouse.changing.detail', compact('detChange', 'change'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function accept(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $change = ChangeWarehouse::findOrFail($id);
            $change->id_reciever = Auth::user()->id;
            $change->date_recieve = Carbon::today();
            $change->note_recieve = $request->note;
            $change->status = 2;
            $change->save();

            $detChange = DetailChangeWarehouse::where('id_change_warehouse', $id)->get();
            foreach ($detChange as $detail) {
                $detProduct = DetailProduct::find($detail->id_replacement);
                if ($detProduct) {
                    $product = Product::find($detProduct->id_product);
                    if ($change->to == 'BKS') {
                        $detProduct->stock -= $detail->qty;
                        $detProduct->warehouse_stock += $detail->qty;
                        if ($product) {
                            $product->stock -= $detail->qty;
                            $product->warehouse_stock += $detail->qty;
                            $product->save();
                        }
                    } else {
                        $detProduct->stock += $detail->qty;
                        $detProduct->warehouse_stock -= $detail->qty;
                        if ($product) {
                            $product->stock += $detail->qty;
                            $product->warehouse_stock -= $detail->qty;
                            $product->save();
                        }
                    }
                    $detProduct->save();
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Barang transfer berhasil diterima dan stok telah diperbarui.'
                ]);
            }

            return redirect()->back()->with('success', 'Barang transfer berhasil diterima dan stok telah diperbarui.');
        });
    }
}
