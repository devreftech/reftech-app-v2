<?php

namespace App\Http\Controllers;

use App\Models\DetailProduct;
use App\Models\ItemProductSet;
use App\Models\Product;
use App\Models\ProductSet;
use App\Models\SerialProduct;
use App\Models\Supplier;
use App\Models\SparePartVendorPrice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductSetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function getProductSetCategories()
    {
        $existingCategories = ProductSet::join('product', 'product.id', '=', 'product_set.id_product')
            ->whereNotNull('product.category')
            ->where('product.category', '!=', '-')
            ->whereNotIn('product.category', ['Consumable Part', 'Non Consumable Part', 'Sparepart'])
            ->distinct()
            ->pluck('product.category')
            ->toArray();
        $defaults = [
            'Bearing Kit Airend',
            'Bearing Kit Main Motor',
            'Bearing Kit Fan Motor',
            'Non Bearing Kit',
            'Seal Kit',
            'Gasket Kit',
            'Valve Kit',
            'Filter Kit',
            'Overhaul Kit'
        ];
        return array_values(array_unique(array_merge($defaults, $existingCategories)));
    }

    public function index()
    {
        $productSets = ProductSet::has('product')->with('product')->get();
        $totalSet = $productSets->count();
        $totalItems = ItemProductSet::count();

        $bearingAirendCount = $productSets->filter(function ($ps) {
            $p = $ps->product;
            if (!$p) return false;
            if ($p->category === 'Bearing Kit Airend') return true;
            $text = ($p->commodity ?? '') . ' ' . ($p->description ?? '');
            return (stripos($text, 'Bearing') !== false && stripos($text, 'Airend') !== false) ||
                   ($p->category === 'Bearing Kit' && stripos($text, 'Motor') === false) ||
                   (stripos($text, 'Bearing Kit for Sigma') !== false);
        })->count();

        $bearingMainMotorCount = $productSets->filter(function ($ps) {
            $p = $ps->product;
            if (!$p) return false;
            if ($p->category === 'Bearing Kit Main Motor') return true;
            $text = ($p->commodity ?? '') . ' ' . ($p->description ?? '');
            return stripos($text, 'Bearing') !== false && stripos($text, 'Main Motor') !== false;
        })->count();

        $bearingFanMotorCount = $productSets->filter(function ($ps) {
            $p = $ps->product;
            if (!$p) return false;
            if ($p->category === 'Bearing Kit Fan Motor') return true;
            $text = ($p->commodity ?? '') . ' ' . ($p->description ?? '');
            return stripos($text, 'Bearing') !== false && stripos($text, 'Fan Motor') !== false;
        })->count();

        $bearingCount = $bearingAirendCount + $bearingMainMotorCount + $bearingFanMotorCount;
        $nonBearingCount = $totalSet - $bearingCount;
        $inStock = $productSets->filter(fn($ps) => ($ps->product->stock ?? 0) + ($ps->product->warehouse_stock ?? 0) > 0)->count();
        $outStock = $totalSet - $inStock;
        $categories = $this->getProductSetCategories();

        return view('pages.warehouse.product-set.index', compact(
            'totalSet',
            'totalItems',
            'bearingCount',
            'bearingAirendCount',
            'bearingMainMotorCount',
            'bearingFanMotorCount',
            'nonBearingCount',
            'inStock',
            'outStock',
            'categories'
        ));
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
        $product = new Product();
        $product->commodity = $request->commodity;
        $product->description = $request->description;
        $product->detail_desc = $request->detail_desc ?? '-';
        $product->go = '-';
        $product->category = $request->category ? trim($request->category) : 'Non Bearing Kit';
        $product->dimension = '-';
        $product->unit = $request->unit ?? 'Set';
        $product->note = '-';
        $product->first_stock = 0;
        $product->warehouse_stock = 0;
        $product->stock = 0;
        $product->pending_stock = 0;
        $product->weight = 0;
        $product->date = Carbon::today();
        $product->save();

        $replace = new DetailProduct();
        $replace->id_product = $product->id;
        $replace->replacement = $request->commodity;
        $replace->modal = 0;
        $replace->warehouse_stock = 0;
        $replace->stock = 0;
        $replace->save();

        $equiv = new SerialProduct();
        $equiv->id_product = $product->id;
        $equiv->brand = "brand";
        $equiv->fxp_parts = "-";
        $equiv->pn = $request->commodity;
        $equiv->detail = $request->detail_desc;
        $equiv->rental = '0';
        $equiv->second = '0';
        $equiv->new = '0';
        $equiv->bar = '-';
        $equiv->air_cap = '-';
        $equiv->image = '-';
        $equiv->price = 0;
        $equiv->save();

        $productSet = new ProductSet();
        $productSet->id_product = $product->id;
        $productSet->save();

        return redirect('product-set/' . $productSet->id)->with('success', 'Data Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $productSet = ProductSet::findOrFail($id);
        $itemProduct = ItemProductSet::with([
            'replacement.product.serial.sparePartVendorPrices.supplier',
            'replacement.product.detail',
            'replacement.detailProductIn.productIn.supp',
        ])
            ->where('id_product_set', $id)
            ->get();
        $product = Product::find($productSet->id_product);
        $allStock = ($product->stock ?? 0) + ($product->warehouse_stock ?? 0);
        $replacement = DetailProduct::with('product.serial')->get();
        $suppliers = Supplier::orderBy('supplier')->get();
        $categories = $this->getProductSetCategories();
        return view('pages.warehouse.product-set.detail', compact('productSet', 'itemProduct', 'product', 'allStock', 'replacement', 'suppliers', 'categories'));
    }

    public function store_equivalent(Request $request, $id)
    {
        $request->validate([
            'id_product' => 'required|exists:product,id',
            'brand'      => 'required|string',
            'pn'         => 'required|string',
        ]);

        $equiv = new SerialProduct();
        $equiv->id_product = $request->id_product;
        $equiv->brand = $request->brand;
        $equiv->pn = $request->pn;
        $equiv->detail = $request->detail ?? '-';
        $equiv->price = $request->price ?? 0;
        $equiv->fxp_parts = '-';
        $equiv->rental = '0';
        $equiv->second = '0';
        $equiv->new = '0';
        $equiv->bar = '-';
        $equiv->air_cap = '-';
        $equiv->image = '-';
        $equiv->save();

        return redirect('product-set/' . $id)->with('success', 'Equivalent brand berhasil ditambahkan ke komponen.');
    }

    public function store_vendor_price(Request $request, $id)
    {
        $request->validate([
            'id_serial_product' => 'required|exists:serial_product,id',
            'id_supplier'       => 'required|exists:supplier,id',
            'price_idr'         => 'required|numeric|min:0',
            'date'              => 'required|date',
        ]);

        SparePartVendorPrice::create([
            'id_serial_product' => $request->id_serial_product,
            'id_supplier'       => $request->id_supplier,
            'price_usd'         => $request->price_usd ? floatval($request->price_usd) : 0,
            'kurs_usd'          => 0,
            'price_idr'         => floatval($request->price_idr),
            'date'              => $request->date,
        ]);

        return redirect('product-set/' . $id)->with('success', 'Harga vendor berhasil disimpan.');
    }

    public function destroy_vendor_price($id)
    {
        $vp = SparePartVendorPrice::findOrFail($id);
        $vp->delete();
        return response()->json(['success' => true, 'message' => 'Harga vendor berhasil dihapus.']);
    }

    public function destroy_equivalent($id)
    {
        $serial = SerialProduct::findOrFail($id);
        $serial->sparePartVendorPrices()->delete();
        $serial->delete();
        return response()->json(['success' => true, 'message' => 'Merk equivalent berhasil dihapus.']);
    }

    public function search_products(Request $request)
    {
        $q = trim($request->q ?? '');
        if (!$q) {
            return response()->json(['results' => []]);
        }

        $products = Product::where(function($query) use ($q) {
                $query->where('commodity', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%");
            })
            ->orWhereHas('serial', function($sq) use ($q) {
                $sq->where('brand', 'like', "%{$q}%")
                   ->orWhere('pn', 'like', "%{$q}%");
            })
            ->with(['serial'])
            ->take(25)
            ->get();

        $results = [];
        foreach ($products as $p) {
            if ($p->serial && $p->serial->count() > 0) {
                foreach ($p->serial as $s) {
                    $results[] = [
                        'id'        => 'serial_' . $s->id,
                        'text'      => "[{$s->brand}] {$s->pn} — (Master: {$p->commodity})",
                        'brand'     => $s->brand,
                        'pn'        => $s->pn,
                        'detail'    => $s->detail ?? '',
                        'price'     => (float) ($s->price ?? 0),
                        'commodity' => $p->commodity,
                    ];
                }
            } else {
                $results[] = [
                    'id'        => 'prod_' . $p->id,
                    'text'      => "{$p->commodity} — {$p->description}",
                    'brand'     => '',
                    'pn'        => $p->commodity,
                    'detail'    => $p->detail_desc ?? '',
                    'price'     => 0,
                    'commodity' => $p->commodity,
                ];
            }
        }

        return response()->json(['results' => $results]);
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
        $productSet = ProductSet::findOrFail($id);
        $product = Product::findOrFail($productSet->id_product);
        $product->commodity = $request->commodity ?? $product->commodity;
        $product->detail_desc = $request->detail_desc ?? $product->detail_desc;
        $product->category = $request->category ?? $product->category;
        $product->description = $request->description ?? $product->description;
        $product->unit = $request->unit ?? $product->unit;
        $product->save();

        $dProduct = DetailProduct::where('id_product', $product->id)->first();
        if ($dProduct) {
            $dProduct->replacement = $product->commodity;
            $dProduct->save();
        }

        return redirect('product-set/' . $id)->with('success', 'Product Set berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $productSet = ProductSet::findOrFail($id);
        $productId = $productSet->id_product;
        ItemProductSet::where('id_product_set', $id)->delete();
        $productSet->delete();
        if ($productId) {
            Product::where('id', $productId)->delete();
            DetailProduct::where('id_product', $productId)->delete();
            SerialProduct::where('id_product', $productId)->delete();
        }

        return response()->json(1);
    }

    public function destroy_item($id)
    {
        $item = ItemProductSet::findOrFail($id);
        $productSetId = $item->id_product_set;
        $item->delete();

        // Recalculate stock
        $productSet = ProductSet::find($productSetId);
        if ($productSet) {
            $product = Product::find($productSet->id_product);
            $dProduct = DetailProduct::where('id_product', $product->id)->first();
            $itemProduct = ItemProductSet::with('replacement')->where('id_product_set', $productSetId)->get();
            $allReplacements = $itemProduct->map(fn($it) => $it->replacement)->filter();
            $minStock = $allReplacements->count() > 0 
                ? (int) $allReplacements->min(fn($rep) => ($rep->stock ?? 0) + ($rep->warehouse_stock ?? 0))
                : 0;
            if ($product) {
                $product->stock = $minStock;
                $product->save();
            }
            if ($dProduct) {
                $dProduct->stock = $minStock;
                $dProduct->save();
            }
        }

        return response()->json(1);
    }

    public function store_item(Request $request, $id)
    {
        $productSet = ProductSet::findOrFail($id);
        $product = Product::findOrFail($productSet->id_product);
        $dProduct = DetailProduct::where('id_product', $product->id)->first();

        // Check if item already exists in this set
        $existing = ItemProductSet::where('id_product_set', $id)
            ->where('id_replacement', $request->replacement)
            ->first();
        if ($existing) {
            return redirect('product-set/' . $id)->with('error', 'Komponen ini sudah terdaftar dalam bundle.');
        }

        $item = new ItemProductSet();
        $item->id_product_set = $id;
        $item->id_replacement = $request->replacement;
        $item->save();

        $itemProduct = ItemProductSet::with('replacement')->where('id_product_set', $id)->get();
        $allReplacements = $itemProduct->map(fn($it) => $it->replacement)->filter();
        $minStock = $allReplacements->count() > 0 
            ? (int) $allReplacements->min(fn($rep) => ($rep->stock ?? 0) + ($rep->warehouse_stock ?? 0))
            : 0;

        $product->stock = $minStock;
        $product->save();
        if ($dProduct) {
            $dProduct->stock = $minStock;
            $dProduct->save();
        }

        return redirect('product-set/' . $id)->with('success', 'Komponen berhasil ditambahkan ke dalam bundle.');
    }
}
