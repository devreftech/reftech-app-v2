@extends('layouts.sales.app')
@section('title', $invoice->no_invoice)
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="header">
        </div>
        <div class="content">
            <div class="title">
            </div>
            <div class="table-responsive text-nowrap">
            </div>
            <div class="mb-2">
            </div>
            <div class="amount">
            </div>
            <div class="row mt-4">
            </div>
        </div>
    </div>
</div>
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print-header.css" />
    <link rel="stylesheet" href="style.css">
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
@push('script')
    <script>
        $(document).ready(function() {
            // Ambil tinggi dari elemen <pre>
            var preHeight = $('#notePre').outerHeight();
            // Atur tinggi elemen <p> menjadi sama dengan tinggi elemen <pre>
            $('#noteParagraph').css('height', preHeight + 'px');
        });
    </script>
@endpush
