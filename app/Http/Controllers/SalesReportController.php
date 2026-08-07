<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Prospect;
use App\Models\SalesReports;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $startDate1 = Carbon::createFromDate(2025, 1)->startOfMonth()->toDateString();
        $endDate1 = Carbon::createFromDate(2025, 6)->endOfMonth()->toDateString();

        $startDate2 = Carbon::createFromDate(2025, 7)->startOfMonth()->toDateString();
        $endDate2 = Carbon::createFromDate(2025, 12)->endOfMonth()->toDateString();

        $pOutSemester1 = DB::table('product as p')
            ->join('detail_product as dp', 'dp.id_product', '=', 'p.id')
            ->join('detail_product_out as dpo', 'dpo.id_detail_product', '=', 'dp.id')
            ->join('product_out as po', 'dpo.id_product_out', '=', 'po.id')
            ->whereBetween('po.date', [$startDate1, $endDate1])
            ->select('p.id', 'p.commodity', DB::raw('SUM(dpo.qty) as total_keluar'))
            ->groupBy('p.id', 'p.commodity')
            ->get();

        $pOutSemester2 = DB::table('product as p')
            ->join('detail_product as dp', 'dp.id_product', '=', 'p.id')
            ->join('detail_product_out as dpo', 'dpo.id_detail_product', '=', 'dp.id')
            ->join('product_out as po', 'dpo.id_product_out', '=', 'po.id')
            ->whereBetween('po.date', [$startDate2, $endDate2])
            ->select('p.id', 'p.commodity', DB::raw('SUM(dpo.qty) as total_keluar'))
            ->groupBy('p.id', 'p.commodity')
            ->get();

        $pInSemester1 = DB::table('product as p')
            ->join('detail_product as dp', 'dp.id_product', '=', 'p.id')
            ->join('detail_product_in as dpi', 'dpi.id_detail_product', '=', 'dp.id')
            ->join('product_in as pi', 'dpi.id_product_in', '=', 'pi.id')
            ->whereBetween('pi.date', [$startDate1, $endDate1])
            ->select('p.id', 'p.commodity', DB::raw('SUM(dpi.qty) as total_keluar'))
            ->groupBy('p.id', 'p.commodity')
            ->get();

        $pInSemester2 = DB::table('product as p')
            ->join('detail_product as dp', 'dp.id_product', '=', 'p.id')
            ->join('detail_product_in as dpi', 'dpi.id_detail_product', '=', 'dp.id')
            ->join('product_in as pi', 'dpi.id_product_in', '=', 'pi.id')
            ->whereBetween('pi.date', [$startDate2, $endDate2])
            ->select('p.id', 'p.commodity', DB::raw('SUM(dpi.qty) as total_keluar'))
            ->groupBy('p.id', 'p.commodity')
            ->get();
        $products = Product::all();
        $pIn1 = $pInSemester1->keyBy('id');
        $pOut1 = $pOutSemester1->keyBy('id');
        $pIn2 = $pInSemester2->keyBy('id');
        $pOut2 = $pOutSemester2->keyBy('id');

        $result = [];

        foreach ($products as $product) {
            $id = $product->id;

            $in1 = $pIn1[$id]->total_keluar ?? 0;
            $out1 = $pOut1[$id]->total_keluar ?? 0;
            $in2 = $pIn2[$id]->total_keluar ?? 0;
            $out2 = $pOut2[$id]->total_keluar ?? 0;

            $result[] = [
                'id' => $product->id,
                'commodity' => $product->commodity,
                'GO' => $product->go,
                'pIn1' => $in1,
                'pOut1' => $out1,
                'pIn2' => $in2,
                'pOut2' => $out2,
                'AllStock' => $product->stock + $product->warehouse_stock,
            ];
        }
        // dd($result);
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        return view('pages.warehouse.reports.index', compact('noSaleProspect'));
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
            'year' =>
                'required',
            'semester' =>
                'required',
        ];

        $message = [
            'year.required' => 'Field year Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);

        $report = new SalesReports;
        $report->year = $request->year;
        $report->semester = $request->semester;
        $reportSave = $report->save();
        if ($reportSave) {
            return redirect('/sale-report')->with('message', 'data telah ditambahkan');
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
        $reports = SalesReports::find($id);
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        return view('pages.warehouse.reports.detail', compact('noSaleProspect', 'reports'));
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
        //
    }

    public function detailOnline($id)
    {
        $reports = SalesReports::find($id);
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        return view('pages.warehouse.reports.detail-online', compact('noSaleProspect', 'reports'));
    }

    public function detailOffline($id)
    {
        $reports = SalesReports::find($id);
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        return view('pages.warehouse.reports.detail-offline', compact('noSaleProspect', 'reports'));
    }

    public function yearly($year)
    {
        $startDate1 = Carbon::createFromDate($year, 1)->startOfMonth()->toDateString();
        $endDate1 = Carbon::createFromDate($year, 6)->endOfMonth()->toDateString();

        $startDate2 = Carbon::createFromDate($year, 7)->startOfMonth()->toDateString();
        $endDate2 = Carbon::createFromDate($year, 12)->endOfMonth()->toDateString();

        $pOutSemester1 = DB::table('product as p')
            ->join('detail_product as dp', 'dp.id_product', '=', 'p.id')
            ->join('detail_product_out as dpo', 'dpo.id_detail_product', '=', 'dp.id')
            ->join('product_out as po', 'dpo.id_product_out', '=', 'po.id')
            ->whereBetween('po.date', [$startDate1, $endDate1])
            ->select('p.id', 'p.commodity', DB::raw('SUM(dpo.qty) as total_keluar'))
            ->groupBy('p.id', 'p.commodity')
            ->get();

        $pOutSemester2 = DB::table('product as p')
            ->join('detail_product as dp', 'dp.id_product', '=', 'p.id')
            ->join('detail_product_out as dpo', 'dpo.id_detail_product', '=', 'dp.id')
            ->join('product_out as po', 'dpo.id_product_out', '=', 'po.id')
            ->whereBetween('po.date', [$startDate2, $endDate2])
            ->select('p.id', 'p.commodity', DB::raw('SUM(dpo.qty) as total_keluar'))
            ->groupBy('p.id', 'p.commodity')
            ->get();

        $pInSemester1 = DB::table('product as p')
            ->join('detail_product as dp', 'dp.id_product', '=', 'p.id')
            ->join('detail_product_in as dpi', 'dpi.id_detail_product', '=', 'dp.id')
            ->join('product_in as pi', 'dpi.id_product_in', '=', 'pi.id')
            ->whereBetween('pi.date', [$startDate1, $endDate1])
            ->select('p.id', 'p.commodity', DB::raw('SUM(dpi.qty) as total_keluar'))
            ->groupBy('p.id', 'p.commodity')
            ->get();

        $pInSemester2 = DB::table('product as p')
            ->join('detail_product as dp', 'dp.id_product', '=', 'p.id')
            ->join('detail_product_in as dpi', 'dpi.id_detail_product', '=', 'dp.id')
            ->join('product_in as pi', 'dpi.id_product_in', '=', 'pi.id')
            ->whereBetween('pi.date', [$startDate2, $endDate2])
            ->select('p.id', 'p.commodity', DB::raw('SUM(dpi.qty) as total_keluar'))
            ->groupBy('p.id', 'p.commodity')
            ->get();
        $products = Product::all();
        $pIn1 = $pInSemester1->keyBy('id');
        $pOut1 = $pOutSemester1->keyBy('id');
        $pIn2 = $pInSemester2->keyBy('id');
        $pOut2 = $pOutSemester2->keyBy('id');

        $result = [];

        foreach ($products as $product) {
            $id = $product->id;

            $in1 = $pIn1[$id]->total_keluar ?? 0;
            $out1 = $pOut1[$id]->total_keluar ?? 0;
            $in2 = $pIn2[$id]->total_keluar ?? 0;
            $out2 = $pOut2[$id]->total_keluar ?? 0;

            $result[] = [
                'id' => $product->id,
                'commodity' => $product->commodity,
                'GO' => $product->go,
                'pIn1' => $in1,
                'pOut1' => $out1,
                'pIn2' => $in2,
                'pOut2' => $out2,
                'AllStock' => $product->stock + $product->warehouse_stock,
            ];
        }
        // dd($result);
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        return view('pages.warehouse.reports.inout', compact('noSaleProspect', 'year'));
    }
}
