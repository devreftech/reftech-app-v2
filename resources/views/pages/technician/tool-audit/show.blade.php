@extends('layouts.sales.app')
@section('title', 'Audit Tools - ' . $audit->no_audit)
@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 mb-2 gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Audit Tools /</span> {{ $audit->no_audit }}
                @php
                    $headerBadge = [
                        'Draft' => 'bg-label-secondary',
                        'Submitted' => 'bg-label-warning',
                        'Verified' => 'bg-label-success',
                        'Rejected' => 'bg-label-danger',
                    ][$audit->status_submit] ?? 'bg-label-secondary';
                @endphp
                <span class="badge {{ $headerBadge }} align-middle ms-1">{{ $audit->status_submit }}</span>
            </h4>
            <p class="text-muted mb-0 small">
                Periode {{ $audit->period->tahun }} Triwulan {{ $audit->period->semester }} (Q{{ $audit->period->semester }}) &bull;
                {{ \Carbon\Carbon::parse($audit->period->tanggal_mulai)->format('d M') }} s/d
                {{ \Carbon\Carbon::parse($audit->period->tanggal_selesai)->format('d M Y') }}
                @if ($audit->submitted_at)
                    &bull; Disubmit: {{ \Carbon\Carbon::parse($audit->submitted_at)->format('d M Y H:i') }}
                @endif
            </p>
        </div>
        @if ($editable)
            <div class="d-flex align-items-center gap-2">
                <div id="globalAutoSaveStatus" class="badge bg-label-success px-3 py-2 rounded-pill small" style="display: inline-flex; align-items: center; gap: 4px;">
                    <i class="mdi mdi-cloud-check fs-6"></i> <span>Draft Tersimpan Otomatis</span>
                </div>
            </div>
        @endif
    </div>

    @if ($editable)
        <div class="alert alert-info d-flex align-items-center mb-3 py-2 px-3 rounded" role="alert">
            <i class="mdi mdi-information-outline me-2 fs-5"></i>
            <div style="font-size: 12.5px;">
                <strong>Sistem Auto-Save Aktif:</strong> Setiap perubahan kondisi, jumlah, dan upload foto langsung tersimpan otomatis ke database sebagai <strong>Draft</strong>. Anda bisa mencicil pengisian kapan saja. Klik tombol <strong>"Kirim Audit ke Admin"</strong> hanya jika seluruh tools sudah selesai diperiksa.
            </div>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('tool-audit.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="mdi mdi-arrow-left me-1"></i> Kembali
        </a>
        @if ($editable)
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-label-secondary btn-sm px-3" id="btnManualSaveDraft">
                    <i class="mdi mdi-content-save-outline me-1"></i> Simpan Draft
                </button>
            </div>
        @endif
    </div>

    <form id="formAuditSubmit" action="{{ route('tool-audit.submit', $audit->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @foreach ($audit->items as $item)
            @php
                $tool = $item->fixedAsset;
                $master = $tool->toolsMaster ?? null;
                $kondisi = old("items.{$item->id}.kondisi", $item->kondisi);
            @endphp
            <div class="card mb-3 border tool-item-card" data-item-id="{{ $item->id }}">
                <div class="card-body p-3">
                    <div class="row g-3 align-items-start">
                        <div class="col-md-2 text-center">
                            @if ($tool && $tool->foto_awal)
                                <img src="{{ asset($tool->foto_awal) }}" alt="foto awal"
                                    style="width:100%;max-width:100px;aspect-ratio:1/1;object-fit:cover;border-radius:6px;" class="border">
                                <div class="small text-muted mt-1" style="font-size: 11px;">Foto Awal</div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-1 fw-bold text-dark">{{ $master->nama_tools ?? '-' }}</h6>
                            <div class="text-muted small mb-2">Qty terdaftar: <span class="fw-bold">{{ $tool->qty }}</span></div>

                            @if ($editable)
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="number" class="form-control qty-actual-input" name="items[{{ $item->id }}][qty_actual]"
                                        data-item-id="{{ $item->id }}"
                                        value="{{ old("items.{$item->id}.qty_actual", $item->qty_actual ?? $tool->qty) }}"
                                        min="0" required>
                                    <label>Qty Sekarang</label>
                                </div>
                            @else
                                <div class="text-muted small">Qty sekarang: <span class="fw-bold text-dark">{{ $item->qty_actual ?? '-' }}</span></div>
                            @endif
                        </div>
                        <div class="col-md-3">
                            @if ($editable)
                                <div class="btn-group w-100 kondisi-group" data-item="{{ $item->id }}" role="group">
                                    <input type="radio" class="btn-check kondisi-radio" data-item="{{ $item->id }}"
                                        name="items[{{ $item->id }}][kondisi]" id="ada-{{ $item->id }}" value="Ada"
                                        {{ $kondisi == 'Ada' ? 'checked' : '' }} autocomplete="off">
                                    <label class="btn btn-outline-success btn-sm" for="ada-{{ $item->id }}">Ada</label>

                                    <input type="radio" class="btn-check kondisi-radio" data-item="{{ $item->id }}"
                                        name="items[{{ $item->id }}][kondisi]" id="rusak-{{ $item->id }}" value="Rusak"
                                        {{ $kondisi == 'Rusak' ? 'checked' : '' }} autocomplete="off">
                                    <label class="btn btn-outline-warning btn-sm" for="rusak-{{ $item->id }}">Rusak</label>

                                    <input type="radio" class="btn-check kondisi-radio" data-item="{{ $item->id }}"
                                        name="items[{{ $item->id }}][kondisi]" id="hilang-{{ $item->id }}" value="Hilang"
                                        {{ $kondisi == 'Hilang' ? 'checked' : '' }} autocomplete="off">
                                    <label class="btn btn-outline-danger btn-sm" for="hilang-{{ $item->id }}">Hilang</label>
                                </div>

                                <div class="mt-2 alasan-wrap-{{ $item->id }}"
                                    style="display: {{ in_array($kondisi, ['Rusak', 'Hilang']) ? 'block' : 'none' }};">
                                    <textarea class="form-control form-control-sm alasan-input" data-item-id="{{ $item->id }}"
                                        name="items[{{ $item->id }}][alasan]"
                                        placeholder="{{ $kondisi == 'Hilang' ? 'Catatan (opsional)...' : 'Alasan kerusakan...' }}">{{ old("items.{$item->id}.alasan", $item->alasan) }}</textarea>
                                </div>

                                <div class="mt-2 metode-wrap-{{ $item->id }}"
                                    style="display: {{ $kondisi == 'Hilang' ? 'block' : 'none' }};">
                                    <select class="form-select form-select-sm metode-ganti-select" data-item-id="{{ $item->id }}"
                                        name="items[{{ $item->id }}][metode_ganti]">
                                        <option value="">-- Metode Ganti --</option>
                                        <option value="Beli Sendiri"
                                            {{ old("items.{$item->id}.metode_ganti", $item->metode_ganti) == 'Beli Sendiri' ? 'selected' : '' }}>
                                            Beli Sendiri</option>
                                        <option value="Potong Bonus"
                                            {{ old("items.{$item->id}.metode_ganti", $item->metode_ganti) == 'Potong Bonus' ? 'selected' : '' }}>
                                            Potong Bonus</option>
                                    </select>
                                    @if ($master && ($master->foto_referensi || $master->link_pembelian))
                                        <div class="small mt-2 p-2 bg-label-info rounded" style="font-size: 11px;">
                                            Referensi beli: {{ $master->nama_tools }}
                                            @if ($master->link_pembelian)
                                                — <a href="{{ $master->link_pembelian }}" target="_blank" rel="noopener">{{ $master->link_pembelian }}</a>
                                            @endif
                                            @if ($master->foto_referensi)
                                                <div class="mt-1">
                                                    <img src="{{ asset($master->foto_referensi) }}" alt="referensi"
                                                        style="max-width:80px;border-radius:4px;">
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @else
                                @php
                                    $badge = ['Ada' => 'bg-label-success', 'Rusak' => 'bg-label-warning', 'Hilang' => 'bg-label-danger'][$item->kondisi] ?? 'bg-label-secondary';
                                @endphp
                                <span class="badge {{ $badge }}">{{ $item->kondisi ?? 'Belum diisi' }}</span>
                                @if ($item->alasan)
                                    <div class="small text-muted mt-1">{{ $item->alasan }}</div>
                                @endif
                                @if ($item->metode_ganti)
                                    <div class="small text-muted mt-1">Ganti: {{ $item->metode_ganti }}</div>
                                @endif
                            @endif
                        </div>
                        <div class="col-md-3 text-center foto-upload-wrapper" data-item-id="{{ $item->id }}">
                            <div class="foto-preview-container">
                                @if ($item->foto_audit)
                                    <img src="{{ asset($item->foto_audit) }}" alt="foto audit" class="foto-preview-img"
                                        style="width:100%;max-width:100px;aspect-ratio:1/1;object-fit:cover;border-radius:6px;">
                                    <div class="small text-muted mt-1 foto-preview-label" style="font-size: 11px;">Foto Audit</div>
                                @else
                                    <div class="no-foto-placeholder text-muted small p-3 border rounded bg-light" style="font-size: 11px;">
                                        <i class="mdi mdi-camera-plus-outline fs-4 d-block mb-1 text-secondary"></i> Belum ada foto
                                    </div>
                                @endif
                            </div>
                            <div class="upload-status text-primary small mt-1" style="display:none; font-size: 11px;"></div>
                            @if ($editable)
                                <input type="file" class="form-control form-control-sm mt-2 foto-audit-input" accept="image/*"
                                    name="items[{{ $item->id }}][foto_audit]" {{ $item->foto_audit ? '' : 'required' }}>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if ($editable)
            <div class="card p-3 mb-4 bg-white shadow-sm border">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div>
                        <h6 class="fw-bold mb-0">Sudah Selesai Memeriksa Semua Tools?</h6>
                        <small class="text-muted">Pastikan seluruh tools telah difoto dan ditentukan kondisinya sebelum dikirim ke Admin.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-label-secondary px-3" id="btnManualSaveDraftBottom">
                            <i class="mdi mdi-content-save-outline me-1"></i> Simpan Draft
                        </button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" onclick="return confirm('Kirim seluruh hasil audit ini ke Admin untuk diverifikasi? Pastikan semua foto dan data sudah sesuai.');">
                            <i class="mdi mdi-send me-1"></i> Kirim Audit ke Admin
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </form>
@endsection()

@push('page-script')
    <script>
        const csrfToken = '{{ csrf_token() }}';
        const globalStatus = document.getElementById('globalAutoSaveStatus');

        function setAutoSaveStatus(state, text) {
            if (!globalStatus) return;
            if (state === 'saving') {
                globalStatus.className = 'badge bg-label-warning px-3 py-2 rounded-pill small';
                globalStatus.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan draft...';
            } else if (state === 'saved') {
                globalStatus.className = 'badge bg-label-success px-3 py-2 rounded-pill small';
                globalStatus.innerHTML = '<i class="mdi mdi-cloud-check me-1"></i> ' + (text || 'Draft Tersimpan Otomatis');
            } else if (state === 'error') {
                globalStatus.className = 'badge bg-label-danger px-3 py-2 rounded-pill small';
                globalStatus.innerHTML = '<i class="mdi mdi-alert-circle me-1"></i> Gagal menyimpan otomatis';
            }
        }

        // Auto save item function
        let autoSaveTimeouts = {};

        function triggerItemAutoSave(itemId) {
            setAutoSaveStatus('saving');

            clearTimeout(autoSaveTimeouts[itemId]);
            autoSaveTimeouts[itemId] = setTimeout(function() {
                const card = document.querySelector(`.tool-item-card[data-item-id="${itemId}"]`);
                if (!card) return;

                const qtyInput = card.querySelector('.qty-actual-input');
                const kondisiRadio = card.querySelector('.kondisi-radio:checked');
                const alasanInput = card.querySelector('.alasan-input');
                const metodeSelect = card.querySelector('.metode-ganti-select');

                const payload = {
                    _token: csrfToken,
                    qty_actual: qtyInput ? qtyInput.value : 0,
                    kondisi: kondisiRadio ? kondisiRadio.value : null,
                    alasan: alasanInput ? alasanInput.value : null,
                    metode_ganti: metodeSelect ? metodeSelect.value : null,
                };

                fetch(`/tool-audit/item/${itemId}/auto-save`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        setAutoSaveStatus('saved');
                    } else {
                        setAutoSaveStatus('error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    setAutoSaveStatus('error');
                });
            }, 600); // 600ms debounce
        }

        // Listeners for radio kondisi
        document.querySelectorAll('.kondisi-radio').forEach(function (radio) {
            radio.addEventListener('change', function () {
                var id = this.getAttribute('data-item');
                var alasanWrap = document.querySelector('.alasan-wrap-' + id);
                var metodeWrap = document.querySelector('.metode-wrap-' + id);
                if (alasanWrap) {
                    alasanWrap.style.display = (this.value === 'Rusak' || this.value === 'Hilang') ? 'block' : 'none';
                    var textarea = alasanWrap.querySelector('textarea');
                    if (textarea) textarea.placeholder = this.value === 'Hilang' ? 'Catatan (opsional)...' : 'Alasan kerusakan...';
                }
                if (metodeWrap) metodeWrap.style.display = this.value === 'Hilang' ? 'block' : 'none';

                triggerItemAutoSave(id);
            });
        });

        // Listeners for inputs
        document.querySelectorAll('.qty-actual-input').forEach(function(input) {
            input.addEventListener('input', function() {
                triggerItemAutoSave(this.getAttribute('data-item-id'));
            });
        });

        document.querySelectorAll('.alasan-input').forEach(function(input) {
            input.addEventListener('input', function() {
                triggerItemAutoSave(this.getAttribute('data-item-id'));
            });
        });

        document.querySelectorAll('.metode-ganti-select').forEach(function(select) {
            select.addEventListener('change', function() {
                triggerItemAutoSave(this.getAttribute('data-item-id'));
            });
        });

        // Manual Save Draft buttons
        function submitManualDraft() {
            const form = document.getElementById('formAuditSubmit');
            if (!form) return;
            const originalAction = form.action;
            form.action = '{{ route("tool-audit.save-draft", $audit->id) }}';
            
            // Remove required temporary on file inputs so draft can submit partially
            document.querySelectorAll('.foto-audit-input').forEach(el => el.removeAttribute('required'));
            form.submit();
        }

        const btnDraftTop = document.getElementById('btnManualSaveDraft');
        const btnDraftBottom = document.getElementById('btnManualSaveDraftBottom');
        if (btnDraftTop) btnDraftTop.addEventListener('click', submitManualDraft);
        if (btnDraftBottom) btnDraftBottom.addEventListener('click', submitManualDraft);

        // AJAX Photo Upload
        const submitBtn = document.querySelector('button[type="submit"]');

        document.querySelectorAll('.foto-audit-input').forEach(function (input) {
            input.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;

                const wrapper = this.closest('.foto-upload-wrapper');
                const itemId = wrapper.getAttribute('data-item-id');
                const statusDiv = wrapper.querySelector('.upload-status');
                const previewContainer = wrapper.querySelector('.foto-preview-container');
                const fileInput = this;

                // Disable input and submit button
                fileInput.disabled = true;
                if (submitBtn) submitBtn.disabled = true;
                statusDiv.style.display = 'block';
                statusDiv.innerHTML = '<span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span> Mengunggah foto...';
                setAutoSaveStatus('saving');

                const formData = new FormData();
                formData.append('foto_audit', file);
                formData.append('_token', csrfToken);

                fetch(`/tool-audit/item/${itemId}/upload-photo`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update preview
                        previewContainer.innerHTML = `
                            <img src="${data.foto_url}" alt="foto audit" class="foto-preview-img"
                                style="width:100%;max-width:100px;aspect-ratio:1/1;object-fit:cover;border-radius:6px;">
                            <div class="small text-muted mt-1 foto-preview-label" style="font-size: 11px;">Foto Audit</div>
                        `;
                        // Remove required attribute
                        fileInput.removeAttribute('required');
                        fileInput.value = '';
                        
                        statusDiv.innerHTML = '<span class="text-success"><i class="mdi mdi-check-circle"></i> Berhasil diunggah & tersimpan</span>';
                        setAutoSaveStatus('saved');
                    } else {
                        throw new Error('Gagal mengunggah foto.');
                    }
                })
                .catch(error => {
                    console.error(error);
                    let errMsg = 'Gagal mengunggah foto.';
                    if (error.errors && error.errors.foto_audit) {
                        errMsg = error.errors.foto_audit.join(', ');
                    } else if (error.message) {
                        errMsg = error.message;
                    }
                    alert(errMsg);
                    statusDiv.innerHTML = `<span class="text-danger"><i class="mdi mdi-alert-circle"></i> ${errMsg}</span>`;
                    setAutoSaveStatus('error');
                })
                .finally(() => {
                    fileInput.disabled = false;
                    const anyUploading = Array.from(document.querySelectorAll('.upload-status')).some(el => {
                        return el.style.display === 'block' && el.innerHTML.includes('spinner-border');
                    });
                    if (!anyUploading && submitBtn) {
                        submitBtn.disabled = false;
                    }
                });
            });
        });
    </script>
@endpush

