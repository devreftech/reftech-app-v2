@extends('layouts.sales.app')
@section('title', 'Kanban Board - ' . $board->title)

@php
    $activeLabels = $board->labels ?? [
        'primary' => 'Biru',
        'success' => 'Hijau',
        'danger' => 'Merah',
        'warning' => 'Kuning',
        'info' => 'Cyan',
        'secondary' => 'Abu-abu',
    ];
@endphp

@section('content')
    <div class="app-kanban">
        <!-- Board Header -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center py-3 mb-4 gap-3 border-bottom">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="mdi mdi-view-week-outline" style="font-size: 19px;"></i>
                    </div>
                    <h4 class="fw-bold mb-0 text-heading" style="font-size: 1.15rem; letter-spacing: -0.2px;">{{ $board->title }}</h4>
                </div>
                
                <div class="dropdown ms-1">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle rounded-pill" type="button" id="boardSwitcher" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 12px; font-weight: 600; padding: 5px 12px;">
                        <i class="mdi mdi-swap-horizontal me-1"></i> Switch Board
                    </button>
                    <div class="dropdown-menu" aria-labelledby="boardSwitcher">
                        @foreach ($myBoards as $mb)
                            <a class="dropdown-item {{ $mb->id == $board->id ? 'active' : '' }}" href="{{ route('kanban.boards.show', $mb->id) }}">
                                {{ $mb->title }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Search Box -->
                <div class="input-group input-group-merge input-group-sm ms-sm-2" style="width: 240px;">
                    <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="kanbanSearchInput" placeholder="{{ $board->type === 'monitoring' ? 'Cari PO, Client, RJO, KII...' : 'Cari kartu tugas...' }}">
                </div>

                @if ($board->type === 'monitoring')
                    <!-- Accounting Multi-Toggle Tab Switcher -->
                    <div class="accounting-filter-tabs d-inline-flex align-items-center p-1 bg-white border rounded-pill shadow-xs ms-sm-2" style="border-color: #e2e8f0 !important;">
                        @foreach ($accountingUsers ?? [] as $accountingUser)
                            @php
                                $firstName = explode(' ', trim($accountingUser->name))[0];
                            @endphp
                            <button type="button" class="btn btn-xs rounded-pill accounting-tab-btn active px-3 py-1 fw-bold" data-accounting-id="{{ $accountingUser->id }}" style="font-size: 12px; transition: all 0.2s ease;" title="{{ $accountingUser->name }}">
                                <i class="mdi mdi-account-outline me-1"></i> {{ $firstName }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('kanban.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill" style="font-size: 12px;">
                    <i class="mdi mdi-arrow-left me-1"></i> Portal
                </a>
                @if (auth()->user()->role === 'Admin' || auth()->id() == $board->created_by)
                    <!-- Notification Bell Dropdown for Delete Requests -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm position-relative dropdown-toggle hide-arrow rounded-circle" type="button" id="deleteRequestsDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px; padding: 0;">
                            <i class="mdi mdi-bell-outline"></i>
                            <span class="badge bg-danger rounded-circle position-absolute top-0 start-100 translate-middle" id="deleteRequestsBadge" style="display: none; padding: 3px 6px; font-size: 9px; line-height: 1; transform: translate(-25%, -25%) !important;">0</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="deleteRequestsDropdownBtn" id="deleteRequestsContainer" style="width: 320px; max-height: 400px; overflow-y: auto; font-size: 13px; z-index: 1050;">
                            <li><h6 class="dropdown-header px-2 py-1">Pengajuan Hapus Tugas</h6></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <div id="deleteRequestsList">
                                <li class="text-center py-3 text-muted">Loading...</li>
                            </div>
                        </ul>
                    </div>
                @endif
                @if (auth()->user()->role === 'Admin' || (auth()->user()->role === 'Accounting' && $board->type === 'monitoring'))
                    <button class="btn btn-outline-secondary btn-sm rounded-circle" data-bs-toggle="modal" data-bs-target="#boardSettingsModal" style="width: 32px; height: 32px; padding: 0;" title="Pengaturan Papan">
                        <i class="mdi mdi-cog"></i>
                    </button>
                @endif
                <button class="btn btn-outline-secondary btn-sm rounded-circle" type="button" id="btnToggleFullscreen" style="width: 32px; height: 32px; padding: 0;" title="Mode Layar Penuh">
                    <i class="mdi mdi-fullscreen" id="fsIcon"></i>
                </button>
            </div>
        </div>



        <!-- Kanban Board Wrapper -->
        <div class="position-relative kanban-scroll-wrapper">
            <div class="kanban-wrapper overflow-auto" style="min-height: 500px;"></div>
            
            <!-- Scroll indicator arrows -->
            <button type="button" class="btn btn-icon btn-primary rounded-circle shadow position-absolute start-0 top-50 translate-middle-y ms-3 btn-board-scroll" id="btnScrollBoardLeft" style="z-index: 99; width: 40px; height: 40px; padding: 0;">
                <i class="mdi mdi-chevron-left" style="font-size: 24px;"></i>
            </button>
            <button type="button" class="btn btn-icon btn-primary rounded-circle shadow position-absolute end-0 top-50 translate-middle-y me-3 btn-board-scroll" id="btnScrollBoardRight" style="z-index: 99; width: 40px; height: 40px; padding: 0;">
                <i class="mdi mdi-chevron-right" style="font-size: 24px;"></i>
            </button>
        </div>
    </div>

    <!-- Create Task Modal -->
    <div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createTaskForm">
                    <input type="hidden" id="createTaskColumnId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="createTaskTitle" class="form-label">Task Title</label>
                            @if ($board->type === 'monitoring')
                                <select class="form-select select2-create" id="createTaskTitleSelect" required style="width: 100%;">
                                    <option value="">-- Pilih PO + Perusahaan --</option>
                                </select>
                                <input type="hidden" id="createTaskTitle" name="title">
                                <input type="hidden" id="createTaskPendingPoId" name="pending_po_id">
                            @else
                                <input type="text" class="form-control" id="createTaskTitle" required placeholder="Enter task title">
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="createTaskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="createTaskDescription" rows="2" placeholder="Task description..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="createTaskAssignee" class="form-label">Assign To</label>
                            <select class="form-select select2-create" id="createTaskAssignee" name="assignees[]" multiple="multiple" data-placeholder="Choose assignee">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="createTaskDueDate" class="form-label">Due Date</label>
                            <input type="text" class="form-control flatpickr" id="createTaskDueDate" placeholder="YYYY-MM-DD">
                        </div>
                        <div class="mb-3">
                            <label for="createTaskPriority" class="form-label">Prioritas</label>
                            <select class="form-select" id="createTaskPriority">
                                <option value="low">Rendah</option>
                                <option value="medium" selected>Sedang</option>
                                <option value="high">Tinggi</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Task Details Modal (Center) -->
    <div class="modal fade" id="taskDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header border-bottom flex-column align-items-start pb-2">
                    <div class="d-flex align-items-center w-100 justify-content-between">
                        <!-- Top Left: Status selector + Title -->
                        <div class="d-flex align-items-center">
                            <div class="dropdown me-3">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="dropdownTaskStatus" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span id="currentStatusText">Status</span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownTaskStatus" id="dropdownTaskStatusMenu">
                                    @foreach ($board->columns as $column)
                                        @if ($board->type === 'monitoring' && auth()->user()->role === 'ServiceM' && (in_array(strtoupper(trim($column->title)), ['INVOICE', 'CANCEL PO']) || str_contains(strtoupper($column->title), 'PO MENYUSUL')))
                                            @continue
                                        @endif
                                        <li><a class="dropdown-item btn-change-status-top" href="javascript:void(0);" data-column-id="column_{{ $column->id }}">{{ $column->title }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <h4 class="modal-title fw-bold" id="taskDetailsModalLabel">Judul Tugas</h4>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Quick Actions Row right under title -->
                    <div class="d-flex flex-wrap gap-2 mt-3 mb-1">
                        @if ($board->type !== 'monitoring')
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-quick-member" id="btnQuickMember">
                                <i class="mdi mdi-account-outline me-1"></i>+ Members
                            </button>
                        @endif
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-quick-date" id="btnQuickDate">
                            <i class="mdi mdi-calendar-blank-outline me-1"></i>+ Dates
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-quick-checklist" id="btnQuickChecklist">
                            <i class="mdi mdi-checkbox-marked-outline me-1"></i>+ Checklist
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-quick-attachment" id="btnQuickAttachment">
                            <i class="mdi mdi-paperclip me-1"></i>+ Attachment
                        </button>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Left Section (Width 7/12): Main task info, labels, checklists -->
                        <div class="col-lg-7 border-end pe-lg-4">
                            
                            <!-- Sales Order / PO details if exists -->
                             <div id="soDetailsContainer" class="mb-4" style="display: none;">
                                 <div class="card border border-light-subtle shadow-sm" style="background-color: #fbfbfc; border-radius: 12px; border: 1px solid #eef0f4 !important;">
                                     <div class="card-body p-3.5">
                                         <h6 class="fw-bold mb-3 text-dark d-flex align-items-center pb-2" style="font-size: 14.5px; border-bottom: 2px solid #5a8dee;">
                                             <i class="mdi mdi-file-document-outline me-2 text-primary" style="font-size: 18px;"></i> Detail Purchase Order
                                         </h6>
                                         <div class="row g-3" style="font-size: 13px;">
                                             <div class="col-sm-6">
                                                 <span class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Nomor PO</span>
                                                 <strong id="soPoNumber" class="text-dark" style="font-size: 13.5px;"></strong>
                                             </div>
                                             <div class="col-sm-6">
                                                 <span class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Nomor SO</span>
                                                 <span id="soSoNumber" class="text-dark fw-semibold" style="font-size: 13.5px;">-</span>
                                             </div>
                                             <div class="col-sm-6">
                                                 <span class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Entity / Tipe Invoice</span>
                                                 <span id="soEntityType" class="badge" style="font-size: 11px; font-weight: 700; padding: 3px 8px;">-</span>
                                             </div>
                                             <div class="col-sm-6">
                                                 <span class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Client / Perusahaan</span>
                                                 <strong id="soClientName" class="text-dark" style="font-size: 13.5px;"></strong>
                                             </div>
                                             <div class="col-sm-6">
                                                 <span class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Nomor Penawaran (Quote)</span>
                                                 <span id="soQuoteNumber" style="font-size: 13px;"></span>
                                             </div>
                                             @if (auth()->user()->role !== 'ServiceM')
                                             <div class="col-sm-6">
                                                 <span class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Total Nett</span>
                                                 <span id="soQuoteNett" class="fw-bold text-primary" style="font-size: 13.5px;"></span>
                                             </div>
                                             @endif
                                             <div class="col-sm-6">
                                                 <span class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Sales Person</span>
                                                 <span id="soSalesPerson" class="text-dark" style="font-size: 13px;"></span>
                                             </div>

                                             <!-- Invoices & Payments status -->
                                             @if (auth()->user()->role !== 'ServiceM')
                                             <div class="col-sm-12 mt-2">
                                                 <span class="text-muted d-block mb-1.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Invoice & Status Pembayaran</span>
                                                 <div id="soInvoicesContainer" class="d-flex flex-column gap-1.5 mt-1">
                                                     <!-- Dynamically populated via JS -->
                                                 </div>
                                             </div>
                                             @endif
 
                                             @if ($board->type === 'monitoring')
                                             <!-- Delivery / Surat Jalan -->
                                             <div class="col-sm-12 mt-2">
                                                 <span class="text-muted d-block mb-1.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Surat Jalan (Delivery Order)</span>
                                                 <div id="soDeliveriesContainer" class="d-flex flex-wrap gap-1.5 mt-1">
                                                     <!-- Dynamically populated via JS -->
                                                 </div>
                                             </div>
 
                                             <!-- Service Report Selection -->
                                             <div class="col-sm-12 mt-3 pt-2 border-top">
                                                 <span class="text-muted d-block mb-1.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Service Report</span>
                                                 
                                                 <!-- Connected Service Report Card Display -->
                                                 <div id="connectedReportContainer" style="display: none;" class="mb-2">
                                                     <div class="d-flex align-items-center justify-content-between p-2.5 rounded border border-success bg-white" style="font-size: 12.5px; border-color: #71dd37 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                                         <div class="d-flex align-items-center gap-2">
                                                             <i class="mdi mdi-file-check-outline text-success" style="font-size: 20px;"></i>
                                                             <div>
                                                                 <a id="connectedReportLink" href="#" target="_blank" class="fw-bold text-success d-block text-decoration-none"></a>
                                                                 <small id="connectedReportDate" class="text-muted" style="font-size: 10.5px;"></small>
                                                             </div>
                                                         </div>
                                                         <button type="button" class="btn btn-xs btn-outline-danger px-2 btn-unlink-report" style="padding: 2px 6px;">
                                                             <i class="mdi mdi-link-off me-1"></i>Putuskan
                                                         </button>
                                                     </div>
                                                 </div>

                                                 <!-- Select & Connect Form Container -->
                                                 <div id="reportSelectorContainer" style="display: block;" class="mt-1">
                                                     <div class="d-flex align-items-center gap-2">
                                                         <div class="flex-grow-1">
                                                             <select class="form-select select2-edit" id="editTaskServiceReport" name="service_report_id" style="width: 100%;">
                                                                 <!-- Dynamically populated via JS -->
                                                             </select>
                                                         </div>
                                                         <button class="btn btn-sm btn-primary d-flex align-items-center gap-1" type="button" id="btnConnectReport" style="height: 38px;">
                                                             <i class="mdi mdi-link-variant"></i> Hubungkan
                                                         </button>
                                                     </div>
                                                 </div>
                                             </div>
                                             @endif

                                             <!-- BAST -->
                                             <div class="col-sm-12 mt-3 pt-2 border-top">
                                                 <span class="text-muted d-block mb-1.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">BAST (Berita Acara Serah Terima)</span>
                                                 <div id="bastExistingContainer" style="display: none;">
                                                     <a id="bastExistingLink" href="#" target="_blank" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1">
                                                         <i class="mdi mdi-file-check-outline"></i> <span id="bastExistingLabel"></span>
                                                     </a>
                                                 </div>
                                                 <div id="bastCreateContainer" style="display: none;">
                                                     <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" id="btnCreateBastFromCard">
                                                         <i class="mdi mdi-file-sign"></i> Buat BAST
                                                     </button>
                                                 </div>
                                             </div>

                                             <!-- Detail Project (Project Monitoring / Sales Order) — cuma
                                                  muncul kalau card Ringkasan Kesehatan Keuangan gak ada
                                                  (belum ada PendingPO), soalnya link-nya sama, biar gak dobel. -->
                                             <div class="col-sm-12 mt-3 pt-2 border-top" id="soDetailProjectContainer">
                                                 <a id="soDetailProjectLink" href="#" target="_blank" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                                                     <i class="mdi mdi-open-in-new"></i> Detail Project
                                                 </a>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <!-- Ringkasan Kesehatan Keuangan (data sama kayak halaman Project Monitoring) -->
                             <div id="financialHealthContainer" class="mb-4" style="display: none;">
                                 <div class="card border border-light-subtle shadow-sm" style="background-color: #fbfbfc; border-radius: 12px; border: 1px solid #eef0f4 !important;">
                                     <div class="card-body p-3.5">
                                         <h6 class="fw-bold mb-3 text-dark d-flex align-items-center pb-2" style="font-size: 14.5px; border-bottom: 2px solid #71dd37;">
                                             <i class="mdi mdi-chart-line me-2 text-success" style="font-size: 18px;"></i> Ringkasan Kesehatan Keuangan
                                         </h6>
                                         <div class="row g-3" style="font-size: 13px;">
                                             <div class="col-4">
                                                 <span class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Revenue</span>
                                                 <strong id="fhRevenue" class="text-success" style="font-size: 13.5px;"></strong>
                                             </div>
                                             <div class="col-4">
                                                 <span class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Total Biaya</span>
                                                 <strong id="fhTotalCost" class="text-danger" style="font-size: 13.5px;"></strong>
                                             </div>
                                             <div class="col-4">
                                                 <span class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Net Profit</span>
                                                 <strong id="fhProfit" style="font-size: 13.5px;"></strong>
                                             </div>
                                             <div class="col-sm-12 mt-1">
                                                 <div class="d-flex justify-content-between mb-1">
                                                     <span class="text-muted small">Rasio Pengeluaran vs Margin</span>
                                                     <span class="badge" id="fhMarginBadge" style="font-size: 10.5px; font-weight: 700; padding: 3px 8px; background-color: rgba(113, 221, 55, 0.15); color: #71dd37;"></span>
                                                 </div>
                                                 <div class="progress rounded-pill" style="height: 10px; overflow: hidden; background-color: rgba(0,0,0,0.05);">
                                                     <div class="progress-bar bg-danger" role="progressbar" id="fhCostBar" style="width: 0%"></div>
                                                     <div class="progress-bar bg-success" role="progressbar" id="fhProfitBar" style="width: 0%"></div>
                                                 </div>
                                             </div>
                                             <div class="col-sm-12">
                                                 <a id="fhDetailLink" href="#" target="_blank" class="small">
                                                     <i class="mdi mdi-open-in-new me-1"></i>Lihat rincian di Project Monitoring
                                                 </a>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                            @if ($board->type !== 'monitoring')
                            <!-- Quotation terhubung -->
                            <div class="mb-4" id="quotationLinkContainer">
                                <label class="form-label text-muted fw-semibold" style="font-size: 11px;">Quotation Terhubung</label>
                                <div id="quotationLinkedView" class="align-items-center justify-content-between p-2 rounded border bg-white" style="display: none; font-size: 12.5px;">
                                    <div class="d-flex align-items-center gap-2 min-width-0">
                                        <i class="mdi mdi-file-document-outline text-primary" style="font-size: 18px;"></i>
                                        <div class="min-width-0">
                                            <a id="quotationLinkedNo" href="#" target="_blank" class="fw-bold text-primary d-block text-truncate text-decoration-none"></a>
                                            <small id="quotationLinkedCompany" class="text-muted d-block text-truncate"></small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-xs btn-outline-danger flex-shrink-0" id="btnUnlinkQuotation">
                                        <i class="mdi mdi-link-off me-1"></i>Putuskan
                                    </button>
                                </div>
                                <div id="quotationLinkForm" style="display: none;">
                                    <div class="d-flex align-items-center gap-2">
                                        <select class="form-select form-select-sm select2-link-quote" id="linkQuotationSelect" style="width: 100%;" data-placeholder="Cari no. quote / perusahaan..."></select>
                                        <button type="button" class="btn btn-sm btn-primary flex-shrink-0" id="btnLinkQuotation"><i class="mdi mdi-link-variant me-1"></i>Hubungkan</button>
                                    </div>
                                    <small class="text-muted" style="font-size: 10.5px;">Sambungkan kartu ini ke Unit Quotation yang belum punya kartu.</small>
                                </div>
                            </div>

                            <!-- Pengeluaran Project (manajemen biaya per kartu) -->
                            <div class="mb-4" id="taskExpenseContainer">
                                <div class="card border border-light-subtle shadow-sm" style="background-color: #fbfbfc; border-radius: 12px; border: 1px solid #eef0f4 !important;">
                                    <div class="card-body p-3.5">
                                        <div class="d-flex align-items-center justify-content-between pb-2 mb-3" style="border-bottom: 2px solid #ff9f43;">
                                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center" style="font-size: 14.5px;">
                                                <i class="mdi mdi-cash-multiple me-2 text-warning" style="font-size: 18px;"></i> Pengeluaran Project
                                            </h6>
                                            <span class="badge bg-label-warning fw-bold" id="taskExpenseTotal" style="font-size: 12px;">Rp 0</span>
                                        </div>

                                        <div id="taskExpensesList" class="d-flex flex-column gap-1 mb-3" style="font-size: 12.5px;"></div>

                                        <div id="taskExpenseFormWrap" style="display: none;">
                                            <button type="button" class="btn btn-xs btn-outline-primary" id="btnShowExpenseForm"><i class="mdi mdi-plus me-1"></i>Tambah Biaya</button>
                                            <form id="taskExpenseForm" class="mt-2 border rounded p-2 bg-white" style="display: none;" enctype="multipart/form-data">
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <input type="text" class="form-control form-control-sm" id="expenseName" placeholder="Nama biaya" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <select class="form-select form-select-sm" id="expenseCategory" required>
                                                            <option value="Transport">Transport</option>
                                                            <option value="Akomodasi">Akomodasi</option>
                                                            <option value="Konsumsi">Konsumsi</option>
                                                            <option value="Material">Material</option>
                                                            <option value="Alat">Alat</option>
                                                            <option value="Lain-lain">Lain-lain</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="text" class="form-control form-control-sm" id="expenseAmount" placeholder="Nominal (Rp)" inputmode="numeric" autocomplete="off" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="text" class="form-control form-control-sm flatpickr-expense" id="expenseDate" placeholder="YYYY-MM-DD" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="file" class="form-control form-control-sm" id="expenseReceipt" accept=".jpg,.jpeg,.png,.pdf">
                                                    </div>
                                                </div>
                                                <div class="text-end mt-2">
                                                    <button type="button" class="btn btn-xs btn-outline-secondary" id="btnCancelExpenseForm">Batal</button>
                                                    <button type="submit" class="btn btn-xs btn-primary">Simpan Biaya</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Daily Project Reports Section (Laporan Harian Proyek) -->
                            <div class="mb-4" id="taskProjectReportsContainer">
                                <div class="card border border-light-subtle shadow-sm" style="background-color: #f6faff; border-radius: 12px; border: 1px solid #d9e9ff !important;">
                                    <div class="card-body p-3.5">
                                        <div class="d-flex align-items-center justify-content-between pb-2 mb-3" style="border-bottom: 2px solid #007bff;">
                                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center" style="font-size: 14.5px;">
                                                <i class="mdi mdi-clipboard-text-clock-outline me-2 text-primary" style="font-size: 18px;"></i> Laporan Harian Proyek
                                            </h6>
                                            <span class="badge bg-label-primary fw-bold" id="taskProjectReportsCount" style="font-size: 12px;">0 Laporan</span>
                                        </div>

                                        <div id="taskProjectReportsList" class="d-flex flex-column gap-2 mb-3" style="font-size: 12.5px;">
                                            <!-- Dynamically populated via JS -->
                                        </div>

                                        <div class="text-end">
                                            <a href="#" target="_blank" class="btn btn-xs btn-primary" id="btnCreateDailyReport">
                                                <i class="mdi mdi-plus me-1"></i>Buat Daily Report Hari Ini
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Labels section -->
                            <div class="mb-4">
                                <label class="form-label text-muted fw-semibold" style="font-size: 11px;">Labels</label>
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    <div id="labelsListContainer" class="d-flex flex-wrap gap-1 align-items-center">
                                        <!-- Render dynamic label badges here -->
                                    </div>
                                    
                                    <!-- Add label dropdown plus button -->
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-xs btn-outline-secondary btn-icon rounded-circle dropdown-toggle hide-arrow" type="button" id="dropdownLabels" data-bs-toggle="dropdown" aria-expanded="false" style="width:24px;height:24px;min-width:24px;padding:0;">
                                            <i class="mdi mdi-plus" style="font-size: 14px;"></i>
                                        </button>
                                        <ul class="dropdown-menu p-2" aria-labelledby="dropdownLabels" style="min-width: 180px;">
                                            <li><h6 class="dropdown-header px-1 py-1">Pilih Label</h6></li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            @foreach($activeLabels as $color => $name)
                                                <li><a class="dropdown-item rounded btn-toggle-label py-1" href="javascript:void(0);" data-color="{{ $color }}"><span class="badge bg-{{ $color }} me-2">&nbsp;</span>{{ $name ?: $color }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Metadata row (Assignee, Due Date) -->
                            <div class="row mb-4">
                                <div class="col-sm-6 {{ $board->type === 'monitoring' ? 'd-none' : '' }}">
                                    <label for="editTaskAssignee" class="form-label text-muted fw-semibold" style="font-size: 11px;">Ditugaskan Kepada</label>
                                    <div class="w-100">
                                        <select class="form-select select2-edit" id="editTaskAssignee" name="assignees[]" multiple="multiple" data-placeholder="Pilih Penerima" style="width: 100%;">
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="editTaskDueDate" class="form-label text-muted fw-semibold" style="font-size: 11px;">Tanggal Batas Waktu</label>
                                    <input type="text" class="form-control flatpickr" id="editTaskDueDate" placeholder="YYYY-MM-DD">
                                </div>
                                <div class="col-sm-6">
                                    <label for="editTaskPriority" class="form-label text-muted fw-semibold" style="font-size: 11px;">Prioritas</label>
                                    <select class="form-select" id="editTaskPriority">
                                        <option value="low">Rendah</option>
                                        <option value="medium">Sedang</option>
                                        <option value="high">Tinggi</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Description area with inline editing -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold mb-0" style="font-size: 13.5px;"><i class="mdi mdi-text-align-left me-2"></i>Deskripsi</label>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" id="btnEditDescription">Edit</button>
                                </div>
                                
                                <!-- Static description view -->
                                <div id="descriptionStaticView" class="p-3 rounded bg-light" style="font-size: 13.5px; white-space: pre-wrap; min-height: 50px; color: #4f5157;">
                                    Tambahkan deskripsi detail tugas...
                                </div>
                                
                                <!-- Description edit form -->
                                <div id="descriptionEditForm" style="display: none;">
                                    <textarea class="form-control mb-2" id="editTaskDescription" rows="4" placeholder="Tambahkan deskripsi detail tugas..."></textarea>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-outline-secondary btn-xs me-1" id="btnCancelDescription">Batal</button>
                                        <button type="button" class="btn btn-primary btn-xs" id="btnSaveDescription">Simpan</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Attachments area -->
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2" style="font-size: 13.5px;"><i class="mdi mdi-paperclip me-2 text-primary"></i>Lampiran</label>
                                <div id="attachmentsListContainer" class="d-flex flex-column gap-2">
                                    <!-- Render attachments dynamically -->
                                </div>
                            </div>

                            <!-- Checklists Container -->
                            <div id="taskChecklistsContainer">
                                <!-- Render checklists dynamically -->
                            </div>
                        </div>

                        <!-- Right Section (Width 5/12): Combined chronological timeline feed & composer -->
                        <div class="col-lg-5 ps-lg-4 mt-4 mt-lg-0">
                            <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size: 11px; letter-spacing: 1px;">Aktivitas & Komentar</h6>
                            
                            <!-- Comment input -->
                            <div class="mb-3 position-relative pb-3 border-bottom">
                                <textarea class="form-control mb-2" id="commentTextInput" rows="2" placeholder="Tulis komentar... Ketik @ untuk me-mention anggota papan"></textarea>
                                <!-- Mention Dropdown -->
                                <div id="mentionDropdown" class="dropdown-menu shadow-sm" style="display: none; position: absolute; left: 0; top: 75px; z-index: 1100; width: 100%; max-height: 150px; overflow-y: auto;"></div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-primary btn-sm" id="submitCommentBtn">Kirim</button>
                                </div>
                            </div>

                            <!-- Unified chronological feed (comments & activities) -->
                            <div id="timelineFeedContainer" class="pe-1" style="max-height: 420px; overflow-y: auto;">
                                <!-- Dynamic comments and activity timeline -->
                            </div>

                            <!-- Delete Task action button at the bottom -->
                            <div class="pt-3 border-top mt-4 d-flex justify-content-between">
                                @if (auth()->user()->role === 'Admin' || auth()->id() == $board->created_by)
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="deleteTaskBtn">
                                        <i class="mdi mdi-trash-can-outline me-1"></i>Hapus Tugas
                                    </button>
                                @else
                                    <button type="button" class="btn btn-outline-warning btn-sm" id="requestDeleteTaskBtn">
                                        <i class="mdi mdi-alert-circle-outline me-1"></i>Ajukan Hapus Tugas
                                    </button>
                                @endif
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="editTaskId">
    <input type="file" id="taskAttachmentInput" style="display:none;">

    @if (auth()->user()->role === 'Admin' || (auth()->user()->role === 'Accounting' && $board->type === 'monitoring'))
        <!-- Board Settings Modal (Wide 2-Column Layout) -->
        <div class="modal fade" id="boardSettingsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                    <div class="modal-header bg-light py-3 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                <i class="mdi mdi-cog-outline" style="font-size: 20px;"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0 text-heading">Pengaturan Papan Kanban</h5>
                                <small class="text-muted">Atur informasi papan, kelola anggota, label kustom, dan susun urutan kolom</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="boardSettingsForm" enctype="multipart/form-data">
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <!-- SISI KIRI: Informasi Umum, Anggota, Label, dan Notifikasi -->
                                <div class="col-lg-6 border-end-lg pe-lg-4">
                                    <h6 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2" style="font-size: 13.5px;">
                                        <i class="mdi mdi-information-outline"></i> Informasi & Anggota Papan
                                    </h6>

                                    <div class="mb-3">
                                        <label for="settingsBoardTitle" class="form-label fw-semibold">Judul Papan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="settingsBoardTitle" name="title" value="{{ $board->title }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="settingsBoardDescription" class="form-label fw-semibold">Deskripsi</label>
                                        <textarea class="form-control" id="settingsBoardDescription" name="description" rows="2" placeholder="Deskripsi singkat papan...">{{ $board->description }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="settingsBoardMembers" class="form-label fw-semibold">Kelola Anggota (Members)</label>
                                        <select class="select2 form-select" id="settingsBoardMembers" name="member_ids[]" multiple="multiple" data-placeholder="Pilih anggota papan">
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" {{ $board->members->contains($user->id) ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->role }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3 pt-2 border-top">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0 fw-semibold">Kustom Nama Label (Tag)</label>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-xs btn-outline-primary dropdown-toggle hide-arrow" id="settingsAddLabelBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-plus"></i> Tambah Label
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end p-2" id="settingsAddLabelMenu" style="min-width: 150px;">
                                                    <!-- Dynamically populated via JS -->
                                                </ul>
                                            </div>
                                        </div>
                                        <div id="settingsLabelsContainer" class="row g-2">
                                            @foreach($activeLabels as $color => $name)
                                                <div class="col-sm-6 settings-label-item" data-color="{{ $color }}">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <span class="input-group-text bg-{{ $color }} text-white" style="width: 32px; border: 0;">&nbsp;</span>
                                                        <input type="text" class="form-control form-control-sm" name="labels[{{ $color }}]" value="{{ $board->labels ? ($board->labels[$color] ?? '') : '' }}" placeholder="{{ $name }}">
                                                        <button class="btn btn-outline-danger btn-remove-settings-label" type="button"><i class="mdi mdi-close"></i></button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    @if ($board->type === 'monitoring')
                                        <div class="mb-3 border-top pt-3">
                                            <label for="settingsNotificationSound" class="form-label fw-semibold">Suara Notifikasi PO Baru (mp3/wav/ogg)</label>
                                            <input type="file" class="form-control form-control-sm" id="settingsNotificationSound" name="notification_sound" accept="audio/*">
                                            @if ($board->notification_sound)
                                                <div class="d-flex align-items-center mt-2 gap-2">
                                                    <small class="text-success mb-0">
                                                        <i class="mdi mdi-check-circle-outline"></i> Suara aktif: {{ basename($board->notification_sound) }}
                                                    </small>
                                                    <button type="button" class="btn btn-xs btn-outline-info py-0 px-2" id="btnTestSound" data-sound="{{ asset($board->notification_sound) }}">
                                                        <i class="mdi mdi-volume-high me-1"></i> Test
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- SISI KANAN: Kelola Kolom & Urutan Kolom (Drag & Drop) -->
                                <div class="col-lg-6 ps-lg-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <h6 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2" style="font-size: 13.5px;">
                                                <i class="mdi mdi-view-column-outline"></i> Kelola & Urutan Kolom
                                            </h6>
                                            <small class="text-muted">Tahan & geser baris atau gunakan tombol &uarr;&darr; untuk menyusun urutan kolom</small>
                                        </div>
                                        <button type="button" class="btn btn-xs btn-primary" id="settingsAddColBtn">
                                            <i class="mdi mdi-plus me-1"></i> Tambah Kolom
                                        </button>
                                    </div>

                                    <div class="p-2 bg-light rounded border mb-3" style="max-height: 420px; overflow-y: auto;">
                                        <div id="settingsColumnsContainer" class="d-flex flex-column gap-2">
                                            @foreach ($board->columns as $index => $column)
                                                <div class="settings-column-item card border shadow-xs mb-0 bg-white" data-id="{{ $column->id }}" draggable="true" style="cursor: grab; transition: all 0.2s ease;">
                                                    <div class="card-body p-2 d-flex align-items-center gap-2">
                                                        <div class="col-drag-handle text-muted px-1" title="Tahan & geser untuk mengubah urutan" style="cursor: grab;">
                                                            <i class="mdi mdi-drag-vertical" style="font-size: 20px;"></i>
                                                        </div>
                                                        <span class="badge bg-label-primary rounded-pill col-order-badge px-2" style="font-size: 11px; min-width: 24px;">{{ $index + 1 }}</span>
                                                        <input type="hidden" name="columns[][id]" value="{{ $column->id }}">
                                                        <input type="text" class="form-control form-control-sm border-0 bg-transparent fw-semibold" name="columns[][title]" value="{{ $column->title }}" required placeholder="Nama Kolom" style="box-shadow: none;">
                                                        <div class="d-flex align-items-center gap-1 ms-auto flex-shrink-0">
                                                            <button type="button" class="btn btn-xs btn-icon btn-outline-secondary btn-move-col-up" title="Pindah ke Atas">
                                                                <i class="mdi mdi-arrow-up" style="font-size: 14px;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-xs btn-icon btn-outline-secondary btn-move-col-down" title="Pindah ke Bawah">
                                                                <i class="mdi mdi-arrow-down" style="font-size: 14px;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-xs btn-icon btn-outline-danger btn-remove-settings-column ms-1" title="Hapus Kolom">
                                                                <i class="mdi mdi-trash-can-outline" style="font-size: 14px;"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    @if ($board->type === 'monitoring')
                                        <div class="border-top pt-3">
                                            <h6 class="fw-bold text-primary mb-1 d-flex align-items-center gap-2" style="font-size: 13px;">
                                                <i class="mdi mdi-account-switch-outline"></i> Accounting &rarr; Sales Mapping
                                            </h6>
                                            <p class="text-muted mb-2" style="font-size: 11.5px;">Atur sales yang ditangani tiap Accounting (dipakai untuk filter tab Accounting).</p>
                                            <div id="accountingMappingContainer" style="max-height: 160px; overflow-y: auto;">
                                                @foreach ($accountingUsers as $accountingUser)
                                                    <div class="mb-2">
                                                        <label class="form-label mb-1" style="font-size: 12px; font-weight: 600;">{{ $accountingUser->name }}</label>
                                                        <select class="select2 form-select form-select-sm accounting-mapping-select" multiple="multiple" data-accounting-id="{{ $accountingUser->id }}" data-placeholder="Pilih sales yang ditangani">
                                                            @foreach ($salesUsers as $salesUser)
                                                                <option value="{{ $salesUser->id }}" {{ $accountingUser->handledSales->contains($salesUser->id) ? 'selected' : '' }}>{{ $salesUser->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-xs btn-outline-primary mt-1" id="btnSaveAccountingMapping">
                                                <i class="mdi mdi-content-save-outline me-1"></i> Simpan Mapping
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light py-2 border-top d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="mdi mdi-information-outline me-1"></i> Urutan kolom yang disusun di sini akan otomatis diterapkan pada Kanban Board.</small>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary btn-sm px-4" id="btnSaveBoardSettings">
                                    <i class="mdi mdi-check-circle-outline me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @include('components.modal.bast.create')
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/jkanban/jkanban.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-kanban.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        /* Custom modal width expansion for desktop screen layouts */
        @media (min-width: 1200px) {
            #taskDetailsModal .modal-xl {
                max-width: 1300px !important;
            }
        }

        /* Settings Modal Column Drag & Reorder Styling */
        .settings-column-item.dragging {
            opacity: 0.4 !important;
            background-color: #f8fafc !important;
            border: 1.5px dashed #4f46e5 !important;
        }
        .settings-column-item.drag-over {
            border-top: 2.5px solid #6366f1 !important;
        }
        .col-drag-handle {
            cursor: grab !important;
        }
        .col-drag-handle:active {
            cursor: grabbing !important;
        }
        .settings-column-item:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05) !important;
        }

        /* Fixed height for the entire Kanban board area to fit viewport */
        .kanban-wrapper {
            height: calc(100vh - 160px) !important;
            overflow: auto !important;
        }

        /* Column cards container takes remaining height and scrolls internally */
        .kanban-wrapper main.kanban-drag {
            height: calc(100vh - 225px) !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 8px 12px 12px 12px !important;
        }

        /* Webkit scrollbars styling for kanban columns */
        .kanban-wrapper main.kanban-drag::-webkit-scrollbar {
            width: 5px;
        }
        .kanban-wrapper main.kanban-drag::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.15);
            border-radius: 4px;
        }
        .kanban-wrapper main.kanban-drag::-webkit-scrollbar-track {
            background: transparent;
        }

        /* CSS overrides for Kanban Fullscreen Mode */
        body.kanban-fullscreen-mode .layout-menu {
            display: none !important;
        }
        body.kanban-fullscreen-mode .layout-navbar {
            display: none !important;
        }
        body.kanban-fullscreen-mode .layout-page {
            padding-left: 0 !important;
            padding-top: 0 !important;
            margin-left: 0 !important;
        }
        body.kanban-fullscreen-mode .content-wrapper {
            padding: 0 !important;
            margin: 0 !important;
        }
        body.kanban-fullscreen-mode .container-fluid,
        body.kanban-fullscreen-mode .container-xxl {
            max-width: 100% !important;
            width: 100% !important;
            padding: 10px 15px !important;
        }
        body.kanban-fullscreen-mode .kanban-wrapper {
            height: calc(100vh - 100px) !important;
        }
        body.kanban-fullscreen-mode main.kanban-drag {
            height: calc(100vh - 165px) !important;
        }

        /* Custom dynamic notification styles */
        .custom-toast {
            width: 350px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            pointer-events: auto;
            animation: toastSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards, toastFadeOut 0.5s ease 4.5s forwards;
            transform: translateX(120%);
        }
        @keyframes toastSlideIn {
            to { transform: translateX(0); }
        }
        @keyframes toastFadeOut {
            to { opacity: 0; transform: translateY(-20px); pointer-events: none; }
        }
        .toast-bell-icon {
            width: 40px;
            height: 40px;
            background: rgba(105, 108, 255, 0.1);
            color: #696cff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            animation: bellRing 1.5s ease-in-out infinite alternate;
        }
        @keyframes bellRing {
            0%, 100% { transform: rotate(0); }
            20% { transform: rotate(15deg); }
            40% { transform: rotate(-15deg); }
            60% { transform: rotate(10deg); }
            80% { transform: rotate(-10deg); }
        }

        .kanban-wrapper {
            cursor: grab;
        }
        .kanban-wrapper.grabbing-board,
        .kanban-wrapper.grabbing-board * {
            cursor: grabbing !important;
            user-select: none;
        }
        .kanban-board {
            min-height: 480px;
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 10px 10px 14px 10px;
            cursor: default;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
        }
        .kanban-board:hover {
            border-color: #cbd5e1;
        }
        .kanban-board header {
            padding: 8px 10px;
            background: linear-gradient(135deg, #666cff 0%, #545be8 100%);
            border-radius: 8px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 6px rgba(102, 108, 255, 0.25);
            border: none;
        }
        .kanban-col-header-custom {
            user-select: none;
        }
        .btn-add-task-custom {
            opacity: 0.9;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .kanban-board:hover .btn-add-task-custom {
            opacity: 1;
        }
        .btn-add-task-custom:hover {
            background-color: #ffffff !important;
            border-color: #ffffff !important;
            color: #666cff !important;
            transform: scale(1.08);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
        }
        /* Segmented Control / Tab Switcher for Accounting Filter */
        .accounting-filter-tabs {
            gap: 2px;
        }
        .accounting-tab-btn {
            border: none;
            color: #64748b;
            background: transparent;
            cursor: pointer;
            line-height: 1.4;
        }
        .accounting-tab-btn:hover {
            color: #1e293b;
            background-color: #f1f5f9;
        }
        .accounting-tab-btn.active {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.35);
        }
        .kanban-title-board {
            font-size: 15px;
            font-weight: 700;
            color: #4f5157;
        }
        .kanban-item {
            background: #fff;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            cursor: grab;
            border-left: 3px solid #666cff;
            transition: all 0.2s ease;
        }
        .kanban-item.border-left-primary { border-left: 3px solid #666cff !important; }
        .kanban-item.border-left-success { border-left: 3px solid #28c76f !important; }
        .kanban-item.border-left-danger { border-left: 3px solid #ea5455 !important; }
        .kanban-item.border-left-warning { border-left: 3px solid #ff9f43 !important; }
        .kanban-item.border-left-info { border-left: 3px solid #03c3ec !important; }
        .kanban-item.border-left-secondary { border-left: 3px solid #8592a3 !important; }
        .kanban-item:active {
            cursor: grabbing;
        }
        .kanban-item:hover {
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .kanban-drag {
            min-height: 380px !important;
            padding-bottom: 20px !important;
        }
        .kanban-title-button {
            position: static !important;
            display: block !important;
            width: 100% !important;
            margin: 10px 0 0 0 !important;
            text-align: center !important;
            border: 1px dashed #ced4da !important;
            background: #fff !important;
            color: #6c757d !important;
            border-radius: 6px !important;
            padding: 8px !important;
            font-size: 13px !important;
            transition: all 0.2s !important;
        }
        .kanban-title-button:hover {
            background-color: #e9ecef !important;
            color: #495057 !important;
        }
        .btn-board-scroll {
            opacity: 0 !important;
            transition: all 0.25s ease-in-out !important;
            pointer-events: none !important;
        }
        .kanban-scroll-wrapper:hover .btn-board-scroll.scroll-active {
            opacity: 0.85 !important;
            pointer-events: auto !important;
        }
        .kanban-scroll-wrapper:hover .btn-board-scroll.scroll-active:hover {
            opacity: 1 !important;
            transform: scale(1.1) translateY(-50%) !important;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/jkanban/jkanban.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('page-script')
    <script>
        $(document).ready(function() {
            const boardId = "{{ $board->id }}";
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            const boardType = "{{ $board->type }}";
            const userRole = "{{ auth()->user()->role }}";
            let kanbanBoardInstance = null;
            const boardLabels = @json($board->labels ?? (object)[]);
            const accountingSalesMap = @json(($accountingUsers ?? collect())->mapWithKeys(function ($acc) {
                return [(string) $acc->id => $acc->handledSales->pluck('id')->map(fn ($id) => (string) $id)->values()];
            }));
            const defaultLabels = {
                primary: 'Biru',
                success: 'Hijau',
                danger: 'Merah',
                warning: 'Kuning',
                info: 'Cyan',
                secondary: 'Abu-abu'
            };
            function getLabelName(color) {
                return (boardLabels && boardLabels[color]) ? boardLabels[color] : (defaultLabels[color] || color);
            }

            // Initialize select2
            function initSelect2(selector) {
                if ($(selector).length) {
                    $(selector).each(function() {
                        var $this = $(this);
                        $this.wrap('<div class="position-relative w-100"></div>').select2({
                            dropdownParent: $this.parent(),
                            placeholder: $this.data('placeholder'),
                            allowClear: true,
                            width: '100%'
                        });
                    });
                }
            }

            initSelect2('.select2');
            initSelect2('.select2-create');
            initSelect2('.select2-edit');

            // Initialize Flatpickr
            $(".flatpickr").flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true
            });
            $(".flatpickr-expense").flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true
            });

            // Select2 pencari Unit Quotation untuk fitur "Hubungkan ke Quotation"
            if (boardType !== 'monitoring' && $('#linkQuotationSelect').length) {
                $('#linkQuotationSelect').select2({
                    dropdownParent: $('#linkQuotationSelect').closest('.modal'),
                    placeholder: $('#linkQuotationSelect').data('placeholder'),
                    width: '100%',
                    ajax: {
                        url: '{{ route("kanban.linkable-quotations") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) { return { q: params.term || '' }; },
                        processResults: function (data) {
                            return {
                                results: (data.quotations || []).map(function (q) {
                                    return { id: q.id, text: `${q.no_quote} — ${q.company}` };
                                })
                            };
                        }
                    }
                });
            }

            // Date formatter to DD-MM-YYYY
            function formatDateDisplay(dateStr) {
                if (!dateStr) return '';
                const parts = dateStr.split('-');
                if (parts.length === 3) {
                    return `${parts[2]}-${parts[1]}-${parts[0]}`; // DD-MM-YYYY
                }
                return dateStr;
            }

            // Calculate due date urgency background label class
            function getDateUrgencyClass(dueDateStr) {
                if (!dueDateStr) return 'bg-label-secondary';
                const today = new Date();
                today.setHours(0,0,0,0);
                
                const dueDate = new Date(dueDateStr);
                dueDate.setHours(0,0,0,0);
                
                const diffTime = dueDate.getTime() - today.getTime();
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays < 0) {
                    return 'bg-label-danger'; // Overdue
                } else if (diffDays <= 1) {
                    return 'bg-label-warning'; // Due today or tomorrow
                }
                return 'bg-label-secondary'; // Safe
            }

            function getColumnHeaderTheme(title) {
                const t = (title || '').toUpperCase();
                if (t.includes('REFTECH')) return { color: '#4f46e5', bgSoft: '#eef2ff', icon: 'mdi-file-document-outline' };
                if (t.includes('E-COMMERCE') || t.includes('ECOMMERCE')) return { color: '#7c3aed', bgSoft: '#f5f3ff', icon: 'mdi-shopping-outline' };
                if (t.includes('JADWAL') || t.includes('DRAFT') || t.includes('SPK')) return { color: '#0284c7', bgSoft: '#f0f9ff', icon: 'mdi-calendar-clock-outline' };
                if (t.includes('PROCESS') || t.includes('PROSES')) return { color: '#d97706', bgSoft: '#fffbeb', icon: 'mdi-progress-clock' };
                if (t.includes('DONE') || t.includes('PAID') || t.includes('COMPLETED') || t.includes('SELESAI')) return { color: '#059669', bgSoft: '#ecfdf5', icon: 'mdi-check-decagram-outline' };
                if (t.includes('KEMBALI')) return { color: '#0d9488', bgSoft: '#f0fdfa', icon: 'mdi-keyboard-return' };
                if (t.includes('INVOICE')) return { color: '#2563eb', bgSoft: '#eff6ff', icon: 'mdi-receipt-text-outline' };
                if (t.includes('CANCEL') || t.includes('BATAL')) return { color: '#dc2626', bgSoft: '#fef2f2', icon: 'mdi-close-circle-outline' };
                if (t.includes('MENYUSUL') || t.includes('GANTI')) return { color: '#ea580c', bgSoft: '#fff7ed', icon: 'mdi-swap-horizontal' };
                return { color: '#475569', bgSoft: '#f1f5f9', icon: 'mdi-view-dashboard-outline' };
            }

            let lastTaskId = 0;

            // Fetch and render Kanban data
            function loadKanbanBoard() {
                // If a card is currently being dragged, skip reload to prevent glitching
                if ($('.gu-mirror').length > 0) return;

                $.ajax({
                    url: `/kanban/boards/${boardId}/data`,
                    method: 'GET',
                    success: function(data) {
                        const currentHash = JSON.stringify(data);
                        if (currentHash === lastBoardDataHash) {
                            return; // No changes, bypass DOM rebuild
                        }
                        lastBoardDataHash = currentHash;

                        // Track max task id
                        let maxTaskId = 0;
                        data.forEach(function(column) {
                            if (column.item) {
                                column.item.forEach(function(task) {
                                    const idNum = parseInt(task.id);
                                    if (idNum > maxTaskId) {
                                        maxTaskId = idNum;
                                    }
                                });
                            }
                        });
                        if (maxTaskId > lastTaskId) {
                            lastTaskId = maxTaskId;
                        }

                        $('.kanban-wrapper').html('');
                        
                        // Map items for jKanban rendering
                        const boards = data.map(function(column) {
                            const colTheme = getColumnHeaderTheme(column.title);
                            const count = column.item ? column.item.length : 0;
                            return {
                                id: column.id,
                                title: `
                                    <div class="kanban-col-header-custom d-flex align-items-center justify-content-between w-100" style="user-select: none;">
                                        <div class="d-flex align-items-center gap-2 min-width-0 flex-grow-1 pe-1">
                                            <div class="col-indicator-pill d-flex align-items-center justify-content-center" style="width: 26px; height: 26px; border-radius: 6px; background: rgba(255, 255, 255, 0.2); color: #ffffff; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.06);">
                                                <i class="mdi ${colTheme.icon}" style="font-size: 15px;"></i>
                                            </div>
                                            <span class="kanban-col-title text-truncate fw-bold" style="font-size: 12.5px; color: #ffffff; letter-spacing: 0.2px;" title="${column.title}">
                                                ${column.title}
                                            </span>
                                            <span class="badge rounded-pill" style="background: rgba(255, 255, 255, 0.22); color: #ffffff; font-size: 10.5px; font-weight: 700; padding: 2px 7px; flex-shrink: 0; border: 1px solid rgba(255, 255, 255, 0.35);">
                                                ${count}
                                            </span>
                                        </div>
                                        <button class="btn btn-xs btn-icon btn-add-task-custom" data-column-id="${column.id}" style="width: 26px; height: 26px; min-width: 26px; border-radius: 6px; color: #ffffff; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); transition: all 0.2s ease;" type="button" title="Tambah Tugas ke ${column.title}">
                                            <i class="mdi mdi-plus" style="font-size: 15px;"></i>
                                        </button>
                                    </div>
                                `,
                                item: column.item.map(function(task) {
                                    let avatarsHtml = '';
                                    if (task.assignees && task.assignees.length > 0) {
                                        avatarsHtml = '<div class="avatar-group d-flex align-items-center">';
                                        task.assignees.forEach(function(member, index) {
                                            let initial = member.name.charAt(0).toUpperCase();
                                            let style = index > 0 ? 'margin-left: -6px;' : '';
                                            avatarsHtml += `
                                                <div class="avatar avatar-xs pull-up" data-bs-toggle="tooltip" data-bs-placement="top" title="${member.name}" style="${style}">
                                                    ${member.avatar ? 
                                                        `<img src="${member.avatar}" class="rounded-circle" style="width:20px;height:20px;object-fit:cover;border: 1.5px solid #fff;">` : 
                                                        `<span class="avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center" style="width:20px;height:20px;font-size:9px;line-height:20px;border: 1.5px solid #fff;">${initial}</span>`
                                                    }
                                                </div>`;
                                        });
                                        avatarsHtml += '</div>';
                                    }

                                    let checklistBadgeHtml = '';
                                    let progressBarHtml = '';
                                    if (task.total_checklists > 0) {
                                        const isDone = task.completed_checklists === task.total_checklists;
                                        const badgeClass = isDone ? 'bg-label-success' : 'bg-label-secondary';
                                        checklistBadgeHtml = `
                                            <span class="badge ${badgeClass}" style="font-size:10px; padding: 3px 6px;" title="Progres checklist">
                                                <i class="mdi mdi-checkbox-marked-circle-outline me-1" style="font-size: 11px;"></i>${task.completed_checklists}/${task.total_checklists}
                                            </span>
                                        `;

                                        const percent = Math.round((task.completed_checklists / task.total_checklists) * 100);
                                        const barColor = percent === 100 ? 'bg-success' : 'bg-primary';
                                        progressBarHtml = `
                                            <div class="mt-2" style="font-size: 10px;">
                                                <div class="d-flex justify-content-between text-muted mb-1">
                                                    <span>Checklist</span>
                                                    <span>${percent}%</span>
                                                </div>
                                                <div class="progress" style="height: 4px; background-color: #ebedf2; border-radius: 2px;">
                                                    <div class="progress-bar ${barColor}" role="progressbar" style="width: ${percent}%;" aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        `;
                                    }

                                    let entityBadgeHtml = '';
                                    if (boardType === 'monitoring' && task.entity_type) {
                                        if (task.entity_type === 'KII') {
                                            entityBadgeHtml = `<span class="badge bg-danger text-white fw-bold" style="font-size: 10px; padding: 3px 7px; border-radius: 4px; line-height: 1; letter-spacing: 0.3px;" title="Invoice Kojisha (KII)">KII</span>`;
                                        } else {
                                            entityBadgeHtml = `<span class="badge bg-primary text-white fw-bold" style="font-size: 10px; padding: 3px 7px; border-radius: 4px; line-height: 1; letter-spacing: 0.3px;" title="Invoice Reftech (RJO)">RJO</span>`;
                                        }
                                    }

                                    let footerHtml = `
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="d-flex align-items-center flex-wrap gap-1">
                                                ${entityBadgeHtml}
                                                ${task.due_date ? `<span class="badge ${getDateUrgencyClass(task.due_date)}" style="font-size:10px;"><i class="mdi mdi-calendar-blank-outline me-1"></i>${formatDateDisplay(task.due_date)}</span>` : ''}
                                                ${checklistBadgeHtml}
                                            </div>
                                            ${avatarsHtml}
                                        </div>
                                    `;

                                    let priorityHtml = '';
                                    const taskPriority = task.priority || 'medium';
                                    if (taskPriority === 'high') {
                                        priorityHtml = `<span class="badge bg-danger" style="font-size: 8px; padding: 2px 5px; border-radius: 3px; line-height: 1;">Tinggi</span>`;
                                    } else if (taskPriority === 'low') {
                                        priorityHtml = `<span class="badge bg-info" style="font-size: 8px; padding: 2px 5px; border-radius: 3px; line-height: 1;">Rendah</span>`;
                                    } else {
                                        priorityHtml = `<span class="badge bg-warning" style="font-size: 8px; padding: 2px 5px; border-radius: 3px; line-height: 1;">Sedang</span>`;
                                    }
                                    let labelsHtml = '<div class="d-flex flex-wrap gap-1 mb-2 align-items-center">';
                                    labelsHtml += priorityHtml;
                                    if (task.labels && task.labels.length > 0) {
                                        task.labels.forEach(function(color) {
                                            labelsHtml += `<span class="badge bg-${color}" style="font-size: 8px; padding: 2px 5px; border-radius: 3px; line-height: 1;">${getLabelName(color)}</span>`;
                                        });
                                    }
                                    labelsHtml += '</div>';

                                    let poNum = '';
                                    let companyName = '';

                                    if (task.no_po) {
                                        poNum = task.no_po.startsWith('[') ? task.no_po : `[${task.no_po}]`;
                                        companyName = task.company || '';
                                        if (!companyName) {
                                            const emDashMatch = task.title.match(/^(.*?)\s*—\s*(.*)$/);
                                            const dashMatch = task.title.match(/^(.*?)\s+-\s+(.*)$/);
                                            if (emDashMatch) companyName = emDashMatch[2];
                                            else if (dashMatch) companyName = dashMatch[2];
                                        }
                                    } else {
                                        const bracketMatch = task.title.match(/^\[(.*?)\]\s*-\s*(.*)$/);
                                        const emDashMatch = task.title.match(/^(.*?)\s*—\s*(.*)$/);
                                        const dashMatch = task.title.match(/^(.*?)\s+-\s+(.*)$/);

                                        if (bracketMatch) {
                                            poNum = `[${bracketMatch[1]}]`;
                                            companyName = bracketMatch[2];
                                        } else if (emDashMatch) {
                                            poNum = '-';
                                            companyName = emDashMatch[2];
                                        } else if (dashMatch) {
                                            poNum = dashMatch[1];
                                            companyName = dashMatch[2];
                                        } else {
                                            poNum = task.title;
                                            companyName = task.company || '';
                                        }
                                    }

                                    const poTextColor = (task.entity_type === 'KII') ? 'text-danger' : 'text-primary';
                                    let displayTitleHtml = `
                                        <div class="${poTextColor} fw-bold" style="font-size: 13px; line-height: 1.35; word-break: break-word;" title="${poNum}">${poNum}</div>
                                        ${companyName ? `<div class="text-heading fw-semibold mt-1" style="font-size: 12.5px; line-height: 1.35; word-break: break-word;">${companyName}</div>` : ''}
                                    `;

                                    let nettHtml = '';
                                    if (task.nett && task.nett > 0 && boardType === 'monitoring' && userRole !== 'ServiceM') {
                                        const formattedVal = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(task.nett);
                                        const nettBadgeClass = (task.entity_type === 'KII') ? 'bg-label-danger' : 'bg-label-primary';
                                        nettHtml = `<div class="mt-1"><span class="badge ${nettBadgeClass}" style="font-size: 10px; font-weight: 600; padding: 3px 6px;">${formattedVal}</span></div>`;
                                    }

                                    return {
                                        id: task.id,
                                        title: `
                                            <div class="kanban-item-content" data-task-id="${task.id}" data-task='${JSON.stringify(task).replace(/'/g, "&#39;")}'>
                                                ${labelsHtml}
                                                <div class="mb-1" style="line-height: 1.4;">
                                                    ${displayTitleHtml}
                                                    ${task.description ? `<div class="text-muted mt-1" style="font-size: 11.5px; line-height: 1.35; word-break: break-word;">${task.description}</div>` : ''}
                                                </div>
                                                ${nettHtml}
                                                ${progressBarHtml}
                                                ${footerHtml}
                                            </div>
                                        `
                                    };
                                })
                            };
                        });

                        kanbanBoardInstance = new jKanban({
                            element: '.kanban-wrapper',
                            gutter: '15px',
                            widthBoard: '260px',
                            dragBoards: false,
                            boards: boards,
                            addItemButton: false,
                            click: function(el) {
                                // Find task data inside DOM
                                const contentEl = el.querySelector('.kanban-item-content');
                                if (contentEl) {
                                    const task = JSON.parse(contentEl.getAttribute('data-task'));
                                    openTaskSidebar(task);
                                }
                            },
                            dropEl: function(el, target, source, sibling) {
                                const taskId = el.querySelector('.kanban-item-content').getAttribute('data-task-id');
                                const targetColId = target.parentElement.getAttribute('data-id');
                                
                                // Find vertical index of the element
                                const items = Array.from(target.children);
                                const newPos = items.indexOf(el);

                                syncTaskMovement(taskId, targetColId, newPos);
                            }
                        });
                        
                        // Apply custom border colors to cards based on active labels or KII entity
                        $('.kanban-item-content').each(function() {
                            const taskData = $(this).data('task');
                            if (boardType === 'monitoring' && taskData && taskData.entity_type === 'KII') {
                                $(this).closest('.kanban-item').addClass('border-left-danger');
                            } else if (taskData && taskData.labels && taskData.labels.length > 0) {
                                const firstColor = taskData.labels[0];
                                $(this).closest('.kanban-item').addClass('border-left-' + firstColor);
                            }
                        });

                        updateBoardScrollArrows();

                        // Re-apply active filters (search text + sales) after re-render
                        applyKanbanFilters();

                        $('[data-toggle="tooltip"]').tooltip();

                        // Auto-open task modal if URL has ?task_id=X or ?open_task=X
                        const urlParams = new URLSearchParams(window.location.search);
                        const autoTaskId = urlParams.get('task_id') || urlParams.get('open_task');
                        if (autoTaskId && !window.autoTaskOpened) {
                            window.autoTaskOpened = true;
                            setTimeout(function() {
                                const contentEl = document.querySelector(`.kanban-item-content[data-task-id="${autoTaskId}"]`);
                                if (contentEl) {
                                    try {
                                        const task = JSON.parse(contentEl.getAttribute('data-task'));
                                        openTaskSidebar(task);
                                    } catch (err) {
                                        openTaskSidebar({ id: autoTaskId });
                                    }
                                } else {
                                    openTaskSidebar({ id: autoTaskId });
                                }
                            }, 350);
                        }
                    }
                });
            }

            // Sync card dragging to DB
            function syncTaskMovement(taskId, targetColId, newPos) {
                $.ajax({
                    url: '/kanban/tasks/move',
                    method: 'POST',
                    data: {
                        task_id: taskId,
                        target_column_id: targetColId,
                        new_position: newPos,
                        _token: csrfToken
                    },
                    error: function() {
                        loadKanbanBoard();
                        alert('Gagal memindahkan tugas.');
                    }
                });
            }

            // Sync column reordering to DB
            function syncColumnMovement(order) {
                $.ajax({
                    url: '{{ route("kanban.columns.reorder") }}',
                    method: 'POST',
                    data: {
                        board_id: {{ $board->id }},
                        order: order,
                        _token: csrfToken
                    },
                    success: function(res) {
                        // Successfully reordered columns
                    },
                    error: function(xhr) {
                        loadKanbanBoard();
                        alert('Gagal memindahkan kolom.');
                    }
                });
            }

            // Open Create Task Modal
            function openCreateTaskModal(boardId) {
                $('#createTaskColumnId').val(boardId);
                $('#createTaskForm')[0].reset();
                $('#createTaskAssignee').val(null).trigger('change');
                
                @if ($board->type === 'monitoring')
                    $('#createTaskTitleSelect').val('').trigger('change');
                    $('#createTaskPendingPoId').val('');
                    
                    $.ajax({
                        url: '{{ route("kanban.monitoring-document.available-pos") }}',
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                let html = '<option value="">-- Pilih PO + Perusahaan --</option>';
                                response.pos.forEach(function(po) {
                                    const entityTag = po.entity_type ? `[${po.entity_type}] ` : '';
                                    html += `<option value="${po.id}" data-no-po="${po.no_po}" data-company="${po.company}" data-sales="${po.sales}">[${po.no_po}] ${entityTag}- ${po.company} (Sales: ${po.sales})</option>`;
                                });
                                $('#createTaskTitleSelect').html(html).trigger('change');
                            }
                        }
                    });
                @endif
                
                $('#createTaskModal').modal('show');
            }

            @if ($board->type === 'monitoring')
                $(document).ready(function() {
                    $('#createTaskTitleSelect').on('change', function() {
                        const selectedOption = $(this).find('option:selected');
                        if (selectedOption.val()) {
                            const title = `[${selectedOption.data('no-po')}] - ${selectedOption.data('company')}`;
                            const sales = selectedOption.data('sales');
                            $('#createTaskTitle').val(title);
                            $('#createTaskPendingPoId').val(selectedOption.val());
                            $('#createTaskDescription').val(`Sales: ${sales}`);
                        } else {
                            $('#createTaskTitle').val('');
                            $('#createTaskPendingPoId').val('');
                            $('#createTaskDescription').val('');
                        }
                    });
                });
            @endif

            // Handle Create Task Submission
            $('#createTaskForm').submit(function(e) {
                e.preventDefault();
                const colId = $('#createTaskColumnId').val();
                const title = $('#createTaskTitle').val();
                const desc = $('#createTaskDescription').val();
                const assignees = $('#createTaskAssignee').val() || [];
                const dueDate = $('#createTaskDueDate').val();
                const priority = $('#createTaskPriority').val();
                const pendingPoId = $('#createTaskPendingPoId').length ? $('#createTaskPendingPoId').val() : null;

                $.ajax({
                    url: '/kanban/tasks',
                    method: 'POST',
                    data: {
                        board_id: boardId,
                        column_id: colId,
                        title: title,
                        description: desc,
                        assignees: assignees,
                        due_date: dueDate,
                        priority: priority,
                        pending_po_id: pendingPoId,
                        _token: csrfToken
                    },
                    success: function(response) {
                        $('#createTaskModal').modal('hide');
                        loadKanbanBoard();
                    },
                    error: function() {
                        alert('Gagal menambah tugas baru.');
                    }
                });
            });

            // Variable to keep track of currently active task data in modal
            let currentTaskData = null;
            let currentBastPrefill = null;
            const currentUserId = {{ auth()->id() }};
            let isProgrammaticChange = false;
            let lastBoardDataHash = '';
            let taskDetailsInterval = null;

            // Open Task Details Modal and load all details
            function openTaskSidebar(task) {
                const taskId = task.id;
                
                // Clear any existing polling interval
                if (taskDetailsInterval) {
                    clearInterval(taskDetailsInterval);
                }

                isProgrammaticChange = true;
                // Clear modal fields first
                $('#editTaskId').val(taskId);
                $('#taskDetailsModalLabel').text('Loading...');
                $('#currentStatusText').text('Status');
                $('#editTaskAssignee').val([]).trigger('change', { programmatic: true });
                const fpInit = document.querySelector("#editTaskDueDate")._flatpickr;
                if (fpInit) {
                    fpInit.clear(false);
                } else {
                    $('#editTaskDueDate').val('');
                }
                $('#taskAttachmentInput').val('');
                isProgrammaticChange = false;

                if (boardType !== 'monitoring') {
                    $('#taskExpensesList').html('');
                    $('#taskExpenseTotal').text('Rp 0');
                    if (document.getElementById('taskExpenseForm')) $('#taskExpenseForm')[0].reset();
                    $('#taskExpenseForm').hide();
                    $('#btnShowExpenseForm').show();
                    $('#linkQuotationSelect').val(null).trigger('change');
                    $('#quotationLinkedView').css('display', 'none');
                    $('#quotationLinkForm').hide();
                }

                $('#descriptionStaticView').html('<div class="text-center py-2"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>');
                $('#labelsListContainer').html('');
                $('#attachmentsListContainer').html('');
                $('#taskChecklistsContainer').html('');
                $('#commentTextInput').val('');
                $('#timelineFeedContainer').html('<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading feed...</div>');

                // Hide edit form and show static description
                $('#descriptionEditForm').hide();
                $('#descriptionStaticView').show();

                // Show modal first to give instant response
                const modal = new bootstrap.Modal(document.getElementById('taskDetailsModal'));
                modal.show();

                // Fetch details via AJAX
                loadTaskDetails(taskId);

                // Start polling every 5 seconds
                taskDetailsInterval = setInterval(function() {
                    loadTaskDetails(taskId);
                }, 5000);
            }

            // Stop polling when modal is closed
            $(document).on('hide.bs.modal', '#taskDetailsModal', function () {
                if (taskDetailsInterval) {
                    clearInterval(taskDetailsInterval);
                    taskDetailsInterval = null;
                }
            });

            function loadTaskDetails(taskId) {
                $.ajax({
                    url: `/kanban/tasks/${taskId}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            currentTaskData = response.task;

                            if (response.so_details) {
                                $('#soPoNumber').text(response.so_details.no_po || '-');
                                $('#soSoNumber').text(response.so_details.no_so || '-');
                                $('#soClientName').text(response.so_details.company || '-');

                                if (response.so_details.entity_type === 'KII') {
                                    $('#soEntityType').removeClass('bg-primary text-white').addClass('bg-danger text-white').text('KII (Kojisha)');
                                } else {
                                    $('#soEntityType').removeClass('bg-danger text-white').addClass('bg-primary text-white').text('RJO (Reftech)');
                                }
                                
                                // Render Quotation as a Link or Plain text for ServiceM
                                if (response.so_details.quote_no !== 'N/A') {
                                    if (userRole === 'ServiceM' || !response.so_details.quote_link) {
                                        $('#soQuoteNumber').text(response.so_details.quote_no);
                                    } else {
                                        $('#soQuoteNumber').html(`<a href="${response.so_details.quote_link}" target="_blank" class="fw-semibold text-primary"><i class="mdi mdi-open-in-new me-1" style="font-size:12px;"></i>${response.so_details.quote_no}</a>`);
                                    }
                                } else {
                                    $('#soQuoteNumber').text('N/A');
                                }
                                
                                if (userRole !== 'ServiceM' && response.so_details.quote_nett) {
                                    const quoteNettClass = (response.so_details.entity_type === 'KII') ? 'text-danger' : 'text-primary';
                                    $('#soQuoteNett').removeClass('text-primary text-danger').addClass(quoteNettClass).text('Rp ' + response.so_details.quote_nett);
                                }
                                $('#soSalesPerson').text(response.so_details.sales_name);

                                // Render Invoices & Payments
                                let invoicesHtml = '';
                                if (response.so_details.invoices && response.so_details.invoices.length > 0) {
                                    response.so_details.invoices.forEach(function(inv) {
                                        const statusBadge = inv.status === 'Paid' ? 'bg-label-success' : (inv.status === 'Pending Confirmation' ? 'bg-label-warning' : 'bg-label-danger');
                                        invoicesHtml += `
                                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-white border mb-1" style="font-size:12px;">
                                                <a href="${inv.link}" target="_blank" class="fw-semibold text-heading"><i class="mdi mdi-link-variant me-1" style="font-size:12px;"></i>${inv.no_invoice} (${inv.term_name})</a>
                                                <span class="badge ${statusBadge}" style="font-size:9.5px; padding: 2px 6px;">${inv.status}</span>
                                            </div>
                                        `;
                                    });
                                } else {
                                    invoicesHtml = '<span class="text-muted" style="font-size:12px;">Belum ada invoice yang diterbitkan.</span>';
                                }
                                $('#soInvoicesContainer').html(invoicesHtml);

                                // Render Delivery / Surat Jalan
                                let deliveriesHtml = '';
                                if (response.so_details.deliveries && response.so_details.deliveries.length > 0) {
                                    response.so_details.deliveries.forEach(function(del) {
                                        deliveriesHtml += `
                                            <a href="${del.link}" target="_blank" class="badge bg-label-info p-2 me-1 mb-1" style="font-size:11px;">
                                                <i class="mdi mdi-truck-delivery-outline me-1"></i>Surat Jalan #${del.id} (${del.destination})
                                            </a>
                                        `;
                                    });
                                } else {
                                    deliveriesHtml = '<span class="text-muted" style="font-size:12px;">Belum ada surat jalan yang dibuat.</span>';
                                }
                                $('#soDeliveriesContainer').html(deliveriesHtml);

                                // Render Connected Service Report flow
                                if (response.so_details.active_report) {
                                    $('#connectedReportLink')
                                        .text(response.so_details.active_report.jobdesc)
                                        .attr('href', response.so_details.active_report.link);
                                    $('#connectedReportDate').text('Dibuat: ' + response.so_details.active_report.date);
                                    
                                    $('#connectedReportContainer').show();
                                    $('#reportSelectorContainer').hide();
                                } else {
                                    $('#connectedReportContainer').hide();
                                    $('#reportSelectorContainer').show();
                                }

                                // Populate available reports dropdown options
                                isProgrammaticChange = true;
                                let reportsHtml = '<option value="">-- Hubungkan Service Report --</option>';
                                if (response.so_details.available_reports && response.so_details.available_reports.length > 0) {
                                    response.so_details.available_reports.forEach(function(rep) {
                                        const isSelected = (rep.id == response.so_details.service_report_id) ? 'selected' : '';
                                        reportsHtml += `<option value="${rep.id}" ${isSelected}>${rep.jobdesc}</option>`;
                                    });
                                }
                                $('#editTaskServiceReport').html(reportsHtml).trigger('change', { programmatic: true });
                                isProgrammaticChange = false;

                                // Render BAST section
                                currentBastPrefill = response.so_details.bast_prefill || null;
                                if (response.so_details.bast) {
                                    $('#bastExistingLabel').text('Lihat BAST ' + response.so_details.bast.no_bast);
                                    $('#bastExistingLink').attr('href', response.so_details.bast.show_link || response.so_details.bast.print_link);
                                    $('#bastExistingContainer').show();
                                    $('#bastCreateContainer').hide();
                                } else {
                                    $('#bastExistingContainer').hide();
                                    $('#bastCreateContainer').show();
                                }

                                // Tombol Detail Project — ke Project Monitoring kalau kartunya udah
                                // punya PendingPO, fallback ke halaman quotation kalau belum.
                                $('#soDetailProjectLink').attr('href', response.so_details.project_monitoring_link || response.so_details.quote_link);
                                $('#soDetailProjectContainer').show();

                                $('#soDetailsContainer').show();

                                // Ringkasan Kesehatan Keuangan: Sembunyikan di monitoring document, tapi tampilkan di kanban lain (seperti board 2) jika datanya ada
                                if (boardType !== 'monitoring' && response.so_details.financial_health) {
                                    var fh = response.so_details.financial_health;
                                    var fmtRp = function (n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); };
                                    $('#fhRevenue').text(fmtRp(fh.revenue));
                                    $('#fhTotalCost').text(fmtRp(fh.total_cost));
                                    $('#fhProfit')
                                        .text(fmtRp(fh.profit))
                                        .removeClass('text-primary text-danger')
                                        .addClass(fh.profit >= 0 ? 'text-primary' : 'text-danger');

                                    var costRatio = fh.revenue > 0 ? (fh.total_cost / fh.revenue) * 100 : 0;
                                    var profitRatio = fh.revenue > 0 ? (fh.profit / fh.revenue) * 100 : 0;
                                    $('#fhCostBar').css('width', costRatio + '%');
                                    $('#fhProfitBar').css('width', profitRatio + '%');
                                    $('#fhMarginBadge').text(fh.margin + '% Margin');
                                    $('#fhDetailLink').attr('href', response.so_details.project_monitoring_link || response.so_details.quote_link);

                                    $('#financialHealthContainer').show();
                                } else {
                                    $('#financialHealthContainer').hide();
                                }
                            } else {
                                $('#soDetailsContainer').hide();
                                $('#financialHealthContainer').hide();
                            }
                            
                            isProgrammaticChange = true;
                            // Set fields if changed
                            const titleRaw = currentTaskData.title || '';
                            if (boardType === 'monitoring') {
                                const titleMatch = titleRaw.match(/^\[(.*?)\]\s*-\s*(.*)$/);
                                if (titleMatch) {
                                    const modalBadgeClass = (response.so_details && response.so_details.entity_type === 'KII') ? 'bg-label-danger' : 'bg-label-primary';
                                    $('#taskDetailsModalLabel').html(`<span class="badge ${modalBadgeClass} px-3 py-2 me-2" style="font-size: 13.5px; font-weight: 600;"><i class="mdi mdi-receipt-text-outline me-1"></i>${titleMatch[1]}</span> <span class="text-dark fw-bold" style="font-size: 16px;">${titleMatch[2]}</span>`);
                                } else {
                                    $('#taskDetailsModalLabel').text(titleRaw);
                                }
                            } else {
                                $('#taskDetailsModalLabel').text(titleRaw);
                            }
                            if ($('#currentStatusText').text() !== currentTaskData.column_title) {
                                $('#currentStatusText').text(currentTaskData.column_title);
                            }
                            const currentSel = $('#editTaskAssignee').val() || [];
                            const serverSel = currentTaskData.assignees || [];
                            const currentSelStr = currentSel.map(String).sort().join(',');
                            const serverSelStr = serverSel.map(String).sort().join(',');
                            if (currentSelStr !== serverSelStr) {
                                $('#editTaskAssignee').val(serverSel).trigger('change', { programmatic: true });
                            }
                            if ($('#editTaskDueDate').val() != (currentTaskData.due_date || '')) {
                                const fpSet = document.querySelector("#editTaskDueDate")._flatpickr;
                                if (fpSet) {
                                    fpSet.setDate(currentTaskData.due_date || '', false);
                                } else {
                                    $('#editTaskDueDate').val(currentTaskData.due_date || '');
                                }
                            }
                            if ($('#editTaskPriority').val() !== currentTaskData.priority) {
                                $('#editTaskPriority').val(currentTaskData.priority || 'medium');
                            }
                            isProgrammaticChange = false;
                            
                            // Set Description (only if user is not actively editing it)
                            if (!$('#descriptionEditForm').is(':visible')) {
                                if (currentTaskData.description) {
                                    $('#descriptionStaticView').text(currentTaskData.description);
                                } else {
                                    $('#descriptionStaticView').html('<span class="text-muted italic">Tambahkan deskripsi detail tugas...</span>');
                                }
                                $('#editTaskDescription').val(currentTaskData.description ?? '');
                            }

                            // Render Labels
                            renderLabels(currentTaskData.labels);

                            // Render Attachments
                            renderAttachments(response.attachments);

                            // Render Checklists (only if user is not currently typing in a checklist input)
                            const isTypingInChecklist = $('#taskChecklistsContainer').find('input:focus').length > 0;
                            if (!isTypingInChecklist) {
                                renderChecklists(response.checklists);
                            }

                            // Render Unified Chronological Feed
                            renderTimelineFeed(response.feed);

                            // Render biaya kartu & status hubungan quotation
                            if (boardType !== 'monitoring') {
                                renderTaskExpenses(response);
                                renderQuotationLink(response);
                            }

                            // Render Laporan Harian Proyek (Daily Project Reports)
                            renderProjectReports(response);
                        }
                    },
                    error: function() {
                        // Silent fail on background polling
                    }
                });
            }

            const rpFmt = function (n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); };

            function renderProjectReports(response) {
                const colTitle = (response.task && response.task.column_title ? response.task.column_title : '').toLowerCase();
                const isAllowedColumn = colTitle.includes('progress') || colTitle.includes('proses') || colTitle.includes('done') || colTitle.includes('selesai');

                if (!isAllowedColumn) {
                    $('#taskProjectReportsContainer').hide();
                    return;
                }
                $('#taskProjectReportsContainer').show();

                const reports = response.project_reports || [];
                const createUrl = response.create_project_report_url || '#';

                $('#btnCreateDailyReport').attr('href', createUrl);
                $('#taskProjectReportsCount').text(`${reports.length} Laporan`);

                let html = '';
                if (reports.length === 0) {
                    html = '<p class="text-muted small mb-0"><i class="mdi mdi-information-outline me-1"></i>Belum ada Laporan Harian untuk project ini.</p>';
                } else {
                    reports.forEach(function (r) {
                        const dayBadge = r.day_number ? `<span class="badge bg-primary me-1">Hari ke-${r.day_number}</span>` : '';
                        const dateStr = r.report_date ? `${r.day_name ? r.day_name + ', ' : ''}${r.report_date}` : '-';

                        html += `
                            <div class="p-2.5 rounded bg-white border d-flex flex-column gap-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        ${dayBadge}
                                        <span class="fw-semibold text-dark" style="font-size: 12px;">${dateStr}</span>
                                    </div>
                                    <div class="d-inline-block text-nowrap">
                                        <a href="${r.show_url}" target="_blank" class="btn btn-xs btn-label-info me-1" title="Lihat Detail"><i class="mdi mdi-eye-outline me-1"></i>Detail</a>
                                        <a href="${r.print_url}" target="_blank" class="btn btn-xs btn-label-secondary" title="Cetak PDF"><i class="mdi mdi-printer me-1"></i>Print</a>
                                    </div>
                                </div>
                                ${r.achievement_today ? `<div class="text-muted text-truncate" style="font-size: 11px;"><i class="mdi mdi-check-all text-success me-1"></i>${r.achievement_today}</div>` : ''}
                                <div class="text-muted d-flex align-items-center justify-content-between" style="font-size: 10.5px;">
                                    <span><i class="mdi mdi-account-outline me-1"></i>${r.creator_name}</span>
                                    <span>${r.days_remaining ? 'Sisa: ' + r.days_remaining : ''}</span>
                                </div>
                            </div>
                        `;
                    });
                }
                $('#taskProjectReportsList').html(html);
            }

            function renderTaskExpenses(response) {
                const expenses = response.expenses || [];
                const canManage = !!response.can_manage_expense;
                $('#taskExpenseTotal').text(rpFmt(response.expense_total));

                const colTitle = (response.task && response.task.column_title ? response.task.column_title : '').toLowerCase();
                const isProgressOrDone = colTitle.includes('progress') || colTitle.includes('proses') || colTitle.includes('done') || colTitle.includes('selesai');

                let html = '';
                if (expenses.length === 0) {
                    html = '<p class="text-muted small mb-0">Belum ada pengeluaran dicatat.</p>';
                } else {
                    expenses.forEach(function (e) {
                        const receipt = e.receipt
                            ? `<a href="${e.receipt}" target="_blank" class="text-muted ms-1" title="Lihat struk"><i class="mdi mdi-paperclip"></i></a>`
                            : '';
                        const del = e.can_delete
                            ? `<button type="button" class="btn btn-xs btn-text-danger btn-icon btn-delete-task-expense" data-id="${e.id}" style="width:20px;height:20px;padding:0;"><i class="mdi mdi-close"></i></button>`
                            : '';
                        html += `
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-white border">
                                <div class="min-width-0">
                                    <span class="fw-semibold text-heading">${e.name}</span>${receipt}
                                    <div class="text-muted" style="font-size:10.5px;">
                                        <span class="badge bg-label-secondary" style="font-size:9px;">${e.category}</span>
                                        ${e.date || ''} &bull; ${e.user_name}
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                    <span class="fw-bold text-dark">${rpFmt(e.amount)}</span>
                                    ${del}
                                </div>
                            </div>`;
                    });
                }
                $('#taskExpensesList').html(html);

                if (canManage) {
                    $('#taskExpenseFormWrap').show();
                    if (isProgressOrDone) {
                        $('#btnShowExpenseForm').prop('disabled', true).addClass('disabled').attr('title', 'Penambahan biaya dinonaktifkan pada tahap In Progress & Done');
                        $('#taskExpenseForm').hide();
                    } else {
                        $('#btnShowExpenseForm').prop('disabled', false).removeClass('disabled').removeAttr('title');
                    }
                } else {
                    $('#taskExpenseFormWrap').hide();
                }
            }

            function renderQuotationLink(response) {
                const lq = response.linked_quotation;
                const canLink = !!response.can_link_quotation;

                if (lq) {
                    $('#quotationLinkedNo').text(lq.no_quote || ('Quotation #' + lq.id)).attr('href', lq.link);
                    $('#quotationLinkedCompany').text(lq.company || '');
                    $('#quotationLinkedView').css('display', 'flex');
                    $('#quotationLinkForm').hide();
                    // Sudah jadi PO -> tidak boleh diputus
                    $('#btnUnlinkQuotation').toggle(canLink && !lq.is_po);
                } else {
                    $('#quotationLinkedView').css('display', 'none');
                    $('#quotationLinkForm').toggle(canLink);
                }
            }

            // --- Pengeluaran Project (biaya per kartu) ---
            // Format tampilan nominal jadi "5.000.000" sambil mengetik; angka mentah dikirim saat submit.
            $('#expenseAmount').on('input', function () {
                const digits = $(this).val().replace(/\D/g, '');
                $(this).val(digits ? Number(digits).toLocaleString('id-ID') : '');
            });

            $('#btnShowExpenseForm').click(function () {
                $(this).hide();
                $('#taskExpenseForm').show();
                $('#expenseName').focus();
            });
            $('#btnCancelExpenseForm').click(function () {
                $('#taskExpenseForm')[0].reset();
                $('#taskExpenseForm').hide();
                $('#btnShowExpenseForm').show();
            });
            $('#taskExpenseForm').submit(function (e) {
                e.preventDefault();
                const taskId = $('#editTaskId').val();
                if (!taskId) return;

                const fd = new FormData();
                fd.append('name', $('#expenseName').val());
                fd.append('category', $('#expenseCategory').val());
                fd.append('amount', $('#expenseAmount').val().replace(/\D/g, ''));
                fd.append('date', $('#expenseDate').val());
                if ($('#expenseReceipt')[0].files[0]) {
                    fd.append('receipt', $('#expenseReceipt')[0].files[0]);
                }
                fd.append('_token', csrfToken);

                const $btn = $(this).find('button[type="submit"]').prop('disabled', true);
                $.ajax({
                    url: `/kanban/tasks/${taskId}/expenses`,
                    method: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function () {
                        $('#taskExpenseForm')[0].reset();
                        $('#taskExpenseForm').hide();
                        $('#btnShowExpenseForm').show();
                        loadTaskDetails(taskId);
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON && (xhr.responseJSON.error || xhr.responseJSON.message);
                        alert(msg || 'Gagal menyimpan biaya.');
                    },
                    complete: function () { $btn.prop('disabled', false); }
                });
            });
            $(document).on('click', '.btn-delete-task-expense', function () {
                const expenseId = $(this).data('id');
                const taskId = $('#editTaskId').val();
                if (!confirm('Hapus pengeluaran ini?')) return;
                $.ajax({
                    url: `/kanban/task-expenses/${expenseId}`,
                    method: 'DELETE',
                    data: { _token: csrfToken },
                    success: function () { loadTaskDetails(taskId); },
                    error: function (xhr) {
                        const msg = xhr.responseJSON && (xhr.responseJSON.error || xhr.responseJSON.message);
                        alert(msg || 'Gagal menghapus biaya.');
                    }
                });
            });

            // --- Hubungkan / putuskan kartu ke Unit Quotation ---
            $('#btnLinkQuotation').click(function () {
                const taskId = $('#editTaskId').val();
                const quoteId = $('#linkQuotationSelect').val();
                if (!taskId || !quoteId) { alert('Pilih quotation dulu.'); return; }
                $.ajax({
                    url: `/kanban/tasks/${taskId}/link-quotation`,
                    method: 'POST',
                    data: { id_unit_quotation: quoteId, _token: csrfToken },
                    success: function () {
                        $('#linkQuotationSelect').val(null).trigger('change');
                        loadTaskDetails(taskId);
                        loadKanbanBoard();
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON && (xhr.responseJSON.error || xhr.responseJSON.message);
                        alert(msg || 'Gagal menghubungkan quotation.');
                    }
                });
            });
            $('#btnUnlinkQuotation').click(function () {
                const taskId = $('#editTaskId').val();
                if (!confirm('Putuskan hubungan kartu ini dari quotation?')) return;
                $.ajax({
                    url: `/kanban/tasks/${taskId}/unlink-quotation`,
                    method: 'POST',
                    data: { _token: csrfToken },
                    success: function () {
                        loadTaskDetails(taskId);
                        loadKanbanBoard();
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON && (xhr.responseJSON.error || xhr.responseJSON.message);
                        alert(msg || 'Gagal memutuskan hubungan.');
                    }
                });
            });

            // Labels Rendering
            function renderLabels(labels) {
                let html = '';
                
                if (labels && labels.length > 0) {
                    labels.forEach(function(color) {
                        html += `
                            <span class="badge bg-${color} me-1 py-1 d-inline-flex align-items-center" style="font-size: 11px; gap: 4px; padding-right: 6px;">
                                ${getLabelName(color)}
                                <a href="javascript:void(0);" class="text-white btn-remove-label-from-task" data-color="${color}" style="opacity: 0.8; font-size: 10px; line-height: 1;"><i class="mdi mdi-close"></i></a>
                            </span>
                        `;
                    });
                } else {
                    html += '<span class="text-muted small">Belum ada label</span>';
                }
                $('#labelsListContainer').html(html);
            }

            // Attachments Rendering
            function renderAttachments(attachments) {
                let html = '';
                if (attachments && attachments.length > 0) {
                    attachments.forEach(function(att) {
                        const isImage = ['png', 'jpg', 'jpeg', 'gif', 'webp'].includes(att.file_type.toLowerCase());
                        
                        let previewHtml = '';
                        if (isImage) {
                            previewHtml = `<img src="${att.file_path}" class="rounded me-3 border" style="width: 80px; height: 60px; object-fit: cover;">`;
                        } else {
                            previewHtml = `
                                <div class="rounded me-3 bg-label-primary border d-flex align-items-center justify-content-center" style="width: 80px; height: 60px;">
                                    <i class="mdi mdi-file-document-outline" style="font-size: 24px;"></i>
                                </div>
                            `;
                        }

                        html += `
                            <div class="d-flex align-items-center border p-2 rounded bg-white shadow-xs attachment-item" data-id="${att.id}">
                                ${previewHtml}
                                <div class="flex-grow-1 min-width-0">
                                    <h6 class="mb-0 small fw-bold text-truncate" style="font-size: 13px;"><a href="${att.file_path}" target="_blank" class="text-body">${att.file_name}</a></h6>
                                    <small class="text-muted" style="font-size: 11px;">${att.file_size} • Pengunggah: ${att.uploaded_by} • ${att.uploaded_at}</small>
                                </div>
                                <button class="btn btn-xs btn-text-danger btn-icon btn-delete-attachment" type="button" data-id="${att.id}"><i class="mdi mdi-close"></i></button>
                            </div>
                        `;
                    });
                } else {
                    html = '<p class="text-muted small mb-0">Belum ada lampiran file.</p>';
                }
                $('#attachmentsListContainer').html(html);
            }

            // Checklists Rendering
            function renderChecklists(checklists) {
                let html = '';
                if (checklists && checklists.length > 0) {
                    checklists.forEach(function(c) {
                        let itemsHtml = '';
                        if (c.items && c.items.length > 0) {
                            c.items.forEach(function(item) {
                                const checkedAttr = item.is_completed ? 'checked' : '';
                                const textStyle = item.is_completed ? 'text-decoration: line-through; color: #a1acb8;' : '';
                                itemsHtml += `
                                    <div class="d-flex align-items-center mb-2 checklist-item-row" data-item-id="${item.id}">
                                        <input type="checkbox" class="form-check-input me-2 btn-toggle-checklist-item" ${checkedAttr}>
                                        <span class="flex-grow-1 text-muted" style="font-size: 13px; ${textStyle}">${item.title}</span>
                                        <button class="btn btn-xs btn-text-danger btn-icon btn-delete-checklist-item" type="button" style="padding: 0; width: 20px; height: 20px;"><i class="mdi mdi-close"></i></button>
                                    </div>
                                `;
                            });
                        }

                        html += `
                            <div class="mb-4 border p-3 rounded checklist-block" data-checklist-id="${c.id}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-heading text-uppercase" style="font-size:12.5px;"><i class="mdi mdi-checkbox-marked-outline me-2 text-primary"></i>${c.title}</h6>
                                    <button class="btn btn-xs btn-outline-danger btn-delete-checklist" type="button">Hapus</button>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <span class="small me-2 text-muted progress-percent fw-semibold" style="min-width: 30px;">${c.percent}%</span>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar" role="progressbar" style="width: ${c.percent}%;" aria-valuenow="${c.percent}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="checklist-items-list mb-3">
                                    ${itemsHtml}
                                </div>
                                <div class="add-item-block">
                                    <button class="btn btn-xs btn-outline-primary btn-show-add-item" type="button"><i class="mdi mdi-plus me-1"></i>Tambah Item</button>
                                    <div class="add-item-form mt-2" style="display:none;">
                                        <div class="input-group input-group-merge">
                                            <input type="text" class="form-control form-control-sm checklist-item-input" placeholder="Nama item...">
                                            <button class="btn btn-primary btn-xs btn-save-checklist-item" type="button">Tambah</button>
                                            <button class="btn btn-outline-secondary btn-xs btn-cancel-checklist-item" type="button">Batal</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                $('#taskChecklistsContainer').html(html);
            }

            // Timeline Feed Rendering (Comments & Activities)
            function renderTimelineFeed(feed) {
                let html = '';
                if (!feed || feed.length === 0) {
                    html = '<p class="text-muted small text-center py-3">Belum ada aktivitas atau komentar.</p>';
                } else {
                    feed.forEach(function(item) {
                        if (item.type === 'comment') {
                            let editDeleteActions = '';
                            if (item.user_id === currentUserId) {
                                editDeleteActions = `
                                    <a href="javascript:void(0);" class="btn-edit-comment text-muted me-2" data-id="${item.id}" style="font-size: 11px;"><i class="mdi mdi-pencil-outline me-1"></i>Edit</a>
                                    <a href="javascript:void(0);" class="btn-delete-comment text-danger" data-id="${item.id}" style="font-size: 11px;"><i class="mdi mdi-trash-can-outline me-1"></i>Hapus</a>
                                `;
                            }

                            let avatar = '';
                            if (item.actor_avatar) {
                                avatar = `<img src="${item.actor_avatar}" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">`;
                            } else {
                                avatar = `<span class="avatar-initial rounded-circle bg-label-primary d-inline-block text-center fw-semibold" style="width: 28px; height: 28px; line-height: 28px; font-size: 11px;">${item.actor_initials}</span>`;
                            }

                            // Parse mentions
                            let commentText = item.text;
                            commentText = commentText.replace(/@([a-zA-Z0-9\s]+?)(?=\s@|\s\w+?:|\s*$|\.|\,)/g, function(match) {
                                return `<span class="badge bg-label-primary">${match}</span>`;
                            });

                            html += `
                                <div class="comment-block mb-3" id="comment-block-${item.id}">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="avatar avatar-xs me-2">
                                            ${avatar}
                                        </div>
                                        <span class="fw-semibold text-heading me-2" style="font-size: 12.5px;">${item.actor}</span>
                                        <small class="text-muted" style="font-size: 10px;">${item.created_at}</small>
                                    </div>
                                    <!-- Comment bubble -->
                                    <div class="p-2 border rounded bg-white shadow-xs" style="margin-left: 32px; font-size: 13px; color: #4f5157;">
                                        <div class="comment-display-text" id="comment-display-text-${item.id}" style="white-space: pre-wrap;">${commentText}</div>
                                        <div class="comment-edit-area d-none" id="comment-edit-area-${item.id}">
                                            <textarea class="form-control form-control-sm mb-1" id="comment-edit-input-${item.id}" rows="2">${item.text}</textarea>
                                            <div class="text-end">
                                                <button class="btn btn-xs btn-outline-secondary btn-cancel-edit-comment me-1" data-id="${item.id}">Batal</button>
                                                <button class="btn btn-xs btn-primary btn-save-edit-comment" data-id="${item.id}">Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="margin-left: 32px;" class="mt-1">
                                        ${editDeleteActions}
                                    </div>
                                </div>
                            `;
                        } else if (item.type === 'activity') {
                            let avatar = '';
                            if (item.actor_avatar) {
                                avatar = `<img src="${item.actor_avatar}" class="rounded-circle" style="width: 20px; height: 20px; object-fit: cover;">`;
                            } else {
                                avatar = `<span class="avatar-initial rounded-circle bg-label-secondary d-inline-block text-center fw-semibold" style="width: 20px; height: 20px; line-height: 20px; font-size: 9px;">${item.actor_initials}</span>`;
                            }

                            html += `
                                <div class="activity-block mb-3 d-flex align-items-center text-muted" style="margin-left: 8px; font-size: 12.5px;">
                                    <div class="avatar avatar-xs me-2" style="width: 20px; height: 20px;">
                                        ${avatar}
                                    </div>
                                    <div>
                                        <span class="fw-semibold text-heading me-1">${item.actor}</span>
                                        <span>${item.text}</span>
                                        <small class="text-muted ms-2" style="font-size: 10px;">${item.created_at}</small>
                                    </div>
                                </div>
                            `;
                        }
                    });
                }
                $('#timelineFeedContainer').html(html);
            }

            // Inline Title Edit
            $('#taskDetailsModalLabel').css('cursor', 'pointer').click(function() {
                const currentTitle = $(this).text();
                const newTitle = prompt('Ubah Judul Tugas:', currentTitle);
                if (newTitle && newTitle.trim() && newTitle.trim() !== currentTitle) {
                    const taskId = $('#editTaskId').val();
                    $.ajax({
                        url: `/kanban/tasks/${taskId}/update`,
                        method: 'POST',
                        data: {
                            title: newTitle.trim(),
                            description: $('#editTaskDescription').val(),
                            assignees: $('#editTaskAssignee').val(),
                            due_date: $('#editTaskDueDate').val(),
                            priority: $('#editTaskPriority').val(),
                            column_id: 'column_' + currentTaskData.column_id,
                            _token: csrfToken
                        },
                        success: function() {
                            loadTaskDetails(taskId);
                            loadKanbanBoard();
                        }
                    });
                }
            });

            // Inline Description Edit Toggles
            $('#btnEditDescription').click(function() {
                $('#descriptionStaticView').hide();
                $('#descriptionEditForm').show();
                $('#editTaskDescription').focus();
            });

            $('#btnCancelDescription').click(function() {
                $('#descriptionEditForm').hide();
                $('#descriptionStaticView').show();
            });

            $('#btnSaveDescription').click(function() {
                const taskId = $('#editTaskId').val();
                const desc = $('#editTaskDescription').val();
                
                $.ajax({
                    url: `/kanban/tasks/${taskId}/update`,
                    method: 'POST',
                    data: {
                        title: currentTaskData.title,
                        description: desc,
                        assignees: $('#editTaskAssignee').val(),
                        due_date: $('#editTaskDueDate').val(),
                        priority: $('#editTaskPriority').val(),
                        column_id: 'column_' + currentTaskData.column_id,
                        _token: csrfToken
                    },
                    success: function() {
                        loadTaskDetails(taskId);
                        loadKanbanBoard();
                    },
                    error: function() {
                        alert('Gagal memperbarui deskripsi.');
                    }
                });
            });

            // Metadata changes (Assignee, Due Date auto-sync)
            $(document).on('select2:select select2:unselect', '#editTaskAssignee', function(e) {
                const taskId = $('#editTaskId').val();
                if (!taskId || !currentTaskData) return;
                const newAssignees = $(this).val() || [];

                $.ajax({
                    url: `/kanban/tasks/${taskId}/update`,
                    method: 'POST',
                    data: {
                        title: currentTaskData.title,
                        description: $('#editTaskDescription').val(),
                        assignees: newAssignees,
                        due_date: $('#editTaskDueDate').val(),
                        priority: $('#editTaskPriority').val(),
                        column_id: 'column_' + currentTaskData.column_id,
                        _token: csrfToken
                    },
                    success: function() {
                        loadTaskDetails(taskId);
                        loadKanbanBoard();
                    }
                });
            });

            $('#editTaskDueDate').change(function() {
                if (isProgrammaticChange) return;
                const taskId = $('#editTaskId').val();
                if (!taskId || !currentTaskData) return;
                const newDate = $(this).val();
                if (newDate == currentTaskData.due_date) return;

                $.ajax({
                    url: `/kanban/tasks/${taskId}/update`,
                    method: 'POST',
                    data: {
                        title: currentTaskData.title,
                        description: $('#editTaskDescription').val(),
                        assignees: $('#editTaskAssignee').val(),
                        due_date: newDate,
                        priority: $('#editTaskPriority').val(),
                        column_id: 'column_' + currentTaskData.column_id,
                        _token: csrfToken
                    },
                    success: function() {
                        loadTaskDetails(taskId);
                        loadKanbanBoard();
                    }
                });
            });

            $('#editTaskPriority').change(function() {
                if (isProgrammaticChange) return;
                const taskId = $('#editTaskId').val();
                if (!taskId || !currentTaskData) return;
                const newPriority = $(this).val();
                if (newPriority == currentTaskData.priority) return;
                $.ajax({
                    url: `/kanban/tasks/${taskId}/update`,
                    method: 'POST',
                    data: {
                        title: currentTaskData.title,
                        description: $('#editTaskDescription').val(),
                        assignees: $('#editTaskAssignee').val(),
                        due_date: $('#editTaskDueDate').val(),
                        priority: newPriority,
                        column_id: 'column_' + currentTaskData.column_id,
                        _token: csrfToken
                    },
                    success: function() {
                        loadTaskDetails(taskId);
                        loadKanbanBoard();
                    }
                });
            });

            // Top Status Change Dropdown Selector
            $(document).on('click', '.btn-change-status-top', function() {
                const taskId = $('#editTaskId').val();
                const colId = $(this).data('column-id');
                
                $.ajax({
                    url: `/kanban/tasks/${taskId}/update`,
                    method: 'POST',
                    data: {
                        title: currentTaskData.title,
                        description: $('#editTaskDescription').val(),
                        assignees: $('#editTaskAssignee').val(),
                        due_date: $('#editTaskDueDate').val(),
                        priority: $('#editTaskPriority').val(),
                        column_id: colId,
                        _token: csrfToken
                    },
                    success: function() {
                        loadTaskDetails(taskId);
                        loadKanbanBoard();
                    },
                    error: function() {
                        alert('Gagal memindahkan tugas.');
                    }
                });
            });

            // Quick Actions Handlers
            $('#btnQuickMember').click(function() {
                $('#editTaskAssignee').select2('open');
            });

            $('#btnQuickDate').click(function() {
                $('#editTaskDueDate').focus();
            });

            $('#btnQuickChecklist').click(function() {
                const title = prompt('Masukkan nama checklist:', 'Checklist');
                if (title && title.trim()) {
                    const taskId = $('#editTaskId').val();
                    $.ajax({
                        url: `/kanban/tasks/${taskId}/checklists`,
                        method: 'POST',
                        data: {
                            title: title.trim(),
                            _token: csrfToken
                        },
                        success: function() {
                            loadTaskDetails(taskId);
                        },
                        error: function() {
                            alert('Gagal membuat checklist.');
                        }
                    });
                }
            });

            $('#btnQuickAttachment').click(function() {
                $('#taskAttachmentInput').click();
            });

            $('#taskAttachmentInput').change(function() {
                const file = this.files[0];
                if (!file) return;

                const taskId = $('#editTaskId').val();
                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', csrfToken);

                // Show visual upload feedback
                $('#attachmentsListContainer').append(`
                    <div id="uploadingLoader" class="d-flex align-items-center border p-2 rounded bg-light">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        <span class="small text-muted">Mengunggah file...</span>
                    </div>
                `);

                $.ajax({
                    url: `/kanban/tasks/${taskId}/attachments`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $('#taskAttachmentInput').val('');
                        loadTaskDetails(taskId);
                    },
                    error: function(xhr) {
                        $('#uploadingLoader').remove();
                        const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal mengunggah file. Pastikan ukuran file maks 10MB.';
                        alert(errorMsg);
                    }
                });
            });

            $(document).on('click', '.btn-delete-attachment', function(e) {
                e.preventDefault();
                const attId = $(this).data('id');
                const taskId = $('#editTaskId').val();
                
                if (confirm('Apakah Anda yakin ingin menghapus lampiran ini?')) {
                    $.ajax({
                        url: `/kanban/attachments/${attId}`,
                        method: 'DELETE',
                        data: {
                            _token: csrfToken
                        },
                        success: function() {
                            loadTaskDetails(taskId);
                        },
                        error: function() {
                            alert('Gagal menghapus lampiran.');
                        }
                    });
                }
            });

            // Toggle labels
            $('.btn-toggle-label').click(function() {
                const taskId = $('#editTaskId').val();
                const color = $(this).data('color');
                
                let selectedLabels = currentTaskData.labels ? [...currentTaskData.labels] : [];
                const idx = selectedLabels.indexOf(color);
                if (idx !== -1) {
                    selectedLabels.splice(idx, 1); // remove
                } else {
                    selectedLabels.push(color); // add
                }

                $.ajax({
                    url: `/kanban/tasks/${taskId}/labels`,
                    method: 'POST',
                    data: {
                        labels: selectedLabels,
                        _token: csrfToken
                    },
                    success: function() {
                        loadTaskDetails(taskId);
                        loadKanbanBoard();
                    },
                    error: function() {
                        alert('Gagal memperbarui label.');
                    }
                });
            });

            // Remove label from task via click on (x) in badge
            $(document).on('click', '.btn-remove-label-from-task', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const color = $(this).data('color');
                const taskId = $('#editTaskId').val();
                
                let selectedLabels = currentTaskData.labels ? [...currentTaskData.labels] : [];
                selectedLabels = selectedLabels.filter(item => item !== color);

                $.ajax({
                    url: `/kanban/tasks/${taskId}/labels`,
                    method: 'POST',
                    data: {
                        labels: selectedLabels,
                        _token: csrfToken
                    },
                    success: function() {
                        loadTaskDetails(taskId);
                        loadKanbanBoard();
                    },
                    error: function() {
                        alert('Gagal menghapus label dari tugas.');
                    }
                });
            });

            // Checklist Item CRUD
            $(document).on('click', '.btn-delete-checklist', function() {
                const block = $(this).closest('.checklist-block');
                const checklistId = block.data('checklist-id');
                const taskId = $('#editTaskId').val();
                
                if (confirm('Hapus checklist ini?')) {
                    $.ajax({
                        url: `/kanban/checklists/${checklistId}`,
                        method: 'DELETE',
                        data: { _token: csrfToken },
                        success: function() {
                            loadTaskDetails(taskId);
                        }
                    });
                }
            });

            $(document).on('click', '.btn-show-add-item', function() {
                $(this).hide();
                $(this).next('.add-item-form').show().find('.checklist-item-input').focus();
            });

            $(document).on('click', '.btn-cancel-checklist-item', function() {
                const form = $(this).closest('.add-item-form');
                form.hide().find('.checklist-item-input').val('');
                form.prev('.btn-show-add-item').show();
            });

            $(document).on('click', '.btn-save-checklist-item', function() {
                const form = $(this).closest('.add-item-form');
                const input = form.find('.checklist-item-input');
                const title = input.val().trim();
                const checklistId = form.closest('.checklist-block').data('checklist-id');
                const taskId = $('#editTaskId').val();

                if (!title) return;

                $.ajax({
                    url: `/kanban/checklists/${checklistId}/items`,
                    method: 'POST',
                    data: {
                        title: title,
                        _token: csrfToken
                    },
                    success: function() {
                        loadTaskDetails(taskId);
                    }
                });
            });

            $(document).on('change', '.btn-toggle-checklist-item', function() {
                const row = $(this).closest('.checklist-item-row');
                const itemId = row.data('item-id');
                const taskId = $('#editTaskId').val();

                $.ajax({
                    url: `/kanban/checklist-items/${itemId}/toggle`,
                    method: 'POST',
                    data: { _token: csrfToken },
                    success: function() {
                        loadTaskDetails(taskId);
                    }
                });
            });

            $(document).on('click', '.btn-delete-checklist-item', function() {
                const row = $(this).closest('.checklist-item-row');
                const itemId = row.data('item-id');
                const taskId = $('#editTaskId').val();

                $.ajax({
                    url: `/kanban/checklist-items/${itemId}`,
                    method: 'DELETE',
                    data: { _token: csrfToken },
                    success: function() {
                        loadTaskDetails(taskId);
                    }
                });
            });

            // Comments Edit & Delete actions
            $(document).on('click', '.btn-edit-comment', function() {
                const commentId = $(this).data('id');
                $(`#comment-display-text-${commentId}`).addClass('d-none');
                $(`#comment-edit-area-${commentId}`).removeClass('d-none');
            });

            $(document).on('click', '.btn-cancel-edit-comment', function() {
                const commentId = $(this).data('id');
                $(`#comment-edit-area-${commentId}`).addClass('d-none');
                $(`#comment-display-text-${commentId}`).removeClass('d-none');
            });

            $(document).on('click', '.btn-save-edit-comment', function() {
                const commentId = $(this).data('id');
                const text = $(`#comment-edit-input-${commentId}`).val().trim();
                const taskId = $('#editTaskId').val();

                if (!text) return;

                $.ajax({
                    url: `/kanban/comments/${commentId}/update`,
                    method: 'POST',
                    data: {
                        comment: text,
                        _token: csrfToken
                    },
                    success: function() {
                        loadTaskDetails(taskId);
                    },
                    error: function() {
                        alert('Gagal menyunting komentar.');
                    }
                });
            });

            // Delete comment action
            $(document).on('click', '.btn-delete-comment', function() {
                const commentId = $(this).data('id');
                const taskId = $('#editTaskId').val();

                if (confirm('Hapus komentar ini?')) {
                    $.ajax({
                        url: `/kanban/comments/${commentId}`,
                        method: 'DELETE',
                        data: { _token: csrfToken },
                        success: function() {
                            loadTaskDetails(taskId);
                        },
                        error: function() {
                            alert('Gagal menghapus komentar.');
                        }
                    });
                }
            });

            // Handle Submit Comment
            $('#submitCommentBtn').click(function() {
                const taskId = $('#editTaskId').val();
                const commentText = $('#commentTextInput').val().trim();
                
                if (!commentText) return;

                $.ajax({
                    url: `/kanban/tasks/${taskId}/comment`,
                    method: 'POST',
                    data: {
                        comment: commentText,
                        _token: csrfToken
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#commentTextInput').val('');
                            loadTaskDetails(taskId);
                        }
                    },
                    error: function() {
                        alert('Gagal mengirim komentar.');
                    }
                });
            });

            // Board members list for @mentions autocomplete
            const boardMembers = [
                @foreach($board->members as $member)
                    { id: "{{ $member->id }}", name: "{{ $member->name }}" },
                @endforeach
            ];

            const commentInput = $('#commentTextInput');
            const mentionDropdown = $('#mentionDropdown');
            let mentionQueryStart = -1;

            commentInput.on('input', function() {
                const text = $(this).val();
                const caretPos = this.selectionStart;
                
                // Find the last index of '@' before caret
                const textBeforeCaret = text.substring(0, caretPos);
                const lastAtPos = textBeforeCaret.lastIndexOf('@');
                
                if (lastAtPos !== -1) {
                    // Ensure it is either at index 0 or preceded by a space/newline
                    const charBeforeAt = lastAtPos > 0 ? textBeforeCaret.charAt(lastAtPos - 1) : ' ';
                    
                    if (charBeforeAt === ' ' || charBeforeAt === '\n') {
                        const query = textBeforeCaret.substring(lastAtPos + 1);
                        // Ensure the query doesn't contain spaces
                        if (!query.includes(' ')) {
                            mentionQueryStart = lastAtPos;
                            showMentionDropdown(query);
                            return;
                        }
                    }
                }
                
                hideMentionDropdown();
            });

            // Prevent Enter and adjust arrows when dropdown is open
            commentInput.on('keydown', function(e) {
                if (mentionDropdown.is(':visible')) {
                    const activeItem = mentionDropdown.find('.dropdown-item.active');
                    
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (activeItem.length) {
                            const next = activeItem.next('.dropdown-item');
                            if (next.length) {
                                activeItem.removeClass('active');
                                next.addClass('active');
                            }
                        } else {
                            mentionDropdown.find('.dropdown-item').first().addClass('active');
                        }
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (activeItem.length) {
                            const prev = activeItem.prev('.dropdown-item');
                            if (prev.length) {
                                activeItem.removeClass('active');
                                prev.addClass('active');
                            }
                        } else {
                            mentionDropdown.find('.dropdown-item').last().addClass('active');
                        }
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (activeItem.length) {
                            activeItem.click();
                        } else {
                            mentionDropdown.find('.dropdown-item').first().click();
                        }
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        hideMentionDropdown();
                    }
                }
            });

            // Close dropdown on click outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.position-relative').length) {
                    hideMentionDropdown();
                }
            });

            function showMentionDropdown(query) {
                const matches = boardMembers.filter(m => m.name.toLowerCase().includes(query.toLowerCase()));
                
                if (matches.length === 0) {
                    hideMentionDropdown();
                    return;
                }

                let html = '';
                matches.forEach((m, index) => {
                    const activeClass = index === 0 ? 'active' : '';
                    html += `<a href="javascript:void(0);" class="dropdown-item ${activeClass} btn-select-mention" data-name="${m.name}">${m.name}</a>`;
                });

                mentionDropdown.html(html).show();
            }

            function hideMentionDropdown() {
                mentionDropdown.hide().html('');
                mentionQueryStart = -1;
            }

            // Handle mention selection
            $(document).on('click', '.btn-select-mention', function() {
                const name = $(this).data('name');
                const text = commentInput.val();
                const caretPos = commentInput[0].selectionStart;
                
                // Replace the "@query" string with "@Name "
                const beforeMention = text.substring(0, mentionQueryStart);
                const afterCaret = text.substring(caretPos);
                
                const newText = beforeMention + '@' + name + ' ' + afterCaret;
                commentInput.val(newText);
                
                // Reset caret position right after the mention
                const newCaretPos = mentionQueryStart + name.length + 2; // +2 for @ and space
                commentInput[0].setSelectionRange(newCaretPos, newCaretPos);
                
                hideMentionDropdown();
                commentInput.focus();
            });

            // Handle Edit Task Submission
            $('#editTaskForm').submit(function(e) {
                e.preventDefault();
                const taskId = $('#editTaskId').val();
                const title = $('#editTaskTitle').val();
                const desc = $('#editTaskDescription').val();
                const assignee = $('#editTaskAssignee').val();
                const dueDate = $('#editTaskDueDate').val();
                const columnId = $('#editTaskColumn').val();

                $.ajax({
                    url: `/kanban/tasks/${taskId}/update`,
                    method: 'POST',
                    data: {
                        title: title,
                        description: desc,
                        assignees: assignee,
                        due_date: dueDate,
                        priority: $('#editTaskPriority').val(),
                        column_id: columnId,
                        _token: csrfToken
                    },
                    success: function(response) {
                        const modalEl = document.getElementById('taskDetailsModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        modal.hide();
                        loadKanbanBoard();
                    },
                    error: function() {
                        alert('Gagal memperbarui tugas.');
                    }
                });
            });

            // Handle Link Service Report click
            $(document).on('click', '#btnConnectReport', function(e) {
                e.preventDefault();
                const taskId = $('#editTaskId').val();
                if (!taskId || !currentTaskData) return;
                
                const selectedReportId = $('#editTaskServiceReport').val();
                if (!selectedReportId) {
                    alert('Silakan pilih Service Report terlebih dahulu.');
                    return;
                }

                $.ajax({
                    url: `/kanban/tasks/${taskId}/update`,
                    method: 'POST',
                    data: {
                        title: currentTaskData.title,
                        description: $('#editTaskDescription').val(),
                        assignees: $('#editTaskAssignee').val(),
                        due_date: $('#editTaskDueDate').val(),
                        priority: $('#editTaskPriority').val(),
                        column_id: 'column_' + currentTaskData.column_id,
                        service_report_id: selectedReportId,
                        _token: csrfToken
                    },
                    success: function() {
                        loadTaskDetails(taskId);
                        loadKanbanBoard();
                    },
                    error: function() {
                        alert('Gagal menghubungkan Service Report.');
                    }
                });
            });

            // Handle Unlink Service Report
            $(document).on('click', '.btn-unlink-report', function(e) {
                e.preventDefault();
                const taskId = $('#editTaskId').val();
                if (!taskId || !currentTaskData) return;
                if (confirm('Apakah Anda yakin ingin memutuskan hubungan dengan Service Report ini?')) {
                    $.ajax({
                        url: `/kanban/tasks/${taskId}/update`,
                        method: 'POST',
                        data: {
                            title: currentTaskData.title,
                            description: $('#editTaskDescription').val(),
                            assignees: $('#editTaskAssignee').val(),
                            due_date: $('#editTaskDueDate').val(),
                            priority: $('#editTaskPriority').val(),
                            column_id: 'column_' + currentTaskData.column_id,
                            service_report_id: null,
                            _token: csrfToken
                        },
                        success: function() {
                            loadTaskDetails(taskId);
                            loadKanbanBoard();
                        },
                        error: function() {
                            alert('Gagal memutuskan Service Report.');
                        }
                    });
                }
            });

            // Handle Create BAST click
            $(document).on('click', '#btnCreateBastFromCard', function() {
                const taskId = $('#editTaskId').val();
                const prefill = currentBastPrefill || {};
                if (typeof window.openBastModal === 'function') {
                    window.openBastModal({
                        idKanbanTask: taskId,
                        idQuotation: prefill.id_quotation || '',
                        entity: prefill.entity || 'Reftech',
                        customerName: prefill.customer_name || '',
                        workTitle: prefill.work_title || '',
                        poNumber: prefill.po_number || '',
                    });
                }
            });

            $(document).on('bast:saved', function(e, response) {
                const taskId = $('#editTaskId').val();
                if (taskId) {
                    loadTaskDetails(taskId);
                }
                if (response && response.bast && response.bast.print_link) {
                    window.open(response.bast.print_link, '_blank');
                }
            });

            // Handle Delete Task
            $('#deleteTaskBtn').click(function() {
                const taskId = $('#editTaskId').val();
                if (confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
                    $.ajax({
                        url: `/kanban/tasks/${taskId}`,
                        method: 'DELETE',
                        data: {
                            _token: csrfToken
                        },
                        success: function(response) {
                            const modalEl = document.getElementById('taskDetailsModal');
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            modal.hide();
                            loadKanbanBoard();
                        },
                        error: function() {
                            alert('Gagal menghapus tugas.');
                        }
                    });
                }
            });

            // Load board initial
            loadKanbanBoard();

            // Poll Kanban board data every 5 seconds in the background
            setInterval(function() {
                loadKanbanBoard();
            }, 5000);

            // Custom Add Task click handler
            $(document).on('click', '.btn-add-task-custom', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const colId = $(this).data('column-id');
                openCreateTaskModal(colId);
            });

            // JS definitions for all possible colors
            const allColors = {
                primary: { name: 'Biru', class: 'bg-primary' },
                success: { name: 'Hijau', class: 'bg-success' },
                danger: { name: 'Merah', class: 'bg-danger' },
                warning: { name: 'Kuning', class: 'bg-warning' },
                info: { name: 'Cyan', class: 'bg-info' },
                secondary: { name: 'Abu-abu', class: 'bg-secondary' }
            };

            // Function to refresh settings labels dropdown options
            function refreshSettingsLabelDropdown() {
                let activeColors = [];
                $('.settings-label-item').each(function() {
                    activeColors.push($(this).data('color'));
                });

                let menuHtml = '';
                let hasAvailable = false;

                Object.keys(allColors).forEach(function(color) {
                    if (!activeColors.includes(color)) {
                        menuHtml += `<li><a class="dropdown-item rounded btn-add-settings-label-option" href="javascript:void(0);" data-color="${color}"><span class="badge bg-${color} me-2">&nbsp;</span>${allColors[color].name}</a></li>`;
                        hasAvailable = true;
                    }
                });

                if (hasAvailable) {
                    $('#settingsAddLabelMenu').html(menuHtml);
                    $('#settingsAddLabelBtn').prop('disabled', false);
                } else {
                    $('#settingsAddLabelMenu').html('<li><span class="dropdown-item-text text-muted small">Semua warna aktif</span></li>');
                    $('#settingsAddLabelBtn').prop('disabled', true);
                }
            }

            // Trigger when Settings Modal is shown
            $('#boardSettingsModal').on('show.bs.modal', function() {
                refreshSettingsLabelDropdown();
                updateSettingsColumnOrder();
            });

            // Handle Add Label selection from dropdown
            $(document).on('click', '.btn-add-settings-label-option', function(e) {
                e.preventDefault();
                const color = $(this).data('color');
                const colorDetails = allColors[color];

                const html = `
                    <div class="col-sm-6 settings-label-item" data-color="${color}">
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text bg-${color} text-white" style="width: 32px; border: 0;">&nbsp;</span>
                            <input type="text" class="form-control form-control-sm" name="labels[${color}]" value="" placeholder="${colorDetails.name}">
                            <button class="btn btn-outline-danger btn-remove-settings-label" type="button"><i class="mdi mdi-close"></i></button>
                        </div>
                    </div>
                `;

                $('#settingsLabelsContainer').append(html);
                refreshSettingsLabelDropdown();
            });

            // Handle remove label in Board Settings Modal
            $(document).on('click', '.btn-remove-settings-label', function() {
                $(this).closest('.settings-label-item').remove();
                refreshSettingsLabelDropdown();
            });

            // Helper: Update numbers, buttons, and input attributes for settings columns
            function updateSettingsColumnOrder() {
                const total = $('.settings-column-item').length;
                $('.settings-column-item').each(function(index) {
                    $(this).find('.col-order-badge').text(index + 1);
                    $(this).find('input[type="hidden"]').attr('name', 'columns[' + index + '][id]');
                    $(this).find('input[type="text"]').attr('name', 'columns[' + index + '][title]');

                    $(this).find('.btn-move-col-up').prop('disabled', index === 0);
                    $(this).find('.btn-move-col-down').prop('disabled', index === total - 1);
                });
            }

            // Move Column Up in Settings Modal
            $(document).on('click', '.btn-move-col-up', function(e) {
                e.preventDefault();
                const item = $(this).closest('.settings-column-item');
                const prev = item.prev('.settings-column-item');
                if (prev.length) {
                    item.insertBefore(prev);
                    updateSettingsColumnOrder();
                }
            });

            // Move Column Down in Settings Modal
            $(document).on('click', '.btn-move-col-down', function(e) {
                e.preventDefault();
                const item = $(this).closest('.settings-column-item');
                const next = item.next('.settings-column-item');
                if (next.length) {
                    item.insertAfter(next);
                    updateSettingsColumnOrder();
                }
            });

            // Admin/Accounting: Board Settings Column Add
            $('#settingsAddColBtn').click(function() {
                const count = $('.settings-column-item').length + 1;
                const html = `
                    <div class="settings-column-item card border shadow-xs mb-0 bg-white" data-id="" draggable="true" style="cursor: grab; transition: all 0.2s ease;">
                        <div class="card-body p-2 d-flex align-items-center gap-2">
                            <div class="col-drag-handle text-muted px-1" title="Tahan & geser untuk mengubah urutan" style="cursor: grab;">
                                <i class="mdi mdi-drag-vertical" style="font-size: 20px;"></i>
                            </div>
                            <span class="badge bg-label-primary rounded-pill col-order-badge px-2" style="font-size: 11px; min-width: 24px;">${count}</span>
                            <input type="hidden" name="columns[][id]" value="">
                            <input type="text" class="form-control form-control-sm border-0 bg-transparent fw-semibold" name="columns[][title]" value="" required placeholder="Nama Kolom Baru" style="box-shadow: none;">
                            <div class="d-flex align-items-center gap-1 ms-auto flex-shrink-0">
                                <button type="button" class="btn btn-xs btn-icon btn-outline-secondary btn-move-col-up" title="Pindah ke Atas">
                                    <i class="mdi mdi-arrow-up" style="font-size: 14px;"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-icon btn-outline-secondary btn-move-col-down" title="Pindah ke Bawah">
                                    <i class="mdi mdi-arrow-down" style="font-size: 14px;"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-icon btn-outline-danger btn-remove-settings-column ms-1" title="Hapus Kolom">
                                    <i class="mdi mdi-trash-can-outline" style="font-size: 14px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                $('#settingsColumnsContainer').append(html);
                updateSettingsColumnOrder();
            });

            // Admin/Accounting: Board Settings Column Remove
            $(document).on('click', '.btn-remove-settings-column', function() {
                if ($('.settings-column-item').length > 1) {
                    $(this).closest('.settings-column-item').remove();
                    updateSettingsColumnOrder();
                } else {
                    alert('Minimal harus memiliki 1 kolom!');
                }
            });

            // Drag & Drop Columns Reorder in Settings Modal
            let draggedColItem = null;

            $(document).on('dragstart', '.settings-column-item', function(e) {
                draggedColItem = this;
                $(this).addClass('dragging');
                if (e.originalEvent && e.originalEvent.dataTransfer) {
                    e.originalEvent.dataTransfer.effectAllowed = 'move';
                    e.originalEvent.dataTransfer.setData('text/html', this.innerHTML);
                }
            });

            $(document).on('dragover', '.settings-column-item', function(e) {
                e.preventDefault();
                if (e.originalEvent && e.originalEvent.dataTransfer) {
                    e.originalEvent.dataTransfer.dropEffect = 'move';
                }
                if (draggedColItem && draggedColItem !== this) {
                    const bounding = this.getBoundingClientRect();
                    const offset = e.originalEvent.clientY - bounding.top;
                    if (offset > bounding.height / 2) {
                        this.parentNode.insertBefore(draggedColItem, this.nextSibling);
                    } else {
                        this.parentNode.insertBefore(draggedColItem, this);
                    }
                    updateSettingsColumnOrder();
                }
            });

            $(document).on('dragend', '.settings-column-item', function(e) {
                $(this).removeClass('dragging');
                $('.settings-column-item').removeClass('drag-over');
                draggedColItem = null;
                updateSettingsColumnOrder();
            });

            // Scroll Arrow Navigation and indicator visibility
            function updateBoardScrollArrows() {
                const wrapper = $('.kanban-wrapper')[0];
                if (!wrapper) return;

                const scrollLeft = wrapper.scrollLeft;
                const scrollWidth = wrapper.scrollWidth;
                const clientWidth = wrapper.clientWidth;

                if (scrollWidth > clientWidth) {
                    if (scrollLeft > 5) {
                        $('#btnScrollBoardLeft').addClass('scroll-active');
                    } else {
                        $('#btnScrollBoardLeft').removeClass('scroll-active');
                    }

                    if (scrollLeft + clientWidth < scrollWidth - 5) {
                        $('#btnScrollBoardRight').addClass('scroll-active');
                    } else {
                        $('#btnScrollBoardRight').removeClass('scroll-active');
                    }
                } else {
                    $('#btnScrollBoardLeft').removeClass('scroll-active');
                    $('#btnScrollBoardRight').removeClass('scroll-active');
                }
            }

            $('.kanban-wrapper').on('scroll', function() {
                updateBoardScrollArrows();
            });

            $(window).on('resize', function() {
                updateBoardScrollArrows();
            });

            $('#btnScrollBoardLeft').click(function() {
                const wrapper = $('.kanban-wrapper')[0];
                if (wrapper) {
                    wrapper.scrollBy({ left: -300, behavior: 'smooth' });
                }
            });

            $('#btnScrollBoardRight').click(function() {
                const wrapper = $('.kanban-wrapper')[0];
                if (wrapper) {
                    wrapper.scrollBy({ left: 300, behavior: 'smooth' });
                }
            });

            // Horizontal Drag-to-Scroll (Grab to scroll) on Kanban Board wrapper
            const slider = document.querySelector('.kanban-wrapper');
            let isDown = false;
            let startX;
            let scrollLeft;

            slider.addEventListener('mousedown', (e) => {
                // Prevent drag-scroll when clicking cards, forms, inputs, buttons or link elements
                if (e.target.closest('.kanban-item') || e.target.closest('button') || e.target.closest('a') || e.target.closest('select') || e.target.closest('input') || e.target.closest('textarea')) {
                    return;
                }
                isDown = true;
                slider.classList.add('grabbing-board');
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });

            slider.addEventListener('mouseleave', () => {
                isDown = false;
                slider.classList.remove('grabbing-board');
            });

            slider.addEventListener('mouseup', () => {
                isDown = false;
                slider.classList.remove('grabbing-board');
            });

            slider.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - slider.offsetLeft;
                const walk = (x - startX) * 1.5; // multiplier for scrolling speed sensitivity
                slider.scrollLeft = scrollLeft - walk;
            });

            // Non-Admin: Handle request deletion click
            $(document).on('click', '#requestDeleteTaskBtn', function() {
                const taskId = $('#editTaskId').val();
                if (confirm('Apakah Anda yakin ingin mengajukan penghapusan tugas ini kepada Admin?')) {
                    $.ajax({
                        url: `/kanban/tasks/${taskId}/request-delete`,
                        method: 'POST',
                        data: {
                            _token: csrfToken
                        },
                        success: function() {
                            alert('Pengajuan hapus tugas berhasil dikirim ke Admin.');
                            const modalEl = document.getElementById('taskDetailsModal');
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            modal.hide();
                            loadKanbanBoard();
                        },
                        error: function() {
                            alert('Gagal mengirimkan pengajuan hapus tugas.');
                        }
                    });
                }
            });

            // Admin only: Poll and load delete requests
            @if (auth()->user()->role === 'Admin' || auth()->id() == $board->created_by)
                function loadDeleteRequests() {
                    $.ajax({
                        url: `/kanban/boards/${boardId}/delete-requests`,
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                const reqs = response.requests;
                                if (reqs.length > 0) {
                                    $('#deleteRequestsBadge').text(reqs.length).show();
                                    
                                    let html = '';
                                    reqs.forEach(function(req) {
                                        let avatar = '';
                                        if (req.requested_by_avatar) {
                                            avatar = `<img src="${req.requested_by_avatar}" class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">`;
                                        } else {
                                            const init = req.requested_by_name.charAt(0).toUpperCase();
                                            avatar = `<span class="avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center fw-semibold" style="width: 24px; height: 24px; font-size: 10px;">${init}</span>`;
                                        }

                                        html += `
                                            <li class="dropdown-item d-flex align-items-center justify-content-between py-2 border-bottom" style="white-space: normal;">
                                                <div class="d-flex align-items-center" style="max-width: 75%;">
                                                    <div class="avatar avatar-xs me-2" style="width: 24px; height: 24px;">
                                                        ${avatar}
                                                    </div>
                                                    <div>
                                                        <strong class="d-block text-heading" style="font-size: 12px;">${req.task_title}</strong>
                                                        <small class="text-muted" style="font-size: 10px;">Oleh ${req.requested_by_name} • ${req.created_at}</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-1">
                                                    <button type="button" class="btn btn-xs btn-success btn-icon btn-approve-delete" data-id="${req.id}" title="Approve"><i class="mdi mdi-check"></i></button>
                                                    <button type="button" class="btn btn-xs btn-danger btn-icon btn-reject-delete" data-id="${req.id}" title="Reject"><i class="mdi mdi-close"></i></button>
                                                </div>
                                            </li>
                                        `;
                                    });
                                    $('#deleteRequestsList').html(html);
                                } else {
                                    $('#deleteRequestsBadge').hide().text('0');
                                    $('#deleteRequestsList').html('<li class="text-center py-3 text-muted">Tidak ada pengajuan baru</li>');
                                }
                            }
                        }
                    });
                }

                // Initial load and set interval
                loadDeleteRequests();
                setInterval(loadDeleteRequests, 5000);

                // Handle approve delete request click
                $(document).on('click', '.btn-approve-delete', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const reqId = $(this).data('id');
                    if (confirm('Apakah Anda yakin menyetujui penghapusan tugas ini?')) {
                        $.ajax({
                            url: `/kanban/delete-requests/${reqId}/approve`,
                            method: 'POST',
                            data: {
                                _token: csrfToken
                            },
                            success: function() {
                                loadDeleteRequests();
                                loadKanbanBoard();
                            },
                            error: function() {
                                alert('Gagal menyetujui penghapusan.');
                            }
                        });
                    }
                });

                // Handle reject delete request click
                $(document).on('click', '.btn-reject-delete', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const reqId = $(this).data('id');
                    $.ajax({
                        url: `/kanban/delete-requests/${reqId}/reject`,
                        method: 'POST',
                        data: {
                            _token: csrfToken
                        },
                        success: function() {
                            loadDeleteRequests();
                        },
                        error: function() {
                            alert('Gagal menolak pengajuan.');
                        }
                    });
                });
            @endif

            // Client-side Card Search + Accounting multi-toggle filter
            function applyKanbanFilters() {
                const query = $('#kanbanSearchInput').val().toLowerCase().trim();
                
                let allowedSalesIds = null;
                if (boardType === 'monitoring') {
                    const activeButtons = $('.accounting-tab-btn.active');
                    const totalButtons = $('.accounting-tab-btn').length;
                    
                    // If only a subset of accounting tabs is active, collect allowed sales IDs
                    if (activeButtons.length > 0 && activeButtons.length < totalButtons) {
                        allowedSalesIds = [];
                        activeButtons.each(function() {
                            const accId = String($(this).data('accounting-id'));
                            const salesList = accountingSalesMap[accId] || [];
                            salesList.forEach(function(sId) {
                                if (!allowedSalesIds.includes(String(sId))) {
                                    allowedSalesIds.push(String(sId));
                                }
                            });
                        });
                    }
                }

                $('.kanban-item').each(function() {
                    const titleText = $(this).find('.text-heading, .text-primary, .text-danger').text().toLowerCase();
                    const descText = $(this).find('.text-muted').text().toLowerCase();
                    const taskData = $(this).find('.kanban-item-content').data('task') || {};
                    const entityText = (taskData.entity_type || '').toLowerCase();
                    const entityFullName = (taskData.entity_type === 'KII' ? 'kojisha' : (taskData.entity_type === 'RJO' ? 'reftech' : ''));
                    const poText = (taskData.no_po || '').toLowerCase();
                    const soText = (taskData.no_so || '').toLowerCase();

                    const matchesSearch = !query || titleText.includes(query) || descText.includes(query) || poText.includes(query) || soText.includes(query) || (boardType === 'monitoring' && (entityText.includes(query) || entityFullName.includes(query)));
                    const matchesAccounting = (boardType !== 'monitoring') || !allowedSalesIds || allowedSalesIds.includes(String(taskData.id_sales));

                    if (matchesSearch && matchesAccounting) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                // Update column badge counters to reflect visible cards
                $('.kanban-board').each(function() {
                    const visibleCount = $(this).find('.kanban-item:visible').length;
                    $(this).find('.kanban-col-header-custom .badge').text(visibleCount);
                });
            }

            $(document).on('keyup', '#kanbanSearchInput', applyKanbanFilters);

            // Accounting Filter Multi-Toggle Tab Switcher click
            $(document).on('click', '.accounting-tab-btn', function() {
                const isCurrentlyActive = $(this).hasClass('active');
                const totalActive = $('.accounting-tab-btn.active').length;
                const totalButtons = $('.accounting-tab-btn').length;

                if (totalActive === totalButtons) {
                    // When all are currently active, clicking one isolates that specific tab
                    $('.accounting-tab-btn').removeClass('active').addClass('text-muted fw-semibold').removeClass('fw-bold');
                    $(this).addClass('active fw-bold').removeClass('text-muted fw-semibold');
                } else if (isCurrentlyActive && totalActive === 1) {
                    // Clicking the only active tab re-enables all tabs
                    $('.accounting-tab-btn').addClass('active fw-bold').removeClass('text-muted fw-semibold');
                } else {
                    // Toggle normal state
                    $(this).toggleClass('active');
                    if ($(this).hasClass('active')) {
                        $(this).removeClass('text-muted fw-semibold').addClass('fw-bold');
                    } else {
                        $(this).addClass('text-muted fw-semibold').removeClass('fw-bold');
                    }
                }

                applyKanbanFilters();
            });

            // Handle test sound click in Board Settings
            $(document).on('click', '#btnTestSound', function(e) {
                e.preventDefault();
                const soundUrl = $(this).data('sound');
                if (soundUrl) {
                    try {
                        const audio = new Audio(soundUrl);
                        audio.play();
                    } catch(err) {
                        alert('Browser memblokir autoplay suara. Silakan berinteraksi dengan halaman (klik bebas) terlebih dahulu.');
                    }
                }
            });

            // Submit board settings
            $('#boardSettingsForm').submit(function(e) {
                e.preventDefault();
                
                // Format arrays for proper indexed array parameter
                $('.settings-column-item').each(function(index, el) {
                    $(el).find('input[type="hidden"]').attr('name', 'columns[' + index + '][id]');
                    $(el).find('input[type="text"]').attr('name', 'columns[' + index + '][title]');
                });

                const formData = new FormData(this);
                formData.append('_token', csrfToken);

                $.ajax({
                    url: `/kanban/boards/${boardId}/update`,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#boardSettingsModal').modal('hide');
                        window.location.reload();
                    },
                    error: function() {
                        alert('Gagal memperbarui pengaturan papan.');
                    }
                });
            });

            @if ($board->type === 'monitoring')
            // Save Accounting -> Sales mapping
            $('#btnSaveAccountingMapping').click(function() {
                const $btn = $(this);
                const mappings = [];
                $('.accounting-mapping-select').each(function() {
                    mappings.push({
                        id_accounting: $(this).data('accounting-id'),
                        sales_ids: $(this).val() || []
                    });
                });

                $btn.prop('disabled', true);
                $.ajax({
                    url: '{{ route("kanban.monitoring-document.accounting-mapping") }}',
                    method: 'POST',
                    data: {
                        mappings: mappings,
                        _token: csrfToken
                    },
                    success: function(response) {
                        alert(response.message || 'Mapping berhasil disimpan.');
                        window.location.reload();
                    },
                    error: function() {
                        alert('Gagal menyimpan mapping Accounting-Sales.');
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });

            // Beautiful Floating Toast Notification
            function showCustomNotification() {
                let container = $('#customToastContainer');
                if (!container.length) {
                    $('body').append('<div id="customToastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999; pointer-events: none;"></div>');
                }
                
                const toastHtml = `
                    <div class="custom-toast">
                        <div class="toast-bell-icon">
                            <i class="mdi mdi-bell-ring-outline"></i>
                        </div>
                        <div style="flex-grow: 1;">
                            <h6 class="fw-bold mb-1 text-primary" style="font-size: 14px; margin-bottom: 2px;">Ada PO Baru Masuk!</h6>
                            <p class="text-muted mb-0" style="font-size: 12px; line-height: 1.4;">Dokumen telah berhasil disinkronisasi ke dalam papan monitoring.</p>
                        </div>
                        <button type="button" class="btn-close" style="font-size: 10px; opacity: 0.5;" onclick="$(this).closest('.custom-toast').remove()"></button>
                    </div>
                `;
                
                $('#customToastContainer').append(toastHtml);
                
                setTimeout(function() {
                    $('#customToastContainer .custom-toast').first().remove();
                }, 5200);
            }

            // Polling check for new cards
            function pollNewCards() {
                if (lastTaskId === 0) return;
                
                $.ajax({
                    url: `/accounting/monitoring-document/check-new-cards/${lastTaskId}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.new_cards > 0) {
                            const soundPath = "{{ $board->notification_sound ? asset($board->notification_sound) : asset('assets/audio/kanban-notification-default.wav') }}";
                            try {
                                const audio = new Audio(soundPath);
                                audio.play();
                            } catch(e) {
                                console.error("Audio playback blocked:", e);
                            }

                            showCustomNotification();
                            loadKanbanBoard();
                        }
                    }
                });
            }

            // Fullscreen Toggle Handler
            $('#btnToggleFullscreen').click(function(e) {
                e.preventDefault();
                $('body').toggleClass('kanban-fullscreen-mode');
                const isFullscreen = $('body').hasClass('kanban-fullscreen-mode');
                
                if (isFullscreen) {
                    $('#fsIcon').removeClass('mdi-fullscreen').addClass('mdi-fullscreen-exit');
                    $('#fsText').text('Exit Fullscreen');
                    
                    const elem = document.documentElement;
                    if (elem.requestFullscreen) {
                        elem.requestFullscreen();
                    } else if (elem.webkitRequestFullscreen) { /* Safari */
                        elem.webkitRequestFullscreen();
                    } else if (elem.msRequestFullscreen) { /* IE11 */
                        elem.msRequestFullscreen();
                    }
                } else {
                    $('#fsIcon').removeClass('mdi-fullscreen-exit').addClass('mdi-fullscreen');
                    $('#fsText').text('Fullscreen');
                    
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.webkitExitFullscreen) { /* Safari */
                        document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) { /* IE11 */
                        document.msExitFullscreen();
                    }
                }
                window.dispatchEvent(new Event('resize'));
            });

            // Handle native browser escape/exit fullscreen
            $(document).on('fullscreenchange webkitfullscreenchange mozfullscreenchange MSFullscreenChange', function() {
                const nativeFullscreen = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
                if (!nativeFullscreen) {
                    $('body').removeClass('kanban-fullscreen-mode');
                    $('#fsIcon').removeClass('mdi-fullscreen-exit').addClass('mdi-fullscreen');
                    $('#fsText').text('Fullscreen');
                    window.dispatchEvent(new Event('resize'));
                }
            });

            setInterval(pollNewCards, 10000);
            @endif
        });
    </script>
@endpush
