{{-- Developer Command Center & DevOps Sentinel Content Component --}}
@php
    $telemetry = $telemetry ?? [];
    $dbMetrics = $dbMetrics ?? [];
    $todayActivity = $todayActivity ?? [];
    $maintenance = $maintenance ?? [];
    $logsData = $logsData ?? ['entries' => [], 'error_count_24h' => 0, 'warning_count_24h' => 0, 'file_size' => '0 B'];
    $recentAuditLogs = $recentAuditLogs ?? [];
    $roleDistribution = $roleDistribution ?? [];
    $errorFindingsDaily = $errorFindingsDaily ?? ['labels' => [], 'series' => [], 'total' => 0, 'open' => 0, 'resolved' => 0, 'today' => 0];
@endphp

@push('before-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/apex-charts/apex-charts.css" />
@endpush

<style>
    .dev-dashboard-root {
        --dev-bg-dark: #0f172a;
        --dev-card-bg: #1e293b;
        --dev-card-border: rgba(148, 163, 184, 0.12);
        --dev-primary: #6366f1;
        --dev-emerald: #10b981;
        --dev-cyan: #06b6d4;
        --dev-amber: #f59e0b;
        --dev-rose: #f43f5e;
    }

    .dev-card {
        border-radius: 14px;
        border: 1px solid var(--dev-card-border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .dev-card:hover {
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
    }

    .dev-card-dark {
        background: #1e293b;
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.08);
    }

    .dev-pulse-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: devPulse 2s infinite;
    }

    .dev-pulse-dot.danger {
        background-color: #f43f5e;
        box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.7);
    }

    @keyframes devPulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    .dev-terminal {
        background: #090d16;
        border-radius: 10px;
        color: #e2e8f0;
        font-family: 'Fira Code', 'Consolas', 'Monaco', monospace;
        font-size: 0.82rem;
        max-height: 480px;
        overflow-y: auto;
    }

    .dev-terminal::-webkit-scrollbar {
        width: 6px;
    }
    .dev-terminal::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 4px;
    }

    .dev-log-row {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding: 8px 12px;
        transition: background 0.15s ease;
        cursor: pointer;
    }

    .dev-log-row:hover {
        background: rgba(255, 255, 255, 0.04);
    }

    .dev-badge-tech {
        font-size: 0.72rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        letter-spacing: 0.3px;
    }

    .btn-dev-action {
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.85rem;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-dev-action:hover {
        transform: translateY(-2px);
    }
</style>

<div class="dev-dashboard-root mb-4">
    {{-- TOP TELEMETRY BANNER --}}
    <div class="card dev-card dev-card-dark mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #1e293b 100%);">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 text-white d-flex align-items-center justify-content-center" style="background: rgba(99, 102, 241, 0.25); border: 1px solid rgba(99, 102, 241, 0.4);">
                        <i class="mdi mdi-console-network fs-2 text-indigo"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <h4 class="mb-0 text-white fw-bold">Developer Command Center</h4>
                            <span class="badge bg-label-success rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1">
                                <span class="dev-pulse-dot me-1"></span>
                                <span id="dev-live-status">All Systems Operational</span>
                            </span>
                            @if ($telemetry['app_debug'] ?? false)
                                <span class="badge bg-label-warning rounded-pill px-2 py-1">
                                    <i class="mdi mdi-bug-outline me-1"></i> DEBUG ON
                                </span>
                            @else
                                <span class="badge bg-label-secondary rounded-pill px-2 py-1">
                                    <i class="mdi mdi-shield-check-outline me-1"></i> DEBUG OFF
                                </span>
                            @endif
                        </div>
                        <p class="mb-0 text-white-50 small">
                            <i class="mdi mdi-server-network me-1"></i> OS: <strong class="text-white">{{ $telemetry['server_os'] ?? 'Linux' }}</strong>
                            <span class="mx-2">•</span>
                            <i class="mdi mdi-database me-1"></i> DB: <strong class="text-white">{{ $telemetry['db_database'] ?? 'MySQL' }}</strong>
                            <span class="mx-2">•</span>
                            <i class="mdi mdi-clock-outline me-1"></i> Server Time: <strong class="text-white" id="dev-server-time">{{ $telemetry['server_time'] ?? '-' }}</strong>
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="px-3 py-2 rounded-3 text-center" style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.08);">
                        <div class="small text-white-50" style="font-size: 0.7rem;">PHP VERSION</div>
                        <div class="fw-bold text-white fs-6">v{{ $telemetry['php_version'] ?? '8.2' }}</div>
                    </div>
                    <div class="px-3 py-2 rounded-3 text-center" style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.08);">
                        <div class="small text-white-50" style="font-size: 0.7rem;">LARAVEL</div>
                        <div class="fw-bold text-white fs-6">v{{ $telemetry['laravel_version'] ?? '10.x' }}</div>
                    </div>
                    <div class="px-3 py-2 rounded-3 text-center" style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.08);">
                        <div class="small text-white-50" style="font-size: 0.7rem;">DB LATENCY</div>
                        <div class="fw-bold text-emerald fs-6" id="dev-db-latency">{{ $telemetry['db_latency_ms'] ?? 0 }} ms</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 ms-2" id="btn-refresh-telemetry" title="Refresh Telemetry">
                        <i class="mdi mdi-refresh me-1"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- CORE 4 KPI TELEMETRY CARDS --}}
    <div class="row g-3 mb-4">
        {{-- 1. Database Health & Size --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card dev-card h-100 p-3">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Database Sentinel</span>
                        <h4 class="mb-0 mt-1 fw-bold text-primary">{{ number_format($dbMetrics['total_quotes'] + $dbMetrics['total_clients'] + $dbMetrics['total_activities'], 0) }}</h4>
                    </div>
                    <div class="rounded-3 p-2 bg-label-primary">
                        <i class="mdi mdi-database-check fs-4"></i>
                    </div>
                </div>
                <div class="small text-muted mb-2">
                    <span>Quotes: <strong>{{ number_format($dbMetrics['total_quotes'] ?? 0) }}</strong></span>
                    <span class="mx-1">•</span>
                    <span>PO: <strong>{{ number_format($dbMetrics['total_po'] ?? 0) }}</strong></span>
                    <span class="mx-1">•</span>
                    <span>Clients: <strong>{{ number_format($dbMetrics['total_clients'] ?? 0) }}</strong></span>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top">
                    <span class="badge bg-label-success rounded-pill small">
                        <i class="mdi mdi-check-circle-outline me-1"></i> MySQL Connected
                    </span>
                    <small class="text-muted">{{ $telemetry['db_version'] ?? 'MySQL 8' }}</small>
                </div>
            </div>
        </div>

        {{-- 2. Storage & Memory --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card dev-card h-100 p-3">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Disk & Storage</span>
                        <h4 class="mb-0 mt-1 fw-bold text-info">{{ $telemetry['disk_free_formatted'] ?? '-' }} <span class="fs-7 text-muted fw-normal">Free</span></h4>
                    </div>
                    <div class="rounded-3 p-2 bg-label-info">
                        <i class="mdi mdi-harddisk fs-4"></i>
                    </div>
                </div>
                <div class="progress mb-2" style="height: 6px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $telemetry['disk_usage_percent'] ?? 20 }}%" aria-valuenow="{{ $telemetry['disk_usage_percent'] ?? 20 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top text-muted small">
                    <span>Used: <strong>{{ $telemetry['disk_usage_percent'] ?? 0 }}%</strong> of {{ $telemetry['disk_total_formatted'] ?? '-' }}</span>
                    <span>RAM: <strong>{{ $telemetry['memory_usage_fmt'] ?? '-' }}</strong></span>
                </div>
            </div>
        </div>

        {{-- 3. Users & Sessions --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card dev-card h-100 p-3">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">User Roster & Auth</span>
                        <h4 class="mb-0 mt-1 fw-bold text-success">{{ $dbMetrics['active_users'] ?? 0 }} <span class="fs-7 text-muted fw-normal">/ {{ $dbMetrics['total_users'] ?? 0 }} Active</span></h4>
                    </div>
                    <div class="rounded-3 p-2 bg-label-success">
                        <i class="mdi mdi-account-multiple-check fs-4"></i>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-1 mb-2">
                    @foreach (array_slice($roleDistribution, 0, 4) as $rd)
                        <span class="badge bg-label-secondary" style="font-size: 0.68rem;">{{ $rd['role'] }}: {{ $rd['total'] }}</span>
                    @endforeach
                </div>
                <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top text-muted small">
                    <span>Driver: <strong>{{ strtoupper($telemetry['session_driver'] ?? 'FILE') }}</strong></span>
                    <span>Cache: <strong>{{ strtoupper($telemetry['cache_driver'] ?? 'FILE') }}</strong></span>
                </div>
            </div>
        </div>

        {{-- 4. Errors & Logs 24h --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card dev-card h-100 p-3">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Exception Stream (24h)</span>
                        <h4 class="mb-0 mt-1 fw-bold {{ ($logsData['error_count_24h'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                            {{ $logsData['error_count_24h'] ?? 0 }} <span class="fs-7 text-muted fw-normal">Errors</span>
                        </h4>
                    </div>
                    <div class="rounded-3 p-2 {{ ($logsData['error_count_24h'] ?? 0) > 0 ? 'bg-label-danger' : 'bg-label-success' }}">
                        <i class="mdi mdi-alert-circle-outline fs-4"></i>
                    </div>
                </div>
                <div class="small text-muted mb-2">
                    <span>Warnings: <strong>{{ $logsData['warning_count_24h'] ?? 0 }}</strong></span>
                    <span class="mx-1">•</span>
                    <span>Log File Size: <strong>{{ $logsData['file_size'] ?? '0 B' }}</strong></span>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top">
                    @if (($logsData['error_count_24h'] ?? 0) === 0)
                        <span class="badge bg-label-success rounded-pill small">
                            <i class="mdi mdi-shield-check me-1"></i> Clean State
                        </span>
                    @else
                        <span class="badge bg-label-danger rounded-pill small">
                            <i class="mdi mdi-alert me-1"></i> Action Required
                        </span>
                    @endif
                    <a href="#section-log-stream" class="small text-primary fw-semibold">View Logs &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: MAINTENANCE SENTINEL & QUICK ACTION TOOLBOX --}}
    <div class="row g-3 mb-4">
        {{-- Maintenance Sentinel --}}
        <div class="col-lg-5">
            <div class="card dev-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-shield-lock-outline fs-4 text-warning"></i>
                        <h5 class="mb-0 fw-bold">Maintenance Sentinel</h5>
                    </div>
                    <a href="{{ route('developer.maintenance.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="mdi mdi-cog me-1"></i> Manage
                    </a>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-3" style="background: {{ ($maintenance['is_active'] ?? false) ? 'rgba(244, 63, 94, 0.08)' : 'rgba(16, 185, 129, 0.08)' }}; border: 1px solid {{ ($maintenance['is_active'] ?? false) ? 'rgba(244, 63, 94, 0.2)' : 'rgba(16, 185, 129, 0.2)' }};">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-2 {{ ($maintenance['is_active'] ?? false) ? 'bg-danger text-white' : 'bg-success text-white' }}">
                                <i class="mdi {{ ($maintenance['is_active'] ?? false) ? 'mdi-lock' : 'mdi-lock-open-variant' }} fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold {{ ($maintenance['is_active'] ?? false) ? 'text-danger' : 'text-success' }}">
                                    {{ ($maintenance['is_active'] ?? false) ? 'MAINTENANCE MODE ACTIVE' : 'SYSTEM LIVE & PUBLIC ACCESS' }}
                                </h6>
                                <small class="text-muted">
                                    {{ ($maintenance['is_active'] ?? false) ? 'Hanya Developer yang dapat mengakses sistem.' : 'Seluruh pengguna dapat bertransaksi normal.' }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="list-group list-group-flush small">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted"><i class="mdi mdi-palette-outline me-1"></i> Template Tampilan</span>
                            <span class="badge bg-label-dark">{{ ucfirst($maintenance['template'] ?? 'animated') }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted"><i class="mdi mdi-music-note me-1"></i> Ambient BGM</span>
                            <span class="fw-semibold {{ ($maintenance['bgm_enabled'] ?? false) ? 'text-success' : 'text-muted' }}">
                                {{ ($maintenance['bgm_enabled'] ?? false) ? ($maintenance['bgm_title'] ?? 'Enabled') : 'Disabled' }}
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted"><i class="mdi mdi-calendar-clock me-1"></i> Jadwal Maintenance</span>
                            @if (!empty($maintenance['plan_start_time']))
                                <span class="badge bg-label-warning">{{ $maintenance['plan_start_time'] }}</span>
                            @else
                                <span class="text-muted">Tidak ada jadwal</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Action Toolbox --}}
        <div class="col-lg-7">
            <div class="card dev-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-lightning-bolt-outline fs-4 text-primary"></i>
                        <h5 class="mb-0 fw-bold">DevOps Quick Actions</h5>
                    </div>
                    <span class="badge bg-label-primary rounded-pill">1-Click Artisan Triggers</span>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-outline-danger w-100 btn-dev-action" data-action="clear_cache">
                                <i class="mdi mdi-trash-can-outline fs-5"></i>
                                <div class="text-start">
                                    <div class="fw-bold">Optimize &amp; Clear All</div>
                                    <div class="small opacity-75" style="font-size: 0.72rem;">Flush app, config, route &amp; views</div>
                                </div>
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-outline-primary w-100 btn-dev-action" data-action="clear_views">
                                <i class="mdi mdi-file-code-outline fs-5"></i>
                                <div class="text-start">
                                    <div class="fw-bold">Clear Blade Views</div>
                                    <div class="small opacity-75" style="font-size: 0.72rem;">Recompile all Blade templates</div>
                                </div>
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-outline-info w-100 btn-dev-action" data-action="cache_routes">
                                <i class="mdi mdi-routes fs-5"></i>
                                <div class="text-start">
                                    <div class="fw-bold">Cache Application Routes</div>
                                    <div class="small opacity-75" style="font-size: 0.72rem;">Boost routing resolution speed</div>
                                </div>
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-outline-success w-100 btn-dev-action" data-action="ping_db">
                                <i class="mdi mdi-database-search-outline fs-5"></i>
                                <div class="text-start">
                                    <div class="fw-bold">Test MySQL DB Ping</div>
                                    <div class="small opacity-75" style="font-size: 0.72rem;">Check response time &amp; state</div>
                                </div>
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-outline-secondary w-100 btn-dev-action" data-action="clear_logs">
                                <i class="mdi mdi-file-document-remove-outline fs-5"></i>
                                <div class="text-start">
                                    <div class="fw-bold">Truncate Log File</div>
                                    <div class="small opacity-75" style="font-size: 0.72rem;">Empty laravel.log file</div>
                                </div>
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('developer.mailbox.index') }}" class="btn btn-outline-dark w-100 btn-dev-action">
                                <i class="mdi mdi-email-sync-outline fs-5 text-warning"></i>
                                <div class="text-start">
                                    <div class="fw-bold">Mailbox Management</div>
                                    <div class="small opacity-75" style="font-size: 0.72rem;">Central email &amp; SMTP settings</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 3: LIVE APPLICATION LOG STREAM & TODAY MUTATION TELEMETRY --}}
    <div class="row g-3 mb-4" id="section-log-stream">
        {{-- Log Stream Terminal --}}
        <div class="col-lg-8">
            <div class="card dev-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-terminal fs-4 text-danger"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">Live Application Error Logs</h5>
                            <small class="text-muted">Direct stream dari <code>storage/logs/laravel.log</code></small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" id="log-filter-input" class="form-control form-control-sm" placeholder="Filter log..." style="width: 160px;">
                        <button type="button" class="btn btn-sm btn-outline-danger" id="btn-quick-clear-logs" title="Truncate Log">
                            <i class="mdi mdi-trash-can-outline"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="dev-terminal p-2" id="dev-log-container">
                        @if (empty($logsData['entries']))
                            <div class="text-center py-5 text-muted">
                                <i class="mdi mdi-check-decagram fs-1 text-success mb-2 d-block"></i>
                                <h6 class="text-white">Tidak ada catatan error di laravel.log</h6>
                                <small>Sistem berjalan bersih tanpa exception baru.</small>
                            </div>
                        @else
                            @foreach ($logsData['entries'] as $log)
                                @php
                                    $badgeClass = match ($log['level']) {
                                        'EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR' => 'bg-danger text-white',
                                        'WARNING', 'NOTICE' => 'bg-warning text-dark',
                                        'INFO' => 'bg-info text-white',
                                        default => 'bg-secondary text-white',
                                    };
                                @endphp
                                <div class="dev-log-row d-flex align-items-start gap-2 log-item" data-id="{{ $log['id'] }}" data-message="{{ strtolower($log['message']) }}">
                                    <span class="badge {{ $badgeClass }} dev-badge-tech flex-shrink-0">{{ $log['level'] }}</span>
                                    <div class="flex-grow-1 text-truncate" style="min-width: 0;">
                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            <span class="text-white-50" style="font-size: 0.75rem;">{{ $log['timestamp'] }} ({{ $log['time_ago'] }})</span>
                                            @if ($log['has_trace'])
                                                <span class="badge bg-label-secondary" style="font-size: 0.65rem;">Click for Stack Trace</span>
                                            @endif
                                        </div>
                                        <div class="text-light text-truncate mt-1" style="font-size: 0.82rem;" title="{{ $log['message'] }}">
                                            {{ $log['message'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Today's Mutations & DB Activity --}}
        <div class="col-lg-4">
            <div class="card dev-card h-100">
                <div class="card-header py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-chart-timeline-variant fs-4 text-success"></i>
                        <h5 class="mb-0 fw-bold">Today's Data Mutations</h5>
                    </div>
                    <small class="text-muted">Aktivitas penambahan record hari ini</small>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-3 rounded-3 text-center" style="background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.15);">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">New Quotes</small>
                                <h3 class="mb-0 mt-1 fw-bold text-primary">{{ $todayActivity['quotes'] ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3 text-center" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.15);">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">PO Closing</small>
                                <h3 class="mb-0 mt-1 fw-bold text-success">{{ $todayActivity['po'] ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3 text-center" style="background: rgba(6, 182, 212, 0.08); border: 1px solid rgba(6, 182, 212, 0.15);">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">New Clients</small>
                                <h3 class="mb-0 mt-1 fw-bold text-info">{{ $todayActivity['clients'] ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3 text-center" style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.15);">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Activities</small>
                                <h3 class="mb-0 mt-1 fw-bold text-warning">{{ $todayActivity['activities'] ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <h6 class="fw-bold mb-2 small text-uppercase text-muted">User Distribution</h6>
                        @foreach ($roleDistribution as $rd)
                            <div class="d-flex align-items-center justify-content-between mb-1 small">
                                <span>{{ $rd['role'] }}</span>
                                <strong class="text-dark">{{ $rd['total'] }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 3.5: TEMUAN ERROR SYSTEM 500 — GRAFIK HARIAN --}}
    <div class="row g-3 mb-4" id="section-error-findings-chart">
        <div class="col-12">
            <div class="card dev-card">
                <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-alert-octagon-outline fs-4 text-danger"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">Temuan Error System 500 — Harian</h5>
                            <small class="text-muted">Jumlah error 500 baru per hari, 14 hari terakhir (tab Helpdesk &gt; Temuan Error System)</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-label-danger rounded-pill" id="err-chart-badge-open">{{ $errorFindingsDaily['open'] ?? 0 }} Open</span>
                        <span class="badge bg-label-secondary rounded-pill" id="err-chart-badge-total">{{ $errorFindingsDaily['total'] ?? 0 }} Total</span>
                        <span class="badge bg-label-primary rounded-pill" id="err-chart-badge-today">{{ $errorFindingsDaily['today'] ?? 0 }} Hari Ini</span>
                        <a href="{{ route('helpdesk.index') }}#navs-system-errors" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="mdi mdi-open-in-new me-1"></i> Buka Helpdesk
                        </a>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div id="err-findings-chart"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 4: TODAY'S SYSTEM & AUDIT ACTIVITY STREAM (PAGINATED) --}}
    <div class="card dev-card">
        <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-shield-account-outline fs-4 text-primary"></i>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold">Today's System &amp; Audit Activity Stream</h5>
                        <span class="badge bg-label-primary rounded-pill" id="dev-audit-badge-total">{{ $todayAuditLogs['total'] ?? 0 }} Event Hari Ini</span>
                    </div>
                    <small class="text-muted">Aktivitas sistem dan pengguna pada hari ini ({{ \Carbon\Carbon::today()->format('d M Y') }})</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                    <input type="text" id="dev-audit-search-input" class="form-control border-start-0" placeholder="Cari user / aksi...">
                </div>
                <a href="{{ route('activity-log.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Buka modul activity log lengkap">
                    <i class="mdi mdi-open-in-new me-1"></i> Buka Full Log
                </a>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 140px;">Waktu</th>
                        <th>User</th>
                        <th>Type / Action</th>
                        <th>Deskripsi</th>
                        <th style="width: 120px;">IP Address</th>
                    </tr>
                </thead>
                <tbody id="dev-audit-table-body">
                    @if (empty($todayAuditLogs['items']))
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="mdi mdi-information-outline fs-3 d-block mb-1 text-muted"></i>
                                Belum ada catatan aktivitas di tabel activity_logs untuk hari ini.
                            </td>
                        </tr>
                    @else
                        @foreach ($todayAuditLogs['items'] as $audit)
                            <tr>
                                <td class="small">
                                    <div class="fw-semibold text-dark">{{ $audit['time_ago'] }}</div>
                                    <small class="text-muted">{{ $audit['timestamp'] }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-xs bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-account fs-6"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">{{ $audit['user_name'] }}</div>
                                            <small class="text-muted">{{ $audit['user_role'] }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-info">{{ $audit['type'] }}</span>
                                    <span class="badge bg-label-secondary ms-1">{{ $audit['action'] }}</span>
                                </td>
                                <td class="text-wrap small text-muted" style="max-width: 450px;">
                                    {{ $audit['description'] }}
                                </td>
                                <td class="small text-muted font-monospace">
                                    {{ $audit['ip_address'] }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex align-items-center justify-content-between py-2 px-3 border-top flex-wrap gap-2">
            <small class="text-muted" id="dev-audit-info">
                Menampilkan {{ count($todayAuditLogs['items'] ?? []) }} dari {{ $todayAuditLogs['total'] ?? 0 }} aktivitas hari ini
            </small>
            <nav aria-label="Audit pagination">
                <ul class="pagination pagination-sm mb-0" id="dev-audit-pagination">
                    {{-- Generated via JS --}}
                </ul>
            </nav>
        </div>
    </div>
</div>

{{-- MODAL LOG STACK TRACE --}}
<div class="modal fade" id="modalDevLogDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content dev-card-dark" style="background: #0f172a; color: #f8fafc; border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-header border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
                <div class="d-flex align-items-center gap-2">
                    <span id="modal-log-badge" class="badge bg-danger dev-badge-tech">ERROR</span>
                    <h5 class="modal-title text-white mb-0" id="modal-log-title">Exception Stack Trace</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="mb-3">
                    <label class="small text-white-50 text-uppercase fw-semibold">Timestamp</label>
                    <div id="modal-log-time" class="text-light font-monospace small"></div>
                </div>
                <div class="mb-3">
                    <label class="small text-white-50 text-uppercase fw-semibold">Message</label>
                    <div id="modal-log-message" class="p-3 rounded-2 text-danger fw-semibold" style="background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); font-family: monospace;"></div>
                </div>
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label class="small text-white-50 text-uppercase fw-semibold">Stack Trace</label>
                        <button type="button" class="btn btn-xs btn-outline-light rounded-pill px-2 py-1" id="btn-copy-stacktrace" style="font-size: 0.72rem;">
                            <i class="mdi mdi-content-copy me-1"></i> Copy Trace
                        </button>
                    </div>
                    <pre id="modal-log-trace" class="p-3 rounded-2 text-white-50" style="background: #050811; max-height: 380px; overflow-y: auto; font-size: 0.75rem; white-space: pre-wrap; font-family: 'Fira Code', monospace;"></pre>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>
<script>
    (function() {
        let currentAuditPage = 1;
        let currentAuditSearch = '';

        function renderAuditTable(data) {
            const tbody = document.getElementById('dev-audit-table-body');
            const info = document.getElementById('dev-audit-info');
            const badge = document.getElementById('dev-audit-badge-total');
            const pagination = document.getElementById('dev-audit-pagination');

            if (!tbody) return;

            if (badge) badge.innerText = `${data.total || 0} Event Hari Ini`;
            if (info) info.innerText = `Menampilkan ${data.items ? data.items.length : 0} dari ${data.total || 0} aktivitas hari ini`;

            if (!data.items || data.items.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="mdi mdi-information-outline fs-3 d-block mb-1 text-muted"></i>
                            Tidak ada catatan aktivitas yang cocok untuk hari ini.
                        </td>
                    </tr>
                `;
                if (pagination) pagination.innerHTML = '';
                return;
            }

            let rowsHtml = '';
            data.items.forEach(audit => {
                rowsHtml += `
                    <tr>
                        <td class="small">
                            <div class="fw-semibold text-dark">${audit.time_ago}</div>
                            <small class="text-muted">${audit.timestamp}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-xs bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-account fs-6"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small">${audit.user_name}</div>
                                    <small class="text-muted">${audit.user_role}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-info">${audit.type}</span>
                            <span class="badge bg-label-secondary ms-1">${audit.action}</span>
                        </td>
                        <td class="text-wrap small text-muted" style="max-width: 450px;">
                            ${audit.description}
                        </td>
                        <td class="small text-muted font-monospace">
                            ${audit.ip_address}
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = rowsHtml;

            // Render Pagination Buttons
            if (pagination) {
                let pagHtml = '';
                const cur = data.current_page;
                const last = data.last_page;

                if (last > 1) {
                    pagHtml += `
                        <li class="page-item ${cur <= 1 ? 'disabled' : ''}">
                            <a class="page-link dev-audit-page-btn" href="javascript:void(0);" data-page="${cur - 1}">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                        </li>
                    `;

                    for (let i = 1; i <= last; i++) {
                        if (i === 1 || i === last || (i >= cur - 1 && i <= cur + 1)) {
                            pagHtml += `
                                <li class="page-item ${i === cur ? 'active' : ''}">
                                    <a class="page-link dev-audit-page-btn" href="javascript:void(0);" data-page="${i}">${i}</a>
                                </li>
                            `;
                        } else if (i === cur - 2 || i === cur + 2) {
                            pagHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                        }
                    }

                    pagHtml += `
                        <li class="page-item ${cur >= last ? 'disabled' : ''}">
                            <a class="page-link dev-audit-page-btn" href="javascript:void(0);" data-page="${cur + 1}">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </li>
                    `;
                }
                pagination.innerHTML = pagHtml;
            }
        }

        function fetchAuditLogs(page = 1, search = '') {
            currentAuditPage = page;
            currentAuditSearch = search;

            const url = `{{ route('developer.api.audit_logs') }}?page=${page}&search=${encodeURIComponent(search)}`;
            fetch(url)
                .then(res => res.json())
                .then(res => {
                    if (res.success && res.data) {
                        renderAuditTable(res.data);
                    }
                })
                .catch(err => {
                    console.error('Audit fetch error:', err);
                });
        }

        // Initialize pagination on load
        renderAuditTable({
            items: @json($todayAuditLogs['items'] ?? []),
            current_page: {{ (int) ($todayAuditLogs['current_page'] ?? 1) }},
            last_page: {{ (int) ($todayAuditLogs['last_page'] ?? 1) }},
            total: {{ (int) ($todayAuditLogs['total'] ?? 0) }}
        });

        // Delegated: Audit pagination click
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.dev-audit-page-btn');
            if (!btn) return;
            const p = parseInt(btn.dataset.page);
            if (p && p > 0) {
                fetchAuditLogs(p, currentAuditSearch);
            }
        });

        // Debounced search for audit logs
        let auditSearchTimer = null;
        document.addEventListener('input', function(e) {
            if (e.target && e.target.id === 'dev-audit-search-input') {
                clearTimeout(auditSearchTimer);
                const q = e.target.value.trim();
                auditSearchTimer = setTimeout(() => {
                    fetchAuditLogs(1, q);
                }, 350);
            }
        });

        // Delegated: Log row click -> Open detail modal
        document.addEventListener('click', function(e) {
            const row = e.target.closest('.dev-log-row');
            if (!row) return;

            const logId = row.dataset.id;
            if (!logId) return;

            fetch("{{ route('developer.api.log_detail') }}?id=" + encodeURIComponent(logId))
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.log) {
                        const l = data.log;
                        document.getElementById('modal-log-time').innerText = l.timestamp + ' (' + l.time_ago + ')';
                        document.getElementById('modal-log-message').innerText = l.message;
                        document.getElementById('modal-log-trace').innerText = l.stack_trace || 'No stack trace recorded.';
                        
                        const badge = document.getElementById('modal-log-badge');
                        badge.innerText = l.level;
                        badge.className = 'badge dev-badge-tech ' + (['EMERGENCY','CRITICAL','ERROR'].includes(l.level) ? 'bg-danger' : 'bg-warning text-dark');

                        const modalEl = document.getElementById('modalDevLogDetail');
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    }
                })
                .catch(err => {
                    console.error('Failed to fetch log details:', err);
                });
        });

        // Delegated: Copy stacktrace
        document.addEventListener('click', function(e) {
            const btnCopyTrace = e.target.closest('#btn-copy-stacktrace');
            if (!btnCopyTrace) return;

            const traceText = document.getElementById('modal-log-trace').innerText;
            navigator.clipboard.writeText(traceText).then(() => {
                const orig = btnCopyTrace.innerHTML;
                btnCopyTrace.innerHTML = '<i class="mdi mdi-check me-1"></i> Copied!';
                setTimeout(() => btnCopyTrace.innerHTML = orig, 2000);
            });
        });

        // Delegated: Filter log entries
        document.addEventListener('input', function(e) {
            if (e.target && e.target.id === 'log-filter-input') {
                const q = e.target.value.toLowerCase().trim();
                document.querySelectorAll('#dev-log-container .log-item').forEach(el => {
                    const msg = el.dataset.message || '';
                    el.style.display = (msg.includes(q) || q === '') ? 'flex' : 'none';
                });
            }
        });

        // Delegated: DevOps Action Handlers
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-dev-action');
            if (!btn) return;

            const action = btn.dataset.action;
            if (!action) return;

            const actionLabels = {
                'clear_cache': 'Optimize & Clear All Cache',
                'clear_views': 'Clear Compiled Blade Views',
                'cache_routes': 'Cache Application Routes',
                'ping_db': 'Test MySQL Connection Ping',
                'clear_logs': 'Truncate Application Log (laravel.log)'
            };

            const label = actionLabels[action] || action;

            Swal.fire({
                title: 'Eksekusi ' + label + '?',
                text: 'Aksi ini akan menjalankan perintah sistem terkait.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Jalankan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#6366f1',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch("{{ route('developer.actions.run') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ action: action })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Eksekusi gagal');
                        }
                        return data;
                    })
                    .catch(err => {
                        Swal.showValidationMessage(`Gagal: ${err.message}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: result.value.message,
                        icon: 'success',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        if (action === 'clear_logs') {
                            location.reload();
                        }
                    });
                }
            });
        });

        // Delegated: Quick clear logs button in terminal header
        document.addEventListener('click', function(e) {
            const btnQuickClearLogs = e.target.closest('#btn-quick-clear-logs');
            if (!btnQuickClearLogs) return;

            const btn = document.querySelector('[data-action="clear_logs"]');
            if (btn) btn.click();
        });

        // Delegated: Live Telemetry Refresh
        document.addEventListener('click', function(e) {
            const btnRefresh = e.target.closest('#btn-refresh-telemetry');
            if (!btnRefresh) return;

            const icon = btnRefresh.querySelector('i');
            if (icon) icon.classList.add('mdi-spin');

            fetch("{{ route('developer.api.telemetry') }}")
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.telemetry) {
                        const t = data.telemetry;
                        const timeEl = document.getElementById('dev-server-time');
                        const latencyEl = document.getElementById('dev-db-latency');
                        if (timeEl) timeEl.innerText = t.server_time || '-';
                        if (latencyEl) latencyEl.innerText = (t.db_latency_ms || 0) + ' ms';
                    }
                })
                .finally(() => {
                    setTimeout(() => {
                        if (icon) icon.classList.remove('mdi-spin');
                    }, 500);
                });
        });
    })();

    // Grafik harian "Temuan Error System 500" — auto-sinkron tiap kali ada error baru
    // (di-poll berkala, sama seperti pola notifikasi navbar lain di app ini).
    (function() {
        const chartEl = document.querySelector('#err-findings-chart');
        if (!chartEl || typeof ApexCharts === 'undefined') return;

        const initial = @json($errorFindingsDaily);
        let lastTotal = initial.total || 0;

        const chart = new ApexCharts(chartEl, {
            chart: {
                type: 'bar',
                height: 260,
                toolbar: { show: false },
                animations: { enabled: true, easing: 'easeinout', speed: 400 },
            },
            series: [{ name: 'Error 500', data: initial.series || [] }],
            colors: ['#dc3545'],
            plotOptions: {
                bar: { borderRadius: 4, columnWidth: '45%' },
            },
            dataLabels: { enabled: false },
            xaxis: {
                categories: initial.labels || [],
                labels: { style: { fontSize: '11px' } },
            },
            yaxis: {
                labels: { formatter: (v) => Math.round(v) },
            },
            grid: { borderColor: 'rgba(148, 163, 184, 0.15)' },
            tooltip: { y: { formatter: (v) => v + ' temuan' } },
        });
        chart.render();

        function updateBadges(data) {
            const badgeOpen = document.getElementById('err-chart-badge-open');
            const badgeTotal = document.getElementById('err-chart-badge-total');
            const badgeToday = document.getElementById('err-chart-badge-today');
            if (badgeOpen) badgeOpen.innerText = (data.open || 0) + ' Open';
            if (badgeTotal) badgeTotal.innerText = (data.total || 0) + ' Total';
            if (badgeToday) badgeToday.innerText = (data.today || 0) + ' Hari Ini';
        }

        function refreshErrorFindingsChart() {
            fetch("{{ route('developer.api.error_findings_daily') }}")
                .then((res) => res.json())
                .then((res) => {
                    if (!res.success || !res.data) return;
                    const data = res.data;

                    chart.updateOptions({ xaxis: { categories: data.labels || [] } });
                    chart.updateSeries([{ name: 'Error 500', data: data.series || [] }]);
                    updateBadges(data);

                    // Kalau ada temuan baru sejak poll terakhir, kasih notice singkat via badge pulse.
                    if ((data.total || 0) > lastTotal) {
                        const badgeOpen = document.getElementById('err-chart-badge-open');
                        if (badgeOpen) {
                            badgeOpen.classList.add('mdi-spin');
                            setTimeout(() => badgeOpen.classList.remove('mdi-spin'), 800);
                        }
                    }
                    lastTotal = data.total || 0;
                })
                .catch(() => {});
        }

        // Poll tiap 30 detik — cukup responsif tanpa membebani server (sesuai pola
        // polling notifikasi navbar lain di app ini, cuma dengan interval lebih longgar
        // karena grafik gak sekritikal badge notifikasi).
        setInterval(refreshErrorFindingsChart, 30000);
    })();
</script>


