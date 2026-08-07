<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="/assets/" data-template="vertical-menu-template">

<head>
    @include('includes.sales.meta')

    {{--  css  --}}
    @stack('before-style')

    @include('includes.sales.style')

    @stack('after-style')


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
    @if ((Auth::check() && Auth::id() === 23) || Auth::id() === 16 || Auth::id() === 18)
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
    @endif
</head>

<body>
    @if (Auth::check() && Auth::id() === 16)
        <audio id="bgm" autoplay loop style="display: none;">
            <source src="{{ asset('asset/sound-ari.mp3') }}" type="audio/mpeg">
        </audio>
    @endif
    <!--  Layout wrapper  -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!--  Side bar  -->
            @include('components.dashboard.sidebar')
            <!--  END: Side Bar  -->

            <!-- Layout Page -->
            <div class="layout-page">

                <!--  Navbar  -->
                @include('layouts.sales.navbar')
                <!--  END: Navbar  -->

                <!-- Content wrapper -->
                <div class="content-wrapper">

                    @if (!View::hasSection('no-container'))
                        <div class="container-xxl flex-grow-1 container-p-y">
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

    {{--  javascript --}}
    @stack('before-script')

    @include('includes.sales.script')

    @stack('after-script')

    {{-- Main JS --}}
    <script src="{{ asset('assets') }}/js/main.js"></script>

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

    @stack('page-script')

    @stack('script')

    {{-- Modal NPWP Error (dipakai saat klik Upload PO dengan NPWP tidak valid) --}}
    <div class="modal fade" id="modalNpwpError" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content text-center">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <i class="mdi mdi-alert-circle text-danger" style="font-size: 56px;"></i>
                    </div>
                    <h5 class="fw-bold text-danger mb-2">NPWP Belum Lengkap!</h5>
                    <p class="text-muted mb-4">TOLONG ISI NPWP CLIENT TERLEBIH DAHULU (minimal 10 angka).</p>
                    <button type="button" class="btn btn-danger waves-effect waves-light px-5"
                        data-bs-dismiss="modal">OK, Mengerti</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-upload-po').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var npwp = (this.dataset.npwp || '').replace(/[^0-9]/g, '');
                    if (npwp.length <= 10) {
                        new bootstrap.Modal(document.getElementById('modalNpwpError')).show();
                    } else {
                        new bootstrap.Modal(document.getElementById('uploadPo')).show();
                    }
                });
            });
        });
    </script>

</body>

</html>
