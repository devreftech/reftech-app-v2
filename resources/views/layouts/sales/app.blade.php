<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="/assets/" data-template="vertical-menu-template">

<head>
    @include('includes.sales.meta')

    {{--  css  --}}
    @stack('before-style')

    @include('includes.sales.style')

    @stack('after-style')
    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />


    {{--  laravel style  --}}
    <script src="{{ asset('/assets') }}/vendor/js/helpers.js"></script>

    {{-- ! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section --}}
    {{-- ? Template customizer: To hide customizer set displayCustomizer value false in config.js.  --}}
    <script src="{{ asset('/assets') }}/vendor/js/template-customizer.js"></script>

    {{--  ? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.   --}}
    <script src="{{ asset('assets') }}/js/config.js"></script>

    {{-- Dark/light mode shim: webpack bundles don't expose globals, so we shim them manually --}}
    <script>
    (function () {
        var LS_KEY = 'templateCustomizer-vertical-menu-template-Style';

        var DARK_CORE_ID  = 'rt-dark-core-css';
        var DARK_THEME_ID = 'rt-dark-theme-css';

        function applyCSSStyle(isDark) {
            if (isDark) {
                // Append core-dark.css after existing stylesheets so it wins the cascade
                if (!document.getElementById(DARK_CORE_ID)) {
                    var coreLink = document.querySelector('link.template-customizer-core-css');
                    if (coreLink) {
                        var el = document.createElement('link');
                        el.id   = DARK_CORE_ID;
                        el.rel  = 'stylesheet';
                        el.href = (coreLink.getAttribute('href') || '').replace('core.css', 'core-dark.css');
                        document.head.appendChild(el);
                    }
                }
                // Append theme-default-dark.css after existing stylesheets
                if (!document.getElementById(DARK_THEME_ID)) {
                    var themeLink = document.querySelector('link[href*="theme-default.css"]');
                    if (themeLink) {
                        var el2 = document.createElement('link');
                        el2.id   = DARK_THEME_ID;
                        el2.rel  = 'stylesheet';
                        el2.href = (themeLink.getAttribute('href') || '').replace('theme-default.css', 'theme-default-dark.css');
                        document.head.appendChild(el2);
                    }
                }
            } else {
                // Remove dark CSS to revert to light mode
                var dc = document.getElementById(DARK_CORE_ID);
                if (dc) dc.parentNode.removeChild(dc);
                var dt = document.getElementById(DARK_THEME_ID);
                if (dt) dt.parentNode.removeChild(dt);
            }
        }

        // Restore style from localStorage before render (prevents flash)
        var stored = localStorage.getItem(LS_KEY);
        if (stored === 'dark-style') {
            document.documentElement.classList.remove('light-style');
            document.documentElement.classList.add('dark-style');
            applyCSSStyle(true);
        }

        // Shim window.Helpers so main.js can call isLightStyle()
        window.Helpers = window.Helpers || {
            isLightStyle: function () {
                return document.documentElement.classList.contains('light-style');
            }
        };

        // Shim window.templateCustomizer so main.js doesn't remove the style-switcher toggle
        window.templateCustomizer = window.templateCustomizer || {
            settings: {
                defaultShowDropdownOnHover: true,
                defaultMenuCollapsed: false
            },
            setStyle: function (style) {
                var dark = (style === 'dark');
                document.documentElement.classList.remove('light-style', 'dark-style');
                document.documentElement.classList.add(style + '-style');
                applyCSSStyle(dark);
                localStorage.setItem(LS_KEY, style + '-style');
            }
        };
    })();
    </script>
    @routes
    {{-- @if ((Auth::check() && Auth::id() === 23) || Auth::id() === 16 || Auth::id() === 18)
        <style>
            body::before {
                content: "";
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: url('{{ asset('asset/bg-ari.jpg') }}');
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
                filter: blur(8px);
                opacity: 0.9;
                z-index: -1;
            }
        </style>
    @endif --}}
</head>

<body>
    {{-- @if (Auth::check() && Auth::id() === 16)
        <audio id="bgm" autoplay loop style="display: none;">
            <source src="{{ asset('asset/sound-ari.mp3') }}" type="audio/mpeg">
        </audio>
    @endif --}}
    <!--  Layout wrapper  -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!--  Side bar  -->
            @include('components.dashboard.sidebar')
            <!--  END: Side Bar  -->

            <!-- Layout Page -->
            <div class="layout-page">

                <!--  Maintenance Warning Banner & Modal  -->
                @include('components.maintenance-warning')

                <!--  Navbar  -->
                @include('layouts.sales.navbar')
                <!--  END: Navbar  -->

                <!-- Content wrapper -->
                <div class="content-wrapper">

                    @if (!View::hasSection('no-container'))
                        <div class="container-fluid flex-grow-1 container-p-y">
                            <!--  Content  -->
                            @yield('content')
                            <!--  END: Content  -->
                        </div>
                    @else
                        <!--  Content  -->
                        @yield('content')
                        <!--  END: Content  -->
                    @endif
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- END : Content Wrapper -->

            </div>
            <!-- End : Layout Page -->
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

    {{-- Modals pushed here render outside any transformed/hover-animated ancestor,
         so their position:fixed backdrop/dialog isn't trapped by a card's hover transform. --}}
    @stack('modals')

    {{--  javascript --}}
    @stack('before-script')

    @include('includes.sales.script')

    @stack('after-script')

    {{-- Main JS --}}
    <script src="{{ asset('assets') }}/js/main.js?v={{ file_exists(public_path('assets/js/main.js')) ? filemtime(public_path('assets/js/main.js')) : time() }}"></script>

    @if (Auth::check() && in_array(Auth::user()->role, ['Accounting', 'Admin', 'Sales']))
        {{-- Polling notifikasi payment & PO menunggu invoice (Unit Quotation) — bell bergerak + suara tanpa reload --}}
        <script>
            window.paymentNotifUnreadUrl = '{{ route('notifications.payment.unread') }}';
            window.paymentNotifReadUrlTemplate = '{{ url('notifications/payment/__ID__/read') }}';
            window.csrfToken = '{{ csrf_token() }}';
            window.currentUserRole = '{{ Auth::user()->role }}';
        </script>
        <script src="{{ asset('assets') }}/includes/navbar-payment-notif.js?v={{ file_exists(public_path('assets/includes/navbar-payment-notif.js')) ? filemtime(public_path('assets/includes/navbar-payment-notif.js')) : time() }}"></script>
    @endif

    {{-- Patch setStyle so icon updates on click without page reload --}}
    <script>
    (function () {
        var toggle = document.querySelector('.style-switcher-toggle');
        if (!toggle || !window.templateCustomizer) return;
        var icon = toggle.querySelector('i');

        function syncIcon() {
            var isDark = document.documentElement.classList.contains('dark-style');
            icon.classList.toggle('mdi-weather-night', !isDark);
            icon.classList.toggle('mdi-weather-sunny', isDark);
        }

        var orig = window.templateCustomizer.setStyle.bind(window.templateCustomizer);
        window.templateCustomizer.setStyle = function (style) {
            orig(style);
            syncIcon();
        };

        syncIcon();
    })();
    </script>

    <script>
        $(document).on('click', '.view-quote', function(e) {
            e.preventDefault(); // Mencegah perubahan halaman segera

            var id = $(this).data('id');
            var idQ = $(this).data('quotation');
            var href = $(this).attr('href'); // Ambil URL tujuan

            $.ajax({
                url: '{{ url('quotation') }}/' + id + '/view_comment',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Token CSRF
                },
                success: function(response) {
                    console.log(response); // Lakukan apa yang perlu dilakukan setelah AJAX sukses

                    // Arahkan ke halaman baru setelah AJAX selesai
                    window.location.href = href;
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText); // Tangani error jika ada
                }
            });
        });
        $(document).on('click', '.view-quotation', function(e) {
            e.preventDefault(); // Mencegah perubahan halaman segera

            var id = $(this).data('id');
            var idQ = $(this).data('quotation');
            var href = $(this).attr('href'); // Ambil URL tujuan

            console.log(id);

            $.ajax({
                url: '{{ url('quotation') }}/' + id + '/view_comment',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Token CSRF
                },
                success: function(response) {
                    console.log(response); // Lakukan apa yang perlu dilakukan setelah AJAX sukses

                    // Arahkan ke halaman baru setelah AJAX selesai
                    window.location.href = href;
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText); // Tangani error jika ada
                }
            });
        });
        $(document).on('click', '.view-prospect', function(e) {
            e.preventDefault(); // Mencegah perubahan halaman segera

            var id = $(this).data('id');
            var idQ = $(this).data('quotation');
            var href = $(this).attr('href'); // Ambil URL tujuan

            $.ajax({
                url: '{{ url('prospect') }}/' + id + '/view_comment',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Token CSRF
                },
                success: function(response) {
                    console.log(response); // Lakukan apa yang perlu dilakukan setelah AJAX sukses

                    // Arahkan ke halaman baru setelah AJAX selesai
                    window.location.href = href;
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText); // Tangani error jika ada
                }
            });
        });
    </script>

    {{-- SweetAlert2 JS --}}
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>

    @stack('page-script')

    @stack('script')
    @stack('scripts')

    {{-- Modal NPWP Error / Quick Input (dipakai saat klik Upload PO dengan NPWP tidak valid) --}}
    <div class="modal fade" id="modalNpwpError" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-danger d-flex align-items-center mb-0">
                        <i class="mdi mdi-alert-circle-outline me-2 fs-4"></i> NPWP Client Belum Diisi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formQuickNpwp" autocomplete="off">
                    @csrf
                    <input type="hidden" id="quickNpwpClientId" value="">
                    <div class="modal-body p-4">
                        <div class="text-center mb-3">
                            <img src="{{ asset('assets/img/illustrations/npwp-warning.jpg') }}" alt="NPWP Belum Lengkap" class="img-fluid rounded" style="max-height: 120px; object-fit: contain;">
                        </div>
                        <p class="text-muted small text-center mb-3">
                            Quotation ini menggunakan PPN. Masukkan No. NPWP untuk <strong id="quickNpwpClientName" class="text-dark">Client</strong> agar dapat melanjutkan proses Upload PO.
                        </p>
                        <div class="mb-2">
                            <label for="inputQuickNpwp" class="form-label fw-semibold text-dark">Nomor NPWP Client <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-card-account-details-outline"></i></span>
                                <input type="text" class="form-control" id="inputQuickNpwp" name="npwp"
                                    placeholder="00.000.000.0-000.000" maxlength="25" required>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <small class="text-muted" style="font-size: 11px;">Format 15 atau 16 digit NPWP / NIK</small>
                            </div>
                            <div id="quickNpwpError" class="text-danger small mt-2 d-none"></div>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2 d-flex justify-content-between">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSaveQuickNpwp" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Simpan & Lanjut Upload PO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Floating Chat Bubble Component (Disabled on Print, Piping RAB, Piping Materials, and Smart Quote Create Views) --}}
    @unless(request()->routeIs('unit-quotation.print') || request()->is('smart-quote/*/print') || request()->is('*print*') || request()->is('piping-rab*') || request()->is('piping-materials*') || request()->is('smart-quote/create*') || request()->routeIs('unit-quotation.create') || View::hasSection('hide-chat'))
        @include('includes.sales.chat-bubble')
    @endunless

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var activeTriggerBtn = null;
            var activeUploadType = 'general'; // 'general' or 'unit'
            var modalNpwpEl = document.getElementById('modalNpwpError');
            var modalNpwp = modalNpwpEl ? new bootstrap.Modal(modalNpwpEl) : null;
            var inputNpwp = document.getElementById('inputQuickNpwp');
            var inputClientId = document.getElementById('quickNpwpClientId');
            var clientNameEl = document.getElementById('quickNpwpClientName');
            var errorEl = document.getElementById('quickNpwpError');
            var formQuickNpwp = document.getElementById('formQuickNpwp');
            var btnSaveNpwp = document.getElementById('btnSaveQuickNpwp');

            // Auto-format NPWP input as user types
            if (inputNpwp) {
                inputNpwp.addEventListener('input', function(e) {
                    var val = this.value.replace(/\D/g, '');
                    if (val.length > 16) val = val.substring(0, 16);
                    if (val.length <= 15) {
                        var formatted = '';
                        if (val.length > 0) formatted += val.substring(0, 2);
                        if (val.length > 2) formatted += '.' + val.substring(2, 5);
                        if (val.length > 5) formatted += '.' + val.substring(5, 8);
                        if (val.length > 8) formatted += '.' + val.substring(8, 9);
                        if (val.length > 9) formatted += '-' + val.substring(9, 12);
                        if (val.length > 12) formatted += '.' + val.substring(12, 15);
                        this.value = formatted;
                    } else {
                        this.value = val;
                    }
                    if (errorEl) errorEl.classList.add('d-none');
                });
            }

            function openNpwpModal(btn, type) {
                activeTriggerBtn = btn;
                activeUploadType = type;
                if (inputClientId) inputClientId.value = btn.dataset.clientId || '';
                if (clientNameEl) clientNameEl.textContent = btn.dataset.clientName || 'Client';
                if (inputNpwp) {
                    inputNpwp.value = btn.dataset.npwp || '';
                    if (inputNpwp.value) {
                        inputNpwp.dispatchEvent(new Event('input'));
                    }
                }
                if (errorEl) {
                    errorEl.textContent = '';
                    errorEl.classList.add('d-none');
                }
                if (modalNpwp) {
                    modalNpwp.show();
                    setTimeout(function() {
                        if (inputNpwp) inputNpwp.focus();
                    }, 400);
                }
            }

            // General / Parts / Service / Overhaul / Unit-Sales
            document.querySelectorAll('.btn-upload-po').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    var isNonPPN = this.dataset.tax === '0' || this.dataset.tax === '0.00' || !this.dataset.tax || parseFloat(this.dataset.tax) === 0;
                    var npwp = (this.dataset.npwp || '').replace(/[^0-9a-zA-Z]/g, '');
                    if (!isNonPPN && npwp.length < 14) {
                        e.preventDefault();
                        e.stopPropagation();
                        openNpwpModal(this, 'general');
                    } else {
                        if (!this.hasAttribute('data-bs-toggle')) {
                            var uploadPoModal = document.getElementById('uploadPo');
                            if (uploadPoModal) new bootstrap.Modal(uploadPoModal).show();
                        }
                    }
                });
            });

            // Unit-Quotation page (Smart Quote)
            document.querySelectorAll('.btn-upload-po-unit').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    var isNonPPN = this.dataset.tax === '0' || this.dataset.tax === '0.00' || !this.dataset.tax || parseFloat(this.dataset.tax) === 0;
                    var npwp = (this.dataset.npwp || '').replace(/[^0-9a-zA-Z]/g, '');
                    if (!isNonPPN && npwp.length < 14) {
                        e.preventDefault();
                        e.stopPropagation();
                        openNpwpModal(this, 'unit');
                    } else {
                        var modalUploadPO = document.getElementById('modalUploadPO');
                        if (modalUploadPO) new bootstrap.Modal(modalUploadPO).show();
                    }
                });
            });

            // Handle Quick NPWP Save via AJAX
            if (formQuickNpwp) {
                formQuickNpwp.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var rawNpwp = (inputNpwp ? inputNpwp.value : '').trim();
                    var cleanNpwp = rawNpwp.replace(/[^0-9a-zA-Z]/g, '');
                    var clientId = inputClientId ? inputClientId.value : '';

                    if (!cleanNpwp || cleanNpwp.length < 14) {
                        if (errorEl) {
                            errorEl.textContent = 'Nomor NPWP harus minimal 15 digit angka.';
                            errorEl.classList.remove('d-none');
                        }
                        if (inputNpwp) inputNpwp.focus();
                        return;
                    }

                    if (!clientId) {
                        if (errorEl) {
                            errorEl.textContent = 'ID Client tidak ditemukan. Silakan refresh halaman.';
                            errorEl.classList.remove('d-none');
                        }
                        return;
                    }

                    if (btnSaveNpwp) {
                        btnSaveNpwp.disabled = true;
                        btnSaveNpwp.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';
                    }

                    fetch('/client/' + clientId + '/quick-update-npwp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ npwp: rawNpwp })
                    })
                    .then(function(response) {
                        return response.json().then(function(data) {
                            return { status: response.status, ok: response.ok, data: data };
                        });
                    })
                    .then(function(result) {
                        if (btnSaveNpwp) {
                            btnSaveNpwp.disabled = false;
                            btnSaveNpwp.innerHTML = '<i class="mdi mdi-check-circle-outline me-1"></i> Simpan & Lanjut Upload PO';
                        }

                        if (!result.ok || !result.data.success) {
                            var errMsg = (result.data && (result.data.message || result.data.error)) || 'Gagal menyimpan NPWP.';
                            if (errorEl) {
                                errorEl.textContent = errMsg;
                                errorEl.classList.remove('d-none');
                            }
                            return;
                        }

                        // Update dataset npwp on buttons
                        if (activeTriggerBtn) {
                            activeTriggerBtn.dataset.npwp = result.data.npwp;
                        }
                        document.querySelectorAll('[data-client-id="' + clientId + '"]').forEach(function(el) {
                            el.dataset.npwp = result.data.npwp;
                        });

                        // Hide NPWP modal
                        if (modalNpwp) {
                            modalNpwp.hide();
                        }

                        // Show success toast and trigger PO modal
                        Swal.fire({
                            icon: 'success',
                            title: 'NPWP Berhasil Disimpan!',
                            text: 'Membuka form upload PO...',
                            timer: 1200,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });

                        setTimeout(function() {
                            if (activeUploadType === 'unit') {
                                var modalUploadPO = document.getElementById('modalUploadPO');
                                if (modalUploadPO) new bootstrap.Modal(modalUploadPO).show();
                            } else {
                                var uploadPoModal = document.getElementById('uploadPo');
                                if (uploadPoModal) new bootstrap.Modal(uploadPoModal).show();
                            }
                        }, 500);
                    })
                    .catch(function(err) {
                        if (btnSaveNpwp) {
                            btnSaveNpwp.disabled = false;
                            btnSaveNpwp.innerHTML = '<i class="mdi mdi-check-circle-outline me-1"></i> Simpan & Lanjut Upload PO';
                        }
                        if (errorEl) {
                            errorEl.textContent = 'Terjadi kesalahan sistem saat menyimpan data.';
                            errorEl.classList.remove('d-none');
                        }
                    });
                });
            }

            // Show Session Alerts
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    customClass: {
                        confirmButton: 'btn btn-danger waves-effect'
                    }
                });
            @endif

            @if(session('success') || session('message'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') ?: session('message') }}",
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif
        });
    </script>

</body>

</html>
