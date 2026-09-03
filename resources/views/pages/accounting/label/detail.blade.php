@extends('layouts.sales.app')
@section('title', 'Delivery Order')
@section('content')
    @php
        $recipientPic = null;
        $recipientAddress = null;

        if (isset($pendingPO) && $pendingPO) {
            $isCombined = (bool) $pendingPO->combine_shipping_and_parts;
            if ($isCombined) {
                $recipientPic = $pendingPO->shipping_recipient;
                if (($pendingPO->shipping_address_type ?? 'customer') === 'manual' && $pendingPO->shipping_address_manual) {
                    $recipientAddress = $pendingPO->shipping_address_manual;
                }
            } else {
                $recipientPic = $pendingPO->doc_recipient;
                if (($pendingPO->doc_address_type ?? 'customer') === 'manual' && $pendingPO->doc_address_manual) {
                    $recipientAddress = $pendingPO->doc_address_manual;
                }
            }
        }

        $toCompany = $quote->pic?->client?->company ?? '-';
        $toPicName = $recipientPic?->name_pic ?? $quote->pic?->name_pic ?? $quote->attn ?? '-';
        $toPicPhone = $recipientPic?->phone ?? $recipientPic?->phone_pic ?? $quote->pic?->phone_pic ?? $quote->pic?->phone ?? $quote->pic?->client?->phone ?? '-';

        if (!$recipientAddress) {
            if ($invoice->invoiceTo == '2' && $quote->pic?->client?->subAddress) {
                $recipientAddress = $quote->pic->client->subAddress;
            } else {
                $recipientAddress = $quote->pic?->client?->address ?? '-';
            }
        }
    @endphp
    <div class="row invoice-preview">
        {{-- Invoice --}}
        @if ($invoice->flag == 'Reftech')
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body" style="margin-left: 20mm">
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column mb-2 text-black"
                            style="margin-left: 20mm">
                            <div class="mb-xl-0 pb-1" style="border-bottom: 1px solid black; width:70%">
                                <div class="d-flex svg-illustration align-items-center gap-2">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png"
                                                alt="" srcset="" width="60%">
                                        </span>
                                    </span>
                                </div>
                                <p class="mb-1 mx-2 fw-bolder text-black">PT Reftech Jaya Optima</p>
                                <p class="mb-1 mx-2 fw-bolder fs-tiny"><span class="text-danger">Compressor</span> | <span
                                        class="text-success">Sparepart</span> | <span class="text-grey">Rental</span> |
                                    <span class="text-info">Service</span>
                                </p>
                                <p class="mb-1 mx-2 fw-bolder fs-tiny"
                                    style="border-bottom: 1px solid black; width:fit-content;">
                                    Office :</p>
                                <div class="mx-2" style="font-size: 10px">
                                    <p class="mb-1 text-black">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                                    <p class="mb-1 text-black">Bandung – Jawa Barat 40218</p>
                                    <p class="mb-1 text-black">
                                        <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                                        54417653
                                        {{ '   ' }}<i
                                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>accounting@reftech.id
                                    </p>
                                    <p class="mb-1">
                                    </p>
                                </div>
                            </div>
                            <div class="text-end">
                                <h1 class="fw-bold text-black m-2 p-2" style="border: 2px solid black;">Dokumen</h1>
                            </div>
                        </div>
                        <div class="from p-1 text-black"
                            style="border:2px solid black; border-radius:5px; width:25%; margin-left: 20mm">
                            <div class="row">
                                <div class="col-4 pr-0">
                                    <p class="text-black">From :</p>
                                </div>
                                <div class="col-8 px-0">
                                    <p class="mb-0">Rayi</p>
                                    <p class="mb-0 fst-italic">Staff Accounting</p>
                                </div>
                            </div>
                        </div>
                        <div class="my-5"></div>
                        <div class="float-end text-black" id="info-cust"
                            style="border:3px solid black; border-radius:15px; width:40%; margin-top:150px">
                            <div class="row">
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0 pt-1">TO</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 fw-semibold pt-1">: {{ $toCompany }}</p>
                                </div>
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0">ALAMAT</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 ">: {{ $recipientAddress }}</p>
                                </div>
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0">Attn.</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 ">: {{ $toPicName }}</p>
                                </div>
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0">Phone</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 ">: {{ $toPicPhone }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body" style="margin-left: 20mm">
                        <div class="text-center">
                            <div class="row" style="border-bottom: 1px solid black">
                                <div class="col-2">
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                        <span class="app-brand-logo demo">
                                            <span style="color: var(--bs-primary)">
                                                <img class="text-md" src="{{ asset('/asset') }}/logo/Logo-update-size.png"
                                                    alt="" srcset="" width="50%">
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="title">
                                        <p class="mb-1 fs-5 fw-bolder text-black">PT KOJISHA INNOTIV INDONESIA</p>
                                        <p class="mb-1 mx-2 fw-bolder fs-tiny"
                                            style="border-bottom: 1px solid black; display:inline-block">Office :</p>
                                        <div style="font-size: 10px">
                                            <p class="mb-1">Jl. Nancep, RT 01 RW 03 Kampung Cigebang Desa Cibening, Setu
                                            </p>
                                            <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                            <p class="mb-1">
                                                <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62
                                                812-1000-0997
                                                {{ ' | ' }}<i
                                                    class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2"></div>
                            </div>


                            {{-- <div class="d-flex mb-xl-0 pb-1">
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img class="text-md" src="{{ asset('/asset') }}/logo/Logo-update-size.png"
                                                alt="" srcset="" width="60%">
                                        </span>
                                    </span>
                                </div>
                                <div class="title">
                                    <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                                    <p class="mb-1 mx-2 fw-bolder fs-tiny"
                                        style="border-bottom: 1px solid black; display:inline-block">Office :</p>
                                    <div style="font-size: 10px">
                                        <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                                        <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                        <p class="mb-1">
                                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62 812-1000-0997
                                            {{ ' | ' }}<i
                                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                                        </p>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                        <div class="title mt-2" style="border:3px solid black; border-radius:15px; width:25%;">
                            <h1 class="fw-bold text-black text-center m-2">Dokumen</h1>
                        </div>
                        <div class="my-5"></div>
                        <div class="float-end" id="info-cust"
                            style="border:6px double black; width:40%; border-radius:15px; margin-top:200px">
                            <div class="row">
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0 pt-1">TO</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 fw-semibold pt-1">: {{ $toCompany }}</p>
                                </div>
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0">ALAMAT</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 ">: {{ $recipientAddress }}</p>
                                </div>
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0">Attn.</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 ">: {{ $toPicName }}</p>
                                </div>
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0">Phone</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 ">: {{ $toPicPhone }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="btn-group w-100 mb-3">
                        <a class="btn btn-primary waves-effect" target="_blank"
                            href="{{ route('invoice.label_print', $invoice->id) }}">
                            Download
                        </a>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('print.invoice', $invoice->id) }}" target="_blank">
                                    <i class="mdi mdi-file-document-outline me-1"></i> Invoice
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('invoice.label_print', $invoice->id) }}" target="_blank">
                                    <i class="mdi mdi-package-variant-closed me-1"></i> Sampul
                                </a>
                            </li>
                        </ul>
                    </div>
                    <a href="#" class="btn btn-outline-danger d-grid w-100 waves-effect delete-invoice mb-3"
                    <button type="button" 
                            class="btn btn-outline-warning d-grid w-100 mb-3 waves-effect"
                            data-bs-toggle="modal" data-bs-target="#editRecipientModal">
                        <i class="mdi mdi-account-edit-outline me-1"></i>Ubah Alamat & PIC
                    </button>

                    <a href="#" class="btn btn-outline-danger d-grid w-100 waves-effect delete-invoice mb-3"
                        data-id="{{ $quote->id }}">Delete</a>
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <a type="button" data-bs-toggle="modal" data-bs-target="#changeDate"
                        class="d-grid w-100 waves-effect">
                        <button type="button" class="btn btn-secondary">
                            Change Date And Address
                        </button>
                    </a>
                </div>
            </div>
        </div>
        @include('components.modal.accounting.delivery.change-date-label')

        {{-- MODAL UBAH ALAMAT & PIC --}}
        <div class="modal fade" id="editRecipientModal" tabindex="-1" aria-labelledby="editRecipientModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('invoice.label_recipient', $invoice->id) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-primary text-white py-3">
                            <h5 class="modal-title text-white d-flex align-items-center fw-bold" id="editRecipientModalLabel">
                                <i class="mdi mdi-account-edit-outline me-2 fs-4"></i>Ubah Alamat & PIC Penerima Label
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4 text-start">
                            <div class="alert alert-primary d-flex align-items-center mb-4 py-2 px-3 rounded-2" role="alert">
                                <i class="mdi mdi-domain me-2 fs-5"></i>
                                <div>
                                    <span class="fw-bold">Perusahaan Klien:</span> {{ $toCompany }}
                                </div>
                            </div>

                            {{-- SECTION 1: PIC PENERIMA & TELEPON --}}
                            <div class="card border mb-4 bg-light">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                                        <i class="mdi mdi-account-badge-outline text-primary me-2 fs-5"></i>1. Data PIC Penerima & Kontak
                                    </h6>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-dark">Pilih PIC Terdaftar</label>
                                        <select class="form-select" id="modalPicSelect" onchange="onModalPicSelectChange(this)">
                                            @php
                                                $clientPics = $quote->pic?->client?->pic ?? collect();
                                                $hasSelectedPic = false;
                                            @endphp
                                            @foreach ($clientPics as $cpic)
                                                @php
                                                    $isSelected = ($recipientPic && $recipientPic->id === $cpic->id) || (!$recipientPic && ($toPicName === $cpic->name_pic));
                                                    if ($isSelected) { $hasSelectedPic = true; }
                                                @endphp
                                                <option value="{{ $cpic->id }}" 
                                                        data-name="{{ $cpic->name_pic }}" 
                                                        data-phone="{{ $cpic->phone_pic }}"
                                                        {{ $isSelected ? 'selected' : '' }}>
                                                    {{ $cpic->name_pic }} {{ $cpic->position ? '('.$cpic->position.')' : '' }} — {{ $cpic->phone_pic ?: 'No Telp (-)' }}
                                                </option>
                                            @endforeach
                                            <option value="manual" {{ !$hasSelectedPic && $toPicName !== '-' ? 'selected' : '' }}>
                                                -- Input PIC Manual Lain --
                                            </option>
                                        </select>
                                    </div>

                                    <input type="hidden" name="pic_mode" id="modalPicMode" value="{{ $hasSelectedPic ? 'select' : 'manual' }}">
                                    <input type="hidden" name="pic_id" id="modalPicIdHidden" value="{{ $recipientPic?->id }}">

                                    <div id="manualPicContainer" style="{{ $hasSelectedPic ? 'display: none;' : 'display: block;' }}">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-dark">Nama PIC <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="mdi mdi-account"></i></span>
                                                <input type="text" class="form-control" name="manual_pic_name" id="modalPicName" 
                                                       value="{{ $toPicName !== '-' ? $toPicName : '' }}" placeholder="Masukkan nama PIC...">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label fw-semibold text-dark">Nomor Telepon / HP <span class="text-muted small">(Otomatis mengikuti PIC / dapat diedit)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="mdi mdi-phone"></i></span>
                                            <input type="text" class="form-control" name="manual_pic_phone" id="modalPicPhone" 
                                                   value="{{ $toPicPhone !== '-' ? $toPicPhone : '' }}" placeholder="Contoh: 0812-3456-7890">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 2: ALAMAT PENGIRIMAN --}}
                            <div class="card border bg-light mb-0">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                                        <i class="mdi mdi-map-marker-outline text-danger me-2 fs-5"></i>2. Alamat Pengiriman
                                    </h6>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-dark">Pilih Opsi Alamat Client</label>
                                        <select class="form-select" name="address_preset" id="modalAddressSelect" onchange="onModalAddressSelectChange(this)">
                                            <option value="customer_main" data-address="{{ $quote->pic?->client?->address ?? '' }}" data-type="customer" data-dest="1"
                                                {{ $recipientAddress === $quote->pic?->client?->address ? 'selected' : '' }}>
                                                Alamat Utama: {{ Str::limit($quote->pic?->client?->address ?? '-', 75) }}
                                            </option>
                                            @if ($quote->pic?->client?->subAddress)
                                                <option value="customer_sub" data-address="{{ $quote->pic->client->subAddress }}" data-type="customer" data-dest="2"
                                                    {{ $recipientAddress === $quote->pic->client->subAddress ? 'selected' : '' }}>
                                                    Sub Address / NPWP: {{ Str::limit($quote->pic->client->subAddress, 75) }}
                                                </option>
                                            @endif
                                            @if ($quote->pic?->client?->plants && count($quote->pic->client->plants) > 0)
                                                @foreach ($quote->pic->client->plants as $plant)
                                                    <option value="plant_{{ $plant->id }}" data-address="{{ $plant->address }}" data-type="plant" data-dest="1"
                                                        {{ $recipientAddress === $plant->address ? 'selected' : '' }}>
                                                        Plant: {{ $plant->name }} ({{ Str::limit($plant->address, 65) }})
                                                    </option>
                                                @endforeach
                                            @endif
                                            <option value="manual" data-type="manual" data-dest="1"
                                                {{ ($recipientAddress !== $quote->pic?->client?->address && $recipientAddress !== $quote->pic?->client?->subAddress && !in_array($recipientAddress, $quote->pic?->client?->plants?->pluck('address')->toArray() ?? [])) ? 'selected' : '' }}>
                                                -- Input Alamat Manual / Kustom --
                                            </option>
                                        </select>
                                    </div>

                                    <input type="hidden" name="address_type" id="modalAddressType" value="manual">
                                    <input type="hidden" name="destination" id="modalDestination" value="{{ $invoice->invoiceTo ?? '1' }}">

                                    <div class="mb-0">
                                        <label class="form-label fw-semibold text-dark">Alamat Lengkap Dokumen <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="address_manual" id="modalAddress" rows="3" 
                                                  placeholder="Tuliskan alamat lengkap pengiriman dokumen di sini...">{{ $recipientAddress !== '-' ? $recipientAddress : '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-white border-top py-3">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="mdi mdi-close me-1"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-primary fw-semibold shadow-sm">
                                <i class="mdi mdi-check-circle-outline me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection
    @push('after-style')
        <!-- Page CSS -->
        <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
        <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
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
            $('#backButton').click(function() {
                window.history.back();
            });

            const dateInput = document.getElementById('dateInput');
            const resetCheckbox = document.getElementById('checkDate');

            if (resetCheckbox && dateInput) {
                // Saat checkbox di-check
                resetCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        dateInput.value = ''; // Hapus nilai date
                    }
                });

                // Saat input tanggal diisi
                dateInput.addEventListener('input', function() {
                    if (this.value) {
                        resetCheckbox.checked = false; // Uncheck checkbox
                    }
                });
            }

            function onModalPicSelectChange(select) {
                var opt = select.options[select.selectedIndex];
                var isManual = (select.value === 'manual');
                
                var manualContainer = document.getElementById('manualPicContainer');
                var picNameInput    = document.getElementById('modalPicName');
                var picPhoneInput   = document.getElementById('modalPicPhone');
                var picModeHidden   = document.getElementById('modalPicMode');
                var picIdHidden     = document.getElementById('modalPicIdHidden');
                
                if (isManual) {
                    manualContainer.style.display = 'block';
                    picModeHidden.value = 'manual';
                    picIdHidden.value   = '';
                    picNameInput.focus();
                } else {
                    manualContainer.style.display = 'none';
                    picModeHidden.value = 'select';
                    picIdHidden.value   = select.value;
                    picNameInput.value  = opt.getAttribute('data-name') || '';
                    picPhoneInput.value = opt.getAttribute('data-phone') || '';
                }
            }

            function onModalAddressSelectChange(select) {
                var opt = select.options[select.selectedIndex];
                var addr = opt.getAttribute('data-address') || '';
                var type = opt.getAttribute('data-type') || 'manual';
                var dest = opt.getAttribute('data-dest') || '1';

                document.getElementById('modalAddressType').value = type;
                document.getElementById('modalDestination').value = dest;

                var addressTextarea = document.getElementById('modalAddress');
                if (select.value !== 'manual' && addr) {
                    addressTextarea.value = addr;
                } else if (select.value === 'manual') {
                    addressTextarea.focus();
                }
            }
        </script>
    @endpush
