@extends('layouts.sales.app')
@section('title', 'Unit Catalog — ' . $catalog->unit->sku)
@section('content')
    @php
        $u            = $catalog->unit;
        $isCompressor = in_array($u->unit, ['PISTON COMPRESSOR', 'AIR COMPRESSOR SCREW']);
        $isDryer      = in_array($u->unit, ['REFRIGERANT AIR DRYER', 'DESICANT DRYER']);
    @endphp

    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">
                <a href="{{ route('catalog-unit.index') }}" class="text-muted">Unit Catalog</a> /
            </span>
            {{ $u->sku }}
        </h4>
        @if (in_array(Auth::user()->role, ['Admin', 'Sales']))
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm"
                    data-bs-toggle="modal" data-bs-target="#modalEditPrice">
                    <i class="mdi mdi-pencil-outline me-1"></i>Update Price
                </button>
                <button type="button" class="btn btn-label-danger btn-sm btn-delete-catalog"
                    data-id="{{ $catalog->id }}">
                    <i class="mdi mdi-trash-can-outline me-1"></i>Delete
                </button>
            </div>
        @endif
    </div>

    @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Unit Information --}}
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header py-3 d-flex align-items-center gap-2">
                    <h6 class="mb-0 fw-semibold">Unit Information</h6>
                    <a href="{{ route('unit-global.show', $catalog->id_unit) }}"
                        class="badge bg-label-primary small ms-auto" target="_blank">
                        View Unit Global <i class="mdi mdi-open-in-new ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    @include('components.detail-row', ['label' => 'Category',  'value' => $u->unit])
                    @include('components.detail-row', ['label' => 'SKU',       'value' => $u->sku])
                    @include('components.detail-row', ['label' => 'Brand',     'value' => $u->brand])
                    @include('components.detail-row', ['label' => 'Model',     'value' => $u->model])

                    @if ($isCompressor)
                        @include('components.detail-row', ['label' => 'Type',          'value' => $u->type_unit])
                        @include('components.detail-row', ['label' => 'Motor Power',   'value' => $u->power])
                        @include('components.detail-row', ['label' => 'Air Capacity',  'value' => $u->air_cap ? $u->air_cap . ' m³/min' : null])
                        @include('components.detail-row', ['label' => 'Max. Pressure', 'value' => $u->bar ? $u->bar . ' Bar' : null])
                        @include('components.detail-row', ['label' => 'Drive',         'value' => $u->connect])
                        @include('components.detail-row', ['label' => 'Cooling',       'value' => $u->cooling])
                        @include('components.detail-row', ['label' => 'Discharge',     'value' => $u->exhaust])
                    @elseif ($isDryer)
                        @include('components.detail-row', ['label' => 'Air Capacity',     'value' => $u->air_cap ? $u->air_cap . ' m³/min' : null])
                        @include('components.detail-row', ['label' => 'Refrigerant Type', 'value' => $u->refrigerant_type])
                        @include('components.detail-row', ['label' => 'PDP',              'value' => $u->pdp])
                    @endif

                    @include('components.detail-row', ['label' => 'Dimension', 'value' => $u->dimension])
                    @include('components.detail-row', ['label' => 'Weight',    'value' => $u->weight ? $u->weight . ' Kg' : null])

                    @if ($u->filtration)
                        @include('components.detail-row', ['label' => 'Filtration',  'value' => $u->filtration])
                        @include('components.detail-row', ['label' => 'Oil Content', 'value' => $u->oil_content])
                        @include('components.detail-row', ['label' => 'Grade',       'value' => $u->grade])
                    @endif

                    @if (!$isCompressor && !$isDryer && $u->desc)
                        @include('components.detail-row', ['label' => 'Description', 'value' => $u->desc])
                    @endif

                    <hr class="my-3">

                    {{-- Current pricing summary --}}
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">IDR Price</small>
                        <span class="fw-semibold">Rp {{ number_format($catalog->price_idr, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">USD Price</small>
                        <span class="fw-semibold">
                            {{ $catalog->price_usd > 0 ? '$ ' . number_format($catalog->price_usd, 2) : '-' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Status</small>
                        @if ($catalog->is_active)
                            <span class="badge bg-label-success">Active</span>
                        @else
                            <span class="badge bg-label-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Price History --}}
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header py-3">
                    <h6 class="mb-0 fw-semibold">Price History</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Date</th>
                                <th class="text-center">IDR Price</th>
                                <th class="text-center">USD Price</th>
                                <th>Note</th>
                                <th>Changed By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($catalog->priceHistory as $h)
                                <tr>
                                    <td class="text-center text-nowrap">{{ $h->created_at->format('d-m-Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-between px-2">
                                            <span>Rp.</span>
                                            <span>{{ number_format($h->price_idr, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        {{ $h->price_usd > 0 ? '$ ' . number_format($h->price_usd, 2) : '-' }}
                                    </td>
                                    <td>{{ $h->note ?: '-' }}</td>
                                    <td>{{ $h->changedBy?->name ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No price history yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Edit Pricing --}}
    <div class="modal fade" id="modalEditPrice" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Update Price & Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('catalog-unit.update', $catalog->id) }}" method="POST" id="form-catalog">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">IDR Price</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control text-end"
                                    id="price-idr-display"
                                    autocomplete="off">
                                <input type="hidden" name="price_idr" id="price-idr-raw" value="{{ $catalog->price_idr }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">USD Price <span class="text-muted small">(reference)</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="price_usd"
                                    step="0.01" min="0"
                                    value="{{ $catalog->price_usd }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Spec Notes</label>
                            <textarea class="form-control" name="spec_note" rows="3"
                                placeholder="Additional specification notes...">{{ $catalog->spec_note }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active"
                                    id="switchActive" value="1"
                                    {{ $catalog->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="switchActive">
                                    {{ $catalog->is_active ? 'Active' : 'Inactive' }}
                                </label>
                            </div>
                        </div>

                        <div class="mb-1" id="note-wrapper" style="display:none;">
                            <label class="form-label">Reason for Price Change</label>
                            <input type="text" class="form-control" name="note"
                                placeholder="Optional — recorded in price history">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('after-script')
    <script>
        function fmtRupiah(n) {
            n = String(n).replace(/\D/g, '');
            return n ? n.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
        }

        function parseRupiah(str) {
            return parseInt(String(str).replace(/\./g, '')) || 0;
        }

        var displayEl = document.getElementById('price-idr-display');
        var rawEl     = document.getElementById('price-idr-raw');
        var noteWrap  = document.getElementById('note-wrapper');
        var origIdr   = parseInt(rawEl.value) || 0;

        // Format IDR display field when modal opens
        document.getElementById('modalEditPrice')?.addEventListener('show.bs.modal', function () {
            displayEl.value = fmtRupiah(rawEl.value);
            noteWrap.style.display = 'none';
        });

        displayEl.addEventListener('input', function () {
            var raw = this.value.replace(/\D/g, '');
            this.value  = fmtRupiah(raw);
            rawEl.value = raw || 0;
            noteWrap.style.display = (parseInt(raw) !== origIdr) ? '' : 'none';
        });

        document.querySelector('[name="price_usd"]')?.addEventListener('input', function () {
            noteWrap.style.display = '';
        });

        // Safety net: sync raw field from display before submit
        document.getElementById('form-catalog')?.addEventListener('submit', function () {
            rawEl.value = parseRupiah(displayEl.value) || 0;
        });

        document.querySelector('.btn-delete-catalog')?.addEventListener('click', function () {
            if (!confirm('Remove this unit from the catalog?')) return;
            var id = this.dataset.id;
            fetch('/catalog-unit/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            }).then(function () {
                window.location.href = '/catalog-unit';
            });
        });

        document.getElementById('switchActive')?.addEventListener('change', function () {
            this.nextElementSibling.textContent = this.checked ? 'Active' : 'Inactive';
        });
    </script>
@endpush
