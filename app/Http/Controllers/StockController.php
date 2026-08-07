<?php

namespace App\Http\Controllers;

use App\Models\DetailProduct;
use App\Models\Product;
use App\Models\Prospect;
use App\Models\Unit;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        return view('pages.warehouse.stock.index',compact('noSaleProspect'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
        $product = Product::find($id);
        $details = DetailProduct::where('id_product', $id)->get();

        // dd($request->all());
        $product->first_stock = $request->first_stock;
        $product->stock = $request->office_recent_stock;
        $product->warehouse_stock = $request->warehouse_recent_stock;
        $product->pending_stock = $request->pending_recent_stock;
        $product->date = $request->date;
        $productSave = $product->save();
        if ($productSave) {
            foreach ($details as $item => $detail) {
                $detail->stock = $request->office_stock[$item];
                $detail->warehouse_stock = $request->warehouse_stock[$item];
                $detailSave = $detail->save();
            }
        }
        if ($detailSave) {
            return redirect('/product/' .$id)->with('message', 'data telah di tambahkan');
        }
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
    
    public function updateUnit(Request $request, $id)
    {
        $product = Unit::find($id);
        $details = DetailProduct::where('id_product', $id)->get();

        // dd($request->all());
        $product->first_stock = $request->first_stock;
        $product->stock = $request->office_recent_stock;
        $product->warehouse_stock = $request->warehouse_recent_stock;
        $product->date = $request->date;
        $productSave = $product->save();
        if ($productSave) {
            foreach ($details as $item => $detail) {
                $detail->stock = $request->office_stock[$item];
                $detail->warehouse_stock = $request->warehouse_stock[$item];
                $detailSave = $detail->save();
            }
        }
        if ($detailSave) {
            return redirect('/unit/' .$id)->with('message', 'data telah di tambahkan');
        }
    }
}
