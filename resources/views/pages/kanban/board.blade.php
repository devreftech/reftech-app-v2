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
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center py-3 mb-4 gap-2 border-bottom">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <h4 class="fw-bold mb-0 text-primary">{{ $board->title }}</h4>
                <div class="dropdown">
                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="boardSwitcher" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Switch Board
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
                <div class="input-group input-group-merge input-group-sm ms-2" style="width: 220px;">
                    <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                    <input type="text" class="form-control" id="kanbanSearchInput" placeholder="Cari kartu tugas...">
                </div>
                @if ($board->type === 'monitoring')
                    <select class="form-select form-select-sm ms-2" id="kanbanAccountingFilter" style="width: 200px;">
                        <option value="">-- Semua Accounting --</option>
                        @foreach ($accountingUsers ?? [] as $accountingUser)
                            <option value="{{ $accountingUser->id }}">{{ $accountingUser->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('kanban.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Portal
                </a>
                @if (auth()->user()->role === 'Admin' || auth()->id() == $board->created_by)
                    <!-- Notification Bell Dropdown for Delete Requests -->
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-sm position-relative dropdown-toggle hide-arrow" type="button" id="deleteRequestsDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="padding-right: 12px; padding-left: 12px;">
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
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#boardSettingsModal">
                        <i class="mdi mdi-cog me-1"></i>
                    </button>
                @endif
                <button class="btn btn-outline-primary btn-sm" type="button" id="btnToggleFullscreen">
                    <i class="mdi mdi-fullscreen me-1" id="fsIcon"></i>
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
                            <select class="form-select select2-create" id="createTaskAssignee" data-placeholder="Choose assignee">
                                <option value="">Unassigned</option>
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
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-quick-member" id="btnQuickMember">
                            <i class="mdi mdi-account-outline me-1"></i>+ Members
                        </button>
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
                                                 <span class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Client / Perusahaan</span>
                                                 <strong id="soClientName" class="text-dark" style="font-size: 13.5px;"></strong>
                                             </div>
                                             <div class="col-sm-6">
                                                 <span class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Nomor Penawaran (Quote)</span>
                                                 <span id="soQuoteNumber" style="font-size: 13px;"></span>
                                             </div>
                                             <div class="col-sm-6">
                                                 <span class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Total Nett</span>
                                                 <span id="soQuoteNett" class="fw-bold text-primary" style="font-size: 13.5px;"></span>
                                             </div>
                                             <div class="col-sm-6">
                                                 <span class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Sales Person</span>
                                                 <span id="soSalesPerson" class="text-dark" style="font-size: 13px;"></span>
                                             </div>
                                             
                                             <!-- Invoices & Payments status -->
                                             <div class="col-sm-12 mt-2">
                                                 <span class="text-muted d-block mb-1.5" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Invoice & Status Pembayaran</span>
                                                 <div id="soInvoicesContainer" class="d-flex flex-column gap-1.5 mt-1">
                                                     <!-- Dynamically populated via JS -->
                                                 </div>
                                             </div>
 
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

                            <!-- Metadata row (Assignee, Due Date, and Priority dropdowns) -->
                            <div class="row mb-4">
                                <div class="col-sm-6">
                                    <label for="editTaskAssignee" class="form-label text-muted fw-semibold" style="font-size: 11px;">Ditugaskan Kepada</label>
                                    <div class="w-100">
                                        <select class="form-select select2-edit" id="editTaskAssignee" name="assignees[]" multiple="multiple" data-placeholder="Pilih Penerima" style="width: 100%;">
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label for="editTaskDueDate" class="form-label text-muted fw-semibold" style="font-size: 11px;">Tanggal Batas Waktu</label>
                                    <input type="text" class="form-control flatpickr" id="editTaskDueDate" placeholder="YYYY-MM-DD">
                                </div>
                                <div class="col-sm-3">
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

    @if (auth()->user()->role === 'Admin')
        <!-- Board Settings Modal -->
        <div class="modal fade" id="boardSettingsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Board Settings</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="boardSettingsForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="settingsBoardTitle" class="form-label">Board Title</label>
                                <input type="text" class="form-control" id="settingsBoardTitle" name="title" value="{{ $board->title }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="settingsBoardDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="settingsBoardDescription" name="description" rows="2">{{ $board->description }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="settingsBoardMembers" class="form-label">Manage Members</label>
                                <select class="select2 form-select" id="settingsBoardMembers" name="member_ids[]" multiple="multiple" data-placeholder="Select members">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ $board->members->contains($user->id) ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->role }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Manage Columns</label>
                                    <button type="button" class="btn btn-xs btn-outline-primary" id="settingsAddColBtn">
                                        <i class="mdi mdi-plus"></i> Add Column
                                    </button>
                                </div>
                                <div id="settingsColumnsContainer">
                                    @foreach ($board->columns as $column)
                                        <div class="input-group mb-2 settings-column-item" data-id="{{ $column->id }}">
                                            <input type="hidden" name="columns[][id]" value="{{ $column->id }}">
                                            <input type="text" class="form-control" name="columns[][title]" value="{{ $column->title }}" required>
                                            <button class="btn btn-outline-danger btn-remove-settings-column" type="button"><i class="mdi mdi-close"></i></button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3 border-top pt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0 fw-bold">Custom Label Names</label>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-xs btn-outline-primary dropdown-toggle hide-arrow" id="settingsAddLabelBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-plus"></i> Add Label
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end p-2" id="settingsAddLabelMenu" style="min-width: 150px;">
                                            <!-- Dynamically populated via JS -->
                                        </ul>
                                    </div>
                                </div>
                                <div id="settingsLabelsContainer" class="row g-2">
                                    @foreach($activeLabels as $color => $name)
                                        <div class="col-sm-6 settings-label-item" data-color="{{ $color }}">
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text bg-{{ $color }} text-white" style="width: 35px; border: 0;">&nbsp;</span>
                                                <input type="text" class="form-control" name="labels[{{ $color }}]" value="{{ $board->labels ? ($board->labels[$color] ?? '') : '' }}" placeholder="{{ $name }}">
                                                <button class="btn btn-outline-danger btn-remove-settings-label" type="button"><i class="mdi mdi-close"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @if ($board->type === 'monitoring')
                            <div class="mb-3 border-top pt-3">
                                <label for="settingsNotificationSound" class="form-label fw-bold">Notification Sound (mp3/wav/ogg)</label>
                                <input type="file" class="form-control" id="settingsNotificationSound" name="notification_sound" accept="audio/*">
                                @if ($board->notification_sound)
                                    <div class="d-flex align-items-center mt-2 gap-2">
                                        <small class="text-success mb-0">
                                            <i class="mdi mdi-check-circle-outline"></i> Sound kustom aktif: {{ basename($board->notification_sound) }}
                                        </small>
                                        <button type="button" class="btn btn-xs btn-outline-info py-0 px-2" id="btnTestSound" data-sound="{{ asset($board->notification_sound) }}">
                                            <i class="mdi mdi-volume-high me-1"></i> Test
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3 border-top pt-3">
                                <label class="form-label fw-bold">Accounting &rarr; Sales Mapping</label>
                                <p class="text-muted mb-2" style="font-size: 12px;">Atur sales yang ditangani tiap Accounting, dipakai untuk filter "{{ '-- Semua Accounting --' }}" di halaman board.</p>
                                <div id="accountingMappingContainer">
                                    @foreach ($accountingUsers as $accountingUser)
                                        <div class="mb-2">
                                            <label class="form-label" style="font-size: 12.5px;">{{ $accountingUser->name }}</label>
                                            <select class="select2 form-select accounting-mapping-select" multiple="multiple" data-accounting-id="{{ $accountingUser->id }}" data-placeholder="Pilih sales yang ditangani">
                                                @foreach ($salesUsers as $salesUser)
                                                    <option value="{{ $salesUser->id }}" {{ $accountingUser->handledSales->contains($salesUser->id) ? 'selected' : '' }}>{{ $salesUser->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="btnSaveAccountingMapping">
                                    <i class="mdi mdi-content-save-outline me-1"></i> Simpan Mapping
                                </button>
                            </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="btnSaveBoardSettings">Save Changes</button>
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
            background-color: #f5f6f8;
            border-radius: 8px;
            padding: 10px;
            cursor: default;
        }
        .kanban-board header {
            padding: 10px 5px;
            border-bottom: 2px solid #ebedf2;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
                            return {
                                id: column.id,
                                title: `
                                    <div class="d-flex justify-content-between align-items-center w-100 pe-2" style="gap: 10px;">
                                        <div class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-between flex-grow-1 py-1 px-2 bg-white" style="border-color: #eef0f4; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); pointer-events: none; max-width: calc(100% - 30px); min-width: 0;">
                                            <span class="kanban-title-board text-truncate" style="font-size: 13px; font-weight: 700; color: #4f5157; margin-right: 5px;" title="${column.title}">${column.title}</span>
                                            <span class="badge bg-primary rounded-pill" style="font-size: 10px; padding: 2.5px 6.5px; flex-shrink: 0;">${column.item ? column.item.length : 0}</span>
                                        </div>
                                        <button class="btn btn-xs btn-text-primary btn-icon rounded-circle btn-add-task-custom" data-column-id="${column.id}" style="width: 24px; height: 24px; min-width: 24px; padding: 0;" type="button">
                                            <i class="mdi mdi-plus" style="font-size: 16px;"></i>
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

                                    let footerHtml = `
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="d-flex align-items-center gap-1">
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

                                    let displayTitleHtml = `<div class="text-heading fw-semibold" style="font-size: 13px; white-space: normal; word-break: break-word;">${task.title}</div>`;
                                    const poMatch = task.title.match(/^\[(.*?)\]\s*-\s*(.*)$/);
                                    if (poMatch) {
                                        const poNum = poMatch[1];
                                        const company = poMatch[2];
                                        displayTitleHtml = `
                                            <div class="text-primary fw-bold text-truncate" style="font-size: 13.5px;" title="${poNum}">[${poNum}]</div>
                                            <div class="text-heading fw-semibold mt-1" style="font-size: 12.5px; line-height: 1.35; white-space: normal; word-break: break-word;">${company}</div>
                                        `;
                                    }

                                    let nettHtml = '';
                                    if (task.nett && task.nett > 0 && boardType === 'monitoring') {
                                        const formattedVal = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(task.nett);
                                        nettHtml = `<div class="mt-1"><span class="badge bg-label-primary" style="font-size: 10px; font-weight: 600; padding: 3px 6px;">${formattedVal}</span></div>`;
                                    }

                                    return {
                                        id: task.id,
                                        title: `
                                            <div class="kanban-item-content" data-task-id="${task.id}" data-task='${JSON.stringify(task).replace(/'/g, "&#39;")}'>
                                                ${labelsHtml}
                                                <div class="mb-1" style="line-height: 1.4;">${displayTitleHtml}</div>
                                                ${nettHtml}
                                                <small class="text-muted d-block text-truncate mt-1" style="max-width: 200px;">${task.description ? task.description : ''}</small>
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
                        
                        // Apply custom border colors to cards based on active labels
                        $('.kanban-item-content').each(function() {
                            const taskData = $(this).data('task');
                            if (taskData && taskData.labels && taskData.labels.length > 0) {
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

            // Open Create Task Modal
            function openCreateTaskModal(boardId) {
                $('#createTaskColumnId').val(boardId);
                $('#createTaskForm')[0].reset();
                $('#createTaskAssignee').val('').trigger('change');
                
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
                                    html += `<option value="${po.id}" data-no-po="${po.no_po}" data-company="${po.company}" data-sales="${po.sales}">[${po.no_po}] - ${po.company} (Sales: ${po.sales})</option>`;
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
                const assignee = $('#createTaskAssignee').val();
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
                        assigned_to: assignee,
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
                                $('#soPoNumber').text(response.so_details.no_po);
                                $('#soClientName').text(response.so_details.company);
                                
                                // Render Quotation as a Link
                                if (response.so_details.quote_no !== 'N/A') {
                                    $('#soQuoteNumber').html(`<a href="${response.so_details.quote_link}" target="_blank" class="fw-semibold text-primary"><i class="mdi mdi-open-in-new me-1" style="font-size:12px;"></i>${response.so_details.quote_no}</a>`);
                                } else {
                                    $('#soQuoteNumber').text('N/A');
                                }
                                
                                $('#soQuoteNett').text('Rp ' + response.so_details.quote_nett);
                                $('#soSalesPerson').text(response.so_details.sales_name);

                                // Render Invoices and associate their payments sequentially
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

                                $('#soDetailsContainer').show();
                            } else {
                                $('#soDetailsContainer').hide();
                            }
                            
                            isProgrammaticChange = true;
                            // Set fields if changed
                            const titleRaw = currentTaskData.title || '';
                            const titleMatch = titleRaw.match(/^\[(.*?)\]\s*-\s*(.*)$/);
                            if (titleMatch) {
                                $('#taskDetailsModalLabel').html(`<span class="badge bg-label-primary px-3 py-2 me-2" style="font-size: 13.5px; font-weight: 600;"><i class="mdi mdi-receipt-text-outline me-1"></i>${titleMatch[1]}</span> <span class="text-dark fw-bold" style="font-size: 16px;">${titleMatch[2]}</span>`);
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
                        }
                    },
                    error: function() {
                        // Silent fail on background polling
                    }
                });
            }

            $(document).on('click', '#btnCreateBastFromCard', function() {
                const taskId = $('#editTaskId').val();
                const prefill = currentBastPrefill || {};
                window.openBastModal({
                    idKanbanTask: taskId,
                    idQuotation: prefill.id_quotation || '',
                    entity: prefill.entity || 'Reftech',
                    customerName: prefill.customer_name || '',
                    workTitle: prefill.work_title || '',
                    poNumber: prefill.po_number || '',
                });
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

            // Metadata changes (Assignee, Due Date, and Priority auto-sync)
            // Metadata changes (Assignee, Due Date, and Priority auto-sync)
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
                        assigned_to: assignee,
                        due_date: dueDate,
                        column_id: columnId,
                        service_report_id: $('#editTaskServiceReport').val(),
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
                if (!taskId) return;
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
                        }
                    });
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
            });

            // Handle Add Label selection from dropdown
            $(document).on('click', '.btn-add-settings-label-option', function(e) {
                e.preventDefault();
                const color = $(this).data('color');
                const colorDetails = allColors[color];

                const html = `
                    <div class="col-sm-6 settings-label-item" data-color="${color}">
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text bg-${color} text-white" style="width: 35px; border: 0;">&nbsp;</span>
                            <input type="text" class="form-control" name="labels[${color}]" value="" placeholder="${colorDetails.name}">
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

            // Admin: Board Settings Column management
            $('#settingsAddColBtn').click(function() {
                var html = `
                    <div class="input-group mb-2 settings-column-item">
                        <input type="hidden" name="columns[][id]" value="">
                        <input type="text" class="form-control" name="columns[][title]" placeholder="New Column" required>
                        <button class="btn btn-outline-danger btn-remove-settings-column" type="button"><i class="mdi mdi-close"></i></button>
                    </div>
                `;
                $('#settingsColumnsContainer').append(html);
            });

            $(document).on('click', '.btn-remove-settings-column', function() {
                if ($('.settings-column-item').length > 1) {
                    $(this).closest('.settings-column-item').remove();
                } else {
                    alert('Minimal harus memiliki 1 kolom!');
                }
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

            // Client-side Card Search + Accounting filter
            function applyKanbanFilters() {
                const query = $('#kanbanSearchInput').val().toLowerCase().trim();
                const accountingFilter = $('#kanbanAccountingFilter').val();
                const allowedSalesIds = accountingFilter ? (accountingSalesMap[accountingFilter] || []) : null;

                $('.kanban-item').each(function() {
                    const titleText = $(this).find('.text-heading, .text-primary').text().toLowerCase();
                    const descText = $(this).find('.text-muted').text().toLowerCase();
                    const taskData = $(this).find('.kanban-item-content').data('task') || {};

                    const matchesSearch = !query || titleText.includes(query) || descText.includes(query);
                    const matchesAccounting = !allowedSalesIds || allowedSalesIds.includes(String(taskData.id_sales));

                    if (matchesSearch && matchesAccounting) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            $(document).on('keyup', '#kanbanSearchInput', applyKanbanFilters);
            $(document).on('change', '#kanbanAccountingFilter', applyKanbanFilters);

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
