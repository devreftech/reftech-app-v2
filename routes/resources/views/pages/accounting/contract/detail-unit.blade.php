@extends('layouts.sales.app')
@section('title', 'Selling Contract — ' . $contract->no_contract)
@section('content')
    <div class="row invoice-preview">
        {{-- Contract Document --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="" width="60%">
                                    </span>
                                </span>
                            </div>
                            <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                            <div style="font-size: 10px">
                                <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                <p class="mb-1">
                                    <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                                    &nbsp;|&nbsp;
                                    <i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>info@reftech.id
                                </p>
                            </div>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold">SELLING CONTRACT</h3>
                            <div><span class="fw-bolder">#{{ $contract->no_contract }}</span></div>
                            <div class="mt-1">
                                <span class="text-muted">{{ Carbon\Carbon::parse($contract->date)->format('d-m-Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-0">

                <div class="card-body mb-3">
                    <h6 class="fw-semibold fs-4 mb-3">Quote To:</h6>
                    <div class="row">
                        <div class="col-2 fw-medium">
                            <p class="mb-1">Company</p>
                            <p class="mb-1">Name PIC</p>
                            <p class="mb-1">Phone</p>
                        </div>
                        <div class="col-4">
                            <p class="mb-1">: {{ $unitQuote->client?->company ?? '-' }}</p>
                            <p class="mb-1">: {{ $unitQuote->pic?->name_pic ?? '-' }}</p>
                            <p class="mb-1">: {{ $unitQuote->client?->phone ?? '-' }}</p>
                        </div>
                        <div class="col-3 fw-medium text-end">
                            <p class="mb-1">Seller :</p>
                            <p class="mb-1">Email :</p>
                        </div>
                        <div class="col-3 text-end">
                            <p class="mb-1">PT Reftech Jaya Optima</p>
                            <p class="mb-1">{{ $unitQuote->client?->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered m-0">
                        <thead class="table-light border-top">
                            <tr>
                                <th style="width:3%">No.</th>
                                <th style="width:52%">Item Description</th>
                                <th>Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        @php
                            $specLabels = [
                                'brand'=>'Brand','model'=>'Model','type_unit'=>'Type',
                                'bar'=>'Max Pressure','air_cap'=>'Air Capacity','power'=>'Motor Power',
                                'voltage'=>'Voltage','connect'=>'Drive','cooling'=>'Cooling Method',
                                'exhaust'=>'Connection','refrigerant_type'=>'Refrigerant Type','pdp'=>'PDP',
                                'filtration'=>'Filtration','oil_content'=>'Oil Content','grade'=>'Grade',
                                'capacity'=>'Capacity','material'=>'Material','test_pressure'=>'Test Pressure',
                                'inlet_pressure'=>'Inlet Pressure','outlet_pressure'=>'Outlet Pressure',
                                'inlet_cap'=>'Inlet Capacity (LP)','outlet_cap'=>'Outlet Capacity (HP)',
                                'dimension'=>'Dimension','weight'=>'Weight',
                            ];
                            $specUnits = [
                                'bar'=>' Bar','air_cap'=>' m³/min','test_pressure'=>' Bar',
                                'inlet_pressure'=>' Bar','outlet_pressure'=>' Bar',
                                'inlet_cap'=>' m³/min','outlet_cap'=>' m³/min',
                                'weight'=>' Kg','capacity'=>' Liter',
                            ];
                        @endphp
                        <tbody>
                            @foreach ($unitQuote->details as $i => $item)
                                <tr style="font-size:13px;">
                                    <td class="align-top text-center">{{ $i + 1 }}</td>
                                    <td class="align-top">
                                        @if ($item->type === 'unit' && $item->unit)
                                            <p class="mb-1 fw-semibold" style="font-size:12px;">
                                                {{ $item->label ?: ($item->unit->brand . ' ' . $item->unit->model) }}
                                            </p>
                                            @php $specs = $item->getSpecVisibleArray(); @endphp
                                            @if (!empty($specs))
                                                <div style="font-size:10px; color:#555; font-family:Inter,sans-serif; margin-top:2px;">
                                                    @foreach ($specs as $field)
                                                        @if ($field === 'unit') @continue @endif
                                                        @php $val = $item->unit->$field ?? null; @endphp
                                                        @if ($val && isset($specLabels[$field]))
                                                            <div style="display:flex; padding:1px 0;">
                                                                <span style="color:#888; min-width:110px; flex-shrink:0;">{{ $specLabels[$field] }}</span>
                                                                <span>: {{ $val }}{{ $specUnits[$field] ?? '' }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        @else
                                            <p class="mb-0 fw-semibold" style="font-size:12px;">{{ $item->label }}</p>
                                            @if ($item->description)
                                                <div style="font-size:10px; color:#555;">{{ $item->description }}</div>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="align-top">{{ $item->qty }} {{ $item->info_qty ?? 'Unit' }}</td>
                                    <td class="align-top text-end">Rp {{ number_format($item->price, 0, '', '.') }}</td>
                                    <td class="align-top text-end">Rp {{ number_format($item->amount, 0, '', '.') }}</td>
                                </tr>
                            @endforeach

                            @php
                                $afterDisc = $unitQuote->diskon > 0
                                    ? $unitQuote->subtotal - ($unitQuote->subtotal * $unitQuote->diskon / 100)
                                    : $unitQuote->subtotal;
                            @endphp
                            <tr class="border-top" style="font-size:13px;">
                                <td colspan="3" class="align-top px-4 py-4">
                                    <span>Thanks for your business</span>
                                </td>
                                <td colspan="1" class="text-end px-4 py-4">
                                    <p class="mb-2">Subtotal:</p>
                                    @if ($unitQuote->diskon > 0)
                                        <p class="mb-2">Discount {{ $unitQuote->diskon }}%:</p>
                                        <p class="mb-2">After Discount:</p>
                                    @endif
                                    <p class="mb-2">Tax {{ $unitQuote->tax ? '(11%)' : '' }}:</p>
                                </td>
                                <td class="text-end px-4 py-4">
                                    <p class="fw-semibold mb-2">Rp {{ number_format($unitQuote->subtotal, 0, '', '.') }}</p>
                                    @if ($unitQuote->diskon > 0)
                                        <p class="fw-semibold mb-2">- Rp {{ number_format($unitQuote->subtotal * $unitQuote->diskon / 100, 0, '', '.') }}</p>
                                        <p class="fw-semibold mb-2">Rp {{ number_format($afterDisc, 0, '', '.') }}</p>
                                    @endif
                                    <p class="fw-semibold mb-2">{{ $unitQuote->tax ? 'Rp ' . number_format($unitQuote->tax_amount, 0, '', '.') : '0' }}</p>
                                </td>
                            </tr>
                            <tr style="font-size:14px;">
                                <td colspan="4" style="background-color:#E7FF00;">
                                    <p class="fw-bold mb-0 text-end">TOTAL PRICE, {{ $unitQuote->tax ? 'INCLUDE' : 'EXCLUDE' }} VAT 11%</p>
                                </td>
                                <td style="background-color:#E7FF00;">
                                    <p class="fw-bold mb-0 text-end">Rp {{ number_format($unitQuote->total, 0, '', '.') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card-body mt-2">
                    <h5 class="my-4">Term & Condition</h5>
                    <div class="row">
                        <div class="col-3 fw-medium">
                            <p class="mb-1">Validity Of Quotation</p>
                            <p class="mb-1">Price</p>
                            <p class="mb-1">Delivery Process</p>
                            <p class="mb-1">Payment</p>
                        </div>
                        <div class="col">
                            <p class="mb-1">: {{ $unitQuote->validity ?? '-' }}</p>
                            <p class="mb-1">: {{ $unitQuote->pricing ?? '-' }}</p>
                            <p class="mb-1">: {{ $unitQuote->delivery_process ?? '-' }}</p>
                            <p class="mb-1">: {{ $unitQuote->payment ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Signatures --}}
                <div class="card-body">
                    <div class="row mt-3">
                        <div class="col-4 my-5 text-center">
                            <p class="fs-normal fw-medium">Authorized By,</p>
                            <img src="{{ asset('/asset') }}/contract/sign-irene.jpeg" alt=""
                                style="width: 100px; height: 77px;">
                            <p class="pt-3">Mrs. Irene</p>
                            <p>PT. Reftech Jaya Optima</p>
                        </div>
                        <div class="col-4"></div>
                        <div class="col-4 my-5 text-center">
                            <p class="fs-normal fw-medium">Accepted By Customer,</p>
                            <div class="pb-5"></div>
                            <p class="pt-5">{{ $unitQuote->pic?->name_pic ?? '-' }}</p>
                            <p>{{ $unitQuote->client?->company ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Actions --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    @if ($contract->level == '0')
                        <button type="button" class="btn btn-primary d-grid w-100 waves-effect mb-3"
                            data-bs-toggle="modal" data-bs-target="#modalAcceptContractUnit">
                            <i class="mdi mdi-check me-1"></i> Approve
                        </button>
                        <a href="#" class="btn btn-outline-danger d-grid w-100 mb-3 waves-effect delete-contract"
                            data-id="{{ $contract->id }}">
                            <i class="mdi mdi-close me-1"></i> Reject
                        </a>
                    @elseif ($contract->level == '1')
                        <a class="btn btn-primary d-grid w-100 mb-3 waves-effect" target="_blank"
                            href="{{ route('contract.print', $contract->id) }}">
                            <i class="mdi mdi-download me-1"></i> Download
                        </a>
                        <a href="#" class="btn btn-outline-danger d-grid w-100 mb-3 waves-effect delete-contract"
                            data-id="{{ $contract->id }}">
                            <i class="mdi mdi-delete-outline me-1"></i> Delete
                        </a>
                    @endif
                    <a href="{{ route('unit-quotation.show', $unitQuote->id) }}"
                        class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect">
                        <i class="mdi mdi-arrow-left me-1"></i> Back to Quotation
                    </a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <p class="fw-semibold mb-2">Contract Info</p>
                    <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                        <span class="text-muted">No. Contract</span>
                        <span class="fw-medium">{{ $contract->no_contract }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                        <span class="text-muted">Type</span>
                        <span>{{ $contract->type }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                        <span class="text-muted">Date</span>
                        <span>{{ Carbon\Carbon::parse($contract->date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                        <span class="text-muted">Client</span>
                        <span>{{ $unitQuote->client?->company ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                        <span class="text-muted">Quotation</span>
                        <a href="{{ route('unit-quotation.show', $unitQuote->id) }}">
                            {{ $unitQuote->no_quote }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Approve --}}
    <div class="modal fade" id="modalAcceptContractUnit" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('accept.contract', $contract->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-0">
                        <h5 class="modal-title">Approve Selling Contract</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3">
                            Request dari <strong>{{ $unitQuote->client?->company ?? '-' }}</strong>
                            &nbsp;·&nbsp; {{ $unitQuote->no_quote }}
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">No. Selling Contract</label>
                            <input type="text" class="form-control" name="no_contract"
                                value="{{ $formattedNumberSC }}/{{ $unitQuote->tax ? 'P' : 'NP' }}/SELLCTX/RJO/{{ $thisYear }}"
                                required>
                            <div class="form-text text-danger">
                                Last No SC: {{ \App\Models\Contract::where('type','Selling')->where('level','1')->whereYear('date',now())->orderByDesc('id')->value('no_contract') ?? '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary waves-effect">Approve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        $(document).on('click', '.delete-contract', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "This contract will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-primary me-3 waves-effect',
                    cancelButton: 'btn btn-label-secondary waves-effect',
                },
                buttonsStyling: false,
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url('contract') }}/' + id,
                        type: 'DELETE',
                        data: { '_token': '{{ csrf_token() }}' },
                        success: function (response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: 'success', title: 'Deleted!',
                                    text: 'Contract has been deleted.',
                                    customClass: { confirmButton: 'btn btn-success waves-effect' },
                                    buttonsStyling: false,
                                }).then(function () {
                                    window.location.href = '/contract';
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
