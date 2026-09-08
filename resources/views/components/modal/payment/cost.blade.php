<div class="modal fade" id="addCost" tabindex="-1" aria-labelledby="addCostLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('payment.addCost', $payment->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2 mb-0" id="addCostLabel">
                        <i class="mdi mdi-currency-usd text-info fs-4"></i> {{ $payment->cost > 0 ? 'Edit' : 'Tambah' }} Biaya Tambahan (Cost)
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
                        <label class="form-label fw-semibold small text-muted text-uppercase" style="font-size: 11px;">Nominal Biaya (Cost / Admin Bank) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">Rp</span>
                            <input type="text" class="form-control invoice-item-cost-label" id="costLabel"
                                name="harga" placeholder="Masukkan nominal cost" data-type="currency"
                                min="0" value="{{ old('cost', number_format($payment->cost ?? 0, 0, ',', '.')) }}" required>
                            <input class="form-control invoice-item-cost" type="number" name="cost"
                                id="cost" value="{{ old('cost', $payment->cost ?? 0) }}" hidden>
                        </div>
                        <small class="text-muted d-block mt-1">Biaya admin transfer atau biaya lain yang memotong nominal diterima.</small>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5 px-4 bg-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-label-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info rounded-pill px-3 shadow-sm text-white">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Cost
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
