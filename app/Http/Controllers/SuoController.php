<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Delivery;
use App\Models\DetailDelivery;
use App\Models\DetailQuotation;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\SubtitleQuotation;
use App\Models\Suo;
use App\Models\SuoDetail;
use App\Models\UnitQuotation;
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
        $noSuo = $this->generateNoSuo();

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
        $suo    = Suo::with(['detail', 'sales', 'confirmedBy', 'approvedBy'])->findOrFail($id);
        $role   = Auth::user()->role;
        $client = Client::where('company', $suo->company)->first();

        $quotation       = null;
        $quotationDetail = collect();
        $invoice         = null;
        if ($suo->id_unit_quotation) {
            $unitQuotation = UnitQuotation::with(['details.unit', 'details.equivalent.product'])->find($suo->id_unit_quotation);
            if ($unitQuotation) {
                $quotationDetail = $unitQuotation->details
                    ->reject(fn($d) => in_array($d->type, ['header', 'heading']))
                    ->map(fn($item) => (object) [
                        'detail_product' => $item->type === 'unit' && $item->unit
                            ? ($item->label ?: ($item->unit->brand . ' ' . $item->unit->model))
                            : $item->label,
                        'qty'      => $item->qty,
                        'info_qty' => $item->info_qty ?? 'Unit',
                    ])
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
                        'qty'            => $item->qty,
                        'info_qty'       => $item->info_qty,
                    ])
                    ->values();
            } else {
                $quotationDetail = DetailQuotation::where('id_quotation', $suo->id_quotation)->get();
            }

            $invoice = Invoice::where('id_quotation', $suo->id_quotation)->first();
        }

        return view('pages.suo.detail', compact('suo', 'role', 'client', 'quotation', 'quotationDetail', 'invoice'));
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
        $suggested = $this->generateBookingInvoiceNumber();
        $last = Suo::whereNotNull('no_invoice_booking')->orderByDesc('id')->value('no_invoice_booking');
        return response()->json(['suggested' => $suggested, 'last' => $last]);
    }

    public function approve(Request $request, $id)
    {
        $suo = Suo::findOrFail($id);

        $noInvoice = $request->no_invoice_booking ?: $this->generateBookingInvoiceNumber();
        $suo->no_invoice_booking = $noInvoice;
        $suo->status       = 'confirmed';
        $suo->approved_by  = Auth::id();
        $suo->approved_at  = Carbon::now();
        $suo->save();

        return response()->json(['success' => true, 'no_invoice' => $noInvoice]);
    }

    public function storeDelivery(Request $request, $id)
    {
        $suo = Suo::with('detail')->findOrFail($id);

        $delivery = new Delivery();
        $delivery->id_suo       = $suo->id;
        $delivery->id_invoice   = null;
        $delivery->date         = $request->date ?? Carbon::today()->toDateString();
        $delivery->destination  = $request->destination;
        $delivery->type         = $request->type ?? 'Ekspedisi';
        $delivery->code         = 'Sparepart';
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

        $linkedIds = Suo::whereNotNull('id_quotation')->pluck('id_quotation');

        $quotations = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->where('quotation.id_sales', $suo->id_sales)
            ->where('quotation.level', '1')
            ->where('quotation.is_primary', '1')
            ->whereNotIn('quotation.id', $linkedIds)
            ->orderByDesc('quotation.created_at')
            ->get(['quotation.id', 'quotation.no_quote', 'quotation.title', 'quotation.created_at', 'client.company']);

        return response()->json(['data' => $quotations]);
    }

    public function linkQuotation(Request $request, $id)
    {
        $request->validate(['id_quotation' => 'required|exists:quotation,id']);

        $suo = Suo::findOrFail($id);

        $alreadyLinked = Suo::whereNotNull('id_quotation')
            ->where('id_quotation', $request->id_quotation)
            ->where('id', '!=', $suo->id)
            ->exists();

        if ($alreadyLinked) {
            return response()->json(['success' => false, 'message' => 'Penawaran ini sudah dihubungkan ke SUO lain.'], 422);
        }

        $suo->status       = 'converted';
        $suo->id_quotation = $request->id_quotation;
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

        $suo = new Suo();
        $suo->no_suo       = $this->generateNoSuo();
        $suo->company      = $client->company ?? '-';
        $suo->pic          = $quotation->pic->name_pic ?? '-';
        $suo->address      = $client->address ?? '-';
        $suo->notes        = 'Diajukan otomatis dari Penawaran ' . $quotation->no_quote;
        $suo->id_sales     = $quotation->id_sales;
        $suo->status       = 'submitted';
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

        $suo = new Suo();
        $suo->no_suo            = $this->generateNoSuo();
        $suo->company           = $client->company ?? '-';
        $suo->pic               = $quotation->attn ?: ($quotation->pic?->name_pic ?? '-');
        $suo->address           = $quotation->address ?: ($client->address ?? '-');
        $suo->notes             = 'Diajukan otomatis dari Penawaran Unit ' . $quotation->no_quote;
        $suo->id_sales          = $quotation->id_sales;
        $suo->status            = 'submitted';
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
        $data = Suo::where('id_sales', Auth::id())
            ->orderByDesc('created_at')
            ->get(['id', 'no_suo', 'company', 'pic', 'status', 'no_invoice_booking', 'created_at']);

        return response()->json(['data' => $data]);
    }

    public function dataLogistic()
    {
        $data = Suo::whereIn('status', ['submitted', 'confirmed', 'goods_out', 'converted'])
            ->orderByDesc('created_at')
            ->get(['id', 'no_suo', 'company', 'pic', 'status', 'created_at']);

        return response()->json(['data' => $data]);
    }

    public function dataAccounting()
    {
        $data = Suo::whereIn('status', ['confirmed', 'goods_out', 'converted'])
            ->orderByDesc('created_at')
            ->get(['id', 'no_suo', 'company', 'pic', 'status', 'no_invoice_booking', 'created_at']);

        return response()->json(['data' => $data]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function generateNoSuo()
    {
        $romans = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        $now    = Carbon::now();
        $roman  = $romans[$now->month - 1];
        $year   = $now->year;
        $count  = Suo::where('no_suo', 'LIKE', "SUO%-{$roman}-{$year}")->count();
        return 'SUO' . str_pad($count + 1, 3, '0', STR_PAD_LEFT) . '-' . $roman . '-' . $year;
    }

    private function generateBookingInvoiceNumber()
    {
        $romans = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        $now    = Carbon::now();
        $roman  = $romans[$now->month - 1];
        $year   = $now->year;

        // Ambil invoice terakhir dengan format angka/... (bukan ID numerik panjang)
        $lastInvoice = Invoice::where('no_invoice', 'LIKE', '%/%')
            ->orderByDesc('id')
            ->value('no_invoice');

        $lastSuoBooked = Suo::whereNotNull('no_invoice_booking')
            ->orderByDesc('id')
            ->value('no_invoice_booking');

        $seqInvoice = 0;
        if ($lastInvoice) {
            preg_match('/^(\d+)\//', $lastInvoice, $m);
            $seqInvoice = isset($m[1]) ? (int) $m[1] : 0;
        }

        $seqSuo = 0;
        if ($lastSuoBooked) {
            preg_match('/^(\d+)\//', $lastSuoBooked, $m);
            $seqSuo = isset($m[1]) ? (int) $m[1] : 0;
        }

        $next = max($seqInvoice, $seqSuo) + 1;

        return $next . '/SJ-P/RJO/' . $roman . '/' . $year;
    }
}
