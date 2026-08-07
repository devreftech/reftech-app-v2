<?php

namespace App\Http\Controllers;

use App\Models\DetailProduct;
use App\Models\DetailProductOut;
use App\Models\DetailQuotation;
use App\Models\Invoice;
use App\Models\ItemProductSet;
use App\Models\Product;
use App\Models\ProductOut;
use App\Models\ProductSet;
use App\Models\Prospect;
use App\Models\SerialProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        return view('pages.warehouse.product-out.index', compact('noSaleProspect'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = SerialProduct::join('product', 'serial_product.id_product', '=', 'product.id')->get('serial_product.*');
        return view('pages.warehouse.product-out.form', compact('product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rule = [
            'invoice' => 'required',
            'detail_client' => 'required',
            'vers' => 'required',
            'date' => 'required',
            'shipping' => 'required',
            'note' => 'required',
        ];
        $message = [
            'invoice.required' => 'Field No Invoice Wajib Diisi',
            'detail_client.required' => 'Field Detail Client Wajib Diisi',
            'vers.required' => 'Field Offline / Online Wajib Diisi',
            'date.required' => 'Field Date Wajib Diisi',
            'shipping.required' => 'Field Shipping Wajib Diisi',
            'note.required' => 'Field Note Wajib Diisi',
        ];
        // dd($request->all());
        $this->validate($request, $rule, $message);
        // Masukan Data ke Tabel Product Out
        $productOut = new ProductOut();
        $productOut->id_user = Auth::user()->id;
        $productOut->invoice = $request->invoice;
        $productOut->po = $request->po;
        $productOut->no_type = "1";
        $productOut->detail_client = $request->detail_client;
        $productOut->vers = $request->vers;
        $productOut->date = $request->date;
        $productOut->note = $request->note;
        $productOut->shipping = $request->shipping;
        $productOut->total = $request->total;
        $productOutSave = $productOut->save();
        if ($productOutSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            foreach ($request->equivalent as $item => $value) {
                $dProduct = DetailProduct::findOrFail($request->replacement[$item]);
                $productSet = ProductSet::where('id_product', $dProduct->id_product)->first();

                if ($productSet) {

                    $items = ItemProductSet::where('id_product_set', $productSet->id)->get();

                    foreach ($items as $psItem) {

                        $replacement = DetailProduct::find($psItem->id_replacement);

                        if ($replacement) {
                            $replacement->stock -= $request->qty[$item];
                            $replacement->save();
                        }
                    }
                }
                $dProductIn = new DetailProductOut;
                $dProductIn->id_product_out = $productOut->id;
                $dProductIn->id_detail_product = $request->replacement[$item];
                $dProductIn->id_serial_product = $request->equivalent[$item];
                $dProductIn->qty = $request->qty[$item];
                $dProductIn->price = $request->price[$item];
                $dProductIn->amount = $request->amount[$item];
                $dProductIn->warehouse = $request->warehouse[$item];
                $productD = DetailProduct::where('id', $request->replacement[$item])->first();
                if ($request->warehouse[$item] == 'BDG') {
                    $productD->stock = $productD->stock - $request->qty[$item];
                } else {
                    $productD->warehouse_stock = $productD->warehouse_stock - $request->qty[$item];
                }
                $productD->save();
                $product = Product::where('id', $productD->id_product)->first();
                if ($request->warehouse[$item] == 'BDG') {
                    $product->stock = $product->stock - $request->qty[$item];
                } else {
                    $product->warehouse_stock = $product->warehouse_stock - $request->qty[$item];
                }
                // dd($product);
                $product->save();
                $dProductSave = $dProductIn->save();
            }
        }
        if ($dProductSave) {
            return redirect('/product-out/' . $productOut->id)->with('message', 'data telah di tambahkan');
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
        $product = ProductOut::find($id);
        $detail = DetailProductOut::where('id_product_out', $id)->get();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        return view('pages.warehouse.product-out.detail', compact('product', 'noSaleProspect', 'detail'));
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
        $product = ProductOut::find($id);
        $detail = DetailProductOut::where('id_product_out', $id)->get();

        $delProductOut = $product->delete();

        foreach ($detail as $dProductOut) {
            $dProductOut->detailProduct->stock = $dProductOut->detailProduct->stock + $dProductOut->qty;
            $dProductOut->detailProduct->save();
            $dProductOut->detailProduct->product->stock = $dProductOut->detailProduct->product->stock + $dProductOut->qty;
            $dProductOut->detailProduct->product->save();
            $delDetProductOut = $dProductOut->delete();
        }

        if ($delProductOut || $delDetProductOut) {
            return 1;
        } else {
            return 0;
        }
    }
    public function index_invoice()
    {
        return view('pages.warehouse.product-out.invoice');
    }
    public function invoice($id)
    {
        $invoice = Invoice::find($id);
        $detailQ = DetailQuotation::where('id_quotation', $invoice->id_quotation)->get();
        $product = SerialProduct::all();
        // dd($product);
        $dProduct = DetailProduct::all();
        return view('pages.warehouse.product-out.form-invoice', compact('invoice', 'detailQ', 'product', 'dProduct'));
    }

    public function invoice_store(Request $request)
    {
        $rule = [
            'invoice' => 'required',
            'detail_client' => 'required',
            'vers' => 'required',
            'date' => 'required',
            'shipping' => 'required',
            'note' => 'required',
        ];
        $message = [
            'invoice.required' => 'Field No Invoice Wajib Diisi',
            'detail_client.required' => 'Field Detail Client Wajib Diisi',
            'vers.required' => 'Field Offline / Online Wajib Diisi',
            'date.required' => 'Field Date Wajib Diisi',
            'shipping.required' => 'Field Shipping Wajib Diisi',
            'note.required' => 'Field Note Wajib Diisi',
        ];
        // $invoice = Invoice::find($id);
        // $detailQ = DetailQuotation::where('id_quotation', $invoice->id_quotation)->get();
        // dd($request->all());
        $this->validate($request, $rule, $message);
        // Masukan Data ke Tabel Product Out
        $productOut = new ProductOut();
        $productOut->id_user = Auth::user()->id;
        $productOut->invoice = $request->invoice;
        $productOut->po = $request->po;
        $productOut->no_type = "1";
        $productOut->detail_client = $request->detail_client;
        $productOut->vers = $request->vers;
        $productOut->date = $request->date;
        $productOut->note = $request->note;
        $productOut->shipping = $request->shipping;
        $productOut->total = $request->total;
        $productOutSave = $productOut->save();
        if ($productOutSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            foreach ($request->equivalent as $item => $value) {
                $dProductIn = new DetailProductOut;
                $dProductIn->id_product_out = $productOut->id;
                $dProductIn->id_detail_product = $request->replacement[$item];
                $dProductIn->id_serial_product = $request->equivalent[$item];
                $dProductIn->qty = $request->qty[$item];
                $dProductIn->price = $request->price[$item];
                $dProductIn->amount = $request->amount[$item];
                $dProductIn->warehouse = $request->warehouse[$item];
                $productD = DetailProduct::where('id', $request->replacement[$item])->first();
                if ($request->warehouse[$item] == 'BDG') {
                    $productD->stock = $productD->stock - $request->qty[$item];
                } else {
                    $productD->warehouse_stock = $productD->warehouse_stock - $request->qty[$item];
                }
                $productD->save();
                $product = Product::where('id', $productD->id_product)->first();
                if ($request->warehouse[$item] == 'BDG') {
                    $product->stock = $product->stock - $request->qty[$item];
                } else {
                    $product->warehouse_stock = $product->warehouse_stock - $request->qty[$item];
                }
                // dd($product);
                $product->save();
                $dProductSave = $dProductIn->save();
            }
        }
        if ($dProductSave) {
            return redirect('/product-out')->with('message', 'data telah di tambahkan');
        }
    }


    public function change_no(Request $request, $id)
    {
        $product = ProductOut::find($id);
        $product->no_type = $request->status;
        $productSave = $product->save();
        if ($productSave) {
            return response()->json(['success' => 'Status berhasil diperbarui']);
        } else {
            return response()->json(['error' => 'Gagal menyimpan perubahan status'], 500);
        }
    }
}
