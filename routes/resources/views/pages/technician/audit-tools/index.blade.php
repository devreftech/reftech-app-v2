@extends('layouts.sales.app')
@section('title', 'My Service Reports')
@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">User /</span> Audit Tools</h4>
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-sm-row mb-4">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile.show', Auth::user()) }}"><i
                            class="mdi mdi-account-outline me-1 mdi-20px"></i>Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile.edit', Auth::user()) }}"><i
                            class="mdi mdi-cog-outline me-1 mdi-20px"></i>Account Settings</a>
                </li>
                @if (Auth::user()->role == 'Admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.create') }}"><i
                                class="mdi mdi-account-multiple-outline me-1 mdi-20px"></i>Create Account</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:void(0);"><i
                                class="mdi mdi-tools me-1 mdi-20px"></i>Tools</a>
                    </li>
                @endif
            </ul>
            <div class="card mb-3">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-audit table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>ID</th>
                                <th>No Audit</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- /Account -->
        </div>
    </div>
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
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-audit.js"></script>
@endpush
