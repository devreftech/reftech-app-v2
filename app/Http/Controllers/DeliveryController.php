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
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     * Supports both direct view rendering and server-side DataTables AJAX requests.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Handle Server-Side DataTables AJAX request
        if ($request->ajax() && ($request->has('draw') || $request->has('columns'))) {
            $draw   = (int) $request->input('draw', 1);
            $start  = max(0, (int) $request->input('start', 0));
            $length = max(1, min(100, (int) $request->input('length', 15)));
            $search = trim($request->input('search.value', ''));

            $query = Delivery::select([
                'id', 'no_do', 'entity', 'id_invoice', 'id_suo', 'id_unit_quotation',
                'customer_name', 'address', 'po_number', 'date', 'type', 'code', 'destination',
                'driver_name', 'vehicle_no', 'sign_token', 'customer_signature', 'customer_signed_at', 'created_at'
            ])
            ->with([
                'invoice:id,id_quotation,no_invoice,no_po,flag,invoiceTo',
                'invoice.quote:id,id_pic',
                'invoice.quote.pic:id,id_client',
                'invoice.quote.pic.client:id,company,address,subAddress',
                'unitQuotation:id,no_quote,po_number,id_client',
                'unitQuotation.client:id,company,address,subAddress,info',
                'suo:id,no_suo,no_invoice_booking,company,address',
            ]);

            $recordsTotal = Delivery::count();

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('no_do', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('po_number', 'like', "%{$search}%")
                      ->orWhere('driver_name', 'like', "%{$search}%")
                      ->orWhereHas('invoice', function ($inv) use ($search) {
                          $inv->where('no_invoice', 'like', "%{$search}%")
                              ->orWhere('no_po', 'like', "%{$search}%");
                      })
                      ->orWhereHas('unitQuotation', function ($uq) use ($search) {
                          $uq->where('no_quote', 'like', "%{$search}%")
                             ->orWhere('po_number', 'like', "%{$search}%");
                      })
                      ->orWhereHas('suo', function ($suo) use ($search) {
                          $suo->where('no_suo', 'like', "%{$search}%")
                              ->orWhere('no_invoice_booking', 'like', "%{$search}%")
                              ->orWhere('company', 'like', "%{$search}%");
                      });
                });
            }

            $recordsFiltered = $query->count();
            $deliveries = $query->latest('id')->skip($start)->take($length)->get();

            $data = $deliveries->map(function ($del) {
                $delType = strtolower($del->type ?? 'ekspedisi');
                $printUrl = ($del->code === 'Manual' || !$del->id_invoice)
                    ? route('delivery.print_manual', $del->id) . '?format=' . $delType
                    : route('print.delivery', $del->id) . '?format=' . $delType;

                return [
                    'id'            => $del->id,
                    'do_number'     => $del->do_number,
                    'po_number'     => $del->po_number_display,
                    'customer'      => $del->customer_name_display,
                    'address'       => $del->address_display,
                    'date'          => $del->date ? \Carbon\Carbon::parse($del->date)->format('d/m/Y') : '-',
                    'code'          => $del->code ?: ($del->id_suo ? 'SUO' : 'Delivery'),
                    'entity'        => $del->entity_display,
                    'type'          => $delType,
                    'driver_name'   => $del->driver_name,
                    'is_signed'     => $del->isSignedByCustomer(),
                    'signed_at'     => $del->customer_signed_at ? \Carbon\Carbon::parse($del->customer_signed_at)->format('d/m/Y H:i') : null,
                    'show_url'      => route('delivery.show', $del->id),
                    'print_url'     => $printUrl,
                    'sign_url'      => $del->sign_url,
                    'delete_url'    => route('delivery.destroy', $del->id),
                    'reset_url'     => route('delivery.reset-signature', $del->id),
                ];
            });

            return response()->json([
                'draw'            => $draw,
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ]);
        }

        // 1. Fast SQL counts for Metric Cards
        $totalCount       = Delivery::count();
        $ekspedisiCount   = Delivery::where('type', 'ekspedisi')->count();
        $teknisiCount     = Delivery::where('type', 'teknisi')->count();
        $signedCount      = Delivery::whereNotNull('customer_signature')->whereNotNull('customer_signed_at')->count();
        $pendingSignCount = $totalCount - $signedCount;

        return view('pages.accounting.delivery.index', compact(
            'totalCount',
            'ekspedisiCount',
            'teknisiCount',
            'signedCount',
            'pendingSignCount'
        ));
    }

    /**
     * Search clients for Select2 AJAX in manual DO creation.
     */
    public function searchClients(Request $request)
    {
        $q = trim($request->input('q', ''));
        $page = max(1, (int) $request->input('page', 1));
        $limit = 30;

        $query = Client::select(['id', 'company', 'address', 'subAddress', 'info']);

        if (!empty($q)) {
            $query->where('company', 'like', "%{$q}%");
        }

        $totalCount = $query->count();
        $clients = $query->orderBy('company')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        $results = $clients->map(function ($c) {
            return [
                'id'          => $c->id,
                'text'        => $c->company,
                'company'     => $c->company,
                'address'     => $c->address,
                'sub_address' => $c->subAddress,
                'entity'      => ($c->info === 'Kojisha') ? 'Kojisha' : 'Reftech',
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $limit) < $totalCount,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $invoices = Invoice::select(['id', 'id_quotation', 'id_unit_quotation', 'no_invoice', 'no_po', 'flag', 'invoiceTo'])
            ->whereNotNull('no_invoice')
            ->with([
                'quote:id,id_pic',
                'quote.pic:id,id_client',
                'quote.pic.client:id,company,address,subAddress',
                'unitQuote:id,id_client',
                'unitQuote.client:id,company,address,subAddress,info',
            ])
            ->orderByDesc('id')
            ->take(150)
            ->get();

        return view('pages.accounting.delivery.create', compact('invoices'));
    }

    /**
     * Generate sequential DO number for manual delivery orders.
     */
    public function generateNoDo($entity = 'Reftech')
    {
        $code = ($entity === 'Kojisha') ? 'KII' : 'RT';
        $year = date('Y');
        $month = date('m');
        $prefix = "SJ/{$code}/{$year}/{$month}/";

        $latest = Delivery::where('no_do', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('no_do');

        $nextSeq = 1;
        if ($latest) {
            $parts = explode('/', $latest);
            $lastSeq = (int) end($parts);
            $nextSeq = $lastSeq + 1;
        } else {
            $countThisMonth = Delivery::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
            $nextSeq = $countThisMonth + 1;
        }

        return $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Store a newly created manual delivery order (from modal or dedicated form).
     */
    public function storeManualCustom(Request $request)
    {
        $request->validate([
            'source_type'   => 'required|in:invoice,standalone',
            'id_invoice'    => 'nullable|required_if:source_type,invoice|integer',
            'entity'        => 'required|in:Reftech,Kojisha',
            'customer_name' => 'required|string|max:255',
            'address'       => 'required|string',
            'date'          => 'nullable|date',
            'type'          => 'required|in:teknisi,ekspedisi',
            'no_do'         => 'nullable|string|max:255',
            'po_number'     => 'nullable|string|max:255',
            'driver_name'   => 'nullable|string|max:255',
            'vehicle_no'    => 'nullable|string|max:255',
            'note'          => 'nullable|string',
            'items'         => 'required|array|min:1',
            'items.*.product'  => 'required|string|max:255',
            'items.*.desc'     => 'nullable|string',
            'items.*.qty'      => 'required|numeric|min:0.01',
            'items.*.info_qty' => 'required|string|max:50',
        ], [
            'customer_name.required' => 'Nama Customer / Perusahaan wajib diisi.',
            'address.required'       => 'Alamat Pengiriman wajib diisi.',
            'items.required'         => 'Minimal 1 item barang harus dimasukkan.',
            'items.*.product.required' => 'Nama barang pada setiap baris item wajib diisi.',
            'items.*.qty.required'     => 'Jumlah Qty wajib diisi.',
        ]);

        $delivery = DB::transaction(function () use ($request) {
            $entity = $request->input('entity', 'Reftech') ?: 'Reftech';
            $noDo = $request->filled('no_do') ? trim($request->input('no_do')) : $this->generateNoDo($entity);

            $delivery = new Delivery();
            $delivery->no_do = $noDo;
            $delivery->entity = $entity;
            if ($request->input('source_type') === 'invoice' && $request->filled('id_invoice')) {
                $delivery->id_invoice = $request->input('id_invoice');
            }
            $delivery->customer_name = $request->input('customer_name');
            $delivery->address = $request->input('address');
            $delivery->po_number = $request->input('po_number');
            $delivery->destination = $request->input('destination', '1') ?: '1';
            $delivery->driver_name = $request->input('driver_name');
            $delivery->vehicle_no = $request->input('vehicle_no');
            $delivery->note = $request->input('note');
            $delivery->date = $request->filled('date') ? $request->input('date') : null;
            $delivery->type = $request->input('type', 'ekspedisi');
            $delivery->code = 'Manual';
            $delivery->save();

            foreach ($request->input('items', []) as $item) {
                if (empty($item['product']) && empty($item['desc'])) {
                    continue;
                }
                $dDelivery = new DetailDelivery();
                $dDelivery->id_delivery = $delivery->id;
                $dDelivery->type = 'item';
                $dDelivery->product = $item['product'] ?? '';
                $dDelivery->desc = $item['desc'] ?? null;
                $dDelivery->qty = (float) ($item['qty'] ?? 1);
                $dDelivery->info_qty = $item['info_qty'] ?? 'Pcs';
                $dDelivery->view = '0';
                $dDelivery->save();
            }

            return $delivery;
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Surat Jalan Manual (' . $delivery->do_number . ') berhasil dibuat.',
                'delivery' => [
                    'id' => $delivery->id,
                    'no_do' => $delivery->do_number,
                    'show_url' => route('delivery.show', $delivery->id),
                    'print_url' => route('print.delivery', $delivery->id),
                ],
            ]);
        }

        return redirect()->route('delivery.index')->with('success', 'Surat Jalan Manual (' . $delivery->do_number . ') berhasil dibuat.');
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
            'destination' => 'required',
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
        $delivery->code = $quote->type ?? 'Sparepart';
        $deliverySave = $delivery->save();

        if (optional($quote)->type == 'Sparepart') {
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

        return redirect('/delivery/' . $delivery->id)->with("success", "Data Delivery Telah Ditambahkan");
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
        if (!$delivery) {
            abort(404, 'Surat Jalan tidak ditemukan.');
        }
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

        // Manual delivery or delivery without standard invoice quote
        if ($delivery->code == 'Manual' || !$delivery->id_invoice) {
            return $this->show_manual($id);
        }

        $invoice  = Invoice::find($delivery->id_invoice);
        if (!$invoice || !$invoice->id_quotation) {
            return $this->show_manual($id);
        }

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
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $delivery = Delivery::findOrFail($id);
        $dDelivery = DetailDelivery::where('id_delivery', $id)->get();

        $invoices = Invoice::with(['quote.pic.client', 'unitQuote.client'])
            ->whereNotNull('no_invoice')
            ->orderByDesc('id')
            ->get();

        return view('pages.accounting.delivery.edit', compact('delivery', 'dDelivery', 'invoices'));
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
        $delivery = Delivery::findOrFail($id);

        $request->validate([
            'source_type'   => 'nullable|in:invoice,standalone',
            'id_invoice'    => 'nullable|integer',
            'entity'        => 'required|in:Reftech,Kojisha',
            'customer_name' => 'required|string|max:255',
            'address'       => 'required|string',
            'date'          => 'nullable|date',
            'type'          => 'required|in:teknisi,ekspedisi',
            'no_do'         => 'nullable|string|max:255',
            'po_number'     => 'nullable|string|max:255',
            'driver_name'   => 'nullable|string|max:255',
            'vehicle_no'    => 'nullable|string|max:255',
            'note'          => 'nullable|string',
            'items'         => 'nullable|array|min:1',
            'items.*.product'  => 'required_with:items|string|max:255',
            'items.*.desc'     => 'nullable|string',
            'items.*.qty'      => 'required_with:items|numeric|min:0.01',
            'items.*.info_qty' => 'required_with:items|string|max:50',
        ], [
            'customer_name.required' => 'Nama Customer / Perusahaan wajib diisi.',
            'address.required'       => 'Alamat Pengiriman wajib diisi.',
            'items.*.product.required' => 'Nama barang pada setiap baris item wajib diisi.',
            'items.*.qty.required'     => 'Jumlah Qty wajib diisi.',
        ]);

        DB::transaction(function () use ($request, $delivery) {
            $delivery->entity = $request->input('entity', $delivery->entity ?: 'Reftech');
            if ($request->filled('no_do')) {
                $delivery->no_do = trim($request->input('no_do'));
            }
            if ($request->input('source_type') === 'invoice' && $request->filled('id_invoice')) {
                $delivery->id_invoice = $request->input('id_invoice');
            } elseif ($request->input('source_type') === 'standalone') {
                $delivery->id_invoice = null;
            }
            $delivery->customer_name = $request->input('customer_name');
            $delivery->address = $request->input('address');
            $delivery->po_number = $request->input('po_number');
            $delivery->destination = $request->input('destination', $delivery->destination ?: '1') ?: '1';
            $delivery->driver_name = $request->input('driver_name');
            $delivery->vehicle_no = $request->input('vehicle_no');
            $delivery->note = $request->input('note');
            $delivery->date = $request->filled('date') ? $request->input('date') : null;
            $delivery->type = $request->input('type', $delivery->type ?: 'ekspedisi');
            $delivery->save();

            if ($request->has('items') && is_array($request->input('items'))) {
                DetailDelivery::where('id_delivery', $delivery->id)->delete();
                foreach ($request->input('items', []) as $item) {
                    if (empty($item['product']) && empty($item['desc'])) {
                        continue;
                    }
                    $dDelivery = new DetailDelivery();
                    $dDelivery->id_delivery = $delivery->id;
                    $dDelivery->type = 'item';
                    $dDelivery->product = $item['product'] ?? '';
                    $dDelivery->desc = $item['desc'] ?? null;
                    $dDelivery->qty = (float) ($item['qty'] ?? 1);
                    $dDelivery->info_qty = $item['info_qty'] ?? 'Pcs';
                    $dDelivery->view = '0';
                    $dDelivery->save();
                }
            }
        });

        return redirect()->route('delivery.show', $delivery->id)->with('success', 'Surat Jalan (' . $delivery->do_number . ') berhasil diperbarui.');
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
        if (!$delivery) {
            return response()->json(['success' => false, 'message' => 'Delivery tidak ditemukan.'], 404);
        }

        $detDelevery = DetailDelivery::where('id_delivery', $id)->get();

        $delDelivery = $delivery->delete();
        $delDetDelivery = true;
        foreach ($detDelevery as $product) {
            $delDetDelivery = $product->delete() && $delDetDelivery;
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => $delDelivery && $delDetDelivery,
                'message' => 'Surat Jalan berhasil dihapus.'
            ]);
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
        if (!$invoice) {
            return redirect()->route('delivery.index')->with('error', 'Invoice tidak ditemukan');
        }
        return view('pages.accounting.delivery.manual.form-teknisi', compact('invoice'));
    }

    public function create_manual_ekspedisi($id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return redirect()->route('delivery.index')->with('error', 'Invoice tidak ditemukan');
        }
        return view('pages.accounting.delivery.manual.form-ekspedisi', compact('invoice'));
    }

    public function store_manual(Request $request, $id)
    {
        $rule = [
            'destination' => 'required',
        ];
        $message = [
            'destination.required' => 'Field Destination Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);

        $delivery = new Delivery;
        $delivery->id_invoice = $id;
        $delivery->date = $request->date;
        $delivery->destination = $request->destination;
        $delivery->type = $request->type;
        $delivery->code = 'Manual';
        $deliverySave = $delivery->save();

        $status = true;
        if ($request->has('product') && is_array($request->product)) {
            foreach ($request->product as $item => $value) {
                if (empty($value)) continue;
                $dDelivery = new DetailDelivery;
                $dDelivery->id_delivery = $delivery->id;
                $dDelivery->product = $request->product[$item] ?? '';
                $dDelivery->desc = $request->detail_product[$item] ?? null;
                $dDelivery->qty = $request->qty[$item] ?? 1;
                $dDelivery->info_qty = $request->info_qty[$item] ?? 'Pcs';
                $dDelivery->view = '0';
                $status = $dDelivery->save() && $status;
            }
        }

        if ($deliverySave && $status) {
            return redirect('/delivery/manual/' . $delivery->id)->with("success", "Data Delivery Telah Ditambahkan");
        }

        return redirect('/delivery/' . $delivery->id)->with("success", "Data Delivery Telah Ditambahkan");
    }

    public function show_manual($id)
    {
        $delivery = Delivery::findOrFail($id);
        $dDelivery = DetailDelivery::where('id_delivery', $id)->get();
        $invoice = $delivery->id_invoice ? Invoice::find($delivery->id_invoice) : null;
        $quote = ($invoice && $invoice->id_quotation) ? Quotation::find($invoice->id_quotation) : null;
        $subQuote = $quote ? SubtitleQuotation::with('detail')->where('id_quotation', $quote->id)->get() : collect();

        return view("pages.accounting.delivery.manual.detail", compact('subQuote', 'delivery', 'dDelivery', 'invoice', 'quote'));
    }

    public function print_delivery_manual($id)
    {
        $delivery = Delivery::findOrFail($id);
        $dDelivery = DetailDelivery::where('id_delivery', $id)->get();
        $invoice = $delivery->id_invoice ? Invoice::find($delivery->id_invoice) : null;
        $quote = ($invoice && $invoice->id_quotation) ? Quotation::find($invoice->id_quotation) : null;
        $subQuote = $quote ? SubtitleQuotation::with('detail')->where('id_quotation', $quote->id)->get() : collect();
        $format = request('format', $delivery->type);

        return view("pages.accounting.delivery.manual.detail-print", compact('subQuote', 'delivery', 'dDelivery', 'invoice', 'quote', 'format'));
    }

    public function print_delivery($id)
    {
        $delivery  = Delivery::findOrFail($id);
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

        // Manual delivery or standalone delivery
        if ($delivery->code == 'Manual' || !$delivery->id_invoice) {
            return $this->print_delivery_manual($id);
        }

        $invoice  = Invoice::find($delivery->id_invoice);
        if (!$invoice || !$invoice->id_quotation) {
            return $this->print_delivery_manual($id);
        }

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
