@extends('layouts.sales.app')
@section('title', 'Sampul — ' . ($invoice->no_invoice ?? '#' . $invoice->id))
@section('content')
    <div class="row invoice-preview">
        {{-- Sampul Preview --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body" style="margin-left: 20mm">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column mb-2 text-black"
                        style="margin-left: 20mm">
                        <div class="mb-xl-0 pb-1" style="border-bottom: 1px solid black; width:70%">
                            <div class="d-flex svg-illustration align-items-center gap-2">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png"
                                            alt="" srcset="" width="60%">
                                    </span>
                                </span>
                            </div>
                            <p class="mb-1 mx-2 fw-bolder text-black">PT Reftech Jaya Optima</p>
                            <p class="mb-1 mx-2 fw-bolder fs-tiny">
                                <span class="text-danger">Compressor</span> |
                                <span class="text-success">Sparepart</span> |
                                <span class="text-grey">Rental</span> |
                                <span class="text-info">Service</span>
                            </p>
                            <p class="mb-1 mx-2 fw-bolder fs-tiny" style="border-bottom: 1px solid black; width:fit-content;">
                                Office :</p>
                            <div class="mx-2" style="font-size: 10px">
                                <p class="mb-1 text-black">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                <p class="mb-1 text-black">Bandung – Jawa Barat 40218</p>
                                <p class="mb-1 text-black">
                                    <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                                    &nbsp;&nbsp;<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                                </p>
                            </div>
                        </div>
                        <div class="text-end">
                            <h1 class="fw-bold text-black m-2 p-2" style="border: 2px solid black;">Dokumen</h1>
                        </div>
                    </div>

                    <div class="from p-1 text-black"
                        style="border:2px solid black; border-radius:5px; width:25%; margin-left: 20mm">
                        <div class="row">
                            <div class="col-4 pr-0">
                                <p class="text-black">From :</p>
                            </div>
                            <div class="col-8 px-0">
                                <p class="mb-0">Rayi</p>
                                <p class="mb-0 fst-italic">Staff Accounting</p>
                            </div>
                        </div>
                    </div>

                    <div class="my-5"></div>
                    <div class="float-end text-black" id="info-cust"
                        style="border:3px solid black; border-radius:15px; width:40%; margin-top:150px">
                        <div class="row">
                            <div class="col-4 px-0">
                                <p class="mb-0 fw-semibold p-4 py-0 pt-1">TO</p>
                            </div>
                            <div class="col-8">
                                <p class="mb-0 fw-semibold pt-1">: {{ $quote->client?->company }}</p>
                            </div>
                            <div class="col-4 px-0">
                                <p class="mb-0 fw-semibold p-4 py-0">ALAMAT</p>
                            </div>
                            <div class="col-8">
                                @if ($invoice->invoiceTo == '1')
                                    <p class="mb-0">: {{ $quote->client?->address }}</p>
                                @else
                                    <p class="mb-0">: {{ $quote->client?->subAddress }}</p>
                                @endif
                            </div>
                            <div class="col-4 px-0">
                                <p class="mb-0 fw-semibold p-4 py-0">Attn.</p>
                            </div>
                            <div class="col-8">
                                <p class="mb-0">: {{ $quote->pic?->name_pic ?? $quote->attn }}</p>
                            </div>
                            <div class="col-4 px-0">
                                <p class="mb-0 fw-semibold p-4 py-0">Phone</p>
                            </div>
                            <div class="col-8">
                                <p class="mb-0">: {{ $quote->pic?->phone_pic ?? $quote->client?->phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="btn-group w-100 mb-3">
                        <a class="btn btn-primary waves-effect" target="_blank"
                            href="{{ route('invoice.unit.label_print', $invoice->id) }}">
                            Download
                        </a>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('invoice.show_unit.print', $invoice->id) }}" target="_blank">
                                    <i class="mdi mdi-file-document-outline me-1"></i> Invoice
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('invoice.unit.label_print', $invoice->id) }}" target="_blank">
                                    <i class="mdi mdi-package-variant-closed me-1"></i> Sampul
                                </a>
                            </li>
                        </ul>
                    </div>
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">Back</button>
                    <a href="{{ route('invoice.show_unit', $invoice->id) }}"
                       class="btn btn-outline-info d-grid w-100 waves-effect">View Invoice</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
@endpush
@push('script')
    <script>
        $('#backButton').click(function () { window.history.back(); });
    </script>
@endpush
