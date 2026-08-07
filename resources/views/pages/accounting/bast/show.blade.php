@extends('layouts.sales.app')
@section('title', 'Detail BAST - ' . $bast->no_bast)

@section('content')
    @php
        $isReftech = $bast->entity === 'Reftech';
        $entityFullName = $isReftech ? 'PT. Reftech Jaya Optima' : 'PT. Kojisha Innotiv Indonesia';
    @endphp

    <div class="d-flex justify-content-between align-items-center py-3 mb-1">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Accounting / <a href="{{ route('bast.index') }}" class="text-muted">BAST</a> /</span> Detail
        </h4>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row invoice-preview">
        {{-- LEFT: Document Preview Card --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card mb-3 shadow-sm border-0">
                <div class="card-body p-4">
                    {{-- Header Logo & Company Info --}}
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column mb-0">
                        <div class="mb-xl-0 pb-1">
                            @if ($isReftech)
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="Reftech Logo" width="60%">
                                        </span>
                                    </span>
                                </div>
                                <p class="mb-1 fw-bolder" style="font-size: 15px">PT Reftech Jaya Optima</p>
                                <div style="font-size: 12px; color: #555;">
                                    <p class="mb-0">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                    <p class="mb-0">Bandung – Jawa Barat 40218</p>
                                    <p class="mb-0"><i class="mdi mdi-phone-outline me-1" style="font-size:11px;"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1" style="font-size:11px;"></i>admin@reftech.id &nbsp;|&nbsp; <i class="mdi mdi-web me-1" style="font-size:11px;"></i>www.reftech.id</p>
                                </div>
                            @else
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img src="{{ asset('/asset') }}/logo/Kojisha-Log.png" alt="Kojisha Logo" width="60%">
                                        </span>
                                    </span>
                                </div>
                                <p class="mb-1 fw-bolder" style="font-size: 15px">PT Kojisha Innotiv Indonesia</p>
                                <div style="font-size: 12px; color: #555;">
                                    <p class="mb-0">Jl. Nancep No. 45A, Setu</p>
                                    <p class="mb-0">Cibitung - Kab. Bekasi 17320</p>
                                    <p class="mb-0"><i class="mdi mdi-phone-outline me-1" style="font-size:11px;"></i>+62 812-1000-0997 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1" style="font-size:11px;"></i>admin@kojisha.com</p>
                                </div>
                            @endif
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold mb-1" style="letter-spacing:1px; color:#696cff;">BAST</h3>
                            <p class="mb-1 fw-semibold" style="font-size:14px;">#{{ $bast->no_bast }}</p>
                            <p class="mb-1 text-muted" style="font-size:12px;"><i class="mdi mdi-calendar-outline me-1"></i>{{ $bast->work_date->format('d F Y') }}</p>
                            <p class="mb-0 text-muted" style="font-size:11px;">Dibuat oleh: {{ $bast->creator->name ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Accent Divider --}}
                    <div style="height:3px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:14px 0 20px;"></div>

                    {{-- BAST Title & Intro --}}
                    <div class="text-center mb-4">
                        <h5 class="fw-bold mb-1 text-uppercase text-dark" style="letter-spacing:0.5px;">Berita Acara Serah Terima Pekerjaan</h5>
                        <span class="badge bg-label-primary px-3 py-1 fs-6 fw-semibold">{{ $bast->no_bast }}</span>
                    </div>

                    <p class="mb-3 text-dark" style="font-size: 13.5px; line-height: 1.6;">
                        Bersama dengan ini kami <strong>{{ $entityFullName }}</strong>, telah menyelesaikan pekerjaan hingga
                        <strong class="text-success">SELESAI</strong> untuk pekerjaan sbb :
                    </p>

                    <div class="border rounded-3 p-3 text-center fw-bold text-uppercase mb-4 shadow-sm"
                        style="font-size: 15px; background: #f8f9fa; border-left: 4px solid #696cff !important; color: #2c3e50;">
                        {{ $bast->work_title }}
                    </div>

                    {{-- Metadata Details --}}
                    <div class="card border mb-4 bg-light shadow-none">
                        <div class="card-body p-3">
                            <table class="table table-borderless table-sm mb-0" style="font-size: 13px; color: #333;">
                                <tr>
                                    <td style="width: 220px;" class="fw-semibold text-muted">Tanggal Pekerjaan</td>
                                    <td style="width: 20px;">:</td>
                                    <td class="fw-bold">{{ $bast->work_date->format('d-m-Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Pemberi Pekerjaan / Customer</td>
                                    <td>:</td>
                                    <td class="fw-bold">{{ $bast->customer_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Sesuai PO / Kontrak No.</td>
                                    <td>:</td>
                                    <td class="fw-bold">{{ $bast->po_number ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Units Table --}}
                    <h6 class="fw-bold mb-2 text-dark" style="font-size: 13px;">Terhadap unit-unit sebagai berikut:</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle mb-0" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 8%;" class="text-center">No.</th>
                                    <th>Unit</th>
                                    <th>Serial No.</th>
                                    <th style="width: 15%;" class="text-center">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bast->units as $index => $unit)
                                    <tr>
                                        <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                        <td class="fw-bold text-dark">{{ $unit->unit_name }}</td>
                                        <td>{{ $unit->serial_no ?: '-' }}</td>
                                        <td class="text-center fw-semibold">{{ $unit->qty }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Tidak ada detail unit.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Test Running Result --}}
                    <h6 class="fw-bold mb-2 text-dark" style="font-size: 13px;">Hasil pengecekan pada saat test running:</h6>
                    <div class="border rounded-3 p-3 mb-4 bg-light" style="min-height: 80px; white-space: pre-wrap; font-size: 13px; color: #333; line-height: 1.6;">{{ $bast->test_running_result ?: '-' }}</div>

                    <p class="mb-2 text-dark" style="font-size: 13px;">
                        Demikian <strong>BERITA ACARA SERAH TERIMA PEKERJAAN</strong> ini ditandatangani oleh kedua belah pihak:
                    </p>
                    <ul class="mb-3 text-dark" style="font-size: 13px;">
                        <li>Pelaksana pekerjaan&nbsp; : <strong>{{ $entityFullName }}</strong></li>
                        <li>Pemberi pekerjaan&nbsp; : <strong>{{ $bast->customer_name }}</strong></li>
                    </ul>
                    <p class="mb-4 text-dark" style="font-size: 13px;">
                        Dengan ini segala hal yang berhubungan dengan pekerjaan tersebut di atas dinyatakan
                        <strong class="text-success">SELESAI</strong>.
                    </p>

                    {{-- Signature Box Preview --}}
                    <div class="border rounded-3 p-4 bg-light mt-4">
                        <div class="row text-center">
                            <div class="col-6">
                                <p class="fw-bold text-muted small text-uppercase mb-5">Pelaksana Pekerjaan<br><span class="text-dark">{{ $entityFullName }}</span></p>
                                <div class="border-top border-dark mx-auto" style="width: 70%;"></div>
                            </div>
                            <div class="col-6">
                                <p class="fw-bold text-muted small text-uppercase mb-5">Pemberi Pekerjaan<br><span class="text-dark">{{ $bast->customer_name }}</span></p>
                                <div class="border-top border-dark mx-auto" style="width: 70%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Quick Action Sidebar --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3 border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-primary bg-gradient py-3 px-4 d-flex align-items-center justify-content-between text-white">
                    <h6 class="card-title mb-0 fw-bold text-white d-flex align-items-center">
                        <i class="mdi mdi-lightning-bolt-outline me-2 fs-5"></i> Quick Actions
                    </h6>
                    <span class="badge bg-white text-primary fw-semibold" style="font-size: 10px;">BAST</span>
                </div>

                <div class="card-body p-3">
                    {{-- Print PDF --}}
                    <div class="mb-2">
                        <a href="{{ route('bast.print', $bast->id) }}" target="_blank"
                           class="btn btn-primary d-grid w-100 shadow-sm py-2"
                           style="background: linear-gradient(135deg, #696cff 0%, #3f42db 100%); border: none;">
                            <span class="d-flex align-items-center justify-content-center gap-1 fw-bold fs-6">
                                <i class="mdi mdi-printer-outline fs-5"></i> Print BAST
                            </span>
                        </a>
                    </div>

                    {{-- Edit BAST --}}
                    <div class="mb-2">
                        <button type="button" class="btn btn-outline-primary d-grid w-100 btn-edit-bast" data-id="{{ $bast->id }}">
                            <span class="d-flex align-items-center justify-content-center gap-1">
                                <i class="mdi mdi-pencil-outline"></i> Edit BAST
                            </span>
                        </button>
                    </div>

                    {{-- Delete BAST --}}
                    <div class="mb-2">
                        <button type="button" class="btn btn-outline-danger d-grid w-100 btn-delete-bast" data-id="{{ $bast->id }}" data-no="{{ $bast->no_bast }}">
                            <span class="d-flex align-items-center justify-content-center gap-1">
                                <i class="mdi mdi-trash-can-outline"></i> Hapus BAST
                            </span>
                        </button>
                    </div>

                    <hr class="my-3">

                    {{-- Back --}}
                    <div>
                        <a href="{{ route('bast.index') }}" class="btn btn-label-secondary d-grid w-100">
                            <span class="d-flex align-items-center justify-content-center gap-1">
                                <i class="mdi mdi-arrow-left"></i> Kembali ke List BAST
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.modal.bast.create')
@endsection

@push('script')
    <script>
        $(document).on('click', '.btn-edit-bast', function() {
            const id = $(this).data('id');
            $.get(`{{ url('/bast') }}/${id}/edit-data`, function(response) {
                const b = response.bast;
                window.openBastModal({
                    bastId: b.id,
                    entity: b.entity,
                    customerName: b.customer_name,
                    workTitle: b.work_title,
                    poNumber: b.po_number,
                    workDate: b.work_date,
                    testRunningResult: b.test_running_result,
                    units: b.units,
                });
            });
        });

        $(document).on('click', '.btn-delete-bast', function() {
            const id = $(this).data('id');
            const no = $(this).data('no');
            if (!confirm(`Hapus BAST ${no}? Tindakan ini tidak bisa dibatalkan.`)) return;

            $.ajax({
                url: `{{ url('/bast') }}/${id}`,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function() {
                    window.location.href = '{{ route('bast.index') }}';
                }
            });
        });

        $(document).on('bast:saved', function() {
            window.location.reload();
        });
    </script>
@endpush
