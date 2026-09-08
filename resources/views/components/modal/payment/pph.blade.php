<div class="modal fade" id="addPPH" tabindex="-1" aria-labelledby="addPPHLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('payment.addPph', $payment->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2 mb-0" id="addPPHLabel">
                        <i class="mdi mdi-percent text-primary fs-4"></i> {{ $payment->pph > 0 ? 'Edit' : 'Tambah' }} Potongan PPH
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase" style="font-size: 11px;">Nominal Potongan PPH <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">Rp</span>
                            <input type="text" class="form-control invoice-item-pph-label" id="pphLabel"
                                name="harga" placeholder="Masukkan nominal PPH" data-type="currency"
                                min="0" value="{{ old('pph', number_format($payment->pph ?? 0, 0, ',', '.')) }}" required>
                            <input class="form-control invoice-item-pph" type="number" name="pph"
                                id="pph" value="{{ old('pph', $payment->pph ?? 0) }}" hidden>
                        </div>
                        <small class="text-muted d-block mt-1">Potongan PPH akan mengurangi nett pembayaran yang masuk ke kas/bank.</small>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5 px-4 bg-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-label-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-3 shadow-sm">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan PPH
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
