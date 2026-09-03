@extends('layouts.sales.app')
@section('title', isset($report) ? 'Edit Daily Project Report' : 'Create Daily Project Report')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Service Department / Project Reports /</span>
            {{ isset($report) ? 'Edit Report' : 'Create Daily Report' }}
        </h4>
        <a href="{{ route('service-reports.index', ['tab' => 'project']) }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ isset($report) ? route('project-reports.update', $report->id) : route('project-reports.store') }}"
        method="POST" enctype="multipart/form-data" id="projectReportForm">
        @csrf
        @if (isset($report))
            @method('PUT')
        @endif

        {{-- CARD 1: INFORMASI PROYEK & KONTRAK --}}
        <div class="card mb-4 border-top border-primary border-3">
            <div class="card-header bg-light-primary py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="mdi mdi-file-document-outline me-2"></i>DAILY REPORT FORM / LAPORAN HARIAN
                    </h5>
                    <div>
                        <span id="draftSaveIndicator" class="badge bg-label-success me-2">
                            <i class="mdi mdi-check-circle me-1"></i> Sesi Aktif & Auto-Saved
                        </span>
                        <span class="badge bg-primary">Reftech Project Report</span>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="p-2 px-3 rounded bg-label-primary border border-primary d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 280px;">
                                <i class="mdi mdi-view-dashboard-outline fs-4 text-primary"></i>
                                <div class="flex-grow-1">
                                    <label class="form-label mb-1 fw-semibold text-primary" style="font-size: 12px;">Hubungkan ke Project Kanban Board (Opsional)</label>
                                    <select name="kanban_task_id" id="kanbanTaskSelect" class="form-select select2">
                                        <option value="">-- Pilih Project (Board: Project HVAC) --</option>
                                        @foreach ($kanbanTasks ?? [] as $kt)
                                            <option value="{{ $kt->id }}"
                                                {{ old('kanban_task_id', $report->kanban_task_id ?? ($selectedKanbanTaskId ?? '')) == $kt->id ? 'selected' : '' }}>
                                                {{ $kt->title }}{{ $kt->column ? ' [' . $kt->column->title . ']' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if (isset($report) && $report->kanbanTask)
                                <a href="{{ url('/kanban/board/' . $report->kanbanTask->board_id . '?task=' . $report->kanbanTask->id) }}" target="_blank" class="btn btn-sm btn-primary text-nowrap">
                                    <i class="mdi mdi-open-in-new me-1"></i>Buka di Kanban
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">No. Laporan</label>
                        <input type="text" name="report_number" class="form-control"
                            value="{{ old('report_number', $report->report_number ?? ($suggestedReportNo ?? '')) }}"
                            placeholder="PR-YYYYMM-001" />
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Nama Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" name="job_name" class="form-control" required
                            value="{{ old('job_name', $report->job_name ?? ($prefilledJobName ?? '')) }}"
                            placeholder="Contoh: Overhaul & Installation Compressor..." />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Client / Customer</label>
                        <select name="client_id" class="form-select select2">
                            <option value="">-- Pilih Client (Opsional) --</option>
                            @foreach ($clients as $c)
                                <option value="{{ $c->id }}"
                                    {{ old('client_id', $report->client_id ?? ($prefilledClientId ?? '')) == $c->id ? 'selected' : '' }}>
                                    {{ $c->company }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">No. Surat Perjanjian / PO</label>
                        <input type="text" name="contract_no" class="form-control"
                            value="{{ old('contract_no', $report->contract_no ?? ($prefilledContractNo ?? '')) }}"
                            placeholder="No. Kontrak / PO / SPK" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal Laporan <span class="text-danger">*</span></label>
                        <input type="date" name="report_date" class="form-control" required
                            value="{{ old('report_date', isset($report) ? $report->report_date->format('Y-m-d') : ($today ? $today->format('Y-m-d') : date('Y-m-d'))) }}" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kontraktor Pelaksana</label>
                        <input type="text" name="contractor_name" class="form-control"
                            value="{{ old('contractor_name', $report->contractor_name ?? 'PT. REFTECH JAYA OPTIMA') }}" />
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Hari Ke</label>
                        <input type="text" name="day_number" class="form-control"
                            value="{{ old('day_number', $report->day_number ?? ($suggestedDayNumber ?? '1')) }}" placeholder="Contoh: 1, 2, 3..." />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Hari</label>
                        <input type="text" name="day_name" class="form-control"
                            value="{{ old('day_name', $report->day_name ?? ($defaultDayName ?? 'Senin')) }}"
                            placeholder="Senin / Selasa / ..." />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Sisa Waktu / Durasi</label>
                        <input type="text" name="days_remaining" class="form-control"
                            value="{{ old('days_remaining', $report->days_remaining ?? '') }}"
                            placeholder="Contoh: 5 Hari / 2 Minggu" />
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 2: SECTION A. PEKERJAAN --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-label-dark py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <span class="badge bg-dark me-2">A</span> PEKERJAAN
                </h6>
                <button type="button" class="btn btn-sm btn-primary" id="btnAddTask">
                    <i class="mdi mdi-plus me-1"></i> Tambah Pekerjaan
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0 align-middle" id="tableTasks">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">NO</th>
                                <th>JENIS PEKERJAAN <span class="text-danger">*</span></th>
                                <th style="width: 250px;">LOKASI PEKERJAAN</th>
                                <th style="width: 250px;">KETERANGAN</th>
                                <th style="width: 50px;" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="taskRowsContainer">
                            @if (isset($report) && $report->tasks->count() > 0)
                                @foreach ($report->tasks as $idx => $task)
                                    <tr class="task-row">
                                        <td class="text-center row-number fw-bold">{{ $idx + 1 }}</td>
                                        <td>
                                            <input type="text" name="tasks[{{ $idx }}][task_name]"
                                                class="form-control form-control-sm" required
                                                value="{{ $task->task_name }}" placeholder="Uraian jenis pekerjaan..." />
                                        </td>
                                        <td>
                                            <input type="text" name="tasks[{{ $idx }}][location]"
                                                class="form-control form-control-sm" value="{{ $task->location }}"
                                                placeholder="Area / Ruang Mesin..." />
                                        </td>
                                        <td>
                                            <input type="text" name="tasks[{{ $idx }}][notes]"
                                                class="form-control form-control-sm" value="{{ $task->notes }}"
                                                placeholder="Keterangan progres..." />
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                                <i class="mdi mdi-delete-outline"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                @for ($i = 0; $i < 3; $i++)
                                    <tr class="task-row">
                                        <td class="text-center row-number fw-bold">{{ $i + 1 }}</td>
                                        <td>
                                            <input type="text" name="tasks[{{ $i }}][task_name]"
                                                class="form-control form-control-sm" {{ $i == 0 ? 'required' : '' }}
                                                placeholder="Uraian jenis pekerjaan..." />
                                        </td>
                                        <td>
                                            <input type="text" name="tasks[{{ $i }}][location]"
                                                class="form-control form-control-sm" placeholder="Area / Ruang Mesin..." />
                                        </td>
                                        <td>
                                            <input type="text" name="tasks[{{ $i }}][notes]"
                                                class="form-control form-control-sm" placeholder="Keterangan progres..." />
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                                <i class="mdi mdi-delete-outline"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endfor
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ROW: SECTION B (BAHAN/MATERIAL) & SECTION C (PERALATAN) --}}
        <div class="row g-4 mb-4">
            {{-- SECTION B. BAHAN / MATERIAL --}}
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-label-dark py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">
                            <span class="badge bg-dark me-2">B</span> BAHAN / MATERIAL
                        </h6>
                        <button type="button" class="btn btn-sm btn-primary" id="btnAddMaterial">
                            <i class="mdi mdi-plus me-1"></i> Tambah
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0 align-middle" id="tableMaterials">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 45px;" class="text-center">NO</th>
                                        <th>JENIS BAHAN YANG DIGUNAKAN</th>
                                        <th style="width: 45px;" class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="materialRowsContainer">
                                    @if (isset($report) && $report->materials->count() > 0)
                                        @foreach ($report->materials as $idx => $mat)
                                            <tr class="material-row">
                                                <td class="text-center row-number fw-bold">{{ $idx + 1 }}</td>
                                                <td>
                                                    <input type="text"
                                                        name="materials[{{ $idx }}][material_name]"
                                                        class="form-control form-control-sm"
                                                        value="{{ $mat->material_name }}"
                                                        placeholder="Nama bahan/material..." />
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                                        <i class="mdi mdi-delete-outline"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @for ($i = 0; $i < 3; $i++)
                                            <tr class="material-row">
                                                <td class="text-center row-number fw-bold">{{ $i + 1 }}</td>
                                                <td>
                                                    <input type="text"
                                                        name="materials[{{ $i }}][material_name]"
                                                        class="form-control form-control-sm"
                                                        placeholder="Nama bahan/material..." />
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                                        <i class="mdi mdi-delete-outline"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endfor
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION C. PERALATAN YANG DIGUNAKAN --}}
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-label-dark py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">
                            <span class="badge bg-dark me-2">C</span> PERALATAN YANG DIGUNAKAN
                        </h6>
                        <button type="button" class="btn btn-sm btn-primary" id="btnAddEquipment">
                            <i class="mdi mdi-plus me-1"></i> Tambah
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0 align-middle" id="tableEquipments">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 45px;" class="text-center">NO</th>
                                        <th>NAMA PERALATAN</th>
                                        <th style="width: 80px;">JUMLAH</th>
                                        <th style="width: 90px;">SATUAN</th>
                                        <th style="width: 45px;" class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="equipmentRowsContainer">
                                    @if (isset($report) && $report->equipments->count() > 0)
                                        @foreach ($report->equipments as $idx => $eq)
                                            <tr class="equipment-row">
                                                <td class="text-center row-number fw-bold">{{ $idx + 1 }}</td>
                                                <td>
                                                    <input type="text"
                                                        name="equipments[{{ $idx }}][equipment_name]"
                                                        class="form-control form-control-sm"
                                                        value="{{ $eq->equipment_name }}"
                                                        placeholder="Nama alat..." />
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        name="equipments[{{ $idx }}][qty]"
                                                        class="form-control form-control-sm"
                                                        value="{{ $eq->qty }}" placeholder="1" />
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        name="equipments[{{ $idx }}][unit]"
                                                        class="form-control form-control-sm"
                                                        value="{{ $eq->unit }}" placeholder="Unit/Set" />
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                                        <i class="mdi mdi-delete-outline"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @for ($i = 0; $i < 3; $i++)
                                            <tr class="equipment-row">
                                                <td class="text-center row-number fw-bold">{{ $i + 1 }}</td>
                                                <td>
                                                    <input type="text"
                                                        name="equipments[{{ $i }}][equipment_name]"
                                                        class="form-control form-control-sm" placeholder="Nama alat..." />
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        name="equipments[{{ $i }}][qty]"
                                                        class="form-control form-control-sm" placeholder="1" />
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        name="equipments[{{ $i }}][unit]"
                                                        class="form-control form-control-sm" placeholder="Unit/Set" />
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                                        <i class="mdi mdi-delete-outline"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endfor
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW: SECTION D (TENAGA KERJA) & SECTION E (CUACA) --}}
        <div class="row g-4 mb-4">
            {{-- SECTION D. TENAGA KERJA --}}
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-label-dark py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">
                            <span class="badge bg-dark me-2">D</span> TENAGA KERJA
                        </h6>
                        <button type="button" class="btn btn-sm btn-primary" id="btnAddManpower">
                            <i class="mdi mdi-plus me-1"></i> Tambah
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0 align-middle" id="tableManpowers">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 45px;" class="text-center">NO</th>
                                        <th>JABATAN</th>
                                        <th style="width: 140px;">JUMLAH ORANG</th>
                                        <th style="width: 45px;" class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="manpowerRowsContainer">
                                    @if (isset($report) && $report->manpowers->count() > 0)
                                        @foreach ($report->manpowers as $idx => $mp)
                                            <tr class="manpower-row">
                                                <td class="text-center row-number fw-bold">{{ $idx + 1 }}</td>
                                                <td>
                                                    <input type="text"
                                                        name="manpowers[{{ $idx }}][position]"
                                                        class="form-control form-control-sm"
                                                        value="{{ $mp->position }}"
                                                        placeholder="Site Manager / Teknisi / ..." />
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        name="manpowers[{{ $idx }}][manpower_count]"
                                                        class="form-control form-control-sm"
                                                        value="{{ $mp->manpower_count }}" placeholder="Org" />
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                                        <i class="mdi mdi-delete-outline"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @php
                                            $defaultPositions = ['Site Manager', 'Supervisor', 'Teknisi'];
                                        @endphp
                                        @foreach ($defaultPositions as $i => $pos)
                                            <tr class="manpower-row">
                                                <td class="text-center row-number fw-bold">{{ $i + 1 }}</td>
                                                <td>
                                                    <input type="text"
                                                        name="manpowers[{{ $i }}][position]"
                                                        class="form-control form-control-sm" value="{{ $pos }}"
                                                        placeholder="Jabatan..." />
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        name="manpowers[{{ $i }}][manpower_count]"
                                                        class="form-control form-control-sm" value="1"
                                                        placeholder="1" />
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                                        <i class="mdi mdi-delete-outline"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION E. CUACA --}}
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-label-dark py-2">
                        <h6 class="mb-0 fw-bold text-dark">
                            <span class="badge bg-dark me-2">E</span> CUACA (LAPORAN CUACA)
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="weather_cerah" id="w_cerah"
                                        value="1"
                                        {{ old('weather_cerah', $report->weather_cerah ?? 1) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="w_cerah">
                                        <i class="mdi mdi-weather-sunny text-warning me-1"></i> CERAH
                                    </label>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">JAM :</span>
                                    <input type="text" name="weather_cerah_time" class="form-control"
                                        value="{{ old('weather_cerah_time', $report->weather_cerah_time ?? '08:00 s/d 17:00') }}"
                                        placeholder="08:00 s/d 17:00" />
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="weather_hujan" id="w_hujan"
                                        value="1"
                                        {{ old('weather_hujan', $report->weather_hujan ?? 0) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="w_hujan">
                                        <i class="mdi mdi-weather-rainy text-primary me-1"></i> HUJAN
                                    </label>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">JAM :</span>
                                    <input type="text" name="weather_hujan_time" class="form-control"
                                        value="{{ old('weather_hujan_time', $report->weather_hujan_time ?? '') }}"
                                        placeholder="Contoh: 14:30 - 17:00" />
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="weather_mendung" id="w_mendung"
                                        value="1"
                                        {{ old('weather_mendung', $report->weather_mendung ?? 0) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="w_mendung">
                                        <i class="mdi mdi-weather-cloudy text-secondary me-1"></i> MENDUNG
                                    </label>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">JAM :</span>
                                    <input type="text" name="weather_mendung_time" class="form-control"
                                        value="{{ old('weather_mendung_time', $report->weather_mendung_time ?? '') }}"
                                        placeholder="Contoh: 14:00 - 17:00" />
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center">
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="weather_dll" id="w_dll"
                                        value="1"
                                        {{ old('weather_dll', $report->weather_dll ?? 0) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="w_dll">
                                        <i class="mdi mdi-dots-horizontal me-1"></i> DLL
                                    </label>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">JAM :</span>
                                    <input type="text" name="weather_dll_time" class="form-control"
                                        value="{{ old('weather_dll_time', $report->weather_dll_time ?? '') }}"
                                        placeholder="Jam operasional..." />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD: EVALUASI, PLANNING, & KENDALA --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-label-secondary py-2">
                <h6 class="mb-0 fw-bold text-dark"><i class="mdi mdi-notebook-outline me-1"></i> EVALUASI & RENCANA KERJA</h6>
            </div>
            <div class="card-body pt-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary">PLANNING HARI INI:</label>
                        <textarea name="planning_today" class="form-control" rows="3"
                            placeholder="Tulis rencana kerja hari ini...">{{ old('planning_today', $report->planning_today ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-success">PENCAPAIAN HARI INI:</label>
                        <textarea name="achievement_today" class="form-control" rows="3"
                            placeholder="Tulis capaian / hasil kerja hari ini...">{{ old('achievement_today', $report->achievement_today ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-danger">KENDALA :</label>
                        <textarea name="issues_constraints" class="form-control" rows="3"
                            placeholder="Tulis kendala / hambatan di lapangan (jika ada)...">{{ old('issues_constraints', $report->issues_constraints ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-info">RENCANA PEKERJAAN HARI BERIKUTNYA:</label>
                        <textarea name="next_plan" class="form-control" rows="3"
                            placeholder="Tulis rencana pekerjaan untuk besok/hari berikutnya...">{{ old('next_plan', $report->next_plan ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD: PENGESAHAN / TANDA TANGAN (PEMBERI TUGAS & KONTRAKTOR PELAKSANA) --}}
        <div class="card mb-4 shadow-sm border-top border-info border-3">
            <div class="card-header bg-light-info py-2">
                <h6 class="mb-0 fw-bold text-dark"><i class="mdi mdi-shield-check-outline me-1"></i> PENGESAHAN & TANDA TANGAN</h6>
            </div>
            <div class="card-body pt-3">
                <div class="row g-4">
                    {{-- Pemberi Tugas --}}
                    <div class="col-md-6 border-end">
                        <div class="p-3 bg-light rounded text-center">
                            <h6 class="fw-bold mb-3">PEMBERI TUGAS (CLIENT / PENGAWAS)</h6>
                            <div class="mb-3">
                                <label class="form-label text-start d-block fw-semibold">Nama PIC / Penanggung Jawab:</label>
                                <input type="text" name="client_pic_name" class="form-control form-control-sm"
                                    value="{{ old('client_pic_name', $report->client_pic_name ?? '') }}"
                                    placeholder="Nama Lengkap Pemberi Tugas..." />
                            </div>
                            <div class="mb-2">
                                <label class="form-label text-start d-block fw-semibold">Upload Foto Tanda Tangan / Paraf:</label>
                                <input type="file" name="client_sign" class="form-control form-control-sm" accept="image/*" />
                            </div>
                            @if (isset($report) && $report->client_sign)
                                <div class="mt-2 text-center">
                                    <small class="text-muted d-block mb-1">Tanda Tangan Saat Ini:</small>
                                    <img src="{{ Storage::disk('public')->url($report->client_sign) }}" alt="Sign Client"
                                        style="max-height: 80px;" class="border rounded p-1 bg-white" />
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Kontraktor Pelaksana --}}
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded text-center">
                            <h6 class="fw-bold mb-1">KONTRAKTOR PELAKSANA</h6>
                            <div class="text-primary fw-bold small mb-3">PT. REFTECH JAYA OPTIMA</div>
                            <div class="mb-3">
                                <label class="form-label text-start d-block fw-semibold">Nama PIC / Pelaksana:</label>
                                <input type="text" name="contractor_pic_name" class="form-control form-control-sm"
                                    value="{{ old('contractor_pic_name', $report->contractor_pic_name ?? (optional(Auth::user())->name ?? '')) }}"
                                    placeholder="Nama PIC Reftech..." />
                            </div>
                            <div class="mb-2">
                                <label class="form-label text-start d-block fw-semibold">Upload Foto Tanda Tangan / Paraf:</label>
                                <input type="file" name="contractor_sign" class="form-control form-control-sm" accept="image/*" />
                            </div>
                            @if (isset($report) && $report->contractor_sign)
                                <div class="mt-2 text-center">
                                    <small class="text-muted d-block mb-1">Tanda Tangan Saat Ini:</small>
                                    <img src="{{ Storage::disk('public')->url($report->contractor_sign) }}" alt="Sign Contractor"
                                        style="max-height: 80px;" class="border rounded p-1 bg-white" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD: FOTO DOKUMENTASI (POSISI TEPAT SETELAH CARD KONTRAKTOR PELAKSANA) --}}
        <div class="card mb-4 shadow-sm border-top border-warning border-3">
            <div class="card-header bg-label-warning py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="mdi mdi-camera me-1"></i> DOKUMENTASI FOTO PEKERJAAN LAPANGAN
                </h6>
                <button type="button" class="btn btn-sm btn-warning" id="btnAddPhotoInput">
                    <i class="mdi mdi-plus me-1"></i> Tambah Foto
                </button>
            </div>
            <div class="card-body pt-3">
                <p class="text-muted small mb-3">
                    <i class="mdi mdi-information-outline me-1"></i> Foto-foto dokumentasi ini akan ditampilkan pada lampiran di bawah kotak tanda tangan / halaman berikutnya pada cetak PDF.
                </p>

                {{-- Existing Photos (If Editing) --}}
                @if (isset($report) && $report->photos->count() > 0)
                    <div class="mb-4">
                        <label class="form-label fw-bold">Foto Dokumentasi Tersimpan:</label>
                        <div class="row g-3">
                            @foreach ($report->photos as $photo)
                                <div class="col-md-3 col-sm-6" id="photo-card-{{ $photo->id }}">
                                    <div class="card border h-100 shadow-none">
                                        <div class="position-relative">
                                            <img src="{{ $photo->url }}" class="card-img-top"
                                                style="height: 160px; object-fit: cover;" alt="Foto Dokumentasi" />
                                            <button type="button"
                                                class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 btn-delete-photo-ajax"
                                                data-id="{{ $photo->id }}" title="Hapus Foto">
                                                <i class="mdi mdi-delete-outline"></i>
                                            </button>
                                        </div>
                                        <div class="card-body p-2">
                                            <input type="text" class="form-control form-control-sm photo-caption-input"
                                                value="{{ $photo->caption }}" placeholder="Keterangan foto..."
                                                data-id="{{ $photo->id }}" />
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <hr>
                @endif

                {{-- Upload New Photos Container --}}
                <div id="photoInputsContainer">
                    <div class="row g-3 photo-input-row mb-3 align-items-center">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">Pilih File Foto:</label>
                            <input type="file" name="photos[]" class="form-control form-control-sm" accept="image/*" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Keterangan / Caption Foto:</label>
                            <input type="text" name="photo_captions[]" class="form-control form-control-sm"
                                placeholder="Contoh: Kondisi sebelum perbaikan / Pemasangan valve..." />
                        </div>
                        <div class="col-md-1 text-center pt-3">
                            <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-photo-row">
                                <i class="mdi mdi-delete-outline"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <a href="{{ route('service-reports.index', ['tab' => 'project']) }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-close me-1"></i> Batal
                </a>
                <div>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="mdi mdi-content-save me-1"></i> {{ isset($report) ? 'Simpan Perubahan' : 'Simpan Report' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('script')
    <script>
        $(function() {
            $('.select2').select2({
                placeholder: "-- Pilih --",
                allowClear: true,
                width: '100%'
            });

            // Re-index row numbers
            function updateRowNumbers(containerSelector) {
                $(containerSelector + ' .row-number').each(function(idx) {
                    $(this).text(idx + 1);
                });
            }

            // Remove Row Handler
            $(document).on('click', '.btn-remove-row', function() {
                var tbody = $(this).closest('tbody');
                if (tbody.find('tr').length > 1) {
                    $(this).closest('tr').remove();
                    updateRowNumbers('#' + tbody.attr('id'));
                } else {
                    $(this).closest('tr').find('input').val('');
                }
            });

            // Add Task Row
            $('#btnAddTask').on('click', function() {
                var rowCount = $('#taskRowsContainer tr').length;
                var html = `
                    <tr class="task-row">
                        <td class="text-center row-number fw-bold">${rowCount + 1}</td>
                        <td>
                            <input type="text" name="tasks[${rowCount}][task_name]" class="form-control form-control-sm" placeholder="Uraian jenis pekerjaan..." />
                        </td>
                        <td>
                            <input type="text" name="tasks[${rowCount}][location]" class="form-control form-control-sm" placeholder="Area / Ruang Mesin..." />
                        </td>
                        <td>
                            <input type="text" name="tasks[${rowCount}][notes]" class="form-control form-control-sm" placeholder="Keterangan progres..." />
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                <i class="mdi mdi-delete-outline"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#taskRowsContainer').append(html);
            });

            // Add Material Row
            $('#btnAddMaterial').on('click', function() {
                var rowCount = $('#materialRowsContainer tr').length;
                var html = `
                    <tr class="material-row">
                        <td class="text-center row-number fw-bold">${rowCount + 1}</td>
                        <td>
                            <input type="text" name="materials[${rowCount}][material_name]" class="form-control form-control-sm" placeholder="Nama bahan/material..." />
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                <i class="mdi mdi-delete-outline"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#materialRowsContainer').append(html);
            });

            // Add Equipment Row
            $('#btnAddEquipment').on('click', function() {
                var rowCount = $('#equipmentRowsContainer tr').length;
                var html = `
                    <tr class="equipment-row">
                        <td class="text-center row-number fw-bold">${rowCount + 1}</td>
                        <td>
                            <input type="text" name="equipments[${rowCount}][equipment_name]" class="form-control form-control-sm" placeholder="Nama alat..." />
                        </td>
                        <td>
                            <input type="text" name="equipments[${rowCount}][qty]" class="form-control form-control-sm" placeholder="1" />
                        </td>
                        <td>
                            <input type="text" name="equipments[${rowCount}][unit]" class="form-control form-control-sm" placeholder="Unit/Set" />
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                <i class="mdi mdi-delete-outline"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#equipmentRowsContainer').append(html);
            });

            // Add Manpower Row
            $('#btnAddManpower').on('click', function() {
                var rowCount = $('#manpowerRowsContainer tr').length;
                var html = `
                    <tr class="manpower-row">
                        <td class="text-center row-number fw-bold">${rowCount + 1}</td>
                        <td>
                            <input type="text" name="manpowers[${rowCount}][position]" class="form-control form-control-sm" placeholder="Jabatan..." />
                        </td>
                        <td>
                            <input type="text" name="manpowers[${rowCount}][manpower_count]" class="form-control form-control-sm" placeholder="1" />
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                <i class="mdi mdi-delete-outline"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#manpowerRowsContainer').append(html);
            });

            // Add Photo Input Row
            $('#btnAddPhotoInput').on('click', function() {
                var html = `
                    <div class="row g-3 photo-input-row mb-3 align-items-center">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">Pilih File Foto:</label>
                            <input type="file" name="photos[]" class="form-control form-control-sm" accept="image/*" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Keterangan / Caption Foto:</label>
                            <input type="text" name="photo_captions[]" class="form-control form-control-sm" placeholder="Contoh: Kondisi sesudah perbaikan..." />
                        </div>
                        <div class="col-md-1 text-center pt-3">
                            <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-photo-row">
                                <i class="mdi mdi-delete-outline"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#photoInputsContainer').append(html);
            });

            $(document).on('click', '.btn-remove-photo-row', function() {
                $(this).closest('.photo-input-row').remove();
            });

            // Delete Existing Photo (AJAX)
            $(document).on('click', '.btn-delete-photo-ajax', function() {
                var photoId = $(this).data('id');
                var card = $('#photo-card-' + photoId);

                Swal.fire({
                    title: 'Hapus foto ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary me-2',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(res) {
                    if (res.value) {
                        $.ajax({
                            url: '/project-reports/photo/' + photoId,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function() {
                                card.fadeOut(300, function() { $(this).remove(); });
                            }
                        });
                    }
                });
            });

            // Update photo caption on change
            $(document).on('change', '.photo-caption-input', function() {
                var photoId = $(this).data('id');
                var caption = $(this).val();
                $.ajax({
                    url: '/project-reports/photo/' + photoId,
                    type: 'PATCH',
                    data: { caption: caption },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            });

            // =========================================================================
            // 1. SESSION HEARTBEAT (KEEPS SESSION & CSRF ALIVE INDEFINITELY)
            // =========================================================================
            setInterval(function() {
                fetch('/chat/unread-count', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function() {
                    $('#draftSaveIndicator').html('<i class="mdi mdi-check-circle me-1"></i> Sesi Aktif & Auto-Saved');
                })
                .catch(function() {
                    console.log('Keep-alive heartbeat failed');
                });
            }, 120000); // 2 menit sekali

            // =========================================================================
            // 2. LOCALSTORAGE DRAFT AUTO-SAVE & AUTO-RECOVERY (FAIL-SAFE)
            // =========================================================================
            var isEditMode = {{ isset($report) ? 'true' : 'false' }};
            var DRAFT_KEY = isEditMode ? 'reftech_proj_report_draft_edit_{{ $report->id ?? 0 }}' : 'reftech_proj_report_draft_create';
            var autoSaveTimer = null;

            function collectFormData() {
                var data = {
                    report_number: $('input[name="report_number"]').val(),
                    job_name: $('input[name="job_name"]').val(),
                    client_id: $('select[name="client_id"]').val(),
                    contract_no: $('input[name="contract_no"]').val(),
                    report_date: $('input[name="report_date"]').val(),
                    contractor_name: $('input[name="contractor_name"]').val(),
                    day_number: $('input[name="day_number"]').val(),
                    day_name: $('input[name="day_name"]').val(),
                    days_remaining: $('input[name="days_remaining"]').val(),
                    
                    weather_cerah: $('#w_cerah').is(':checked'),
                    weather_cerah_time: $('input[name="weather_cerah_time"]').val(),
                    weather_hujan: $('#w_hujan').is(':checked'),
                    weather_hujan_time: $('input[name="weather_hujan_time"]').val(),
                    weather_mendung: $('#w_mendung').is(':checked'),
                    weather_mendung_time: $('input[name="weather_mendung_time"]').val(),
                    weather_dll: $('#w_dll').is(':checked'),
                    weather_dll_time: $('input[name="weather_dll_time"]').val(),

                    planning_today: $('textarea[name="planning_today"]').val(),
                    achievement_today: $('textarea[name="achievement_today"]').val(),
                    issues_constraints: $('textarea[name="issues_constraints"]').val(),
                    next_plan: $('textarea[name="next_plan"]').val(),

                    client_pic_name: $('input[name="client_pic_name"]').val(),
                    contractor_pic_name: $('input[name="contractor_pic_name"]').val(),

                    tasks: [],
                    materials: [],
                    equipments: [],
                    manpowers: []
                };

                $('#taskRowsContainer tr').each(function() {
                    var tName = $(this).find('input[name*="[task_name]"]').val();
                    var tLoc = $(this).find('input[name*="[location]"]').val();
                    var tNotes = $(this).find('input[name*="[notes]"]').val();
                    if (tName || tLoc || tNotes) {
                        data.tasks.push({ task_name: tName, location: tLoc, notes: tNotes });
                    }
                });

                $('#materialRowsContainer tr').each(function() {
                    var mName = $(this).find('input[name*="[material_name]"]').val();
                    if (mName) {
                        data.materials.push({ material_name: mName });
                    }
                });

                $('#equipmentRowsContainer tr').each(function() {
                    var eName = $(this).find('input[name*="[equipment_name]"]').val();
                    var eQty = $(this).find('input[name*="[qty]"]').val();
                    var eUnit = $(this).find('input[name*="[unit]"]').val();
                    if (eName || eQty || eUnit) {
                        data.equipments.push({ equipment_name: eName, qty: eQty, unit: eUnit });
                    }
                });

                $('#manpowerRowsContainer tr').each(function() {
                    var mpPos = $(this).find('input[name*="[position]"]').val();
                    var mpCount = $(this).find('input[name*="[manpower_count]"]').val();
                    if (mpPos || mpCount) {
                        data.manpowers.push({ position: mpPos, manpower_count: mpCount });
                    }
                });

                return data;
            }

            function triggerAutoSave() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(function() {
                    var data = collectFormData();
                    try {
                        localStorage.setItem(DRAFT_KEY, JSON.stringify(data));
                        $('#draftSaveIndicator').html('<i class="mdi mdi-check-circle me-1"></i> Sesi Aktif & Draft Disimpan (' + moment().format('HH:mm:ss') + ')');
                    } catch (e) {}
                }, 1000);
            }

            // Listen on inputs
            $(document).on('input change', '#projectReportForm input, #projectReportForm textarea, #projectReportForm select', function() {
                triggerAutoSave();
            });

            // Form Submit - Clear draft
            $('#projectReportForm').on('submit', function() {
                try {
                    localStorage.removeItem(DRAFT_KEY);
                } catch (e) {}
            });

            // Check if draft exists on create mode
            if (!isEditMode) {
                var savedDraft = localStorage.getItem(DRAFT_KEY);
                if (savedDraft) {
                    try {
                        var parsed = JSON.parse(savedDraft);
                        if (parsed && (parsed.job_name || (parsed.tasks && parsed.tasks.length > 0))) {
                            Swal.fire({
                                title: 'Draf Sebelumnya Ditemukan!',
                                text: 'Ada draf laporan pekerjaan "' + (parsed.job_name || 'Tanpa Judul') + '" yang belum tersimpan. Ingin memulihkan data tersebut?',
                                icon: 'info',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Pulihkan Draf',
                                cancelButtonText: 'Mulai Baru (Hapus Draf)',
                                customClass: {
                                    confirmButton: 'btn btn-primary me-2',
                                    cancelButton: 'btn btn-label-secondary'
                                },
                                buttonsStyling: false
                            }).then(function(res) {
                                if (res.value) {
                                    restoreDraftData(parsed);
                                } else if (res.dismiss === Swal.DismissReason.cancel) {
                                    localStorage.removeItem(DRAFT_KEY);
                                }
                            });
                        }
                    } catch (e) {}
                }
            }

            function restoreDraftData(data) {
                if (data.report_number) $('input[name="report_number"]').val(data.report_number);
                if (data.job_name) $('input[name="job_name"]').val(data.job_name);
                if (data.contract_no) $('input[name="contract_no"]').val(data.contract_no);
                if (data.report_date) $('input[name="report_date"]').val(data.report_date);
                if (data.contractor_name) $('input[name="contractor_name"]').val(data.contractor_name);
                if (data.day_number) $('input[name="day_number"]').val(data.day_number);
                if (data.day_name) $('input[name="day_name"]').val(data.day_name);
                if (data.days_remaining) $('input[name="days_remaining"]').val(data.days_remaining);
                if (data.client_id) $('select[name="client_id"]').val(data.client_id).trigger('change');

                $('#w_cerah').prop('checked', data.weather_cerah);
                $('input[name="weather_cerah_time"]').val(data.weather_cerah_time);
                $('#w_hujan').prop('checked', data.weather_hujan);
                $('input[name="weather_hujan_time"]').val(data.weather_hujan_time);
                $('#w_mendung').prop('checked', data.weather_mendung);
                $('input[name="weather_mendung_time"]').val(data.weather_mendung_time);
                $('#w_dll').prop('checked', data.weather_dll);
                $('input[name="weather_dll_time"]').val(data.weather_dll_time);

                $('textarea[name="planning_today"]').val(data.planning_today);
                $('textarea[name="achievement_today"]').val(data.achievement_today);
                $('textarea[name="issues_constraints"]').val(data.issues_constraints);
                $('textarea[name="next_plan"]').val(data.next_plan);

                if (data.client_pic_name) $('input[name="client_pic_name"]').val(data.client_pic_name);
                if (data.contractor_pic_name) $('input[name="contractor_pic_name"]').val(data.contractor_pic_name);

                // Restore Tasks
                if (data.tasks && data.tasks.length > 0) {
                    $('#taskRowsContainer').empty();
                    data.tasks.forEach(function(t, idx) {
                        var html = `
                            <tr class="task-row">
                                <td class="text-center row-number fw-bold">${idx + 1}</td>
                                <td>
                                    <input type="text" name="tasks[${idx}][task_name]" class="form-control form-control-sm" value="${t.task_name || ''}" placeholder="Uraian jenis pekerjaan..." />
                                </td>
                                <td>
                                    <input type="text" name="tasks[${idx}][location]" class="form-control form-control-sm" value="${t.location || ''}" placeholder="Area / Ruang Mesin..." />
                                </td>
                                <td>
                                    <input type="text" name="tasks[${idx}][notes]" class="form-control form-control-sm" value="${t.notes || ''}" placeholder="Keterangan progres..." />
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                        <i class="mdi mdi-delete-outline"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        $('#taskRowsContainer').append(html);
                    });
                }

                // Restore Materials
                if (data.materials && data.materials.length > 0) {
                    $('#materialRowsContainer').empty();
                    data.materials.forEach(function(m, idx) {
                        var html = `
                            <tr class="material-row">
                                <td class="text-center row-number fw-bold">${idx + 1}</td>
                                <td>
                                    <input type="text" name="materials[${idx}][material_name]" class="form-control form-control-sm" value="${m.material_name || ''}" placeholder="Nama bahan/material..." />
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                        <i class="mdi mdi-delete-outline"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        $('#materialRowsContainer').append(html);
                    });
                }

                // Restore Equipments
                if (data.equipments && data.equipments.length > 0) {
                    $('#equipmentRowsContainer').empty();
                    data.equipments.forEach(function(eq, idx) {
                        var html = `
                            <tr class="equipment-row">
                                <td class="text-center row-number fw-bold">${idx + 1}</td>
                                <td>
                                    <input type="text" name="equipments[${idx}][equipment_name]" class="form-control form-control-sm" value="${eq.equipment_name || ''}" placeholder="Nama alat..." />
                                </td>
                                <td>
                                    <input type="text" name="equipments[${idx}][qty]" class="form-control form-control-sm" value="${eq.qty || ''}" placeholder="1" />
                                </td>
                                <td>
                                    <input type="text" name="equipments[${idx}][unit]" class="form-control form-control-sm" value="${eq.unit || ''}" placeholder="Unit/Set" />
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                        <i class="mdi mdi-delete-outline"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        $('#equipmentRowsContainer').append(html);
                    });
                }

                // Restore Manpowers
                if (data.manpowers && data.manpowers.length > 0) {
                    $('#manpowerRowsContainer').empty();
                    data.manpowers.forEach(function(mp, idx) {
                        var html = `
                            <tr class="manpower-row">
                                <td class="text-center row-number fw-bold">${idx + 1}</td>
                                <td>
                                    <input type="text" name="manpowers[${idx}][position]" class="form-control form-control-sm" value="${mp.position || ''}" placeholder="Jabatan..." />
                                </td>
                                <td>
                                    <input type="text" name="manpowers[${idx}][manpower_count]" class="form-control form-control-sm" value="${mp.manpower_count || ''}" placeholder="1" />
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                                        <i class="mdi mdi-delete-outline"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        $('#manpowerRowsContainer').append(html);
                    });
                }

                $('#draftSaveIndicator').html('<i class="mdi mdi-check-circle me-1"></i> Draf Berhasil Dipulihkan!');
            }
        });
    </script>
@endpush
