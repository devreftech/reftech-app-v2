<?php

namespace App\Http\Controllers;

use App\Services\MaintenanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeveloperMaintenanceController extends Controller
{
    /**
     * Display the public maintenance page for blocked users/guests.
     */
    public function showMaintenancePage()
    {
        $details = MaintenanceService::getDetails();

        // If maintenance is not active, redirect to home
        if (!MaintenanceService::isActive()) {
            return redirect('/');
        }

        return view('errors.maintenance', compact('details'));
    }

    /**
     * Public JSON endpoint for client-side polling / status checking.
     */
    public function status()
    {
        return response()->json(MaintenanceService::getDetails());
    }

    /**
     * Developer-only: Display the maintenance control settings.
     */
    public function index()
    {
        if (Auth::user()?->role !== 'Developer') {
            abort(403, 'Akses khusus role Developer.');
        }

        $details = MaintenanceService::getDetails();
        return view('pages.developer.maintenance', compact('details'));
    }

    /**
     * Developer-only: Toggle maintenance mode ON or OFF.
     */
    public function toggle(Request $request)
    {
        if (Auth::user()?->role !== 'Developer') {
            abort(403, 'Akses khusus role Developer.');
        }

        $action = $request->input('action'); // 'activate', 'deactivate', 'schedule', 'cancel_plan'
        $startedBy = Auth::user()->name . ' (' . Auth::user()->email . ')';

        if ($action === 'activate') {
            $message = $request->input('message', 'Sistem sedang dalam proses pemeliharaan & pembaruan dari Staging ke Production.');
            $endTime = $request->input('end_time');

            MaintenanceService::activate($message, $endTime, $startedBy);

            return redirect()->back()->with('success', 'Maintenance Mode berhasil DIAKTIFKAN. Hanya role Developer yang dapat mengakses sistem.');
        } elseif ($action === 'deactivate') {
            MaintenanceService::deactivate();

            return redirect()->back()->with('success', 'Maintenance Mode berhasil DINONAKTIFKAN. Seluruh user dapat mengakses sistem kembali.');
        } elseif ($action === 'schedule') {
            $startTime = $request->input('plan_start_time');
            if (empty($startTime)) {
                return redirect()->back()->with('error', 'Waktu mulai maintenance wajib diisi.');
            }

            $endTime = $request->input('plan_end_time');
            $warnMinutes = (int) $request->input('plan_warn_minutes', 30);
            $message = $request->input('plan_message', 'Pemberitahuan: Sistem akan memasuki masa pemeliharaan (Maintenance). Mohon segera simpan pekerjaan dan transaksi Anda.');
            $autoActivate = $request->has('auto_activate');

            MaintenanceService::schedulePlan($startTime, $endTime, $warnMinutes, $message, $autoActivate, $startedBy);

            return redirect()->back()->with('success', 'Jadwal Pemeliharaan (Planning Maintenance) berhasil DISIMPAN. Notifikasi peringatan akan otomatis muncul ke semua user.');
        } elseif ($action === 'cancel_plan') {
            MaintenanceService::cancelPlan();

            return redirect()->back()->with('success', 'Jadwal Pemeliharaan berhasil DIBATALKAN.');
        }

        return redirect()->back();
    }
}
