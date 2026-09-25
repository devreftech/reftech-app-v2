@php
    $uid = @$serial ? $serial->id : 'new-' . $product->id;
@endphp
<form action="{{ @$serial ? route('product.equivalent.update', $serial->id) : route('product.equivalent', $product->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    @if (@$serial)
        @method('patch')
    @endif

    <div class="modal fade" id="{{ @$serial ? 'editEquivalent-' . $serial->id : 'createEquivalent-' . $product->id }}" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                {{-- Modal Header --}}
                <div class="modal-header bg-light py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                <i class="mdi {{ @$serial ? 'mdi-pencil-outline' : 'mdi-shuffle-variant' }} fs-4"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                @if (@$serial)
                                    Edit Equivalent: <span class="text-primary">{{ $serial->pn }}</span>
                                @else
                                    Tambah Equivalent Baru
                                @endif
                            </h5>
                            <small class="text-muted" style="font-size: 12px;">
                                Part ekuivalen / alternatif untuk <strong>{{ $product->commodity }}</strong>
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
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

                    <div class="card border border-light-subtle shadow-none bg-body-tertiary rounded-3 p-3 mb-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="brand-{{ $uid }}" class="form-control bg-white" name="brand"
                                        placeholder="Contoh: Donaldson / Fleetguard" value="{{ old('brand', @$serial->brand ?? '') }}" required>
                                    <label for="brand-{{ $uid }}">Brand / Merk <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="pn-{{ $uid }}" class="form-control bg-white" name="pn"
                                        placeholder="Nomor Part" value="{{ old('pn', @$serial->pn ?? '') }}" required>
                                    <label for="pn-{{ $uid }}">Part Number <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline input-group" data-price="1">
                                    <span class="input-group-text bg-light text-muted fw-semibold">Rp</span>
                                    <input type="text" class="form-control bg-white invoice-item-price-label" id="price-label-{{ $uid }}"
                                        data-id="{{ @$serial ? $serial->id : '0' }}" min="0" placeholder="Harga Jual" data-type="currency"
                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                        value="{{ old('price', @$serial->price ? number_format($serial->price, 0, ',', '.') : '') }}">
                                    <input class="form-control invoice-item-price" type="number" name="price"
                                        id="price-{{ @$serial ? $serial->id : '0' }}" value="{{ old('price', @$serial->price ?? '') }}" hidden>
                                    <label for="price-label-{{ $uid }}">Harga Estimasi</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="image-{{ $uid }}" class="form-control bg-white" name="image"
                                        placeholder="https://drive.google.com/..." value="{{ old('image', @$serial->image ?? '') }}">
                                    <label for="image-{{ $uid }}">Link Foto (Google Drive URL)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="detail-{{ $uid }}" name="detail" value="{{ old('detail', @$serial->detail ?? '') }}">
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer bg-light py-3 px-4 border-top d-flex justify-content-between align-items-center">
                    <small class="text-muted"><span class="text-danger">*</span> Wajib diisi</small>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-label-secondary waves-effect rounded-pill px-3" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light rounded-pill px-4 shadow-sm">
                            <i class="mdi mdi-check-circle-outline me-1"></i>
                            {{ @$serial ? 'Simpan Perubahan' : 'Simpan Equivalent' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
