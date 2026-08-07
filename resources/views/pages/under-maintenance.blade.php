<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../../assets/" data-template="vertical-menu-template">

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
    @routes
</head>

<body>
    <!--  Layout wrapper  -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Layout Page -->
            <div class="layout-page">
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <div class="container-fluid flex-grow-1 container-p-y">

                        <!--  Content  -->
                        <div class="misc-wrapper">
                            <h3 class="mb-2 mx-2">Oops! Langganan Kamu Sudah Habis 😕</h3>
                            <p class="mb-4 mx-2">Akses ke layanan ini sementara tidak tersedia. Yuk, perpanjang langgananmu agar bisa digunakan kembali.</p>
                            <div class="d-flex justify-content-center mt-5">
                                <div class="d-flex flex-column align-items-center">
                                    <img src="{{ asset('assets') }}/img/illustrations/misc-under-maintenance-illustration.png"
                                        alt="misc-under-maintenance" class="img-fluid zindex-1" width="290">
                                    <div>
                                        <a href="https://wa.me/6282180006012?text=Halo%20saya%20ingin%20bertanya"
                                            class="btn btn-primary text-center my-5 waves-effect waves-light">Hubungi Kami</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  END: Content  -->

                    </div>
                </div>
                <!-- END : Content Wrapper -->

            </div>
            <!-- End : Layout Page -->
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    {{--  javascript --}}
    @stack('before-script')

    @include('includes.sales.script')

    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/page-misc.css" />
    <link rel="stylesheet" href="style.css">
    @stack('after-script')

    {{-- Main JS --}}
    <script src="{{ asset('assets') }}/js/main.js"></script>

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
</body>

</html>
