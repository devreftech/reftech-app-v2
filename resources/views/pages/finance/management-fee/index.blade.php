@extends('layouts.sales.app')
@section('title', 'Management Fee - Finance')
@section('content')

<div class="container-fluid flex-grow-1 container-p-y px-4">
    {{-- Breadcrumb & Title --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold py-1 mb-1 text-dark">
                <i class="mdi mdi-cash-refund text-primary me-2"></i>Management Fee
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0" style="font-size: 12.5px;">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Finance</li>
                    <li class="breadcrumb-item active">Management Fee</li>
                </ol>
            </nav>
        </div>
        <div>
            <span class="badge bg-label-primary px-3 py-2 fw-semibold" style="font-size: 12px;">
                <i class="mdi mdi-shield-check-outline me-1"></i> Kebijakan Pajak Fee 2026 Aktif
            </span>
        </div>
    </div>

    {{-- Alert Banner Info Kebijakan 2026 --}}
    <div class="alert alert-primary py-2.5 px-3 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="mdi mdi-information-outline fs-4 flex-shrink-0 mt-0.5"></i>
            <div class="small w-100">
                <div class="fw-bold mb-1">Panduan Kebijakan Pajak Management Fee 2026:</div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-white text-dark border fw-medium px-2 py-1">&lt; Rp 1,5 Juta : <strong>Pajak 0%</strong> (Bebas Pajak)</span>
                    <span class="badge bg-white text-dark border fw-medium px-2 py-1">Rp 1,5 Juta - Rp 5 Juta : <strong>Pajak 3.68%</strong></span>
                    <span class="badge bg-white text-dark border fw-medium px-2 py-1">&gt; Rp 5 Juta : <strong>Pajak 10%</strong></span>
                    <span class="badge bg-warning text-dark border fw-semibold px-2 py-1"><i class="mdi mdi-lock-outline me-1"></i>Maksimal Total Fee: <strong>10% Pre-PPN</strong></span>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI / Summary Cards --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Gross Fee --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card border-0 shadow-sm overflow-hidden h-100" style="border-left: 4px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold" style="font-size: 12px;">Total Gross Fee</span>
                        <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-cash-minus fs-5"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">Rp {{ number_format($totalGrossFee, 0, ',', '.') }}</h5>
                    <span class="text-muted small" style="font-size: 11px;">Akumulasi fee pengurang omset</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Potongan Pajak PPh --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card border-0 shadow-sm overflow-hidden h-100" style="border-left: 4px solid #ff3e1d !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold" style="font-size: 12px;">Potongan Pajak (2026)</span>
                        <div class="avatar avatar-sm bg-label-danger rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-percent-outline fs-5"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1 text-danger">Rp {{ number_format($totalTaxDeduction, 0, ',', '.') }}</h5>
                    <span class="text-muted small" style="font-size: 11px;">Total withholding tax PPh fee</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Nett Fee Pending / Siap Ditransfer --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card border-0 shadow-sm overflow-hidden h-100" style="border-left: 4px solid #ffab00 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold" style="font-size: 12px;">Nett Fee Pending / Unpaid</span>
                        <div class="avatar avatar-sm bg-label-warning rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-clock-sand fs-5"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1 text-warning">Rp {{ number_format($totalPendingFee, 0, ',', '.') }}</h5>
                    <span class="text-muted small" style="font-size: 11px;">Menunggu proses transfer Finance</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Nett Fee Telah Ditransfer (Paid) --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card border-0 shadow-sm overflow-hidden h-100" style="border-left: 4px solid #71dd37 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold" style="font-size: 12px;">Nett Fee Telah Ditransfer</span>
                        <div class="avatar avatar-sm bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-check-decagram-outline fs-5"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1 text-success">Rp {{ number_format($totalPaidFee, 0, ',', '.') }}</h5>
                    <span class="text-muted small" style="font-size: 11px;">Berhasil dicairkan ke rekening</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Filter & Data Table Card --}}
    <div class="card border-0 shadow-sm overflow-hidden">
        {{-- Filter Header --}}
        <div class="card-header bg-light border-bottom py-3 px-4">
            <form action="{{ route('finance.management-fee.index') }}" method="GET" id="form-filter">
                <div class="row g-2 align-items-center">
                    {{-- Search Keyword --}}
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="input-group input-group-merge input-group-sm">
                            <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                placeholder="Cari No Quote, PO, Customer, Rekening...">
                        </div>
                    </div>

                    {{-- Status Fee --}}
                    <div class="col-lg-2 col-md-3 col-6">
                        <select class="form-select form-select-sm" name="fee_status" onchange="this.form.submit()">
                            <option value="all" {{ request('fee_status') == 'all' ? 'selected' : '' }}>Semua Status Fee</option>
                            <option value="unpaid" {{ request('fee_status') == 'unpaid' ? 'selected' : '' }}>🔴 Unpaid (Belum)</option>
                            <option value="pending_transfer" {{ request('fee_status') == 'pending_transfer' ? 'selected' : '' }}>🟡 Siap Ditransfer</option>
                            <option value="paid" {{ request('fee_status') == 'paid' ? 'selected' : '' }}>🟢 Paid (Sudah Ditransfer)</option>
                        </select>
                    </div>

                    {{-- Sales Person --}}
                    <div class="col-lg-2 col-md-3 col-6">
                        <select class="form-select form-select-sm" name="sales_id" onchange="this.form.submit()">
                            <option value="all">Semua Sales</option>
                            @foreach ($salesList as $s)
                                <option value="{{ $s->id }}" {{ request('sales_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Filter & Actions --}}
                    <div class="col-lg-4 col-md-12 col-12 d-flex gap-2">
                        <input type="date" class="form-control form-control-sm" name="start_date" value="{{ request('start_date') }}" title="Dari Tanggal">
                        <input type="date" class="form-control form-control-sm" name="end_date" value="{{ request('end_date') }}" title="Sampai Tanggal">
                        <button type="submit" class="btn btn-sm btn-primary px-2.5" title="Terapkan Filter">
                            <i class="mdi mdi-filter-variant"></i>
                        </button>
                        <a href="{{ route('finance.management-fee.index') }}" class="btn btn-sm btn-label-secondary px-2.5" title="Reset Filter">
                            <i class="mdi mdi-refresh"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table Data --}}
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                <thead class="table-light" style="font-size: 11.5px;">
                    <tr>
                        <th class="text-center" style="width: 4%;">No</th>
                        <th style="width: 16%;">Quotation &amp; PO</th>
                        <th style="width: 18%;">Customer &amp; Sales</th>
                        <th class="text-end" style="width: 12%;">Quote (Pre-PPN)</th>
                        <th class="text-end" style="width: 11%;">Gross Fee</th>
                        <th class="text-center" style="width: 12%;">Pajak (2026)</th>
                        <th class="text-end fw-bold" style="width: 12%;">Nett Transfer</th>
                        <th style="width: 15%;">Info Rekening</th>
                        <th class="text-center" style="width: 10%;">Status Pencairan</th>
                        <th class="text-center" style="width: 8%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = $items->firstItem() ?: 1; @endphp
                    @forelse ($items as $item)
                        @php
                            $taxData = $item->fee_tax_data;
                            $preTax = floatval($item->subtotal ?? 0) - floatval($item->diskon ?? 0);
                            if ($preTax <= 0) {
                                $preTax = floatval($item->total ?? 0) - floatval($item->tax_amount ?? 0);
                            }
                            $clientName = $item->client?->company ?: ($item->client?->name ?? 'Customer');
                        @endphp
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $no++ }}</td>
                            
                            {{-- Quotation & PO Info --}}
                            <td>
                                <a href="{{ route('unit-quotation.show', $item->id) }}" class="fw-bold text-primary text-decoration-none d-block">
                                    #{{ $item->no_quote }}
                                </a>
                                @if ($item->po_number)
                                    <span class="badge bg-label-success px-1.5 py-0.5 mt-0.5" style="font-size: 10px;">
                                        <i class="mdi mdi-clipboard-text-outline me-0.5"></i>PO: {{ $item->po_number }}
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary px-1.5 py-0.5 mt-0.5 text-uppercase" style="font-size: 9.5px;">
                                        {{ str_replace('_', ' ', $item->status) }}
                                    </span>
                                @endif
                                <div class="text-muted small" style="font-size: 10px;">
                                    {{ $item->date?->format('d/m/Y') }}
                                </div>
                            </td>

                            {{-- Customer & Sales --}}
                            <td>
                                <div class="fw-semibold text-dark text-truncate" style="max-width: 180px;" title="{{ $clientName }}">
                                    {{ $clientName }}
                                </div>
                                <div class="text-muted small" style="font-size: 11px;">
                                    <i class="mdi mdi-account-outline me-1"></i>{{ $item->sales?->name ?? '-' }}
                                </div>
                            </td>

                            {{-- Nilai Penawaran Pre-PPN --}}
                            <td class="text-end fw-semibold text-dark">
                                Rp {{ number_format($preTax, 0, ',', '.') }}
                            </td>

                            {{-- Gross Fee --}}
                            <td class="text-end text-danger fw-bold">
                                Rp {{ number_format($item->fee, 0, ',', '.') }}
                            </td>

                            {{-- Pajak Fee 2026 --}}
                            <td class="text-center">
                                @if ($taxData->tax_amount > 0)
                                    <span class="badge bg-label-danger px-2 py-1 fw-bold" style="font-size: 10.5px;">
                                        {{ $taxData->tax_rate_label }} (-Rp {{ number_format($taxData->tax_amount, 0, ',', '.') }})
                                    </span>
                                @else
                                    <span class="badge bg-label-success px-2 py-1" style="font-size: 10.5px;">
                                        0% (Bebas Pajak)
                                    </span>
                                @endif
                            </td>

                            {{-- Nett Transfer (Yang Diterima) --}}
                            <td class="text-end fw-bolder text-primary fs-6">
                                Rp {{ number_format($taxData->net_fee, 0, ',', '.') }}
                            </td>

                            {{-- Info Rekening --}}
                            <td>
                                @if ($item->fee_bank_account)
                                    <div class="fw-semibold text-dark" style="font-size: 11.5px;">
                                        <span class="badge bg-label-info px-1.5 py-0.5 me-1" style="font-size: 9.5px;">{{ $item->fee_bank_name ?: 'Bank' }}</span>
                                        {{ $item->fee_bank_account }}
                                    </div>
                                    <div class="text-muted small text-truncate" style="font-size: 11px; max-width: 160px;" title="{{ $item->fee_bank_holder }}">
                                        a.n {{ $item->fee_bank_holder ?: '-' }}
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic" style="font-size: 11px;">
                                        <i class="mdi mdi-alert-circle-outline text-warning me-0.5"></i>Rekening belum diisi
                                    </span>
                                @endif
                            </td>

                            {{-- Status Pencairan Fee --}}
                            <td class="text-center">
                                @if ($item->fee_payment_status === 'paid')
                                    <span class="badge bg-success px-2.5 py-1 text-uppercase fw-bold" style="font-size: 10.5px;">
                                        <i class="mdi mdi-check me-0.5"></i> Paid
                                    </span>
                                    @if ($item->fee_transfer_date)
                                        <div class="text-muted small" style="font-size: 10px;">
                                            {{ $item->fee_transfer_date->format('d/m/Y') }}
                                        </div>
                                    @endif
                                @elseif ($item->fee_payment_status === 'pending_transfer')
                                    <span class="badge bg-warning text-dark px-2.5 py-1 text-uppercase fw-bold" style="font-size: 10.5px;">
                                        <i class="mdi mdi-clock-outline me-0.5"></i> Siap Transfer
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary px-2.5 py-1 text-uppercase fw-semibold" style="font-size: 10.5px;">
                                        Unpaid
                                    </span>
                                @endif
                            </td>

                            {{-- Action Buttons --}}
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    {{-- Tombol Proses Transfer / Rekening Modal --}}
                                    <button type="button" class="btn btn-icon btn-sm btn-label-primary btn-open-disbursement"
                                        title="Kelola Rekening & Status Transfer"
                                        data-id="{{ $item->id }}"
                                        data-no-quote="{{ $item->no_quote }}"
                                        data-client="{{ $clientName }}"
                                        data-sales="{{ $item->sales?->name ?? '-' }}"
                                        data-pretax="Rp {{ number_format($preTax, 0, ',', '.') }}"
                                        data-gross="Rp {{ number_format($item->fee, 0, ',', '.') }}"
                                        data-tax-label="{{ $taxData->tax_rate_label }}"
                                        data-tax-amount="Rp {{ number_format($taxData->tax_amount, 0, ',', '.') }}"
                                        data-net="Rp {{ number_format($taxData->net_fee, 0, ',', '.') }}"
                                        data-bank-name="{{ $item->fee_bank_name }}"
                                        data-bank-account="{{ $item->fee_bank_account }}"
                                        data-bank-holder="{{ $item->fee_bank_holder }}"
                                        data-payment-status="{{ $item->fee_payment_status ?: 'unpaid' }}"
                                        data-transfer-date="{{ $item->fee_transfer_date ? $item->fee_transfer_date->format('Y-m-d') : '' }}"
                                        data-transfer-note="{{ $item->fee_transfer_note }}"
                                        data-proof-url="{{ $item->fee_transfer_proof ? \Illuminate\Support\Facades\Storage::url($item->fee_transfer_proof) : '' }}"
                                        data-action-url="{{ route('finance.management-fee.update-disbursement', $item->id) }}">
                                        <i class="mdi mdi-cash-fast"></i>
                                    </button>

                                    {{-- Lihat Bukti Transfer --}}
                                    @if ($item->fee_transfer_proof)
                                        <a href="{{ \Illuminate\Support\Facades\Storage::url($item->fee_transfer_proof) }}" target="_blank"
                                            class="btn btn-icon btn-sm btn-label-success" title="Lihat Bukti Transfer">
                                            <i class="mdi mdi-file-document-check-outline"></i>
                                        </a>
                                    @endif

                                    {{-- Detail Smart Quote --}}
                                    <a href="{{ route('unit-quotation.show', $item->id) }}" target="_blank"
                                        class="btn btn-icon btn-sm btn-label-secondary" title="Buka Detail Smart Quote">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="mdi mdi-cash-remove text-muted opacity-50 mb-2" style="font-size: 48px;"></i>
                                    <h6 class="fw-bold mb-1 text-dark">Tidak Ada Data Management Fee</h6>
                                    <p class="small text-muted mb-0">Belum ada penawaran dengan catatan management fee yang sesuai filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        @if ($items->hasPages())
            <div class="card-footer bg-light border-top py-2 px-4 d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    Menampilkan {{ $items->firstItem() }} s/d {{ $items->lastItem() }} dari total {{ $items->total() }} data
                </span>
                <div>
                    {{ $items->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal Proses Pencairan & Transfer Management Fee --}}
