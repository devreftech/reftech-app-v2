<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::with('parent')
            ->withCount(['positions', 'employees'])
            ->orderBy('name')
            ->paginate(15);

        return view('pages.hr.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentOptions = Department::orderBy('name')->get();

        return view('pages.hr.departments.create', compact('parentOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        Department::create($data);

        return redirect()->route('employees.index', ['tab' => 'departments'])->with('success', 'Departemen berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $parentOptions = Department::where('id', '!=', $department->id)->orderBy('name')->get();

        return view('pages.hr.departments.edit', compact('department', 'parentOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $department->update($data);

        return redirect()->route('employees.index', ['tab' => 'departments'])->with('success', 'Departemen berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        if ($department->children()->exists()) {
            return back()->with('error', 'Departemen ini masih punya sub-departemen, pindahkan/hapus dulu sub-departemennya.');
        }

        if ($department->positions()->exists()) {
            return back()->with('error', 'Departemen ini masih dipakai oleh posisi tertentu, tidak bisa dihapus.');
        }

        if ($department->employees()->exists()) {
            return back()->with('error', 'Departemen ini masih dipakai oleh data karyawan, tidak bisa dihapus.');
        }

        $department->delete();

        return redirect()->route('employees.index', ['tab' => 'departments'])->with('success', 'Departemen berhasil dihapus');
    }

    /**
     * Remove multiple departments by selection.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:departments,id',
        ]);

        $ids = $validated['ids'];
        $deletedCount = 0;
        $failedCount = 0;
        $failedNames = [];

        foreach ($ids as $id) {
            $department = Department::find($id);
            if (!$department) continue;

            if ($department->children()->exists() || $department->positions()->exists() || $department->employees()->exists()) {
                $failedCount++;
                $failedNames[] = $department->name;
                continue;
            }

            $department->delete();
            $deletedCount++;
        }

        if ($failedCount > 0 && $deletedCount > 0) {
            return redirect()->route('employees.index', ['tab' => 'departments'])
                ->with('warning', "{$deletedCount} departemen berhasil dihapus. {$failedCount} departemen (" . implode(', ', $failedNames) . ") dilewati karena masih memiliki relasi karyawan, posisi, atau sub-departemen.");
        } elseif ($failedCount > 0 && $deletedCount === 0) {
            return redirect()->route('employees.index', ['tab' => 'departments'])
                ->with('error', "Gagal menghapus departemen terpilih (" . implode(', ', $failedNames) . ") karena masih memiliki relasi karyawan, posisi, atau sub-departemen.");
        }

        return redirect()->route('employees.index', ['tab' => 'departments'])
            ->with('success', "{$deletedCount} departemen terpilih berhasil dihapus.");
    }
}
