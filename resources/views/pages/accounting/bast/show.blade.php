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
                <div class="card-body" style="padding: 30px;">
                    {{-- Header Logo & Company Info --}}
                    <div class="d-flex justify-content-between align-items-start mb-0" style="display: flex !important; flex-direction: row !important; justify-content: space-between !important; align-items: flex-start !important;">
                        <div class="mb-0 pb-1">
                            @if ($isReftech)
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-1">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="Reftech Logo" width="60%">
                                        </span>
                                    </span>
                                </div>
                                <p class="mb-0 text-uppercase fw-bold" style="font-size: 13.5px; color: #4f46e5; letter-spacing: 0.5px; line-height: 1.2;">
                                    COMPRESSED AIR SOLUTION
                                </p>
                                <p class="mb-1" style="font-size: 11px; font-weight: 600; color: #475569;">
                                    Sales &nbsp;|&nbsp; Service &nbsp;|&nbsp; Rental &nbsp;|&nbsp; Measurement Air Audit
                                </p>
                                <div class="d-flex align-items-center gap-1" style="font-size: 10.5px; color: #475569; font-weight: 500;">
                                    <i class="mdi mdi-certificate-outline me-1 text-primary"></i>
                                    <span class="fw-bold" style="color: #696cff;">ISO Certified:</span> 
                                    ISO 9001:2015 &nbsp;|&nbsp; ISO 14001:2015 &nbsp;|&nbsp; ISO 45001:2018
                                </div>
                            @else
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img src="{{ asset('/asset') }}/logo/Kojisha-Log.png" alt="Kojisha Logo" width="60%">
                                        </span>
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="text-end" style="padding-top: 10px;">
                            @if ($isReftech)
                                <p class="fw-bolder text-uppercase" style="font-size: 16px; color: #4f46e5; letter-spacing: 0.5px; line-height: 1.2; margin-bottom: 6px !important;">PT REFTECH JAYA OPTIMA</p>
                                <div style="font-size: 12px; line-height: 1.35; color: #334155; font-weight: 500;">
                                    <p class="mb-0">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                    <p class="mb-0">Bandung – Jawa Barat 40218</p>
                                    <p class="mb-0 text-nowrap" style="font-size: 11px; white-space: nowrap;"><i class="mdi mdi-phone-outline me-1 text-primary"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1 text-primary"></i>admin@reftech.id &nbsp;|&nbsp; <i class="mdi mdi-web me-1 text-primary"></i>www.reftech.id</p>
                                </div>
                            @else
                                <p class="fw-bolder text-uppercase" style="font-size: 16px; color: #4f46e5; letter-spacing: 0.5px; line-height: 1.2; margin-bottom: 6px !important;">PT KOJISHA INNOTIV INDONESIA</p>
                                <div style="font-size: 12px; line-height: 1.35; color: #334155; font-weight: 500;">
                                    <p class="mb-0">Jl. Nancep No. 45A, Setu</p>
                                    <p class="mb-0">Cibitung - Kab. Bekasi 17320</p>
                                    <p class="mb-0 text-nowrap" style="font-size: 11px; white-space: nowrap;"><i class="mdi mdi-phone-outline me-1 text-primary"></i>+62 812-1000-0997 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1 text-primary"></i>admin@kojisha.com</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Accent Divider --}}
                    <div style="height:3px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:14px 0 20px;"></div>

                    {{-- BAST Title & Intro --}}
                    <div class="text-center mb-4">
                        <h4 class="fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px; color: #4f46e5; font-size: 18px;">Berita Acara Serah Terima Pekerjaan</h4>
                        <div class="fw-bold" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 18px; color: #4f46e5; letter-spacing: 0.5px;">{{ $bast->no_bast }}</div>
                    </div>

                    <p class="mb-3" style="font-size: 13.5px; line-height: 1.6; color: #1e293b;">
                        Bersama dengan ini kami <strong class="text-uppercase">{{ $entityFullName }}</strong>, telah menyelesaikan pekerjaan hingga
                        <strong class="text-success">SELESAI</strong> untuk pekerjaan sbb :
                    </p>

                    <div class="border rounded-3 p-3 text-center fw-bold text-uppercase mb-4 shadow-sm"
                        style="font-size: 18px; border-left: 4px solid #696cff !important;">
                        {{ $bast->work_title }}
                    </div>

                    {{-- Metadata Details --}}
                    <div class="card border mb-4 shadow-none">
                        <div class="card-body p-3.5">
                            <table class="table table-borderless mb-0" style="font-size: 13.5px;">
                                <tr>
                                    <td style="width: 240px; padding: 8px 4px; color: #475569;" class="fw-semibold">Tanggal Pekerjaan</td>
                                    <td style="width: 20px; padding: 8px 4px; color: #475569;">:</td>
                                    <td style="padding: 8px 4px;" class="fw-bold text-dark">{{ $bast->work_date->format('d-m-Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 4px; color: #475569;" class="fw-semibold">Pemberi Pekerjaan / Customer</td>
                                    <td style="padding: 8px 4px; color: #475569;">:</td>
                                    <td style="padding: 8px 4px;" class="fw-bold text-dark">{{ $bast->customer_name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 4px; color: #475569;" class="fw-semibold">Sesuai PO / Kontrak No.</td>
                                    <td style="padding: 8px 4px; color: #475569;">:</td>
                                    <td style="padding: 8px 4px;" class="fw-bold text-dark">{{ $bast->po_number ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Units Table --}}
                    <h6 class="fw-bold mb-3 mt-3" style="font-size: 13.5px;">Terhadap unit-unit sebagai berikut:</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle mb-0" style="font-size: 13px;">
                            <thead>
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
                                        <td class="fw-bold">{{ $unit->unit_name }}</td>
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
                    <label class="form-label fw-bold mb-2 text-dark" style="font-size: 13.5px;">Hasil pengecekan pada saat test running :</label>
                    <div class="mb-4">
                        <textarea class="form-control" rows="4" style="font-size: 13.5px; background-color: #fff; border: 1px solid #d9dee3; resize: vertical;" readonly placeholder="Hasil pengecekan pada saat test running...">{{ $bast->test_running_result }}</textarea>
                    </div>

                    <p class="mb-2 text-dark" style="font-size: 13px;">
                        Demikian <strong>BERITA ACARA SERAH TERIMA PEKERJAAN</strong> ini ditandatangani oleh kedua belah pihak:
                    </p>
                    <table class="table table-borderless table-sm mb-3 ms-2 text-dark" style="font-size: 13px; width: auto;">
                        <tr>
                            <td style="width: 170px; padding: 2px 0;">• Pelaksana pekerjaan</td>
                            <td style="width: 15px; padding: 2px 0;">:</td>
                            <td style="padding: 2px 0;"><strong>{{ $entityFullName }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">• Pemberi pekerjaan</td>
                            <td style="padding: 2px 0;">:</td>
                            <td style="padding: 2px 0;"><strong>{{ $bast->customer_name }}</strong></td>
                        </tr>
                    </table>
                    <p class="mb-4 text-dark" style="font-size: 13px;">
                        Dengan ini segala hal yang berhubungan dengan pekerjaan tersebut di atas dinyatakan
                        <strong class="text-success">SELESAI</strong>.
                    </p>

                    {{-- Signature Box Preview --}}
                    <div class="border rounded-3 p-4 bg-light mt-4">
                        <div class="row text-center">
                            <div class="col-6">
                                <p class="fw-bold text-muted small text-uppercase mb-1">Pelaksana Pekerjaan</p>
                                <p class="fw-bold text-dark text-uppercase mb-0" style="font-size: 13.5px;">{{ $entityFullName }}</p>
                                <div style="height: 95px;"></div>
                                <div class="border-top border-dark mx-auto" style="width: 70%;"></div>
                                <small class="text-muted d-block mt-1">Project / Service Dept.</small>
                            </div>
                            <div class="col-6">
                                <p class="fw-bold text-muted small text-uppercase mb-1">Pemberi Pekerjaan</p>
                                <p class="fw-bold text-dark text-uppercase mb-0" style="font-size: 13.5px;">{{ $bast->customer_name }}</p>
                                <div style="height: 95px;"></div>
                                <div class="border-top border-dark mx-auto" style="width: 70%;"></div>
                                <small class="text-muted d-block mt-1">Authorized Representative</small>
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
