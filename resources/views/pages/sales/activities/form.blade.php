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

<form action="{{ route('action.leads', $leads->id) }}" method="post" enctype="multipart/form-data" id="formDailyCall{{ $leads->id }}">
    @csrf
    <div class="modal fade" id="createAction{{ $leads->id }}" tabindex="-1" aria-labelledby="modalDailyCallLabel{{ $leads->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                
                <!-- Modal Header with Modern Aesthetic -->
                <div class="modal-header border-bottom px-4 py-3 bg-body-tertiary">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-info text-white shadow-xs" style="width: 44px; height: 44px;">
                            <i class="mdi mdi-phone-forward-outline fs-3"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="modalDailyCallLabel{{ $leads->id }}">
                                Catat Daily Call / Follow Up
                            </h5>
                            <small class="text-muted d-flex align-items-center gap-1 mt-1">
                                <i class="mdi mdi-domain fs-tiny"></i>
                                <span class="fw-semibold text-primary">{{ $leads->company ?? '-' }}</span>
                                @if(!empty($leads->pic_first?->name_pic))
                                    • <span>PIC: {{ $leads->pic_first->name_pic }}</span>
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
                                <input type="radio" class="btn-check daily-action-radio" name="action" id="actionPhoneLeads{{ $leads->id }}" value="Phone Office" required>
                                <label class="btn btn-outline-secondary w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center rounded-3 daily-action-card text-center" for="actionPhoneLeads{{ $leads->id }}">
                                    <div class="avatar avatar-sm mb-2 rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-phone-in-talk fs-4"></i>
                                    </div>
                                    <span class="fw-bold text-dark mb-0" style="font-size: 0.88rem;">Phone Office</span>
                                    <small class="text-muted" style="font-size: 0.72rem;">Telepon kantor</small>
                                </label>
                            </div>

                            <!-- Option 2: WhatsApp -->
                            <div class="col-4">
                                <input type="radio" class="btn-check daily-action-radio" name="action" id="actionWaLeads{{ $leads->id }}" value="WhatsApp" checked required>
                                <label class="btn btn-outline-secondary w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center rounded-3 daily-action-card text-center" for="actionWaLeads{{ $leads->id }}">
                                    <div class="avatar avatar-sm mb-2 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-whatsapp fs-4"></i>
                                    </div>
                                    <span class="fw-bold text-dark mb-0" style="font-size: 0.88rem;">WhatsApp</span>
                                    <small class="text-muted" style="font-size: 0.72rem;">Chat / call WA</small>
                                </label>
                            </div>

                            <!-- Option 3: Visit -->
                            <div class="col-4">
                                <input type="radio" class="btn-check daily-action-radio" name="action" id="actionVisitLeads{{ $leads->id }}" value="Visit" required>
                                <label class="btn btn-outline-secondary w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center rounded-3 daily-action-card text-center" for="actionVisitLeads{{ $leads->id }}">
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
                                    <label for="leadsDate{{ $leads->id }}" class="form-label fw-bold text-dark mb-0" style="font-size: 0.82rem;">
                                        <i class="mdi mdi-calendar text-primary me-1"></i> Tanggal Aktivitas
                                    </label>
                                    <span class="badge bg-info text-white rounded-pill px-2 py-1 fs-tiny shadow-xs" id="leadsWeekBadge{{ $leads->id }}">
                                        Week {{ $currentWeek }}
                                    </span>
                                </div>
                                <input class="form-control bg-white border leads-date-input" type="date" id="leadsDate{{ $leads->id }}" name="date" value="{{ $defaultDate }}" required data-leads-id="{{ $leads->id }}">
                                <input type="hidden" name="week" id="leadsInputWeek{{ $leads->id }}" value="{{ $currentWeek }}">
                                <small class="text-muted mt-1 d-block" style="font-size: 0.72rem;">Minggu otomatis terhitung kalender kerja</small>
                            </div>
                        </div>

                        <!-- Jadwal Next Follow Up -->
                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label for="leadsFollowUp{{ $leads->id }}" class="form-label fw-bold text-dark mb-0" style="font-size: 0.82rem;">
                                        <i class="mdi mdi-clock-outline text-warning me-1"></i> Next Follow Up
                                    </label>
                                    <small class="text-muted" style="font-size: 0.72rem;">Target kontak kembali</small>
                                </div>
                                <input class="form-control bg-white border leads-followup-input" type="date" id="leadsFollowUp{{ $leads->id }}" name="follow_up" value="{{ $defaultFollowUp }}">
                                
                                <!-- Quick Shortcut Buttons -->
                                <div class="d-flex align-items-center gap-1 mt-2 flex-wrap">
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill py-0 px-2 quick-fu-leads-btn" data-target="#leadsFollowUp{{ $leads->id }}" data-days="3" style="font-size: 0.7rem;">+3 Hari</button>
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill py-0 px-2 quick-fu-leads-btn" data-target="#leadsFollowUp{{ $leads->id }}" data-days="7" style="font-size: 0.7rem;">+1 Mgg</button>
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill py-0 px-2 quick-fu-leads-btn" data-target="#leadsFollowUp{{ $leads->id }}" data-days="14" style="font-size: 0.7rem;">+2 Mgg</button>
                                    <button type="button" class="btn btn-xs btn-primary rounded-pill py-0 px-2 quick-fu-leads-btn" data-target="#leadsFollowUp{{ $leads->id }}" data-months="1" style="font-size: 0.7rem;">+1 Bulan</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Status Respon & Tahapan Prospek (Issue) -->
                    <div class="row g-3 mb-3">
                        <!-- Status Respon -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold text-dark mb-2" style="font-size: 0.85rem;">
                                <i class="mdi mdi-check-decagram-outline text-primary me-1"></i> Status Respon
                            </label>
                            <div class="d-flex gap-2">
                                <input type="radio" class="btn-check" name="status" id="statusLeadsResponded{{ $leads->id }}" value="Responded" checked required>
                                <label class="btn btn-outline-success flex-fill rounded-3 py-2 d-flex align-items-center justify-content-center gap-1" for="statusLeadsResponded{{ $leads->id }}">
                                    <i class="mdi mdi-check-circle-outline fs-5"></i>
                                    <span class="fw-semibold" style="font-size: 0.82rem;">Responded</span>
                                </label>

                                <input type="radio" class="btn-check" name="status" id="statusLeadsNotRespon{{ $leads->id }}" value="Not Respon" required>
                                <label class="btn btn-outline-secondary flex-fill rounded-3 py-2 d-flex align-items-center justify-content-center gap-1" for="statusLeadsNotRespon{{ $leads->id }}">
                                    <i class="mdi mdi-phone-missed fs-5"></i>
                                    <span class="fw-semibold" style="font-size: 0.82rem;">Not Responded</span>
                                </label>
                            </div>
                        </div>

                        <!-- Tahapan Leads / Issue Status -->
                        <div class="col-12 col-md-6">
                            <label for="selectIssue{{ $leads->id }}" class="form-label fw-bold text-dark mb-2" style="font-size: 0.85rem;">
                                <i class="mdi mdi-flag-checkered text-primary me-1"></i> Tahapan Prospek (Status)
                            </label>
                            <div class="form-floating form-floating-outline">
                                <select class="form-select rounded-3 border" id="selectIssue{{ $leads->id }}" name="issues" required>
                                    @foreach ($issue as $issues)
                                        <option value="{{ $issues->id }}" {{ $issues->id == $leads->id_issues ? 'selected' : '' }}>
                                            {{ $issues->issue }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="selectIssue{{ $leads->id }}">Pilih Tahapan Leads</label>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Catatan & Quick Templates -->
                    <div class="mb-2">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label for="leadsNote{{ $leads->id }}" class="form-label fw-bold text-dark mb-0" style="font-size: 0.85rem;">
                                <i class="mdi mdi-note-text-outline text-primary me-1"></i> Catatan Aktivitas
                            </label>
                            <small class="text-muted" style="font-size: 0.72rem;">Hasil percakapan / kesepakatan</small>
                        </div>
                        <textarea id="leadsNote{{ $leads->id }}" class="form-control rounded-3" name="note" rows="3" placeholder="Tuliskan ringkasan hasil percakapan, kebutuhan mesin/sparepart, jadwal follow up lanjutan, dsb..."></textarea>
                        
                        <!-- Quick Note Chips -->
                        <div class="d-flex align-items-center gap-1 mt-2 flex-wrap">
                            <small class="text-muted me-1" style="font-size: 0.72rem;"><i class="mdi mdi-lightning-bolt-outline"></i> Template:</small>
                            <button type="button" class="badge bg-label-secondary border-0 quick-note-leads-chip py-1 px-2" data-target="#leadsNote{{ $leads->id }}" data-text="Perkenalan profil Reftech dan katalog produk/service.">Perkenalan Produk</button>
                            <button type="button" class="badge bg-label-secondary border-0 quick-note-leads-chip py-1 px-2" data-target="#leadsNote{{ $leads->id }}" data-text="Follow up penawaran harga & konfirmasi penerimaan quotation.">Follow Up Quote</button>
                            <button type="button" class="badge bg-label-secondary border-0 quick-note-leads-chip py-1 px-2" data-target="#leadsNote{{ $leads->id }}" data-text="Client ada kebutuhan unit baru/service, dijadwalkan survey/meeting.">Ada Kebutuhan Unit</button>
                            <button type="button" class="badge bg-label-secondary border-0 quick-note-leads-chip py-1 px-2" data-target="#leadsNote{{ $leads->id }}" data-text="Nomor tidak tersambung / PIC sedang meeting, follow up ulang.">Tidak Tersambung</button>
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
                        <span>Simpan Daily Call</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</form>

@push('after-style')
<style>
    .daily-action-card {
        border: 2px solid #e0e4e8 !important;
        background-color: #ffffff;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }
    .daily-action-card:hover {
        border-color: #03c3ec !important;
        background-color: rgba(3, 195, 236, 0.04);
        transform: translateY(-2px);
    }
    .btn-check:checked + .daily-action-card {
        border-color: #03c3ec !important;
        background-color: rgba(3, 195, 236, 0.08) !important;
        box-shadow: 0 4px 12px rgba(3, 195, 236, 0.2);
    }
    .quick-note-leads-chip {
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .quick-note-leads-chip:hover {
        background-color: #03c3ec !important;
        color: #ffffff !important;
    }
</style>
@endpush

@push('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dynamic Week calculation on Date Change for Leads
        document.querySelectorAll('.leads-date-input').forEach(function(input) {
            input.addEventListener('change', function() {
                var leadsId = this.dataset.leadsId;
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
                
                var badge = document.getElementById('leadsWeekBadge' + leadsId);
                var inputWeek = document.getElementById('leadsInputWeek' + leadsId);
                if (badge) badge.textContent = 'Week ' + weekNum;
                if (inputWeek) inputWeek.value = weekNum;
            });
        });

        // Quick Follow Up Shortcut Buttons for Leads
        document.querySelectorAll('.quick-fu-leads-btn').forEach(function(btn) {
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
                parent.querySelectorAll('.quick-fu-leads-btn').forEach(function(b) {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-primary');
                });
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
            });
        });

        // Quick Note Chip Insertion for Leads
        document.querySelectorAll('.quick-note-leads-chip').forEach(function(chip) {
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
    });
</script>
@endpush
