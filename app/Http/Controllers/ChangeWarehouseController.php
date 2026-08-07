<?php

namespace App\Http\Controllers;

use App\Models\ChangeWarehouse;
use App\Models\DetailChangeWarehouse;
use App\Models\DetailProduct;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChangeWarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.warehouse.changing.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $detProduct = DetailProduct::join('product', 'detail_product.id_product', '=', 'product.id')->get('detail_product.*');
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
        // dd($request->all());
        $changing = new ChangeWarehouse();
        $changing->id_sender = Auth::user()->id;
        $changing->date = Carbon::now();
        $changing->status = 1;
        $changing->title = $request->title;
        $changing->kurir = $request->kurir;
        $changing->note = $request->note;
        $changing->to = $request->info;
        $changing->from = $request->info == 'BDG' ? 'BKS' : 'BDG';
        $changingSave = $changing->save();

        foreach ($request->replacement as $item => $value) {
            $detChanging = new DetailChangeWarehouse();
            $detChanging->id_change_warehouse = $changing->id;
            $detChanging->id_replacement = $request->replacement[$item];
            $detChanging->qty = $request->qty[$item];
            $detChanging->save();
        }
        if ($changingSave) {
            return redirect('/change-warehouse')->with('success', 'Data telah ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $change = ChangeWarehouse::find($id);
        $detChange = DetailChangeWarehouse::where('id_change_warehouse', $id)->get();
        foreach ($detChange as $detail) {
            $detProduct = DetailProduct::find($detail->id_replacement);
            if (!$detProduct) {
                dd('Detail product not found', $detail->id_replacement);
            }

            $product = Product::find($detProduct->id_product);
            if (!$product) {
                dd('Product not found', $detProduct->id_product);
            }
        }
        // dd($change->to);
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
        $change = ChangeWarehouse::find($id);
        $change->id_reciever = Auth::user()->id;
        $change->date_recieve = Carbon::today();
        $change->note_recieve = $request->note;
        $change->status = 2;
        $changeSave = $change->save();
        $detChange = DetailChangeWarehouse::where('id_change_warehouse', $id)->get();
        foreach ($detChange as $detail) {
            $detProduct = DetailProduct::find($detail->id_replacement);
            $product = Product::find($detProduct->id_product);
            if ($change->to == 'BKS') {
                $detProduct->stock -= $detail->qty;
                $detProduct->warehouse_stock += $detail->qty;

                $product->stock -= $detail->qty;
                $product->warehouse_stock += $detail->qty;
            } else {
                $detProduct->stock += $detail->qty;
                $detProduct->warehouse_stock -= $detail->qty;

                $product->stock += $detail->qty;
                $product->warehouse_stock -= $detail->qty;
            }
            $detProduct->save();
            $product->save();
        }
        if ($changeSave) {
            return redirect('/change-warehouse')->with('success', 'Data telah ditambahkan');
        }
    }
}
