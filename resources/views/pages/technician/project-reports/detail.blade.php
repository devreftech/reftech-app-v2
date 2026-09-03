@extends('layouts.sales.app')
@section('title', 'Detail Daily Project Report - ' . $report->job_name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Service Department / Project Reports /</span> Detail Report
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('service-reports.index', ['tab' => 'project']) }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('project-reports.print', $report->id) }}" target="_blank" class="btn btn-label-secondary">
                <i class="mdi mdi-printer me-1"></i> Cetak / Print PDF
            </a>
            <a href="{{ route('project-reports.edit', $report->id) }}" class="btn btn-primary">
                <i class="mdi mdi-pencil-outline me-1"></i> Edit Report
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- HEADER CARD --}}
    <div class="card mb-4 border-top border-primary border-3 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title text-primary mb-0 fw-bold">
                    <i class="mdi mdi-file-document-outline me-2"></i>DAILY REPORT FORM (LAPORAN HARIAN)
                </h5>
                <small class="text-muted">{{ $report->report_number ?: 'No. Draft' }}</small>
            </div>
            <div>
                @if ($report->status == 'approved')
                    <span class="badge bg-success px-3 py-2">Approved</span>
                @elseif($report->status == 'completed')
                    <span class="badge bg-primary px-3 py-2">Completed</span>
                @else
                    <span class="badge bg-secondary px-3 py-2">Draft</span>
                @endif
            </div>
        </div>
        <div class="card-body pt-3">
            <div class="row g-3">
                <div class="col-md-6 border-end">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 170px;" class="text-muted fw-semibold">Nama Pekerjaan</td>
                            <td class="fw-bold">: {{ $report->job_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">No. Surat Perjanjian</td>
                            <td>: {{ $report->contract_no ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Client / Perusahaan</td>
                            <td>: {{ $report->client ? $report->client->company : '-' }}</td>
                        </tr>
                        @if ($report->kanbanTask)
                        <tr>
                            <td class="text-muted fw-semibold">Project Kanban</td>
                            <td>: 
                                <a href="{{ url('/kanban/board/' . $report->kanbanTask->board_id . '?task=' . $report->kanbanTask->id) }}" target="_blank" class="badge bg-label-primary">
                                    <i class="mdi mdi-view-dashboard-outline me-1"></i>{{ $report->kanbanTask->title }} <i class="mdi mdi-open-in-new ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted fw-semibold">Kontraktor Pelaksana</td>
                            <td>: {{ $report->contractor_name }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 140px;" class="text-muted fw-semibold">Tanggal</td>
                            <td>: {{ $report->report_date ? $report->report_date->format('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Hari</td>
                            <td>: {{ $report->day_name ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Hari Ke</td>
                            <td>: <span class="badge bg-label-info">{{ $report->day_number ?: '-' }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Sisa Waktu</td>
                            <td>: {{ $report->days_remaining ?: '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION A: PEKERJAAN --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-label-dark py-2">
            <h6 class="mb-0 fw-bold text-dark"><span class="badge bg-dark me-2">A</span> PEKERJAAN</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">NO</th>
                            <th>JENIS PEKERJAAN</th>
                            <th style="width: 250px;">LOKASI PEKERJAAN</th>
                            <th style="width: 250px;">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report->tasks as $idx => $task)
                            <tr>
                                <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                                <td>{{ $task->task_name }}</td>
                                <td>{{ $task->location ?: '-' }}</td>
                                <td>{{ $task->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Tidak ada data pekerjaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SECTION B & C: BAHAN & PERALATAN --}}
    <div class="row g-4 mb-4">
        {{-- Section B --}}
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-label-dark py-2">
                    <h6 class="mb-0 fw-bold text-dark"><span class="badge bg-dark me-2">B</span> BAHAN / MATERIAL</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">NO</th>
                                    <th>JENIS BAHAN YANG DIGUNAKAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($report->materials as $idx => $mat)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                                        <td>{{ $mat->material_name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-3">Tidak ada data bahan/material.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section C --}}
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-label-dark py-2">
                    <h6 class="mb-0 fw-bold text-dark"><span class="badge bg-dark me-2">C</span> PERALATAN YANG DIGUNAKAN</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">NO</th>
                                    <th>NAMA PERALATAN</th>
                                    <th style="width: 100px;">JUMLAH</th>
                                    <th style="width: 100px;">SATUAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($report->equipments as $idx => $eq)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                                        <td>{{ $eq->equipment_name }}</td>
                                        <td>{{ $eq->qty ?: '-' }}</td>
                                        <td>{{ $eq->unit ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Tidak ada data peralatan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION D & E: TENAGA KERJA & CUACA --}}
    <div class="row g-4 mb-4">
        {{-- Section D --}}
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-label-dark py-2">
                    <h6 class="mb-0 fw-bold text-dark"><span class="badge bg-dark me-2">D</span> TENAGA KERJA</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">NO</th>
                                    <th>JABATAN</th>
                                    <th style="width: 150px;">JUMLAH (ORANG)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($report->manpowers as $idx => $mp)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                                        <td>{{ $mp->position }}</td>
                                        <td>{{ $mp->manpower_count ?: '-' }} Orang</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Tidak ada data tenaga kerja.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section E --}}
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-label-dark py-2">
                    <h6 class="mb-0 fw-bold text-dark"><span class="badge bg-dark me-2">E</span> CUACA (LAPORAN CUACA)</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>
                                <i class="mdi {{ $report->weather_cerah ? 'mdi-checkbox-marked text-success' : 'mdi-checkbox-blank-outline text-muted' }} me-2 fs-5"></i>
                                <strong class="text-warning"><i class="mdi mdi-weather-sunny me-1"></i> CERAH</strong>
                            </span>
                            <span class="text-muted small">{{ $report->weather_cerah && $report->weather_cerah_time ? 'JAM : ' . $report->weather_cerah_time : '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>
                                <i class="mdi {{ $report->weather_hujan ? 'mdi-checkbox-marked text-success' : 'mdi-checkbox-blank-outline text-muted' }} me-2 fs-5"></i>
                                <strong class="text-primary"><i class="mdi mdi-weather-rainy me-1"></i> HUJAN</strong>
                            </span>
                            <span class="text-muted small">{{ $report->weather_hujan && $report->weather_hujan_time ? 'JAM : ' . $report->weather_hujan_time : '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>
                                <i class="mdi {{ $report->weather_mendung ? 'mdi-checkbox-marked text-success' : 'mdi-checkbox-blank-outline text-muted' }} me-2 fs-5"></i>
                                <strong class="text-secondary"><i class="mdi mdi-weather-cloudy me-1"></i> MENDUNG</strong>
                            </span>
                            <span class="text-muted small">{{ $report->weather_mendung && $report->weather_mendung_time ? 'JAM : ' . $report->weather_mendung_time : '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>
                                <i class="mdi {{ $report->weather_dll ? 'mdi-checkbox-marked text-success' : 'mdi-checkbox-blank-outline text-muted' }} me-2 fs-5"></i>
                                <strong><i class="mdi mdi-dots-horizontal me-1"></i> DLL</strong>
                            </span>
                            <span class="text-muted small">{{ $report->weather_dll && $report->weather_dll_time ? 'JAM : ' . $report->weather_dll_time : '-' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- EVALUASI & PLANNING --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-label-secondary py-2">
            <h6 class="mb-0 fw-bold text-dark"><i class="mdi mdi-notebook-outline me-1"></i> EVALUASI & RENCANA KERJA</h6>
        </div>
        <div class="card-body pt-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded h-100">
                        <label class="fw-bold text-primary mb-2 d-block">PLANNING HARI INI:</label>
                        <p class="mb-0 text-muted" style="white-space: pre-line;">{{ $report->planning_today ?: '-' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded h-100">
                        <label class="fw-bold text-success mb-2 d-block">PENCAPAIAN HARI INI:</label>
                        <p class="mb-0 text-muted" style="white-space: pre-line;">{{ $report->achievement_today ?: '-' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded h-100">
                        <label class="fw-bold text-danger mb-2 d-block">KENDALA :</label>
                        <p class="mb-0 text-muted" style="white-space: pre-line;">{{ $report->issues_constraints ?: '-' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded h-100">
                        <label class="fw-bold text-info mb-2 d-block">RENCANA PEKERJAAN HARI BERIKUTNYA:</label>
                        <p class="mb-0 text-muted" style="white-space: pre-line;">{{ $report->next_plan ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PENGESAHAN / TANDA TANGAN --}}
    <div class="card mb-4 shadow-sm border-top border-info border-3">
        <div class="card-header bg-light-info py-2">
            <h6 class="mb-0 fw-bold text-dark"><i class="mdi mdi-shield-check-outline me-1"></i> PENGESAHAN & TANDA TANGAN</h6>
        </div>
        <div class="card-body pt-3">
            <div class="row g-4 text-center">
                {{-- Pemberi Tugas --}}
                <div class="col-md-6 border-end">
                    <div class="p-3 bg-light rounded h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-1">PEMBERI TUGAS</h6>
                            <small class="text-muted">{{ $report->client ? $report->client->company : 'Client / Owner' }}</small>
                        </div>
                        <div class="my-3 py-2">
                            @if ($report->client_sign)
                                <img src="{{ Storage::disk('public')->url($report->client_sign) }}" alt="Sign Client"
                                    style="max-height: 90px;" class="border rounded p-1 bg-white" />
                            @else
                                <div class="text-muted fst-italic py-3">( Belum ditandatangani )</div>
                            @endif
                        </div>
                        <div>
                            <strong class="d-block text-decoration-underline">{{ $report->client_pic_name ?: '________________________' }}</strong>
                            <small class="text-muted">Penanggung Jawab / PIC</small>
                        </div>
                    </div>
                </div>

                {{-- Kontraktor Pelaksana --}}
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-1">KONTRAKTOR PELAKSANA</h6>
                            <small class="text-primary fw-bold">{{ $report->contractor_name }}</small>
                        </div>
                        <div class="my-3 py-2">
                            @if ($report->contractor_sign)
                                <img src="{{ Storage::disk('public')->url($report->contractor_sign) }}" alt="Sign Contractor"
                                    style="max-height: 90px;" class="border rounded p-1 bg-white" />
                            @else
                                <div class="text-muted fst-italic py-3">( Belum ditandatangani )</div>
                            @endif
                        </div>
                        <div>
                            <strong class="d-block text-decoration-underline">{{ $report->contractor_pic_name ?: ($report->creator ? $report->creator->name : '________________________') }}</strong>
                            <small class="text-muted">Pelaksana Lapangan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FOTO DOKUMENTASI (POSISI TEPAT SETELAH CARD KONTRAKTOR PELAKSANA) --}}
    <div class="card mb-4 shadow-sm border-top border-warning border-3">
        <div class="card-header bg-label-warning py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="mdi mdi-camera me-1"></i> DOKUMENTASI FOTO PEKERJAAN LAPANGAN ({{ $report->photos->count() }})
            </h6>
            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalAddPhoto">
                <i class="mdi mdi-plus me-1"></i> Tambah Foto
            </button>
        </div>
        <div class="card-body pt-3">
            @if ($report->photos->count() > 0)
                <div class="row g-3">
                    @foreach ($report->photos as $photo)
                        <div class="col-md-3 col-sm-6" id="photo-card-{{ $photo->id }}">
                            <div class="card border h-100 shadow-none">
                                <a href="{{ $photo->url }}" target="_blank">
                                    <img src="{{ $photo->url }}" class="card-img-top"
                                        style="height: 180px; object-fit: cover;" alt="Foto Dokumentasi" />
                                </a>
                                <div class="card-body p-2">
                                    <p class="small mb-0 text-dark fw-semibold">{{ $photo->caption ?: 'Dokumentasi Lapangan' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="mdi mdi-image-multiple-outline fs-1 mb-2 d-block"></i>
                    Belum ada foto dokumentasi untuk laporan ini.
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL TAMBAH FOTO --}}
    <div class="modal fade" id="modalAddPhoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('project-reports.photo.upload', $report->id) }}" method="POST"
                enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Foto Dokumentasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih File Foto:</label>
                        <input type="file" name="photo" class="form-control" accept="image/*" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan / Caption Foto:</label>
                        <input type="text" name="caption" class="form-control"
                            placeholder="Contoh: Kondisi kompresor setelah diservis..." />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload Foto</button>
                </div>
            </form>
        </div>
    </div>
@endsection
