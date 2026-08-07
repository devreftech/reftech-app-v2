<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Delivery;
use App\Models\DetailDelivery;
use App\Models\DetailQuotation;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Suo;
use App\Models\SubtitleQuotation;
use App\Models\UnitQuotation;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.accounting.delivery.manual.form');
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
            'destination' =>
                'required',
        ];
        $message = [
            'destination.required' => 'Field Destination Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        $invoice = Invoice::find($request->id_invoice);
        $quote = Quotation::find($invoice->id_quotation);
        $dQuote = DetailQuotation::where('id_quotation', $invoice->id_quotation)->get();

        $delivery = new Delivery;
        $delivery->id_invoice = $request->id_invoice;
        $delivery->date = $request->date;
        $delivery->destination = $request->destination;
        $delivery->type = $request->type;
        $delivery->code = $quote->type;
        $deliverySave = $delivery->save();
        if ($quote->type == 'Sparepart') {
            foreach ($dQuote as $item => $value) {
                if ($request->qty[$item] >= 1) {
                    $dDelivery = new DetailDelivery;
                    $dDelivery->id_delivery = $delivery->id;
                    $dDelivery->id_pn = $value->id_equivalent;
                    $dDelivery->desc = $value->detail_product;
                    $dDelivery->qty = $request->qty[$item];
                    $dDelivery->info_qty = $value->info_qty;
                    $delivery->view = '0';
                    $status = $dDelivery->save();
                }
            }
        }
        if ($quote->type == 'Sparepart') {
            if ($deliverySave && $status) {
                return redirect('/delivery/' . $delivery->id)->with("success", "Data Delivery Telah Ditambahkan");
            }
        } else {
            if ($deliverySave) {
                return redirect('/delivery/' . $delivery->id)->with("success", "Data Delivery Telah Ditambahkan");
            }
        }
        // else {
        //     return redirect('/delivery/' . $delivery->id)->with("success", "Data Delivery Telah Ditambahkan");
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $delivery = Delivery::find($id);
        $dDelivery = DetailDelivery::where('id_delivery', $id)->get();

        // SUO delivery — tidak punya id_invoice
        if ($delivery->id_suo) {
            $suo    = Suo::with(['detail', 'sales'])->find($delivery->id_suo);
            $client = Client::where('company', $suo->company)->first();
            return view('pages.suo.sj-detail', compact('delivery', 'dDelivery', 'suo', 'client'));
        }

        // Unit Quotation delivery
        if ($delivery->id_unit_quotation) {
            $unitQuote = UnitQuotation::with(['client', 'pic', 'sales'])->find($delivery->id_unit_quotation);
            $invoice   = $delivery->id_invoice ? Invoice::find($delivery->id_invoice) : null;
            return view('pages.unit-quotation.sj-detail', compact('delivery', 'dDelivery', 'unitQuote', 'invoice'));
        }

        $invoice  = Invoice::find($delivery->id_invoice);
        $quote    = Quotation::find($invoice->id_quotation);
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $quote->id)->get();

        return view("pages.accounting.delivery.detail", compact('subQuote', 'delivery', 'dDelivery', 'invoice', 'quote'));
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
        $delivery = Delivery::find($id);
        $detDelevery = DetailDelivery::where('id_delivery', $id)->get();

        $delDelivery = $delivery->delete();
        $delDetDelivery = true;
        foreach ($detDelevery as $product) {
            $delDetDelivery = $product->delete() && $delDetDelivery;
        }
        if ($delDelivery && $delDetDelivery) {
            return 1;
        } else {
            return 0;
        }
    }
    public function create_manual_teknisi($id)
    {
        $invoice = Invoice::find($id);
        return view('pages.accounting.delivery.manual.form-teknisi', compact('invoice'));
    }
    public function create_manual_ekspedisi($id)
    {
        $invoice = Invoice::find($id);
        return view('pages.accounting.delivery.manual.form-ekspedisi', compact('invoice'));
    }
    public function store_manual(Request $request, $id)
    {
        $rule = [
            'destination' =>
                'required',
        ];
        $message = [
            'destination.required' => 'Field Destination Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        // dd($request->all());
        $invoice = Invoice::find($id);
        // $quote = Quotation::find($invoice->id_quotation);
        // $dQuote = DetailQuotation::where('id_quotation', $invoice->id_quotation)->get();

        $delivery = new Delivery;
        $delivery->id_invoice = $id;
        $delivery->date = $request->date;
        $delivery->destination = $request->destination;
        $delivery->type = $request->type;
        $delivery->code = 'Manual';
        $deliverySave = $delivery->save();
        // if ($quote->type == 'Sparepart') {
        foreach ($request->product as $item => $value) {
            $dDelivery = new DetailDelivery;
            $dDelivery->id_delivery = $delivery->id;
            $dDelivery->product = $request->product[$item];
            $dDelivery->desc = $request->detail_product[$item];
            $dDelivery->qty = $request->qty[$item];
            $dDelivery->info_qty = $request->info_qty[$item];
            $delivery->view = '0';
            $status = $dDelivery->save();
        }
        // }
        // if ($quote->type == 'Sparepart') {
        if ($deliverySave && $status) {
            return redirect('/delivery/manual/' . $delivery->id)->with("success", "Data Delivery Telah Ditambahkan");
        }
        // } else {
        // if ($deliverySave) {
        //         return redirect('/delivery/' . $delivery->id)->with("success", "Data Delivery Telah Ditambahkan");
        //     }
        // }
        // else {
        //     return redirect('/delivery/' . $delivery->id)->with("success", "Data Delivery Telah Ditambahkan");
        // }
    }

    public function show_manual($id)
    {
        $delivery = Delivery::find($id);
        $dDelivery = DetailDelivery::where('id_delivery', $id)->get();
        $invoice = invoice::find($delivery->id_invoice);
        $quote = Quotation::find($invoice->id_quotation);
        // $dQuote = DetailQuotation::where('id_quotation', $invoice->id_quotation)->get();
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $quote->id)->get();

        return view("pages.accounting.delivery.manual.detail", compact('subQuote', 'delivery', 'dDelivery', 'invoice', 'quote'));
    }
    public function print_delivery_manual($id)
    {
        $delivery = Delivery::find($id);
        $dDelivery = DetailDelivery::where('id_delivery', $id)->get();
        $invoice = invoice::find($delivery->id_invoice);
        $quote = Quotation::find($invoice->id_quotation);
        // $dQuote = DetailQuotation::where('id_quotation', $invoice->id_quotation)->get();
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $quote->id)->get();

        return view("pages.accounting.delivery.manual.detail-print", compact('subQuote', 'delivery', 'dDelivery', 'invoice', 'quote'));
    }
    public function print_delivery($id)
    {
        $delivery  = Delivery::find($id);
        $dDelivery = DetailDelivery::where('id_delivery', $id)->get();

        // SUO delivery — tidak ada id_invoice
        if ($delivery->id_suo) {
            $suo    = Suo::with(['detail', 'sales'])->find($delivery->id_suo);
            $client = Client::where('company', $suo->company)->first();
            $view   = request('format') == '1' ? 'pages.suo.sj-print-type1' : 'pages.suo.sj-print';
            return view($view, compact('delivery', 'dDelivery', 'suo', 'client'));
        }

        // Unit Quotation delivery
        if ($delivery->id_unit_quotation) {
            $unitQuote = UnitQuotation::with(['client', 'pic', 'sales'])->find($delivery->id_unit_quotation);
            $invoice   = $delivery->id_invoice ? Invoice::find($delivery->id_invoice) : null;
            return view('pages.unit-quotation.sj-print', compact('delivery', 'dDelivery', 'unitQuote', 'invoice'));
        }

        $invoice  = Invoice::find($delivery->id_invoice);
        $quote    = Quotation::find($invoice->id_quotation);
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $quote->id)->get();

        return view("pages.accounting.delivery.detail-print", compact('subQuote', 'delivery', 'dDelivery', 'invoice', 'quote'));
    }

    public function change_date(Request $request, $id)
    {
        $delivery = Delivery::find($id);

        if (@$request->check == '1') {
            $delivery->date = NULL;
        } else {
            $delivery->date = $request->date;
        }

        $delivery->destination = $request->destination;
        $status = $delivery->save();

        if ($status) {
            return redirect('/delivery/' . $id)->with('massage', 'Data telah terkirim');
        }
    }
    public function change_date_label(Request $request, $id)
    {
        $invoice = Invoice::find($id);

        if (@$request->check == '1') {
            $invoice->date = NULL;
        } else {
            $invoice->date = $request->date;
        }

        $invoice->invoiceTo = $request->destination;
        $status = $invoice->save();

        if ($status) {
            return redirect('/invoice/label_detail/' . $id)->with('massage', 'Data telah terkirim');
        }
    }
    public function change_desc(Request $request, $id)
    {
        $delivery = Delivery::find($id);
        $dDelivery = DetailDelivery::where('id_delivery', $delivery->id)->get();
        // dd($dDelivery);
        $checkedIds = (array) $request->input('checker', []);

        foreach ($dDelivery as $value) {
            $value->view = in_array($value->id, $checkedIds) ? '1' : '0';
            $status = $value->save();
        }

        if ($status) {
            return redirect('/delivery/' . $id)->with('message', 'Data telah terkirim');
        } else {
            return redirect('/delivery/' . $id)->with('error', 'Terjadi kesalahan saat mengirim data');
        }
    }

    public function toggleItemView($id)
    {
        $item = DetailDelivery::findOrFail($id);
        $item->view = $item->view == '1' ? '0' : '1';
        $item->save();

        return response()->json(['success' => true, 'view' => $item->view]);
    }
}
