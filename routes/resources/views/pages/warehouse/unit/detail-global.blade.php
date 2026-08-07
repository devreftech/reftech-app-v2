@extends('layouts.sales.app')
@section('title', 'Data Unit')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Units /</span> {{ $product->sku }}
    </h4>
    <div class="row mb-3">
        <div class="col-12 mb-4">
            <div class="card">
                @if (auth::user()->role == 'Admin' || auth::user()->role == 'Logistic')
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            {{-- Harga Pricelist --}}
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted small">Pricelist:</span>
                                <form action="{{ route('unit-global.update-price', $product->id) }}" method="POST"
                                    class="d-flex align-items-center gap-1" id="form-price">
                                    @csrf
                                    @method('PATCH')
                                    <div class="input-group input-group-sm" style="width:200px;">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control text-end rupiah-price"
                                            name="price_display"
                                            value="{{ $product->serial ? number_format($product->serial->first()?->price ?? 0, 0, ',', '.') : '0' }}"
                                            autocomplete="off">
                                        <input type="hidden" name="price" id="price-raw"
                                            value="{{ $product->serial->first()?->price ?? 0 }}">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                </form>
                            </div>
                            <div>
                                <a type="button" data-bs-toggle="modal" data-bs-target="#updateProduct-{{ $product->id }}">
                                    <button type="button" class="btn btn-sm btn-label-primary">Edit</button>
                                </a>
                                <a href="#" data-id="{{ $product->id }}"
                                    class="btn btn-sm btn-label-danger delete-product">Delete
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="card-body">
                    @php
                        $isCompressor = in_array($product->unit, ['PISTON COMPRESSOR', 'AIR COMPRESSOR SCREW']);
                        $isDryer      = in_array($product->unit, ['REFRIGERANT AIR DRYER', 'DESICANT DRYER']);
                    @endphp
                    <div class="row mb-1">
                        {{-- Kolom Kiri: Spec --}}
                        <div class="col-6">
                            @include('components.detail-row', ['label' => 'Kategori', 'value' => $product->unit])
                            @include('components.detail-row', ['label' => 'SKU',      'value' => $product->sku])
                            @include('components.detail-row', ['label' => 'Brand',    'value' => $product->brand])
                            @include('components.detail-row', ['label' => 'Model',    'value' => $product->model])

                            @if ($isCompressor)
                                @include('components.detail-row', ['label' => 'Type Compressor',       'value' => $product->type_unit])
                                @include('components.detail-row', ['label' => 'Short Description',     'value' => $product->desc])
                                @include('components.detail-row', ['label' => 'Max. Working Pressure', 'value' => $product->bar ? $product->bar . ' Bar' : null])
                                @include('components.detail-row', ['label' => 'Air Capacity',          'value' => $product->air_cap ? $product->air_cap . ' m³/min' : null])
                                @include('components.detail-row', ['label' => 'Motor Power',           'value' => $product->power])
                            @elseif ($isDryer)
                                @include('components.detail-row', ['label' => 'FAD / Air Capacity', 'value' => $product->air_cap ? $product->air_cap . ' m³/min' : null])
                                @include('components.detail-row', ['label' => 'Refrigerant Type',   'value' => $product->refrigerant_type])
                                @include('components.detail-row', ['label' => 'PDP',                'value' => $product->pdp])
                            @else
                                @include('components.detail-row', ['label' => 'Short Description', 'value' => $product->desc])
                            @endif

                            <hr class="my-2">
                            @include('components.detail-row', ['label' => 'Status',     'value' => $product->status])
                            @include('components.detail-row', ['label' => 'Stock Awal', 'value' => $product->frist_stock])
                        </div>

                        {{-- Kolom Kanan: Spec lanjutan + Note --}}
                        <div class="col-6">
                            @if ($isCompressor)
                                @include('components.detail-row', ['label' => 'Rated Voltage',        'value' => $product->voltage])
                                @include('components.detail-row', ['label' => 'Drive',                'value' => $product->connect])
                                @include('components.detail-row', ['label' => 'Cooling Method',       'value' => $product->cooling])
                                @include('components.detail-row', ['label' => 'Discharge Connection', 'value' => $product->exhaust])
                            @elseif ($isDryer)
                                @include('components.detail-row', ['label' => 'Rated Voltage', 'value' => $product->voltage])
                            @endif

                            @include('components.detail-row', ['label' => 'Dimension', 'value' => $product->dimension])
                            @include('components.detail-row', ['label' => 'Weight',    'value' => $product->weight ? $product->weight . ' Kg' : null])

                            @if ($product->note)
                                <div class="row mb-1">
                                    <div class="col-4 text-muted">Note</div>
                                    <div class="col-8">
                                        <pre class="mb-0" style="font-family: inherit; white-space: pre-wrap;">: {{ $product->note }}</pre>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if (auth::user()->role == 'Admin' || auth::user()->role == 'Logistic')
            <div class="row">
                <div class="col-md-6 col-12 ">
                    <div class="d-flex justify-content-between mb-2">
                        <h5 class="fw-bold pb-1 mb-2">
                            Sparepart Consumable Part
                        </h5>
                        <a type="button" data-bs-toggle="modal" data-bs-target="#createSparepart">
                            <button type="button" class="btn btn-primary">
                                + New Sparepart
                            </button>
                        </a>
                    </div>
                    <div class="card mb-4">
                        <div class="table-responsive text-nowrap h-100">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>PN</th>
                                        <th>Desc</th>
                                        <th>Quantity</th>
                                        <th>Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($consumable as $part)
                                        @php
                                            $allStock = $part->warehouse_stock + $part->stock;
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $part->pn }}
                                            </td>
                                            <td>
                                                {{ $part->description }}
                                            </td>
                                            <td>
                                                {{ $part->qty }} {{ $part->equivalent->product->unit ?? 'Pcs' }}
                                            </td>
                                            <td>
                                                {{ $allStock }}
                                            </td>
                                            <td>
                                                <a href="#" data-id="{{ $part->id }}"
                                                    class="btn btn-sm btn-label-danger delete-sparepart">
                                                    <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                Kamu belum punya Consumable Part.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <h5 class="fw-bold pb-1 mb-2">
                        Sparepart Non Consumable Part
                    </h5>
                    <div class="card">
                        <div class="table-responsive text-nowrap h-100">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>PN</th>
                                        <th>Desc</th>
                                        <th>Quantity</th>
                                        <th>Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($nonconsumable as $part)
                                        @php
                                            $allStock = $part->warehouse_stock + $part->stock;
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $part->pn }}
                                            </td>
                                            <td>
                                                {{ $part->description }}
                                            </td>
                                            <td>
                                                {{ $part->qty }} {{ $part->equivalent->product->unit ?? 'Pcs' }}
                                            </td>
                                            <td>
                                                {{ $allStock }}
                                            </td>
                                            <td>
                                                <a href="#" data-id="{{ $part->id }}"
                                                    class="btn btn-sm btn-label-danger delete-sparepart">
                                                    <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                Kamu belum punya Consumable Part.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12 flex-1 mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <h5 class="fw-bold pb-1 mb-2">
                            Equivalent
                        </h5>
                        <a type="button" data-bs-toggle="modal" data-bs-target="#createEquivalent-{{ $product->id }}">
                            <button type="button" class="btn btn-primary">
                                + New Equivalent
                            </button>
                        </a>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="datatable-product-equivalent table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Brand</th>
                                        <th>PN</th>
                                        <th>Bar</th>
                                        <th>Air Capacity</th>
                                        <th></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-6 col-12 ">
                    <div class="d-flex justify-content-between mb-2">
                        <h5 class="fw-bold pb-1 mb-2">
                            Sparepart Consumable Part
                        </h5>
                    </div>
                    <div class="card mb-4">
                        <div class="table-responsive text-nowrap h-100">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>PN</th>
                                        <th>Desc</th>
                                        <th>Quantity</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($consumable as $part)
                                        @php
                                            $allStock = $part->warehouse_stock + $part->stock;
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $part->pn }}
                                            </td>
                                            <td>
                                                {{ $part->description }}
                                            </td>
                                            <td>
                                                {{ $part->qty }} {{ $part->info_qty }}
                                            </td>
                                            <td>
                                                {{ $allStock }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                Kamu belum punya Consumable Part.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="d-flex justify-content-between mb-2">
                        <h5 class="fw-bold pb-1 mb-2">
                            Sparepart Non Consumable Part
                        </h5>
                    </div>
                    <div class="card">
                        <div class="table-responsive text-nowrap h-100">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>PN</th>
                                        <th>Desc</th>
                                        <th>Quantity</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($nonconsumable as $part)
                                        @php
                                            $allStock = $part->warehouse_stock + $part->stock;
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $part->pn }}
                                            </td>
                                            <td>
                                                {{ $part->description }}
                                            </td>
                                            <td>
                                                {{ $part->qty }} {{ $part->info_qty }}
                                            </td>
                                            <td>
                                                {{ $allStock }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                Kamu belum punya Consumable Part.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{-- <div class="row">
            <div class="col-12 col-lg-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <table class="datatable-product-in-detail table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>invoice</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <table class="datatable-product-out-detail table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>invoice</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table class="datatable-product-quotation table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>no quote</th>
                                    <th>equivalent</th>
                                    <th>Qty</th>
                                    <th>price</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
    @include('components.modal.warehouse.unit.form-global')
    {{-- @include('components.modal.warehouse.unit.stock') --}}
    @include('components.modal.warehouse.unit.sparepart')
    @include('components.modal.warehouse.replacement.form')
    @include('components.modal.warehouse.equivalent.form-global')
    @php
        $no = 0;
    @endphp
    @foreach ($serials as $serial)
        @include('components.modal.warehouse.equivalent.form-global')
        @php
            $no++;
        @endphp
    @endforeach
    @foreach ($details as $detail)
        @include('components.modal.warehouse.replacement.form-price')
    @endforeach
@endsection()
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/bootstrap-select/bootstrap-select.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/tagify/tagify.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/bloodhound/bloodhound.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/includes/table-equivalent-global.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product-in-detail.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product-out-detail.js"></script>
    <script src="{{ asset('assets') }}/includes/table-quotation-product.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush
@push('script')
    <script>
        // Rupiah formatter untuk field pricelist
        $(document).on('input', '.rupiah-price', function () {
            var raw = $(this).val().replace(/\./g, '').replace(/\D/g, '');
            $(this).val(raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
            $('#price-raw').val(raw);
        });

        $(document).on('click', '.delete-product', function() {
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
                        'url': '{{ url('unit') }}/' + id,
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
                                    window.location.href = '/unit-global';
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
        $(document).on('click', '.delete-replacement', function() {
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
                        'url': '{{ url('product') }}/replacement/' + id,
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
                                    location.reload();
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
        $(document).on('click', '.delete-equivalent', function() {
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
                        'url': '{{ url('product') }}/equivalent/' + id,
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
                                    location.reload();
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
        $(document).on('click', '.delete-sparepart', function() {
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
                        'url': '{{ url('delete') }}/sparepart/' + id,
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
                                    location.reload();
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
        $(() => {

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }


            $(".invoice-item-price-label").on('keyup', function() {
                var input = $(this)
                var id = input.data('id');
                var input_val = input.val();

                // original length
                var original_len = input_val.length;

                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);
                input_val = input_val;

                // send updated string to input
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                console.log(id);
                console.log(nomorInt);
                $(`#price-${id}`).val(nomorInt);
            });
            $(".invoice-item-modal-label").on('keyup', function() {
                var input = $(this)
                var id = input.data('id');
                var input_val = input.val();

                // original length
                var original_len = input_val.length;

                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);
                input_val = input_val;

                // send updated string to input
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                console.log(id);
                console.log(nomorInt);
                $(`#modal-${id}`).val(nomorInt);
            });
        });
    </script>
@endpush
