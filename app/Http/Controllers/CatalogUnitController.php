<?php

namespace App\Http\Controllers;

use App\Models\CatalogUnit;
use App\Models\CatalogUnitPriceHistory;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogUnitController extends Controller
{
    public function index()
    {
        $availableUnits = Unit::where('type', 'global')
            ->whereNotIn('id', CatalogUnit::pluck('id_unit'))
            ->orderBy('unit')
            ->orderBy('brand')
            ->get(['id', 'sku', 'brand', 'model', 'unit']);

        return view('pages.catalog-unit.index', compact('availableUnits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_unit'   => 'required|exists:unit,id|unique:catalog_unit,id_unit',
            'price_idr' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0',
        ], [
            'id_unit.unique' => 'Unit ini sudah ada di katalog.',
        ]);

        $catalog = CatalogUnit::create([
            'id_unit'   => $request->id_unit,
            'price_idr' => $request->price_idr,
            'price_usd' => $request->price_usd,
            'spec_note' => $request->spec_note,
            'is_active' => true,
        ]);

        CatalogUnitPriceHistory::create([
            'id_catalog_unit' => $catalog->id,
            'price_idr'       => $request->price_idr,
            'price_usd'       => $request->price_usd,
            'changed_by'      => Auth::id(),
            'note'            => 'Penambahan unit ke katalog',
        ]);

        return redirect()->route('catalog-unit.show', $catalog->id)
            ->with('message', 'Unit berhasil ditambahkan ke katalog.');
    }

    public function show($id)
    {
        $catalog = CatalogUnit::with(['unit', 'priceHistory.changedBy'])->findOrFail($id);
        return view('pages.catalog-unit.detail', compact('catalog'));
    }

    public function update(Request $request, $id)
    {
        $priceIdr = (int) preg_replace('/\D/', '', $request->input('price_idr', 0));
        $request->merge(['price_idr' => $priceIdr]);

        $request->validate([
            'price_idr' => 'required|integer|min:0',
            'price_usd' => 'required|numeric|min:0',
        ]);

        $catalog = CatalogUnit::findOrFail($id);
        $priceChanged = $catalog->price_idr != $request->price_idr
            || $catalog->price_usd != $request->price_usd;

        $catalog->price_idr = $request->price_idr;
        $catalog->price_usd = $request->price_usd;
        $catalog->spec_note = $request->spec_note;
        $catalog->is_active = $request->boolean('is_active', true);
        $catalog->save();

        if ($priceChanged) {
            CatalogUnitPriceHistory::create([
                'id_catalog_unit' => $catalog->id,
                'price_idr'       => $request->price_idr,
                'price_usd'       => $request->price_usd,
                'changed_by'      => Auth::id(),
                'note'            => $request->note,
            ]);
        }

        return redirect()->route('catalog-unit.show', $id)
            ->with('message', 'Katalog berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $catalog = CatalogUnit::findOrFail($id);
        $catalog->delete();
        return response()->json(1);
    }

    public function search(Request $request)
    {
        $q = $request->query('q', '');

        $results = CatalogUnit::with('unit')
            ->where('is_active', true)
            ->whereHas('unit', function ($query) use ($q) {
                $query->where('sku', 'like', "%$q%")
                    ->orWhere('brand', 'like', "%$q%")
                    ->orWhere('model', 'like', "%$q%")
                    ->orWhere('unit', 'like', "%$q%");
            })
            ->limit(20)
            ->get()
            ->map(function ($item) {
                $u = $item->unit;
                return [
                    'id'               => $item->id,
                    'id_unit'          => $item->id_unit,
                    'sku'              => $u->sku,
                    'brand'            => $u->brand,
                    'model'            => $u->model,
                    'category'         => $u->unit,
                    'price_idr'        => $item->price_idr,
                    'price_usd'        => $item->price_usd,
                    'power'            => $u->power,
                    'air_cap'          => $u->air_cap,
                    'bar'              => $u->bar,
                    'cooling'          => $u->cooling,
                    'connect'          => $u->connect,
                    'exhaust'          => $u->exhaust,
                    'refrigerant_type' => $u->refrigerant_type,
                    'pdp'              => $u->pdp,
                    'dimension'        => $u->dimension,
                    'weight'           => $u->weight,
                    'spec_note'        => $item->spec_note,
                    'desc'             => $u->desc,
                    'type_unit'        => $u->type_unit,
                ];
            });

        return response()->json(['data' => $results]);
    }
}
