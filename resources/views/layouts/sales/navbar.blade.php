<nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="mdi mdi-menu mdi-24px"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper mb-0">
                <a class="nav-item nav-link search-toggler fw-normal px-0" href="javascript:void(0);">
                    <i class="mdi mdi-magnify mdi-24px scaleX-n1-rtl"></i>
                    <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
                </a>
            </div>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">

            <!-- Developer Maintenance Badge -->
            @if (Auth::user()?->isDeveloper())
                @php
                    $maintDet = \App\Services\MaintenanceService::getDetails();
                    $isMaintActive = !empty($maintDet['is_active']);
                    $isMaintPlanned = !empty($maintDet['is_planned']);
                @endphp
                <li class="nav-item me-2">
                    <a href="{{ route('developer.maintenance.index') }}"
                        class="btn btn-sm {{ $isMaintActive ? 'btn-danger animate__animated animate__pulse animate__infinite' : ($isMaintPlanned ? 'btn-label-warning' : 'btn-label-success') }} rounded-pill px-3 py-1 d-flex align-items-center"
                        title="Klik untuk kelola Maintenance Mode">
                        <i class="mdi {{ $isMaintActive ? 'mdi-alert-octagon' : ($isMaintPlanned ? 'mdi-clock-alert-outline' : 'mdi-check-circle-outline') }} me-1"></i>
                        <span class="fw-bold">{{ $isMaintActive ? 'Maintenance ON' : ($isMaintPlanned ? 'Plan: ' . $maintDet['plan_start_time'] : 'System LIVE') }}</span>
                    </a>
                </li>
            @endif

            <!-- Presensi Harian Quick Widget & Alert Upload Surat Dokter Susulan & Alert Rekap Denda Payroll -->
            @if (Auth::user() && Auth::user()->role !== 'Client')
                @php
                    $navEmp = Auth::user()->employee;
                    $navTodayAtt = null;
                    $navPendingSickLeave = null;
                    $isHrCutoffRole = in_array(Auth::user()->role, ['Finance', 'Finance Manager', 'developer', 'Developer']) 
                        || (method_exists(Auth::user(), 'isDeveloper') && Auth::user()->isDeveloper());
                    $cutoffAlertData = null;

                    if ($isHrCutoffRole) {
                        $cutoffAlertData = \App\Services\Hr\PayrollCutoffService::getCutoffRecapSummary();
                    }

                    // Alert & Approval Klaim Reimbursement (Finance & HR Management)
                    $userRole = Auth::user()?->role;
                    $isFinanceOrHrRole = in_array($userRole, ['Admin', 'Developer', 'Finance', 'Finance Manager', 'Accounting', 'Super Admin'])
                        || (method_exists(Auth::user(), 'isDeveloper') && Auth::user()->isDeveloper());
                    $unreadReimbursementCount = 0;
                    $pendingReimbursementsNotif = collect();
                    if (Auth::check() && $isFinanceOrHrRole) {
                        try {
                            $unreadReimbursementCount = \App\Models\HrReimbursement::where('status', 'Pending')->count();
                            $pendingReimbursementsNotif = \App\Models\HrReimbursement::where('status', 'Pending')
                                ->with(['employee.user', 'employee.department'])
                                ->latest()
                                ->take(10)
                                ->get();
                        } catch (\Throwable $e) {
                            $unreadReimbursementCount = 0;
                            $pendingReimbursementsNotif = collect();
                        }
                    }

                    // Alert & Approval Cuti / Izin Karyawan (Role-based / Account Configurable)
                    $isLeaveAlertEligible = \App\Services\Hr\LeaveAlertService::isUserEligible(Auth::user());
                    $leaveAlertPendingCount = 0;
                    $leaveAlertPendingList = collect();
                    if ($isLeaveAlertEligible) {
                        $leaveAlertPendingCount = \App\Services\Hr\LeaveAlertService::getPendingCount();
                        if ($leaveAlertPendingCount > 0) {
                            $leaveAlertPendingList = \App\Services\Hr\LeaveAlertService::getPendingRequests(15);
                        }
                    }

                    if ($navEmp) {
                        $navPendingSickLeave = \App\Models\HrLeaveRequest::where('employee_id', $navEmp->id)
                            ->whereNull('attachment')
                            ->whereHas('leaveType', function($q) {
                                $q->where('code', 'SK')->orWhere('name', 'LIKE', '%Sakit%');
                            })
                            ->where('status', '!=', 'Rejected')
                            ->latest()
                            ->first();

                        if ($navEmp->can_online_attendance) {
                            \App\Models\HrAttendance::processAutoClockOutIfDue();
                            $navTodayAtt = \App\Models\Hr\HrAttendance::where('employee_id', $navEmp->id)
                                ->whereDate('date', \Carbon\Carbon::today('Asia/Jakarta'))
                                ->first();
                        }
                    }
                @endphp

                {{-- Alert Khusus HR/Finance: Rekap Denda Presensi Cutoff & Reminder Payment Payroll/Expense --}}
                @if ($cutoffAlertData && !empty($cutoffAlertData['period']['is_recap_ready']) && empty($cutoffAlertData['is_fully_completed']))
                <li class="nav-item me-2">
                    @if (empty($cutoffAlertData['has_payroll_generated']))
                    <button type="button" class="btn btn-sm btn-label-danger rounded-pill px-3 py-1 d-flex align-items-center shadow-xs border border-danger text-danger fw-bold animate__animated animate__pulse animate__infinite" data-bs-toggle="modal" data-bs-target="#modalAttendanceCutoffRecapAlert" title="Rekap denda presensi cutoff telah siap per jam 09:00. Klik untuk melihat detail & proses ke Payroll.">
                        <i class="mdi mdi-cash-minus me-1 text-danger"></i>
                        <span class="d-none d-sm-inline">Rekap Denda Payroll</span>
                        <span class="d-inline d-sm-none">Denda Cutoff</span>
                        @if ($cutoffAlertData['late_employees_count'] > 0)
                            <span class="badge bg-danger rounded-pill ms-1 font-10">{{ $cutoffAlertData['late_employees_count'] }} Org</span>
                        @endif
                    </button>
                    @else
                    <button type="button" class="btn btn-sm btn-label-warning rounded-pill px-3 py-1 d-flex align-items-center shadow-xs border border-warning text-warning fw-bold animate__animated animate__pulse animate__infinite" data-bs-toggle="modal" data-bs-target="#modalAttendanceCutoffRecapAlert" title="Payroll periode ini belum diproses Payment / belum diposting ke Finance Expense. Klik untuk selesaikan.">
                        <i class="mdi mdi-clock-alert-outline me-1 text-warning"></i>
                        <span class="d-none d-sm-inline">Payroll: Belum Payment/Expense</span>
                        <span class="d-inline d-sm-none">Payroll Pending</span>
                        <span class="badge bg-warning text-dark rounded-pill ms-1 font-10">{{ $cutoffAlertData['existing_payroll']->code }}</span>
                    </button>
                    @endif
                </li>
                @endif

                {{-- Alert Khusus: Pengajuan Klaim Reimbursement Pending (Finance & HR Management) --}}
                @if ($isFinanceOrHrRole && $unreadReimbursementCount > 0)
                <li class="nav-item me-2" id="navReimbursementAlertItem">
                    <button type="button" class="btn btn-sm btn-label-warning rounded-pill px-3 py-1 d-flex align-items-center shadow-xs border border-warning text-warning fw-bold animate__animated animate__pulse animate__infinite" data-bs-toggle="modal" data-bs-target="#modalReimbursementApprovalAlert" title="Ada {{ $unreadReimbursementCount }} klaim reimbursement menunggu persetujuan Finance. Klik untuk quick review & approval.">
                        <i class="mdi mdi-receipt-text-clock-outline me-1 text-warning"></i>
                        <span class="d-none d-sm-inline">Klaim Reimbursement</span>
                        <span class="d-inline d-sm-none">Klaim</span>
                        <span class="badge bg-warning text-dark rounded-pill ms-1 font-10" id="navReimbursementAlertBadge">{{ $unreadReimbursementCount }}</span>
                    </button>
                </li>
                @endif

                {{-- Alert Khusus: Pengajuan Cuti / Izin Pending Approval (Finance & Akun Terdaftar) --}}
                @if ($isLeaveAlertEligible && $leaveAlertPendingCount > 0)
                <li class="nav-item me-2" id="navLeaveAlertItem">
                    <button type="button" class="btn btn-sm btn-label-info rounded-pill px-3 py-1 d-flex align-items-center shadow-xs border border-info text-info fw-bold animate__animated animate__pulse animate__infinite" data-bs-toggle="modal" data-bs-target="#modalLeaveApprovalAlert" title="Ada {{ $leaveAlertPendingCount }} pengajuan cuti/izin menunggu persetujuan. Klik untuk quick review & approval.">
                        <i class="mdi mdi-calendar-alert me-1 text-info"></i>
                        <span class="d-none d-sm-inline">Approval Cuti/Izin</span>
                        <span class="d-inline d-sm-none">Cuti/Izin</span>
                        <span class="badge bg-info text-white rounded-pill ms-1 font-10" id="navLeaveAlertBadge">{{ $leaveAlertPendingCount }}</span>
                    </button>
                </li>
                @endif

                {{-- Alert Khusus: Surat Dokter Menyusul Belum Diunggah --}}
                @if ($navPendingSickLeave)
                <li class="nav-item me-2">
                    <button type="button" class="btn btn-sm btn-label-warning rounded-pill px-3 py-1 d-flex align-items-center shadow-xs border border-warning text-warning fw-bold animate__animated animate__pulse animate__infinite" data-bs-toggle="modal" data-bs-target="#navModalUploadLateSick-{{ $navPendingSickLeave->id }}" title="Pengajuan Sakit belum ada surat dokter. Klik untuk upload sekarang.">
                        <i class="mdi mdi-hospital-box-outline me-1 text-danger"></i>
                        <span class="d-none d-sm-inline">Upload Surat Dokter</span>
                        <span class="d-inline d-sm-none">Surat Dokter</span>
                    </button>
                </li>
                @endif
                @if ($navEmp && $navEmp->can_online_attendance)
                <li class="nav-item me-2">
                    @if (!$navTodayAtt || !$navTodayAtt->clock_in)
                        @if (\Carbon\Carbon::today('Asia/Jakarta')->isWeekend())
                            <button type="button" class="btn btn-sm btn-label-secondary rounded-pill px-3 py-1 d-flex align-items-center shadow-xs" data-bs-toggle="modal" data-bs-target="#navClockInModal" title="Hari Libur Akhir Pekan (Sabtu/Minggu). Presensi opsional.">
                                <i class="mdi mdi-calendar-weekend me-1 text-primary"></i>
                                <span class="fw-semibold d-none d-sm-inline">Libur Weekend</span>
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1 d-flex align-items-center shadow-xs" data-bs-toggle="modal" data-bs-target="#navClockInModal" title="Klik untuk Presensi Masuk (Clock In)">
                                <i class="mdi mdi-clock-in me-1"></i>
                                <span class="fw-bold d-none d-sm-inline">Clock In</span>
                            </button>
                        @endif
                    @elseif ($navTodayAtt && !$navTodayAtt->clock_out)
                        <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 py-1 d-flex align-items-center shadow-xs" data-bs-toggle="modal" data-bs-target="#navClockOutModal" title="Presensi Masuk: {{ substr($navTodayAtt->clock_in, 0, 5) }}. Klik untuk Presensi Pulang (Clock Out)">
                            <i class="mdi mdi-clock-check me-1"></i>
                            <span class="fw-bold d-none d-sm-inline">Masuk {{ substr($navTodayAtt->clock_in, 0, 5) }}</span>
                        </button>
                    @else
                        <a href="{{ route('hr.portal.index') }}" class="btn btn-sm btn-label-success rounded-pill px-3 py-1 d-flex align-items-center" title="Presensi Hari Ini Selesai: {{ substr($navTodayAtt->clock_in, 0, 5) }} - {{ substr($navTodayAtt->clock_out, 0, 5) }}">
                            <i class="mdi mdi-check-all me-1 text-success"></i>
                            <span class="fw-bold d-none d-sm-inline text-success">Hadir</span>
                        </a>
                    @endif
                </li>
                @endif
            @endif

            <!-- Style Switcher -->
            <li class="nav-item me-1 me-xl-0">
                <a class="nav-link btn btn-text-secondary rounded-pill btn-icon style-switcher-toggle hide-arrow"
                    href="javascript:void(0);">
                    <i class="mdi mdi-24px"></i>
                </a>
            </li>
            <!--/ Style Switcher -->
            <!-- Dynamic Customizable Quick Action (All Logged-in Roles, Max 4 items) -->
            @if (Auth::check())
                @php
                    $userQuickActions = \App\Services\QuickActionService::getUserQuickActions(Auth::user());
                    $activeActionIds  = collect($userQuickActions)->pluck('id')->all();
                @endphp
                <li class="nav-item dropdown me-2 me-xl-1" id="navbarQuickActionDropdownItem">
                    <a class="nav-link btn btn-text-primary rounded-pill btn-icon dropdown-toggle hide-arrow"
                        href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" title="Quick Action (Shortcut Cepat)">
                        <i class="mdi mdi-plus-circle-outline mdi-24px text-primary"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end py-2 shadow-lg quick-action-dropdown-menu" id="qaNavbarDropdownMenu" style="min-width: 270px;">
                        <li class="dropdown-header d-flex align-items-center justify-content-between py-2 border-bottom mb-1">
                            <span class="fw-bold text-primary d-flex align-items-center">
                                <i class="mdi mdi-lightning-bolt me-1 text-warning"></i>Quick Action
                            </span>
                            <button type="button" 
                                class="btn btn-icon btn-xs btn-label-secondary rounded-circle shadow-none" 
                                data-bs-toggle="modal" 
                                data-bs-target="#quickActionSettingModal"
                                title="Atur Pilihan Shortcut Saya"
                                style="width: 26px; height: 26px;">
                                <i class="mdi mdi-cog-outline fs-6"></i>
                            </button>
                        </li>

                        <div id="qaNavbarItemsContainer">
                            @forelse ($userQuickActions as $qa)
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ $qa['url'] }}">
                                        <div class="avatar avatar-xs me-2 flex-shrink-0">
                                            <span class="avatar-initial rounded-circle {{ $qa['icon_bg'] }}">
                                                <i class="{{ $qa['icon'] }}"></i>
                                            </span>
                                        </div>
                                        <div class="text-truncate">
                                            <span class="fw-semibold d-block text-truncate">{{ $qa['title'] }}</span>
                                            <small class="text-muted d-block text-truncate">{{ $qa['subtitle'] }}</small>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="px-3 py-3 text-center text-muted small">
                                    Belum ada shortcut dipilih.
                                </li>
                            @endforelse
                        </div>

                        <li class="border-top mt-1 pt-1 px-2 text-center">
                            <a href="javascript:void(0);" 
                               class="dropdown-item text-center small text-primary fw-semibold py-1 rounded d-flex align-items-center justify-content-center"
                               data-bs-toggle="modal" 
                               data-bs-target="#quickActionSettingModal">
                                <i class="mdi mdi-tune-variant me-1"></i> Sesuaikan Shortcut (Max 4)
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
            <!--/ Quick Action -->

            <!-- Quick Action (Kanban - All Roles) -->
            @if (Auth::check())
                @php
                    $kanbanUser = Auth::user();
                    $kanbanBoardsQuery = \App\Models\KanbanBoard::query();
                    if ($kanbanUser->role !== 'Admin') {
                        $kanbanBoardsQuery->where(function ($q) use ($kanbanUser) {
                            $q->whereHas('members', function ($mq) use ($kanbanUser) {
                                $mq->where('users.id', $kanbanUser->id);
                            })
                            ->orWhere('created_by', $kanbanUser->id)
                            ->orWhereHas('tasks', function ($tq) use ($kanbanUser) {
                                $tq->where('assigned_to', $kanbanUser->id)
                                   ->orWhereHas('assignees', fn($aq) => $aq->where('users.id', $kanbanUser->id));
                            });
                            if (in_array($kanbanUser->role, ['Accounting', 'Finance Manager', 'Finance'])) {
                                $q->orWhere('type', 'monitoring');
                            }
                        });
                    }
                    if ($kanbanUser->role === 'Sales') {
                        $kanbanBoardsQuery->where(function ($sq) {
                            $sq->where('type', '!=', 'monitoring')
                               ->orWhereNull('type');
                        })
                        ->where('title', 'not like', '%Monitoring Document%')
                        ->where('id', '!=', 1);
                    }
                    $pmKanbanBoards = $kanbanBoardsQuery
                        ->withCount(['tasks'])
                        ->with(['columns' => fn($cq) => $cq->orderBy('position')])
                        ->orderBy('updated_at', 'desc')
                        ->get();
                @endphp
                <li class="nav-item dropdown me-2 me-xl-1">
                    <a class="nav-link btn btn-text-primary rounded-pill btn-icon dropdown-toggle hide-arrow position-relative"
                        href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" title="Quick Action &bull; Papan Kanban">
                        <i class="mdi mdi-view-dashboard-outline mdi-24px text-primary"></i>
                        @if ($pmKanbanBoards->count() > 0)
                            <span class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-success mt-2 border"></span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end py-2 shadow-lg" style="min-width: 320px; max-width: 380px;">
                        <li class="dropdown-header d-flex align-items-center justify-content-between py-2 border-bottom mb-2">
                            <span class="fw-bold text-primary d-flex align-items-center">
                                <i class="mdi mdi-lightning-bolt text-warning me-1"></i>Quick Action &bull; Kanban
                            </span>
                            <span class="badge bg-label-primary rounded-pill">{{ $pmKanbanBoards->count() }} Papan</span>
                        </li>

                        @if ($pmKanbanBoards->count() > 0)
                            <li class="px-3 py-1">
                                <small class="text-uppercase text-muted fw-bold" style="font-size: 11px;">Papan Kanban Yang Anda Ikuti</small>
                            </li>
                            <div class="overflow-auto" style="max-height: 280px;">
                                @foreach ($pmKanbanBoards as $board)
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center py-2 px-3" href="{{ route('kanban.boards.show', $board->id) }}">
                                            <div class="avatar avatar-sm me-3 flex-shrink-0">
                                                <span class="avatar-initial rounded-circle bg-label-primary text-primary fw-bold">
                                                    <i class="mdi mdi-view-column-outline"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1 text-truncate">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="fw-semibold text-dark text-truncate d-block">{{ $board->title }}</span>
                                                    <span class="badge bg-label-secondary rounded-pill text-xs ms-1">{{ $board->tasks_count }} Task</span>
                                                </div>
                                                <small class="text-muted d-block">
                                                    {{ $board->columns->count() }} Kolom: 
                                                    {{ $board->columns->take(3)->pluck('title')->implode(', ') }}
                                                    @if ($board->columns->count() > 3)...@endif
                                                </small>
                                            </div>
                                            <i class="mdi mdi-chevron-right text-muted ms-2"></i>
                                        </a>
                                    </li>
                                @endforeach
                            </div>
                        @else
                            <li class="px-3 py-4 text-center text-muted">
                                <i class="mdi mdi-view-dashboard-off-outline mdi-36px text-secondary d-block mb-1"></i>
                                <div class="fw-semibold text-dark">Belum Ada Kanban yang Diikuti</div>
                                <small class="text-muted">Anda belum tergabung di papan Kanban manapun.</small>
                            </li>
                        @endif

                        <li class="border-top mt-2 pt-2 px-2">
                            <div class="d-flex gap-2">
                                <a class="btn btn-xs btn-outline-primary flex-grow-1" href="{{ route('kanban.index') }}">
                                    <i class="mdi mdi-view-dashboard me-1"></i> Buka Semua Kanban
                                </a>
                                <a class="btn btn-xs btn-outline-secondary" href="{{ route('service-reports.index', ['tab' => 'project']) }}" title="Daily Project Report">
                                    <i class="mdi mdi-clipboard-text-clock-outline me-1"></i> Project Report
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
            @endif

            <style>
                /* Notification Dropdown Modern Redesign */
                .notif-dropdown-menu {
                    width: 420px !important;
                    max-width: calc(100vw - 20px) !important;
                    border-radius: 16px !important;
                    border: 1px solid rgba(67, 89, 113, 0.1) !important;
                    box-shadow: 0 16px 45px -8px rgba(34, 48, 62, 0.22) !important;
                    padding: 0 !important;
                    overflow: hidden;
                }
                html.dark-style .notif-dropdown-menu {
                    background: #2b2c40 !important;
                    border-color: rgba(255, 255, 255, 0.08) !important;
                    box-shadow: 0 16px 45px -8px rgba(0, 0, 0, 0.6) !important;
                }
                .notif-dropdown-header {
                    padding: 14px 18px 12px 18px;
                    background: #ffffff;
                    border-bottom: 1px solid rgba(67, 89, 113, 0.08);
                }
                html.dark-style .notif-dropdown-header {
                    background: #2b2c40;
                    border-bottom-color: rgba(255, 255, 255, 0.08);
                }
                .notif-scrollable-list {
                    max-height: 29rem;
                    overflow-y: auto;
                    overflow-x: hidden;
                    padding: 10px 12px;
                    scrollbar-width: thin;
                    scrollbar-color: rgba(67, 89, 113, 0.2) transparent;
                }
                .notif-scrollable-list::-webkit-scrollbar {
                    width: 5px;
                }
                .notif-scrollable-list::-webkit-scrollbar-thumb {
                    background: rgba(67, 89, 113, 0.2);
                    border-radius: 4px;
                }
                html.dark-style .notif-scrollable-list::-webkit-scrollbar-thumb {
                    background: rgba(255, 255, 255, 0.2);
                }

                /* Unified Modern Card */
                .notif-card {
                    display: block;
                    text-decoration: none !important;
                    margin-bottom: 9px;
                    padding: 12px 14px;
                    border-radius: 12px;
                    background: #ffffff;
                    border: 1px solid rgba(67, 89, 113, 0.1);
                    box-shadow: 0 2px 6px rgba(67, 89, 113, 0.04);
                    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
                    position: relative;
                }
                .notif-card:last-child {
                    margin-bottom: 4px;
                }
                .notif-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 20px rgba(34, 48, 62, 0.12);
                    border-color: rgba(105, 108, 255, 0.4);
                }
                html.dark-style .notif-card {
                    background: #32344d;
                    border-color: rgba(255, 255, 255, 0.07);
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
                }
                html.dark-style .notif-card:hover {
                    background: #393b56;
                    border-color: rgba(105, 108, 255, 0.5);
                }

                /* Card Type Accents & Tints */
                .notif-card-prospect-new {
                    background: #f8faff;
                    border-left: 4px solid #696cff !important;
                }
                html.dark-style .notif-card-prospect-new {
                    background: rgba(105, 108, 255, 0.08);
                    border-left: 4px solid #696cff !important;
                }
                .notif-card-prospect-assigned {
                    background: #f4fbf7;
                    border-left: 4px solid #28c76f !important;
                }
                html.dark-style .notif-card-prospect-assigned {
                    background: rgba(40, 199, 111, 0.08);
                    border-left: 4px solid #28c76f !important;
                }
                .notif-card-ap-today {
                    background: #fff8f8;
                    border-left: 4px solid #ea5455 !important;
                }
                html.dark-style .notif-card-ap-today {
                    background: rgba(234, 84, 85, 0.08);
                    border-left: 4px solid #ea5455 !important;
                }
                .notif-card-ap-soon {
                    background: #fffbf5;
                    border-left: 4px solid #ff9f43 !important;
                }
                html.dark-style .notif-card-ap-soon {
                    background: rgba(255, 159, 67, 0.08);
                    border-left: 4px solid #ff9f43 !important;
                }
                .notif-card-cancel-po {
                    background: #fff8f8;
                    border-left: 4px solid #ea5455 !important;
                }
                html.dark-style .notif-card-cancel-po {
                    background: rgba(234, 84, 85, 0.08);
                    border-left: 4px solid #ea5455 !important;
                }
                .notif-card-pr-mention {
                    background: #fffdf5;
                    border-left: 4px solid #ffab00 !important;
                }
                html.dark-style .notif-card-pr-mention {
                    background: rgba(255, 171, 0, 0.08);
                    border-left: 4px solid #ffab00 !important;
                }
                .notif-card-comment {
                    background: #fbfbfe;
                    border-left: 4px solid #00cfe8 !important;
                }
                html.dark-style .notif-card-comment {
                    background: rgba(0, 207, 232, 0.08);
                    border-left: 4px solid #00cfe8 !important;
                }
                .notif-card-read {
                    opacity: 0.78;
                    background: #fcfcfc;
                }
                html.dark-style .notif-card-read {
                    background: #2b2c40;
                    opacity: 0.75;
                }

                /* Inner Flex Layout */
                .notif-card-inner {
                    display: flex;
                    align-items: flex-start;
                    gap: 12px;
                }
                .notif-card-avatar {
                    width: 38px;
                    height: 38px;
                    border-radius: 10px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 19px;
                    flex-shrink: 0;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
                }
                .notif-card-content {
                    flex: 1;
                    min-width: 0;
                }
                .notif-card-meta {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 6px;
                    margin-bottom: 4px;
                }
                .notif-badge-pill {
                    font-size: 10px;
                    font-weight: 700;
                    padding: 2px 7px;
                    border-radius: 6px;
                    letter-spacing: 0.2px;
                }
                .notif-time-ago {
                    font-size: 11px;
                    color: #a1acb8;
                    white-space: nowrap;
                    display: inline-flex;
                    align-items: center;
                    gap: 3px;
                }
                .notif-card-title {
                    font-size: 13.5px;
                    font-weight: 700;
                    color: #384551;
                    margin: 0 0 3px 0;
                    line-height: 1.35;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                html.dark-style .notif-card-title {
                    color: #e4e6f0;
                }
                .notif-card-desc {
                    font-size: 12px;
                    color: #697a8d;
                    margin: 0;
                    line-height: 1.42;
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
                html.dark-style .notif-card-desc {
                    color: #a3a7c2;
                }
                .notif-unread-dot {
                    width: 8px;
                    height: 8px;
                    border-radius: 50%;
                    background-color: #696cff;
                    display: inline-block;
                    flex-shrink: 0;
                    margin-top: 5px;
                    box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.2);
                }
                .notif-unread-dot.dot-danger {
                    background-color: #ea5455;
                    box-shadow: 0 0 0 3px rgba(234, 84, 85, 0.2);
                }
                .notif-unread-dot.dot-success {
                    background-color: #28c76f;
                    box-shadow: 0 0 0 3px rgba(40, 199, 111, 0.2);
                }
            </style>

            <!-- Notification -->
            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-2 me-xl-1">
                <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" id="navbarBellToggle"
                    href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-expanded="false">
                    <i class="mdi mdi-bell-outline mdi-24px" id="navbarBellIcon"></i>
                    @php
                        $userRole = Auth::user()?->role;
                        $isProspectRole = in_array($userRole, ['Admin', 'Developer', 'Super Admin', 'Sales', 'Support']) || in_array(Auth::id(), \App\Http\Controllers\ProspectController::PROSPECT_NOTIF_RECIPIENT_IDS);
                        $isFinanceOrHrRole = in_array($userRole, ['Admin', 'Developer', 'Finance', 'Finance Manager', 'Accounting', 'Super Admin']);

                        $unreadCommentCount = 0;
                        if (Auth::user()?->role != 'Admin' && @$unreadComment && $isProspectRole) {
                            $unreadCommentCount = $unreadComment->count();
                        }
                        $unreadProspectCount = $unreadCommentCount;
                        $prospectNotifications = collect();
                        $unreadProspectNotifCount = 0;
                        $unreadProspectCreatedCount = 0;
                        $unreadProspectAssignedCount = 0;
                        if (Auth::check() && $isProspectRole) {
                            try {
                                $prospectNotifications = \App\Models\ProspectNotification::where('id_user', Auth::id())
                                    ->whereIn('type', ['prospect_created', 'prospect_assigned'])
                                    ->with(['prospect.pic.client', 'prospect.support'])
                                    ->latest()
                                    ->take(15)
                                    ->get();

                                $unreadProspectNotifCount = $prospectNotifications->where('is_read', false)->count();
                                $unreadProspectCreatedCount = $prospectNotifications->where('is_read', false)->where('type', 'prospect_created')->count();
                                $unreadProspectAssignedCount = $prospectNotifications->where('is_read', false)->where('type', 'prospect_assigned')->count();
                            } catch (\Throwable $e) {
                                $prospectNotifications = collect();
                            }
                        }
                        $unreadKanbanMentionCount = (@$kanbanMentions ? $kanbanMentions->where('is_read', 0)->count() : 0);
                        
                        $myActiveToolAudit = null;
                        $incomingToolTransfers = collect();
                        if (Auth::check()) {
                            try {
                                $myActiveToolAudit = \App\Models\ToolAudit::with('period')
                                    ->where('id_technician', Auth::id())
                                    ->whereIn('status_submit', ['Draft', 'Rejected'])
                                    ->whereHas('period', function ($q) {
                                        $q->where('status', 'Open');
                                    })
                                    ->latest('id')
                                    ->first();

                                $incomingToolTransfers = \App\Models\ToolTransfer::with(['fixedAsset.toolsMaster', 'fromUser'])
                                    ->where('id_to_user', Auth::id())
                                    ->where('status', 'Pending')
                                    ->latest('id')
                                    ->get();
                            } catch (\Throwable $e) {
                                $myActiveToolAudit = null;
                                $incomingToolTransfers = collect();
                            }
                        }

                        $unreadReimbursementCount = 0;
                        $pendingReimbursementsNotif = collect();
                        if (Auth::check() && $isFinanceOrHrRole) {
                            try {
                                $unreadReimbursementCount = \App\Models\HrReimbursement::where('status', 'Pending')->count();
                                $pendingReimbursementsNotif = \App\Models\HrReimbursement::where('status', 'Pending')
                                    ->with(['employee.user', 'employee.department'])
                                    ->latest()
                                    ->take(10)
                                    ->get();
                            } catch (\Throwable $e) {
                                $unreadReimbursementCount = 0;
                                $pendingReimbursementsNotif = collect();
                            }
                        }

                        $hasBadge = false;
                        if (Auth::user()?->role == 'Admin' && @$unreadCommentAdmin && $unreadCommentAdmin->count() >= 1) $hasBadge = true;
                        if ($unreadCommentCount >= 1) $hasBadge = true;
                        if ($unreadProspectNotifCount >= 1) $hasBadge = true;
                        if (@$prMentions && $prMentions->count() >= 1) $hasBadge = true;
                        if (@$unreadKanbanMentionCount >= 1) $hasBadge = true;
                        if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']) && @$pendingCancelQuotes && $pendingCancelQuotes->count() >= 1) $hasBadge = true;
                        if (in_array(Auth::user()?->role, ['Accounting', 'Admin', 'Sales']) && @$paymentUnreadCount >= 1) $hasBadge = true;
                        if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']) && (@$apDueTodayCount >= 1 || @$apDueSoonCount >= 1 || @$apOverdueCount >= 1)) $hasBadge = true;
                        if ($myActiveToolAudit) $hasBadge = true;
                        if ($incomingToolTransfers->count() >= 1) $hasBadge = true;
                        if ($unreadReimbursementCount >= 1) $hasBadge = true;
                        if (isset($isLeaveAlertEligible) && $isLeaveAlertEligible && isset($leaveAlertPendingCount) && $leaveAlertPendingCount >= 1) $hasBadge = true;
                    @endphp
                    <span id="navbarBellDot" class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border {{ $hasBadge ? '' : 'd-none' }}"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end notif-dropdown-menu shadow-lg">
                    <li class="dropdown-menu-header">
                        <div class="notif-dropdown-header">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-xs bg-label-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                        <i class="mdi mdi-bell-ring-outline fs-6"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold fs-6">Notifikasi</h6>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @php
                                        $totalBadges = 0;
                                        if (Auth::user()?->role == 'Admin' && @$unreadCommentAdmin) $totalBadges += $unreadCommentAdmin->count();
                                        if ($unreadCommentCount > 0) $totalBadges += $unreadCommentCount;
                                        if ($unreadProspectNotifCount > 0) $totalBadges += $unreadProspectNotifCount;
                                        if (@$prMentions) $totalBadges += $prMentions->count();
                                        if (@$unreadKanbanMentionCount > 0) $totalBadges += $unreadKanbanMentionCount;
                                        if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']) && @$pendingCancelQuotes) $totalBadges += $pendingCancelQuotes->count();
                                        if (in_array(Auth::user()?->role, ['Accounting', 'Admin', 'Sales']) && @$paymentUnreadCount) $totalBadges += $paymentUnreadCount;
                                        if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']) && @$apDueTodayCount) $totalBadges += $apDueTodayCount;
                                        if ($myActiveToolAudit) $totalBadges += 1;
                                        if ($incomingToolTransfers->count() > 0) $totalBadges += $incomingToolTransfers->count();
                                        if ($unreadReimbursementCount > 0) $totalBadges += $unreadReimbursementCount;
                                        if (isset($isLeaveAlertEligible) && $isLeaveAlertEligible && isset($leaveAlertPendingCount) && $leaveAlertPendingCount > 0) $totalBadges += $leaveAlertPendingCount;
                                    @endphp
                                    <span id="notifTotalBadge" class="badge rounded-pill bg-danger py-1 px-2 fw-bold {{ $totalBadges > 0 ? '' : 'd-none' }}" style="font-size: 11px;">
                                        {{ $totalBadges }} Baru
                                    </span>
                                    <button type="button" id="btnMarkAllNotifRead" class="btn btn-xs btn-outline-primary py-1 px-2 d-inline-flex align-items-center gap-1 rounded-pill" title="Tandai semua sudah dibaca" style="font-size: 11px; font-weight: 600;">
                                        <i class="mdi mdi-check-all fs-6"></i>
                                        <span>Tandai Semua Dibaca</span>
                                    </button>
                                </div>
                            </div>
                            {{-- Category Badges --}}
                            <div class="d-flex flex-wrap gap-1 mt-2 align-items-center">
                                @if (isset($isLeaveAlertEligible) && $isLeaveAlertEligible && isset($leaveAlertPendingCount) && $leaveAlertPendingCount > 0)
                                    <span class="badge rounded-pill bg-info text-white" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalLeaveApprovalAlert">
                                        <i class="mdi mdi-calendar-alert me-1"></i> {{ $leaveAlertPendingCount }} Izin/Cuti Menunggu
                                    </span>
                                @endif
                                @if ($isFinanceOrHrRole && $unreadReimbursementCount > 0)
                                    <a href="{{ route('hr.reimbursements.index') }}" class="badge rounded-pill bg-warning text-dark text-decoration-none">
                                        <i class="mdi mdi-receipt-text-outline me-1"></i> {{ $unreadReimbursementCount }} Klaim Reimbursement
                                    </a>
                                @endif
                                @if ($incomingToolTransfers->count() > 0)
                                    <span class="badge rounded-pill bg-warning text-dark">
                                        <i class="mdi mdi-account-arrow-right me-1"></i> {{ $incomingToolTransfers->count() }} Transfer Tools Masuk
                                    </span>
                                @endif
                                @if ($myActiveToolAudit)
                                    <span class="badge rounded-pill bg-warning text-dark">
                                        <i class="mdi mdi-tools me-1"></i> 1 Audit Tools Wajib
                                    </span>
                                @endif
                                @if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']))
                                    @if (@$apDueTodayCount > 0)
                                        <span class="badge rounded-pill bg-danger">{{ $apDueTodayCount }} Hutang Hari Ini</span>
                                    @elseif (@$apDueSoonCount > 0)
                                        <span class="badge rounded-pill bg-warning text-dark">{{ $apDueSoonCount }} Hutang Segera</span>
                                    @endif
                                @endif
                                @if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']) && @$pendingCancelQuotes && $pendingCancelQuotes->count() > 0)
                                    <span class="badge rounded-pill bg-danger">{{ $pendingCancelQuotes->count() }} Cancel PO</span>
                                @endif
                                @if ($unreadProspectNotifCount > 0 && $isProspectRole)
                                    @if ($unreadProspectCreatedCount > 0)
                                        <span id="prospectCreatedCountBadge" class="badge rounded-pill bg-primary">{{ $unreadProspectCreatedCount }} Prospect Baru</span>
                                    @endif
                                    @if ($unreadProspectAssignedCount > 0)
                                        <span id="prospectAssignedCountBadge" class="badge rounded-pill bg-success">{{ $unreadProspectAssignedCount }} Ditugaskan</span>
                                    @endif
                                @endif
                                @if (in_array(Auth::user()?->role, ['Accounting', 'Admin', 'Sales']))
                                    <span id="paymentNotifCountBadge" class="badge rounded-pill bg-label-success {{ (@$paymentUnreadCount > 0) ? '' : 'd-none' }}">{{ @$paymentUnreadCount ?: 0 }} Transaksi</span>
                                @endif
                                @if (Auth::user()?->role == 'Admin')
                                    @if (@$unreadCommentAdmin && $unreadCommentAdmin->count() > 0)
                                        <span class="badge rounded-pill bg-label-primary">{{ $unreadCommentAdmin->count() }} New Comment</span>
                                    @endif
                                @elseif ($isProspectRole)
                                    @if ($unreadCommentCount > 0)
                                        <span id="commentUnreadCountBadge" class="badge rounded-pill bg-label-primary">{{ $unreadCommentCount }} Komentar Baru</span>
                                    @endif
                                @endif
                                @if (@$unreadKanbanMentionCount > 0)
                                    <span id="kanbanMentionCountBadge" class="badge rounded-pill bg-label-info">{{ $unreadKanbanMentionCount }} Mention Kanban</span>
                                @endif
                            </div>
                        </div>
                    </li>
                    <li class="dropdown-notifications-list notif-scrollable-list">
                        @php
                            $unifiedNotifications = collect();

                            // 1. AP Notifications (Hutang)
                            if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']) && @$apNotifications && $apNotifications->count() > 0) {
                                foreach ($apNotifications as $apNotif) {
                                    $unifiedNotifications->push([
                                        'type' => 'ap',
                                        'time' => \Carbon\Carbon::parse($apNotif->created_at ?? $apNotif->due_date),
                                        'item' => $apNotif,
                                    ]);
                                }
                            }

                            // 2. Cancel PO
                            if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']) && @$pendingCancelQuotes && $pendingCancelQuotes->count() > 0) {
                                foreach ($pendingCancelQuotes as $cancelQ) {
                                    $unifiedNotifications->push([
                                        'type' => 'cancel_po',
                                        'time' => \Carbon\Carbon::parse($cancelQ->updated_at ?? $cancelQ->created_at),
                                        'item' => $cancelQ,
                                    ]);
                                }
                            }

                            // 3. PR Mentions
                            if (@$prMentions && $prMentions->count() > 0) {
                                foreach ($prMentions as $pm) {
                                    $unifiedNotifications->push([
                                        'type' => 'pr_mention',
                                        'time' => \Carbon\Carbon::parse($pm->created_at),
                                        'item' => $pm,
                                    ]);
                                }
                            }

                            // 4. Prospect Notifications (Baru & Ditugaskan)
                            if ($isProspectRole && $prospectNotifications && $prospectNotifications->count() > 0) {
                                foreach ($prospectNotifications as $pn) {
                                    $unifiedNotifications->push([
                                        'type' => 'prospect',
                                        'time' => \Carbon\Carbon::parse($pn->created_at),
                                        'item' => $pn,
                                    ]);
                                }
                            }

                            // 5. Comments on Prospects
                            $renderedCommentIds = [];
                            if ($isProspectRole && Auth::user()?->role != 'Admin' && @$comment) {
                                foreach ($comment as $item) {
                                    if ($item->type == 'prospect') {
                                        $renderedCommentIds[] = $item->idC;
                                        $unifiedNotifications->push([
                                            'type' => 'prospect_comment',
                                            'time' => \Carbon\Carbon::parse($item->date),
                                            'item' => $item,
                                        ]);
                                    }
                                }
                            }

                            // 5b. Mention comments for current user
                            if (Auth::check() && $isProspectRole) {
                                try {
                                    $userMentions = \App\Models\MentionComment::where('id_mention', Auth::id())
                                        ->whereNotIn('id_comment', $renderedCommentIds)
                                        ->with(['comment.user', 'comment.prospect.pic.client'])
                                        ->latest()
                                        ->take(10)
                                        ->get();
                                    foreach ($userMentions as $um) {
                                        if ($um->comment) {
                                            $unifiedNotifications->push([
                                                'type' => 'comment_mention',
                                                'time' => \Carbon\Carbon::parse($um->comment->date ?? $um->created_at),
                                                'item' => $um,
                                            ]);
                                        }
                                    }
                                } catch (\Throwable $e) {}
                            }

                            // 6. Payment Notifications
                            if (in_array(Auth::user()?->role, ['Accounting', 'Sales']) && @$paymentNotifications && $paymentNotifications->count() > 0) {
                                foreach ($paymentNotifications as $pn) {
                                    $unifiedNotifications->push([
                                        'type' => 'payment',
                                        'time' => \Carbon\Carbon::parse($pn->created_at),
                                        'item' => $pn,
                                    ]);
                                }
                            }

                            // 7. Kanban Mentions
                            if (@$kanbanMentions && $kanbanMentions->count() > 0) {
                                foreach ($kanbanMentions as $km) {
                                    $unifiedNotifications->push([
                                        'type' => 'kanban_mention',
                                        'time' => \Carbon\Carbon::parse($km->comment_created_at ?? $km->mention_created_at),
                                        'item' => $km,
                                    ]);
                                }
                            }

                            // 8. Tool Audit Notification for Technician
                            if ($myActiveToolAudit) {
                                $unifiedNotifications->push([
                                    'type' => 'tool_audit',
                                    'time' => \Carbon\Carbon::parse($myActiveToolAudit->updated_at ?? ($myActiveToolAudit->period?->tanggal_mulai ?? now())),
                                    'item' => $myActiveToolAudit,
                                ]);
                            }

                            // 9. Incoming Tool Transfer Requests for Technician
                            if ($incomingToolTransfers && $incomingToolTransfers->count() > 0) {
                                foreach ($incomingToolTransfers as $trf) {
                                    $unifiedNotifications->push([
                                        'type' => 'tool_transfer_incoming',
                                        'time' => \Carbon\Carbon::parse($trf->requested_at),
                                        'item' => $trf,
                                    ]);
                                }
                            }

                            // 10. Pengingat Upload Surat Dokter Susulan untuk Karyawan Sakit
                            if (isset($navPendingSickLeave) && $navPendingSickLeave) {
                                $unifiedNotifications->push([
                                    'type' => 'pending_sick_leave',
                                    'time' => \Carbon\Carbon::parse($navPendingSickLeave->created_at),
                                    'item' => $navPendingSickLeave,
                                ]);
                            }

                            // 11. Pengajuan Klaim Reimbursement untuk Finance / HR
                            if ($isFinanceOrHrRole && isset($pendingReimbursementsNotif) && $pendingReimbursementsNotif->count() > 0) {
                                foreach ($pendingReimbursementsNotif as $rNotif) {
                                    $unifiedNotifications->push([
                                        'type' => 'pending_reimbursement',
                                        'time' => \Carbon\Carbon::parse($rNotif->created_at),
                                        'item' => $rNotif,
                                    ]);
                                }
                            }

                            // 12. Pengajuan Cuti / Izin Karyawan untuk Finance & Akun Terdaftar
                            if (isset($isLeaveAlertEligible) && $isLeaveAlertEligible && isset($leaveAlertPendingList) && $leaveAlertPendingList->count() > 0) {
                                foreach ($leaveAlertPendingList as $lNotif) {
                                    $unifiedNotifications->push([
                                        'type' => 'pending_leave_request',
                                        'time' => \Carbon\Carbon::parse($lNotif->created_at),
                                        'item' => $lNotif,
                                    ]);
                                }
                            }

                            // Sort descending chronologically (newest first)
                            $unifiedNotifications = $unifiedNotifications->sortByDesc(function ($n) {
                                return $n['time'] ? $n['time']->timestamp : 0;
                            })->values();
                        @endphp

                        {{-- Chronological Notification Feed --}}
                        <div id="unifiedNotifList">
                            @foreach ($unifiedNotifications as $notif)
                                @if ($notif['type'] === 'ap')
                                    @php $apNotif = $notif['item']; @endphp
                                    <a href="{{ $apNotif->url }}"
                                        class="notif-card {{ $apNotif->status_type === 'today' ? 'notif-card-ap-today' : 'notif-card-ap-soon' }}">
                                        <div class="notif-card-inner">
                                            <div class="notif-card-avatar {{ $apNotif->avatar_bg }} text-white">
                                                <i class="mdi {{ $apNotif->icon }}"></i>
                                            </div>
                                            <div class="notif-card-content">
                                                <div class="notif-card-meta">
                                                    <span class="badge {{ $apNotif->badge_class }} notif-badge-pill">
                                                        {{ $apNotif->status_badge }}
                                                    </span>
                                                    <span class="notif-time-ago">
                                                        <i class="mdi mdi-calendar-alert fs-7"></i> Due {{ \Carbon\Carbon::parse($apNotif->due_date)->format('d/m/Y') }}
                                                    </span>
                                                </div>
                                                <h6 class="notif-card-title">{{ $apNotif->invoice }}</h6>
                                                <p class="notif-card-desc">
                                                    <strong>{{ $apNotif->supplier_name }}</strong> • Sisa: <span class="text-danger fw-bold">Rp {{ number_format($apNotif->remaining, 0, ',', '.') }}</span>
                                                </p>
                                            </div>
                                            <span class="notif-unread-dot dot-danger"></span>
                                        </div>
                                    </a>
                                @elseif ($notif['type'] === 'cancel_po')
                                    @php $cancelQ = $notif['item']; @endphp
                                    <a href="{{ route('unit-quotation.show', $cancelQ->id) }}"
                                        class="notif-card notif-card-cancel-po">
                                        <div class="notif-card-inner">
                                            <div class="notif-card-avatar bg-danger text-white">
                                                <i class="mdi mdi-file-cancel-outline"></i>
                                            </div>
                                            <div class="notif-card-content">
                                                <div class="notif-card-meta">
                                                    <span class="badge bg-label-danger notif-badge-pill">Cancel PO</span>
                                                    <span class="notif-time-ago">
                                                        <i class="mdi mdi-clock-outline fs-7"></i> {{ \Carbon\Carbon::parse($cancelQ->updated_at)->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <h6 class="notif-card-title text-danger">{{ $cancelQ->no_quote }}</h6>
                                                <p class="notif-card-desc">
                                                    <strong>{{ $cancelQ->sales?->name }}</strong> mengajukan pembatalan PO ({{ $cancelQ->client?->company ?? '-' }})
                                                </p>
                                            </div>
                                            <span class="notif-unread-dot dot-danger"></span>
                                        </div>
                                    </a>
                                @elseif ($notif['type'] === 'pr_mention')
                                    @php $pm = $notif['item']; @endphp
                                    <a href="{{ route('purchase-request.show', $pm->discussion->id_pending) }}#diskusi"
                                        class="notif-card notif-card-pr-mention view-pr-mention {{ $pm->level == '0' ? 'notif-card-unread' : 'notif-card-read' }}"
                                        data-mention-id="{{ $pm->id }}" data-read="{{ $pm->level == '0' ? '0' : '1' }}">
                                        <div class="notif-card-inner">
                                            <div class="notif-card-avatar bg-label-warning text-warning">
                                                <i class="mdi mdi-at"></i>
                                            </div>
                                            <div class="notif-card-content">
                                                <div class="notif-card-meta">
                                                    <span class="badge bg-label-warning notif-badge-pill">PR Mention</span>
                                                    <span class="notif-time-ago">
                                                        <i class="mdi mdi-clock-outline fs-7"></i> {{ \Carbon\Carbon::parse($pm->created_at)->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <h6 class="notif-card-title">PR #{{ $pm->discussion->pending->no_pending ?? $pm->discussion->id_pending }}</h6>
                                                <p class="notif-card-desc">Kamu di-mention dalam diskusi Purchase Request</p>
                                            </div>
                                            @if ($pm->level == '0')
                                                <span class="notif-unread-dot"></span>
                                            @endif
                                        </div>
                                    </a>
                                @elseif ($notif['type'] === 'prospect')
                                    @php
                                        $ap = $notif['item'];
                                        $p = $ap->prospect;
                                        $pic = $p?->pic;
                                        $client = $pic?->client;
                                        $isCreated = ($ap->type === 'prospect_created');
                                        $isRead = (bool) $ap->is_read;
                                    @endphp
                                    @if ($p)
                                        <a href="{{ route('prospect.show', $p->id) }}"
                                            class="notif-card {{ $isCreated ? 'notif-card-prospect-new' : 'notif-card-prospect-assigned' }} prospect-notif-item {{ $isRead ? 'notif-card-read' : '' }}"
                                            data-prospect-notif-id="{{ $ap->id }}"
                                            data-read="{{ $isRead ? '1' : '0' }}"
                                            onclick="if(window.prospectNotifReadUrlTemplate && !{{ $isRead ? 'true' : 'false' }}){$.post(window.prospectNotifReadUrlTemplate.replace('__ID__', {{ $ap->id }}), {_token: '{{ csrf_token() }}'});}">
                                            <div class="notif-card-inner">
                                                <div class="notif-card-avatar {{ $isCreated ? 'bg-primary text-white' : 'bg-success text-white' }}">
                                                    <i class="mdi {{ $isCreated ? 'mdi-account-star-outline' : 'mdi-account-arrow-right-outline' }}"></i>
                                                </div>
                                                <div class="notif-card-content">
                                                    <div class="notif-card-meta">
                                                        <span class="badge {{ $isCreated ? 'bg-label-primary' : 'bg-label-success' }} notif-badge-pill">
                                                            {{ $isCreated ? 'Marketing Lead Baru' : 'Ditugaskan' }}
                                                        </span>
                                                        <span class="notif-time-ago">
                                                            <i class="mdi mdi-clock-outline fs-7"></i> {{ $ap->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                    <h6 class="notif-card-title">{{ $client->company ?? ($p->company_name ?? 'Perusahaan Baru') }}</h6>
                                                    <p class="notif-card-desc">
                                                        @if ($isCreated && $p->support)
                                                            <span class="fw-semibold text-dark">Oleh: {{ $p->support->name }}</span> • 
                                                        @endif
                                                        Kategori: <strong class="{{ $isCreated ? 'text-primary' : 'text-success' }}">{{ $p->category ?: 'General' }}</strong> • {{ \Illuminate\Support\Str::limit($p->kebutuhan, 48) }}
                                                    </p>
                                                </div>
                                                @unless ($isRead)
                                                    <span class="notif-unread-dot {{ $isCreated ? '' : 'dot-success' }}"></span>
                                                @endunless
                                            </div>
                                        </a>
                                    @endif
                                @elseif ($notif['type'] === 'prospect_comment')
                                    @php
                                        $item = $notif['item'];
                                        $date = \Carbon\Carbon::parse($item->date);
                                        $isUnread = ($item->level == 1);
                                        $prospectId = $item->idP ?? $item->idQ;
                                    @endphp
                                    <a href="{{ route('prospect.show', $prospectId) }}#viewComment"
                                        class="notif-card notif-card-comment view-prospect {{ $isUnread ? 'notif-card-unread' : 'notif-card-read' }}"
                                        data-id="{{ $item->idC }}" data-quotation="{{ $prospectId }}" data-read="{{ $isUnread ? '0' : '1' }}">
                                        <div class="notif-card-inner">
                                            <div class="notif-card-avatar">
                                                <img src="{{ url('') . '/' . $item->image }}" alt class="w-100 h-100 rounded-3" style="object-fit: cover;" onerror="this.src='{{ asset('assets/img/avatars/1.png') }}'" />
                                            </div>
                                            <div class="notif-card-content">
                                                <div class="notif-card-meta">
                                                    <span class="badge bg-label-info notif-badge-pill">Komentar Lead</span>
                                                    <span class="notif-time-ago">
                                                        <i class="mdi mdi-clock-outline fs-7"></i> {{ $date->diffInHours(\Carbon\Carbon::now()) > 24 ? $date->format('d M y') : $date->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <h6 class="notif-card-title">{{ $item->company ?? ('Marketing Lead #' . $prospectId) }}</h6>
                                                <p class="notif-card-desc">
                                                    <span class="fw-semibold text-dark">{{ $item->name }}:</span> {{ \Illuminate\Support\Str::limit($item->comment, 65) }}
                                                </p>
                                            </div>
                                            @if ($isUnread)
                                                <span class="notif-unread-dot"></span>
                                            @endif
                                        </div>
                                    </a>
                                @elseif ($notif['type'] === 'comment_mention')
                                    @php
                                        $um = $notif['item'];
                                        $c = $um->comment;
                                        $u = $c?->user;
                                        $p = $c?->prospect;
                                        $client = $p?->pic?->client;
                                        $date = \Carbon\Carbon::parse($c?->date ?? $um->created_at);
                                        $isUnread = ($um->level == '0');
                                        $prospectId = $c?->id_prospect;
                                    @endphp
                                    @if ($prospectId)
                                        <a href="{{ route('prospect.show', $prospectId) }}#viewComment"
                                            class="notif-card notif-card-comment view-prospect {{ $isUnread ? 'notif-card-unread' : 'notif-card-read' }}"
                                            data-id="{{ $c?->id }}" data-quotation="{{ $prospectId }}" data-read="{{ $isUnread ? '0' : '1' }}"
                                            onclick="if({{ $isUnread ? 'true' : 'false' }}){$.post('{{ url('prospect') }}/{{ $c?->id }}/view_comment', {_token: '{{ csrf_token() }}'});}">
                                            <div class="notif-card-inner">
                                                <div class="notif-card-avatar bg-label-warning text-warning">
                                                    @if ($u?->image)
                                                        <img src="{{ url('') . '/' . $u->image }}" alt class="w-100 h-100 rounded-3" style="object-fit: cover;" onerror="this.src='{{ asset('assets/img/avatars/1.png') }}'" />
                                                    @else
                                                        <i class="mdi mdi-at"></i>
                                                    @endif
                                                </div>
                                                <div class="notif-card-content">
                                                    <div class="notif-card-meta">
                                                        <span class="badge bg-label-warning notif-badge-pill">Mention Prospek</span>
                                                        <span class="notif-time-ago">
                                                            <i class="mdi mdi-clock-outline fs-7"></i> {{ $date->diffInHours(\Carbon\Carbon::now()) > 24 ? $date->format('d M y') : $date->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                    <h6 class="notif-card-title">{{ $client->company ?? ($p->company_name ?? ('Prospect #' . $prospectId)) }}</h6>
                                                    <p class="notif-card-desc">
                                                        <span class="fw-semibold text-dark">{{ $u->name ?? 'Seseorang' }}:</span> {{ \Illuminate\Support\Str::limit($c?->comment, 65) }}
                                                    </p>
                                                </div>
                                                @if ($isUnread)
                                                    <span class="notif-unread-dot dot-danger"></span>
                                                @endif
                                            </div>
                                        </a>
                                    @endif
                                @elseif ($notif['type'] === 'payment')
                                    @php
                                        $pn = $notif['item'];
                                        $isInvoiceRequested = $pn->type === 'invoice_requested';
                                        $isInvoiceApproved = $pn->type === 'invoice_approved';
                                        $isContractRequested = $pn->type === 'contract_requested';
                                        $isContractApproved = $pn->type === 'contract_approved';
                                        $isContractSigned = $pn->type === 'contract_signed';
                                        $pnAmount = $pn->type === 'payment'
                                            ? ($pn->payment->amount ?? 0)
                                            : (($isContractRequested || $isContractApproved || ($isContractSigned && !$pn->id_invoice))
                                                ? ($pn->unitQuotation->total ?? 0)
                                                : ($pn->unitQuotation->total ?? 0) * ($pn->invoice->percent ?? 100) / 100);
                                        $pnUrl = route('unit-quotation.show', $pn->id_unit_quotation);
                                        if ($pn->type === 'payment') {
                                            if ($pn->id_payment) {
                                                $pnUrl = route('payment_detail.payment', $pn->id_payment);
                                            } else {
                                                $pnUrl = route('payment_index.payment');
                                            }
                                        } elseif ($pn->id_invoice) {
                                            if ($isInvoiceRequested) {
                                                $pnUrl = route('before.accept.unit', $pn->id_invoice);
                                            } elseif ($isInvoiceApproved) {
                                                $pnUrl = route('invoice.show_unit', $pn->id_invoice);
                                            } elseif ($isContractSigned && in_array(Auth::user()->role, ['Accounting', 'Admin'])) {
                                                $pnUrl = route('before.accept.unit', $pn->id_invoice);
                                            }
                                        } elseif ($isContractRequested) {
                                            $pnUrl = route('contract.index');
                                        } elseif ($isContractApproved || $isContractSigned) {
                                            if ($pn->id_unit_quotation) {
                                                $contract = $pn->unitQuotation?->contracts?->sortByDesc('id')->first()
                                                    ?? \App\Models\Contract::where('id_unit_quotation', $pn->id_unit_quotation)->latest('id')->first();
                                                if ($contract) {
                                                    $pnUrl = route('contract.show', $contract->id);
                                                }
                                            }
                                        }
                                        
                                        $avatarBg = 'bg-label-success text-success';
                                        $iconClass = 'mdi-cash-multiple';
                                        $typeBadgeLabel = 'Payment';
                                        $typeBadgeClass = 'bg-label-success';
                                        $typeCardClass = 'notif-card-prospect-assigned';

                                        if ($isInvoiceRequested) {
                                            $avatarBg = 'bg-label-primary text-primary';
                                            $iconClass = 'mdi-file-document-outline';
                                            $typeBadgeLabel = 'Invoice Baru';
                                            $typeBadgeClass = 'bg-label-primary';
                                            $typeCardClass = 'notif-card-prospect-new';
                                        } elseif ($isInvoiceApproved) {
                                            $avatarBg = 'bg-label-info text-info';
                                            $iconClass = 'mdi-check-decagram-outline';
                                            $typeBadgeLabel = 'Invoice ACC';
                                            $typeBadgeClass = 'bg-label-info';
                                            $typeCardClass = 'notif-card-comment';
                                        } elseif ($isContractRequested) {
                                            $avatarBg = 'bg-label-warning text-warning';
                                            $iconClass = 'mdi-file-sign';
                                            $typeBadgeLabel = 'Kontrak Baru';
                                            $typeBadgeClass = 'bg-label-warning';
                                            $typeCardClass = 'notif-card-pr-mention';
                                        } elseif ($isContractApproved) {
                                            $avatarBg = 'bg-label-success text-success';
                                            $iconClass = 'mdi-file-check-outline';
                                            $typeBadgeLabel = 'Kontrak ACC';
                                            $typeBadgeClass = 'bg-label-success';
                                            $typeCardClass = 'notif-card-prospect-assigned';
                                        } elseif ($isContractSigned) {
                                            $avatarBg = 'bg-label-success text-success';
                                            $iconClass = 'mdi-draw-pen';
                                            $typeBadgeLabel = 'Kontrak TTD';
                                            $typeBadgeClass = 'bg-label-success';
                                            $typeCardClass = 'notif-card-prospect-assigned';
                                        }
                                    @endphp
                                    <a href="{{ $pnUrl }}"
                                        class="notif-card {{ $typeCardClass }} payment-notif-item {{ $pn->is_read ? 'notif-card-read' : 'payment-notif-unread' }}"
                                        data-notif-id="{{ $pn->id }}" data-read="{{ $pn->is_read ? '1' : '0' }}">
                                        <div class="notif-card-inner">
                                            <div class="notif-card-avatar {{ $avatarBg }}">
                                                <i class="mdi {{ $iconClass }}"></i>
                                            </div>
                                            <div class="notif-card-content">
                                                <div class="notif-card-meta">
                                                    <span class="badge {{ $typeBadgeClass }} notif-badge-pill">{{ $typeBadgeLabel }}</span>
                                                    <span class="notif-time-ago">
                                                        <i class="mdi mdi-clock-outline fs-7"></i> {{ $pn->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <h6 class="notif-card-title">{{ $pn->unitQuotation->no_quote ?? '-' }}</h6>
                                                <p class="notif-card-desc">
                                                    @if ($isInvoiceRequested)
                                                        Invoice senilai <strong class="text-primary">Rp {{ number_format($pnAmount, 0, '', '.') }}</strong> menunggu diterbitkan ({{ $pn->unitQuotation->client->company ?? '-' }})
                                                    @elseif ($isInvoiceApproved)
                                                        Invoice senilai <strong class="text-info">Rp {{ number_format($pnAmount, 0, '', '.') }}</strong> sudah di-acc Accounting ({{ $pn->unitQuotation->client->company ?? '-' }})
                                                    @elseif ($isContractRequested)
                                                        Pengajuan Selling Contract baru ({{ $pn->unitQuotation->client->company ?? '-' }})
                                                    @elseif ($isContractApproved)
                                                        Selling Contract sudah di-acc Accounting ({{ $pn->unitQuotation->client->company ?? '-' }})
                                                    @elseif ($isContractSigned)
                                                        @if (in_array(Auth::user()->role, ['Accounting', 'Admin']) && $pn->id_invoice)
                                                            Kontrak ditandatangani & Invoice <strong class="text-success">Rp {{ number_format($pnAmount, 0, '', '.') }}</strong> menunggu diterbitkan ({{ $pn->unitQuotation->client->company ?? '-' }})
                                                        @else
                                                            Selling Contract telah ditandatangani Customer ({{ $pn->unitQuotation->client->company ?? '-' }})
                                                        @endif
                                                    @else
                                                        Payment <strong class="text-success">Rp {{ number_format($pnAmount, 0, '', '.') }}</strong> ditambahkan ({{ $pn->unitQuotation->client->company ?? '-' }})
                                                    @endif
                                                </p>
                                            </div>
                                            @unless ($pn->is_read)
                                                <span class="notif-unread-dot dot-success"></span>
                                            @endunless
                                        </div>
                                    </a>
                                @elseif ($notif['type'] === 'kanban_mention')
                                    @php
                                        $km = $notif['item'];
                                        $isUnread = !$km->is_read;
                                        $date = \Carbon\Carbon::parse($km->comment_created_at ?? $km->mention_created_at);
                                        $authorPhoto = $km->author_photo ? url($km->author_photo) : asset('assets/img/avatars/1.png');
                                    @endphp
                                    <a href="{{ route('notifications.kanban.go', $km->comment_id) }}"
                                        class="notif-card notif-card-comment kanban-mention-item {{ $isUnread ? 'notif-card-unread' : 'notif-card-read' }}"
                                        data-comment-id="{{ $km->comment_id }}" data-read="{{ $isUnread ? '0' : '1' }}">
                                        <div class="notif-card-inner">
                                            <div class="notif-card-avatar">
                                                <img src="{{ $authorPhoto }}" alt="{{ $km->author_name }}" class="w-100 h-100 rounded-3" style="object-fit: cover;" onerror="this.src='{{ asset('assets/img/avatars/1.png') }}'" />
                                            </div>
                                            <div class="notif-card-content">
                                                <div class="notif-card-meta">
                                                    <span class="badge bg-label-primary notif-badge-pill"><i class="mdi mdi-view-column-outline me-1"></i>{{ $km->board_name }}</span>
                                                    <span class="notif-time-ago">
                                                        <i class="mdi mdi-clock-outline fs-7"></i> {{ $date->diffInHours(\Carbon\Carbon::now()) > 24 ? $date->format('d M y') : $date->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <h6 class="notif-card-title">{{ $km->task_title }}</h6>
                                                <p class="notif-card-desc">
                                                    <span class="fw-semibold text-dark">{{ $km->author_name }}:</span> {{ \Illuminate\Support\Str::limit(strip_tags($km->comment), 65) }}
                                                </p>
                                            </div>
                                            @if ($isUnread)
                                                <span class="notif-unread-dot dot-primary"></span>
                                            @endif
                                        </div>
                                    </a>
                                @elseif ($notif['type'] === 'tool_audit')
                                    @php
                                        $auditItem = $notif['item'];
                                        $periodItem = $auditItem->period;
                                        $isRejected = $auditItem->status_submit === 'Rejected';
                                        $dueDate = $periodItem ? \Carbon\Carbon::parse($periodItem->tanggal_selesai) : null;
                                        $daysLeft = $dueDate ? ceil(now()->floatDiffInDays($dueDate, false)) : 0;
                                    @endphp
                                    <a href="{{ route('tool-audit.show', $auditItem->id) }}"
                                        class="notif-card notif-card-comment notif-card-unread"
                                        style="border-left: 3px solid #ff9f43 !important; background: rgba(255, 159, 67, 0.06) !important;">
                                        <div class="notif-card-inner">
                                            <div class="notif-card-avatar d-flex align-items-center justify-content-center bg-label-warning rounded-3" style="width: 40px; height: 40px; min-width: 40px;">
                                                <i class="mdi mdi-tools fs-4 text-warning"></i>
                                            </div>
                                            <div class="notif-card-content">
                                                <div class="notif-card-meta">
                                                    <span class="badge {{ $isRejected ? 'bg-label-danger' : 'bg-label-warning' }} notif-badge-pill">
                                                        <i class="mdi {{ $isRejected ? 'mdi-alert-circle' : 'mdi-clock-alert-outline' }} me-1"></i>
                                                        {{ $isRejected ? 'Perlu Revisi Audit' : 'Self-Audit Tools' }}
                                                    </span>
                                                    @if ($periodItem)
                                                        <span class="notif-time-ago text-danger fw-semibold">
                                                            <i class="mdi mdi-calendar-alert fs-7"></i>
                                                            @if ($daysLeft > 0)
                                                                Sisa {{ $daysLeft }} hari
                                                            @elseif ($daysLeft === 0)
                                                                Batas Hari Ini!
                                                            @else
                                                                Terlewat {{ abs($daysLeft) }} hari
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>
                                                <h6 class="notif-card-title text-dark fw-bold">
                                                    Periode {{ $periodItem ? $periodItem->period_title : 'Audit Tools Berkala' }}
                                                </h6>
                                                <p class="notif-card-desc mb-0">
                                                    @if ($isRejected)
                                                        <span class="text-danger fw-semibold">Audit ditolak:</span> {{ \Illuminate\Support\Str::limit($auditItem->catatan_admin ?? 'Silakan periksa catatan dan perbaiki data audit.', 60) }}
                                                    @else
                                                        Audit tools wajib diisi sebelum {{ $dueDate ? $dueDate->translatedFormat('d M Y') : 'batas waktu' }}. Klik untuk mulai.
                                                    @endif
                                                </p>
                                            </div>
                                            <span class="notif-unread-dot dot-warning"></span>
                                        </div>
                                    </a>
                                @elseif ($notif['type'] === 'tool_transfer_incoming')
                                    @php
                                        $trfItem = $notif['item'];
                                        $trfAsset = $trfItem->fixedAsset;
                                        $trfMaster = $trfAsset?->toolsMaster;
                                        $trfDate = \Carbon\Carbon::parse($trfItem->requested_at);
                                    @endphp
                                    <a href="{{ route('tool-audit.index') }}#tab-tools"
                                        class="notif-card notif-card-comment notif-card-unread"
                                        style="border-left: 3px solid #ffab00 !important; background: rgba(255, 171, 0, 0.08) !important;">
                                        <div class="notif-card-inner">
                                            <div class="notif-card-avatar d-flex align-items-center justify-content-center bg-label-warning rounded-3" style="width: 40px; height: 40px; min-width: 40px;">
                                                <i class="mdi mdi-account-switch fs-4 text-warning"></i>
                                            </div>
                                            <div class="notif-card-content">
                                                <div class="notif-card-meta">
                                                    <span class="badge bg-label-warning notif-badge-pill">
                                                        <i class="mdi mdi-account-arrow-right me-1"></i> Transfer Masuk
                                                    </span>
                                                    <span class="notif-time-ago">
                                                        <i class="mdi mdi-clock-outline fs-7"></i> {{ $trfDate->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <h6 class="notif-card-title text-dark fw-bold">
                                                    {{ $trfMaster?->nama_tools ?? ($trfAsset?->desc ?? 'Alat Kerja') }}
                                                </h6>
                                                <p class="notif-card-desc mb-0">
                                                    <span class="fw-semibold text-primary">{{ $trfItem->fromUser?->name }}</span> ingin mentransfer alat ini kepada Anda. Klik untuk meninjau.
                                                </p>
                                            </div>
                                            <span class="notif-unread-dot dot-warning"></span>
                                        </div>
                                    </a>
                                @elseif ($notif['type'] === 'pending_sick_leave')
                                    @php
                                        $sickItem = $notif['item'];
                                        $sickDate = \Carbon\Carbon::parse($sickItem->start_date);
                                    @endphp
                                    <a href="javascript:void(0);"
                                        class="notif-card notif-card-comment notif-card-unread"
                                        style="border-left: 3px solid #ef4444 !important; background: rgba(239, 68, 68, 0.08) !important;"
                                        data-bs-toggle="modal" data-bs-target="#navModalUploadLateSick-{{ $sickItem->id }}">
                                        <div class="notif-card-inner">
                                            <div class="notif-card-avatar d-flex align-items-center justify-content-center bg-label-danger rounded-3" style="width: 40px; height: 40px; min-width: 40px;">
                                                <i class="mdi mdi-hospital-box-outline fs-4 text-danger"></i>
                                            </div>
                                            <div class="notif-card-content">
                                                <div class="notif-card-meta">
                                                    <span class="badge bg-label-danger notif-badge-pill">
                                                        <i class="mdi mdi-alert-circle-outline me-1"></i> Surat Dokter Diperlukan
                                                    </span>
                                                    <span class="notif-time-ago">
                                                        <i class="mdi mdi-calendar-outline fs-7"></i> {{ $sickDate->translatedFormat('d M Y') }}
                                                    </span>
                                                </div>
                                                <h6 class="notif-card-title text-dark fw-bold">
                                                    Surat Dokter Belum Diunggah
                                                </h6>
                                                <p class="notif-card-desc mb-0">
                                                    Pengajuan sakit Anda ({{ $sickDate->translatedFormat('d M Y') }}) belum dilampiri surat dokter. Klik di sini untuk mengunggah sekarang.
                                                </p>
                                            </div>
                                            <span class="notif-unread-dot dot-danger"></span>
                                        </div>
                                    </a>
                                @elseif ($notif['type'] === 'pending_reimbursement')
                                    @php
                                        $claimItem = $notif['item'];
                                        $cDate = \Carbon\Carbon::parse($claimItem->event_date);
                                        $empName = $claimItem->employee?->user?->name ?? ($claimItem->employee?->nik ? 'Karyawan ' . $claimItem->employee->nik : 'Karyawan');
                                    @endphp
                                    <a href="{{ route('hr.reimbursements.index') }}"
                                        class="notif-card notif-card-comment notif-card-unread"
                                        style="border-left: 3px solid #f59e0b !important; background: rgba(245, 158, 11, 0.08) !important;">
                                        <div class="notif-card-inner">
                                            <div class="notif-card-avatar d-flex align-items-center justify-content-center bg-label-warning rounded-3" style="width: 40px; height: 40px; min-width: 40px;">
                                                <i class="mdi mdi-receipt-text-clock-outline fs-4 text-warning"></i>
                                            </div>
                                            <div class="notif-card-content">
                                                <div class="notif-card-meta">
                                                    <span class="badge bg-label-warning notif-badge-pill">
                                                        <i class="mdi mdi-cash-fast me-1"></i> Klaim: {{ $claimItem->claim_type }}
                                                    </span>
                                                    <span class="notif-time-ago">
                                                        <i class="mdi mdi-clock-outline fs-7"></i> {{ \Carbon\Carbon::parse($claimItem->created_at)->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <h6 class="notif-card-title text-dark fw-bold">
                                                    Rp {{ number_format($claimItem->amount, 0, ',', '.') }} &bull; {{ $empName }}
                                                </h6>
                                                <p class="notif-card-desc mb-0">
                                                    {{ $claimItem->description ?: 'Pengajuan klaim operasional baru menunggu verifikasi/pencairan Finance.' }}
                                                </p>
                                            </div>
                                            <span class="notif-unread-dot dot-warning"></span>
                                        </div>
                                    </a>
                                @elseif ($notif['type'] === 'pending_leave_request')
                                    @php
                                        $leaveItem = $notif['item'];
                                        $empName = $leaveItem->employee?->user?->name ?? ($leaveItem->employee?->nik ? 'Karyawan ' . $leaveItem->employee->nik : 'Karyawan');
                                        $lType = $leaveItem->leaveType?->name ?? 'Cuti/Izin';
                                    @endphp
                                    <a href="javascript:void(0);"
                                        class="notif-card notif-card-comment notif-card-unread"
                                        style="border-left: 3px solid #06b6d4 !important; background: rgba(6, 182, 212, 0.08) !important;"
                                        data-bs-toggle="modal" data-bs-target="#modalLeaveApprovalAlert">
                                        <div class="notif-card-inner">
                                            <div class="notif-card-avatar d-flex align-items-center justify-content-center bg-label-info rounded-3" style="width: 40px; height: 40px; min-width: 40px;">
                                                <i class="mdi mdi-calendar-clock-outline fs-4 text-info"></i>
                                            </div>
                                            <div class="notif-card-content">
                                                <div class="notif-card-meta">
                                                    <span class="badge bg-label-info notif-badge-pill">
                                                        <i class="mdi mdi-calendar-account-outline me-1"></i> {{ $lType }} ({{ $leaveItem->total_days }} Hari)
                                                    </span>
                                                    <span class="notif-time-ago">
                                                        <i class="mdi mdi-clock-outline fs-7"></i> {{ \Carbon\Carbon::parse($leaveItem->created_at)->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <h6 class="notif-card-title text-dark fw-bold">
                                                    {{ $empName }} &bull; {{ \Carbon\Carbon::parse($leaveItem->start_date)->format('d M') }} s/d {{ \Carbon\Carbon::parse($leaveItem->end_date)->format('d M Y') }}
                                                </h6>
                                                <p class="notif-card-desc mb-0">
                                                    {{ $leaveItem->reason ?: 'Permohonan izin/cuti karyawan menunggu persetujuan Anda.' }}
                                                </p>
                                            </div>
                                            <span class="notif-unread-dot" style="background: #06b6d4;"></span>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        {{-- Empty State Placeholder --}}
                        <div id="emptyNotifAlert" class="text-center text-muted py-5 {{ ($unifiedNotifications->count() > 0) ? 'd-none' : '' }}">
                            <div class="avatar avatar-md mx-auto mb-2 bg-label-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                                <i class="mdi mdi-bell-check-outline fs-3 text-secondary"></i>
                            </div>
                            <h6 class="mb-1 text-secondary fw-semibold">Tidak ada notifikasi baru</h6>
                            <small class="text-muted">Semua notifikasi penting sudah Anda lihat</small>
                        </div>
                    </li>

                    <li class="dropdown-menu-footer border-top p-2 bg-light">
                        <a href="{{ route('index.notif') }}" class="btn btn-sm btn-primary w-100 d-flex align-items-center justify-content-center gap-1 py-2">
                            <span>Lihat Semua Notifikasi</span>
                            <i class="mdi mdi-arrow-right fs-6"></i>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ Notification -->

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ url('') . '/' . Auth::user()?->image }}" alt
                            class="w-px-40 h-auto rounded-circle" />
                    </div>
                </a>
                @if (Auth::user())

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ in_array(Auth::user()?->role, ['Client', 'Client Vendor']) ? route('profile.edit', Auth::user()?->id) : route('profile.show', Auth::user()?->id) }}">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="{{ url('') . '/' . Auth::user()?->image }}" alt
                                                class="w-px-40 h-auto rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block">{{ Auth::user()?->name }}</span>
                                        <small class="text-muted">{{ Auth::user()?->role }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        @if (!in_array(Auth::user()?->role, ['Client', 'Client Vendor']))
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.show', Auth::user()?->id) }}">
                                    <i class="mdi mdi-account-circle-outline me-2"></i>
                                    <span class="align-middle">My Portal</span>
                                </a>
                            </li>
                        @endif
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit', Auth::user()?->id) }}">
                                <i class="mdi mdi-cog-outline me-2"></i>
                                <span class="align-middle">Settings</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();"
                                target="_blank">
                                <i class="mdi mdi-logout me-2"></i>
                                <span class="align-middle">Log Out</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                @endif
            </li>
            <!--/ User -->
        </ul>
    </div>

    <!-- Search Small Screens -->
    <div class="navbar-search-wrapper search-input-wrapper d-none">
        <input type="text" class="form-control search-input container-fluid border-0" placeholder="Search..."
            aria-label="Search..." />
        <i class="mdi mdi-close search-toggler cursor-pointer"></i>
    </div>
</nav>

<!-- Navbar Dropdown Blur Backdrop Overlay (Quick Action, Kanban, Notifikasi, My Account) -->
<div id="navbarDropdownBackdrop" class="navbar-dropdown-backdrop"></div>

<style>
    .navbar-dropdown-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        background-color: rgba(15, 23, 42, 0.42);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        z-index: 1065;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        overflow: hidden;
        contain: strict;
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }

    .navbar-dropdown-backdrop.show {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    #layout-navbar.navbar-active-dropdown {
        z-index: 1070 !important;
    }

    #layout-navbar .dropdown.show .dropdown-menu,
    #layout-navbar .dropdown-menu.show {
        z-index: 1075 !important;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.22) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var navbar = document.getElementById('layout-navbar');
        var backdrop = document.getElementById('navbarDropdownBackdrop');
        if (!navbar || !backdrop) return;

        // Pindahkan backdrop ke akhir body agar menutupi seluruh halaman tanpa terpotong overflow
        if (backdrop.parentNode !== document.body) {
            document.body.appendChild(backdrop);
        }

        var targetDropdowns = navbar.querySelectorAll('.navbar-nav > .dropdown');

        targetDropdowns.forEach(function (dd) {
            dd.addEventListener('show.bs.dropdown', function () {
                navbar.classList.add('navbar-active-dropdown');
                backdrop.classList.add('show');
            });

            dd.addEventListener('hidden.bs.dropdown', function () {
                setTimeout(function () {
                    var anyOpen = navbar.querySelector('.navbar-nav > .dropdown.show, .navbar-nav > .dropdown .dropdown-menu.show');
                    if (!anyOpen) {
                        backdrop.classList.remove('show');
                        navbar.classList.remove('navbar-active-dropdown');
                    }
                }, 40);
            });
        });

        // Klik pada backdrop untuk menutup dropdown aktif
        backdrop.addEventListener('click', function () {
            targetDropdowns.forEach(function (dd) {
                var toggleBtn = dd.querySelector('[data-bs-toggle="dropdown"]');
                if (toggleBtn && typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                    var instance = bootstrap.Dropdown.getInstance(toggleBtn);
                    if (instance) {
                        instance.hide();
                    }
                }
            });
            backdrop.classList.remove('show');
            navbar.classList.remove('navbar-active-dropdown');
        });

        // Handler tombol "Tandai Semua Dibaca"
        var markAllBtn = document.getElementById('btnMarkAllNotifRead');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var origHtml = markAllBtn.innerHTML;
                markAllBtn.disabled = true;
                markAllBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin fs-6"></i> Memproses...';

                fetch('{{ route('notifications.mark_all_read') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    markAllBtn.innerHTML = '<i class="mdi mdi-check text-success fs-6"></i> Selesai!';

                    // 1. Sembunyikan titik lonceng merah
                    var bellDot = document.getElementById('navbarBellDot');
                    if (bellDot) bellDot.classList.add('d-none');

                    // 2. Sembunyikan badge total & kategori
                    var totalBadge = document.getElementById('notifTotalBadge');
                    if (totalBadge) totalBadge.classList.add('d-none');

                    var prospectCreatedBadge = document.getElementById('prospectCreatedCountBadge');
                    if (prospectCreatedBadge) prospectCreatedBadge.classList.add('d-none');

                    var prospectAssignedBadge = document.getElementById('prospectAssignedCountBadge');
                    if (prospectAssignedBadge) prospectAssignedBadge.classList.add('d-none');

                    var paymentBadge = document.getElementById('paymentNotifCountBadge');
                    if (paymentBadge) paymentBadge.classList.add('d-none');

                    var commentBadge = document.getElementById('commentUnreadCountBadge');
                    if (commentBadge) commentBadge.classList.add('d-none');

                    var kanbanBadge = document.getElementById('kanbanMentionCountBadge');
                    if (kanbanBadge) kanbanBadge.classList.add('d-none');

                    // 3. Ubah semua kartu notifikasi unread menjadi read
                    document.querySelectorAll('.notif-card').forEach(function (card) {
                        card.classList.add('notif-card-read');
                        card.classList.remove('payment-notif-unread', 'notif-card-unread');
                        card.setAttribute('data-read', '1');
                        var dot = card.querySelector('.notif-unread-dot');
                        if (dot) dot.remove();
                    });

                    // 4. Tutup semua floating toast & modal alert prospect yang sedang muncul di layar
                    document.querySelectorAll('.prospect-floating-toast, .invoice-floating-toast').forEach(function (t) {
                        t.classList.add('toast-hiding');
                        setTimeout(function () { t.remove(); }, 300);
                    });
                    var prospectModalEl = document.getElementById('prospectUrgentModal');
                    if (prospectModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var pm = bootstrap.Modal.getInstance(prospectModalEl);
                        if (pm) pm.hide();
                    }

                    setTimeout(function () {
                        markAllBtn.disabled = false;
                        markAllBtn.innerHTML = '<i class="mdi mdi-check-all fs-6"></i> <span>Tandai Semua Dibaca</span>';
                    }, 1800);
                })
                .catch(function (err) {
                    console.error('Error marking all as read:', err);
                    markAllBtn.disabled = false;
                    markAllBtn.innerHTML = origHtml;
                });
            });
        }
    });
</script>

{{-- Modal Presensi Cepat (Clock In & Clock Out) dari Navbar --}}
@if (Auth::user() && (Auth::user()->employee || in_array(Auth::user()->role, ['Super Admin', 'HRD', 'admin', 'Management'])))
    @php
        $navModalEmp = Auth::user()->employee ?? (object)[
            'id' => 0,
            'nik' => 'HR-ADMIN',
            'position' => (object)['name' => Auth::user()->role ?? 'HR Administrator'],
            'department' => null,
            'can_online_attendance' => true
        ];
        $navModalAtt = ($navModalEmp && isset($navModalEmp->id) && $navModalEmp->id > 0)
            ? \App\Models\Hr\HrAttendance::where('employee_id', $navModalEmp->id)->whereDate('date', \Carbon\Carbon::today('Asia/Jakarta'))->first()
            : null;
        
        $navSelfieSetting = \Illuminate\Support\Facades\DB::table('hr_attendance_settings')->where('key', 'is_selfie_required')->first();
        $navIsSelfieRequired = $navSelfieSetting && $navSelfieSetting->value === '1';

        $navWifiSetting = \Illuminate\Support\Facades\DB::table('hr_attendance_settings')->where('key', 'is_wifi_restriction_enabled')->first();
        $navIsWifiRestrictionEnabled = $navWifiSetting && $navWifiSetting->value === '1';
        $navActiveWifis = \App\Models\Hr\HrOfficeWifi::where('is_active', true)->get();
        $navAllowedIps = $navActiveWifis->pluck('ip_address')->toArray();
        $navClientIp = request()->ip();

        $navIsWifiVerified = !$navIsWifiRestrictionEnabled
            || in_array($navClientIp, $navAllowedIps)
            || (app()->isLocal() && in_array($navClientIp, ['127.0.0.1', '::1']));

        $navEarliestSetting = \Illuminate\Support\Facades\DB::table('hr_attendance_settings')->where('key', 'earliest_clock_in_time')->first();
        $navEarliestTime = $navEarliestSetting ? ($navEarliestSetting->value ?? '07:00') : '07:00';
        $navNowTimeJakarta = \Carbon\Carbon::now('Asia/Jakarta')->format('H:i');
        $navIsBeforeEarliest = $navNowTimeJakarta < $navEarliestTime;
    @endphp

    <!-- Modal Clock In (Prospect / Jam Pulang Style) -->
    <style>
        #navClockInModal {
            z-index: 1095 !important;
        }
        #navClockInModal .modal-dialog {
            max-width: 550px;
            margin: 1.75rem auto;
        }
        #navClockInModal .modal-content {
            border-radius: 20px !important;
            border: 0 !important;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.28) !important;
            overflow: hidden;
            background: #ffffff;
        }
        html.dark-style #navClockInModal .modal-content {
            background: #2b2c40 !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7) !important;
            color: #e4e6f0;
        }
        #navClockInModal .clockin-top-stripe {
            height: 5px;
            background: linear-gradient(90deg, #10b981 0%, #06b6d4 50%, #3b82f6 100%);
            background-size: 200% 100%;
            animation: clockoutShimmer 3s ease-in-out infinite;
            width: 100%;
        }
        .clockin-hero-halo {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.22) 0%, rgba(16, 185, 129, 0.04) 70%, transparent 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .clockin-hero-inner {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
        }
        #navClockInModal .clockin-detail-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
        }
        html.dark-style #navClockInModal .clockin-detail-card {
            background: #32344d;
            border-color: #3f4262;
        }
        #navClockInModal .clockin-card-header {
            background: #ffffff;
            border-bottom: 1px solid #edf0f5;
            padding: 11px 16px;
        }
        html.dark-style #navClockInModal .clockin-card-header {
            background: #2b2c40;
            border-bottom-color: #3f4262;
        }
        #navClockInModal .clockin-card-body {
            padding: 12px 16px;
        }
        #navClockInModal .clockin-info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 0;
            border-bottom: 1px dashed #e2e8f0;
        }
        html.dark-style #navClockInModal .clockin-info-row {
            border-bottom-color: #434665;
        }
        #navClockInModal .clockin-info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        #navClockInModal .clockin-info-label {
            font-size: 0.815rem;
            color: #64748b;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        html.dark-style #navClockInModal .clockin-info-label {
            color: #a1a4b8;
        }
        #navClockInModal .clockin-info-val {
            font-size: 0.865rem;
            font-weight: 600;
            color: #1e293b;
            text-align: right;
            max-width: 68%;
        }
        html.dark-style #navClockInModal .clockin-info-val {
            color: #f1f5f9;
        }
        #navClockInModal .clockin-guide-card {
            background: #ecfdf5;
            border: 1px solid #d1fae5;
            border-radius: 12px;
            padding: 10px 14px;
        }
        html.dark-style #navClockInModal .clockin-guide-card {
            background: #064e3b;
            border-color: #047857;
        }
        #navClockInModal .btn-action-clockin {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 10px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.38);
            transition: all 0.2s ease;
        }
        #navClockInModal .btn-action-clockin:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.48);
            transform: translateY(-1px);
        }
        #navClockInModal .btn-dismiss-clockin {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 10px 18px;
            border-radius: 12px;
            transition: all 0.15s ease;
        }
        #navClockInModal .btn-dismiss-clockin:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        html.dark-style #navClockInModal .btn-dismiss-clockin {
            background: #334155;
            color: #e2e8f0;
            border-color: #475569;
        }
        html.dark-style #navClockInModal .btn-dismiss-clockin:hover {
            background: #475569;
            color: #ffffff;
        }
    </style>

    <div class="modal fade" id="navClockInModal" tabindex="-1" aria-labelledby="navClockInModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <!-- Top Accent Gradient Line -->
                <div class="clockin-top-stripe"></div>

                <form action="{{ route('hr.portal.clockin') }}" method="POST" id="navClockInForm">
                    @csrf
                    <input type="hidden" name="device_id" id="navClockInDeviceId">
                    <input type="hidden" name="device_info" id="navClockInDeviceInfo">
                    <input type="hidden" name="selfie_image" id="navClockInSelfieImage">

                    <div class="modal-body p-4 pt-3">
                        <!-- Top Status Badge & Close Button -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge rounded-pill px-3 py-1 text-uppercase fw-bold" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="mdi mdi-clock-start me-1"></i> PRESENSI MASUK &bull; {{ $navEarliestTime }} - {{ $workStartTime ?? '08:00' }} WIB
                            </span>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- Hero Icon Halo & Centered Titles -->
                        <div class="text-center mb-3">
                            <div class="clockin-hero-halo mx-auto mb-2">
                                <div class="clockin-hero-inner">
                                    <i class="mdi mdi-clock-in fs-2"></i>
                                </div>
                            </div>

                            <h4 class="modal-title fw-bold text-dark mb-1" id="navClockInModalLabel" style="letter-spacing: -0.3px;">
                                Presensi Masuk (Clock In)
                            </h4>
                            <p class="text-muted small mb-0 px-2" style="line-height: 1.55;">
                                Silakan catat kehadiran kerja Anda hari ini. Selamat beraktivitas &amp; semoga hari Anda produktif!
                            </p>
                        </div>

                        {{-- Alert Banner Validasi Keamanan (Earliest Time, WiFi & Device Lock) --}}
                        <div id="navClockInAlertsContainer">
                            {{-- Earliest Clock-In Time Alert --}}
                            <div class="alert alert-warning d-flex align-items-start text-start font-12 py-2 px-3 mb-3 shadow-xs {{ $navIsBeforeEarliest ? '' : 'd-none' }}" id="navClockInEarlyAlert">
                                <i class="mdi mdi-clock-alert-outline fs-4 me-2 text-warning flex-shrink-0 mt-n1"></i>
                                <div>
                                    <div class="fw-bold text-dark">Presensi Masuk Belum Dibuka</div>
                                    <div class="text-dark small mt-1" id="navClockInEarlyAlertText">
                                        Presensi masuk baru dibuka mulai pukul <strong class="text-primary font-monospace">{{ $navEarliestTime }} WIB</strong>.
                                    </div>
                                    <div class="text-muted font-11 mt-1">
                                        <i class="mdi mdi-information-outline me-1"></i>Silakan lakukan presensi setelah jam operasional dibuka.
                                    </div>
                                </div>
                            </div>

                            {{-- WiFi Alert --}}
                            @if ($navIsWifiRestrictionEnabled && !$navIsWifiVerified)
                                <div class="alert alert-danger d-flex align-items-start text-start font-12 py-2 px-3 mb-3 shadow-xs" id="navClockInWifiAlert">
                                    <i class="mdi mdi-wifi-alert fs-4 me-2 text-danger flex-shrink-0 mt-n1"></i>
                                    <div>
                                        <div class="fw-bold text-danger">Presensi Tidak Diizinkan (WiFi Tidak Sesuai)</div>
                                        <div class="text-danger small mt-1">
                                            IP Anda terdeteksi: <span class="font-monospace fw-bold bg-white text-dark px-1.5 py-0.5 rounded border border-danger-subtle">{{ $navClientIp }}</span>. Anda belum terhubung ke jaringan WiFi Kantor yang diizinkan.
                                        </div>
                                        <div class="text-muted font-11 mt-1">
                                            <i class="mdi mdi-information-outline me-1"></i>Silakan sambungkan perangkat ke WiFi kantor untuk mengaktifkan tombol Clock In.
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-danger d-flex align-items-start text-start font-12 py-2 px-3 mb-3 shadow-xs d-none" id="navClockInWifiAlert">
                                    <i class="mdi mdi-wifi-alert fs-4 me-2 text-danger flex-shrink-0 mt-n1"></i>
                                    <div>
                                        <div class="fw-bold text-danger">Presensi Tidak Diizinkan (WiFi Tidak Sesuai)</div>
                                        <div class="text-danger small mt-1" id="navClockInWifiAlertText">
                                            Anda tidak terhubung ke jaringan WiFi Kantor yang diizinkan.
                                        </div>
                                        <div class="text-muted font-11 mt-1">
                                            <i class="mdi mdi-information-outline me-1"></i>Silakan sambungkan perangkat ke WiFi kantor.
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Device Lock Alert --}}
                            <div class="alert alert-danger d-flex align-items-start text-start font-12 py-2 px-3 mb-3 shadow-xs d-none" id="navClockInDeviceAlert">
                                <i class="mdi mdi-cellphone-lock fs-4 me-2 text-danger flex-shrink-0 mt-n1"></i>
                                <div>
                                    <div class="fw-bold text-danger">Presensi Tidak Diizinkan (Perangkat Terkunci)</div>
                                    <div class="text-danger small mt-1" id="navClockInDeviceAlertText"></div>
                                    <div class="text-muted font-11 mt-1">
                                        <i class="mdi mdi-shield-alert-outline me-1"></i>Kebijakan Anti-Titip Absen: 1 HP/Laptop hanya dapat digunakan 1 akun per hari.
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($navIsSelfieRequired)
                            {{-- Live Selfie Camera Container --}}
                            <div class="position-relative rounded-3 overflow-hidden mb-3 bg-dark shadow-sm border border-success" style="height: 200px;">
                                <video id="navCameraVideo" autoplay playsinline muted style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
                                <canvas id="navCameraCanvas" style="display: none;"></canvas>
                                
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-success shadow-xs d-flex align-items-center gap-1">
                                        <span class="spinner-grow spinner-grow-sm text-white" role="status" style="width: 8px; height: 8px;"></span>
                                        <i class="mdi mdi-camera-iris"></i> Kamera Live
                                    </span>
                                </div>

                                <div id="navCameraLoadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark bg-opacity-75 text-white">
                                    <div class="spinner-border text-success mb-2" role="status"></div>
                                    <span class="small">Mengaktifkan kamera selfie...</span>
                                </div>

                                <div id="navCameraErrorAlert" class="position-absolute bottom-0 start-0 w-100 p-2 bg-danger bg-opacity-90 text-white font-11 d-none text-center">
                                    <i class="mdi mdi-alert-circle me-1"></i> Izin kamera diperlukan untuk presensi!
                                </div>
                            </div>
                        @endif

                        <!-- Card 1: Structured Detail Card -->
                        <div class="clockin-detail-card mb-3">
                            <div class="clockin-card-header d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2 text-truncate pe-2">
                                    @if (Auth::user()->image && file_exists(public_path(Auth::user()->image)))
                                        <img src="{{ asset(Auth::user()->image) }}" class="rounded-circle flex-shrink-0" style="width: 28px; height: 28px; object-fit: cover;" alt="{{ Auth::user()->name }}">
                                    @else
                                        <span class="badge rounded-circle p-2 bg-label-success flex-shrink-0" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="mdi mdi-account-circle-outline fs-6"></i>
                                        </span>
                                    @endif
                                    <span class="fw-bold text-dark text-truncate" style="font-size: 0.9rem;" title="{{ Auth::user()->name }}">
                                        {{ Auth::user()->name }}
                                    </span>
                                </div>
                                <span class="text-muted flex-shrink-0" style="font-size: 0.76rem;">
                                    <i class="mdi mdi-calendar-today me-1"></i>{{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('D MMM Y') }}
                                </span>
                            </div>
                            <div class="clockin-card-body">
                                <div class="clockin-info-row">
                                    <span class="clockin-info-label">
                                        <i class="mdi mdi-clock-outline text-success fs-6"></i> Waktu Saat Ini
                                    </span>
                                    <span class="clockin-info-val">
                                        <span class="font-monospace fw-bold text-success" style="font-size: 0.95rem;">
                                            <span id="navLiveClockTimeIn">{{ \Carbon\Carbon::now()->format('H:i:s') }}</span> WIB
                                        </span>
                                    </span>
                                </div>
                                <div class="clockin-info-row">
                                    <span class="clockin-info-label">
                                        <i class="mdi mdi-badge-account-horizontal-outline text-primary fs-6"></i> Jabatan / Divisi
                                    </span>
                                    <span class="clockin-info-val text-truncate">
                                        {{ $navModalEmp->position->name ?? ($navModalEmp->department->name ?? 'Staff') }} ({{ $navModalEmp->nik ?? '-' }})
                                    </span>
                                </div>
                                <div class="clockin-info-row">
                                    <span class="clockin-info-label">
                                        <i class="mdi mdi-office-building-marker-outline text-info fs-6"></i> Tipe Kehadiran
                                    </span>
                                    <span class="clockin-info-val">
                                        <div class="btn-group btn-group-sm shadow-2xs" role="group" aria-label="Tipe Kehadiran">
                                            <input type="radio" class="btn-check" name="work_type" id="navWorkTypeWfo" value="WFO" checked autocomplete="off">
                                            <label class="btn btn-outline-primary py-1 px-2.5 font-11 fw-semibold" for="navWorkTypeWfo">WFO</label>

                                            <input type="radio" class="btn-check" name="work_type" id="navWorkTypeWfh" value="WFH" autocomplete="off">
                                            <label class="btn btn-outline-primary py-1 px-2.5 font-11 fw-semibold" for="navWorkTypeWfh">WFH</label>
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Guidance Notice -->
                        <div class="clockin-guide-card d-flex align-items-center gap-2 mb-3">
                            <i class="mdi mdi-shield-check text-success fs-5 flex-shrink-0"></i>
                            <div class="small text-dark" style="font-size: 0.8rem; line-height: 1.45;">
                                @if ($navIsSelfieRequired)
                                    Posisikan wajah Anda tepat di depan kamera. Foto akan diverifikasi secara otomatis saat menekan tombol konfirmasi.
                                @else
                                    Presensi terproteksi keamanan IP WiFi Kantor, Anti-Titip Absen Device Lock, dan verifikasi kehadiran akurat.
                                @endif
                            </div>
                        </div>

                        <!-- Actions Footer -->
                        <!-- Actions Footer -->
                        <div class="d-flex align-items-center gap-2 pt-1">
                            <button type="button" class="btn btn-dismiss-clockin" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-action-clockin flex-grow-1 d-flex align-items-center justify-content-center gap-1.5" id="btnSubmitNavClockIn"
                                    {{ ($navIsBeforeEarliest || ($navIsWifiRestrictionEnabled && !$navIsWifiVerified)) ? 'disabled title="Presensi belum dibuka atau WiFi tidak sesuai"' : '' }}>
                                <i class="mdi mdi-clock-in fs-5"></i>
                                <span>Konfirmasi Clock In</span>
                            </button>
                        </div>

                        <!-- Quick Link: Leave / Sick / Permission / Visit Customer Request -->
                        <div class="text-center pt-3 border-top mt-3">
                            <a href="javascript:void(0);" class="text-muted font-11 text-decoration-none d-inline-flex align-items-center gap-1" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#navLeaveRequestModal">
                                <i class="mdi mdi-map-marker-account-outline text-primary fs-6"></i>
                                <span>Berhalangan hadir / tugas luar? <strong class="text-primary text-decoration-underline">Ajukan Izin / Sakit / Visit Customer</strong></span>
                            </a>
                        </div>

                        <!-- Testing & Simulation Playground Tools -->
                        <div class="mt-3 pt-2 border-top bg-light p-2.5 rounded-3 border">
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <span class="font-10 text-uppercase fw-bold text-muted">
                                    <i class="mdi mdi-flask-outline text-primary me-1"></i>Test Simulasi Respons Rules:
                                </span>
                                <span class="badge bg-label-secondary font-10">Uji Coba</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2.5 py-1 font-11 flex-grow-1 shadow-xs fw-semibold" onclick="testSimulateClockIn('on_time')">
                                    <i class="mdi mdi-check-circle-outline me-0.5"></i> Test Tepat Waktu (&lt; 08:00)
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-warning rounded-pill px-2.5 py-1 font-11 flex-grow-1 shadow-xs fw-semibold" onclick="testSimulateClockIn('late')">
                                    <i class="mdi mdi-clock-alert-outline me-0.5"></i> Test Terlambat (&gt; 08:00)
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Pengajuan Sakit, Izin & Visit Customer -->
    <div class="modal fade" id="navLeaveRequestModal" tabindex="-1" aria-labelledby="navLeaveRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div style="height: 4px; background: linear-gradient(90deg, #f59e0b 0%, #ef4444 50%, #3b82f6 100%); width: 100%;"></div>
                <form action="{{ route('hr.portal.leave.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header py-3 px-4 bg-white border-bottom">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="avatar avatar-sm bg-warning-subtle text-warning rounded-3 d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-clipboard-text-clock-outline fs-5 text-warning"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark font-15 mb-0" id="navLeaveRequestModalLabel">Form Pengajuan Sakit, Izin &amp; Visit Customer</h5>
                                <small class="text-muted font-11">Bebas denda presensi setelah diverifikasi oleh HR</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        @php
                            $modalLeaveTypes = \App\Models\HrLeaveType::where('is_active', true)
                                ->where(function($q) {
                                    $q->whereIn('code', ['SK', 'IZ', 'VC', 'DL'])
                                      ->orWhere('name', 'LIKE', '%Sakit%')
                                      ->orWhere('name', 'LIKE', '%Izin%')
                                      ->orWhere('name', 'LIKE', '%Visit%')
                                      ->orWhere('name', 'LIKE', '%Dinas%');
                                })
                                ->orderBy('id')
                                ->get();
                        @endphp

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label font-11 fw-bold text-dark mb-1">Jenis Pengajuan <span class="text-danger">*</span></label>
                                <select name="leave_type_id" class="form-select font-13 rounded-3" required id="navLeaveTypeSelect" onchange="toggleAttachmentRequirement(this)">
                                    @foreach ($modalLeaveTypes as $lType)
                                        @php
                                            $icon = match($lType->code) {
                                                'SK' => '💊',
                                                'VC' => '🚗',
                                                'DL' => '🏢',
                                                default => '📝',
                                            };
                                        @endphp
                                        <option value="{{ $lType->id }}" data-require-attachment="{{ $lType->requires_attachment ? '1' : '0' }}" data-code="{{ $lType->code }}">
                                            @if($lType->code === 'SK')
                                                {{ $icon }} {{ $lType->name }} (Wajib Lampirkan Surat Dokter)
                                            @elseif($lType->code === 'VC' || str_contains(strtolower($lType->name), 'visit'))
                                                🚗 {{ $lType->name }} (Kunjungan Klien / Lapangan)
                                            @else
                                                {{ $icon }} {{ $lType->name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6">
                                <label class="form-label font-11 fw-bold text-dark mb-1">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control font-13 rounded-3" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-6">
                                <label class="form-label font-11 fw-bold text-dark mb-1">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control font-13 rounded-3" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label font-11 fw-bold text-dark mb-1">Alasan / Keterangan Keperluan <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control font-13 rounded-3" rows="3" placeholder="Jelaskan alasan izin / kondisi sakit / agenda visit customer yang dilakukan..." required></textarea>
                            </div>

                            <div class="col-12" id="navLeaveAttachmentContainer">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label font-11 fw-bold text-dark mb-0">
                                        Lampiran Surat Dokter / Bukti Pendukung
                                    </label>
                                    <span class="badge bg-warning-subtle text-warning font-10 rounded-pill px-2 py-0.5">
                                        <i class="mdi mdi-clock-outline me-0.5"></i> Bisa Upload Menyusul
                                    </span>
                                </div>
                                <input type="file" name="attachment" id="navLeaveAttachmentInput" class="form-control font-12 rounded-3" accept=".jpg,.jpeg,.png,.pdf,.webp">
                                <div class="form-text font-10 text-muted mt-1">
                                    <i class="mdi mdi-information-outline text-primary me-0.5"></i> Jika belum periksa ke klinik/RS saat ini, surat dokter <strong>dapat diunggah menyusul</strong> setelah Anda selesai berobat.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light py-2.5 px-4 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-label-secondary font-12 rounded-3 px-3 border-0" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary font-12 fw-bold rounded-3 px-4 shadow-xs border-0">
                            <i class="mdi mdi-send-check me-1"></i> Kirim Pengajuan ke HR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Feedback Clock In: Tepat Waktu (< 08:00 WIB) dengan Loading Animation & Ucapan Terima Kasih --}}
    <div class="modal fade" id="modalClockInFeedbackOnTime" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden text-center position-relative">
                <div style="height: 5px; background: linear-gradient(90deg, #10b981 0%, #06b6d4 100%);"></div>
                
                <div class="modal-body p-4 pt-4 pb-3">
                    {{-- Animated Success Halo Icon --}}
                    <div class="mb-3 position-relative d-inline-block">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 88px; height: 88px; background: rgba(16, 185, 129, 0.12); box-shadow: 0 0 0 12px rgba(16, 185, 129, 0.06); animation: pulseGlow 2s infinite;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 62px; height: 62px; background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 8px 16px rgba(16, 185, 129, 0.35);">
                                <i class="mdi mdi-check-bold text-white fs-1 animate__animated animate__zoomIn"></i>
                            </div>
                        </div>
                    </div>

                    <h4 class="fw-bolder text-dark mb-1 font-18" id="feedbackOnTimeTitle">Presensi Berhasil Dicatat!</h4>
                    <p class="text-success fw-bold font-14 mb-2">
                        🎉 Terima kasih sudah hadir tepat waktu!
                    </p>
                    <p class="text-muted font-12 mb-3 px-2" style="line-height: 1.5;">
                        Selamat beraktivitas! Semoga hari kerja Anda menyenangkan, produktif, dan penuh keberkahan. 🌟
                    </p>

                    {{-- Summary Box --}}
                    <div class="bg-label-success rounded-3 p-3 mb-3 border border-success border-opacity-25 text-start">
                        <div class="row g-2 font-12">
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Waktu Presensi</span>
                                <strong class="text-dark font-13 font-monospace" id="feedbackOnTimeClock">07:55 WIB</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span class="text-muted d-block font-11">Status Kehadiran</span>
                                <span class="badge bg-success rounded-pill font-11" id="feedbackOnTimeStatus">Tepat Waktu</span>
                            </div>
                            <div class="col-6 pt-2 border-top border-success border-opacity-25">
                                <span class="text-muted d-block font-11">Tipe Presensi</span>
                                <strong class="text-dark" id="feedbackOnTimeWorkType">WFO (Kantor)</strong>
                            </div>
                            <div class="col-6 pt-2 border-top border-success border-opacity-25 text-end">
                                <span class="text-muted d-block font-11">Potongan Denda</span>
                                <strong class="text-success font-monospace" id="feedbackOnTimePenalty">Rp 0 (Bebas Denda)</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 py-2.5 px-4 d-flex justify-content-center">
                    <button type="button" class="btn btn-success font-13 fw-bold rounded-pill px-5 shadow-sm" data-bs-dismiss="modal">
                        Mulai Bekerja 🚀
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Feedback Clock In: Terlambat (> 08:00 WIB) dengan Notifikasi Detail Frekuensi Keterlambatan --}}
    <div class="modal fade" id="modalClockInFeedbackLate" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 490px;">
            <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden text-center position-relative">
                <div style="height: 5px; background: linear-gradient(90deg, #f59e0b 0%, #ef4444 100%);"></div>
                
                <div class="modal-body p-4 pt-4 pb-3">
                    {{-- Warning Icon Halo --}}
                    <div class="mb-3 position-relative d-inline-block">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 88px; height: 88px; background: rgba(245, 158, 11, 0.14); box-shadow: 0 0 0 12px rgba(245, 158, 11, 0.07);">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 62px; height: 62px; background: linear-gradient(135deg, #f59e0b, #ef4444); box-shadow: 0 8px 16px rgba(239, 68, 68, 0.35);">
                                <i class="mdi mdi-clock-alert-outline text-white fs-1 animate__animated animate__headShake"></i>
                            </div>
                        </div>
                    </div>

                    <h4 class="fw-bolder text-dark mb-1 font-18" id="feedbackLateTitle">Presensi Masuk Tercatat</h4>
                    
                    {{-- Warning Box Highlight --}}
                    <div class="alert alert-warning text-dark border border-warning border-opacity-50 p-3 rounded-3 mb-3 text-start font-12 shadow-xs">
                        <div class="d-flex align-items-start gap-2.5 mb-0">
                            <i class="mdi mdi-alert-circle text-warning fs-4 flex-shrink-0 mt-n1"></i>
                            <div>
                                <span class="fw-bold font-13 d-block text-dark">
                                    Anda terlambat hari ini (<span id="feedbackLateMinutes" class="text-danger font-monospace fw-bolder">15</span> menit).
                                </span>
                                <span class="text-dark font-12 d-block mt-1" id="feedbackLateCountText">
                                    Ini adalah <strong class="text-danger font-monospace">keterlambatan ke-<span id="feedbackLateCount">1</span></strong> Anda di bulan ini.
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Breakdown Summary --}}
                    <div class="card bg-label-secondary border-0 p-3 mb-3 text-start rounded-3">
                        <div class="row g-2 font-12">
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Jam Masuk</span>
                                <strong class="text-dark font-13 font-monospace" id="feedbackLateClock">08:15 WIB</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span class="text-muted d-block font-11">Potongan Denda</span>
                                <strong class="text-danger font-13 font-monospace" id="feedbackLatePenalty">Rp 50.000</strong>
                            </div>
                            <div class="col-12 pt-2 border-top border-secondary border-opacity-25">
                                <span class="text-muted d-block font-11">Keterangan Skema Presensi:</span>
                                <span class="text-dark fw-semibold" id="feedbackLateStatusLabel">Terlambat ke-1 (Tier 1)</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted font-11 mb-2 px-1 text-center" style="line-height: 1.45;">
                        <i class="mdi mdi-information-outline me-0.5 text-primary"></i>
                        Tetap semangat bekerja! Mari tingkatkan ketepatan waktu untuk menjaga performa kerja dan menghindari akumulasi potongan denda payroll di akhir bulan.
                    </p>
                </div>

                <div class="modal-footer bg-light border-0 py-2.5 px-4 d-flex justify-content-center">
                    <button type="button" class="btn btn-primary font-13 fw-bold rounded-pill px-5 shadow-sm" data-bs-dismiss="modal">
                        Saya Mengerti &amp; Mulai Bekerja
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Khusus: Upload Surat Dokter Susulan --}}
    @if (isset($navPendingSickLeave) && $navPendingSickLeave)
    <div class="modal fade" id="navModalUploadLateSick-{{ $navPendingSickLeave->id }}" tabindex="-1" aria-labelledby="navModalUploadLateSickLabel-{{ $navPendingSickLeave->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div style="height: 4px; background: linear-gradient(90deg, #ef4444 0%, #f59e0b 100%); width: 100%;"></div>
                <form action="{{ route('hr.portal.leave.upload-attachment', $navPendingSickLeave->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header py-3 px-4 bg-white border-bottom">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="avatar avatar-sm bg-danger-subtle text-danger rounded-3 d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-hospital-box-outline fs-5"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark font-15 mb-0" id="navModalUploadLateSickLabel-{{ $navPendingSickLeave->id }}">Upload Surat Dokter Susulan</h5>
                                <small class="text-muted font-11">Pengajuan Sakit: {{ \Carbon\Carbon::parse($navPendingSickLeave->start_date)->translatedFormat('d M Y') }}</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="alert alert-warning d-flex align-items-start font-12 py-2.5 px-3 mb-3 border-0 bg-warning-subtle text-dark rounded-3">
                            <i class="mdi mdi-information-outline fs-5 me-2 text-warning flex-shrink-0 mt-0.5"></i>
                            <div>
                                Silakan unggah foto atau file PDF surat keterangan dokter / kwitansi klinik sebagai bukti verifikasi sah untuk HR.
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label font-11 fw-bold text-dark mb-1">
                                File Foto / PDF Surat Dokter <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="attachment" class="form-control font-12 rounded-3" accept=".jpg,.jpeg,.png,.pdf,.webp" required>
                            <small class="text-muted font-10 mt-1 d-block">Format didukung: JPG, PNG, PDF (Maks. 5MB).</small>
                        </div>
                    </div>

                    <div class="modal-footer bg-light py-2.5 px-4 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-label-secondary font-12 rounded-3 px-3 border-0" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-danger font-12 fw-bold rounded-3 px-4 shadow-xs border-0">
                            <i class="mdi mdi-upload me-1"></i> Unggah Surat Dokter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Clock Out -->
    <div class="modal fade" id="navClockOutModal" tabindex="-1" aria-labelledby="navClockOutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div style="height: 4px; background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%); width: 100%;"></div>
                <form action="{{ route('hr.portal.clockout') }}" method="POST" id="navClockOutForm">
                    @csrf
                    <input type="hidden" name="device_id" id="navClockOutDeviceId">
                    <input type="hidden" name="device_info" id="navClockOutDeviceInfo">

                    <div class="modal-header border-bottom py-3 bg-light">
                        <h5 class="modal-title fw-bold text-warning d-flex align-items-center mb-0" id="navClockOutModalLabel">
                            <i class="mdi mdi-clock-out me-2 fs-4"></i> Presensi Pulang (Clock Out)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="avatar avatar-xl mx-auto mb-3" style="width: 60px; height: 60px;">
                            <span class="avatar-initial rounded-circle bg-label-warning">
                                <i class="mdi mdi-clock-check-outline fs-1 text-warning"></i>
                            </span>
                        </div>

                        <div class="font-monospace text-dark fw-bold fs-3 mb-1" id="navLiveClockTimeOut">
                            {{ \Carbon\Carbon::now()->format('H:i:s') }}
                        </div>
                        <div class="text-muted font-12 mb-3">
                            <i class="mdi mdi-calendar-blank-outline me-1"></i>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </div>

                        <!-- Attendance Info Card -->
                        <div class="card bg-label-secondary border-0 p-3 text-start mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="font-11 text-muted text-uppercase fw-semibold">Presensi Masuk Hari Ini</span>
                                <span class="badge bg-label-success font-10">Tercatat</span>
                            </div>
                            <div class="fw-bold text-dark font-14">
                                <i class="mdi mdi-clock-in me-1 text-success"></i>Masuk: {{ $navModalAtt ? substr($navModalAtt->clock_in, 0, 5) : '-' }} WIB
                            </div>
                            <div class="text-muted font-11 mt-1">
                                {{ Auth::user()->name }} &bull; {{ $navModalEmp->position->name ?? 'Staff' }}
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex align-items-center text-start font-11 py-2 px-3 mb-0">
                            <i class="mdi mdi-information-outline fs-5 me-2 text-warning flex-shrink-0"></i>
                            <span>Pastikan pekerjaan hari ini telah selesai. Anda akan dialihkan ke <strong>Portal Mandiri</strong> setelah Clock Out.</span>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2 border-top">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning waves-effect waves-light" id="btnSubmitNavClockOut">
                            <i class="mdi mdi-clock-out me-1"></i> Konfirmasi Clock Out
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Pop-up Notifikasi Rekap Denda & Absensi Payroll (Auto Trigger Cutoff Jam 09:00, sampai Batch Payroll dibayar & masuk Expense) --}}
    @if ($cutoffAlertData && !empty($cutoffAlertData['period']['is_recap_ready']) && empty($cutoffAlertData['is_fully_completed']))
    <div class="modal fade" id="modalAttendanceCutoffRecapAlert" tabindex="-1" aria-labelledby="modalAttendanceCutoffRecapAlertLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div style="height: 5px; background: {{ empty($cutoffAlertData['has_payroll_generated']) ? 'linear-gradient(90deg, #ef4444 0%, #f97316 50%, #6366f1 100%)' : 'linear-gradient(90deg, #f59e0b 0%, #ef4444 50%, #3b82f6 100%)' }}; width: 100%;"></div>
                
                <div class="modal-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm rounded-circle bg-label-{{ empty($cutoffAlertData['has_payroll_generated']) ? 'danger' : 'warning' }} d-flex align-items-center justify-content-center">
                            <i class="mdi {{ empty($cutoffAlertData['has_payroll_generated']) ? 'mdi-bell-ring-outline text-danger' : 'mdi-alert-circle-outline text-warning' }} fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark font-15 mb-0" id="modalAttendanceCutoffRecapAlertLabel">
                                @if (empty($cutoffAlertData['has_payroll_generated']))
                                    Rekap Denda &amp; Absensi Siap Masuk Payroll
                                @else
                                    Payroll Menunggu Payment &amp; Input ke Expense
                                @endif
                            </h5>
                            <span class="text-muted font-11">Cut-off Siklus Gajian Reftech &bull; Pukul 09:00 WIB</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="dismissRecapAlert('{{ $cutoffAlertData['period']['year'] }}', '{{ $cutoffAlertData['period']['month'] }}')"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Periode Pill Header --}}
                    <div class="d-flex align-items-center justify-content-between bg-label-primary rounded-3 p-2.5 mb-3 border border-primary border-opacity-25">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-calendar-range text-primary fs-5"></i>
                            <div>
                                <span class="font-10 text-uppercase fw-bold text-primary d-block">Periode Cut-Off Presensi</span>
                                <span class="font-13 fw-bold text-dark">
                                    {{ $cutoffAlertData['period']['start_date']->translatedFormat('d M Y') }} s/d {{ $cutoffAlertData['period']['end_date']->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>
                        <span class="badge bg-success rounded-pill px-2.5 py-1 font-11">
                            <i class="mdi mdi-check-circle me-1"></i>Terkunci 09:00 WIB
                        </span>
                    </div>

                    @if (!empty($cutoffAlertData['has_payroll_generated']))
                        {{-- Status Box Payroll yang telah dibuat namun belum Paid / belum masuk Expense --}}
                        <div class="card border border-warning bg-label-warning p-3 rounded-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="font-11 text-muted text-uppercase fw-bold d-block">Batch Payroll Terdaftar</span>
                                    <h5 class="fw-bold text-dark mb-0 font-monospace">{{ $cutoffAlertData['existing_payroll']->code }}</h5>
                                </div>
                                <div class="text-end">
                                    <span class="font-11 text-muted d-block">Total Gaji Bersih</span>
                                    <h5 class="fw-bolder text-primary mb-0 font-monospace">Rp {{ number_format($cutoffAlertData['existing_payroll']->total_net_amount, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 pt-2 border-top border-warning border-opacity-25">
                                <span class="badge {{ $cutoffAlertData['is_paid'] ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill font-11">
                                    Status Gaji: {{ $cutoffAlertData['existing_payroll']->status }}
                                </span>
                                <span class="badge {{ $cutoffAlertData['has_expense_posted'] ? 'bg-success' : 'bg-danger' }} rounded-pill font-11">
                                    {{ $cutoffAlertData['has_expense_posted'] ? 'Sudah Masuk Expense (' . ($cutoffAlertData['existing_payroll']->expense?->no_expense ?? 'EXP') . ')' : 'Belum Dibukukan ke Finance Expense' }}
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- 4-Card Summary Grid --}}
                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <div class="card bg-label-danger border-0 p-3 h-100 rounded-3">
                                <span class="font-11 text-muted d-block mb-1">Total Denda Keterlambatan</span>
                                <h4 class="fw-bolder text-danger mb-1 font-monospace">Rp {{ number_format($cutoffAlertData['total_late_penalty'], 0, ',', '.') }}</h4>
                                <span class="font-11 text-muted">
                                    <strong>{{ $cutoffAlertData['late_employees_count'] }}</strong> Karyawan ({{ $cutoffAlertData['total_late_incidents'] }} Kejadian)
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-label-warning border-0 p-3 h-100 rounded-3">
                                <span class="font-11 text-muted d-block mb-1">Potongan Alpa / Tanpa Izin</span>
                                <h4 class="fw-bolder text-warning mb-1 font-monospace">Rp {{ number_format($cutoffAlertData['total_absence_penalty'], 0, ',', '.') }}</h4>
                                <span class="font-11 text-muted">
                                    <strong>{{ $cutoffAlertData['total_absence_days'] }}</strong> Hari Alpa Tercatat
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-label-info border-0 p-3 h-100 rounded-3">
                                <span class="font-11 text-muted d-block mb-1">Total Jam Lembur</span>
                                <h4 class="fw-bolder text-info mb-1">{{ $cutoffAlertData['total_overtime_hours'] }} Jam</h4>
                                <span class="font-11 text-muted">Lembur tervalidasi</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-label-dark border-0 p-3 h-100 rounded-3">
                                <span class="font-11 text-muted d-block mb-1">Total Akumulasi Potongan</span>
                                <h4 class="fw-bolder text-dark mb-1 font-monospace">Rp {{ number_format($cutoffAlertData['total_deduction_combined'], 0, ',', '.') }}</h4>
                                <span class="font-11 text-muted">Denda Terlambat + Alpa</span>
                            </div>
                        </div>
                    </div>

                    <div class="alert {{ empty($cutoffAlertData['has_payroll_generated']) ? 'alert-secondary' : 'alert-warning' }} font-11 py-2 px-3 mb-0 d-flex align-items-center rounded-3">
                        <i class="mdi {{ empty($cutoffAlertData['has_payroll_generated']) ? 'mdi-information-outline text-primary' : 'mdi-alert-circle text-warning' }} fs-5 me-2 flex-shrink-0"></i>
                        @if (empty($cutoffAlertData['has_payroll_generated']))
                            <span>Data denda &amp; presensi di atas telah otomatis diintegrasikan ke perhitungan <strong>Payroll (Penggajian)</strong> Reftech.</span>
                        @else
                            <span>Notifikasi ini akan tetap aktif sebagai pengingat Finance sampai batch payroll <strong>dibayar (Paid)</strong> dan <strong>dibukukan ke Finance Expense</strong>.</span>
                        @endif
                    </div>
                </div>

                <div class="modal-footer bg-light py-2.5 px-4 border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-label-secondary font-12 rounded-3 px-3 border-0" data-bs-dismiss="modal" onclick="dismissRecapAlert('{{ $cutoffAlertData['period']['year'] }}', '{{ $cutoffAlertData['period']['month'] }}')">
                        Nanti Saja
                    </button>
                    <div class="d-flex gap-2">
                        <a href="{{ route('hr.attendances.penalties', ['month' => $cutoffAlertData['period']['month'], 'year' => $cutoffAlertData['period']['year']]) }}" class="btn btn-outline-danger font-12 fw-bold rounded-3 px-3 shadow-xs">
                            <i class="mdi mdi-table-eye me-1"></i> Buka Rekap Denda
                        </a>
                        @if (empty($cutoffAlertData['has_payroll_generated']))
                            <a href="{{ route('hr.payrolls.index', ['month' => $cutoffAlertData['period']['month'], 'year' => $cutoffAlertData['period']['year']]) }}" class="btn btn-primary font-12 fw-bold rounded-3 px-3 shadow-xs">
                                <i class="mdi mdi-calculator me-1"></i> Proses ke Payroll
                            </a>
                        @else
                            <a href="{{ route('hr.payrolls.show', $cutoffAlertData['existing_payroll']->id) }}" class="btn btn-primary font-12 fw-bold rounded-3 px-3 shadow-xs">
                                <i class="mdi mdi-cash-check me-1"></i> Proses Payment &amp; Expense
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Alert Quick Approval Cuti / Izin Karyawan --}}
    @if ($isLeaveAlertEligible && $leaveAlertPendingCount > 0)
    <div class="modal fade" id="modalLeaveApprovalAlert" tabindex="-1" aria-labelledby="modalLeaveApprovalAlertLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div style="height: 5px; background: linear-gradient(90deg, #0ea5e9 0%, #6366f1 50%, #a855f7 100%); width: 100%;"></div>
                
                <div class="modal-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm rounded-circle bg-label-info d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-calendar-check-outline text-info fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark font-15 mb-0" id="modalLeaveApprovalAlertLabel">
                                Approval Cuti, Izin &amp; Visit Customer
                            </h5>
                            <span class="text-muted font-11">Terdapat <strong class="text-primary">{{ $leaveAlertPendingCount }} permohonan</strong> yang menunggu persetujuan Anda</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="d-flex flex-column gap-3">
                        @foreach ($leaveAlertPendingList as $pendingItem)
                            @php
                                $emp = $pendingItem->employee;
                                $uName = $emp?->user?->name ?? ($emp?->nik ? 'Karyawan ' . $emp->nik : 'Karyawan #' . $pendingItem->employee_id);
                                $lType = $pendingItem->leaveType;
                                $typeBadgeColor = match($lType?->code ?? '') {
                                    'CT' => 'primary',
                                    'SK' => 'danger',
                                    'VC' => 'success',
                                    'DL' => 'info',
                                    default => 'warning',
                                };
                            @endphp
                            <div class="card border border-light-subtle shadow-none rounded-3 p-3 bg-white" style="border: 1px solid #e2e8f0 !important;">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-2 pb-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="avatar avatar-sm rounded-circle bg-label-primary d-flex align-items-center justify-content-center fw-bold text-primary font-12">
                                            {{ strtoupper(substr($uName, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0 font-14">{{ $uName }}</h6>
                                            <span class="text-muted font-11">{{ $emp?->department?->name ?? 'Departemen -' }} &bull; NIK: {{ $emp?->nik ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-label-{{ $typeBadgeColor }} rounded-pill font-11 px-2.5 py-1">
                                            <i class="mdi mdi-tag-outline me-1"></i>{{ $lType?->name ?? 'Izin' }}
                                        </span>
                                        <span class="badge bg-label-dark rounded-pill font-11 px-2.5 py-1">
                                            {{ $pendingItem->total_days }} Hari
                                        </span>
                                    </div>
                                </div>

                                <div class="row g-2 mb-2 font-12 text-secondary">
                                    <div class="col-md-6">
                                        <span class="d-block text-muted font-11"><i class="mdi mdi-calendar me-1"></i>Periode Tanggal:</span>
                                        <strong class="text-dark">
                                            {{ \Carbon\Carbon::parse($pendingItem->start_date)->translatedFormat('d M Y') }}
                                            @if ($pendingItem->start_date != $pendingItem->end_date)
                                                s/d {{ \Carbon\Carbon::parse($pendingItem->end_date)->translatedFormat('d M Y') }}
                                            @endif
                                        </strong>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="d-block text-muted font-11"><i class="mdi mdi-comment-text-outline me-1"></i>Alasan:</span>
                                        <span class="text-dark">{{ $pendingItem->reason ?: '-' }}</span>
                                    </div>
                                    @if ($pendingItem->attachment)
                                    <div class="col-12 mt-1">
                                        <a href="{{ asset($pendingItem->attachment) }}" target="_blank" class="btn btn-xs btn-label-primary rounded-pill px-2.5 py-1 font-11 text-decoration-none">
                                            <i class="mdi mdi-paperclip me-1"></i>Lihat Surat Lampiran / Bukti
                                        </a>
                                    </div>
                                    @elseif ($pendingItem->leaveType?->code === 'SK' || str_contains(strtolower($pendingItem->leaveType?->name ?? ''), 'sakit'))
                                    <div class="col-12 mt-1">
                                        <span class="badge bg-label-warning font-10">
                                            <i class="mdi mdi-clock-outline me-1"></i>Surat dokter menyusul
                                        </span>
                                    </div>
                                    @endif
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="text-muted font-10">
                                        Diajukan: {{ \Carbon\Carbon::parse($pendingItem->created_at)->diffForHumans() }}
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        {{-- Reject Form --}}
                                        <form action="{{ route('hr.leaves.reject', $pendingItem->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="rejection_note" class="rejection-note-input" value="Ditolak oleh manajemen">
                                            <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-3 py-1 font-11 fw-semibold btn-reject-leave" data-id="{{ $pendingItem->id }}" data-name="{{ $uName }}">
                                                <i class="mdi mdi-close me-1"></i>Tolak
                                            </button>
                                        </form>

                                        {{-- Approve Form --}}
                                        <form action="{{ route('hr.leaves.approve', $pendingItem->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-success rounded-pill px-3 py-1 font-11 fw-bold shadow-xs">
                                                <i class="mdi mdi-check me-1"></i>Setujui
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer bg-light py-2.5 px-4 border-top d-flex justify-content-between">
                    <a href="{{ route('hr.leaves.index', ['tab' => 'alert_settings']) }}" class="btn btn-label-secondary font-12 rounded-3 px-3">
                        <i class="mdi mdi-cog-outline me-1"></i> Atur Penerima Alert
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-label-secondary font-12 rounded-3 px-3" data-bs-dismiss="modal">Tutup</button>
                        <a href="{{ route('hr.leaves.index') }}" class="btn btn-primary font-12 fw-bold rounded-3 px-3 shadow-xs">
                            <i class="mdi mdi-format-list-bulleted me-1"></i> Buka Manajemen Cuti
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Alert Quick Approval Reimbursement Karyawan --}}
    @if ($isFinanceOrHrRole && isset($pendingReimbursementsNotif) && $pendingReimbursementsNotif->count() > 0)
    <div class="modal fade" id="modalReimbursementApprovalAlert" tabindex="-1" aria-labelledby="modalReimbursementApprovalAlertLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div style="height: 5px; background: linear-gradient(90deg, #f59e0b 0%, #ec4899 50%, #8b5cf6 100%); width: 100%;"></div>
                
                <div class="modal-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm rounded-circle bg-label-warning d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-receipt-text-clock-outline text-warning fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark font-15 mb-0" id="modalReimbursementApprovalAlertLabel">
                                Review &amp; Approval Klaim Reimbursement
                            </h5>
                            <span class="text-muted font-11">Terdapat <strong class="text-warning">{{ $unreadReimbursementCount }} klaim biaya</strong> menunggu verifikasi / persetujuan Finance</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="d-flex flex-column gap-3">
                        @foreach ($pendingReimbursementsNotif as $claimItem)
                            @php
                                $emp = $claimItem->employee;
                                $uName = $emp?->user?->name ?? ($emp?->nik ? 'Karyawan ' . $emp->nik : 'Karyawan #' . $claimItem->employee_id);
                            @endphp
                            <div class="card border border-light-subtle shadow-none rounded-3 p-3 bg-white" style="border: 1px solid #e2e8f0 !important;">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-2 pb-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="avatar avatar-sm rounded-circle bg-label-warning d-flex align-items-center justify-content-center fw-bold text-warning font-12">
                                            {{ strtoupper(substr($uName, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0 font-14">{{ $uName }}</h6>
                                            <span class="text-muted font-11">{{ $emp?->department?->name ?? 'Departemen -' }} &bull; NIK: {{ $emp?->nik ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-label-primary rounded-pill font-11 px-2.5 py-1">
                                            <i class="mdi mdi-tag-outline me-1"></i>{{ $claimItem->claim_type }}
                                        </span>
                                        <span class="badge bg-label-dark font-monospace rounded-pill font-11 px-2.5 py-1">
                                            {{ $claimItem->claim_number }}
                                        </span>
                                    </div>
                                </div>

                                <div class="row g-2 mb-2 font-12 text-secondary">
                                    <div class="col-md-6">
                                        <span class="d-block text-muted font-11"><i class="mdi mdi-calendar-blank-outline me-1"></i>Tanggal Nota:</span>
                                        <strong class="text-dark">
                                            {{ \Carbon\Carbon::parse($claimItem->event_date)->translatedFormat('d M Y') }}
                                        </strong>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="d-block text-muted font-11"><i class="mdi mdi-cash-multiple me-1"></i>Nominal Klaim:</span>
                                        <strong class="text-success fs-6">
                                            Rp {{ number_format($claimItem->amount, 0, ',', '.') }}
                                        </strong>
                                    </div>
                                    <div class="col-12">
                                        <span class="d-block text-muted font-11"><i class="mdi mdi-text-box-outline me-1"></i>Keperluan / Keterangan:</span>
                                        <span class="text-dark">{{ $claimItem->description ?: '-' }}</span>
                                    </div>
                                    @if ($claimItem->receipt_image)
                                    <div class="col-12 mt-1">
                                        <a href="{{ asset($claimItem->receipt_image) }}" target="_blank" class="btn btn-xs btn-label-primary rounded-pill px-2.5 py-1 font-11 text-decoration-none d-inline-flex align-items-center gap-1">
                                            <i class="mdi mdi-receipt-text-outline"></i>Lihat Foto Struk / Nota Pembayaran
                                        </a>
                                    </div>
                                    @endif
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="text-muted font-10">
                                        Diajukan: {{ \Carbon\Carbon::parse($claimItem->created_at)->diffForHumans() }}
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        {{-- Reject Form --}}
                                        <form action="{{ route('hr.reimbursements.reject', $claimItem->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="rejection_reason" class="rejection-reason-input" value="Ditolak oleh finance">
                                            <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-3 py-1 font-11 fw-semibold btn-reject-reimbursement" data-id="{{ $claimItem->id }}" data-num="{{ $claimItem->claim_number }}" data-name="{{ $uName }}">
                                                <i class="mdi mdi-close me-1"></i>Tolak
                                            </button>
                                        </form>

                                        {{-- Approve Form --}}
                                        <form action="{{ route('hr.reimbursements.approve', $claimItem->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-success rounded-pill px-3 py-1 font-11 fw-bold shadow-xs">
                                                <i class="mdi mdi-check me-1"></i>Setujui
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer bg-light py-2.5 px-4 border-top d-flex justify-content-between">
                    <span class="text-muted font-11">Pencairan dana langsung dapat diposting ke Finance Expense di modul HR</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-label-secondary font-12 rounded-3 px-3" data-bs-dismiss="modal">Tutup</button>
                        <a href="{{ route('hr.reimbursements.index') }}" class="btn btn-warning text-dark font-12 fw-bold rounded-3 px-3 shadow-xs">
                            <i class="mdi mdi-format-list-bulleted me-1"></i> Buka Modul Reimbursement
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif

    <script>
        function dismissRecapAlert(year, month) {
            try {
                sessionStorage.setItem('reftech_cutoff_recap_dismissed_' + year + '_' + month, '1');
            } catch (e) {}
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Auto Popup Modal Rekap Denda Cutoff Jam 09:00 (seperti SUO / Tool Audit)
            @if ($cutoffAlertData && !empty($cutoffAlertData['period']['is_recap_ready']) && empty($cutoffAlertData['is_fully_completed']))
                (function() {
                    var y = '{{ $cutoffAlertData['period']['year'] }}';
                    var m = '{{ $cutoffAlertData['period']['month'] }}';
                    var dismissed = sessionStorage.getItem('reftech_cutoff_recap_dismissed_' + y + '_' + m);
                    if (!dismissed) {
                        var modalEl = document.getElementById('modalAttendanceCutoffRecapAlert');
                        if (modalEl) {
                            setTimeout(function() {
                                var bsAlertModal = new bootstrap.Modal(modalEl);
                                bsAlertModal.show();
                            }, 800);
                        }
                    }
                })();
            @endif

            // Quick Rejection Handler for Leave Approval Modal
            document.querySelectorAll('.btn-reject-leave').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var empName = this.getAttribute('data-name');
                    var form = this.closest('form');
                    var noteInput = form ? form.querySelector('.rejection-note-input') : null;

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Tolak Pengajuan Cuti / Izin?',
                            text: 'Masukkan alasan penolakan untuk ' + empName + ':',
                            input: 'textarea',
                            inputPlaceholder: 'Tuliskan alasan penolakan di sini...',
                            inputAttributes: {
                                'aria-label': 'Alasan penolakan'
                            },
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: '<i class="mdi mdi-close me-1"></i>Ya, Tolak',
                            cancelButtonText: 'Batal',
                            inputValidator: function(value) {
                                if (!value || !value.trim()) {
                                    return 'Alasan penolakan wajib diisi!';
                                }
                            }
                        }).then(function(result) {
                            if (result.isConfirmed && noteInput) {
                                noteInput.value = result.value;
                                form.submit();
                            }
                        });
                    } else {
                        var reason = prompt('Masukkan alasan penolakan untuk ' + empName + ':');
                        if (reason && reason.trim() && noteInput) {
                            noteInput.value = reason;
                            form.submit();
                        }
                    }
                });
            });

            // Quick Rejection Handler for Reimbursement Approval Modal
            document.querySelectorAll('.btn-reject-reimbursement').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var empName = this.getAttribute('data-name');
                    var claimNum = this.getAttribute('data-num');
                    var form = this.closest('form');
                    var noteInput = form ? form.querySelector('.rejection-reason-input') : null;

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Tolak Klaim Reimbursement?',
                            text: 'Masukkan alasan penolakan untuk klaim ' + claimNum + ' (' + empName + '):',
                            input: 'textarea',
                            inputPlaceholder: 'Tuliskan alasan penolakan di sini...',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: '<i class="mdi mdi-close me-1"></i>Ya, Tolak',
                            cancelButtonText: 'Batal',
                            inputValidator: function(value) {
                                if (!value || !value.trim()) {
                                    return 'Alasan penolakan wajib diisi!';
                                }
                            }
                        }).then(function(result) {
                            if (result.isConfirmed && noteInput) {
                                noteInput.value = result.value;
                                form.submit();
                            }
                        });
                    } else {
                        var reason = prompt('Masukkan alasan penolakan untuk klaim ' + claimNum + ':');
                        if (reason && reason.trim() && noteInput) {
                            noteInput.value = reason;
                            form.submit();
                        }
                    }
                });
            });

            // Auto Live Polling Background untuk Reimbursement & Leave Request (Realtime tanpa reload halaman)
            @if (Auth::check() && ($isFinanceOrHrRole || $isLeaveAlertEligible))
            (function() {
                var pendingCheckUrl = '{{ route('hr.notifications.pending-check') }}';
                setInterval(function() {
                    fetch(pendingCheckUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (!data) return;

                        // Update Bell Dot
                        var bellDot = document.getElementById('navbarBellDot');
                        if (bellDot && data.total > 0) {
                            bellDot.classList.remove('d-none');
                        }

                        // Update Reimbursement Pill & Badge
                        var reimbBadge = document.getElementById('navReimbursementAlertBadge');
                        var reimbItem = document.getElementById('navReimbursementAlertItem');
                        if (data.reimbursements > 0) {
                            if (reimbBadge) reimbBadge.textContent = data.reimbursements;
                            if (reimbItem) reimbItem.classList.remove('d-none');
                        }

                        // Update Leave Pill & Badge
                        var leaveBadge = document.getElementById('navLeaveAlertBadge');
                        var leaveItem = document.getElementById('navLeaveAlertItem');
                        if (data.leaves > 0) {
                            if (leaveBadge) leaveBadge.textContent = data.leaves;
                            if (leaveItem) leaveItem.classList.remove('d-none');
                        }
                    })
                    .catch(function(err) {});
                }, 30000); // Polling background otomatis setiap 30 detik
            })();
            @endif

            // 1. Device Token (UUID) Management
            function getOrCreateDeviceId() {
                var key = 'reftech_attendance_device_uuid';
                var devId = localStorage.getItem(key);
                if (!devId) {
                    try {
                        if (window.crypto && window.crypto.randomUUID) {
                            devId = 'DEV-' + window.crypto.randomUUID();
                        } else {
                            devId = 'DEV-' + 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                                var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                                return v.toString(16);
                            });
                        }
                    } catch (e) {
                        devId = 'DEV-' + Date.now() + '-' + Math.floor(Math.random() * 1000000);
                    }
                    localStorage.setItem(key, devId);
                }
                return devId;
            }

            var deviceId = getOrCreateDeviceId();
            var deviceInfo = navigator.userAgent;

            var inDev = document.getElementById('navClockInDeviceId');
            var inInfo = document.getElementById('navClockInDeviceInfo');
            var outDev = document.getElementById('navClockOutDeviceId');
            var outInfo = document.getElementById('navClockOutDeviceInfo');

            if (inDev) inDev.value = deviceId;
            if (inInfo) inInfo.value = deviceInfo;
            if (outDev) outDev.value = deviceId;
            if (outInfo) outInfo.value = deviceInfo;

            // 1.5 Validasi Keamanan Pra-Clock In (Earliest Time, WiFi & Device Lock)
            var isWifiBlockedInitially = {{ ($navIsWifiRestrictionEnabled && !$navIsWifiVerified) ? 'true' : 'false' }};
            var isEarlyBlockedInitially = {{ $navIsBeforeEarliest ? 'true' : 'false' }};
            function verifyClockInEligibility() {
                var btn = document.getElementById('btnSubmitNavClockIn');
                var deviceAlert = document.getElementById('navClockInDeviceAlert');
                var deviceAlertText = document.getElementById('navClockInDeviceAlertText');
                var wifiAlert = document.getElementById('navClockInWifiAlert');
                var earlyAlert = document.getElementById('navClockInEarlyAlert');
                var earlyAlertText = document.getElementById('navClockInEarlyAlertText');

                if (isEarlyBlockedInitially) {
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.add('disabled');
                        btn.setAttribute('title', 'Presensi masuk belum dibuka.');
                    }
                    if (earlyAlert) earlyAlert.classList.remove('d-none');
                }

                if (isWifiBlockedInitially) {
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.add('disabled');
                        btn.setAttribute('title', 'Jaringan WiFi kantor tidak sesuai');
                    }
                    if (wifiAlert) wifiAlert.classList.remove('d-none');
                }

                var checkUrl = "{{ route('hr.portal.verify-pre-clockin') }}";
                fetch(checkUrl + '?device_id=' + encodeURIComponent(deviceId), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (!data.allowed) {
                        if (btn) {
                            btn.disabled = true;
                            btn.classList.add('disabled');
                            btn.setAttribute('title', data.message || 'Presensi tidak dapat dilakukan.');
                        }
                        if (data.type === 'early_clockin_blocked') {
                            if (earlyAlert && earlyAlertText) {
                                earlyAlertText.textContent = data.message;
                                earlyAlert.classList.remove('d-none');
                            }
                        } else if (data.type === 'device_error') {
                            if (deviceAlert && deviceAlertText) {
                                deviceAlertText.textContent = data.message;
                                deviceAlert.classList.remove('d-none');
                            }
                        } else if (data.type === 'wifi_error') {
                            if (wifiAlert) {
                                wifiAlert.classList.remove('d-none');
                                var wifiText = document.getElementById('navClockInWifiAlertText');
                                if (wifiText) wifiText.textContent = data.message;
                            }
                        } else {
                            if (deviceAlert && deviceAlertText) {
                                deviceAlertText.textContent = data.message;
                                deviceAlert.classList.remove('d-none');
                            }
                        }
                    } else {
                        if (earlyAlert) earlyAlert.classList.add('d-none');
                        if (deviceAlert) deviceAlert.classList.add('d-none');
                        if (wifiAlert) wifiAlert.classList.add('d-none');
                        if (btn) {
                            btn.disabled = false;
                            btn.classList.remove('disabled');
                            btn.removeAttribute('title');
                        }
                    }
                })
                .catch(function(err) {
                    console.warn('Verifikasi Pra-Clock In gagal:', err);
                });
            }

            var clockInModalEl = document.getElementById('navClockInModal');
            if (clockInModalEl) {
                clockInModalEl.addEventListener('show.bs.modal', function() {
                    verifyClockInEligibility();
                });
            }

            // 2. Live clock updater for nav modals
            function updateNavLiveTime() {
                var now = new Date();
                var timeStr = String(now.getHours()).padStart(2, '0') + ':' +
                              String(now.getMinutes()).padStart(2, '0') + ':' +
                              String(now.getSeconds()).padStart(2, '0');
                var inEl = document.getElementById('navLiveClockTimeIn');
                var outEl = document.getElementById('navLiveClockTimeOut');
                if (inEl) inEl.textContent = timeStr;
                if (outEl) outEl.textContent = timeStr;
            }
            setInterval(updateNavLiveTime, 1000);

            // 3. Live Selfie Camera Stream Lifecycle
            var cameraStream = null;
            var isSelfieRequired = {{ $navIsSelfieRequired ? 'true' : 'false' }};
            var clockInModal = document.getElementById('navClockInModal');

            if (clockInModal && isSelfieRequired) {
                clockInModal.addEventListener('shown.bs.modal', function () {
                    var videoEl = document.getElementById('navCameraVideo');
                    var loaderEl = document.getElementById('navCameraLoadingOverlay');
                    var errEl = document.getElementById('navCameraErrorAlert');

                    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                        navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: 'user',
                                width: { ideal: 640 },
                                height: { ideal: 480 }
                            },
                            audio: false
                        }).then(function(stream) {
                            cameraStream = stream;
                            if (videoEl) {
                                videoEl.srcObject = stream;
                                videoEl.play();
                            }
                            if (loaderEl) loaderEl.classList.add('d-none');
                            if (errEl) errEl.classList.add('d-none');
                        }).catch(function(err) {
                            console.error('Kamera gagal diakses:', err);
                            if (loaderEl) loaderEl.classList.add('d-none');
                            if (errEl) {
                                errEl.classList.remove('d-none');
                                errEl.textContent = 'Gagal mengakses kamera: ' + (err.message || 'Izinkan akses kamera browser.');
                            }
                        });
                    } else {
                        if (loaderEl) loaderEl.classList.add('d-none');
                        if (errEl) {
                            errEl.classList.remove('d-none');
                            errEl.textContent = 'Browser Anda tidak mendukung akses kamera live.';
                        }
                    }
                });

                clockInModal.addEventListener('hidden.bs.modal', function () {
                    if (cameraStream) {
                        cameraStream.getTracks().forEach(function(track) {
                            track.stop();
                        });
                        cameraStream = null;
                    }
                });
            }

            // 4. Loading state & Snapshot capture on submit & AJAX Seamless Flow
            window.testSimulateClockIn = function(type) {
                var clockInModalEl = document.getElementById('navClockInModal');
                if (clockInModalEl) {
                    var modalInstance = bootstrap.Modal.getInstance(clockInModalEl);
                    if (modalInstance) modalInstance.hide();
                }

                if (type === 'on_time') {
                    var onTimeEl = document.getElementById('modalClockInFeedbackOnTime');
                    if (onTimeEl) {
                        var cEl = document.getElementById('feedbackOnTimeClock');
                        var sEl = document.getElementById('feedbackOnTimeStatus');
                        var pEl = document.getElementById('feedbackOnTimePenalty');
                        var wEl = document.getElementById('feedbackOnTimeWorkType');
                        if (cEl) cEl.textContent = '07:52 WIB';
                        if (sEl) sEl.textContent = 'Tepat Waktu (Disiplin)';
                        if (pEl) pEl.textContent = 'Rp 0 (Bebas Denda)';
                        if (wEl) wEl.textContent = 'WFO (Kantor)';
                        var m = new bootstrap.Modal(onTimeEl);
                        m.show();
                    }
                } else {
                    var lateEl = document.getElementById('modalClockInFeedbackLate');
                    if (lateEl) {
                        var mEl = document.getElementById('feedbackLateMinutes');
                        var cntEl = document.getElementById('feedbackLateCount');
                        var clkEl = document.getElementById('feedbackLateClock');
                        var penEl = document.getElementById('feedbackLatePenalty');
                        var lblEl = document.getElementById('feedbackLateStatusLabel');
                        if (mEl) mEl.textContent = '15';
                        if (cntEl) cntEl.textContent = '1';
                        if (clkEl) clkEl.textContent = '08:15 WIB';
                        if (penEl) penEl.textContent = 'Rp 50.000';
                        if (lblEl) lblEl.textContent = 'Terlambat ke-1 di bulan ini (Denda Tier 1 Rp 50.000)';
                        var m = new bootstrap.Modal(lateEl);
                        m.show();
                    }
                }
            };

            var formIn = document.getElementById('navClockInForm');
            if (formIn) {
                formIn.addEventListener('submit', function(e) {
                    var btn = document.getElementById('btnSubmitNavClockIn');
                    if (btn && btn.disabled) {
                        e.preventDefault();
                        return false;
                    }
                    
                    if (isSelfieRequired) {
                        var video = document.getElementById('navCameraVideo');
                        var canvas = document.getElementById('navCameraCanvas');
                        var selfieInput = document.getElementById('navClockInSelfieImage');

                        if (video && canvas && selfieInput) {
                            var vWidth = video.videoWidth || 640;
                            var vHeight = video.videoHeight || 480;

                            canvas.width = vWidth;
                            canvas.height = vHeight;
                            var ctx = canvas.getContext('2d');
                            
                            // Flip horizontal mirror
                            ctx.translate(vWidth, 0);
                            ctx.scale(-1, 1);
                            ctx.drawImage(video, 0, 0, vWidth, vHeight);
                            
                            try {
                                var base64 = canvas.toDataURL('image/jpeg', 0.85);
                                selfieInput.value = base64;
                            } catch (err) {
                                console.error('Snapshot error:', err);
                            }
                        }
                    }

                    // AJAX Submit Flow (Tanpa redirect reload halaman portal)
                    e.preventDefault();

                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses Presensi...';
                    }

                    var formData = new FormData(formIn);
                    fetch(formIn.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(res) {
                        return res.json().then(function(data) {
                            return { status: res.status, ok: res.ok, data: data };
                        });
                    })
                    .then(function(result) {
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="mdi mdi-clock-in fs-5"></i> <span>Konfirmasi Clock In</span>';
                        }

                        if (!result.ok || !result.data.success) {
                            alert(result.data.message || 'Gagal melakukan presensi. Silakan coba kembali.');
                            return;
                        }

                        // Close clock in modal
                        var clockInModalEl = document.getElementById('navClockInModal');
                        if (clockInModalEl) {
                            var modalInst = bootstrap.Modal.getInstance(clockInModalEl);
                            if (modalInst) modalInst.hide();
                        }

                        var data = result.data;
                        if (data.is_on_time) {
                            // Tepat Waktu -> Buka feedback modal On-Time
                            var onTimeEl = document.getElementById('modalClockInFeedbackOnTime');
                            if (onTimeEl) {
                                var cEl = document.getElementById('feedbackOnTimeClock');
                                var sEl = document.getElementById('feedbackOnTimeStatus');
                                var pEl = document.getElementById('feedbackOnTimePenalty');
                                var wEl = document.getElementById('feedbackOnTimeWorkType');
                                if (cEl) cEl.textContent = data.clock_in_time_formatted || 'Tercatat';
                                if (sEl) sEl.textContent = data.status_label || 'Tepat Waktu';
                                if (pEl) pEl.textContent = data.penalty_formatted ? data.penalty_formatted + ' (Bebas Denda)' : 'Rp 0 (Bebas Denda)';
                                if (wEl) wEl.textContent = data.work_type || 'WFO';
                                var m = new bootstrap.Modal(onTimeEl);
                                m.show();
                            }
                        } else {
                            // Terlambat -> Buka feedback modal Late
                            var lateEl = document.getElementById('modalClockInFeedbackLate');
                            if (lateEl) {
                                var mEl = document.getElementById('feedbackLateMinutes');
                                var cntEl = document.getElementById('feedbackLateCount');
                                var clkEl = document.getElementById('feedbackLateClock');
                                var penEl = document.getElementById('feedbackLatePenalty');
                                var lblEl = document.getElementById('feedbackLateStatusLabel');
                                if (mEl) mEl.textContent = data.late_minutes;
                                if (cntEl) cntEl.textContent = data.late_count;
                                if (clkEl) clkEl.textContent = data.clock_in_time_formatted;
                                if (penEl) penEl.textContent = data.penalty_formatted;
                                if (lblEl) lblEl.textContent = data.status_label;
                                var m = new bootstrap.Modal(lateEl);
                                m.show();
                            }
                        }

                        // Update Clock In button in navbar live to "Masuk: XX:XX WIB"
                        var navClockInBtn = document.querySelector('[data-bs-target="#navClockInModal"]');
                        if (navClockInBtn) {
                            navClockInBtn.outerHTML = '<button type="button" class="btn btn-sm btn-label-success rounded-pill px-3 py-1 d-flex align-items-center shadow-xs" title="Presensi Masuk Tercatat">' +
                                '<i class="mdi mdi-clock-check-outline me-1 text-success"></i>' +
                                '<span class="fw-bold d-none d-sm-inline">Masuk: ' + (data.clock_in_time_formatted || 'Tercatat') + '</span>' +
                                '<span class="fw-bold d-inline d-sm-none">Hadir</span>' +
                            '</button>';
                        }
                    })
                    .catch(function(err) {
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="mdi mdi-clock-in fs-5"></i> <span>Konfirmasi Clock In</span>';
                        }
                        console.error('Clock In AJAX Error:', err);
                        alert('Terjadi kesalahan saat memproses presensi. Silakan coba kembali.');
                    });
                });
            }

            var formOut = document.getElementById('navClockOutForm');
            if (formOut) {
                formOut.addEventListener('submit', function() {
                    var btn = document.getElementById('btnSubmitNavClockOut');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mencatat Clock Out...';
                    }
                });
            }

            // 5. Auto-prompt Clock In Modal on first login/visit on Weekdays (Monday - Friday)
            @php
                $isWeekday = \Carbon\Carbon::today('Asia/Jakarta')->isWeekday();
                $hasClockedInToday = $navModalAtt && !empty($navModalAtt->clock_in);
                $todayDateKey = \Carbon\Carbon::today('Asia/Jakarta')->toDateString();
            @endphp

            @if ($isWeekday && !$hasClockedInToday)
                (function() {
                    var todayKey = 'clock_in_auto_prompted_{{ Auth::id() }}_{{ $todayDateKey }}';
                    var hasPrompted = sessionStorage.getItem(todayKey);

                    if (!hasPrompted) {
                        // Mark as prompted in this session so navigating pages does not re-open the modal
                        sessionStorage.setItem(todayKey, '1');

                        var triggerAutoClockIn = function() {
                            setTimeout(function() {
                                // Anti-collision 1: Skip if any other modal is already showing
                                if (document.querySelector('.modal.show')) {
                                    return;
                                }

                                // Anti-collision 2: Skip on print, document sign, or maintenance views
                                var path = window.location.pathname;
                                if (path.includes('/print') || path.includes('/sign/') || path.includes('/under-maintenance')) {
                                    return;
                                }

                                // Open Clock In modal safely
                                var clockInModalEl = document.getElementById('navClockInModal');
                                if (clockInModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                    var modalInstance = bootstrap.Modal.getInstance(clockInModalEl) || new bootstrap.Modal(clockInModalEl);
                                    modalInstance.show();
                                }
                            }, 800);
                        };

                        if (document.readyState === 'complete') {
                            triggerAutoClockIn();
                        } else {
                            window.addEventListener('load', triggerAutoClockIn);
                        }
                    }
                })();
            @endif
        });
    </script>
