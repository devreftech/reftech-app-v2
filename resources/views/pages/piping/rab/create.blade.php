@extends('layouts.sales.app')
@section('title', 'Estimasi / RAB Piping Baru')

@section('hide-chat') @endsection

@push('before-style')
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
<style>
    html, body {
        max-width: 100vw !important;
        overflow-x: hidden !important;
    }
    .layout-wrapper, .layout-container, .layout-page, .content-wrapper {
        max-width: 100% !important;
        overflow-x: hidden !important;
    }
    .content-wrapper > .container-fluid {
        max-width: 100% !important;
        overflow-x: hidden !important;
        box-sizing: border-box !important;
    }
    .rf-chat-widget-wrapper {
        display: none !important;
    }
    .rab-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        overflow: hidden;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    .section-header-clean {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 20px;
    }
    .table-responsive {
        width: 100% !important;
        overflow-x: auto !important;
    }
    .rab-table {
        width: 100% !important;
        margin: 0 !important;
    }
    .rab-table th {
        background: #f1f5f9;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
        padding: 10px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #cbd5e1;
    }
    .rab-table td {
        padding: 10px 12px;
        vertical-align: middle;
        background: #ffffff;
        border-color: #f1f5f9;
    }
    .rab-table tr:hover td {
        background: #f8fafc;
    }
    .form-control-clean, .form-select-clean {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 12.5px;
        padding: 5px 8px;
        background-color: #ffffff;
        color: #1e293b;
        transition: all 0.15s;
    }
    .form-control-clean:focus, .form-select-clean:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        background-color: #ffffff;
    }
    .pipe-badge-calc {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 6px;
        padding: 4px 6px;
        font-size: 11px;
    }
    .sticky-summary-bar {
        position: fixed;
        bottom: 20px;
        left: calc(16.25rem + 1.5rem);
        right: 1.5rem;
        z-index: 1040;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid #0284c7;
        box-shadow: 0 10px 30px rgba(2, 132, 199, 0.22);
        transition: left 0.2s ease-in-out, right 0.2s ease-in-out;
    }
    .layout-menu-collapsed .sticky-summary-bar {
        left: calc(4.5rem + 1.5rem);
    }
    @media (max-width: 1199.98px) {
        .sticky-summary-bar {
            left: 1rem;
            right: 1rem;
            bottom: 12px;
        }
    }
    #formRab {
        padding-bottom: 100px;
    }
    .autosave-pill {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
        background: #ecfdf5;
        color: #059669;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-weight: 600;
    }
    /* Select2 integration styling */
    .select2-container .select2-selection--single {
        height: 33px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        padding: 2px 4px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 27px !important;
        font-size: 12.5px !important;
        color: #1e293b !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 31px !important;
    }
    .select2-dropdown {
        border-color: #cbd5e1 !important;
        font-size: 12.5px !important;
        box-shadow: 0 4px 14px rgba(0,0,0,0.08) !important;
        z-index: 1060 !important;
    }
    .select2-container--default .select2-results__group {
        font-size: 11px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        color: #0284c7 !important;
        background: #f1f5f9 !important;
        padding: 5px 8px !important;
        letter-spacing: 0.5px !important;
    }
</style>
@endpush

