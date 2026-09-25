<?php

use App\Http\Controllers\Hr\AssetController;
use App\Http\Controllers\Hr\AttendanceController;
use App\Http\Controllers\Hr\DashboardController;
use App\Http\Controllers\Hr\DepartmentController;
use App\Http\Controllers\Hr\EmployeeController;
use App\Http\Controllers\Hr\EvaluationController;
use App\Http\Controllers\Hr\LeaveController;
use App\Http\Controllers\Hr\PayrollController;
use App\Http\Controllers\Hr\PortalController;
use App\Http\Controllers\Hr\PositionController;
use App\Http\Controllers\Hr\ReimbursementController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════════════
// MODUL ENTERPRISE HR MANAGEMENT (HRMS SUITE)
// ══════════════════════════════════════════════════════════════════════
Route::middleware(['auth'])->group(function () {

    // ══════════════════════════════════════════════════════════════════════
    // PORTAL MANDIRI KARYAWAN (EMPLOYEE SELF-SERVICE) — SEMUA ROLE STAF
    // ══════════════════════════════════════════════════════════════════════
    Route::get('/hr/my-portal', [PortalController::class, 'index'])->name('hr.portal.index');
    Route::get('/hr/my-portal/verify-pre-clockin', [PortalController::class, 'verifyPreClockIn'])->name('hr.portal.verify-pre-clockin');
    Route::post('/hr/my-portal/clock-in', [PortalController::class, 'clockIn'])->name('hr.portal.clockin');
    Route::post('/hr/my-portal/clock-out', [PortalController::class, 'clockOut'])->name('hr.portal.clockout');

    // ══════════════════════════════════════════════════════════════════════
    // HR MANAGEMENT (ADMIN, DEVELOPMENT & FINANCE ONLY)
    // ══════════════════════════════════════════════════════════════════════
    Route::middleware(['hr.access'])->group(function () {

        // ── 0. HR Executive Hub & Dashboard ──────────────────────────────
        Route::get('/hr', [DashboardController::class, 'index'])->name('hr.index');
        Route::get('/hr/dashboard', [DashboardController::class, 'index'])->name('hr.dashboard');

        // ── 1. Core HR & Struktur Organisasi (Fase 1) ────────────────────
        Route::post('/employees/bulk-status', [EmployeeController::class, 'bulkUpdateStatus'])->name('employees.bulk-status');
        Route::post('/employees/bulk-delete', [EmployeeController::class, 'bulkDestroy'])->name('employees.bulk-destroy');
        Route::resource('/employees', EmployeeController::class);
        Route::get('/hr/employees', [EmployeeController::class, 'index'])->name('hr.employees.index');

        Route::post('/departments/bulk-delete', [DepartmentController::class, 'bulkDestroy'])->name('departments.bulk-destroy');
        Route::resource('/departments', DepartmentController::class)->except(['show']);

        Route::post('/positions/bulk-delete', [PositionController::class, 'bulkDestroy'])->name('positions.bulk-destroy');
        Route::resource('/positions', PositionController::class)->except(['show']);

        // ── 2. Attendance & Leave Management (Fase 2) ────────────────────
        // Presensi Kehadiran & Jaringan WiFi Kantor
        Route::get('/hr/attendances', [AttendanceController::class, 'index'])->name('hr.attendances.index');
        Route::get('/hr/attendances/penalties', [AttendanceController::class, 'penalties'])->name('hr.attendances.penalties');
        Route::post('/hr/attendances', [AttendanceController::class, 'store'])->name('hr.attendances.store');
        Route::delete('/hr/attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('hr.attendances.destroy');
        Route::post('/hr/attendances/wifis', [AttendanceController::class, 'storeWifi'])->name('hr.attendances.wifis.store');
        Route::put('/hr/attendances/wifis/{wifi}', [AttendanceController::class, 'updateWifi'])->name('hr.attendances.wifis.update');
        Route::delete('/hr/attendances/wifis/{wifi}', [AttendanceController::class, 'destroyWifi'])->name('hr.attendances.wifis.destroy');
        Route::post('/hr/attendances/wifis/toggle-restriction', [AttendanceController::class, 'toggleWifiRestriction'])->name('hr.attendances.wifis.toggle-restriction');
        Route::post('/hr/attendances/settings/update', [AttendanceController::class, 'updateSecuritySettings'])->name('hr.attendances.settings.update');
        Route::post('/hr/attendances/auto-clockout-now', [AttendanceController::class, 'runAutoClockOutNow'])->name('hr.attendances.auto-clockout-now');

        // Cuti & Perizinan
        Route::get('/hr/leaves', [LeaveController::class, 'index'])->name('hr.leaves.index');
        Route::post('/hr/leaves', [LeaveController::class, 'store'])->name('hr.leaves.store');
        Route::post('/hr/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('hr.leaves.approve');
        Route::post('/hr/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('hr.leaves.reject');
        Route::post('/hr/leaves/balances/bulk', [LeaveController::class, 'updateBulkBalance'])->name('hr.leaves.balances.bulk');
        Route::post('/hr/leaves/balances/individual', [LeaveController::class, 'updateIndividualBalance'])->name('hr.leaves.balances.individual');
        Route::post('/hr/leaves/balances/{balance}/toggle', [LeaveController::class, 'toggleBalanceStatus'])->name('hr.leaves.balances.toggle');

        // ── 3. Payroll Engine & Slip Gaji Digital (Fase 3) ───────────────
        Route::get('/hr/payrolls', [PayrollController::class, 'index'])->name('hr.payrolls.index');
        Route::post('/hr/payrolls', [PayrollController::class, 'store'])->name('hr.payrolls.store');
        Route::get('/hr/payrolls/{payroll}', [PayrollController::class, 'show'])->name('hr.payrolls.show');
        Route::patch('/hr/payrolls/{payroll}/status', [PayrollController::class, 'updateStatus'])->name('hr.payrolls.status');
        Route::get('/hr/payrolls/slip/{item}', [PayrollController::class, 'slip'])->name('hr.payrolls.slip');
        Route::post('/hr/employees/salary/bulk-adjustment', [PayrollController::class, 'bulkUpdateSalary'])->name('hr.employees.salary.bulk');
        Route::post('/hr/employees/{employee}/salary', [PayrollController::class, 'updateSalary'])->name('hr.employees.salary');
        Route::post('/hr/payrolls/{payroll}/post-expense', [PayrollController::class, 'postToExpense'])->name('hr.payrolls.post-expense');

        // ── 4. Reimbursement & Work Assets (Fase 4) ──────────────────────
        // Klaim Biaya Operasional
        Route::get('/hr/reimbursements', [ReimbursementController::class, 'index'])->name('hr.reimbursements.index');
        Route::post('/hr/reimbursements', [ReimbursementController::class, 'store'])->name('hr.reimbursements.store');
        Route::post('/hr/reimbursements/{reimbursement}/approve', [ReimbursementController::class, 'approve'])->name('hr.reimbursements.approve');
        Route::post('/hr/reimbursements/{reimbursement}/reject', [ReimbursementController::class, 'reject'])->name('hr.reimbursements.reject');
        Route::post('/hr/reimbursements/{reimbursement}/paid', [ReimbursementController::class, 'markPaid'])->name('hr.reimbursements.paid');

        // Inventaris Alat Kerja
        Route::get('/hr/assets', [AssetController::class, 'index'])->name('hr.assets.index');
        Route::post('/hr/assets', [AssetController::class, 'store'])->name('hr.assets.store');
        Route::put('/hr/assets/{asset}', [AssetController::class, 'update'])->name('hr.assets.update');
        Route::delete('/hr/assets/{asset}', [AssetController::class, 'destroy'])->name('hr.assets.destroy');

        // ── 5. Evaluasi Kinerja (Fase 5) ────────────────────────────────
        Route::get('/hr/evaluations', [EvaluationController::class, 'index'])->name('hr.evaluations.index');
        Route::post('/hr/evaluations', [EvaluationController::class, 'store'])->name('hr.evaluations.store');
        Route::delete('/hr/evaluations/{evaluation}', [EvaluationController::class, 'destroy'])->name('hr.evaluations.destroy');
    });
});
