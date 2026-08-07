<?php

namespace App\Http\Controllers;

use App\Models\ChangeStatus;
use App\Models\Comment;
use App\Models\Contract;
use App\Models\DetailProduct;
use App\Models\DetailQuotation;
use App\Models\Invoice;
use App\Models\Machine;
use App\Models\Payment;
use App\Models\PowerServicePrice;
use App\Models\Product;
use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\SerialProduct;
use App\Models\Unit;
use App\Models\UnitPmTemplateItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Halaman "unit siap ditawarkan" — datanya dimuat via AJAX per tab (lihat
        // table-unit-ready.js), sumbernya fixed_asset (qc_status='ok') join unit.
        // Versi lama berbasis Machine ada di index-legacy.blade.php (tidak dipakai lagi).
        return view('pages.warehouse.unit.index');
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
        $rule = [
            'desc' =>
                'required',

            'serial' =>
                'required',
        ];

        $message = [
            'desc.required' => 'Field desc Wajib Diisi',
            'serial.required' => 'Field serial Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        $machine = new Machine();
        $machine->id_client = 5387;
        $machine->id_unit = $request->unit;
        $machine->serial = $request->serial;
        $machine->status = $request->status;
        $machine->status_unit = $request->status_unit;
        $machine->desc = $request->desc;
        $machine->tag = $request->tag;
        $machine->price = $request->semuanya;
        $machine->price_rental = $request->rental;
        $machine->price_best = $request->best;
        $machine->location = '-';
        $unitSave = $machine->save();
        if ($unitSave) {
            return redirect('/unit')->with('message', 'data telah di tambahkan');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Unit::find($id);
        $allStock = $product->stock + $product->warehouse_stock;
        $details = DetailProduct::where('id_product', $id)->get();
        $serials = SerialProduct::where('id_product', $id)->get();
        $equivalent = DetailProduct::all();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();


        // Comment Buat Admin
        $firstComments = Comment::where('id_user', Auth::id())
            ->groupBy('id_status')
            ->get();

        $statusIds = $firstComments->pluck('id_status')->toArray();
        $dates = $firstComments->pluck('created_at', 'id_status');

        $commentsQuery = Comment::join('change_status as c', 'c.id', '=', 'comment.id_status')
            ->join('quotation as q', 'q.id', '=', 'c.id_quotation')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->whereIn('comment.id_status', $statusIds)
            ->where(function ($query) use ($dates) {
                foreach ($dates as $statusId => $createdAt) {
                    $query->orWhere(function ($subQuery) use ($statusId, $createdAt) {
                        $subQuery->where('comment.id_status', $statusId)
                            ->whereRaw('TIMESTAMPDIFF(SECOND, ?, comment.created_at) > 0', [$createdAt]);
                    });
                }
            })
            ->where('comment.id_user', '!=', Auth::id());

        // Ambil semua komentar yang relevan
        $commentAdmin = $commentsQuery->orderBy('comment.id_status')
            ->orderByDesc('comment.created_at')
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

        // Filter untuk komentar dengan level '1'
        $unreadCommentAdmin = $commentsQuery->where('comment.level', '1')
            ->orderBy('comment.id_status')
            ->orderByDesc('comment.created_at')
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

        // End Comment Admin
        $quotationComment = Quotation::join('change_status as c', 'c.id_quotation', '=', 'quotation.id')
            ->join('comment as o', 'o.id_status', '=', 'c.id')
            ->join('users as u', 'u.id', '=', 'o.id_user')
            ->where('quotation.id_sales', Auth::id())
            ->where('o.type', 'quotation')  // Pastikan filter type di sini
            ->where('o.id_user', '!=', Auth::id())
            ->orderBy('o.date', 'DESC')
            ->select(['quotation.id as idQ', 'o.id as idC', 'o.id_user', 'o.level', 'o.comment', 'o.date', 'o.type', 'quotation.no_quote', 'u.name', 'u.image']);

        // Query untuk mengambil data dengan type "prospect"
        $prospectComment = Comment::join('prospect as p', 'comment.id_prospect', '=', 'p.id')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->join('pic as pi', 'pi.id', '=', 'p.id_pic')
            ->join('client as c', 'c.id', '=', 'pi.id_client')
            ->where('p.id_sales', Auth::id())
            ->where('comment.type', 'prospect')  // Pastikan filter type di sini
            ->where('comment.id_user', '!=', Auth::id())
            ->orderBy('comment.date', 'DESC')
            ->select(['p.id as idP', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'comment.type', 'c.company', 'u.name', 'u.image']);

        // Menggabungkan kedua query menggunakan union
        $comment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->take(5)
            ->get();
        $unreadComment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->where('o.level', '1')
            ->take(5)
            ->get();
        return view('pages.warehouse.unit.detail', compact('equivalent', 'product', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'details', 'leveledProspect', 'noSaleProspect', 'serials', 'allStock'));
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
        $rule = [
            'sku'  => 'required',
            'unit' => 'required',
        ];
        $message = [
            'sku.required'  => 'Field SKU wajib diisi',
            'unit.required' => 'Kategori unit wajib dipilih',
        ];

        $this->validate($request, $rule, $message);
        $unit = Unit::find($id);
        $unit->sku              = $request->sku;
        $unit->brand            = $request->brand ?? '';
        $unit->model            = $request->model ?? '';
        $unit->desc             = $request->desc ?? $unit->desc;
        $unit->unit             = $request->unit;
        $unit->voltage          = $request->voltage ?? '';
        $unit->bar              = $request->bar ?: null;
        $unit->power            = $request->power ?? '';
        $unit->air_cap          = $request->air_cap ?: null;
        $unit->connect          = $request->connect ?? '';
        $unit->type_unit        = $request->type_unit ?? '';
        $unit->generation       = $request->generation ?: null;
        $unit->cooling          = $request->cooling ?? '';
        $unit->exhaust          = $request->exhaust ?? '';
        $unit->refrigerant_type = $request->refrigerant_type ?? '';
        $unit->pdp              = $request->pdp ?? '';
        $unit->dimension        = $request->dimension ?? '';
        $unit->weight           = $request->weight ?: null;
        $unit->filtration       = $request->filtration ?? '';
        $unit->oil_content      = $request->oil_content ?? '';
        $unit->grade            = $request->grade ?? '';
        $unit->material         = $request->material ?? '';
        $unit->capacity         = $request->capacity ?: null;
        $unit->test_pressure    = $request->test_pressure ?: null;
        $unit->note             = $request->note;
        $unit->status           = $request->status;
        $unitSave = $unit->save();
        if ($unitSave) {
            SerialProduct::updateOrCreate(
                ['id_product' => $unit->id],
                [
                    'brand'     => $unit->brand ?? '',
                    'pn'        => $unit->model ?? '',
                    'bar'       => $unit->bar,
                    'air_cap'   => $unit->air_cap,
                    'detail'    => $unit->desc,
                    'image'     => '',
                    'fxp_parts' => '',
                    'rental'    => '0',
                    'second'    => '0',
                    'new'       => '0',
                ]
            );
            return redirect('/unit-global/' . $unit->id)->with('message', 'data telah di tambahkan');
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
        $unit = Unit::find($id);

        if (!$unit) {
            return redirect('/unit-global/' . $id)->with('error', 'Produk tidak ditemukan');
        }

        // $replacement = DetailProduct::where('id_product', $id)->get();
        // $equivalents = SerialProduct::where('id_product', $id)->get();

        SerialProduct::where('id_product', $id)->delete();
        $delUnit = $unit->delete();

        if ($delUnit) {
            return 1;
        } else {
            return 0;
        }
    }
    public function quotationDetail($id)
    {
        $totalAmount = 0;
        $dateNow = Carbon::now();
        $numberSP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $dateNow)->where('q.tax', '11')->where('contract.type', 'Selling')->groupBy('contract.id')->get('contract.id');
        $numberSNP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $dateNow)->where('q.tax', '0')->where('contract.type', 'Selling')->groupBy('contract.id')->get('contract.id');
        $numberCP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $dateNow)->where('q.tax', '11')->where('contract.type', 'Order')->groupBy('contract.id')->get('contract.id');
        $numberCNP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $dateNow)->where('q.tax', '0')->where('contract.type', 'Order')->groupBy('contract.id')->get('contract.id');
        $formattedNumberSP = str_pad($numberSP->count() + 1, 3, '0', STR_PAD_LEFT);
        $formattedNumberSNP = str_pad($numberSNP->count() + 1, 3, '0', STR_PAD_LEFT);
        $formattedNumberCP = str_pad($numberCP->count() + 1, 3, '0', STR_PAD_LEFT);
        $formattedNumberCNP = str_pad($numberCNP->count() + 1, 3, '0', STR_PAD_LEFT);
        $quote = Quotation::find($id);
        $quotations = Quotation::where('primary_id', $quote->primary_id)->get();
        $lastQuote = Quotation::where('primary_id', $quote->primary_id)->orderByDesc('num_rev')->first();
        $primQuote = Quotation::where('primary_id', $quote->primary_id)->where('is_primary', '1')->first();
        $invoice = Invoice::where('id_quotation', $id)->get();
        $dquote = DetailQuotation::where('id_quotation', $id)->get();
        $payments = Payment::where('id_quotation', $id)->get();
        $product = Product::join('serial_product as s', 's.id_product', '=', 'product.id')->get(['s.id', 'product.go', 's.pn']);
        $admin = User::where('role', 'Admin')->get();
        $noQuote = substr($quote->no_quote, 0, 3);
        $today = Carbon::now();
        $tax = ($quote->subtotal - $quote->diskon) * $quote->tax / 100;
        $afterDisc = $quote->subtotal - $quote->diskon;
        // dd($invoice[0]->no_invoice);
        $thisYear = $today->year;
        foreach ($payments as $payment) {
            $totalAmount += $payment->amount;
        }
        $status = ChangeStatus::where('id_quotation', $quote->primary_id)->with('comment')->get();


        // Comment Buat Admin
        $firstComments = Comment::where('id_user', Auth::id())
            ->groupBy('id_status')
            ->get();

        $statusIds = $firstComments->pluck('id_status')->toArray();
        $dates = $firstComments->pluck('created_at', 'id_status');

        $commentsQuery = Comment::join('change_status as c', 'c.id', '=', 'comment.id_status')
            ->join('quotation as q', 'q.id', '=', 'c.id_quotation')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->whereIn('comment.id_status', $statusIds)
            ->where(function ($query) use ($dates) {
                foreach ($dates as $statusId => $createdAt) {
                    $query->orWhere(function ($subQuery) use ($statusId, $createdAt) {
                        $subQuery->where('comment.id_status', $statusId)
                            ->whereRaw('TIMESTAMPDIFF(SECOND, ?, comment.created_at) > 0', [$createdAt]);
                    });
                }
            })
            ->where('comment.id_user', '!=', Auth::id());

        // Ambil semua komentar yang relevan
        $commentAdmin = $commentsQuery->orderBy('comment.id_status')
            ->orderByDesc('comment.created_at')
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

        // Filter untuk komentar dengan level '1'
        $unreadCommentAdmin = $commentsQuery->where('comment.level', '1')
            ->orderBy('comment.id_status')
            ->orderByDesc('comment.created_at')
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

        // End Comment Admin
        $quotationComment = Quotation::join('change_status as c', 'c.id_quotation', '=', 'quotation.id')
            ->join('comment as o', 'o.id_status', '=', 'c.id')
            ->join('users as u', 'u.id', '=', 'o.id_user')
            ->where('quotation.id_sales', Auth::id())
            ->where('o.type', 'quotation')  // Pastikan filter type di sini
            ->where('o.id_user', '!=', Auth::id())
            ->orderBy('o.date', 'DESC')
            ->select(['quotation.id as idQ', 'o.id as idC', 'o.id_user', 'o.level', 'o.comment', 'o.date', 'o.type', 'quotation.no_quote', 'u.name', 'u.image']);

        // Query untuk mengambil data dengan type "prospect"
        $prospectComment = Comment::join('prospect as p', 'comment.id_prospect', '=', 'p.id')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->join('pic as pi', 'pi.id', '=', 'p.id_pic')
            ->join('client as c', 'c.id', '=', 'pi.id_client')
            ->where('p.id_sales', Auth::id())
            ->where('comment.type', 'prospect')  // Pastikan filter type di sini
            ->where('comment.id_user', '!=', Auth::id())
            ->orderBy('comment.date', 'DESC')
            ->select(['p.id as idP', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'comment.type', 'c.company', 'u.name', 'u.image']);

        // Menggabungkan kedua query menggunakan union
        $comment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->take(5)
            ->get();
        $unreadComment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->where('o.level', '1')
            ->take(5)
            ->get();
        // dd($comment);
        $remaining = $quote->harga_total - $totalAmount;
        // dd($formattedNumberSP);
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();
        return view("pages.sales.quotation.unit.detail", compact('quote', 'status', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'leveledProspect', 'noSaleProspect', 'lastQuote', 'primQuote', 'quotations', 'dquote', 'admin', 'noQuote', 'thisYear', 'tax', 'formattedNumberSP', 'formattedNumberSNP', 'formattedNumberCP', 'formattedNumberCNP', 'invoice', 'payments', 'remaining', 'afterDisc'));
    }

    public function indexGlobal()
    {
        return view('pages.warehouse.unit.index-global');
    }

    public function checkSku(Request $request)
    {
        $sku    = trim($request->query('sku', ''));
        $exists = Unit::where('sku', $sku)->where('type', 'global')->first(['id', 'sku', 'unit', 'brand', 'model']);
        return response()->json([
            'exists' => (bool) $exists,
            'data'   => $exists,
        ]);
    }

    public function storeGlobal(Request $request)
    {
        $rule = [
            'sku'  => 'required',
            'unit' => 'required',
        ];
        $message = [
            'sku.required'  => 'Field SKU wajib diisi',
            'unit.required' => 'Kategori unit wajib dipilih',
        ];
        $this->validate($request, $rule, $message);

        $lastUnit = Unit::orderBy('id', 'desc')->first();
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $unit = new Unit;
        if (@$lastUnit) {
            $unit->id = ($lastUnit->id > $lastProduct->id) ? $lastUnit->id + 1 : $lastProduct->id + 1;
        } else {
            $unit->id = $lastProduct->id + 1;
        }
        $unit->sku              = $request->sku;
        $unit->brand            = $request->brand ?? '';
        $unit->model            = $request->model ?? '';
        $unit->desc             = $request->desc ?? '';
        $unit->unit             = $request->unit;
        $unit->voltage          = $request->voltage ?? '';
        $unit->bar              = $request->bar ?: null;
        $unit->power            = $request->power ?? '';
        $unit->air_cap          = $request->air_cap ?: null;
        $unit->connect          = $request->connect ?? '';
        $unit->type_unit        = $request->type_unit ?? '';
        $unit->generation       = $request->generation ?: null;
        $unit->cooling          = $request->cooling ?? '';
        $unit->exhaust          = $request->exhaust ?? '';
        $unit->refrigerant_type = $request->refrigerant_type ?? '';
        $unit->pdp              = $request->pdp ?? '';
        $unit->dimension        = $request->dimension ?? '';
        $unit->weight           = $request->weight ?: null;
        $unit->filtration       = $request->filtration ?? '';
        $unit->oil_content      = $request->oil_content ?? '';
        $unit->grade            = $request->grade ?? '';
        $unit->material         = $request->material ?? '';
        $unit->capacity         = $request->capacity ?: null;
        $unit->test_pressure    = $request->test_pressure ?: null;
        $unit->note             = $request->note;
        $unit->stock            = 0;
        $unit->first_stock      = 0;
        $unit->warehouse_stock  = 0;
        $unit->status           = 'global';
        $unit->type             = 'global';
        // foreach ($request->unit as $key => $value) {
        //     if ($value == 'rental') {
        //         $unit->rental = '1';
        //     }
        //     if ($value == 'second') {
        //         $unit->second = '1';
        //     }
        //     if ($value == 'new') {
        //         $unit->new = '1';
        //     }
        // }
        $unitSave = $unit->save();
        if ($unitSave) {
            SerialProduct::create([
                'id_product' => $unit->id,
                'brand'      => $unit->brand ?? '',
                'pn'         => $unit->model ?? '',
                'bar'        => $unit->bar,
                'air_cap'    => $unit->air_cap,
                'detail'     => $unit->desc,
                'image'      => '',
                'fxp_parts'  => '',
                'rental'     => '0',
                'second'     => '0',
                'new'        => '0',
            ]);
            return redirect('/unit-global/' . $unit->id)->with('message', 'data telah di tambahkan');
        }
    }
    /**
     * Search semua Unit Global (bukan cuma yang sudah dipublish ke catalog_unit).
     * Dipakai picker "Load Template PM" di form Unit Quotation.
     */
    public function searchGlobal(Request $request)
    {
        $q = trim($request->query('q', ''));
        $like = '%' . $q . '%';

        $units = Unit::where('sku', 'like', $like)
            ->orWhere('brand', 'like', $like)
            ->orWhere('model', 'like', $like)
            ->orWhere('unit', 'like', $like)
            ->orderBy('brand')
            ->orderBy('sku')
            ->limit(30)
            ->get(['id', 'sku', 'brand', 'model', 'unit', 'power']);

        return response()->json($units);
    }

    public function showGlobal($id)
    {
        $product = Unit::find($id);
        $allStock = $product->stock + $product->warehouse_stock;
        $details = DetailProduct::where('id_product', $id)->get();
        $serials = SerialProduct::where('id_product', $id)->get();
        $equivalent = SerialProduct::where('rental','0')->where('second','0')->where('new','0')->get();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();

        // Comment Buat Admin
        $firstComments = Comment::where('id_user', Auth::id())
            ->groupBy('id_status')
            ->get();

        $statusIds = $firstComments->pluck('id_status')->toArray();
        $dates = $firstComments->pluck('created_at', 'id_status');

        $commentsQuery = Comment::join('change_status as c', 'c.id', '=', 'comment.id_status')
            ->join('quotation as q', 'q.id', '=', 'c.id_quotation')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->whereIn('comment.id_status', $statusIds)
            ->where(function ($query) use ($dates) {
                foreach ($dates as $statusId => $createdAt) {
                    $query->orWhere(function ($subQuery) use ($statusId, $createdAt) {
                        $subQuery->where('comment.id_status', $statusId)
                            ->whereRaw('TIMESTAMPDIFF(SECOND, ?, comment.created_at) > 0', [$createdAt]);
                    });
                }
            })
            ->where('comment.id_user', '!=', Auth::id());

        // Ambil semua komentar yang relevan
        $commentAdmin = $commentsQuery->orderBy('comment.id_status')
            ->orderByDesc('comment.created_at')
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

        // Filter untuk komentar dengan level '1'
        $unreadCommentAdmin = $commentsQuery->where('comment.level', '1')
            ->orderBy('comment.id_status')
            ->orderByDesc('comment.created_at')
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

        // End Comment Admin
        $quotationComment = Quotation::join('change_status as c', 'c.id_quotation', '=', 'quotation.id')
            ->join('comment as o', 'o.id_status', '=', 'c.id')
            ->join('users as u', 'u.id', '=', 'o.id_user')
            ->where('quotation.id_sales', Auth::id())
            ->where('o.type', 'quotation')  // Pastikan filter type di sini
            ->where('o.id_user', '!=', Auth::id())
            ->orderBy('o.date', 'DESC')
            ->select(['quotation.id as idQ', 'o.id as idC', 'o.id_user', 'o.level', 'o.comment', 'o.date', 'o.type', 'quotation.no_quote', 'u.name', 'u.image']);

        // Query untuk mengambil data dengan type "prospect"
        $prospectComment = Comment::join('prospect as p', 'comment.id_prospect', '=', 'p.id')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->join('pic as pi', 'pi.id', '=', 'p.id_pic')
            ->join('client as c', 'c.id', '=', 'pi.id_client')
            ->where('p.id_sales', Auth::id())
            ->where('comment.type', 'prospect')  // Pastikan filter type di sini
            ->where('comment.id_user', '!=', Auth::id())
            ->orderBy('comment.date', 'DESC')
            ->select(['p.id as idP', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'comment.type', 'c.company', 'u.name', 'u.image']);

        // Menggabungkan kedua query menggunakan union
        $comment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->take(5)
            ->get();
        $unreadComment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->where('o.level', '1')
            ->take(5)
            ->get();
        return view('pages.warehouse.unit.detail-global', compact('equivalent', 'product', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'details', 'leveledProspect', 'noSaleProspect', 'serials', 'allStock'));
    }

    /**
     * Ambil draft item Template Penawaran PM (tersimpan di unit_pm_template_items) untuk
     * sebuah unit + level PM, plus tarif jasa power_service_prices.
     * Dipakai AJAX di halaman detail-global untuk builder & preview sebelum digenerate ke quotation.
     */
    public function pmTemplate(Request $request, $id)
    {
        $level = strtoupper($request->query('level', 'PM1'));
        if (!in_array($level, ['PM1', 'PM2', 'PM3', 'PM4'])) {
            return response()->json(['message' => 'Level PM tidak valid.'], 422);
        }

        $unit = Unit::findOrFail($id);

        // Item template tersimpan, urut apa adanya (bisa selang-seling header/part/custom)
        $rawItems = UnitPmTemplateItem::where('id_unit', $id)
            ->where('level', $level)
            ->orderBy('sort_order')
            ->get();

        // Bulk-fetch data equivalent (brand/pn/stok live) buat item type=part, supaya pas
        // di-load ke form quotation bisa jadi baris "Spare Part" beneran (id_equivalent kebawa)
        // dan terkoneksi ke logic baca stok saat konversi ke PO — bukan cuma baris teks custom.
        $equivalentIds = $rawItems->where('type', 'part')->pluck('id_equivalent')->filter()->unique()->values();
        $equivalentsMap = collect();
        if ($equivalentIds->isNotEmpty()) {
            $equivalentsMap = SerialProduct::join('product as p', 'p.id', '=', 'serial_product.id_product')
                ->whereIn('serial_product.id', $equivalentIds)
                ->select(
                    'serial_product.id as id_equivalent',
                    'serial_product.pn',
                    'serial_product.brand',
                    'serial_product.price',
                    'p.description as product_desc',
                    'p.go as genuine_status',
                    'p.unit as product_unit',
                    'p.warehouse_stock',
                    'p.stock',
                    'p.pending_stock'
                )
                ->get()
                ->keyBy('id_equivalent');
        }

        $formatBullets = function ($text) {
            if (empty($text)) return '';
            $lines = preg_split('/\r\n|\r|\n/', $text);
            $formatted = [];
            foreach ($lines as $idx => $line) {
                $trimmed = trim(preg_replace('/^[\s\-\*\•]+/u', '', $line));
                if ($trimmed !== '') {
                    if (preg_match('/^Scope of Work\s*:?/i', $trimmed) || (substr($trimmed, -1) === ':' && $idx === 0)) {
                        $formatted[] = $trimmed;
                    } else {
                        $formatted[] = '• ' . $trimmed;
                    }
                }
            }
            return implode("\n", $formatted);
        };

        $items = $rawItems->map(function ($item) use ($equivalentsMap, $formatBullets) {
            $mapped = [
                'id' => $item->id,
                'type' => $item->type,
                'id_equivalent' => $item->id_equivalent,
                'label' => $item->label,
                'description' => $formatBullets($item->description),
                'qty' => (float) $item->qty,
                'info_qty' => $item->info_qty ?: 'Pcs',
                'price' => (float) $item->price,
            ];
            if ($item->type === 'part' && $item->id_equivalent && $equivalentsMap->has($item->id_equivalent)) {
                $mapped['equivalent'] = $equivalentsMap->get($item->id_equivalent);
            }
            return $mapped;
        });

        // Saran (bukan otomatis dipasang) buat tombol "Tambah Jasa Service" — prefill dari pricelist Forecast
        $normalizedPower = \App\Http\Controllers\ForecastController::normalizePower($unit->power);
        $servicePrice = $normalizedPower
            ? PowerServicePrice::where('power', $normalizedPower)->first()
            : null;

        $standardTemplate = PowerServicePrice::where('power', 'STANDARD_TEMPLATE')->first()
            ?? PowerServicePrice::whereNotNull('desc_pm1')->latest()->first();

        $priceField = 'price_' . strtolower($level);
        $descField = 'desc_' . strtolower($level);
        $noteField = 'note_' . strtolower($level);
        $serviceFee = $servicePrice ? (float) $servicePrice->{$priceField} : null;
        
        $serviceDesc = ($standardTemplate && !empty($standardTemplate->{$descField}))
            ? $standardTemplate->{$descField}
            : ($servicePrice ? $servicePrice->{$descField} : null);

        $serviceNote = ($standardTemplate && !empty($standardTemplate->{$noteField}))
            ? $standardTemplate->{$noteField}
            : ($servicePrice ? $servicePrice->{$noteField} : null);

        $serviceDesc = $formatBullets($serviceDesc);
        $serviceNote = $formatBullets($serviceNote);

        return response()->json([
            'unit' => [
                'id' => $unit->id,
                'sku' => $unit->sku,
                'brand' => $unit->brand,
                'model' => $unit->model,
                'power' => $unit->power,
            ],
            'level' => $level,
            'items' => $items,
            'note' => $serviceNote,
            'service_suggestion' => [
                'label' => 'Preventive Maintenance ' . $level,
                'description' => $serviceDesc,
                'amount' => $serviceFee,
                'matched' => (bool) $servicePrice,
                'power_normalized' => $normalizedPower,
            ],
        ]);
    }

    /**
     * Simpan (replace penuh) daftar item Template Penawaran PM untuk sebuah unit + level.
     * Dipanggil dari builder di halaman detail-global tiap kali user klik "Simpan Template"
     * atau "Generate ke Quotation" (auto-save dulu supaya Forecast Sales ikut update).
     */
    public function pmTemplateSave(Request $request, $id)
    {
        $level = strtoupper($request->input('level', ''));
        if (!in_array($level, ['PM1', 'PM2', 'PM3', 'PM4'])) {
            return response()->json(['message' => 'Level PM tidak valid.'], 422);
        }

        Unit::findOrFail($id);
        $items = $request->input('items', []);

        DB::transaction(function () use ($id, $level, $items) {
            UnitPmTemplateItem::where('id_unit', $id)->where('level', $level)->delete();

            foreach ($items as $i => $item) {
                if (empty($item['label'])) {
                    continue;
                }
                $type = in_array($item['type'] ?? '', ['custom', 'header']) ? $item['type'] : 'part';
                UnitPmTemplateItem::create([
                    'id_unit' => $id,
                    'level' => $level,
                    'type' => $type,
                    'id_equivalent' => $item['id_equivalent'] ?? null,
                    'label' => $item['label'],
                    'description' => $item['description'] ?? null,
                    'qty' => $item['qty'] ?? 1,
                    'info_qty' => $item['info_qty'] ?? 'Pcs',
                    'price' => $item['price'] ?? 0,
                    'sort_order' => $i,
                ]);
            }
        });

        return response()->json(['message' => 'Template PM ' . $level . ' berhasil disimpan.']);
    }

    public function updatePrice(Request $request, $id)
    {
        SerialProduct::where('id_product', $id)
            ->update(['price' => $request->input('price', 0)]);

        return redirect()->route('unit-global.show', $id)
            ->with('success', 'Harga pricelist berhasil diperbarui.');
    }

    public function corfac()
    {
        return view('pages.warehouse.unit.cor-factor');

    }
    public function updateUnitReftech(Request $request, $id)
    {
        $machine = Machine::find($id);
        // dd($request->all());
        $machine->status = $request->status;
        $machine->status_unit = $request->status_unit;
        $machine->price = $request->total;
        $machine->price_rental = $request->totalRental;
        $machine->price_best = $request->totalBest;
        $machine->desc = $request->desc;
        $machine->tag = $request->tag;
        $unitSave = $machine->save();
        if ($unitSave) {
            return redirect('/unit')->with('message', 'data telah di tambahkan');
        }
    }

}
