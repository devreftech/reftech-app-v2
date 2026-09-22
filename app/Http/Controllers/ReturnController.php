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
use Illuminate\Http\Request;

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
        $return = Retur::findOrFail($id);
        $dReturn = DetailReturn::where('id_retur', $id)->with('replacement.product')->get();
        $pending = $return->id_pending ? PendingPO::find($return->id_pending) : null;
        $quote = $pending ? Quotation::find($pending->id_quotation) : null;
        $productIn = $return->id_product_in ? ProductIn::find($return->id_product_in) : null;

        return view('pages.warehouse.return.detail', compact('return', 'dReturn', 'pending', 'quote', 'productIn'));
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
