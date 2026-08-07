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
use App\Models\Quotation;
use App\Models\Retur;
use App\Models\SerialProduct;
use App\Models\ServiceOrder;
use App\Models\SubtitleQuotation;
use App\Models\ProjectExpense;
use App\Models\UnitQuotation;
use Auth;
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
        $pending = PendingPO::find($id);

        if ($pending->id_unit_quotation) {
            $quote = UnitQuotation::with(['client', 'pic', 'sales'])->findOrFail($pending->id_unit_quotation);
            $invoices = Invoice::where('id_unit_quotation', $quote->id)->orderByRaw("FIELD(type,'DP','BP','CT')")->get();
            $activity = ChangeStatus::where('id_pending', $id)->with('comment')->get();
            $resis = Expanse::where('id_pending', $id)->where('type', 'Resi')->get();
            $dPending = DetailPendingPO::with('equivalent.product')->where('id_pending', $id)->get();
            $purchase = PurchaseRequest::where('id_pending', $id)->get();
            $return = Retur::where('id_pending', $id)->get();
            $allproductOut = ProductOut::leftJoin('pending_po', 'product_out.id', '=', 'pending_po.id_product_out')
                ->whereNull('pending_po.id_product_out')
                ->groupBy('product_out.id')
                ->select('product_out.*')
                ->get();
            $product = ProductOut::find($pending->id_product_out);
            $detProduct = DetailProductOut::where('id_product_out', $pending->id_product_out)->get();

            return view('pages.pending.detail-unit', compact(
                'pending', 'quote', 'invoices', 'activity', 'resis',
                'dPending', 'purchase', 'return', 'allproductOut', 'product', 'detProduct'
            ));
        }

        $quotation = Quotation::find($pending->id_quotation);
        $detQuotation = DetailQuotation::where('id_quotation', $pending->id_quotation)->get();
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $pending->id_quotation)->get();
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
            ->select('product_out.*')
            ->get();
        // $allEquiv = SerialProduct::all();
        // $detProduct = DetailProductOut::where('id_product_out', $allproductOut[0]->id)->get();
        $purchase = PurchaseRequest::where('id_pending', $id)->get();

        // dd($detail);
        // dd($status->count());
        return view('pages.pending.detail', compact('purchase', 'return', 'detProduct', 'activity', 'allproductOut', 'subQuote', 'pending', 'quotation', 'invoice', 'detQuotation', 'resi', 'product', 'resis'));
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
        $pending = PendingPO::find($id);
        $dPending = DetailPendingPO::where('id_pending', $id)->get();
        $cekstock = 0;
        foreach ($dPending as $detail) {
            $cekstock += $detail->bdg + $detail->bks;
        }
        if ($cekstock == 0 && !$pending->id_unit_quotation) {
            $quote = Quotation::find($pending->id_quotation);
            $dQuote = DetailQuotation::where('id_quotation', $quote->id)->get();
            foreach ($dQuote as $item) {
                $equivalent = SerialProduct::find($item->id_equivalent);
                $product = Product::find($equivalent->id_product);
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
        $pending = PendingPO::find($id);
        $quote = Quotation::find($pending->id_quotation);
        $dQuote = DetailQuotation::where('id_quotation', $quote->id)->get();
        $dPending = DetailPendingPO::where('id_pending', $id)->get();
        foreach ($request->status as $key => $value) {
            $product = Product::join('serial_product as sp', 'sp.id_product', '=', 'product.id')->where('sp.id', $dQuote[$key]->id_equivalent)->select('product.*')->first();
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
        // dd($request->all());
        $pending = PendingPO::find($id);
        $quote = Quotation::find($pending->id_quotation);
        $dQuote = DetailQuotation::where('id_quotation', $quote->id)->get();
        $dPending = DetailPendingPO::where('id_pending', $id)->get();
        // dd($dPending);
        foreach ($request->status as $key => $value) {
            $product = Product::join('serial_product as sp', 'sp.id_product', '=', 'product.id')->where('sp.id', $request->equivalent[$key])->select('product.*')->first();
            // if ($dPending[$key]->bdg != 0 || $dPending[$key]->bks != 0) {
            $product->stock += $dPending[$key]->bdg;
            $product->warehouse_stock += $dPending[$key]->bks;
            $product->pending_stock -= $dPending[$key]->bdg + $dPending[$key]->bks;
            $dPending[$key]->bdg = 0;
            $dPending[$key]->bks = 0;
            // }
            $dPending[$key]->id_equivalent = $request->equivalent[$key];
            $dPending[$key]->status = $value;
            $dPending[$key]->bdg = $request->bdg[$key];
            $dPending[$key]->bks = $request->bks[$key];
            $dPending[$key]->note = $request->note[$key];
            $dPending[$key]->save();
            // dd($item->id_equivalent);
            if ($value == 2) {
                $product->stock -= $request->bdg[$key];
                $product->warehouse_stock -= $request->bks[$key];
                $product->pending_stock += $request->bdg[$key] + $request->bks[$key];
            }
            $product->save();
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
                $note = 'Reday Stock';
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
            $quote = Quotation::find($pending->id_quotation);
            $Dquote = DetailQuotation::where('id_quotation', $pending->id_quotation)->get();
            foreach ($Dquote as $item) {
                $product = Product::join('serial_product as sp', 'sp.id', '=', 'product.id')->where('sp.id', $item->id_equivalent)->select('product.*')->first();
                $product->stock += $item->qty;
                $product->pending_stock -= $item->qty;
                $product->save();
            }
        }
        if ($request->status == '6') {
            if ($pending->type == 'Project') {
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
        $role = Auth::user()->role;
        $selectedYear = $request->get('year', date('Y'));

        // Query available years for filter dropdown (both Project and Non Project)
        // Satu query untuk semua PendingPO, dipakai ulang untuk Non Project & Project di bawah
        // (sebelumnya 3x query PendingPO::get() terpisah untuk hal yang sama)
        $allPending = PendingPO::with([
            'quote.pic.client',
            'quote.sales',
            'quote.invoice',
            'unitQuotation.client',
            'unitQuotation.sales',
        ])->get();

        $availableYears = $allPending->map(function ($pending) {
            $date = $pending->quote?->po_date ?? $pending->date;
            return $date ? Carbon::parse($date)->year : null;
        })->filter()->unique()->sortDesc()->values()->all();

        $currentYear = intval(date('Y'));
        if (!in_array($currentYear, $availableYears)) {
            $availableYears[] = $currentYear;
            rsort($availableYears);
        }

        // ==========================================
        // 1. SALES ORDER (Non Project) DATA
        // ==========================================
        $allOrders = $allPending->where('type', 'Non Project')->values();

        if ($selectedYear !== 'all') {
            $allOrders = $allOrders->filter(function ($order) use ($selectedYear) {
                $date = $order->quote?->po_date ?? $order->date;
                return $date && Carbon::parse($date)->year == $selectedYear;
            });
        }

        if ($role === 'Sales') {
            $allOrders = $allOrders->filter(function ($order) {
                $quoteSales = $order->quote?->id_sales;
                $unitSales = $order->unitQuotation?->id_sales;
                return ($quoteSales == Auth::id()) || ($unitSales == Auth::id());
            });
        }

        $orderIds = $allOrders->pluck('id');
        $materialCostByOrder = PurchaseRequest::whereIn('id_pending', $orderIds)
            ->where('status', '3')
            ->groupBy('id_pending')->selectRaw('id_pending, SUM(amount) as total')
            ->pluck('total', 'id_pending');
        $shippingCostByOrder = Expanse::whereIn('id_pending', $orderIds)
            ->where('type', 'Resi')
            ->groupBy('id_pending')->selectRaw('id_pending, SUM(cost) as total')
            ->pluck('total', 'id_pending');

        $allOrders = $allOrders->map(function ($order) use ($materialCostByOrder, $shippingCostByOrder) {
            $order->order_date = $order->quote?->po_date ?? $order->date;
            $order->company = $order->unitQuotation?->client?->company
                ?? $order->quote?->pic?->client?->company
                ?? '-';
            $order->sales_name = $order->unitQuotation?->sales?->name
                ?? $order->quote?->sales?->name
                ?? '-';
            $order->sales_image = $order->unitQuotation?->sales?->image
                ?? $order->quote?->sales?->image
                ?? null;
            $order->revenue = $order->unitQuotation ? ($order->unitQuotation->total ?? 0) : ($order->quote?->nett ?? 0);
            $order->no_po = $order->unitQuotation ? ($order->unitQuotation->po_number ?? '-') : ($order->quote?->invoice->first()?->no_po ?? '-');
            $order->detail_route = route('pending-po.show', $order->id);
            $order->material_cost = (float) $materialCostByOrder->get($order->id, 0);
            $order->shipping_cost = (float) $shippingCostByOrder->get($order->id, 0);
            $order->total_cost = $order->material_cost + $order->shipping_cost;
            $order->profit = $order->revenue - $order->total_cost;
            return $order;
        })->sortByDesc(function ($order) {
            return $order->order_date ? Carbon::parse($order->order_date)->timestamp : 0;
        })->values();

        $newOrders = $allOrders->filter(fn($o) => $o->status == 0);
        $checkPartsOrders = $allOrders->filter(fn($o) => in_array($o->status, [1, 2, 3, 4]));
        $deliveryOrders = $allOrders->filter(fn($o) => $o->status == 5);
        $completedOrders = $allOrders->filter(fn($o) => $o->status == 6);
        $returnOrders = $allOrders->filter(fn($o) => $o->status == 8);
        $delayedOrders = $allOrders->filter(fn($o) => $o->status == 9);

        $totalOrdersCount = $allOrders->count();
        $totalRevenueSOrder = $allOrders->sum('revenue');
        $totalCostSOrder = $allOrders->sum('total_cost');
        $totalProfitSOrder = $totalRevenueSOrder - $totalCostSOrder;
        $overallMarginSOrder = $totalRevenueSOrder > 0 ? ($totalProfitSOrder / $totalRevenueSOrder) * 100 : 0;

        // ==========================================
        // 2. PROJECT MONITORING DATA
        // ==========================================
        $projects = $allPending->where('type', 'Project')->values();

        if ($selectedYear !== 'all') {
            $projects = $projects->filter(function ($project) use ($selectedYear) {
                $date = $project->date ?? null;
                return $date && Carbon::parse($date)->year == $selectedYear;
            });
        }

        if ($role === 'Sales') {
            $projects = $projects->filter(function ($project) {
                $quoteSales = $project->quote?->id_sales;
                $unitSales = $project->unitQuotation?->id_sales;
                return ($quoteSales == Auth::id()) || ($unitSales == Auth::id());
            });
        }

        $projectIds = $projects->pluck('id');
        $materialCostByProject = PurchaseRequest::whereIn('id_pending', $projectIds)
            ->where('status', '3')
            ->groupBy('id_pending')->selectRaw('id_pending, SUM(amount) as total')
            ->pluck('total', 'id_pending');
        $generalCostByProject = ProjectExpense::whereIn('id_pending', $projectIds)
            ->groupBy('id_pending')->selectRaw('id_pending, SUM(amount) as total')
            ->pluck('total', 'id_pending');
        $shippingCostByProject = Expanse::whereIn('id_pending', $projectIds)
            ->where('type', 'Resi')
            ->groupBy('id_pending')->selectRaw('id_pending, SUM(cost) as total')
            ->pluck('total', 'id_pending');

        $projects = $projects->map(function ($project) use ($materialCostByProject, $generalCostByProject, $shippingCostByProject) {
            $project->order_date = $project->date;
            $project->company = $project->unitQuotation?->client?->company
                ?? $project->quote?->pic?->client?->company
                ?? '-';
            $project->area = $project->unitQuotation?->client?->area
                ?? $project->quote?->pic?->client?->area
                ?? '-';
            $project->sales_name = $project->unitQuotation?->sales?->name
                ?? $project->quote?->sales?->name
                ?? '-';
            $project->sales_image = $project->unitQuotation?->sales?->image
                ?? $project->quote?->sales?->image
                ?? null;
            $project->revenue = $project->unitQuotation ? ($project->unitQuotation->total ?? 0) : ($project->quote?->nett ?? 0);
            $project->no_po = $project->unitQuotation ? ($project->unitQuotation->po_number ?? '-') : ($project->quote?->invoice->first()?->no_po ?? '-');
            $project->detail_route = $project->id_unit_quotation
                ? route('pending-po.show', $project->id)
                : route('project-monitoring.show', $project->id);
            $project->material_cost = (float) $materialCostByProject->get($project->id, 0);
            $project->general_cost = (float) $generalCostByProject->get($project->id, 0);
            $project->shipping_cost = (float) $shippingCostByProject->get($project->id, 0);
            $project->total_cost = $project->material_cost + $project->general_cost + $project->shipping_cost;
            $project->profit = $project->revenue - $project->total_cost;
            $project->margin = $project->revenue > 0 ? ($project->profit / $project->revenue) * 100 : 0;
            return $project;
        })->sortByDesc(function ($project) {
            return $project->order_date ? Carbon::parse($project->order_date)->timestamp : 0;
        })->values();

        $newProjects = $projects->filter(fn($p) => $p->status == 0);
        $checkPartsProjects = $projects->filter(fn($p) => $p->status != 0 && $p->status != 6 && ($p->project_status_step ?? 1) == 1);
        $schedulingProjects = $projects->filter(fn($p) => $p->status != 0 && $p->status != 6 && ($p->project_status_step ?? 1) == 2);
        $inProgressProjects = $projects->filter(fn($p) => $p->status != 0 && $p->status != 6 && ($p->project_status_step ?? 1) >= 3);
        $completedProjects = $projects->filter(fn($p) => $p->status == 6);

        $totalProjectsCount = $projects->count();
        $totalRevenueProject = $projects->sum('revenue');
        $totalMaterialProject = $projects->sum('material_cost');
        $totalGeneralProject = $projects->sum('general_cost');
        $totalShippingProject = $projects->sum('shipping_cost');
        $totalCostProject = $totalMaterialProject + $totalGeneralProject + $totalShippingProject;
        $totalProfitProject = $totalRevenueProject - $totalCostProject;
        $overallMarginProject = $totalRevenueProject > 0 ? ($totalProfitProject / $totalRevenueProject) * 100 : 0;

        // Legacy compatibility variables for modals
        $schedules = ServiceOrder::join(DB::raw("(
            SELECT id_sales_order, MAX(id) as max_id
            FROM service_order
            GROUP BY id_sales_order
        ) so_max"), 'service_order.id', '=', 'so_max.max_id')
            ->join('pending_po as p', 'p.id', '=', 'service_order.id_sales_order')
            ->where('p.status', 2)
            ->select('service_order.*', 'p.no_pending', 'p.title')
            ->get();
        $orders = PendingPO::where('status', 2)->where('type', 'Project')->get();

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
        $resi = Expanse::find($id);
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
        $schedule = ServiceOrder::find($id);
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
        $schedule = ServiceOrder::find($id);
        $schedule->SJ = $request->has('SJ') ? '1' : '0';
        $schedule->BA = $request->has('BA') ? '1' : '0';
        $schedule->note_doc = $request->note;
        $schedulesave = $schedule->save();
        if ($schedule->SJ == '1' && $schedule->BA == '1') {
            $order = PendingPO::find($schedule->id_sales_order);
            $order->status = '9';
            $order->save();
        }
        if ($schedulesave) {
            return redirect('/sales-order')->with('message', 'data telah di tambahkan');
        }
    }
    public function returProduct(Request $request, $id)
    {
        $pending = PendingPO::find($id);
        $quote = Quotation::find($pending->id_quotation);
        // dd($pending);
        $return = new Retur();
        $return->id_pending = $id;
        $return->no_return = $request->no_return;
        $return->status = 0;
        $return->date = Carbon::now();
        $returnSave = $return->save();

        $pending->status = '8';
        $pending->save();
        $productOut = ProductOut::find($pending->id_product_out);
        $detProduct = DetailProductOut::where('id_product_out', $productOut->id)->get();
        foreach ($request->qty as $key => $value) {
            if ($value != 0) {
                $dproduct = DetailProduct::find($detProduct[$key]->id_detail_product);
                $product = Product::find($dproduct->id_product);
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
        $pending = PendingPO::find($id);
        $pending->status = '6';
        $pending->save();
        $return = Retur::where('id_pending', $id)->get();
        foreach ($return as $retur) {
            $dproduct = DetailProduct::find($retur->id_replacement);
            $product = Product::find($dproduct->id_product);
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
