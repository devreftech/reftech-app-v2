<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::with('department')
            ->withCount('employees')
            ->orderBy('name')
            ->paginate(15);

        return view('pages.hr.positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('pages.hr.positions.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePositionRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        Position::create($data);

        return redirect()->route('employees.index', ['tab' => 'positions'])->with('success', 'Posisi berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('pages.hr.positions.edit', compact('position', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePositionRequest $request, Position $position)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $position->update($data);

        return redirect()->route('employees.index', ['tab' => 'positions'])->with('success', 'Posisi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        if ($position->employees()->exists()) {
            return back()->with('error', 'Posisi ini masih dipakai oleh data karyawan, tidak bisa dihapus.');
        }

        $position->delete();

        return redirect()->route('employees.index', ['tab' => 'positions'])->with('success', 'Posisi berhasil dihapus');
    }

    /**
     * Remove multiple positions by selection.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:positions,id',
        ]);

        $ids = $validated['ids'];
        $deletedCount = 0;
        $failedCount = 0;
        $failedNames = [];

        foreach ($ids as $id) {
            $position = Position::find($id);
            if (!$position) continue;

            if ($position->employees()->exists()) {
                $failedCount++;
                $failedNames[] = $position->name;
                continue;
            }

            $position->delete();
            $deletedCount++;
        }

        if ($failedCount > 0 && $deletedCount > 0) {
            return redirect()->route('employees.index', ['tab' => 'positions'])
                ->with('warning', "{$deletedCount} posisi berhasil dihapus. {$failedCount} posisi (" . implode(', ', $failedNames) . ") dilewati karena masih digunakan oleh data karyawan.");
        } elseif ($failedCount > 0 && $deletedCount === 0) {
            return redirect()->route('employees.index', ['tab' => 'positions'])
                ->with('error', "Gagal menghapus posisi terpilih (" . implode(', ', $failedNames) . ") karena masih digunakan oleh data karyawan.");
        }

        return redirect()->route('employees.index', ['tab' => 'positions'])
            ->with('success', "{$deletedCount} posisi terpilih berhasil dihapus.");
    }
}
