<?php

namespace App\Http\Controllers;

use App\Models\DetailQuotation;
use App\Models\DetailReturn;
use App\Models\Invoice;
use App\Models\PendingPO;
use App\Models\Quotation;
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
    public function index()
    {
        return view('pages.warehouse.return.index');
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
        $return = ReturnQ::find($id);
        $dReturn = DetailReturn::where('id_retur', $id)->get();
        $pending = PendingPO::find($return->id_pending);
        $quote = Quotation::find($pending->id_quotation);
        // $tax = $return->subtotal * $return->tax / 100;
        // dd($pending);
        // $dQuote = DetailQuotation::where('id_quotation', $return->id_quotation)->get();
        return view('pages.warehouse.return.detail', compact('return', 'dReturn', 'pending', 'quote'));
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
        //
    }

    public function accept($id)
    {
        $dReturn = DetailReturn::find($id);
        $dReturn->status = 1;
        $returnSave = $dReturn->save();
        if ($returnSave) {
            return 1;
        } else {
            return 0;
        }
    }

    public function accept_return($id)
    {
        $return = ReturnQ::find($id);
        $dReturn = DetailReturn::where('id_return', $id)->get();
        $quote = Quotation::find($return->id_quotation);

        $return->lvl = '1';
        $status = $return->save();

        foreach ($dReturn as $product) {
            $equiv = SerialProduct::find($product->id_pn);
            $equiv->product->stock += $product->qty;
            $status = $equiv->save();

            $dQuote = DetailQuotation::where('id_equivalent', $product->id_pn)->where('id_quotation', $return->id_quotation)->first();
            if ($dQuote->qty - $product->qty == 0) {
                $dQuoteDel = $dQuote->delete();
            } elseif ($dQuote->qty - $product->qty > 0) {
                $dQuote->qty -= $product->qty;
                if ($dQuote->disc > 0) {
                    $dQuote->amount = $dQuote->qty * $dQuote->price * ($dQuote->disc / 100);
                } elseif ($dQuote->disc == 0) {
                    $dQuote->amount = $dQuote->qty * $dQuote->price;
                }
                $dQuote->save();
            }
        }
        $detailQ = DetailQuotation::where('id_quotation', $quote->id)->get();
        $subtotal = 0;
        foreach ($detailQ as $product) {
            $subtotal += $product->amount;
        }

        $quote->subtotal = $subtotal;
        $dTotal = $subtotal - $quote->diskon;
        $quote->nett = $subtotal;
        $quote->total_no_tax = $dTotal + $quote->shipping;
        $quote->harga_total = $dTotal + ($dTotal * $quote->tax / 100) + $quote->shipping;
        $status = $quote->save();
        if ($status && $dQuoteDel) {
            return 1;
        } else {
            return 0;
        }

    }
}
