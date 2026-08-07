<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | Reftech Apps</title>

    {{-- CSS --}}
    @include('includes.sales.style')

    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/page-auth.css">

    <script src="{{ asset('/assets') }}/vendor/js/helpers.js"></script>
    <script src="{{ asset('/assets') }}/vendor/js/template-customizer.js"></script>
    <script src="{{ asset('assets') }}/js/config.js"></script>
</head>

<body>
    <div class="authentication-wrapper authentication-cover">

        <!-- Brand Logo -->
        <a href="{{ url('/') }}" class="auth-cover-brand d-flex align-items-center gap-2">
            <img src="{{ asset('/assets') }}/img/favicon/logo-baru.png"
                alt="Reftech" style="height: 36px; width: auto;" />
            <span class="app-brand-text demo text-heading fw-bold">V 5.5.1</span>
        </a>

        <div class="authentication-inner row m-0">

            <!-- Kiri: Ilustrasi Full Background (tampil hanya di layar besar) -->
            <div class="d-none d-lg-block col-lg-7 col-xl-8 position-relative p-0 overflow-hidden">
                <img src="{{ asset('assets') }}/img/illustrations/new-bg-5.png"
                    alt="Login Illustration"
                    data-app-light-img="illustrations/auth-login-illustration-light.png"
                    data-app-dark-img="illustrations/auth-login-illustration-dark.png"
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;" />
            </div>
            <!-- /Kiri -->

            <!-- Kanan: Form Login -->
            <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg position-relative py-sm-5 px-4 py-4">
                <div class="w-px-400 mx-auto pt-5 pt-lg-0">

                    <h4 class="mb-2 fw-semibold">Reftech ERP Suite</h4>
                    <p class="mb-4">Integrated Business Management System</p>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible mb-3" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework"
                        method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="form-floating form-floating-outline mb-3 fv-plugins-icon-container">
                            <input type="text" class="form-control" id="email" name="email"
                                placeholder="nama@email.com" value="{{ old('email') }}" autofocus>
                            <label for="email">Email</label>
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3 fv-plugins-icon-container">
                            <div class="form-password-toggle">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" id="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            name="password" required autocomplete="current-password"
                                            placeholder="············" aria-describedby="password">
                                        <label for="password">Password</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer">
                                        <i class="mdi mdi-eye-off-outline"></i>
                                    </span>
                                </div>
                            </div>
                            @error('password')
                                <span class="text-danger small d-block mt-1">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>
                            <a href="#" class="float-end mb-1">
                                <span>Forgot Password?</span>
                            </a>
                        </div>

                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100 waves-effect waves-light"
                                type="submit">Sign in</button>
                        </div>
                        <input type="hidden">
                    </form>

                </div>
            </div>
            <!-- /Kanan -->

        </div>
    </div>

    @include('includes.sales.script')

    <!-- Vendors JS -->
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets') }}/js/main.js"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets') }}/js/pages-auth.js"></script>

    <script>
        $(document).ready(function () {
            $(".cursor-pointer").click(function () {
                $(this).children().toggleClass("mdi-eye-off-outline mdi-eye-outline");
                toggleInputType($('#password'));
            });

            function toggleInputType(inputElement) {
                var currentType = inputElement.attr("type");
                var newType = (currentType === "password") ? "text" : "password";
                inputElement.attr("type", newType);
            }
        });
    </script>
</body>

</html>
