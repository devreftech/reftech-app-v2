<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource, with search (nama/email/NIK) and
     * filter per departemen.
     */
    public function index()
    {
        // 1. Optimized Employees query with select columns for fast loading
        $employees = Employee::with([
                'user:id,name,email,image',
                'department:id,name',
                'position:id,name'
            ])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nik', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when(request('id_department'), function ($query, $idDepartment) {
                $query->where('id_department', $idDepartment);
            })
            ->when(request('employment_status'), function ($query, $status) {
                $query->where('employment_status', $status);
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        // 2. High-speed single query for employee status metrics
        $statusCounts = Employee::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN employment_status = 'Tetap' THEN 1 ELSE 0 END) as tetap,
            SUM(CASE WHEN employment_status = 'Kontrak' THEN 1 ELSE 0 END) as kontrak,
            SUM(CASE WHEN employment_status = 'Probation' THEN 1 ELSE 0 END) as probation,
            SUM(CASE WHEN employment_status = 'Perlu Verifikasi' THEN 1 ELSE 0 END) as perlu_verifikasi,
            SUM(CASE WHEN employment_status = 'Resign' THEN 1 ELSE 0 END) as resign
        ")->first();

        $stats = [
            'total' => (int) ($statusCounts->total ?? 0),
            'tetap' => (int) ($statusCounts->tetap ?? 0),
            'kontrak' => (int) ($statusCounts->kontrak ?? 0),
            'probation' => (int) ($statusCounts->probation ?? 0),
            'perlu_verifikasi' => (int) ($statusCounts->perlu_verifikasi ?? 0),
            'resign' => (int) ($statusCounts->resign ?? 0),
        ];

        // 3. Fast AJAX response for employee tab filtering & pagination
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'html' => view('pages.hr.employees._table', compact('employees'))->render(),
                'stats' => $stats,
                'total' => $employees->total(),
            ]);
        }

        // 4. Departments with counts for Tab 2
        $departments = Department::with('parent:id,name')
            ->withCount(['positions', 'employees'])
            ->orderBy('name')
            ->get();

        // 5. Positions with counts for Tab 3
        $positions = Position::with('department:id,name')
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        return view('pages.hr.employees.index', compact('employees', 'departments', 'positions', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $positions = Position::where('is_active', true)->orderBy('name')->get();

        // Hanya user yang belum punya data karyawan dan bukan role Client yang bisa dipilih.
        $users = User::whereDoesntHave('employee')
            ->where('role', '!=', 'Client')
            ->orderBy('name')
            ->get();

        return view('pages.hr.employees.create', compact('departments', 'positions', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();
        $data['can_online_attendance'] = $request->boolean('can_online_attendance', true);

        Employee::create($data);

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load(['user', 'department', 'position', 'salary', 'salaryHistories.creator']);

        return view('pages.hr.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $employee->loadMissing(['user', 'department', 'position']);

        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $positions = Position::where('is_active', true)->orderBy('name')->get();

        // User yang sudah tertaut ke employee ini tetap ditampilkan di dropdown,
        // ditambah user lain yang masih belum punya data karyawan.
        $users = User::where('role', '!=', 'Client')
            ->where(function ($query) use ($employee) {
                $query->whereDoesntHave('employee')
                    ->orWhere('id', $employee->user_id);
            })
            ->orderBy('name')
            ->get();

        return view('pages.hr.employees.edit', compact('employee', 'departments', 'positions', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $data = $request->validated();
        $data['can_online_attendance'] = $request->boolean('can_online_attendance', true);

        $employee->update($data);

        return redirect()->route('employees.show', $employee->id)->with('success', 'Data karyawan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil dihapus');
    }

    /**
     * Remove multiple employees by selection.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:employees,id',
        ]);

        $ids = $validated['ids'];
        $count = count($ids);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            Employee::whereIn('id', $ids)->delete();
            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('employees.index', ['tab' => 'employees'])
                ->with('success', "{$count} data karyawan terpilih berhasil dihapus.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data karyawan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update employment status for selected employees.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:employees,id',
            'status' => 'required|in:Tetap,Kontrak,Probation,Resign',
        ]);

        $ids = $validated['ids'];
        $status = $validated['status'];
        $count = count($ids);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $updateData = ['employment_status' => $status];
            if ($status === 'Resign') {
                $updateData['resign_date'] = \Carbon\Carbon::now();
            } else {
                $updateData['resign_date'] = null;
            }

            Employee::whereIn('id', $ids)->update($updateData);
            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('employees.index', ['tab' => 'employees'])
                ->with('success', "Status kepegawaian {$count} karyawan terpilih berhasil diubah menjadi: {$status}.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui status kepegawaian: ' . $e->getMessage());
        }
    }
}