<div class="modal fade" id="modalDisbursement" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-cash-fast fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Proses Pencairan Management Fee</h5>
                        <span class="text-muted small" style="font-size: 11px;" id="modal-disburse-quote-title">#Quote</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-disbursement" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-3 p-md-4">
                    {{-- Detail Nominal & Pajak Fee 2026 Summary --}}
                    <div class="p-3 rounded-3 bg-light border mb-4">
                        <div class="row g-2 text-center align-items-center">
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block" style="font-size: 10px;">Nilai Quote (Pre-PPN)</span>
                                <span class="fw-bold text-dark" id="modal-d-pretax" style="font-size: 12.5px;">-</span>
                            </div>
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block" style="font-size: 10px;">Gross Fee (Diputuskan)</span>
                                <span class="fw-bold text-danger" id="modal-d-gross" style="font-size: 12.5px;">-</span>
                            </div>
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block" style="font-size: 10px;" id="modal-d-tax-label">Pajak Fee (2026)</span>
                                <span class="fw-bold text-danger" id="modal-d-tax-amount" style="font-size: 12.5px;">-</span>
                            </div>
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block" style="font-size: 10px;">Nett Fee yang Ditransfer</span>
                                <span class="fw-bolder text-primary fs-6" id="modal-d-net">-</span>
                            </div>
                        </div>
                    </div>

                    {{-- Form Data Rekening --}}
                    <h6 class="fw-bold text-dark mb-2" style="font-size: 12.5px;">
                        <i class="mdi mdi-bank-outline me-1 text-primary"></i> Data Rekening Tujuan Penerima Fee
                    </h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold" for="input_fee_bank_name">Nama Bank</label>
                            <input type="text" class="form-control form-control-sm" id="input_fee_bank_name" name="fee_bank_name"
                                placeholder="Contoh: BCA, Mandiri, BRI, BNI">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold" for="input_fee_bank_account">Nomor Rekening</label>
                            <input type="text" class="form-control form-control-sm" id="input_fee_bank_account" name="fee_bank_account"
                                placeholder="Contoh: 1234567890">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold" for="input_fee_bank_holder">Nama Pemilik Rekening (A/N)</label>
                            <input type="text" class="form-control form-control-sm" id="input_fee_bank_holder" name="fee_bank_holder"
                                placeholder="Contoh: John Doe">
                        </div>
                    </div>

                    <div class="divider my-3">
                        <div class="divider-text text-muted small" style="font-size: 11px;">Status &amp; Eksekusi Transfer</div>
                    </div>

                    {{-- Form Status Transfer & Upload Bukti --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" for="input_fee_payment_status">Status Pencairan Fee <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="input_fee_payment_status" name="fee_payment_status" required>
                                <option value="unpaid">🔴 Belum Ditransfer (Unpaid)</option>
                                <option value="pending_transfer">🟡 Siap Ditransfer (Pending Action)</option>
                                <option value="paid">🟢 Sudah Ditransfer (Paid)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" for="input_fee_transfer_date">Tanggal Transfer</label>
                            <input type="date" class="form-control form-control-sm" id="input_fee_transfer_date" name="fee_transfer_date"
                                value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold" for="input_fee_transfer_proof">Upload Bukti Transfer <span class="text-muted">(JPG, PNG, PDF maks 5MB)</span></label>
                            <input type="file" class="form-control form-control-sm" id="input_fee_transfer_proof" name="fee_transfer_proof" accept="image/*,.pdf">
                            <div id="proof-preview-wrap" class="mt-1 d-none">
                                <a href="#" target="_blank" id="proof-preview-link" class="small fw-semibold text-primary">
                                    <i class="mdi mdi-paperclip me-0.5"></i> Lihat Bukti Transfer Saat Ini
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold" for="input_fee_transfer_note">Catatan / Nomor Referensi Transfer <span class="text-muted">(Opsional)</span></label>
                        <textarea class="form-control form-control-sm" id="input_fee_transfer_note" name="fee_transfer_note" rows="2"
                            placeholder="Contoh: No ref transfer bank #TRX123456789 atau catatan transfer..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Status Pencairan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        $(document).on('click', '.btn-open-disbursement', function () {
            var btn = $(this);
            var id = btn.data('id');
            var noQuote = btn.data('no-quote');
            var client = btn.data('client');
            var sales = btn.data('sales');
            var pretax = btn.data('pretax');
            var gross = btn.data('gross');
            var taxLabel = btn.data('tax-label');
            var taxAmount = btn.data('tax-amount');
            var net = btn.data('net');
            var bankName = btn.data('bank-name');
            var bankAccount = btn.data('bank-account');
            var bankHolder = btn.data('bank-holder');
            var paymentStatus = btn.data('payment-status');
            var transferDate = btn.data('transfer-date');
            var transferNote = btn.data('transfer-note');
            var proofUrl = btn.data('proof-url');
            var actionUrl = btn.data('action-url');

            $('#form-disbursement').attr('action', actionUrl);
            $('#modal-disburse-quote-title').text('#' + noQuote + ' — ' + client + ' (Sales: ' + sales + ')');
            $('#modal-d-pretax').text(pretax);
            $('#modal-d-gross').text(gross);
            $('#modal-d-tax-label').text('Pajak Fee (' + taxLabel + ')');
            $('#modal-d-tax-amount').text(taxAmount > 0 ? '- ' + taxAmount : 'Rp 0');
            $('#modal-d-net').text(net);

            $('#input_fee_bank_name').val(bankName);
            $('#input_fee_bank_account').val(bankAccount);
            $('#input_fee_bank_holder').val(bankHolder);
            $('#input_fee_payment_status').val(paymentStatus || 'unpaid');
            $('#input_fee_transfer_date').val(transferDate || '{{ date("Y-m-d") }}');
            $('#input_fee_transfer_note').val(transferNote);

            if (proofUrl) {
                $('#proof-preview-wrap').removeClass('d-none');
                $('#proof-preview-link').attr('href', proofUrl);
            } else {
                $('#proof-preview-wrap').addClass('d-none');
            }

            var modal = new bootstrap.Modal(document.getElementById('modalDisbursement'));
            modal.show();
        });
    });
</script>
@endpush

@endsection
