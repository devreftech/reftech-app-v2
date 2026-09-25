@extends('layouts.sales.app')
@section('title', 'My Setting')
@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">User /</span> Account Settings</h4>
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
                <li class="nav-item">
                    <a class="nav-link active" href="javascript:void(0);"><i
                            class="mdi mdi-account-multiple-outline me-1 mdi-20px"></i>Create Account</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('audit-tools.index')}}"><i class="mdi mdi-tools me-1 mdi-20px"></i>Tools</a>
                </li>
            </ul>
            <div class="card mb-4">
                <form action="{{ route('profile.store') }}" id="formCreateUser" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h4 class="mb-0">Profile Details</h4>
                        <div id="clientVendorBadge" class="badge bg-label-info d-none p-2 fs-6">
                            <i class="mdi mdi-account-badge-outline me-1"></i> Mode Input Cepat: Client Vendor (Nama, Email & Password)
                        </div>
                    </div>
                    
                    <div class="card-body" id="photoUploadSection">
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img src="{{ asset('asset') }}/profile/profile.jpg" alt="user-avatar"
                                class="d-block w-px-120 h-px-120 rounded shadow-xs" id="uploadedAvatar">
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-2 mb-3 waves-effect waves-light"
                                    tabindex="0">
                                    <span class="d-none d-sm-block">Upload new photo</span>
                                    <i class="mdi mdi-tray-arrow-up d-block d-sm-none"></i>
                                    <input type="file" id="upload" class="account-file-input" name="image"
                                        hidden="" accept="image/png, image/jpeg">
                                </label>
                                <button type="button"
                                    class="btn btn-outline-secondary account-image-reset mb-3 waves-effect">
                                    <i class="mdi mdi-reload d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Reset</span>
                                </button>

                                <div class="text-muted small">Allowed JPG, GIF or PNG. Max size of 800K</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-2 mt-1">
                        <div class="row mt-2 gy-4">
                            <div class="col-md-6">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select fw-semibold" id="roleSelect"
                                            aria-label="Default select example" name="role" required>
                                            <option value="Client Vendor" selected>Client Vendor</option>
                                            <option value="Sales">Sales</option>
                                            <option value="Technician">Technician</option>
                                            <option value="Warehouse">Warehouse</option>
                                            <option value="Accounting">Accounting</option>
                                            <option value="Logistic">Logistic</option>
                                            <option value="Supervisor">Supervisor</option>
                                            <option value="Project Manager">Project Manager</option>
                                        </select>
                                        <label for="roleSelect">Role Select</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="text" id="name" name="name"
                                        value="{{ old('name') }}" placeholder="PT / Nama Vendor atau Client" required />
                                    <label for="name">Nama <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="email" id="email" name="email"
                                        value="{{ old('email') }}" placeholder="vendor@example.com" required />
                                    <label for="email">E-mail <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" id="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="new-password"
                                            placeholder="············" aria-describedby="password">
                                        <label for="password">Password <span class="text-danger">*</span></label>
                                    </div>
                                    <span class="input-group-text cursor-pointer">
                                        <i class="mdi mdi-eye-off-outline"></i>
                                    </span>
                                </div>
                            </div>

                            {{-- Additional fields for standard internal roles --}}
                            <div class="col-md-6 extra-role-field">
                                <div class="form-floating form-floating-outline mb-3 fv-plugins-icon-container">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text">+62</span>
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" pattern="[0-9]*"
                                                placeholder="8123094857" id="phone" name="phone"
                                                value="{{ old('phone') }}">
                                            <label for="phone">Phone</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 extra-role-field">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="area" name="area"
                                        placeholder="Put Area here..." value="{{ old('area') }}" />
                                    <label for="area">Area</label>
                                </div>
                            </div>

                            <div class="col-md-6 extra-role-field">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="text" id="code" name="code"
                                        value="{{ old('code') }}" placeholder="contoh: RZA" />
                                    <label for="code">Code</label>
                                </div>
                            </div>
                        </div>

                        <div id="clientVendorInfoAlert" class="alert alert-primary d-flex align-items-center mt-3 mb-0" role="alert">
                            <i class="mdi mdi-information-outline fs-4 me-2"></i>
                            <div>
                                <strong>Role Client Vendor:</strong> Cukup isi <strong>Nama</strong>, <strong>Email</strong>, dan <strong>Password</strong>. Foto avatar otomatis menggunakan default.
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2 shadow-xs">
                                <i class="mdi mdi-check-circle-outline me-1"></i> Simpan User
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /Account -->
        </div>
    </div>
    <!-- / Content -->
@endsection
@push('after-style')
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush
@push('after-script')
    <!-- Vendors JS -->
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/cleavejs/cleave.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/cleavejs/cleave-phone.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script>
        $(document).ready(function() {
            $(".cursor-pointer").click(function() {
                $(this).children().toggleClass("mdi-eye-off-outline mdi-eye-outline");
                toggleInputType($('#password'));
            });

            function toggleInputType(inputElement) {
                var currentType = inputElement.attr("type");
                var newType = (currentType === "password") ? "text" : "password";
                inputElement.attr("type", newType);
            }

            $("#phone").on("input", function() {
                $(this).val($(this).val().replace(/[^0-9]/g, ''));
            });

            function handleRoleChange() {
                var selectedRole = $('#roleSelect').val();
                if (selectedRole === 'Client Vendor') {
                    $('#photoUploadSection').slideUp(200);
                    $('.extra-role-field').slideUp(200);
                    $('#clientVendorBadge').removeClass('d-none');
                    $('#clientVendorInfoAlert').slideDown(200);
                    $('#phone').removeAttr('required');
                    $('#area').removeAttr('required');
                } else {
                    $('#photoUploadSection').slideDown(200);
                    $('.extra-role-field').slideDown(200);
                    $('#clientVendorBadge').addClass('d-none');
                    $('#clientVendorInfoAlert').slideUp(200);
                    $('#phone').attr('required', 'required');
                    $('#area').attr('required', 'required');
                }
            }

            $('#roleSelect').on('change', handleRoleChange);
            handleRoleChange();
        });
    </script>
@endpush
@push('page-script')
    <!-- Page JS -->
    <script src="{{ asset('assets') }}/js/pages-account-settings-account.js"></script>
@endpush
