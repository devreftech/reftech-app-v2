<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DeveloperDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeveloperDashboardController extends Controller
{
    protected DeveloperDashboardService $service;

    public function __construct(DeveloperDashboardService $service)
    {
        $this->service = $service;
    }

    /**
     * Tampilkan halaman Developer Dashboard utama.
     */
    public function index(Request $request)
    {
        if (!Auth::user()?->isDeveloper()) {
            abort(403, 'Akses khusus role Developer.');
        }

        $data = $this->service->getDeveloperDashboardData();

        return view('pages.developer.dashboard', $data);
    }

    /**
     * Endpoint API Telemetry JSON untuk auto-refresh data server & live radar.
     */
    public function ajaxTelemetry()
    {
        if (!Auth::user()?->isDeveloper()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $telemetry = $this->service->getSystemTelemetry();
        $dbMetrics = $this->service->getDatabaseMetrics();
        $todayActivity = $this->service->getTodayMutationStats();
        $maintenance = \App\Services\MaintenanceService::getDetails();

        return response()->json([
            'success'       => true,
            'telemetry'     => $telemetry,
            'dbMetrics'     => $dbMetrics,
            'todayActivity' => $todayActivity,
            'maintenance'   => $maintenance,
        ]);
    }

    /**
     * Endpoint API JSON untuk grafik harian "Temuan Error System 500" (auto-refresh).
     */
    public function ajaxErrorFindingsDaily(Request $request)
    {
        if (!Auth::user()?->isDeveloper()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $days = (int) $request->input('days', 14);
        $data = $this->service->getErrorFindingsDaily($days);

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * Endpoint API untuk mengeksekusi Quick Actions DevOps.
     */
    public function runAction(Request $request)
    {
        if (!Auth::user()?->isDeveloper()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'action' => 'required|string',
        ]);

        $action = $request->input('action');
        $result = $this->service->executeAction($action);

        return response()->json($result);
    }

    /**
     * Endpoint API untuk mendapatkan detail log stack trace berdasarkan ID hash.
     */
    public function getLogDetail(Request $request)
    {
        if (!Auth::user()?->isDeveloper()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $id = $request->input('id');
        $logsData = $this->service->getParsedApplicationLogs(100);

        $target = null;
        foreach ($logsData['entries'] as $entry) {
            if ($entry['id'] === $id) {
                $target = $entry;
                break;
            }
        }

        if (!$target) {
            return response()->json(['success' => false, 'message' => 'Log entry tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'log'     => $target,
        ]);
    }

    /**
     * Endpoint API untuk memuat aktivitas audit hari ini dengan pagination dan search.
     */
    public function ajaxAuditLogs(Request $request)
    {
        if (!Auth::user()?->isDeveloper()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $page = (int) $request->input('page', 1);
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 10);

        $auditData = $this->service->getTodayAuditLogs($page, $perPage, $search);

        return response()->json([
            'success' => true,
            'data'    => $auditData,
        ]);
    }
}

