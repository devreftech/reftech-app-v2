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
    public function index(Request $request)
    {
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');
        $lowStockCount = Product::whereRaw('stock <= COALESCE(min_stock, 5)')->where('stock', '>', 0)->count();
        $outOfStockCount = Product::where('stock', '<=', 0)->count();

        return view('pages.warehouse.stock.index', compact(
            'noSaleProspect',
            'totalProducts',
            'totalStock',
            'lowStockCount',
            'outOfStockCount'
        ));
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
        $product = Product::findOrFail($id);
        $details = DetailProduct::where('id_product', $id)->get();

        $product->first_stock = $request->first_stock ?? 0;
        $product->stock = $request->office_recent_stock ?? 0;
        $product->warehouse_stock = $request->warehouse_recent_stock ?? 0;
        $product->pending_stock = $request->pending_recent_stock ?? 0;
        $product->date = $request->date;
        $productSave = $product->save();

        if ($productSave && $details->count() > 0) {
            foreach ($details as $item => $detail) {
                $detail->stock = $request->office_stock[$item] ?? 0;
                $detail->warehouse_stock = $request->warehouse_stock[$item] ?? 0;
                $detail->save();
            }
        }

        return redirect('/product/' . $id)->with('message', 'Data stok berhasil diperbarui');
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
        $product = Unit::findOrFail($id);
        $details = DetailProduct::where('id_product', $id)->get();

        $product->first_stock = $request->first_stock ?? 0;
        $product->stock = $request->office_recent_stock ?? 0;
        $product->warehouse_stock = $request->warehouse_recent_stock ?? 0;
        $product->date = $request->date;
        $productSave = $product->save();

        if ($productSave && $details->count() > 0) {
            foreach ($details as $item => $detail) {
                $detail->stock = $request->office_stock[$item] ?? 0;
                $detail->warehouse_stock = $request->warehouse_stock[$item] ?? 0;
                $detail->save();
            }
        }

        return redirect('/unit/' . $id)->with('message', 'Data stok berhasil diperbarui');
    }
}
