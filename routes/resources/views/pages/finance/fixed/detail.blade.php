@extends('layouts.sales.app')
@section('title', 'Fixed Asset')
@section('content')
    <div class="row invoice-preview">
        {{-- Invoice --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
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
                            <h3 class="fw-bold">Fixed Asset</h3>
                            <div>
                                <span class="fw-bolder">{{ $fixed->no_invoice ?? 'no invoice' }}</span>
                            </div>
                            <div class="mt-1">
                                <span class="text-muted">{{ Carbon\Carbon::parse($fixed->date)->format('d-m-Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="card-body mb-3">
                    <div class="d-flex justify-content-between flex-md-column flex-column">
                        <div class="row">
                            <h4>{{ $fixed->type }}</h4>
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-6 fw-medium">
                                        <p class="mb-1">Metode</p>
                                        <p class="mb-1">Umur Aktiva</p>
                                        <p class="mb-1">Status Bayar</p>
                                        {{-- <p class="mb-1">bank</p> --}}
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1">: {{ $fixed->metode }}</p>
                                        <p class="mb-1">: {{ $fixed->Umur }} Bulan</p>
                                        <p class="mb-1">: {{ $fixed->status == 0 ? 'Belum Bayar' : 'Sudah Bayar' }}</p>
                                        {{-- <p class="mb-1">: {{ $expense->bank->bank }} {{ $expense->bank->no_rek }}</p> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-6 fw-medium text-end">
                                        <p class="mb-1">Tanggal Beli :</p>
                                        <p class="mb-1">Tanggal Pakai :</p>
                                        <p class="mb-1">Tanggal Bayar :</p>
                                    </div>
                                    <div class="col-6 text-end">
                                        <p class="mb-1">{{ Carbon\Carbon::parse($fixed->date)->format('d-m-Y') }}</p>
                                        <p class="mb-1">{{ Carbon\Carbon::parse($fixed->pakai)->format('d-m-Y') }}</p>
                                        <p class="mb-1">
                                            {{ $fixed->status == 0 ? 'Belum Bayar' : Carbon\Carbon::parse($fixed->bayar)->format('d-m-Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered m-0 mb-4">
                        <thead class="table-light border-top">
                            <tr>
                                <th>Keterangan</th>
                                <th>Qty</th>
                                <th>Total Asset Awal</th>
                                <th>Total Penyusutan</th>
                                <th>Nilai Buku</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="font-size: 13px">
                                <td class="align-top">
                                    <p class="mb-0 fw-semibold">
                                        {{ $fixed->desc }}
                                    </p>
                                </td>
                                <td class="align-top">
                                    <p>
                                        {{ $fixed->qty }}
                                    </p>
                                </td>
                                <td class="align-top">RP {{ number_format($fixed->total, 2, ',', '.') }}</td>
                                <td class="align-top">RP {{ number_format($totalPenyusutan, 2, ',', '.') }}</td>
                                <td class="align-top">RP {{ number_format($nilaiBuku, 2, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="{{ route('expense.print', $fixed->id) }}" disabled>
                        Download
                    </a>
                    {{-- <a href="javascript{0}" type="button" class="btn btn-outline-secondary d-grid w-100 waves-effect mb-3">
                        Download
                    </a> --}}
                    <a href="#" class="btn btn-danger d-grid w-100 waves-effect delete-fixed mb-3"
                        data-id="{{ $fixed->id }}">Delete</a>
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
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
        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }

        $('#backButton').click(function() {
            window.history.back();
        });

        $(document).on('click', '.delete-fixed', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('fixed') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/fixed';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
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
