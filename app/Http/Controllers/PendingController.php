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
use Auth;
use Cache;
use Carbon\Carbon;
use DB;
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

            return view('pages.pending.detail-unit', compact(
                'pending', 'quote', 'invoices', 'activity', 'resis',
                'dPending', 'purchases', 'purchase', 'return', 'allproductOut', 'product', 'detProduct'
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
        // $allEquiv = SerialProduct::all();
        // $detProduct = DetailProductOut::where('id_product_out', $allproductOut[0]->id)->get();
        $purchases = PurchaseRequest::where('id_pending', $id)->with(['details.equivalent.product', 'purchaseOrders'])->get();
        $purchase = $purchases->first();
        $serial = collect();

        return view('pages.pending.detail', compact('purchases', 'purchase', 'return', 'detProduct', 'activity', 'allproductOut', 'subQuote', 'pending', 'quotation', 'invoice', 'detQuotation', 'resi', 'product', 'resis', 'serial'));
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
                $productSave = $product->save();
            }
        }
        $pending->status = '6';
        $pending->id_product_out = $request->product;
        $pendingSave = $pending->save();
        if ($pendingSave) {
            return redirect('/pending-po/' . $id)->with('message', 'Product Out telah disambungkan');
        }
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

        if (str_contains(request()->header('referer'), 'project-monitoring')) {
            return redirect()->route('project-monitoring.show', $id)
                ->with('success', 'Pengecekan logistik / status barang proyek berhasil diperbarui.');
        }
        return redirect('/pending-po/' . $id)->with('message', 'Product Pending PO telah diedit');
    }
    public function statusEdit(Request $request, $id)
    {
        $pending = PendingPO::findOrFail($id);
        $hasApprovedInvoice = $this->hasApprovedInvoice($pending);
        if (!$hasApprovedInvoice) {
            return redirect()->back()->with('error', 'Proses logistik dikunci karena invoice belum di-approve oleh Accounting.');
        }
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
                    $product = Product::join('serial_product as sp', 'sp.id', '=', 'product.id')->where('sp.id', $item->id_equivalent)->select('product.*')->first();
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
        $pending = PendingPO::find($id);
        $quote = Quotation::find($pending->id_quotation);
        $Dquote = DetailQuotation::where('id_quotation', $pending->id_quotation)->get();
        $dPending = DetailPendingPO::where('id_pending', $id)->whereNot('status', '7')->get();

        $fullRep = [];
        $no = 0;
        foreach ($Dquote as $item) {
            $equivalent = SerialProduct::find($item->id_equivalent);
            $fullRep[$no] = DetailProduct::where('id_product', $equivalent->id_product)->get();
            $no++;
        }
        return view('pages.pending.form', compact('Dquote', 'fullRep', 'pending', 'quote', 'dPending', 'id'));
    }
    public function pending_out_project($id)
    {
        $pending = PendingPO::find($id);
        $quote = Quotation::find($pending->id_quotation);
        // $Dquote = DetailServiceQuotation::where('id_quotation', $pending->id_quotation)->get();
        $dPending = DetailPendingPO::where('id_pending', $id)->whereNot('status', '7')->get();

        $fullRep = [];
        $fullEquiv = [];
        $no = 0;
        foreach ($dPending as $item) {
            $fullEquiv[$no] = SerialProduct::find($item->id_equivalent);
            $fullRep[$no] = DetailProduct::where('id_product', $fullEquiv[$no]->id_product)->get();
            $no++;
        }
        // dd($dPending);
        return view('pages.pending.form-project', compact('fullRep', 'fullEquiv', 'dPending', 'pending', 'quote', 'dPending', 'id'));
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
        // dd($request->all());
        $pending = PendingPO::findOrFail($id);
        $hasApprovedInvoice = $this->hasApprovedInvoice($pending);
        if (!$hasApprovedInvoice) {
            return redirect()->back()->with('error', 'Proses logistik dikunci karena invoice belum di-approve oleh Accounting.');
        }
        $isKojisha = ($pending->quote && (method_exists($pending->quote, 'isKojisha') ? $pending->quote->isKojisha() : ($pending->quote->flag === 'Kojisha')))
            || ($pending->unitQuotation && (method_exists($pending->unitQuotation, 'isKojisha') ? $pending->unitQuotation->isKojisha() : false))
            || ($request->flag === 'Kojisha')
            || (is_string($request->invoice) && str_contains($request->invoice, '/KII/'))
            || (is_string($request->po) && str_contains($request->po, 'KII'));
            
        $flag = $isKojisha ? 'Kojisha' : 'Reftech';

        // Masukan Data ke Tabel Product Out
        $productOut = new ProductOut();
        $productOut->flag = $flag;
        $productOut->no_product_out = (new \App\Http\Controllers\ProductOutController())->generateNoProductOut($request->warehouse[0] ?? 'BDG', $flag);
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
        $pending->id_product_out = $productOut->id;
        $pending->save();
        if ($productOutSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            foreach ($request->equivalent as $item => $value) {
                $dProductIn = new DetailProductOut();
                $dProductIn->id_product_out = $productOut->id;
                $dProductIn->id_detail_product = $request->replacement[$item];
                $dProductIn->id_serial_product = $request->equivalent[$item];
                $dProductIn->qty = $request->qty[$item];
                $dProductIn->price = $request->price[$item];
                $dProductIn->amount = $request->amount[$item];
                $dProductIn->warehouse = $request->warehouse[$item];
                $productD = DetailProduct::where('id', $request->replacement[$item])->first();
                if ($request->warehouse[$item] == 'BDG') {
                    $productD->stock -= $request->qty[$item];
                } else {
                    $productD->warehouse_stock -= $request->qty[$item];
                }
                $productD->save();
                $product = Product::where('id', $productD->id_product)->first();
                // if ($request->warehouse[$item] == 'BDG') {
                $product->pending_stock -= $request->qty[$item];
                // } else {
                //     $product->pending_stock -= $request->qty[$item];
                //     $product->stock += $request->qty[$item];
                //     $product->warehouse_stock -= $request->qty[$item];
                // }
                $product->save();
                $dProductSave = $dProductIn->save();
            }
        }
        if ($dProductSave) {
            return redirect('/pending-po-done')->with('message', 'data telah di tambahkan');
        }
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
            'quote.pic.client',
            'quote.sales',
            'quote.invoice',
            'unitQuotation.client',
            'unitQuotation.sales',
            'unitQuotation.invoices',
            'unitQuotation.payments',
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

        $confirmedPayments = DB::table('payment')
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

        $confirmedQuoteIds = $confirmedPayments->whereNotNull('id_quotation')->pluck('id_quotation')->flip()->all();
        $confirmedUnitQuoteIds = $confirmedPayments->whereNotNull('id_unit_quotation')->pluck('id_unit_quotation')->flip()->all();

        // Micro-caches for repeated string parsing & asset URLs
        $dateCache = [];
        $avatarCache = [];
        $termCache = [];

        // Pre-allocate collections for single-pass bucketing (eliminates 11 collection filters and multiple sum loops)
        $allOrders = collect();
        $projects = collect();

        $newOrders = collect();
        $checkPartsOrders = collect();
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

        // ==========================================
        // UNIFIED SINGLE-PASS PROCESSING & BUCKETING
        // ==========================================
        $allMasterOrders = $allPending->map(function ($order) use (
            $matCosts, $shipCosts, $genCosts, $defaultAvatar,
            $confirmedQuoteIds, $confirmedUnitQuoteIds,
            $projectBaseUrl, $pendingBaseUrl, &$dateCache, &$avatarCache,
            $allOrders, $projects,
            $newOrders, $checkPartsOrders, $deliveryOrders, $completedOrders, $returnOrders, $delayedOrders,
            $newProjects, $checkPartsProjects, $schedulingProjects, $inProgressProjects, $completedProjects,
            &$totalRevenueSOrder, &$totalCostSOrder,
            &$totalRevenueProject, &$totalMaterialProject, &$totalGeneralProject, &$totalShippingProject
        ) {
            $isProject = ($order->type === 'Project');
            $order->order_type = $isProject ? 'Project' : 'Non-Project';

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
                    case 8: $progressLabel = 'Return'; $progressBadge = 'bg-label-warning'; break;
                    case 9: $progressLabel = 'Delayed'; $progressBadge = 'bg-label-danger'; break;
                    default: $progressLabel = 'In Progress'; $progressBadge = 'bg-label-primary'; break;
                }
            }
            $order->progress_label = $progressLabel;
            $order->progress_badge = $progressBadge;

            // Revenue
            $uqSub = $unitQuote ? (floatval($unitQuote->subtotal ?? 0) - floatval($unitQuote->diskon ?? 0)) : 0;
            if ($unitQuote && $uqSub <= 0) {
                $uqSub = floatval($unitQuote->total ?? 0) - floatval($unitQuote->tax_amount ?? 0);
            }
            $order->revenue = $unitQuote ? $uqSub : floatval($quote?->nett ?? 0);

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
                    case 8: $returnOrders->push($order); break;
                    case 9: $delayedOrders->push($order); break;
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
            $file_name = $request->no_track . '.' . $file_ext;

            // Path
            $upload_path = base_path('../public_html/asset/resi');
            $foto->move($upload_path, $file_name);

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
        $pending = PendingPO::findOrFail($id);
        // dd($pending);
        $return = new Retur();
        $return->id_pending = $id;
        $return->no_return = $request->no_return;
        $return->status = 0;
        $return->date = Carbon::now();
        $returnSave = $return->save();

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
                $detReturnSave = $detReturn->save();
                // -- Stock
                $dproduct->stock += $value;
                $product->stock += $value;
                $dproduct->save();
                $product->save();
            }
        }
        if ($detReturnSave && $returnSave) {
            return redirect()->back()->with('success', 'Data Return Telah Ditambahkan');
        }
    }
    public function clearReturn($id)
    {
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
            $returSave = $retur->save();
            // -- Stock
            $dproduct->stock -= $retur->qty;
            $product->stock -= $retur->qty;
            $dproduct->save();
            $product->save();
        }
        if ($returSave) {
            return 1;
        } else {
            return 0;
        }
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
}
