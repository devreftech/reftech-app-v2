@extends('layouts.sales.app')
@section('title', 'Detail Employee')
@section('content')
    @php
        $latestDetail = $detail->first();
        $roleColors = [
            'Admin' => 'primary',
            'Sales' => 'success',
            'Sales Manager' => 'success',
            'Accounting' => 'warning',
            'Finance Manager' => 'warning',
            'Logistic' => 'info',
            'Technician' => 'dark',
            'ServiceM' => 'dark',
            'Support' => 'secondary',
            'Client' => 'secondary',
        ];
        $roleColor = $roleColors[$users->role] ?? 'secondary';
        $statusColor = $users->active == '1' ? 'success' : 'secondary';
    @endphp

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Employee /</span> {{ $users->name }}
    </h4>

    <div class="row">
        {{-- Profile Card --}}
        <div class="col-12 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <img src="{{ url('') . '/' . $users->image }}" alt="{{ $users->name }}"
                        class="rounded-circle mb-3"
                        style="width:120px;height:120px;object-fit:cover;border:3px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.08);">
                    <h5 class="mb-0 fw-bold">{{ $users->name }}</h5>
                    <p class="text-muted mb-2">{{ $latestDetail->position ?? '-' }}</p>
                    <div class="d-flex justify-content-center gap-2 mb-3 flex-wrap">
                        <span class="badge bg-label-{{ $roleColor }}">{{ $users->role }}</span>
                        <span class="badge bg-label-{{ $statusColor }}">
                            {{ $users->active == '1' ? 'Active' : 'Non Active' }}
                        </span>
                    </div>
                    <ul class="list-unstyled text-start mb-4" style="font-size:13px;">
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="mdi mdi-card-account-details-outline text-muted"></i>
                            <span>{{ $users->nip }} &middot; {{ $users->code }}</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="mdi mdi-map-marker-outline text-muted"></i>
                            <span>{{ $latestDetail->area ?? '-' }}</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="mdi mdi-email-outline text-muted"></i>
                            <span class="text-truncate">{{ $users->email ?: '-' }}</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="mdi mdi-phone-outline text-muted"></i>
                            <span>{{ $users->phone ?: '-' }}</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-calendar-outline text-muted"></i>
                            <span>Bergabung {{ $users->date_in ? \Carbon\Carbon::parse($users->date_in)->translatedFormat('d F Y') : '-' }}</span>
                        </li>
                    </ul>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        @if ($users->role == 'Sales')
                            <button type="button" class="btn btn-sm btn-label-success" data-bs-toggle="modal"
                                data-bs-target="#updateTarget-{{ $users->id }}">
                                <i class="mdi mdi-target me-1"></i>Edit Target
                            </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-label-primary" data-bs-toggle="modal"
                            data-bs-target="#updateUsers-{{ $users->id }}">
                            <i class="mdi mdi-pencil-outline me-1"></i>Edit Profile
                        </button>
                        <a href="#" data-id="{{ $users->id }}" class="btn btn-sm btn-label-danger delete-user">
                            <i class="mdi mdi-trash-can-outline me-1"></i>Delete
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Cards --}}
        <div class="col-12 col-lg-8 mb-3">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Pribadi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block mb-1">NIP</small>
                                    <span class="fw-medium">{{ $users->nip ?: '-' }}</span>
                                </div>
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block mb-1">Kode</small>
                                    <span class="fw-medium">{{ $users->code ?: '-' }}</span>
                                </div>
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block mb-1">Tanggal Lahir</small>
                                    <span class="fw-medium">{{ $users->birthday ? \Carbon\Carbon::parse($users->birthday)->translatedFormat('d F Y') : '-' }}</span>
                                </div>
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block mb-1">No. HP</small>
                                    <span class="fw-medium">{{ $users->phone ?: '-' }}</span>
                                </div>
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block mb-1">Email</small>
                                    <span class="fw-medium">{{ $users->email ?: '-' }}</span>
                                </div>
                                <div class="col-12 col-md-4">
                                    <small class="text-muted d-block mb-1">Alamat</small>
                                    <span class="fw-medium">{{ $users->address ?: '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Kepegawaian</h5>
                        </div>
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block mb-1">Tanggal Masuk</small>
                                    <span class="fw-medium">{{ $users->date_in ? \Carbon\Carbon::parse($users->date_in)->translatedFormat('d F Y') : '-' }}</span>
                                </div>
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block mb-1">Posisi</small>
                                    <span class="fw-medium">{{ $latestDetail->position ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block mb-1">Area</small>
                                    <span class="fw-medium">{{ $latestDetail->area ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block mb-1">Role</small>
                                    <span class="badge bg-label-{{ $roleColor }}">{{ $users->role }}</span>
                                </div>
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block mb-1">Status</small>
                                    <span class="badge bg-label-{{ $statusColor }}">
                                        {{ $users->active == '1' ? 'Active' : 'Non Active' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($users->role == 'Sales' && $target)
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Target Bulanan</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-6 col-md-4">
                                        <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <span class="badge bg-label-secondary p-1 rounded"><i class="mdi mdi-account-multiple-plus-outline"></i></span>
                                                <span class="fw-semibold text-dark" style="font-size:0.78rem;">Leads</span>
                                            </div>
                                            <h6 class="mb-0 fw-bold text-dark">{{ $target->leads ?? 0 }}</h6>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <span class="badge bg-label-info p-1 rounded"><i class="mdi mdi-phone-outline"></i></span>
                                                <span class="fw-semibold text-dark" style="font-size:0.78rem;">Daily Call</span>
                                            </div>
                                            <h6 class="mb-0 fw-bold text-dark">{{ $target->dc ?? 0 }}</h6>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <span class="badge bg-label-primary p-1 rounded"><i class="mdi mdi-account-multiple-outline"></i></span>
                                                <span class="fw-semibold text-dark" style="font-size:0.78rem;">CRM</span>
                                            </div>
                                            <h6 class="mb-0 fw-bold text-dark">{{ $target->crm ?? 0 }}</h6>
                                        </div>
                                    </div>
                                    @if ($target->visit)
                                        <div class="col-6 col-md-4">
                                            <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                                <div class="d-flex align-items-center gap-1 mb-1">
                                                    <span class="badge bg-label-warning p-1 rounded"><i class="mdi mdi-map-marker-outline"></i></span>
                                                    <span class="fw-semibold text-dark" style="font-size:0.78rem;">Visit</span>
                                                </div>
                                                <h6 class="mb-0 fw-bold text-dark">{{ $target->visit }}</h6>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-6 col-md-4">
                                        <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <span class="badge bg-label-info p-1 rounded"><i class="mdi mdi-email-multiple-outline"></i></span>
                                                <span class="fw-semibold text-dark" style="font-size:0.78rem;">Quotation</span>
                                            </div>
                                            <h6 class="mb-0 fw-bold text-dark">{{ $target->quote ?? 0 }}</h6>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <span class="badge bg-label-success p-1 rounded"><i class="mdi mdi-cash-multiple"></i></span>
                                                <span class="fw-semibold text-dark" style="font-size:0.78rem;">Total</span>
                                            </div>
                                            <h6 class="mb-0 fw-bold text-success">Rp {{ number_format($target->total ?? 0, 0, ',', '.') }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- History Position --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">History Position</h5>
                    <button type="button" class="btn btn-sm btn-label-primary waves-effect waves-light"
                        data-bs-toggle="modal" data-bs-target="#newPosition-{{ $users->id }}">
                        <i class="mdi mdi-plus me-1"></i>Update Position
                    </button>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Position</th>
                                <th>Role</th>
                                <th>Area</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($detail as $details)
                                <tr>
                                    <td>{{ $details->date ? \Carbon\Carbon::parse($details->date)->translatedFormat('d F Y') : '-' }}</td>
                                    <td>{{ $details->position ?: '-' }}</td>
                                    <td>
                                        <span class="badge bg-label-{{ $roleColors[$details->roles] ?? 'secondary' }}">
                                            {{ $details->roles ?: '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $details->area ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Tidak ada history posisi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('components.modal.user.form')
    @include('components.modal.user.position')
    @include('components.modal.user.target')
@endsection
@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/pages-account-settings-account.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush
@push('script')
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

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }
            $(".total-label").on('keyup click change', function() {
                var input = $(this)
                var input_val = input.val();

                // original length
                var original_len = input_val.length;

                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);
                input_val = input_val;

                // send updated string to input
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                $(`#semuanya`).val(nomorInt);
                console.log('ini value semuanya :' + $('#semuanya').val());
            });
            $('#ddSales').on('change', function() {
                var role = $(this).val();
                console.log(role);
                if (role == 'Sales') {
                    $('#inputTarget').removeAttr('hidden');
                } else {
                    $('#inputTarget').attr('hidden', true);
                }
            });
        });
        $(document).on('click', '.delete-user', function() {
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
                        'url': '{{ url('employee') }}/' + id,
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
                                    window.location.href = '/employee';
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
    </script>
@endpush
