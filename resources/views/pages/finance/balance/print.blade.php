@extends('layouts.sales.app')
@section('title', 'Print Opname')
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y" style="max-width: 900px;">

        {{-- Toolbar (hidden on print) --}}
        <div class="d-flex justify-content-end gap-2 mb-4 no-print">
            <button type="button" class="btn btn-label-secondary btn-sm" onclick="window.close()">
                <i class="mdi mdi-close me-1"></i> Close
            </button>
            <button type="button" class="btn btn-primary btn-sm shadow-sm" onclick="window.print()">
                <i class="mdi mdi-printer-outline me-1"></i> Print
            </button>
        </div>

        @include('pages.finance.balance._report')

        <p class="text-muted small mt-4 mb-0 no-print">Dicetak pada {{ now()->translatedFormat('j F Y, H:i') }}</p>
    </div>
</div>
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print-income.css" />
    <style>
        .invoice-print .lvl-0 { font-size: 1rem; }
        .invoice-print table td, .invoice-print table th { padding: .5rem .75rem; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
