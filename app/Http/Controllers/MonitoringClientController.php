<?php

namespace App\Http\Controllers;

use App\Models\ChangeStatus;
use App\Models\Client;
use App\Models\Comment;
use App\Models\DetailQuotation;
use App\Models\Machine;
use App\Models\Mainlog;
use App\Models\Monitoring;
use App\Models\MonitoringMonthly;
use App\Models\MonitoringWeekly;
use App\Models\Pic;
use App\Models\PnMonitoring;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Reports;
use App\Models\StatusMonitoring;
use App\Models\Termncon;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class MonitoringClientController extends Controller
{
    public function index()
    {
        $allDryer = Machine::join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->join('unit as u', 'u.id', '=', 'sp.id_product')
            ->where('machine.id_client', 1277)
            ->where('u.unit', 'REFRIGERANT AIR DRYER')
            ->whereNotBetween('machine.id', [472, 481])
            ->count();
        $allPlant = Machine::where('id_client', 1277)->whereNotBetween('id', [472, 481])->count();
        $allPlantMonitoring = Machine::where('id_client', 1277)->whereNotBetween('id', [472, 481])
            ->whereHas('monitoring', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();
        $GT = Machine::where('id_client', 1277)->where('location', 'GT 1-2')->whereNotBetween('id', [472, 481])->count();
        $GTMonitoring = Machine::where('id_client', 1277)->where('location', 'GT 1-2')->whereNotBetween('id', [472, 481])
            ->whereHas('monitoring', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();
        $GT3 = Machine::where('id_client', 1277)->where('location', 'GT3/BOILER')->whereNotBetween('id', [472, 481])->count();
        $GT3Monitoring = Machine::where('id_client', 1277)->where('location', 'GT3/BOILER')->whereNotBetween('id', [472, 481])
            ->whereHas('monitoring', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();
        $INC = Machine::where('id_client', 1277)->where('location', 'INC')->whereNotBetween('id', [472, 481])->count();
        $INCMonitoring = Machine::where('id_client', 1277)->where('location', 'INC')->whereNotBetween('id', [472, 481])
            ->whereHas('monitoring', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();
        $PM12 = Machine::where('id_client', 1277)->where('location', 'BM 1-2')->whereNotBetween('id', [472, 481])->count();
        $PM12Monitoring = Machine::where('id_client', 1277)->where('location', 'BM 1-2')->whereNotBetween('id', [472, 481])
            ->whereHas('monitoring', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();
        $PM35 = Machine::where('id_client', 1277)->whereBetween('location', ['BM 3', 'BM 5'])->whereNotBetween('id', [472, 481])->count();
        $PM35Monitoring = Machine::where('id_client', 1277)->whereBetween('location', ['BM 3', 'BM 5'])->whereNotBetween('id', [472, 481])
            ->whereHas('monitoring', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();
        $PM78 = Machine::where('id_client', 1277)->whereBetween('location', ['BM 7', 'BM 8'])->whereNotBetween('id', [472, 481])->count();
        $PM78Monitoring = Machine::where('id_client', 1277)->whereBetween('location', ['BM 7', 'BM 8'])->whereNotBetween('id', [472, 481])
            ->whereHas('monitoring', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();

        $day = Carbon::today();
        $month = $day->month;
        $year = $day->year;

        $weekly1 = MonitoringWeekly::where('week', 1)->whereMonth('date', $month)->whereYear('date', $year)->count();
        $weekly2 = MonitoringWeekly::where('week', 2)->whereMonth('date', $month)->whereYear('date', $year)->count();
        $weekly3 = MonitoringWeekly::where('week', 3)->whereMonth('date', $month)->whereYear('date', $year)->count();
        $weekly4 = MonitoringWeekly::where('week', 4)->whereMonth('date', $month)->whereYear('date', $year)->count();
        $weekly5 = MonitoringWeekly::where('week', 5)->whereMonth('date', $month)->whereYear('date', $year)->count();

        $weekly1April = MonitoringWeekly::where('week', 1)->whereMonth('date', 6)->whereYear('date', $year)->count();
        $weekly2April = MonitoringWeekly::where('week', 2)->whereMonth('date', 6)->whereYear('date', $year)->count();
        $weekly3April = MonitoringWeekly::where('week', 3)->whereMonth('date', 6)->whereYear('date', $year)->count();
        $weekly4April = MonitoringWeekly::where('week', 4)->whereMonth('date', 6)->whereYear('date', $year)->count();
        $weekly5April = MonitoringWeekly::where('week', 5)->whereMonth('date', 6)->whereYear('date', $year)->count();

        $monthly = MonitoringMonthly::whereMonth('date', $month)->whereYear('date', $year)->count();

        $cleaningApril = Reports::join('machine as m', 'm.id', '=', 'reports.id_machine')->where('m.id_client', 1277)->whereNotBetween('m.id', [472, 481])->whereMonth('date', 4)->whereYear('date', $year)->where('type', 'Cleaning')->count();
        $cleaning = Reports::join('machine as m', 'm.id', '=', 'reports.id_machine')->where('m.id_client', 1277)->whereNotBetween('m.id', [472, 481])->whereMonth('date', $month)->whereYear('date', $year)->where('type', 'Cleaning')->count();

        $machines = Machine::with([
            'unit',
            'unit.unit',
            'monitoring' => function ($query) use ($month, $year) {
                $query->whereMonth('date', $month)
                    ->whereYear('date', $year);
            }
        ])->where('id_client', 1277)->whereNotBetween('id', [472, 481])->get();

        $result = $machines->filter(function ($machine) {
            return $machine->monitoring->isNotEmpty();
        })->map(function ($machine) {
            return [
                'machine' => $machine->unit->brand . ' ' . $machine->unit->unit->sku . ' - ' . $machine->tag . ' - ' . $machine->location,
                'id' => $machine->id,
                'log' => $machine->monitoring->filter(function ($log) {
                    return !is_null($log->desc) && $log->desc !== '-' && $log->desc !== 'normal' && $log->desc !== 'Normal';
                })->map(function ($log) {
                    return [
                        'log' => $log->desc,
                        'date' => \Carbon\Carbon::parse($log->date)->format('d'),
                        'pic' => $log->pic->name
                    ];
                }),
                'mainlog' => $machine->monitoring->filter(function ($mainlog) {
                    return !is_null($mainlog->main_desc) && $mainlog->main_desc !== '-';
                })->map(function ($mainlog) {
                    return [
                        'id' => $mainlog->id,
                        'id_pic' => $mainlog->id_pic,
                        'id_machine' => $mainlog->machine->id,
                        'id_service' => $mainlog->reports->first()->id ?? NULL,
                        'log' => $mainlog->main_desc,
                        'date' => \Carbon\Carbon::parse($mainlog->date)->format('d'),
                        'technician' => $mainlog->technician
                    ];
                }),
            ];
        });
        $issued = Monitoring::join('status_monitoring as sm', 'monitoring.id', '=', 'sm.id_monitoring')
            ->join('machine as m', 'monitoring.id_machine', '=', 'm.id')
            ->join('serial_product as sp', 'sp.id', '=', 'm.id_unit')
            ->join('unit as un', 'un.id', '=', 'sp.id_product')
            ->join('users as u', 'monitoring.id_pic', '=', 'u.id')
            ->where('m.id_client', 1277)
            ->where('sm.status', '0')
            ->where('monitoring.issue', '!=', '-')
            ->whereNotNull('monitoring.issue')
            ->groupBy('monitoring.id')
            ->select(
                'monitoring.*',
                'u.name',
                'm.tag',
                'm.location',
                'monitoring.id as monId',
                DB::raw("CONCAT(sp.brand, ' ', un.sku) as machine")
            )->get();


        $start = Carbon::create(2025, 1, 1); // Mulai dari Januari 2025
        $end = Carbon::create(2026, 12, 1); // Sampai Desember 2026

        $no_semester = 1;
        while ($start->lessThanOrEqualTo($end)) {
            $semester[] = [
                'semmester' => 'Semester ' . $no_semester,
                'year' => $start->format('Y'),
                'semmesterNum' => $no_semester,
            ];
            if ($start->month == '1') {
                $no_semester++;
            } else {
                $no_semester--;
            }
            $start->addMonths(6);
        }
        // dd($results);
        return view('pages.monitoring.client.index', compact(
            'cleaning',
            'cleaningApril',
            'month',
            'year',
            'allDryer',
            'allPlant',
            'allPlantMonitoring',
            'GT',
            'GTMonitoring',
            'GT3',
            'GT3Monitoring',
            'INC',
            'INCMonitoring',
            'PM12',
            'PM12Monitoring',
            'PM35',
            'PM35Monitoring',
            'PM78',
            'PM78Monitoring',
            'result',
            'issued',
            'weekly1April',
            'weekly2April',
            'weekly3April',
            'weekly4April',
            'weekly5April',
            'weekly1',
            'weekly2',
            'weekly3',
            'weekly4',
            'weekly5',
            'monthly'
        ));
    }

    public function detailWeekly()
    {
        $month = now()->month;
        $year = now()->year;

        $machines = Machine::join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->join('unit as un', 'un.id', '=', 'sp.id_product')
            ->whereNotBetween('machine.id', [472, 481])
            ->where('machine.id_client', 1277)
            ->select(
                'machine.id',
                'machine.tag',
                'machine.location',
                DB::raw("CONCAT(sp.brand, ' ', un.sku) as machine")
            )
            ->get();

        $results = [];

        foreach ($machines as $machine) {
            $weeks = MonitoringWeekly::where('id_machine', $machine->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->pluck('week')
                ->toArray();

            $cleaning = Reports::where('id_machine', $machine->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->where('type', 'Cleaning')
                ->count();

            $results[] = [
                'id' => $machine->id,
                'machine' => $machine->machine,
                'tag' => $machine->tag,
                'location' => $machine->location,
                'month' => $month,
                'cleaning' => $cleaning,
                'week1' => in_array(1, $weeks) ? 1 : 0,
                'week2' => in_array(2, $weeks) ? 1 : 0,
                'week3' => in_array(3, $weeks) ? 1 : 0,
                'week4' => in_array(4, $weeks) ? 1 : 0,
                'week5' => in_array(5, $weeks) ? 1 : 0,
            ];
        }
        // dd($weeks);
        return view('pages.monitoring.client.detail-weekly');
    }
    public function editIssue(Request $request, $id)
    {
        $monitoring = Monitoring::find($id);
        // $monitoring->date = $request->date;
        $monitoring->issue = $request->issue;
        $monitoringSave = $monitoring->save();
        if ($monitoringSave) {
            $statusM = StatusMonitoring::where('id_monitoring', $id)->first();
            $statusM->date = $request->date;
            $statusM->status = '1';
            $statussave = $statusM->save();
        }
        if ($monitoringSave) {
            return redirect('/monitoring-client/fajarPaper-monitoring')->with('success', 'Data telah di buat');
        }
    }

    public function monitoring()
    {
        return view('pages.monitoring.client.monitoring');
    }
    public function service_report()
    {
        return view('pages.monitoring.client.service-report');
    }

    public function show($id)
    {
        $monitoring = Monitoring::find($id);
        $status = StatusMonitoring::where('id_monitoring', $id)->get();
        $pn = PnMonitoring::where('id_monitoring', $id)->get();
        $quotes = Quotation::where('id_monitoring', $id)->get();
        $maintenance = Mainlog::whereNull('id_issue')->where('id_machine', $monitoring->id_machine)->get();
        // dd($quotes);
        return view('pages.monitoring.client.detail', compact('monitoring', 'status', 'pn', 'quotes', 'maintenance'));
    }

    public function addMainlog(Request $request, $id)
    {
        // dd($request->mainlog);
        // $idmon = $id;
        $mainlog = Mainlog::find($request->mainlog);
        $mainlog->id_issue = $id;
        $mainlogSave = $mainlog->save();
        $status = new StatusMonitoring();
        $status->id_monitoring = $id;
        $status->id_pic = Auth::user()->id;
        $status->status = '4';
        $status->desc = 'Done';
        // $status->date = $request->date;
        $status->date = Carbon::today();
        $statusSave = $status->save();

        if ($mainlogSave && $statusSave) {
            return redirect('/monitoring-client/fajarPaper')->with('success', 'Mainlog telah di tambah');
        }
    }
    public function updateIssue(Request $request, $id)
    {
        $monitoring = Monitoring::find($id);
        $monitoring->issue = $request->issue;
        $monitroingSave = $monitoring->save();
        if ($monitroingSave) {
            return redirect('/monitoring-client/fajarPaper/' . $id)->with('success', 'Data telah di buat');
        }
    }
    public function updateRecommendation(Request $request, $id)
    {
        $monitoring = Monitoring::find($id);
        $monitoring->recommendation = $request->recommendation;
        $monitroingSave = $monitoring->save();
        if ($monitroingSave) {
            return redirect('/monitoring-client/fajarPaper/' . $id)->with('success', 'Data telah di buat');
        }
    }
    public function updatePN(Request $request, $id)
    {
        $pn = new PnMonitoring();
        $pn->id_monitoring = $id;
        $pn->pn = $request->pn;
        $pn->desc = $request->desc;
        $pn->stock = $request->stock;
        $pn->date = Carbon::today();
        $pnSave = $pn->save();
        if ($pnSave) {
            return redirect('/monitoring-client/fajarPaper/' . $id)->with('success', 'Data telah di buat');
        }
    }
    public function deletePN($id)
    {
        $pn = PnMonitoring::find($id);
        $pnDel = $pn->delete();
        if ($pnDel) {
            return 1;
        } else {
            return 0;
        }
    }
    public function updateStatus(Request $request, $id)
    {
        $status = new StatusMonitoring();
        $status->id_monitoring = $id;
        $status->id_pic = Auth::user()->id;
        $status->status = $request->status;
        $status->desc = $request->desc;
        $status->date = $request->date;
        // $status->date = Carbon::today();
        $statusSave = $status->save();
        if ($statusSave) {
            return redirect('/monitoring-client/fajarPaper/' . $id)->with('success', 'Data telah di buat');
        }
    }
    public function arsipStatus($id)
    {
        $status = new StatusMonitoring();
        $status->id_monitoring = $id;
        $status->id_pic = Auth::user()->id;
        $status->desc = 'Status Archived!';
        $status->status = '5';
        $status->date = Carbon::today();
        $statusSave = $status->save();
        if ($statusSave) {
            return 1;
        } else {
            return 0;
        }
    }
    public function backArsipStatus($id)
    {
        $lastStatus = StatusMonitoring::where('id_monitoring', $id)
            ->orderBy('id', 'desc') // Pastikan urutan dari terbaru ke terlama
            ->skip(1) // Lewati satu data terakhir
            ->first(); // Ambil satu data berikutnya
        $status = new StatusMonitoring();
        $status->id_monitoring = $id;
        $status->id_pic = Auth::user()->id;
        $status->desc = 'Back From Archived!';
        $status->status = $lastStatus->status;
        $status->date = Carbon::today();
        $statusSave = $status->save();
        if ($statusSave) {
            return 1;
        } else {
            return 0;
        }
    }

    public function indexArsip()
    {
        return view('pages.monitoring.client.arsip');
    }

    public function storeQuotation(Request $request, $id)
    {
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
        $quotation->id_pic = $request->id_pic;
        $quotation->id_sales = $request->id_sales;
        $quotation->id_service = NULL;
        $quotation->id_monitoring = $id;
        $quotation->id_support = $client->id_support;
        $quotation->is_primary = "1";
        $quotation->primary_id = 0;
        $quotation->num_rev = 0;
        $quotation->destination = $request->destination;
        $quotation->no_pr = $request->no_pr;
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
        $quotation->shipping = $request->shipping;
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
        $quotation->primary_id = $quotation->id;
        $quotation->save();
        if ($quoteSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            foreach ($request->product as $item => $value) {
                $dQuote = new DetailQuotation();
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
            $stats = new ChangeStatus();
            $stats->id_quotation = $quotation->id;
            $stats->date = Carbon::now();
            $stats->note = 'Quotation has been created';
            $stats->status = "10";
            $stats->save();
            if ($dQuoteSave) {
                // Masukan Data ke dalam Tabel Term n Condition
                $termncon = new Termncon();
                $termncon->id_quotation = $quotation->id;
                $termncon->validity = $request->validity;
                $termncon->pricing = $request->pricing;
                $termncon->delivery_process = $request->delivery_process;
                $termncon->payment = $request->payment;
                $termncon->note = $request->note;
                $termnconSave = $termncon->save();
            }
        }
        if ($termnconSave) {
            return redirect('/monitoring-client/fajarPaper/' . $id)->with('message', 'data telah di tambahkan');
        }

    }

    public function createQuotation($id)
    {
        $dateNow = Carbon::now();
        $numberQ = Quotation::whereYear('estimated_date', $dateNow)->where('id_sales', Auth::user()->id)->count();
        $formattedNumberQ = str_pad($numberQ + 1, 3, '0', STR_PAD_LEFT);
        $monthNow = $dateNow->month;
        $formattedMonthNow = $this->convertToRoman($monthNow);
        $pic = Pic::join('client', 'client.id', '=', 'id_client')->where('client.id', 1277)->get('pic.*');
        $sales = User::where('role', 'sales')->get();
        $product = collect([]);
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
        $monitoringId = $id;
        return view('pages.monitoring.client.form-quotation', compact('monitoringId', 'pic', 'sales', 'formattedNumberQ', 'formattedMonthNow', 'product'));
    }

    public function reports()
    {
        $today = Carbon::today();
        $month = $today->format('m');
        $summary = Monitoring::join('machine as m', 'monitoring.id_machine', '=', 'm.id')
            ->join('serial_product as sp', 'sp.id', '=', 'm.id_unit')
            ->join('main_log as ml', 'ml.id_issue', '=', 'monitoring.id')
            ->join('unit as un', 'un.id', '=', 'sp.id_product')
            ->whereMonth('monitoring.date', $today)
            ->whereYear('monitoring.date', $today)
            ->select(
                'monitoring.*',
                'ml.desc',
                'm.tag',
                'm.location',
                DB::raw("CONCAT(sp.brand, ' ', un.sku) as machine")
            )
            ->get();
        $quoteMon = Monitoring::join('quotation as q', 'monitoring.id', '=', 'q.id_monitoring')
            ->join('machine as m', 'monitoring.id_machine', '=', 'm.id')
            ->join('serial_product as sp', 'sp.id', '=', 'm.id_unit')
            ->join('unit as un', 'un.id', '=', 'sp.id_product')
            ->whereMonth('monitoring.date', $today)
            ->whereYear('monitoring.date', $today)
            ->select(
                'monitoring.*',
                'm.tag',
                'm.location',
                'q.no_quote',
                DB::raw("CONCAT(sp.brand, ' ', un.sku) as machine")
            )
            ->get();
        $latestStatus = StatusMonitoring::selectRaw('MAX(id) as id')
            ->groupBy('id_monitoring');

        $statusMon = StatusMonitoring::with('monitoring')
            ->whereIn('id', $latestStatus)
            ->where('status', '3')
            ->whereMonth('date', $today)
            ->whereYear('date', $today)
            ->get();
        // dd($statusMon);
        return view('pages.monitoring.client.reports', compact('month', 'summary', 'quoteMon', 'statusMon'));
    }

    public function reportsMonthly($month, $year)
    {
        return view('pages.monitoring.client.reports-monthly');
    }

    public function summaryPrint()
    {
        $today = Carbon::today();
        $summary = Monitoring::join('machine as m', 'monitoring.id_machine', '=', 'm.id')
            ->join('serial_product as sp', 'sp.id', '=', 'm.id_unit')
            ->join('main_log as ml', 'ml.id_issue', '=', 'monitoring.id')
            ->join('unit as un', 'un.id', '=', 'sp.id_product')
            ->whereMonth('monitoring.date', $today)
            ->whereYear('monitoring.date', $today)
            ->select(
                'monitoring.*',
                'ml.desc',
                'm.tag',
                'm.location',
                DB::raw("CONCAT(sp.brand, ' ', un.sku) as machine")
            )
            ->get();
        $month = $today->format('F');
        // dd($month);
        return view('pages.monitoring.client.summary-print', compact('summary', 'month'));
    }
    public function quotePrint()
    {
        $today = Carbon::today();

        $quoteMon = Monitoring::join('quotation as q', 'monitoring.id', '=', 'q.id_monitoring')
            ->join('machine as m', 'monitoring.id_machine', '=', 'm.id')
            ->join('serial_product as sp', 'sp.id', '=', 'm.id_unit')
            ->join('unit as un', 'un.id', '=', 'sp.id_product')
            ->whereMonth('monitoring.date', $today)
            ->whereYear('monitoring.date', $today)
            ->select(
                'monitoring.*',
                'm.tag',
                'm.location',
                'q.title',
                'q.no_quote',
                DB::raw("CONCAT(sp.brand, ' ', un.sku) as machine")
            )
            ->get();
        $month = $today->format('F');
        return view('pages.monitoring.client.quote-print', compact('quoteMon', 'month'));
    }
    public function holdPrint()
    {
        $today = Carbon::today();
        $latestStatus = StatusMonitoring::selectRaw('MAX(id) as id')
            ->groupBy('id_monitoring');

        $statusMon = StatusMonitoring::join('monitoring', 'monitoring.id', '=', 'status_monitoring.id_monitoring')
            ->join('machine as m', 'monitoring.id_machine', '=', 'm.id')
            ->join('serial_product as sp', 'sp.id', '=', 'm.id_unit')
            ->join('unit as un', 'un.id', '=', 'sp.id_product')
            ->whereIn('status_monitoring.id', $latestStatus)
            ->where('status_monitoring.status', '3')
            ->whereMonth('monitoring.date', $today)
            ->whereYear('monitoring.date', $today)
            ->select(
                'monitoring.*',
                'm.tag',
                'm.location',
                DB::raw("CONCAT(sp.brand, ' ', un.sku) as machine")
            )
            ->get();
        $month = $today->format('F');
        return view('pages.monitoring.client.hold-print', compact('statusMon', 'month'));
    }
    public function summaryPrintMonth($month)
    {
        $today = Carbon::today()->setMonth($month);
        $summary = Monitoring::join('machine as m', 'monitoring.id_machine', '=', 'm.id')
            ->join('serial_product as sp', 'sp.id', '=', 'm.id_unit')
            ->join('main_log as ml', 'ml.id_issue', '=', 'monitoring.id')
            ->join('unit as un', 'un.id', '=', 'sp.id_product')
            ->whereMonth('monitoring.date', $today)
            ->whereYear('monitoring.date', $today)
            ->select(
                'monitoring.*',
                'ml.desc',
                'm.tag',
                'm.location',
                DB::raw("CONCAT(sp.brand, ' ', un.sku) as machine")
            )
            ->get();
        $month = $today->format('F');
        // dd($month);
        return view('pages.monitoring.client.summary-print', compact('summary', 'month'));
    }
    public function quotePrintMonth($month)
    {
        $today = Carbon::today()->setMonth($month);

        $quoteMon = Monitoring::join('quotation as q', 'monitoring.id', '=', 'q.id_monitoring')
            ->join('machine as m', 'monitoring.id_machine', '=', 'm.id')
            ->join('serial_product as sp', 'sp.id', '=', 'm.id_unit')
            ->join('unit as un', 'un.id', '=', 'sp.id_product')
            ->whereMonth('monitoring.date', $today)
            ->whereYear('monitoring.date', $today)
            ->select(
                'monitoring.*',
                'm.tag',
                'm.location',
                'q.title',
                'q.no_quote',
                DB::raw("CONCAT(sp.brand, ' ', un.sku) as machine")
            )
            ->get();
        $month = $today->format('F');
        return view('pages.monitoring.client.quote-print', compact('quoteMon', 'month'));
    }
    public function holdPrintMonth($month)
    {
        $today = Carbon::today()->setMonth($month);
        $latestStatus = StatusMonitoring::selectRaw('MAX(id) as id')
            ->groupBy('id_monitoring');

        $statusMon = StatusMonitoring::join('monitoring', 'monitoring.id', '=', 'status_monitoring.id_monitoring')
            ->join('machine as m', 'monitoring.id_machine', '=', 'm.id')
            ->join('serial_product as sp', 'sp.id', '=', 'm.id_unit')
            ->join('unit as un', 'un.id', '=', 'sp.id_product')
            ->whereIn('status_monitoring.id', $latestStatus)
            ->where('status_monitoring.status', '3')
            ->whereMonth('monitoring.date', $today)
            ->whereYear('monitoring.date', $today)
            ->select(
                'monitoring.*',
                'm.tag',
                'm.location',
                DB::raw("CONCAT(sp.brand, ' ', un.sku) as machine")
            )
            ->get();
        $month = $today->format('F');
        return view('pages.monitoring.client.hold-print', compact('statusMon', 'month'));
    }

    public function arsip()
    {
        return view('pages.monitoring.client.arsip');
    }
    public function acceptIssue($id)
    {
        $issue = Monitoring::find($id);
        $issue->issue_level = '1';
        $issueSave = $issue->save();
        // dd($id, $issue);
        if ($issueSave) {
            return 1;
        } else {
            return 0;
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
