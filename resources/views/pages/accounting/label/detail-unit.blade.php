@extends('layouts.sales.app')
@section('title', 'Sampul Dokumen — ' . ($invoice->no_invoice ?? '#' . $invoice->id))
@section('content')
    @php
        $isKojisha = ($quote->client?->info ?? $invoice->flag) === 'Kojisha';
        $senderName = auth()->user()?->name ?? 'Staff Accounting';
        $senderRole = auth()->user()?->role ?? 'Accounting';
    @endphp

    <style>
        .sampul-canvas {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08), 0 0 1px 1px rgba(0, 0, 0, 0.04);
            padding: 36px 44px;
            position: relative;
            min-height: 520px;
        }

        .kop-border {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
        }

        .dokumen-badge {
            border: 2.5px solid #0f172a;
            border-radius: 8px;
            padding: 8px 20px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-weight: 800;
            font-size: 1.35rem;
            color: #0f172a;
            background: #ffffff;
            display: inline-block;
        }

        .box-from {
            border: 1.5px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 10px;
            padding: 12px 18px;
            display: inline-block;
            min-width: 260px;
        }

        .box-to {
            border: 2px solid #0f172a;
            background-color: #ffffff;
            border-radius: 14px;
            padding: 22px 26px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        }

        .box-to-table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 0.925rem;
            color: #1e293b;
        }

        .doc-meta-strip {
            border-top: 1px dashed #cbd5e1;
            padding-top: 14px;
            margin-top: 24px;
        }

        @media (max-width: 768px) {
            .sampul-canvas {
                padding: 20px;
            }
            .box-from {
                width: 100%;
            }
        }
    </style>

    <div class="container-fluid flex-grow-1 container-p-y p-0">
        {{-- Header Breadcrumb & Actions --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="mdi mdi-package-variant-closed me-2 text-primary"></i>Label Sampul Dokumen
                </h4>
                <p class="text-muted mb-0 small">Pratinjau label sampul pengiriman dokumen invoice & tagihan</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-label-primary fs-6 px-3 py-2">
                    <i class="mdi mdi-file-document-outline me-1"></i>{{ $invoice->no_invoice ?? 'Draft' }}
                </span>
            </div>
        </div>

        <div class="row invoice-preview">
            {{-- Sampul Preview Sheet --}}
            <div class="col-xl-9 col-lg-8 col-12 mb-lg-0 mb-4">
                <div class="sampul-canvas">
                    {{-- KOP SURAT / HEADER --}}
                    <div class="kop-border mb-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    @if ($isKojisha)
                                        <img src="{{ asset('/asset/logo/Logo-update-size.png') }}"
                                             alt="Kojisha Logo" style="height: 48px; object-fit: contain;">
                                    @else
                                        <img src="{{ asset('/asset/logo/Reftech-Log.png') }}"
                                             alt="Reftech Logo" style="height: 46px; object-fit: contain;">
                                    @endif
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark tracking-wide">
                                        {{ $isKojisha ? 'PT KOJISHA INNOTIV INDONESIA' : 'PT REFTECH JAYA OPTIMA' }}
                                    </h5>
                                    @unless ($isKojisha)
                                        <div class="fw-semibold small my-1" style="font-size: 11px; letter-spacing: 0.05em;">
                                            <span class="text-danger">COMPRESSOR</span> &bull;
                                            <span class="text-success">SPAREPART</span> &bull;
                                            <span class="text-secondary">RENTAL</span> &bull;
                                            <span class="text-info">SERVICE</span>
                                        </div>
                                    @endunless
                                    <div class="text-muted small" style="font-size: 10.5px; line-height: 1.4;">
                                        @if ($isKojisha)
                                            <div>Jl. Nancep No. 45A, Setu, Cibitung – Kab. Bekasi 17320</div>
                                            <div><i class="mdi mdi-phone-outline me-1"></i>+62 812-1000-0997 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1"></i>admin@kojisha.com</div>
                                        @else
                                            <div>Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</div>
                                            <div><i class="mdi mdi-phone-outline me-1"></i>(022) 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1"></i>accounting@reftech.id &nbsp;|&nbsp; www.reftech.id</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="dokumen-badge shadow-xs">
                                    <i class="mdi mdi-email-seal-outline me-1"></i>DOKUMEN
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FROM SECTION --}}
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-4 gap-3">
                        <div class="box-from shadow-xs">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="mdi mdi-send-check text-primary"></i>
                                <span class="fw-bold text-uppercase text-muted small" style="letter-spacing: 0.05em;">FROM (Pengirim):</span>
                            </div>
                            <div class="ps-3 border-start border-2 border-primary">
                                <div class="fw-bold text-dark fs-6">Mr. {{ $senderName }}</div>
                                <div class="text-muted small">{{ $senderRole }} &bull; {{ $isKojisha ? 'PT Kojisha Innotiv Indonesia' : 'PT Reftech Jaya Optima' }}</div>
                            </div>
                        </div>

                        {{-- DOCUMENT QUICK INFO --}}
                        @if ($quote->po_number ?? $invoice->no_po)
                            <div class="text-end text-muted">
                                <div class="fs-6"><strong>PO No:</strong> <span class="text-dark fw-bold">{{ $quote->po_number ?? $invoice->no_po }}</span></div>
                            </div>
                        @endif
                    </div>

                    {{-- SPACING FOR REALISTIC ENVELOPE FEEL (Shifted down) --}}
                    <div style="height: 5.3cm;"></div>

                    {{-- TO / RECIPIENT BOX --}}
                    @php
                        $recipientPic = null;
                        $recipientAddress = null;

                        if (isset($pendingPO) && $pendingPO) {
                            $isCombined = (bool) $pendingPO->combine_shipping_and_parts;
                            if ($isCombined) {
                                // Pengiriman Disatukan: Pakai shipping recipient & shipping address
                                $recipientPic = $pendingPO->shipping_recipient;
                                if (($pendingPO->shipping_address_type ?? 'customer') === 'manual' && $pendingPO->shipping_address_manual) {
                                    $recipientAddress = $pendingPO->shipping_address_manual;
                                }
                            } else {
                                // Pengiriman Dokumen Dipisah: Pakai doc recipient & doc address
                                $recipientPic = $pendingPO->doc_recipient;
                                if (($pendingPO->doc_address_type ?? 'customer') === 'manual' && $pendingPO->doc_address_manual) {
                                    $recipientAddress = $pendingPO->doc_address_manual;
                                }
                            }
                        }

                        $toCompany = $quote->client?->company ?? '-';
                        $toPicName = $recipientPic?->name_pic ?? $quote->pic?->name_pic ?? $quote->attn ?? '-';
                        $toPicPhone = $recipientPic?->phone ?? $recipientPic?->phone_pic ?? $quote->pic?->phone_pic ?? $quote->pic?->phone ?? $quote->client?->phone ?? '-';

                        if (!$recipientAddress) {
                            if ($invoice->invoiceTo == '2' && $quote->client?->subAddress) {
                                $recipientAddress = $quote->client->subAddress;
                            } else {
                                $recipientAddress = $quote->client?->address ?? '-';
                            }
                        }
                    @endphp
                    <div class="row justify-content-end">
                        <div class="col-xl-7 col-lg-8 col-md-10 col-12">
                            <div class="box-to shadow-sm">
                                <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom border-dark">
                                    <span class="fw-bold text-dark text-uppercase fs-6" style="letter-spacing: 0.08em;">
                                        <i class="mdi mdi-map-marker-radius-outline me-1 text-danger"></i>KEPADA YTH. (TO):
                                    </span>
                                </div>
                                <table class="table table-borderless table-sm box-to-table mb-0">
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0" style="width: 105px;">Perusahaan</td>
                                        <td class="px-1">:</td>
                                        <td class="fw-bold text-dark fs-6 pe-0">{{ $toCompany }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">Alamat</td>
                                        <td class="px-1">:</td>
                                        <td class="fw-medium text-dark pe-0 text-wrap lh-base">{{ $recipientAddress }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">Attn. (PIC)</td>
                                        <td class="px-1">:</td>
                                        <td class="fw-bold text-primary pe-0">{{ $toPicName }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">Telepon / HP</td>
                                        <td class="px-1">:</td>
                                        <td class="fw-medium text-dark pe-0">
                                            <i class="mdi mdi-phone me-1 text-muted"></i>{{ $toPicPhone }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- BOTTOM METADATA STRIP --}}
                    <div class="doc-meta-strip d-flex justify-content-end align-items-center text-muted small mt-5">
                        <div class="fst-italic small">
                            Mohon konfirmasi setelah dokumen diterima dengan baik.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Actions --}}
            <div class="col-xl-3 col-lg-4 col-12 invoice-actions">
                <div class="card modern-card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="mdi mdi-printer-outline me-2 text-primary fs-5"></i>Aksi Dokumen
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <a class="btn btn-primary d-flex align-items-center justify-content-center w-100 mb-2 py-2 fw-semibold waves-effect shadow-sm"
                            target="_blank"
                            href="{{ route('invoice.unit.label_print', $invoice->id) }}">
                            <i class="mdi mdi-printer me-2 fs-5"></i>Cetak Sampul
                        </a>

                        <button type="button" 
                                class="btn btn-outline-warning d-flex align-items-center justify-content-center w-100 mb-3 py-2 fw-semibold waves-effect"
                                data-bs-toggle="modal" data-bs-target="#editRecipientModal">
                            <i class="mdi mdi-account-edit-outline me-2 fs-5"></i>Ubah Alamat & PIC
                        </button>

                        <a href="{{ route('invoice.show_unit', $invoice->id) }}"
                           class="btn btn-outline-primary d-flex align-items-center justify-content-center w-100 mb-2 py-2 fw-semibold waves-effect">
                            <i class="mdi mdi-arrow-left-circle-outline me-1 fs-5"></i>Kembali ke Detail Invoice
                        </a>

                        <button class="btn btn-label-secondary d-flex align-items-center justify-content-center w-100 py-2 waves-effect" id="backButton">
                            <i class="mdi mdi-keyboard-backspace me-1"></i>Kembali Sebelumnya
                        </button>
                    </div>
                </div>

                {{-- Delivery Info Card --}}
                <div class="card modern-card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2 text-dark fw-bold small text-uppercase">
                            <i class="mdi mdi-information-outline text-info"></i>Petunjuk Pengiriman
                        </div>
                        <p class="text-muted small mb-0 lh-base">
                            Tempelkan label sampul ini pada bagian depan amplop dokumen fisik invoice sebelum dikirimkan ke pihak kurir/klien.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL UBAH ALAMAT & PIC --}}
    <div class="modal fade" id="editRecipientModal" tabindex="-1" aria-labelledby="editRecipientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('invoice.unit.label_recipient', $invoice->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title text-white d-flex align-items-center fw-bold" id="editRecipientModalLabel">
                            <i class="mdi mdi-account-edit-outline me-2 fs-4"></i>Ubah Alamat & PIC Penerima Label
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
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
                                            $clientPics = $quote->client?->pic ?? collect();
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
                                        <option value="customer_main" data-address="{{ $quote->client?->address ?? '' }}" data-type="customer" data-dest="1"
                                            {{ $recipientAddress === $quote->client?->address ? 'selected' : '' }}>
                                            Alamat Utama: {{ Str::limit($quote->client?->address ?? '-', 75) }}
                                        </option>
                                        @if ($quote->client?->subAddress)
                                            <option value="customer_sub" data-address="{{ $quote->client->subAddress }}" data-type="customer" data-dest="2"
                                                {{ $recipientAddress === $quote->client->subAddress ? 'selected' : '' }}>
                                                Sub Address / NPWP: {{ Str::limit($quote->client->subAddress, 75) }}
                                            </option>
                                        @endif
                                        @if ($quote->client?->plants && count($quote->client->plants) > 0)
                                            @foreach ($quote->client->plants as $plant)
                                                <option value="plant_{{ $plant->id }}" data-address="{{ $plant->address }}" data-type="plant" data-dest="1"
                                                    {{ $recipientAddress === $plant->address ? 'selected' : '' }}>
                                                    Plant: {{ $plant->name }} ({{ Str::limit($plant->address, 65) }})
                                                </option>
                                            @endforeach
                                        @endif
                                        <option value="manual" data-type="manual" data-dest="1"
                                            {{ ($recipientAddress !== $quote->client?->address && $recipientAddress !== $quote->client?->subAddress && !in_array($recipientAddress, $quote->client?->plants?->pluck('address')->toArray() ?? [])) ? 'selected' : '' }}>
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
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
@endpush

@push('script')
    <script>
        $('#backButton').click(function () { window.history.back(); });

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
