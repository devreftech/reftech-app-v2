@extends('layouts.sales.app')
@section('title', 'Monthly Reports')
@section('content')
    <div class="row invoice-preview">
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-row flex-column">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img class="text-md"
                                            src="{{ url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png') }}"
                                            alt="" srcset="" width="60%">
                                    </span>
                                </span>
                            </div>
                            <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                            <div style="font-size: 10px">
                                <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                <p class="mb-1">
                                    <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                                    54417653{{ '  |  ' }}<i
                                        class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                                </p>
                            </div>
                        </div>
                        <div>
                            <h3 class="fw-bold">MONTHLY MONITORING</h3>
                            <h3 class="fw-bold mt-0">AIR DRYER</h3>
                            <div class="mt-1">
                                <span class="text-muted">Month {{ $thisMonth }}</span>
                            </div>
                            {{-- <p class="text-muted mt-1">{{$startDate}} - {{$endDate}}</p> --}}
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="table-responsive text-nowrap mt-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <th>Machine</th>
                                <th>Condition</th>
                                <th>HP (High Pressure)</th>
                                <th>LP (Low Pressure)</th>
                                <th>Strainer</th>
                                <th>PIC</th>
                            </thead>
                            <tbody>
                                @foreach ($monitoringDRYER as $item)
                                    <tr class="{{ $item->idM == $machine->id ? 'bg-label-warning' : '' }}">
                                        <td>{{ $item->unit->brand }} {{ $item->unit->unit->sku }} || {{ $item->tag }}
                                            - {{ $item->location }}
                                        </td>
                                        <td>{{ $item->condition ?? '-' }}</td>
                                        <td>{{ $item->hp ?? '-' }}</td>
                                        <td>{{ $item->lp ?? '-' }}</td>
                                        <td>{{ $item->strainer ?? '-' }}</td>
                                        <td>{{ $item->name ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-5">
                        <div class="col-4 mt-5 text-center">
                            <p>PT Reftech Jaya Optima</p>
                            <div class="">
                                <img src="{{ url('') . '/asset/sign/ttdAngel.jpg' }}" alt="" srcset=""
                                    height="100">
                            </div>
                            <p>Angel Irene</p>
                        </div>
                        <div class="col-4"></div>
                        <div class="col-4 mt-5 text-center">
                            <p>PT Fajar Surya Wisesa</p>
                            <div class="pb-5"></div>
                            <p class="pt-3">..........................................</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="{{ route('service-manager-monthly.print', [request()->route('id'), request()->route('month')]) }}">
                        Download
                    </a>
                    <button id="buttonShare" data-id="1"
                        class="btn btn-success d-grid w-100 waves-effect mb-3">Bagikan</button>
                </div>
            </div>
        </div>
        {{-- End : Button Invoice --}}
    </div>
@endsection
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush
@push('script')
    <script>
        $(() => {
            $('#buttonShare').on('click', function() {
                const id = $(this).data('id')
                if (navigator.share) {
                    navigator.share({
                        title: 'Service Reports',
                        text: 'Check out this link!',
                        url: '{{ route('service-reports.show', ':id') }}'.replace(':id', id)
                    }).then(() => {
                        console.log('Thanks for sharing!');
                    }).catch(err => {
                        console.error('Error sharing:', err);
                    });
                } else {
                    alert('Sharing not supported in this browser.');
                }
            });
        });
    </script>
@endpush
