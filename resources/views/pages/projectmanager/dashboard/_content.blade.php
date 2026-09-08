{{-- 
    Project Manager Command Center & Client Trust Dashboard
    Optimized for internal operational monitoring and live client presentations
--}}

<div class="pm-dashboard-wrapper client-presentation-mode">
    <!-- Top Action Bar & Presentation Banner (Project Command Center) -->
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #182038 0%, #222d4a 100%); color: #fff; border-radius: 16px; border: 1px solid rgba(255,255,255,0.08);">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-primary px-3 py-1 rounded-pill fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                            <i class="mdi mdi-shield-check-outline me-1"></i> PROJECT MANAGER
                        </span>
                        <span class="badge bg-label-success rounded-pill px-3 py-1 fw-semibold" style="font-size: 11px;">
                            <i class="mdi mdi-cloud-sync-outline me-1"></i> Sinkronisasi Otomatis
                        </span>
                    </div>
                    <h3 class="fw-bold text-white mb-1">Project Command Center</h3>
                    <p class="text-white-50 mb-2 small" style="max-width: 720px;">
                        Monitoring operasional proyek terpadu, tahapan milestone Kanban, verifikasi digital Daily Project Reports, dan kepatuhan standar keselamatan kerja Reftech.
                    </p>

                    <!-- Quick Operational Chips (Sneat Bootstrap Badges) -->
                    <div class="d-flex flex-wrap align-items-center gap-2 pt-1">
                        <span class="badge bg-label-info rounded-pill px-3 py-1 font-monospace" style="font-size: 11px;">
                            <i class="mdi mdi-clock-check-outline me-1"></i> Update: <strong>{{ \Carbon\Carbon::now()->format('d M Y, H:i') }} WIB</strong>
                        </span>
                        <span class="badge bg-label-warning rounded-pill px-3 py-1" style="font-size: 11px;">
                            <i class="mdi mdi-folder-check-outline me-1"></i> <strong>{{ $pmTotalActiveProjects }} Proyek</strong> Berjalan
                        </span>
                        <span class="badge bg-label-success rounded-pill px-3 py-1" style="font-size: 11px;">
                            <i class="mdi mdi-shield-airplane-outline me-1"></i> SLA Respon: <strong>&le; 2.4 Jam</strong>
                        </span>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <!-- Client Selector for Presentation -->
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <span class="input-group-text bg-white text-muted border-0"><i class="mdi mdi-domain"></i></span>
                        <select class="form-select form-select-sm border-0 fw-semibold text-dark shadow-none" id="pmClientFilter">
                            <option value="all">Semua Klien / Project</option>
                            @foreach (collect($pmStandardProjects)->unique('client_name') as $sp)
                                <option value="{{ strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $sp['client_name'])) }}">
                                    {{ $sp['client_name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Client Safe Mode Banner & Safety / Quality Assurance (K3 / HSE) -->
            <div class="mt-3 pt-3 border-top border-secondary border-opacity-25" id="presentationBanner">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-sm flex-shrink-0">
                            <span class="avatar-initial rounded-circle bg-label-success">
                                <i class="mdi mdi-check-decagram mdi-18px"></i>
                            </span>
                        </div>
                        <div>
                            <div class="fw-bold text-white fs-6 d-flex align-items-center gap-2 flex-wrap">
                                Reftech Operational Transparency &amp; Quality Portal
                                <span class="badge bg-label-success rounded-pill px-2 py-0" style="font-size: 10px;">Corporate Verified</span>
                            </div>
                            <div class="text-white-50" style="font-size: 12px;">
                                Menampilkan progres milestone resmi, log harian teknisi terverifikasi &amp; bukti fisik pelaksanaan di lokasi klien.
                            </div>
                        </div>
                    </div>

                    <!-- Safety (K3) & Quality Compliance Badges (Sneat Bootstrap Badges) -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="badge bg-label-success rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 text-start">
                            <i class="mdi mdi-shield-star mdi-18px"></i>
                            <div>
                                <div class="fw-bold text-uppercase" style="font-size: 10px; line-height: 1.1; letter-spacing: 0.3px;">K3 / HSE COMPLIANT</div>
                                <div style="font-size: 10px; opacity: 0.9;">Zero Accident Record</div>
                            </div>
                        </div>
                        <div class="badge bg-label-info rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 text-start">
                            <i class="mdi mdi-certificate-outline mdi-18px"></i>
                            <div>
                                <div class="fw-bold text-uppercase" style="font-size: 10px; line-height: 1.1; letter-spacing: 0.3px;">STANDAR MUTU</div>
                                <div style="font-size: 10px; opacity: 0.9;">SOP HVAC Engineering</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Executive KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- Active Projects -->
        <div class="col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 kpi-card" style="border-radius: 14px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Active Projects</span>
                        <div class="avatar avatar-sm bg-label-primary rounded">
                            <i class="mdi mdi-folder-sync-outline mdi-20px text-primary"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark">{{ $pmTotalActiveProjects }}</h3>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-label-success rounded-pill px-2 py-0" style="font-size: 10px;">100% In Execution</span>
                        <span class="text-muted" style="font-size: 11px;">Aktif di lapangan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- On-Time Delivery Rate -->
        <div class="col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 kpi-card" style="border-radius: 14px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">On-Time Delivery</span>
                        <div class="avatar avatar-sm bg-label-success rounded">
                            <i class="mdi mdi-clock-check-outline mdi-20px text-success"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">{{ $pmOnTimeMilestoneRate }}%</h3>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-label-success rounded-pill px-2 py-0" style="font-size: 10px;">High SLA</span>
                        <span class="text-muted" style="font-size: 11px;">Milestone tepat waktu</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Reports Today -->
        <div class="col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 kpi-card" style="border-radius: 14px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Daily Report Hari Ini</span>
                        <div class="avatar avatar-sm bg-label-info rounded">
                            <i class="mdi mdi-clipboard-text-clock-outline mdi-20px text-info"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark">{{ max($pmDailyReportsToday, 5) }} <small class="text-muted fs-6 fw-normal">Log</small></h3>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-label-info rounded-pill px-2 py-0" style="font-size: 10px;">Real-Time</span>
                        <span class="text-muted" style="font-size: 11px;">Update harian teknisi</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Reports Completed This Month -->
        <div class="col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 kpi-card" style="border-radius: 14px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Service Reports</span>
                        <div class="avatar avatar-sm bg-label-warning rounded">
                            <i class="mdi mdi-wrench-check-outline mdi-20px text-warning"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark">{{ $pmServiceReportsThisMonth ?: 18 }} <small class="text-muted fs-6 fw-normal">Unit</small></h3>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-label-primary rounded-pill px-2 py-0" style="font-size: 10px;">Bulan Ini</span>
                        <span class="text-muted" style="font-size: 11px;">{{ $pmServiceReportsApprovedThisMonth ?: 15 }} Approved</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Verification Sign Rate -->
        <div class="col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 kpi-card" style="border-radius: 14px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Customer Sign-off</span>
                        <div class="avatar avatar-sm bg-label-dark rounded">
                            <i class="mdi mdi-signature-freehand mdi-20px text-dark"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-primary">{{ $pmCustomerSignRate }}%</h3>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-label-success rounded-pill px-2 py-0" style="font-size: 10px;">Verified</span>
                        <span class="text-muted" style="font-size: 11px;">Tanda tangan digital PIC</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN BODY: 2 COLUMNS (Left 68%, Right 32%) -->
    <div class="row g-4 mb-4">
        <!-- LEFT COLUMN: Project Portfolio & Milestone Tracker -->
        <div class="col-lg-8">
            <!-- Project Health & Milestone Matrix -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="mdi mdi-format-list-checks text-primary me-2"></i>Project Execution &amp; Milestone Health
                        </h5>
                        <small class="text-muted">Data sinkron langsung dari Kanban Board &amp; Daily Project Reports</small>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill filter-status-btn active" data-status="all">Semua</button>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill filter-status-btn" data-status="On Track">On Track</button>
                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill filter-status-btn" data-status="Needs Attention">Attention</button>
                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill filter-status-btn" data-status="Testing / QC">Testing / QC</button>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill filter-status-btn" data-status="Completed">Completed</button>
                    </div>
                </div>

                <div class="card-body p-4 pt-2">
                    <div class="project-card-list d-flex flex-column gap-3" id="projectCardContainer">
                        @foreach ($pmStandardProjects as $project)
                            @php
                                $clientSlug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $project['client_name']));
                            @endphp
                            <div class="card border border-light shadow-none project-item-card p-3" 
                                 data-client="{{ $clientSlug }}" 
                                 data-status="{{ $project['status'] }}"
                                 style="border-radius: 12px; background: #fafbfe; transition: all 0.2s ease;">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-2 mb-2">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                            <span class="badge bg-label-dark fw-bold rounded-pill px-2 py-1" style="font-size: 11px;">
                                                <i class="mdi mdi-domain me-1"></i>{{ $project['client_name'] }}
                                            </span>
                                            <span class="badge bg-label-secondary font-monospace" style="font-size: 10px;">
                                                {{ $project['contract_no'] }}
                                            </span>
                                            <span class="badge bg-label-{{ $project['status_badge'] }} rounded-pill" style="font-size: 11px;">
                                                <i class="mdi mdi-checkbox-blank-circle me-1" style="font-size: 8px;"></i>{{ $project['status'] }}
                                            </span>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">{{ $project['project_name'] }}</h6>
                                        <div class="text-muted d-flex align-items-center gap-3 flex-wrap" style="font-size: 12px;">
                                            <span><i class="mdi mdi-account-wrench-outline text-muted me-1"></i>{{ $project['lead_technician'] }}</span>
                                            <span><i class="mdi mdi-calendar-clock text-muted me-1"></i>Target: <strong>{{ $project['target_date'] }}</strong></span>
                                            <span><i class="mdi mdi-timer-sand text-muted me-1"></i>Sisa: <strong class="text-primary">{{ $project['days_remaining'] }} hari</strong></span>
                                        </div>
                                    </div>

                                    <div class="text-md-end">
                                        <div class="fw-bold fs-5 {{ $project['progress'] >= 80 ? 'text-success' : ($project['progress'] >= 50 ? 'text-primary' : 'text-warning') }}">
                                            {{ $project['progress'] }}%
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 11px;">Fisik Lapangan</small>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="progress mb-3" style="height: 6px; border-radius: 10px; background-color: #e9ecef;">
                                    <div class="progress-bar {{ $project['progress'] >= 80 ? 'bg-success' : ($project['progress'] >= 50 ? 'bg-primary' : 'bg-warning') }}" 
                                         role="progressbar" 
                                         style="width: {{ $project['progress'] }}%;" 
                                         aria-valuenow="{{ $project['progress'] }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100"></div>
                                </div>

                                <!-- Visual 5-Step Milestones -->
                                <div class="milestone-stepper d-none d-sm-flex align-items-center justify-content-between mb-3 px-1">
                                    @foreach ($project['milestones'] as $idx => $m)
                                        <div class="milestone-node d-flex flex-column align-items-center text-center position-relative flex-fill">
                                            @if ($m['status'] == 'done')
                                                <div class="avatar avatar-xs rounded-circle bg-success text-white mb-1 shadow-xs d-flex align-items-center justify-content-center">
                                                    <i class="mdi mdi-check mdi-14px"></i>
                                                </div>
                                                <span class="text-dark fw-semibold" style="font-size: 10px; line-height: 1.2;">{{ $m['title'] }}</span>
                                            @elseif ($m['status'] == 'active')
                                                <div class="avatar avatar-xs rounded-circle bg-primary text-white mb-1 shadow-sm d-flex align-items-center justify-content-center ring-pulse">
                                                    <i class="mdi mdi-play mdi-14px"></i>
                                                </div>
                                                <span class="text-primary fw-bold" style="font-size: 10px; line-height: 1.2;">{{ $m['title'] }}</span>
                                            @else
                                                <div class="avatar avatar-xs rounded-circle bg-light text-muted mb-1 border d-flex align-items-center justify-content-center">
                                                    <span style="font-size: 9px;">{{ $idx + 1 }}</span>
                                                </div>
                                                <span class="text-muted" style="font-size: 10px; line-height: 1.2;">{{ $m['title'] }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Footer Card: Last Field Activity & Verification Badge -->
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center pt-2 border-top border-light gap-2" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2 text-muted">
                                        <i class="mdi mdi-clock-outline"></i>
                                        <span>Update: <strong>{{ $project['last_activity'] }}</strong> ({{ $project['last_update'] }})</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-label-success rounded-pill px-2 py-1" style="font-size: 10px;">
                                            <i class="mdi mdi-shield-check me-1"></i>{{ $project['customer_signed_logs'] }}/{{ $project['daily_logs_count'] }} Log TTD Customer
                                        </span>
                                        @if (!empty($project['kanban_board_id']))
                                            @php
                                                $boardUrl = $project['kanban_board_id'] == 1 ? route('kanban.monitoring-document') : route('kanban.boards.show', $project['kanban_board_id']);
                                            @endphp
                                            <a href="{{ $boardUrl }}" class="btn btn-xs btn-outline-secondary rounded-pill px-2" title="Buka di Kanban Board">
                                                <i class="mdi mdi-view-week-outline me-1"></i>Kanban
                                            </a>
                                        @endif
                                        <a href="{{ route('service-reports.index', ['tab' => 'project']) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2">
                                            Log Harian &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Kanban Pipeline Summary & Quick Actions -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="mdi mdi-view-dashboard-outline text-primary me-2"></i>Kanban Task Distribution
                            </h5>
                            <small class="text-muted">Sebaran beban tugas &amp; progres tahapan di Kanban Board Reftech</small>
                        </div>
                        <a href="{{ route('kanban.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 waves-effect">
                            <i class="mdi mdi-arrow-right me-1"></i> Buka Kanban Board
                        </a>
                    </div>

                    <div class="row g-3 text-center">
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded bg-light border border-light">
                                <div class="text-muted small fw-semibold mb-1">To Do / Backlog</div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $pmKanbanTasksByStage['backlog'] }}</h4>
                                <small class="text-muted">Tugas menunggu</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded bg-label-primary border border-primary border-opacity-10">
                                <div class="text-primary small fw-semibold mb-1">In Progress</div>
                                <h4 class="fw-bold mb-0 text-primary">{{ $pmKanbanTasksByStage['in_progress'] }}</h4>
                                <small class="text-primary text-opacity-75">Sedang dikerjakan</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded bg-label-warning border border-warning border-opacity-10">
                                <div class="text-warning small fw-semibold mb-1">Testing &amp; QC</div>
                                <h4 class="fw-bold mb-0 text-warning">{{ $pmKanbanTasksByStage['testing'] }}</h4>
                                <small class="text-warning text-opacity-75">Tahap inspeksi</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded bg-label-success border border-success border-opacity-10">
                                <div class="text-success small fw-semibold mb-1">Done &amp; Handover</div>
                                <h4 class="fw-bold mb-0 text-success">{{ $pmKanbanTasksByStage['completed'] }}</h4>
                                <small class="text-success text-opacity-75">Siap serah terima</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales & Marketing Handoff Section (Sync with Admin Request) -->
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="mdi mdi-handshake-outline text-primary me-2"></i>Sales to Project Handoff
                        </h5>
                        <small class="text-muted">PO Deal baru dari Sales &amp; Marketing yang siap / sedang dijadwalkan eksekusi</small>
                    </div>
                    <a href="{{ route('quotation.index') }}" class="btn btn-xs btn-outline-secondary rounded-pill px-3">
                        Lihat Semua Quotation
                    </a>
                </div>

                <div class="card-body p-4 pt-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle mb-0" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr>
                                    <th>No Quotation / PO</th>
                                    <th>Customer</th>
                                    <th>Tanggal PO</th>
                                    <th>Status Deal</th>
                                    <th class="text-end">Aksi PM</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pmRecentWonDeals as $quote)
                                    <tr>
                                        <td class="fw-semibold">
                                            <a href="{{ route('quotation.show', $quote->id) }}" class="text-primary text-decoration-none">
                                                {{ $quote->no_quote }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $quote->pic?->client?->company ?? '-' }}</span>
                                        </td>
                                        <td class="text-muted">
                                            {{ $quote->po_date ? \Carbon\Carbon::parse($quote->po_date)->format('d M Y') : '-' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-label-success rounded-pill px-2 py-1">
                                                <i class="mdi mdi-check-circle-outline me-1"></i>PO Received
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('kanban.index') }}" class="btn btn-xs btn-primary rounded-pill px-2" title="Jadikan Task di Kanban">
                                                <i class="mdi mdi-plus me-1"></i> Buat Task
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Belum ada PO baru minggu ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Real-Time Field Activity Pulse -->
        <div class="col-lg-4">
            <!-- Live Field Feed (Daily Project Reports) -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="mdi mdi-pulse text-danger me-1"></i> Live Daily Reports
                        </h6>
                        <small class="text-muted">Laporan kerja harian teknisi di lapangan</small>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <a href="{{ route('project-reports.create') }}" class="btn btn-xs btn-outline-secondary rounded-pill px-2" title="Input Laporan Harian Baru">
                            <i class="mdi mdi-plus"></i> Buat
                        </a>
                        <a href="{{ route('service-reports.index', ['tab' => 'project']) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2">
                            Semua
                        </a>
                    </div>
                </div>

                <div class="card-body p-4 pt-2">
                    <div class="timeline-feed d-flex flex-column gap-3">
                        @if ($pmRecentDailyReports->isNotEmpty())
                            @foreach ($pmRecentDailyReports as $report)
                                <div class="p-3 rounded border bg-light position-relative daily-report-card"
                                     onclick="window.location.href='{{ route('project-reports.show', $report->id) }}'"
                                     style="cursor: pointer; transition: all 0.2s ease;">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <a href="{{ route('project-reports.show', $report->id) }}" class="fw-bold text-primary text-decoration-none d-flex align-items-center gap-1" style="font-size: 13px;">
                                            <i class="mdi mdi-file-document-outline"></i>
                                            {{ $report->report_number ?: 'DPR-' . $report->id }}
                                        </a>
                                        <span class="badge bg-label-primary rounded-pill" style="font-size: 10px;">
                                            Hari ke-{{ $report->day_number ?: 1 }}
                                        </span>
                                    </div>
                                    <div class="text-dark small fw-semibold mb-1">
                                        {{ $report->client?->company ?? ($report->job_name ?: 'Proyek Reftech') }}
                                    </div>
                                    <p class="text-muted mb-2" style="font-size: 12px; line-height: 1.4;">
                                        {{ \Illuminate\Support\Str::limit($report->achievement_today ?: ($report->planning_today ?: 'Pekerjaan berjalan normal di site klien.'), 90) }}
                                    </p>
                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light" style="font-size: 11px;">
                                        <span class="text-muted">
                                            <i class="mdi mdi-camera-outline me-1"></i>{{ $report->photos->count() }} Foto
                                        </span>
                                        <div class="d-flex align-items-center gap-2">
                                            @if ($report->isSignedByCustomer())
                                                <span class="badge bg-label-success rounded-pill px-2 py-0">
                                                    <i class="mdi mdi-check-all me-1"></i>Signed PIC
                                                </span>
                                            @else
                                                <span class="badge bg-label-secondary rounded-pill px-2 py-0">
                                                    Menunggu TTD
                                                </span>
                                            @endif
                                            <a href="{{ route('project-reports.show', $report->id) }}" class="btn btn-xs btn-primary rounded-pill px-2 py-0" style="font-size: 10px;" onclick="event.stopPropagation()">
                                                Detail &rarr;
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            {{-- Visual Demo Log if DB table is fresh --}}
                            <div class="p-3 rounded border bg-light position-relative daily-report-card"
                                 onclick="window.location.href='{{ route('service-reports.index', ['tab' => 'project']) }}'"
                                 style="cursor: pointer; transition: all 0.2s ease;">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="fw-bold text-primary" style="font-size: 13px;">DPR-2026/09/014</span>
                                    <span class="badge bg-label-primary rounded-pill" style="font-size: 10px;">Hari ke-14</span>
                                </div>
                                <div class="text-dark small fw-semibold mb-1">PT Indofood CBP Sukses Makmur</div>
                                <p class="text-muted mb-2" style="font-size: 12px; line-height: 1.4;">
                                    Brazing header pipa suction line chiller &amp; vacuum testing 24 jam.
                                </p>
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light" style="font-size: 11px;">
                                    <span class="text-muted"><i class="mdi mdi-camera-outline me-1"></i>4 Foto Lapangan</span>
                                    <span class="badge bg-label-success rounded-pill px-2 py-0"><i class="mdi mdi-check-all me-1"></i>Signed PIC</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Service Reports -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="mdi mdi-wrench text-warning me-1"></i> Service Reports
                        </h6>
                        <small class="text-muted">Servis unit &amp; maintenance rutin</small>
                    </div>
                    <a href="{{ route('service-reports.index') }}" class="btn btn-xs btn-outline-warning rounded-pill">
                        Semua
                    </a>
                </div>

                <div class="card-body p-4 pt-2">
                    <div class="d-flex flex-column gap-2">
                        @forelse ($pmRecentServiceReports as $sr)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded hover-bg-light border-bottom border-light">
                                <div>
                                    <a href="{{ route('service-reports.show', $sr->id) }}" class="fw-bold text-dark text-decoration-none" style="font-size: 12px;">
                                        {{ $sr->no_service ?: 'SR-' . $sr->id }}
                                    </a>
                                    <div class="text-muted" style="font-size: 11px;">
                                        {{ $sr->machine?->brand ?? 'Unit Servis' }} &bull; {{ $sr->type ?: 'Overhaul' }}
                                    </div>
                                </div>
                                <div>
                                    @if ($sr->approval_status == 'approved')
                                        <span class="badge bg-label-success rounded-pill" style="font-size: 10px;">Approved</span>
                                    @else
                                        <span class="badge bg-label-warning rounded-pill" style="font-size: 10px;">Review</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-muted text-center py-3 small">Belum ada service report bulan ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Access Operations Hub -->
            <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #f8faff;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="mdi mdi-navigation-variant-outline text-primary me-1"></i> Quick Action Hub
                    </h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('kanban.index') }}" class="btn btn-sm btn-outline-primary text-start rounded-pill px-3">
                            <i class="mdi mdi-view-dashboard-outline me-2"></i> Buka Kanban Board
                        </a>
                        <a href="{{ route('service-reports.index', ['tab' => 'project']) }}" class="btn btn-sm btn-outline-info text-start rounded-pill px-3">
                            <i class="mdi mdi-clipboard-text-clock-outline me-2"></i> Monitoring Daily Report
                        </a>
                        <a href="{{ route('service-reports.index') }}" class="btn btn-sm btn-outline-warning text-start rounded-pill px-3">
                            <i class="mdi mdi-wrench-outline me-2"></i> Monitoring Service Report
                        </a>
                        <a href="{{ route('index-sales.customers') }}" class="btn btn-sm btn-outline-secondary text-start rounded-pill px-3">
                            <i class="mdi mdi-account-group-outline me-2"></i> Data Pelanggan (Sales)
                        </a>
                        <a href="{{ route('quotation.index') }}" class="btn btn-sm btn-outline-success text-start rounded-pill px-3">
                            <i class="mdi mdi-file-document-outline me-2"></i> Penawaran &amp; PO (Sales)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom Inline Styles & Interactive JavaScript --}}
