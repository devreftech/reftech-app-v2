@php
    $today = \Carbon\Carbon::today();
    $defaultDate = $today->format('Y-m-d');
    $defaultFollowUp = $today->copy()->addMonth()->format('Y-m-d');
    
    // Hitung week otomatis kalender kerja Senin-Minggu
    $dayOfMonth = (int) $today->day;
    $firstDayOfMonth = (int) $today->copy()->startOfMonth()->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
    $offset = ($firstDayOfMonth - 1);
    $currentWeek = max(1, min(5, (int) floor(($dayOfMonth + $offset - 1) / 7) + 1));
@endphp

<form action="{{ route('action.crm', $existing->id) }}" method="post" enctype="multipart/form-data" id="formCrmVisit{{ $existing->id }}">
    @csrf
    <div class="modal fade" id="createAction{{ $existing->id }}" tabindex="-1" aria-labelledby="modalCrmVisitLabel{{ $existing->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                
                <!-- Modal Header with Modern Aesthetic -->
                <div class="modal-header border-bottom px-4 py-3 bg-body-tertiary">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary text-white shadow-xs" style="width: 44px; height: 44px;">
                            <i class="mdi mdi-calendar-check-outline fs-3"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="modalCrmVisitLabel{{ $existing->id }}">
                                Catat Aktivitas CRM / Visit
                            </h5>
                            <small class="text-muted d-flex align-items-center gap-1 mt-1">
                                <i class="mdi mdi-domain fs-tiny"></i>
                                <span class="fw-semibold text-primary">{{ $existing->company ?? '-' }}</span>
                                @if(!empty($existing->pic_first?->name_pic))
                                    • <span>PIC: {{ $existing->pic_first->name_pic }}</span>
                                @endif
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Section 1: Visual Action Selector (Card-Based Segmented Options) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2" style="font-size: 0.85rem;">
                            <i class="mdi mdi-gesture-tap-button text-primary me-1"></i> Pilih Jenis Aktivitas
                        </label>
                        <div class="row g-2">
                            <!-- Option 1: Phone Office -->
                            <div class="col-4">
                                <input type="radio" class="btn-check crm-action-radio" name="action" id="actionPhone{{ $existing->id }}" value="Phone Office" required>
                                <label class="btn btn-outline-secondary w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center rounded-3 crm-action-card text-center" for="actionPhone{{ $existing->id }}">
                                    <div class="avatar avatar-sm mb-2 rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-phone-in-talk fs-4"></i>
                                    </div>
                                    <span class="fw-bold text-dark mb-0" style="font-size: 0.88rem;">Phone Office</span>
                                    <small class="text-muted" style="font-size: 0.72rem;">Telepon kantor</small>
                                </label>
                            </div>

                            <!-- Option 2: WhatsApp -->
                            <div class="col-4">
                                <input type="radio" class="btn-check crm-action-radio" name="action" id="actionWa{{ $existing->id }}" value="WhatsApp" checked required>
                                <label class="btn btn-outline-secondary w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center rounded-3 crm-action-card text-center" for="actionWa{{ $existing->id }}">
                                    <div class="avatar avatar-sm mb-2 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-whatsapp fs-4"></i>
                                    </div>
                                    <span class="fw-bold text-dark mb-0" style="font-size: 0.88rem;">WhatsApp</span>
                                    <small class="text-muted" style="font-size: 0.72rem;">Chat / call WA</small>
                                </label>
                            </div>

                            <!-- Option 3: Visit -->
                            <div class="col-4">
                                <input type="radio" class="btn-check crm-action-radio" name="action" id="actionVisit{{ $existing->id }}" value="Visit" required>
                                <label class="btn btn-outline-secondary w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center rounded-3 crm-action-card text-center" for="actionVisit{{ $existing->id }}">
                                    <div class="avatar avatar-sm mb-2 rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-map-marker-radius fs-4"></i>
                                    </div>
                                    <span class="fw-bold text-dark mb-0" style="font-size: 0.88rem;">Visit / Meeting</span>
                                    <small class="text-muted" style="font-size: 0.72rem;">Kunjungan onsite</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Date & Follow Up Configuration -->
                    <div class="row g-3 mb-3">
                        <!-- Tanggal Aktivitas & Auto Week -->
                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label for="crmDate{{ $existing->id }}" class="form-label fw-bold text-dark mb-0" style="font-size: 0.82rem;">
                                        <i class="mdi mdi-calendar text-primary me-1"></i> Tanggal Aktivitas
                                    </label>
                                    <span class="badge bg-primary text-white rounded-pill px-2 py-1 fs-tiny shadow-xs" id="crmWeekBadge{{ $existing->id }}">
                                        Week {{ $currentWeek }}
                                    </span>
                                </div>
                                <input class="form-control bg-white border crm-date-input" type="date" id="crmDate{{ $existing->id }}" name="date" value="{{ $defaultDate }}" required data-client-id="{{ $existing->id }}">
                                <input type="hidden" name="week" id="crmInputWeek{{ $existing->id }}" value="{{ $currentWeek }}">
                                <small class="text-muted mt-1 d-block" style="font-size: 0.72rem;">Minggu otomatis terhitung kalender kerja</small>
                            </div>
                        </div>

                        <!-- Jadwal Next Follow Up -->
                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label for="crmFollowUp{{ $existing->id }}" class="form-label fw-bold text-dark mb-0" style="font-size: 0.82rem;">
                                        <i class="mdi mdi-clock-outline text-warning me-1"></i> Next Follow Up
                                    </label>
                                    <small class="text-muted" style="font-size: 0.72rem;">Target kontak kembali</small>
                                </div>
                                <input class="form-control bg-white border crm-followup-input" type="date" id="crmFollowUp{{ $existing->id }}" name="follow_up" value="{{ $defaultFollowUp }}">
                                
                                <!-- Quick Shortcut Buttons -->
                                <div class="d-flex align-items-center gap-1 mt-2 flex-wrap">
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill py-0 px-2 quick-fu-btn" data-target="#crmFollowUp{{ $existing->id }}" data-days="7" style="font-size: 0.7rem;">+1 Mgg</button>
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill py-0 px-2 quick-fu-btn" data-target="#crmFollowUp{{ $existing->id }}" data-days="14" style="font-size: 0.7rem;">+2 Mgg</button>
                                    <button type="button" class="btn btn-xs btn-primary rounded-pill py-0 px-2 quick-fu-btn" data-target="#crmFollowUp{{ $existing->id }}" data-months="1" style="font-size: 0.7rem;">+1 Bulan</button>
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill py-0 px-2 quick-fu-btn" data-target="#crmFollowUp{{ $existing->id }}" data-months="2" style="font-size: 0.7rem;">+2 Bulan</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Status Respon (Clean Radio Pills) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-2" style="font-size: 0.85rem;">
                            <i class="mdi mdi-check-decagram-outline text-primary me-1"></i> Status Respon
                        </label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check crm-status-radio" name="status" id="statusResponded{{ $existing->id }}" value="Responded" data-client-id="{{ $existing->id }}" checked required>
                            <label class="btn btn-outline-success flex-fill rounded-3 py-2 d-flex align-items-center justify-content-center gap-2" for="statusResponded{{ $existing->id }}">
                                <i class="mdi mdi-check-circle-outline fs-5"></i>
                                <span class="fw-semibold" style="font-size: 0.85rem;">Responded (Direspon)</span>
                            </label>

                            <input type="radio" class="btn-check crm-status-radio" name="status" id="statusNotRespon{{ $existing->id }}" value="Not Respon" data-client-id="{{ $existing->id }}" required>
                            <label class="btn btn-outline-secondary flex-fill rounded-3 py-2 d-flex align-items-center justify-content-center gap-2" for="statusNotRespon{{ $existing->id }}">
                                <i class="mdi mdi-phone-missed fs-5"></i>
                                <span class="fw-semibold" style="font-size: 0.85rem;">No Responded</span>
                            </label>
                        </div>

                        <!-- Opsi Update Status Customer (Hanya muncul jika No Responded dipilih) -->
                        <div id="wrapNonActiveCheckbox{{ $existing->id }}" class="mt-2 p-3 rounded-3 d-none" style="background-color: #fffaf0; border: 1px dashed #f59e0b;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <small class="fw-bold text-dark d-flex align-items-center gap-1" style="font-size: 0.8rem;">
                                    <i class="mdi mdi-account-cog-outline text-warning"></i> Ubah Status Customer (Opsional):
                                </small>
                                <span class="badge bg-label-warning py-0 px-2" style="font-size: 0.68rem;">Auto-Update Status</span>
                            </div>
                            <div class="d-flex gap-3 flex-wrap">
                                <div class="form-check d-flex align-items-center gap-2 m-0 ps-0">
                                    <input class="form-check-input ms-0 me-1 crm-status-update-check" type="checkbox" name="customer_status_action" value="3" id="checkNonActive{{ $existing->id }}" data-client-id="{{ $existing->id }}" style="cursor: pointer;">
                                    <label class="form-check-label fw-semibold text-secondary cursor-pointer mb-0" for="checkNonActive{{ $existing->id }}" style="font-size: 0.8rem; cursor: pointer;">
                                        <i class="mdi mdi-account-off-outline text-warning me-1"></i> Customer Non-Aktif
                                    </label>
                                </div>
                                <div class="form-check d-flex align-items-center gap-2 m-0 ps-0">
                                    <input class="form-check-input ms-0 me-1 crm-status-update-check" type="checkbox" name="customer_status_action" value="1" id="checkBangkrupt{{ $existing->id }}" data-client-id="{{ $existing->id }}" style="cursor: pointer;">
                                    <label class="form-check-label fw-semibold text-danger cursor-pointer mb-0" for="checkBangkrupt{{ $existing->id }}" style="font-size: 0.8rem; cursor: pointer;">
                                        <i class="mdi mdi-alert-octagon-outline text-danger me-1"></i> Customer Bangkrupt
                                    </label>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">*Pilih salah satu jika ingin mengubah status customer ini secara otomatis.</small>
                        </div>
                    </div>

                    <!-- Section 4: Catatan & Quick Templates -->
                    <div class="mb-2">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label for="crmNote{{ $existing->id }}" class="form-label fw-bold text-dark mb-0" style="font-size: 0.85rem;">
                                <i class="mdi mdi-note-text-outline text-primary me-1"></i> Catatan Aktivitas
                            </label>
                            <small class="text-muted" style="font-size: 0.72rem;">Hasil percakapan / kesepakatan</small>
                        </div>
                        <textarea id="crmNote{{ $existing->id }}" class="form-control rounded-3" name="note" rows="3" placeholder="Tuliskan ringkasan hasil percakapan, kebutuhan sparepart/unit, jadwal visit lanjutan, dsb..."></textarea>
                        
                        <!-- Quick Note Chips -->
                        <div class="d-flex align-items-center gap-1 mt-2 flex-wrap">
                            <small class="text-muted me-1" style="font-size: 0.72rem;"><i class="mdi mdi-lightning-bolt-outline"></i> Template:</small>
                            <button type="button" class="badge bg-label-secondary border-0 quick-note-chip py-1 px-2" data-target="#crmNote{{ $existing->id }}" data-text="Follow up kebutuhan maintenance rutin & penawaran sparepart.">Follow Up Penawaran</button>
                            <button type="button" class="badge bg-label-secondary border-0 quick-note-chip py-1 px-2" data-target="#crmNote{{ $existing->id }}" data-text="Kunjungan rutin ke pabrik/lokasi client untuk pengecekan unit.">Kunjungan Rutin</button>
                            <button type="button" class="badge bg-label-secondary border-0 quick-note-chip py-1 px-2" data-target="#crmNote{{ $existing->id }}" data-text="Client meminta revisi penawaran harga & spec unit.">Minta Revisi Harga</button>
                            <button type="button" class="badge bg-label-secondary border-0 quick-note-chip py-1 px-2" data-target="#crmNote{{ $existing->id }}" data-text="Client konfirmasi PO akan diproses bulan ini.">PO Akan Diproses</button>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-top px-4 py-3 bg-body-tertiary d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-label-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2">
                        <i class="mdi mdi-content-save-check-outline fs-5"></i>
                        <span>Simpan Aktivitas</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</form>

