<?php

namespace App\Http\Controllers;

use App\Models\DetailPurchaseOrder;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;

class POController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.accounting.purchase.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $previewNoPo = $this->generateNoPo();
        $units = \App\Models\Unit::where('type', 'global')->orderBy('brand')->get();
        $products = Product::orderBy('commodity')->get();
        return view('pages.accounting.purchase.form', compact('suppliers', 'previewNoPo', 'units', 'products'));
    }

    private function generateNoPo(): string
    {
        $year = now()->format('Y');
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $roman = $romanMonths[(int) now()->format('n') - 1];
        $suffix = "-P/RJO/{$roman}/{$year}";

        $last = PurchaseOrder::where('no_po', 'like', '%' . $suffix)
            ->orderByDesc('no_po')
            ->value('no_po');

        $lastSeq = $last ? (int) substr($last, 0, 3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $nextSeq . $suffix;
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
            'no_po' => 'required|string|unique:purchase_order,no_po',
        ];
        $this->validate($request, $rule);

        $supplier = Supplier::find($request->supplier);
        $itemCategories = $request->item_category ?? [];
        $purchase = new PurchaseOrder();
        $purchase->id_supplier = $request->supplier;
        $purchase->no_po = $request->no_po;
        $purchase->category = in_array('Unit', $itemCategories) ? 'Unit' : 'Sparepart';
        $purchase->company = $supplier->supplier;
        $purchase->attn = $request->attn ?? '';
        $purchase->mobile = $request->mobile ?? '';
        $purchase->delivery = $request->delivery ?? '';
        $purchase->date = $request->date;
        $purchase->email = $supplier->email ?? '-';
        $purchase->phone = $supplier->phone ?? '-';
        $purchase->address = $supplier->address ?? '-';
        $purchase->payment = $request->payment ?? '';
        $purchase->note = $request->note ?? '';
        $purchase->subtotal = $request->subtotal;
        $purchase->vat = $request->tax;
        $purchase->diskon = $request->diskon;
        $purchase->total = $request->harga_total;
        $purchaseSave = $purchase->save();
        $dPurchaseSave = true;
        if ($purchaseSave) {
            foreach ($request->product as $key => $value) {
                $itemCategory = $itemCategories[$key] ?? 'Sparepart';
                $dPurchase = new DetailPurchaseOrder();
                $dPurchase->id_purchase_order = $purchase->id;
                $dPurchase->product = $value;
                $dPurchase->category = $itemCategory;
                $dPurchase->id_unit = $itemCategory == 'Unit' ? ($request->id_unit[$key] ?? null) : null;
                $dPurchase->id_product = $itemCategory == 'Sparepart' ? ($request->id_product[$key] ?? null) : null;
                $dPurchase->qty = $request->qty[$key];
                $dPurchase->info_qty = $request->info_qty[$key];
                $dPurchase->price = $request->price[$key];
                $dPurchase->disc = $request->disc[$key];
                $dPurchase->amount = $request->amount[$key];
                $dPurchaseSave = $dPurchase->save();
            }
        }
        if ($purchaseSave && $dPurchaseSave) {
            return redirect('purchase')->with('success', 'data berhasil ditambahkan');
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
        $purchase = PurchaseOrder::find($id);
        $dPurchase = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
        $tax = $purchase->total * 11 / 100;
        $totalPph = 0;
        foreach ($dPurchase as $product) {
            $pph = ($product->amount * $product->pph) / 100;
            $totalPph += $pph;
        }
        return view('pages.accounting.purchase.detail', compact('purchase', 'dPurchase', 'tax', 'totalPph'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $purchase = PurchaseOrder::find($id);
        $dPurchase = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
        $suppliers = Supplier::all();
        $units = \App\Models\Unit::where('type', 'global')->orderBy('brand')->get();
        $products = Product::orderBy('commodity')->get();
        return view('pages.accounting.purchase.form', compact('suppliers', 'purchase', 'dPurchase', 'units', 'products'));
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
        $this->validate($request, [
            'no_po' => 'required|string|unique:purchase_order,no_po,' . $id,
        ]);
        $supplier = Supplier::find($request->supplier);
        $itemCategories = $request->item_category ?? [];
        $purchase = PurchaseOrder::find($id);
        $purchase->id_supplier = $request->supplier;
        $purchase->no_po = $request->no_po;
        $purchase->category = in_array('Unit', $itemCategories) ? 'Unit' : 'Sparepart';
        $purchase->company = $supplier->supplier;
        $purchase->attn = $request->attn ?? '';
        $purchase->mobile = $request->mobile ?? '';
        $purchase->delivery = $request->delivery ?? '';
        $purchase->date = $request->date;
        $purchase->email = $supplier->email ?? '-';
        $purchase->phone = $supplier->phone ?? '-';
        $purchase->address = $supplier->address ?? '-';
        $purchase->payment = $request->payment ?? '';
        $purchase->note = $request->note ?? '';
        $purchase->subtotal = $request->subtotal;
        $purchase->vat = $request->tax;
        $purchase->diskon = $request->diskon;
        $purchase->total = $request->harga_total;
        $purchaseSave = $purchase->save();
        $dPurchaseSave = true;
        if ($purchaseSave) {
            $submittedIds = [];
            foreach ($request->product as $key => $value) {
                $itemCategory = $itemCategories[$key] ?? 'Sparepart';
                $detailId = $request->detail_id[$key] ?? null;
                $dPurchase = $detailId ? DetailPurchaseOrder::find($detailId) : null;
                if (!$dPurchase) {
                    $dPurchase = new DetailPurchaseOrder();
                    $dPurchase->id_purchase_order = $purchase->id;
                }
                $dPurchase->product = $value;
                $dPurchase->category = $itemCategory;
                $dPurchase->id_unit = $itemCategory == 'Unit' ? ($request->id_unit[$key] ?? null) : null;
                $dPurchase->id_product = $itemCategory == 'Sparepart' ? ($request->id_product[$key] ?? null) : null;
                $dPurchase->qty = $request->qty[$key];
                $dPurchase->info_qty = $request->info_qty[$key];
                $dPurchase->price = $request->price[$key];
                $dPurchase->disc = $request->disc[$key];
                $dPurchase->amount = $request->amount[$key];
                $dPurchaseSave = $dPurchase->save();
                $submittedIds[] = $dPurchase->id;
            }
            DetailPurchaseOrder::where('id_purchase_order', $purchase->id)
                ->whereNotIn('id', $submittedIds)
                ->delete();
        }
        if ($purchaseSave && $dPurchaseSave) {
            return redirect('purchase/'. $id)->with('success', 'data berhasil ditambahkan');
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
        $purchase = PurchaseOrder::find($id);
        $dPurchase = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
        $purchaseDel = $purchase->delete();
        foreach ($dPurchase as $order) {
            $order->delete();
        }
        if ($purchaseDel) {
            return 1;
        } else {
            return 0;
        }

    }
    public function show_print($id)
    {
        $purchase = PurchaseOrder::find($id);
        $dPurchase = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
        $tax = $purchase->total * 11 / 100;
        return view('pages.accounting.purchase.detail-print', compact('purchase', 'dPurchase', 'tax'));
    }

    public function add_pph(Request $request, $id)
    {

        $PO = PurchaseOrder::find($id);
        $DPO = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
        foreach ($DPO as $item => $value) {
            $value->pph = $request->pph[$item];
            $status = $value->save();
        }
        if ($status) {
            return redirect('/purchase/' . $id)->with('massage', 'Data telah terkirim');
        }
    }
    public function delete_pph($id)
    {
        $purchase = PurchaseOrder::find($id);
        $dPurchase = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
        foreach ($dPurchase as $item => $value) {
            $value->pph = 0;
            $status = $value->save();
        }
        if ($status) {
            return 1;
        } else {
            return 0;
        }

    }
}
