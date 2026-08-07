@extends('layouts.sales.app')
@section('title', 'Marketing Tools')
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework"
                action="{{ route('pending-po.update', $id) }}" method="post" enctype="multipart/form-data">
                @method('PATCH')
                @csrf
                <h2>Choose Equivalent For Prospect {{ $quote->pic->client->company }}</h2>
                @php
                    $no = 0;
                @endphp
                @foreach ($Dquote as $item)
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-2">{{ $item->equivalent->brand }} - {{ $item->equivalent->pn }}</p>
                            <div class="form-floating form-floating-outline mb-2">
                                <select id="product-{{ $no }}"
                                    class="select2 form-select form-select-lg invoice-item-replacement"
                                    data-allow-clear="true" name="replacement[]">
                                    <option> ---- Choose Replacement Here ---- </option>
                                    @foreach ($fullRep[$no] as $replacement)
                                        <option value="{{ $replacement->id }}"
                                            {{ @$dPending[$no]->id_replacement == $replacement->id ? 'selected' : '' }}>
                                            {{ $replacement->replacement }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="select2Basic">Replacement</label>
                            </div>
                            <textarea class="form-control invoice-item-detail-product" rows="2" id="detailProduct{{ $no }}"
                                name="desc[]">{{ old('detail_product[]', $item->detail_product) }}</textarea>
                        </div>
                        <div class="col-1">
                            <p class="mb-2">Qty </p>
                            <input type="number" class="form-control qty-input" name="qty[]"
                                data-index="{{ $no }}" data-max="{{ $item->qty }}" min="0"
                                max="{{ $item->qty }}" value="{{ old('qty[]', @$dPending[$no]->qty ?? 0) }}">
                            <p>on Quote: {{ $item->qty }} {{ $item->info_qty }}</p>
                        </div>
                        <div class="col-1">
                            <p class="mb-2">Qty Warehouse</p>
                            <input type="number" class="form-control qty-warehouse-input" name="qty_warehouse[]"
                                data-index="{{ $no }}" data-max="{{ $item->qty }}" min="0"
                                max="{{ $item->qty }}" value="{{ old('qty[]', @$dPending[$no]->qty_warehouse ?? 0) }}">
                            <p>on Quote: {{ $item->qty }} {{ $item->info_qty }}</p>
                        </div>
                        <div class="col-4">
                            <p class="mb-2">Note</p>
                            <textarea class="form-control " rows="2" id="note" name="note[]">{{ @$dPending[$no]->note }}</textarea>
                        </div>
                    </div>
                    <hr>
                    @php
                        $no++;
                    @endphp
                @endforeach
                <div class="float-end">
                    <a href="{{ route('quotation.index') }}" type="button" class="btn btn-lg btn-outline-secondary">
                        Back
                    </a>
                    <button :disabled="focused" type="submit" class="btn btn-lg btn-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- @include('components.modal.notulen.form') --}}
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
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
    <script src="{{ asset('assets') }}/includes/table-pending-admin.js"></script>
    <script src="{{ asset('assets') }}/includes/table-pending.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush

@push('script')
    <script>
        function initializeSelect2Replacement() {
            $('.invoice-item-replacement').select2({
                placeholder: ' ---- Choose Part Number Here ---- ',
                allowClear: true,
                width: '100%',
            });
        }

        $(document).ready(function() {
            initializeSelect2Replacement();
        });
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();

        });

        function syncInputs(index) {
            var $qty = $('.qty-input[data-index="' + index + '"]');
            var $warehouse = $('.qty-warehouse-input[data-index="' + index + '"]');

            var max = parseInt($qty.data('max'));
            var qtyVal = parseInt($qty.val()) || 0;
            var warehouseVal = parseInt($warehouse.val()) || 0;

            var maxForQty = max - warehouseVal;
            var maxForWarehouse = max - qtyVal;

            $qty.attr('max', maxForQty);
            $warehouse.attr('max', maxForWarehouse);

            $qty.prop('disabled', maxForQty <= 0);
            $warehouse.prop('disabled', maxForWarehouse <= 0);
        }

        $('.qty-input, .qty-warehouse-input').on('input', function() {
            var index = $(this).data('index');
            syncInputs(index);
        });

        // Initial sync on page load
        $('.qty-input').each(function() {
            var index = $(this).data('index');
            syncInputs(index);
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
    </script>
@endpush
