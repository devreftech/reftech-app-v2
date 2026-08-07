@extends('layouts.sales.app')
@section('title', 'Service Reports')
@section('content')
    <div class="row invoice-preview">
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-row flex-column">
                        @if ($service->pic->client->info == 'Reftech')
                            <div class="mb-xl-0 pb-1">
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img class="text-md"
                                                src="{{ url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png') }}"
                                                alt="" srcset="" width="60%">
                                        </span>
                                    </span>
                                </div>
                                <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                                <div style="font-size: 10px">
                                    <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                    <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                    <p class="mb-1">
                                        <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                                        54417653{{ '  |  ' }}<i
                                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="mb-xl-0 pb-1">
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img class="text-md" src="{{ asset('/asset') }}/logo/Kojisha-Log.png"
                                                alt="" srcset="" width="60%">
                                        </span>
                                    </span>
                                </div>
                                <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                                <div style="font-size: 10px">
                                    <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                                    <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                    <p class="mb-1">
                                        <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62 812-1000-0997
                                        {{ '   ' }}<i
                                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                                    </p>
                                </div>
                            </div>
                        @endif
                        <div>
                            <h3 class="fw-bold">SERVICE REPORT</h3>
                            <div>
                                <span class="fw-bolder">#{{ $service->no_service }}</span>
                            </div>
                            <div class="mt-1">
                                <span class="text-muted">{{ $service->date }}</span>
                            </div>
                            <div class="mt-1">
                                @php
                                    $badgeClass = '';
                                    $label = $service->type;

                                    switch ($service->type) {
                                        case 'Visit':
                                            $badgeClass = 'success';
                                            break;
                                        case 'Service':
                                            $badgeClass = 'danger';
                                            break;
                                        case 'General':
                                            $badgeClass = 'primary';
                                            $label = 'General Check';
                                            break;
                                        default:
                                            $badgeClass = '';
                                            break;
                                    }
                                @endphp
                                <span
                                    class="badge fs-6 rounded-pill bg-label-{{ $badgeClass }}">{{ $label }}</span>
                            </div>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-lg-2 col-4 fw-medium">
                            <p class="mb-1">Customers </p>
                            <p class="mb-1">Address </p>
                            <p class="mb-1">PIC </p>
                        </div>
                        <div class="col-lg-4 col-8">
                            <p class="mb-1">: {{ $service->pic->client->company }}</p>
                            <p class="mb-1">: {{ $service->pic->client->area }}</p>
                            <p class="mb-1">: {{ $service->pic->name_pic }}</p>
                        </div>
                        <div class="col-lg-2 col-4 fw-medium">
                            <p class="mb-1">Unit Type </p>
                            <p class="mb-1">Serial Number </p>
                            <p class="mb-1">Running & Load </p>
                        </div>
                        <div class="col-lg-4 col-8">
                            <p class="mb-1">: {{ $service->machine->unit->brand }}
                                {{ $service->machine->unit->unit->sku }}</p>
                            <p class="mb-1">: {{ $service->machine->serial }}
                                {{ $service->machine->tag ? '| ' . $service->machine->tag : '' }}
                                {{ $service->machine->location ? '| ' . $service->machine->location : '' }}</p>
                            <p class="mb-1">: {{ $service->running }} | {{ $service->load }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-2 col-4 fw-medium">
                            <p class="mb-1">Job Description </p>
                        </div>
                        <div class="col-lg-10 col-8 d-flex gap-1">
                            <p>: </p>
                            <p class="mb-1"> {{ $service->jobdesc }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <h5 class="my-2">Description</h5>
                            <pre class="mb-1"
                                style="font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $service->desc }}
                            </pre>
                        </div>
                        <div class="col-lg-6 col-12">
                            <h5 class="my-2">Recomendation</h5>
                            <pre class="mb-1"
                                style="font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $service->recomendation }}</pre>
                        </div>
                    </div>
                    <hr>
                    <h5 class="my-4">Picture</h5>
                    <div class="row mb-5">
                        @foreach ($pict as $picture)
                            <div class="col-lg-4 col-12 text-center">
                                <img src="{{ url('') . '/' . $picture->picture }}" alt="" srcset=""
                                    style="max-width : 200px;">
                                <p>{{ $picture->keterangan }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="row mt-5">
                        <div class="col-4 mt-5 text-center">
                            <p>{{ $service->pic->client->info == 'Reftech' ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia' }}</p>
                            @if (isset($service->technician->sign))
                                <img src="{{ url('') . '/' . $service->technician->sign }}" alt="" srcset=""
                                    height="100">
                            @else
                                <div class="pb-5"></div>
                            @endif
                            <p class="pt-3">( {{ $service->technician->name }} )</p>
                        </div>
                        <div class="col-4"></div>
                        <div class="col-4 mt-5 text-center">
                            <p class="">{{ $service->pic->client->company }}</p>
                            @if (isset($service->sign_client))
                                <img src="{{ url('') . '/' . $service->sign_client }}" alt="" srcset=""
                                    height="100">
                            @else
                                <div class="pb-5"></div>
                            @endif
                            <p class="pt-3">( {{ $service->pic->name_pic }} )</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="{{ route('service-reports.print', $service->id) }}">
                        Download
                    </a>
                    <button id="buttonShare" data-id="{{ $service->id }}"
                        class="btn btn-success d-grid w-100 waves-effect mb-3">Bagikan</button>
                    <a href="{{ route('service-reports.edit', $service->id) }}"
                        class="btn btn-outline-warning d-grid w-100 waves-effect mb-3">Edit</a>
                    @if (Auth::user()->role == 'Technician' || Auth::user()->role == 'Coordinator')
                        <a href="#" class="btn btn-outline-danger d-grid w-100 waves-effect delete-service mb-3"
                            data-id="{{ $service->id }}">Delete</a>
                    @endif
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    @if ($pict->isNotEmpty())
                        <a href="#" class="btn btn-danger d-grid w-100 waves-effect delete-image mb-3"
                            data-id="{{ $service->id }}">Delete Image Reports</a>
                    @else
                        <a type="button" data-bs-toggle="modal" data-bs-target="#inputImage"
                            class="d-grid w-100 waves-effect mb-3">
                            <button type="button" class="btn btn-primary">
                                Input Image Reports
                            </button>
                        </a>
                    @endif
                    @if (isset($service->sign_client))
                        <a href="#" class="btn btn-danger d-grid w-100 waves-effect delete-hand-sign mb-3"
                            data-id="{{ $service->id }}">Delete Hand Sign</a>
                    @else
                        <a type="button" data-bs-toggle="modal" data-bs-target="#inputSign-{{ $service->id }}"
                            class="d-grid w-100 waves-effect mb-3">
                            <button type="button" class="btn btn-secondary">
                                Input Hand Sign
                            </button>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        {{-- End : Button Invoice --}}
    </div>
    @include('components.modal.service.sign')
    @include('components.modal.service.image')
@endsection
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush
@push('script')
    <script>
        $(() => {
            $('#formFileMultiple').on('change', function() {
                var files = this.files;
                var dynamicInputsContainer = $('#dynamicInputsContainer');
                var dynamicInputsContainer = $('#dynamicInputsPhotoContainer');
                dynamicInputsContainer.empty();


                // Hanya mengambil satu file (file pertama)
                var file = files[0];
                console.log(file);
                const previewContainer = document.getElementById('image-preview');
                previewContainer.innerHTML = '';

                const reader = new FileReader();

                reader.onload = function(e) {
                    const imageContainer = document.createElement('div');
                    const imageElement = document.createElement('img');
                    imageContainer.className = 'image-container'; // Tambahkan kelas sesuai kebutuhan

                    // Set maksimum lebar dan tinggi untuk gambar
                    imageElement.style.maxWidth =
                        '800px'; // Ganti dengan nilai maksimum lebar yang Anda inginkan
                    imageElement.style.maxHeight =
                        '500px'; // Ganti dengan nilai maksimum tinggi yang Anda inginkan

                    imageElement.src = e.target.result;

                    imageContainer.appendChild(imageElement);
                    previewContainer.appendChild(imageContainer);
                };

                reader.readAsDataURL(file);
            });
            $('#buttonShare').on('click', function() {
                const id = $(this).data('id')
                if (navigator.share) {
                    navigator.share({
                        title: 'Service Reports',
                        text: 'Check This Service Reports!',
                        url: '{{ route('service-reports.print', ':id') }}'.replace(':id', id)
                    }).then(() => {
                        console.log('Thanks for sharing!');
                    }).catch(err => {
                        console.error('Error sharing:', err);
                    });
                } else {
                    alert('Sharing not supported in this browser.');
                }
            });

            $('#backButton').click(function() {
                window.history.back();
            });
        });
        $(document).ready(function() {
            $('#formFileMultiplePict').on('change', function(event) {
                var files = this.files;
                var dynamicInputsContainer = $('#dynamicInputsPhotoContainer');
                console.log(dynamicInputsContainer);
                console.log(files.length);

                dynamicInputsContainer.empty();

                for (var i = 0; i < files.length; i++) {
                    var dynamicInput =
                        '<input class="form-control mb-2" type="text" name="description[]" placeholder="Deskripsi untuk File ' +
                        (i + 1) + '">';
                    dynamicInputsContainer.append(dynamicInput);
                }

                if (files.length !== 3 && files.length !== 6 && files.length !== 9 && files.length !== 12) {
                    alert('Gambar Wajib Kelipatan 3! 3/6/9/12 Maksimal 12');
                    this.value = ''; // Menghapus file yang tidak memenuhi syarat
                    dynamicInputsContainer.empty();
                    return; // Menghentikan eksekusi lebih lanjut jika jumlah file tidak memenuhi syarat
                }

                console.log(files);

                // photo
                const previewContainer = document.getElementById('photo-preview');
                previewContainer.innerHTML = '';
                const filesArray = Array.from(event.target.files);
                console.log(filesArray.map(file => file.name));
                for (let i = 0; i < filesArray.length; i++) {
                    const file = filesArray[i];
                    const reader = new FileReader();

                    reader.onload = (function(fileIndex) {
                        return function(e) {
                            const image = new Image();
                            image.src = e.target.result;

                            image.onload = function() {
                                const canvas = document.createElement('canvas');
                                const ctx = canvas.getContext('2d');

                                // Tentukan ukuran baru untuk gambar
                                const MAX_WIDTH = 150;
                                const MAX_HEIGHT = 150;
                                let width = image.width;
                                let height = image.height;

                                if (width > height) {
                                    if (width > MAX_WIDTH) {
                                        height *= MAX_WIDTH / width;
                                        width = MAX_WIDTH;
                                    }
                                } else {
                                    if (height > MAX_HEIGHT) {
                                        width *= MAX_HEIGHT / height;
                                        height = MAX_HEIGHT;
                                    }
                                }

                                canvas.width = width;
                                canvas.height = height;

                                ctx.drawImage(image, 0, 0, width, height);

                                const resizedImageURL = canvas.toDataURL(file.type);

                                const rowContainer = document.createElement('div');
                                const imageContainer = document.createElement('div');
                                const imageElement = document.createElement('img');
                                const inputElement = document.createElement('input');

                                rowContainer.className = 'row';
                                imageContainer.className = 'col-6 col-md-4 photo-container';
                                imageElement.className = 'mb-2';
                                imageElement.src = resizedImageURL;

                                inputElement.className = 'form-control mb-3';
                                inputElement.type = 'text';
                                inputElement.name = 'description[' + (i) + ']';
                                inputElement.placeholder = 'Deskripsi untuk Photo ' + (
                                    fileIndex + 1);

                                imageContainer.appendChild(imageElement);
                                imageContainer.appendChild(inputElement);
                                previewContainer.appendChild(imageContainer);
                            };
                        };
                    })(i);

                    reader.readAsDataURL(file);
                }


            });
        });
        $(document).on('click', '.delete-hand-sign', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('service-reports') }}/del-sign/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/service-reports/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-image', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('service-reports') }}/del-image/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/service-reports/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-service', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('service-reports') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/service-reports';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
            // Swal.fire({
            //     title: "Are you sure?",
            //     text: "You won't be able to revert this!",
            //     icon: "warning",
            //     showCancelButton: true,
            //     confirmButtonColor: "#3085d6",
            //     cancelButtonColor: "#d33",
            //     confirmButtonText: "Yes, delete it!"
            // }).then((result) => {
            //     if (result.isConfirmed) {
            //         $.ajax({
            //             'url': '{{ url('leads') }}/' + id,
            //             'type': 'POST',
            //             'data': {
            //                 '_method': 'DELETE',
            //                 '_token': '{{ csrf_token() }}'
            //             },
            //             success: function(response) {
            //                 if (response == 1) {
            //                     Swal.fire({
            //                         title: "Deleted!",
            //                         text: "Your file has been deleted.",
            //                         icon: "success"
            //                     })
            //                     window.setTimeout(function() {
            //                         location.reload();
            //                     }, 2000);
            //                 } else {
            //                     Swal.fire({
            //                         icon: 'error',
            //                         title: 'Oops...',
            //                         text: 'Data Failed to Delete!'
            //                     });
            //                 }
            //             }
            //         });
            //     }
            // });
        });
    </script>
@endpush
