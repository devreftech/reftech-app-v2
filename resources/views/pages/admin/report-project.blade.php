@extends('layouts.sales.app')
@section('title', 'Report Project')
@section('content')
    @php
        $bulanMap = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];
    @endphp

    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-label-info fs-6 px-3 py-2">
                    <i class="mdi mdi-briefcase-outline me-1"></i> Project
                </span>
                <span class="text-muted fw-semibold">{{ $year }}</span>
            </div>
            <h4 class="fw-bold mb-1 text-heading">Report Quotation — Admin / Sales Manager</h4>
            <p class="text-muted mb-0 small">Akumulasi quotation & unit quotation yang dibuat langsung oleh Admin / Sales Manager (di luar quotation milik tim Sales reguler).</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('quotation.index') }}" class="btn btn-label-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
            <div class="dropdown">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="mdi mdi-calendar-text me-1"></i> {{ $year }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @foreach ($yearList as $yr)
                        <li>
                            <a class="dropdown-item waves-effect {{ $yr == $year ? 'active' : '' }}"
                                href="{{ route('report.project', $yr) }}">{{ $yr }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-3">
                    <span class="text-muted fw-semibold small text-uppercase">Quotation Aktif</span>
                    <h4 class="mb-0 fw-bold text-dark mt-1">{{ $quoteCount }}</h4>
                    <div class="text-muted small">Rp {{ number_format($quoteTotal, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-3">
                    <span class="text-muted fw-semibold small text-uppercase">PO Achieved</span>
                    <h4 class="mb-0 fw-bold text-success mt-1">{{ $poCount }}</h4>
                    <div class="text-muted small">Rp {{ number_format($poTotal, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-3">
                    <span class="text-muted fw-semibold small text-uppercase">Loss</span>
                    <h4 class="mb-0 fw-bold text-danger mt-1">{{ $lossCount }}</h4>
                    <div class="text-muted small">Rp {{ number_format($lossTotal, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-3">
                    <span class="text-muted fw-semibold small text-uppercase">Total PO / Orang</span>
                    <h4 class="mb-0 fw-bold text-dark mt-1">{{ count($data) }}</h4>
                    <div class="text-muted small">Admin / Sales Manager aktif</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly accumulation per manager --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3">
            <h6 class="card-title mb-0 fw-bold text-dark">
                <i class="mdi mdi-chart-bar me-2 text-primary"></i> Akumulasi PO Achieved per Bulan
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="min-width:170px">Nama</th>
                        @for ($m = 1; $m <= 12; $m++)
                            <th class="text-center">{{ $bulanMap[$m] }}</th>
                        @endfor
                        <th class="text-center">Total</th>
                        <th class="text-center">Quotation Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $row['image'] ? url('') . '/' . $row['image'] : asset('assets/img/avatars/1.png') }}"
                                        class="rounded-circle" width="30" height="30" style="object-fit:cover;">
                                    <div>
                                        <div class="fw-semibold">{{ $row['name'] }}</div>
                                        <span class="badge bg-label-secondary" style="font-size:0.65rem">{{ $row['role'] }}</span>
                                    </div>
                                </div>
                            </td>
                            @for ($m = 1; $m <= 12; $m++)
                                <td class="text-end text-nowrap small">
                                    {{ $row['jumlah'][$m] > 0 ? number_format($row['jumlah'][$m], 0, ',', '.') : '—' }}
                                </td>
                            @endfor
                            <td class="text-end text-nowrap fw-bold">{{ number_format($row['total'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ $row['quoteCount'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center text-muted py-4">Belum ada data untuk tahun ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
