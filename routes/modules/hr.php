<?php

use App\Http\Controllers\Hr\AssetController;
use App\Http\Controllers\Hr\AttendanceController;
use App\Http\Controllers\Hr\BonusController;
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
    Route::post('/hr/my-portal/leave-request', [PortalController::class, 'storeLeave'])->name('hr.portal.leave.store');
    // ESS Reimbursements (Employee Klaim Operasional)
    Route::post('/hr/reimbursements', [ReimbursementController::class, 'store'])->name('hr.reimbursements.store');
    Route::put('/hr/reimbursements/{reimbursement}', [ReimbursementController::class, 'update'])->name('hr.reimbursements.update');
    Route::delete('/hr/reimbursements/{reimbursement}', [ReimbursementController::class, 'destroy'])->name('hr.reimbursements.destroy');
    Route::get('/hr/notifications/pending-check', [ReimbursementController::class, 'pendingCheck'])->name('hr.notifications.pending-check');

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
        Route::get('/hr/attendances/settings', [AttendanceController::class, 'settings'])->name('hr.attendances.settings');
        Route::get('/hr/attendances/penalties', [AttendanceController::class, 'penalties'])->name('hr.attendances.penalties');
        Route::get('/hr/attendances/penalties/export', [AttendanceController::class, 'exportPenalties'])->name('hr.attendances.penalties.export');
        Route::post('/hr/attendances', [AttendanceController::class, 'store'])->name('hr.attendances.store');
        Route::delete('/hr/attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('hr.attendances.destroy');
        Route::post('/hr/attendances/wifis', [AttendanceController::class, 'storeWifi'])->name('hr.attendances.wifis.store');
        Route::put('/hr/attendances/wifis/{wifi}', [AttendanceController::class, 'updateWifi'])->name('hr.attendances.wifis.update');
        Route::delete('/hr/attendances/wifis/{wifi}', [AttendanceController::class, 'destroyWifi'])->name('hr.attendances.wifis.destroy');
        Route::post('/hr/attendances/wifis/toggle-restriction', [AttendanceController::class, 'toggleWifiRestriction'])->name('hr.attendances.wifis.toggle-restriction');
        Route::post('/hr/attendances/settings/update', [AttendanceController::class, 'updateSecuritySettings'])->name('hr.attendances.settings.update');
        Route::post('/hr/attendances/auto-clockout-now', [AttendanceController::class, 'runAutoClockOutNow'])->name('hr.attendances.auto-clockout-now');
        Route::post('/hr/attendances/holidays', [AttendanceController::class, 'storeHoliday'])->name('hr.attendances.holidays.store');
        Route::put('/hr/attendances/holidays/{holiday}', [AttendanceController::class, 'updateHoliday'])->name('hr.attendances.holidays.update');
        Route::delete('/hr/attendances/holidays/{holiday}', [AttendanceController::class, 'destroyHoliday'])->name('hr.attendances.holidays.destroy');
        Route::post('/hr/attendances/holidays/import-defaults', [AttendanceController::class, 'importDefaultHolidays'])->name('hr.attendances.holidays.import-defaults');

        // Cuti & Perizinan
        Route::get('/hr/leaves', [LeaveController::class, 'index'])->name('hr.leaves.index');
        Route::post('/hr/leaves', [LeaveController::class, 'store'])->name('hr.leaves.store');
        Route::post('/hr/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('hr.leaves.approve');
        Route::post('/hr/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('hr.leaves.reject');
        Route::post('/hr/leaves/balances/bulk', [LeaveController::class, 'updateBulkBalance'])->name('hr.leaves.balances.bulk');
        Route::post('/hr/leaves/balances/individual', [LeaveController::class, 'updateIndividualBalance'])->name('hr.leaves.balances.individual');
        Route::post('/hr/leaves/balances/{balance}/toggle', [LeaveController::class, 'toggleBalanceStatus'])->name('hr.leaves.balances.toggle');
        Route::post('/hr/leaves/settings/alert-recipients', [LeaveController::class, 'updateAlertSettings'])->name('hr.leaves.settings.alert-recipients');

        // ── 3. Payroll Engine & Slip Gaji Digital (Fase 3) ───────────────
        Route::get('/hr/payrolls', [PayrollController::class, 'index'])->name('hr.payrolls.index');
        Route::post('/hr/payrolls', [PayrollController::class, 'store'])->name('hr.payrolls.store');
        Route::get('/hr/payrolls/{payroll}', [PayrollController::class, 'show'])->name('hr.payrolls.show');
        Route::patch('/hr/payrolls/{payroll}/status', [PayrollController::class, 'updateStatus'])->name('hr.payrolls.status');
        Route::get('/hr/payrolls/slip/{item}', [PayrollController::class, 'slip'])->name('hr.payrolls.slip');
        Route::post('/hr/employees/salary/bulk-adjustment', [PayrollController::class, 'bulkUpdateSalary'])->name('hr.employees.salary.bulk');
        Route::post('/hr/employees/{employee}/salary', [PayrollController::class, 'updateSalary'])->name('hr.employees.salary');
        Route::post('/hr/payrolls/{payroll}/post-expense', [PayrollController::class, 'postToExpense'])->name('hr.payrolls.post-expense');
        Route::post('/hr/payrolls/{payroll}/resync', [PayrollController::class, 'resync'])->name('hr.payrolls.resync');

        // ── 4. Reimbursement & Work Assets (Fase 4) ──────────────────────
        // Klaim Biaya Operasional
        Route::get('/hr/reimbursements', [ReimbursementController::class, 'index'])->name('hr.reimbursements.index');
        Route::post('/hr/reimbursements/{reimbursement}/approve', [ReimbursementController::class, 'approve'])->name('hr.reimbursements.approve');
        Route::post('/hr/reimbursements/{reimbursement}/reject', [ReimbursementController::class, 'reject'])->name('hr.reimbursements.reject');
        Route::post('/hr/reimbursements/{reimbursement}/paid', [ReimbursementController::class, 'markPaid'])->name('hr.reimbursements.paid');
        Route::post('/hr/reimbursements/{reimbursement}/post-expense', [ReimbursementController::class, 'postToExpense'])->name('hr.reimbursements.post-expense');

        // Inventaris Alat Kerja
        Route::get('/hr/assets', [AssetController::class, 'index'])->name('hr.assets.index');
        Route::post('/hr/assets', [AssetController::class, 'store'])->name('hr.assets.store');
        Route::put('/hr/assets/{asset}', [AssetController::class, 'update'])->name('hr.assets.update');
        Route::delete('/hr/assets/{asset}', [AssetController::class, 'destroy'])->name('hr.assets.destroy');

        // ── 5. Evaluasi Kinerja (Fase 5) ────────────────────────────────
        Route::get('/hr/evaluations', [EvaluationController::class, 'index'])->name('hr.evaluations.index');
        Route::post('/hr/evaluations', [EvaluationController::class, 'store'])->name('hr.evaluations.store');
        Route::delete('/hr/evaluations/{evaluation}', [EvaluationController::class, 'destroy'])->name('hr.evaluations.destroy');

        // ── 6. Bonus & Insentif Semesteran ──────────────────────────────
        Route::get('/hr/bonuses', [BonusController::class, 'index'])->name('hr.bonuses.index');
        Route::post('/hr/bonuses', [BonusController::class, 'store'])->name('hr.bonuses.store');
        Route::get('/hr/bonuses/{bonus}', [BonusController::class, 'show'])->name('hr.bonuses.show');
        Route::patch('/hr/bonuses/{bonus}/status', [BonusController::class, 'updateStatus'])->name('hr.bonuses.status');
        Route::delete('/hr/bonuses/{bonus}', [BonusController::class, 'destroy'])->name('hr.bonuses.destroy');
        Route::post('/hr/bonuses/{bonus}/post-expense', [BonusController::class, 'postToExpense'])->name('hr.bonuses.post-expense');
        Route::post('/hr/bonuses/{bonus}/add-employee', [BonusController::class, 'addEmployee'])->name('hr.bonuses.add-employee');
        Route::delete('/hr/bonuses/recipients/{recipient}', [BonusController::class, 'removeRecipient'])->name('hr.bonuses.recipients.destroy');
        Route::post('/hr/bonuses/recipients/{recipient}/items', [BonusController::class, 'updateRecipientItems'])->name('hr.bonuses.recipients.items');
        Route::get('/hr/bonuses/slip/{recipient}', [BonusController::class, 'slip'])->name('hr.bonuses.slip');
    });
});
