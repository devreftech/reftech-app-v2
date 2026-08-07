<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../../assets/" data-template="vertical-menu-template">

<head>
    @include('includes.sales.meta')
    @section('title', 'Monitoring Visit')
    @include('includes.sales.style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />


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
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="container">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h1 class="text-center">Watermark Reftech</h1>
                        </div>
                        <div class="card-body">
                            {{-- <form action="{{route('watermark.upload')}}" method="post" id="photoUploadForm" enctype="multipart/form-data"> --}}
                            <form id="photoUploadForm" enctype="multipart/form-data">
                                @csrf
                                <p>Step 1. Ambil photo (bisa lebih dari 1) Max : 3 MB</p>
                                <input class="form-control mb-3" type="file" id="photoInput" name="photos[]" multiple
                                    accept="image/jpeg, image/jpg, image/png">

                                <div id="previewContainer" class="mb-3"></div>

                                <p>Step 2. Click Upload Photo.... tunggu proses uploadnya</p>
                                <button class="btn btn-primary mb-3" type="submit">Upload & Watermark</button>

                                <div id="progressBars" class="mb-3"></div>
                                <div class="mb-3" id="uploadStatus"></div>
                            </form>

                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <p>Step 3. Bila sudah selesai maka Click Download untuk mendapatkan ZIPnya</p>
                            <div class="d-flex gap-3">
                                {{-- <a class="btn btn-twitter waves-effect"
                                    href="{{ route('watermark.download') }}">
                                    Download All Watermarked Photos
                                </a> --}}
                                <button type="button" class="btn btn-twitter waves-effect" id="downloadBtn" disabled>
                                    Download All Watermarked Photos
                                </button>
                                <iframe id="hiddenDownloader" style="display: none;"></iframe>
                                <button type="button" class="btn btn-danger" id="resetBtn">Reset All Photos</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-backdrop fade"></div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

    {{--  javascript --}}
    @stack('before-script')

    @include('includes.sales.script')

    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/js/main.js"></script>

    <script>
        document.getElementById('photoInput').addEventListener('change', function(event) {
            const maxSize = 3 * 1024 * 1024
            const files = event.target.files;
            const previewContainer = document.getElementById('previewContainer');
            previewContainer.innerHTML = '';

            for (let file of files) {

                if (file.size > maxSize) {
                    alert(`${file.name} terlalu besar! Maksimal 3 MB.`);
                    this.value = ''; // reset input
                    break;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        // Buat canvas
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        canvas.width = img.width;
                        canvas.height = img.height;

                        // Gambar foto asli
                        ctx.drawImage(img, 0, 0);

                        // Load gambar watermark
                        const watermark = new Image();
                        watermark.src = '/asset/WM-REFTECH.png'; // Path relatif dari public/
                        watermark.onload = function() {
                            // Hitung ukuran & posisi
                            const scale = 1; // Resize watermark ke 50% dari lebar gambar utama
                            const wmWidth = img.width * scale;
                            const wmHeight = watermark.height * (wmWidth / watermark.width);

                            // Posisi: kanan bawah
                            const x = (img.width - wmWidth) / 2;
                            const y = (img.height - wmHeight) / 2;

                            ctx.globalAlpha = 0.6; // Opacity watermark
                            ctx.drawImage(watermark, x, y, wmWidth, wmHeight);
                            ctx.globalAlpha = 1;

                            // Tampilkan hasil
                            const watermarkedImg = document.createElement('img');
                            watermarkedImg.src = canvas.toDataURL();
                            watermarkedImg.style.maxWidth = '200px';
                            watermarkedImg.style.margin = '10px';

                            previewContainer.appendChild(watermarkedImg);
                        };
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('photoUploadForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const input = document.getElementById('photoInput');
            const files = input.files;
            const progressBarsContainer = document.getElementById('progressBars');
            const statusContainer = document.getElementById('uploadStatus');
            const downloadBtn = document.getElementById('downloadBtn');

            progressBarsContainer.innerHTML = '';
            statusContainer.innerHTML = '';
            downloadBtn.disabled = true; // Disable saat awal upload

            if (files.length === 0) return;

            let completedUploads = 0;
            const totalFiles = files.length;

            [...files].forEach((file) => {
                const formData = new FormData();
                formData.append('photos[]', file);

                const wrapper = document.createElement('div');
                wrapper.classList.add('mb-3');

                const label = document.createElement('div');
                label.textContent = file.name;

                const progress = document.createElement('div');
                progress.className = 'progress';
                progress.innerHTML = `
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                 role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                0%
            </div>
        `;

                const progressBar = progress.querySelector('.progress-bar');

                wrapper.appendChild(label);
                wrapper.appendChild(progress);
                progressBarsContainer.appendChild(wrapper);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', "{{ route('watermark.upload') }}", true);
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                let simulateInterval;

                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 90);
                        progressBar.style.width = percent + '%';
                        progressBar.textContent = percent + '%';
                        progressBar.setAttribute('aria-valuenow', percent);
                    }
                });

                xhr.onloadstart = function() {
                    label.textContent = `${file.name} ⏳ Uploading...`;
                };

                xhr.upload.addEventListener('load', function() {
                    simulateInterval = setInterval(() => {
                        if (parseInt(progressBar.style.width) < 99) {
                            let next = parseInt(progressBar.style.width) + 1;
                            progressBar.style.width = next + '%';
                            progressBar.textContent = next + '%';
                            progressBar.setAttribute('aria-valuenow', next);
                        }
                    }, 50);
                });

                function handleComplete(success = true) {
                    clearInterval(simulateInterval);
                    progressBar.style.width = '100%';
                    progressBar.textContent = '100%';
                    progressBar.setAttribute('aria-valuenow', 100);

                    if (success) {
                        label.textContent = `${file.name} ✅ Done`;
                    } else {
                        progressBar.classList.remove('bg-success');
                        progressBar.classList.add('bg-danger');
                        label.textContent = `${file.name} ❌ Upload error`;
                    }

                    completedUploads++;
                    if (completedUploads === totalFiles) {
                        setTimeout(() => {
                            statusContainer.innerHTML = success ?
                                `<div class="alert alert-success">✅ All uploads successful!</div>` :
                                `<div class="alert alert-warning">⚠️ Some uploads failed.</div>`;
                            downloadBtn.disabled = false; // Enable tombol download
                            progressBarsContainer.innerHTML = ''; // opsional hapus bar
                        }, 800);
                    }
                }

                xhr.onload = function() {
                    handleComplete(true);
                };

                xhr.onerror = function() {
                    handleComplete(false);
                };

                xhr.send(formData);
            });
        });

        document.getElementById('downloadBtn').addEventListener('click', function() {
            const iframe = document.getElementById('hiddenDownloader');
            if (!iframe) {
                console.error('iframe hiddenDownloader tidak ditemukan!');
                return;
            }

            iframe.src = "{{ route('watermark.download') }}";

            setTimeout(() => {
                location.reload(); // refresh halaman
            }, 3000);
        });

        document.getElementById('resetBtn').addEventListener('click', function() {
            if (!confirm('Are you sure you want to reset and delete all uploaded photos?')) return;

            fetch("{{ route('watermark.reset') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ All temporary photos deleted.');
                        document.getElementById('previewContainer').innerHTML = '';
                        document.getElementById('uploadStatus').innerHTML = '';
                        document.getElementById('progressBars').innerHTML = '';
                        document.getElementById('photoInput').value = '';
                    } else {
                        alert('❌ Failed to reset.');
                    }
                })
                .catch(() => alert('❌ Error during reset.'));
        });
    </script>


    @stack('script')
</body>

</html>
