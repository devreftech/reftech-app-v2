@extends('layouts.sales.app')
@section('title', 'Delivery Order (Surat Jalan)')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center py-3 mb-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Accounting /</span> Delivery Order
            </h4>
            <p class="text-muted mb-0 small">Daftar Surat Jalan / Pengiriman Barang dari Invoice, Quotation, dan Manual</p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('delivery.create') }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                <i class="mdi mdi-plus fs-5"></i> Buat Surat Jalan Manual
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-check-circle-outline me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Metric Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-semibold small d-block mb-1">Total Surat Jalan</span>
                            <h4 class="fw-bold mb-0 text-dark">{{ number_format($totalCount, 0, ',', '.') }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="mdi mdi-truck-delivery-outline fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-semibold small d-block mb-1">Ekspedisi / Logistik</span>
                            <h4 class="fw-bold mb-0 text-info">{{ number_format($ekspedisiCount, 0, ',', '.') }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="mdi mdi-truck-fast-outline fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-semibold small d-block mb-1">Teknisi / Hand Carry</span>
                            <h4 class="fw-bold mb-0 text-warning">{{ number_format($teknisiCount, 0, ',', '.') }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="mdi mdi-account-wrench-outline fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-semibold small d-block mb-1">TTD Digital Customer</span>
                            <h4 class="fw-bold mb-0 text-success">{{ number_format($signedCount, 0, ',', '.') }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="mdi mdi-draw-pen fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card border-0 shadow-sm overflow-hidden mb-4">
        <div class="table-responsive text-nowrap p-3">
            <table class="table table-hover align-middle mb-0" id="deliveryTable" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">No. Surat Jalan</th>
                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Customer / Perusahaan</th>
                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Tanggal</th>
                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Tipe / Sumber</th>
                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Pengiriman</th>
                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Status TTD</th>
                        <th class="fw-bold text-uppercase text-center" style="font-size: 11px; letter-spacing: 0.5px; width: 60px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Populated via Server-Side DataTables AJAX --}}
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        .delivery-row:hover {
            background-color: rgba(105, 108, 255, 0.04) !important;
            transition: background-color 0.15s ease-in-out;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable) {
                $('#deliveryTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('delivery.index') }}",
                    order: [[0, 'desc']],
                    pageLength: 15,
                    language: {
                        emptyTable: 'Belum ada Surat Jalan (Delivery Order) yang dibuat.',
                        zeroRecords: 'Data Surat Jalan tidak ditemukan.',
                        search: '',
                        searchPlaceholder: 'Cari Surat Jalan / Customer / PO...',
                        processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Memuat data...'
                    },
                    dom: '<"row mx-2 py-2"<"col-md-6"l><"col-md-6"f>>t<"row mx-2 py-2"<"col-md-6"i><"col-md-6"p>>',
                    columns: [
                        {
                            data: 'do_number',
                            name: 'no_do',
                            render: function(data, type, row) {
                                let html = `<a href="${row.show_url}" class="fw-bold text-primary text-decoration-none">
                                    <i class="mdi mdi-file-document-outline me-1"></i>${data}
                                </a>`;
                                if (row.po_number && row.po_number !== '-') {
                                    html += `<div class="small text-muted font-monospace" style="font-size: 11px;">PO: ${row.po_number}</div>`;
                                }
                                return html;
                            }
                        },
                        {
                            data: 'customer',
                            name: 'customer_name',
                            render: function(data, type, row) {
                                let html = `<span class="fw-semibold text-dark d-inline-block text-truncate" style="max-width: 250px;" title="${data}">${data}</span>`;
                                if (row.address && row.address !== '-') {
                                    html += `<div class="small text-muted text-truncate" style="max-width: 250px; font-size: 11px;" title="${row.address}">
                                        <i class="mdi mdi-map-marker-outline me-0.5"></i>${row.address}
                                    </div>`;
                                }
                                return html;
                            }
                        },
                        {
                            data: 'date',
                            name: 'date',
                            render: function(data) {
                                return `<span class="text-muted small"><i class="mdi mdi-calendar-blank-outline me-1"></i>${data}</span>`;
                            }
                        },
                        {
                            data: 'code',
                            name: 'code',
                            render: function(data, type, row) {
                                let codeLower = (data || '').toLowerCase();
                                let badgeClass = 'bg-label-secondary';
                                if (codeLower === 'unit') badgeClass = 'bg-label-primary';
                                else if (codeLower === 'sparepart') badgeClass = 'bg-label-info';
                                else if (codeLower === 'service') badgeClass = 'bg-label-warning';
                                else if (codeLower === 'suo') badgeClass = 'bg-label-dark';

                                let html = `<span class="badge ${badgeClass}">${data}</span>`;
                                if (row.entity === 'Kojisha') {
                                    html += ` <span class="badge bg-label-danger ms-1">Kojisha</span>`;
                                }
                                return html;
                            }
                        },
                        {
                            data: 'type',
                            name: 'type',
                            render: function(data, type, row) {
                                let isTeknisi = (data || '').toLowerCase() === 'teknisi';
                                let html = isTeknisi
                                    ? `<span class="badge bg-label-warning"><i class="mdi mdi-account-wrench-outline me-1"></i>Teknisi</span>`
                                    : `<span class="badge bg-label-info"><i class="mdi mdi-truck-fast-outline me-1"></i>Ekspedisi</span>`;
                                if (row.driver_name) {
                                    html += `<div class="small text-muted" style="font-size: 11px;">${row.driver_name}</div>`;
                                }
                                return html;
                            }
                        },
                        {
                            data: 'is_signed',
                            name: 'customer_signed_at',
                            render: function(data, type, row) {
                                if (data) {
                                    return `<span class="badge bg-label-success" title="Ditandatangani pada ${row.signed_at || ''}">
                                        <i class="mdi mdi-check-decagram me-1"></i> Ditandatangani
                                    </span>`;
                                }
                                return `<span class="badge bg-label-secondary"><i class="mdi mdi-clock-outline me-1"></i> Belum TTD</span>`;
                            }
                        },
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                let resetBtn = '';
                                if (row.is_signed) {
                                    resetBtn = `
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="${row.reset_url}" method="POST" class="d-inline" onsubmit="return confirm('Reset tanda tangan customer?');">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="dropdown-item d-flex align-items-center text-warning">
                                                    <i class="mdi mdi-refresh me-2"></i> Reset Tanda Tangan
                                                </button>
                                            </form>
                                        </li>
                                    `;
                                }
                                return `
                                    <div class="dropdown" onclick="event.stopPropagation();">
                                        <button type="button" class="btn btn-sm btn-icon btn-label-secondary hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center" href="${row.show_url}">
                                                    <i class="mdi mdi-eye-outline me-2 text-primary"></i> Lihat Detail
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center" href="${row.print_url}" target="_blank">
                                                    <i class="mdi mdi-printer-outline me-2 text-success"></i> Cetak Surat Jalan
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center btn-copy-sign-url" href="javascript:void(0);" data-url="${row.sign_url}">
                                                    <i class="mdi mdi-link-variant me-2 text-info"></i> Salin Link TTD Online
                                                </a>
                                            </li>
                                            ${resetBtn}
                                        </ul>
                                    </div>
                                `;
                            }
                        }
                    ],
                    createdRow: function(row, data) {
                        $(row).addClass('delivery-row').attr('data-href', data.show_url).css('cursor', 'pointer');
                    }
                });
            }

            // Clickable row to open show page
            $(document).on('click', '.delivery-row', function(e) {
                if (!$(e.target).closest('a, button, .dropdown, form, select, input').length) {
                    window.location.href = $(this).data('href');
                }
            });

            // Copy Sign URL to clipboard with fallback
            function copyTextToClipboard(text) {
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(function() {
                        showCopySuccess(text);
                    }).catch(function() {
                        fallbackCopy(text);
                    });
                } else {
                    fallbackCopy(text);
                }
            }

            function fallbackCopy(text) {
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                textArea.style.top = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    const successful = document.execCommand('copy');
                    if (successful) {
                        showCopySuccess(text);
                    } else {
                        prompt("Salin tautan TTD online secara manual:", text);
                    }
                } catch (err) {
                    prompt("Salin tautan TTD online secara manual:", text);
                }
                document.body.removeChild(textArea);
            }

            function showCopySuccess(url) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersalin!',
                        text: 'Tautan Tanda Tangan Online telah disalin ke clipboard.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert('Tautan Tanda Tangan Online berhasil disalin:\n' + url);
                }
            }

            $(document).on('click', '.btn-copy-sign-url', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const url = $(this).data('url') || $(this).attr('data-url');
                if (!url) {
                    alert('Tautan tanda tangan tidak tersedia.');
                    return;
                }
                copyTextToClipboard(url);
            });
        });
    </script>
@endpush
