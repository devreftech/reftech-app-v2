@extends('layouts.sales.app')
@section('title', 'Detail Audit Tools')
@section('content')
    <div class="row invoice-preview">
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                        <div class="mb-xl-0 pb-3">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img class="text-md" src="{{ asset('assets') }}/img/favicon/logo-reftech1.png"
                                            alt="" srcset="">
                                    </span>
                                </span>
                                <span class="h4 mb-0 app-brand-text fw-bold fs-2">PT REFTECH JAYA OPTIMA</span>
                            </div>
                            <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 27</p>
                            <p class="mb-1">Bandung – Jawa Barat 40218</p>
                            <p>
                                <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1"></i>022 54417653
                            </p>
                        </div>
                        <div>
                            <h3 class="fw-bold">AUDIT TOOLS</h3>
                            <div>
                                <span class="fw-bolder">#{{ $audit->no_audit }}</span>
                            </div>
                            <div class="mt-1">
                                <span
                                    class="text-muted">{{ $audit->status }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="card-body mb-3">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 my-3">
                            <h6 class="pb-2 fw-semibold fs-4">Tools :</h6>
                        </div>
                        <div class="col-md-6 my-3">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2 fw-medium">
                            <p class="mb-1">Name </p>
                            <p class="mb-1">Code </p>
                            <p class="mb-1">Phone </p>
                        </div>
                        <div class="col-4">
                            <p class="mb-1">: {{ $audit->technician->name }}</p>
                            <p class="mb-1">: {{ $audit->technician->code }}</p>
                            <p class="mb-1">: {{ $audit->technician->phone }}</p>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead class="table-light border-top">
                            <tr>
                                <th>No.</th>
                                <th>Tools</th>
                                <th>Qty</th>
                                <th>Description</th>
                                <th>Assesment</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 0;
                            @endphp
                            @foreach ($tools as $tool)
                                @php
                                    $no++;
                                @endphp
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td class="text-nowrap">{{ $tool->tools }}</td>
                                    <td>{{ $tool->qty }}</td>
                                    <td>{{ $tool->desc }}</td>
                                    <td>{{ $tool->assesment }}</td>
                                    <td>{{ $tool->note }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-body">
                    <h5 class="my-4">Terms & Conditions</h5>
                    <div class="row">
                        <div class="col-3 fw-medium">
                            <p class="mb-1">Note </p>
                        </div>
                        <div class="col">
                            <p class="mb-1">: {{ $audit->note }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="{{route('audit-tools.print', $audit->id)}}">
                        Print
                    </a>
                    <a href="#" type="button"
                        class="btn btn-outline-secondary d-grid w-100 waves-effect">Download</a>
                </div>
            </div>
        </div>
        {{-- End : Button Invoice --}}
    </div>
@endsection
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
@endpush
