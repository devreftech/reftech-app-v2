@extends('layouts.sales.app')
@section('title', 'Product In')
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
                            <h3 class="fw-bold">Stock Opname</h3>
                            <div>
                                <span class="fw-bolder mb-1">#PERIODE CATURWULAN - {{ $opname->periode }}</span>
                            </div>
                            <span class="fw-medium">Petugas Gudang - {{ $opname->user->name }}</span>
                            <div class="mt-1">
                                <span class="text-muted">{{ Carbon\Carbon::parse($opname->date)->format('d-m-Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-opname table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>id</th>
                                <th>Product</th>
                                <th>Stock Web</th>
                                <th>Stock Gudang</th>
                                <th>Selisih</th>
                                <th>Stock Sebelumnya</th>
                                <th>Note</th>
                                <th>Button</th>
                            </tr>
                        </thead>
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
                        href="{{ route('opname.show_print', $opname->id) }}">
                        Download
                    </a>
                    <a href="#" class="btn btn-danger d-grid w-100 waves-effect delete"
                        data-id="{{ $opname->id }}">Delete</a>
                </div>
            </div>
        </div>
        {{-- End : Button Invoice --}}
    </div>
    @include('components.modal.warehouse.opname.form-product')
    @include('components.modal.warehouse.opname.edit-opname')

@endsection
@push('after-style')
    <!-- Page CSS -->
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
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/tables-datatables-advanced.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
    <script src="{{ asset('assets') }}/includes/table-opname-stock.js"></script>
@endpush
@push('script')
    <script>
        $(document).ready(function() {

            // Saat replacement dipilih
            $(document).on('click', '.editStock', function() {
                let id = $(this).data('id');
                console.log(id);

                let url = '/stock-opname/update/' + id;
                $('#formEditStock').attr('action', url);

                $('.edit_sistem').val('');
                $('.edit_gudang').val('');
                $('.edit_selisih').val('');
                $('.edit_note').val('');

                // sementara return boleh buat testing
                // return;

                $.ajax({
                    url: '/show/replacement/' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        console.log(res);

                        $('.edit_title').text(res.product);
                        $('.edit_gudang').val(res.gudang);
                        $('.edit_bdg').val(res.bdg);
                        $('.edit_bks').val(res.bks);
                        $('.edit_sistem').val(res.web);
                        $('.edit_selisih').val(res.selisih);
                        $('.edit_note').val(res.note);
                        hitungSelisih();
                    },
                    error: function() {
                        alert('Gagal mengambil stock');
                    }
                });
            });
            // Saat replacement dipilih
            $('#replacement').on('change', function() {
                let replacementId = $(this).val();

                // Reset jika kosong
                if (!replacementId) {
                    $('#sistem').val('');
                    $('#gudang').val('');
                    $('#selisih').val('');
                    return;
                }

                // Ambil stock sistem via AJAX
                $.ajax({
                    url: '/stock/replacement/' + replacementId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        console.log(res);

                        let stockSistem = res.stock_sistem ?? 0;

                        $('#sistem').val(stockSistem);
                        hitungSelisih();
                    },
                    error: function() {
                        alert('Gagal mengambil stock sistem');
                    }
                });
            });

            // Saat stock gudang diinput
            $('#bdg').on('keyup change', function() {
                hitungSelisih();
            });
            $('.edit_bdg').on('keyup change', function() {
                hitungSelisihEdit();
            });
            $('#bks').on('keyup change', function() {
                hitungSelisih();
            });
            $('.edit_bks').on('keyup change', function() {
                hitungSelisihEdit();
            });

            // Function hitung selisih
            function hitungSelisih() {
                let sistem = parseInt($('#sistem').val()) || 0;
                let bdg = parseInt($('#bdg').val()) || 0;
                let bks = parseInt($('#bks').val()) || 0;
                let selisih = sistem - bdg - bks;

                $('#selisih').val(selisih);
            }

            function hitungSelisihEdit() {
                let sistem = parseInt($('.edit_sistem').val()) || 0;
                let bdg = parseInt($('.edit_bdg').val()) || 0;
                $('.edit_selisih').val(sistem - bdg);
            }

        });
    </script>
@endpush
