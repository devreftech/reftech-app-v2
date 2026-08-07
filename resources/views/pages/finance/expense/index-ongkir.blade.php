@extends('layouts.sales.app')
@section('title', 'Ongkir Logistik')
@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Expense /</span> Ongkir Logistik
            </h4>
            <p class="text-muted mb-0 small"><i class="mdi mdi-truck-delivery-outline me-1"></i> Posting biaya ongkir dari Logistik ke Finance</p>
        </div>
    </div>

    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center">
            <h6 class="card-title mb-0 fw-bold text-dark">
                <i class="mdi mdi-truck-delivery-outline me-2 text-primary fs-5"></i> Daftar Ongkir
            </h6>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-expense-ongkir table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>No Pending</th>
                        <th>Title</th>
                        <th>Kurir</th>
                        <th>No Track</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <form id="formPostOngkir" method="post" action="">
        @csrf
        <div class="modal fade" id="postOngkirModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Posting Ongkir ke Finance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3" id="ongkir-info-text"></p>
                        <div class="form-floating form-floating-outline mb-3">
                            <select class="form-select" id="id_bank" name="id_bank" required>
                                <option value="">---- Pilih Kas/Bank ----</option>
                                @foreach ($bank as $b)
                                    <option value="{{ $b->id }}">{{ $b->bank }}</option>
                                @endforeach
                            </select>
                            <label for="id_bank">Sumber Kas/Bank</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-3">
                            <select class="form-select" id="id_account" name="id_account" required>
                                <option value="">---- Pilih Account ----</option>
                                @foreach ($account as $a)
                                    <option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>
                                @endforeach
                            </select>
                            <label for="id_account">Kategori Beban (Account)</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-3">
                            <input type="text" class="form-control" id="memo" name="memo" placeholder="Memo">
                            <label for="memo">Memo</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Posting</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-advanced.js"></script>
    <script src="{{ asset('assets') }}/includes/table-expense-ongkir.js"></script>
@endpush