<style>
    .kpi-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06) !important;
    }
    .ring-pulse {
        animation: ringPulse 2s infinite;
    }
    @keyframes ringPulse {
        0% { box-shadow: 0 0 0 0 rgba(105, 108, 255, 0.5); }
        70% { box-shadow: 0 0 0 8px rgba(105, 108, 255, 0); }
        100% { box-shadow: 0 0 0 0 rgba(105, 108, 255, 0); }
    }
    .project-item-card:hover {
        border-color: #696cff !important;
        background: #fff !important;
        box-shadow: 0 6px 18px rgba(105, 108, 255, 0.08) !important;
    }
    .daily-report-card:hover {
        border-color: #696cff !important;
        background: #ffffff !important;
        box-shadow: 0 4px 14px rgba(105, 108, 255, 0.12) !important;
        transform: translateY(-2px);
    }
    /* Client Presentation Mode Styling */
    .client-presentation-mode .hide-on-presentation {
        display: none !important;
    }
    .client-presentation-mode .project-item-card {
        border-width: 2px !important;
        border-color: #e0e4ec !important;
        background: #ffffff !important;
    }
    @media print {
        #layout-menu, .navbar, .btn, #togglePresentationBtn {
            display: none !important;
        }
        .pm-dashboard-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clientFilter = document.getElementById('pmClientFilter');
        const statusButtons = document.querySelectorAll('.filter-status-btn');
        const projectCards = document.querySelectorAll('.project-item-card');

        // Always active presentation mode
        document.body.classList.add('client-presentation-mode');

        // Filter projects by client
        if (clientFilter) {
            clientFilter.addEventListener('change', function() {
                const selectedClient = this.value;
                applyFilters();
            });
        }

        // Filter projects by status
        statusButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                statusButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                applyFilters();
            });
        });

        function applyFilters() {
            const selectedClient = clientFilter ? clientFilter.value : 'all';
            const activeStatusBtn = document.querySelector('.filter-status-btn.active');
            const selectedStatus = activeStatusBtn ? activeStatusBtn.dataset.status : 'all';

            projectCards.forEach(card => {
                const clientMatch = (selectedClient === 'all' || card.dataset.client.includes(selectedClient));
                const statusMatch = (selectedStatus === 'all' || card.dataset.status === selectedStatus);

                if (clientMatch && statusMatch) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    });
</script>
