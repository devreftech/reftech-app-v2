<?php

namespace App\Http\Controllers;

use App\Models\RentalAccessory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalAccessoryController extends Controller
{
    /**
     * Get list of rental accessories as JSON for DataTables.
     */
    public function data(Request $request)
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('rental_accessories')) {
            return response()->json(['data' => []]);
        }

        try {
            $group = $request->get('group');
            $query = RentalAccessory::query();

            if ($group) {
                $query->where('category', $group);
            }

            $data = $query->orderBy('id', 'desc')->get();

            return response()->json(['data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created accessory in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|in:hose,header,reducer,cable,nipple',
            'name' => 'required|string|max:255',
            'stock' => 'nullable|integer|min:0',
        ]);

        $data = $request->only([
            'category',
            'code',
            'name',
            'brand',
            'size',
            'length',
            'max_pressure',
            'connection',
            'material',
            'extra_spec',
            'stock',
            'condition',
            'rental_status',
            'location',
            'notes',
        ]);

        $data['stock'] = $data['stock'] ?? 1;
        $data['condition'] = $data['condition'] ?? 'ok';
        $data['rental_status'] = $data['rental_status'] ?? 'available';

        $accessory = RentalAccessory::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data aksesoris rental berhasil ditambahkan',
            'data' => $accessory,
        ]);
    }

    /**
     * Show accessory detail.
     */
    public function show($id)
    {
        $accessory = RentalAccessory::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $accessory,
        ]);
    }

    /**
     * Update the specified accessory in storage.
     */
    public function update(Request $request, $id)
    {
        $accessory = RentalAccessory::findOrFail($id);

        $request->validate([
            'category' => 'required|string|in:hose,header,reducer,cable,nipple',
            'name' => 'required|string|max:255',
            'stock' => 'nullable|integer|min:0',
        ]);

        $data = $request->only([
            'category',
            'code',
            'name',
            'brand',
            'size',
            'length',
            'max_pressure',
            'connection',
            'material',
            'extra_spec',
            'stock',
            'condition',
            'rental_status',
            'location',
            'notes',
        ]);

        $accessory->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data aksesoris rental berhasil diperbarui',
            'data' => $accessory,
        ]);
    }

    /**
     * Remove the specified accessory from storage.
     */
    public function destroy($id)
    {
        $accessory = RentalAccessory::findOrFail($id);
        $accessory->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data aksesoris rental berhasil dihapus',
        ]);
    }
}
