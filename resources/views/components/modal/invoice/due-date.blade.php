@php
    $quoteObj  = $quote ?? null;
    $clientObj = $quoteObj?->client ?? null;
    $termStr   = $invoice->term ?? $quoteObj?->payment_method ?? '';
    preg_match('/\d+/', $termStr, $mDays);
    $tempoDays = isset($mDays[0]) && intval($mDays[0]) > 0 ? intval($mDays[0]) : 30;

    $invDateObj = $invoice->date ? \Carbon\Carbon::parse($invoice->date) : \Carbon\Carbon::today();

    $paymentRec = isset($payments) && $payments
        ? ($payments->firstWhere('type', 'Tempo') ?? $payments->first())
        : null;

    if ($paymentRec?->due_date) {
        $defaultDueDate = \Carbon\Carbon::parse($paymentRec->due_date)->format('Y-m-d');
    } else {
        $defaultDueDate = (clone $invDateObj)->addDays($tempoDays)->format('Y-m-d');
    }
@endphp

<div class="modal fade" id="dueDate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark mb-0">
                    <i class="mdi mdi-calendar-clock me-1 text-warning"></i> Setting Tanggal Jatuh Tempo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('invoice.due_date', $invoice->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="p-3 mb-3 rounded bg-light-subtle border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">No. Invoice</span>
                            <span class="fw-bold text-primary">{{ $invoice->no_invoice }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Term / Payment Method</span>
                            <span class="fw-semibold text-dark">{{ $termStr ?: 'Tempo' }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Invoice <span class="text-danger">*</span></label>
                        <input class="form-control" type="date" id="modal-due-inv-date" name="date"
                            value="{{ $invDateObj->format('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold mb-0">Tanggal Jatuh Tempo (Due Date) <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="btn-recalc-due" style="font-size:11px;">
                                <i class="mdi mdi-refresh me-1"></i>Reset ke Default (+{{ $tempoDays }} Hari)
                            </button>
                        </div>
                        <input class="form-control fw-bold text-dark border-warning" type="date" id="modal-due-date-val" name="due_date"
                            value="{{ $defaultDueDate }}" required>
                        <div class="form-text text-muted mt-1" style="font-size: 11px;">
                            <i class="mdi mdi-information-outline me-1 text-warning"></i>
                            Otomatis dihitung dari <strong>Tanggal Invoice + {{ $tempoDays }} Hari</strong>. Anda dapat mengedit tanggal ini secara manual jika ada penyesuaian (misal: keterlambatan pengiriman ekspedisi).
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold">Catatan / Keterangan Penyesuaian <span class="text-muted small">(Opsional)</span></label>
                        <input type="text" class="form-control" name="note" value="{{ $paymentRec?->note }}"
                            placeholder="misal: Ditambah 5 hari karena waktu transit ekspedisi">
                    </div>
                </div>

                <div class="modal-footer bg-light py-2 border-top">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning shadow-sm">
                        <i class="mdi mdi-content-save me-1"></i> Simpan Due Date
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        const tempoDays = {{ $tempoDays }};
        const invDateEl = document.getElementById('modal-due-inv-date');
        const dueDateEl = document.getElementById('modal-due-date-val');
        const resetBtn  = document.getElementById('btn-recalc-due');

        function calculateDefaultDueDate(invDateStr) {
            if (!invDateStr) return '';
            const d = new Date(invDateStr);
            d.setDate(d.getDate() + tempoDays);
            return d.toISOString().slice(0, 10);
        }

        if (invDateEl && dueDateEl) {
            // When invoice date changes, auto-update due date default
            invDateEl.addEventListener('change', function () {
                dueDateEl.value = calculateDefaultDueDate(this.value);
            });

            // Reset button helper
            if (resetBtn) {
                resetBtn.addEventListener('click', function () {
                    dueDateEl.value = calculateDefaultDueDate(invDateEl.value);
                });
            }
        }
    })();
</script>
