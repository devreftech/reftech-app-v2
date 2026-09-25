<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\DetailProduct;
use App\Models\Product;
use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\SerialProduct;
use App\Models\Unit;
use App\Services\DeletionGuardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $isSales = ($user && $user->role === 'Sales' && $user->id != 3);

        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();

        if ($isSales) {
            $commodity = 0;
            $dproduct = 0;
            $sproduct = 0;
            $asset = 0;
            $revenue = 0;
            $commentAdmin = collect();
            $unreadCommentAdmin = collect();
        } else {
            $kpis = Cache::remember('product_stock_kpis', 300, function () {
                $commodity = Product::count();
                $dproduct = DetailProduct::count();
                $sproduct = SerialProduct::count();
                $asset = DetailProduct::sum(DB::raw('modal * stock'));
                $revenue = DB::table(DB::raw('(SELECT p.stock * s.price AS val FROM serial_product s JOIN product p ON p.id = s.id_product GROUP BY p.id) as sub'))
                    ->sum('val');
                return compact('commodity', 'dproduct', 'sproduct', 'asset', 'revenue');
            });

            $commodity = $kpis['commodity'];
            $dproduct = $kpis['dproduct'];
            $sproduct = $kpis['sproduct'];
            $asset = $kpis['asset'];
            $revenue = $kpis['revenue'];

            // Comment Buat Admin
            $firstComments = Comment::where('id_user', Auth::id())
                ->groupBy('id_status')
                ->get();

            $statusIds = $firstComments->pluck('id_status')->toArray();
            $dates = $firstComments->pluck('created_at', 'id_status');

            if (!empty($statusIds)) {
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

                // Filter in-memory
                $unreadCommentAdmin = $commentAdmin->where('level', '1')->values();
            } else {
                $commentAdmin = collect();
                $unreadCommentAdmin = collect();
            }
        }

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
        $unreadComment = $comment->where('level', '1')->values()->take(5);
        return view('pages.warehouse.product.index', compact('commodity', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'leveledProspect', 'noSaleProspect', 'dproduct', 'sproduct', 'asset', 'revenue'));
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
        // Rules for validation
        $rule = [
            'commodity' => 'required',
            'dimension' => 'required',
            'description' => 'required',
            'note' => 'required',
        ];

        // Custom validation messages
        $message = [
            'commodity.required' => 'Field commodity Wajib Diisi',
            'dimension.required' => 'Field dimension Wajib Diisi',
            'description.required' => 'Field description Wajib Diisi',
            'note.required' => 'Field note Wajib Diisi',
        ];
        // Perform validation
        $this->validate($request, $rule, $message);

        $lastUnit = Unit::orderBy('id', 'desc')->first();
        $lastProduct = Product::orderBy('id', 'desc')->first();

        // Creating new product instance
        $product = new Product;
        if ($lastUnit->id > $lastProduct->id) {
            $product->id = $lastUnit->id + 1;
        } else {
            $product->id = $lastProduct->id + 1;
        }
        $product->commodity = $request->commodity;
        $product->dimension = $request->dimension;
        $product->description = $request->description;
        $product->detail_desc = $request->detail_desc;
        $product->category = $request->category;
        $product->procurement_type = $request->procurement_type ?? 'ready_stock';
        $product->go = $request->go;
        $product->weight = $request->weight;
        $product->first_stock = 0;
        $product->warehouse_stock = 0;
        $product->stock = 0;
        $product->unit = $request->unit;
        // Set note and current date
        $product->note = $request->note;
        $product->date = Carbon::now();

        // Save the product
        $productSave = $product->save();

        // Redirect based on product type
        if ($productSave) {
            return redirect('/product/' . $product->id)->with('message', 'Data telah ditambahkan');
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
        $product = Product::find($id);
        $allStock = $product->stock + $product->warehouse_stock;
        $details = DetailProduct::where('id_product', $id)
            ->withCount(['detailProductIn as total_in', 'detailProductOut as total_out'])
            ->get();
        $serials = SerialProduct::where('id_product', $id)->get();
        $partInquiries = SerialProduct::with(['sparePartVendorPrices.supplier'])
            ->where('id_product', $id)
            ->whereHas('sparePartVendorPrices')
            ->get();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();

        // Grafik Barang Masuk & Keluar per bulan (12 bulan terakhir)
        $chartStart = Carbon::now()->startOfMonth()->subMonths(11);
        $monthlyInByYm = DB::table('detail_product_in as d')
            ->join('product_in as p', 'p.id', '=', 'd.id_product_in')
            ->join('detail_product as dp', 'dp.id', '=', 'd.id_detail_product')
            ->where('dp.id_product', $id)
            ->where('p.date', '>=', $chartStart->toDateString())
            ->selectRaw("DATE_FORMAT(p.date, '%Y-%m') as ym, SUM(d.qty) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');
        $monthlyOutByYm = DB::table('detail_product_out as d')
            ->join('product_out as p', 'p.id', '=', 'd.id_product_out')
            ->join('detail_product as dp', 'dp.id', '=', 'd.id_detail_product')
            ->where('dp.id_product', $id)
            ->where('p.date', '>=', $chartStart->toDateString())
            ->selectRaw("DATE_FORMAT(p.date, '%Y-%m') as ym, SUM(d.qty) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $monthlyLabels = [];
        $monthlyIn = [];
        $monthlyOut = [];
        for ($m = 0; $m < 12; $m++) {
            $cursor = $chartStart->copy()->addMonths($m);
            $ym = $cursor->format('Y-m');
            $monthlyLabels[] = $cursor->translatedFormat('M Y');
            $monthlyIn[] = (float) ($monthlyInByYm[$ym] ?? 0);
            $monthlyOut[] = (float) ($monthlyOutByYm[$ym] ?? 0);
        }

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
        return view('pages.warehouse.product.detail', compact('product', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'details', 'leveledProspect', 'noSaleProspect', 'serials', 'allStock', 'partInquiries', 'monthlyLabels', 'monthlyIn', 'monthlyOut'));
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
            'commodity' =>
                'required',

            'dimension' =>
                'required',

            'description' =>
                'required',
        ];

        $message = [
            'commodity.required' => 'Field commodity Wajib Diisi',
            'dimension.required' => 'Field dimension Wajib Diisi',
            'description.required' => 'Field description Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        // dd($request);

        $product = Product::find($id);
        $product->commodity = $request->commodity;
        $product->dimension = $request->dimension;
        $product->description = $request->description;
        $product->detail_desc = $request->detail_desc;
        $product->category = $request->category;
        $product->procurement_type = $request->procurement_type ?? 'ready_stock';
        $product->unit = $request->unit;
        $product->weight = $request->weight;
        $product->go = $request->go;
        $product->note = $request->note;
        $productSave = $product->save();

        if ($productSave) {
            return redirect()->back()->with('message', 'data telah ditambahkan');
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
        $product = Product::find($id);

        if (!$product) {
            return redirect('/product/' . $id)->with('error', 'Produk tidak ditemukan');
        }

        $guard = app(DeletionGuardService::class);
        $check = $guard->checkProductDeletion($product);
        if (!$check['allowed']) {
            return redirect('/product/' . $id)->with('error', 'Produk tidak dapat dihapus karena ' . implode(', ', $check['reasons']));
        }

        $delProduct = $product->delete();

        // foreach ($replacement as $replace) {
        //     $delReplace = $replace->delete();
        // }

        // foreach ($equivalents as $equivalent) {
        //     $delEqui = $equivalent->delete();
        // }

        if ($delProduct) {
            return 1;
        } else {
            return 0;
        }
    }

    public function storeReplacement(Request $request, $id)
    {

        $rule = [
            'replacement' =>
                'required',
        ];

        $message = [
            'replacement.required' => 'Field Replacement Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        // dd($request);

        $replace = new DetailProduct;
        $replace->id_product = $id;
        $replace->replacement = $request->replacement;
        $replace->modal = 0;
        $replace->warehouse_stock = 0;
        $replace->stock = 0;
        $replace->is_opname = $request->has('is_opname') ? (bool)$request->is_opname : true;
        $replaceSave = $replace->save();

        $previousUrl = url()->previous();
        if ($replaceSave) {
            return redirect($previousUrl)->with('success', 'Data berhasil disimpan!');
        }
    }
    public function updateReplacement(Request $request, $id)
    {
        $replace = DetailProduct::findOrFail($id);
        $replace->replacement = $request->replacement;
        if (Auth::user()->role == 'Admin' || Auth::user()->isDeveloper() || Auth::id() == 3) {
            $replace->modal = $request->modal;
        }
        if ($request->has('is_opname')) {
            $replace->is_opname = (bool)$request->is_opname;
        }
        $replaceSave = $replace->save();

        $previousUrl = url()->previous();
        if ($replaceSave) {
            return redirect($previousUrl)->with('success', 'Data berhasil disimpan!');
        }
    }
    public function toggleOpname($id)
    {
        $replace = DetailProduct::findOrFail($id);
        $replace->is_opname = !$replace->is_opname;
        $replace->save();

        return response()->json([
            'success' => true,
            'id' => $replace->id,
            'is_opname' => $replace->is_opname,
            'message' => 'Status SKU ' . $replace->replacement . ' berhasil diubah menjadi ' . ($replace->is_opname ? 'Bisa Diopname' : 'Tidak Diopname')
        ]);
    }
    public function destroyReplacement($id)
    {
        $replacement = DetailProduct::find($id);

        if (!$replacement) {
            return response()->json(['error' => 'Replacement not found.'], 404);
        }

        $guard = app(DeletionGuardService::class);
        $check = $guard->checkReplacementDeletion($replacement);
        if (!$check['allowed']) {
            return response()->json([
                'error' => 'Replacement tidak dapat dihapus karena ' . implode(', ', $check['reasons']),
            ], 422);
        }

        $delReplace = $replacement->delete();

        if ($delReplace) {
            return 1;
        } else {
            return 0;
        }
    }
    public function storeEquivalent(Request $request, $id)
    {

        $rule = [
            'image' =>
                'required',

            'brand' =>
                'required',

            'pn' =>
                'required',
        ];

        $message = [
            'image.required' => 'Field Image Wajib Diisi',
            'brand.required' => 'Field brand Wajib Diisi',
            'pn.required' => 'Field pn Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        // dd($request->all());

        $equiv = new SerialProduct;
        $equiv->id_product = $id;
        $equiv->brand = $request->brand;
        $equiv->fxp_parts = "-";
        $equiv->pn = $request->pn;
        $equiv->detail = $request->detail;
        if ($request->detail != NULL) {
            if (isset($request->unit)) {
                foreach ($request->unit as $key => $value) {
                    if ($value == 'rental') {
                        $equiv->rental = '1';
                    }
                    if ($value == 'second') {
                        $equiv->second = '1';
                    }
                    if ($value == 'new') {
                        $equiv->new = '1';
                    }
                }
            }
        } else {
            $equiv->rental = '0';
            $equiv->second = '0';
            $equiv->new = '0';
        }
        if ($request->bar != NULL) {
            $equiv->bar = $request->bar;
            $equiv->air_cap = $request->air_cap;
        } else {
            $equiv->price = $request->price;
        }
        $equiv->image = $request->image;
        $equivSave = $equiv->save();

        $previousUrl = url()->previous();
        if ($equivSave) {
            return redirect($previousUrl)->with('success', 'Data berhasil disimpan!');
        }
    }
    public function updateEquivalent(Request $request, $id)
    {

        $rule = [
            'image' =>
                'required',

            'brand' =>
                'required',

            'pn' =>
                'required',
        ];

        $message = [
            'image.required' => 'Field Image Wajib Diisi',
            'brand.required' => 'Field brand Wajib Diisi',
            'pn.required' => 'Field pn Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        // dd($request);

        $equiv = SerialProduct::find($id);
        $equiv->brand = $request->brand;
        $equiv->fxp_parts = "-";
        $equiv->pn = $request->pn;
        if ($request->bar != NULL) {
            $equiv->bar = $request->bar;
            $equiv->air_cap = $request->air_cap;
        } else {
            $equiv->price = $request->price;
        }
        $equiv->image = $request->image;
        $equiv->rental = '0';
        $equiv->second = '0';
        $equiv->new = '0';
        if ($request->detail != NULL) {
            if (isset($request->unit)) {
                foreach ($request->unit as $key => $value) {
                    if ($value == 'rental') {
                        $equiv->rental = '1';
                    }
                    if ($value == 'second') {
                        $equiv->second = '1';
                    }
                    if ($value == 'new') {
                        $equiv->new = '1';
                    }
                }
            }
        }
        $equivSave = $equiv->save();

        $previousUrl = url()->previous();
        if ($equivSave) {
            return redirect($previousUrl)->with('success', 'Data berhasil disimpan!');
        }
    }
    public function destroyEquivalent($id)
    {
        $equivalent = SerialProduct::find($id);

        if (!$equivalent) {
            return response()->json(['error' => 'Equivalent not found.'], 404);
        }

        $guard = app(DeletionGuardService::class);
        $check = $guard->checkEquivalentDeletion($equivalent);
        if (!$check['allowed']) {
            return response()->json([
                'error' => 'Equivalent tidak dapat dihapus karena ' . implode(', ', $check['reasons']),
            ], 422);
        }

        $delEqui = $equivalent->delete();

        if ($delEqui) {
            return 1;
        } else {
            return 0;
        }
    }

    public function indexMaster()
    {
        $commodity = Product::count();
        $dproduct = DetailProduct::count();
        $sproduct = SerialProduct::count();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $readyStockCount = Product::where('procurement_type', 'ready_stock')->orWhereNull('procurement_type')->count();
        $byOrderCount = Product::where('procurement_type', 'by_order')->count();
        return view('pages.warehouse.master.index', compact('commodity', 'dproduct', 'noSaleProspect', 'sproduct', 'readyStockCount', 'byOrderCount'));
    }
    public function indexUnit()
    {
        return view('pages.warehouse.unit.index');
    }
    public function showUnit($id)
    {
        $product = Product::find($id);
        $allStock = $product->stock + $product->warehouse_stock;
        $details = DetailProduct::where('id_product', $id)->get();
        $serials = SerialProduct::where('id_product', $id)->get();
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
        return view('pages.warehouse.unit.detail', compact('product', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'details', 'leveledProspect', 'noSaleProspect', 'serials', 'allStock'));
    }

    /**
     * Data catalog produk untuk DataTables.
     * Menggunakan selective columns, relasi transaksi, dan cache untuk performa tinggi.
     */
    public function getSalesData(Request $request)
    {
        $user = Auth::user();
        $isSales = ($user && strtolower($user->role) === 'sales' && $user->id != 3);
        $cacheKey = 'catalog_products_' . ($isSales ? 'sales_v1' : 'all_v1');

        if ($request->has('refresh')) {
            Cache::forget($cacheKey);
        }

        $data = Cache::remember($cacheKey, 180, function () use ($isSales) {
            // Count Product In per id_product
            $productInCounts = DB::table('detail_product_in as dpi')
                ->join('detail_product as dp', 'dp.id', '=', 'dpi.id_detail_product')
                ->select('dp.id_product', DB::raw('COUNT(dpi.id) as total_in'))
                ->groupBy('dp.id_product')
                ->pluck('total_in', 'dp.id_product');

            // Count Product Out per id_product
            $productOutCounts = DB::table('detail_product_out as dpo')
                ->join('detail_product as dp', 'dp.id', '=', 'dpo.id_detail_product')
                ->select('dp.id_product', DB::raw('COUNT(dpo.id) as total_out'))
                ->groupBy('dp.id_product')
                ->pluck('total_out', 'dp.id_product');

            // Count Quotation per id_product (level 1 & is_primary 1)
            $quotationCounts = DB::table('detail_quotation as dq')
                ->join('serial_product as sp', 'sp.id', '=', 'dq.id_equivalent')
                ->join('quotation as q', 'q.id', '=', 'dq.id_quotation')
                ->where('q.level', '1')
                ->where('q.is_primary', '1')
                ->select('sp.id_product', DB::raw('COUNT(dq.id) as total_quote'))
                ->groupBy('sp.id_product')
                ->pluck('total_quote', 'sp.id_product');

            // Count Product Set: cek apakah produk masuk ke dalam Product Set (sebagai item kit)
            $productSetItems = DB::table('item_product_set as ips')
                ->join('detail_product as dp', 'dp.id', '=', 'ips.id_replacement')
                ->join('product_set as ps', 'ps.id', '=', 'ips.id_product_set')
                ->join('product as p_parent', 'p_parent.id', '=', 'ps.id_product')
                ->select('dp.id_product', 'p_parent.commodity as parent_name')
                ->get()
                ->groupBy('id_product');

            // Cek juga produk yang merupakan master/header Product Set
            $productSetParents = DB::table('product_set as ps')
                ->pluck('id', 'id_product');

            // Latest purchase price (LAST HPP) per id_product from detail_product_in
            $lastHppRecords = DB::table('detail_product_in as dpi')
                ->join('detail_product as dp', 'dp.id', '=', 'dpi.id_detail_product')
                ->select('dp.id_product', 'dpi.modal', 'dpi.hpp')
                ->where(function ($q) {
                    $q->where('dpi.modal', '>', 0)
                      ->orWhere('dpi.hpp', '>', 0);
                })
                ->orderByDesc('dpi.id')
                ->get()
                ->unique('id_product')
                ->keyBy('id_product');

            // Average purchase price (AVG HPP) and replacement from detail_product per id_product
            $avgHppRecords = DB::table('detail_product')
                ->select('id_product', 'replacement', 'modal', 'hpp')
                ->where(function ($q) {
                    $q->where('modal', '>', 0)
                      ->orWhere('hpp', '>', 0);
                })
                ->orderByDesc('id')
                ->get()
                ->groupBy('id_product');

            $productsQuery = DB::table('serial_product as s')
                ->join('product as p', 'p.id', '=', 's.id_product');

            if ($isSales) {
                $productsQuery->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(REPLACE(p.category, "-", " "))'), ['consumable part', 'non consumable part']);
                });
            } else {
                $productsQuery->where(function ($q) {
                    $q->whereNull('p.category')
                        ->orWhereNotIn('p.category', ['Unit', 'unit']);
                });
            }

            $products = $productsQuery->select([
                    's.id',
                    'p.id as product_id',
                    'p.commodity',
                    'p.category',
                    's.image',
                    's.brand',
                    's.pn',
                    'p.description',
                    'p.go',
                    'p.stock',
                    'p.warehouse_stock',
                    'p.pending_stock',
                    's.price',
                    's.price_updated_at',
                    's.updated_at',
                ])
                ->orderByDesc('s.id')
                ->get();

            foreach ($products as $row) {
                $pid = $row->product_id;
                $in = (int) ($productInCounts[$pid] ?? 0);
                $out = (int) ($productOutCounts[$pid] ?? 0);
                $quote = (int) ($quotationCounts[$pid] ?? 0);

                $setRows = $productSetItems[$pid] ?? null;
                $itemSetCount = $setRows ? count($setRows) : 0;
                $isParentSet = isset($productSetParents[$pid]);
                $totalSet = $itemSetCount + ($isParentSet ? 1 : 0);

                $setNames = $setRows ? $setRows->pluck('parent_name')->unique()->implode(', ') : '';
                if ($isParentSet) {
                    $setNames = $setNames ? ($setNames . ', Master Set') : 'Master Set';
                }

                $lastRec = $lastHppRecords->get($pid);
                $lastVal = $lastRec ? (float) ($lastRec->modal ?: $lastRec->hpp) : null;

                $avgList = $avgHppRecords[$pid] ?? null;
                $avgRec = $avgList ? $avgList->first() : null;
                $avgVal = $avgRec ? (float) ($avgRec->modal ?: $avgRec->hpp) : null;
                $replacementName = $avgRec ? $avgRec->replacement : null;

                $row->total_in = $in;
                $row->total_out = $out;
                $row->total_quotation = $quote;
                $row->total_set = $totalSet;
                $row->in_product_set = $totalSet > 0;
                $row->product_set_names = $setNames;
                $row->total_transaksi = $in + $out + $quote + $totalSet;

                $row->avg_hpp = $avgVal;
                $row->last_hpp = $lastVal;
                $row->replacement_name = $replacementName;
            }

            return $products;
        });

        return response()->json(['data' => $data]);
    }

}