@endif

{{-- Modal & Notifikasi Ucapan Jam Pulang Otomatis (All Roles / Karyawan) --}}
@if (Auth::check())
    @php
        $navAttSettings = \Illuminate\Support\Facades\DB::table('hr_attendance_settings')->pluck('value', 'key')->toArray();
        $navIsGreetingEnabled = ($navAttSettings['is_clock_out_greeting_enabled'] ?? '1') === '1';
        $navGreetingTitle = $navAttSettings['clock_out_greeting_title'] ?? 'Terima Kasih Atas Kerja Keras Hari Ini! 🎉';
        $navGreetingMessage = $navAttSettings['clock_out_greeting_message'] ?? 'Jam kerja operasional kantor hari ini telah selesai. Selamat beristirahat, nikmati waktu berkualitas bersama keluarga, dan sampai jumpa besok!';
        $navAutoOutTime = $navAttSettings['auto_clock_out_time'] ?? '17:00';
        
        $navCurrentTimeJakarta = \Carbon\Carbon::now('Asia/Jakarta');
        $navIsAfterClockOutTime = $navCurrentTimeJakarta->format('H:i') >= $navAutoOutTime;
        $navTodayDateKey = $navCurrentTimeJakarta->toDateString();

        $navUserEmployee = Auth::user()->employee;
        $navUserAttToday = $navUserEmployee 
            ? \App\Models\Hr\HrAttendance::where('employee_id', $navUserEmployee->id)->whereDate('date', $navTodayDateKey)->first()
            : null;
        $navHasClockedIn = $navUserAttToday && !empty($navUserAttToday->clock_in);
        $navHasClockedOut = $navUserAttToday && !empty($navUserAttToday->clock_out);
    @endphp

    @if ($navIsGreetingEnabled)
        <!-- Modal Ucapan Jam Pulang (Clock Out Greeting Modal - Prospect Style) -->
        <style>
            @keyframes clockoutShimmer {
                0% { background-position: -200% 0; }
                100% { background-position: 200% 0; }
            }
            #modalClockOutGreeting {
                z-index: 1095 !important;
            }
            #modalClockOutGreeting .modal-dialog {
                max-width: 550px;
                margin: 1.75rem auto;
            }
            #modalClockOutGreeting .modal-content {
                border-radius: 20px !important;
                border: 0 !important;
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.28) !important;
                overflow: hidden;
                background: #ffffff;
            }
            html.dark-style #modalClockOutGreeting .modal-content {
                background: #2b2c40 !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7) !important;
                color: #e4e6f0;
            }
            #modalClockOutGreeting .clockout-top-stripe {
                height: 5px;
                background: linear-gradient(90deg, #f59e0b 0%, #ec4899 50%, #6366f1 100%);
                background-size: 200% 100%;
                animation: clockoutShimmer 3s ease-in-out infinite;
                width: 100%;
            }
            .clockout-hero-halo {
                width: 76px;
                height: 76px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(245, 158, 11, 0.22) 0%, rgba(245, 158, 11, 0.04) 70%, transparent 100%);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .clockout-hero-inner {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 6px 18px rgba(245, 158, 11, 0.4);
            }
            #modalClockOutGreeting .clockout-detail-card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                overflow: hidden;
            }
            html.dark-style #modalClockOutGreeting .clockout-detail-card {
                background: #32344d;
                border-color: #3f4262;
            }
            #modalClockOutGreeting .clockout-card-header {
                background: #ffffff;
                border-bottom: 1px solid #edf0f5;
                padding: 11px 16px;
            }
            html.dark-style #modalClockOutGreeting .clockout-card-header {
                background: #2b2c40;
                border-bottom-color: #3f4262;
            }
            #modalClockOutGreeting .clockout-card-body {
                padding: 12px 16px;
            }
            #modalClockOutGreeting .clockout-info-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 7px 0;
                border-bottom: 1px dashed #e2e8f0;
            }
            html.dark-style #modalClockOutGreeting .clockout-info-row {
                border-bottom-color: #434665;
            }
            #modalClockOutGreeting .clockout-info-row:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }
            #modalClockOutGreeting .clockout-info-label {
                font-size: 0.815rem;
                color: #64748b;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            html.dark-style #modalClockOutGreeting .clockout-info-label {
                color: #a1a4b8;
            }
            #modalClockOutGreeting .clockout-info-val {
                font-size: 0.865rem;
                font-weight: 600;
                color: #1e293b;
                text-align: right;
                max-width: 68%;
            }
            html.dark-style #modalClockOutGreeting .clockout-info-val {
                color: #f1f5f9;
            }
            #modalClockOutGreeting .clockout-guide-card {
                background: #fffbeb;
                border: 1px solid #fef3c7;
                border-radius: 12px;
                padding: 10px 14px;
            }
            html.dark-style #modalClockOutGreeting .clockout-guide-card {
                background: #3b2a1a;
                border-color: #573c23;
            }
            #modalClockOutGreeting .btn-action-clockout {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                border: none;
                color: #ffffff;
                font-weight: 600;
                font-size: 0.9rem;
                padding: 10px 20px;
                border-radius: 12px;
                box-shadow: 0 4px 14px rgba(245, 158, 11, 0.38);
                transition: all 0.2s ease;
            }
            #modalClockOutGreeting .btn-action-clockout:hover {
                background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
                color: #ffffff;
                box-shadow: 0 6px 18px rgba(245, 158, 11, 0.48);
                transform: translateY(-1px);
            }
            #modalClockOutGreeting .btn-dismiss-clockout {
                background: #f1f5f9;
                color: #475569;
                border: 1px solid #e2e8f0;
                font-weight: 600;
                font-size: 0.88rem;
                padding: 10px 18px;
                border-radius: 12px;
                transition: all 0.15s ease;
            }
            #modalClockOutGreeting .btn-dismiss-clockout:hover {
                background: #e2e8f0;
                color: #1e293b;
            }
            html.dark-style #modalClockOutGreeting .btn-dismiss-clockout {
                background: #334155;
                color: #e2e8f0;
                border-color: #475569;
            }
            html.dark-style #modalClockOutGreeting .btn-dismiss-clockout:hover {
                background: #475569;
                color: #ffffff;
            }
        </style>

        <div class="modal fade" id="modalClockOutGreeting" tabindex="-1" aria-labelledby="modalClockOutGreetingLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <!-- Top Accent Gradient Line -->
                    <div class="clockout-top-stripe"></div>

                    <div class="modal-body p-4 pt-3">
                        <!-- Top Status Badge & Close Button -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge rounded-pill px-3 py-1 text-uppercase fw-bold" style="background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="mdi mdi-weather-sunset-down me-1"></i> JAM PULANG KANTOR &bull; {{ $navAutoOutTime }} WIB
                            </span>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- Hero Icon Halo & Centered Titles -->
                        <div class="text-center mb-3">
                            <div class="clockout-hero-halo mx-auto mb-2">
                                <div class="clockout-hero-inner">
                                    <i class="mdi mdi-party-popper fs-2"></i>
                                </div>
                            </div>

                            <h4 class="modal-title fw-bold text-dark mb-1" id="modalClockOutGreetingTitle" style="letter-spacing: -0.3px;">
                                {{ $navGreetingTitle }}
                            </h4>
                            <p class="text-muted small mb-0 px-2" style="line-height: 1.55;">
                                Jam operasional kantor hari ini telah selesai. Terima kasih atas dedikasi dan kerja keras Anda hari ini!
                            </p>
                        </div>

                        <!-- Card 1: Structured Detail Card -->
                        <div class="clockout-detail-card mb-3">
                            <div class="clockout-card-header d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2 text-truncate pe-2">
                                    @if (Auth::user()->image && file_exists(public_path(Auth::user()->image)))
                                        <img src="{{ asset(Auth::user()->image) }}" class="rounded-circle flex-shrink-0" style="width: 28px; height: 28px; object-fit: cover;" alt="{{ Auth::user()->name }}">
                                    @else
                                        <span class="badge rounded-circle p-2 bg-label-warning flex-shrink-0" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="mdi mdi-account-circle-outline fs-6"></i>
                                        </span>
                                    @endif
                                    <span class="fw-bold text-dark text-truncate" style="font-size: 0.9rem;" title="{{ Auth::user()->name }}">
                                        {{ Auth::user()->name }}
                                    </span>
                                </div>
                                <span class="text-muted flex-shrink-0" style="font-size: 0.76rem;">
                                    <i class="mdi mdi-calendar-today me-1"></i>{{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('D MMM Y') }}
                                </span>
                            </div>
                            <div class="clockout-card-body">
                                <div class="clockout-info-row">
                                    <span class="clockout-info-label">
                                        <i class="mdi mdi-badge-account-horizontal-outline text-primary fs-6"></i> Jabatan / Divisi
                                    </span>
                                    <span class="clockout-info-val text-truncate">
                                        {{ $navUserEmployee->position->name ?? ($navUserEmployee->department->name ?? (Auth::user()->role ?? 'Staff')) }}
                                    </span>
                                </div>
                                <div class="clockout-info-row">
                                    <span class="clockout-info-label">
                                        <i class="mdi mdi-clock-check-outline text-success fs-6"></i> Presensi Hari Ini
                                    </span>
                                    <span class="clockout-info-val">
                                        @if ($navHasClockedOut)
                                            <span class="badge bg-label-success fw-bold font-11"><i class="mdi mdi-check-all me-1"></i>Pulang: {{ substr($navUserAttToday->clock_out, 0, 5) }} WIB</span>
                                        @elseif ($navHasClockedIn)
                                            <span class="badge bg-label-warning text-dark fw-bold font-11"><i class="mdi mdi-clock-alert-outline me-1"></i>Masuk: {{ substr($navUserAttToday->clock_in, 0, 5) }} (Belum Pulang)</span>
                                        @else
                                            <span class="badge bg-label-secondary font-11">Tidak Ada Presensi</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="clockout-info-row">
                                    <span class="clockout-info-label">
                                        <i class="mdi mdi-format-quote-open text-warning fs-6"></i> Pesan Apresiasi
                                    </span>
                                    <span class="clockout-info-val text-muted text-start font-12 fw-normal" id="modalClockOutGreetingMessage" style="max-width: 65%; line-height: 1.45;">
                                        {{ $navGreetingMessage }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Guidance Notice -->
                        <div class="clockout-guide-card d-flex align-items-center gap-2 mb-3">
                            <i class="mdi mdi-shield-heart text-danger fs-5 flex-shrink-0"></i>
                            <div class="small text-dark" style="font-size: 0.8rem; line-height: 1.45;">
                                Hati-hati di perjalanan pulang, utamakan keselamatan diri, dan nikmati waktu istirahat berkualitas bersama keluarga tercinta.
                            </div>
                        </div>

                        <!-- Actions Footer -->
                        <div class="d-flex align-items-center gap-2 pt-1">
                            @if ($navUserEmployee && $navUserEmployee->can_online_attendance && $navHasClockedIn && !$navHasClockedOut)
                                <button type="button" class="btn btn-dismiss-clockout flex-grow-1" data-bs-dismiss="modal">
                                    Tutup
                                </button>
                                <button type="button" class="btn btn-action-clockout flex-grow-1 d-flex align-items-center justify-content-center gap-1.5" onclick="triggerClockOutFromGreeting()">
                                    <i class="mdi mdi-clock-out fs-5"></i>
                                    <span>Clock Out Sekarang</span>
                                </button>
                            @else
                                <button type="button" class="btn btn-action-clockout w-100 d-flex align-items-center justify-content-center gap-1.5" data-bs-dismiss="modal">
                                    <i class="mdi mdi-check-circle-outline fs-5"></i>
                                    <span>Terima Kasih &amp; Selamat Beristirahat</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function triggerClockOutFromGreeting() {
                var greetingEl = document.getElementById('modalClockOutGreeting');
                if (greetingEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var greetingInstance = bootstrap.Modal.getInstance(greetingEl);
                    if (greetingInstance) greetingInstance.hide();
                }

                setTimeout(function() {
                    var clockOutModalEl = document.getElementById('navClockOutModal');
                    if (clockOutModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var outInstance = bootstrap.Modal.getInstance(clockOutModalEl) || new bootstrap.Modal(clockOutModalEl);
                        outInstance.show();
                    }
                }, 400);
            }

            document.addEventListener('DOMContentLoaded', function() {
                @if ($navIsAfterClockOutTime)
                    (function() {
                        var greetingKey = 'clock_out_greeting_shown_{{ Auth::id() }}_{{ $navTodayDateKey }}';
                        var hasShownGreeting = sessionStorage.getItem(greetingKey);

                        if (!hasShownGreeting) {
                            sessionStorage.setItem(greetingKey, '1');

                            var triggerGreetingModal = function() {
                                setTimeout(function() {
                                    // Collision check: do not interrupt open modals or print/sign routes
                                    if (document.querySelector('.modal.show')) {
                                        return;
                                    }
                                    var path = window.location.pathname;
                                    if (path.includes('/print') || path.includes('/sign/') || path.includes('/under-maintenance')) {
                                        return;
                                    }

                                    var greetingEl = document.getElementById('modalClockOutGreeting');
                                    if (greetingEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                        var modalInstance = bootstrap.Modal.getInstance(greetingEl) || new bootstrap.Modal(greetingEl);
                                        modalInstance.show();
                                    }
                                }, 900);
                            };

                            if (document.readyState === 'complete') {
                                triggerGreetingModal();
                            } else {
                                window.addEventListener('load', triggerGreetingModal);
                            }
                        }
                    })();
                @endif
            });
        </script>
    @endif
@endif

@if (Auth::check())
    @php
        $navQaCategorized = \App\Services\QuickActionService::getCategorizedCatalogForUser(Auth::user());
        $navQaUserActions = \App\Services\QuickActionService::getUserQuickActions(Auth::user());
        $navQaDefaultIds  = \App\Services\QuickActionService::getDefaultActionIdsForRole(Auth::user()?->role);
    @endphp

    <!-- Modal Quick Action Settings -->
    <div class="modal fade" id="quickActionSettingModal" tabindex="-1" aria-labelledby="quickActionSettingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div style="height: 4px; background: linear-gradient(90deg, #696cff 0%, #03c3ec 100%); width: 100%;"></div>
                
                <div class="modal-header border-bottom py-3 px-4 bg-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-sm flex-shrink-0">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                <i class="mdi mdi-tune-variant"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-heading mb-0" id="quickActionSettingModalLabel">
                                Atur Quick Action Saya
                            </h5>
                            <small class="text-muted">Pilih dari menu aplikasi atau <strong>buat direct link custom sendiri</strong> (maksimal 4 slot).</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span id="qaCounterBadge" class="badge bg-label-primary rounded-pill px-3 py-2 fw-bold" style="font-size: 0.8rem;">
                            <i class="mdi mdi-check-circle-outline me-1"></i><span id="qaSelectedCount">0</span> / 4 Slot Terisi
                        </span>
                        <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <div class="modal-body p-4">
                    <!-- Live Active 4-Slots Preview Bar -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-heading text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.5px;">
                                <i class="mdi mdi-view-grid-plus-outline text-primary me-1"></i>Pintasan Aktif Navbar (4 Slot)
                            </span>
                            <button type="button" id="qaResetDefaultBtn" class="btn btn-xs btn-label-secondary d-flex align-items-center">
                                <i class="mdi mdi-restore me-1"></i> Reset Default Role
                            </button>
                        </div>
                        <div class="row g-2" id="qaSlotsPreviewContainer">
                            <!-- Injected by JavaScript -->
                        </div>
                    </div>

                    <!-- Navigation Tabs: Menu Catalog vs Custom Link -->
                    <ul class="nav nav-pills nav-fill mb-3 border rounded-3 p-1 bg-body-tertiary" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active py-2 fw-semibold d-flex align-items-center justify-content-center" id="tab-catalog-btn" data-bs-toggle="pill" data-bs-target="#tab-catalog-pane" type="button" role="tab" aria-selected="true">
                                <i class="mdi mdi-format-list-checks me-1 fs-5"></i> Pilih Menu Aplikasi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-2 fw-semibold d-flex align-items-center justify-content-center" id="tab-custom-btn" data-bs-toggle="pill" data-bs-target="#tab-custom-pane" type="button" role="tab" aria-selected="false">
                                <i class="mdi mdi-link-plus me-1 fs-5"></i> Buat Direct Link Custom
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content border-0 p-0">
                        <!-- TAB 1: MENU CATALOG -->
                        <div class="tab-pane fade show active" id="tab-catalog-pane" role="tabpanel" tabindex="0">
                            <!-- Search & Quick Filter Bar -->
                            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                                <div class="input-group input-group-merge shadow-xs flex-grow-1">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="mdi mdi-magnify text-muted"></i>
                                    </span>
                                    <input type="text" id="qaSearchInput" class="form-control border-start-0 ps-0" placeholder="Cari nama menu / halaman..." autocomplete="off">
                                </div>
                            </div>

                            <div class="qa-categories-container">
                                @foreach ($navQaCategorized as $category => $items)
                                    <div class="qa-category-group mb-4" data-category="{{ strtolower($category) }}">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="fw-bold text-heading mb-0 text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                                                <i class="mdi mdi-circle-small text-primary me-1"></i>{{ $category }}
                                            </h6>
                                            <span class="badge bg-label-secondary rounded-pill" style="font-size: 0.7rem;">{{ count($items) }} Menu</span>
                                        </div>

                                        <div class="row g-2">
                                            @foreach ($items as $item)
                                                <div class="col-md-6 col-12 qa-item-col" data-search="{{ strtolower($item['title'] . ' ' . $item['subtitle'] . ' ' . $category) }}">
                                                    <label class="qa-card-option d-flex align-items-center p-2 rounded-3 border w-100 cursor-pointer transition bg-body" id="qa_card_{{ $item['id'] }}" for="qa_check_{{ $item['id'] }}">
                                                        <div class="form-check me-2 ms-1 mb-0">
                                                            <input class="form-check-input qa-checkbox" 
                                                                   type="checkbox" 
                                                                   value="{{ $item['id'] }}" 
                                                                   id="qa_check_{{ $item['id'] }}"
                                                                   data-title="{{ $item['title'] }}"
                                                                   data-subtitle="{{ $item['subtitle'] }}"
                                                                   data-icon="{{ $item['icon'] }}"
                                                                   data-iconbg="{{ $item['icon_bg'] }}"
                                                                   data-url="{{ $item['url'] }}">
                                                        </div>
                                                        <div class="avatar avatar-sm me-2 flex-shrink-0">
                                                            <span class="avatar-initial rounded-circle {{ $item['icon_bg'] }}">
                                                                <i class="{{ $item['icon'] }}"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-grow-1 text-truncate">
                                                            <span class="fw-semibold text-heading d-block text-truncate" style="font-size: 0.86rem;">
                                                                {{ $item['title'] }}
                                                            </span>
                                                            <small class="text-muted d-block text-truncate" style="font-size: 0.74rem;">
                                                                {{ $item['subtitle'] }}
                                                            </small>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- TAB 2: CUSTOM DIRECT LINK FORM -->
                        <div class="tab-pane fade" id="tab-custom-pane" role="tabpanel" tabindex="0">
                            <div class="card border border-dashed rounded-3 p-3 mb-3 bg-body">
                                <h6 class="fw-bold text-heading mb-3 d-flex align-items-center">
                                    <i class="mdi mdi-plus-circle text-primary me-2"></i>Tambah Direct Link Custom
                                </h6>

                                <div class="row g-3">
                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small">Nama Shortcut <span class="text-danger">*</span></label>
                                        <input type="text" id="customQaTitle" class="form-control" placeholder="Contoh: Monitoring PO Urgent" maxlength="40">
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small">Keterangan / Subtitle</label>
                                        <input type="text" id="customQaSubtitle" class="form-control" placeholder="Contoh: Akses Cepat SUO" maxlength="40">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Direct URL / Path Tujuan <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-tertiary"><i class="mdi mdi-link-variant"></i></span>
                                            <input type="text" id="customQaUrl" class="form-control" placeholder="Contoh: /sales-order?tab=urgent atau https://...">
                                        </div>
                                        <small class="text-muted" style="font-size: 0.74rem;">Bisa berupa path internal (misal: <code>/product-out</code>) atau URL lengkap.</small>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small">Pilihan Icon</label>
                                        <select id="customQaIcon" class="form-select">
                                            <option value="mdi mdi-link-variant">🔗 Link Default (mdi-link-variant)</option>
                                            <option value="mdi mdi-star-outline">⭐ Bintang / Favorit (mdi-star-outline)</option>
                                            <option value="mdi mdi-lightning-bolt-outline">⚡ Petir / Urgent (mdi-lightning-bolt-outline)</option>
                                            <option value="mdi mdi-file-document-outline">📄 Dokumen (mdi-file-document-outline)</option>
                                            <option value="mdi mdi-chart-line">📈 Grafik &amp; Laporan (mdi-chart-line)</option>
                                            <option value="mdi mdi-cart-outline">🛒 Keranjang &amp; Order (mdi-cart-outline)</option>
                                            <option value="mdi mdi-package-variant-closed">📦 Barang &amp; Stok (mdi-package-variant-closed)</option>
                                            <option value="mdi mdi-tools">🛠️ Tool &amp; Servis (mdi-tools)</option>
                                            <option value="mdi mdi-cash-multiple">💵 Keuangan &amp; Kas (mdi-cash-multiple)</option>
                                            <option value="mdi mdi-calendar-clock">🕒 Jadwal &amp; Waktu (mdi-calendar-clock)</option>
                                            <option value="mdi mdi-account-group-outline">👥 Pelanggan / User (mdi-account-group-outline)</option>
                                            <option value="mdi mdi-open-in-new">↗️ Link Eksternal (mdi-open-in-new)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small">Warna Badge</label>
                                        <select id="customQaColor" class="form-select">
                                            <option value="bg-label-primary">Primary (Ungu/Biru)</option>
                                            <option value="bg-label-success">Success (Hijau)</option>
                                            <option value="bg-label-warning">Warning (Oranye/Kuning)</option>
                                            <option value="bg-label-info">Info (Cyan)</option>
                                            <option value="bg-label-danger">Danger (Merah)</option>
                                            <option value="bg-label-secondary">Secondary (Abu-abu)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="button" id="btnAddCustomLink" class="btn btn-primary d-inline-flex align-items-center">
                                            <i class="mdi mdi-plus-circle-outline me-1"></i> Tambahkan ke Slot Shortcut
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-2 px-4 bg-body d-flex align-items-center justify-content-between">
                    <small class="text-muted" id="qaFooterHint">Pilih atau buat maksimal 4 shortcut.</small>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" id="btnSaveQuickActions" class="btn btn-primary px-4">
                            <i class="mdi mdi-content-save-outline me-1"></i> Simpan Shortcut
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .qa-card-option {
            transition: all 0.18s ease-in-out;
            cursor: pointer;
            user-select: none;
        }
        .qa-card-option:hover {
            border-color: rgba(105, 108, 255, 0.4) !important;
            transform: translateY(-1px);
        }
        .qa-card-option.selected {
            border-color: #696cff !important;
            background-color: rgba(105, 108, 255, 0.08) !important;
        }
        html.dark-style .qa-card-option.selected {
            background-color: rgba(105, 108, 255, 0.16) !important;
            border-color: #696cff !important;
        }
        .qa-slot-card {
            border-radius: 10px;
            border: 1px dashed rgba(67, 89, 113, 0.2);
            padding: 8px 12px;
            min-height: 58px;
            display: flex;
            align-items: center;
            background: #fdfdfd;
            transition: all 0.15s ease;
        }
        html.dark-style .qa-slot-card {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.12);
        }
        .qa-slot-card.filled {
            border-style: solid;
            border-color: rgba(105, 108, 255, 0.35);
            background: #ffffff;
            box-shadow: 0 2px 5px rgba(67, 89, 113, 0.05);
        }
        html.dark-style .qa-slot-card.filled {
            background: #2b2c40;
            border-color: rgba(105, 108, 255, 0.4);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var defaultActionIds = @json($navQaDefaultIds);
            var initialUserActions = @json($navQaUserActions);
            var updateUrl = "{{ route('user.quick-actions.update') }}";
            var csrfToken = "{{ csrf_token() }}";
            var maxItems = 4;

            // In-memory array of selected items: strings or custom objects
            var selectedItems = [];
            if (Array.isArray(initialUserActions)) {
                selectedItems = initialUserActions.slice(0, maxItems).map(function(item) {
                    if (item.type === 'custom') {
                        return {
                            type: 'custom',
                            title: item.title,
                            subtitle: item.subtitle,
                            url: item.raw_url || item.url,
                            icon: item.icon,
                            icon_bg: item.icon_bg
                        };
                    } else {
                        return item.id;
                    }
                });
            }

            var checkboxes = document.querySelectorAll('.qa-checkbox');
            var counterEl = document.getElementById('qaSelectedCount');
            var counterBadge = document.getElementById('qaCounterBadge');
            var slotsContainer = document.getElementById('qaSlotsPreviewContainer');
            var searchInput = document.getElementById('qaSearchInput');
            var resetBtn = document.getElementById('qaResetDefaultBtn');
            var saveBtn = document.getElementById('btnSaveQuickActions');
            var addCustomBtn = document.getElementById('btnAddCustomLink');

            // Render 4 Slot Preview Cards and sync checkboxes
            function renderSlots() {
                if (counterEl) counterEl.textContent = selectedItems.length;

                if (counterBadge) {
                    if (selectedItems.length === maxItems) {
                        counterBadge.className = 'badge bg-success rounded-pill px-3 py-2 fw-bold';
                    } else if (selectedItems.length > 0) {
                        counterBadge.className = 'badge bg-label-primary rounded-pill px-3 py-2 fw-bold';
                    } else {
                        counterBadge.className = 'badge bg-label-danger rounded-pill px-3 py-2 fw-bold';
                    }
                }

                // Sync catalog checkboxes & card styling
                checkboxes.forEach(function (cb) {
                    var isSelected = selectedItems.some(function(it) {
                        return typeof it === 'string' && it === cb.value;
                    });
                    cb.checked = isSelected;
                    var card = document.getElementById('qa_card_' + cb.value);
                    if (card) {
                        if (isSelected) {
                            card.classList.add('selected', 'border-primary', 'bg-label-primary');
                        } else {
                            card.classList.remove('selected', 'border-primary', 'bg-label-primary');
                        }
                    }
                });

                // Render Slot Cards (1 to 4)
                if (slotsContainer) {
                    var html = '';
                    for (var i = 0; i < maxItems; i++) {
                        var item = selectedItems[i];
                        if (item) {
                            var title = '';
                            var subtitle = '';
                            var icon = 'mdi mdi-link-variant';
                            var iconBg = 'bg-label-primary';
                            var isCustom = typeof item === 'object' && item.type === 'custom';

                            if (isCustom) {
                                title = item.title;
                                subtitle = item.subtitle || 'Direct Link';
                                icon = item.icon || icon;
                                iconBg = item.icon_bg || iconBg;
                            } else {
                                var cb = document.getElementById('qa_check_' + item);
                                if (cb) {
                                    title = cb.getAttribute('data-title') || item;
                                    subtitle = cb.getAttribute('data-subtitle') || '';
                                    icon = cb.getAttribute('data-icon') || icon;
                                    iconBg = cb.getAttribute('data-iconbg') || iconBg;
                                } else {
                                    title = item;
                                    subtitle = 'Menu';
                                }
                            }

                            html += '<div class="col-md-6 col-12">' +
                                '<div class="qa-slot-card filled d-flex align-items-center justify-content-between gap-2">' +
                                    '<div class="d-flex align-items-center gap-2 text-truncate">' +
                                        '<div class="avatar avatar-xs flex-shrink-0">' +
                                            '<span class="avatar-initial rounded-circle ' + iconBg + '">' +
                                                '<i class="' + icon + '"></i>' +
                                            '</span>' +
                                        '</div>' +
                                        '<div class="text-truncate">' +
                                            '<span class="fw-bold text-heading d-block text-truncate" style="font-size: 0.82rem;">' + title + '</span>' +
                                            '<small class="text-muted d-block text-truncate" style="font-size: 0.72rem;">' +
                                                (isCustom ? '<span class="badge bg-label-warning px-1 py-0 me-1" style="font-size: 0.65rem;">Direct Link</span>' : '') +
                                                subtitle +
                                            '</small>' +
                                        '</div>' +
                                    '</div>' +
                                    '<button type="button" class="btn btn-icon btn-xs btn-label-danger rounded-circle flex-shrink-0 shadow-none qa-remove-slot-btn" data-index="' + i + '" title="Hapus dari slot">' +
                                        '<i class="mdi mdi-close fs-6"></i>' +
                                    '</button>' +
                                '</div>' +
                            '</div>';
                        } else {
                            html += '<div class="col-md-6 col-12">' +
                                '<div class="qa-slot-card text-muted d-flex align-items-center justify-content-center gap-2">' +
                                    '<i class="mdi mdi-plus-circle-outline opacity-50"></i>' +
                                    '<span class="small opacity-75">Slot ' + (i + 1) + ' Kosong</span>' +
                                '</div>' +
                            '</div>';
                        }
                    }
                    slotsContainer.innerHTML = html;

                    // Bind remove buttons
                    slotsContainer.querySelectorAll('.qa-remove-slot-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var idx = parseInt(btn.getAttribute('data-index'), 10);
                            if (!isNaN(idx) && idx >= 0 && idx < selectedItems.length) {
                                selectedItems.splice(idx, 1);
                                renderSlots();
                            }
                        });
                    });
                }
            }

            // Checkbox change listener
            checkboxes.forEach(function (cb) {
                cb.addEventListener('change', function () {
                    var val = cb.value;
                    if (cb.checked) {
                        if (selectedItems.length >= maxItems) {
                            cb.checked = false;
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Maksimal 4 Slot',
                                    text: 'Slot penuh! Hapus salah satu shortcut pada preview di atas terlebih dahulu.',
                                    timer: 2500,
                                    showConfirmButton: false
                                });
                            } else {
                                alert('Maksimal 4 shortcut dapat dipilih!');
                            }
                            return;
                        }
                        if (!selectedItems.includes(val)) {
                            selectedItems.push(val);
                        }
                    } else {
                        selectedItems = selectedItems.filter(function(it) {
                            return it !== val;
                        });
                    }
                    renderSlots();
                });
            });

            // Add Custom Direct Link
            if (addCustomBtn) {
                addCustomBtn.addEventListener('click', function () {
                    if (selectedItems.length >= maxItems) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Slot Penuh (4 / 4)',
                                text: 'Slot sudah mencapai batas 4 item. Hapus salah satu shortcut terlebih dahulu untuk menambah link baru.'
                            });
                        } else {
                            alert('Maksimal 4 shortcut!');
                        }
                        return;
                    }

                    var titleInput = document.getElementById('customQaTitle');
                    var subtitleInput = document.getElementById('customQaSubtitle');
                    var urlInput = document.getElementById('customQaUrl');
                    var iconSelect = document.getElementById('customQaIcon');
                    var colorSelect = document.getElementById('customQaColor');

                    var title = titleInput ? titleInput.value.trim() : '';
                    var subtitle = subtitleInput ? subtitleInput.value.trim() : '';
                    var url = urlInput ? urlInput.value.trim() : '';
                    var icon = iconSelect ? iconSelect.value : 'mdi mdi-link-variant';
                    var iconBg = colorSelect ? colorSelect.value : 'bg-label-primary';

                    if (!title) {
                        if (titleInput) titleInput.focus();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'warning', title: 'Nama Wajib Diisi', text: 'Silakan isi nama shortcut custom Anda.' });
                        }
                        return;
                    }

                    if (!url) {
                        if (urlInput) urlInput.focus();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'warning', title: 'URL Wajib Diisi', text: 'Silakan masukkan link/URL tujuan.' });
                        }
                        return;
                    }

                    // Add to selected items
                    selectedItems.push({
                        type: 'custom',
                        title: title,
                        subtitle: subtitle || 'Direct Link',
                        url: url,
                        icon: icon,
                        icon_bg: iconBg
                    });

                    // Clear inputs
                    if (titleInput) titleInput.value = '';
                    if (subtitleInput) subtitleInput.value = '';
                    if (urlInput) urlInput.value = '';

                    renderSlots();

                    // Switch back to tab 1 or show toast
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Link Ditambahkan!',
                            text: '"' + title + '" berhasil dimasukkan ke daftar shortcut.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            }

            // Live Search Filter
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    var q = searchInput.value.trim().toLowerCase();
                    var itemCols = document.querySelectorAll('.qa-item-col');
                    var categoryGroups = document.querySelectorAll('.qa-category-group');

                    itemCols.forEach(function (col) {
                        var searchMeta = col.getAttribute('data-search') || '';
                        if (!q || searchMeta.indexOf(q) !== -1) {
                            col.style.display = '';
                        } else {
                            col.style.display = 'none';
                        }
                    });

                    categoryGroups.forEach(function (group) {
                        var visibleCols = group.querySelectorAll('.qa-item-col:not([style*="display: none"])');
                        if (visibleCols.length > 0) {
                            group.style.display = '';
                        } else {
                            group.style.display = 'none';
                        }
                    });
                });
            }

            // Reset to Role Default
            if (resetBtn) {
                resetBtn.addEventListener('click', function () {
                    selectedItems = defaultActionIds.slice(0, maxItems);
                    renderSlots();

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Reset ke Default',
                            text: 'Pilihan shortcut telah disetel ulang sesuai default role Anda.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            }

            // Save via AJAX
            if (saveBtn) {
                saveBtn.addEventListener('click', function () {
                    if (selectedItems.length === 0) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Pilih / Buat Shortcut',
                                text: 'Silakan pilih minimal 1 menu atau direct link custom.'
                            });
                        } else {
                            alert('Silakan pilih minimal 1 menu shortcut.');
                        }
                        return;
                    }

                    var payload = selectedItems.slice(0, maxItems);

                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';

                    fetch(updateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            quick_actions: payload
                        })
                    })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="mdi mdi-content-save-outline me-1"></i> Simpan Shortcut';

                        if (data && data.success) {
                            // Update items in navbar dropdown live
                            var container = document.getElementById('qaNavbarItemsContainer');
                            if (container && data.quick_actions && Array.isArray(data.quick_actions)) {
                                var html = '';
                                data.quick_actions.forEach(function (item) {
                                    html += '<li>' +
                                        '<a class="dropdown-item d-flex align-items-center py-2" href="' + item.url + '">' +
                                            '<div class="avatar avatar-xs me-2 flex-shrink-0">' +
                                                '<span class="avatar-initial rounded-circle ' + item.icon_bg + '">' +
                                                    '<i class="' + item.icon + '"></i>' +
                                                '</span>' +
                                            '</div>' +
                                            '<div class="text-truncate">' +
                                                '<span class="fw-semibold d-block text-truncate">' + item.title + '</span>' +
                                                '<small class="text-muted d-block text-truncate">' + item.subtitle + '</small>' +
                                            '</div>' +
                                        '</a>' +
                                    '</li>';
                                });
                                container.innerHTML = html;
                            }

                            // Close modal
                            var modalEl = document.getElementById('quickActionSettingModal');
                            if (modalEl && typeof bootstrap !== 'undefined') {
                                var modalInstance = bootstrap.Modal.getInstance(modalEl);
                                if (modalInstance) modalInstance.hide();
                            }

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil Disimpan!',
                                    text: 'Shortcut Quick Action Anda telah diperbarui.',
                                    timer: 1800,
                                    showConfirmButton: false
                                });
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menyimpan',
                                    text: data.message || 'Terjadi kesalahan saat menyimpan Quick Action.'
                                });
                            } else {
                                alert(data.message || 'Gagal menyimpan.');
                            }
                        }
                    })
                    .catch(function (err) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="mdi mdi-content-save-outline me-1"></i> Simpan Shortcut';
                        console.error('Quick Action save error:', err);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Sistem',
                                text: 'Tidak dapat terhubung ke server.'
                            });
                        }
                    });
                });
            }

            renderSlots();
        });
    </script>
@endif