@push('after-style')
<style>
    .crm-action-card {
        border: 2px solid #e0e4e8 !important;
        background-color: #ffffff;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }
    .crm-action-card:hover {
        border-color: #696cff !important;
        background-color: rgba(105, 108, 255, 0.04);
        transform: translateY(-2px);
    }
    .btn-check:checked + .crm-action-card {
        border-color: #696cff !important;
        background-color: rgba(105, 108, 255, 0.08) !important;
        box-shadow: 0 4px 12px rgba(105, 108, 255, 0.2);
    }
    .quick-note-chip {
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .quick-note-chip:hover {
        background-color: #696cff !important;
        color: #ffffff !important;
    }
</style>
@endpush

@push('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dynamic Week calculation on Date Change
        document.querySelectorAll('.crm-date-input').forEach(function(input) {
            input.addEventListener('change', function() {
                var clientId = this.dataset.clientId;
                var dateVal = this.value;
                if (!dateVal) return;
                
                var d = new Date(dateVal);
                var dayOfMonth = d.getDate();
                
                // First day of month ISO weekday (1: Mon, 7: Sun)
                var firstDay = new Date(d.getFullYear(), d.getMonth(), 1);
                var firstDayIso = firstDay.getDay() === 0 ? 7 : firstDay.getDay();
                var offset = firstDayIso - 1;
                var weekNum = Math.floor((dayOfMonth + offset - 1) / 7) + 1;
                weekNum = Math.max(1, Math.min(5, weekNum));
                
                var badge = document.getElementById('crmWeekBadge' + clientId);
                var inputWeek = document.getElementById('crmInputWeek' + clientId);
                if (badge) badge.textContent = 'Week ' + weekNum;
                if (inputWeek) inputWeek.value = weekNum;
            });
        });

        // Quick Follow Up Shortcut Buttons
        document.querySelectorAll('.quick-fu-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var targetInput = document.querySelector(this.dataset.target);
                if (!targetInput) return;

                var baseDate = new Date();
                var days = this.dataset.days ? parseInt(this.dataset.days) : 0;
                var months = this.dataset.months ? parseInt(this.dataset.months) : 0;

                if (days > 0) {
                    baseDate.setDate(baseDate.getDate() + days);
                }
                if (months > 0) {
                    baseDate.setMonth(baseDate.getMonth() + months);
                }

                var yyyy = baseDate.getFullYear();
                var mm = String(baseDate.getMonth() + 1).padStart(2, '0');
                var dd = String(baseDate.getDate()).padStart(2, '0');
                targetInput.value = yyyy + '-' + mm + '-' + dd;

                // Visual button toggle
                var parent = this.parentElement;
                parent.querySelectorAll('.quick-fu-btn').forEach(function(b) {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-primary');
                });
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
            });
        });

        // Quick Note Chip Insertion
        document.querySelectorAll('.quick-note-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                var textarea = document.querySelector(this.dataset.target);
                if (!textarea) return;
                var text = this.dataset.text;
                if (textarea.value.trim() === '' || textarea.value.trim() === '-') {
                    textarea.value = text;
                } else {
                    textarea.value = textarea.value.trim() + ' ' + text;
                }
                textarea.focus();
            });
        });

        // Toggle Checkbox Status Customer when status is No Responded
        document.querySelectorAll('.crm-status-radio').forEach(function(radio) {
            radio.addEventListener('change', function() {
                var clientId = this.dataset.clientId;
                var wrapCheckbox = document.getElementById('wrapNonActiveCheckbox' + clientId);
                if (!wrapCheckbox) return;

                if (this.value === 'Not Respon' && this.checked) {
                    wrapCheckbox.classList.remove('d-none');
                } else {
                    wrapCheckbox.classList.add('d-none');
                    // Uncheck all status checkboxes in this client's modal
                    wrapCheckbox.querySelectorAll('.crm-status-update-check').forEach(function(chk) {
                        chk.checked = false;
                    });
                }
            });
        });

        // Mutually exclusive customer status checkboxes (Non-Aktif vs Bangkrupt)
        document.querySelectorAll('.crm-status-update-check').forEach(function(chk) {
            chk.addEventListener('change', function() {
                if (this.checked) {
                    var clientId = this.dataset.clientId;
                    var wrapCheckbox = document.getElementById('wrapNonActiveCheckbox' + clientId);
                    if (!wrapCheckbox) return;

                    wrapCheckbox.querySelectorAll('.crm-status-update-check').forEach(function(otherChk) {
                        if (otherChk !== chk) {
                            otherChk.checked = false;
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
