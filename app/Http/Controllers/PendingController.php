<?php

namespace App\Http\Controllers;

use App\Models\ChangeStatus;
use App\Models\Comment;
use App\Models\DetailPendingPO;
use App\Models\DetailProduct;
use App\Models\DetailProductOut;
use App\Models\DetailQuotation;
use App\Models\DetailReturn;
use App\Models\DetailServiceQuotation;
use App\Models\Expanse;
use App\Models\Invoice;
use App\Models\PendingPO;
use App\Models\Product;
use App\Models\ProductOut;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\Quotation;
use App\Models\Retur;
use App\Models\SerialProduct;
use App\Models\ServiceOrder;
use App\Models\SubtitleQuotation;
use App\Models\ProjectExpense;
use App\Models\UnitQuotation;
use App\Models\UnitQuotationDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PendingController extends Controller
{
    private function hasApprovedInvoice(PendingPO $pending): bool
    {
        if ($pending->id_unit_quotation) {
            return Invoice::where('id_unit_quotation', $pending->id_unit_quotation)->whereNotNull('no_invoice')->exists();
        }
        return Invoice::where('id_quotation', $pending->id_quotation)->whereNotNull('no_invoice')->exists();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = PendingPO::join('quotation as q', 'pending_po.id_quotation', '=', 'q.id')
            ->leftJoin('invoice as i', 'q.id', '=', 'i.id_quotation')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')
            ->join('client as c', 'p.id_client', '=', 'c.id')
            ->join('users as u', 'q.id_sales', '=', 'u.id')
            ->select(
                'pending_po.id',
                'u.name',
                'c.company',
                'i.no_po',
                'pending_po.status',
                'i.status_p',
                'i.note_p',
            )
            ->get();
        // dd($data);
        $pendingPO = PendingPO::with('detail')->get();
        return view('pages.pending.index', compact('pendingPO'));
    }
    public function indexOrder()
    {
        $newCount = PendingPO::where('status', operator: 0)
            ->where('type', 'Non Project')
            ->count();
        $listCount = PendingPO::whereIn('pending_po.status', [1, 2, 3, 4])
            ->where('type', 'Non Project')
            ->count();
        $deliveryCount = PendingPO::where('pending_po.status', 5)
            ->where('type', 'Non Project')
            ->count();
        return view('pages.pending.order', compact('newCount', 'deliveryCount', 'listCount'));
    }
    public function indexList()
    {
        $newCount = PendingPO::where('status', operator: 0)
            ->where('type', 'Non Project')
            ->count();
        $listCount = PendingPO::whereIn('pending_po.status', [1, 2, 3, 4])
            ->where('type', 'Non Project')
            ->count();
        $deliveryCount = PendingPO::where('pending_po.status', 5)
            ->where('type', 'Non Project')
            ->count();
        return view('pages.pending.list', compact('newCount', 'deliveryCount', 'listCount'));
    }
    public function indexDelivery()
    {
        return view('pages.pending.delivery');
    }
    public function indexCompleted()
    {
        return view('pages.pending.completed');
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
        $user = Auth::user();
        $role = $user->role;

        if (in_array($role, ['Client', 'Guest'])) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses detail Sales Order ini.');
        }

        $pending = PendingPO::with(['quote', 'unitQuotation'])->findOrFail($id);

        if ($role === 'Sales') {
            $quoteSales = $pending->quote?->id_sales;
            $unitSales = $pending->unitQuotation?->id_sales;
            $isOwner = ($quoteSales == $user->id) || ($unitSales == $user->id);
            if (!$isOwner) {
                abort(403, 'Anda tidak memiliki izin untuk mengakses order ini.');
            }
        }

        $allProductOuts = ProductOut::where('id_pending', $pending->id)
            ->orWhere('id', $pending->id_product_out)
            ->with(['detail.detailProduct.product', 'user'])
            ->orderBy('id', 'desc')
            ->get();
        $allProductOutIds = $allProductOuts->pluck('id')->filter()->all();
        $shippedQtyMap = DetailProductOut::whereIn('id_product_out', $allProductOutIds)
            ->get()
            ->groupBy('id_serial_product');

        if ($pending->id_unit_quotation) {
            $quote = UnitQuotation::with(['client', 'pic', 'sales', 'details'])->findOrFail($pending->id_unit_quotation);
            $invoices = Invoice::where('id_unit_quotation', $quote->id)->orderByRaw("FIELD(type,'DP','BP','CT')")->get();
            $activity = ChangeStatus::where('id_pending', $id)->with('comment')->get();
            $resis = Expanse::where('id_pending', $id)->where('type', 'Resi')->get();
            $dPending = DetailPendingPO::with('equivalent.product')->where('id_pending', $id)->get();
            $purchases = PurchaseRequest::where('id_pending', $id)->with(['details.equivalent.product', 'purchaseOrders'])->get();
            $purchase = $purchases->first();
            $return = Retur::where('id_pending', $id)->get();
            $allproductOut = ProductOut::leftJoin('pending_po', 'product_out.id', '=', 'pending_po.id_product_out')
                ->whereNull('pending_po.id_product_out')
                ->groupBy('product_out.id')
                ->select('product_out.*')
                ->get();
            $product = ProductOut::find($pending->id_product_out);
            $detProduct = $pending->id_product_out ? DetailProductOut::where('id_product_out', $pending->id_product_out)->get() : collect();

            $firstInv = $invoices->first();
            $invoiceNo = $firstInv?->no_invoice ?? '';
            $poNo = $quote->po_number ?? ($firstInv?->no_po ?? '-');
            $clientName = $quote->client?->company ?? '';
            $clientAddress = $quote->client?->address ?? '';
            $clientPic = $pending->shipping_recipient?->name ?? $quote->pic?->name ?? '';
            $clientPhone = $pending->shipping_recipient?->phone ?? $quote->pic?->phone ?? '';
            $detailClientFormatted = $clientName;
            if (!empty($clientPic)) { $detailClientFormatted .= "\nAttn: " . $clientPic . ($clientPhone ? " (" . $clientPhone . ")" : ""); }
            if (!empty($clientAddress)) { $detailClientFormatted .= "\nAlamat: " . $clientAddress; }

            $isKojisha = (method_exists($quote, 'isKojisha') ? $quote->isKojisha() : ($quote->flag === 'Kojisha'))
                || (is_string($invoiceNo) && str_contains($invoiceNo, '/KII/'))
                || (is_string($poNo) && str_contains($poNo, 'KII'));
            $flag = $isKojisha ? 'Kojisha' : 'Reftech';
            $nextNoProductOut = (new \App\Http\Controllers\ProductOutController())->generateNoProductOut('BDG', $flag);

            // Modal items
            $modalItemsData = [];
            foreach ($dPending as $idx => $item) {
                $equiv = $item->id_equivalent ? SerialProduct::with('product')->find($item->id_equivalent) : null;
                $rep = $equiv ? DetailProduct::with('product')->where('id_product', $equiv->id_product)->get() : collect();
                $defaultRep = !empty($item->id_replacement) ? DetailProduct::with('product')->find($item->id_replacement) : $rep->first();
                $defaultRepText = $defaultRep
                    ? "{$defaultRep->replacement} | {$defaultRep->product?->commodity} (BDG: {$defaultRep->stock}, BKS: {$defaultRep->warehouse_stock})"
                    : ($equiv ? '-- Pilih Replacement --' : '-- Non-Inventory / Ready Stock (Tanpa SKU Fisik) --');
                $itemName = $equiv?->product?->commodity ?? $item->note ?? ('Item #' . ($idx + 1));
                $itemQty = (float)($item->service?->qty ?? ($item->qty ?? 1));
                $alreadyShipped = $equiv ? (float)($shippedQtyMap->get($equiv->id)?->sum('qty') ?? 0) : 0;
                $remainingQty = max(0, $itemQty - $alreadyShipped);
                $itemPrice = (float)($item->price ?? 0);
                $itemAmount = (float)($item->amount ?? ($remainingQty * $itemPrice));

                $modalItemsData[] = [
                    'id' => $item->id,
                    'equiv' => $equiv,
                    'replacements' => $rep,
                    'default_rep' => $defaultRep,
                    'default_rep_id' => $defaultRep?->id ?? 0,
                    'default_rep_text' => $defaultRepText,
                    'default_serial_id' => $equiv?->id ?? 0,
                    'name' => $itemName,
                    'pn' => $equiv?->pn ?? '-',
                    'description' => $equiv?->product?->detail_desc ?? $item->note ?? '',
                    'stock_bdg' => $defaultRep?->stock ?? 0,
                    'stock_bks' => $defaultRep?->warehouse_stock ?? 0,
                    'qty_ordered' => $itemQty,
                    'qty_shipped' => $alreadyShipped,
                    'qty_remaining' => $remainingQty,
                    'price' => $itemPrice,
                    'amount' => $itemAmount,
                    'is_non_inventory' => empty($item->id_equivalent),
                ];
            }

            return view('pages.pending.detail-unit', compact(
                'pending', 'quote', 'invoices', 'activity', 'resis',
                'dPending', 'purchases', 'purchase', 'return', 'allproductOut', 'product', 'detProduct',
                'modalItemsData', 'nextNoProductOut', 'allProductOuts', 'invoiceNo', 'poNo', 'detailClientFormatted'
            ));
        }

        $quotation = Quotation::with('pic.client')->findOrFail($pending->id_quotation);
        $detQuotation = DetailQuotation::where('id_quotation', $pending->id_quotation)->get();
        $subQuote = SubtitleQuotation::with('detail.pending.equivalent.product')->where('id_quotation', $pending->id_quotation)->get();
        $invoice = Invoice::where('id_quotation', $quotation->id)->first();
        $activity = ChangeStatus::where('id_pending', $id)->with('comment')->get();
        $resi = Expanse::where('id_pending', $id)->where('type', 'Resi')->first();
        $resis = Expanse::where('id_pending', $id)->where('type', 'Resi')->get();
        $product = ProductOut::find($pending->id_product_out);
        $detProduct = DetailProductOut::where('id_product_out', $pending->id_product_out)->get();
        $return = Retur::where('id_pending', $id)->get();
        $allproductOut = ProductOut::leftJoin('pending_po', 'product_out.id', '=', 'pending_po.id_product_out')
            ->whereNull('pending_po.id_product_out')
            ->groupBy('product_out.id')
            ->select('product_out.id', 'product_out.invoice')
            ->get();
        $purchases = PurchaseRequest::where('id_pending', $id)->with(['details.equivalent.product', 'purchaseOrders'])->get();
        $purchase = $purchases->first();
        $serial = collect();

        $invoiceNo = $invoice?->no_invoice ?? '';
        $poNo = $invoice?->no_po ?? ($quotation->no_po ?? '-');
        $clientName = $quotation->pic?->client?->company ?? '';
        $clientAddress = $quotation->pic?->client?->address ?? '';
        $clientPic = $pending->shipping_recipient?->name ?? $quotation->pic?->name ?? '';
        $clientPhone = $pending->shipping_recipient?->phone ?? $quotation->pic?->phone ?? '';
        $detailClientFormatted = $clientName;
        if (!empty($clientPic)) { $detailClientFormatted .= "\nAttn: " . $clientPic . ($clientPhone ? " (" . $clientPhone . ")" : ""); }
        if (!empty($clientAddress)) { $detailClientFormatted .= "\nAlamat: " . $clientAddress; }

        $isKojisha = (method_exists($quotation, 'isKojisha') ? $quotation->isKojisha() : ($quotation->flag === 'Kojisha'))
            || (is_string($invoiceNo) && str_contains($invoiceNo, '/KII/'))
            || (is_string($poNo) && str_contains($poNo, 'KII'));
        $flag = $isKojisha ? 'Kojisha' : 'Reftech';
        $nextNoProductOut = (new \App\Http\Controllers\ProductOutController())->generateNoProductOut('BDG', $flag);

        // Modal items for regular pending PO
        $dPending = DetailPendingPO::with('equivalent.product')->where('id_pending', $id)->whereNot('status', '7')->get();
        $modalItemsData = [];
        foreach ($dPending as $idx => $item) {
            $equiv = $item->id_equivalent ? SerialProduct::with('product')->find($item->id_equivalent) : null;
            $rep = $equiv ? DetailProduct::with('product')->where('id_product', $equiv->id_product)->get() : collect();
            $defaultRep = !empty($item->id_replacement) ? DetailProduct::with('product')->find($item->id_replacement) : $rep->first();
            $defaultRepText = $defaultRep
                ? "{$defaultRep->replacement} | {$defaultRep->product?->commodity} (BDG: {$defaultRep->stock}, BKS: {$defaultRep->warehouse_stock})"
                : ($equiv ? '-- Pilih Replacement --' : '-- Non-Inventory / Ready Stock (Tanpa SKU Fisik) --');
            $itemName = $equiv?->product?->commodity ?? $item->note ?? ('Item #' . ($idx + 1));
            $itemQty = (float)($item->service?->qty ?? ($item->qty ?? 1));
            $alreadyShipped = $equiv ? (float)($shippedQtyMap->get($equiv->id)?->sum('qty') ?? 0) : 0;
            $remainingQty = max(0, $itemQty - $alreadyShipped);
            $itemPrice = (float)($item->price ?? 0);
            $itemAmount = (float)($item->amount ?? ($remainingQty * $itemPrice));

            $modalItemsData[] = [
                'id' => $item->id,
                'equiv' => $equiv,
                'replacements' => $rep,
                'default_rep' => $defaultRep,
                'default_rep_id' => $defaultRep?->id ?? 0,
                'default_rep_text' => $defaultRepText,
                'default_serial_id' => $equiv?->id ?? 0,
                'name' => $itemName,
                'pn' => $equiv?->pn ?? '-',
                'description' => $equiv?->product?->detail_desc ?? $item->note ?? '',
                'stock_bdg' => $defaultRep?->stock ?? 0,
                'stock_bks' => $defaultRep?->warehouse_stock ?? 0,
                'qty_ordered' => $itemQty,
                'qty_shipped' => $alreadyShipped,
                'qty_remaining' => $remainingQty,
                'price' => $itemPrice,
                'amount' => $itemAmount,
                'is_non_inventory' => empty($item->id_equivalent),
            ];
        }

        return view('pages.pending.detail', compact(
            'purchases', 'purchase', 'return', 'detProduct', 'activity', 'allproductOut', 'subQuote', 'pending',
            'quotation', 'invoice', 'detQuotation', 'resi', 'product', 'resis', 'serial',
            'modalItemsData', 'nextNoProductOut', 'allProductOuts', 'invoiceNo', 'poNo', 'detailClientFormatted'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pending = PendingPO::find($id);
        if (!$pending) {
            return redirect()->route('pending-po.index')->with('error', 'Pending PO tidak ditemukan');
        }
        $quote = Quotation::find($pending->id_quotation);
        $Dquote = DetailQuotation::where('id_quotation', $pending->id_quotation)->get();
        $dPending = DetailPendingPO::where('id_pending', $id)->get();

        $fullRep = [];
        $no = 0;
        foreach ($Dquote as $item) {
            $equivalent = SerialProduct::find($item->id_equivalent);
            $fullRep[$no] = DetailProduct::where('id_product', $equivalent->id_product)->get();
            $no++;
        }
        // dd($fullRep);
        // dd($dPending);
        return view('pages.pending.form', compact('Dquote', 'fullRep', 'pending', 'quote', 'dPending', 'id'));
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
        // dd($request->all());
        $pending = PendingPO::find($id);
        $quote = Quotation::find($pending->id_quotation);
        $dQuote = DetailQuotation::where('id_quotation', $quote->id)->get();
        $dPending = DetailPendingPO::where('id_pending', $id)->get();

        // Hapus data lama
        foreach ($dPending as $item) {
            $item->delete();
        }

        // Simpan data baru
        $totalPendingQty = 0;
        foreach ($dQuote as $key => $value) {
            $itemPending = new DetailPendingPO;
            $itemPending->id_pending = $id;
            $itemPending->id_replacement = $request->replacement[$key];
            $itemPending->desc = $request->desc[$key];
            $itemPending->qty = $request->qty[$key];
            $itemPending->note = $request->note[$key];
            if ($value->qty == $request->qty[$key]) {
                $itemPending->status = 0;
            } else {
                $itemPending->status = 1;
            }
            $itemPending->save();
            $totalPendingQty += $request->qty[$key];
        }

        $totalQuoteQty = $dQuote->sum('qty');
        // $totalPendingQty = DetailPendingPO::where('id_pending', $id)->sum('qty');
        // dd($totalPendingQty);

        if ($totalPendingQty == $totalQuoteQty) {
            $pending->status = 2;
            $pending->save();
        } else {
            $pending->status = 1;
            $pending->save();
        }

        return redirect('/pending-po')->with('message', 'Pending PO telah dibuat');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pending = PendingPO::find($id);

        if (!$pending) {
            return redirect('/pending-po')->with('error', 'Pending PO tidak ditemukan');
        }

        $guard = app(DeletionGuardService::class);
        $check = $guard->checkPendingDeletion($pending);
        if (!$check['allowed']) {
            return redirect('/pending-po')->with('error', 'Pending PO tidak dapat dihapus karena ' . implode(', ', $check['reasons']));
        }

        $pending->delete();

        return redirect('/pending-po')->with('success', 'Pending PO berhasil dihapus');
    }
    public function connect_out(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $pending = PendingPO::findOrFail($id);
            $dPending = DetailPendingPO::where('id_pending', $id)->get();
            $cekstock = 0;
            foreach ($dPending as $detail) {
                $cekstock += $detail->bdg + $detail->bks;
            }
            if ($pending->id_unit_quotation) {
                if ($cekstock != 0) {
                    foreach ($dPending as $item) {
                        $product = Product::join('serial_product as sp', 'sp.id_product', '=', 'product.id')->where('sp.id', $item->id_equivalent)->select('product.*')->first();
                        if (!$product) {
                            continue;
                        }
                        $product->stock += $item->bdg;
                        $product->warehouse_stock += $item->bks;
                        $product->pending_stock -= $item->bdg + $item->bks;
                        $product->save();
                        $item->bdg = 0;
                        $item->bks = 0;
                        $item->save();
                    }
                }
            } elseif ($cekstock == 0) {
                $quote = Quotation::findOrFail($pending->id_quotation);
                $dQuote = DetailQuotation::where('id_quotation', $quote->id)->get();
                foreach ($dQuote as $item) {
                    $equivalent = SerialProduct::find($item->id_equivalent);
                    if (!$equivalent) {
                        continue;
                    }
                    $product = Product::find($equivalent->id_product);
                    if (!$product) {
                        continue;
                    }
                    $product->pending_stock -= $item->qty;
                    $product->stock += $item->qty;
                    $product->save();
                }
            }
            $pending->status = '6';
            $pending->id_product_out = $request->product;
            $pending->save();

            return redirect('/pending-po/' . $id)->with('message', 'Product Out telah disambungkan');
        });
    }
    public function productEdit(Request $request, $id)
    {
        $pending = PendingPO::findOrFail($id);
        $quote = Quotation::findOrFail($pending->id_quotation);
        $dQuote = DetailQuotation::where('id_quotation', $quote->id)->get();
        $dPending = DetailPendingPO::where('id_pending', $id)->get();
        foreach ($request->status as $key => $value) {
            $product = Product::join('serial_product as sp', 'sp.id_product', '=', 'product.id')->where('sp.id', $dQuote[$key]->id_equivalent)->select('product.*')->first();
            if (!$product) {
                continue;
            }
            // dd($dQuote[$key]->bdg);
            if ($dQuote[$key]->bdg != 0 || $dQuote[$key]->bks != 0) {
                $product->stock += $dQuote[$key]->bdg;
                $product->warehouse_stock += $dQuote[$key]->bks;
                $product->pending_stock -= $dQuote[$key]->bdg + $dQuote[$key]->bks;
                $dQuote[$key]->bdg = 0;
                $dQuote[$key]->bks = 0;
                $product->save();
            }
            $dQuote[$key]->status = $value;
            $dQuote[$key]->bdg = $request->bdg[$key];
            $dQuote[$key]->bks = $request->bks[$key];
            $dQuote[$key]->note = $request->note[$key];
            $dQuote[$key]->save();
            // dd($item->id_equivalent);
            if ($value == 2) {
                $product->stock -= $request->bdg[$key];
                $product->warehouse_stock -= $request->bks[$key];
                $product->pending_stock += $request->bdg[$key] + $request->bks[$key];
                $product->save();
            }
        }
        return redirect('/pending-po/' . $id)->with('message', 'Product Pending PO telah diedit');
    }
    public function projectEdit(Request $request, $id)
    {
        $pending = PendingPO::findOrFail($id);
        $dPending = DetailPendingPO::where('id_pending', $id)->get();

        try {
            DB::transaction(function () use ($request, $dPending) {
                foreach ($request->status as $key => $value) {
                    if (!isset($dPending[$key])) {
                        continue;
                    }
                    $product = Product::join('serial_product as sp', 'sp.id_product', '=', 'product.id')->where('sp.id', $request->equivalent[$key])->select('product.*')->first();
                    if (!$product) {
                        continue;
                    }

                    // Release this row's current reservation first, so the availability check
                    // below and the re-allocation below it are both against the true stock.
                    $product->stock += $dPending[$key]->bdg;
                    $product->warehouse_stock += $dPending[$key]->bks;
                    $product->pending_stock -= $dPending[$key]->bdg + $dPending[$key]->bks;

                    $newBdg = (int) ($request->bdg[$key] ?? 0);
                    $newBks = (int) ($request->bks[$key] ?? 0);

                    if ($value == 2 && ($newBdg > $product->stock || $newBks > $product->warehouse_stock)) {
                        throw new \RuntimeException('Alokasi stok melebihi stok tersedia untuk ' . ($product->commodity ?? 'produk') . " (BDG tersedia: {$product->stock}, BKS tersedia: {$product->warehouse_stock}).");
                    }

                    $dPending[$key]->id_equivalent = $request->equivalent[$key];
                    $dPending[$key]->status = $value;
                    $dPending[$key]->bdg = $newBdg;
                    $dPending[$key]->bks = $newBks;
                    $dPending[$key]->note = $request->note[$key] ?? null;
                    $dPending[$key]->save();

                    if ($value == 2) {
                        $product->stock -= $newBdg;
                        $product->warehouse_stock -= $newBks;
                        $product->pending_stock += $newBdg + $newBks;
                    }
                    $product->save();
                }
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['bdg' => $e->getMessage()])->withInput();
        }
        $request->validate([
            'no_pending' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'delivery' => 'nullable|string|max:255',
            'shipping_address_type' => 'nullable|in:customer,manual',
            'shipping_address_manual' => 'nullable|string|max:1000',
            'doc_address_type' => 'nullable|in:customer,manual',
            'doc_address_manual' => 'nullable|string|max:1000',
            'charged' => 'nullable|string|max:255',
            'doc_charged' => 'nullable|string|max:255',
            'shipping_charged' => 'nullable|string|max:255',
        ]);

        $pending = PendingPO::findOrFail($id);

        $combine = $request->has('combine_shipping_and_parts') || $request->combine_shipping_and_parts == 1;

        $pending->no_pending = $request->no_pending;
        $pending->title = $request->title;
        $pending->delivery = $request->delivery;
        $pending->combine_shipping_and_parts = $combine;

        $pending->shipping_address_type = $request->shipping_address_type ?? 'customer';
        $pending->shipping_address_manual = $request->shipping_address_type === 'manual' ? $request->shipping_address_manual : null;

        if ($combine) {
            $pending->doc_address_type = $pending->shipping_address_type;
            $pending->doc_address_manual = $pending->shipping_address_manual;
            $pending->charged = $request->charged;
            $pending->doc_charged = null;
            $pending->shipping_charged = null;
        } else {
            $pending->doc_address_type = $request->doc_address_type ?? 'customer';
            $pending->doc_address_manual = $request->doc_address_type === 'manual' ? $request->doc_address_manual : null;
            $pending->charged = null;
            $pending->doc_charged = $request->doc_charged;
            $pending->shipping_charged = $request->shipping_charged;
        }

        $pending->save();

        if (str_contains(request()->header('referer'), 'project-monitoring')) {
            return redirect()->route('project-monitoring.show', $id)->with('success', 'Informasi project berhasil diperbarui.');
        }

        return redirect()->back()->with('message', 'Informasi Project Pending PO berhasil diperbarui');
    }
    public function statusEdit(Request $request, $id)
    {
        $pending = PendingPO::findOrFail($id);
        $hasApprovedInvoice = $this->hasApprovedInvoice($pending);
        if (!$hasApprovedInvoice) {
            return redirect()->back()->with('error', 'Proses logistik dikunci karena invoice belum di-approve oleh Accounting.');
        }

        return DB::transaction(function () use ($request, $pending, $id) {
            $pending->status = $request->status;
            $pending->save();

            switch ($request->status) {
                case 1:
                    $note = 'On Check';
                    break;
                case 2:
                    $note = 'Ready Stock';
                    break;
                case 3:
                    $note = 'Kurang';
                    break;
                case 4:
                    $note = 'Pre order';
                    break;
                case 5:
                    $note = 'Delivery Process';
                    break;
                case 6:
                    $note = 'Done';
                    break;
                case 8:
                    $note = 'Partial Delivery';
                    break;
                default:
                    $note = 'Cancel';
                    break;
            }

            $status = new ChangeStatus();
            $status->id_pending = $pending->id;
            $status->status = $request->status;
            $status->note = $note;
            $status->date = Carbon::now();
            $status->save();
            if ($request->status == '7') {
                if ($pending->id_unit_quotation) {
                    $dPendingCancel = DetailPendingPO::where('id_pending', $pending->id)->get();
                    foreach ($dPendingCancel as $item) {
                        $product = Product::join('serial_product as sp', 'sp.id_product', '=', 'product.id')->where('sp.id', $item->id_equivalent)->select('product.*')->first();
                        if (!$product) {
                            continue;
                        }
                        $product->stock += $item->bdg;
                        $product->warehouse_stock += $item->bks;
                        $product->pending_stock -= $item->bdg + $item->bks;
                        $product->save();
                        $item->bdg = 0;
                        $item->bks = 0;
                        $item->save();
                    }
                } else {
                    $Dquote = DetailQuotation::where('id_quotation', $pending->id_quotation)->get();
                    foreach ($Dquote as $item) {
                        $product = Product::join('serial_product as sp', 'sp.id_product', '=', 'product.id')->where('sp.id', $item->id_equivalent)->select('product.*')->first();
                        if (!$product) {
                            continue;
                        }
                        $product->stock += $item->qty;
                        $product->pending_stock -= $item->qty;
                        $product->save();
                    }
                }
            }
            if ($request->status == '6') {
                if ($pending->id_unit_quotation) {
                    return redirect('/pending-po/product-out-project/' . $id)->with('message', 'Status Product Pending PO telah diedit');
                } else {
                    return redirect('/pending-po/product-out/' . $id)->with('message', 'Status Product Pending PO telah diedit');
                }
            } else {
                if (str_contains(request()->header('referer'), 'project-monitoring')) {
                    return redirect()->route('project-monitoring.show', $id)->with('success', 'Status proyek berhasil diperbarui.');
                }
                return redirect('/pending-po/' . $id)->with('message', 'Status Product Pending PO telah diedit');
            }
        });
    }
    public function changeType($id)
    {
        $pending = PendingPO::findOrFail($id);

        if ($pending->type === 'Project') {
            $pending->type = 'Non Project';
            $pending->project_category = null;
            $pending->project_status_step = null;
            $pending->save();

            return redirect()->route('pending-po.show', $id)
                ->with('message', 'Tipe pesanan berhasil dipindahkan ke Sales Order.');
        } else {
            $pending->type = 'Project';
            $pending->project_category = 'Service PM';
            $pending->project_status_step = 1;
            $pending->save();

            return redirect()->route('project-monitoring.show', $id)
                ->with('success', 'Tipe pesanan berhasil dipindahkan ke Project Monitoring.');
        }
    }
    public function add_comment(Request $request, $id)
    {
        $stats = ChangeStatus::where('id_pending', $id)->orderByDesc('date')->first();
        $comment = new Comment();
        $comment->id_status = $stats->id;
        $comment->id_user = Auth::user()->id;
        $comment->date = Carbon::now();
        $comment->comment = $request->comment;
        $comment->level = '1';
        // $comment->type = 'quotation';
        $commentSave = $comment->save();
        if ($commentSave) {
            return redirect('/pending-po/' . $id)->with('message', 'Comment Pending PO telah dibuat');
        }
    }
    public function updateAddresses(Request $request, $id)
    {
        $pending = PendingPO::findOrFail($id);
        $combine = $request->has('combine_shipping_and_parts') || $request->combine_shipping_and_parts == 1;
        $pending->combine_shipping_and_parts = $combine;

        $ship_type = $request->input('shipping_address_type', 'customer');
        $ship_manual = $ship_type === 'manual' ? $request->input('shipping_address_manual') : ($ship_type !== 'customer' ? $ship_type : null);

        $pending->shipping_address_type = ($ship_type === 'customer') ? 'customer' : 'manual';
        $pending->shipping_address_manual = $ship_manual;

        if ($combine) {
            $pending->doc_address_type = $pending->shipping_address_type;
            $pending->doc_address_manual = $pending->shipping_address_manual;
            $pending->doc_recipient_id = $request->input('shipping_recipient_id');
            $pending->shipping_recipient_id = $request->input('shipping_recipient_id');
        } else {
            $doc_type = $request->input('doc_address_type', 'customer');
            $doc_manual = $doc_type === 'manual' ? $request->input('doc_address_manual') : ($doc_type !== 'customer' ? $doc_type : null);

            $pending->doc_address_type = ($doc_type === 'customer') ? 'customer' : 'manual';
            $pending->doc_address_manual = $doc_manual;
            $pending->doc_recipient_id = $request->input('doc_recipient_id');
            $pending->shipping_recipient_id = $request->input('shipping_recipient_id');
        }
        $pending->save();

        return redirect()->back()->with('message', 'Alamat pengiriman berhasil diperbarui.');
    }
    public function deliveryEdit(Request $request, $id)
    {
        $pending = PendingPO::findOrFail($id);
        $hasApprovedInvoice = $this->hasApprovedInvoice($pending);
        if (!$hasApprovedInvoice) {
            return redirect()->back()->with('error', 'Proses logistik dikunci karena invoice belum di-approve oleh Accounting.');
        }
        $pending->delivery = $request->delivery;
        $pending->save();
        return redirect('/pending-po/' . $id)->with('message', 'Status Product Pending PO telah diedit');
    }
    public function pending_out($id)
    {
        $pending = PendingPO::with([
            'quote.invoice',
            'quote.pic.client',
            'shipping_recipient',
        ])->find($id);

        if (!$pending) {
            return redirect()->route('pending-po.index')->with('error', 'Pending PO tidak ditemukan');
        }

        $quote    = $pending->quote;
        $dPending = DetailPendingPO::where('id_pending', $id)->whereNot('status', '7')->get();

        // Dokumen references (null-safe)
        $invoiceNo = $quote?->invoice?->first()?->no_invoice ?? '';
        $poNo      = $quote?->invoice?->first()?->no_po ?? '';

        // Client info (null-safe)
        $clientName    = $quote?->pic?->client?->company ?? '';
        $clientAddress = $quote?->pic?->client?->address ?? '';
        $clientPic     = $pending->shipping_recipient?->name ?? $quote?->pic?->name ?? '';
        $clientPhone   = $pending->shipping_recipient?->phone ?? $quote?->pic?->phone ?? '';

        $detailClientFormatted = $clientName;
        if (!empty($clientPic)) {
            $detailClientFormatted .= "\nAttn: " . $clientPic . ($clientPhone ? " (" . $clientPhone . ")" : "");
        }
        if (!empty($clientAddress)) {
            $detailClientFormatted .= "\nAlamat: " . $clientAddress;
        }

        // Build itemsData from $dPending with shipment tracking
        $allProductOuts = ProductOut::where('id_pending', $pending->id)
            ->orWhere('id', $pending->id_product_out)
            ->with('detail')
            ->get();
        $allProductOutIds = $allProductOuts->pluck('id')->filter()->all();
        $shippedQtyMap = DetailProductOut::whereIn('id_product_out', $allProductOutIds)
            ->get()
            ->groupBy('id_serial_product');

        $fullRep   = [];
        $fullEquiv = [];
        $itemsData = [];
        $no = 0;

        foreach ($dPending as $item) {
            $equiv = $item->id_equivalent ? SerialProduct::with('product')->find($item->id_equivalent) : null;
            $fullEquiv[$no] = $equiv;
            $rep = $equiv ? DetailProduct::where('id_product', $equiv->id_product)->get() : collect([]);
            $fullRep[$no] = $rep;

            // Default replacement
            $defaultRep = null;
            if (!empty($item->id_replacement)) {
                $defaultRep = DetailProduct::with('product')->find($item->id_replacement);
            } elseif ($rep->isNotEmpty()) {
                $defaultRep = $rep->first();
            }

            $defaultRepId   = $defaultRep?->id ?? 0;
            $defaultRepText = $defaultRep
                ? "{$defaultRep->replacement} | {$defaultRep->product?->commodity} (BDG: {$defaultRep->stock}, BKS: {$defaultRep->warehouse_stock})"
                : ($item->id_equivalent ? '-- Pilih Replacement --' : '-- Non-Inventory / Ready Stock (Tanpa SKU Fisik) --');

            $itemName   = $equiv?->product?->commodity ?? $item->note ?? ('Item #' . ($no + 1));
            $itemQty    = (float)($item->service?->qty ?? 1);
            $alreadyShipped = $equiv ? (float)($shippedQtyMap->get($equiv->id)?->sum('qty') ?? 0) : 0;
            $remainingQty = max(0, $itemQty - $alreadyShipped);
            $itemPrice  = (float)($item->price ?? 0);
            $itemAmount = (float)($item->amount ?? ($remainingQty * $itemPrice));

            $itemsData[] = [
                'dPending'         => $item,
                'equiv'            => $equiv,
                'replacements'     => $rep,
                'default_rep'      => $defaultRep,
                'default_rep_id'   => $defaultRepId,
                'default_rep_text' => $defaultRepText,
                'default_stock'    => $defaultRep?->stock ?? 0,
                'default_wh_stock' => $defaultRep?->warehouse_stock ?? 0,
                'default_serial_id'=> $equiv?->id ?? 0,
                'name'             => $itemName,
                'qty_ordered'      => $itemQty,
                'qty_shipped'      => $alreadyShipped,
                'qty_remaining'    => $remainingQty,
                'qty'              => $remainingQty,
                'price'            => $itemPrice,
                'amount'           => $itemAmount,
                'warehouse'        => 'BDG',
                'is_non_inventory' => empty($item->id_equivalent),
            ];

            $no++;
        }

        // Financial defaults
        $subtotal   = $quote?->subtotal ?? array_sum(array_column($itemsData, 'amount'));
        $shipping   = $quote?->shipping ?? 0;
        $grandTotal = $quote?->total ?? ($subtotal + $shipping);

        // Auto-generate No. Barang Keluar
        $isKojisha = ($quote && $quote->flag === 'Kojisha')
            || (is_string($invoiceNo) && str_contains($invoiceNo, '/KII/'))
            || (is_string($poNo) && str_contains($poNo, 'KII'));
        $flag = $isKojisha ? 'Kojisha' : 'Reftech';

        $nextNoProductOut = (new \App\Http\Controllers\ProductOutController())->generateNoProductOut('BDG', $flag);

        return view('pages.pending.form', compact(
            'fullRep', 'fullEquiv', 'dPending', 'pending', 'quote', 'id',
            'nextNoProductOut', 'invoiceNo', 'poNo',
            'clientName', 'clientAddress', 'clientPic', 'clientPhone',
            'detailClientFormatted', 'itemsData',
            'subtotal', 'shipping', 'grandTotal', 'allProductOuts'
        ));
    }
    public function pending_out_project($id)
    {
        $pending = PendingPO::with([
            'quote.invoice',
            'quote.pic.client',
            'unitQuotation.invoices',
            'unitQuotation.client',
            'unitQuotation.pic',
            'unitQuotation.details',
            'shipping_recipient',
            'doc_recipient'
        ])->find($id);

        if (!$pending) {
            return redirect()->route('pending-po.index')->with('error', 'Pending PO tidak ditemukan');
        }

        $quote = $pending->quote ?? $pending->unitQuotation;
        $isUnit = (bool) $pending->id_unit_quotation;
        $dPending = DetailPendingPO::where('id_pending', $id)->whereNot('status', '7')->get();

        // Dokumen references
        $invoiceNo = $pending->unitQuotation?->invoices?->first()?->no_invoice 
            ?? $pending->quote?->invoice?->first()?->no_invoice 
            ?? '';
        $poNo = $pending->unitQuotation?->po_number 
            ?? $pending->unitQuotation?->invoices?->first()?->no_po 
            ?? $pending->quote?->invoice?->first()?->no_po 
            ?? '';

        // Client info
        $clientName = $pending->unitQuotation?->client?->company 
            ?? $pending->quote?->pic?->client?->company 
            ?? '';
        $clientAddress = $pending->unitQuotation?->address 
            ?? $pending->quote?->pic?->client?->address 
            ?? '';
        $clientPic = $pending->shipping_recipient?->name 
            ?? $pending->unitQuotation?->pic?->name 
            ?? $pending->quote?->pic?->name 
            ?? '';
        $clientPhone = $pending->shipping_recipient?->phone 
            ?? $pending->unitQuotation?->pic?->phone 
            ?? $pending->quote?->pic?->phone 
            ?? '';

        $detailClientFormatted = $clientName;
        if (!empty($clientPic)) {
            $detailClientFormatted .= "\nAttn: " . $clientPic . ($clientPhone ? " (" . $clientPhone . ")" : "");
        }
        if (!empty($clientAddress)) {
            $detailClientFormatted .= "\nAlamat: " . $clientAddress;
        }

        // Build itemsData from $dPending with shipment tracking
        $allProductOuts = ProductOut::where('id_pending', $pending->id)
            ->orWhere('id', $pending->id_product_out)
            ->with('detail')
            ->get();
        $allProductOutIds = $allProductOuts->pluck('id')->filter()->all();
        $shippedQtyMap = DetailProductOut::whereIn('id_product_out', $allProductOutIds)
            ->get()
            ->groupBy('id_serial_product');

        $fullRep = [];
        $fullEquiv = [];
        $itemsData = [];
        $no = 0;

        foreach ($dPending as $item) {
            $equiv = $item->id_equivalent ? SerialProduct::with('product')->find($item->id_equivalent) : null;
            $fullEquiv[$no] = $equiv;
            $rep = $equiv ? DetailProduct::where('id_product', $equiv->id_product)->get() : collect([]);
            $fullRep[$no] = $rep;

            // UnitQuotationDetail matching if applicable
            $unitDetail = null;
            if ($pending->unitQuotation && $pending->unitQuotation->details) {
                $unitDetail = $pending->unitQuotation->details->first(function ($d) use ($item) {
                    return ($item->id_equivalent && $d->id_equivalent == $item->id_equivalent)
                        || (!empty($item->note) && ($d->label === $item->note || str_contains($d->label ?? '', $item->note)));
                }) ?? $pending->unitQuotation->details->get($no);
            }

            $itemName = $unitDetail?->label ?? $equiv?->product?->commodity ?? $item->note ?? ('Item #' . ($no + 1));
            $itemDesc = $unitDetail?->description ?? '';
            $itemQty = (float)($unitDetail?->qty ?? $item->service?->qty ?? 1);
            $alreadyShipped = $equiv ? (float)($shippedQtyMap->get($equiv->id)?->sum('qty') ?? 0) : 0;
            $remainingQty = max(0, $itemQty - $alreadyShipped);
            $itemPrice = (float)($unitDetail?->price ?? $item->service?->price ?? 0);
            $itemAmount = (float)($unitDetail?->amount ?? ($remainingQty * $itemPrice));

            // Default replacement resolution
            $defaultRep = null;
            if (!empty($item->id_replacement)) {
                $defaultRep = DetailProduct::with('product')->find($item->id_replacement);
            } elseif ($rep->isNotEmpty()) {
                $defaultRep = $rep->first();
            }

            $defaultRepId = $defaultRep?->id ?? 0;
            $defaultRepText = $defaultRep
                ? "{$defaultRep->replacement} | {$defaultRep->product?->commodity} (BDG: {$defaultRep->stock}, BKS: {$defaultRep->warehouse_stock})"
                : ($item->id_equivalent ? '-- Pilih Replacement --' : '-- Non-Inventory / Ready Stock (Tanpa SKU Fisik) --');

            $itemsData[] = [
                'dPending' => $item,
                'unitDetail' => $unitDetail,
                'equiv' => $equiv,
                'replacements' => $rep,
                'default_rep' => $defaultRep,
                'default_rep_id' => $defaultRepId,
                'default_rep_text' => $defaultRepText,
                'default_stock' => $defaultRep?->stock ?? 0,
                'default_wh_stock' => $defaultRep?->warehouse_stock ?? 0,
                'default_serial_id' => $equiv?->id ?? 0,
                'name' => $itemName,
                'description' => $itemDesc,
                'qty_ordered' => $itemQty,
                'qty_shipped' => $alreadyShipped,
                'qty_remaining' => $remainingQty,
                'qty' => $remainingQty,
                'price' => $itemPrice,
                'amount' => $itemAmount,
                'warehouse' => 'BDG',
                'is_non_inventory' => empty($item->id_equivalent),
            ];

            $no++;
        }

        // Financial defaults
        $subtotal = $pending->unitQuotation?->subtotal 
            ?? $pending->quote?->subtotal 
            ?? array_sum(array_column($itemsData, 'amount'));
        $shipping = $pending->unitQuotation?->shipping 
            ?? $pending->quote?->shipping 
            ?? 0;
        $grandTotal = $pending->unitQuotation?->total 
            ?? ($subtotal + $shipping);

        // Auto-generate No. Barang Keluar (BK) sama seperti di form barang keluar manual
        $isKojisha = ($pending->quote && (method_exists($pending->quote, 'isKojisha') ? $pending->quote->isKojisha() : ($pending->quote->flag === 'Kojisha')))
            || ($pending->unitQuotation && (method_exists($pending->unitQuotation, 'isKojisha') ? $pending->unitQuotation->isKojisha() : false))
            || (is_string($invoiceNo) && str_contains($invoiceNo, '/KII/'))
            || (is_string($poNo) && str_contains($poNo, 'KII'));
            
        $flag = $isKojisha ? 'Kojisha' : 'Reftech';

        $nextNoProductOut = (new \App\Http\Controllers\ProductOutController())->generateNoProductOut('BDG', $flag);

        return view('pages.pending.form-project', compact(
            'fullRep',
            'fullEquiv',
            'dPending',
            'pending',
            'quote',
            'id',
            'isUnit',
            'nextNoProductOut',
            'invoiceNo',
            'poNo',
            'clientName',
            'clientAddress',
            'clientPic',
            'clientPhone',
            'detailClientFormatted',
            'itemsData',
            'subtotal',
            'shipping',
            'grandTotal',
            'allProductOuts'
        ));
    }

    public function searchReplacements(Request $request)
    {
        $search = $request->input('q');

        $query = DetailProduct::join('product', 'detail_product.id_product', '=', 'product.id')
            ->leftJoin('serial_product as sp', 'sp.id_product', '=', 'product.id')
            ->select(
                'detail_product.id',
                'detail_product.replacement',
                'detail_product.stock',
                'detail_product.warehouse_stock',
                'detail_product.id_product',
                'product.commodity',
                'product.detail_desc',
                'product.go',
                DB::raw('MIN(sp.id) as serial_id')
            )
            ->groupBy(
                'detail_product.id',
                'detail_product.replacement',
                'detail_product.stock',
                'detail_product.warehouse_stock',
                'detail_product.id_product',
                'product.commodity',
                'product.detail_desc',
                'product.go'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('detail_product.replacement', 'like', "%{$search}%")
                    ->orWhere('product.commodity', 'like', "%{$search}%")
                    ->orWhere('product.detail_desc', 'like', "%{$search}%")
                    ->orWhere('sp.pn', 'like', "%{$search}%");
            });
        }

        $results = $query->limit(40)->get()->map(function ($p) {
            $goCode = $p->go == 'Genuine' ? 'G' : 'R';
            $text = "{$p->replacement} | {$p->commodity} (BDG: {$p->stock}, BKS: {$p->warehouse_stock}) - {$goCode}";
            return [
                'id' => $p->id,
                'text' => $text,
                'replacement' => $p->replacement,
                'commodity' => $p->commodity,
                'stock' => (int)$p->stock,
                'warehouse_stock' => (int)$p->warehouse_stock,
                'serial_id' => (int)$p->serial_id,
                'go' => $goCode,
            ];
        });

        return response()->json($results);
    }

    public function product_out(Request $request, $id)
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
        $this->validate($request, $rule, $message);
        
        $pending = PendingPO::findOrFail($id);
        $hasApprovedInvoice = $this->hasApprovedInvoice($pending);
        if (!$hasApprovedInvoice) {
            return redirect()->back()->with('error', 'Proses logistik dikunci karena invoice belum di-approve oleh Accounting.');
        }

        return DB::transaction(function () use ($request, $pending, $id) {
            $isKojisha = ($pending->quote && (method_exists($pending->quote, 'isKojisha') ? $pending->quote->isKojisha() : ($pending->quote->flag === 'Kojisha')))
                || ($pending->unitQuotation && (method_exists($pending->unitQuotation, 'isKojisha') ? $pending->unitQuotation->isKojisha() : false))
                || ($request->flag === 'Kojisha')
                || (is_string($request->invoice) && str_contains($request->invoice, '/KII/'))
                || (is_string($request->po) && str_contains($request->po, 'KII'));
                
            $flag = $isKojisha ? 'Kojisha' : 'Reftech';

            $selectedItems = $request->input('selected_items'); // Array of selected row indices if from modal

            // Masukan Data ke Tabel Product Out
            $productOut = new ProductOut();
            $productOut->flag = $flag;
            $productOut->id_pending = $pending->id;
            $productOut->no_product_out = $request->filled('no_product_out') 
                ? trim($request->input('no_product_out')) 
                : (new \App\Http\Controllers\ProductOutController())->generateNoProductOut($request->warehouse[0] ?? 'BDG', $flag);
            $productOut->id_user = Auth::user()->id;
            $productOut->invoice = $request->invoice;
            $productOut->po = $request->po;
            $productOut->no_type = "1";
            $productOut->detail_client = $request->detail_client;
            $productOut->vers = $request->vers ?? 'Offline';
            $productOut->date = $request->date ?? date('Y-m-d');
            $productOut->note = $request->note ?? '-';
            $productOut->shipping = $request->shipping ?? 0;
            $productOut->total = $request->total ?? 0;
            $productOut->save();

            if (empty($pending->id_product_out)) {
                $pending->id_product_out = $productOut->id;
                $pending->save();
            }

            // Masukan Data Ke Tabel Detail Product Out
            $thisBatchQty = 0;
            $thisBatchTotalAmount = 0;

            if ($request->has('equivalent') && is_array($request->equivalent)) {
                foreach ($request->equivalent as $item => $value) {
                    // If selected_items is passed (from modal), skip rows that are not selected
                    if (is_array($selectedItems) && !in_array($item, $selectedItems) && !in_array((string)$item, $selectedItems)) {
                        continue;
                    }

                    $qtyOut = (int)($request->qty[$item] ?? 0);
                    if ($qtyOut <= 0) {
                        continue; // Skip items with 0 qty in this partial shipment
                    }
                    $thisBatchQty += $qtyOut;

                    $itemPrice = (int)($request->price[$item] ?? 0);
                    $itemAmount = (int)($request->amount[$item] ?? ($qtyOut * $itemPrice));
                    $thisBatchTotalAmount += $itemAmount;

                    $dProductIn = new DetailProductOut();
                    $dProductIn->id_product_out = $productOut->id;
                    $dProductIn->id_detail_product = !empty($request->replacement[$item]) ? (int)$request->replacement[$item] : 0;
                    $dProductIn->id_serial_product = !empty($request->equivalent[$item]) ? (int)$request->equivalent[$item] : 0;
                    $dProductIn->qty = $qtyOut;
                    $dProductIn->price = $itemPrice;
                    $dProductIn->amount = $itemAmount;
                    $dProductIn->warehouse = $request->warehouse[$item] ?? 'BDG';
                    if (!empty($request->replacement[$item])) {
                        $productD = DetailProduct::where('id', $request->replacement[$item])->first();
                        if ($productD) {
                            if (($request->warehouse[$item] ?? 'BDG') == 'BDG') {
                                $productD->stock -= $qtyOut;
                            } else {
                                $productD->warehouse_stock -= $qtyOut;
                            }
                            $productD->save();
                            $product = Product::where('id', $productD->id_product)->first();
                            if ($product) {
                                $product->pending_stock -= $qtyOut;
                                $product->save();
                            }
                        }
                    }
                    $dProductIn->save();
                }
            }

            if ($thisBatchQty === 0) {
                // If nothing was actually selected, rollback and throw error
                throw new \Exception('Minimal pilih 1 item dengan kuantitas lebih dari 0 untuk dikirim.');
            }

            if (empty($productOut->total) || $productOut->total == 0) {
                $productOut->total = $thisBatchTotalAmount + (int)$productOut->shipping;
                $productOut->save();
            }

            // Hitung total seluruh pengiriman untuk Pending PO ini
            $allProductOutIds = ProductOut::where('id_pending', $pending->id)
                ->orWhere('id', $pending->id_product_out)
                ->pluck('id')
                ->filter()
                ->unique()
                ->all();

            $totalShippedQty = (int) DetailProductOut::whereIn('id_product_out', $allProductOutIds)->sum('qty');

            $totalOrderedQty = 0;
            if ($pending->id_quotation) {
                $totalOrderedQty = (int) DetailQuotation::where('id_quotation', $pending->id_quotation)->sum('qty');
                if ($totalOrderedQty === 0) {
                    $totalOrderedQty = (int) DetailPendingPO::where('id_pending', $pending->id)->whereNot('status', '7')->sum(DB::raw('bdg + bks'));
                }
            } elseif ($pending->id_unit_quotation) {
                $totalOrderedQty = (int) UnitQuotationDetail::where('id_unit_quotation', $pending->id_unit_quotation)->sum('qty');
                if ($totalOrderedQty === 0) {
                    $totalOrderedQty = (int) DetailPendingPO::where('id_pending', $pending->id)->whereNot('status', '7')->sum(DB::raw('bdg + bks'));
                }
            } else {
                $totalOrderedQty = (int) DetailPendingPO::where('id_pending', $pending->id)->whereNot('status', '7')->sum(DB::raw('bdg + bks'));
            }

            // Tentukan status: Done (6) jika seluruh barang terkirim vs Partial Delivery (8) jika baru sebagian
            if ($totalShippedQty >= $totalOrderedQty && $totalOrderedQty > 0) {
                $pending->status = '6'; // Otomatis Done
                $statusNote = 'Done (Seluruh barang telah terkirim: ' . $totalShippedQty . '/' . $totalOrderedQty . ')';
            } elseif ($totalShippedQty > 0) {
                $pending->status = '8'; // Partial Delivery
                $statusNote = 'Partial Delivery (' . $totalShippedQty . '/' . $totalOrderedQty . ' item terkirim)';
            }
            $pending->save();

            // Log activity status change
            if (isset($statusNote)) {
                $changeStatus = new ChangeStatus();
                $changeStatus->id_pending = $pending->id;
                $changeStatus->status = $pending->status;
                $changeStatus->note = $statusNote;
                $changeStatus->date = Carbon::now();
                $changeStatus->save();
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat Jalan / Barang Keluar berhasil dibuat (' . $productOut->no_product_out . ')',
                    'product_out_id' => $productOut->id,
                    'no_product_out' => $productOut->no_product_out,
                ]);
            }

            return redirect()->back()->with('message', 'Surat Jalan / Barang Keluar berhasil dibuat (' . $productOut->no_product_out . ')');
        });
    }

    public function indexSOrder(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        if (in_array($role, ['Client', 'Guest'])) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman Sales Order.');
        }

        $selectedYear = $request->get('year', date('Y'));

        // Query available years for filter dropdown via fast cached query
        $availableYears = Cache::remember('sorder_available_years', 3600, function () {
            $yearsFromQuotes = DB::table('quotation')->whereNotNull('po_date')->selectRaw('DISTINCT YEAR(po_date) as yr')->pluck('yr')->all();
            $yearsFromPending = DB::table('pending_po')->whereNotNull('date')->selectRaw('DISTINCT YEAR(date) as yr')->pluck('yr')->all();
            return collect(array_merge($yearsFromQuotes, $yearsFromPending, [(int)date('Y')]))->filter()->unique()->sortDesc()->values()->all();
        });

        // Query PendingPO directly filtered by year and role at SQL level
        $pendingQuery = PendingPO::query();
        if ($selectedYear !== 'all') {
            $pendingQuery->leftJoin('quotation', 'pending_po.id_quotation', '=', 'quotation.id')
                ->whereRaw('YEAR(COALESCE(quotation.po_date, pending_po.date)) = ?', [$selectedYear])
                ->select('pending_po.*');
        }
        if ($role === 'Sales') {
            $pendingQuery->where(function ($q) {
                $q->whereHas('quote', fn($q2) => $q2->where('id_sales', Auth::id()))
                  ->orWhereHas('unitQuotation', fn($q2) => $q2->where('id_sales', Auth::id()));
            });
        }

        $allPending = $pendingQuery->with([
            'quote:id,id_pic,id_sales,po_date,nett,total_no_tax,harga_total,flag,note',
            'quote.pic:id,id_client',
            'quote.pic.client:id,company,info,area',
            'quote.sales:id,name,image',
            'quote.invoice:id,id_quotation,no_invoice,no_po,flag,status_p,term',
            'unitQuotation:id,id_client,id_sales,po_number,payment,payment_method,subtotal,diskon,total,tax_amount',
            'unitQuotation.client:id,company,info,area',
            'unitQuotation.sales:id,name,image',
            'unitQuotation.invoices:id,id_unit_quotation,no_invoice,no_po,flag,status_p,term',
            'parentPending:id,no_pending,type,status,date,title',
            'linkedChildren:id,parent_id,no_pending,type,status,date,title',
        ])->get()
          ->sortByDesc(function ($order) {
              return $order->quote?->po_date ?? $order->date ?? '';
          })->values();

        $defaultAvatar = asset('assets/img/avatars/1.png');
        $projectBaseUrl = url('/project-monitoring') . '/';
        $pendingBaseUrl = url('/pending-po') . '/';

        // Batch fetch auxiliary costs across all records in fast SQL queries (converted to native arrays)
        $allPendingIds = $allPending->pluck('id');

        $matCosts = PurchaseRequestDetail::join('purchase_request', 'purchase_request.id', '=', 'purchase_request_detail.id_purchase_request')
            ->whereIn('purchase_request.id_pending', $allPendingIds)
            ->where('purchase_request.status', '3')
            ->groupBy('purchase_request.id_pending')
            ->selectRaw('purchase_request.id_pending, SUM(purchase_request_detail.amount) as total')
            ->pluck('total', 'id_pending')
            ->all();

        $shipCosts = Expanse::whereIn('id_pending', $allPendingIds)
            ->where('type', 'Resi')
            ->groupBy('id_pending')->selectRaw('id_pending, SUM(cost) as total')
            ->pluck('total', 'id_pending')
            ->all();

        $projectPendingIds = $allPending->where('type', 'Project')->pluck('id');
        $genCosts = ProjectExpense::whereIn('id_pending', $projectPendingIds)
            ->groupBy('id_pending')->selectRaw('id_pending, SUM(amount) as total')
            ->pluck('total', 'id_pending')
            ->all();

        $allQuoteIds = $allPending->pluck('id_quotation')->filter()->all();
        $allUnitQuoteIds = $allPending->pluck('id_unit_quotation')->filter()->all();

        // Batch fetch order items for quick preview drawer
        $allDetQuotes = !empty($allQuoteIds)
            ? DetailQuotation::with(['equivalent.product'])
                ->whereIn('id_quotation', $allQuoteIds)
                ->get()
                ->groupBy('id_quotation')
            : collect();

        $allUnitQuoteDetails = !empty($allUnitQuoteIds)
            ? UnitQuotationDetail::with(['equivalent.product', 'unit'])
                ->whereIn('id_unit_quotation', $allUnitQuoteIds)
                ->get()
                ->groupBy('id_unit_quotation')
            : collect();

        $allPendingDetails = !empty($allPendingIds)
            ? DetailPendingPO::with(['equivalent.product', 'service'])
                ->whereIn('id_pending', $allPendingIds)
                ->get()
                ->groupBy('id_pending')
            : collect();

        $confirmedQuoteIds = [];
        $confirmedUnitQuoteIds = [];
        if (!empty($allQuoteIds) || !empty($allUnitQuoteIds)) {
            $paymentRows = DB::table('payment')
                ->where(function($q) use ($allQuoteIds, $allUnitQuoteIds) {
                    if (!empty($allQuoteIds)) {
                        $q->whereIn('id_quotation', $allQuoteIds);
                    }
                    if (!empty($allUnitQuoteIds)) {
                        $q->orWhereIn('id_unit_quotation', $allUnitQuoteIds);
                    }
                })
                ->where('level', 1)
                ->select('id_quotation', 'id_unit_quotation')
                ->get();

            foreach ($paymentRows as $row) {
                if ($row->id_quotation) $confirmedQuoteIds[$row->id_quotation] = true;
                if ($row->id_unit_quotation) $confirmedUnitQuoteIds[$row->id_unit_quotation] = true;
            }
        }

        // Micro-caches for repeated string parsing & asset URLs
        $dateCache = [];
        $avatarCache = [];
        $termCache = [];

        // Pre-allocate collections for single-pass bucketing (eliminates 11 collection filters and multiple sum loops)
        $allOrders = collect();
        $projects = collect();

        $newOrders = collect();
        $checkPartsOrders = collect();
        $partialDeliveryOrders = collect();
        $deliveryOrders = collect();
        $completedOrders = collect();
        $returnOrders = collect();
        $delayedOrders = collect();

        $newProjects = collect();
        $checkPartsProjects = collect();
        $schedulingProjects = collect();
        $inProgressProjects = collect();
        $completedProjects = collect();

        $totalRevenueSOrder = 0;
        $totalCostSOrder = 0;

        $totalRevenueProject = 0;
        $totalMaterialProject = 0;
        $totalGeneralProject = 0;
        $totalShippingProject = 0;

        // Batch pre-fetch all ProductOuts and DetailProductOuts for all pending orders
        $allPendingIds = $allPending->pluck('id')->all();
        $allProductOutLegacyIds = $allPending->pluck('id_product_out')->filter()->unique()->all();

        $allProductOuts = ProductOut::where(function($q) use ($allPendingIds, $allProductOutLegacyIds) {
            if (!empty($allPendingIds)) {
                $q->whereIn('id_pending', $allPendingIds);
            }
            if (!empty($allProductOutLegacyIds)) {
                $q->orWhereIn('id', $allProductOutLegacyIds);
            }
        })
        ->with('detail')
        ->orderByDesc('id')
        ->get();

        $productOutsByPending = [];
        foreach ($allProductOuts as $poItem) {
            if ($poItem->id_pending) {
                $productOutsByPending[$poItem->id_pending][] = $poItem;
            }
        }
        foreach ($allPending as $p) {
            if ($p->id_product_out) {
                $found = false;
                if (isset($productOutsByPending[$p->id])) {
                    foreach ($productOutsByPending[$p->id] as $existingPo) {
                        if ($existingPo->id == $p->id_product_out) {
                            $found = true;
                            break;
                        }
                    }
                }
                if (!$found) {
                    $legacy = $allProductOuts->firstWhere('id', $p->id_product_out);
                    if ($legacy) {
                        $productOutsByPending[$p->id][] = $legacy;
                    }
                }
            }
        }

        // ==========================================
        // UNIFIED SINGLE-PASS PROCESSING & BUCKETING
        // ==========================================
        $allMasterOrders = $allPending->map(function ($order) use (
            $matCosts, $shipCosts, $genCosts, $defaultAvatar,
            $allDetQuotes, $allUnitQuoteDetails, $allPendingDetails,
            $confirmedQuoteIds, $confirmedUnitQuoteIds,
            $productOutsByPending,
            $projectBaseUrl, $pendingBaseUrl, &$dateCache, &$avatarCache,
            $allOrders, $projects,
            $newOrders, $checkPartsOrders, $partialDeliveryOrders, $deliveryOrders, $completedOrders, $returnOrders, $delayedOrders,
            $newProjects, $checkPartsProjects, $schedulingProjects, $inProgressProjects, $completedProjects,
            &$totalRevenueSOrder, &$totalCostSOrder,
            &$totalRevenueProject, &$totalMaterialProject, &$totalGeneralProject, &$totalShippingProject
        ) {
            $isProject = ($order->type === 'Project');
            $order->order_type = $isProject ? 'Project' : 'Non-Project';

            // Parent & Linked PO references
            $order->parent_no_pending = $order->parentPending?->no_pending;
            $order->is_parent = $order->linkedChildren && $order->linkedChildren->isNotEmpty();
            $order->is_child = !empty($order->parent_id);
            $order->linked_children_count = $order->linkedChildren ? $order->linkedChildren->count() : 0;
            $order->linked_children_list = $order->linkedChildren ? $order->linkedChildren->map(fn($c) => [
                'id' => $c->id,
                'no_pending' => $c->no_pending,
                'title' => $c->title,
            ])->values()->all() : [];

            $quote = $order->quote;
            $unitQuote = $order->unitQuotation;

            $orderDate = $quote?->po_date ?? $order->date;
            $order->order_date = $orderDate;
            if ($orderDate) {
                if (!isset($dateCache[$orderDate])) {
                    $ts = strtotime($orderDate);
                    $dateCache[$orderDate] = [$ts, date('d-m-Y', $ts)];
                }
                [$order->date_timestamp, $order->formatted_date] = $dateCache[$orderDate];
            } else {
                $order->date_timestamp = 0;
                $order->formatted_date = '-';
            }

            $order->company = $unitQuote?->client?->company
                ?? $quote?->pic?->client?->company
                ?? '-';
            $order->sales_name = $unitQuote?->sales?->name
                ?? $quote?->sales?->name
                ?? '-';
            $salesImg = $unitQuote?->sales?->image ?? $quote?->sales?->image;
            $order->sales_avatar = $avatarCache[$salesImg] ??= ($salesImg ? asset($salesImg) : $defaultAvatar);

            $firstInv = $unitQuote?->invoices?->first() ?? $quote?->invoice?->first();

            $order->no_po = $unitQuote ? ($unitQuote->po_number ?? '-') : ($firstInv?->no_po ?? '-');
            $order->detail_route = ($isProject && !$order->id_unit_quotation)
                ? $projectBaseUrl . $order->id
                : $pendingBaseUrl . $order->id;

            // Flag (RJO / KII)
            $flag = 'RJO';
            if ($firstInv) {
                if ($firstInv->flag === 'Kojisha' || ($firstInv->no_invoice && str_contains($firstInv->no_invoice, '/KII/'))) {
                    $flag = 'KII';
                } elseif ($firstInv->flag === 'Reftech' || ($firstInv->no_invoice && str_contains($firstInv->no_invoice, '/RJO/'))) {
                    $flag = 'RJO';
                }
            }
            if ($flag === 'RJO') {
                $quoteFlag = $unitQuote?->flag ?? $quote?->flag ?? null;
                $clientInfo = $unitQuote?->client?->info ?? $quote?->pic?->client?->info ?? null;
                if ($quoteFlag === 'Kojisha' || $clientInfo === 'Kojisha') {
                    $flag = 'KII';
                }
            }
            $order->flag = $flag;

            // Payment info (Rule: PAID = Confirmed DP/CBD/Lunas, Credit Paid = Tempo/Credit, UNPAID = Belum bayar)
            $hasConfirmedPayment = false;
            if ($quote) {
                if (isset($confirmedQuoteIds[$quote->id])) {
                    $hasConfirmedPayment = true;
                } elseif ($quote->invoice) {
                    foreach ($quote->invoice as $inv) {
                        if ($inv->status_p == 1) {
                            $hasConfirmedPayment = true;
                            break;
                        }
                    }
                }
            }
            if ($unitQuote) {
                if (isset($confirmedUnitQuoteIds[$unitQuote->id])) {
                    $hasConfirmedPayment = true;
                } elseif ($unitQuote->invoices && $unitQuote->invoices->where('status_p', 1)->isNotEmpty()) {
                    $hasConfirmedPayment = true;
                }
            }

            $invTerm = $firstInv?->term;
            $quoteTerm = $quote?->termcon;
            $termStr = $invTerm ?: ($quoteTerm ?: ($unitQuote?->payment ?: ($unitQuote?->payment_method ?: null)));

            $isCredit = false;
            if ($termStr && preg_match('/(day|hari|tempo|credit|kredit|top|net)/i', $termStr)) {
                $isCredit = true;
            }

            if ($hasConfirmedPayment) {
                $order->payment_label = 'PAID';
                $order->payment_badge = 'bg-label-success';
                $order->payment_detail = $termStr ?: 'Lunas / Terkonfirmasi';
            } elseif ($isCredit) {
                $order->payment_label = 'Credit Paid';
                $order->payment_badge = 'bg-label-purple';
                $order->payment_detail = $termStr ?: 'Credit / Tempo';
            } else {
                $order->payment_label = 'UNPAID';
                $order->payment_badge = 'bg-label-danger';
                $order->payment_detail = $termStr ?: 'Belum Ada Pembayaran';
            }

            // Progress & Status badges
            $progressLabel = 'New';
            $progressBadge = 'bg-label-secondary';

            if ($isProject) {
                if ($order->status == 6) {
                    $progressLabel = 'Done';
                    $progressBadge = 'bg-label-success';
                } elseif ($order->status == 0) {
                    $progressLabel = 'New';
                    $progressBadge = 'bg-label-secondary';
                } elseif ($order->status == 8) {
                    $progressLabel = 'Partial Delivery';
                    $progressBadge = 'bg-label-info';
                } elseif ($order->status == 9) {
                    $progressLabel = 'Delayed';
                    $progressBadge = 'bg-label-danger';
                } else {
                    $step = $order->project_status_step ?? 1;
                    $cat = $order->project_category ?? 'Service PM';
                    if ($step == 1) {
                        $progressBadge = 'bg-label-warning';
                        $progressLabel = in_array($cat, ['Rental', 'Unit']) ? 'Check Unit' : ($cat === 'Piping' ? 'Check Material' : 'Check Parts');
                    } elseif ($step == 2) {
                        $progressBadge = 'bg-label-info';
                        $progressLabel = in_array($cat, ['Rental', 'Unit']) ? 'Jadwal Pickup' : ($cat === 'Piping' ? 'Kirim Material' : 'Waiting Schedule');
                    } elseif ($step == 3) {
                        $progressBadge = 'bg-label-primary';
                        $progressLabel = ($cat === 'Rental') ? 'Commissioning' : (($cat === 'Unit') ? 'Jadwal Commissioning' : 'In Progress');
                    } else {
                        $progressBadge = 'bg-label-primary';
                        $progressLabel = ($cat === 'Rental') ? 'Pickup Kembali Unit' : (($cat === 'Piping') ? 'Commissioning' : 'In Progress');
                    }
                }
            } else {
                switch ($order->status) {
                    case 0: $progressLabel = 'New PO'; $progressBadge = 'bg-label-secondary'; break;
                    case 1: $progressLabel = 'On Check'; $progressBadge = 'bg-label-warning'; break;
                    case 2: $progressLabel = 'Ready Stock'; $progressBadge = 'bg-label-info'; break;
                    case 3: $progressLabel = 'Kurang'; $progressBadge = 'bg-label-danger'; break;
                    case 4: $progressLabel = 'Pre-delivery'; $progressBadge = 'bg-label-primary'; break;
                    case 5: $progressLabel = 'Delivery Process'; $progressBadge = 'bg-label-info'; break;
                    case 6: $progressLabel = 'Done'; $progressBadge = 'bg-label-success'; break;
                    case 7: $progressLabel = 'Cancel'; $progressBadge = 'bg-label-danger'; break;
                    case 8: $progressLabel = 'Partial Delivery'; $progressBadge = 'bg-label-info'; break;
                    case 9: $progressLabel = 'Delayed'; $progressBadge = 'bg-label-danger'; break;
                    default: $progressLabel = 'In Progress'; $progressBadge = 'bg-label-primary'; break;
                }
            }
            $order->progress_label = $progressLabel;
            $order->progress_badge = $progressBadge;

            // Extract linked ProductOut shipments and shipped qty mapping
            $linkedProductOuts = $productOutsByPending[$order->id] ?? [];
            $shippedQtyMapBySerial = [];
            $totalShippedQtyAll = 0;
            $productOutsSummaryList = [];

            foreach ($linkedProductOuts as $poItem) {
                $poQtySum = 0;
                if ($poItem->detail) {
                    foreach ($poItem->detail as $pod) {
                        $podQty = (float)($pod->qty ?? 0);
                        $poQtySum += $podQty;
                        $sid = (int)($pod->id_serial_product ?? 0);
                        if ($sid > 0) {
                            $shippedQtyMapBySerial[$sid] = ($shippedQtyMapBySerial[$sid] ?? 0) + $podQty;
                        }
                    }
                }
                $totalShippedQtyAll += $poQtySum;
                $productOutsSummaryList[] = [
                    'id' => $poItem->id,
                    'no_product_out' => $poItem->no_product_out ?: ('BK #' . $poItem->id),
                    'date' => $poItem->date ? date('d-m-Y', strtotime($poItem->date)) : '-',
                    'total_qty' => $poQtySum,
                    'flag' => $poItem->flag,
                    'show_url' => route('product-out.show', $poItem->id),
                ];
            }

            // Extract items detail for quick slide drawer
            $itemsList = [];
            $searchTexts = [];
            $totalOrderedQtyAll = 0;

            if ($order->id_quotation && isset($allDetQuotes[$order->id_quotation])) {
                foreach ($allDetQuotes[$order->id_quotation] as $dq) {
                    $brandPn = $dq->equivalent ? trim(($dq->equivalent->brand ?? '') . ' ' . ($dq->equivalent->pn ?? '')) : '';
                    $name = $brandPn ?: ($dq->detail_product ?: 'Item #' . $dq->id);
                    $desc = $dq->detail_product ?: ($dq->equivalent?->product?->description ?? '');
                    $go = $dq->equivalent?->product?->go ?? null;
                    $qty = (float) ($dq->qty ?? 1);
                    $totalOrderedQtyAll += $qty;
                    $equivId = (int) ($dq->id_equivalent ?? 0);
                    $shippedQty = $equivId ? ($shippedQtyMapBySerial[$equivId] ?? 0) : 0;
                    $remainingQty = max(0, $qty - $shippedQty);
                    $fulfillmentStatus = ($shippedQty >= $qty && $qty > 0) ? 'fulfilled' : ($shippedQty > 0 ? 'partial' : 'unfulfilled');

                    $unit = $dq->info_qty ?: ($dq->equivalent?->product?->unit ?: 'pcs');
                    $bdg = (int) ($dq->equivalent?->product?->stock ?? 0);
                    $bks = (int) ($dq->equivalent?->product?->warehouse_stock ?? 0);
                    $totalStock = $bdg + $bks;
                    $statusText = match ((int) ($dq->status ?? 0)) {
                        1 => 'On Check',
                        2 => 'Ready Stock',
                        3 => 'Kurang',
                        4 => 'Pre-Order',
                        5 => 'Delivery Process',
                        6 => 'Done',
                        default => 'Belum Di Cek',
                    };
                    $statusBadge = match ((int) ($dq->status ?? 0)) {
                        1 => 'bg-label-warning',
                        2 => 'bg-label-info',
                        3 => 'bg-label-danger',
                        4 => 'bg-label-primary',
                        5 => 'bg-label-info',
                        6 => 'bg-label-success',
                        default => 'bg-label-secondary',
                    };

                    $itemsList[] = [
                        'name' => $name,
                        'brand_pn' => $brandPn,
                        'description' => $desc,
                        'go' => $go,
                        'qty' => $qty,
                        'qty_shipped' => $shippedQty,
                        'qty_remaining' => $remainingQty,
                        'fulfillment_status' => $fulfillmentStatus,
                        'unit' => $unit,
                        'bdg' => $bdg,
                        'bks' => $bks,
                        'total_stock' => $totalStock,
                        'is_enough' => $totalStock >= $remainingQty,
                        'status' => $statusText,
                        'status_badge' => $statusBadge,
                        'note' => $dq->note ?: '',
                    ];
                    $searchTexts[] = $name . ' ' . $desc . ' ' . $brandPn;
                }
            } elseif ($order->id_unit_quotation) {
                if (isset($allPendingDetails[$order->id]) && $allPendingDetails[$order->id]->isNotEmpty()) {
                    foreach ($allPendingDetails[$order->id] as $dp) {
                        $brandPn = $dp->equivalent ? trim(($dp->equivalent->brand ?? '') . ' ' . ($dp->equivalent->pn ?? '')) : '';
                        $name = $brandPn ?: ($dp->note ?: ($dp->service?->detail_service ?: 'Item #' . $dp->id));
                        $desc = $dp->equivalent?->product?->description ?? ($dp->service?->detail_service ?? '');
                        $go = $dp->equivalent?->product?->go ?? null;
                        $qty = (float) ($dp->service?->qty ?? ($dp->bdg + $dp->bks ?: 1));
                        $totalOrderedQtyAll += $qty;
                        $equivId = (int) ($dp->id_equivalent ?? 0);
                        $shippedQty = $equivId ? ($shippedQtyMapBySerial[$equivId] ?? 0) : 0;
                        $remainingQty = max(0, $qty - $shippedQty);
                        $fulfillmentStatus = ($shippedQty >= $qty && $qty > 0) ? 'fulfilled' : ($shippedQty > 0 ? 'partial' : 'unfulfilled');

                        $unit = $dp->service?->unit ?: ($dp->equivalent?->product?->unit ?: 'pcs');
                        $bdg = (int) ($dp->bdg ?? ($dp->equivalent?->product?->stock ?? 0));
                        $bks = (int) ($dp->bks ?? ($dp->equivalent?->product?->warehouse_stock ?? 0));
                        $totalStock = $bdg + $bks;
                        $statusText = match ((int) ($dp->status ?? 0)) {
                            1 => 'On Check',
                            2 => 'Ready Stock',
                            3 => 'Kurang',
                            4 => 'Pre-Order',
                            5 => 'Delivery Process',
                            6 => 'Done',
                            default => 'Belum Di Cek',
                        };
                        $statusBadge = match ((int) ($dp->status ?? 0)) {
                            1 => 'bg-label-warning',
                            2 => 'bg-label-info',
                            3 => 'bg-label-danger',
                            4 => 'bg-label-primary',
                            5 => 'bg-label-info',
                            6 => 'bg-label-success',
                            default => 'bg-label-secondary',
                        };

                        $itemsList[] = [
                            'name' => $name,
                            'brand_pn' => $brandPn,
                            'description' => $desc,
                            'go' => $go,
                            'qty' => $qty,
                            'qty_shipped' => $shippedQty,
                            'qty_remaining' => $remainingQty,
                            'fulfillment_status' => $fulfillmentStatus,
                            'unit' => $unit,
                            'bdg' => $bdg,
                            'bks' => $bks,
                            'total_stock' => $totalStock,
                            'is_enough' => $totalStock >= $remainingQty,
                            'status' => $statusText,
                            'status_badge' => $statusBadge,
                            'note' => $dp->note ?: '',
                        ];
                        $searchTexts[] = $name . ' ' . $desc . ' ' . $brandPn;
                    }
                } elseif (isset($allUnitQuoteDetails[$order->id_unit_quotation])) {
                    foreach ($allUnitQuoteDetails[$order->id_unit_quotation] as $uqd) {
                        $brandPn = $uqd->equivalent ? trim(($uqd->equivalent->brand ?? '') . ' ' . ($uqd->equivalent->pn ?? '')) : '';
                        $name = $uqd->label ?: ($brandPn ?: ($uqd->unit?->name ?? 'Item #' . $uqd->id));
                        $desc = $uqd->description ?: ($uqd->equivalent?->product?->description ?? '');
                        $go = $uqd->equivalent?->product?->go ?? null;
                        $qty = (float) ($uqd->qty ?? 1);
                        $totalOrderedQtyAll += $qty;
                        $equivId = (int) ($uqd->id_equivalent ?? 0);
                        $shippedQty = $equivId ? ($shippedQtyMapBySerial[$equivId] ?? 0) : 0;
                        $remainingQty = max(0, $qty - $shippedQty);
                        $fulfillmentStatus = ($shippedQty >= $qty && $qty > 0) ? 'fulfilled' : ($shippedQty > 0 ? 'partial' : 'unfulfilled');

                        $unit = $uqd->info_qty ?: 'pcs';

                        $itemsList[] = [
                            'name' => $name,
                            'brand_pn' => $brandPn,
                            'description' => $desc,
                            'go' => $go,
                            'qty' => $qty,
                            'qty_shipped' => $shippedQty,
                            'qty_remaining' => $remainingQty,
                            'fulfillment_status' => $fulfillmentStatus,
                            'unit' => $unit,
                            'bdg' => 0,
                            'bks' => 0,
                            'total_stock' => 0,
                            'is_enough' => true,
                            'status' => 'Order Item',
                            'status_badge' => 'bg-label-primary',
                            'note' => '',
                        ];
                        $searchTexts[] = $name . ' ' . $desc . ' ' . $brandPn;
                    }
                }
            } elseif (isset($allPendingDetails[$order->id]) && $allPendingDetails[$order->id]->isNotEmpty()) {
                foreach ($allPendingDetails[$order->id] as $dp) {
                    $brandPn = $dp->equivalent ? trim(($dp->equivalent->brand ?? '') . ' ' . ($dp->equivalent->pn ?? '')) : '';
                    $name = $brandPn ?: ($dp->note ?: ($dp->service?->detail_service ?: 'Item #' . $dp->id));
                    $desc = $dp->equivalent?->product?->description ?? ($dp->service?->detail_service ?? '');
                    $go = $dp->equivalent?->product?->go ?? null;
                    $qty = (float) ($dp->service?->qty ?? ($dp->bdg + $dp->bks ?: 1));
                    $totalOrderedQtyAll += $qty;
                    $equivId = (int) ($dp->id_equivalent ?? 0);
                    $shippedQty = $equivId ? ($shippedQtyMapBySerial[$equivId] ?? 0) : 0;
                    $remainingQty = max(0, $qty - $shippedQty);
                    $fulfillmentStatus = ($shippedQty >= $qty && $qty > 0) ? 'fulfilled' : ($shippedQty > 0 ? 'partial' : 'unfulfilled');

                    $unit = $dp->service?->unit ?: ($dp->equivalent?->product?->unit ?: 'pcs');
                    $bdg = (int) ($dp->bdg ?? 0);
                    $bks = (int) ($dp->bks ?? 0);
                    $totalStock = $bdg + $bks;
                    $statusText = match ((int) ($dp->status ?? 0)) {
                        1 => 'On Check',
                        2 => 'Ready Stock',
                        3 => 'Kurang',
                        4 => 'Pre-Order',
                        5 => 'Delivery Process',
                        6 => 'Done',
                        default => 'Belum Di Cek',
                    };
                    $statusBadge = match ((int) ($dp->status ?? 0)) {
                        1 => 'bg-label-warning',
                        2 => 'bg-label-info',
                        3 => 'bg-label-danger',
                        4 => 'bg-label-primary',
                        5 => 'bg-label-info',
                        6 => 'bg-label-success',
                        default => 'bg-label-secondary',
                    };

                    $itemsList[] = [
                        'name' => $name,
                        'brand_pn' => $brandPn,
                        'description' => $desc,
                        'go' => $go,
                        'qty' => $qty,
                        'qty_shipped' => $shippedQty,
                        'qty_remaining' => $remainingQty,
                        'fulfillment_status' => $fulfillmentStatus,
                        'unit' => $unit,
                        'bdg' => $bdg,
                        'bks' => $bks,
                        'total_stock' => $totalStock,
                        'is_enough' => $totalStock >= $remainingQty,
                        'status' => $statusText,
                        'status_badge' => $statusBadge,
                        'note' => $dp->note ?: '',
                    ];
                    $searchTexts[] = $name . ' ' . $desc . ' ' . $brandPn;
                }
            }

            if (empty($itemsList) && !empty($order->title)) {
                $itemsList[] = [
                    'name' => $order->title,
                    'brand_pn' => '',
                    'description' => $order->title,
                    'go' => null,
                    'qty' => 1,
                    'qty_shipped' => $totalShippedQtyAll > 0 ? 1 : 0,
                    'qty_remaining' => $totalShippedQtyAll > 0 ? 0 : 1,
                    'fulfillment_status' => $totalShippedQtyAll > 0 ? 'fulfilled' : 'unfulfilled',
                    'unit' => 'item',
                    'bdg' => 0,
                    'bks' => 0,
                    'total_stock' => 0,
                    'is_enough' => true,
                    'status' => $order->progress_label,
                    'status_badge' => $order->progress_badge,
                    'note' => '',
                ];
                $searchTexts[] = $order->title;
                $totalOrderedQtyAll = 1;
            }

            $order->items_list = $itemsList;
            $order->items_search_text = implode(' ', $searchTexts);

            // Revenue
            $uqSub = $unitQuote ? (floatval($unitQuote->subtotal ?? 0) - floatval($unitQuote->diskon ?? 0)) : 0;
            if ($unitQuote && $uqSub <= 0) {
                $uqSub = floatval($unitQuote->total ?? 0) - floatval($unitQuote->tax_amount ?? 0);
            }
            $order->revenue = $unitQuote ? $uqSub : floatval($quote?->nett ?? 0);

            $order->drawer_data = [
                'id' => $order->id,
                'no_pending' => $order->no_pending,
                'no_po' => $order->no_po,
                'company' => $order->company,
                'formatted_date' => $order->formatted_date,
                'sales_name' => $order->sales_name,
                'sales_avatar' => $order->sales_avatar,
                'progress_label' => $order->progress_label,
                'progress_badge' => $order->progress_badge,
                'payment_label' => $order->payment_label,
                'payment_badge' => $order->payment_badge,
                'payment_detail' => $order->payment_detail,
                'revenue' => number_format($order->revenue, 0, ',', '.'),
                'detail_route' => $order->detail_route,
                'create_product_out_route' => ($order->type === 'Project') 
                    ? route('pending-po.product_out_project', $order->id) 
                    : route('pending-po.product_out', $order->id),
                'items' => $itemsList,
                'total_ordered_qty' => $totalOrderedQtyAll,
                'total_shipped_qty' => $totalShippedQtyAll,
                'total_remaining_qty' => max(0, $totalOrderedQtyAll - $totalShippedQtyAll),
                'product_outs' => $productOutsSummaryList,
            ];

            // Costs & Profits
            $order->material_cost = (float) ($matCosts[$order->id] ?? 0);
            $order->shipping_cost = (float) ($shipCosts[$order->id] ?? 0);

            if ($isProject) {
                $order->area = $unitQuote?->client?->area ?? $quote?->pic?->client?->area ?? '-';
                $order->general_cost = (float) ($genCosts[$order->id] ?? 0);
                $order->total_cost = $order->material_cost + $order->general_cost + $order->shipping_cost;
                $order->profit = $order->revenue - $order->total_cost;
                $order->margin = $order->revenue > 0 ? ($order->profit / $order->revenue) * 100 : 0;

                $projects->push($order);
                $totalRevenueProject += $order->revenue;
                $totalMaterialProject += $order->material_cost;
                $totalGeneralProject += $order->general_cost;
                $totalShippingProject += $order->shipping_cost;

                if ($order->status == 0) {
                    $newProjects->push($order);
                } elseif ($order->status == 6) {
                    $completedProjects->push($order);
                } else {
                    $pStep = $order->project_status_step ?? 1;
                    if ($pStep == 1) {
                        $checkPartsProjects->push($order);
                    } elseif ($pStep == 2) {
                        $schedulingProjects->push($order);
                    } else {
                        $inProgressProjects->push($order);
                    }
                }
            } else {
                $order->general_cost = 0;
                $order->total_cost = $order->material_cost + $order->shipping_cost;
                $order->profit = $order->revenue - $order->total_cost;

                $allOrders->push($order);
                $totalRevenueSOrder += $order->revenue;
                $totalCostSOrder += $order->total_cost;

                switch ($order->status) {
                    case 0: $newOrders->push($order); break;
                    case 1:
                    case 2:
                    case 3:
                    case 4: $checkPartsOrders->push($order); break;
                    case 5: $deliveryOrders->push($order); break;
                    case 6: $completedOrders->push($order); break;
                    case 7: $returnOrders->push($order); break;
                    case 8: $partialDeliveryOrders->push($order); break;
                    case 9: $delayedOrders->push($order); break;
                    default: $checkPartsOrders->push($order); break;
                }
            }

            return $order;
        });

        // Collections already pre-sorted descending from single-pass
        $totalMasterOrdersCount = $allMasterOrders->count();
        $totalMasterInProgressCount = $allMasterOrders->where('status', '!=', 6)->count();

        $totalOrdersCount = $allOrders->count();
        $totalProfitSOrder = $totalRevenueSOrder - $totalCostSOrder;
        $overallMarginSOrder = $totalRevenueSOrder > 0 ? ($totalProfitSOrder / $totalRevenueSOrder) * 100 : 0;

        $totalProjectsCount = $projects->count();
        $totalCostProject = $totalMaterialProject + $totalGeneralProject + $totalShippingProject;
        $totalProfitProject = $totalRevenueProject - $totalCostProject;
        $overallMarginProject = $totalRevenueProject > 0 ? ($totalProfitProject / $totalRevenueProject) * 100 : 0;

        // Legacy compatibility variables for modals (clean & lightweight)
        $schedules = collect();
        $orders = collect();

        return view('pages.sorder.index', compact(
            'availableYears',
            'selectedYear',

            // Sales Order variables
            'allOrders',
            'newOrders',
            'checkPartsOrders',
            'partialDeliveryOrders',
            'deliveryOrders',
            'completedOrders',
            'returnOrders',
            'delayedOrders',
            'totalOrdersCount',
            'totalRevenueSOrder',
            'totalCostSOrder',
            'totalProfitSOrder',
            'overallMarginSOrder',

            // Project Monitoring variables
            'projects',
            'newProjects',
            'checkPartsProjects',
            'schedulingProjects',
            'inProgressProjects',
            'completedProjects',
            'totalProjectsCount',
            'totalRevenueProject',
            'totalMaterialProject',
            'totalGeneralProject',
            'totalShippingProject',
            'totalCostProject',
            'totalProfitProject',
            'overallMarginProject',

            // Master Sales Order variables
            'allMasterOrders',
            'totalMasterOrdersCount',
            'totalMasterInProgressCount',

            // Legacy modals variables
            'schedules',
            'orders'
        ));
    }
    public function indexDone()
    {
        $data = PendingPO::join('quotation as q', 'pending_po.id_quotation', '=', 'q.id')
            ->leftJoin('invoice as i', 'q.id', '=', 'i.id_quotation')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')
            ->join('client as c', 'p.id_client', '=', 'c.id')
            ->join('users as u', 'q.id_sales', '=', 'u.id')
            ->select(
                'pending_po.id',
                'u.name',
                'c.company',
                'i.no_po',
                'pending_po.status',
                'i.status_p',
                'i.note_p',
            )
            ->get();
        // dd($data);
        return view('pages.pending.done');
    }
    public function indexProject()
    {
        $data = PendingPO::join('quotation as q', 'pending_po.id_quotation', '=', 'q.id')
            ->leftJoin('invoice as i', 'q.id', '=', 'i.id_quotation')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')
            ->join('client as c', 'p.id_client', '=', 'c.id')
            ->join('users as u', 'q.id_sales', '=', 'u.id')
            ->select(
                'pending_po.id',
                'u.name',
                'c.company',
                'i.no_po',
                'pending_po.status',
                'i.status_p',
                'i.note_p',
            )
            ->get();
        // dd($data);
        return view('pages.pending.project');
    }

    public function upload_resi(Request $request, $id)
    {
        // dd($request->all());
        $pending = PendingPO::findOrFail($id);
        $hasApprovedInvoice = $this->hasApprovedInvoice($pending);
        if (!$hasApprovedInvoice) {
            return redirect()->back()->with('error', 'Proses logistik dikunci karena invoice belum di-approve oleh Accounting.');
        }
        $invoice = Invoice::find($id);
        $resi = new Expanse();
        $resi->image = '';

        if ($request->hasFile('file')) {
            $foto = $request->file('file');

            // Validasi
            $request->validate([
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            // Ekstensi
            $file_ext = $foto->getClientOriginalExtension();

            // Nama file aman
            // $sanitized_file_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $quote->no_quote);

            // Susun nama file
            $cleanTrack = preg_replace('/[^A-Za-z0-9\-]/', '_', (string) $request->no_track) ?: ('resi_' . time());
            $file_name = $cleanTrack . '_' . \Illuminate\Support\Str::random(6) . '.' . $file_ext;

            // Path
            $targetDir = file_exists(base_path('../public_html/asset/resi'))
                ? base_path('../public_html/asset/resi')
                : public_path('asset/resi');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $foto->move($targetDir, $file_name);

            // simpan di DB
            $resi->image = 'asset/resi/' . $file_name;
        }

        $resi->id_pending = $id;
        $resi->kurir = $request->kurir;
        $resi->no_track = $request->no_track;
        $resi->charged = $request->charged;
        $resi->cost = $request->cost;
        $resi->description = $request->description;
        $resi->type = "Resi";
        $resi->date = $request->date;
        $resi->status = $request->charged == 1 ? 'pending' : null;
        $resiSave = $resi->save();
        if ($resiSave) {
            return redirect('/pending-po/' . $id)->with('message', 'data telah di tambahkan');
        }

        return redirect('/pending-po/' . $id)->with('error', 'Gagal menyimpan data resi');
    }
    public function delete_resi($id)
    {
        $resi = Expanse::findOrFail($id);
        $delResi = $resi->delete();
        if ($delResi) {
            return 1;
        } else {
            return 0;
        }
    }
    public function schedule(Request $request, $id)
    {
        // dd($request->all());
        $schedule = new ServiceOrder();
        $schedule->id_sales_order = $id;
        $schedule->BA = '0';
        $schedule->SJ = '0';
        $schedule->note_schedule = $request->note;
        $schedule->date_schedule = $request->date_schedule;
        $schedulesave = $schedule->save();
        if ($schedulesave) {
            return redirect('/sales-order')->with('message', 'data telah di tambahkan');
        }
    }
    public function reschedule(Request $request, $id)
    {
        $schedule = ServiceOrder::findOrFail($id);
        $reschedule = new ServiceOrder();
        $reschedule->id_sales_order = $schedule->id_sales_order;
        $reschedule->BA = $schedule->BA;
        $reschedule->SJ = $schedule->SJ;
        $reschedule->note_schedule = $request->note;
        $reschedule->date_schedule = $request->date_schedule;
        $reschedulesave = $reschedule->save();
        if ($reschedulesave) {
            return redirect('/sales-order')->with('message', 'data telah di tambahkan');
        }
    }
    public function dokumentasi(Request $request, $id)
    {
        // dd($request->all());
        $schedule = ServiceOrder::findOrFail($id);
        $schedule->SJ = $request->has('SJ') ? '1' : '0';
        $schedule->BA = $request->has('BA') ? '1' : '0';
        $schedule->note_doc = $request->note;
        $schedulesave = $schedule->save();
        if ($schedule->SJ == '1' && $schedule->BA == '1') {
            $order = PendingPO::find($schedule->id_sales_order);
            if ($order) {
                $order->status = '9';
                $order->save();
            }
        }
        if ($schedulesave) {
            return redirect('/sales-order')->with('message', 'data telah di tambahkan');
        }
    }
    public function returProduct(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $pending = PendingPO::findOrFail($id);
            // dd($pending);
            $return = new Retur();
            $return->id_pending = $id;
            $return->no_return = $request->no_return;
            $return->status = 0;
            $return->date = Carbon::now();
            $return->save();

            $pending->status = '8';
            $pending->save();
            $productOut = ProductOut::findOrFail($pending->id_product_out);
            $detProduct = DetailProductOut::where('id_product_out', $productOut->id)->get();
            foreach ($request->qty as $key => $value) {
                if ($value != 0) {
                    $dproduct = DetailProduct::find($detProduct[$key]->id_detail_product);
                    if (!$dproduct) {
                        continue;
                    }
                    $product = Product::find($dproduct->id_product);
                    if (!$product) {
                        continue;
                    }
                    $detReturn = new DetailReturn();
                    $detReturn->id_retur = $return->id;
                    $detReturn->id_replacement = $detProduct[$key]->id_detail_product;
                    $detReturn->qty = $value;
                    $detReturn->note = $request->note[$key] ?? '-';
                    $detReturn->status = 0;
                    $detReturn->date = Carbon::today();
                    $detReturn->save();
                    // -- Stock
                    $dproduct->stock += $value;
                    $product->stock += $value;
                    $dproduct->save();
                    $product->save();
                }
            }

            return redirect()->back()->with('success', 'Data Return Telah Ditambahkan');
        });
    }

    public function clearReturn($id)
    {
        return DB::transaction(function () use ($id) {
            $pending = PendingPO::findOrFail($id);
            $pending->status = '6';
            $pending->save();
            $return = Retur::where('id_pending', $id)->get();
            foreach ($return as $retur) {
                $dproduct = DetailProduct::find($retur->id_replacement);
                if (!$dproduct) {
                    continue;
                }
                $product = Product::find($dproduct->id_product);
                if (!$product) {
                    continue;
                }
                $retur->status = 1;
                $retur->save();
                // -- Stock
                $dproduct->stock -= $retur->qty;
                $product->stock -= $retur->qty;
                $dproduct->save();
                $product->save();
            }

            return 1;
        });
    }
    public function donePending($id)
    {
        $pending = PendingPO::findOrFail($id);
        $hasApprovedInvoice = $this->hasApprovedInvoice($pending);
        if (!$hasApprovedInvoice) {
            return redirect()->back()->with('error', 'Proses logistik dikunci karena invoice belum di-approve oleh Accounting.');
        }
        $pending->status = '6';
        $pendingSave = $pending->save();
        if ($pendingSave) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Bulk update status for multiple Pending POs from sales-order page.
     * Status 6 (Done) does NOT redirect to product-out — just changes status
     * and records a note "Part belum diinput barang keluar".
     */
    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:pending_po,id',
            'status' => 'required|integer|in:0,1,2,3,4,5,6,8,9',
        ]);

        $ids    = $request->input('ids');
        $status = (string) $request->input('status');
        $note   = $request->input('note', '');

        $statusLabels = [
            '0' => 'New PO',
            '1' => 'On Check',
            '2' => 'Ready Stock',
            '3' => 'Kurang',
            '4' => 'Pre-delivery',
            '5' => 'Delivery Process',
            '6' => 'Done',
            '8' => 'Return',
            '9' => 'Delayed',
        ];

        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($ids, $status, $note, $statusLabels, &$updated, &$skipped) {
            foreach ($ids as $id) {
                $pending = PendingPO::find($id);
                if (!$pending) { $skipped++; continue; }

                $oldStatus = $pending->status;
                $pending->status = $status;
                $pending->save();

                // Record into change_status log
                $changeNote = trim($note);
                if ($status === '6' && empty($changeNote)) {
                    $changeNote = 'Part belum diinput barang keluar.';
                }
                if (!empty($changeNote) || $status !== $oldStatus) {
                    ChangeStatus::create([
                        'id_pending' => $id,
                        'id_user'    => Auth::id(),
                        'status'     => $status,
                        'date'       => now(),
                        'note'       => $changeNote ?: ('Bulk update status ke: ' . ($statusLabels[$status] ?? $status)),
                    ]);
                }

                $updated++;
            }
        });

        return response()->json([
            'success' => true,
            'updated' => $updated,
            'skipped' => $skipped,
            'message' => "Berhasil update {$updated} PO ke status " . ($statusLabels[$status] ?? $status) . ($skipped ? " ({$skipped} dilewati)" : '') . '.',
        ]);
    }

    /**
     * Link multiple Sales Orders (Pending POs) to a parent Sales Order (same job/project).
     */
    public function linkSalesOrders(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|integer|exists:pending_po,id',
            'child_ids' => 'required|array|min:1',
            'child_ids.*' => 'integer|exists:pending_po,id',
        ]);

        $parentId = (int) $request->input('parent_id');
        $childIds = array_values(array_unique(array_map('intval', $request->input('child_ids'))));

        // Ensure parent_id is not in child_ids
        $childIds = array_filter($childIds, fn($id) => $id !== $parentId);

        if (empty($childIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada SO anak yang valid untuk dikaitkan.',
            ], 422);
        }

        $parent = PendingPO::with(['quote.pic.client', 'unitQuotation.client'])->findOrFail($parentId);

        // If the chosen parent is itself already a child of another parent, resolve to the root parent
        if ($parent->parent_id) {
            $parentId = $parent->parent_id;
            $parent = PendingPO::with(['quote.pic.client', 'unitQuotation.client'])->findOrFail($parentId);
        }

        $parentClientId = $parent->client_id;
        $parentClientName = $parent->client_name;

        // Verify that all children belong to the exact same client
        $children = PendingPO::with(['quote.pic.client', 'unitQuotation.client'])->whereIn('id', $childIds)->get();
        foreach ($children as $child) {
            $childClientId = $child->client_id;
            $childClientName = $child->client_name;

            // If both have client IDs and they differ, or if client names differ
            $isSameClient = ($parentClientId && $childClientId)
                ? ($parentClientId === $childClientId)
                : (strcasecmp(trim($parentClientName), trim($childClientName)) === 0 && $parentClientName !== '-');

            if (!$isSameClient && ($parentClientName !== '-' || $childClientName !== '-')) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal mengaitkan: SO #{$child->no_pending} ({$childClientName}) memiliki Client yang berbeda dengan SO Utama #{$parent->no_pending} ({$parentClientName}). Sales Order yang dikaitkan wajib berasal dari 1 Client yang sama.",
                ], 422);
            }
        }

        DB::transaction(function () use ($parentId, $childIds, $parent) {
            // Update all children
            foreach ($childIds as $childId) {
                $child = PendingPO::find($childId);
                if (!$child) continue;

                // If this child previously had its own children, transfer them to the new root parent
                PendingPO::where('parent_id', $childId)->update(['parent_id' => $parentId]);

                $child->parent_id = $parentId;
                $child->save();
            }

            // Ensure the parent itself has parent_id = null
            $parent->parent_id = null;
            $parent->save();
        });

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengaitkan " . count($childIds) . " Sales Order ke SO Utama #{$parent->no_pending} (Client: {$parentClientName}).",
        ]);
    }

    /**
     * Unlink a Sales Order from its parent, or unlink all children if parent.
     */
    public function unlinkSalesOrder(Request $request, $id)
    {
        $pending = PendingPO::findOrFail($id);
        $unlinkAll = $request->boolean('unlink_all', false);

        DB::transaction(function () use ($pending, $unlinkAll) {
            if ($pending->parent_id) {
                // If it's a child, unlink itself
                $pending->parent_id = null;
                $pending->save();
            }

            if ($unlinkAll) {
                // If requested, unlink all its children
                PendingPO::where('parent_id', $pending->id)->update(['parent_id' => null]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Kaitan Sales Order #{$pending->no_pending} berhasil dilepas.",
        ]);
    }

    /**
     * Get all linked Sales Orders in the group (Parent + all Children).
     */
    public function getLinkedGroup($id)
    {
        $pending = PendingPO::with(['quote.pic.client', 'unitQuotation.client', 'parentPending', 'linkedChildren'])->findOrFail($id);

        // Find root parent
        $rootId = $pending->parent_id ?: $pending->id;
        $root = PendingPO::with([
            'quote.pic.client',
            'quote.sales',
            'unitQuotation.client',
            'unitQuotation.sales',
            'linkedChildren.quote.pic.client',
            'linkedChildren.quote.sales',
            'linkedChildren.unitQuotation.client',
            'linkedChildren.unitQuotation.sales',
        ])->findOrFail($rootId);

        $members = collect([$root])->merge($root->linkedChildren)->map(function ($po) use ($rootId) {
            $isRoot = ($po->id === $rootId);
            $company = $po->unitQuotation?->client?->company ?? $po->quote?->pic?->client?->company ?? '-';
            $sales = $po->unitQuotation?->sales?->name ?? $po->quote?->sales?->name ?? '-';
            $date = $po->quote?->po_date ?? $po->date;

            return [
                'id' => $po->id,
                'no_pending' => $po->no_pending,
                'type' => $po->type,
                'title' => $po->title,
                'status' => $po->status,
                'company' => $company,
                'sales' => $sales,
                'date' => $date ? date('d-m-Y', strtotime($date)) : '-',
                'is_parent' => $isRoot,
                'detail_url' => ($po->type === 'Project' && !$po->id_unit_quotation)
                    ? url('/project-monitoring/' . $po->id)
                    : url('/pending-po/' . $po->id),
            ];
        });

        return response()->json([
            'success' => true,
            'parent_id' => $rootId,
            'parent_no_pending' => $root->no_pending,
            'total_linked' => $members->count(),
            'members' => $members,
        ]);
    }
}
