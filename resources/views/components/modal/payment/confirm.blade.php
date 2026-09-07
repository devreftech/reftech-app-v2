<form action="{{ route('confirm-payment.quotation', $payment->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal fade animate__animated fadeIn" id="confirmPayment" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-3 px-4">
                    <h5 class="modal-title text-white d-flex align-items-center gap-2">
                        <i class="mdi mdi-check-decagram-outline"></i> Konfirmasi Penerimaan Pembayaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 mb-3 small d-flex align-items-center gap-2">
                        <i class="mdi mdi-information fs-5"></i>
                        <div>
                            Nominal: <strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong><br>
                            Saldo rekening bank yang dipilih akan otomatis bertambah saat diverifikasi.
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Tanggal Masuk Rekening <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" name="date" required
                                value="{{ $payment->date ? \Carbon\Carbon::parse($payment->date)->format('Y-m-d') : \Carbon\Carbon::today()->format('Y-m-d') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small d-flex justify-content-between align-items-center">
                                <span>Rekening Bank Tujuan Masuk <span class="text-danger">*</span></span>
                                @if(!empty($suggestedReason))
                                    <span class="badge bg-label-success" style="font-size: 10.5px;">
                                        <i class="mdi mdi-auto-fix me-1"></i> Auto-Sync: {{ $suggestedReason }}
                                    </span>
                                @endif
                            </label>
                            <select name="id_bank" class="form-select" required>
                                <option value="">-- Pilih Rekening Bank --</option>
                                @php
                                    $selectedBankId = $payment->id_bank ?: ($defaultBankId ?? null);
                                @endphp
                                @foreach ($banks ?? \App\Models\Bank::where('is_active', 1)->get() as $bank)
                                    <option value="{{ $bank->id }}" {{ $selectedBankId == $bank->id ? 'selected' : '' }}>
                                        [{{ strtoupper($bank->entity ?? 'REFTECH') }}] {{ $bank->nama_bank }} - {{ $bank->no_rekening }} (a/n {{ $bank->atas_nama }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                <i class="mdi mdi-information-outline me-1"></i> Rekening otomatis disinkronkan sesuai yang tertera pada Invoice (PPN / Non-PPN). Anda tetap dapat mengubahnya jika customer membayar via rekening lain.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check me-1"></i> Verifikasi &amp; Tambah Saldo
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
