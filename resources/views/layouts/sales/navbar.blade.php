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

            <!-- Presensi Harian Quick Widget -->
            @if (Auth::user() && Auth::user()->role !== 'Client')
                @php
                    $navEmp = Auth::user()->employee;
                    $navTodayAtt = null;
                    if ($navEmp && $navEmp->can_online_attendance) {
                        \App\Models\HrAttendance::processAutoClockOutIfDue();
                        $navTodayAtt = \App\Models\Hr\HrAttendance::where('employee_id', $navEmp->id)
                            ->whereDate('date', \Carbon\Carbon::today('Asia/Jakarta'))
                            ->first();
                    }
                @endphp
                @if ($navEmp && $navEmp->can_online_attendance)
                <li class="nav-item me-2">
                    @if (!$navTodayAtt || !$navTodayAtt->clock_in)
                        <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1 d-flex align-items-center shadow-xs" data-bs-toggle="modal" data-bs-target="#navClockInModal" title="Klik untuk Presensi Masuk (Clock In)">
                            <i class="mdi mdi-clock-in me-1"></i>
                            <span class="fw-bold d-none d-sm-inline">Clock In</span>
                        </button>
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
                            <a class="dropdown-item" href="{{ route('profile.show', Auth::user()?->id) }}">
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
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.show', Auth::user()?->id) }}">
                                <i class="mdi mdi-account-circle-outline me-2"></i>
                                <span class="align-middle">My Portal</span>
                            </a>
                        </li>
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
@if (Auth::user() && Auth::user()->employee && Auth::user()->employee->can_online_attendance)
    @php
        $navModalEmp = Auth::user()->employee;
        $navModalAtt = \App\Models\Hr\HrAttendance::where('employee_id', $navModalEmp->id)
            ->whereDate('date', \Carbon\Carbon::today())
            ->first();
        
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
    @endphp

    <!-- Modal Clock In -->
    <div class="modal fade" id="navClockInModal" tabindex="-1" aria-labelledby="navClockInModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div style="height: 4px; background: linear-gradient(90deg, #10b981 0%, #059669 100%); width: 100%;"></div>
                <form action="{{ route('hr.portal.clockin') }}" method="POST" id="navClockInForm">
                    @csrf
                    <input type="hidden" name="device_id" id="navClockInDeviceId">
                    <input type="hidden" name="device_info" id="navClockInDeviceInfo">
                    <input type="hidden" name="selfie_image" id="navClockInSelfieImage">

                    <div class="modal-header border-bottom py-3 bg-light">
                        <h5 class="modal-title fw-bold text-success d-flex align-items-center mb-0" id="navClockInModalLabel">
                            <i class="mdi mdi-clock-in me-2 fs-4"></i> Presensi Masuk (Clock In)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        
                        {{-- Alert Banner Validasi Keamanan (WiFi & Device Lock) --}}
                        <div id="navClockInAlertsContainer">
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
                            <div class="position-relative rounded-3 overflow-hidden mb-3 bg-dark shadow-sm border border-success" style="height: 220px;">
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
                        @else
                            <!-- Live Time & Date Display Standard -->
                            <div class="avatar avatar-xl mx-auto mb-3" style="width: 60px; height: 60px;">
                                <span class="avatar-initial rounded-circle bg-label-success">
                                    <i class="mdi mdi-clock-outline fs-1 text-success"></i>
                                </span>
                            </div>
                        @endif

                        <div class="font-monospace text-dark fw-bold fs-3 mb-1" id="navLiveClockTimeIn">
                            {{ \Carbon\Carbon::now()->format('H:i:s') }}
                        </div>
                        <div class="text-muted font-12 mb-3">
                            <i class="mdi mdi-calendar-blank-outline me-1"></i>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </div>

                        <!-- Employee Details Card -->
                        <div class="card bg-label-secondary border-0 p-3 text-start mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="font-11 text-muted text-uppercase fw-semibold">Karyawan</span>
                                <span class="badge bg-label-success font-10">Aktif WFO</span>
                            </div>
                            <div class="fw-bold text-dark font-14">{{ Auth::user()->name }}</div>
                            <div class="text-muted font-11">
                                NIK: {{ $navModalEmp->nik ?? '-' }} &bull; {{ $navModalEmp->position->name ?? ($navModalEmp->department->name ?? 'Staff') }}
                            </div>
                        </div>

                        <!-- Work Type selection (Segmented Button Switcher) -->
                        <div class="text-start mb-3">
                            <label class="form-label font-11 text-muted text-uppercase fw-semibold mb-1">Tipe Kehadiran</label>
                            <div class="btn-group w-100 shadow-xs" role="group" aria-label="Tipe Kehadiran">
                                <input type="radio" class="btn-check" name="work_type" id="navWorkTypeWfo" value="WFO" checked autocomplete="off">
                                <label class="btn btn-outline-primary py-2 d-flex align-items-center justify-content-center gap-1.5 fw-semibold" for="navWorkTypeWfo">
                                    <i class="mdi mdi-office-building fs-5"></i>
                                    <span>WFO</span>
                                </label>

                                <input type="radio" class="btn-check" name="work_type" id="navWorkTypeWfh" value="WFH" autocomplete="off">
                                <label class="btn btn-outline-primary py-2 d-flex align-items-center justify-content-center gap-1.5 fw-semibold" for="navWorkTypeWfh">
                                    <i class="mdi mdi-home-outline fs-5"></i>
                                    <span>WFH</span>
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-success d-flex align-items-center text-start font-11 py-2 px-3 mb-0">
                            <i class="mdi mdi-shield-check-outline fs-5 me-2 text-success flex-shrink-0"></i>
                            <span>
                                @if ($navIsSelfieRequired)
                                    Posisikan wajah Anda tepat di depan kamera. Foto akan diambil otomatis saat Anda menekan konfirmasi.
                                @else
                                    Presensi terproteksi keamanan IP &amp; Device Lock. Anda akan dialihkan ke <strong>Portal Mandiri</strong>.
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2 border-top">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success waves-effect waves-light" id="btnSubmitNavClockIn"
                                {{ ($navIsWifiRestrictionEnabled && !$navIsWifiVerified) ? 'disabled title="Jaringan WiFi kantor tidak sesuai"' : '' }}>
                            <i class="mdi mdi-clock-in me-1"></i> Clock In
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            // 1.5 Validasi Keamanan Pra-Clock In (WiFi & Device Lock)
            var isWifiBlockedInitially = {{ ($navIsWifiRestrictionEnabled && !$navIsWifiVerified) ? 'true' : 'false' }};
            function verifyClockInEligibility() {
                var btn = document.getElementById('btnSubmitNavClockIn');
                var deviceAlert = document.getElementById('navClockInDeviceAlert');
                var deviceAlertText = document.getElementById('navClockInDeviceAlertText');
                var wifiAlert = document.getElementById('navClockInWifiAlert');

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
                        if (data.type === 'device_error') {
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

            // 4. Loading state & Snapshot capture on submit
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

                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mencatat Presensi &amp; Membuka Portal...';
                    }
                });
            }

            var formOut = document.getElementById('navClockOutForm');
            if (formOut) {
                formOut.addEventListener('submit', function() {
                    var btn = document.getElementById('btnSubmitNavClockOut');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mencatat Clock Out &amp; Membuka Portal...';
                    }
                });
            }
        });
    </script>
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


