<?php

namespace App\Http\Controllers;

use App\Models\DetailProduct;
use App\Models\DetailStockOpname;
use App\Models\StockOpname;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpnameController extends Controller
{
    public function index()
    {
        return view('pages.warehouse.opname.index');
    }
    public function store(Request $request)
    {
        $opname = new StockOpname;
        $opname->id_user = Auth::user()->id;
        $opname->date = Carbon::today();
        $opname->year = Carbon::today()->year;
        $opname->periode = $request->periode;
        $opname->note = $request->note ?? '-';
        $opnameSave = $opname->save();
        if ($opnameSave) {
            return redirect()->back()->with('success', 'Stock Opname berhasil ditambahkan!');
        }
    }
    public function show($id)
    {
        $opname = StockOpname::find($id);
        $detailOpname = DetailStockOpname::where('id_stock_opname', $id)->get();
        $usedProductIds = $detailOpname->pluck('id_product')->toArray();

        $product = DetailProduct::whereNotIn('id', $usedProductIds)->get();
        return view('pages.warehouse.opname.detail', compact('opname', 'detailOpname', 'product'));
    }
    public function show_print($id)
    {
        $opname = StockOpname::find($id);
        $prevStockOpnameId = StockOpname::where('id', '<', $id)
            ->orderBy('id', 'desc')
            ->value('id');

        $detailOpname = DB::table('detail_product as dp')
            ->leftJoin('detail_stock_opname as dso', function ($join) use ($id) {
                $join->on('dso.id_product', '=', 'dp.id')
                    ->where('dso.id_stock_opname', '=', $id);
            })
            ->leftJoin('detail_stock_opname as prev', function ($join) use ($prevStockOpnameId) {
                $join->on('prev.id_product', '=', 'dp.id')
                    ->where('prev.id_stock_opname', '=', $prevStockOpnameId);
            })
            ->leftJoin('product as p', 'p.id', '=', 'dp.id_product')
            ->select(
                'dp.id',
                'dp.replacement',
                'p.description',
                'p.unit',
                // 'dp.stock as stock_sistem',
                DB::raw('COALESCE(dso.stock_sistem, 0) as stock_sistem'),
                DB::raw('COALESCE(dso.stock_gudang, 0) as stock_gudang'),
                DB::raw('COALESCE(dso.selisih, 0) as selisih'),
                DB::raw('COALESCE(prev.stock_sistem, 0) as prev_qty')
            )
            ->get();

        // $detailOpname = DB::table('detail_product as dp')
        //     ->leftJoin('detail_stock_opname as dso', function ($join) use ($id) {
        //         $join->on('dso.id_product', '=', 'dp.id')
        //             ->where('dso.id_stock_opname', '=', $id);
        //     })
        //     ->leftJoin('product as p', 'p.id', '=', 'dp.id_product')
        //     ->select(
        //         'dp.id',
        //         'dp.replacement',
        //         'p.description',
        //         'p.unit',
        //         // 'dp.stock as stock_sistem',
        //         DB::raw('COALESCE(dso.stock_sistem, 0) as stock_sistem'),
        //         DB::raw('COALESCE(dso.stock_gudang, 0) as stock_gudang'),
        //         DB::raw('COALESCE(dso.selisih, 0) as selisih')
        //     )
        //     ->orderBy('dp.replacement')
        //     ->get();
        $product = DetailProduct::all();
        return view('pages.warehouse.opname.detail-print', compact('opname', 'detailOpname', 'product'));
    }
    public function store_product(Request $request, $id)
    {
        $replacement = DetailProduct::find($request->replacement);
        $stock_gudang = $request->stock_bdg + $request->stock_bks;
        $opname = new DetailStockOpname;
        $opname->id_stock_opname = $id;
        $opname->id_product = $request->replacement;
        $opname->stock_sistem = $replacement->stock + $replacement->warehouse_stock;
        $opname->stock_bdg = $request->stock_bdg;
        $opname->stock_bks = $request->stock_bks;
        $opname->stock_gudang = $stock_gudang;
        $opname->selisih = $replacement->stock + $replacement->warehouse_stock - $stock_gudang;
        $opname->note = $request->note;
        $opnameSave = $opname->save();
        if ($opnameSave) {
            return redirect()->back()->with('success', 'Stock Opname Product berhasil ditambahkan!');
        }
    }
    public function update_product(Request $request, $id)
    {
        $opname = DetailStockOpname::find($id);
        $stock_gudang = $request->stock_bdg + $request->stock_bks;
        $replacement = DetailProduct::find($opname->id_product);
        $opname->stock_sistem = $replacement->stock + $replacement->warehouse_stock;
        $opname->stock_bdg = $request->stock_bdg;
        $opname->stock_bks = $request->stock_bks;
        $opname->stock_gudang = $stock_gudang;
        $opname->selisih = $replacement->stock + $replacement->warehouse_stock - $stock_gudang;
        $opname->note = $request->note;
        $opnameSave = $opname->save();
        if ($opnameSave) {
            return redirect()->back()->with('success', 'Stock Opname Product berhasil ditambahkan!');
        }
    }
    public function stock_replacement($id)
    {
        $product = DetailProduct::find($id);

        return response()->json([
            'stock_sistem' => $product ? $product->stock + $product->warehouse_stock : 0
        ]);
    }
    public function show_replacement($id)
    {
        $opname = DetailStockOpname::find($id);

        if (!$opname) {
            return response()->json([
                'message' => 'Data stock opname tidak ditemukan'
            ], 404);
        }

        $product = DetailProduct::find($opname->id_product);

        return response()->json([
            'product' => optional($product)->replacement,
            'web' => $opname->stock_sistem ?? 0,
            'gudang' => $opname->stock_gudang ?? 0,
            'bdg' => $opname->stock_bdg ?? 0,
            'bks' => $opname->stock_bks ?? 0,
            'selisih' => $opname->selisih ?? 0,
            'note' => $opname->note ?? '',
        ]);
    }
}