@section('content')
    <!-- Top Nav & Breadcrumb Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h4 class="fw-bold mb-0 text-dark"><i class="mdi mdi-calculator-variant text-primary me-2"></i>Kalkulator Estimasi / RAB Piping</h4>
                <span class="autosave-pill" id="autoSaveIndicator">
                    <i class="mdi mdi-check-circle" style="font-size: 14px;"></i> Autosave Aktif
                </span>
            </div>
            <p class="text-muted mb-0 small">Kalkulasi presisi HPP, konversi panjang meter ke batang (+waste), margin, dan opsi multi-vendor.</p>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnResetDraft" onclick="resetAutoDraft()" title="Hapus draft dan mulai dari awal">
                <i class="mdi mdi-refresh me-1"></i> Reset Draft
            </button>
            <a href="{{ route('piping-rab.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Alert Banner when draft is restored -->
    <div class="alert alert-info alert-dismissible fade show d-none mb-3 py-2 px-3 shadow-sm" id="draftRestoredAlert" role="alert">
        <div class="d-flex align-items-center justify-content-between">
            <div class="small">
                <i class="mdi mdi-restore me-1"></i> <strong>Draft Dipulihkan:</strong> Data yang sebelumnya Anda input telah dimuat kembali secara otomatis.
            </div>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>

    <form id="formRab" action="{{ route('piping-rab.store') }}" method="POST">
        @csrf

        <!-- 1. General Project Information (Sales -> Customer -> PIC Flow) -->
        <div class="rab-card mb-4">
            <div class="section-header-clean d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded px-2 py-1"><i class="mdi mdi-domain fs-6"></i></span>
                    <span class="fw-bold text-dark fs-6">Informasi Proyek & Klien</span>
                </div>
                <span class="badge bg-label-primary fw-bold px-3 py-2">{{ $noRab }}</span>
                <input type="hidden" name="no_rab" value="{{ $noRab }}">
            </div>

            <div class="p-4">
                <div class="row g-3">
                    <!-- 1. Pilih Sales Person Terlebih Dahulu -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-primary required">
                            <i class="mdi mdi-account-tie me-1"></i> 1. Pilih Sales Person
                        </label>
                        <select name="id_sales" id="selectSales" class="form-select form-select-clean fw-semibold" required onchange="onSalesChanged(this.value)">
                            <option value="">-- Pilih Sales Dulu --</option>
                            @foreach($salesList as $s)
                                <option value="{{ $s->id }}" {{ Auth::id() == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Customer / Perusahaan (Searchable via Select2) -->
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-primary required">
                            <i class="mdi mdi-domain me-1"></i> 2. Customer / Perusahaan (Ketik Cari PT)
                        </label>
                        <select name="id_client" id="selectClient" class="form-select form-select-clean select2-client" required>
                            <option value="">-- Pilih Sales Terlebih Dahulu --</option>
                        </select>
                    </div>

                    <!-- 3. PIC Customer -->
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">
                            <i class="mdi mdi-card-account-details-outline me-1"></i> 3. PIC Customer
                        </label>
                        <select name="id_pic" id="selectPic" class="form-select form-select-clean">
                            <option value="">-- Pilih PIC --</option>
                        </select>
                    </div>

                    <!-- 4. Tanggal Estimasi -->
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted required">
                            <i class="mdi mdi-calendar me-1"></i> Tanggal
                        </label>
                        <input type="date" name="rab_date" id="inputRabDate" class="form-control form-control-clean" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- 5. Nama Proyek -->
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted required">Nama Pekerjaan / Proyek</label>
                        <input type="text" name="project_name" id="inputProjectName" class="form-control form-control-clean" placeholder="Contoh: Instalasi Piping Kompresor Jalur Utama & Drop Point" required oninput="triggerAutoSave()">
                    </div>

                    <!-- 6. Lokasi / Area Plant -->
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Lokasi / Area Plant</label>
                        <input type="text" name="location_plant" id="inputLocationPlant" class="form-control form-control-clean" placeholder="Contoh: Plant B - Cikarang / Workshop Utility" oninput="triggerAutoSave()">
                    </div>

                    <!-- 7. Catatan Teknis Internal -->
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Catatan Teknis Internal</label>
                        <textarea name="notes" id="inputNotes" class="form-control form-control-clean" rows="1" placeholder="Asumsi teknis, ketinggian instalasi pipa, safety permit, jadwal weekend..." oninput="triggerAutoSave()"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Sections Container (Dynamic Plant/Area Blocks) -->
        <div id="sectionsContainer">
            <!-- Sections populated dynamically -->
        </div>

        <!-- Add Section Button -->
        <div class="text-center mb-5">
            <button type="button" id="btnAddSection" class="btn btn-outline-primary shadow-sm px-4 py-2" style="border-style: dashed; border-width: 2px;">
                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Area / Section Pekerjaan Baru
            </button>
        </div>

        <!-- 3. Sticky Bottom Grand Summary (Clean & Minimalist) -->
        <div class="sticky-summary-bar p-3 mb-4">
            <div class="row align-items-center gx-3 gy-2 m-0 p-0">
                <div class="col-lg-8 d-flex flex-wrap align-items-center gap-4">
                    <div>
                        <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Total HPP Modal</small>
                        <span class="fs-5 fw-bold text-dark" id="displayGrandHpp">Rp 0</span>
                    </div>
                    <div class="border-start ps-4">
                        <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Gross Margin Proyek</small>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="fs-5 fw-bold text-success" id="displayGrandMargin">Rp 0</span>
                            <span class="badge bg-label-success" id="displayGrandMarginPercent" style="font-size: 10px;">+0%</span>
                        </div>
                    </div>
                    <div class="border-start ps-4">
                        <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Grand Total Harga Jual</small>
                        <span class="fs-4 fw-bold text-primary" id="displayGrandSelling">Rp 0</span>
                    </div>
                </div>
                <div class="col-lg-4 text-end d-flex justify-content-end align-items-center gap-2">
                    <span class="small text-muted d-none d-xl-inline me-2" id="lastSavedTime" style="font-size: 11px;">Otomatis tersimpan</span>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Estimasi RAB
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('after-script')
<script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
<script>
    const STORAGE_KEY = 'reftech_piping_rab_create_draft';
    const rawMaterials = @json($materials);
    const materialMap = {};
    rawMaterials.forEach(m => {
        materialMap[m.id] = m;
    });

    let sectionCounter = 0;
    let itemCounter = 0;

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Select2 on Client Dropdown
        initSelect2Client();

        // Setup Heartbeat keep-alive (pings server every 5 minutes)
        setInterval(function () {
            fetch('/piping-materials/search-api?category=pipe&limit=1')
                .then(() => {
                    const indicator = document.getElementById('autoSaveIndicator');
                    if (indicator) {
                        indicator.innerHTML = '<i class="mdi mdi-check-circle" style="font-size: 14px;"></i> Sesi Aktif & Saved';
                    }
                })
                .catch(() => console.log('Keep-alive ping error'));
        }, 300000);

        // PIC Loader on Client Change (Using jQuery for Select2 compatibility)
        $('#selectClient').on('change', function () {
            loadPicsForClient($(this).val());
            triggerAutoSave();
        });

        // Add Section Button
        document.getElementById('btnAddSection').addEventListener('click', function () {
            addSection(`Area / Section ${sectionCounter + 1}`);
            triggerAutoSave();
        });

        // Form Submit Handler
        document.getElementById('formRab').addEventListener('submit', function () {
            localStorage.removeItem(STORAGE_KEY);
        });

        // Check if draft exists in LocalStorage
        const savedDraft = localStorage.getItem(STORAGE_KEY);
        if (savedDraft) {
            try {
                const parsed = JSON.parse(savedDraft);
                restoreDraft(parsed);
                const alertElem = document.getElementById('draftRestoredAlert');
                if (alertElem) alertElem.classList.remove('d-none');
            } catch (e) {
                console.error('Error parsing draft:', e);
                initInitialFlow();
            }
        } else {
            initInitialFlow();
        }
    });

    function initSelect2Client() {
        if ($('#selectClient').data('select2')) {
            $('#selectClient').select2('destroy');
        }
        $('#selectClient').select2({
            placeholder: '-- Cari / Pilih Customer (Ketik Nama PT) --',
            allowClear: true,
            width: '100%'
        });
    }

    function initInitialFlow() {
        const initialSalesId = document.getElementById('selectSales').value;
        if (initialSalesId) {
            onSalesChanged(initialSalesId);
        }
        initDefaultSection();
    }

    function initDefaultSection() {
        addSection('Material & Jalur Utama (Section 1)');
    }

    // When Sales Dropdown Changes -> Load Clients of this Sales
    function onSalesChanged(salesId, preselectedClientId = null, preselectedPicId = null) {
        const selectClient = $('#selectClient');
        selectClient.html('<option value="">-- Loading Customer... --</option>');

        if (!salesId) {
            selectClient.html('<option value="">-- Pilih Sales Terlebih Dahulu --</option>');
            initSelect2Client();
            return;
        }

        fetch(`/smart-quote/clients-by-sales/${salesId}`)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">-- Cari / Pilih Customer (Ketik Nama PT) --</option>';
                if (data.clients && data.clients.length > 0) {
                    data.clients.forEach(c => {
                        const isSel = preselectedClientId && preselectedClientId == c.id ? 'selected' : '';
                        options += `<option value="${c.id}" ${isSel}>${c.company}</option>`;
                    });
                } else {
                    options += '<option value="">Tidak ada customer untuk sales ini</option>';
                }
                selectClient.html(options);
                initSelect2Client();

                if (preselectedClientId) {
                    selectClient.val(preselectedClientId).trigger('change');
                    loadPicsForClient(preselectedClientId, preselectedPicId);
                }
            })
            .catch(() => {
                selectClient.html('<option value="">-- Gagal memuat customer --</option>');
                initSelect2Client();
            });
    }

    function loadPicsForClient(clientId, selectedPicId = null) {
        const selectPic = document.getElementById('selectPic');
        if (!selectPic) return;
        selectPic.innerHTML = '<option value="">-- Loading PIC... --</option>';

        if (!clientId) {
            selectPic.innerHTML = '<option value="">-- Pilih PIC --</option>';
            return;
        }

        fetch(`/smart-quote/pics/${clientId}`)
            .then(res => res.json())
            .then(data => {
                const picList = Array.isArray(data) ? data : (data.pics || []);
                let options = '<option value="">-- Pilih PIC --</option>';
                if (picList.length > 0) {
                    picList.forEach(p => {
                        const isSel = (selectedPicId && selectedPicId == p.id) ? 'selected' : '';
                        const picName = p.name_pic || p.name || ('PIC #' + p.id);
                        const picPos = p.position ? ` (${p.position})` : '';
                        options += `<option value="${p.id}" ${isSel}>${picName}${picPos}</option>`;
                    });
                } else {
                    options = '<option value="">-- Tidak ada data PIC --</option>';
                }
                selectPic.innerHTML = options;
            })
            .catch(err => {
                console.error('Error loading PIC:', err);
                selectPic.innerHTML = '<option value="">-- Gagal memuat PIC --</option>';
            });
    }

    // Add Section (Clean Card with HTML Table)
    function addSection(defaultName = 'Section') {
        const sIndex = sectionCounter++;
        const container = document.getElementById('sectionsContainer');

        const sectionHtml = `
            <div class="rab-card" id="section_${sIndex}" data-section-index="${sIndex}">
                <!-- Section Header -->
                <div class="section-header-clean d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 620px;">
                        <span class="badge bg-primary rounded p-1" title="Area Section"><i class="mdi mdi-folder-open-outline fs-6"></i></span>
                        <div class="input-group input-group-sm flex-grow-1 shadow-sm">
                            <span class="input-group-text bg-white text-muted fw-semibold border-end-0" style="font-size: 11.5px;">
                                <i class="mdi mdi-pencil-outline text-primary me-1"></i> Head Title Penawaran:
                            </span>
                            <input type="text" name="sections[${sIndex}][name]" class="form-control form-control-sm fw-bold text-dark border-start-0 section-name-input bg-white" value="${defaultName}" placeholder="Ketik judul section / head title penawaran..." oninput="triggerAutoSave()" title="Klik untuk mengubah judul section (akan otomatis menjadi Head Title pada Surat Penawaran)" required>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end d-none d-md-block">
                            <span class="badge bg-label-primary px-3 py-2 fw-bold fs-6" id="sec_subtotal_sell_${sIndex}">Subtotal Jual: Rp 0</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeSection(${sIndex})" title="Hapus Section Ini">
                            <i class="mdi mdi-trash-can-outline fs-5"></i>
                        </button>
                    </div>
                </div>

                <!-- Section Items Table -->
                <div class="table-responsive">
                    <table class="table rab-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 35px;" class="text-center">#</th>
                                <th style="width: 27%;">Item & Spesifikasi</th>
                                <th style="width: 20%;">Kalkulasi Meter / Qty</th>
                                <th style="width: 21%;">Supplier & HPP Modal (Rp)</th>
                                <th style="width: 14%;">Margin Laba</th>
                                <th style="width: 14%;" class="text-end">Total Jual (Rp)</th>
                                <th style="width: 40px;" class="text-center"><i class="mdi mdi-cog-outline"></i></th>
                            </tr>
                        </thead>
                        <tbody id="items_container_${sIndex}">
                            <!-- Item rows injected here -->
                        </tbody>
                    </table>
                </div>

                <!-- Section Footer Buttons -->
                <div class="p-3 bg-white border-top d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-top: 1px solid #e2e8f0 !important;">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-primary px-3 shadow-xs" onclick="addItem(${sIndex}, 'material')">
                            <i class="mdi mdi-pipe me-1"></i> + Material Pipa/Fitting
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success px-3" onclick="addItem(${sIndex}, 'service')">
                            <i class="mdi mdi-wrench-outline me-1"></i> + Jasa Instalasi
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info px-3" onclick="addItem(${sIndex}, 'other')">
                            <i class="mdi mdi-plus-box-outline me-1"></i> + Alat / Custom
                        </button>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-label-secondary px-3 py-2 fw-semibold" style="font-size: 12px;">
                            Subtotal HPP Modal: <strong class="text-dark ms-1" id="sec_subtotal_hpp_${sIndex}">Rp 0</strong>
                        </span>
                    </div>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', sectionHtml);

        // Add 1 default material row
        addItem(sIndex, 'material');
        return sIndex;
    }

    function removeSection(sIndex) {
        const totalSections = document.querySelectorAll('.rab-card').length;
        if (totalSections <= 1) {
            alert('Minimal harus ada 1 Section dalam RAB!');
            return;
        }
        if (confirm('Hapus section ini beserta seluruh item di dalamnya?')) {
            const sec = document.getElementById(`section_${sIndex}`);
            if (sec) sec.remove();
            recalculateGrandTotals();
            triggerAutoSave();
        }
    }

    function renderMaterialSelectOptions(selectedId = '') {
        const categories = {
            'pipe': 'Pipa (Pipa Galvanis, Airnet, SS304, dll)',
            'fitting': 'Fitting & Sambungan (Elbow, Tee, Union, Quick Drop)',
            'valve': 'Valve & Katup (Ball Valve, Butterfly, dll)',
            'support': 'Support & Bracket (Unistrut, As Drat, U-Bolt)',
            'consumable': 'Consumables & Sealing (Sealtape, Gasket, dll)',
            'other': 'Lainnya'
        };

        let html = '<option value="">-- 🔍 Cari & Pilih Material (Ketik Nama / Ukuran) --</option>';

        Object.keys(categories).forEach(catKey => {
            const items = rawMaterials.filter(m => m.category === catKey);
            if (items.length > 0) {
                html += `<optgroup label="${categories[catKey]}">`;
                items.forEach(m => {
                    const isSel = selectedId && selectedId == m.id ? 'selected' : '';
                    const sizeLabel = m.size ? ` - ${m.size}` : '';
                    const typeLabel = m.material_type ? ` [${m.material_type}]` : '';
                    html += `<option value="${m.id}" ${isSel}>${m.item_name}${sizeLabel}${typeLabel}</option>`;
                });
                html += `</optgroup>`;
            }
        });

        return html;
    }

    function formatRupiahNumber(val) {
        if (val === null || val === undefined || val === '') return '';
        const num = Math.round(parseFloat(String(val).replace(/[^0-9]/g, ''))) || 0;
        if (num === 0) return '0';
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function parseRupiahNumber(val) {
        if (!val) return 0;
        const clean = String(val).replace(/[^0-9]/g, '');
        return parseFloat(clean) || 0;
    }

    function onHppInput(elem, sIndex, iIndex) {
        const raw = elem.value.replace(/[^0-9]/g, '');
        if (raw) {
            const formatted = new Intl.NumberFormat('id-ID').format(parseInt(raw, 10));
            elem.value = formatted;
        } else {
            elem.value = '';
        }
        calcRowTotal(sIndex, iIndex);
    }

    // Add Item Row (HTML <tr> Table Row)
    function addItem(sIndex, itemType = 'material', initialData = null) {
        const iIndex = itemCounter++;
        const itemsContainer = document.getElementById(`items_container_${sIndex}`);
        if (!itemsContainer) return;

        const rowNumber = itemsContainer.querySelectorAll('tr').length + 1;

        const matId = initialData ? initialData.id_piping_material : '';
        const itemName = initialData ? initialData.item_name : (itemType === 'service' ? 'Jasa Instalasi & Pengelasan Piping' : '');
        const size = initialData ? initialData.size : '';
        const spec = initialData ? initialData.spec : '';
        const unit = initialData ? initialData.unit : (itemType === 'material' ? 'Batang' : (itemType === 'service' ? 'Lot' : 'Pcs'));
        const inputMeter = initialData ? initialData.input_length_meter : '';
        const lengthPerUnit = initialData ? initialData.length_per_unit : 6.00;
        const wastePercent = initialData && initialData.waste_percent !== null && initialData.waste_percent !== undefined ? initialData.waste_percent : 5;
        const qty = initialData ? parseFloat(initialData.calculated_qty) : 1;
        const hpp = initialData && initialData.unit_price_hpp ? Math.round(parseFloat(initialData.unit_price_hpp)) : 0;
        const idSupplier = initialData ? initialData.id_supplier : '';
        const marginType = initialData ? initialData.margin_type : 'percent';
        const marginValue = initialData && initialData.margin_value !== null && initialData.margin_value !== undefined ? parseFloat(initialData.margin_value) : 25;
        const unitSell = initialData && initialData.unit_selling_price ? Math.round(parseFloat(initialData.unit_selling_price)) : 0;

        const rowHtml = `
            <tr id="item_row_${sIndex}_${iIndex}" data-section="${sIndex}" data-item="${iIndex}">
                <!-- Col 1: Index Number -->
                <td class="text-center text-muted fw-semibold small row-idx-num">${rowNumber}</td>

                <!-- Col 2: Item Selection & Description -->
                <td>
                    <input type="hidden" name="sections[${sIndex}][items][${iIndex}][item_type]" value="${itemType}">
                    ${itemType === 'material' ? `
                        <div class="mb-1">
                            <select name="sections[${sIndex}][items][${iIndex}][id_piping_material]" id="mat_select_${sIndex}_${iIndex}" class="form-select-clean form-select-sm w-100 material-selector">
                                ${renderMaterialSelectOptions(matId)}
                            </select>
                        </div>
                        <input type="text" name="sections[${sIndex}][items][${iIndex}][item_name]" class="form-control-clean form-control-sm w-100 item-name-input" value="${itemName}" placeholder="Deskripsi/keterangan..." oninput="triggerAutoSave()">
                    ` : `
                        <input type="text" name="sections[${sIndex}][items][${iIndex}][item_name]" class="form-control-clean form-control-sm w-100 item-name-input fw-semibold mb-1" value="${itemName}" placeholder="Nama pekerjaan / alat..." oninput="triggerAutoSave()" required>
                        <input type="text" name="sections[${sIndex}][items][${iIndex}][spec]" class="form-control-clean form-control-sm w-100" value="${spec || ''}" placeholder="Spesifikasi / detail tambahan..." oninput="triggerAutoSave()">
                    `}
                    <input type="hidden" name="sections[${sIndex}][items][${iIndex}][size]" class="item-size-input" value="${size || ''}">
                </td>

                <!-- Col 3: Meter Calculation & Final Qty -->
                <td>
                    <div class="pipe-badge-calc ${itemType === 'material' ? '' : 'd-none'} mb-1">
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-muted fw-semibold" style="font-size: 11px;">Mtr:</span>
                            <input type="number" step="0.1" name="sections[${sIndex}][items][${iIndex}][input_length_meter]" class="form-control-clean form-control-sm input-meter text-center px-1" value="${inputMeter || ''}" placeholder="0" oninput="calcPipeQty(${sIndex}, ${iIndex})" style="width: 68px;" title="Kebutuhan panjang dalam meter">
                            <span class="text-muted fw-bold" style="font-size: 11px;">+</span>
                            <input type="number" step="0.5" name="sections[${sIndex}][items][${iIndex}][waste_percent]" class="form-control-clean form-control-sm input-waste text-center px-1" value="${wastePercent}" placeholder="5" oninput="calcPipeQty(${sIndex}, ${iIndex})" title="Waste %" style="width: 58px;">
                            <span class="text-muted fw-semibold" style="font-size: 11px;">%</span>
                        </div>
                        <div class="mt-1 text-muted" style="font-size: 10px;">
                            1 btg = <span class="length-display fw-semibold text-dark">${lengthPerUnit}</span>m
                        </div>
                        <input type="hidden" name="sections[${sIndex}][items][${iIndex}][length_per_unit]" class="input-length-per-unit" value="${lengthPerUnit}">
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <input type="number" step="0.01" name="sections[${sIndex}][items][${iIndex}][calculated_qty]" class="form-control-clean form-control-sm input-qty text-center fw-bold text-primary" value="${qty}" min="0.01" oninput="calcRowTotal(${sIndex}, ${iIndex})" style="width: 72px;" title="Jumlah Qty" required>
                        <input type="text" name="sections[${sIndex}][items][${iIndex}][unit]" class="form-control-clean form-control-sm input-unit text-center bg-light" value="${unit}" style="width: 78px; cursor: not-allowed; font-weight: 500;" readonly title="Satuan otomatis dari material">
                    </div>
                </td>

                <!-- Col 4: Supplier & HPP Modal -->
                <td>
                    <select name="sections[${sIndex}][items][${iIndex}][id_supplier]" class="form-select-clean form-select-sm w-100 vendor-select mb-1" onchange="onVendorChanged(${sIndex}, ${iIndex}, this)">
                        <option value="">-- Pilih Supplier --</option>
                    </select>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text py-0 bg-light text-muted" style="font-size: 11px;">HPP</span>
                        <input type="text" name="sections[${sIndex}][items][${iIndex}][unit_price_hpp]" class="form-control form-control-sm input-hpp" value="${formatRupiahNumber(hpp)}" placeholder="0" oninput="onHppInput(this, ${sIndex}, ${iIndex})">
                    </div>
                </td>

                <!-- Col 5: Margin Laba -->
                <td>
                    <div class="input-group input-group-sm mb-1">
                        <select name="sections[${sIndex}][items][${iIndex}][margin_type]" class="form-select form-select-sm margin-type p-1" onchange="calcRowTotal(${sIndex}, ${iIndex})" style="max-width: 50px;">
                            <option value="percent" ${marginType === 'percent' ? 'selected' : ''}>%</option>
                            <option value="nominal" ${marginType === 'nominal' ? 'selected' : ''}>Rp</option>
                        </select>
                        <input type="number" step="0.5" name="sections[${sIndex}][items][${iIndex}][margin_value]" class="form-control form-control-sm margin-value" value="${marginValue}" placeholder="Margin" oninput="calcRowTotal(${sIndex}, ${iIndex})">
                    </div>
                    <small class="text-muted d-block" style="font-size: 10px;">Jual/unit: <span class="fw-semibold text-dark unit-sell-display">Rp 0</span></small>
                    <input type="hidden" name="sections[${sIndex}][items][${iIndex}][unit_selling_price]" class="input-unit-selling-price" value="${unitSell}">
                </td>

                <!-- Col 6: Subtotal Jual -->
                <td class="text-end">
                    <div class="fw-bold text-primary row-total-sell-display fs-6">Rp 0</div>
                    <small class="text-muted" style="font-size: 10px;">HPP: <span class="row-total-hpp-display">Rp 0</span></small>
                </td>

                <!-- Col 7: Delete Action -->
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-outline-danger p-1 rounded-circle border-0" onclick="removeItem(${sIndex}, ${iIndex})" title="Hapus Baris">
                        <i class="mdi mdi-close fs-6"></i>
                    </button>
                </td>
            </tr>
        `;

        itemsContainer.insertAdjacentHTML('beforeend', rowHtml);

        // Initialize Select2 on Material Dropdown
        if (itemType === 'material') {
            const $matSelect = $(`#mat_select_${sIndex}_${iIndex}`);
            $matSelect.select2({
                placeholder: '-- 🔍 Ketik untuk Cari Material --',
                allowClear: true,
                width: '100%'
            }).on('change', function () {
                onMaterialSelected(sIndex, iIndex, $(this).val());
            });
        }

        if (matId && materialMap[matId]) {
            populateVendorsForRow(sIndex, iIndex, matId, idSupplier);
        }

        calcRowTotal(sIndex, iIndex);
        updateRowNumbers(sIndex);
    }

    function removeItem(sIndex, iIndex) {
        const row = document.getElementById(`item_row_${sIndex}_${iIndex}`);
        if (row) row.remove();
        updateRowNumbers(sIndex);
        calcSectionTotals(sIndex);
        recalculateGrandTotals();
        triggerAutoSave();
    }

    function updateRowNumbers(sIndex) {
        const itemsContainer = document.getElementById(`items_container_${sIndex}`);
        if (!itemsContainer) return;
        const rows = itemsContainer.querySelectorAll('tr');
        rows.forEach((r, idx) => {
            const numElem = r.querySelector('.row-idx-num');
            if (numElem) numElem.innerText = idx + 1;
        });
    }

    function populateVendorsForRow(sIndex, iIndex, materialId, selectedSupplierId) {
        const row = document.getElementById(`item_row_${sIndex}_${iIndex}`);
        if (!row || !materialMap[materialId]) return;

        const mat = materialMap[materialId];
        const vendorSelect = row.querySelector('.vendor-select');
        vendorSelect.innerHTML = '<option value="">-- Pilih Supplier --</option>';

        if (mat.vendor_prices && mat.vendor_prices.length > 0) {
            mat.vendor_prices.sort((a, b) => parseFloat(a.price_idr) - parseFloat(b.price_idr));
            mat.vendor_prices.forEach((vp, vIdx) => {
                const isCheapest = vIdx === 0;
                const opt = document.createElement('option');
                opt.value = vp.id_supplier;
                const priceClean = Math.round(parseFloat(vp.price_idr)) || 0;
                opt.dataset.price = priceClean;
                const supName = vp.supplier ? vp.supplier.supplier : 'Supplier #' + vp.id_supplier;
                opt.innerText = `${supName} - Rp ${new Intl.NumberFormat('id-ID').format(priceClean)} ${isCheapest ? '⭐ Termurah' : ''}`;
                if (selectedSupplierId && vp.id_supplier == selectedSupplierId) {
                    opt.selected = true;
                }
                vendorSelect.appendChild(opt);
            });
        }
    }

    function onMaterialSelected(sIndex, iIndex, materialId) {
        const row = document.getElementById(`item_row_${sIndex}_${iIndex}`);
        if (!row) return;

        const nameInput = row.querySelector('.item-name-input');
        const sizeInput = row.querySelector('.item-size-input');
        const unitInput = row.querySelector('.input-unit');
        const lengthInput = row.querySelector('.input-length-per-unit');
        const wasteInput = row.querySelector('.input-waste');
        const vendorSelect = row.querySelector('.vendor-select');
        const hppInput = row.querySelector('.input-hpp');
        const pipeCalcBox = row.querySelector('.pipe-badge-calc');

        if (!materialId || !materialMap[materialId]) {
            vendorSelect.innerHTML = '<option value="">-- Pilih Supplier --</option>';
            return;
        }

        const mat = materialMap[materialId];
        nameInput.value = mat.item_name + (mat.material_type ? ' (' + mat.material_type + ')' : '');
        sizeInput.value = mat.size || '';
        unitInput.value = mat.unit || 'Batang';
        const lengthPerBtg = mat.length_per_unit || 6.00;
        lengthInput.value = lengthPerBtg;
        const lengthDisplay = row.querySelector('.length-display');
        if (lengthDisplay) lengthDisplay.innerText = lengthPerBtg;
        wasteInput.value = mat.default_waste_percent || 5.0;

        if (mat.category === 'pipe') {
            pipeCalcBox.classList.remove('d-none');
        } else {
            pipeCalcBox.classList.add('d-none');
        }

        populateVendorsForRow(sIndex, iIndex, materialId, null);

        if (mat.vendor_prices && mat.vendor_prices.length > 0) {
            vendorSelect.selectedIndex = 1;
            hppInput.value = formatRupiahNumber(mat.vendor_prices[0].price_idr);
        }

        calcPipeQty(sIndex, iIndex);
        calcRowTotal(sIndex, iIndex);
    }

    function onVendorChanged(sIndex, iIndex, selectElem) {
        const selectedOpt = selectElem.options[selectElem.selectedIndex];
        if (selectedOpt && selectedOpt.dataset.price) {
            const row = document.getElementById(`item_row_${sIndex}_${iIndex}`);
            const hppInput = row.querySelector('.input-hpp');
            hppInput.value = formatRupiahNumber(selectedOpt.dataset.price);
            calcRowTotal(sIndex, iIndex);
        }
    }

    function calcPipeQty(sIndex, iIndex) {
        const row = document.getElementById(`item_row_${sIndex}_${iIndex}`);
        if (!row) return;

        const meterInput = row.querySelector('.input-meter');
        const wasteInput = row.querySelector('.input-waste');
        const lengthInput = row.querySelector('.input-length-per-unit');
        const qtyInput = row.querySelector('.input-qty');

        const meter = parseFloat(meterInput.value) || 0;
        const wastePercent = parseFloat(wasteInput.value) || 0;
        const lengthPerBatang = parseFloat(lengthInput.value) || 6.00;

        if (meter > 0) {
            const meterWithWaste = meter + (meter * (wastePercent / 100));
            const batangCount = Math.ceil(meterWithWaste / lengthPerBatang);
            qtyInput.value = batangCount;
        }

        calcRowTotal(sIndex, iIndex);
    }

    function calcRowTotal(sIndex, iIndex) {
        const row = document.getElementById(`item_row_${sIndex}_${iIndex}`);
        if (!row) return;

        const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
        const hpp = parseRupiahNumber(row.querySelector('.input-hpp').value);
        const marginType = row.querySelector('.margin-type').value;
        const marginVal = parseFloat(row.querySelector('.margin-value').value) || 0;

        let unitSelling = 0;
        if (marginType === 'percent') {
            unitSelling = hpp + (hpp * (marginVal / 100));
        } else {
            unitSelling = hpp + marginVal;
        }

        unitSelling = Math.round(unitSelling);
        const totalHpp = Math.round(qty * hpp);
        const totalSelling = Math.round(qty * unitSelling);

        row.querySelector('.input-unit-selling-price').value = unitSelling;
        row.querySelector('.unit-sell-display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(unitSelling);
        row.querySelector('.row-total-sell-display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalSelling);
        row.querySelector('.row-total-hpp-display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalHpp);

        calcSectionTotals(sIndex);
        recalculateGrandTotals();
        triggerAutoSave();
    }

    function calcSectionTotals(sIndex) {
        const section = document.getElementById(`section_${sIndex}`);
        if (!section) return;

        let secHpp = 0;
        let secSelling = 0;

        const itemRows = section.querySelectorAll('tbody tr');
        itemRows.forEach(row => {
            const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
            const hpp = parseRupiahNumber(row.querySelector('.input-hpp').value);
            const unitSelling = parseFloat(row.querySelector('.input-unit-selling-price').value) || 0;

            secHpp += (qty * hpp);
            secSelling += (qty * unitSelling);
        });

        const subtotalSellElem = document.getElementById(`sec_subtotal_sell_${sIndex}`);
        const subtotalHppElem = document.getElementById(`sec_subtotal_hpp_${sIndex}`);

        if (subtotalSellElem) subtotalSellElem.innerText = 'Subtotal Jual: Rp ' + new Intl.NumberFormat('id-ID').format(secSelling);
        if (subtotalHppElem) subtotalHppElem.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(secHpp);
    }

    function recalculateGrandTotals() {
        let grandHpp = 0;
        let grandSelling = 0;

        const allRows = document.querySelectorAll('tbody tr[id^="item_row_"]');
        allRows.forEach(row => {
            const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
            const hpp = parseRupiahNumber(row.querySelector('.input-hpp').value);
            const unitSelling = parseFloat(row.querySelector('.input-unit-selling-price').value) || 0;

            grandHpp += (qty * hpp);
            grandSelling += (qty * unitSelling);
        });

        const grandMargin = grandSelling - grandHpp;
        const grandMarginPercent = grandHpp > 0 ? ((grandMargin / grandHpp) * 100).toFixed(1) : 0;

        document.getElementById('displayGrandHpp').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandHpp);
        document.getElementById('displayGrandMargin').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandMargin);
        document.getElementById('displayGrandMarginPercent').innerText = '+' + grandMarginPercent + '%';
        document.getElementById('displayGrandSelling').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandSelling);
    }

    // AutoSave to LocalStorage
    let autoSaveTimeout = null;
    function triggerAutoSave() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function () {
            saveDraftToLocalStorage();
        }, 400);
    }

    function saveDraftToLocalStorage() {
        const draft = {
            id_sales: document.getElementById('selectSales').value,
            id_client: $('#selectClient').val(),
            id_pic: document.getElementById('selectPic').value,
            rab_date: document.getElementById('inputRabDate').value,
            project_name: document.getElementById('inputProjectName').value,
            location_plant: document.getElementById('inputLocationPlant').value,
            notes: document.getElementById('inputNotes').value,
            sections: []
        };

        const sectionCards = document.querySelectorAll('.rab-card[data-section-index]');
        sectionCards.forEach(secCard => {
            const sNameInput = secCard.querySelector('.section-name-input');
            const secData = {
                name: sNameInput ? sNameInput.value : '',
                items: []
            };

            const itemRows = secCard.querySelectorAll('tbody tr[id^="item_row_"]');
            itemRows.forEach(row => {
                const itemTypeInput = row.querySelector('input[name*="[item_type]"]');
                const matSelect = row.querySelector('.material-selector');
                const nameInput = row.querySelector('.item-name-input');
                const sizeInput = row.querySelector('.item-size-input');
                const specInput = row.querySelector('input[name*="[spec]"]');
                const meterInput = row.querySelector('.input-meter');
                const wasteInput = row.querySelector('.input-waste');
                const lengthInput = row.querySelector('.input-length-per-unit');
                const qtyInput = row.querySelector('.input-qty');
                const unitInput = row.querySelector('.input-unit');
                const vendorSelect = row.querySelector('.vendor-select');
                const hppInput = row.querySelector('.input-hpp');
                const marginTypeSelect = row.querySelector('.margin-type');
                const marginValInput = row.querySelector('.margin-value');
                const unitSellInput = row.querySelector('.input-unit-selling-price');

                secData.items.push({
                    item_type: itemTypeInput ? itemTypeInput.value : 'material',
                    id_piping_material: matSelect ? matSelect.value : '',
                    item_name: nameInput ? nameInput.value : '',
                    size: sizeInput ? sizeInput.value : '',
                    spec: specInput ? specInput.value : '',
                    input_length_meter: meterInput ? meterInput.value : '',
                    waste_percent: wasteInput ? wasteInput.value : 5,
                    length_per_unit: lengthInput ? lengthInput.value : 6.00,
                    calculated_qty: qtyInput ? qtyInput.value : 1,
                    unit: unitInput ? unitInput.value : 'Batang',
                    id_supplier: vendorSelect ? vendorSelect.value : '',
                    unit_price_hpp: hppInput ? hppInput.value : 0,
                    margin_type: marginTypeSelect ? marginTypeSelect.value : 'percent',
                    margin_value: marginValInput ? marginValInput.value : 25,
                    unit_selling_price: unitSellInput ? unitSellInput.value : 0,
                });
            });

            draft.sections.push(secData);
        });

        localStorage.setItem(STORAGE_KEY, JSON.stringify(draft));

        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const lastSavedElem = document.getElementById('lastSavedTime');
        if (lastSavedElem) {
            lastSavedElem.innerText = 'Autosaved pk ' + timeStr;
        }
    }

    function restoreDraft(draft) {
        if (draft.id_sales) {
            document.getElementById('selectSales').value = draft.id_sales;
            onSalesChanged(draft.id_sales, draft.id_client, draft.id_pic);
        } else if (draft.id_client) {
            onSalesChanged(document.getElementById('selectSales').value, draft.id_client, draft.id_pic);
        }
        if (draft.rab_date) document.getElementById('inputRabDate').value = draft.rab_date;
        if (draft.project_name) document.getElementById('inputProjectName').value = draft.project_name;
        if (draft.location_plant) document.getElementById('inputLocationPlant').value = draft.location_plant;
        if (draft.notes) document.getElementById('inputNotes').value = draft.notes;

        const container = document.getElementById('sectionsContainer');
        container.innerHTML = '';

        if (draft.sections && draft.sections.length > 0) {
            draft.sections.forEach(secData => {
                const sIndex = addSection(secData.name);
                const itemsContainer = document.getElementById(`items_container_${sIndex}`);
                itemsContainer.innerHTML = '';

                if (secData.items && secData.items.length > 0) {
                    secData.items.forEach(itemData => {
                        addItem(sIndex, itemData.item_type || 'material', itemData);
                    });
                } else {
                    addItem(sIndex, 'material');
                }
            });
        } else {
            initDefaultSection();
        }

        recalculateGrandTotals();
    }

    function resetAutoDraft() {
        if (confirm('Yakin ingin mereset draft dan mengulang dari awal?')) {
            localStorage.removeItem(STORAGE_KEY);
            window.location.reload();
        }
    }
</script>
@endpush
