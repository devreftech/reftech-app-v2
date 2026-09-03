<?php

namespace App\Http\Controllers;

use App\Models\PipingMaterial;
use App\Models\PipingMaterialVendorPrice;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PipingMaterialController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $search = $request->query('search');

        $query = PipingMaterial::with(['vendorPrices.supplier'])->orderBy('category')->orderBy('item_name');

        if ($category) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%")
                  ->orWhere('material_type', 'like', "%{$search}%");
            });
        }

        $materials = $query->get();
        $suppliers = Supplier::orderBy('supplier', 'asc')->get(['id', 'supplier']);

        $stats = [
            'total'      => PipingMaterial::count(),
            'pipe'       => PipingMaterial::where('category', 'pipe')->count(),
            'fitting'    => PipingMaterial::where('category', 'fitting')->count(),
            'valve'      => PipingMaterial::where('category', 'valve')->count(),
            'support'    => PipingMaterial::where('category', 'support')->count(),
            'consumable' => PipingMaterial::where('category', 'consumable')->count(),
        ];

        return view('pages.piping.materials.index', compact('materials', 'suppliers', 'stats', 'category', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code'             => 'nullable|string|max:50|unique:piping_materials,item_code',
            'category'              => 'required|in:pipe,fitting,valve,support,consumable,other',
            'material_type'         => 'nullable|string|max:100',
            'item_name'             => 'required|string|max:255',
            'size'                  => 'nullable|string|max:100',
            'connection_type'       => 'nullable|string|max:100',
            'unit'                  => 'required|string|max:50',
            'length_per_unit'       => 'nullable|numeric|min:0',
            'default_waste_percent' => 'nullable|numeric|min:0|max:100',
            'notes'                 => 'nullable|string',
            // Initial vendor price (optional)
            'id_supplier'           => 'nullable|exists:supplier,id',
            'price_idr'             => 'nullable|numeric|min:0',
            'price_date'            => 'nullable|date',
            'vendor_notes'          => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $material = PipingMaterial::create([
                'item_code'             => $validated['item_code'] ?? null,
                'category'              => $validated['category'],
                'material_type'         => $validated['material_type'] ?? null,
                'item_name'             => $validated['item_name'],
                'size'                  => $validated['size'] ?? null,
                'connection_type'       => $validated['connection_type'] ?? null,
                'unit'                  => $validated['unit'],
                'length_per_unit'       => $validated['length_per_unit'] ?? ($validated['category'] === 'pipe' ? 6.00 : null),
                'default_waste_percent' => $validated['default_waste_percent'] ?? 5.00,
                'notes'                 => $validated['notes'] ?? null,
            ]);

            if (!empty($validated['id_supplier']) && isset($validated['price_idr'])) {
                PipingMaterialVendorPrice::create([
                    'id_piping_material' => $material->id,
                    'id_supplier'        => $validated['id_supplier'],
                    'price_idr'          => $validated['price_idr'],
                    'date'               => $validated['price_date'] ?? now()->toDateString(),
                    'notes'              => $validated['vendor_notes'] ?? null,
                    'is_primary'         => true,
                ]);
            }

            DB::commit();
            return redirect()->route('piping-materials.index')->with('success', 'Material Piping berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $material = PipingMaterial::findOrFail($id);

        $validated = $request->validate([
            'item_code'             => 'nullable|string|max:50|unique:piping_materials,item_code,' . $id,
            'category'              => 'required|in:pipe,fitting,valve,support,consumable,other',
            'material_type'         => 'nullable|string|max:100',
            'item_name'             => 'required|string|max:255',
            'size'                  => 'nullable|string|max:100',
            'connection_type'       => 'nullable|string|max:100',
            'unit'                  => 'required|string|max:50',
            'length_per_unit'       => 'nullable|numeric|min:0',
            'default_waste_percent' => 'nullable|numeric|min:0|max:100',
            'notes'                 => 'nullable|string',
        ]);

        $material->update($validated);

        return redirect()->route('piping-materials.index')->with('success', 'Data Material berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $material = PipingMaterial::findOrFail($id);
        $material->delete();

        return redirect()->route('piping-materials.index')->with('success', 'Material berhasil dihapus.');
    }

    public function storeVendorPrice(Request $request, $materialId)
    {
        $material = PipingMaterial::findOrFail($materialId);

        $validated = $request->validate([
            'id_supplier' => 'required|exists:supplier,id',
            'price_idr'   => 'required|numeric|min:0',
            'date'        => 'nullable|date',
            'notes'       => 'nullable|string|max:255',
            'is_primary'  => 'nullable|boolean',
        ]);

        if (!empty($validated['is_primary'])) {
            PipingMaterialVendorPrice::where('id_piping_material', $materialId)->update(['is_primary' => false]);
        }

        PipingMaterialVendorPrice::updateOrCreate(
            [
                'id_piping_material' => $materialId,
                'id_supplier'        => $validated['id_supplier'],
            ],
            [
                'price_idr'  => $validated['price_idr'],
                'date'       => $validated['date'] ?? now()->toDateString(),
                'notes'      => $validated['notes'] ?? null,
                'is_primary' => $validated['is_primary'] ?? false,
            ]
        );

        return back()->with('success', 'Harga vendor berhasil diperbarui.');
    }

    public function deleteVendorPrice($priceId)
    {
        $price = PipingMaterialVendorPrice::findOrFail($priceId);
        $price->delete();

        return back()->with('success', 'Harga vendor berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $category = $request->get('category', '');

        $materials = PipingMaterial::with(['vendorPrices.supplier' => function ($q) {
            $q->select('id', 'supplier');
        }])
        ->when($category, fn($q) => $q->where('category', $category))
        ->when($query, function ($q) use ($query) {
            $q->where(function ($sub) use ($query) {
                $sub->where('item_name', 'like', "%{$query}%")
                    ->orWhere('item_code', 'like', "%{$query}%")
                    ->orWhere('size', 'like', "%{$query}%")
                    ->orWhere('material_type', 'like', "%{$query}%");
            });
        })
        ->limit(30)
        ->get();

        $results = $materials->map(function ($mat) {
            $sortedPrices = $mat->vendorPrices->sortBy('price_idr');
            $cheapest = $sortedPrices->first();
            return [
                'id'                    => $mat->id,
                'item_code'             => $mat->item_code,
                'category'              => $mat->category,
                'item_name'             => $mat->item_name,
                'material_type'         => $mat->material_type,
                'size'                  => $mat->size,
                'connection_type'       => $mat->connection_type,
                'unit'                  => $mat->unit,
                'length_per_unit'       => (float) $mat->length_per_unit,
                'default_waste_percent' => (float) $mat->default_waste_percent,
                'cheapest_price_idr'    => $cheapest ? (float) $cheapest->price_idr : 0,
                'cheapest_supplier'     => $cheapest && $cheapest->supplier ? $cheapest->supplier->supplier : 'Belum Ada Harga',
                'vendor_prices'         => $sortedPrices->map(function ($vp) {
                    return [
                        'id_supplier'   => $vp->id_supplier,
                        'supplier_name' => $vp->supplier ? $vp->supplier->supplier : '-',
                        'price_idr'     => (float) $vp->price_idr,
                        'date'          => $vp->date ? $vp->date->format('d/m/Y') : '-',
                        'notes'         => $vp->notes,
                        'is_primary'    => (bool) $vp->is_primary,
                    ];
                })->values(),
            ];
        });

        return response()->json(['data' => $results]);
    }
}
