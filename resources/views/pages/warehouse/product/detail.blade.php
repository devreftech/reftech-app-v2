@extends('layouts.sales.app')
@section('title', 'Data Product')
@section('content')

{{-- Breadcrumb --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="mdi mdi-package-variant-closed text-primary"></i> {{ $product->commodity }}
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 13px;">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ url('product') }}">Products</a></li>
                <li class="breadcrumb-item active">{{ $product->commodity }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="#" data-bs-toggle="modal" data-bs-target="#updateStock-{{ $product->id }}"
            class="btn btn-sm btn-label-success rounded-pill px-3">
            <i class="mdi mdi-tray-arrow-down me-1"></i> Edit Stock
        </a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#updateProduct-{{ $product->id }}"
            class="btn btn-sm btn-label-primary rounded-pill px-3">
            <i class="mdi mdi-pencil-outline me-1"></i> Edit
        </a>
        @if (Auth::user()->role == 'Admin')
            <a href="#" data-id="{{ $product->id }}" class="btn btn-sm btn-label-danger rounded-pill px-3 delete-product">
                <i class="mdi mdi-delete-outline me-1"></i> Delete
            </a>
        @endif
    </div>
</div>

{{-- Stock Metric Cards --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="mdi mdi-warehouse fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Warehouse Stock</small>
                    <span class="fw-bold text-dark" style="font-size: 15px;">{{ $product->warehouse_stock }} {{ $product->unit }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-info"><i class="mdi mdi-office-building-outline fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Office Stock</small>
                    <span class="fw-bold text-dark" style="font-size: 15px;">{{ $product->stock }} {{ $product->unit }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-warning"><i class="mdi mdi-timer-sand fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Pending Stock</small>
                    <span class="fw-bold text-dark" style="font-size: 15px;">{{ $product->pending_stock }} {{ $product->unit }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-success"><i class="mdi mdi-cube-outline fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">All Stock</small>
                    <span class="fw-bold text-success" style="font-size: 15px;">{{ $allStock }} {{ $product->unit }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- LEFT COLUMN --}}
    <div class="col-xl-8">
        {{-- Product Info --}}
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-information-outline text-primary"></i> Informasi Produk
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3" style="font-size: 13px;">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Comodity</small>
                        <span class="fw-semibold text-dark">{{ $product->commodity }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Short Description</small>
                        <span class="fw-semibold text-dark">{{ $product->detail_desc ?: '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Genuine / OEM</small>
                        <span class="fw-semibold text-dark">{{ $product->go ?: '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Category</small>
                        <span class="badge bg-label-secondary rounded-pill px-3 py-1">{{ $product->category ?: '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Dimension</small>
                        <span class="fw-semibold text-dark">{{ $product->dimension ?: '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Weight</small>
                        <span class="fw-semibold text-dark">{{ $product->weight }} Gram</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Stock Awal</small>
                        <span class="fw-semibold text-dark">{{ $product->first_stock }} {{ $product->unit }} ({{ $product->date }})</span>
                    </div>
                </div>
                <hr class="my-3">
                <div class="row g-3" style="font-size: 13px;">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Note</small>
                        <p class="mb-0 text-dark" style="white-space: pre-wrap; max-width: 100%; overflow-x: auto;">{{ $product->note ?: '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Description</small>
                        <p class="mb-0 text-dark" style="white-space: pre-wrap; max-width: 100%; overflow-x: auto;">{{ $product->description ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Part Inquiry --}}
        @if ($partInquiries->isNotEmpty())
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-magnify text-primary"></i> Part Inquiry
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Brand</th>
                                <th>Part Number</th>
                                <th>Harga Jual</th>
                                <th>Jumlah Vendor</th>
                                <th>Last Inquiry</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($partInquiries as $pi)
                                <tr>
                                    <td>{{ $pi->brand }}</td>
                                    <td>{{ $pi->pn }}</td>
                                    <td>Rp {{ number_format($pi->price, 0, ',', '.') }}</td>
                                    <td>{{ $pi->sparePartVendorPrices->count() }} vendor</td>
                                    <td>{{ $pi->sparePartVendorPrices->max('date') ? \Carbon\Carbon::parse($pi->sparePartVendorPrices->max('date'))->format('d M Y') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('part-inquiry.show', $pi->id) }}" class="btn btn-sm btn-label-primary">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Product In / Out --}}
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-arrow-down-bold-box-outline text-primary"></i> Product In
                </h6>
            </div>
            <div class="card-body">
                <table class="datatable-product-in-detail table table-striped">
                    <thead>
                        <tr>
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
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-arrow-up-bold-box-outline text-primary"></i> Product Out
                </h6>
            </div>
            <div class="card-body">
                <table class="datatable-product-out-detail table table-striped">
                    <thead>
                        <tr>
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
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-file-document-multiple-outline text-primary"></i> Quotation
                </h6>
            </div>
            <div class="card-body">
                <table class="datatable-product-quotation table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>no quote</th>
                            <th>equivalent</th>
                            <th>Qty</th>
                            <th>price</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        {{-- Equivalent --}}
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-shuffle-variant text-primary"></i> Equivalent
                </h6>
                <button type="button" class="btn btn-xs btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createEquivalent-{{ $product->id }}">
                    <i class="mdi mdi-plus me-1"></i> New
                </button>
            </div>
            <div class="card-body">
                <table class="datatable-product-equivalent{{Auth::user()->role == 'Logistic' ? '-logistik' : ''}} table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Brand</th>
                            <th>PN</th>
                            <th>Price</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="col-xl-4">
        {{-- Replacement --}}
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-swap-horizontal text-primary"></i> Replacement
                </h6>
                <button type="button" class="btn btn-xs btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createReplacement-{{ $product->id }}">
                    <i class="mdi mdi-plus me-1"></i> New
                </button>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Replacement</th>
                            <th>Stock</th>
                            @if (Auth::user()->role == 'Admin')
                                <th>Modal</th>
                            @endif
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($details as $detail)
                            @php
                                $allRep = $detail->stock + $detail->warehouse_stock;
                            @endphp
                            <tr>
                                <td>
                                    {{ $detail->replacement }}
                                </td>
                                <td>
                                    {{ $allRep }} {{ $detail->product->unit }}
                                </td>
                                @if (Auth::user()->role == 'Admin')
                                    <td>
                                        Rp.{{ number_format($detail->modal, 0, '', '.') }}
                                    </td>
                                @endif
                                <td>
                                    @if (Auth::user()->role == 'Admin')
                                        <a href="#" data-id="{{ $detail->id }}"
                                            class="btn btn-sm btn-label-danger delete-replacement">
                                            <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline m-0"></i>
                                        </a>
                                    @endif
                                    <a type="button" data-bs-toggle="modal"
                                        data-bs-target="#editReplacement-{{ $detail->id }}">
                                        <button type="button" class="btn btn-sm btn-label-primary">
                                            <i
                                                class="menu-icon tf-icons mdi mdi-14px mdi-note-edit-outline m-0"></i>
                                        </button>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    Kamu belum punya Replacement.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
    @include('components.modal.warehouse.product.form')
    @include('components.modal.warehouse.product.stock')
    @include('components.modal.warehouse.replacement.form')
    @include('components.modal.warehouse.equivalent.form')
    @foreach ($serials as $serial)
        @include('components.modal.warehouse.equivalent.form')
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
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/includes/table-equivalent.js"></script>
    <script src="{{ asset('assets') }}/includes/table-equivalent-logistik.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product-in-detail.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product-out-detail.js"></script>
    <script src="{{ asset('assets') }}/includes/table-quotation-product.js"></script>
@endpush
@push('script')
    <script></script>
@endpush

@push('script')
    <script>
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
                        'url': '{{ url('product') }}/' + id,
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
                                    window.location.href = '/product';
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
