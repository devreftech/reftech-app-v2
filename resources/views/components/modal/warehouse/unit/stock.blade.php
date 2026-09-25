<form action="{{ route('update.stock-unit', $product->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    @if (@$product)
        @method('patch')
    @endif

    <div class="modal fade" id="{{ 'updateStock-' . $product->id }}" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                {{-- Modal Header --}}
                <div class="modal-header bg-light py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-circle bg-label-success">
                                <i class="mdi mdi-tray-arrow-down fs-4"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                Update Stock: <span class="text-primary">{{ $product->commodity }}</span>
                            </h5>
                            <small class="text-muted" style="font-size: 12px;">
                                Atur stok awal, stok aktual kantor/gudang, dan rincian replacement
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-alert-circle-outline fs-5"></i>
                                <strong>Terjadi kesalahan input:</strong>
                            </div>
                            <ul class="mb-0 mt-2 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Section 1: Stok Awal & Tanggal Efektif --}}
                    <div class="card border border-light-subtle shadow-none bg-body-tertiary rounded-3 mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="mdi mdi-calendar-clock-outline text-primary fs-5"></i>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">Stok Awal & Tanggal Efektif</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <div class="form-floating form-floating-outline">
                                        <input type="number" step="any" id="first_stock_unit_{{ $product->id }}" class="form-control bg-white"
                                            name="first_stock" value="{{ old('first_stock', $product->first_stock ?? 0) }}"
                                            placeholder="Stok Awal">
                                        <label for="first_stock_unit_{{ $product->id }}">First Stock ({{ $product->unit ?? 'Unit' }})</label>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-floating form-floating-outline">
                                        <input type="date" id="date_unit_{{ $product->id }}" class="form-control bg-white" name="date"
                                            value="{{ old('date', $product->date ?? now()->format('Y-m-d')) }}">
                                        <label for="date_unit_{{ $product->id }}">Tanggal Berlaku</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Ringkasan Stok Terkini / Live Overview --}}
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-chart-box-outline text-primary fs-5"></i>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">Stok Terkini (Recent Stock)</h6>
                            </div>
                            @if (count($details) > 0)
                                <span class="badge rounded-pill px-2 py-1" style="background-color: #f1f5f9; color: #475569; font-size: 11px; border: 1px solid #e2e8f0;">
                                    <i class="mdi mdi-calculator text-primary me-1"></i>Otomatis dari Rincian Part
                                </span>
                            @endif
                        </div>

                        <div class="row g-2">
                            {{-- Warehouse Stock (BKS) --}}
                            <div class="col-md-6 col-sm-6">
                                <div class="card border h-100 p-3 rounded-3 shadow-none position-relative overflow-hidden"
                                    style="background-color: #ffffff; border-color: #e2e8f0 !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="text-secondary fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Warehouse (BKS)</span>
                                        <div class="avatar avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 28px; height: 28px; background: #e0e7ff; color: #4338ca;">
                                            <i class="mdi mdi-warehouse fs-6"></i>
                                        </div>
                                    </div>
                                    @if (count($details) > 0)
                                        <input type="hidden" id="recent-warehouse-stock-unit-{{ $product->id }}"
                                            class="recent-warehouse-stock" name="warehouse_recent_stock"
                                            value="{{ old('warehouse_recent_stock', $product->warehouse_stock ?? 0) }}">
                                        <div class="d-flex align-items-baseline gap-1">
                                            <span class="fw-bold recent-warehouse-stock-preview" style="font-size: 22px; color: #1e293b;">
                                                {{ $product->warehouse_stock ?? 0 }}
                                            </span>
                                            <small class="text-muted fw-semibold" style="font-size: 12px;">{{ $product->unit ?? 'Unit' }}</small>
                                        </div>
                                    @else
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="any" id="recent-warehouse-stock-unit-{{ $product->id }}"
                                                class="form-control fw-bold text-center recent-warehouse-stock manual-stock-input"
                                                style="color: #1e293b; background: #f8fafc;"
                                                name="warehouse_recent_stock" value="{{ old('warehouse_recent_stock', $product->warehouse_stock ?? 0) }}">
                                            <span class="input-group-text bg-light text-muted">{{ $product->unit ?? 'Unit' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Office Stock (BDG) --}}
                            <div class="col-md-6 col-sm-6">
                                <div class="card border h-100 p-3 rounded-3 shadow-none position-relative overflow-hidden"
                                    style="background-color: #ffffff; border-color: #e2e8f0 !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="text-secondary fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Office (BDG)</span>
                                        <div class="avatar avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 28px; height: 28px; background: #e0f2fe; color: #0369a1;">
                                            <i class="mdi mdi-office-building-outline fs-6"></i>
                                        </div>
                                    </div>
                                    @if (count($details) > 0)
                                        <input type="hidden" id="recent-office-stock-unit-{{ $product->id }}"
                                            class="recent-office-stock" name="office_recent_stock"
                                            value="{{ old('office_recent_stock', $product->stock ?? 0) }}">
                                        <div class="d-flex align-items-baseline gap-1">
                                            <span class="fw-bold recent-office-stock-preview" style="font-size: 22px; color: #1e293b;">
                                                {{ $product->stock ?? 0 }}
                                            </span>
                                            <small class="text-muted fw-semibold" style="font-size: 12px;">{{ $product->unit ?? 'Unit' }}</small>
                                        </div>
                                    @else
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="any" id="recent-office-stock-unit-{{ $product->id }}"
                                                class="form-control fw-bold text-center recent-office-stock manual-stock-input"
                                                style="color: #1e293b; background: #f8fafc;"
                                                name="office_recent_stock" value="{{ old('office_recent_stock', $product->stock ?? 0) }}">
                                            <span class="input-group-text bg-light text-muted">{{ $product->unit ?? 'Unit' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Total Live Stock Banner --}}
                        <div class="d-flex align-items-center justify-content-between p-3 mt-3 rounded-3 border"
                            style="background-color: #f0fdf4; border-color: #bbf7d0 !important;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 28px; height: 28px; background: #dcfce7; color: #15803d;">
                                    <i class="mdi mdi-cube-outline fs-6"></i>
                                </div>
                                <div>
                                    <span class="fw-semibold text-dark d-block" style="font-size: 13px;">Total Keseluruhan (All Stock)</span>
                                    <small class="text-muted" style="font-size: 11px;">Warehouse + Office</small>
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3 py-2 fw-bold fs-6 total-all-stock-preview"
                                style="background-color: #15803d; color: #ffffff; letter-spacing: 0.3px;">
                                {{ ($product->stock ?? 0) + ($product->warehouse_stock ?? 0) }} {{ $product->unit ?? 'Unit' }}
                            </span>
                        </div>
                    </div>

                    {{-- Section 3: Rincian Replacement / Sub Items --}}
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-puzzle-outline text-primary fs-5"></i>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">Rincian Stok Part / Replacement</h6>
                            </div>
                            <span class="badge bg-label-primary rounded-pill px-2 py-1" style="font-size: 11px;">
                                {{ count($details) }} Part Terdaftar
                            </span>
                        </div>

                        @if (count($details) > 0)
                            <div class="table-responsive border rounded-3 overflow-hidden">
                                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th style="width: 35%;">Nama Replacement / Part</th>
                                            <th style="width: 25%;">Office Stock (BDG)</th>
                                            <th style="width: 25%;">Warehouse Stock (BKS)</th>
                                            <th style="width: 10%;" class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($details as $index => $detail)
                                            <tr class="replacement-row">
                                                <td class="text-muted fw-semibold">{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="fw-semibold text-dark">{{ $detail->replacement }}</div>
                                                    <small class="text-muted" style="font-size: 11px;">ID Part: #{{ $detail->id }}</small>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" step="any"
                                                            id="office-stock-unit-{{ $product->id }}-{{ $index }}"
                                                            class="form-control office-stock"
                                                            name="office_stock[]" data-id="{{ $index }}"
                                                            value="{{ old('office_stock.' . $index, $detail->stock ?? 0) }}"
                                                            placeholder="0">
                                                        <span class="input-group-text">{{ $product->unit ?? 'Unit' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" step="any"
                                                            id="warehouse-stock-unit-{{ $product->id }}-{{ $index }}"
                                                            class="form-control warehouse-stock"
                                                            name="warehouse_stock[]" data-id="{{ $index }}"
                                                            value="{{ old('warehouse_stock.' . $index, $detail->warehouse_stock ?? 0) }}"
                                                            placeholder="0">
                                                        <span class="input-group-text">{{ $product->unit ?? 'Unit' }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge bg-label-secondary row-subtotal fw-bold">
                                                        {{ ($detail->stock ?? 0) + ($detail->warehouse_stock ?? 0) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center p-4 border rounded-3 bg-light">
                                <i class="mdi mdi-package-variant-closed text-muted fs-1 d-block mb-1"></i>
                                <h6 class="fw-semibold text-muted mb-1" style="font-size: 14px;">Tidak Ada Item Replacement</h6>
                                <p class="text-muted mb-0" style="font-size: 12px;">
                                    Unit ini tidak memiliki daftar part replacement. Anda dapat menginput stok Office & Warehouse langsung pada kotak ringkasan di atas.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer bg-light py-3 px-4 border-top d-flex justify-content-between align-items-center">
                    <div class="text-muted d-none d-sm-flex align-items-center gap-1" style="font-size: 12px;">
                        <i class="mdi mdi-information-outline text-primary"></i>
                        <span>Pastikan data stok fisik telah dihitung akurat.</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-label-secondary waves-effect rounded-pill px-3" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light rounded-pill px-4 shadow-sm">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('script')
    <script>
        $(() => {
            const modalId = '#updateStock-{{ $product->id }}';
            const unitName = '{{ $product->unit ?? "Unit" }}';

            function calculateTotals() {
                const $modal = $(modalId);
                let totalOffice = 0;
                let totalWarehouse = 0;

                const hasDetails = $modal.find('.replacement-row').length > 0;

                if (hasDetails) {
                    $modal.find('.replacement-row').each(function() {
                        const officeVal = parseFloat($(this).find('.office-stock').val()) || 0;
                        const warehouseVal = parseFloat($(this).find('.warehouse-stock').val()) || 0;
                        const rowSubtotal = officeVal + warehouseVal;

                        $(this).find('.row-subtotal').text(rowSubtotal);
                        totalOffice += officeVal;
                        totalWarehouse += warehouseVal;
                    });

                    $modal.find('.recent-office-stock').val(totalOffice);
                    $modal.find('.recent-office-stock-preview').text(totalOffice);

                    $modal.find('.recent-warehouse-stock').val(totalWarehouse);
                    $modal.find('.recent-warehouse-stock-preview').text(totalWarehouse);
                } else {
                    totalOffice = parseFloat($modal.find('.recent-office-stock').val()) || 0;
                    totalWarehouse = parseFloat($modal.find('.recent-warehouse-stock').val()) || 0;
                }

                const grandTotal = totalOffice + totalWarehouse;
                $modal.find('.total-all-stock-preview').text(grandTotal + ' ' + unitName);
            }

            $(modalId).on('input keyup change', '.office-stock, .warehouse-stock, .manual-stock-input', function() {
                calculateTotals();
            });

            $(modalId).on('shown.bs.modal', function() {
                calculateTotals();
            });
        });
    </script>
@endpush
