<?php

namespace App\Http\Controllers;

use App\Models\DetailProduct;
use App\Models\DetailQuotation;
use App\Models\DetailReturn;
use App\Models\Invoice;
use App\Models\PendingPO;
use App\Models\ProductIn;
use App\Models\Quotation;
use App\Models\Retur;
use App\Models\ReturnQ;
use App\Models\SerialProduct;
use App\Models\UnitQuotation;
use App\Models\UnitQuotationDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $returns = Retur::with([
            'pending.quotation.pic.client',
            'pending.quotation.sales',
            'pending.unitQuotation.client',
            'pending.unitQuotation.sales',
            'quotation.pic.client',
            'quotation.sales',
            'unitQuotation.client',
            'unitQuotation.sales',
            'sales',
            'productIn.supplier',
            'detail.replacement.product',
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        $totalCount = $returns->count();
        $pendingCount = $returns->where('status', 0)->count();
        $completedCount = $returns->where('status', 1)->count();
        $totalQty = $returns->sum(function ($r) {
            return $r->detail->sum('qty');
        });

        return view('pages.warehouse.return.index', compact(
            'returns',
            'totalCount',
            'pendingCount',
            'completedCount',
            'totalQty'
        ));
    }

    /**
     * Store a sales return request from Quotation / Smart Quote.
     */
    public function requestSalesReturn(Request $request, $type, $id)
    {
        $request->validate([
            'selected_items' => 'required|array|min:1',
            'reason_category' => 'required|string',
            'resolution' => 'required|string|in:replacement,refund,deposit',
            'qty' => 'required|array',
        ], [
            'selected_items.required' => 'Pilih minimal satu item yang ingin diajukan retur.',
            'reason_category.required' => 'Pilih kategori alasan retur.',
            'resolution.required' => 'Pilih solusi yang diajukan.',
        ]);

        return DB::transaction(function () use ($request, $type, $id) {
            $quote = null;
            $unitQuote = null;
            $pending = null;
            $salesId = Auth::id();

            if ($type === 'smart-quote' || $type === 'unit') {
                $unitQuote = UnitQuotation::with(['details', 'pending'])->findOrFail($id);
                $pending = $unitQuote->pending;
                $salesId = $unitQuote->id_sales ?? Auth::id();
            } else {
                $quote = Quotation::with(['detail', 'pending'])->findOrFail($id);
                $pending = $quote->pending;
                $salesId = $quote->id_sales ?? Auth::id();
            }

            // Auto-generate No Return: RET/YYYYMM/XXXX
            $prefix = 'RET/' . date('Ym') . '/';
            $countThisMonth = Retur::where('no_return', 'like', $prefix . '%')->count() + 1;
            $noReturn = $prefix . sprintf('%04d', $countThisMonth);

            $return = new Retur();
            $return->id_pending = $pending?->id;
            $return->id_quotation = $quote?->id;
            $return->id_unit_quotation = $unitQuote?->id;
            $return->id_sales = $salesId;
            $return->no_return = $noReturn;
            $return->status = 0; // 0 = Menunggu Review
            $return->reason_category = $request->reason_category;
            $return->reason_note = $request->reason_note;
            $return->resolution = $request->resolution;
            $return->bank_name = $request->resolution === 'refund' ? $request->bank_name : null;
            $return->bank_account = $request->resolution === 'refund' ? $request->bank_account : null;
            $return->bank_holder = $request->resolution === 'refund' ? $request->bank_holder : null;
            $return->date = Carbon::now();
            $return->save();

            $totalAmount = 0;

            foreach ($request->selected_items as $idx) {
                $qty = (int)($request->qty[$idx] ?? 1);
                if ($qty <= 0) continue;

                $price = (float)($request->price[$idx] ?? 0);
                $amount = $qty * $price;
                $totalAmount += $amount;

                $detReturn = new DetailReturn();
                $detReturn->id_retur = $return->id;
                $detReturn->id_replacement = !empty($request->id_replacement[$idx]) ? (int)$request->id_replacement[$idx] : null;
                if ($type === 'smart-quote' || $type === 'unit') {
                    $detReturn->id_unit_quotation_detail = !empty($request->item_id[$idx]) ? (int)$request->item_id[$idx] : null;
                } else {
                    $detReturn->id_detail_quotation = !empty($request->item_id[$idx]) ? (int)$request->item_id[$idx] : null;
                }
                $detReturn->item_name = $request->item_name[$idx] ?? 'Item';
                $detReturn->qty = $qty;
                $detReturn->price = $price;
                $detReturn->amount = $amount;
                $detReturn->note = $request->item_note[$idx] ?? '-';
                $detReturn->status = 0;
                $detReturn->date = Carbon::today();
                $detReturn->save();
            }

            $return->total_amount = $totalAmount;
            $return->save();

            return redirect()->back()->with('success', 'Pengajuan retur berhasil dikirim (' . $noReturn . ') dan masuk ke Menu Retur untuk direview.');
        });
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
        $return = Retur::with([
            'pending.quotation.pic.client',
            'quotation.pic.client',
            'unitQuotation.client',
            'sales',
            'productIn.supplier'
        ])->findOrFail($id);

        $dReturn = DetailReturn::where('id_retur', $id)->with('replacement.product')->get();
        $pending = $return->id_pending ? PendingPO::find($return->id_pending) : null;
        $quote = $return->id_quotation ? Quotation::find($return->id_quotation) : ($pending ? Quotation::find($pending->id_quotation) : null);
        $unitQuote = $return->id_unit_quotation ? UnitQuotation::find($return->id_unit_quotation) : ($pending ? UnitQuotation::find($pending->id_unit_quotation) : null);
        $productIn = $return->id_product_in ? ProductIn::find($return->id_product_in) : null;

        return view('pages.warehouse.return.detail', compact('return', 'dReturn', 'pending', 'quote', 'unitQuote', 'productIn'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return redirect()->route('invoice.index')->with('error', 'Invoice tidak ditemukan');
        }
        $quote = Quotation::where('id', $invoice->id_quotation)->first();
        // dd($quote);
        $dQuote = DetailQuotation::where('id_quotation', $invoice->id_quotation)->get();
        return view('pages.accounting.return.form', compact('invoice', 'quote', 'dQuote'));
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
        $invoice = Invoice::find($id);
        $quote = Quotation::find($invoice->id_quotation);
        $return = new ReturnQ;
        $return->id_quotation = $invoice->id_quotation;
        $return->no_return = $request->no_return;
        $return->subtotal = $request->subtotal;
        $return->tax = $request->tax;
        $return->total = $request->total;
        $return->note = $request->note;
        $return->date = $request->date;
        $return->lvl = '0';
        $status = $return->save();
        foreach ($request->equivalent as $item => $value) {
            if ($request->qty[$item] >= 1) {
                $dReturn = new DetailReturn;
                $dReturn->id_return = $return->id;
                $dReturn->id_pn = $request->equivalent[$item];
                $dReturn->detail_product = $request->detail_equivalent[$item];
                $dReturn->qty = $request->qty[$item];
                $dReturn->info_qty = $request->info_qty[$item];
                $dReturn->price = $request->price[$item];
                $status = $dReturn->save();
            }

            //         $equiv = SerialProduct::find($request->equivalent[$item]);
            //         $equiv->product->stock += $request->qty[$item];
            //         $status = $equiv->save();

            //         $dQuote = DetailQuotation::where('id_equivalent', $request->equivalent[$item])->first();
            //         // dd($dQuote);
            //         if ($dQuote->qty - $request->qty[$item] == 0) {
            //             $status = $dQuote->delete();
            //         }elseif ($dQuote->qty - $request->qty[$item] > 0)  {
            //             $dQuote->qty -= $request->qty[$item];
            //             if ($dQuote->disc > 0) {
            //                 $dQuote->amount = $dQuote->qty * $dQuote->price * ($dQuote->disc / 100);
            //             } elseif ($dQuote->disc == 0) {
            //                 $dQuote->amount = $dQuote->qty * $dQuote->price;
            //             }
            //             $dQuote->save();
            //         }
            //     }
            //     $detailQ = DetailQuotation::where('id', $quote->id)->get();
            //     $subtotal = 0;
            //     foreach ($detailQ as $product) {
            //         $subtotal += $product->amount;
            //     }

            //     $quote->subtotal = $subtotal;
            //     $dTotal = $subtotal - $quote->diskon;
            //     $quote->nett = $subtotal;
            //     $quote->total_no_tax = $dTotal + $quote->shipping;
            //     $quote->harga_total = $dTotal + ($dTotal * $quote->tax / 100) + $quote->shipping;
            //     $status = $quote->save();
        }
        if (
            $status
        ) {
            return redirect('/invoice/' . $id)->with('message', 'data telah di return');
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
        try {
            $return = Retur::findOrFail($id);
            DetailReturn::where('id_retur', $id)->delete();
            $return->delete();

            return redirect()->route('return.index')->with('success', 'Data return berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data return: ' . $e->getMessage());
        }
    }

    public function accept($id)
    {
        $dReturn = DetailReturn::find($id);
        if (!$dReturn) {
            return 0;
        }
        $dReturn->status = 1;
        $returnSave = $dReturn->save();
        return $returnSave ? 1 : 0;
    }

    public function accept_return($id)
    {
        $return = Retur::find($id);
        if (!$return) {
            return 0;
        }
        $dReturn = DetailReturn::where('id_retur', $id)->get();
        $pending = $return->id_pending ? PendingPO::find($return->id_pending) : null;
        $quote = $pending ? Quotation::find($pending->id_quotation) : null;

        $return->status = 1;
        $return->done_date = now();
        $status = $return->save();

        if ($quote) {
            foreach ($dReturn as $product) {
                if ($product->id_replacement) {
                    $equiv = DetailProduct::find($product->id_replacement);
                    if ($equiv && $equiv->product) {
                        $equiv->product->stock += $product->qty;
                        $equiv->product->save();
                    }
                }
            }
        }
        return $status ? 1 : 0;
    }
}
