<?php

namespace App\Services\Hr;

use App\Models\Employee;
use App\Models\Hr\HrAttendance;
use App\Models\Hr\HrHoliday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollCutoffService
{
    /**
     * Ambil konfigurasi pengaturan cut-off & penggajian dari database.
     */
    public static function getCutoffSettings(): array
    {
        $settings = DB::table('hr_attendance_settings')->pluck('value', 'key')->toArray();

        return [
            'pay_date' => (int) ($settings['payroll_pay_date'] ?? 28),
            'release_time' => $settings['payroll_cutoff_release_time'] ?? '09:00',
            'auto_stepback_weekend' => ($settings['payroll_auto_stepback_weekend'] ?? '1') === '1',
            'auto_stepback_holiday' => ($settings['payroll_auto_stepback_holiday'] ?? '1') === '1',
            'alpha_penalty_rate' => (float) ($settings['alpha_penalty_rate'] ?? 50000),
        ];
    }

    /**
     * Hitung tanggal cut-off presensi efektif (Working-Day Cutoff) untuk bulan dan tahun tertentu.
     * Aturan Reftech:
     * - Target tanggal gajian: Tanggal dari setting (default: 28).
     * - Jika tanggal gajian jatuh pada hari Minggu -> Mundur ke Jumat.
     * - Jika tanggal gajian jatuh pada hari Senin -> Mundur ke Jumat minggu sebelumnya.
     * - Jika tanggal gajian jatuh pada hari Sabtu -> Mundur ke Jumat.
     * - Jika tanggal hasil mundur ternyata Libur Nasional (hr_holidays) -> Mundur terus ke hari kerja sebelumnya (Kamis, dst).
     *
     * @param int|null $month Bulan penggajian (1-12)
     * @param int|null $year Tahun penggajian (contoh: 2026)
     * @return Carbon Tanggal akhir cutoff presensi
     */
    public static function getCutoffEndDate(?int $month = null, ?int $year = null): Carbon
    {
        $now = Carbon::now();
        $month = $month ?: $now->month;
        $year = $year ?: $now->year;
        $config = self::getCutoffSettings();
        $payDate = min($config['pay_date'], 28); // amankan maksimal tgl 28/31

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $actualPayDay = min($payDate, $daysInMonth);
        $targetDate = Carbon::createFromDate($year, $month, $actualPayDay)->startOfDay();

        if ($config['auto_stepback_weekend']) {
            // Jika tanggal target jatuh hari Minggu -> mundur 2 hari ke Jumat
            if ($targetDate->isSunday()) {
                $targetDate->subDays(2);
            }
            // Jika tanggal target jatuh hari Senin -> mundur 3 hari ke Jumat minggu sebelumnya
            elseif ($targetDate->isMonday()) {
                $targetDate->subDays(3);
            }
            // Jika tanggal target jatuh hari Sabtu -> mundur 1 hari ke Jumat
            elseif ($targetDate->isSaturday()) {
                $targetDate->subDay();
            }
        }

        // Cek apakah tanggal target tersebut merupakan Hari Libur Nasional atau Weekend, jika iya mundur terus
        while (self::isNonWorkingDay($targetDate, $config)) {
            $targetDate->subDay();
        }

        return $targetDate;
    }

    /**
     * Cek apakah suatu tanggal adalah hari non-kerja berdasarkan setting aktif.
     */
    public static function isNonWorkingDay(Carbon $date, ?array $config = null): bool
    {
        $config = $config ?: self::getCutoffSettings();

        if ($config['auto_stepback_weekend'] && $date->isWeekend()) {
            return true;
        }

        if ($config['auto_stepback_holiday'] && HrHoliday::isHoliday($date)) {
            return true;
        }

        return false;
    }

    /**
     * Hitung periode lengkap (start_date s/d end_date) untuk penggajian bulan berjalan.
     * Start date = 1 hari setelah cutoff bulan sebelumnya.
     */
    public static function getPayrollPeriod(?int $month = null, ?int $year = null): array
    {
        $now = Carbon::now();
        $month = $month ?: $now->month;
        $year = $year ?: $now->year;
        $config = self::getCutoffSettings();

        $endDate = self::getCutoffEndDate($month, $year);

        // Cari cutoff bulan sebelumnya
        $prevMonthDate = Carbon::createFromDate($year, $month, 1)->subMonth();
        $prevCutoffEnd = self::getCutoffEndDate($prevMonthDate->month, $prevMonthDate->year);
        $startDate = $prevCutoffEnd->copy()->addDay();

        // Release datetime: Sesuai setting jam (contoh: 09:00:00) pada tanggal cutoff end
        $releaseTimeParts = explode(':', $config['release_time']);
        $hour = (int) ($releaseTimeParts[0] ?? 9);
        $minute = (int) ($releaseTimeParts[1] ?? 0);

        $releaseDateTime = $endDate->copy()->setTime($hour, $minute, 0);
        $isRecapReady = $now->gte($releaseDateTime);

        return [
            'month' => $month,
            'year' => $year,
            'month_name' => Carbon::createFromDate($year, $month, 1)->translatedFormat('F'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'release_time' => $config['release_time'],
            'release_datetime' => $releaseDateTime,
            'is_recap_ready' => $isRecapReady,
            'pay_date_setting' => $config['pay_date'],
        ];
    }

    /**
     * Ambil statistik rekapitulasi denda dan presensi untuk periode cutoff bulan berjalan.
     */
    public static function getCutoffRecapSummary(?int $month = null, ?int $year = null): array
    {
        $period = self::getPayrollPeriod($month, $year);
        $startDateStr = $period['start_date']->toDateString();
        $endDateStr = $period['end_date']->toDateString();

        $attendances = HrAttendance::whereBetween('date', [$startDateStr, $endDateStr])->get();

        $totalLatePenalty = (float) $attendances->sum('penalty_amount');
        $lateEmployeesCount = $attendances->where('penalty_amount', '>', 0)->pluck('employee_id')->unique()->count();
        $totalLateIncidents = $attendances->where('late_minutes', '>', 0)->count();
        $totalAbsenceDays = $attendances->where('status', 'Alpa')->count();
        $totalOvertimeMinutes = (int) $attendances->sum('overtime_minutes');
        $totalOvertimeHours = (int) round($totalOvertimeMinutes / 60);

        $config = self::getCutoffSettings();
        $alphaRate = $config['alpha_penalty_rate'];
        $totalAbsencePenalty = $totalAbsenceDays * $alphaRate;

        // Cek status batch payroll untuk periode ini
        $existingPayroll = \App\Models\HrPayroll::with('expense')
            ->where('period_month', $period['month'])
            ->where('period_year', $period['year'])
            ->first();

        $hasPayrollGenerated = $existingPayroll !== null;
        $isPaid = $existingPayroll && $existingPayroll->status === 'Paid';
        $hasExpensePosted = $existingPayroll && !empty($existingPayroll->expense_id);
        $isFullyCompleted = $isPaid && $hasExpensePosted;

        return [
            'period' => $period,
            'total_late_penalty' => $totalLatePenalty,
            'late_employees_count' => $lateEmployeesCount,
            'total_late_incidents' => $totalLateIncidents,
            'total_absence_days' => $totalAbsenceDays,
            'total_absence_penalty' => $totalAbsencePenalty,
            'total_overtime_hours' => $totalOvertimeHours,
            'total_deduction_combined' => $totalLatePenalty + $totalAbsencePenalty,
            'has_payroll_generated' => $hasPayrollGenerated,
            'existing_payroll' => $existingPayroll,
            'is_paid' => $isPaid,
            'has_expense_posted' => $hasExpensePosted,
            'is_fully_completed' => $isFullyCompleted,
        ];
    }

    /**
     * Hybrid Auto-Payroll: Otomatis generate atau sinkronkan ulang (re-sync) batch payroll
     * dengan snapshot data presensi, lembur, dan master gaji terbaru.
     */
    public static function generateOrSyncPayrollBatch(int $month, int $year, ?int $userId = null, bool $forceResync = false, ?string $paymentDate = null, ?string $notes = null): \App\Models\HrPayroll
    {
        $cutoffPeriod = self::getPayrollPeriod($month, $year);
        $startDate = $cutoffPeriod['start_date'];
        $endDate = $cutoffPeriod['end_date'];
        $monthName = $cutoffPeriod['month_name'];
        $code = 'PAY-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT);

        $payroll = \App\Models\HrPayroll::where('period_month', $month)
            ->where('period_year', $year)
            ->first();

        if ($payroll && !$forceResync) {
            return $payroll;
        }

        if ($payroll && $payroll->status === 'Paid') {
            throw new \Exception("Batch payroll periode {$monthName} {$year} sudah berstatus Paid (Lunas), tidak dapat di-regenerate.");
        }

        $config = self::getCutoffSettings();
        $alphaPenaltyRate = (float) ($config['alpha_penalty_rate'] ?? 50000);
        $payDateFormatted = $paymentDate ?: Carbon::createFromDate($year, $month, min($config['pay_date'], 28))->toDateString();

        DB::beginTransaction();
        try {
            if (!$payroll) {
                $payroll = \App\Models\HrPayroll::create([
                    'code' => $code,
                    'title' => "Gaji Karyawan {$monthName} {$year}",
                    'period_month' => $month,
                    'period_year' => $year,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'payment_date' => $payDateFormatted,
                    'status' => 'Draft',
                    'generated_by' => $userId,
                    'notes' => $notes ?: 'Auto-Generated saat periode cut-off presensi tercapai.',
                ]);
            } else {
                $payroll->update([
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'title' => "Gaji Karyawan {$monthName} {$year}",
                    'notes' => $notes ?: ($payroll->notes ?: 'Tersinkronkan ulang dengan data presensi terbaru.'),
                ]);
            }

            $employees = Employee::with('salary')
                ->where('employment_status', '!=', 'Resign')
                ->get();

            $totalBasic = 0;
            $totalAllowance = 0;
            $totalOvertime = 0;
            $totalDeductions = 0;
            $totalNet = 0;

            foreach ($employees as $emp) {
                $salary = $emp->salary ?? \App\Models\HrSalary::create([
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

                // Absence deduction: sesuai setting penalti alpa
                $deductionAbsence = $absenceDays * $alphaPenaltyRate;
                $deductionBpjs = (float) ($salary->bpjs_kesehatan + $salary->bpjs_ketenagakerjaan);
                
                // Akumulasi denda keterlambatan presensi
                $totalLatePenalty = (float) $attendanceRecords->sum('penalty_amount');
                $deductionOther = $totalLatePenalty;

                $totalItemDeductions = $deductionAbsence + $deductionBpjs + $deductionOther;
                $itemAllowances = $salary->total_allowance;
                $netSalary = max(0, ($salary->basic_salary + $itemAllowances + $overtimePay) - $totalItemDeductions);

                $slipNumber = 'SLIP/' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '/' . str_pad($emp->id, 4, '0', STR_PAD_LEFT);

                \App\Models\HrPayrollItem::updateOrCreate(
                    [
                        'payroll_id' => $payroll->id,
                        'employee_id' => $emp->id,
                    ],
                    [
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
                        'payment_status' => $payroll->status === 'Paid' ? 'Paid' : 'Unpaid',
                        'meta_data' => [
                            'bank_name' => $salary->bank_name,
                            'bank_account_number' => $salary->bank_account_number,
                            'bank_account_holder' => $salary->bank_account_holder,
                            'late_penalty_total' => $totalLatePenalty,
                            'late_count' => $attendanceRecords->where('late_minutes', '>', 0)->count(),
                        ],
                    ]
                );

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
            return $payroll;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
