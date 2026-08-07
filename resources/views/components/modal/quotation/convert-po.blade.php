<form action="{{ route('convert-po.quotation', $quote->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="convertPo" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <div class="onboarding-content mb-0">
                        <!-- Premium Info Header Card (Sames layout as Sales Order Detail header) -->
                        <div class="p-4 text-white rounded-3 position-relative overflow-hidden mb-4" style="background: linear-gradient(135deg, #696cff 0%, #3f42b3 100%) !important;">
                            <!-- Subtle background circle decorations -->
                            <div class="position-absolute" style="top: -50px; right: -50px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.08); z-index: 1;"></div>
                            <div class="position-absolute" style="bottom: -80px; left: -80px; width: 180px; height: 180px; border-radius: 50%; background: rgba(255,255,255,0.05); z-index: 1;"></div>
                            
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 position-relative" style="z-index: 2;">
                                <div class="text-start">
                                    <span class="badge bg-white text-primary fw-bold text-uppercase px-2.5 py-1 mb-2" style="border-radius: 5px; font-size: 11px;">Quotation</span>
                                    <h4 class="text-white fw-bold mb-1" style="font-size: 20px;">{{ $quote->pic->client->company }}</h4>
                                    <p class="mb-0 text-white opacity-80" style="font-size: 12.5px;">
                                        <i class="mdi mdi-account-circle-outline me-1"></i> PIC: {{ $quote->pic->name }}
                                    </p>
                                </div>
                                <div class="text-start text-sm-end d-flex flex-column align-items-start align-items-sm-end gap-1">
                                    <div class="px-3 py-1.5 rounded text-white font-monospace" style="font-size: 12px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.1);">
                                        <i class="mdi mdi-file-document-outline me-1"></i> {{ $quote->no_quote }}
                                    </div>
                                    <div class="fw-semibold text-white mt-1" style="font-size: 13px;">
                                        Total: <span class="text-warning fw-bold">Rp {{ number_format($quote->harga_total, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Kiri: Informasi PO -->
                            <div class="col-md-6 pe-md-4 border-end">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <div class="form-floating form-floating-outline">
                                            <input class="form-control" type="date" id="po_date" name="po_date"
                                                value="{{ old('po_date', @$quote->po_date ?? now()->format('Y-m-d')) }}">
                                            <label for="po_date">Date PO</label>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="selectWeek" aria-label="Default select example"
                                                name="week">
                                                <option disabled>----- Choose Week -----</option>
                                                <option value="1">Week 1</option>
                                                <option value="2">Week 2</option>
                                                <option value="3">Week 3</option>
                                                <option value="4">Week 4</option>
                                                <option value="5">Week 5</option>
                                            </select>
                                            <label for="selectWeek">Week</label>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="selectType" aria-label="Default select example"
                                                name="type">
                                                <option disabled>----- Choose Type -----</option>
                                                <option value="Non Project"
                                                    {{ $quote->type == 'Sparepart' && $quote->quote_for == 'Sparepart' ? 'selected' : '' }}>
                                                    Non Project</option>
                                                <option value="Project"
                                                    {{ $quote->type != 'Sparepart' && $quote->quote_for != 'Sparepart' ? 'selected' : '' }}>
                                                    Project</option>
                                            </select>
                                            <label for="selectType">Type</label>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="convert_selectEkspedisi"
                                                aria-label="Default select example" name="ekspidisi">
                                                <option>----- Choose Ekspidisi -----</option>
                                                <option value="1">JNT / JNE / Cargo</option>
                                                <option value="2">Send By Technian</option>
                                                <option value="3">Taken Directly</option>
                                                <option value="4">Others</option>
                                            </select>
                                            <label for="convert_selectEkspedisi">Ekspedisi</label>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="NoPending"
                                                placeholder="xxx/xx/xx/xxxx" aria-describedby="floatingInputFilledHelp"
                                                name="no_pending" value="{{ $noPending ?? '' }}">
                                            <label for="NoPending">No Pending</label>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="title"
                                                placeholder="Put your title here"
                                                aria-describedby="floatingInputFilledHelp" name="title" value="{{ old('title', $quote->title) }}">
                                            <label for="title">Title</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea name="note" id="note" class="form-control h-px-80" placeholder="Put Note Here.....">-</textarea>
                                            <label for="note">Note</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Kanan: Alamat Pengiriman -->
                            <div class="col-md-6 ps-md-4 text-start mt-3 mt-md-0">
                                <h5 class="fw-bold text-primary mb-3"><i class="mdi mdi-map-marker-outline me-1"></i> Informasi Alamat Pengiriman</h5>
                                
                                <!-- Toggle switch: Gabung/Pisah Alamat -->
                                <div class="mb-3 p-3 bg-label-light rounded border">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <label class="fw-bold text-dark d-block mb-0" style="font-size: 13.5px;">Gabungkan Alamat Pengiriman?</label>
                                            <small class="text-muted" style="font-size: 11px;">Kirim dokumen & barang ke alamat yang sama.</small>
                                        </div>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" name="combine_shipping_and_parts" id="convert_combine_shipping_and_parts" value="1" checked onchange="toggleAddressLayout('convert')">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- CONTAINER ALAMAT GABUNG -->
                                <div id="convert_combined_address_section">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Alamat Pengiriman (Dokumen & Barang)</label>
                                        <select class="form-select mb-2" id="convert_combined_address_select" onchange="onAddressSelectChange('convert', 'combined')">
                                            <option value="customer">Main Address: {{ $quote->pic->client->address }}</option>
                                            @if (!empty($quote->pic->client->subAddress))
                                                <option value="{{ $quote->pic->client->subAddress }}">Sub Address: {{ $quote->pic->client->subAddress }}</option>
                                            @endif
                                            @foreach ($quote->pic->client->plants as $plant)
                                                <option value="{{ $plant->address }}">Plant: {{ $plant->name }} ({{ $plant->address }})</option>
                                            @endforeach
                                            <option value="manual">-- Alamat Lain (Isi Manual) --</option>
                                        </select>
                                        
                                        <div id="convert_combined_manual_wrapper" class="d-none mt-2">
                                            <textarea class="form-control" name="shipping_address_manual" id="convert_combined_address_manual" rows="3" placeholder="Masukkan alamat manual lengkap..."></textarea>
                                        </div>

                                        <div class="mt-3">
                                            <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Penerima (Dokumen & Barang)</label>
                                            <select class="form-select" id="convert_combined_recipient_select">
                                                @foreach ($quote->pic->client->pic as $c_pic)
                                                    <option value="{{ $c_pic->id }}" {{ $quote->id_pic == $c_pic->id ? 'selected' : '' }}>
                                                        {{ $c_pic->name_pic }} {{ $c_pic->posisi ? '(' . $c_pic->posisi . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div id="convert_combined_charged_wrap" class="card shadow-none bg-transparent border border-label-secondary mt-2" style="display:none;">
                                            <div class="card-body p-3">
                                                <p class="fw-semibold text-start d-block mb-0" style="font-size: 12.5px;">Charged to</p>
                                                <div class="form-check form-check-inline mt-2">
                                                    <input class="form-check-input" type="radio" id="convert_combined_charged_company" value="1">
                                                    <label class="form-check-label" for="convert_combined_charged_company">Company</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="convert_combined_charged_customer" value="2">
                                                    <label class="form-check-label" for="convert_combined_charged_customer">Customer</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CONTAINER ALAMAT PISAH -->
                                <div id="convert_split_address_section" class="d-none">
                                    <!-- Alamat Dokumen -->
                                    <div class="mb-3 pb-3 border-bottom">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Alamat Pengiriman Dokumen / Invoice</label>
                                        <select class="form-select mb-2" id="convert_doc_address_select" onchange="onAddressSelectChange('convert', 'doc')">
                                            <option value="customer">Main Address: {{ $quote->pic->client->address }}</option>
                                            @if (!empty($quote->pic->client->subAddress))
                                                <option value="{{ $quote->pic->client->subAddress }}">Sub Address: {{ $quote->pic->client->subAddress }}</option>
                                            @endif
                                            @foreach ($quote->pic->client->plants as $plant)
                                                <option value="{{ $plant->address }}">Plant: {{ $plant->name }} ({{ $plant->address }})</option>
                                            @endforeach
                                            <option value="manual">-- Alamat Lain (Isi Manual) --</option>
                                        </select>

                                        <div id="convert_doc_manual_wrapper" class="d-none mt-2">
                                            <textarea class="form-control" name="doc_address_manual" id="convert_doc_address_manual" rows="2" placeholder="Masukkan alamat dokumen manual..."></textarea>
                                        </div>

                                        <div class="mt-3">
                                            <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Penerima Dokumen / Invoice</label>
                                            <select class="form-select" id="convert_doc_recipient_select">
                                                @foreach ($quote->pic->client->pic as $c_pic)
                                                    <option value="{{ $c_pic->id }}" {{ $quote->id_pic == $c_pic->id ? 'selected' : '' }}>
                                                        {{ $c_pic->name_pic }} {{ $c_pic->posisi ? '(' . $c_pic->posisi . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div id="convert_doc_charged_wrap" class="card shadow-none bg-transparent border border-label-secondary mt-2" style="display:none;">
                                            <div class="card-body p-3">
                                                <p class="fw-semibold text-start d-block mb-0" style="font-size: 12.5px;">Charged to (Dokumen)</p>
                                                <div class="form-check form-check-inline mt-2">
                                                    <input class="form-check-input" type="radio" id="convert_doc_charged_company" value="1">
                                                    <label class="form-check-label" for="convert_doc_charged_company">Company</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="convert_doc_charged_customer" value="2">
                                                    <label class="form-check-label" for="convert_doc_charged_customer">Customer</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Alamat Barang -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Alamat Pengiriman Barang</label>
                                        <select class="form-select mb-2" id="convert_shipping_address_select" onchange="onAddressSelectChange('convert', 'shipping')">
                                            <option value="customer">Main Address: {{ $quote->pic->client->address }}</option>
                                            @if (!empty($quote->pic->client->subAddress))
                                                <option value="{{ $quote->pic->client->subAddress }}">Sub Address: {{ $quote->pic->client->subAddress }}</option>
                                            @endif
                                            @foreach ($quote->pic->client->plants as $plant)
                                                <option value="{{ $plant->address }}">Plant: {{ $plant->name }} ({{ $plant->address }})</option>
                                            @endforeach
                                            <option value="manual">-- Alamat Lain (Isi Manual) --</option>
                                        </select>

                                        <div id="convert_shipping_manual_wrapper" class="d-none mt-2">
                                            <textarea class="form-control" name="shipping_address_manual" id="convert_shipping_address_manual" rows="2" placeholder="Masukkan alamat barang manual..."></textarea>
                                        </div>

                                        <div class="mt-3">
                                            <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Penerima Barang</label>
                                            <select class="form-select" id="convert_shipping_recipient_select">
                                                @foreach ($quote->pic->client->pic as $c_pic)
                                                    <option value="{{ $c_pic->id }}" {{ $quote->id_pic == $c_pic->id ? 'selected' : '' }}>
                                                        {{ $c_pic->name_pic }} {{ $c_pic->posisi ? '(' . $c_pic->posisi . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div id="convert_shipping_charged_wrap" class="card shadow-none bg-transparent border border-label-secondary mt-2" style="display:none;">
                                            <div class="card-body p-3">
                                                <p class="fw-semibold text-start d-block mb-0" style="font-size: 12.5px;">Charged to (Barang)</p>
                                                <div class="form-check form-check-inline mt-2">
                                                    <input class="form-check-input" type="radio" id="convert_shipping_charged_company" value="1">
                                                    <label class="form-check-label" for="convert_shipping_charged_company">Company</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="convert_shipping_charged_customer" value="2">
                                                    <label class="form-check-label" for="convert_shipping_charged_customer">Customer</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
@push('script')
    <script>
        function onAddressSelectChange(prefix, type) {
            var selectEl = document.getElementById(prefix + '_' + type + '_address_select');
            var wrapperEl = document.getElementById(prefix + '_' + type + '_manual_wrapper');
            
            if (selectEl && selectEl.value === 'manual') {
                if (wrapperEl) wrapperEl.classList.remove('d-none');
            } else {
                if (wrapperEl) wrapperEl.classList.add('d-none');
            }
        }

        // Correlates "Charged to" with the active shipping address layout:
        // combined mode -> single "charged" group; split mode -> separate doc/shipping groups.
        function syncChargedFields(prefix) {
            var ekspedisiEl = document.getElementById(prefix + '_selectEkspedisi');
            var showCharged = !!ekspedisiEl && ekspedisiEl.value === '1';
            var combineCheckbox = document.getElementById(prefix + '_combine_shipping_and_parts');
            var isCombined = !!combineCheckbox && combineCheckbox.checked;

            function setGroup(wrapId, companyId, customerId, name, active) {
                var wrapEl = document.getElementById(wrapId);
                var companyEl = document.getElementById(companyId);
                var customerEl = document.getElementById(customerId);
                if (wrapEl) wrapEl.style.display = active ? '' : 'none';
                [companyEl, customerEl].forEach(function(el) {
                    if (!el) return;
                    if (active) {
                        el.removeAttribute('disabled');
                        el.setAttribute('name', name);
                    } else {
                        el.setAttribute('disabled', 'disabled');
                        el.removeAttribute('name');
                    }
                });
            }

            setGroup(prefix + '_combined_charged_wrap', prefix + '_combined_charged_company', prefix + '_combined_charged_customer', 'charged', showCharged && isCombined);
            setGroup(prefix + '_doc_charged_wrap', prefix + '_doc_charged_company', prefix + '_doc_charged_customer', 'doc_charged', showCharged && !isCombined);
            setGroup(prefix + '_shipping_charged_wrap', prefix + '_shipping_charged_company', prefix + '_shipping_charged_customer', 'shipping_charged', showCharged && !isCombined);
        }

        function toggleAddressLayout(prefix) {
            var combineCheckbox = document.getElementById(prefix + '_combine_shipping_and_parts');
            var combinedSection = document.getElementById(prefix + '_combined_address_section');
            var splitSection = document.getElementById(prefix + '_split_address_section');
            
            var combinedSelect = document.getElementById(prefix + '_combined_address_select');
            var combinedManual = document.getElementById(prefix + '_combined_address_manual');
            var combinedRecipient = document.getElementById(prefix + '_combined_recipient_select');
            
            var docSelect = document.getElementById(prefix + '_doc_address_select');
            var docManual = document.getElementById(prefix + '_doc_address_manual');
            var docRecipient = document.getElementById(prefix + '_doc_recipient_select');
            
            var shippingSelect = document.getElementById(prefix + '_shipping_address_select');
            var shippingManual = document.getElementById(prefix + '_shipping_address_manual');
            var shippingRecipient = document.getElementById(prefix + '_shipping_recipient_select');

            if (combineCheckbox && combineCheckbox.checked) {
                // Show Combined, Hide Split
                if (combinedSection) combinedSection.classList.remove('d-none');
                if (splitSection) splitSection.classList.add('d-none');
                
                // Enable Combined inputs, Disable Split inputs
                if (combinedSelect) {
                    combinedSelect.removeAttribute('disabled');
                    combinedSelect.setAttribute('name', 'shipping_address_type');
                }
                if (combinedManual) {
                    combinedManual.removeAttribute('disabled');
                    combinedManual.setAttribute('name', 'shipping_address_manual');
                }
                if (combinedRecipient) {
                    combinedRecipient.removeAttribute('disabled');
                    combinedRecipient.setAttribute('name', 'shipping_recipient_id');
                }
                
                if (docSelect) {
                    docSelect.setAttribute('disabled', 'disabled');
                    docSelect.removeAttribute('name');
                }
                if (docManual) {
                    docManual.setAttribute('disabled', 'disabled');
                    docManual.removeAttribute('name');
                }
                if (docRecipient) {
                    docRecipient.setAttribute('disabled', 'disabled');
                    docRecipient.removeAttribute('name');
                }
                if (shippingSelect) {
                    shippingSelect.setAttribute('disabled', 'disabled');
                    shippingSelect.removeAttribute('name');
                }
                if (shippingManual) {
                    shippingManual.setAttribute('disabled', 'disabled');
                    shippingManual.removeAttribute('name');
                }
                if (shippingRecipient) {
                    shippingRecipient.setAttribute('disabled', 'disabled');
                    shippingRecipient.removeAttribute('name');
                }
            } else {
                // Show Split, Hide Combined
                if (combinedSection) combinedSection.classList.add('d-none');
                if (splitSection) splitSection.classList.remove('d-none');
                
                // Enable Split inputs, Disable Combined inputs
                if (combinedSelect) {
                    combinedSelect.setAttribute('disabled', 'disabled');
                    combinedSelect.removeAttribute('name');
                }
                if (combinedManual) {
                    combinedManual.setAttribute('disabled', 'disabled');
                    combinedManual.removeAttribute('name');
                }
                if (combinedRecipient) {
                    combinedRecipient.setAttribute('disabled', 'disabled');
                    combinedRecipient.removeAttribute('name');
                }
                
                if (docSelect) {
                    docSelect.removeAttribute('disabled');
                    docSelect.setAttribute('name', 'doc_address_type');
                }
                if (docManual) {
                    docManual.removeAttribute('disabled');
                    docManual.setAttribute('name', 'doc_address_manual');
                }
                if (docRecipient) {
                    docRecipient.removeAttribute('disabled');
                    docRecipient.setAttribute('name', 'doc_recipient_id');
                }
                if (shippingSelect) {
                    shippingSelect.removeAttribute('disabled');
                    shippingSelect.setAttribute('name', 'shipping_address_type');
                }
                if (shippingManual) {
                    shippingManual.removeAttribute('disabled');
                    shippingManual.setAttribute('name', 'shipping_address_manual');
                }
                if (shippingRecipient) {
                    shippingRecipient.removeAttribute('disabled');
                    shippingRecipient.setAttribute('name', 'shipping_recipient_id');
                }
            }
            onAddressSelectChange(prefix, 'combined');
            onAddressSelectChange(prefix, 'doc');
            onAddressSelectChange(prefix, 'shipping');
            syncChargedFields(prefix);
        }

        $(document).ready(function() {
            $('#convert_selectEkspedisi').change(function() {
                syncChargedFields('convert');
            });
            $('#convertPo').on('shown.bs.modal', function () {
                toggleAddressLayout('convert');
            });
            toggleAddressLayout('convert');
        });
    </script>
@endpush
