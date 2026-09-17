<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Bank;
use App\Models\DetailExpense;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\HrAttendance;
use App\Models\HrPayroll;
use App\Models\HrPayrollItem;
use App\Models\HrSalary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));

        $payrolls = HrPayroll::with(['generator', 'approver', 'expense'])
            ->withCount('items')
            ->where('period_year', $year)
            ->orderByDesc('period_month')
            ->get();

        $stats = [
            'total_periods' => $payrolls->count(),
            'total_paid_disbursement' => $payrolls->where('status', 'Paid')->sum('total_net_amount'),
            'pending_approval' => $payrolls->whereIn('status', ['Draft', 'Confirmed'])->count(),
        ];

        // Also fetch active employees for salary management tab
        $employees = Employee::with(['user', 'department', 'position', 'salary', 'salaryHistories.creator'])
            ->where('employment_status', '!=', 'Resign')
            ->orderBy('id')
            ->get();

        return view('pages.hr.payrolls.index', compact('payrolls', 'stats', 'employees', 'year'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2020|max:2035',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $month = (int) $validated['period_month'];
        $year = (int) $validated['period_year'];

        // Check if payroll period already generated
        if (HrPayroll::where('period_month', $month)->where('period_year', $year)->exists()) {
            return redirect()->back()->with('error', "Periode penggajian untuk bulan {$month} tahun {$year} sudah pernah dibuat.");
        }

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $monthName = $startDate->translatedFormat('F');
        $code = 'PAY-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $payroll = HrPayroll::create([
                'code' => $code,
                'title' => "Gaji Karyawan {$monthName} {$year}",
                'period_month' => $month,
                'period_year' => $year,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'payment_date' => $validated['payment_date'],
                'status' => 'Draft',
                'generated_by' => Auth::id(),
                'notes' => $validated['notes'],
            ]);

            $employees = Employee::with('salary')
                ->where('employment_status', '!=', 'Resign')
                ->get();

            $totalBasic = 0;
            $totalAllowance = 0;
            $totalOvertime = 0;
            $totalDeductions = 0;
            $totalNet = 0;

            foreach ($employees as $index => $emp) {
                $salary = $emp->salary ?? HrSalary::create([
                    'employee_id' => $emp->id,
                    'basic_salary' => 5500000.00,
                    'transport_allowance' => 500000.00,
                    'meal_allowance' => 650000.00,
                    'position_allowance' => 750000.00,
                    'bpjs_kesehatan' => 55000.00,
                    'bpjs_ketenagakerjaan' => 110000.00,
                ]);

                // Count attendance in this period
                $attendanceRecords = HrAttendance::where('employee_id', $emp->id)
                    ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->get();

                $presentDays = $attendanceRecords->where('status', 'Hadir')->count();
                $absenceDays = $attendanceRecords->where('status', 'Alpa')->count();
                $overtimeMinutes = $attendanceRecords->sum('overtime_minutes');
                $overtimeHours = (int) round($overtimeMinutes / 60);

                // Overtime pay: standard ~ Rp 25.000 / hour
                $overtimePay = $overtimeHours * 25000;

                // Absence deduction: e.g. Rp 100.000 per unexcused absence
                $deductionAbsence = $absenceDays * 100000;
                $deductionBpjs = (float) ($salary->bpjs_kesehatan + $salary->bpjs_ketenagakerjaan);
                
                // Akumulasi denda keterlambatan presensi
                $totalLatePenalty = (float) $attendanceRecords->sum('penalty_amount');
                $deductionOther = $totalLatePenalty;

                $totalItemDeductions = $deductionAbsence + $deductionBpjs + $deductionOther;
                $itemAllowances = $salary->total_allowance;
                $netSalary = max(0, ($salary->basic_salary + $itemAllowances + $overtimePay) - $totalItemDeductions);

                $slipNumber = 'SLIP/' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '/' . str_pad($emp->id, 4, '0', STR_PAD_LEFT);

                HrPayrollItem::create([
                    'payroll_id' => $payroll->id,
                    'employee_id' => $emp->id,
                    'slip_number' => $slipNumber,
                    'basic_salary' => $salary->basic_salary,
                    'transport_allowance' => $salary->transport_allowance,
                    'meal_allowance' => $salary->meal_allowance,
                    'position_allowance' => $salary->position_allowance,
                    'other_allowance' => $salary->other_allowance,
                    'overtime_pay' => $overtimePay,
                    'deduction_absence' => $deductionAbsence,
                    'deduction_bpjs' => $deductionBpjs,
                    'deduction_other' => $deductionOther,
                    'net_salary' => $netSalary,
                    'attendance_days' => $presentDays,
                    'absence_days' => $absenceDays,
                    'overtime_hours' => $overtimeHours,
                    'payment_status' => 'Unpaid',
                    'meta_data' => [
                        'bank_name' => $salary->bank_name,
                        'bank_account_number' => $salary->bank_account_number,
                        'bank_account_holder' => $salary->bank_account_holder,
                        'late_penalty_total' => $totalLatePenalty,
                        'late_count' => $attendanceRecords->where('late_minutes', '>', 0)->count(),
                    ],
                ]);

                $totalBasic += $salary->basic_salary;
                $totalAllowance += $itemAllowances;
                $totalOvertime += $overtimePay;
                $totalDeductions += $totalItemDeductions;
                $totalNet += $netSalary;
            }

            $payroll->update([
                'total_basic' => $totalBasic,
                'total_allowance' => $totalAllowance,
                'total_overtime' => $totalOvertime,
                'total_deductions' => $totalDeductions,
                'total_net_amount' => $totalNet,
            ]);

            DB::commit();
            return redirect()->route('hr.payrolls.show', $payroll->id)
                ->with('success', "Batch payroll periode {$monthName} {$year} berhasil digenerate untuk {$employees->count()} karyawan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal generate payroll: ' . $e->getMessage());
        }
    }

    public function show(HrPayroll $payroll)
    {
        $payroll->load([
            'generator',
            'approver',
            'expense.bank',
            'items.employee.user',
            'items.employee.department',
            'items.employee.position'
        ]);

        $banks = Bank::orderBy('bank')->get();
        $accounts = Account::where('category', 'Expense')->orderBy('code')->get();

        return view('pages.hr.payrolls.show', compact('payroll', 'banks', 'accounts'));
    }

    public function postToExpense(Request $request, HrPayroll $payroll)
    {
        if ($payroll->expense_id) {
            return redirect()->back()->with('error', 'Batch payroll ini sudah pernah dibukukan ke Finance Expense (No. Voucher: ' . $payroll->expense?->no_expense . ').');
        }

        $validated = $request->validate([
            'id_bank' => 'required|exists:bank,id',
            'id_account' => 'required|exists:account,id',
            'payment_date' => 'required|date',
            'memo' => 'nullable|string|max:500',
        ]);

        $bank = Bank::findOrFail($validated['id_bank']);
        $totalAmount = (float) $payroll->total_net_amount;

        if ($totalAmount <= 0) {
            return redirect()->back()->with('error', 'Total nilai gaji bersih tidak boleh 0.');
        }

        DB::beginTransaction();
        try {
            // 1. Generate No Expense dengan format Romawi standar RefTech
            $romans = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
            $now = Carbon::parse($validated['payment_date']);
            $roman = $romans[$now->month - 1];
            $year = $now->year;
            $count = Expense::where('no_expense', 'LIKE', "EXP%-{$roman}-{$year}")->count();
            $noExpense = 'EXP' . str_pad($count + 1, 3, '0', STR_PAD_LEFT) . '-' . $roman . '-' . $year;

            // 2. Potong saldo Bank
            $bank->saldo -= $totalAmount;
            $bank->save();

            // 3. Simpan Voucher Expense Header
            $memo = $validated['memo'] ?: "Pembayaran Payroll Gaji Karyawan - {$payroll->title} ({$payroll->code})";
            $expense = new Expense();
            $expense->id_bank = $bank->id;
            $expense->no_expense = $noExpense;
            $expense->no_invoice = $payroll->code;
            $expense->memo = $memo;
            $expense->date = $validated['payment_date'];
            $expense->amount = $totalAmount;
            $expense->save();

            // 4. Simpan Detail Jurnal Beban COA (Biaya Gaji & Upah)
            $detailExpense = new DetailExpense();
            $detailExpense->id_expense = $expense->id;
            $detailExpense->id_account = $validated['id_account'];
            $detailExpense->memo = "Gaji Bersih Karyawan {$payroll->title}";
            $detailExpense->amount = $totalAmount;
            $detailExpense->save();

            // 5. Update status Payroll menjadi Paid & hubungkan expense_id
            $payroll->update([
                'expense_id' => $expense->id,
                'status' => 'Paid',
                'payment_date' => $validated['payment_date'],
            ]);

            // 6. Tandai seluruh slip gaji karyawan telah dibayar (Paid)
            $payroll->items()->update([
                'payment_status' => 'Paid',
                'paid_at' => Carbon::now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', "Batch Payroll berhasil dibukukan ke Finance Expense dengan voucher #{$noExpense}! Saldo bank {$bank->bank} dipotong sebesar Rp " . number_format($totalAmount, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memposting payroll ke Finance Expense: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, HrPayroll $payroll)
    {
        $validated = $request->validate([
            'status' => 'required|in:Draft,Confirmed,Approved,Paid',
        ]);

        $status = $validated['status'];
        $updates = ['status' => $status];

        if ($status === 'Approved') {
            $updates['approved_by'] = Auth::id();
            $updates['approved_at'] = Carbon::now();
        }

        if ($status === 'Paid') {
            // Mark all items as paid
            $payroll->items()->update([
                'payment_status' => 'Paid',
                'paid_at' => Carbon::now(),
            ]);
        }

        $payroll->update($updates);

        return redirect()->back()->with('success', "Status batch payroll berhasil diubah menjadi: {$status}.");
    }

    public function slip(HrPayrollItem $item)
    {
        $item->load(['payroll', 'employee.user', 'employee.department', 'employee.position']);

        return view('pages.hr.payrolls.slip', compact('item'));
    }

    public function updateSalary(Request $request, Employee $employee)
    {
        $data = $request->all();

        $nominalFields = [
            'basic_salary',
            'transport_allowance',
            'meal_allowance',
            'position_allowance',
            'other_allowance',
            'bpjs_kesehatan',
            'bpjs_ketenagakerjaan',
        ];

        foreach ($nominalFields as $field) {
            if (isset($data[$field])) {
                $clean = preg_replace('/[^0-9]/', '', (string)$data[$field]);
                $data[$field] = $clean !== '' ? (float)$clean : 0;
            }
        }

        $request->merge($data);

        $validated = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'meal_allowance' => 'nullable|numeric|min:0',
            'position_allowance' => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',
            'bpjs_kesehatan' => 'nullable|numeric|min:0',
            'bpjs_ketenagakerjaan' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:100',
            'effective_date' => 'nullable|date',
            'change_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $currentSalary = $employee->salary;
        $prevBasic = $currentSalary ? (float)$currentSalary->basic_salary : 0;
        $prevGross = $currentSalary ? (float)$currentSalary->total_gross : 0;

        $newBasic = (float)$validated['basic_salary'];
        $newAllowances = (float)($validated['transport_allowance'] ?? 0) +
                         (float)($validated['meal_allowance'] ?? 0) +
                         (float)($validated['position_allowance'] ?? 0) +
                         (float)($validated['other_allowance'] ?? 0);
        $newGross = $newBasic + $newAllowances;

        $incAmount = $prevGross > 0 ? ($newGross - $prevGross) : 0;
        $incPct = ($prevGross > 0 && $incAmount != 0) ? round(($incAmount / $prevGross) * 100, 2) : 0;

        $salaryData = collect($validated)->except(['change_type', 'notes'])->toArray();
        if (empty($salaryData['effective_date'])) {
            $salaryData['effective_date'] = date('Y-m-d');
        }

        HrSalary::updateOrCreate(
            ['employee_id' => $employee->id],
            $salaryData
        );

        // Record history log
        \App\Models\HrSalaryHistory::create([
            'employee_id' => $employee->id,
            'effective_date' => $validated['effective_date'] ?? date('Y-m-d'),
            'basic_salary' => $newBasic,
            'previous_basic_salary' => $prevBasic,
            'transport_allowance' => $validated['transport_allowance'] ?? 0,
            'meal_allowance' => $validated['meal_allowance'] ?? 0,
            'position_allowance' => $validated['position_allowance'] ?? 0,
            'other_allowance' => $validated['other_allowance'] ?? 0,
            'bpjs_kesehatan' => $validated['bpjs_kesehatan'] ?? 0,
            'bpjs_ketenagakerjaan' => $validated['bpjs_ketenagakerjaan'] ?? 0,
            'total_gross' => $newGross,
            'previous_total_gross' => $prevGross,
            'increment_amount' => $incAmount,
            'increment_percentage' => $incPct,
            'change_type' => $validated['change_type'] ?? ($prevBasic > 0 ? 'Kenaikan Tahunan' : 'Penetapan Awal'),
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Struktur gaji pokok & riwayat kenaikan gaji karyawan berhasil disimpan.');
    }

    public function bulkUpdateSalary(Request $request)
    {
        $rawVal = $request->input('adjustment_value');
        if ($request->input('adjustment_type') === 'nominal') {
            $rawVal = preg_replace('/[^0-9]/', '', (string)$rawVal);
        } else {
            $rawVal = str_replace(',', '.', (string)$rawVal);
        }
        $request->merge(['adjustment_value' => $rawVal]);

        $validated = $request->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'required|exists:employees,id',
            'adjustment_type' => 'required|in:percentage,nominal',
            'adjustment_value' => 'required|numeric|min:0.01',
            'effective_date' => 'required|date',
            'change_type' => 'required|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $employeeIds = $validated['employee_ids'];
        $adjType = $validated['adjustment_type'];
        $adjValue = (float)$validated['adjustment_value'];
        $effectiveDate = $validated['effective_date'];
        $changeType = $validated['change_type'];
        $notes = $validated['notes'];

        $updatedCount = 0;

        DB::beginTransaction();
        try {
            $employees = Employee::with('salary')->whereIn('id', $employeeIds)->get();

            foreach ($employees as $employee) {
                $sal = $employee->salary;
                $prevBasic = $sal ? (float)$sal->basic_salary : 0;
                $prevGross = $sal ? (float)$sal->total_gross : 0;

                // Hitung Gaji Pokok Baru
                if ($adjType === 'percentage') {
                    $newBasic = $prevBasic * (1 + ($adjValue / 100));
                    // Pembulatan ke ribuan terdekat
                    $newBasic = round($newBasic, -3);
                } else {
                    $newBasic = $prevBasic + $adjValue;
                }

                $allowances = $sal ? (float)$sal->total_allowance : 0;
                $newGross = $newBasic + $allowances;

                $incAmount = $prevGross > 0 ? ($newGross - $prevGross) : ($newBasic - $prevBasic);
                $incPct = ($prevGross > 0 && $incAmount != 0) ? round(($incAmount / $prevGross) * 100, 2) : ($adjType === 'percentage' ? $adjValue : 0);

                // Update atau Create HrSalary
                HrSalary::updateOrCreate(
                    ['employee_id' => $employee->id],
                    [
                        'basic_salary' => $newBasic,
                        'effective_date' => $effectiveDate,
                    ]
                );

                // Log History
                \App\Models\HrSalaryHistory::create([
                    'employee_id' => $employee->id,
                    'effective_date' => $effectiveDate,
                    'basic_salary' => $newBasic,
                    'previous_basic_salary' => $prevBasic,
                    'transport_allowance' => $sal?->transport_allowance ?? 0,
                    'meal_allowance' => $sal?->meal_allowance ?? 0,
                    'position_allowance' => $sal?->position_allowance ?? 0,
                    'other_allowance' => $sal?->other_allowance ?? 0,
                    'bpjs_kesehatan' => $sal?->bpjs_kesehatan ?? 0,
                    'bpjs_ketenagakerjaan' => $sal?->bpjs_ketenagakerjaan ?? 0,
                    'total_gross' => $newGross,
                    'previous_total_gross' => $prevGross,
                    'increment_amount' => $incAmount,
                    'increment_percentage' => $incPct,
                    'change_type' => $changeType,
                    'notes' => $notes ?: ($adjType === 'percentage' ? "Kenaikan massal +{$adjValue}%" : "Kenaikan massal +Rp " . number_format($adjValue, 0, ',', '.')),
                    'created_by' => Auth::id(),
                ]);

                $updatedCount++;
            }

            DB::commit();

            $typeLabel = $adjType === 'percentage' ? "+{$adjValue}%" : "+Rp " . number_format($adjValue, 0, ',', '.');
            return redirect()->back()->with('success', "Berhasil menerapkan kenaikan gaji massal ({$typeLabel}) untuk {$updatedCount} karyawan terpilih.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses kenaikan gaji massal: ' . $e->getMessage());
        }
    }
}
