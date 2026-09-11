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

            <!-- Style Switcher -->
            <li class="nav-item me-1 me-xl-0">
                <a class="nav-link btn btn-text-secondary rounded-pill btn-icon style-switcher-toggle hide-arrow"
                    href="javascript:void(0);">
                    <i class="mdi mdi-24px"></i>
                </a>
            </li>
            <!--/ Style Switcher -->
            <!-- Quick Action (Sales) -->
            @if (in_array(Auth::user()?->role, ['Sales', 'Sales Manager', 'Admin']))
                <li class="nav-item dropdown me-2 me-xl-1">
                    <a class="nav-link btn btn-text-primary rounded-pill btn-icon dropdown-toggle hide-arrow"
                        href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" title="Quick Action">
                        <i class="mdi mdi-plus-circle-outline mdi-24px text-primary"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end py-2 shadow-lg" style="min-width: 240px;">
                        <li class="dropdown-header d-flex align-items-center py-2 border-bottom mb-1">
                            <span class="fw-bold text-primary">
                                <i class="mdi mdi-lightning-bolt me-1"></i>Quick Action
                            </span>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('unit-quotation.create') }}">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        <i class="mdi mdi-file-document-plus-outline"></i>
                                    </span>
                                </div>
                                <div>
                                    <span class="fw-semibold d-block">Create Quote</span>
                                    <small class="text-muted">Smart Quote</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('leads.index') }}">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-success">
                                        <i class="mdi mdi-account-plus-outline"></i>
                                    </span>
                                </div>
                                <div>
                                    <span class="fw-semibold d-block">Create Leads</span>
                                    <small class="text-muted">Tambah Calon Pelanggan</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/product') }}">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-warning">
                                        <i class="mdi mdi-package-variant-closed"></i>
                                    </span>
                                </div>
                                <div>
                                    <span class="fw-semibold d-block">Stock Spare Part</span>
                                    <small class="text-muted">Cek Data Product & Stok</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/unit') }}">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-info">
                                        <i class="mdi mdi-air-conditioner"></i>
                                    </span>
                                </div>
                                <div>
                                    <span class="fw-semibold d-block">Unit Ready Stock</span>
                                    <small class="text-muted">Cek Unit Siap Ditawarkan</small>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <!-- Quick Action (Accounting) -->
            @if (in_array(Auth::user()?->role, ['Accounting', 'Finance Manager']))
                <li class="nav-item dropdown me-2 me-xl-1">
                    <a class="nav-link btn btn-text-primary rounded-pill btn-icon dropdown-toggle hide-arrow"
                        href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" title="Quick Action Accounting">
                        <i class="mdi mdi-plus-circle-outline mdi-24px text-primary"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end py-2 shadow-lg" style="min-width: 260px;">
                        <li class="dropdown-header d-flex align-items-center py-2 border-bottom mb-1">
                            <span class="fw-bold text-primary">
                                <i class="mdi mdi-lightning-bolt me-1"></i>Quick Action Accounting
                            </span>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('invoice.index') }}">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        <i class="mdi mdi-file-document-check-outline"></i>
                                    </span>
                                </div>
                                <div>
                                    <span class="fw-semibold d-block">Cek Invoice</span>
                                    <small class="text-muted">Daftar & Status Tagihan</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('kanban.monitoring-document') }}">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-info">
                                        <i class="mdi mdi-view-dashboard-outline"></i>
                                    </span>
                                </div>
                                <div>
                                    <span class="fw-semibold d-block">Mon. Document</span>
                                    <small class="text-muted">Monitoring Dokumen Penagihan</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('invoice.request') }}">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-warning">
                                        <i class="mdi mdi-file-clock-outline"></i>
                                    </span>
                                </div>
                                <div>
                                    <span class="fw-semibold d-block">Request Invoice</span>
                                    <small class="text-muted">Antrean Permintaan Invoice</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('payment_index.payment') }}">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-success">
                                        <i class="mdi mdi-cash-check"></i>
                                    </span>
                                </div>
                                <div>
                                    <span class="fw-semibold d-block">Payment Receipt</span>
                                    <small class="text-muted">Penerimaan Pembayaran</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('payment_index.aging') }}">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-danger">
                                        <i class="mdi mdi-calendar-clock"></i>
                                    </span>
                                </div>
                                <div>
                                    <span class="fw-semibold d-block text-dark">Invoice Aging</span>
                                    <small class="text-muted">Piutang Jatuh Tempo (AR)</small>
                                </div>
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
                        $unreadCommentCount = 0;
                        if (Auth::user()?->role != 'Admin' && @$unreadComment) {
                            $unreadCommentCount = $unreadComment->count();
                        }
                        $unreadProspectCount = $unreadCommentCount;
                        $prospectNotifications = collect();
                        $unreadProspectNotifCount = 0;
                        $unreadProspectCreatedCount = 0;
                        $unreadProspectAssignedCount = 0;
                        if (Auth::check()) {
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
                        $hasBadge = false;
                        if (Auth::user()?->role == 'Admin' && @$unreadCommentAdmin && $unreadCommentAdmin->count() >= 1) $hasBadge = true;
                        if ($unreadCommentCount >= 1) $hasBadge = true;
                        if ($unreadProspectNotifCount >= 1) $hasBadge = true;
                        if (@$prMentions && $prMentions->count() >= 1) $hasBadge = true;
                        if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']) && @$pendingCancelQuotes && $pendingCancelQuotes->count() >= 1) $hasBadge = true;
                        if (in_array(Auth::user()?->role, ['Accounting', 'Admin', 'Sales']) && @$paymentUnreadCount >= 1) $hasBadge = true;
                        if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']) && (@$apDueTodayCount >= 1 || @$apDueSoonCount >= 1 || @$apOverdueCount >= 1)) $hasBadge = true;
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
                                        if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']) && @$pendingCancelQuotes) $totalBadges += $pendingCancelQuotes->count();
                                        if (in_array(Auth::user()?->role, ['Accounting', 'Admin', 'Sales']) && @$paymentUnreadCount) $totalBadges += $paymentUnreadCount;
                                        if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']) && @$apDueTodayCount) $totalBadges += $apDueTodayCount;
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
                                @if ($unreadProspectNotifCount > 0)
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
                                @else
                                    @if ($unreadCommentCount > 0)
                                        <span id="commentUnreadCountBadge" class="badge rounded-pill bg-label-primary">{{ $unreadCommentCount }} Komentar Baru</span>
                                    @endif
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
                            if ($prospectNotifications && $prospectNotifications->count() > 0) {
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
                            if (Auth::user()?->role != 'Admin' && @$comment) {
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
                            if (Auth::check()) {
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
                            if (in_array(Auth::user()?->role, ['Accounting', 'Admin', 'Sales']) && @$paymentNotifications && $paymentNotifications->count() > 0) {
                                foreach ($paymentNotifications as $pn) {
                                    $unifiedNotifications->push([
                                        'type' => 'payment',
                                        'time' => \Carbon\Carbon::parse($pn->created_at),
                                        'item' => $pn,
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
                                                            {{ $isCreated ? 'Prospect Baru' : 'Ditugaskan' }}
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
                                                    <span class="badge bg-label-info notif-badge-pill">Komentar Prospect</span>
                                                    <span class="notif-time-ago">
                                                        <i class="mdi mdi-clock-outline fs-7"></i> {{ $date->diffInHours(\Carbon\Carbon::now()) > 24 ? $date->format('d M y') : $date->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <h6 class="notif-card-title">{{ $item->company ?? ('Prospect #' . $prospectId) }}</h6>
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
                                                $contract = \App\Models\Contract::where('id_unit_quotation', $pn->id_unit_quotation)->latest('id')->first();
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
                                <i class="mdi mdi-account-outline me-2"></i>
                                <span class="align-middle">My Profile</span>
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

                    // 3. Ubah semua kartu notifikasi unread menjadi read
                    document.querySelectorAll('.notif-card').forEach(function (card) {
                        card.classList.add('notif-card-read');
                        card.classList.remove('payment-notif-unread', 'notif-card-unread');
                        card.setAttribute('data-read', '1');
                        var dot = card.querySelector('.notif-unread-dot');
                        if (dot) dot.remove();
                    });

                    // 4. Tutup semua floating toast yang sedang muncul di layar
                    document.querySelectorAll('.prospect-floating-toast, .invoice-floating-toast').forEach(function (t) {
                        t.classList.add('toast-hiding');
                        setTimeout(function () { t.remove(); }, 300);
                    });

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
