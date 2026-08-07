@extends('layouts.sales.app')
@section('title', 'Cashflow Statement Detail')
@section('content')
    @php
        $periodYear = \Carbon\Carbon::parse($startDate)->year;
        $printUrl = $month
            ? route('expense-cashflow.print-bulan', [$periodYear, $month])
            : route('expense-cashflow.print-tahun', [$periodYear]);
    @endphp

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Statement / <a href="{{ route('expense-cashflow.index') }}" class="text-muted">Cashflow Statement</a> /</span> Detail
            </h4>
            <p class="text-muted mb-0 small"><i class="mdi mdi-cash-sync me-1"></i> {{ $startString }} &ndash; {{ $endString }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('expense-cashflow.index') }}" class="btn btn-label-secondary">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
            <a href="{{ $printUrl }}" target="_blank" class="btn btn-primary shadow-sm">
                <i class="mdi mdi-printer-outline me-1"></i> Print
            </a>
        </div>
    </div>

    <div class="alert alert-warning d-flex align-items-start gap-2 mb-3" role="alert">
        <i class="mdi mdi-alert-outline fs-5 mt-1"></i>
        <div>
            <div class="fw-semibold">Laporan ini belum lengkap</div>
            <div class="small">
                Belum mencakup <strong>perubahan nilai persediaan</strong> dan <strong>saldo kas awal/akhir periode</strong>
                &mdash; menunggu modul ledger stok &amp; mutasi kas. Angka yang tampil adalah arus kas
                operasi/investasi/pendanaan yang sudah bisa dihitung, bukan saldo kas final.
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body report-preview">
            @include('pages.finance.cashflow._report')
        </div>
    </div>
@endsection

@push('after-style')
    <style>
        .report-preview .lvl-0 { margin-left: 0; font-weight: 800; }
        .report-preview .lvl-1 { margin-left: 20px; font-weight: 600; }
        .report-preview .lvl-2 { margin-left: 40px; }
        .report-preview .lvl-3 { margin-left: 60px; }
    </style>
@endpush
