<?php

namespace App\Http\Controllers;

use App\Models\DetailProduct;
use App\Models\ItemProductSet;
use App\Models\Product;
use App\Models\ProductSet;
use App\Models\SerialProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductSetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.warehouse.product-set.index');
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
        $product = new Product();
        $product->commodity = $request->commodity;
        $product->description = $request->description;
        $product->detail_desc = $request->detail_desc ?? '-';
        $product->detail_desc = $request->detail_desc ?? '-';
        $product->go = '-';
        $product->category = '-';
        $product->dimension = '-';
        $product->unit = '-';
        $product->note = '-';
        $product->first_stock = 0;
        $product->warehouse_stock = 0;
        $product->stock = 0;
        $product->pending_stock = 0;
        $product->weight = 0;
        $product->date = Carbon::today();
        $product->save();

        $replace = new DetailProduct();
        $replace->id_product = $product->id;
        $replace->replacement = $request->commodity;
        $replace->modal = 0;
        $replace->warehouse_stock = 0;
        $replace->stock = 0;
        $replace->save();

        $equiv = new SerialProduct();
        $equiv->id_product = $product->id;
        $equiv->brand = "brand";
        $equiv->fxp_parts = "-";
        $equiv->pn = $request->commodity;
        $equiv->detail = $request->detail_desc;
        $equiv->rental = '0';
        $equiv->second = '0';
        $equiv->new = '0';
        $equiv->bar = '-';
        $equiv->air_cap = '-';
        $equiv->image = '-';
        $equiv->price = 0;
        $equiv->save();

        $productSet = new ProductSet();
        $productSet->id_product = $product->id;
        $productSet->save();

        return redirect('product-set/' . $productSet->id)->with('success', 'Data Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $productSet = ProductSet::find($id);
        $itemProduct = ItemProductSet::with('replacement.item')
            ->where('id_product_set', $id)
            ->get();
        $product = Product::find($productSet->id_product);
        $allStock = $product->stock + $product->warehouse_stock;
        $replacement = DetailProduct::all();
        return view('pages.warehouse.product-set.detail', compact('productSet', 'itemProduct', 'product', 'allStock', 'replacement'));
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
    public function store_item(Request $request, $id)
    {
        $replacement = DetailProduct::find($request->replacement);
        $productSet = ProductSet::find($id);
        $product = Product::find($productSet->id_product);
        $dProduct = DetailProduct::where('detail_product.id_product', $product->id)->first();
        $item = new ItemProductSet();
        $item->id_product_set = $id;
        $item->id_replacement = $request->replacement;
        $itemSave = $item->save();
        $itemProduct = ItemProductSet::with('replacement.item')->where('id_product_set', $id)->get();

        $allReplacements = $itemProduct
            ->map(fn($item) => $item->replacement)
            ->filter();
        $minStock = $allReplacements->min(function ($rep) {
            return $rep->stock + $rep->warehouse_stock;
        });
        // dd($allReplacements);
        $product->stock = $minStock;
        $dProduct->stock = $minStock;
        $product->save();
        $dProduct->save();
        if ($itemSave) {
            return redirect('product-set/' . $id)->with('success', 'Data Berhasil Ditambahkan');
        }
    }
}
