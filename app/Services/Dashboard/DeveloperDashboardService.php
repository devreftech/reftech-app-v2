<?php

namespace App\Services\Dashboard;

use App\Models\Activities;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\HelpdeskTicket;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\UnitQuotation;
use App\Models\User;
use App\Services\MaintenanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DeveloperDashboardService
{
    /**
     * Mengumpulkan semua data telemetry, metrik, log, dan status sistem untuk Developer Dashboard.
     */
    public function getDeveloperDashboardData(): array
    {
        $telemetry = $this->getSystemTelemetry();
        $dbMetrics = $this->getDatabaseMetrics();
        $todayActivity = $this->getTodayMutationStats();
        $maintenance = MaintenanceService::getDetails();
        $logsData = $this->getParsedApplicationLogs(40);
        $todayAuditLogs = $this->getTodayAuditLogs(1, 10);
        $roleDistribution = $this->getUserRoleDistribution();
        $errorFindingsDaily = $this->getErrorFindingsDaily();

        return [
            'adminView'          => 'developer',
            'telemetry'          => $telemetry,
            'dbMetrics'          => $dbMetrics,
            'todayActivity'      => $todayActivity,
            'maintenance'        => $maintenance,
            'logsData'           => $logsData,
            'todayAuditLogs'     => $todayAuditLogs,
            'roleDistribution'   => $roleDistribution,
            'errorFindingsDaily' => $errorFindingsDaily,
        ];
    }

    /**
     * Rekap harian "Temuan Error System 500" (tab Helpdesk) — dipakai buat
     * grafik di Developer Dashboard. Error 500 tersimpan di helpdesk_tickets
     * dengan no_ticket berformat "ERR/YYYY/MM/NNN" (lihat app/Exceptions/Handler.php).
     */
    public function getErrorFindingsDaily(int $days = 14): array
    {
        $start = Carbon::today()->subDays($days - 1);
        $end = Carbon::today()->endOfDay();

        $rows = HelpdeskTicket::where('no_ticket', 'like', 'ERR/%')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $series = [];
        $period = \Carbon\CarbonPeriod::create($start, $end);
        foreach ($period as $date) {
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $series[] = (int) ($rows[$key] ?? 0);
        }

        $totalQuery = HelpdeskTicket::where('no_ticket', 'like', 'ERR/%');

        return [
            'labels'   => $labels,
            'series'   => $series,
            'total'    => (clone $totalQuery)->count(),
            'open'     => (clone $totalQuery)->where('status', 'Open')->count(),
            'resolved' => (clone $totalQuery)->where('status', 'Resolved')->count(),
            'today'    => (int) end($series) ?: 0,
        ];
    }

    /**
     * Dapatkan data telemetry runtime & server.
     */
    public function getSystemTelemetry(): array
    {
        // Ukur DB Latency
        $dbLatencyMs = null;
        $dbConnected = false;
        $dbVersion = 'Unknown';
        try {
            $start = microtime(true);
            $res = DB::select('SELECT VERSION() as ver');
            $dbLatencyMs = round((microtime(true) - $start) * 1000, 2);
            $dbConnected = true;
            if (!empty($res[0]->ver)) {
                $dbVersion = $res[0]->ver;
            }
        } catch (\Throwable $e) {
            $dbConnected = false;
            $dbLatencyMs = -1;
        }

        // Ukur Disk Space
        $diskFree = @disk_free_space(base_path());
        $diskTotal = @disk_total_space(base_path());
        $diskUsagePercent = ($diskTotal && $diskTotal > 0) ? round((($diskTotal - $diskFree) / $diskTotal) * 100, 1) : null;

        // Memory Usage
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);

        return [
            'php_version'        => PHP_VERSION,
            'laravel_version'    => app()->version(),
            'app_env'            => config('app.env', 'production'),
            'app_debug'          => (bool) config('app.debug', false),
            'app_url'            => config('app.url', 'http://127.0.0.1:8000'),
            'server_os'          => PHP_OS_FAMILY . ' (' . php_uname('s') . ')',
            'server_time'        => Carbon::now()->format('d M Y H:i:s T'),
            'db_connection'      => config('database.default', 'mysql'),
            'db_database'        => config('database.connections.' . config('database.default') . '.database', '-'),
            'db_connected'       => $dbConnected,
            'db_latency_ms'      => $dbLatencyMs,
            'db_version'         => $dbVersion,
            'cache_driver'       => config('cache.default', 'file'),
            'session_driver'     => config('session.driver', 'file'),
            'queue_driver'       => config('queue.default', 'sync'),
            'disk_free_bytes'    => $diskFree,
            'disk_total_bytes'   => $diskTotal,
            'disk_free_formatted'=> $this->formatBytes($diskFree),
            'disk_total_formatted'=> $this->formatBytes($diskTotal),
            'disk_usage_percent' => $diskUsagePercent,
            'memory_usage_fmt'   => $this->formatBytes($memoryUsage),
            'memory_peak_fmt'    => $this->formatBytes($memoryPeak),
        ];
    }

    /**
     * Hitung total metrik record utama dalam database.
     */
    public function getDatabaseMetrics(): array
    {
        return Cache::remember('dev_db_metrics_summary', 60, function () {
            $totalUsers = User::count();
            $activeUsers = User::where('active', '1')->count();
            $totalClients = Client::count();
            $totalLegacyQuotes = Quotation::where('level', '1')->where('is_primary', '1')->count();
            $totalSmartQuotes = UnitQuotation::where('is_latest', 1)->count();
            $totalQuotes = $totalLegacyQuotes + $totalSmartQuotes;

            $totalLegacyPo = Quotation::where('status', '100')->where('level', '1')->where('is_primary', '1')->count();
            $totalSmartPo = UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->count();
            $totalPo = $totalLegacyPo + $totalSmartPo;

            $totalActivities = Activities::count();
            $totalInvoices = Invoice::count();
            $totalProducts = Product::count();
            $totalLogs = ActivityLog::count();

            return [
                'total_users'      => $totalUsers,
                'active_users'     => $activeUsers,
                'total_clients'    => $totalClients,
                'total_quotes'     => $totalQuotes,
                'total_po'         => $totalPo,
                'total_activities' => $totalActivities,
                'total_invoices'   => $totalInvoices,
                'total_products'   => $totalProducts,
                'total_logs'       => $totalLogs,
            ];
        });
    }

    /**
     * Rekap mutasi transaksi data hari ini.
     */
    public function getTodayMutationStats(): array
    {
        $today = Carbon::today();

        $newClientsToday = Client::whereDate('created_at', $today)->count();
        $newLegacyQuotesToday = Quotation::whereDate('estimated_date', $today)->where('level', '1')->where('is_primary', '1')->count();
        $newSmartQuotesToday = UnitQuotation::whereDate('date', $today)->where('is_latest', 1)->count();
        $newQuotesToday = $newLegacyQuotesToday + $newSmartQuotesToday;

        $newLegacyPoToday = Quotation::whereDate('po_date', $today)->where('status', '100')->where('level', '1')->where('is_primary', '1')->count();
        $newSmartPoToday = UnitQuotation::whereDate('po_received', $today)->where('status', 'po_received')->where('is_latest', 1)->count();
        $newPoToday = $newLegacyPoToday + $newSmartPoToday;

        $newActivitiesToday = Activities::whereDate('date', $today)->count();
        $newLogsToday = ActivityLog::whereDate('created_at', $today)->count();

        return [
            'clients'    => $newClientsToday,
            'quotes'     => $newQuotesToday,
            'po'         => $newPoToday,
            'activities' => $newActivitiesToday,
            'logs'       => $newLogsToday,
        ];
    }

    /**
     * Distribusi User berdasarkan role.
     */
    public function getUserRoleDistribution(): array
    {
        return Cache::remember('dev_user_roles_dist', 120, function () {
            return User::select('role', DB::raw('COUNT(*) as total'))
                ->groupBy('role')
                ->orderByDesc('total')
                ->get()
                ->map(fn($r) => ['role' => $r->role ?? 'Unassigned', 'total' => (int) $r->total])
                ->toArray();
        });
    }

    /**
     * Ambil aktivitas audit khusus HARI BERJALAN dengan paginasi dan pencarian.
     */
    public function getTodayAuditLogs(int $page = 1, int $perPage = 10, ?string $search = null): array
    {
        try {
            $today = Carbon::today('Asia/Jakarta');
            $query = ActivityLog::with('user:id,name,role,email')
                ->whereBetween('created_at', [
                    $today->copy()->startOfDay(),
                    $today->copy()->endOfDay(),
                ])
                ->latest();

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('action', 'like', "%{$search}%")
                      ->orWhere('type', 'like', "%{$search}%")
                      ->orWhere('ip_address', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($uq) use ($search) {
                          $uq->where('name', 'like', "%{$search}%")
                             ->orWhere('role', 'like', "%{$search}%");
                      });
                });
            }

            $paginator = $query->paginate($perPage, ['*'], 'page', $page);

            $items = collect($paginator->items())->map(function ($log) {
                return [
                    'id'          => $log->id,
                    'user_name'   => $log->user->name ?? 'System/Guest',
                    'user_role'   => $log->user->role ?? '-',
                    'type'        => $log->type ?? 'General',
                    'action'      => $log->action ?? 'Action',
                    'description' => $log->description ?? '-',
                    'ip_address'  => $log->ip_address ?? '127.0.0.1',
                    'time_ago'    => $log->created_at ? $log->created_at->diffForHumans() : '-',
                    'timestamp'   => $log->created_at ? $log->created_at->format('H:i:s') : '-',
                ];
            })->toArray();

            return [
                'items'        => $items,
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'has_more'     => $paginator->hasMorePages(),
            ];
        } catch (\Throwable $e) {
            return [
                'items'        => [],
                'current_page' => 1,
                'last_page'    => 1,
                'per_page'     => $perPage,
                'total'        => 0,
                'has_more'     => false,
            ];
        }
    }

    /**
     * Membaca dan mem-parse isi laravel.log dengan aman.
     */
    public function getParsedApplicationLogs(int $limit = 40): array
    {
        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            return [
                'exists'       => false,
                'file_size'    => '0 B',
                'entries'      => [],
                'error_count_24h' => 0,
                'warning_count_24h' => 0,
            ];
        }

        $fileSize = File::size($logPath);
        $fileSizeFmt = $this->formatBytes($fileSize);

        // Baca maksimal 2MB terakhir dari log file agar tidak boros memory
        $maxBytes = 2 * 1024 * 1024;
        $content = '';
        $fp = @fopen($logPath, 'r');
        if ($fp) {
            if ($fileSize > $maxBytes) {
                fseek($fp, -$maxBytes, SEEK_END);
            }
            $content = fread($fp, $maxBytes);
            fclose($fp);
        }

        if (empty($content)) {
            return [
                'exists'       => true,
                'file_size'    => $fileSizeFmt,
                'entries'      => [],
                'error_count_24h' => 0,
                'warning_count_24h' => 0,
            ];
        }

        $pattern = '/\[(\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:[+-]\d{2}:\d{2})?)\]\s+([a-zA-Z0-9_\.-]+)\.([a-zA-Z]+):\s+(.*?)(?=(?:\[\d{4}-\d{2}-\d{2}|\z))/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $entries = [];
        $errorCount24h = 0;
        $warningCount24h = 0;
        $now = Carbon::now();
        $cutoff24h = $now->copy()->subHours(24);

        // Proses dari entri terbaru (reverse)
        $reversed = array_reverse($matches);
        $count = 0;

        foreach ($reversed as $m) {
            $timestampRaw = $m[1];
            $env = $m[2];
            $level = strtoupper($m[3]);
            $body = trim($m[4]);

            // Ekstrak baris pertama pesan vs stack trace
            $lines = explode("\n", $body, 2);
            $message = trim($lines[0] ?? '');
            $stackTrace = trim($lines[1] ?? '');

            $entryTime = null;
            try {
                $entryTime = Carbon::parse($timestampRaw);
            } catch (\Throwable $e) {
                // Ignore parse errors
            }

            if ($entryTime && $entryTime->gte($cutoff24h)) {
                if (in_array($level, ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR'], true)) {
                    $errorCount24h++;
                } elseif (in_array($level, ['WARNING', 'NOTICE'], true)) {
                    $warningCount24h++;
                }
            }

            if ($count < $limit) {
                $entries[] = [
                    'id'          => md5($timestampRaw . $message . $count),
                    'timestamp'   => $timestampRaw,
                    'time_ago'    => $entryTime ? $entryTime->diffForHumans() : $timestampRaw,
                    'environment' => $env,
                    'level'       => $level,
                    'message'     => $message,
                    'stack_trace' => $stackTrace,
                    'has_trace'   => !empty($stackTrace),
                ];
                $count++;
            }
        }

        return [
            'exists'            => true,
            'file_size'         => $fileSizeFmt,
            'entries'           => $entries,
            'error_count_24h'   => $errorCount24h,
            'warning_count_24h' => $warningCount24h,
        ];
    }

    /**
     * Eksekusi Quick Action DevOps.
     */
    public function executeAction(string $action): array
    {
        try {
            switch ($action) {
                case 'clear_cache':
                    Artisan::call('optimize:clear');
                    $output = Artisan::output();
                    return [
                        'success' => true,
                        'message' => 'Cache aplikasi, config, route, dan view berhasil di-clear.',
                        'output'  => trim($output),
                    ];

                case 'cache_config':
                    Artisan::call('config:cache');
                    $output = Artisan::output();
                    return [
                        'success' => true,
                        'message' => 'Konfigurasi aplikasi berhasil di-cache.',
                        'output'  => trim($output),
                    ];

                case 'cache_routes':
                    Artisan::call('route:cache');
                    $output = Artisan::output();
                    return [
                        'success' => true,
                        'message' => 'Daftar routing berhasil di-cache.',
                        'output'  => trim($output),
                    ];

                case 'clear_views':
                    Artisan::call('view:clear');
                    $output = Artisan::output();
                    return [
                        'success' => true,
                        'message' => 'Compiled Blade views berhasil di-clear.',
                        'output'  => trim($output),
                    ];

                case 'clear_logs':
                    $logPath = storage_path('logs/laravel.log');
                    if (File::exists($logPath)) {
                        File::put($logPath, '');
                    }
                    return [
                        'success' => true,
                        'message' => 'File application log (laravel.log) berhasil dikosongkan.',
                        'output'  => 'laravel.log has been truncated.',
                    ];

                case 'ping_db':
                    $start = microtime(true);
                    $res = DB::select('SELECT VERSION() as ver, NOW() as server_time');
                    $latency = round((microtime(true) - $start) * 1000, 2);
                    $ver = $res[0]->ver ?? 'Unknown';
                    $time = $res[0]->server_time ?? '-';
                    return [
                        'success' => true,
                        'message' => "Koneksi Database MySQL OK! Latency: {$latency} ms.",
                        'output'  => "MySQL Version: {$ver}\nServer Time: {$time}\nPing Response: {$latency} ms",
                    ];

                default:
                    return [
                        'success' => false,
                        'message' => "Aksi '{$action}' tidak dikenali.",
                    ];
            }
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengeksekusi aksi: ' . $e->getMessage(),
                'output'  => $e->getTraceAsString(),
            ];
        }
    }

    /**
     * Format bytes menjadi string yang mudah dibaca (KB, MB, GB).
     */
    private function formatBytes($bytes, int $precision = 2): string
    {
        if (!$bytes || $bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
