<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Delivery;
use App\Models\DetailDelivery;
use App\Models\DetailQuotation;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SerialProduct;
use App\Models\SubtitleQuotation;
use App\Models\Suo;
use App\Models\SuoDetail;
use App\Models\Unit;
use App\Models\UnitQuotation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuoController extends Controller
{
    // ─── Sales ───────────────────────────────────────────────────────────────

    public function index()
    {
        $pendingCount = Suo::where('id_sales', Auth::id())
            ->where('status', 'goods_out')
            ->count();

        return view('pages.suo.index', compact('pendingCount'));
    }

    public function create()
    {
        $noSuo = $this->generateNoSuo(Auth::id());

        $role = Auth::user()->role;
        if ($role === 'Sales') {
            $clients = Client::where('id_sales', Auth::id())
                ->where('role', 'Customers')
                ->orderBy('company')
                ->get(['id', 'company', 'address', 'subAddress']);
        } else {
            $clients = Client::where('role', 'Customers')
                ->orderBy('company')
                ->get(['id', 'company', 'address', 'subAddress']);
        }

        return view('pages.suo.create', compact('noSuo', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company'  => 'required|string|max:255',
            'pic'      => 'required|string|max:255',
            'address'  => 'required|string',
            'item_name' => 'required|array|min:1',
            'qty'      => 'required|array|min:1',
        ]);

        $suo = new Suo();
        $suo->no_suo    = $request->no_suo;
        $suo->company   = $request->company;
        $suo->pic       = $request->pic;
        $suo->address   = $request->address;
        $suo->notes     = $request->notes;
        $suo->id_sales  = Auth::id();
        $suo->status    = 'submitted';
        $suo->save();

        foreach ($request->item_name as $i => $name) {
            if (empty($name)) continue;
            $detail = new SuoDetail();
            $detail->id_suo    = $suo->id;
            $detail->item_name = $name;
            $detail->qty       = $request->qty[$i] ?? 1;
            $detail->unit      = $request->unit[$i] ?? null;
            $detail->notes     = $request->item_notes[$i] ?? null;
            $detail->save();
        }

        return redirect()->route('suo.index')->with('success', 'SUO berhasil dibuat dan dikirim ke Logistic.');
    }

    public function show($id)
    {
        $this->syncSuoInvoiceStatus();
        $suo    = Suo::with(['detail', 'sales', 'confirmedBy', 'approvedBy', 'deliveries'])->findOrFail($id);
        $role   = Auth::user()->role;
        $client = Client::where('company', $suo->company)->first();

        $quotation       = null;
        $unitQuotation   = null;
        $quotationDetail = collect();
        $invoice         = null;
        if ($suo->id_unit_quotation) {
            $unitQuotation = UnitQuotation::with(['details.unit', 'details.equivalent.product'])->find($suo->id_unit_quotation);
            if ($unitQuotation) {
                $quotationDetail = $unitQuotation->details
                    ->reject(fn($d) => in_array($d->type, ['header', 'heading']))
                    ->map(function ($item) {
                        $brand = null;
                        $partNumber = null;
                        $detailProduct = $item->label;

                        if ($item->type === 'unit' && $item->unit) {
                            $brand = $item->unit->brand;
                            $partNumber = $item->unit->model ?: $item->unit->sku;
                            $detailProduct = $item->label ?: ($item->unit->brand . ' ' . $item->unit->model);
                        } elseif ($item->equivalent) {
                            $brand = $item->equivalent->brand ?: null;
                            $partNumber = $item->equivalent->pn ?: null;
                            $prodDesc = optional($item->equivalent->product)->description;
                            $detailProduct = $item->label ?: ($prodDesc ?: trim(($brand ?? '') . ' ' . ($partNumber ?? '')));
                        }

                        return (object) [
                            'detail_product' => $detailProduct,
                            'brand'          => $brand,
                            'part_number'    => $partNumber,
                            'qty'            => $item->qty,
                            'info_qty'       => $item->info_qty ?? 'Unit',
                        ];
                    })
                    ->values();
                $invoice = Invoice::where('id_unit_quotation', $suo->id_unit_quotation)->first();
            }
        } elseif ($suo->id_quotation) {
            $quotation = Quotation::find($suo->id_quotation);

            if ($quotation && $quotation->type === 'Service') {
                $quotationDetail = SubtitleQuotation::with('detail')
                    ->where('id_quotation', $suo->id_quotation)
                    ->get()
                    ->flatMap(fn($subtitle) => $subtitle->detail)
                    ->map(fn($item) => (object) [
                        'detail_product' => $item->product ?: $item->detail,
                        'brand'          => null,
                        'part_number'    => null,
                        'qty'            => $item->qty,
                        'info_qty'       => $item->info_qty,
                    ])
                    ->values();
            } else {
                $quotationDetail = DetailQuotation::with('equivalent.product')
                    ->where('id_quotation', $suo->id_quotation)
                    ->get()
                    ->map(function ($item) {
                        $brand = null;
                        $partNumber = null;
                        if ($item->equivalent) {
                            $brand = $item->equivalent->brand ?: null;
                            $partNumber = $item->equivalent->pn ?: null;
                        }
                        return (object) [
                            'detail_product' => $item->detail_product,
                            'brand'          => $brand,
                            'part_number'    => $partNumber,
                            'qty'            => $item->qty,
                            'info_qty'       => $item->info_qty,
                        ];
                    });
            }

            $invoice = Invoice::where('id_quotation', $suo->id_quotation)->first();
        }

        // Sinkronkan brand, part_number, stok sistem / gudang, serta tentukan status draft otomatis
        $this->enrichSuoDetailWithStock($suo, $unitQuotation, $quotation);

        return view('pages.suo.detail', compact('suo', 'role', 'client', 'quotation', 'unitQuotation', 'quotationDetail', 'invoice'));
    }

    // ─── Logistic ────────────────────────────────────────────────────────────

    public function logisticIndex()
    {
        return view('pages.suo.logistic-index');
    }

    public function checkStock(Request $request, $id)
    {
        $suo = Suo::findOrFail($id);

        foreach ($request->stock_status as $detailId => $status) {
            SuoDetail::where('id', $detailId)->update(['stock_status' => $status]);
        }

        $suo->status       = 'confirmed';
        $suo->confirmed_by = Auth::id();
        $suo->confirmed_at = Carbon::now();
        $suo->save();

        return redirect()->route('suo.logistic.index')->with('success', 'Cek stok selesai, SUO diteruskan ke Accounting.');
    }

    // ─── Accounting ──────────────────────────────────────────────────────────

    public function accountingIndex()
    {
        return view('pages.suo.accounting-index');
    }

    public function suggestBookingNumber($id)
    {
        $suo = Suo::find($id);
        $context = $this->getBookingInvoiceContext($suo);
        return response()->json([
            'suggested' => $context['suggested'],
            'last'      => $context['last'],
            'entity'    => $context['entity'],
        ]);
    }

    public function approve(Request $request, $id)
    {
        $suo = Suo::findOrFail($id);

        $noInvoice = $request->no_invoice_booking ?: $this->generateBookingInvoiceNumber($suo);
        $suo->no_invoice_booking = $noInvoice;
        $suo->approved_by  = Auth::id();
        $suo->approved_at  = Carbon::now();

        // Jika SUO sudah terhubung ke Smart Quote
        if ($suo->id_unit_quotation) {
            $unitQuote = UnitQuotation::find($suo->id_unit_quotation);
            if ($unitQuote) {
                $pendingInvoice = Invoice::where('id_unit_quotation', $unitQuote->id)
                    ->whereNull('no_invoice')
                    ->first();

                if ($pendingInvoice) {
                    $pendingInvoice->no_invoice = $noInvoice;
                    $pendingInvoice->term       = 'Cash Before Delivery';
                    $pendingInvoice->invoiceTo  = '1';
                    $pendingInvoice->date       = now()->toDateString();

                    $amount = $unitQuote->total;
                    if ($pendingInvoice->flag === 'Reftech') {
                        $pendingInvoice->sign = $amount >= 5000000
                            ? 'asset/sign/reftech-m.jpeg'
                            : 'asset/sign/reftech-nm.jpeg';
                    } else {
                        $pendingInvoice->sign = $amount >= 5000000
                            ? 'asset/sign/kojisha-m.jpeg'
                            : 'asset/sign/kojisha-nm.jpeg';
                    }
                    $pendingInvoice->save();

                    $suo->status = 'converted';

                    if ($unitQuote->id_sales) {
                        \App\Models\UnitQuotationPaymentNotification::create([
                            'id_invoice' => $pendingInvoice->id,
                            'id_unit_quotation' => $unitQuote->id,
                            'id_user' => $unitQuote->id_sales,
                            'type' => 'invoice_approved',
                            'is_read' => false,
                        ]);
                    }
                } else {
                    $suo->status = 'confirmed';
                }
            } else {
                $suo->status = 'confirmed';
            }
        } elseif ($suo->id_quotation) {
            $quote = Quotation::find($suo->id_quotation);
            if ($quote) {
                $pendingInvoice = Invoice::where('id_quotation', $quote->id)
                    ->whereNull('no_invoice')
                    ->first();
                if ($pendingInvoice) {
                    $pendingInvoice->no_invoice = $noInvoice;
                    $pendingInvoice->term       = 'Cash Before Delivery';
                    $pendingInvoice->invoiceTo  = $quote->destination;
                    $pendingInvoice->date       = now()->toDateString();
                    $pendingInvoice->save();

                    $suo->status = 'converted';
                } else {
                    $suo->status = 'confirmed';
                }
            } else {
                $suo->status = 'confirmed';
            }
        } else {
            $suo->status = 'confirmed';
        }

        $suo->save();

        return response()->json(['success' => true, 'no_invoice' => $noInvoice]);
    }

    public function storeDelivery(Request $request, $id)
    {
        $suo = Suo::with('detail')->findOrFail($id);

        $delivery = new Delivery();
        $delivery->id_suo            = $suo->id;
        $delivery->id_invoice        = null;
        $delivery->id_unit_quotation = $suo->id_unit_quotation ?: null;
        $delivery->date              = $request->date ?? Carbon::today()->toDateString();
        $delivery->destination       = $request->destination;
        $delivery->type              = $request->type ?? 'Ekspedisi';
        $delivery->code              = 'Sparepart';
        $delivery->save();

        foreach ($suo->detail as $item) {
            $dDelivery = new DetailDelivery();
            $dDelivery->id_delivery = $delivery->id;
            $dDelivery->id_pn       = null;
            $dDelivery->desc        = $item->item_name;
            $dDelivery->qty         = $item->qty;
            $dDelivery->info_qty    = $item->unit;
            $dDelivery->save();
        }

        $suo->status = 'goods_out';
        $suo->save();

        return redirect()->route('suo.show', $suo->id)->with('success', 'Surat Jalan berhasil dibuat, barang sudah keluar.');
    }

    // ─── Convert SUO → Quotation (Sales) ─────────────────────────────────────

    public function convert($id)
    {
        $suo = Suo::with('detail')->findOrFail($id);

        // Pass SUO data to quotation create form via session
        session(['suo_convert' => [
            'id_suo'    => $suo->id,
            'no_suo'    => $suo->no_suo,
            'company'   => $suo->company,
            'pic'       => $suo->pic,
            'address'   => $suo->address,
            'items'     => $suo->detail->map(fn($d) => [
                'item_name' => $d->item_name,
                'qty'       => $d->qty,
                'unit'      => $d->unit,
            ])->toArray(),
        ]]);

        return redirect()->route('create.quotation');
    }

    public function markConverted(Request $request, $id)
    {
        $suo = Suo::findOrFail($id);
        $suo->status        = 'converted';
        $suo->id_quotation  = $request->id_quotation;
        $suo->save();

        return response()->json(['success' => true]);
    }

    // ─── Link SUO → Penawaran yang sudah ada (Sales) ─────────────────────────

    public function linkableQuotations($id)
    {
        $suo = Suo::findOrFail($id);

        $linkedQuotationIds     = Suo::whereNotNull('id_quotation')->pluck('id_quotation');
        $linkedUnitQuotationIds = Suo::whereNotNull('id_unit_quotation')->pluck('id_unit_quotation');

        $quotations = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->where('quotation.id_sales', $suo->id_sales)
            ->where('quotation.level', '1')
            ->where('quotation.is_primary', '1')
            ->whereNotIn('quotation.id', $linkedQuotationIds)
            ->get(['quotation.id', 'quotation.no_quote', 'quotation.title', 'quotation.created_at', 'client.company'])
            ->map(function ($q) {
                $q->source = 'quotation';
                return $q;
            });

        $unitQuotations = UnitQuotation::join('client', 'client.id', '=', 'unit_quotation.id_client')
            ->where('unit_quotation.id_sales', $suo->id_sales)
            ->where('unit_quotation.is_latest', '1')
            ->whereNotIn('unit_quotation.id', $linkedUnitQuotationIds)
            ->get(['unit_quotation.id', 'unit_quotation.no_quote', 'unit_quotation.title', 'unit_quotation.created_at', 'client.company'])
            ->map(function ($q) {
                $q->source = 'unit_quotation';
                return $q;
            });

        $data = $quotations->concat($unitQuotations)->sortByDesc('created_at')->values();

        return response()->json(['data' => $data]);
    }

    public function linkQuotation(Request $request, $id)
    {
        $request->validate([
            'id_quotation' => 'required|integer',
            'source'       => 'required|in:quotation,unit_quotation',
        ]);

        $suo = Suo::findOrFail($id);

        if ($request->source === 'unit_quotation') {
            $unitQuote = UnitQuotation::findOrFail($request->id_quotation);

            $alreadyLinked = Suo::whereNotNull('id_unit_quotation')
                ->where('id_unit_quotation', $request->id_quotation)
                ->where('id', '!=', $suo->id)
                ->exists();

            if ($alreadyLinked) {
                return response()->json(['success' => false, 'message' => 'Smart Quote ini sudah dihubungkan ke SUO lain.'], 422);
            }

            $hasIssuedInvoice = Invoice::where('id_unit_quotation', $unitQuote->id)
                ->whereNotNull('no_invoice')
                ->where('no_invoice', '!=', '')
                ->exists();

            if ($hasIssuedInvoice) {
                $suo->status = 'converted';
            }
            $suo->id_unit_quotation = $request->id_quotation;
            $suo->id_quotation      = null;

            if ($suo->no_invoice_booking) {
                $pendingInvoice = Invoice::where('id_unit_quotation', $unitQuote->id)
                    ->whereNull('no_invoice')
                    ->first();

                if ($pendingInvoice) {
                    $pendingInvoice->no_invoice = $suo->no_invoice_booking;
                    $pendingInvoice->term       = 'Cash Before Delivery';
                    $pendingInvoice->invoiceTo  = '1';
                    $pendingInvoice->date       = now()->toDateString();

                    $amount = $unitQuote->total;
                    if ($pendingInvoice->flag === 'Reftech') {
                        $pendingInvoice->sign = $amount >= 5000000
                            ? 'asset/sign/reftech-m.jpeg'
                            : 'asset/sign/reftech-nm.jpeg';
                    } else {
                        $pendingInvoice->sign = $amount >= 5000000
                            ? 'asset/sign/kojisha-m.jpeg'
                            : 'asset/sign/kojisha-nm.jpeg';
                    }
                    $pendingInvoice->save();

                    if ($unitQuote->id_sales) {
                        \App\Models\UnitQuotationPaymentNotification::create([
                            'id_invoice' => $pendingInvoice->id,
                            'id_unit_quotation' => $unitQuote->id,
                            'id_user' => $unitQuote->id_sales,
                            'type' => 'invoice_approved',
                            'is_read' => false,
                        ]);
                    }
                }
            }
        } else {
            $quote = Quotation::findOrFail($request->id_quotation);

            $alreadyLinked = Suo::whereNotNull('id_quotation')
                ->where('id_quotation', $request->id_quotation)
                ->where('id', '!=', $suo->id)
                ->exists();

            if ($alreadyLinked) {
                return response()->json(['success' => false, 'message' => 'Penawaran ini sudah dihubungkan ke SUO lain.'], 422);
            }

            $hasIssuedInvoice = Invoice::where('id_quotation', $quote->id)
                ->whereNotNull('no_invoice')
                ->where('no_invoice', '!=', '')
                ->exists();

            if ($hasIssuedInvoice) {
                $suo->status = 'converted';
            }
            $suo->id_quotation      = $request->id_quotation;
            $suo->id_unit_quotation = null;

            if ($suo->no_invoice_booking) {
                $pendingInvoice = Invoice::where('id_quotation', $quote->id)
                    ->whereNull('no_invoice')
                    ->first();

                if ($pendingInvoice) {
                    $pendingInvoice->no_invoice = $suo->no_invoice_booking;
                    $pendingInvoice->term       = 'Cash Before Delivery';
                    $pendingInvoice->invoiceTo  = $quote->destination;
                    $pendingInvoice->date       = now()->toDateString();
                    $pendingInvoice->save();
                }
            }
        }

        $suo->save();

        return response()->json(['success' => true]);
    }

    // ─── Ajukan SUO langsung dari Quotation (Sales) ──────────────────────────

    public function storeFromQuotation($quotationId)
    {
        $quotation = Quotation::with('pic.client')->findOrFail($quotationId);

        if (!in_array($quotation->type, ['Sparepart', 'Service'])) {
            return response()->json(['success' => false, 'message' => 'Type penawaran ini tidak didukung untuk SUO.'], 422);
        }

        if (Suo::where('id_quotation', $quotation->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Penawaran ini sudah punya SUO.'], 422);
        }

        if ($quotation->type === 'Sparepart') {
            $items = DetailQuotation::where('id_quotation', $quotation->id)->get()->map(fn($item) => [
                'item_name' => $item->detail_product,
                'qty'       => $item->qty,
                'unit'      => $item->info_qty,
            ]);
        } else {
            $items = SubtitleQuotation::with('detail')
                ->where('id_quotation', $quotation->id)
                ->get()
                ->flatMap(fn($subtitle) => $subtitle->detail)
                ->map(fn($item) => [
                    'item_name' => $item->product ?: $item->detail,
                    'qty'       => $item->qty,
                    'unit'      => $item->info_qty,
                ]);
        }

        if ($items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Penawaran ini belum punya item.'], 422);
        }

        $client = $quotation->pic->client ?? null;

        $hasIssuedInvoice = Invoice::where('id_quotation', $quotation->id)
            ->whereNotNull('no_invoice')
            ->where('no_invoice', '!=', '')
            ->exists();

        $suo = new Suo();
        $suo->no_suo       = $this->generateNoSuo($quotation->id_sales);
        $suo->company      = $client->company ?? '-';
        $suo->pic          = $quotation->pic->name_pic ?? '-';
        $suo->address      = $client->address ?? '-';
        $suo->notes        = 'Diajukan otomatis dari Penawaran ' . $quotation->no_quote;
        $suo->id_sales     = $quotation->id_sales;
        $suo->status       = $hasIssuedInvoice ? 'converted' : 'submitted';
        $suo->id_quotation = $quotation->id;
        $suo->save();

        foreach ($items as $item) {
            $detail = new SuoDetail();
            $detail->id_suo    = $suo->id;
            $detail->item_name = $item['item_name'];
            $detail->qty       = $item['qty'] ?: 1;
            $detail->unit      = $item['unit'];
            $detail->save();
        }

        return response()->json(['success' => true, 'suo_id' => $suo->id]);
    }

    public function storeFromUnitQuotation($unitQuotationId)
    {
        $quotation = UnitQuotation::with(['client', 'pic', 'details.unit', 'details.equivalent.product'])->findOrFail($unitQuotationId);

        if (Suo::where('id_unit_quotation', $quotation->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Penawaran unit ini sudah punya SUO.'], 422);
        }

        $items = $quotation->details
            ->reject(fn($d) => in_array($d->type, ['header', 'heading']))
            ->map(fn($item) => [
                'item_name' => $item->type === 'unit' && $item->unit
                    ? ($item->label ?: ($item->unit->brand . ' ' . $item->unit->model))
                    : $item->label,
                'qty'       => $item->qty,
                'unit'      => $item->info_qty ?? 'Unit',
            ]);

        if ($items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Penawaran unit ini belum punya item.'], 422);
        }

        $client = $quotation->client;

        $hasIssuedInvoice = Invoice::where('id_unit_quotation', $quotation->id)
            ->whereNotNull('no_invoice')
            ->where('no_invoice', '!=', '')
            ->exists();

        $suo = new Suo();
        $suo->no_suo            = $this->generateNoSuo($quotation->id_sales);
        $suo->company           = $client->company ?? '-';
        $suo->pic               = $quotation->attn ?: ($quotation->pic?->name_pic ?? '-');
        $suo->address           = $quotation->address ?: ($client->address ?? '-');
        $suo->notes             = 'Diajukan otomatis dari Penawaran Unit ' . $quotation->no_quote;
        $suo->id_sales          = $quotation->id_sales;
        $suo->status            = $hasIssuedInvoice ? 'converted' : 'submitted';
        $suo->id_unit_quotation = $quotation->id;
        $suo->save();

        foreach ($items as $item) {
            $detail = new SuoDetail();
            $detail->id_suo    = $suo->id;
            $detail->item_name = $item['item_name'];
            $detail->qty       = $item['qty'] ?: 1;
            $detail->unit      = $item['unit'];
            $detail->save();
        }

        return response()->json(['success' => true, 'suo_id' => $suo->id]);
    }

    // ─── AJAX data endpoints ──────────────────────────────────────────────────

    public function dataSales()
    {
        $this->syncSuoInvoiceStatus();

        $isManager = in_array(Auth::user()->role, ['Admin', 'Sales Manager']);

        $query = Suo::query()->with('sales:id,name,image,code');

        // Sama seperti UnitQuotationController: Admin & Sales Manager lihat SUO
        // seluruh tim, sales biasa cuma lihat punya sendiri.
        if (!$isManager) {
            $query->where('id_sales', Auth::id());
        }

        $data = $query->orderByDesc('created_at')
            ->get(['id', 'no_suo', 'company', 'pic', 'status', 'no_invoice_booking', 'created_at', 'id_sales', 'id_unit_quotation', 'id_quotation'])
            ->map(function ($suo) {
                $suo->sales_name  = $suo->sales->name ?? '-';
                $suo->sales_image = $suo->sales->image ?? null;
                $suo->sales_code  = $suo->sales->code ?? null;

                $invoice = null;
                if ($suo->id_unit_quotation) {
                    $invoice = Invoice::where('id_unit_quotation', $suo->id_unit_quotation)->first();
                } elseif ($suo->id_quotation) {
                    $invoice = Invoice::where('id_quotation', $suo->id_quotation)->first();
                }
                if (!$invoice && $suo->no_invoice_booking) {
                    $invoice = Invoice::where('no_invoice', $suo->no_invoice_booking)->first();
                }

                if ($invoice) {
                    $suo->invoice_url = $invoice->id_unit_quotation
                        ? route('invoice.show_unit', $invoice->id)
                        : route('invoice.show', $invoice->id);
                    if (!$suo->no_invoice_booking && $invoice->no_invoice) {
                        $suo->no_invoice_booking = $invoice->no_invoice;
                    }
                } else {
                    $suo->invoice_url = null;
                }

                return $suo;
            });

        return response()->json(['data' => $data]);
    }

    public function dataLogistic()
    {
        $this->syncSuoInvoiceStatus();

        $data = Suo::whereIn('status', ['submitted', 'confirmed', 'goods_out', 'converted'])
            ->orderByDesc('created_at')
            ->get(['id', 'no_suo', 'company', 'pic', 'status', 'created_at']);

        return response()->json(['data' => $data]);
    }

    public function dataAccounting()
    {
        $this->syncSuoInvoiceStatus();

        $data = Suo::with('sales:id,name,image,code')
            ->orderByDesc('created_at')
            ->get(['id', 'no_suo', 'company', 'pic', 'status', 'no_invoice_booking', 'created_at', 'id_sales', 'id_unit_quotation', 'id_quotation'])
            ->map(function ($suo) {
                $suo->sales_name  = $suo->sales->name ?? '-';
                $suo->sales_image = $suo->sales->image ?? null;
                $suo->sales_code  = $suo->sales->code ?? null;

                $invoice = null;
                if ($suo->id_unit_quotation) {
                    $invoice = Invoice::where('id_unit_quotation', $suo->id_unit_quotation)->first();
                } elseif ($suo->id_quotation) {
                    $invoice = Invoice::where('id_quotation', $suo->id_quotation)->first();
                }
                if (!$invoice && $suo->no_invoice_booking) {
                    $invoice = Invoice::where('no_invoice', $suo->no_invoice_booking)->first();
                }

                if ($invoice) {
                    $suo->invoice_url = $invoice->id_unit_quotation
                        ? route('invoice.show_unit', $invoice->id)
                        : route('invoice.show', $invoice->id);
                    if (!$suo->no_invoice_booking && $invoice->no_invoice) {
                        $suo->no_invoice_booking = $invoice->no_invoice;
                    }
                } else {
                    $suo->invoice_url = null;
                }

                return $suo;
            });

        return response()->json(['data' => $data]);
    }

    /**
     * Endpoint polling alert darurat (Urgent Order SUO) untuk Gudang dan Accounting.
     * Mengembalikan data SUO jika ada order mendesak yang butuh aksi segera sesuai workflow:
     * - Role Logistic: SUO dengan status 'submitted' (cek ketersediaan stok).
     * - Role Accounting: SUO dengan status 'confirmed' tanpa no_invoice_booking,
     *   HANYA jika sales pembuat SUO tersebut merupakan bagian dari handling accounting yang login.
     */
    public function urgentCheck()
    {
        if (!Auth::check()) {
            return response()->json(['has_urgent' => false]);
        }

        $user = Auth::user();
        $role = $user->role;
        $userId = $user->id;

        // 1. Gudang (Logistic) atau Admin/Developer:
        // Cek SUO yang baru diajukan (status: 'submitted') dan butuh cek stok gudang segera
        if (in_array($role, ['Logistic', 'Admin', 'Developer'])) {
            $suoSubmitted = Suo::with(['sales:id,name,image,code', 'detail'])
                ->where('status', 'submitted')
                ->latest('id')
                ->first();

            if ($suoSubmitted) {
                $itemsList = $suoSubmitted->detail->map(function ($d) {
                    return [
                        'item_name' => $d->item_name,
                        'qty'       => $d->qty,
                        'unit'      => $d->unit ?: 'Unit',
                    ];
                })->values();

                $itemsSummary = $suoSubmitted->detail->map(function ($d) {
                    return $d->item_name . ' (' . $d->qty . ' ' . ($d->unit ?: 'Unit') . ')';
                })->take(3)->implode(', ');

                if ($suoSubmitted->detail->count() > 3) {
                    $itemsSummary .= ' +' . ($suoSubmitted->detail->count() - 3) . ' lainnya';
                }

                $timeAgo = 'Baru saja';
                if ($suoSubmitted->created_at instanceof \Carbon\Carbon) {
                    $timeAgo = $suoSubmitted->created_at->diffForHumans();
                }

                return response()->json([
                    'has_urgent' => true,
                    'suo' => [
                        'id'           => $suoSubmitted->id,
                        'no_suo'       => $suoSubmitted->no_suo,
                        'company'      => $suoSubmitted->company,
                        'pic'          => $suoSubmitted->pic,
                        'sales_name'   => $suoSubmitted->sales->name ?? 'Sales',
                        'sales_code'   => $suoSubmitted->sales->code ?? null,
                        'sales_image'  => $suoSubmitted->sales && $suoSubmitted->sales->image ? url($suoSubmitted->sales->image) : null,
                        'items'        => $itemsSummary ?: 'Lihat detail barang di formulir SUO',
                        'items_list'   => $itemsList,
                        'items_count'  => $suoSubmitted->detail->count(),
                        'stage'        => 'gudang_check_stock',
                        'stage_badge'  => 'Gudang • Cek Stok',
                        'stage_title'  => 'Pengajuan Urgent Order Baru',
                        'stage_desc'   => 'Sales telah mengajukan Urgent Order (SUO). Gudang wajib segera memeriksa ketersediaan stok fisik barang.',
                        'action_url'   => route('suo.show', $suoSubmitted->id),
                        'action_label' => 'Buka & Cek Stok',
                        'created_at'   => $timeAgo,
                    ]
                ]);
            }
        }

        // 2. Accounting:
        // Cek SUO yang stoknya sudah dikonfirmasi gudang (status: 'confirmed') dan belum punya nomor invoice booking
        // HANYA untuk accounting yang menangani sales pembuat SUO tersebut (atau Admin/Developer)
        if (in_array($role, ['Accounting', 'Admin', 'Developer'])) {
            $suoConfirmedList = Suo::with(['sales:id,name,image,code', 'detail'])
                ->where('status', 'confirmed')
                ->whereNull('no_invoice_booking')
                ->latest('id')
                ->get();

            foreach ($suoConfirmedList as $suo) {
                $allowedAccountingIds = User::getAccountingRecipientsForSales($suo->id_sales, false);

                // Jika user adalah Accounting, pastikan id user ada dalam daftar handling sales terkait
                // Jika Admin/Developer, selalu izinkan
                $isAllowed = in_array($role, ['Admin', 'Developer']) || in_array($userId, $allowedAccountingIds);

                if ($isAllowed) {
                    $itemsList = $suo->detail->map(function ($d) {
                        return [
                            'item_name' => $d->item_name,
                            'qty'       => $d->qty,
                            'unit'      => $d->unit ?: 'Unit',
                        ];
                    })->values();

                    $itemsSummary = $suo->detail->map(function ($d) {
                        return $d->item_name . ' (' . $d->qty . ' ' . ($d->unit ?: 'Unit') . ')';
                    })->take(3)->implode(', ');

                    if ($suo->detail->count() > 3) {
                        $itemsSummary .= ' +' . ($suo->detail->count() - 3) . ' lainnya';
                    }

                    $timeAgo = 'Baru saja';
                    if ($suo->confirmed_at instanceof \Carbon\Carbon) {
                        $timeAgo = $suo->confirmed_at->diffForHumans();
                    } elseif ($suo->updated_at instanceof \Carbon\Carbon) {
                        $timeAgo = $suo->updated_at->diffForHumans();
                    }

                    return response()->json([
                        'has_urgent' => true,
                        'suo' => [
                            'id'           => $suo->id,
                            'no_suo'       => $suo->no_suo,
                            'company'      => $suo->company,
                            'pic'          => $suo->pic,
                            'sales_name'   => $suo->sales->name ?? 'Sales',
                            'sales_code'   => $suo->sales->code ?? null,
                            'sales_image'  => $suo->sales && $suo->sales->image ? url($suo->sales->image) : null,
                            'items'        => $itemsSummary ?: 'Lihat detail barang di formulir SUO',
                            'items_list'   => $itemsList,
                            'items_count'  => $suo->detail->count(),
                            'stage'        => 'accounting_booking_invoice',
                            'stage_badge'  => 'Accounting • Booking Invoice',
                            'stage_title'  => 'Stok Siap • Butuh Booking Invoice',
                            'stage_desc'   => 'Gudang telah mengonfirmasi fisik stok SUO dari ' . ($suo->sales->name ?? 'Sales') . '. Segera input nomor booking invoice agar barang dapat dikirim.',
                            'action_url'   => route('suo.show', $suo->id),
                            'action_label' => 'Buka & Booking Invoice',
                            'created_at'   => $timeAgo,
                        ]
                    ]);
                }
            }
        }

        return response()->json(['has_urgent' => false]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function syncSuoInvoiceStatus()
    {
        $unconvertedSuos = Suo::where('status', '!=', 'converted')
            ->where(function ($q) {
                $q->whereNotNull('id_unit_quotation')
                  ->orWhereNotNull('id_quotation');
            })
            ->get();

        foreach ($unconvertedSuos as $suo) {
            $hasIssued = false;
            if ($suo->id_unit_quotation) {
                $hasIssued = Invoice::where('id_unit_quotation', $suo->id_unit_quotation)
                    ->whereNotNull('no_invoice')
                    ->where('no_invoice', '!=', '')
                    ->exists();
            } elseif ($suo->id_quotation) {
                $hasIssued = Invoice::where('id_quotation', $suo->id_quotation)
                    ->whereNotNull('no_invoice')
                    ->where('no_invoice', '!=', '')
                    ->exists();
            }

            if ($hasIssued) {
                $suo->status = 'converted';
                $suo->save();
            }
        }
    }

    private function generateNoSuo(?int $salesId = null): string
    {
        $year = Carbon::now()->year;
        $sales = $salesId ? User::find($salesId) : Auth::user();
        $userCode = $sales?->code ?: ($sales ? strtoupper(substr($sales->name, 0, 3)) : 'SLS');

        $existing = Suo::where('no_suo', 'LIKE', "%-SUO/{$userCode}/{$year}")->pluck('no_suo');

        $maxSeq = 0;
        foreach ($existing as $no) {
            if (preg_match('/^(\d+)-SUO\//', $no, $matches)) {
                $seq = (int) $matches[1];
                if ($seq > $maxSeq) {
                    $maxSeq = $seq;
                }
            }
        }

        $nextSeq = str_pad($maxSeq + 1, 3, '0', STR_PAD_LEFT);
        return "{$nextSeq}-SUO/{$userCode}/{$year}";
    }

    private function getBookingInvoiceContext($suo = null)
    {
        $romans = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        $now    = Carbon::now();
        $roman  = $romans[$now->month - 1];
        $year   = $now->year;

        // Tentukan entity code (RJO / KII) dan tax code (SJ-P / SJ-NP)
        $isKojisha = false;
        $isTax = true;

        if ($suo) {
            if ($suo->id_unit_quotation) {
                $quote = UnitQuotation::with('client')->find($suo->id_unit_quotation);
                if ($quote) {
                    $isKojisha = optional($quote->client)->info === 'Kojisha'
                        || str_contains((string) $quote->no_quote, 'KII');
                    $isTax = (bool) $quote->tax;
                }
            } elseif ($suo->id_quotation) {
                $quote = Quotation::with('client')->find($suo->id_quotation);
                if ($quote) {
                    $isKojisha = optional($quote->client)->info === 'Kojisha'
                        || str_contains((string) $quote->no_quote, 'KII');
                    $isTax = (bool) $quote->tax;
                }
            }
        }

        $entityCode = $isKojisha ? 'KII' : 'RJO';
        $sjCode     = $isTax ? 'SJ-P' : 'SJ-NP';

        // 1. Ambil invoice dari tabel invoices untuk entitas dan tahun bersangkutan
        $invoices = Invoice::where(function ($q) use ($entityCode, $year) {
                $q->where('no_invoice', 'LIKE', "%/{$entityCode}/%/{$year}")
                  ->orWhere('no_invoice', 'LIKE', "%/{$entityCode}-%/{$year}");
            })
            ->get(['id', 'no_invoice']);

        $maxInvoiceSeq = 0;
        $lastInvoiceNo = null;
        foreach ($invoices as $inv) {
            if (preg_match('/^(\d+)\//', $inv->no_invoice, $m)) {
                $seq = (int) $m[1];
                if ($seq > $maxInvoiceSeq) {
                    $maxInvoiceSeq = $seq;
                    $lastInvoiceNo = $inv->no_invoice;
                }
            }
        }

        // 2. Ambil booking dari tabel suo untuk entitas dan tahun bersangkutan
        $suos = Suo::whereNotNull('no_invoice_booking')
            ->where(function ($q) use ($entityCode, $year) {
                $q->where('no_invoice_booking', 'LIKE', "%/{$entityCode}/%/{$year}")
                  ->orWhere('no_invoice_booking', 'LIKE', "%/{$entityCode}-%/{$year}");
            })
            ->get(['id', 'no_invoice_booking']);

        $maxSuoSeq = 0;
        $lastSuoNo = null;
        foreach ($suos as $s) {
            if (preg_match('/^(\d+)\//', $s->no_invoice_booking, $m)) {
                $seq = (int) $m[1];
                if ($seq > $maxSuoSeq) {
                    $maxSuoSeq = $seq;
                    $lastSuoNo = $s->no_invoice_booking;
                }
            }
        }

        $effectiveLastSeq = max($maxInvoiceSeq, $maxSuoSeq);
        $lastNo = ($maxInvoiceSeq >= $maxSuoSeq) ? $lastInvoiceNo : $lastSuoNo;

        $nextSeq = $effectiveLastSeq + 1;
        $formattedNextSeq = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
        $suggested = "{$formattedNextSeq}/{$sjCode}/{$entityCode}/{$roman}/{$year}";

        return [
            'suggested' => $suggested,
            'last'      => $lastNo,
            'entity'    => $entityCode,
            'sj_code'   => $sjCode,
        ];
    }

    private function generateBookingInvoiceNumber($suo = null)
    {
        return $this->getBookingInvoiceContext($suo)['suggested'];
    }

    /**
     * Sinkronkan item SUO dengan data brand, part number, stok sistem / gudang,
     * serta tentukan saran status stok otomatis (auto draft).
     */
    private function enrichSuoDetailWithStock($suo, $unitQuotation = null, $quotation = null)
    {
        $uqItems = $unitQuotation
            ? $unitQuotation->details->reject(fn($d) => in_array($d->type, ['header', 'heading']))->values()
            : collect();

        $qItems = ($quotation && $quotation->type === 'Sparepart')
            ? DetailQuotation::with('equivalent.product')->where('id_quotation', $quotation->id)->get()
            : collect();

        foreach ($suo->detail as $idx => $sDetail) {
            $sDetail->brand = $sDetail->brand ?? null;
            $sDetail->part_number = $sDetail->part_number ?? null;
            $sDetail->available_stock = null;
            $sDetail->stock_unit = $sDetail->unit ?: 'Unit';

            // 1. Cek dari Unit Quotation (Smart Quote)
            if ($unitQuotation) {
                $matched = $uqItems->first(function ($uItem) use ($sDetail) {
                    $uName = $uItem->label ?: optional($uItem->unit)->brand . ' ' . optional($uItem->unit)->model;
                    return strcasecmp(trim($uName), trim($sDetail->item_name)) === 0;
                }) ?: $uqItems->get($idx);

                if ($matched) {
                    if ($matched->type === 'unit' && $matched->unit) {
                        $sDetail->brand = $matched->unit->brand;
                        $sDetail->part_number = $matched->unit->model ?: $matched->unit->sku;
                        $sDetail->available_stock = (float) ($matched->unit->stock + $matched->unit->warehouse_stock);
                        $sDetail->stock_unit = $matched->unit->unit ?: $sDetail->stock_unit;
                    } elseif ($matched->equivalent) {
                        $sDetail->brand = $matched->equivalent->brand ?: null;
                        $sDetail->part_number = $matched->equivalent->pn ?: null;
                        if (!$sDetail->brand && optional($matched->equivalent->product)->commodity) {
                            $sDetail->part_number = optional($matched->equivalent->product)->commodity;
                        }
                        $prod = $matched->equivalent->product;
                        if ($prod) {
                            $sDetail->available_stock = (float) ($prod->stock + $prod->warehouse_stock);
                            $sDetail->stock_unit = $prod->unit ?: 'Pcs';
                        }
                    }
                }
            } elseif ($quotation && $quotation->type === 'Sparepart') {
                $matched = $qItems->first(function ($qItem) use ($sDetail) {
                    return strcasecmp(trim($qItem->detail_product), trim($sDetail->item_name)) === 0;
                }) ?: $qItems->get($idx);

                if ($matched && $matched->equivalent) {
                    $sDetail->brand = $matched->equivalent->brand ?: null;
                    $sDetail->part_number = $matched->equivalent->pn ?: null;
                    $prod = $matched->equivalent->product;
                    if ($prod) {
                        $sDetail->available_stock = (float) ($prod->stock + $prod->warehouse_stock);
                        $sDetail->stock_unit = $prod->unit ?: 'Pcs';
                    }
                }
            }

            // 2. Fallback pencarian stok berdasarkan part_number jika belum terdeteksi
            if ($sDetail->available_stock === null && !empty($sDetail->part_number)) {
                $unit = Unit::where('model', $sDetail->part_number)->orWhere('sku', $sDetail->part_number)->first();
                if ($unit) {
                    $sDetail->available_stock = (float) ($unit->stock + $unit->warehouse_stock);
                    $sDetail->stock_unit = $unit->unit ?: $sDetail->stock_unit;
                    if (!$sDetail->brand) $sDetail->brand = $unit->brand;
                } else {
                    $sp = SerialProduct::with('product')->where('pn', $sDetail->part_number)->first();
                    if ($sp && $sp->product) {
                        $sDetail->available_stock = (float) ($sp->product->stock + $sp->product->warehouse_stock);
                        $sDetail->stock_unit = $sp->product->unit ?: 'Pcs';
                        if (!$sDetail->brand) $sDetail->brand = $sp->brand;
                    } else {
                        $prod = Product::where('commodity', $sDetail->part_number)->first();
                        if ($prod) {
                            $sDetail->available_stock = (float) ($prod->stock + $prod->warehouse_stock);
                            $sDetail->stock_unit = $prod->unit ?: 'Pcs';
                        }
                    }
                }
            }

            // 3. Fallback pencarian stok berdasarkan deskripsi/nama item
            if ($sDetail->available_stock === null) {
                $prod = Product::where('description', $sDetail->item_name)->first();
                if ($prod) {
                    $sDetail->available_stock = (float) ($prod->stock + $prod->warehouse_stock);
                    $sDetail->stock_unit = $prod->unit ?: 'Pcs';
                }
            }

            if ($sDetail->available_stock === null) {
                $sDetail->available_stock = 0;
            }

            // Tentukan status otomatis (draft) berdasarkan kecukupan stok di sistem
            $sDetail->is_stock_sufficient = ($sDetail->available_stock >= $sDetail->qty);
            $sDetail->auto_draft_status = $sDetail->is_stock_sufficient ? 'ready' : 'not_ready';

            // Status efektif: gunakan yang sudah disimpan di DB jika ada, jika belum gunakan auto draft
            $sDetail->effective_stock_status = $sDetail->stock_status ?: $sDetail->auto_draft_status;
            $sDetail->is_draft_status = empty($sDetail->stock_status);
        }
    }
}
