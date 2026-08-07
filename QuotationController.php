<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use App\Models\ChangeStatus;
use App\Models\Client;
use App\Models\Comment;
use App\Models\Contract;
use App\Models\CrmStatus;
use App\Models\Delivery;
use App\Models\DetailDelivery;
use App\Models\DetailPendingPO;
use App\Models\DetailQuotation;
use App\Models\DetailServiceQuotation;
use App\Models\Invoice;
use App\Models\MachineTemplate;
use App\Models\Payment;
use App\Models\PendingPO;
use App\Models\Pic;
use App\Models\Product;
use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\SerialProduct;
use App\Models\SubtitleQuotation;
use App\Models\SubtitleTemplate;
use App\Models\Termncon;
use App\Models\Unit;
use App\Models\Suo;
use App\Models\User;
use Carbon\Carbon;
use File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Log;
use PDF;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $quotation = Quotation::where('id_sales', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->where('type', 'Sparepart')->get();
        $forecast = Quotation::where('id_sales', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->whereIn('status', ['20', '30', '40', '60', '80'])->where('type', 'Sparepart')->sum('nett');
        $prospect = Quotation::where('id_sales', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->where('status', '80')->where('type', 'Sparepart')->sum('nett');
        $po = Quotation::where('id_sales', Auth::user()->id)->where('status', '100')->where('level', '1')->where('is_primary', '1')->where('type', 'Sparepart')->sum('nett');
        $loss = Quotation::where('id_sales', Auth::user()->id)->where('status', '0')->where('level', '1')->where('is_primary', '1')->where('type', 'Sparepart')->sum('nett');
        $quotationAdmin = Quotation::get();
        $forecastAdmin = Quotation::whereIn('status', ['20', '30', '40', '60', '80'])->where('level', '1')->where('is_primary', '1')->where('type', 'Sparepart')->sum('nett');
        $prospectAdmin = Quotation::where('status', '80')->where('level', '1')->where('is_primary', '1')->where('type', 'Sparepart')->sum('nett');
        $poAdmin = Quotation::where('status', '100')->where('level', '1')->where('is_primary', '1')->where('type', 'Sparepart')->sum('nett');
        $lossAdmin = Quotation::where('status', '0')->where('level', '1')->where('is_primary', '1')->where('type', 'Sparepart')->sum('nett');
        // dd();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();
        $machine = MachineTemplate::all();
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
        $salesList = User::where('role', 'Sales')->orderBy('name')->get(['id', 'name']);
        return view('pages.sales.quotation.index', compact('machine', 'noSaleProspect', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'leveledProspect', 'quotation', 'forecast', 'prospect', 'po', 'loss', 'quotationAdmin', 'forecastAdmin', 'prospectAdmin', 'poAdmin', 'lossAdmin', 'salesList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dateNow = Carbon::now();
        $numberQ = Quotation::whereYear('estimated_date', $dateNow)->where('id_sales', Auth::user()->id)->count();
        $formattedNumberQ = str_pad($numberQ + 1, 3, '0', STR_PAD_LEFT);
        $monthNow = $dateNow->month;
        $formattedMonthNow = $this->convertToRoman($monthNow);
        // $pic = Pic::join('client as c', 'c.id', '=', 'pic.id_client')->where('c.id_sales', Auth::user()->id)->get('pic.*');
        $pic = client::where('client.id_sales', Auth::user()->id)->get();
        $sales = User::where('role', 'sales')->get();
        $product = Product::join('serial_product as s', 's.id_product', '=', 'product.id')->get(['product.id as comId', 's.id', 'product.go', 's.pn', 's.brand', 'product.detail_desc', 'product.unit']);
        // dd($product);


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
        return view('pages.sales.quotation.form', compact('pic', 'sales', 'formattedNumberQ', 'formattedMonthNow', 'product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->pic);
        $pic = Pic::find($request->pic);
        $client = Client::find($pic->id_client);
        $rule = [
            'no_quote' => 'required',
            'title' => 'required',
            'product' => 'required',
            'detail_product' => 'required',
            'expired_date' => 'required',
            'validity' => 'required',
            'pricing' => 'required',
            'delivery_process' => 'required',
            'payment' => 'required',
            'shipping' => 'required',
        ];
        $message = [
            'no_quote.required' => 'Field No Quote Wajib Diisi',
            'title.required' => 'Field Title Wajib Diisi',
            'product.required' => 'Field Product Wajib Diisi',
            'detail_product.required' => 'Field Detail Product Wajib Diisi',
            'expired_date.required' => 'Wajib isi Expired Date',
            'termcon.required' => 'Field Term and Conditions Wajib Diisi',
            'shipping.required' => 'Quotation Wajib memiliki harga Antar',
        ];
        $this->validate($request, $rule, $message);
        $previousUrl = request()->create(url()->previous())->segment(2);
        // dd($previousUrl);
        // dd($request);
        // Masukan Data ke Tabel Quotataion
        $quotation = new Quotation();
        $quotation->id_pic = $request->pic;
        $quotation->id_sales = $request->id_sales;
        $quotation->id_service = NULL;
        $quotation->id_support = $client->id_support;
        $quotation->is_primary = "1";
        $quotation->primary_id = 0;
        $quotation->num_rev = 0;
        $quotation->destination = $request->destination;
        $quotation->no_pr = $request->no_pr;
        $quotation->week = $request->week;
        $quotation->quote_for = $request->type;
        $quotation->status = "20";
        $quotation->status_date = Carbon::today();
        $quotation->note = "-";
        $quotation->expired_date = $request->expired_date;
        $quotation->po_date = NULL;
        $quotation->po_file = NULL;
        if ($previousUrl == 'unit') {
            $quotation->type = 'Unit';
        } else {
            $quotation->type = 'Sparepart';
        }
        $quotation->level = '1';
        $quotation->estimated_date = $request->estimated_date;
        if ($request->tax != NULL) {
            $quotation->tax = $request->tax;
        } else {
            $quotation->tax = 0;
        }
        $quotation->shipping = $request->shipping ?? 0;
        $quotation->no_quote = $request->no_quote;
        $quotation->title = $request->title;
        $quotation->subtotal = $request->subtotal;
        if ($request->diskon != NULL) {
            $quotation->diskon = $request->diskon;
        } else {
            $quotation->diskon = 0;
        }
        $quotation->fee = 0;
        $quotation->nett = $request->subtotal - $request->diskon;
        $quotation->total_no_tax = $request->total_no_tax;
        $quotation->harga_total = $request->harga_total;
        $quotation->flag = $client->info;
        $quoteSave = $quotation->save();
        $quotation->primary_id = $quotation->id;
        $quotation->save();
        if ($quoteSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            foreach ($request->product as $item => $value) {
                $dQuote = new DetailQuotation;
                $dQuote->id_quotation = $quotation->id;
                $dQuote->id_equivalent = $request->product[$item];
                $dQuote->detail_product = $request->detail_product[$item];
                $dQuote->price = $request->price[$item];
                $dQuote->fee = 0;
                $dQuote->qty = $request->qty[$item];
                $dQuote->info_qty = $request->info_qty[$item];
                $dQuote->disc = $request->disc[$item];
                $dQuote->amount = $request->amount[$item];
                $dQuote->pph = 0;
                $dQuote->view = '0';
                $dQuoteSave = $dQuote->save();
            }
            $stats = new ChangeStatus;
            $stats->id_quotation = $quotation->id;
            $stats->date = Carbon::now();
            $stats->note = 'Quotation has been created';
            $stats->status = "10";
            $stats->save();
            if ($dQuoteSave) {
                // Masukan Data ke dalam Tabel Term n Condition
                $termncon = new Termncon;
                $termncon->id_quotation = $quotation->id;
                $termncon->validity = $request->validity;
                $termncon->pricing = $request->pricing;
                $termncon->delivery_process = $request->delivery_process;
                $termncon->payment = $request->payment;
                $termncon->note = $request->note;
                $termnconSave = $termncon->save();

                // Link ke SUO jika convert dari SUO
                if (session('suo_convert')) {
                    $suoData = session('suo_convert');
                    Suo::where('id', $suoData['id_suo'])->update([
                        'status'       => 'converted',
                        'id_quotation' => $quotation->id,
                    ]);
                    session()->forget('suo_convert');
                }
            }
        }
        if ($previousUrl == 'unit') {
            if ($termnconSave) {
                return redirect('/quote/unit-detail/' . $quotation->id)->with('message', 'data telah di tambahkan');
            }
        } else {
            if ($termnconSave) {
                return redirect('/quotation/' . $quotation->id)->with('message', 'data telah di tambahkan');
            }
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
        $quote = Quotation::find($id);
        if ($quote) {
            if ($quote->type === 'Service') {
                return redirect()->route('show-service.quotation', $id);
            } elseif ($quote->type === 'Overhaul') {
                return redirect()->route('show-overhaul.quotation', $id);
            } elseif ($quote->type === 'Unit') {
                return redirect()->route('detail.quote-unit', $id);
            }
        }

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
        $countPending = PendingPO::whereYear('created_at', $thisYear)->count();
        $nextNumber = $countPending + 1;

        $noPending = 'SO-' . $thisYear . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();
        return view("pages.sales.quotation.detail", compact('noPending', 'quote', 'status', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'leveledProspect', 'noSaleProspect', 'lastQuote', 'primQuote', 'quotations', 'dquote', 'admin', 'noQuote', 'thisYear', 'tax', 'formattedNumberSP', 'formattedNumberSNP', 'formattedNumberCP', 'formattedNumberCNP', 'invoice', 'payments', 'remaining', 'afterDisc'));
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
        $quote = Quotation::where('id', $id)->first();
        $lastQuote = Quotation::where('primary_id', $quote->primary_id)->orderByDesc('num_rev')->first();
        $detquote = DetailQuotation::where('id_quotation', $id)->get();
        $rule = [
            'no_quote' => 'required',
            'title' => 'required',
            'product' => 'required',
            'detail_product' => 'required',
            'expired_date' => 'required',
            'validity' => 'required',
            'pricing' => 'required',
            'delivery_process' => 'required',
            'payment' => 'required',
            'shipping' => 'required',
            'type' => 'required',
        ];
        $message = [
            'no_quote.required' => 'Field No Quote Wajib Diisi',
            'title.required' => 'Field Title Wajib Diisi',
            'product.required' => 'Field Product Wajib Diisi',
            'detail_product.required' => 'Field Detail Product Wajib Diisi',
            'expired_date.required' => 'Wajib isi Expired Date',
            'termcon.required' => 'Field Term and Conditions Wajib Diisi',
            'shipping.required' => 'Quotation Wajib memiliki harga Antar',
            'type.required' => 'Quotation Wajib memiliki harga Antar',
        ];
        $this->validate($request, $rule, $message);
        // dd($request->all());
        // Masukan Data ke Tabel Quotataion
        $quote->is_primary = '0';
        $quote->save();

        $quotation = new Quotation();
        $quotation->id_pic = $request->id_pic;
        $quotation->id_sales = $request->id_sales;
        $quotation->id_service = NULL;
        $quotation->id_support = $quote->id_support;
        $quotation->primary_id = $quote->primary_id;
        $quotation->is_primary = '1';
        $quotation->num_rev = $lastQuote->num_rev + 1;
        $quotation->destination = $quote->destination;
        if ($request->no_pr != NULL) {
            $quotation->no_pr = $request->no_pr;
        } else {
            $quotation->no_pr = NULL;
        }
        $quotation->status = $quote->status;
        $quotation->status_date = $quote->status_date;
        $quotation->note = $quote->note;
        $quotation->po_date = $quote->po_date;
        $quotation->po_file = $quote->po_file;
        $quotation->level = $quote->level;
        $quotation->expired_date = $request->expired_date;
        $quotation->estimated_date = $request->estimated_date;
        $quotation->tax = $request->tax;
        $quotation->shipping = $request->shipping ?? 0;
        $quotation->no_quote = $request->no_quote;
        $quotation->title = $request->title;
        $quotation->subtotal = $request->subtotal;
        if ($request->diskon != NULL) {
            $quotation->diskon = $request->diskon;
        } else {
            $quotation->diskon = 0;
        }
        $quotation->fee = $quote->fee;
        $quotation->nett = $request->subtotal - $request->diskon;
        $quotation->total_no_tax = $request->total_no_tax;
        $quotation->harga_total = $request->harga_total;
        $quotation->quote_for = $request->type;
        $quoteSave = $quotation->save();
        if ($quoteSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            foreach ($request->product as $item => $value) {
                $dQuote = new DetailQuotation;
                $dQuote->id_quotation = $quotation->id;
                $dQuote->id_equivalent = $request->product[$item];
                $dQuote->detail_product = $request->detail_product[$item];
                $dQuote->price = $request->price[$item];
                $dQuote->qty = $request->qty[$item];
                $dQuote->fee = 0;
                $dQuote->info_qty = $request->info_qty[$item];
                $dQuote->disc = $request->disc[$item];
                $dQuote->amount = $request->amount[$item];
                $dQuote->pph = 0;
                $dQuote->view = '0';
                $dQuoteSave = $dQuote->save();
            }
            if ($dQuoteSave) {
                // Masukan Data ke dalam Tabel Term n Condition
                $termncon = new Termncon;
                $termncon->id_quotation = $quotation->id;
                $termncon->validity = $request->validity;
                $termncon->pricing = $request->pricing;
                $termncon->delivery_process = $request->delivery_process;
                $termncon->payment = $request->payment;
                $termncon->note = $request->note;
                $termnconSave = $termncon->save();

                $stats = new ChangeStatus;
                $stats->id_quotation = $quotation->primary_id;
                $stats->date = Carbon::now();
                $stats->note = 'Quotation Revision - ' . $lastQuote->num_rev + 1;
                $stats->status = $quotation->status;
                $stats->save();
            }
        }
        if ($termnconSave) {
            return redirect('/quotation/' . $quotation->id)->with("success", "Data Revisi Quotation Telah Ditambahkan");
        }
    }
    public function update_part(Request $request, $id)
    {
        $quote = Quotation::find($id);
        // $lastQuote = Quotation::where('primary_id', $quote->primary_id)->orderByDesc('num_rev')->first();
        $detquote = DetailQuotation::where('id_quotation', $id)->get();
        $rule = [
            'no_quote' => 'required',
            'title' => 'required',
            'product' => 'required',
            'detail_product' => 'required',
            'expired_date' => 'required',
            'validity' => 'required',
            'pricing' => 'required',
            'delivery_process' => 'required',
            'payment' => 'required',
            'shipping' => 'required',
            'type' => 'required',
        ];
        $message = [
            'no_quote.required' => 'Field No Quote Wajib Diisi',
            'title.required' => 'Field Title Wajib Diisi',
            'product.required' => 'Field Product Wajib Diisi',
            'detail_product.required' => 'Field Detail Product Wajib Diisi',
            'expired_date.required' => 'Wajib isi Expired Date',
            'termcon.required' => 'Field Term and Conditions Wajib Diisi',
            'shipping.required' => 'Quotation Wajib memiliki harga Antar',
            'type.required' => 'Quotation Wajib memiliki harga Antar',
        ];
        $this->validate($request, $rule, $message);
        // dd($request->all());
        // Masukan Data ke Tabel Quotataion
        $quote->id_pic = $request->id_pic;
        $quote->id_sales = $request->id_sales;
        $quote->destination = $quote->destination;
        if ($request->no_pr != NULL) {
            $quote->no_pr = $request->no_pr;
        } else {
            $quote->no_pr = NULL;
        }
        $quote->status = $quote->status;
        $quote->status_date = $quote->status_date;
        $quote->note = $quote->note;
        $quote->po_date = $quote->po_date;
        $quote->po_file = $quote->po_file;
        $quote->level = $quote->level;
        $quote->expired_date = $request->expired_date;
        $quote->estimated_date = $request->estimated_date;
        $quote->tax = $request->tax;
        $quote->shipping = $request->shipping;
        $quote->no_quote = $request->no_quote;
        $quote->title = $request->title;
        $quote->subtotal = $request->subtotal;
        if ($request->diskon != NULL) {
            $quote->diskon = $request->diskon;
        } else {
            $quote->diskon = 0;
        }
        $quote->fee = $quote->fee;
        $quote->nett = $request->subtotal - $request->diskon;
        $quote->total_no_tax = $request->total_no_tax;
        $quote->harga_total = $request->harga_total;
        $quote->quote_for = $request->type;
        $quoteSave = $quote->save();
        if ($quoteSave) {
            foreach ($detquote as $item) {
                $item->delete();
            }
            // Masukan Data Ke Tabel Detail Quotataion
            foreach ($request->product as $item => $value) {
                $dQuote = new DetailQuotation;
                $dQuote->id_quotation = $quote->id;
                $dQuote->id_equivalent = $request->product[$item];
                $dQuote->detail_product = $request->detail_product[$item];
                $dQuote->price = $request->price[$item];
                $dQuote->qty = $request->qty[$item];
                $dQuote->fee = 0;
                $dQuote->info_qty = $request->info_qty[$item];
                $dQuote->disc = $request->disc[$item];
                $dQuote->amount = $request->amount[$item];
                $dQuote->pph = 0;
                $dQuote->view = '0';
                $dQuoteSave = $dQuote->save();
            }
            if ($dQuoteSave) {
                // Masukan Data ke dalam Tabel Term n Condition
                $termncon = Termncon::where('id_quotation', $id)->first();
                $termncon->validity = $request->validity;
                $termncon->pricing = $request->pricing;
                $termncon->delivery_process = $request->delivery_process;
                $termncon->payment = $request->payment;
                $termncon->note = $request->note;
                $termnconSave = $termncon->save();

                // $stats = new ChangeStatus;
                // $stats->id_quotation = $quote->primary_id;
                // $stats->date = Carbon::now();
                // $stats->note = 'Quotation Revision - ' . $lastQuote->num_rev + 1;
                // $stats->status = $quote->status;
                // $stats->save();
            }
        }
        if ($termnconSave) {
            return redirect('/quotation/' . $id)->with("success", "Data Revisi Quotation Telah Ditambahkan");
        }
    }
    public function update_service(Request $request, $id)
    {
        // dd($request->all());
        // $lastQuote = Quotation::where('primary_id', $quote->primary_id)->orderByDesc('num_rev')->first();
        $picId = $request->pic ?? $request->id_pic;
        $pic = Pic::find($picId);
        $client = Client::find($pic->id_client);
        $rule = [
            'no_quote' => 'required',
            'title' => 'required',
            'product' => 'required',
            'detail_product' => 'required',
            'expired_date' => 'required',
            'validity' => 'required',
            'pricing' => 'required',
            'delivery_process' => 'required',
            'payment' => 'required',
        ];
        $message = [
            'no_quote.required' => 'Field No Quote Wajib Diisi',
            'title.required' => 'Field Title Wajib Diisi',
            'product.required' => 'Field Product Wajib Diisi',
            'detail_product.required' => 'Field Detail Product Wajib Diisi',
            'expired_date.required' => 'Wajib isi Expired Date',
            'termcon.required' => 'Field Term and Conditions Wajib Diisi',
        ];
        // dd($quote->primary_id);

        $this->validate($request, $rule, $message);
        // Masukan Data ke Tabel Quotataion
        // $quotation = new Quotation();
        $quotation = Quotation::find($id);
        $quotation->id_pic = $picId;
        $quotation->id_sales = $request->id_sales;
        $quotation->id_support = $client->id_support;
        $quotation->status_date = Carbon::today();
        $quotation->note = "-";
        $quotation->expired_date = $request->expired_date;
        $quotation->quote_for = $request->type;
        $quotation->level = '1';
        $quotation->estimated_date = $request->estimated_date;
        if ($request->tax != NULL) {
            $quotation->tax = $request->tax;
        } else {
            $quotation->tax = 0;
        }
        $quotation->shipping = $request->shipping ?? 0;
        $quotation->no_quote = $request->no_quote;
        $quotation->title = $request->title;
        $quotation->subtotal = $request->subtotal;
        if ($request->diskon != NULL) {
            $quotation->diskon = $request->diskon;
        } else {
            $quotation->diskon = 0;
        }
        $quotation->fee = 0;
        $quotation->nett = $request->subtotal - $request->diskon;
        $quotation->total_no_tax = $request->total_no_tax;
        $quotation->harga_total = $request->harga_total;
        $quoteSave = $quotation->save();
        if ($quoteSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            $row = 0;
            $subtitleQ = SubtitleQuotation::where('id_quotation', $id)->get();
            foreach ($subtitleQ as $sub) {
                $sub->delete();
            }
            foreach ($request->subTitle as $item => $subtitleValue) {
                $row++;
                $subtitle = new SubtitleQuotation();
                $subtitle->id_quotation = $id;
                $subtitle->subtitle = $subtitleValue; // Menggunakan $subtitleValue langsung
                $subtitleSave = $subtitle->save();

                if (!empty($request->product[$row])) {
                    foreach ($request->product[$row] as $key => $productValue) {
                        $detService = new DetailServiceQuotation();
                        $detService->id_subtitle = $subtitle->id;
                        $detService->product = $productValue; // Ambil produk berdasarkan indeks
                        $detService->detail = $request->detail_product[$row][$key] ?? null; // Pastikan nilai aman
                        $detService->disc = $request->disc[$row][$key] ?? 0; // Default ke 0 jika kosong
                        $detService->qty = $request->qty[$row][$key] ?? 0; // Default ke 0
                        $detService->price = $request->price[$row][$key] ?? 0; // Default ke 0
                        $detService->info_qty = $request->info_qty[$row][$key] ?? null; // Default null
                        $detService->amount = $request->amount[$row][$key] ?? 0; // Default ke 0
                        $detService->save();
                    }
                }
            }
            if ($subtitleSave) {
                // Masukan Data ke dalam Tabel Term n Condition
                $termncon = Termncon::where('id_quotation', $id)->first();
                $termncon->validity = $request->validity;
                $termncon->pricing = $request->pricing;
                $termncon->warranty = $request->warranty;
                $termncon->delivery_process = $request->delivery_process;
                $termncon->payment = $request->payment;
                $termncon->note = $request->note;
                $termnconSave = $termncon->save();

                // $stats = new ChangeStatus;
                // $stats->id_quotation = $quotation->primary_id;
                // $stats->date = Carbon::now();
                // $stats->note = 'Quotation Revision - ' . $lastQuote->num_rev + 1;
                // $stats->status = $quotation->status;
                // $stats->save();
            }
        }
        if ($termnconSave) {
            return redirect('/quote/service-show/' . $quotation->id)->with('message', 'data telah di tambahkan');
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
        $quotation = Quotation::find($id);
        $quote = Quotation::where('primary_id', $quotation->primary_id)->where('num_rev', $quotation->num_rev - 1)->first();
        $quotes = Quotation::where('primary_id', $quotation->primary_id)->where('level', '1')->get();

        $quotation->level = '0';
        foreach ($quotes as $item) {
            $item->is_primary = '0';
            $item->save();
        }

        if (count($quotes) > 1) {
            $quote->is_primary = '1';
            $quote->save();
        }
        $delQuote = $quotation->save();

        if ($delQuote) {
            return 1;
        } else {
            return 0;
        }
    }
    public function print_quote($id)
    {
        $quote = Quotation::where('id', $id)->first();
        $dquote = DetailQuotation::where('id_quotation', $id)->get();
        $tax = ($quote->subtotal - $quote->diskon) * $quote->tax / 100;
        $afterDisc = $quote->subtotal - $quote->diskon;
        // dd($termncon);
        return view("pages.sales.quotation.detail-print", compact('quote', 'dquote', 'tax', 'afterDisc'));
    }

    public function pdf_quote($id)
    {
        $quote = Quotation::where('id', $id)->first();
        $dquote = DetailQuotation::where('id_quotation', $id)->get();
        // return view("pages.sales.quotation.detail-pdf", compact('quote', 'dquote'));
        $pdf = PDF::loadView("pages.sales.quotation.detail-pdf", compact('quote', 'dquote'));
        return $pdf->stream();
        // return $pdf->download('invoice-'.$quote->client->company.'-'.$quote->no_quote.'.pdf');
    }

    public function edit_revisi($id)
    {
        $quotation = Quotation::where('id', $id)->first();
        $dquotation = DetailQuotation::where('id_quotation', $id)->get();
        $client = Client::all();
        $dateNow = Carbon::now();
        $numberQ = Quotation::whereYear('estimated_date', $dateNow)->where('id_sales', Auth::user()->id)->count();
        $formattedNumberQ = str_pad($numberQ + 1, 3, '0', STR_PAD_LEFT);
        $monthNow = $dateNow->month;
        $formattedMonthNow = $this->convertToRoman($monthNow);
        // $pic = Pic::all();
        $pic = client::where('client.id_sales', Auth::user()->id)->get();
        // dd($pic);
        $product = Product::join('serial_product as s', 's.id_product', '=', 'product.id')->get(['s.id', 'product.go', 's.pn', 's.brand', 'product.detail_desc', 'product.unit']);
        $sales = User::where('role', 'sales')->get();
        // dd($dquotation);
        return view('pages.sales.quotation.form', compact('quotation', 'dquotation', 'sales', 'pic', 'formattedNumberQ', 'formattedMonthNow', 'product'));
    }
    public function edit_parts($id)
    {
        $quotation = Quotation::where('id', $id)->first();
        $dquotation = DetailQuotation::where('id_quotation', $id)->get();
        $client = Client::all();
        $dateNow = Carbon::now();
        $numberQ = Quotation::whereYear('estimated_date', $dateNow)->where('id_sales', Auth::user()->id)->count();
        $formattedNumberQ = str_pad($numberQ + 1, 3, '0', STR_PAD_LEFT);
        $monthNow = $dateNow->month;
        $formattedMonthNow = $this->convertToRoman($monthNow);
        // $pic = Pic::all();
        $pic = client::where('client.id_sales', Auth::user()->id)->get();
        // dd($pic);
        $product = Product::join('serial_product as s', 's.id_product', '=', 'product.id')->get(['s.id', 'product.go', 's.pn', 's.brand', 'product.detail_desc', 'product.unit']);
        $sales = User::where('role', 'sales')->get();
        // dd($dquotation);
        return view('pages.sales.quotation.edit', compact('quotation', 'dquotation', 'sales', 'pic', 'formattedNumberQ', 'formattedMonthNow', 'product'));
    }
    public function edit_service($id)
    {
        $quotation = Quotation::find($id);
        $subtitle = SubtitleQuotation::with('detail')->where('id_quotation', $id)->get();
        $dateNow = Carbon::now();
        $numberQ = Quotation::whereYear('estimated_date', $dateNow)->where('id_sales', Auth::user()->id)->count();
        $formattedNumberQ = str_pad($numberQ + 1, 3, '0', STR_PAD_LEFT);
        $monthNow = $dateNow->month;
        $formattedMonthNow = $this->convertToRoman($monthNow);
        $pic = client::where('client.id_sales', Auth::user()->id)->get();
        $pics = Pic::where('id_client', $quotation->pic->id_client)->get();
        // $pic = Pic::join('client', 'client.id', '=', 'id_client')->where('client.id_sales', Auth::user()->id)->get('pic.*');
        $product = Product::join('serial_product as s', 's.id_product', '=', 'product.id')->get(['product.id as comId', 's.id', 'product.go', 's.pn', 's.brand', 'product.detail_desc', 'product.unit']);
        // dd($subtitle);

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
        return view('pages.sales.quotation.service.edit', compact('quotation', 'subtitle', 'pic', 'pics', 'formattedNumberQ', 'formattedMonthNow', 'product'));
    }

    public function change_status($id, Request $request)
    {
        $rule = [
            'status' => 'required',
            'note' => 'required',
        ];
        $message = [
            'status.required' => 'Field Status Wajib Diisi',
            'note.required' => 'Field note Wajib Diisi',
        ];

        $this->validate($request, $rule, $message);

        $quotation = Quotation::find($id);
        $detQuote = DetailQuotation::where('id_quotation', $id)->get();
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $id)->get();
        $allQuote = Quotation::where('primary_id', $quotation->primary_id)->get();
        // dd($allQuote);
        $pic = Pic::where('id', $quotation->id_pic)->first();
        $client = Client::where('id', $pic->id_client)->first();
        foreach ($allQuote as $quote) {
            $quote->status = $request->status;
            $quote->status_date = Carbon::today();
            $quote->note = $request->note;
            $quote->expired_date = Carbon::now()->addMonth();
            if ($request->status == "100") {
                $quote->po_date = Carbon::now();
                $quote->week_po = $request->week;
            }
            $stats = $quote->save();
        }
        if ($request->status == "100") {
            $action = new Activities;
            $action->id_client = $pic->id_client;
            $action->name = 'Follow Up';
            $action->status = 'Responded';
            $action->date = Carbon::now();
            $action->follow_up = Carbon::now()->addDays(14);
            $action->action = 'Phone Office';
            $action->note = 'Done PO';
            $activitiesSave = $action->save();

            if ($client->id_issues != "5") {
                $client->id_issues = '5';
                $client->role = 'Customers';
                $isuSave = $client->save();

                $status = new CrmStatus();
                $status->id_client = $id;
                $status->status = 2;
                $statSave = $status->save();
            }
            // foreach ($detQuote as $item) {
            //     $product = Product::join('serial_product as sp', 'sp.id', '=', 'product.id')->where('sp.id', $item->id_equivalent)->select('product.*')->first();
            //     $product->stock -= $item->qty;
            //     $product->pending_stock += $item->qty;
            //     $product->save();
            // }

            $pending = new PendingPO;
            $pending->status = 0;

            if ($quotation->type == 'Sparepart') {
                $pending->type = "Non Project";
            } else {
                $pending->type = "Project";
            }
            $pending->id_quotation = $quotation->primary_id;
            $pending->no_pending = $request->no_pending;
            $pending->date = Carbon::now();
            $pending->save();

            if ($quotation->type != 'Sparepart') {
                foreach ($subQuote as $subtitle) {
                    foreach ($subtitle->detail as $detail) {
                        $dPending = new DetailPendingPO();
                        $dPending->id_pending = $pending->id;
                        $dPending->id_detail_service = $detail->id;
                        $dPending->save();
                    }
                }
            }

            $status = new ChangeStatus();
            $status->id_pending = $pending->id;
            $status->note = 'Pending Created';
            $status->status = 0;
            $status->date = Carbon::now();
            $status->save();

        }
        $changeStats = new ChangeStatus;
        $changeStats->id_quotation = $quotation->primary_id;
        $changeStats->date = Carbon::now();
        $changeStats->status = $request->status;
        $changeStats->note = $request->note;
        $changeStats->save();
        if ($stats || $activitiesSave || $isuSave) {
            return redirect('/quotation/' . $id)->with("success", "Data Status Quotation Telah Diubah");
        }
    }
    public function prospect_quote()
    {
        return view('pages.sales.quotation.prospect.index');
    }
    public function po_quote()
    {
        $quotation = Quotation::all();
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
        return view('pages.sales.quotation.po.index', compact('noSaleProspect', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'leveledProspect', 'quotation'));
    }
    public function loss_quote()
    {
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
        return view('pages.sales.quotation.loss.index', compact('leveledProspect', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'noSaleProspect'));
    }

    public function sales_quotation($id)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $quotation = Quotation::where('id_sales', $id)->where('level', '1')->where('is_primary', '1')->whereMonth('estimated_date', $monthNow)->get();
        $forecast = Quotation::where('id_sales', $id)->where('level', '1')->where('is_primary', '1')->whereMonth('estimated_date', $monthNow)->whereIn('status', ['20', '30', '40', '60', '80'])->sum('nett');
        $prospect = Quotation::where('id_sales', $id)->where('level', '1')->where('is_primary', '1')->whereMonth('estimated_date', $monthNow)->where('status', '80')->sum('nett');
        $po = Quotation::where('id_sales', $id)->where('level', '1')->where('is_primary', '1')->whereMonth('po_date', $monthNow)->where('status', '100')->sum('nett');
        $loss = Quotation::where('id_sales', $id)->where('level', '1')->where('is_primary', '1')->whereMonth('estimated_date', $monthNow)->where('status', '0')->sum('nett');
        return view('pages.sales.quotation.sales', compact('quotation', 'forecast', 'prospect', 'po', 'loss'));
    }

    public function sales_po($id)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $quotation = Quotation::where('id_sales', $id)->where('level', '1')->where('is_primary', '1')->whereMonth('po_date', $monthNow)->get();
        return view('pages.sales.quotation.po.sales', compact('quotation'));
    }

    public function convert_po(Request $request, $id)
    {
        $rule = [
            // 'poDate' => 'required',
            'note' => 'required',
        ];
        $message = [
            // 'poDate.required' => 'Field Date Wajib Diisi',
            'note.required' => 'Field note Wajib Diisi',
        ];

        $this->validate($request, $rule, $message);
        // dd($request->all());
        $quotation = Quotation::find($id);
        $detQuote = DetailQuotation::where('id_quotation', $id)->get();
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $id)->get();

        $allQuote = Quotation::where('primary_id', $quotation->primary_id)->get();
        $pic = Pic::where('id', $quotation->id_pic)->first();
        $client = Client::where('id', $pic->id_client)->first();
        foreach ($allQuote as $quote) {
            $quote->status = "100";
            $quote->note = $request->note;
            $quote->week_po = $request->week;
            $quote->po_date = $request->po_date;
            $quoteSave = $quote->save();
        }
        if ($client->id_issues != "5") {
            $client->id_issues = '5';
            $client->role = 'Customers';
            $client->save();
            $status = new CrmStatus();
            $status->id_client = $id;
            $status->status = 2;
            $statSave = $status->save();
        }
        $stats = new ChangeStatus;
        $stats->id_quotation = $quotation->primary_id;
        $stats->date = Carbon::now();
        $stats->status = '100';
        $stats->note = $request->note;
        $stats->save();

        // foreach ($detQuote as $item) {
        //     $product = Product::join('serial_product as sp', 'sp.id_product', '=', 'product.id')->where('sp.id', $item->id_equivalent)->select('product.*')->first();
        //     // dd($item->id_equivalent);
        //     $product->stock -= $item->qty;
        //     $product->pending_stock += $item->qty;
        //     $product->save();
        // }

        $pending = new PendingPO;
        $pending->status = 0;
        $pending->id_quotation = $id;
        if ($quotation->type == 'Sparepart') {
            $pending->type = "Non Project";
        } else {
            $pending->type = "Project";
        }
        $pending->charged = $request->charged;
        $pending->title = $request->title;
        $pending->no_pending = $request->no_pending;
        $pending->delivery = $request->ekspidisi;
        $pending->date = Carbon::now();
        $pending->save();

        if ($quotation->type != 'Sparepart') {
            foreach ($subQuote as $subtitle) {
                foreach ($subtitle->detail as $detail) {
                    $dPending = new DetailPendingPO();
                    $dPending->id_pending = $pending->id;
                    $dPending->id_detail_service = $detail->id;
                    $dPending->save();
                }
            }
        }

        $status = new ChangeStatus();
        $status->id_pending = $pending->id;
        $status->note = 'Pending Created';
        $status->status = 0;
        $status->date = Carbon::now();
        $status->save();

        if ($quoteSave) {
            if ($quotation->type == 'Sparepart') {
                return redirect('/quotation/' . $id)->with("success", "data telah ditambahkan");
            } else {
                return redirect('/quote/service-show/' . $id)->with("success", "data telah ditambahkan");
            }
        }
    }
    public function convert_flag(Request $request, $id)
    {
        $quote = Quotation::find($id);
        if ($quote->flag == 'Reftech') {
            $quote->flag = 'Kojisha';
        } elseif ($quote->flag == 'Kojisha') {
            $quote->flag = 'Reftech';
        }
        $quoteSave = $quote->save();
        if ($quoteSave) {
            return 1;
        } else {
            return 0;
        }
    }

    public function insert_fee(Request $request, $id)
    {
        $quote = Quotation::find($id);
        if (!$quote) {
            return redirect()->back()->with('error', 'Quotation not found');
        }
        $subtotal = $quote->subtotal;

        $dQuotes = DetailQuotation::where('id_quotation', $id)->get();
        $quote->fee = $request->total;
        $quote->nett = $subtotal - $request->total;

        foreach ($dQuotes as $index => $dQuote) {
            if (isset($request->fee[$index])) {
                $dQuote->fee = $request->fee[$index];
                $dQuote->save();
            }
        }

        $quoteSave = $quote->save();
        if ($quoteSave) {
            if ($quote->type == 'Sparepart') {
                return redirect('/quotation/' . $id)->with("success", "data telah ditambahkan");
            } else {
                return redirect('/quote/service-show/' . $id)->with("success", "data telah ditambahkan");
            }
        } else {
            return redirect()->back()->with('error', 'Failed to save quotation');
        }
    }
    public function insert_fee_service(Request $request, $id)
    {
        $quote = Quotation::find($id);
        if (!$quote) {
            return redirect()->back()->with('error', 'Quotation not found');
        }
        $subtotal = $quote->subtotal;

        // $dQuotes = SubtitleQuotation::where('id_quotation', $id)->get();
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $id)->get();

        $quote->fee = $request->total;
        $quote->nett = $subtotal - $request->total;

        $row = 0;
        foreach ($request->fee as $detailId => $fee) {
            if (isset($fee)) {
                $item = DetailServiceQuotation::find($detailId);
                if ($item) {
                    $item->fee = $fee;
                    $item->save();
                }
            }
        }

        $quoteSave = $quote->save();

        if ($quoteSave) {
            if ($quote->type == 'Sparepart') {
                return redirect('/quotation/' . $id)->with("success", "data telah ditambahkan");
            } else {
                return redirect('/quote/service-show/' . $id)->with("success", "data telah ditambahkan");
            }
        } else {
            return redirect()->back()->with('error', 'Failed to save quotation');
        }
    }

    public function delete_fee(Request $request, $id)
    {
        $quote = Quotation::find($id);
        $dQuote = DetailQuotation::where('id_quotation', $id)->get();
        foreach ($dQuote as $detail) {
            $detail->fee = 0;
            $detail->save();
        }
        $quote->fee = 0;
        $quote->nett = $quote->subtotal;
        $quoteSave = $quote->save();
        if ($quoteSave) {
            return 1;
        } else {
            return 0;
        }
    }

    public function request_bp(Request $request, $id)
    {
        $quote = Quotation::find($id);
        $invoice = new Invoice;
        $invoice->id_quotation = $id;
        $invoice->no_po = $request->po;
        $invoice->no_invoice = NULL;
        $invoice->flag = $quote->pic->client->info;
        $invoice->type = 'BP';
        $invoice->date = Carbon::today();
        $invoice->term = NULL;
        $invoice->invoiceTo = NULL;
        $invoice->pph = 0;
        $invoice->sign = NULL;
        $invoiceSave = $invoice->save();
        if ($invoiceSave) {
            if ($quote->type == 'Sparepart') {
                return redirect('/quotation/' . $id)->with("success", "data telah ditambahkan");
            } else {
                return redirect('/quote/service-show/' . $id)->with("success", "data telah ditambahkan");
            }
        } else {
            return response()->json(['error' => 'No file uploaded.'], 400);
        }
    }
    public function request_next_invoice(Request $request, $id)
    {
        $quote = Quotation::find($id);
        $payment = Payment::where('id_quotation')->get();
        $invoiceCount = Invoice::where('id_quotation')->count();
        $invoice = new Invoice;
        $invoice->id_quotation = $id;
        $invoice->no_po = $request->po;
        $invoice->no_invoice = NULL;
        $invoice->flag = $quote->pic->client->info;
        if (@$payment[$invoiceCount]) {
            $invoice->type = $payment[$invoiceCount]->type;
        }
        $invoice->type = 'BP';
        $invoice->date = Carbon::today();
        $invoice->term = NULL;
        $invoice->invoiceTo = NULL;
        $invoice->pph = 0;
        $invoice->sign = NULL;
        $invoiceSave = $invoice->save();
        if ($invoiceSave) {
            if ($quote->type == 'Sparepart') {
                return redirect('/quotation/' . $id)->with("success", "data telah ditambahkan");
            } else {
                return redirect('/quote/service-show/' . $id)->with("success", "data telah ditambahkan");
            }
        } else {
            return response()->json(['error' => 'No file uploaded.'], 400);
        }
    }
    public function upload_po(Request $request, $id)
    {
        $quote = Quotation::find($id);

        if (!$quote) {
            return response()->json(['error' => 'Quotation not found.'], 404);
        }

        $client = $quote->pic?->client;
        if (!$client) {
            return redirect()->back()->with('error', 'Data client tidak ditemukan.');
        }

        $npwpClean = preg_replace('/[^a-zA-Z0-9]/', '', $client->npwp ?? '');
        if (strlen($npwpClean) < 14) {
            $errorMsg = 'NPWP client belum diisi atau kurang dari 14 karakter. Pengajuan PO tidak dapat diproses.';
            if ($quote->type == 'Sparepart') {
                return redirect('/quotation/' . $id)->with('error', $errorMsg);
            } else {
                return redirect('/quote/service-show/' . $id)->with('error', $errorMsg);
            }
        }

        // dd($request->file('uploadPO'));
        if ($request->hasFile('uploadPO')) {
            $foto = $request->file('uploadPO');

            // Validate the file to ensure it's a PDF
            $request->validate([
                'uploadPO' => 'required|file|mimes:pdf|max:3060',
            ]);

            // Get file extension
            $file_ext = $foto->getClientOriginalExtension();

            // Sanitize the quote number to create a valid filename
            $sanitized_file_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $quote->no_quote);

            // Construct file name
            $file_name = $sanitized_file_name . '.' . $file_ext;

            // Set upload path
            $upload_path = 'asset/po';

            // Move the file to the upload path
            $foto->move(public_path($upload_path), $file_name);

            // dd(Auth::user()->id);

            // Update the quote with the new file path
            $quote->po_file = $upload_path . '/' . $file_name;
            $quote->upload_date = Carbon::today();
            $quote->save();
            // create invoice quote
            $invoice = new Invoice;
            $invoice->id_quotation = $id;
            $invoice->no_po = $request->po;
            $invoice->flag = $quote->pic->client->info;

            // Jika quotation berasal dari SUO, gunakan no_invoice_booking langsung
            $suo = Suo::where('id_quotation', $id)->whereNotNull('no_invoice_booking')->first();
            if ($suo) {
                $invoice->no_invoice = $suo->no_invoice_booking;
                $invoice->term = 'Cash Before Delivery';
                $invoice->invoiceTo = $quote->destination;
            } elseif (Auth::user()->id == 16 || Auth::user()->id == 23) {
                $invoice->no_invoice = $quote->no_quote;
                $invoice->term = 'Cash Before Delivery';
                $invoice->invoiceTo = $quote->destination;
            } else {
                $invoice->no_invoice = NULL;
                $invoice->term = NULL;
                $invoice->invoiceTo = NULL;
            }
            $firstPayment = Payment::where('id_quotation', $id)->first();
            if ($firstPayment) {
                if ($firstPayment->type == 'CBD' || $firstPayment->type == 'COD' || $firstPayment->percent == 100) {
                    $invoice->type = 'CT';
                } elseif ($firstPayment->type != 'Tempo') {
                    $invoice->type = $firstPayment->type;
                } else {
                    $invoice->type = 'BP';
                }
            } else {
                $invoice->type = 'CT';
            }
            $invoice->date = Carbon::today();
            $invoice->sign = NULL;
            $invoice->pph = 0;
            $invoice->save();
            if ($quote->type == 'Sparepart') {
                return redirect('/quotation/' . $id)->with('message', 'File has Uploaded');
            } else {
                return redirect('/quote/service-show/' . $id)->with('message', 'File has Uploaded');
            }

        } else {
            return response()->json(['error' => 'No file uploaded.'], 400);
        }
    }
    public function download_po($id)
    {
        $quote = Quotation::find($id);

        if (!$quote) {
            return response()->json(['error' => 'Quotation not found.'], 404);
        }

        $file_path = public_path($quote->po_file);

        if (!file_exists($file_path)) {
            return response()->json(['error' => 'File not found at path: ' . $file_path], 404);
        }

        return response()->download($file_path);
    }
    public function delete_po($id)
    {
        try {
            $quote = Quotation::find($id);
            $pending = PendingPO::where('id_quotation', $id)->get();

            if (!$quote) {
                return response()->json(['error' => 'Quotation not found.'], 404);
            }

            $file_path = public_path($quote->po_file);

            if (file_exists($file_path)) {
                $invoices = Invoice::where('id_quotation', $id)->get();
                foreach ($invoices as $invoice) {
                    $invoice->delete(); // Hapus setiap invoice
                }
                unlink($file_path); // Hapus file
                $quote->po_file = null;
                $quote->upload_date = null;
                $quote->save(); // Simpan perubahan

                foreach ($pending as $item) {
                    $item->delete();
                }

                return response()->json(1, 200); // Sukses
            } else {
                return response()->json(['error' => 'File not found.'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Delete PO Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    public function add_payment(Request $request, $id)
    {
        $quote = Quotation::find($id);
        if (!$quote) {
            return redirect('/quotation/' . $id)->with('error', 'Quotation not found.');
        }

        $paymentCount = Payment::where('id_quotation', $id)->count();
        $payment = new Payment;

        // $targetInvoice = $invoice->count() - 1;
        $payment->id_quotation = $id;
        // $payment->file = $upload_path . '/' . $file_name;
        $payment->amount = $request->amount;
        $payment->type = $request->type;
        $payment->method = $request->method;
        if ($request->method == 'Escrow') {
            $payment->level = 1;
        } else {
            $payment->level = 0;
        }
        $payment->percent = $request->percent;
        if ($request->type == 'Tempo') {
            $payment->tempo = $request->tempo;
        }
        $payment->note = $request->note;
        $payment->save();

        // Trigger auto Kanban card creation for Monitoring Document board
        if ($request->method !== 'Escrow') {
            $pendingPO = \App\Models\PendingPO::where('id_quotation', $id)
                ->where('status', '<', 6)
                ->where('created_at', '>=', '2026-07-17 00:00:00')
                ->first();
            if ($pendingPO) {
                $board = \App\Models\KanbanBoard::where('type', 'monitoring')->first();
                if (!$board) {
                    $board = \App\Models\KanbanBoard::create([
                        'title' => 'Monitoring Document',
                        'description' => 'Papan Kanban khusus untuk memantau dokumen PO/Sales Order.',
                        'type' => 'monitoring',
                        'created_by' => 1,
                    ]);

                    $defaultColumns = ['PO REFTECH', 'PO E-COMMERCE', 'Draft / SPK', 'Invoice Sent', 'Paid / Completed'];
                    foreach ($defaultColumns as $index => $colTitle) {
                        \App\Models\KanbanColumn::create([
                            'board_id' => $board->id,
                            'title' => $colTitle,
                            'position' => $index,
                        ]);
                    }
                }
                if ($board) {
                    $cardExists = \App\Models\KanbanTask::where('board_id', $board->id)
                        ->where('pending_po_id', $pendingPO->id)
                        ->exists();
                    
                    if (!$cardExists) {
                        $idSales = $quote->id_sales;
                        $targetColTitle = ($idSales == 16) ? 'PO E-COMMERCE' : 'PO REFTECH';
                        
                        $column = \App\Models\KanbanColumn::where('board_id', $board->id)
                            ->where('title', $targetColTitle)
                            ->first();
                            
                        if ($column) {
                            $pos = \App\Models\KanbanTask::where('column_id', $column->id)->count();
                            
                            $invoicePo = \App\Models\Invoice::where('id_quotation', $id)->whereNotNull('no_po')->where('no_po', '!=', '')->value('no_po');
                            $poNumber = $invoicePo ?: ($pendingPO->no_pending ?? 'No PO');
                            $companyName = 'Unknown Client';
                            if ($quote->pic && $quote->pic->client) {
                                $companyName = $quote->pic->client->company;
                            }
                            
                            $taskTitle = "[$poNumber] - $companyName";
                            
                            \App\Models\KanbanTask::create([
                                'board_id' => $board->id,
                                'column_id' => $column->id,
                                'pending_po_id' => $pendingPO->id,
                                'title' => $taskTitle,
                                'description' => null,
                                'position' => $pos,
                            ]);
                        }
                    }
                }
            }
        }

        $invoice = Invoice::where('id_quotation', $id)->get();
        // dd($request->type);
        $targetInvoice = $invoice[$paymentCount] ?? null;
        if ($targetInvoice != null) {
            if ($request->type == 'CBD' || $request->type == 'COD') {
                $targetInvoice->type = 'CT';
            } elseif ($request->type != 'Tempo') {
                $targetInvoice->type = $request->type;
            } else {
                if ($request->percent == 100) {
                    $targetInvoice->type = 'CT';
                } else {
                    $targetInvoice->type = 'BP';
                }
            }
            $targetInvoice->save();
        }

        $activity = new ChangeStatus();
        $activity->id_user = Auth::user()->id;
        $activity->id_payment = $payment->id;
        $activity->note = "Payment Created By ";
        $activity->status = 0;
        $activity->date = Carbon::now();
        $activity->save();

        if ($quote->type == 'Sparepart') {
            return redirect('/quotation/' . $id)->with('message', 'File has Uploaded');
        } else {
            return redirect('/quote/service-show/' . $id)->with('message', 'File has Uploaded');
        }
    }

    public function proof_payment(Request $request, $id)
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        $quote = Quotation::find($payment->id_quotation);
        if (!$quote) {
            return response()->json(['error' => 'Quotation not found'], 404);
        }

        $paymentCount = Payment::where('id_quotation', $payment->id_quotation)->count();

        if ($request->hasFile('file')) {
            $foto = $request->file('file');

            // Validasi
            $request->validate([
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            // Ekstensi
            $file_ext = $foto->getClientOriginalExtension();

            // Nama file aman
            $sanitized_file_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $quote->no_quote);

            // Susun nama file
            $file_name = $sanitized_file_name . '-' . ($paymentCount + 1) . '.' . $file_ext;

            // Path
            $upload_path = base_path('../public_html/asset/payment');
            $foto->move($upload_path, $file_name);

            // simpan di DB
            $payment->file = 'asset/payment/' . $file_name;
            $payment->save();

            // ✅ Response sukses
            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diupload',
                'file_url' => asset($payment->file),
                'payment_id' => $payment->id,
            ], 200);
        } else {
            return response()->json(['error' => 'No file uploaded.'], 400);
        }
    }

    public function download_payment($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['error' => 'Quotation not found.'], 404);
        }

        $file_path = public_path($payment->file);

        if (!file_exists($file_path)) {
            return response()->json(['error' => 'File not found at path: ' . $file_path], 404);
        }

        return response()->download($file_path);
    }

    public function delete_payment($id)
    {
        $payment = Payment::find($id);
        $invoice = Invoice::where('id_quotation', $payment->id_quotation)->first();

        if (!$payment) {
            return response()->json(['error' => 'Quotation not found.'], 404);
        }
        $paymentDel = $payment->delete();
        if ($invoice) {
            $invoice->type = 'CT';
            $invoice->save();
        }

        // $file_path = public_path($payment->file);

        // if (file_exists($file_path)) {
        //     unlink($file_path);
        //     $payment->delete();
        //     return 1;
        if ($paymentDel) {
            return 1;
        } else {
            return 0;
        }
    }

    public function confirm_payment(Request $request, $id)
    {
        $payment = Payment::find($id);
        $payment->date_confirm = $request->date;
        $payment->level = 1;
        $paymentSave = $payment->save();
        if ($paymentSave) {
            return redirect('/payment-detail/payment/' . $id)->with('success', 'Data telah ditambahkan');
        } else {
            return 0;
        }
    }

    public function cancel_po($id)
    {
        $quote = Quotation::find($id);

        if (!$quote) {
            return response()->json(['error' => 'Quotation not found'], 404);
        }
        $invoices = Invoice::where('id_quotation', $id)->get();
        $deliveries = Delivery::whereIn('id_invoice', $invoices->pluck('id'))->get();
        $detDeliveries = DetailDelivery::whereIn('id_delivery', $deliveries->pluck('id'))->get();
        $pendings = PendingPO::where('id_quotation', $id)->get();

        // Edit Quotation
        $quote->status = '80';
        $quote->po_date = NULL;
        if ($quote->po_file != NULL) {
            $file_path = public_path($quote->po_file);
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        $quote->po_file = NULL;
        $status = $quote->save();

        // dd($status);
        // Hapus Detail Delivery
        foreach ($detDeliveries as $detDelivery) {
            $detDelivery->delete();
        }

        // Hapus Delivery
        foreach ($deliveries as $delivery) {
            $delivery->delete();
        }

        // Hapus Invoice
        foreach ($invoices as $invoice) {
            $invoice->delete();
        }

        // Hapus Pending
        foreach ($pendings as $pending) {
            $pending->delete();
        }

        if ($status) {
            return 1;
        } else {
            return 0;
        }
    }

    public function add_mention(Request $request, $id)
    {
        $quote = Quotation::find($id);
        $quote->mention = $request->mention;
        $quote->note = $request->note;
        $status = $quote->save();
        if ($status) {
            if ($quote->type == 'Sparepart') {
                return redirect('/quotation/' . $id)->with('message', 'File has Uploaded');
            } else {
                return redirect('/quote/service-show/' . $id)->with('message', 'File has Uploaded');
            }
        }
    }

    public function change_primary(Request $request, $id)
    {
        $quote = Quotation::find($id);
        $quotations = Quotation::where("primary_id", $quote->primary_id)->get();
        foreach ($quotations as $quotation) {
            $quotation->is_primary = '0';
            $quotation->save();
        }
        $quote->is_primary = '1';
        $quoteSave = $quote->save();
        if ($quoteSave) {
            return response()->json(['success' => 'Status berhasil diperbarui']);
        } else {
            return response()->json(['error' => 'Gagal menyimpan perubahan status'], 500);
        }
    }

    public function change_po(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        $quote = Quotation::find($invoice->id_quotation);
        $invoice->no_po = $request->po;
        $invoiceSave = $invoice->save();
        if ($invoiceSave) {
            if ($quote->type == 'Sparepart') {
                return redirect('/quotation/' . $quote->id)->with('message', 'File has Uploaded');
            } else {
                return redirect('/quote/service-show/' . $quote->id)->with('message', 'File has Uploaded');
            }
        }
    }
    public function add_comment(Request $request, $id)
    {
        $previousUrl = request()->create(url()->previous())->segment(2);
        $quotation = Quotation::find($id);
        $stats = ChangeStatus::where('id_quotation', $quotation->primary_id)->orderByDesc('date')->first();
        $comment = new Comment;
        $comment->id_status = $stats->id;
        // $comment->id_prospect = NULL;
        $comment->id_user = Auth::user()->id;
        $comment->date = Carbon::now();
        $comment->comment = $request->comment;
        $comment->level = '1';
        // $comment->type = 'quotation';
        $commentSave = $comment->save();
        if ($previousUrl == 'unit-detail') {
            if ($commentSave) {
                return redirect('/quote/unit-detail/' . $quotation->id . '#viewComment')->with('message', 'data telah di tambahkan');
            }
        } elseif ($previousUrl == 'quotation') {
            if ($commentSave) {
                return redirect('/quotation/' . $id . '#viewComment')->with('massage', 'Data berhasil di buat');
            }
        } else {
            if ($commentSave) {
                return redirect('/quote/service-show/' . $id . '#viewComment')->with('massage', 'Data berhasil di buat');
            }
        }
    }
    public function view_comment($id)
    {
        $comment = Comment::find($id);

        if ($comment) {
            $comment->level = '2';
            $comment->save();

            // Return response JSON
            return response()->json(['message' => 'Comment updated successfully!']);
        } else {
            // Return error jika tidak ditemukan
            return response()->json(['message' => 'Comment not found'], 404);
        }
    }

    public function replacementDetailSparepart($id)
    {
        $sProd = SerialProduct::join('product as p', 'p.id', '=', 'serial_product.id_product')
            ->where('serial_product.id', $id)
            ->get(['p.description AS detail', 'serial_product.price']);
        return response()->json($sProd);
    }
    public function replacementDetailUnit($id)
    {

        $saproduct = SerialProduct::where('serial_product.id', $id)
            ->get(['serial_product.detail', 'serial_product.price']);
        return response()->json($saproduct);
    }

    public function quotationUnit()
    {

        $quotation = Quotation::where('id_sales', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->get();
        $forecast = Quotation::where('id_sales', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->whereIn('status', ['20', '30', '40', '60', '80'])->where('type', 'Unit')->sum('nett');
        $prospect = Quotation::where('id_sales', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->where('status', '80')->where('type', 'Unit')->sum('nett');
        $po = Quotation::where('id_sales', Auth::user()->id)->where('status', '100')->where('level', '1')->where('is_primary', '1')->where('type', 'Unit')->sum('nett');
        $loss = Quotation::where('id_sales', Auth::user()->id)->where('status', '0')->where('level', '1')->where('is_primary', '1')->where('type', 'Unit')->sum('nett');
        $quotationAdmin = Quotation::get();
        $forecastAdmin = Quotation::whereIn('status', ['20', '30', '40', '60', '80'])->where('level', '1')->where('is_primary', '1')->where('type', 'Unit')->sum('nett');
        $prospectAdmin = Quotation::where('status', '80')->where('level', '1')->where('is_primary', '1')->where('type', 'Unit')->sum('nett');
        $poAdmin = Quotation::where('status', '100')->where('level', '1')->where('is_primary', '1')->where('type', 'Unit')->sum('nett');
        $lossAdmin = Quotation::where('status', '0')->where('level', '1')->where('is_primary', '1')->where('type', 'Unit')->sum('nett');
        // dd();
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
        return view('pages.sales.quotation.unit.index', compact('noSaleProspect', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'leveledProspect', 'quotation', 'forecast', 'prospect', 'po', 'loss', 'quotationAdmin', 'forecastAdmin', 'prospectAdmin', 'poAdmin', 'lossAdmin'));
    }

    public function quotationCreateUnit()
    {
        $dateNow = Carbon::now();
        $numberQ = Quotation::whereYear('estimated_date', $dateNow)->where('id_sales', Auth::user()->id)->count();
        $formattedNumberQ = str_pad($numberQ + 1, 3, '0', STR_PAD_LEFT);
        $monthNow = $dateNow->month;
        $formattedMonthNow = $this->convertToRoman($monthNow);
        $pic = client::where('client.id_sales', Auth::user()->id)->get();
        $sales = User::where('role', 'sales')->get();
        $product = Unit::join('serial_product as s', 's.id_product', '=', 'unit.id')->get(['unit.id as comId', 'unit.sku', 's.id', 's.pn', 's.brand']);
        // dd($product);


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
        return view('pages.sales.quotation.unit.form', compact('pic', 'sales', 'formattedNumberQ', 'formattedMonthNow', 'product'));
    }

    public function createService()
    {
        $dateNow = Carbon::now();
        $numberQ = Quotation::whereYear('estimated_date', $dateNow)->where('id_sales', Auth::user()->id)->count();
        $formattedNumberQ = str_pad($numberQ + 1, 3, '0', STR_PAD_LEFT);
        $monthNow = $dateNow->month;
        $formattedMonthNow = $this->convertToRoman($monthNow);
        $pic = client::where('client.id_sales', Auth::user()->id)->get();
        // $pic = Pic::join('client', 'client.id', '=', 'id_client')->where('client.id_sales', Auth::user()->id)->get('pic.*');
        $sales = User::where('role', 'sales')->get();
        $product = Product::join('serial_product as s', 's.id_product', '=', 'product.id')->get(['product.id as comId', 's.id', 'product.go', 's.pn', 's.brand', 'product.detail_desc']);
        // dd($product);


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
        return view('pages.sales.quotation.service.form', compact('pic', 'sales', 'formattedNumberQ', 'formattedMonthNow', 'product'));
    }
    public function storeService(Request $request)
    {
        // dd($request->all());
        $pic = Pic::find($request->pic);
        $client = Client::find($pic->id_client);
        $rule = [
            'no_quote' => 'required',
            'title' => 'required',
            'product' => 'required',
            'detail_product' => 'required',
            'expired_date' => 'required',
            'validity' => 'required',
            'pricing' => 'required',
            'delivery_process' => 'required',
            'payment' => 'required',
        ];
        $message = [
            'no_quote.required' => 'Field No Quote Wajib Diisi',
            'title.required' => 'Field Title Wajib Diisi',
            'product.required' => 'Field Product Wajib Diisi',
            'detail_product.required' => 'Field Detail Product Wajib Diisi',
            'expired_date.required' => 'Wajib isi Expired Date',
            'termcon.required' => 'Field Term and Conditions Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        $previousUrl = request()->create(url()->previous())->segment(2);
        // Masukan Data ke Tabel Quotataion
        $quotation = new Quotation();
        $quotation->id_pic = $request->pic;
        $quotation->id_sales = $request->id_sales;
        $quotation->id_service = NULL;
        $quotation->id_support = $client->id_support;
        $quotation->is_primary = "1";
        $quotation->primary_id = 0;
        $quotation->num_rev = 0;
        $quotation->destination = $request->destination;
        $quotation->week = $request->week;
        $quotation->no_pr = NULL;
        $quotation->status = "20";
        $quotation->status_date = Carbon::today();
        $quotation->note = "-";
        $quotation->expired_date = $request->expired_date;
        $quotation->po_date = NULL;
        $quotation->po_file = NULL;
        $quotation->quote_for = $request->type;
        $quotation->type = 'Service';
        $quotation->level = '1';
        $quotation->estimated_date = $request->estimated_date;
        if ($request->tax != NULL) {
            $quotation->tax = $request->tax;
        } else {
            $quotation->tax = 0;
        }
        $quotation->shipping = $request->shipping ?? 0;
        $quotation->no_quote = $request->no_quote;
        $quotation->title = $request->title;
        $quotation->subtotal = $request->subtotal;
        if ($request->diskon != NULL) {
            $quotation->diskon = $request->diskon;
        } else {
            $quotation->diskon = 0;
        }
        $quotation->fee = 0;
        $quotation->nett = $request->subtotal - $request->diskon;
        $quotation->total_no_tax = $request->total_no_tax;
        $quotation->flag = $client->info;
        $quotation->harga_total = $request->harga_total;
        $quoteSave = $quotation->save();
        $quotation->primary_id = $quotation->id;
        $quotation->save();
        if ($quoteSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            $row = 0;

            foreach ($request->subTitle as $item => $subtitleValue) {
                $row++;
                $subtitle = new SubtitleQuotation();
                $subtitle->id_quotation = $quotation->id;
                $subtitle->subtitle = $subtitleValue; // Menggunakan $subtitleValue langsung
                $subtitleSave = $subtitle->save();

                if (!empty($request->product[$row])) {
                    foreach ($request->product[$row] as $key => $productValue) {
                        $detService = new DetailServiceQuotation();
                        $detService->id_subtitle = $subtitle->id;
                        $detService->product = $productValue; // Ambil produk berdasarkan indeks
                        $detService->detail = $request->detail_product[$row][$key] ?? null; // Pastikan nilai aman
                        $detService->disc = $request->disc[$row][$key] ?? 0; // Default ke 0 jika kosong
                        $detService->qty = $request->qty[$row][$key] ?? 0; // Default ke 0
                        $detService->price = $request->price[$row][$key] ?? 0; // Default ke 0
                        $detService->info_qty = $request->info_qty[$row][$key] ?? null; // Default null
                        $detService->amount = $request->amount[$row][$key] ?? 0; // Default ke 0
                        $detService->save();
                    }
                }
            }
            $stats = new ChangeStatus;
            $stats->id_quotation = $quotation->id;
            $stats->date = Carbon::now();
            $stats->note = 'Quotation has been created';
            $stats->status = "10";
            $stats->save();
            if ($subtitleSave) {
                // Masukan Data ke dalam Tabel Term n Condition
                $termncon = new Termncon;
                $termncon->id_quotation = $quotation->id;
                $termncon->validity = $request->validity;
                $termncon->pricing = $request->pricing;
                $termncon->warranty = $request->warranty;
                $termncon->delivery_process = $request->delivery_process;
                $termncon->payment = $request->payment;
                $termncon->note = $request->note;
                $termnconSave = $termncon->save();
            }
        }
        if ($termnconSave) {
            return redirect('/quote/service-show/' . $quotation->id)->with('message', 'data telah di tambahkan');
        }
    }
    public function updateService(Request $request, $id)
    {
        // dd($request->all());
        $quote = Quotation::find($id);
        $lastQuote = Quotation::where('primary_id', $quote->primary_id)->orderByDesc('num_rev')->first();
        $pic = Pic::find($request->id_pic);
        $client = Client::find($pic->id_client);
        $rule = [
            'no_quote' => 'required',
            'title' => 'required',
            'product' => 'required',
            'detail_product' => 'required',
            'expired_date' => 'required',
            'validity' => 'required',
            'pricing' => 'required',
            'delivery_process' => 'required',
            'payment' => 'required',
        ];
        $message = [
            'no_quote.required' => 'Field No Quote Wajib Diisi',
            'title.required' => 'Field Title Wajib Diisi',
            'product.required' => 'Field Product Wajib Diisi',
            'detail_product.required' => 'Field Detail Product Wajib Diisi',
            'expired_date.required' => 'Wajib isi Expired Date',
            'termcon.required' => 'Field Term and Conditions Wajib Diisi',
        ];
        // dd($quote->primary_id);
        $quote->is_primary = '0';
        $quote->save();

        $this->validate($request, $rule, $message);
        // Masukan Data ke Tabel Quotataion
        $quotation = new Quotation();
        $quotation->id_pic = $request->id_pic;
        $quotation->id_sales = $request->id_sales;
        $quotation->id_service = NULL;
        $quotation->id_support = $client->id_support;
        $quotation->primary_id = $quote->primary_id;
        $quotation->is_primary = '1';
        $quotation->num_rev = $lastQuote->num_rev + 1;
        $quotation->destination = $quote->destination;
        $quotation->no_pr = NULL;
        $quotation->status = "20";
        $quotation->status_date = Carbon::today();
        $quotation->note = "-";
        $quotation->expired_date = $request->expired_date;
        $quotation->type = $quote->type;
        $quotation->quote_for = $request->type;
        $quotation->po_date = NULL;
        $quotation->po_file = NULL;
        $quotation->level = '1';
        $quotation->estimated_date = $request->estimated_date;
        if ($request->tax != NULL) {
            $quotation->tax = $request->tax;
        } else {
            $quotation->tax = 0;
        }
        $quotation->shipping = $request->shipping ?? 0;
        $quotation->no_quote = $request->no_quote;
        $quotation->title = $request->title;
        $quotation->subtotal = $request->subtotal;
        if ($request->diskon != NULL) {
            $quotation->diskon = $request->diskon;
        } else {
            $quotation->diskon = 0;
        }
        $quotation->fee = 0;
        $quotation->nett = $request->subtotal - $request->diskon;
        $quotation->total_no_tax = $request->total_no_tax;
        $quotation->harga_total = $request->harga_total;
        $quoteSave = $quotation->save();
        if ($quoteSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            $row = 0;

            foreach ($request->subTitle as $item => $subtitleValue) {
                $row++;
                $subtitle = new SubtitleQuotation();
                $subtitle->id_quotation = $quotation->id;
                $subtitle->subtitle = $subtitleValue; // Menggunakan $subtitleValue langsung
                $subtitleSave = $subtitle->save();

                if (!empty($request->product[$row])) {
                    foreach ($request->product[$row] as $key => $productValue) {
                        $detService = new DetailServiceQuotation();
                        $detService->id_subtitle = $subtitle->id;
                        $detService->product = $productValue; // Ambil produk berdasarkan indeks
                        $detService->detail = $request->detail_product[$row][$key] ?? null; // Pastikan nilai aman
                        $detService->disc = $request->disc[$row][$key] ?? 0; // Default ke 0 jika kosong
                        $detService->qty = $request->qty[$row][$key] ?? 0; // Default ke 0
                        $detService->price = $request->price[$row][$key] ?? 0; // Default ke 0
                        $detService->info_qty = $request->info_qty[$row][$key] ?? null; // Default null
                        $detService->amount = $request->amount[$row][$key] ?? 0; // Default ke 0
                        $detService->save();
                    }
                }
            }
            if ($subtitleSave) {
                // Masukan Data ke dalam Tabel Term n Condition
                $termncon = new Termncon;
                $termncon->id_quotation = $quotation->id;
                $termncon->validity = $request->validity;
                $termncon->pricing = $request->pricing;
                $termncon->warranty = $request->warranty;
                $termncon->delivery_process = $request->delivery_process;
                $termncon->payment = $request->payment;
                $termncon->note = $request->note;
                $termnconSave = $termncon->save();

                $stats = new ChangeStatus;
                $stats->id_quotation = $quotation->primary_id;
                $stats->date = Carbon::now();
                $stats->note = 'Quotation Revision - ' . $lastQuote->num_rev + 1;
                $stats->status = $quotation->status;
                $stats->save();
            }
        }
        if ($termnconSave) {
            return redirect('/quote/service-show/' . $quotation->id)->with('message', 'data telah di tambahkan');
        }
    }

    public function revisionService($id)
    {
        $quotation = Quotation::find($id);
        $subtitle = SubtitleQuotation::with('detail')->where('id_quotation', $id)->get();
        $dateNow = Carbon::now();
        $numberQ = Quotation::whereYear('estimated_date', $dateNow)->where('id_sales', Auth::user()->id)->count();
        $formattedNumberQ = str_pad($numberQ + 1, 3, '0', STR_PAD_LEFT);
        $monthNow = $dateNow->month;
        $formattedMonthNow = $this->convertToRoman($monthNow);
        $pic = client::where('client.id_sales', Auth::user()->id)->get();
        // $pic = Pic::join('client', 'client.id', '=', 'id_client')->where('client.id_sales', Auth::user()->id)->get('pic.*');
        $product = Product::join('serial_product as s', 's.id_product', '=', 'product.id')->get(['product.id as comId', 's.id', 'product.go', 's.pn', 's.brand', 'product.detail_desc']);
        // dd($subtitle);

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
        return view('pages.sales.quotation.service.form', compact('quotation', 'subtitle', 'pic', 'formattedNumberQ', 'formattedMonthNow', 'product'));
    }
    public function showService($id)
    {
        $totalAmount = 0;
        $dateNow = Carbon::now();
        $quote = Quotation::find($id);
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $id)->get();
        $totalDisc = 0;

        foreach ($subQuote as $subtitle) {
            foreach ($subtitle->detail as $detail) {
                $amountBeforeDisc = $detail->amount / (1 - ($detail->disc / 100));
                // dd($amountBeforeDisc);
                $totalDisc += ($amountBeforeDisc * $detail->disc / 100);
            }
        }
        $quotations = Quotation::where('primary_id', $quote->primary_id)->get();
        // dd($quote);
        $lastQuote = Quotation::where('primary_id', $quote->primary_id)->orderByDesc('num_rev')->first();
        $primQuote = Quotation::where('primary_id', $quote->primary_id)->where('is_primary', '1')->first();
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

        $numberSP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $dateNow)->where('q.tax', '11')->where('contract.type', 'Selling')->groupBy('contract.id')->get('contract.id');
        $numberSNP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $dateNow)->where('q.tax', '0')->where('contract.type', 'Selling')->groupBy('contract.id')->get('contract.id');
        $numberCP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $dateNow)->where('q.tax', '11')->where('contract.type', 'Order')->groupBy('contract.id')->get('contract.id');
        $numberCNP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $dateNow)->where('q.tax', '0')->where('contract.type', 'Order')->groupBy('contract.id')->get('contract.id');
        $formattedNumberSP = str_pad($numberSP->count() + 1, 3, '0', STR_PAD_LEFT);
        $formattedNumberSNP = str_pad($numberSNP->count() + 1, 3, '0', STR_PAD_LEFT);
        $formattedNumberCP = str_pad($numberCP->count() + 1, 3, '0', STR_PAD_LEFT);
        $formattedNumberCNP = str_pad($numberCNP->count() + 1, 3, '0', STR_PAD_LEFT);
        $invoice = Invoice::where('id_quotation', $id)->get();

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
        $countPending = PendingPO::whereYear('created_at', $thisYear)->count();
        $nextNumber = $countPending + 1;

        $noPending = 'SO-' . $thisYear . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();
        return view("pages.sales.quotation.service.detail", compact('noPending', 'totalDisc', 'quote', 'status', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'leveledProspect', 'noSaleProspect', 'lastQuote', 'primQuote', 'quotations', 'subQuote', 'admin', 'noQuote', 'thisYear', 'tax', 'formattedNumberSP', 'formattedNumberSNP', 'formattedNumberCP', 'formattedNumberCNP', 'invoice', 'payments', 'remaining', 'afterDisc'));
    }
    public function destroyService($id)
    {
        $quotation = Quotation::find($id);
        $quote = Quotation::where('primary_id', $quotation->primary_id)->where('num_rev', $quotation->num_rev - 1)->first();
        $quotes = Quotation::where('primary_id', $quotation->primary_id)->where('level', '1')->get();

        $quotation->level = '0';
        foreach ($quotes as $item) {
            $item->is_primary = '0';
            $item->save();
        }

        if (count($quotes) > 1) {
            $quote->is_primary = '1';
            $quote->save();
        }
        $delQuote = $quotation->save();

        if ($delQuote) {
            return 1;
        } else {
            return 0;
        }
    }
    public function printService($id)
    {
        $quote = Quotation::where('id', $id)->first();
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $id)->get();
        $tax = ($quote->subtotal - $quote->diskon) * $quote->tax / 100;
        $afterDisc = $quote->subtotal - $quote->diskon;
        $totalDisc = 0;

        foreach ($subQuote as $subtitle) {
            foreach ($subtitle->detail as $detail) {
                $amountBeforeDisc = $detail->amount / (1 - ($detail->disc / 100));
                $totalDisc += ($amountBeforeDisc * $detail->disc / 100);
            }
        }
        // dd($termncon);
        return view("pages.sales.quotation.service.detail-print", compact('totalDisc', 'quote', 'subQuote', 'tax', 'afterDisc'));
    }
    public function printNoImageService($id)
    {
        $quote = Quotation::where('id', $id)->first();
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $id)->get();
        $tax = ($quote->subtotal - $quote->diskon) * $quote->tax / 100;
        $afterDisc = $quote->subtotal - $quote->diskon;
        $totalDisc = 0;

        foreach ($subQuote as $subtitle) {
            foreach ($subtitle->detail as $detail) {
                $amountBeforeDisc = $detail->amount / (1 - ($detail->disc / 100));
                $totalDisc += ($amountBeforeDisc * $detail->disc / 100);
            }
        }
        // dd($termncon);
        return view("pages.sales.quotation.service.detail-print-no-image", compact('totalDisc', 'quote', 'subQuote', 'tax', 'afterDisc'));
    }

    public function loss_expired()
    {
        $quote = Quotation::where('id_sales', Auth::user()->id)->get();
        foreach ($quote as $item) {
            if ($item->expired_date < Carbon::today()) {
                $item->status = '0';
                $item->save();
            }
        }
    }

    public function chooseMachine(Request $request)
    {
        $machine = MachineTemplate::find($request->machine);
        // dd($machine);
        return redirect('/quote/overhaul-create/' . $request->machine);
    }
    public function createOverhaul(Request $request, $id)
    {

        $machine = MachineTemplate::find($id);
        $subTemplate = SubtitleTemplate::with('detail')->where('id_machine', $id)->get();

        $dateNow = Carbon::now();
        $numberQ = Quotation::whereYear('estimated_date', $dateNow)->where('id_sales', Auth::user()->id)->count();
        $formattedNumberQ = str_pad($numberQ + 1, 3, '0', STR_PAD_LEFT);
        $monthNow = $dateNow->month;
        $formattedMonthNow = $this->convertToRoman($monthNow);
        $pic = client::where('client.id_sales', Auth::user()->id)->get();
        // $pic = Pic::join('client', 'client.id', '=', 'id_client')->where('client.id_sales', Auth::user()->id)->get('pic.*');
        $sales = User::where('role', 'sales')->get();
        // dd($product);
        $totalPrice = 0;
        $totalDisc = 0;
        foreach ($subTemplate as $subtitle) {
            foreach ($subtitle->detail as $detail) {
                $totalPrice += $detail->amount;
                $totalDisc += ($detail->amount * $detail->disc / 100);
            }
        }
        $subtotal = $totalPrice - $totalDisc;
        // dd($subtitle);
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
        return view('pages.sales.quotation.overhaul.form', compact('totalDisc', 'totalPrice', 'subtotal', 'machine', 'subTemplate', 'pic', 'sales', 'formattedNumberQ', 'formattedMonthNow'));
    }
    public function storeOverhaul(Request $request)
    {
        // dd($request->all());
        $pic = Pic::find($request->pic);
        $client = Client::find($pic->id_client);
        $rule = [
            'no_quote' => 'required',
            'title' => 'required',
            'product' => 'required',
            'detail_product' => 'required',
            'expired_date' => 'required',
            'validity' => 'required',
            'pricing' => 'required',
            'delivery_process' => 'required',
            'payment' => 'required',
        ];
        $message = [
            'no_quote.required' => 'Field No Quote Wajib Diisi',
            'title.required' => 'Field Title Wajib Diisi',
            'product.required' => 'Field Product Wajib Diisi',
            'detail_product.required' => 'Field Detail Product Wajib Diisi',
            'expired_date.required' => 'Wajib isi Expired Date',
            'termcon.required' => 'Field Term and Conditions Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        // dd($request->all());
        $previousUrl = request()->create(url()->previous())->segment(2);
        // Masukan Data ke Tabel Quotataion
        $quotation = new Quotation();
        $quotation->id_pic = $request->pic;
        $quotation->id_sales = $request->id_sales;
        $quotation->id_service = NULL;
        $quotation->id_support = $client->id_support;
        $quotation->is_primary = "1";
        $quotation->primary_id = 0;
        $quotation->num_rev = 0;
        $quotation->destination = $request->destination;
        $quotation->week = $request->week;
        $quotation->no_pr = NULL;
        $quotation->status = "20";
        $quotation->status_date = Carbon::today();
        $quotation->note = "-";
        $quotation->expired_date = $request->expired_date;
        $quotation->po_date = NULL;
        $quotation->po_file = NULL;
        $quotation->quote_for = $request->type;
        $quotation->type = 'Overhaul';
        $quotation->level = '1';
        $quotation->estimated_date = $request->estimated_date;
        if ($request->tax != NULL) {
            $quotation->tax = $request->tax;
        } else {
            $quotation->tax = 0;
        }
        $quotation->shipping = $request->shipping ?? 0;
        $quotation->no_quote = $request->no_quote;
        $quotation->title = $request->title;
        $quotation->subtotal = $request->subtotal;
        if ($request->diskon != NULL) {
            $quotation->diskon = $request->diskon;
        } else {
            $quotation->diskon = 0;
        }
        $quotation->fee = 0;
        $quotation->nett = $request->subtotal - $request->diskon;
        $quotation->total_no_tax = $request->total_no_tax;
        $quotation->flag = $client->info;
        $quotation->harga_total = $request->harga_total;
        $quoteSave = $quotation->save();
        $quotation->primary_id = $quotation->id;
        $quotation->save();
        if ($quoteSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            $row = 0;

            foreach ($request->subTitle as $item => $subtitleValue) {
                $row++;
                $subtitle = new SubtitleQuotation();
                $subtitle->id_quotation = $quotation->id;
                $subtitle->subtitle = $subtitleValue; // Menggunakan $subtitleValue langsung
                $subtitleSave = $subtitle->save();

                if (!empty($request->product[$row])) {
                    foreach ($request->product[$row] as $key => $productValue) {
                        $detService = new DetailServiceQuotation();
                        $detService->id_subtitle = $subtitle->id;
                        $detService->product = $productValue; // Ambil produk berdasarkan indeks
                        $detService->detail = $request->detail_product[$row][$key] ?? null; // Pastikan nilai aman
                        $detService->disc = $request->disc[$row][$key] ?? 0; // Default ke 0 jika kosong
                        $detService->qty = $request->qty[$row][$key] ?? 0; // Default ke 0
                        $detService->price = $request->price[$row][$key] ?? 0; // Default ke 0
                        $detService->info_qty = $request->info_qty[$row][$key] ?? null; // Default null
                        $detService->amount = $request->amount[$row][$key] ?? 0; // Default ke 0
                        $detService->save();
                    }
                }
            }
            $stats = new ChangeStatus;
            $stats->id_quotation = $quotation->id;
            $stats->date = Carbon::now();
            $stats->note = 'Quotation has been created';
            $stats->status = "10";
            $stats->save();
            if ($subtitleSave) {
                // Masukan Data ke dalam Tabel Term n Condition
                $termncon = new Termncon;
                $termncon->id_quotation = $quotation->id;
                $termncon->validity = $request->validity;
                $termncon->pricing = $request->pricing;
                $termncon->warranty = $request->warranty;
                $termncon->delivery_process = $request->delivery_process;
                $termncon->payment = $request->payment;
                $termncon->note = $request->note;
                $termnconSave = $termncon->save();
            }
        }
        if ($termnconSave) {
            return redirect('/quote/overhaul-show/' . $quotation->id)->with('message', 'data telah di tambahkan');
        }
    }

    public function showOverhaul($id)
    {
        $totalAmount = 0;
        $dateNow = Carbon::now();
        $quote = Quotation::find($id);
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $id)->get();
        $totalDisc = 0;

        foreach ($subQuote as $subtitle) {
            foreach ($subtitle->detail as $detail) {
                $amountBeforeDisc = $detail->amount / (1 - ($detail->disc / 100));
                $totalDisc += ($amountBeforeDisc * $detail->disc / 100);
            }
        }
        $quotations = Quotation::where('primary_id', $quote->primary_id)->get();
        // dd($quote);
        $lastQuote = Quotation::where('primary_id', $quote->primary_id)->orderByDesc('num_rev')->first();
        $primQuote = Quotation::where('primary_id', $quote->primary_id)->where('is_primary', '1')->first();
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

        $numberSP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $dateNow)->where('q.tax', '11')->where('contract.type', 'Selling')->groupBy('contract.id')->get('contract.id');
        $numberSNP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $dateNow)->where('q.tax', '0')->where('contract.type', 'Selling')->groupBy('contract.id')->get('contract.id');
        $numberCP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $dateNow)->where('q.tax', '11')->where('contract.type', 'Order')->groupBy('contract.id')->get('contract.id');
        $numberCNP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $dateNow)->where('q.tax', '0')->where('contract.type', 'Order')->groupBy('contract.id')->get('contract.id');
        $formattedNumberSP = str_pad($numberSP->count() + 1, 3, '0', STR_PAD_LEFT);
        $formattedNumberSNP = str_pad($numberSNP->count() + 1, 3, '0', STR_PAD_LEFT);
        $formattedNumberCP = str_pad($numberCP->count() + 1, 3, '0', STR_PAD_LEFT);
        $formattedNumberCNP = str_pad($numberCNP->count() + 1, 3, '0', STR_PAD_LEFT);
        $invoice = Invoice::where('id_quotation', $id)->get();

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
        $countPending = PendingPO::whereYear('created_at', $thisYear)->count();
        $nextNumber = $countPending + 1;

        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();
        $noPending = 'SO-' . $thisYear . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        return view("pages.sales.quotation.overhaul.detail", compact('noPending', 'totalDisc', 'quote', 'status', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'leveledProspect', 'noSaleProspect', 'lastQuote', 'primQuote', 'quotations', 'subQuote', 'admin', 'noQuote', 'thisYear', 'tax', 'formattedNumberSP', 'formattedNumberSNP', 'formattedNumberCP', 'formattedNumberCNP', 'invoice', 'payments', 'remaining', 'afterDisc'));
    }
    public function printOverhaul($id)
    {
        $quote = Quotation::where('id', $id)->first();
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $id)->get();
        $tax = ($quote->subtotal - $quote->diskon) * $quote->tax / 100;
        $afterDisc = $quote->subtotal - $quote->diskon;
        $totalDisc = 0;

        foreach ($subQuote as $subtitle) {
            foreach ($subtitle->detail as $detail) {
                $amountBeforeDisc = $detail->amount / (1 - ($detail->disc / 100));
                $totalDisc += ($amountBeforeDisc * $detail->disc / 100);
            }
        }
        // dd($termncon);
        return view("pages.sales.quotation.overhaul.detail-print", compact('totalDisc', 'quote', 'subQuote', 'tax', 'afterDisc'));
    }
    public function printNoImageOverhaul($id)
    {
        $quote = Quotation::where('id', $id)->first();
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $id)->get();
        $tax = ($quote->subtotal - $quote->diskon) * $quote->tax / 100;
        $afterDisc = $quote->subtotal - $quote->diskon;
        $totalDisc = 0;

        foreach ($subQuote as $subtitle) {
            foreach ($subtitle->detail as $detail) {
                $amountBeforeDisc = $detail->amount / (1 - ($detail->disc / 100));
                $totalDisc += ($amountBeforeDisc * $detail->disc / 100);
            }
        }
        // dd($termncon);
        return view("pages.sales.quotation.overhaul.detail-print-no-image", compact('totalDisc', 'quote', 'subQuote', 'tax', 'afterDisc'));
    }
    public function revisionOverhaul($id)
    {
        $quotation = Quotation::find($id);
        $subtitle = SubtitleQuotation::with('detail')->where('id_quotation', $id)->get();
        $dateNow = Carbon::now();
        $numberQ = Quotation::whereYear('estimated_date', $dateNow)->where('id_sales', Auth::user()->id)->count();
        $formattedNumberQ = str_pad($numberQ + 1, 3, '0', STR_PAD_LEFT);
        $monthNow = $dateNow->month;
        $formattedMonthNow = $this->convertToRoman($monthNow);
        $pic = client::where('client.id_sales', Auth::user()->id)->get();
        // $pic = Pic::join('client', 'client.id', '=', 'id_client')->where('client.id_sales', Auth::user()->id)->get('pic.*');
        $product = Product::join('serial_product as s', 's.id_product', '=', 'product.id')->get(['product.id as comId', 's.id', 'product.go', 's.pn', 's.brand', 'product.detail_desc']);
        // dd($subtitle);

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
        return view('pages.sales.quotation.overhaul.form-revisi', compact('quotation', 'subtitle', 'pic', 'formattedNumberQ', 'formattedMonthNow', 'product'));
    }
    public function updateOverhaul(Request $request, $id)
    {
        // dd($request->all());
        $quote = Quotation::find($id);
        $lastQuote = Quotation::where('primary_id', $quote->primary_id)->orderByDesc('num_rev')->first();
        $pic = Pic::find($request->id_pic);
        $client = Client::find($pic->id_client);
        $rule = [
            'no_quote' => 'required',
            'title' => 'required',
            'product' => 'required',
            'detail_product' => 'required',
            'expired_date' => 'required',
            'validity' => 'required',
            'pricing' => 'required',
            'delivery_process' => 'required',
            'payment' => 'required',
        ];
        $message = [
            'no_quote.required' => 'Field No Quote Wajib Diisi',
            'title.required' => 'Field Title Wajib Diisi',
            'product.required' => 'Field Product Wajib Diisi',
            'detail_product.required' => 'Field Detail Product Wajib Diisi',
            'expired_date.required' => 'Wajib isi Expired Date',
            'termcon.required' => 'Field Term and Conditions Wajib Diisi',
        ];
        // dd($quote->primary_id);
        $quote->is_primary = '0';
        $quote->save();

        $this->validate($request, $rule, $message);
        // Masukan Data ke Tabel Quotataion
        $quotation = new Quotation();
        $quotation->id_pic = $request->id_pic;
        $quotation->id_sales = $request->id_sales;
        $quotation->id_service = NULL;
        $quotation->id_support = $client->id_support;
        $quotation->primary_id = $quote->primary_id;
        $quotation->is_primary = '1';
        $quotation->num_rev = $lastQuote->num_rev + 1;
        $quotation->destination = $quote->destination;
        $quotation->no_pr = NULL;
        $quotation->status = "20";
        $quotation->status_date = Carbon::today();
        $quotation->note = "-";
        $quotation->expired_date = $request->expired_date;
        $quotation->type = $quote->type;
        $quotation->quote_for = $request->type;
        $quotation->po_date = NULL;
        $quotation->po_file = NULL;
        $quotation->level = '1';
        $quotation->estimated_date = $request->estimated_date;
        if ($request->tax != NULL) {
            $quotation->tax = $request->tax;
        } else {
            $quotation->tax = 0;
        }
        $quotation->shipping = $request->shipping ?? 0;
        $quotation->no_quote = $request->no_quote;
        $quotation->title = $request->title;
        $quotation->subtotal = $request->subtotal;
        if ($request->diskon != NULL) {
            $quotation->diskon = $request->diskon;
        } else {
            $quotation->diskon = 0;
        }
        $quotation->fee = 0;
        $quotation->nett = $request->subtotal - $request->diskon;
        $quotation->total_no_tax = $request->total_no_tax;
        $quotation->harga_total = $request->harga_total;
        $quoteSave = $quotation->save();
        if ($quoteSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            $row = 0;

            foreach ($request->subTitle as $item => $subtitleValue) {
                $row++;
                $subtitle = new SubtitleQuotation();
                $subtitle->id_quotation = $quotation->id;
                $subtitle->subtitle = $subtitleValue; // Menggunakan $subtitleValue langsung
                $subtitleSave = $subtitle->save();

                if (!empty($request->product[$row])) {
                    foreach ($request->product[$row] as $key => $productValue) {
                        $detService = new DetailServiceQuotation();
                        $detService->id_subtitle = $subtitle->id;
                        $detService->product = $productValue; // Ambil produk berdasarkan indeks
                        $detService->detail = $request->detail_product[$row][$key] ?? null; // Pastikan nilai aman
                        $detService->disc = $request->disc[$row][$key] ?? 0; // Default ke 0 jika kosong
                        $detService->qty = $request->qty[$row][$key] ?? 0; // Default ke 0
                        $detService->price = $request->price[$row][$key] ?? 0; // Default ke 0
                        $detService->info_qty = $request->info_qty[$row][$key] ?? null; // Default null
                        $detService->amount = $request->amount[$row][$key] ?? 0; // Default ke 0
                        $detService->save();
                    }
                }
            }
            if ($subtitleSave) {
                // Masukan Data ke dalam Tabel Term n Condition
                $termncon = new Termncon;
                $termncon->id_quotation = $quotation->id;
                $termncon->validity = $request->validity;
                $termncon->pricing = $request->pricing;
                $termncon->warranty = $request->warranty;
                $termncon->delivery_process = $request->delivery_process;
                $termncon->payment = $request->payment;
                $termncon->note = $request->note;
                $termnconSave = $termncon->save();

                $stats = new ChangeStatus;
                $stats->id_quotation = $quotation->primary_id;
                $stats->date = Carbon::now();
                $stats->note = 'Quotation Revision - ' . $lastQuote->num_rev + 1;
                $stats->status = $quotation->status;
                $stats->save();
            }
        }
        if ($termnconSave) {
            return redirect('/quote/overhaul-show/' . $quotation->id)->with('message', 'data telah di tambahkan');
        }
    }

    public function editOverhaul($id)
    {
        $quotation = Quotation::find($id);
        $subtitle = SubtitleQuotation::with('detail')->where('id_quotation', $id)->get();
        $dateNow = Carbon::now();
        $numberQ = Quotation::whereYear('estimated_date', $dateNow)->where('id_sales', Auth::user()->id)->count();
        $formattedNumberQ = str_pad($numberQ + 1, 3, '0', STR_PAD_LEFT);
        $monthNow = $dateNow->month;
        $formattedMonthNow = $this->convertToRoman($monthNow);
        $pic = client::where('client.id_sales', Auth::user()->id)->get();
        $pics = Pic::where('id_client', $quotation->pic->id_client)->get();
        $product = Product::join('serial_product as s', 's.id_product', '=', 'product.id')->get(['product.id as comId', 's.id', 'product.go', 's.pn', 's.brand', 'product.detail_desc']);

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
        return view('pages.sales.quotation.overhaul.edit', compact('quotation', 'subtitle', 'pic', 'pics', 'formattedNumberQ', 'formattedMonthNow', 'product'));
    }

    public function updateOverhaulDirect(Request $request, $id)
    {
        $picId = $request->pic ?? $request->id_pic;
        $pic = Pic::find($picId);
        $client = Client::find($pic->id_client);
        $rule = [
            'no_quote' => 'required',
            'title' => 'required',
            'product' => 'required',
            'detail_product' => 'required',
            'expired_date' => 'required',
            'validity' => 'required',
            'pricing' => 'required',
            'delivery_process' => 'required',
            'payment' => 'required',
        ];
        $message = [
            'no_quote.required' => 'Field No Quote Wajib Diisi',
            'title.required' => 'Field Title Wajib Diisi',
            'product.required' => 'Field Product Wajib Diisi',
            'detail_product.required' => 'Field Detail Product Wajib Diisi',
            'expired_date.required' => 'Wajib isi Expired Date',
            'termcon.required' => 'Field Term and Conditions Wajib Diisi',
        ];

        $this->validate($request, $rule, $message);
        $quotation = Quotation::find($id);
        $quotation->id_pic = $picId;
        $quotation->id_sales = $request->id_sales;
        $quotation->id_support = $client->id_support;
        $quotation->status_date = Carbon::today();
        $quotation->note = "-";
        $quotation->expired_date = $request->expired_date;
        $quotation->quote_for = $request->type;
        $quotation->level = '1';
        $quotation->estimated_date = $request->estimated_date;
        if ($request->tax != NULL) {
            $quotation->tax = $request->tax;
        } else {
            $quotation->tax = 0;
        }
        $quotation->shipping = $request->shipping ?? 0;
        $quotation->no_quote = $request->no_quote;
        $quotation->title = $request->title;
        $quotation->subtotal = $request->subtotal;
        if ($request->diskon != NULL) {
            $quotation->diskon = $request->diskon;
        } else {
            $quotation->diskon = 0;
        }
        $quotation->fee = 0;
        $quotation->nett = $request->subtotal - $request->diskon;
        $quotation->total_no_tax = $request->total_no_tax;
        $quotation->harga_total = $request->harga_total;
        $quoteSave = $quotation->save();
        if ($quoteSave) {
            $row = 0;
            $subtitleQ = SubtitleQuotation::where('id_quotation', $id)->get();
            foreach ($subtitleQ as $sub) {
                $sub->delete();
            }
            foreach ($request->subTitle as $item => $subtitleValue) {
                $row++;
                $subtitle = new SubtitleQuotation();
                $subtitle->id_quotation = $id;
                $subtitle->subtitle = $subtitleValue;
                $subtitleSave = $subtitle->save();

                if (!empty($request->product[$row])) {
                    foreach ($request->product[$row] as $key => $productValue) {
                        $detService = new DetailServiceQuotation();
                        $detService->id_subtitle = $subtitle->id;
                        $detService->product = $productValue;
                        $detService->detail = $request->detail_product[$row][$key] ?? null;
                        $detService->disc = $request->disc[$row][$key] ?? 0;
                        $detService->qty = $request->qty[$row][$key] ?? 0;
                        $detService->price = $request->price[$row][$key] ?? 0;
                        $detService->info_qty = $request->info_qty[$row][$key] ?? null;
                        $detService->amount = $request->amount[$row][$key] ?? 0;
                        $detService->save();
                    }
                }
            }
            if ($subtitleSave) {
                $termncon = Termncon::where('id_quotation', $id)->first();
                if (!$termncon) {
                    $termncon = new Termncon();
                    $termncon->id_quotation = $id;
                }
                $termncon->validity = $request->validity;
                $termncon->pricing = $request->pricing;
                $termncon->warranty = $request->warranty;
                $termncon->delivery_process = $request->delivery_process;
                $termncon->payment = $request->payment;
                $termncon->note = $request->note;
                $termnconSave = $termncon->save();
            }
        }
        if ($termnconSave) {
            return redirect('/quote/overhaul-show/' . $id)->with("success", "Data Quotation Telah Diubah");
        }
    }

    protected function convertToRoman($month)
    {
        $romanMonth = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $romanMonth[$month];
    }
}
