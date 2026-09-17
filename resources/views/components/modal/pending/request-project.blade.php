<form action="{{ route('purchase-request.store-project', $pending->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="purchaseReqPrj" tabindex="-1" aria-labelledby="purchaseReqPrjLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light py-3 border-bottom">
                    <div>
                        <h5 class="modal-title fw-bold text-primary mb-1 d-flex align-items-center" id="purchaseReqPrjLabel">
                            <i class="mdi mdi-cart-plus me-2 fs-4"></i> Buat Purchase Request (PR - Project)
                        </h5>
                        <div class="text-muted font-12">
                            <span class="fw-semibold text-dark">{{ $pending->quote->invoice[0]?->no_invoice ?? ($pending->quote->pic->client->company ?? '-') }}</span>
                            &bull; SO #{{ $pending->id }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="alert alert-primary d-flex align-items-center py-2 px-3 mb-3">
                        <i class="mdi mdi-information-outline fs-5 me-2"></i>
                        <span class="font-12">
                            Pilih Equivalent dan tentukan <strong>Qty PR</strong> serta <strong>Catatan</strong> item yang ingin dibuatkan Purchase Request.
                        </span>
                    </div>

                    <div class="card border shadow-none mb-0">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="font-12">
                                        <th style="width: 50%;">Item Equivalent</th>
                                        <th style="width: 15%;">Qty</th>
                                        <th style="width: 35%;">Note</th>
                                    </tr>
                                </thead>
                                <tbody class="font-13">
                                    <tr>
                                        <td>
                                            <div class="form-floating form-floating-outline mb-1">
                                                <select class="form-select select2-equivalent-ajax" data-allow-clear="true"
                                                    name="id_equivalent" data-id="1" style="width: 100%;">
                                                    <option value="0"> ---- Pilih / Cari Equivalent Di Sini ---- </option>
                                                </select>
                                                <label for="Equivalent">Equivalent</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" class="form-control fw-bold"
                                                    name="qty" min="0" step="any"
                                                    placeholder="0" required>
                                                <label>Qty</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <textarea class="form-control" name="note" placeholder="Catatan PR..."></textarea>
                                                <label>Note</label>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 border-top">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <i class="mdi mdi-cart-plus me-1"></i> Buat Purchase Request
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
