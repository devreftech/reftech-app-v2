@extends('layouts.sales.app')
@section('title', 'Detail Pengeluaran Kas - ' . ($expense->no_expense ?? $expense->no_voucher))

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <style>
        .voucher-card {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            background: #fff;
        }
        .voucher-header {
            padding: 2rem 2rem 1.25rem 2rem;
            background: linear-gradient(180deg, #fafbfc 0%, #ffffff 100%);
            border-bottom: 2px solid #edf0f2;
        }
        .voucher-meta-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #8592a3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .voucher-meta-val {
            font-size: 0.9375rem;
            font-weight: 600;
            color: #384551;
        }
        .terbilang-box {
            background-color: #f4f5fa;
            border-left: 4px solid var(--bs-primary);
            border-radius: 8px;
            padding: 12px 16px;
        }
        .signature-box {
            border: 1px dashed #d9dee3;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            height: 100%;
        }
        .signature-space {
            height: 60px;
        }
    </style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / <a href="{{ url('/expense') }}" class="text-muted">Expense</a> /</span> Detail Pengeluaran
            </h4>
            <p class="text-muted mb-0">Bukti Pengeluaran Kas / Bank dan Catatan Jurnal Transaksi Keuangan.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button class="btn btn-outline-secondary waves-effect" id="backButton">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </button>
            <a href="{{ route('expense.print', $expense->id) }}" target="_blank" class="btn btn-primary waves-effect">
                <i class="mdi mdi-printer me-1"></i> Cetak Bukti Kas Keluar
            </a>
        </div>
    </div>

    @if ($expense->payroll)
        <div class="alert alert-primary d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between mb-4 shadow-xs" role="alert">
            <div class="d-flex align-items-center mb-2 mb-sm-0">
                <i class="mdi mdi-account-cash fs-3 text-primary me-2"></i>
                <div>
                    <strong>Pengeluaran ini terintegrasi dengan HR Payroll Karyawan:</strong>
                    <span class="ms-1">{{ $expense->payroll->title }} ({{ $expense->payroll->code }})</span>
                </div>
            </div>
            <a href="{{ route('hr.payrolls.show', $expense->payroll->id) }}" class="btn btn-sm btn-primary">
                <i class="mdi mdi-open-in-new me-1"></i> Buka Batch Payroll
            </a>
        </div>
    @endif

    <div class="row">
        <!-- Main Voucher Card -->
        <div class="col-xl-9 col-lg-8 col-12 mb-4">
            <div class="card voucher-card shadow-sm">
                <!-- Header -->
                <div class="voucher-header">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('assets/img/favicon/logo-reftech.png') }}" 
                                 alt="Reftech Logo" 
                                 style="max-height: 52px; width: auto;" 
                                 onerror="this.src='{{ asset('assets/img/favicon/logo-hitam-app.png') }}'">
                            <div>
                                <h5 class="fw-bold text-dark mb-0">PT. REFTECH MULTI MANDIRI</h5>
                                <span class="badge bg-label-primary font-monospace mt-1">BUKTI KAS / BANK KELUAR</span>
                            </div>
                        </div>
                        <div class="text-sm-end">
                            <div class="voucher-meta-label">Nomor Transaksi</div>
                            <h4 class="fw-bold text-primary font-monospace mb-1">{{ $expense->no_expense ?? $expense->no_voucher ?? '-' }}</h4>
                            <div class="text-muted small">
                                <i class="mdi mdi-calendar-blank-outline me-1"></i>
                                {{ \Carbon\Carbon::parse($expense->date)->format('d F Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meta Details -->
                <div class="card-body pt-4">
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-3">
                            <div class="voucher-meta-label">Dibayarkan Kepada (Payee)</div>
                            <div class="voucher-meta-val text-dark">{{ $expense->payee ?: 'Umum / Operasional' }}</div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="voucher-meta-label">No. Invoice / Kwitansi</div>
                            <div class="voucher-meta-val font-monospace">{{ $expense->no_invoice ?: '-' }}</div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="voucher-meta-label">Sumber Dana (Bank / Kas)</div>
                            <div class="voucher-meta-val">
                                @if ($expense->bank)
                                    <span class="badge bg-label-info">
                                        {{ $expense->bank->bank }} ({{ $expense->bank->no_rek }})
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary">Kas Tunai</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="voucher-meta-label">No. Cek / Giro</div>
                            <div class="voucher-meta-val font-monospace">{{ $expense->no_cheque ?: '-' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="voucher-meta-label">Keperluan / Keterangan (Memo)</div>
                            <div class="p-2 rounded bg-light border text-dark fw-medium">{{ $expense->memo ?: '-' }}</div>
                        </div>
                    </div>

                    <!-- Journal Table -->
                    <div class="table-responsive border rounded mb-4">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 140px;">Kode Akun</th>
                                    <th>Nama Akun</th>
                                    <th>Deskripsi / Memo Baris</th>
                                    <th class="text-end" style="width: 170px;">Debit</th>
                                    <th class="text-end" style="width: 170px;">Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 0;
                                    $totalDebit = 0;
                                    $totalKredit = 0;
                                @endphp
                                @foreach ($detailExpense as $detail)
                                    @php
                                        $no++;
                                        $totalDebit += (float) $detail->amount;
                                    @endphp
                                    <tr>
                                        <td class="text-muted small">{{ $no }}</td>
                                        <td>
                                            <span class="badge bg-label-primary font-monospace">
                                                {{ $detail->account?->code ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-dark">{{ $detail->account?->name ?? '-' }}</td>
                                        <td class="text-muted">{{ $detail->memo ?: '-' }}</td>
                                        <td class="text-end fw-bold text-dark">
                                            Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end text-muted">Rp 0</td>
                                    </tr>
                                @endforeach

                                @if ($expense->id_bank)
                                    @php
                                        $totalKredit += (float) $expense->amount;
                                    @endphp
                                    <tr>
                                        <td class="text-muted small">{{ $no + 1 }}</td>
                                        <td>
                                            <span class="badge bg-label-info font-monospace">
                                                1102-003
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-dark">
                                            {{ $expense->bank ? $expense->bank->bank . ' - ' . $expense->bank->no_rek : 'Bank Operasional BCA' }}
                                        </td>
                                        <td class="text-muted">Kas / Bank Pengeluaran</td>
                                        <td class="text-end text-muted">Rp 0</td>
                                        <td class="text-end fw-bold text-dark">
                                            Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @else
                                    @php
                                        $totalKredit += (float) $expense->amount;
                                    @endphp
                                    <tr>
                                        <td class="text-muted small">{{ $no + 1 }}</td>
                                        <td>
                                            <span class="badge bg-label-secondary font-monospace">1101-001</span>
                                        </td>
                                        <td class="fw-semibold text-dark">Kas Utama</td>
                                        <td class="text-muted">Kas Pengeluaran Tunai</td>
                                        <td class="text-end text-muted">Rp 0</td>
                                        <td class="text-end fw-bold text-dark">
                                            Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-light border-top">
                                <tr>
                                    <th colspan="4" class="text-end fw-bold">TOTAL TRANSAKSI :</th>
                                    <th class="text-end fw-bold text-primary fs-6">
                                        Rp {{ number_format($totalDebit, 0, ',', '.') }}
                                    </th>
                                    <th class="text-end fw-bold text-primary fs-6">
                                        Rp {{ number_format($totalKredit, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Terbilang Box -->
                    <div class="terbilang-box mb-4">
                        <div class="voucher-meta-label text-primary">Terbilang (Say Amount)</div>
                        <div class="fw-bold text-dark fs-6 fst-italic">
                            # {{ $terbilang }} Rupiah #
                        </div>
                    </div>

                    <!-- Signatures Authorization Grid -->
                    <div class="row g-3 pt-2">
                        <div class="col-sm-4 col-12">
                            <div class="signature-box">
                                <span class="voucher-meta-label d-block">Dibuat Oleh</span>
                                <div class="signature-space"></div>
                                <span class="fw-semibold text-dark d-block">Staff Finance</span>
                                <small class="text-muted">Tgl: {{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</small>
                            </div>
                        </div>
                        <div class="col-sm-4 col-12">
                            <div class="signature-box">
                                <span class="voucher-meta-label d-block">Diperiksa Oleh</span>
                                <div class="signature-space"></div>
                                <span class="fw-semibold text-dark d-block">Finance Manager</span>
                                <small class="text-muted">Tgl: _______________</small>
                            </div>
                        </div>
                        <div class="col-sm-4 col-12">
                            <div class="signature-box">
                                <span class="voucher-meta-label d-block">Disetujui / Diterima Oleh</span>
                                <div class="signature-space"></div>
                                <span class="fw-semibold text-dark d-block">{{ $expense->payee ?: 'Penerima Kas' }}</span>
                                <small class="text-muted">Tgl: _______________</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Action Center -->
        <div class="col-xl-3 col-lg-4 col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header border-bottom py-3">
                    <h6 class="card-title mb-0 fw-bold">
                        <i class="mdi mdi-cogs text-primary me-2"></i>Aksi Dokumen
                    </h6>
                </div>
                <div class="card-body pt-3 d-flex flex-column gap-2">
                    <a href="{{ route('expense.print', $expense->id) }}" target="_blank"
                       class="btn btn-primary w-100 waves-effect text-start d-flex align-items-center">
                        <i class="mdi mdi-printer me-2"></i> Cetak / Download Voucher
                    </a>
                    <a href="{{ route('expense.create') }}"
                       class="btn btn-outline-primary w-100 waves-effect text-start d-flex align-items-center">
                        <i class="mdi mdi-plus-circle-outline me-2"></i> Tambah Pengeluaran Baru
                    </a>
                    <button type="button" class="btn btn-outline-danger w-100 waves-effect text-start d-flex align-items-center delete-expense"
                        data-id="{{ $expense->id }}">
                        <i class="mdi mdi-trash-can-outline me-2"></i> Hapus Pengeluaran
                    </button>
                    <a href="{{ route('expense.index') }}" class="btn btn-outline-secondary w-100 waves-effect text-start d-flex align-items-center">
                        <i class="mdi mdi-format-list-bulleted me-2"></i> Daftar Pengeluaran
                    </a>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="card shadow-sm border-start border-4 border-success">
                <div class="card-body">
                    <span class="voucher-meta-label d-block mb-1">Total Pengeluaran</span>
                    <h3 class="fw-bold text-success mb-2">Rp {{ number_format($expense->amount, 0, ',', '.') }}</h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-label-success"><i class="mdi mdi-check-circle me-1"></i>Transaksi Sah</span>
                        <span class="text-muted small">Status: Lunas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-script')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#backButton').on('click', function() {
                if (document.referrer && document.referrer !== window.location.href) {
                    window.history.back();
                } else {
                    window.location.href = '{{ url('/expense') }}';
                }
            });

            $(document).on('click', '.delete-expense', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Hapus Pengeluaran Kas?",
                    text: "Saldo akun kas/bank akan dikembalikan otomatis dan catatan jurnal akan dihapus!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal",
                    customClass: {
                        confirmButton: "btn btn-danger me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: '{{ url('expense') }}/' + id,
                            type: 'POST',
                            data: {
                                '_method': 'DELETE',
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Berhasil Dihapus!",
                                        text: "Data pengeluaran telah berhasil dihapus.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    });
                                    window.setTimeout(function() {
                                        window.location.href = '{{ url('/expense') }}';
                                    }, 1500);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Gagal menghapus pengeluaran kas.'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan pada sistem saat menghapus data.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
