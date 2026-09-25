@php
    $formatSidebarBadge = function ($count) {
        $num = (int) ($count ?? 0);
        return $num > 99 ? '99+' : $num;
    };

    $hrPendingLeaveCount = 0;
    $hrPendingReimbursementCount = 0;
    if (Auth::check() && in_array(Auth::user()->role, ['Admin', 'Developer', 'Finance', 'Finance Manager', 'Accounting', 'Super Admin'])) {
        try {
            $hrPendingLeaveCount = \App\Models\HrLeaveRequest::where('status', 'Pending')->count();
            $hrPendingReimbursementCount = \App\Models\HrReimbursement::where('status', 'Pending')->count();
        } catch (\Throwable $e) {
            $hrPendingLeaveCount = 0;
            $hrPendingReimbursementCount = 0;
        }
    }
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                {{-- reftech --}}
                <img src="{{ asset('assets/img/favicon/logo-putih-app.png') }}"
                    alt="logo-reftech" width="45%">
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M11.4854 4.88844C11.0081 4.41121 10.2344 4.41121 9.75715 4.88844L4.51028 10.1353C4.03297 10.6126 4.03297 11.3865 4.51028 11.8638L9.75715 17.1107C10.2344 17.5879 11.0081 17.5879 11.4854 17.1107C11.9626 16.6334 11.9626 15.8597 11.4854 15.3824L7.96672 11.8638C7.48942 11.3865 7.48942 10.6126 7.96672 10.1353L11.4854 6.61667C11.9626 6.13943 11.9626 5.36568 11.4854 4.88844Z"
                    fill="currentColor" fill-opacity="0.6" />
                <path
                    d="M15.8683 4.88844L10.6214 10.1353C10.1441 10.6126 10.1441 11.3865 10.6214 11.8638L15.8683 17.1107C16.3455 17.5879 17.1192 17.5879 17.5965 17.1107C18.0737 16.6334 18.0737 15.8597 17.5965 15.3824L14.0778 11.8638C13.6005 11.3865 13.6005 10.6126 14.0778 10.1353L17.5965 6.61667C18.0737 6.13943 18.0737 5.36568 17.5965 4.88844C17.1192 4.41121 16.3455 4.41121 15.8683 4.88844Z"
                    fill="currentColor" fill-opacity="0.38" />
            </svg>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @if (in_array(Auth::user()?->role, ['Admin', 'Developer', 'Accounting']))
            <!-- Dashboards -->
            <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
                <a href="{{ url('/') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                    <div data-i18n="Dashboards">Dashboards</div>
                </a>
            </li>
            {{-- <li class="menu-item">
                <a href="#" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-phone-incoming-outgoing-outline"></i>
                    <div data-i18n="Activities">Activities</div>
                </a>
            </li> --}}
            {{-- <li class="menu-item {{ request()->is('reports') ? 'active' : '' }}">
                <a href="{{ url('/reports') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-finance"></i>
                    <div data-i18n="Reports">Reports</div>
                </a>
            </li> --}}
            <li class="menu-item {{ request()->is('report/monthly*') ? 'active' : '' }}">
                <a href="{{ route('report.monthly') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-finance"></i>
                    <div data-i18n="Reports">Reports</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('report/*') && !request()->is('report/monthly*') && !request()->is('report/project-profitability*') ? 'active' : '' }}">
                <a href="{{ route('report.current') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-chart-areaspline"></i>
                    <div data-i18n="Overview">Overview</div>
                </a>
            </li>
            <!-- Layouts -->
            @if (Auth::user()->role != 'Accounting')
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Sales & Marketing</span>
            </li>
            <li
                class="menu-item {{ request()->is('leads') || request()->is('leads/detail/*') || request()->is('existing') || request()->is('existing/*') || request()->is('ru') || request()->is('existing-bangkrupt') || request()->is('online-leads*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-account-group-outline"></i>
                    <div data-i18n="Client">Client</div>
                </a>

                <ul class="menu-sub">
                {{--    <li
                        class="menu-item {{ request()->is('leads') || request()->is('leads/detail/*') ? 'active' : '' }}">
                        <a href="{{ url('leads') }}" class="menu-link">
                            <div data-i18n="Leads">Leads</div>
                        </a>
                    </li> --}}
                    <li
                        class="menu-item {{ request()->is('leads-by-sales') ? 'active' : '' }}">
                        <a href="{{ route('index-sales.leads') }}" class="menu-link">
                            <div data-i18n="Leads By Sales">Leads By Sales</div>
                        </a>
                    </li>

                {{--<li
                        class="menu-item {{ request()->is('existing') || request()->is('existing/*') ? 'active' : '' }}">
                        <a href="{{ route('existing.index') }}" class="menu-link">
                            <div data-i18n="Customers">Customers</div>
                        </a>
                    </li> --}}
                    <li
                        class="menu-item {{ request()->is('customers-by-sales') ? 'active' : '' }}">
                        <a href="{{ route('index-sales.customers') }}" class="menu-link">
                            <div data-i18n="Customers By Sales">Customers By Sales</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('online-leads*') ? 'active' : '' }}">
                        <a href="{{ route('online-leads.index') }}" class="menu-link">
                            <div data-i18n="Leads Online">Leads Online</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ request()->is('quotation') || request()->is('quotation/*') || request()->is('po') || request()->is('loss') || request()->is('po/sales/*') ? 'active' : '' }}">
                <a href="{{ route('quotation.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-email-outline"></i>
                    <div data-i18n="Quotation">Quotation</div>
                </a>
            </li>

            @php
                $isMailboxConfigured = Auth::user()?->isDeveloper() || (!empty(Auth::user()?->mailSetting?->smtp_username));
                $unreadInboxCount = $isMailboxConfigured ? (Auth::user()?->mailboxMessages()->where('folder', 'inbox')->where('is_read', false)->count() ?? 0) : 0;
            @endphp
            @if ($isMailboxConfigured)
            <li class="menu-item {{ request()->is('sales/mailbox*') ? 'active' : '' }}">
                <a href="{{ route('sales.mailbox.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-email-fast-outline"></i>
                    <div data-i18n="Mailbox">Mailbox</div>
                    @if ($unreadInboxCount > 0)
                        <div class="badge bg-primary rounded-pill ms-auto">{{ $formatSidebarBadge($unreadInboxCount) }}</div>
                    @endif
                </a>
            </li>
            @endif



            {{-- <li class="menu-item {{ request()->is('unit-quotation') || request()->is('unit-quotation/*') ? 'active' : '' }}">
                <a href="{{ route('unit-quotation.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-document-outline"></i>
                    <div data-i18n="Penawaran Unit">Penawaran Unit</div>
                </a>
            </li> --}}

            <li
                class="menu-item {{ request()->is('prospect') || request()->is('prospect/*') || request()->is('prospect-quotation') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-account-details-outline"></i>
                    <div data-i18n="Marketing Leads">Marketing Leads</div>
                    @if (@$noSaleProspect >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($noSaleProspect) }}</div>
                    @endif
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('prospect') || request()->is('prospect/*') ? 'active' : '' }}">
                        <a href="{{ route('prospect.index') }}" class="menu-link">
                            <div data-i18n="Marketing Leads">Marketing Leads</div>
                            @if (@$noSaleProspect >= 1)
                                <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($noSaleProspect) }}</div>
                            @endif
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('prospect-quotation') ? 'active' : '' }}">
                        <a href="{{ route('quotation.prospect') }}" class="menu-link">
                            <div data-i18n="Quotation">Quotation</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ request()->is('forecast*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-chart-box-plus-outline"></i>
                    <div data-i18n="Forecast">Forecast</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('forecast') && !request()->has('view') ? 'active' : '' }}">
                        <a href="{{ route('forecast.index') }}" class="menu-link">
                            <div data-i18n="Dashboard Forecast">Dashboard Forecast</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('forecast/setup') ? 'active' : '' }}">
                        <a href="{{ route('forecast.setup') }}" class="menu-link">
                            <div data-i18n="Quick Setup Mesin">Quick Setup Mesin</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('forecast/contracts') ? 'active' : '' }}">
                        <a href="{{ route('forecast.contracts') }}" class="menu-link">
                            <div data-i18n="Jadwal Kontrak Servis">Jadwal Kontrak Servis</div>
                        </a>
                    </li>
                </ul>
            </li>


            @if (Auth::user()->role == 'Admin')
            <li class="menu-item {{ request()->is('sales-target') ? 'active' : '' }}">
                <a href="{{ route('sales-target.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-group-outline"></i>
                    <div data-i18n="Sales Management">Sales Management</div>
                </a>
            </li>
            @endif

            @endif

            {{-- <li class="menu-item {{ request()->is('visits/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-office-building-marker-outline"></i>
                    <div data-i18n="Visit">Visit</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('visits/leads') ? 'active' : '' }}">
                        <a href="{{ url('visits/leads') }}" class="menu-link">
                            <div data-i18n="Leads">Leads</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div data-i18n="Customer">Customer</div>
                        </a>
                    </li>
                </ul>
            </li> --}}
            {{-- <li
                class="menu-item {{ request()->is('service-reports') || request()->is('service-reports/*') ? 'active' : '' }}">
                <a href="{{ route('service-reports.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-chart-outline"></i>
                    <div data-i18n="Service Report">Service Report</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('service-manager') || request()->is('service-manager/*') ? 'active' : '' }}">
                <a href="{{ route('service-manager.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-chart-outline"></i>
                    <div data-i18n="Monitoring Fajar Paper">Monitoring Fajar Paper</div>
                </a>
            </li> --}}

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Sales Order</span>
            </li>
            @php $suoAccountingPending = $suoAccountingPending ?? \App\Models\Suo::where('status','confirmed')->whereNull('no_invoice_booking')->count(); @endphp
            <li class="menu-item {{ request()->is('suo-accounting') ? 'active' : '' }}">
                <a href="{{ route('suo.accounting.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lightning-bolt-outline"></i>
                    <div data-i18n="Urgent Order">Urgent Order (SUO)</div>
                    @if ($suoAccountingPending >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($suoAccountingPending) }}</div>
                    @endif
                </a>
            </li>
            <li class="menu-item {{ request()->is('sales-order') || request()->is('pending-po/*') || request()->is('project-monitoring*') ? 'active' : '' }}">
                <a href="{{ route('pending-po.sales-order') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                    <div data-i18n="Sales Order">Sales Order</div>
                </a>
            </li>
            {{-- <li class="menu-item {{ request()->is('new-order') ? 'active' : '' }}">
                <a href="{{ route('pending-po.order') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-cart-plus"></i>
                    <div data-i18n="New Order">New Order</div>
                    @if (@$newCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $newCount }}</div>
                    @endif
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('sales-order/list') || request()->is('sales-order/list') || request()->is('pending-po/*') || request()->is('pending-po-done') || request()->is('pending-po-project') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                    <div data-i18n="Sales Order">Sales Order</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('sales-order/list') ? 'active' : '' }}">
                        <a href="{{ route('pending-po.list') }}" class="menu-link">
                            <div data-i18n="List">List</div>
                            @if (@$listCount >= 1)
                                <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($listCount) }}</div>
                            @endif
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('sales-order/delivery') ? 'active' : '' }}">
                        <a href="{{ route('pending-po.delivery') }}" class="menu-link">
                            <div data-i18n="Delivery & Proccess">Delivery & Proccess</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('pending-po-done') ? 'active' : '' }}">
                        <a href="{{ route('pending-po.completed') }}" class="menu-link">
                            <div data-i18n="Completed">Completed</div>
                        </a>
                    </li>
                </ul>
            </li> --}}
            <li class="menu-item {{ request()->is('return') || request()->is('return/*') ? 'active' : '' }}">
                <a href="{{ route('return.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-archive-cancel"></i>
                    <div data-i18n="Return">Return</div>
                </a>
            </li>

            @if (in_array(Auth::user()?->role, ['Admin', 'developer', 'Developer']) || (method_exists(Auth::user(), 'isDeveloper') && Auth::user()->isDeveloper()))
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Project Management</span>
            </li>
            <li class="menu-item {{ request()->is('project-monitoring*') || (request()->is('sales-order') && request('tab') == 'project-monitoring') ? 'active' : '' }}">
                <a href="{{ route('pending-po.sales-order', ['tab' => 'project-monitoring']) }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-monitor-dashboard"></i>
                    <div data-i18n="Project Monitoring">Project Monitoring</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('kanban*') && !request()->is('accounting/monitoring-document*') ? 'active' : '' }}">
                <a href="{{ route('kanban.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-view-dashboard-outline"></i>
                    <div data-i18n="Project Kanban">Project Kanban</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('project-reports*') || (request()->is('service-reports*') && request('tab') == 'project') ? 'active' : '' }}">
                <a href="{{ route('service-reports.index', ['tab' => 'project']) }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-clipboard-text-clock-outline"></i>
                    <div data-i18n="Daily Project Report">Daily Project Report</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('bast') || request()->is('bast/*') ? 'active' : '' }}">
                <a href="{{ route('bast.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-sign"></i>
                    <div data-i18n="BAST">BAST (Serah Terima)</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('piping-rab*') || request()->is('piping-materials*') || request()->is('schematics*') || request()->is('hvac*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-compass-outline"></i>
                    <div data-i18n="Engineering & RAB">Engineering &amp; RAB</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('piping-rab*') ? 'active' : '' }}">
                        <a href="{{ route('piping-rab.index') }}" class="menu-link">
                            <div data-i18n="Estimasi / RAB Piping">Estimasi / RAB Piping</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('schematics*') ? 'active' : '' }}">
                        <a href="{{ route('schematics.index') }}" class="menu-link">
                            <div data-i18n="Schematic Diagram">Schematic Diagram</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hvac/quick-calculator*') ? 'active' : '' }}">
                        <a href="{{ route('hvac.quick-calculator') }}" class="menu-link">
                            <div data-i18n="HVAC Quick Estimator">HVAC Quick Estimator</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hvac/projects*') || request()->is('hvac/rooms*') ? 'active' : '' }}">
                        <a href="{{ route('hvac.project.index') }}" class="menu-link">
                            <div data-i18n="Daftar Proyek HVAC">Daftar Proyek HVAC</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hvac/master-catalog*') ? 'active' : '' }}">
                        <a href="{{ route('hvac.master.index') }}" class="menu-link">
                            <div data-i18n="Master Unit HVAC">Master Unit HVAC</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ request()->is('report/project-profitability*') || request()->is('payable/expenses*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-cash-multiple"></i>
                    <div data-i18n="Project Cost & Profit">Project Cost &amp; Profit</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('report/project-profitability*') ? 'active' : '' }}">
                        <a href="{{ route('report.project_profitability') }}" class="menu-link">
                            <div data-i18n="Laba Rugi Proyek">Laba Rugi Proyek</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('payable/expenses*') ? 'active' : '' }}">
                        <a href="{{ route('payable.expenses') }}" class="menu-link">
                            <div data-i18n="Biaya Proyek AP">Biaya Proyek AP</div>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            {{-- <li
                class="menu-item {{ request()->is('pending-po') || request()->is('pending-po/*') || request()->is('pending-po-done') || request()->is('pending-po-project') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-text-box-multiple"></i>
                    <div data-i18n="Pending PO">Pending PO</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('pending-po') ? 'active' : '' }}">
                        <a href="{{ route('pending-po.index') }}" class="menu-link">
                            <div data-i18n="Progress">Progress</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('pending-po-project') ? 'active' : '' }}">
                        <a href="{{ route('pending-po.index-project') }}" class="menu-link">
                            <div data-i18n="Project">Project</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('pending-po-done') ? 'active' : '' }}">
                        <a href="{{ route('pending-po.done') }}" class="menu-link">
                            <div data-i18n="Done">Done</div>
                        </a>
                    </li>
                </ul>
            </li> --}}

            @if (Auth::user()->role != 'Accounting')
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Service Departement</span>
            </li>

            {{-- Dinonaktifkan sementara 2026-08-02 atas permintaan user --}}
            {{-- <li class="menu-item {{ request()->is('monitoring-client/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-factory"></i>
                    <div data-i18n="Fajar Paper">Fajar Paper</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('monitoring-client/fajarPaper') ? 'active' : '' }}">
                        <a href="{{ route('monitoring.fajarPaper') }}" class="menu-link">
                            <div data-i18n="Daily Input">Daily Input</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('monitoring-client/fajarPaper-monitoring') ? 'active' : '' }}">
                        <a href="{{ route('monitoring.fajarPaper-monitoring') }}" class="menu-link">
                            <div data-i18n="Monitoring">Monitoring</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('monitoring-client/fajarPaper-service-report') ? 'active' : '' }}">
                        <a href="{{ route('monitoring.fajarPaper-service-report') }}" class="menu-link">
                            <div data-i18n="Service Report">Service Report</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('monitoring-client/fajarPaper-reports') ? 'active' : '' }}">
                        <a href="{{ route('monitoring.fajarPaper-reports') }}" class="menu-link">
                            <div data-i18n="Report">Report</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('po') || request()->is('po/sales/*') ? 'active' : '' }}">
                        <a href="{{ route('quotation.po') }}" class="menu-link">
                            <div data-i18n="Summary">Summary</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('monitoring-client/fajarPaper-archive') ? 'active' : '' }}">
                        <a href="{{ route('monitoring-arsip.fajarPaper') }}" class="menu-link">
                            <div data-i18n="Archived">Archived</div>
                        </a>
                    </li>
                </ul>
            </li> --}}
            {{-- <li
                class="menu-item {{ request()->is('service-manager-prokemas') || request()->is('service-manager-daily-prokemas/*/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-factory"></i>
                    <div data-i18n="Prokemas">Prokemas</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('service-manager-prokemas') || request()->is('service-manager-daily-prokemas/*/*') ? 'active' : '' }}">
                        <a href="{{ route('service-manager-prokemas.index') }}" class="menu-link">
                            <div data-i18n="Monitoring">Monitoring</div>
                        </a>
                    </li>
                </ul>
            </li> --}}
            <li class="menu-item {{ (request()->is('service-reports') || request()->is('service-reports/*')) && request('tab') != 'project' && !request()->is('project-reports*') ? 'active' : '' }}">
                <a href="{{ route('service-reports.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                    <div data-i18n="Service Report">Service Report</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('work-orders*') ? 'active' : '' }}">
                <a href="{{ route('work-orders.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-wrench-cog-outline"></i>
                    <div data-i18n="Work Order">Work Order</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('tool-assignment') || request()->is('tool-assignment/*') ? 'active' : '' }}">
                <a href="{{ route('tool-assignment.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-tools"></i>
                    <div data-i18n="Data Tools">Data Tools</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('tool-audit-verification') || request()->is('tool-audit-verification/*') || request()->is('tool-audit-summary') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-clipboard-check-outline"></i>
                    <div data-i18n="Audit Tools">Audit Tools</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('tool-audit-verification') || request()->is('tool-audit-verification/*') ? 'active' : '' }}">
                        <a href="{{ route('tool-audit-verification.index') }}" class="menu-link">
                            <div data-i18n="Verification">Verification</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('tool-audit-summary') ? 'active' : '' }}">
                        <a href="{{ route('tool-audit-summary.index') }}" class="menu-link">
                            <div data-i18n="Summary Audit Tools">Summary</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ request()->is('under-maintenance') ? 'active' : '' }}">
                <a href="{{ route('under-maintenance') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-car-wrench"></i>
                    <div data-i18n="Vehicle Management">Vehicle Management</div>
                </a>
            </li>
            @endif

            {{-- <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Marketting</span>
            </li> --}}

            @if (Auth::user()->id != 38)
                <li class="menu-header fw-light mt-4">
                    <span class="menu-header-text">Accounting</span>
                </li>
                <li class="menu-item {{ request()->is('key-accounts') ? 'active' : '' }}">
                    <a href="{{ route('key-accounts.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-star-outline"></i>
                        <div data-i18n="Key Accounts">Key Accounts</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('contract') || request()->is('selling/contract') || request()->is('order/contract') ? 'active' : '' }}">
                    <a href="{{ route('contract.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-book-check-outline"></i>
                        <div data-i18n="Selling Contract">Selling Contract</div>
                        @if (@$requestContract >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($requestContract) }}</div>
                        @endif
                    </a>
                </li>
                <li
                    class="menu-item {{ request()->is('invoice') || request()->is('invoice/*') || request()->is('request/invoice') || request()->is('request/invoice/*') || request()->is('index/invoice/kojisha') ? 'active' : '' }}">
                    <a href="{{ route('invoice.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-file-document-check-outline"></i>
                        <div data-i18n="Invoice">Invoice</div>
                        @if (@$requestInvoice >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($requestInvoice) }}</div>
                        @endif
                    </a>
                </li>
                <li class="menu-item {{ request()->is('delivery') || request()->is('delivery/*') ? 'active' : '' }}">
                    <a href="{{ route('delivery.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-truck-delivery-outline"></i>
                        <div data-i18n="Delivery Order">Delivery Order</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('bast') || request()->is('bast/*') ? 'active' : '' }}">
                    <a href="{{ route('bast.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-file-sign"></i>
                        <div data-i18n="BAST">BAST</div>
                    </a>
                </li>
                @php
                    if (!isset($monitoringCount)) {
                        $monitoringBoard = \App\Models\KanbanBoard::where('type', 'monitoring')->first();
                        $monitoringCount = 0;
                        if ($monitoringBoard) {
                            $monitoringCount = \App\Models\KanbanTask::where('board_id', $monitoringBoard->id)
                                ->whereIn('column_id', function($query) use ($monitoringBoard) {
                                    $query->select('id')
                                        ->from('kanban_columns')
                                        ->where('board_id', $monitoringBoard->id)
                                        ->whereIn('title', ['PO REFTECH', 'PO E-COMMERCE']);
                                })
                                ->count();
                        }
                    }
                @endphp
                <li class="menu-item {{ request()->is('accounting/monitoring-document*') ? 'active' : '' }}">
                    <a href="{{ route('kanban.monitoring-document') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-view-dashboard-outline"></i>
                        <div data-i18n="Monitoring Document">Monitoring Document</div>
                        @if ($monitoringCount >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($monitoringCount) }}</div>
                        @endif
                    </a>
                </li>

                <li class="menu-header fw-light mt-4">
                    <span class="menu-header-text">Account Receivable (AR)</span>
                </li>
                <li class="menu-item {{ request()->is('payment-index/invoice') || request()->is('payment-detail/invoice/*') ? 'active' : '' }}">
                    <a href="{{ route('payment_index.invoice') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-file-document-check-outline"></i>
                        <div data-i18n="Sales Invoice">Sales Invoice</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('payment-index/payment') || request()->is('payment-detail/payment/*') ? 'active' : '' }}">
                    <a href="{{ route('payment_index.payment') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-cash-check"></i>
                        <div data-i18n="Payment Receipt">Payment Receipt</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('payment-index/aging*') ? 'active' : '' }}">
                    <a href="{{ route('payment_index.aging') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-calendar-clock-outline"></i>
                        <div data-i18n="Aging Piutang">Aging Piutang</div>
                        @if (@$nodueCount >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($nodueCount) }}</div>
                        @endif
                    </a>
                </li>
                <li class="menu-item {{ request()->is('customer-statement*') ? 'active' : '' }}">
                    <a href="{{ route('customer.statement') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-book-open-page-variant-outline"></i>
                        <div data-i18n="Kartu Piutang (SOA)">Kartu Piutang (SOA)</div>
                    </a>
                </li>

                <li class="menu-header fw-light mt-4">
                    <span class="menu-header-text">Account Payable (AP)</span>
                </li>
                <li class="menu-item {{ request()->is('payable/invoice*') ? 'active' : '' }}">
                    <a href="{{ route('payable.index_invoice') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-receipt-text-outline"></i>
                        <div data-i18n="Purchase Invoice">Purchase Invoice</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('payable/gr-uninvoiced*') ? 'active' : '' }}">
                    <a href="{{ route('payable.gr_uninvoiced') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-truck-delivery-outline"></i>
                        <div data-i18n="GR Belum Ditagih">GR Belum Ditagih</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('payable/receipt*') ? 'active' : '' }}">
                    <a href="{{ route('payable.index_receipt') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-cash-fast"></i>
                        <div data-i18n="Purchase Payment">Purchase Payment</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('payable/aging*') ? 'active' : '' }}">
                    <a href="{{ route('payable.index_aging') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-calendar-alert"></i>
                        <div data-i18n="Aging Hutang">Aging Hutang</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('payable/statement*') ? 'active' : '' }}">
                    <a href="{{ route('payable.statement') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-book-account-outline"></i>
                        <div data-i18n="Kartu Hutang (SOA)">Kartu Hutang (SOA)</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('payable/expenses*') ? 'active' : '' }}">
                    <a href="{{ route('payable.expenses') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-file-table-box-outline"></i>
                        <div data-i18n="Biaya Proyek AP">Biaya Proyek AP</div>
                    </a>
                </li>


                <li class="menu-header fw-light mt-4">
                    <span class="menu-header-text">Finance</span>
                </li>

                <li class="menu-item {{ (request()->is('finance/bank') || request()->is('finance/bank/*') || request()->is('finance/bank-reconciliation*') || request()->is('finance/petty-cash*') || request()->is('finance/security*')) ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons mdi mdi-bank"></i>
                        <div data-i18n="Kas & Bank">Kas &amp; Bank</div>
                        <i class="mdi mdi-lock-outline text-muted ms-auto" style="font-size: 13px;" title="Terproteksi PIN"></i>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->is('finance/bank') || (request()->is('finance/bank/*') && !request()->is('finance/bank-reconciliation*')) ? 'active' : '' }}">
                            <a href="{{ route('bank.index') }}" class="menu-link finance-pin-trigger" data-target-name="Daftar Rekening Bank">
                                <div data-i18n="Daftar Rekening Bank">Daftar Rekening Bank</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('finance/bank-reconciliation*') ? 'active' : '' }}">
                            <a href="{{ route('finance.reconciliation.index') }}" class="menu-link finance-pin-trigger" data-target-name="Rekonsiliasi Bank">
                                <div data-i18n="Rekonsiliasi Bank">Rekonsiliasi Bank</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('finance/petty-cash*') ? 'active' : '' }}">
                            <a href="{{ route('petty_cash.index') }}" class="menu-link finance-pin-trigger" data-target-name="Petty Cash (Kas Kecil)">
                                <div data-i18n="Petty Cash">Petty Cash (Kas Kecil)</div>
                            </a>
                        </li>
                        @if(in_array(Auth::user()?->role, ['Finance Manager', 'Finance', 'Developer']) || Auth::user()?->isDeveloper())
                            <li class="menu-item {{ request()->is('finance/security*') ? 'active' : '' }}">
                                <a href="{{ route('finance.security.manage') }}" class="menu-link">
                                    <div data-i18n="Security">Security</div>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                @php
                    $pendingFeeCount = $pendingFeeCount ?? \App\Models\UnitQuotation::where('fee', '>', 0)
                        ->where('fee_payment_status', '!=', 'paid')
                        ->where('status', 'po_received')
                        ->count();
                @endphp
                <li class="menu-item {{ request()->is('finance/management-fee*') ? 'active' : '' }}">
                    <a href="{{ route('finance.management-fee.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-cash-refund"></i>
                        <div data-i18n="Management Fee">Management Fee</div>
                        @if ($pendingFeeCount >= 1)
                            <div class="badge bg-warning rounded-pill ms-auto">{{ $formatSidebarBadge($pendingFeeCount) }}</div>
                        @endif
                    </a>
                </li>

                <li class="menu-item {{ request()->is('finance/expense-budget*') ? 'active' : '' }}">
                    <a href="{{ route('finance.expense-budget.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-chart-donut"></i>
                        <div data-i18n="Annual Budget">Annual Budget</div>
                    </a>
                </li>

                <li
                    class="menu-item {{ request()->is('expense-account') || request()->is('expense') || request()->is('expense-umum') || request()->is('expense-inventory') || request()->is('expense-ongkir') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons mdi mdi-cash-multiple"></i>
                        <div data-i18n="Expense">Expense</div>
                        {{-- @if (@$nodueCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $nodueCount }}</div>
                    @endif --}}
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->is('expense-account') ? 'active' : '' }}">
                            <a href="{{ route('expense-account.index') }}" class="menu-link">
                                <div data-i18n="Account Database">Account Database</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('expense') ? 'active' : '' }}">
                            <a href="{{ route('expense.index') }}" class="menu-link">
                                <div data-i18n="Expense">Expense</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('expense-inventory') ? 'active' : '' }}">
                            <a href="{{ route('expense-inventory.index') }}" class="menu-link">
                                <div data-i18n="Inventory Adjusment">Inventory Adjusment</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('expense-umum') ? 'active' : '' }}">
                            <a href="{{ route('expense-umum.index') }}" class="menu-link">
                                <div data-i18n="Jurnal Umum">Jurnal Umum</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('expense-ongkir') ? 'active' : '' }}">
                            <a href="{{ route('expense-ongkir.index') }}" class="menu-link">
                                <div data-i18n="Ongkir Logistik">Ongkir Logistik</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item {{ (request()->is('statement*') || request()->is('income*') || request()->is('balance*') || request()->is('equity*') || request()->is('cashflow*')) ? 'active' : '' }}">
                    <a href="{{ route('finance.statement.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-book-open-outline"></i>
                        <div data-i18n="Statement">Statement</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('fixed*') ? 'active' : '' }}">
                    <a href="{{ route('fixed.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-domain"></i>
                        <div data-i18n="Fixed Asset">Fixed Asset</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('proyek-konstruksi*') ? 'active' : '' }}">
                    <a href="{{ route('proyek-konstruksi.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-hammer-wrench"></i>
                        <div data-i18n="Proyek Konstruksi">Proyek Konstruksi</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('report/project-profitability*') ? 'active' : '' }}">
                    <a href="{{ route('report.project_profitability') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-chart-box-outline"></i>
                        <div data-i18n="Laba Rugi Proyek">Laba Rugi Proyek</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('tool-finance') ? 'active' : '' }}">
                    <a href="{{ route('tool-finance.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-finance"></i>
                        <div data-i18n="Kelengkapan Data Finance Tools">Kelengkapan Data Finance Tools</div>
                    </a>
                </li>
            @endif

            {{-- <li
                class="menu-item {{ request()->is('payment-index/invoice') || request()->is('payment-index/invoice-ahmad') || request()->is('payment-index/invoice-rayi') || request()->is('payment-detail/invoice/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-book-check-outline"></i>
                    <div data-i18n="Sales Invoice">Sales Invoice</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('payment-index/invoice') || request()->is('payment-detail/invoice/*') ? 'active' : '' }}">
                        <a href="{{ route('payment_index.invoice') }}" class="menu-link">
                            <div data-i18n="General">General</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('payment-index/invoice-ahmad') || request()->is('payment-detail/invoice/*') ? 'active' : '' }}">
                        <a href="{{ route('payment_index.invoice_ahmad') }}" class="menu-link">
                            <div data-i18n="Yusuf">Yusuf</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('payment-index/invoice-rayi') || request()->is('payment-detail/invoice/*') ? 'active' : '' }}">
                        <a href="{{ route('payment_index.invoice_rayi') }}" class="menu-link">
                            <div data-i18n="Rayi">Rayi</div>
                        </a>
                    </li>
                </ul>
            </li> --}}

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">E-Stock</span>
            </li>

            {{-- Master Data --}}
            <li class="menu-item {{ request()->is('master/product') || request()->is('product') || request()->is('product/*') || request()->is('product-set') || request()->is('unit-acquisition') || request()->is('unit-acquisition/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-database-outline"></i>
                    <div data-i18n="Master Data">Master Data</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('master/product') ? 'active' : '' }}">
                        <a href="{{ route('master.product') }}" class="menu-link">
                            <div data-i18n="Product Master">Product Master</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('product') || request()->is('product/*') ? 'active' : '' }}">
                        <a href="{{ route('product.index') }}" class="menu-link">
                            <div data-i18n="Spare Part">Spare Part</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('product-set') ? 'active' : '' }}">
                        <a href="{{ route('product-set.index') }}" class="menu-link">
                            <div data-i18n="Product Set">Product Set</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('unit-acquisition') || request()->is('unit-acquisition/*') ? 'active' : '' }}">
                        <a href="{{ route('unit-acquisition.index') }}" class="menu-link">
                            <div data-i18n="Unit Acquisition">Unit Acquisition</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('work-orders*') ? 'active' : '' }}">
                        <a href="{{ route('work-orders.index') }}" class="menu-link">
                            <div data-i18n="Work Order">Work Order</div>
                        </a>
                    </li>

                </ul>
            </li>

            {{-- Stock Movement --}}
            <li class="menu-item {{ request()->is('product-in') || request()->is('product-in/*') || request()->is('product-out') || request()->is('product-out/*') || request()->is('change-warehouse') || request()->is('change-warehouse/*') || request()->is('unit-product-in') || request()->is('unit-product-in/*') || request()->is('unit-product-out') || request()->is('unit-product-out/*') || request()->is('warehouse/intercompany*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-swap-horizontal"></i>
                    <div data-i18n="Stock Movement">Stock Movement</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('product-in') || request()->is('product-in/*') ? 'active' : '' }}">
                        <a href="{{ route('product-in.index') }}" class="menu-link">
                            <div data-i18n="Product In">Product In</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('product-out') || request()->is('product-out/*') ? 'active' : '' }}">
                        <a href="{{ route('product-out.index') }}" class="menu-link">
                            <div data-i18n="Product Out">Product Out</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('warehouse/intercompany*') ? 'active' : '' }}">
                        <a href="{{ route('intercompany.index') }}" class="menu-link">
                            <div data-i18n="Rekap BK Kojisha">Rekap BK Kojisha</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('change-warehouse') || request()->is('change-warehouse/*') ? 'active' : '' }}">
                        <a href="{{ route('change-warehouse.index') }}" class="menu-link">
                            <div data-i18n="Warehouse Transfer">Warehouse Transfer</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('unit-product-in') || request()->is('unit-product-in/*') ? 'active' : '' }}">
                        <a href="{{ route('unit-product-in.index') }}" class="menu-link">
                            <div data-i18n="Barang Masuk Unit">Barang Masuk Unit</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('unit-product-out') || request()->is('unit-product-out/*') ? 'active' : '' }}">
                        <a href="{{ route('unit-product-out.index') }}" class="menu-link">
                            <div data-i18n="Unit Keluar">Unit Keluar</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Monitoring --}}
            <li class="menu-item {{ request()->is('stock') || request()->is('stock/*') || request()->is('stock-opname') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-clipboard-list-outline"></i>
                    <div data-i18n="Monitoring">Monitoring</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('stock') || request()->is('stock/*') ? 'active' : '' }}">
                        <a href="{{ route('stock.index') }}" class="menu-link">
                            <div data-i18n="Current Stock">Current Stock</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('stock-opname') ? 'active' : '' }}">
                        <a href="{{ route('opname.index') }}" class="menu-link">
                            <div data-i18n="Stock Opname">Stock Opname</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Procurement --}}
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Procurement</span>
            </li>
            <li class="menu-item {{ request()->is('supplier') ? 'active' : '' }}">
                <a href="{{ route('supplier.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-multiple-outline"></i>
                    <div data-i18n="Supplier">Supplier</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('part-inquiry') || request()->is('part-inquiry/*') ? 'active' : '' }}">
                <a href="{{ route('part-inquiry.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-text-search"></i>
                    <div data-i18n="Part Inquiry">Part Inquiry</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('purchase-request') || request()->is('purchase-request/*') ? 'active' : '' }}">
                <a href="{{ route('purchase-request.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-document-outline"></i>
                    <div data-i18n="Purchase Request">Purchase Request</div>
                    @if (@$prCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($prCount) }}</div>
                    @endif
                </a>
            </li>
            <li class="menu-item {{ request()->is('purchase') || request()->is('purchase/*') ? 'active' : '' }}">
                <a href="{{ route('purchase.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-cart-outline"></i>
                    <div data-i18n="Purchase Order">Purchase Order</div>
                </a>
            </li>

            {{-- Reports --}}
            <li class="menu-item {{ request()->is('sale-report') || request()->is('sale-report/*') || request()->is('sales-report/yearly/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-chart-bar"></i>
                    <div data-i18n="Reports">Reports</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('sale-report') || request()->is('sale-report/*') ? 'active' : '' }}">
                        <a href="{{ route('sale-report.index') }}" class="menu-link">
                            <div data-i18n="Sale Report">Sale Report</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('sales-report/yearly/*') ? 'active' : '' }}">
                        <a href="{{ route('reports.yearly', \Carbon\Carbon::now()->format('Y')) }}" class="menu-link">
                            <div data-i18n="Yearly In / Out">Yearly In / Out</div>
                        </a>
                    </li>
                </ul>
            </li>

            @if (Auth::user()->role != 'Accounting')
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Library</span>
            </li>
            <li class="menu-item {{ request()->is('library/index/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-library-shelves"></i>
                    <div data-i18n="Library">Library</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('library/index/marktool') ? 'active' : '' }}">
                        <a href="{{ route('marktool.index') }}" class="menu-link">
                            <div data-i18n="Marketing Tools">Marketing Tools</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/brosur') ? 'active' : '' }}">
                        <a href="{{ route('brosur.index') }}" class="menu-link">
                            <div data-i18n="Brosur">Brosur</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/partlist') ? 'active' : '' }}">
                        <a href="{{ route('partlist.index') }}" class="menu-link">
                            <div data-i18n="Partlist">Partlist</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/manbook') ? 'active' : '' }}">
                        <a href="{{ route('manbook.index') }}" class="menu-link">
                            <div data-i18n="Manual Book">Manual Book</div>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">HR Management</span>
            </li>
            {{-- 1. Dashboard HR --}}
            <li class="menu-item {{ (request()->is('hr') || request()->is('hr/dashboard')) ? 'active' : '' }}">
                <a href="{{ route('hr.dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-view-dashboard-outline"></i>
                    <div data-i18n="Dashboard HR">Dashboard HR</div>
                </a>
            </li>

            {{-- 2. Sub-Menu: Kepegawaian & Aset --}}
            @php
                $isPersonnelOpen = request()->is('employees*') || request()->is('hr/employees*') || request()->is('hr/assets*') || request()->is('hr/evaluations*') || request()->is('hr/departments*') || request()->is('hr/positions*');
            @endphp
            <li class="menu-item {{ $isPersonnelOpen ? 'open active' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-account-group-outline"></i>
                    <div data-i18n="Kepegawaian & Aset">Kepegawaian &amp; Aset</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ (request()->is('employees*') || request()->is('hr/employees*')) ? 'active' : '' }}">
                        <a href="{{ route('employees.index') }}" class="menu-link">
                            <div data-i18n="Hub Karyawan">Hub Karyawan</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hr/assets*') ? 'active' : '' }}">
                        <a href="{{ route('hr.assets.index') }}" class="menu-link">
                            <div data-i18n="Alat Kerja">Alat Kerja Karyawan</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hr/evaluations*') ? 'active' : '' }}">
                        <a href="{{ route('hr.evaluations.index') }}" class="menu-link">
                            <div data-i18n="Evaluasi Kinerja">Evaluasi Kinerja</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- 3. Sub-Menu: Waktu & Kehadiran --}}
            @php
                $isTimeAttendanceOpen = request()->is('hr/attendances*') || request()->is('hr/leaves*');
            @endphp
            <li class="menu-item {{ $isTimeAttendanceOpen ? 'open active' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-calendar-clock-outline"></i>
                    <div data-i18n="Waktu & Kehadiran">Waktu &amp; Kehadiran</div>
                    @if ($hrPendingLeaveCount > 0)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($hrPendingLeaveCount) }}</div>
                    @endif
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('hr/attendances*') ? 'active' : '' }}">
                        <a href="{{ route('hr.attendances.index') }}" class="menu-link">
                            <div data-i18n="Presensi">Presensi &amp; Jam Kerja</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hr/leaves*') ? 'active' : '' }}">
                        <a href="{{ route('hr.leaves.index') }}" class="menu-link">
                            <div data-i18n="Cuti & Izin">Cuti &amp; Izin</div>
                            @if ($hrPendingLeaveCount > 0)
                                <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($hrPendingLeaveCount) }}</div>
                            @endif
                        </a>
                    </li>
                </ul>
            </li>

            {{-- 4. Sub-Menu: Payroll & Finansial --}}
            @php
                $isPayrollClaimsOpen = request()->is('hr/payrolls*') || request()->is('hr/bonuses*') || request()->is('hr/reimbursements*');
            @endphp
            <li class="menu-item {{ $isPayrollClaimsOpen ? 'open active' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-cash-multiple"></i>
                    <div data-i18n="Payroll & Finansial">Payroll &amp; Finansial</div>
                    @if ($hrPendingReimbursementCount > 0)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($hrPendingReimbursementCount) }}</div>
                    @endif
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('hr/payrolls*') ? 'active' : '' }}">
                        <a href="{{ route('hr.payrolls.index') }}" class="menu-link">
                            <div data-i18n="Payroll">Payroll &amp; Slip Gaji</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hr/bonuses*') ? 'active' : '' }}">
                        <a href="{{ route('hr.bonuses.index') }}" class="menu-link">
                            <div data-i18n="Bonus Semester">Bonus Semesteran</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hr/reimbursements*') ? 'active' : '' }}">
                        <a href="{{ route('hr.reimbursements.index') }}" class="menu-link">
                            <div data-i18n="Reimbursement">Klaim Reimbursement</div>
                            @if ($hrPendingReimbursementCount > 0)
                                <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($hrPendingReimbursementCount) }}</div>
                            @endif
                        </a>
                    </li>
                </ul>
            </li>

            {{-- 5. My Portal (ESS) --}}
            @if (Auth::user()?->employee)
            <li class="menu-item {{ request()->is('hr/my-portal*') ? 'active' : '' }}">
                <a href="{{ route('hr.portal.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-card-account-details-star-outline"></i>
                    <div data-i18n="My Portal">My Portal</div>
                </a>
            </li>
            @endif

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Master</span>
            </li>
            <li class="menu-item {{ request()->is('template') || request()->is('template/*') ? 'active' : '' }}">
                <a href="{{ route('template.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-note-multiple-outline"></i>
                    <div data-i18n="Template Machine">Template Machine</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('employee') || request()->is('employee/*') ? 'active' : '' }}">
                <a href="{{ route('employee.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-circle-outline"></i>
                    <div data-i18n="User">User</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('unit-global') || request()->is('unit-global/*') ? 'active' : '' }}">
                <a href="{{ route('unit-global.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-desktop-tower"></i>
                    <div data-i18n="Unit Global">Unit Global</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('forecast/prices') ? 'active' : '' }}">
                <a href="{{ route('forecast.prices') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-cash-multiple"></i>
                    <div data-i18n="Master Harga Jasa PM">Master Harga Jasa PM</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('piping-materials*') ? 'active' : '' }}">
                <a href="{{ route('piping-materials.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-pipe"></i>
                    <div data-i18n="Material Piping">Material Piping</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('catalog-unit') || request()->is('catalog-unit/*') ? 'active' : '' }}">
                <a href="{{ route('catalog-unit.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-tag-text-outline"></i>
                    <div data-i18n="Catalog Unit">Catalog Unit</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('tool-master') ? 'active' : '' }}">
                <a href="{{ route('tool-master.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-tools"></i>
                    <div data-i18n="Master Tools">Master Tools</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('notulen') || request()->is('notulen/*') ? 'active' : '' }}">
                <a href="{{ route('notulen.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-box-outline"></i>
                    <div data-i18n="Notulen">Notulen</div>
                </a>
            </li>
            @if (Auth::user()?->isDeveloper())
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Helpdesk</span>
            </li>
            <li class="menu-item {{ request()->is('helpdesk') || request()->is('helpdesk/*') ? 'active' : '' }}">
                <a href="{{ route('helpdesk.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lifebuoy"></i>
                    <div data-i18n="Helpdesk">Helpdesk</div>
                    @if (@$openTicketCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($openTicketCount) }}</div>
                    @endif
                </a>
            </li>
            <li class="menu-item {{ request()->is('activity-log') || request()->is('activity-log/*') ? 'active' : '' }}">
                <a href="{{ route('activity-log.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-history"></i>
                    <div data-i18n="Activity Log">Activity Log</div>
                </a>
            </li>
            @endif
        @elseif (Auth::user()?->role == 'Sales')
            <!-- Dashboards -->
            <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
                <a href="{{ url('/') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                    <div data-i18n="Dashboards">Dashboards</div>
                </a>
            </li>
            {{-- <li class="menu-item">
                <a href="#" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-phone-incoming-outgoing-outline"></i>
                    <div data-i18n="Activities">Activities</div>
                </a>
            </li> --}}
            <li class="menu-item {{ request()->is('reports') ? 'active' : '' }}">
                <a href="{{ url('/reports') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-finance"></i>
                    <div data-i18n="Reports">Reports</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('overview') || request()->is('overview/*') || request()->is('overview/*/*') ? 'active' : '' }}">
                <a href="{{ url('/overview') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-eye-outline"></i>
                    <div data-i18n="Overview">Overview</div>
                </a>
            </li>
            {{-- <li class="menu-item {{ request()->is('my-kpi') ? 'active' : '' }}">
                <a href="{{ route('ecommerce.my-kpi') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-certificate-outline"></i>
                    <div data-i18n="Rapor KPI">Rapor KPI</div>
                </a>
            </li> --}}

            <!-- Layouts -->
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Client</span>
            </li>
            <li
                class="menu-item {{ request()->is('leads') || request()->is('leads/detail/*') || request()->is('existing') || request()->is('existing/*') || request()->is('ru') || request()->is('existing-bangkrupt') || request()->is('key-accounts') || request()->is('online-leads*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-account-group-outline"></i>
                    <div data-i18n="Client">Client</div>
                </a>

                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('leads') || request()->is('leads/detail/*') ? 'active' : '' }}">
                        <a href="{{ url('leads') }}" class="menu-link">
                            <div data-i18n="Client">Client</div>
                        </a>
                    </li>

                    <li
                        class="menu-item {{ request()->is('key-accounts') ? 'active' : '' }}">
                        <a href="{{ route('key-accounts.index') }}" class="menu-link">
                            <div data-i18n="Key Accounts">Key Accounts</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('online-leads*') ? 'active' : '' }}">
                        <a href="{{ route('online-leads.index') }}" class="menu-link">
                            <div data-i18n="Leads Online">Leads Online</div>
                        </a>
                    </li>
                    {{--
                    <li class="menu-item {{ request()->is('ru') ? 'active' : '' }}">
                        <a href="{{ route('ru.index') }}" class="menu-link">
                            <div data-i18n="R/U">R/U</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('existing-bangkrupt') ? 'active' : '' }}">
                        <a href="{{ route('index.bangkrupt') }}" class="menu-link">
                            <div data-i18n="List Bangkrupt">List Bangkrupt</div>
                        </a>
                    </li>
                    --}}
                </ul>
            </li>

            <li class="menu-item {{ request()->is('quotation') || request()->is('quotation/*') || request()->is('po') || request()->is('loss') || request()->is('po/sales/*') || request()->is('archive/quotation') ? 'active' : '' }}">
                <a href="{{ route('quotation.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-email-outline"></i>
                    <div data-i18n="Quotation">Quotation</div>
                </a>
            </li>

            @php
                $isMailboxConfiguredSales = Auth::user()?->isDeveloper() || (!empty(Auth::user()?->mailSetting?->smtp_username));
                $unreadInboxCountSales = $isMailboxConfiguredSales ? (Auth::user()?->mailboxMessages()->where('folder', 'inbox')->where('is_read', false)->count() ?? 0) : 0;
            @endphp
            @if ($isMailboxConfiguredSales)
            <li class="menu-item {{ request()->is('sales/mailbox*') ? 'active' : '' }}">
                <a href="{{ route('sales.mailbox.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-email-fast-outline"></i>
                    <div data-i18n="Mailbox">Mailbox</div>
                    @if ($unreadInboxCountSales > 0)
                        <div class="badge bg-primary rounded-pill ms-auto">{{ $formatSidebarBadge($unreadInboxCountSales) }}</div>
                    @endif
                </a>
            </li>
            @endif

            {{-- <li class="menu-item {{ request()->is('unit-quotation') || request()->is('unit-quotation/*') ? 'active' : '' }}">
                <a href="{{ route('unit-quotation.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-document-outline"></i>
                    <div data-i18n="Penawaran Unit">Penawaran Unit</div>
                </a>
            </li> --}}

            <li
                class="menu-item {{ request()->is('prospect') || request()->is('prospect/*') || request()->is('prospect-quotation') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-account-details-outline"></i>
                    <div data-i18n="Marketing Leads">Marketing Leads</div>
                    @if (@$leveledProspect >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($leveledProspect) }}</div>
                    @endif
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('prospect') || request()->is('prospect/*') ? 'active' : '' }}">
                        <a href="{{ route('prospect.index') }}" class="menu-link">
                            <div data-i18n="Marketing Leads">Marketing Leads</div>
                            @if (@$leveledProspect >= 1)
                                <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($leveledProspect) }}</div>
                            @endif
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('prospect-quotation') ? 'active' : '' }}">
                        <a href="{{ route('quotation.prospect') }}" class="menu-link">
                            <div data-i18n="Quotation">Quotation</div>
                        </a>
                    </li>
                </ul>
            </li>
            @if (Auth::id() != 3)
            <li
                class="menu-item {{ request()->is('service-reports') || request()->is('service-reports/*') ? 'active' : '' }}">
                <a href="{{ route('service-reports.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-chart-outline"></i>
                    <div data-i18n="Service Report">Service Report</div>
                    @if (@$reportsCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($reportsCount) }}</div>
                    @endif
                </a>
            </li>
            @endif
            @if (\App\Models\AppSetting::isSalesForecastMenuEnabled())
                <li class="menu-item {{ request()->is('forecast') ? 'active' : '' }}">
                    <a href="{{ route('forecast.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-chart-box-plus-outline"></i>
                        <div data-i18n="Forecast">Forecast</div>
                    </a>
                </li>
            @endif

            @php
                $salesPendingFeeCount = \App\Models\UnitQuotation::where('fee', '>', 0)
                    ->where('id_sales', Auth::id())
                    ->where('fee_payment_status', '!=', 'paid')
                    ->where('status', 'po_received')
                    ->count();
            @endphp
            <li class="menu-item {{ request()->is('finance/management-fee*') ? 'active' : '' }}">
                <a href="{{ route('finance.management-fee.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-cash-refund"></i>
                    <div data-i18n="Management Fee">Management Fee</div>
                    @if ($salesPendingFeeCount >= 1)
                        <div class="badge bg-warning rounded-pill ms-auto">{{ $formatSidebarBadge($salesPendingFeeCount) }}</div>
                    @endif
                </a>
            </li>
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Sales Order</span>
            </li>
            @php
                $suoPending = \App\Models\Suo::where('id_sales', Auth::id())
                    ->whereNull('id_quotation')
                    ->whereNull('id_unit_quotation')
                    ->where('status', '!=', 'converted')
                    ->count();
            @endphp
            <li class="menu-item {{ request()->is('suo') || request()->is('suo/*') ? 'active' : '' }}">
                <a href="{{ route('suo.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lightning-bolt-outline"></i>
                    <div data-i18n="Urgent Order">Urgent Order (SUO)</div>
                    @if ($suoPending >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($suoPending) }}</div>
                    @endif
                </a>
            </li>
            <li class="menu-item {{ request()->is('sales-order') || request()->is('pending-po/*') || request()->is('project-monitoring*') ? 'active' : '' }}">
                <a href="{{ route('pending-po.sales-order') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                    <div data-i18n="Sales Order">Sales Order</div>
                </a>
            </li>
            {{-- <li class="menu-item {{ request()->is('new-order') ? 'active' : '' }}">
                <a href="{{ route('pending-po.order') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-cart-plus"></i>
                    <div data-i18n="New Order">New Order</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('sales-order/list') || request()->is('sales-order/list') || request()->is('pending-po/*') || request()->is('pending-po-done') || request()->is('pending-po-project') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                    <div data-i18n="Sales Order">Sales Order</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('sales-order/list') ? 'active' : '' }}">
                        <a href="{{ route('pending-po.list') }}" class="menu-link">
                            <div data-i18n="List">List</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('sales-order/delivery') ? 'active' : '' }}">
                        <a href="{{ route('pending-po.delivery') }}" class="menu-link">
                            <div data-i18n="Delivery & Proccess">Delivery & Proccess</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('pending-po-done') ? 'active' : '' }}">
                        <a href="{{ route('pending-po.completed') }}" class="menu-link">
                            <div data-i18n="Completed">Completed</div>
                        </a>
                    </li>
                </ul>
            </li> --}}
            <li class="menu-item  {{ request()->is('return') || request()->is('return/*') ? 'active' : '' }}">
                <a href="{{ route('return.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-archive-cancel"></i>
                    <div data-i18n="Return">Return</div>
                </a>
            </li>

            {{-- <li class="menu-item {{ request()->is('visits/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-office-building-marker-outline"></i>
                    <div data-i18n="Visit">Visit</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('visits/leads') ? 'active' : '' }}">
                        <a href="{{ url('visits/leads') }}" class="menu-link">
                            <div data-i18n="Leads">Leads</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div data-i18n="Customer">Customer</div>
                        </a>
                    </li>
                </ul>
            </li> --}}


            {{-- <li
                class="menu-item {{ request()->is('service-reports') || request()->is('service-reports/*') ? 'active' : '' }}">
                <a href="{{ route('service-reports.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-chart-outline"></i>
                    <div data-i18n="Service Report">Service Report</div>
                </a>
            </li> --}}


            @if (Auth::id() != 3)
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">E-Stock</span>
            </li>

            <li class="menu-item {{ request()->is('product') || request()->is('product/*') ? 'active' : '' }}">
                <a href="{{ route('product.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-package-variant"></i>
                    <div data-i18n="Spare Part">Spare Part</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('unit') || request()->is('unit/*') ? 'active' : '' }}">
                <a href="{{ route('unit.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-desktop-tower"></i>
                    <div data-i18n="Stock Unit">Stock Unit</div>
                </a>
            </li>
            @endif


            {{-- <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Pending</span>
            </li>
            <li
                class="menu-item {{ request()->is('pending-po') || request()->is('pending-po/*') || request()->is('pending-po-done') || request()->is('pending-po-project') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-text-box-multiple"></i>
                    <div data-i18n="Pending PO">Pending PO</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('pending-po') ? 'active' : '' }}">
                        <a href="{{ route('pending-po.index') }}" class="menu-link">
                            <div data-i18n="Progress">Progress</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('pending-po-project') ? 'active' : '' }}">
                        <a href="{{ route('pending-po.index-project') }}" class="menu-link">
                            <div data-i18n="Project">Project</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('pending-po-done') ? 'active' : '' }}">
                        <a href="{{ route('pending-po.done') }}" class="menu-link">
                            <div data-i18n="Done">Done</div>
                        </a>
                    </li>
                </ul>
            </li> --}}
            @if (Auth::user()->id == 23)
                <li
                    class="menu-item {{ request()->is('change-warehouse') || request()->is('change-warehouse/*') ? 'active' : '' }}">
                    <a href="{{ route('change-warehouse.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-warehouse"></i>
                        <div data-i18n="Change Warehouse">Change Warehouse</div>
                    </a>
                </li>
            @endif

            @if (Auth::user()->id == 3)
                {{-- Procurement --}}
                <li class="menu-header fw-light mt-4">
                    <span class="menu-header-text">Procurement</span>
                </li>
                <li class="menu-item {{ request()->is('supplier') ? 'active' : '' }}">
                    <a href="{{ route('supplier.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-account-multiple-outline"></i>
                        <div data-i18n="Supplier">Supplier</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('part-inquiry') || request()->is('part-inquiry/*') ? 'active' : '' }}">
                    <a href="{{ route('part-inquiry.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-text-search"></i>
                        <div data-i18n="Part Inquiry">Part Inquiry</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('product') || request()->is('product/*') ? 'active' : '' }}">
                    <a href="{{ route('product.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-package-variant"></i>
                        <div data-i18n="Spare Part">Spare Part</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('unit') || request()->is('unit/*') ? 'active' : '' }}">
                    <a href="{{ route('unit.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-desktop-tower"></i>
                        <div data-i18n="Stock Unit">Stock Unit</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('purchase-request') || request()->is('purchase-request/*') ? 'active' : '' }}">
                    <a href="{{ route('purchase-request.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-file-document-outline"></i>
                        <div data-i18n="Purchase Request">Purchase Request</div>
                        @if (@$prCount >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($prCount) }}</div>
                        @endif
                    </a>
                </li>
                <li class="menu-item {{ request()->is('purchase') || request()->is('purchase/*') ? 'active' : '' }}">
                    <a href="{{ route('purchase.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-cart-outline"></i>
                        <div data-i18n="Purchase Order">Purchase Order</div>
                    </a>
                </li>

                {{-- Project Management --}}
                <li class="menu-header fw-light mt-4">
                    <span class="menu-header-text">Project Management</span>
                </li>
                <li class="menu-item {{ request()->is('piping-rab*') || request()->is('piping-materials*') || request()->is('hvac*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons mdi mdi-compass-outline"></i>
                        <div data-i18n="Engineering & RAB">Engineering &amp; RAB</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->is('piping-rab*') ? 'active' : '' }}">
                            <a href="{{ route('piping-rab.index') }}" class="menu-link">
                                <div data-i18n="RAB Piping">RAB Piping</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('piping-materials*') ? 'active' : '' }}">
                            <a href="{{ route('piping-materials.index') }}" class="menu-link">
                                <div data-i18n="Material Piping">Material Piping</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('hvac*') ? 'active' : '' }}">
                            <a href="{{ route('hvac.quick-calculator') }}" class="menu-link">
                                <div data-i18n="HVAC Estimator">HVAC Estimator</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item {{ request()->is('proyek-konstruksi*') ? 'active' : '' }}">
                    <a href="{{ route('proyek-konstruksi.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-hammer-wrench"></i>
                        <div data-i18n="Proyek Konstruksi">Proyek Konstruksi</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('service-reports*') || request()->is('project-reports*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons mdi mdi-file-document-outline"></i>
                        <div data-i18n="Report">Report</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ (request()->is('service-reports') || request()->is('service-reports/*')) && request('tab') != 'project' && !request()->is('project-reports*') ? 'active' : '' }}">
                            <a href="{{ route('service-reports.index') }}" class="menu-link">
                                <div data-i18n="Service Report">Service Report</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is('project-reports*') || (request()->is('service-reports*') && request('tab') == 'project') ? 'active' : '' }}">
                            <a href="{{ route('service-reports.index', ['tab' => 'project']) }}" class="menu-link">
                                <div data-i18n="Daily Project Report">Daily Project Report</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Marketing Tools</span>
            </li>
            <li class="menu-item {{ request()->is('library/index/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-library-shelves"></i>
                    <div data-i18n="Library">Library</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('library/index/marktool') ? 'active' : '' }}">
                        <a href="{{ route('marktool.index') }}" class="menu-link">
                            <div data-i18n="Marketing Tools">Marketing Tools</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/brosur') ? 'active' : '' }}">
                        <a href="{{ route('brosur.index') }}" class="menu-link">
                            <div data-i18n="Brosur">Brosur</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/partlist') ? 'active' : '' }}">
                        <a href="{{ route('partlist.index') }}" class="menu-link">
                            <div data-i18n="Partlist">Partlist</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/manbook') ? 'active' : '' }}">
                        <a href="{{ route('manbook.index') }}" class="menu-link">
                            <div data-i18n="Manual Book">Manual Book</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ request()->is('cor-factor/calculator') ? 'active' : '' }}">
                <a href="{{ route('calculator.correction') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-desktop-tower"></i>
                    <div data-i18n="Correction Factor Calc">Correction Factor Calc</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('schematics*') ? 'active' : '' }}">
                <a href="{{ route('schematics.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-vector-polyline"></i>
                    <div data-i18n="Schematic Diagram">Schematic Diagram</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('hvac*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-air-conditioner"></i>
                    <div data-i18n="HVAC Cooling Load">HVAC Cooling Load</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('hvac/quick-calculator*') ? 'active' : '' }}">
                        <a href="{{ route('hvac.quick-calculator') }}" class="menu-link">
                            <div data-i18n="Quick Estimator (Sales)">Quick Estimator (Sales)</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hvac/projects*') || request()->is('hvac/rooms*') ? 'active' : '' }}">
                        <a href="{{ route('hvac.project.index') }}" class="menu-link">
                            <div data-i18n="Daftar Proyek HVAC">Daftar Proyek HVAC</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hvac/master-catalog*') ? 'active' : '' }}">
                        <a href="{{ route('hvac.master.index') }}" class="menu-link">
                            <div data-i18n="Master Data & Katalog">Master Data &amp; Katalog</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li
                class="menu-item {{ request()->is('unit-global') || request()->is('unit-global/*') ? 'active' : '' }}">
                <a href="{{ route('unit-global.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-desktop-tower"></i>
                    <div data-i18n="Unit Global">Unit Global</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('catalog-unit') || request()->is('catalog-unit/*') ? 'active' : '' }}">
                <a href="{{ route('catalog-unit.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-tag-text-outline"></i>
                    <div data-i18n="Catalog Unit">Catalog Unit</div>
                </a>
            </li>
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Helpdesk</span>
            </li>
            <li class="menu-item {{ request()->is('helpdesk') || request()->is('helpdesk/*') ? 'active' : '' }}">
                <a href="{{ route('helpdesk.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lifebuoy"></i>
                    <div data-i18n="Helpdesk">Helpdesk</div>
                    @if (@$openTicketCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($openTicketCount) }}</div>
                    @endif
                </a>
            </li>
            @if (Auth::user()?->hasConstructionProjectAccess() && Auth::id() != 3)
            <li class="menu-item {{ request()->is('proyek-konstruksi*') ? 'active' : '' }}">
                <a href="{{ route('proyek-konstruksi.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-hammer-wrench"></i>
                    <div data-i18n="Proyek Konstruksi">Proyek Konstruksi</div>
                </a>
            </li>
            @endif
        @elseif (Auth::user()?->role == 'Support')
            <!-- Dashboards -->
            <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
                <a href="{{ url('/') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                    <div data-i18n="Dashboards">Dashboards</div>
                </a>
            </li>
            {{-- <li class="menu-item {{ request()->is('kanban*') ? 'active' : '' }}">
                <a href="{{ route('kanban.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-view-dashboard-outline"></i>
                    <div data-i18n="Kanban">Kanban</div>
                </a>
            </li> --}}
            {{-- <li class="menu-item">
                <a href="#" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-phone-incoming-outgoing-outline"></i>
                    <div data-i18n="Activities">Activities</div>
                </a>
            </li> --}}
            <li class="menu-item {{ request()->is('reports') || request()->is('reports/*') ? 'active' : '' }}">
                <a href="{{ url('/reports') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-finance"></i>
                    <div data-i18n="Reports">Reports</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('overview') || request()->is('overview/*') || request()->is('overview/*/*') ? 'active' : '' }}">
                <a href="{{ url('/overview') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-eye-outline"></i>
                    <div data-i18n="Overview">Overview</div>
                </a>
            </li>
            <!-- Layouts
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Client</span>
            </li>
            <li
                class="menu-item {{ request()->is('leads') || request()->is('leads/detail/*') || request()->is('existing') || request()->is('existing/*') || request()->is('ru') || request()->is('existing-bangkrupt') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-account-group-outline"></i>
                    <div data-i18n="Client">Client</div>
                </a>

                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('leads') || request()->is('leads/detail/*') ? 'active' : '' }}">
                        <a href="{{ url('leads') }}" class="menu-link">
                            <div data-i18n="Leads">Leads</div>
                        </a>
                    </li>

                    <li
                        class="menu-item {{ request()->is('existing') || request()->is('existing/*') ? 'active' : '' }}">
                        <a href="{{ route('existing.index') }}" class="menu-link">
                            <div data-i18n="Customers">Customers</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('ru') ? 'active' : '' }}">
                        <a href="{{ route('ru.index') }}" class="menu-link">
                            <div data-i18n="R/U">R/U</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('existing-bangkrupt') ? 'active' : '' }}">
                        <a href="{{ route('index.bangkrupt') }}" class="menu-link">
                            <div data-i18n="List Bangkrupt">List Bangkrupt</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ request()->is('quotation') || request()->is('quotation/*') || request()->is('po') || request()->is('loss') || request()->is('po/sales/*') ? 'active' : '' }}">
                <a href="{{ route('quotation.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-email-outline"></i>
                    <div data-i18n="Quotation">Quotation</div>
                </a>
            </li>

            {{-- <li class="menu-item {{ request()->is('unit-quotation') || request()->is('unit-quotation/*') ? 'active' : '' }}">
                <a href="{{ route('unit-quotation.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-document-outline"></i>
                    <div data-i18n="Penawaran Unit">Penawaran Unit</div>
                </a>
            </li> --}}

            <li
                class="menu-item {{ request()->is('service-reports') || request()->is('service-reports/*') ? 'active' : '' }}">
                <a href="{{ route('service-reports.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-chart-outline"></i>
                    <div data-i18n="Service Report">Service Report</div>
                </a>
            </li>
            -->

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Marketing Leads</span>
            </li>

            <li class="menu-item {{ request()->is('prospect') || request()->is('prospect/*') ? 'active' : '' }}">
                <a href="{{ route('prospect.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-details-outline"></i>
                    <div data-i18n="Marketing Leads">Marketing Leads</div>
                </a>
            </li>


            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Library</span>
            </li>
            <li class="menu-item {{ request()->is('library/index/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-library-shelves"></i>
                    <div data-i18n="Library">Library</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('library/index/marktool') ? 'active' : '' }}">
                        <a href="{{ route('marktool.index') }}" class="menu-link">
                            <div data-i18n="Marketing Tools">Marketing Tools</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/brosur') ? 'active' : '' }}">
                        <a href="{{ route('brosur.index') }}" class="menu-link">
                            <div data-i18n="Brosur">Brosur</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/partlist') ? 'active' : '' }}">
                        <a href="{{ route('partlist.index') }}" class="menu-link">
                            <div data-i18n="Partlist">Partlist</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/manbook') ? 'active' : '' }}">
                        <a href="{{ route('manbook.index') }}" class="menu-link">
                            <div data-i18n="Manual Book">Manual Book</div>
                        </a>
                    </li>
                </ul>
            </li>
                        <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">E-Stock</span>
            </li>

            <li
                class="menu-item {{ request()->is('master/product') ||
                request()->is('product') ||
                request()->is('product/*') ||
                request()->is('unit') ||
                request()->is('unit/*')
                    ? 'open'
                    : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-package-variant"></i>
                    <div data-i18n="E-Stock">E-Stock</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('product') || request()->is('product/*') ? 'active' : '' }}">
                        <a href="{{ route('product.index') }}" class="menu-link">
                            <div data-i18n="Product">Product</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('unit') || request()->is('unit/*') ? 'active' : '' }}">
                        <a href="{{ route('unit.index') }}" class="menu-link">
                            <div data-i18n="Unit">Unit</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('work-orders*') ? 'active' : '' }}">
                        <a href="{{ route('work-orders.index') }}" class="menu-link">
                            <div data-i18n="Work Order">Work Order</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Helpdesk</span>
            </li>
            <li class="menu-item {{ request()->is('helpdesk') || request()->is('helpdesk/*') ? 'active' : '' }}">
                <a href="{{ route('helpdesk.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lifebuoy"></i>
                    <div data-i18n="Helpdesk">Helpdesk</div>
                    @if (@$openTicketCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($openTicketCount) }}</div>
                    @endif
                </a>
            </li>
            @if (Auth::user()?->hasConstructionProjectAccess())
            <li class="menu-item {{ request()->is('proyek-konstruksi*') ? 'active' : '' }}">
                <a href="{{ route('proyek-konstruksi.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-hammer-wrench"></i>
                    <div data-i18n="Proyek Konstruksi">Proyek Konstruksi</div>
                </a>
            </li>
            @endif


        @elseif(Auth::user()?->role == 'Logistic')
            <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
                <a href="{{ url('/') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('kanban*') ? 'active' : '' }}">
                <a href="{{ route('kanban.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-view-dashboard-outline"></i>
                    <div data-i18n="Kanban">Kanban</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Warehouse</span>
            </li>
            <li
                class="menu-item {{ request()->is('master/product') || request()->is('product') || request()->is('product/*') || request()->is('product-set') || request()->is('unit') || request()->is('unit/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-database-outline"></i>
                    <div data-i18n="Master Data">Master Data</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('master/product') ? 'active' : '' }}">
                        <a href="{{ route('master.product') }}" class="menu-link">
                            <div data-i18n="SKU">SKU</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('product') || request()->is('product/*') ? 'active' : '' }}">
                        <a href="{{ route('product.index') }}" class="menu-link">
                            <div data-i18n="Sparepart">Sparepart</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('product-set') ? 'active' : '' }}">
                        <a href="{{ route('product-set.index') }}" class="menu-link">
                            <div data-i18n="Product Set">Product Set</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('unit') || request()->is('unit/*') ? 'active' : '' }}">
                        <a href="{{ route('unit.index') }}" class="menu-link">
                            <div data-i18n="Stock Unit">Stock Unit</div>
                        </a>
                    </li>
                </ul>
            </li>
            {{-- Stock Movement Logistic --}}
            <li
                class="menu-item {{ request()->is('product-in') || request()->is('product-in/*') || request()->is('product-out') || request()->is('product-out/*') || request()->is('unit-acquisition') || request()->is('unit-acquisition/*') || request()->is('unit-product-in') || request()->is('unit-product-in/*') || request()->is('unit-product-out') || request()->is('unit-product-out/*') || request()->is('warehouse/intercompany*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-swap-horizontal"></i>
                    <div data-i18n="Stock Movement">Stock Movement</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('product-in') || request()->is('product-in/*') ? 'active' : '' }}">
                        <a href="{{ route('product-in.index') }}" class="menu-link">
                            <div data-i18n="Good Receipt">Good Receipt</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('product-out') || request()->is('product-out/*') ? 'active' : '' }}">
                        <a href="{{ route('product-out.index') }}" class="menu-link">
                            <div data-i18n="Product-Out">Product-Out</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('warehouse/intercompany*') ? 'active' : '' }}">
                        <a href="{{ route('intercompany.index') }}" class="menu-link">
                            <div data-i18n="Rekap BK Kojisha">Rekap BK Kojisha</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('unit-product-in') || request()->is('unit-product-in/*') ? 'active' : '' }}">
                        <a href="{{ route('unit-product-in.index') }}" class="menu-link">
                            <div data-i18n="Barang Masuk Unit">Barang Masuk Unit</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('unit-product-out') || request()->is('unit-product-out/*') ? 'active' : '' }}">
                        <a href="{{ route('unit-product-out.index') }}" class="menu-link">
                            <div data-i18n="Unit Keluar">Unit Keluar</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('unit-acquisition') || request()->is('unit-acquisition/*') ? 'active' : '' }}">
                        <a href="{{ route('unit-acquisition.index') }}" class="menu-link">
                            <div data-i18n="Unit Acquisition">Unit Acquisition</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('work-orders*') ? 'active' : '' }}">
                        <a href="{{ route('work-orders.index') }}" class="menu-link">
                            <div data-i18n="Work Order">Work Order</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li
                class="menu-item {{ request()->is('stock') || request()->is('stock/*') || request()->is('stock-opname') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-clipboard-list-outline"></i>
                    <div data-i18n="Monitoring">Monitoring</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('stock') || request()->is('stock/*') ? 'active' : '' }}">
                        <a href="{{ route('stock.index') }}" class="menu-link">
                            <div data-i18n="Stock">Stock</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('stock-opname') ? 'active' : '' }}">
                        <a href="{{ route('opname.index') }}" class="menu-link">
                            <div data-i18n="Stock Opname">Stock Opname</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ request()->is('change-warehouse') || request()->is('change-warehouse/*') ? 'active' : '' }}">
                <a href="{{ route('change-warehouse.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-swap-horizontal"></i>
                    <div data-i18n="Warehouse Transfer">Warehouse Transfer</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('service-reports') || request()->is('service-reports/*') ? 'active' : '' }}">
                <a href="{{ route('service-reports.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-chart-outline"></i>
                    <div data-i18n="Service Report">Service Report</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Sales Order</span>
            </li>
            @php $suoLogisticPending = $suoLogisticPending ?? \Illuminate\Support\Facades\Cache::remember('suo_logistic_pending_count', 60, function () { return \App\Models\Suo::where('status','submitted')->count(); }); @endphp
            <li class="menu-item {{ request()->is('suo-logistic') ? 'active' : '' }}">
                <a href="{{ route('suo.logistic.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lightning-bolt-outline"></i>
                    <div data-i18n="Urgent Order">Urgent Order (SUO)</div>
                    @if ($suoLogisticPending >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($suoLogisticPending) }}</div>
                    @endif
                </a>
            </li>
            <li class="menu-item {{ request()->is('sales-order') || request()->is('pending-po/*') || request()->is('project-monitoring*') ? 'active' : '' }}">
                <a href="{{ route('pending-po.sales-order') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                    <div data-i18n="Sales Order">Sales Order</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('purchase-request') || request()->is('purchase-request/*') ? 'active' : '' }}">
                <a href="{{ route('purchase-request.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-format-list-group-plus"></i>
                    <div data-i18n="Purchase Request">Purchase Request</div>
                    @if (@$prCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($prCount) }}</div>
                    @endif
                </a>
            </li>
            <li class="menu-item {{ request()->is('return') || request()->is('return/*') ? 'active' : '' }}">
                <a href="{{ route('return.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-archive-cancel"></i>
                    <div data-i18n="Return">Return</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Layanan & Bantuan</span>
            </li>
            <li class="menu-item {{ request()->is('library/index/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-library-shelves"></i>
                    <div data-i18n="Library">Library</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('library/index/marktool') ? 'active' : '' }}">
                        <a href="{{ route('marktool.index') }}" class="menu-link">
                            <div data-i18n="Marketing Tools">Marketing Tools</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/brosur') ? 'active' : '' }}">
                        <a href="{{ route('brosur.index') }}" class="menu-link">
                            <div data-i18n="Brosur">Brosur</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/partlist') ? 'active' : '' }}">
                        <a href="{{ route('partlist.index') }}" class="menu-link">
                            <div data-i18n="Partlist">Partlist</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/manbook') ? 'active' : '' }}">
                        <a href="{{ route('manbook.index') }}" class="menu-link">
                            <div data-i18n="Manual Book">Manual Book</div>
                        </a>
                    </li>
                </ul>
            </li>
            @if (Auth::user()?->hasConstructionProjectAccess())
            <li class="menu-item {{ request()->is('proyek-konstruksi*') ? 'active' : '' }}">
                <a href="{{ route('proyek-konstruksi.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-hammer-wrench"></i>
                    <div data-i18n="Proyek Konstruksi">Proyek Konstruksi</div>
                </a>
            </li>
            @endif
            <li class="menu-item {{ request()->is('helpdesk') || request()->is('helpdesk/*') ? 'active' : '' }}">
                <a href="{{ route('helpdesk.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lifebuoy"></i>
                    <div data-i18n="Helpdesk">Helpdesk</div>
                    @if (@$openTicketCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($openTicketCount) }}</div>
                    @endif
                </a>
            </li>
            @if (Auth::user()?->employee)
            <li class="menu-item {{ request()->is('hr/my-portal*') ? 'active' : '' }}">
                <a href="{{ route('hr.portal.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-card-account-details-star-outline"></i>
                    <div data-i18n="My Portal">My Portal</div>
                </a>
            </li>
            @endif
            <li class="menu-item {{ request()->is('unit-global') || request()->is('unit-global/*') ? 'active' : '' }}">
                <a href="{{ route('unit-global.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-desktop-tower"></i>
                    <div data-i18n="Unit Global">Unit Global</div>
                </a>
            </li>
        @elseif(Auth::user()?->role == 'ServiceM')
            <!-- Dashboards -->
            <li class="menu-item {{ request()->is('service-reports-servicem*') || request()->is('/') ? 'active' : '' }}">
                <a href="{{ route('service-reports.manager') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-chart-outline"></i>
                    <div data-i18n="Service Reports">Service Reports</div>
                    @if (@$srPendingApprovalCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($srPendingApprovalCount) }}</div>
                    @endif
                </a>
            </li>
            <li class="menu-item {{ request()->is('accounting/monitoring-document*') ? 'active' : '' }}">
                <a href="{{ route('kanban.monitoring-document') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-document-multiple-outline"></i>
                    <div data-i18n="Monitoring Document">Monitoring Document</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('kanban*') && !request()->is('accounting/monitoring-document*') ? 'active' : '' }}">
                <a href="{{ route('kanban.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-view-dashboard-outline"></i>
                    <div data-i18n="Kanban">Kanban</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('work-orders*') ? 'active' : '' }}">
                <a href="{{ route('work-orders.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-wrench-cog-outline"></i>
                    <div data-i18n="Work Order">Work Order</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('tool-audit') || request()->is('tool-audit/*') ? 'active' : '' }}">
                <a href="{{ route('tool-audit.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-tools"></i>
                    <div data-i18n="Audit Tools">Audit Tools</div>
                    @php
                        $hasPendingToolAudit = Auth::check() && \App\Models\ToolAudit::where('id_technician', Auth::id())
                            ->whereIn('status_submit', ['Draft', 'Rejected'])
                            ->whereHas('period', fn($q) => $q->where('status', 'Open'))
                            ->exists();
                    @endphp
                    @if ($hasPendingToolAudit)
                        <div class="badge bg-danger rounded-pill ms-auto px-2 py-1 font-10">Wajib</div>
                    @endif
                </a>
            </li>
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Monitoring</span>
            </li>
            <li
                class="menu-item {{ request()->is('service-manager') || request()->is('service-manager/*') ? 'active' : '' }}">
                <a href="{{ route('service-manager.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-chart-outline"></i>
                    <div data-i18n="Monitoring Fajar Paper">Monitoring Fajar Paper</div>
                </a>
            </li>


            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Service Contract</span>
            </li>

            <li class="menu-item {{ request()->is('monitoring-client/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-factory"></i>
                    <div data-i18n="Fajar Paper">Fajar Paper</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('monitoring-client/fajarPaper') ? 'active' : '' }}">
                        <a href="{{ route('monitoring.fajarPaper') }}" class="menu-link">
                            <div data-i18n="Daily Input">Daily Input</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('monitoring-client/fajarPaper-monitoring') ? 'active' : '' }}">
                        <a href="{{ route('monitoring.fajarPaper-monitoring') }}" class="menu-link">
                            <div data-i18n="Monitoring">Monitoring</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('monitoring-client/fajarPaper-service-report') ? 'active' : '' }}">
                        <a href="{{ route('monitoring.fajarPaper-service-report') }}" class="menu-link">
                            <div data-i18n="Service Report">Service Report</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('monitoring-client/fajarPaper-reports') ? 'active' : '' }}">
                        <a href="{{ route('monitoring.fajarPaper-reports') }}" class="menu-link">
                            <div data-i18n="Report">Report</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('po') || request()->is('po/sales/*') ? 'active' : '' }}">
                        <a href="{{ route('quotation.po') }}" class="menu-link">
                            <div data-i18n="Summary">Summary</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('monitoring-client/fajarPaper-archive') ? 'active' : '' }}">
                        <a href="{{ route('monitoring-arsip.fajarPaper') }}" class="menu-link">
                            <div data-i18n="Archived">Archived</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Sales Order</span>
            </li>
            <li class="menu-item {{ request()->is('sales-order') || request()->is('pending-po/*') || request()->is('project-monitoring*') ? 'active' : '' }}">
                <a href="{{ route('pending-po.sales-order') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                    <div data-i18n="Sales Order">Sales Order</div>
                </a>
            </li>
            {{-- <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Notulen</span>
            </li> --}}

            {{-- <li class="menu-item {{ request()->is('notulen') || request()->is('notulen/*') ? 'active' : '' }}">
                <a href="{{ route('notulen.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-box-outline"></i>
                    <div data-i18n="notulen">Notulen</div>
                </a>
            </li> --}}

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Library</span>
            </li>
            <li class="menu-item {{ request()->is('library/index/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-library-shelves"></i>
                    <div data-i18n="Library">Library</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('library/index/marktool') ? 'active' : '' }}">
                        <a href="{{ route('marktool.index') }}" class="menu-link">
                            <div data-i18n="Marketing Tools">Marketing Tools</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/brosur') ? 'active' : '' }}">
                        <a href="{{ route('brosur.index') }}" class="menu-link">
                            <div data-i18n="Brosur">Brosur</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/partlist') ? 'active' : '' }}">
                        <a href="{{ route('partlist.index') }}" class="menu-link">
                            <div data-i18n="Partlist">Partlist</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/manbook') ? 'active' : '' }}">
                        <a href="{{ route('manbook.index') }}" class="menu-link">
                            <div data-i18n="Manual Book">Manual Book</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Helpdesk</span>
            </li>
            <li class="menu-item {{ request()->is('helpdesk') || request()->is('helpdesk/*') ? 'active' : '' }}">
                <a href="{{ route('helpdesk.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lifebuoy"></i>
                    <div data-i18n="Helpdesk">Helpdesk</div>
                    @if (@$openTicketCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($openTicketCount) }}</div>
                    @endif
                </a>
            </li>
            @if (Auth::user()?->hasConstructionProjectAccess())
            <li class="menu-item {{ request()->is('proyek-konstruksi*') ? 'active' : '' }}">
                <a href="{{ route('proyek-konstruksi.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-hammer-wrench"></i>
                    <div data-i18n="Proyek Konstruksi">Proyek Konstruksi</div>
                </a>
            </li>
            @endif
        @elseif(Auth::user()?->role == 'Technician' || Auth::user()?->role == 'Coordinator')
            <!-- Dashboards -->
            <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
                <a href="{{ url('/') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                    <div data-i18n="Dashboards">Dashboards</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('kanban*') ? 'active' : '' }}">
                <a href="{{ route('kanban.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-view-dashboard-outline"></i>
                    <div data-i18n="Kanban">Kanban</div>
                </a>
            </li>
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Technician & Service</span>
            </li>
            <li
                class="menu-item {{ (request()->is('service-reports') || request()->is('service-reports/*')) && request('tab') != 'project' && !request()->is('project-reports*') ? 'active' : '' }}">
                <a href="{{ route('service-reports.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-wrench-outline"></i>
                    <div data-i18n="Service Report">Service Report</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('project-reports*') || (request()->is('service-reports*') && request('tab') == 'project') ? 'active' : '' }}">
                <a href="{{ route('service-reports.index', ['tab' => 'project']) }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-clipboard-text-clock-outline"></i>
                    <div data-i18n="Daily Project Report">Daily Project Report</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('service-manager') || request()->is('service-manager/*') ? 'active' : '' }}">
                <a href="{{ route('service-manager.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-chart-outline"></i>
                    <div data-i18n="Monitoring Fajar Paper">Monitoring Fajar Paper</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('work-orders*') ? 'active' : '' }}">
                <a href="{{ route('work-orders.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-wrench-cog-outline"></i>
                    <div data-i18n="Work Order">Work Order</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('tool-audit') || request()->is('tool-audit/*') ? 'active' : '' }}">
                <a href="{{ route('tool-audit.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-tools"></i>
                    <div data-i18n="Audit Tools">Audit Tools</div>
                    @php
                        $hasPendingToolAudit = Auth::check() && \App\Models\ToolAudit::where('id_technician', Auth::id())
                            ->whereIn('status_submit', ['Draft', 'Rejected'])
                            ->whereHas('period', fn($q) => $q->where('status', 'Open'))
                            ->exists();
                    @endphp
                    @if ($hasPendingToolAudit)
                        <div class="badge bg-danger rounded-pill ms-auto px-2 py-1 font-10">Wajib</div>
                    @endif
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Notulen</span>
            </li>

            <li class="menu-item {{ request()->is('notulen') || request()->is('notulen/*') ? 'active' : '' }}">
                <a href="{{ route('notulen.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-box-outline"></i>
                    <div data-i18n="notulen">Notulen</div>
                    {{-- @if (@$leveledProspect >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $leveledProspect }}</div>
                    @endif --}}
                </a>
            </li>


            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Library</span>
            </li>
            <li class="menu-item {{ request()->is('library/index/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-library-shelves"></i>
                    <div data-i18n="Library">Library</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('library/index/marktool') ? 'active' : '' }}">
                        <a href="{{ route('marktool.index') }}" class="menu-link">
                            <div data-i18n="Marketing Tools">Marketing Tools</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/brosur') ? 'active' : '' }}">
                        <a href="{{ route('brosur.index') }}" class="menu-link">
                            <div data-i18n="Brosur">Brosur</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/partlist') ? 'active' : '' }}">
                        <a href="{{ route('partlist.index') }}" class="menu-link">
                            <div data-i18n="Partlist">Partlist</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/manbook') ? 'active' : '' }}">
                        <a href="{{ route('manbook.index') }}" class="menu-link">
                            <div data-i18n="Manual Book">Manual Book</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Helpdesk</span>
            </li>
            <li class="menu-item {{ request()->is('helpdesk') || request()->is('helpdesk/*') ? 'active' : '' }}">
                <a href="{{ route('helpdesk.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lifebuoy"></i>
                    <div data-i18n="Helpdesk">Helpdesk</div>
                    @if (@$openTicketCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($openTicketCount) }}</div>
                    @endif
                </a>
            </li>
            @if (Auth::user()?->hasConstructionProjectAccess())
            <li class="menu-item {{ request()->is('proyek-konstruksi*') ? 'active' : '' }}">
                <a href="{{ route('proyek-konstruksi.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-hammer-wrench"></i>
                    <div data-i18n="Proyek Konstruksi">Proyek Konstruksi</div>
                </a>
            </li>
            @endif
        @elseif(Auth::user()?->role == 'Client')
            @if (Auth::user()?->level == 1)
                <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
                    <a href="{{ url('/') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                        <div data-i18n="Dashboards">Dashboards</div>
                    </a>
                </li>

                <li class="menu-header fw-light mt-4">
                    <span class="menu-header-text">Service</span>
                </li>

                <li
                    class="menu-item {{ request()->is('service-manager') || request()->is('service-manager/*') ? 'active' : '' }}">
                    <a href="{{ route('under-maintenance') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-file-chart-outline"></i>
                        <div data-i18n="Monitoring">Monitoring</div>
                    </a>
                </li>
                <li
                    class="menu-item {{ request()->is('service-reports') || request()->is('service-reports/*') ? 'active' : '' }}">
                    <a href="{{ route('under-maintenance') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-file-chart-outline"></i>
                        <div data-i18n="Service Report">Service Report</div>
                    </a>
                </li>
            @else
                <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
                    <a href="{{ url('/') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                        <div data-i18n="Dashboards">Dashboards</div>
                    </a>
                </li>

                <li class="menu-header fw-light mt-4">
                    <span class="menu-header-text">Service</span>
                </li>

                <li
                    class="menu-item {{ request()->is('/service-manager-daily/*/*') || request()->is('service-manager-daily/*') ? 'active' : '' }}">
                    <a href="{{ route('under-maintenance') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-file-chart-outline"></i>
                        <div data-i18n="Reports">Reports</div>
                    </a>
                </li>
                <li
                    class="menu-item {{ request()->is('service-reports') || request()->is('service-reports/*') ? 'active' : '' }}">
                    <a href="{{ route('under-maintenance') }}" class="menu-link">
                        <i class="menu-icon tf-icons mdi mdi-file-chart-outline"></i>
                        <div data-i18n="Preventive">Preventive</div>
                    </a>
                </li>
            @endif
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Helpdesk</span>
            </li>
            <li class="menu-item {{ request()->is('helpdesk') || request()->is('helpdesk/*') ? 'active' : '' }}">
                <a href="{{ route('helpdesk.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lifebuoy"></i>
                    <div data-i18n="Helpdesk">Helpdesk</div>
                    @if (@$openTicketCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($openTicketCount) }}</div>
                    @endif
                </a>
            </li>
        @elseif (in_array(Auth::user()?->role, ['Finance Manager', 'Finance']))
            <!-- Dashboard -->
            <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
                <a href="{{ url('/') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>

            @php
                if (!isset($monitoringCount)) {
                    $monitoringBoard = \App\Models\KanbanBoard::where('type', 'monitoring')->first();
                    $monitoringCount = 0;
                    if ($monitoringBoard) {
                        $monitoringCount = \App\Models\KanbanTask::where('board_id', $monitoringBoard->id)
                            ->whereIn('column_id', function($query) use ($monitoringBoard) {
                                $query->select('id')
                                    ->from('kanban_columns')
                                    ->where('board_id', $monitoringBoard->id)
                                    ->whereIn('title', ['PO REFTECH', 'PO E-COMMERCE']);
                            })
                            ->count();
                    }
                }
            @endphp
            <li class="menu-item {{ request()->is('accounting/monitoring-document*') ? 'active' : '' }}">
                <a href="{{ route('kanban.monitoring-document') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-view-dashboard-outline"></i>
                    <div data-i18n="Monitoring Document">Monitoring Document</div>
                    @if ($monitoringCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($monitoringCount) }}</div>
                    @endif
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Sales Order</span>
            </li>
            @php $suoAccountingPending = $suoAccountingPending ?? \App\Models\Suo::where('status','confirmed')->whereNull('no_invoice_booking')->count(); @endphp
            <li class="menu-item {{ request()->is('suo-accounting') ? 'active' : '' }}">
                <a href="{{ route('suo.accounting.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lightning-bolt-outline"></i>
                    <div data-i18n="Urgent Order">Urgent Order (SUO)</div>
                    @if ($suoAccountingPending >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($suoAccountingPending) }}</div>
                    @endif
                </a>
            </li>
            <li class="menu-item {{ request()->is('sales-order') || request()->is('pending-po/*') || request()->is('project-monitoring*') ? 'active' : '' }}">
                <a href="{{ route('pending-po.sales-order') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                    <div data-i18n="Sales Order">Sales Order</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('return') || request()->is('return/*') ? 'active' : '' }}">
                <a href="{{ route('return.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-archive-cancel"></i>
                    <div data-i18n="Return">Return</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Account Receivable (AR)</span>
            </li>
            <li class="menu-item {{ request()->is('payment-index/invoice') || request()->is('payment-detail/invoice/*') ? 'active' : '' }}">
                <a href="{{ route('payment_index.invoice') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-document-check-outline"></i>
                    <div data-i18n="Sales Invoice">Sales Invoice</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('payment-index/payment') || request()->is('payment-detail/payment/*') ? 'active' : '' }}">
                <a href="{{ route('payment_index.payment') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-cash-check"></i>
                    <div data-i18n="Payment Receipt">Payment Receipt</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('payment-index/aging*') ? 'active' : '' }}">
                <a href="{{ route('payment_index.aging') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-calendar-clock-outline"></i>
                    <div data-i18n="Aging Piutang">Aging Piutang</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('customer-statement*') ? 'active' : '' }}">
                <a href="{{ route('customer.statement') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-book-open-page-variant-outline"></i>
                    <div data-i18n="Kartu Piutang (SOA)">Kartu Piutang (SOA)</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Account Payable (AP)</span>
            </li>
            <li class="menu-item {{ request()->is('payable/invoice*') ? 'active' : '' }}">
                <a href="{{ route('payable.index_invoice') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-receipt-text-outline"></i>
                    <div data-i18n="Purchase Invoice">Purchase Invoice</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('payable/gr-uninvoiced*') ? 'active' : '' }}">
                <a href="{{ route('payable.gr_uninvoiced') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-truck-delivery-outline"></i>
                    <div data-i18n="GR Belum Ditagih">GR Belum Ditagih</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('payable/receipt*') ? 'active' : '' }}">
                <a href="{{ route('payable.index_receipt') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-cash-fast"></i>
                    <div data-i18n="Purchase Payment">Purchase Payment</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('payable/aging*') ? 'active' : '' }}">
                <a href="{{ route('payable.index_aging') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-calendar-alert"></i>
                    <div data-i18n="Aging Hutang">Aging Hutang</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('payable/statement*') ? 'active' : '' }}">
                <a href="{{ route('payable.statement') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-book-account-outline"></i>
                    <div data-i18n="Kartu Hutang (SOA)">Kartu Hutang (SOA)</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('payable/expenses*') ? 'active' : '' }}">
                <a href="{{ route('payable.expenses') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-table-box-outline"></i>
                    <div data-i18n="Biaya Proyek AP">Biaya Proyek AP</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Treasury & Kas</span>
            </li>
            <li class="menu-item {{ (request()->is('finance/bank') || request()->is('finance/bank/*') || request()->is('finance/bank-reconciliation*') || request()->is('finance/petty-cash*') || request()->is('finance/security*')) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-bank"></i>
                    <div data-i18n="Kas & Bank">Kas &amp; Bank</div>
                    <i class="mdi mdi-lock-outline text-muted ms-auto" style="font-size: 13px;" title="Terproteksi PIN"></i>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('finance/bank') || (request()->is('finance/bank/*') && !request()->is('finance/bank-reconciliation*')) ? 'active' : '' }}">
                        <a href="{{ route('bank.index') }}" class="menu-link finance-pin-trigger" data-target-name="Daftar Rekening Bank">
                            <div data-i18n="Daftar Rekening Bank">Daftar Rekening Bank</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('finance/bank-reconciliation*') ? 'active' : '' }}">
                        <a href="{{ route('finance.reconciliation.index') }}" class="menu-link finance-pin-trigger" data-target-name="Rekonsiliasi Bank">
                            <div data-i18n="Rekonsiliasi Bank">Rekonsiliasi Bank</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('finance/petty-cash*') ? 'active' : '' }}">
                        <a href="{{ route('petty_cash.index') }}" class="menu-link finance-pin-trigger" data-target-name="Petty Cash (Kas Kecil)">
                            <div data-i18n="Petty Cash">Petty Cash (Kas Kecil)</div>
                        </a>
                    </li>
                    @if(in_array(Auth::user()?->role, ['Finance Manager', 'Finance', 'Developer']) || Auth::user()?->isDeveloper())
                        <li class="menu-item {{ request()->is('finance/security*') ? 'active' : '' }}">
                            <a href="{{ route('finance.security.manage') }}" class="menu-link">
                                <div data-i18n="Security">Security</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
            @php
                $pendingFeeCount = $pendingFeeCount ?? \App\Models\UnitQuotation::where('fee', '>', 0)
                    ->where('fee_payment_status', '!=', 'paid')
                    ->where('status', 'po_received')
                    ->count();
            @endphp
            <li class="menu-item {{ request()->is('finance/management-fee*') ? 'active' : '' }}">
                <a href="{{ route('finance.management-fee.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-cash-refund"></i>
                    <div data-i18n="Management Fee">Management Fee</div>
                    @if ($pendingFeeCount >= 1)
                        <div class="badge bg-warning rounded-pill ms-auto">{{ $formatSidebarBadge($pendingFeeCount) }}</div>
                    @endif
                </a>
            </li>

            @php
                $marketplaceHeldCount = $marketplaceHeldCount ?? \App\Models\Payment::where('method', 'Escrow')
                    ->where('disbursement_status', 'held')
                    ->whereNotNull('id_marketplace')
                    ->count();
            @endphp
            <li class="menu-item {{ request()->is('finance/marketplace*') ? 'active' : '' }}">
                <a href="{{ route('finance.marketplace.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-storefront-outline"></i>
                    <div data-i18n="Marketplace Management">Marketplace Management</div>
                    @if ($marketplaceHeldCount >= 1)
                        <div class="badge bg-warning rounded-pill ms-auto">{{ $formatSidebarBadge($marketplaceHeldCount) }}</div>
                    @endif
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Perpajakan (Taxation)</span>
            </li>
            <li class="menu-item {{ request()->is('finance/tax-report*') ? 'active' : '' }}">
                <a href="{{ route('finance.tax.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-calculator-variant-outline"></i>
                    <div data-i18n="Laporan Pajak (PPN & PPh)">Laporan Pajak (PPN &amp; PPh)</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Budget & Biaya</span>
            </li>
            <li class="menu-item {{ request()->is('finance/expense-budget*') ? 'active' : '' }}">
                <a href="{{ route('finance.expense-budget.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-chart-donut"></i>
                    <div data-i18n="Annual Budget">Annual Budget</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('expense-account*') || request()->is('expense*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-cash-multiple"></i>
                    <div data-i18n="Expense">Expense</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('expense-account*') ? 'active' : '' }}">
                        <a href="{{ route('expense-account.index') }}" class="menu-link">
                            <div data-i18n="COA">COA</div>
                        </a>
                    </li>
                    <li class="menu-item {{ (request()->is('expense') || request()->is('expense/*') || request()->is('expense-inventory*') || request()->is('expense-umum*') || request()->is('expense-ongkir*')) ? 'active' : '' }}">
                        <a href="{{ route('expense.index') }}" class="menu-link">
                            <div data-i18n="Expense">Expense</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ request()->is('fixed*') ? 'active' : '' }}">
                <a href="{{ route('fixed.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-domain"></i>
                    <div data-i18n="Fixed Asset">Fixed Asset</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('proyek-konstruksi*') ? 'active' : '' }}">
                <a href="{{ route('proyek-konstruksi.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-hammer-wrench"></i>
                    <div data-i18n="Proyek Konstruksi">Proyek Konstruksi</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Laporan Finansial</span>
            </li>
            <li class="menu-item {{ (request()->is('statement*') || request()->is('income*') || request()->is('balance*') || request()->is('equity*') || request()->is('cashflow*')) ? 'active' : '' }}">
                <a href="{{ route('finance.statement.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-book-open-outline"></i>
                    <div data-i18n="Laporan Laba Rugi (P&L)">Laporan Laba Rugi (P&amp;L)</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('report/project-profitability*') ? 'active' : '' }}">
                <a href="{{ route('report.project_profitability') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-chart-box-outline"></i>
                    <div data-i18n="Laba Rugi Proyek">Laba Rugi Proyek</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('finance/cashflow-forecast*') ? 'active' : '' }}">
                <a href="{{ route('finance.cashflow.forecast') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-chart-timeline-variant"></i>
                    <div data-i18n="Proyeksi Arus Kas">Proyeksi Arus Kas</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('tool-finance') ? 'active' : '' }}">
                <a href="{{ route('tool-finance.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-finance"></i>
                    <div data-i18n="Kelengkapan Data Tools">Kelengkapan Data Tools</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Procurement</span>
            </li>
            <li class="menu-item {{ request()->is('supplier') ? 'active' : '' }}">
                <a href="{{ route('supplier.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-multiple-outline"></i>
                    <div data-i18n="Supplier">Supplier</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('part-inquiry') || request()->is('part-inquiry/*') ? 'active' : '' }}">
                <a href="{{ route('part-inquiry.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-text-search"></i>
                    <div data-i18n="Part Inquiry">Part Inquiry</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('purchase-request') || request()->is('purchase-request/*') ? 'active' : '' }}">
                <a href="{{ route('purchase-request.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-document-outline"></i>
                    <div data-i18n="Purchase Request">Purchase Request</div>
                    @if (@$prCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($prCount) }}</div>
                    @endif
                </a>
            </li>
            <li class="menu-item {{ request()->is('purchase') || request()->is('purchase/*') ? 'active' : '' }}">
                <a href="{{ route('purchase.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-cart-outline"></i>
                    <div data-i18n="Purchase Order">Purchase Order</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">E-Stock</span>
            </li>

            {{-- Master Data --}}
            <li class="menu-item {{ request()->is('master/product') || request()->is('product') || request()->is('product/*') || request()->is('product-set') || request()->is('unit-acquisition') || request()->is('unit-acquisition/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-database-outline"></i>
                    <div data-i18n="Master Data">Master Data</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('master/product') ? 'active' : '' }}">
                        <a href="{{ route('master.product') }}" class="menu-link">
                            <div data-i18n="Product Master">Product Master</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('product') || request()->is('product/*') ? 'active' : '' }}">
                        <a href="{{ route('product.index') }}" class="menu-link">
                            <div data-i18n="Spare Part">Spare Part</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('product-set') ? 'active' : '' }}">
                        <a href="{{ route('product-set.index') }}" class="menu-link">
                            <div data-i18n="Product Set">Product Set</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('unit-acquisition') || request()->is('unit-acquisition/*') ? 'active' : '' }}">
                        <a href="{{ route('unit-acquisition.index') }}" class="menu-link">
                            <div data-i18n="Unit Acquisition">Unit Acquisition</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('work-orders*') ? 'active' : '' }}">
                        <a href="{{ route('work-orders.index') }}" class="menu-link">
                            <div data-i18n="Work Order">Work Order</div>
                        </a>
                    </li>

                </ul>
            </li>

            {{-- Stock Movement --}}
            <li class="menu-item {{ request()->is('product-in') || request()->is('product-in/*') || request()->is('product-out') || request()->is('product-out/*') || request()->is('change-warehouse') || request()->is('change-warehouse/*') || request()->is('unit-product-in') || request()->is('unit-product-in/*') || request()->is('unit-product-out') || request()->is('unit-product-out/*') || request()->is('warehouse/intercompany*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-swap-horizontal"></i>
                    <div data-i18n="Stock Movement">Stock Movement</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('product-in') || request()->is('product-in/*') ? 'active' : '' }}">
                        <a href="{{ route('product-in.index') }}" class="menu-link">
                            <div data-i18n="Product In">Product In</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('product-out') || request()->is('product-out/*') ? 'active' : '' }}">
                        <a href="{{ route('product-out.index') }}" class="menu-link">
                            <div data-i18n="Product Out">Product Out</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('warehouse/intercompany*') ? 'active' : '' }}">
                        <a href="{{ route('intercompany.index') }}" class="menu-link">
                            <div data-i18n="Rekap BK Kojisha">Rekap BK Kojisha</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('change-warehouse') || request()->is('change-warehouse/*') ? 'active' : '' }}">
                        <a href="{{ route('change-warehouse.index') }}" class="menu-link">
                            <div data-i18n="Warehouse Transfer">Warehouse Transfer</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('unit-product-in') || request()->is('unit-product-in/*') ? 'active' : '' }}">
                        <a href="{{ route('unit-product-in.index') }}" class="menu-link">
                            <div data-i18n="Barang Masuk Unit">Barang Masuk Unit</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('unit-product-out') || request()->is('unit-product-out/*') ? 'active' : '' }}">
                        <a href="{{ route('unit-product-out.index') }}" class="menu-link">
                            <div data-i18n="Unit Keluar">Unit Keluar</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Monitoring --}}
            <li class="menu-item {{ request()->is('stock') || request()->is('stock/*') || request()->is('stock-opname') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-clipboard-list-outline"></i>
                    <div data-i18n="Monitoring">Monitoring</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('stock') || request()->is('stock/*') ? 'active' : '' }}">
                        <a href="{{ route('stock.index') }}" class="menu-link">
                            <div data-i18n="Current Stock">Current Stock</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('stock-opname') ? 'active' : '' }}">
                        <a href="{{ route('opname.index') }}" class="menu-link">
                            <div data-i18n="Stock Opname">Stock Opname</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">HR Management</span>
            </li>
            {{-- 1. Dashboard HR --}}
            <li class="menu-item {{ (request()->is('hr') || request()->is('hr/dashboard')) ? 'active' : '' }}">
                <a href="{{ route('hr.dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-view-dashboard-outline"></i>
                    <div data-i18n="Dashboard HR">Dashboard HR</div>
                </a>
            </li>

            {{-- 2. Sub-Menu: Kepegawaian & Aset --}}
            @php
                $isPersonnelOpen2 = request()->is('employees*') || request()->is('hr/employees*') || request()->is('hr/assets*') || request()->is('hr/evaluations*') || request()->is('hr/departments*') || request()->is('hr/positions*');
            @endphp
            <li class="menu-item {{ $isPersonnelOpen2 ? 'open active' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-account-group-outline"></i>
                    <div data-i18n="Kepegawaian & Aset">Kepegawaian &amp; Aset</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ (request()->is('employees*') || request()->is('hr/employees*')) ? 'active' : '' }}">
                        <a href="{{ route('employees.index') }}" class="menu-link">
                            <div data-i18n="Hub Karyawan">Hub Karyawan</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hr/assets*') ? 'active' : '' }}">
                        <a href="{{ route('hr.assets.index') }}" class="menu-link">
                            <div data-i18n="Alat Kerja">Alat Kerja Karyawan</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hr/evaluations*') ? 'active' : '' }}">
                        <a href="{{ route('hr.evaluations.index') }}" class="menu-link">
                            <div data-i18n="Evaluasi Kinerja">Evaluasi Kinerja</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- 3. Sub-Menu: Waktu & Kehadiran --}}
            @php
                $isTimeAttendanceOpen2 = request()->is('hr/attendances*') || request()->is('hr/leaves*');
            @endphp
            <li class="menu-item {{ $isTimeAttendanceOpen2 ? 'open active' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-calendar-clock-outline"></i>
                    <div data-i18n="Waktu & Kehadiran">Waktu &amp; Kehadiran</div>
                    @if ($hrPendingLeaveCount > 0)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($hrPendingLeaveCount) }}</div>
                    @endif
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('hr/attendances*') ? 'active' : '' }}">
                        <a href="{{ route('hr.attendances.index') }}" class="menu-link">
                            <div data-i18n="Presensi">Presensi &amp; Jam Kerja</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hr/leaves*') ? 'active' : '' }}">
                        <a href="{{ route('hr.leaves.index') }}" class="menu-link">
                            <div data-i18n="Cuti & Izin">Cuti &amp; Izin</div>
                            @if ($hrPendingLeaveCount > 0)
                                <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($hrPendingLeaveCount) }}</div>
                            @endif
                        </a>
                    </li>
                </ul>
            </li>

            {{-- 4. Sub-Menu: Payroll & Finansial --}}
            @php
                $isPayrollClaimsOpen2 = request()->is('hr/payrolls*') || request()->is('hr/bonuses*') || request()->is('hr/reimbursements*');
            @endphp
            <li class="menu-item {{ $isPayrollClaimsOpen2 ? 'open active' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-cash-multiple"></i>
                    <div data-i18n="Payroll & Finansial">Payroll &amp; Finansial</div>
                    @if ($hrPendingReimbursementCount > 0)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($hrPendingReimbursementCount) }}</div>
                    @endif
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('hr/payrolls*') ? 'active' : '' }}">
                        <a href="{{ route('hr.payrolls.index') }}" class="menu-link">
                            <div data-i18n="Payroll">Payroll &amp; Slip Gaji</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hr/bonuses*') ? 'active' : '' }}">
                        <a href="{{ route('hr.bonuses.index') }}" class="menu-link">
                            <div data-i18n="Bonus Semester">Bonus Semesteran</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hr/reimbursements*') ? 'active' : '' }}">
                        <a href="{{ route('hr.reimbursements.index') }}" class="menu-link">
                            <div data-i18n="Reimbursement">Klaim Reimbursement</div>
                            @if ($hrPendingReimbursementCount > 0)
                                <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($hrPendingReimbursementCount) }}</div>
                            @endif
                        </a>
                    </li>
                </ul>
            </li>

            {{-- 5. My Portal (ESS) --}}
            @if (Auth::user()?->employee)
            <li class="menu-item {{ request()->is('hr/my-portal*') ? 'active' : '' }}">
                <a href="{{ route('hr.portal.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-card-account-details-star-outline"></i>
                    <div data-i18n="My Portal">My Portal</div>
                </a>
            </li>
            @endif

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Helpdesk</span>
            </li>
            <li class="menu-item {{ request()->is('helpdesk') || request()->is('helpdesk/*') ? 'active' : '' }}">
                <a href="{{ route('helpdesk.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lifebuoy"></i>
                    <div data-i18n="Helpdesk">Helpdesk</div>
                    @if (@$openTicketCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($openTicketCount) }}</div>
                    @endif
                </a>
            </li>
        @elseif (Auth::user()?->role == 'Sales Manager')
            <!-- Dashboards -->
            <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
                <a href="{{ url('/') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('kanban*') ? 'active' : '' }}">
                <a href="{{ route('kanban.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-view-dashboard-outline"></i>
                    <div data-i18n="Kanban">Kanban</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('reports') ? 'active' : '' }}">
                <a href="{{ url('/reports') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-finance"></i>
                    <div data-i18n="Reports">Reports</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('overview') || request()->is('overview/*') || request()->is('overview/*/*') ? 'active' : '' }}">
                <a href="{{ url('/overview') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-eye-outline"></i>
                    <div data-i18n="Team Performance">Team Performance</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Client</span>
            </li>
            <li
                class="menu-item {{ request()->is('leads') || request()->is('leads/detail/*') || request()->is('existing') || request()->is('existing/*') || request()->is('customer-by-status') || request()->is('key-accounts') || request()->is('online-leads*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-account-group-outline"></i>
                    <div data-i18n="Client">Client</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('leads') || request()->is('leads/detail/*') ? 'active' : '' }}">
                        <a href="{{ url('leads') }}" class="menu-link">
                            <div data-i18n="Client">Client</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('customer-by-status') || request()->is('existing/*') ? 'active' : '' }}">
                        <a href="{{ route('index-status.customers') }}" class="menu-link">
                            <div data-i18n="CRM">CRM</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('key-accounts') ? 'active' : '' }}">
                        <a href="{{ route('key-accounts.index') }}" class="menu-link">
                            <div data-i18n="Key Accounts">Key Accounts</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('online-leads*') ? 'active' : '' }}">
                        <a href="{{ route('online-leads.index') }}" class="menu-link">
                            <div data-i18n="Leads Online">Leads Online</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ request()->is('quotation') || request()->is('quotation/*') || request()->is('po') || request()->is('loss') || request()->is('po/sales/*') || request()->is('archive/quotation') ? 'active' : '' }}">
                <a href="{{ route('quotation.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-email-outline"></i>
                    <div data-i18n="Quotation">Quotation</div>
                </a>
            </li>

            <li
                class="menu-item {{ request()->is('prospect') || request()->is('prospect/*') || request()->is('prospect-quotation') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-account-details-outline"></i>
                    <div data-i18n="Marketing Leads">Marketing Leads</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('prospect') || request()->is('prospect/*') ? 'active' : '' }}">
                        <a href="{{ route('prospect.index') }}" class="menu-link">
                            <div data-i18n="Marketing Leads">Marketing Leads</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('prospect-quotation') ? 'active' : '' }}">
                        <a href="{{ route('quotation.prospect') }}" class="menu-link">
                            <div data-i18n="Quotation">Quotation</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ request()->is('forecast*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-chart-box-plus-outline"></i>
                    <div data-i18n="Forecast">Forecast</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('forecast') && !request()->has('view') ? 'active' : '' }}">
                        <a href="{{ route('forecast.index') }}" class="menu-link">
                            <div data-i18n="Dashboard Forecast">Dashboard Forecast</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('forecast/setup') ? 'active' : '' }}">
                        <a href="{{ route('forecast.setup') }}" class="menu-link">
                            <div data-i18n="Quick Setup Mesin">Quick Setup Mesin</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('forecast/contracts') ? 'active' : '' }}">
                        <a href="{{ route('forecast.contracts') }}" class="menu-link">
                            <div data-i18n="Jadwal Kontrak Servis">Jadwal Kontrak Servis</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Sales Order</span>
            </li>
            <li class="menu-item {{ request()->is('suo') || request()->is('suo/*') ? 'active' : '' }}">
                <a href="{{ route('suo.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lightning-bolt-outline"></i>
                    <div data-i18n="Urgent Order">Urgent Order (SUO)</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('sales-order') || request()->is('pending-po/*') || request()->is('project-monitoring*') ? 'active' : '' }}">
                <a href="{{ route('pending-po.sales-order') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                    <div data-i18n="Sales Order">Sales Order</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('return') || request()->is('return/*') ? 'active' : '' }}">
                <a href="{{ route('return.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-archive-cancel"></i>
                    <div data-i18n="Return">Return</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Service Departement</span>
            </li>
            <li class="menu-item {{ request()->is('service-reports') || request()->is('service-reports/*') ? 'active' : '' }}">
                <a href="{{ route('service-reports.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                    <div data-i18n="Service Report">Service Report</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">E-Stock</span>
            </li>
            <li class="menu-item {{ request()->is('product') || request()->is('product/*') ? 'active' : '' }}">
                <a href="{{ route('product.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-package-variant"></i>
                    <div data-i18n="Spare Part">Spare Part (Stok Part)</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('unit') || request()->is('unit/*') ? 'active' : '' }}">
                <a href="{{ route('unit.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-desktop-tower"></i>
                    <div data-i18n="Stock Unit">Stock Unit</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Team</span>
            </li>
            <li class="menu-item {{ request()->is('sales-target') ? 'active' : '' }}">
                <a href="{{ route('sales-target.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-group-outline"></i>
                    <div data-i18n="Sales Management">Sales Management</div>
                </a>
            </li>
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Helpdesk</span>
            </li>
            <li class="menu-item {{ request()->is('helpdesk') || request()->is('helpdesk/*') ? 'active' : '' }}">
                <a href="{{ route('helpdesk.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lifebuoy"></i>
                    <div data-i18n="Helpdesk">Helpdesk</div>
                    @if (@$openTicketCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($openTicketCount) }}</div>
                    @endif
                </a>
            </li>
            @if (Auth::user()?->hasConstructionProjectAccess())
            <li class="menu-item {{ request()->is('proyek-konstruksi*') ? 'active' : '' }}">
                <a href="{{ route('proyek-konstruksi.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-hammer-wrench"></i>
                    <div data-i18n="Proyek Konstruksi">Proyek Konstruksi</div>
                </a>
            </li>
            @endif
        @elseif (Auth::user()?->role == 'Project Manager')
            <!-- Dashboards -->
            <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
                <a href="{{ url('/') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                    <div data-i18n="Dashboards">Dashboards</div>
                </a>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Project Management</span>
            </li>
            <li class="menu-item {{ request()->is('project-monitoring*') || (request()->is('sales-order') && request('tab') == 'project-monitoring') ? 'active' : '' }}">
                <a href="{{ route('pending-po.sales-order', ['tab' => 'project-monitoring']) }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-monitor-dashboard"></i>
                    <div data-i18n="Project Monitoring">Project Monitoring</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('kanban*') && !request()->is('accounting/monitoring-document*') ? 'active' : '' }}">
                <a href="{{ route('kanban.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-view-dashboard-outline"></i>
                    <div data-i18n="Project Kanban">Project Kanban</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('project-reports*') || (request()->is('service-reports*') && request('tab') == 'project') ? 'active' : '' }}">
                <a href="{{ route('service-reports.index', ['tab' => 'project']) }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-clipboard-text-clock-outline"></i>
                    <div data-i18n="Daily Project Report">Daily Project Report</div>
                </a>
            </li>
            <li class="menu-item {{ (request()->is('service-reports') || request()->is('service-reports/*')) && request('tab') != 'project' && !request()->is('project-reports*') ? 'active' : '' }}">
                <a href="{{ route('service-reports.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-wrench-outline"></i>
                    <div data-i18n="Service Report">Service Report</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('bast') || request()->is('bast/*') ? 'active' : '' }}">
                <a href="{{ route('bast.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-file-sign"></i>
                    <div data-i18n="BAST">BAST (Serah Terima)</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('piping-rab*') || request()->is('piping-materials*') || request()->is('schematics*') || request()->is('hvac*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-compass-outline"></i>
                    <div data-i18n="Engineering & RAB">Engineering &amp; RAB</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('piping-rab*') ? 'active' : '' }}">
                        <a href="{{ route('piping-rab.index') }}" class="menu-link">
                            <div data-i18n="Estimasi / RAB Piping">Estimasi / RAB Piping</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('schematics*') ? 'active' : '' }}">
                        <a href="{{ route('schematics.index') }}" class="menu-link">
                            <div data-i18n="Schematic Diagram">Schematic Diagram</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hvac/quick-calculator*') ? 'active' : '' }}">
                        <a href="{{ route('hvac.quick-calculator') }}" class="menu-link">
                            <div data-i18n="HVAC Quick Estimator">HVAC Quick Estimator</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hvac/projects*') || request()->is('hvac/rooms*') ? 'active' : '' }}">
                        <a href="{{ route('hvac.project.index') }}" class="menu-link">
                            <div data-i18n="Daftar Proyek HVAC">Daftar Proyek HVAC</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('hvac/master-catalog*') ? 'active' : '' }}">
                        <a href="{{ route('hvac.master.index') }}" class="menu-link">
                            <div data-i18n="Master Unit HVAC">Master Unit HVAC</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ request()->is('report/project-profitability*') || request()->is('payable/expenses*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-cash-multiple"></i>
                    <div data-i18n="Project Cost & Profit">Project Cost &amp; Profit</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('report/project-profitability*') ? 'active' : '' }}">
                        <a href="{{ route('report.project_profitability') }}" class="menu-link">
                            <div data-i18n="Laba Rugi Proyek">Laba Rugi Proyek</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('payable/expenses*') ? 'active' : '' }}">
                        <a href="{{ route('payable.expenses') }}" class="menu-link">
                            <div data-i18n="Biaya Proyek AP">Biaya Proyek AP</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Sales &amp; Marketing</span>
            </li>
            <li
                class="menu-item {{ request()->is('leads') || request()->is('leads/detail/*') || request()->is('existing') || request()->is('existing/*') || request()->is('ru') || request()->is('existing-bangkrupt') || request()->is('leads-by-sales') || request()->is('customers-by-sales') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-account-group-outline"></i>
                    <div data-i18n="Client">Client</div>
                </a>

                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('leads-by-sales') ? 'active' : '' }}">
                        <a href="{{ route('index-sales.leads') }}" class="menu-link">
                            <div data-i18n="Leads By Sales">Leads By Sales</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('customers-by-sales') ? 'active' : '' }}">
                        <a href="{{ route('index-sales.customers') }}" class="menu-link">
                            <div data-i18n="Customers By Sales">Customers By Sales</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ request()->is('quotation') || request()->is('quotation/*') || request()->is('po') || request()->is('loss') || request()->is('po/sales/*') ? 'active' : '' }}">
                <a href="{{ route('quotation.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-email-outline"></i>
                    <div data-i18n="Quotation">Quotation</div>
                </a>
            </li>

            @php
                $isMailboxConfigured = Auth::user()?->isDeveloper() || (!empty(Auth::user()?->mailSetting?->smtp_username));
                $unreadInboxCount = $isMailboxConfigured ? (Auth::user()?->mailboxMessages()->where('folder', 'inbox')->where('is_read', false)->count() ?? 0) : 0;
            @endphp
            @if ($isMailboxConfigured)
            <li class="menu-item {{ request()->is('sales/mailbox*') ? 'active' : '' }}">
                <a href="{{ route('sales.mailbox.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-email-fast-outline"></i>
                    <div data-i18n="Mailbox">Mailbox</div>
                    @if ($unreadInboxCount > 0)
                        <div class="badge bg-primary rounded-pill ms-auto">{{ $formatSidebarBadge($unreadInboxCount) }}</div>
                    @endif
                </a>
            </li>
            @endif

            <li
                class="menu-item {{ request()->is('prospect') || request()->is('prospect/*') || request()->is('prospect-quotation') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-account-details-outline"></i>
                    <div data-i18n="Marketing Leads">Marketing Leads</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('prospect') || request()->is('prospect/*') ? 'active' : '' }}">
                        <a href="{{ route('prospect.index') }}" class="menu-link">
                            <div data-i18n="Marketing Leads">Marketing Leads</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('prospect-quotation') ? 'active' : '' }}">
                        <a href="{{ route('quotation.prospect') }}" class="menu-link">
                            <div data-i18n="Quotation">Quotation</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ request()->is('sales-order') || request()->is('pending-po/*') || request()->is('project-monitoring*') ? 'active' : '' }}">
                <a href="{{ route('pending-po.sales-order') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-list-box-outline"></i>
                    <div data-i18n="Sales Order">Sales Order</div>
                </a>
            </li>

            <li class="menu-item {{ request()->is('forecast*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-chart-box-plus-outline"></i>
                    <div data-i18n="Forecast">Forecast</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('forecast') && !request()->has('view') ? 'active' : '' }}">
                        <a href="{{ route('forecast.index') }}" class="menu-link">
                            <div data-i18n="Dashboard Forecast">Dashboard Forecast</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('forecast/setup') ? 'active' : '' }}">
                        <a href="{{ route('forecast.setup') }}" class="menu-link">
                            <div data-i18n="Quick Setup Mesin">Quick Setup Mesin</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('forecast/contracts') ? 'active' : '' }}">
                        <a href="{{ route('forecast.contracts') }}" class="menu-link">
                            <div data-i18n="Jadwal Kontrak Servis">Jadwal Kontrak Servis</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Library</span>
            </li>
            <li class="menu-item {{ request()->is('library/index/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons mdi mdi-library-shelves"></i>
                    <div data-i18n="Library">Library</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('library/index/marktool') ? 'active' : '' }}">
                        <a href="{{ route('marktool.index') }}" class="menu-link">
                            <div data-i18n="Marketing Tools">Marketing Tools</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/brosur') ? 'active' : '' }}">
                        <a href="{{ route('brosur.index') }}" class="menu-link">
                            <div data-i18n="Brosur">Brosur</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/partlist') ? 'active' : '' }}">
                        <a href="{{ route('partlist.index') }}" class="menu-link">
                            <div data-i18n="Partlist">Partlist</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('library/index/manbook') ? 'active' : '' }}">
                        <a href="{{ route('manbook.index') }}" class="menu-link">
                            <div data-i18n="Manual Book">Manual Book</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Helpdesk</span>
            </li>
            <li class="menu-item {{ request()->is('helpdesk') || request()->is('helpdesk/*') ? 'active' : '' }}">
                <a href="{{ route('helpdesk.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-lifebuoy"></i>
                    <div data-i18n="Helpdesk">Helpdesk</div>
                    @if (@$openTicketCount >= 1)
                        <div class="badge bg-danger rounded-pill ms-auto">{{ $formatSidebarBadge($openTicketCount) }}</div>
                    @endif
                </a>
            </li>
        @endif

        @if (Auth::user()?->isDeveloper())
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Developer Tools</span>
            </li>
            <li class="menu-item {{ request()->is('developer/dashboard*') || (request()->is('/') && request()->query('view') === 'developer') ? 'active' : '' }}">
                <a href="{{ route('developer.dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-view-dashboard-variant-outline text-primary"></i>
                    <div data-i18n="Dev Dashboard">Dev Dashboard</div>
                    <div class="badge bg-label-success rounded-pill ms-auto">Live</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('developer/mailbox-management*') ? 'active' : '' }}">
                <a href="{{ route('developer.mailbox.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-email-sync-outline"></i>
                    <div data-i18n="Mailbox Management">Mailbox Management</div>
                    <div class="badge bg-label-primary rounded-pill ms-auto">Central</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('developer/maintenance*') ? 'active' : '' }}">
                <a href="{{ route('developer.maintenance.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-server-network"></i>
                    <div data-i18n="Maintenance Mode">Maintenance Mode</div>
                </a>
            </li>
        @endif

        @if (Auth::user()?->role == 'Guest')
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Daily Project Reports</span>
            </li>
            <li class="menu-item {{ request()->is('service-reports*') || request()->is('project-reports') || (request()->is('project-reports/*') && !request()->is('project-reports/create')) || request()->is('/') ? 'active' : '' }}">
                <a href="{{ route('service-reports.index', ['tab' => 'project']) }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-clipboard-text-clock-outline"></i>
                    <div data-i18n="Daily Reports">Daily Reports</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('project-reports/create') ? 'active' : '' }}">
                <a href="{{ route('project-reports.create') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-plus-box-outline"></i>
                    <div data-i18n="Buat Daily Report">Buat Daily Report</div>
                </a>
            </li>
            @if (Auth::user()?->hasConstructionProjectAccess())
            <li class="menu-item {{ request()->is('proyek-konstruksi*') ? 'active' : '' }}">
                <a href="{{ route('proyek-konstruksi.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-hammer-wrench"></i>
                    <div data-i18n="Proyek Konstruksi">Proyek Konstruksi</div>
                </a>
            </li>
            @endif
        @endif

        @if (Auth::user()?->role == 'Client Vendor')
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">Vendor Portal</span>
            </li>
            <li class="menu-item {{ request()->is('/') ? 'active' : '' }}">
                <a href="{{ url('/') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-view-dashboard-outline"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('project-reports/create') ? 'active' : '' }}">
                <a href="{{ route('project-reports.create') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-plus-box-outline text-primary"></i>
                    <div data-i18n="Buat Daily Report">Buat Daily Report</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('project-reports/*') && !request()->is('project-reports/create') ? 'active' : '' }}">
                <a href="{{ url('/#section-project-reports') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-clipboard-text-clock-outline"></i>
                    <div data-i18n="Daily Reports">Daftar Daily Report</div>
                </a>
            </li>
        @endif

        {{-- Akses Universal Portal Karyawan (ESS) untuk seluruh staf yang sudah terhubung dengan data Employee --}}
        @if (Auth::user() && !in_array(Auth::user()->role, ['Client', 'Client Vendor', 'Logistic']) && Auth::user()->employee)
            <li class="menu-header fw-light mt-4">
                <span class="menu-header-text">My Portal</span>
            </li>
            <li class="menu-item {{ request()->is('hr/my-portal*') ? 'active' : '' }}">
                <a href="{{ route('hr.portal.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-clock-fast text-success"></i>
                    <div data-i18n="My Portal">My Portal</div>
                </a>
            </li>
        @endif
    </ul>
</aside>
