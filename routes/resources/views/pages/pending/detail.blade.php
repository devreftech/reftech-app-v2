@extends('layouts.sales.app')
@section('title', 'Detail Sales Order')
@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h5 class="fw-bold pb-1 mb-3">
            Detail Of {{ $invoice->no_po ?? $quotation->pic->client->company }}
        </h5>
        <div class="tombol">
            @if ($pending->status != '6' && $pending->status != '8' && $pending->status != '9')
                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light"
                    data-bs-toggle="dropdown" aria-expanded="false" {{ auth::user()->role != 'Sales' ? '' : 'disabled' }}>
                    Update
                </button>
                <ul class="dropdown-menu" style="">
                    <li><a class="dropdown-item waves-effect" href="javascript:void(0);" data-bs-toggle="modal"
                            data-bs-target="#deliveryEdit">Kurir</a></li>
                    <li><a class="dropdown-item waves-effect" href="javascript:void(0);" data-bs-toggle="modal"
                            data-bs-target="#statusEdit">Pending PO</a></li>
                    <li><a class="dropdown-item waves-effect" href="javascript:void(0);" data-bs-toggle="modal"
                            data-bs-target="#resiEdit">Upload Resi</a></li>
                </ul>
            @elseif ($pending->status == '6')
                @if ($pending->id_product_out == null)
                    <button type="button" class="btn btn-reddit" data-bs-toggle="modal" data-bs-target="#inputProductOut"
                        {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                        Connect Product Out
                    </button>
                @else
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productReturn"
                        {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                        Retur Barang
                    </button>
                @endif
            @elseif ($pending->status == '9')
                <button type="button" class="btn btn-primary done-po" data-id="{{ $pending->id }}"
                    {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                    Done
                </button>
                <button type="button" class="btn btn-reddit" data-bs-toggle="modal" data-bs-target="#inputProductOut"
                    {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                    Connect Product Out
                </button>
            @endif
            <a href="{{ route('pending-po.index') }}" type="button" class="btn btn-secondary"> Back </a>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">Sales</div>
                        <div class="col-8">: {{ $quotation->sales->name }}</div>
                        <div class="col-4">Flag</div>
                        <div class="col-8">: {{ $quotation->pic->client->info }}</div>
                        <div class="col-4">Client</div>
                        <div class="col-8">: {{ $quotation->pic->client->company }}</div>
                        <div class="col-4">PIC</div>
                        <div class="col-8">: {{ $quotation->pic->name_pic }}</div>
                        <div class="col-4">Address</div>
                        <div class="col-8">: {{ $quotation->pic->client->address }}</div>
                        @php
                            switch ($pending->delivery) {
                                case 1:
                                    $delivery = 'Kurir';
                                    break;
                                case 2:
                                    $delivery = 'Teknisi';
                                    break;
                                case 3:
                                    $delivery = 'Direct';
                                    break;
                                case 4:
                                    $delivery = 'Other';
                                    break;
                                default:
                                    $delivery = 'Error';
                                    break;
                            }
                            switch ($pending->charged) {
                                case 1:
                                    $charged = 'Company';
                                    break;
                                case 2:
                                    $charged = 'Customer';
                                    break;
                                default:
                                    $charged = '';
                                    break;
                            }
                        @endphp
                        <div class="col-4">Kurir</div>
                        <div class="col-8">
                            : {{ $delivery }} {{ $pending->charged ? "($charged)" : '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">No Quotation</div>
                        @php
                            if ($quotation->type == 'Sparepart') {
                                $link = 'quotation.show';
                            } elseif ($quotation->type == 'Overhaul') {
                                $link = 'show-overhaul.quotation';
                            } else {
                                $link = 'show-service.quotation';
                            }

                        @endphp
                        <div class="col-8">: <a class="text-dark cursor-pointer"
                                href="{{ route($link, $quotation->id) }}">{{ $quotation->no_quote }}</a>
                        </div>
                        <div class="col-4">No Invoice</div>
                        <div class="col-8">:
                            @if (@$invoice->no_invoice)
                                <a class="text-dark cursor-pointer" href="{{ route('invoice.show', $invoice->id) }}">
                                    {{ $invoice->no_invoice }}
                                </a>
                            @else
                                Belum ada invoice
                            @endif
                        </div>
                        <div class="col-4">Payment Info</div>
                        <div class="col-8">:
                            {{ $invoice ? ($invoice->status_p == 1 ? 'Payment Confirmed' : 'Unpaid') : 'Belum ada invoice' }}
                        </div>
                        <div class="col-4">PO Date</div>
                        <div class="col-8">: {{ \Carbon\Carbon::parse($quotation->po_date)->format('d-m-Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="row">
                <div class="col-4 mb-2">
                    <div class="card bg-label-secondary">
                        <div class="card-body p-2 d-flex justify-content-center align-items-center">
                            <h5 class="m-0">Date</h5>
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="card">
                        <div class="card-body p-2 d-flex justify-content-center align-items-center">
                            <h6 class="m-0">{{ $resi->date ?? '-' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-4 mb-2">
                    <div class="card bg-label-secondary">
                        <div class="card-body p-2 d-flex justify-content-center align-items-center">
                            <h5 class="m-0">Cargo</h5>
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="card">
                        <div class="card-body p-2 d-flex justify-content-center align-items-center">
                            <h6 class="m-0">{{ $resi->kurir ?? '-' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-4 mb-2">
                    <div class="card bg-label-secondary">
                        <div class="card-body p-2 d-flex justify-content-center align-items-center">
                            <h5 class="m-0">No Resi</h5>
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="card">
                        <div class="card-body p-2 d-flex justify-content-center align-items-center">
                            <h6 class="m-0">{{ $resi->no_track ?? '-' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-4 mb-2">
                    <div class="card bg-label-secondary">
                        <div class="card-body p-2 d-flex justify-content-center align-items-center">
                            <h5 class="m-0">Cost</h5>
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="card">
                        <div class="card-body p-2 d-flex justify-content-center align-items-center">
                            <h6 class="m-0">Rp {{ number_format($resi->cost ?? 0, 0, '.', ',') }}</h6>
                        </div>
                    </div>
                </div>
                @if (@$resi->image != null)
                    <div class="col-8">
                        <a href="{{ url($resi->image) }}" class="btn btn-sm btn-primary d-grid w-100 waves-effect"
                            target="_blank" {{ $resi->image == null ? 'Disabled' : '' }}>
                            Image
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="#" data-id="{{ $resi->id }}" data-pending="{{ $pending->id }}"
                            class="btn btn-label-danger delete-resi waves-effect w-100"
                            {{ $resi->image == null ? 'Disabled' : '' }}>
                            <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline m-0"></i>
                        </a>
                    </div>
                @else
                    <div class="col-12">
                        <button class="btn btn-primary waves-effect w-100" disabled>
                            Belum Ada Resi
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($pending->type == 'Project')
        @if ($pending->status != '6' && $pending->status != '8')
            <div class="mb-3" style="display: flex; justify-content: flex-end;">
                {{-- <button type="button" class="btn btn-facebook float-end" data-bs-toggle="modal"
                    data-bs-target="#replacementEdit" {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                    Update Status Barang
                </button> --}}
                <div class="" style="display: flex; justify-content: flex-end;">
                    <button type="button" class="btn btn-google-plus float-end mx-2" data-bs-toggle="modal"
                        data-bs-target="#purchaseReqPrj" {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                        Purchase Request
                    </button>
                    <button type="button" class="btn btn-facebook float-end" data-bs-toggle="modal"
                        data-bs-target="#replacementEdit" {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                        Update Status Barang
                    </button>
                </div>
            </div>
        @endif
        <div class="card mb-4">
            <div class="table-responsive text-nowrap h-100">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Item</th>
                            <th>Desc</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @php
                            $abjad = 64;
                        @endphp
                        @foreach ($subQuote as $subJudul)
                            @php
                                $no = 0;
                                $abjad++;
                            @endphp
                            <tr style="font-size: 17px border-bottom:none !important;" class="border-top">
                                <td class="align-top" style="border-bottom:none !important; background-color: #f0f0f0;">
                                    <p class="fw-bold mb-0">{{ chr($abjad) }}</p>
                                </td>
                                <td class="text-nowrap align-top" colspan="5"
                                    style="border-bottom:none !important; background-color: #f0f0f0;">
                                    <p class="fw-bold mb-0">{{ $subJudul->subtitle }}</p>
                                </td>
                            </tr>
                            @foreach ($subJudul->detail as $product)
                                @php
                                    switch ($product->pending[0]->status) {
                                        case 1:
                                            $status = 'On Check';
                                            break;
                                        case 2:
                                            $status = 'Ready Stock';
                                            break;
                                        case 3:
                                            $status = 'Kurang';
                                            break;
                                        case 4:
                                            $status = 'Pre-Order';
                                            break;
                                        case 5:
                                            $status = 'Delivery Process';
                                            break;
                                        case 6:
                                            $status = 'Done';
                                            break;
                                        default:
                                            $status = 'Belum Di Cek';
                                            break;
                                    }
                                @endphp
                                <tr style="font-size: 15px;">
                                    <td>
                                        @php
                                            $no++;
                                        @endphp
                                        <p class="mb-1">{{ $no }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-1">{{ $product->product }}</p>
                                    </td>
                                    <td>
                                        @if ($product->detail != '-')
                                            <pre class="mb-0"
                                                style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail }}</pre>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $product->qty }} {{ $product->info_qty }}</p>
                                    </td>
                                    <td>{{ $pending->status == '6' ? 'Done' : $status }}</td>
                                    <td>{{ $pending->status == '6' ? 'Done' : $product->pending[0]->note }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="fw-medium card-title mb-3">
                    Purchase Request
                </h4>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Note</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @php
                            $no = 1;
                        @endphp
                        @forelse ($purchase as $pr)
                            @php
                                switch ($pr->status) {
                                    case 1:
                                        $color_pr = 'text-primary';
                                        $status_pr = 'Sudah di ACC';
                                        break;
                                    case 2:
                                        $color_pr = 'text-warning';
                                        $status_pr = 'Dalam Pengiriman';
                                        break;
                                    case 3:
                                        $color_pr = 'text-success';
                                        $status_pr = 'Done';
                                        break;
                                    default:
                                        $color_pr = 'text-secondary';
                                        $status_pr = 'Belum Di ACC';
                                        break;
                                }
                            @endphp
                            <tr>
                                <td>{{ $no }}</td>
                                <td>
                                    @if ($pr->id_equivalent == '0')
                                        -
                                    @else
                                        {{ $pr->equivalent->brand }} {{ $pr->equivalent->pn }}
                                    @endif
                                </td>
                                {{-- <td>
                                    <pre class="mb-0"
                                        style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $item->detail_product }}</pre>
                                </td> --}}
                                <td>{{ $pr->qty }} {{ $pr->equivalent->product->unit }}</td>
                                {{-- <td>{{ $pr->qty }}</td> --}}
                                <td>{{ $pr->note }}</td>
                                <td class="{{ $color_pr }}">{{ $status_pr }}</td>
                                <td>
                                    <a href="#" data-id="{{ $pr->id }}" data-pending="{{ $pending->id }}"
                                        class="btn btn-sm btn-label-danger delete-request m-2">
                                        <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline m-0"></i>
                                    </a>
                                    {{-- <a type="button" href="#" data-bs-toggle="modal"
                                        data-bs-target="#editMachine-534" data-id="534"
                                        class="btn btn-sm btn-label-primary">
                                        <i class="menu-icon tf-icons mdi mdi-14px mdi-note-edit-outline m-0"></i>
                                    </a>
                                    <a href="http://127.0.0.1:8000/monitoring/daily-create/534"
                                        class="btn btn-sm btn-label-warning m-2">
                                        <i class="menu-icon tf-icons mdi mdi-14px mdi-import m-0"></i>
                                    </a>
                                    <a href="http://127.0.0.1:8000/monitoring/daily-visit/534"
                                        class="btn btn-sm btn-label-success m-2">
                                        <i class="menu-icon tf-icons mdi mdi-14px mdi-eye-outline m-0"></i>
                                    </a> --}}
                                </td>
                            </tr>
                            @php
                                $no++;
                            @endphp
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak Ada Purchase Request</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        @if ($pending->status != '6' && $pending->status != '8')
            <div class="mb-3" style="display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-google-plus float-end mx-2" data-bs-toggle="modal"
                    data-bs-target="#purchaseReq" {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                    Purchase Request
                </button>
                <button type="button" class="btn btn-facebook float-end" data-bs-toggle="modal"
                    data-bs-target="#productEdit" {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                    Update Status Barang
                </button>
            </div>
        @endif
        <div class="card mb-4">
            <div class="table-responsive text-nowrap h-100">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Item</th>
                            <th>Desc</th>
                            <th>G/R</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($detQuotation as $item)
                            @php
                                switch ($item->status) {
                                    case 1:
                                        $status = 'On Check';
                                        break;
                                    case 2:
                                        $status = 'Ready Stock';
                                        break;
                                    case 3:
                                        $status = 'Kurang';
                                        break;
                                    case 4:
                                        $status = 'Pre-Order';
                                        break;
                                    case 5:
                                        $status = 'Delivery Process';
                                        break;
                                    case 6:
                                        $status = 'Done';
                                        break;
                                    default:
                                        $status = 'Belum Di Cek';
                                        break;
                                }
                            @endphp
                            <tr>
                                <td>{{ $no }}</td>
                                <td>
                                    @if ($item->id_equivalent == '0')
                                        -
                                    @else
                                        {{ $item->equivalent->brand }} {{ $item->equivalent->pn }}
                                    @endif
                                </td>
                                <td>
                                    <pre class="mb-0"
                                        style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $item->detail_product }}</pre>
                                </td>
                                <td>{{ $item->equivalent->product->go ?? '-' }}</td>
                                <td>{{ $item->qty }} {{ $item->info_qty }}</td>
                                <td>{{ $pending->status == '6' ? 'Done' : $status }}</td>
                                <td> {{ $pending->status == '6' ? 'Done' : $item->note }}</td>
                            </tr>
                            @php
                                $no++;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="fw-medium card-title mb-3">
                    Purchase Request
                </h4>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Note</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @php
                            $no = 1;
                        @endphp
                        @forelse ($purchase as $pr)
                            @php
                                switch ($pr->status) {
                                    case 1:
                                        $status_pr = 'Sudah di ACC';
                                        break;
                                    case 2:
                                        $status_pr = 'Dalam Pengiriman';
                                        break;
                                    case 3:
                                        $status_pr = 'Done';
                                        break;
                                    default:
                                        $status_pr = 'Belum Di ACC';
                                        break;
                                }
                            @endphp
                            <tr>
                                <td>{{ $no }}</td>
                                <td>
                                    @if ($pr->id_equivalent == '0')
                                        -
                                    @else
                                        {{ $pr->equivalent->brand }} {{ $pr->equivalent->pn }}
                                    @endif
                                </td>
                                {{-- <td>
                                    <pre class="mb-0"
                                        style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $item->detail_product }}</pre>
                                </td> --}}
                                <td>{{ $pr->qty }} {{ $pr->equivalent->product->unit }}</td>
                                {{-- <td>{{ $pr->qty }}</td> --}}
                                <td>{{ $pr->note }}</td>
                                <td>{{ $status_pr }}</td>
                                <td>
                                    <a href="#" data-id="{{ $pr->id }}" data-pending="{{ $pending->id }}"
                                        class="btn btn-sm btn-label-danger delete-request m-2">
                                        <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline m-0"></i>
                                    </a>
                                    {{-- <a type="button" href="#" data-bs-toggle="modal"
                                        data-bs-target="#editMachine-534" data-id="534"
                                        class="btn btn-sm btn-label-primary">
                                        <i class="menu-icon tf-icons mdi mdi-14px mdi-note-edit-outline m-0"></i>
                                    </a>
                                    <a href="http://127.0.0.1:8000/monitoring/daily-create/534"
                                        class="btn btn-sm btn-label-warning m-2">
                                        <i class="menu-icon tf-icons mdi mdi-14px mdi-import m-0"></i>
                                    </a>
                                    <a href="http://127.0.0.1:8000/monitoring/daily-visit/534"
                                        class="btn btn-sm btn-label-success m-2">
                                        <i class="menu-icon tf-icons mdi mdi-14px mdi-eye-outline m-0"></i>
                                    </a> --}}
                                </td>
                            </tr>
                            @php
                                $no++;
                            @endphp
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak Ada Purchase Request</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">

                {{-- Header Action --}}
                <div class="d-flex justify-content-end mb-3">
                    <a href="#" class="btn btn-primary waves-effect clear-return" data-id="{{ $pending->id }}">
                        Clear Return
                    </a>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>No Return</th>
                                <th>No Do</th>
                                <th>Date</th>
                                <th>Date Done</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 0; @endphp
                            @forelse ($return as $retur)
                                @php $no++; @endphp
                                <tr style="font-size: 13px">
                                    <td class="align-top">{{ $no }}</td>
                                    <td class="text-nowrap align-top">
                                        <a href="{{ route('return.show', $retur->id) }}" class="mb-0 text-dark fw-semibold">
                                            {{ $retur->no_return }}
                                        </a>
                                    </td>
                                    <td class="text-nowrap align-top">
                                        <p class="mb-0">
                                            {{ $retur->product_in->no_do ?? 'Belum Ada Product In' }}
                                        </p>
                                    </td>
                                    <td class="align-top">
                                        {{ $retur->date }}
                                    </td>
                                    <td class="align-top">{{ $retur->date_done ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        Tidak ada return di invoice ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    @endif

    @if ($activity->count() >= 1)
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-0">Activity Timeline</h5>
                </div>
            </div>
            <div class="card-body pt-4" id="viewComment">
                <ul class="timeline card-timeline mb-0">
                    @foreach ($activity as $stats)
                        @php
                            if ($stats->status == '1') {
                                $status = 'Pending On Check';
                                $color = 'info';
                            } elseif ($stats->status == '2') {
                                $status = 'Updated in to Ready Stock';
                                $color = 'whatsapp';
                            } elseif ($stats->status == '3') {
                                $status = 'Updated in to Kurang';
                                $color = 'reddit';
                            } elseif ($stats->status == '4') {
                                $status = 'Updated in to Pre-Order';
                                $color = 'primary';
                            } elseif ($stats->status == '5') {
                                $status = 'Updated in to Delivery Process';
                                $color = 'linkedin';
                            } elseif ($stats->status == '6') {
                                $status = 'Pending is Done';
                                $color = 'success';
                            } elseif ($stats->status == '7') {
                                $status = 'Pending is Canceled';
                                $color = 'danger';
                            } elseif ($stats->status == '8') {
                                $status = 'Retur Product';
                                $color = 'warning';
                            } elseif ($stats->status == '9') {
                                $status = 'Delayed Done';
                                $color = 'secondary';
                            } else {
                                $status = 'Pending Created';
                                $color = 'info';
                            }
                        @endphp
                        <li class="timeline-item timeline-item-transparent clearfix">
                            <span class="timeline-point timeline-point-{{ $color }}"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">{{ $status }}</h6>
                                    <small
                                        class="text-muted">{{ $stats->date->diffInHours(Carbon\Carbon::now()) > 24 ? $stats->date->format('d M y h:i:s') : $stats->date->diffForHumans() }}
                                    </small>
                                </div>
                                <p class="mb-3">
                                    {{ $stats->note }}
                                </p>
                                @foreach ($stats->comment as $item)
                                    <div class="d-flex justify-content-between align-items-center px-2 mb-2{{ $item->id_user == Auth::user()->id ? ' rounded bg-label-primary float-end' : '' }}"
                                        style="width : 80%;">
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="{{ url('') . '/' . $item->user->image }}" alt="ini photo"
                                                style="width: 50px;" class="mx-2 rounded-pill">
                                            <p class="mb-0">
                                                <span class="fw-medium">{{ $item->user->name }}</span>:
                                                {{ $item->comment }}
                                            </p>
                                        </div>
                                        <small
                                            class="text-muted">{{ $item->date->diffInHours(Carbon\Carbon::now()) > 24 ? $item->date->format('d M y h:i:s') : $item->date->diffForHumans() }}</small>
                                    </div>
                                @endforeach
                                @php
                                    $lastStat = App\Models\ChangeStatus::where('id_pending', $pending->id)
                                        ->orderByDesc('id')
                                        ->first();
                                @endphp
                            </div>
                        </li>
                        @if ($stats->id == $lastStat->id && $pending->status != '6')
                            <form action="{{ route('pending-po.addComment', $pending->id) }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-floating mt-3">
                                    <input type="text" class="form-control" id="floatingInputFilled"
                                        placeholder="Comment" name="comment" aria-describedby="floatingInputFilledHelp">
                                    <label for="floatingInputFilled">Comment</label>
                                    <span class="form-floating-focused"></span>
                                </div>
                                <button type="submit"
                                    class="btn btn-primary waves-effect waves-light float-end">Comment</button>
                            </form>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if ($pending->status == '6' && $pending->id_product_out != null)
        <div class="card invoice-preview-card">
            <div class="card-body">
                <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                    <div class="mb-xl-0 pb-1">
                        <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                            <span class="app-brand-logo demo">
                                <span style="color: var(--bs-primary)">
                                    <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt=""
                                        srcset="" width="60%">
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="text-end">
                        <h3 class="fw-bold">Barang Keluar ({{ $product->vers }})</h3>
                        <div>
                            <span
                                class="fw-bolder">#{{ $product->no_type == '1' ? $product->invoice : $product->po }}</span>
                        </div>
                        <div class="mt-1">
                            <span class="text-muted">{{ Carbon\Carbon::parse($product->date)->format('d-m-Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-0">
            <div class="card-body mb-3">
                <div class="row">
                    <div class="col-4 col-lg-2 fw-medium">
                        <p class="mb-1">Customers </p>
                    </div>
                    <div class="col-8 col-lg-10">
                        <pre class="mb-1"
                            style="font-size: 15px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $product->detail_client }}</pre>
                    </div>
                </div>
            </div>
            <hr class="my-0">
            <div class="card-body mb-3">
                <div class="row">
                    <div class="col-4 col-lg-2 fw-medium">
                        <p class="mb-1">Note</p>
                    </div>
                    <div class="col-8 col-lg-6">
                        <pre
                            style="font-size: 15px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: auto; white-space: pre-wrap;">: {{ $product->note }}</pre>
                    </div>
                    <div class="col-4 col-lg-2 fw-medium">
                        <p class="mb-1">User</p>
                    </div>
                    <div class="col-8 col-lg-2">
                        <p class="mb-1">: {{ $product->user->name }}</p>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table m-0 mb-4">
                    <thead class="table-light border-top">
                        <tr>
                            <th>No.</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 0;
                        @endphp
                        @foreach ($detProduct as $products)
                            @php
                                $no++;
                            @endphp
                            <tr style="font-size: 13px">
                                <td class="align-top">{{ $no }}</td>
                                <td class="text-nowrap align-top">
                                    <p class="mb-0 fw-semibold" style="font-size: 12px">
                                        {{ $products->detailProduct->replacement }}
                                    </p>
                                    <pre class="mb-0"
                                        style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $products->detailProduct->product->description }}</pre>
                                </td>
                                <td class="align-top">{{ $products->qty }}
                                    {{ $products->detailProduct->product->unit }}
                                </td>
                                <td class="align-top">RP {{ number_format($products->price, 0, '', '.') }}</td>
                                <td class="align-top">RP {{ number_format($products->amount, 0, '', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr style="font-size: 13px;">
                            <td colspan="3" style="border:none;"></td>
                            <td>Shipping</td>
                            <td>: RP {{ number_format($product->shipping, 0, '', '.') }}</td>
                        </tr>
                        <tr style="font-size: 13px">
                            <td colspan="3" style="border:none;"></td>
                            <td style="border:none;">Total</td>
                            <td style="border:none;">: RP {{ number_format($product->total, 0, '', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    {{-- @foreach ($pendingPO as $pending)
        @include('components.modal.pending.detail')
    @endforeach --}}
    @include('components.modal.pending.status')
    @include('components.modal.pending.kurir')
    @include('components.modal.pending.product')
    @include('components.modal.pending.product-out')
    @include('components.modal.pending.project')
    @include('components.modal.pending.return')
    @include('components.modal.pending.resi')
    @if ($pending->type == 'Project')
        @include('components.modal.pending.request-project')
    @else
        @include('components.modal.pending.request')
    @endif
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/bootstrap-select/bootstrap-select.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/tagify/tagify.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/bloodhound/bloodhound.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-pending-non-project-admin.js"></script>
    <script src="{{ asset('assets') }}/includes/table-pending.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush

@push('script')
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
            $('.select-project').select2({
                dropdownParent: $('#purchaseReqPrj')
            });
        });

        $(document).on('click', '.button-clear', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Clear it??",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Cleared it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('notulen') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'PATCH',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Cleared!",
                                    text: "This Notulen has been Cleared.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/notulen';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Cleared!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-resi', function() {
            var id = $(this).data('id');
            var pending = $(this).data('pending');
            Swal.fire({
                title: "Are you sure to Delete it??",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Deleted it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('pending-po') }}/delete-resi/' + id,
                        'type': 'DELETE',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "This Notulen has been Deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/pending-po/' + pending;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Deleted!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });

        $(document).on('click', '.delete-request', function() {
            var id = $(this).data('id');
            var pending = $(this).data('pending');
            Swal.fire({
                title: "Are you sure to Delete it??",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Deleted it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('purchase-request') }}/delete/' + id,
                        'type': 'DELETE',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "This Notulen has been Deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/pending-po/' + pending;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Deleted!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.clear-return', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Accept it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('pending-po') }}/clear-return/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Accepted!",
                                    text: "Your file has been Accepted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                setTimeout(function() {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Accept!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.done-po', function() {
            var id = $(this).data('id');
            var pending = $(this).data('pending');
            Swal.fire({
                title: "Are you sure to Done it??",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Done it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('pending-po') }}/done/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Done!",
                                    text: "This Notulen has been Done.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/pending-po/product-out-project/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Done!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        
    </script>
@endpush
