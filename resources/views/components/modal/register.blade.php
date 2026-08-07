<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template">

<head>
    @include('includes.sales.meta')
    <title>Document</title>
    {{--  css  --}}
    @include('includes.sales.style')
    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/page-auth.css">
    {{--  laravel style  --}}
    <script src="{{ asset('/assets') }}/vendor/js/helpers.js"></script>
    <script src="{{ asset('/assets') }}/vendor/js/template-customizer.js"></script>
    <script src="{{ asset('assets') }}/js/config.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</head>

<body>
    <div class="position-relative">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <!-- Register Card -->
                <div class="card p-2">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mt-5">
                        <a href="index.html" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <img class="text-md" src="{{ asset('assets') }}/img/favicon/logo-reftech1.png"
                                    alt="ini logo">
                            </span>
                            <span class="app-brand-text demo text-heading fw-bold">PT REFTECH JAYA OPTIMA</span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    <div class="card-body mt-2">
                        <h4 class="mb-2 fw-semibold">Adventure starts here 🚀</h4>
                        <p class="mb-4">Make your app management easy and fun!</p>

                        <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework"
                        action="{{ route('register') }}" method="POST" novalidate="novalidate">
                                @csrf

                                <div class="form-floating form-floating-outline mb-3 fv-plugins-icon-container">
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter your name" autofocus="">
                                    <label for="name">Name</label>
                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                </div>

                                <div class="form-floating form-floating-outline mb-3 fv-plugins-icon-container">
                                    <input type="text" class="form-control" id="area" name="area"
                                        placeholder="Enter your Area" autofocus="">
                                    <label for="area">Area</label>
                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                </div>

                                <div class="form-floating form-floating-outline mb-3 fv-plugins-icon-container">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text">+62</span>
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" placeholder="8123094857"
                                                id="phone" name="phone">
                                            <label for="phone">Phone</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-floating form-floating-outline mb-3 fv-plugins-icon-container">
                                    <input type="text" class="form-control" id="email" name="email"
                                        placeholder="Enter your email">
                                    <label for="email">Email</label>
                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                </div>

                                <div class="mb-3 form-password-toggle fv-plugins-icon-container">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input type="password" id="password" class="form-control" name="password"
                                                placeholder="············" aria-describedby="password">
                                            <label for="password">Password</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"><i
                                                class="mdi mdi-eye-off-outline"></i></span>
                                    </div>
                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                </div>

                                <div class="form-floating form-floating-outline mb-3 fv-plugins-icon-container">
                                    <input type="text" class="form-control" id="code" name="code"
                                        placeholder="Enter your Code" autofocus="">
                                    <label for="code">Code</label>
                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                </div>

                                <div class="mb-3 fv-plugins-icon-container">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms-conditions"
                                            name="terms">
                                        <label class="form-check-label" for="terms-conditions">
                                            I agree to
                                            <a href="javascript:void(0);">privacy policy &amp; terms</a>
                                        </label>
                                        <div class="fv-plugins-message-container invalid-feedback"></div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary d-grid w-100 waves-effect waves-light">Sign up</button>
                                <input type="hidden">
                            </form>

                            <p class="text-center">
                                <span>Already have an account?</span>
                                <a href="auth-login-basic.html">
                                    <span>Sign in instead</span>
                                </a>
                            </p>
                    </div>
                </div>
                <!-- Register Card -->
                <img alt="mask" src="{{ asset('assets') }}/img/illustrations/auth-basic-register-mask-light.png"
                    class="authentication-image d-none d-lg-block"
                    data-app-light-img="illustrations/auth-basic-register-mask-light.png"
                    data-app-dark-img="illustrations/auth-basic-register-mask-dark.png">
            </div>
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
        // Restricts input for the given textbox to the given inputFilter.
        function setInputFilter(textbox, inputFilter, errMsg) {
            ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout"].forEach(
                function(event) {
                    textbox.addEventListener(event, function(e) {
                        if (inputFilter(this.value)) {
                            // Accepted value.
                            if (["keydown", "mousedown", "focusout"].indexOf(e.type) >= 0) {
                                this.classList.remove("input-error");
                                this.setCustomValidity("");
                            }

                            this.oldValue = this.value;
                            this.oldSelectionStart = this.selectionStart;
                            this.oldSelectionEnd = this.selectionEnd;
                        } else if (this.hasOwnProperty("oldValue")) {
                            // Rejected value: restore the previous one.
                            this.classList.add("input-error");
                            this.setCustomValidity(errMsg);
                            this.reportValidity();
                            this.value = this.oldValue;
                            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                        } else {
                            // Rejected value: nothing to restore.
                            this.value = "";
                        }
                    });
                });
        }
        setInputFilter(document.getElementById("phone"), function(value) {
            return /^-?\d*$/.test(value);
        }, "Hanya dapat mengisi dengan nomor");
    </script>
</body>

</html>
