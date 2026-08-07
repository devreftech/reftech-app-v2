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
        $commodity = Product::count();
        $dproduct = DetailProduct::count();
        $sproduct = SerialProduct::count();
        $asset = DetailProduct::sum(DB::raw('modal * stock'));
        $revenue = DB::table(DB::raw('(SELECT p.stock * s.price AS val FROM serial_product s JOIN product p ON p.id = s.id_product GROUP BY p.id) as sub'))
            ->sum('val');
        // dd($revenue);
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
        $details = DetailProduct::where('id_product', $id)->get();
        $serials = SerialProduct::where('id_product', $id)->get();
        $partInquiries = SerialProduct::with(['sparePartVendorPrices.supplier'])
            ->where('id_product', $id)
            ->whereHas('sparePartVendorPrices')
            ->get();
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
        return view('pages.warehouse.product.detail', compact('product', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'details', 'leveledProspect', 'noSaleProspect', 'serials', 'allStock', 'partInquiries'));
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
        $replaceSave = $replace->save();

        $previousUrl = url()->previous();
        if ($replaceSave) {
            return redirect($previousUrl)->with('success', 'Data berhasil disimpan!');
        }
    }
    public function updateReplacement(Request $request, $id)
    {
        $replace = DetailProduct::find($id);
        $replace->replacement = $request->replacement;
        if (Auth::user()->role == 'Admin') {
            $replace->modal = $request->modal;
        }
        $replaceSave = $replace->save();

        $previousUrl = url()->previous();
        if ($replaceSave) {
            return redirect($previousUrl)->with('success', 'Data berhasil disimpan!');
        }
    }
    public function destroyReplacement($id)
    {
        $replacement = DetailProduct::find($id);

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
        return view('pages.warehouse.master.index', compact('commodity', 'dproduct', 'noSaleProspect', 'sproduct'));
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

}
