<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use App\Models\Client;
use App\Models\Pic;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("pages.sales.clients.customers.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customers = Client::where('id', $id)->first();
        $charge = Pic::where('id_client', $id)->get();
        $callhis = Activities::where('id_client', $id)->get();
        $quote = Quotation::join('pic','pic.id','=','quotation.id_pic')->where('pic.id_client', $id)->where('level', '1')->get();
        $sales = User::where('role', 'sales')->get();
        return view('pages.sales.clients.customers.detail', compact('customers', 'callhis', 'quote', 'sales', 'charge'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort(404);
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
            'company' =>
                'required',

            'email' =>
                'required',

            'phone' =>
                'required',

            'web' =>
                'nullable',

            'source' =>
                'required',

            'mobile' =>
                'required',

            'address' =>
                'required',

            'area' =>
                'required',

            'machine' =>
                'nullable',
        ];

        $message = [
            'company.required'=> 'Field company Wajib Diisi',
            'email.required'=> 'Field Email Wajib Diisi',
            'phone.required'=> 'Field Phone Wajib Diisi',
            'ru.required'=> 'Wajib Pilih Reseller atau User',
            'source.required'=> 'Field Source Wajib Diisi',
            'mobile.required'=> 'Field Mobile Wajib Diisi',
            'address.required'=> 'Field Address Wajib Diisi',
            'area.required'=> 'Field Area Wajib Diisi',
        ];
        
        $this->validate($request, $rule, $message);
        
        //masukan data ke table Cust(client)
        $customers = Client::find($id);
        $customers->company = $request->company;
        $customers->email = $request->email;
        $customers->phone = $request->phone;
        $customers->ru = $request->ru;
        if ($request->has('web')) {
            $customers->web = $request->web;
        }
        $customers->source = $request->source;
        $customers->mobile = $request->mobile;
        $customers->address = $request->address;
        $customers->area = $request->area;
        if ($request->has('npwp')) {
            $customers->npwp = $request->npwp;
        }
        if ($request->has('subAddress')) {
            $customers->subAddress = $request->subAddress;
        }
        $customersave = $customers->save();

        if($customersave){
            return redirect()->back()->with('message', 'data telah diUpdate');
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
        $hasCompletedQuote = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->where('pic.id_client', $id)
            ->where(function ($q) {
                $q->where('quotation.status', '100')->orWhereNotNull('quotation.po_file');
            })
            ->exists();
        if ($hasCompletedQuote) return 0;

        $custD = Client::find($id);
        $picD = Pic::where('id_client', $id)->get();
        $activitiesD = Activities::where('id_client', $id)->get();
        $visitD = Visit::where('id_client', $id)->get();
        $quoteD = Quotation::join('pic','pic.id','=','quotation.id_pic')->where('pic.id_client', $id)->get();

        $delCust = $custD->delete();
        if( $picD != NULL) {
            foreach ($picD as $pic) {
                $delpic = $pic->delete();
            }
        }
        if( $activitiesD != NULL) {
            foreach ($activitiesD as $activities) {
                $delActivities = $activities->delete();
            }
        }
        if($visitD != NULL) {
            foreach ($visitD as $visit) {
                $delVisits = $visit->delete();
            }
        }
        if($quoteD != NULL) {
            foreach ($quoteD as $quote) {
                $delQuote = $quote->delete();
            }
        }

        if($delCust || $delActivities || $delVisits || $delQuote || $delpic){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * Display a ranking leaderboard of Key Accounts (Top Customers) by PO values YTD.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function keyAccounts(Request $request)
    {
        $selectedYear = $request->query('year', date('Y'));
        $selectedSemester = $request->query('semester', 'all');
        $search = $request->query('search');

        $isSales = Auth::user()->role == 'Sales';
        $salesId = Auth::id();

        // Query available years from quotations
        $years = Quotation::where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->whereNotNull('po_date')
            ->when($isSales, function ($query) use ($salesId) {
                $query->where('id_sales', $salesId);
            })
            ->selectRaw('YEAR(po_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        if (!in_array((int)date('Y'), $years)) {
            array_unshift($years, (int)date('Y'));
        }

        // Handle semester filtering
        if ($selectedSemester == '1') {
            $startDate = Carbon::create($selectedYear, 1, 1)->startOfDay();
            $endDate = Carbon::create($selectedYear, 6, 30)->endOfDay();
        } elseif ($selectedSemester == '2') {
            $startDate = Carbon::create($selectedYear, 7, 1)->startOfDay();
            $endDate = Carbon::create($selectedYear, 12, 31)->endOfDay();
        } else {
            $startDate = Carbon::create($selectedYear, 1, 1)->startOfYear();
            $endDate = Carbon::create($selectedYear, 12, 31)->endOfYear();
        }

        // Total revenue for contribution percentage
        $totalRevenueYear = Quotation::whereBetween('po_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->when($isSales, function ($query) use ($salesId) {
                $query->where('id_sales', $salesId);
            })
            ->sum('nett');

        // Key Accounts ranking query (Paginated by 20, including Outstanding subquery)
        $keyAccountsQuery = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->whereBetween('quotation.po_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('quotation.status', '100')
            ->where('quotation.level', '1')
            ->where('quotation.is_primary', '1');

        if ($isSales) {
            $keyAccountsQuery->where('quotation.id_sales', $salesId);
        }

        if (!empty($search)) {
            $keyAccountsQuery->where('client.company', 'like', '%' . $search . '%');
        }

        $keyAccounts = $keyAccountsQuery->select(
                'client.id',
                'client.company',
                DB::raw('SUM(quotation.nett) as total_po'),
                DB::raw('COUNT(quotation.id) as count_po'),
                DB::raw('MAX(quotation.po_date) as last_po_date'),
                DB::raw('(
                    SELECT COALESCE(SUM(p.amount), 0)
                    FROM payment p
                    JOIN quotation q2 ON q2.id = p.id_quotation
                    JOIN pic pic2 ON pic2.id = q2.id_pic
                    WHERE pic2.id_client = client.id
                      AND p.type = "Tempo"
                      AND p.level = 0'
                      . ($isSales ? ' AND q2.id_sales = ' . (int) $salesId : '') . '
                ) as total_outstanding')
            )
            ->groupBy('client.id', 'client.company')
            ->orderByDesc('total_po')
            ->paginate(20);

        return view('pages.sales.clients.key-accounts.index', compact(
            'keyAccounts',
            'years',
            'selectedYear',
            'selectedSemester',
            'totalRevenueYear',
            'search'
        ));
    }
}

