@extends('layouts.sales.app')
@section('title', 'Audit Tools - ' . $audit->no_audit)
@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Audit Tools /</span> {{ $audit->no_audit }}
        @php
            $headerBadge = [
                'Draft' => 'bg-label-secondary',
                'Submitted' => 'bg-label-warning',
                'Verified' => 'bg-label-success',
                'Rejected' => 'bg-label-danger',
            ][$audit->status_submit] ?? 'bg-label-secondary';
        @endphp
        <span class="badge {{ $headerBadge }} align-middle">{{ $audit->status_submit }}</span>
    </h4>
    <p class="text-muted mb-4">
        Periode {{ $audit->period->tahun }} Semester {{ $audit->period->semester }} —
        {{ \Carbon\Carbon::parse($audit->period->tanggal_mulai)->format('d M') }} s/d
        {{ \Carbon\Carbon::parse($audit->period->tanggal_selesai)->format('d M Y') }}
        @if ($audit->submitted_at)
            <br>Disubmit: {{ \Carbon\Carbon::parse($audit->submitted_at)->format('d M Y H:i') }}
        @endif
    </p>

    <a href="{{ route('tool-audit.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="mdi mdi-arrow-left"></i> Kembali
    </a>

    <form action="{{ route('tool-audit.submit', $audit->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @foreach ($audit->items as $item)
            @php
                $tool = $item->fixedAsset;
                $master = $tool->toolsMaster ?? null;
                $kondisi = old("items.{$item->id}.kondisi", $item->kondisi);
            @endphp
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3 align-items-start">
                        <div class="col-md-2 text-center">
                            @if ($tool && $tool->foto_awal)
                                <img src="{{ asset($tool->foto_awal) }}" alt="foto awal"
                                    style="width:100%;max-width:100px;aspect-ratio:1/1;object-fit:cover;border-radius:6px;">
                                <div class="small text-muted mt-1">Foto Awal</div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-1">{{ $master->nama_tools ?? '-' }}</h6>
                            <div class="text-muted small mb-2">Qty terdaftar: {{ $tool->qty }}</div>

                            @if ($editable)
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="number" class="form-control" name="items[{{ $item->id }}][qty_actual]"
                                        value="{{ old("items.{$item->id}.qty_actual", $item->qty_actual ?? $tool->qty) }}"
                                        min="0" required>
                                    <label>Qty Sekarang</label>
                                </div>
                            @else
                                <div class="text-muted small">Qty sekarang: {{ $item->qty_actual ?? '-' }}</div>
                            @endif
                        </div>
                        <div class="col-md-3">
                            @if ($editable)
                                <div class="btn-group w-100 kondisi-group" data-item="{{ $item->id }}" role="group">
                                    <input type="radio" class="btn-check kondisi-radio" data-item="{{ $item->id }}"
                                        name="items[{{ $item->id }}][kondisi]" id="ada-{{ $item->id }}" value="Ada"
                                        {{ $kondisi == 'Ada' ? 'checked' : '' }} autocomplete="off">
                                    <label class="btn btn-outline-success btn-sm" for="ada-{{ $item->id }}">Ada</label>

                                    <input type="radio" class="btn-check kondisi-radio" data-item="{{ $item->id }}"
                                        name="items[{{ $item->id }}][kondisi]" id="rusak-{{ $item->id }}" value="Rusak"
                                        {{ $kondisi == 'Rusak' ? 'checked' : '' }} autocomplete="off">
                                    <label class="btn btn-outline-warning btn-sm" for="rusak-{{ $item->id }}">Rusak</label>

                                    <input type="radio" class="btn-check kondisi-radio" data-item="{{ $item->id }}"
                                        name="items[{{ $item->id }}][kondisi]" id="hilang-{{ $item->id }}" value="Hilang"
                                        {{ $kondisi == 'Hilang' ? 'checked' : '' }} autocomplete="off">
                                    <label class="btn btn-outline-danger btn-sm" for="hilang-{{ $item->id }}">Hilang</label>
                                </div>

                                <div class="mt-2 alasan-wrap-{{ $item->id }}"
                                    style="display: {{ in_array($kondisi, ['Rusak', 'Hilang']) ? 'block' : 'none' }};">
                                    <textarea class="form-control form-control-sm" name="items[{{ $item->id }}][alasan]"
                                        placeholder="{{ $kondisi == 'Hilang' ? 'Catatan (opsional)...' : 'Alasan kerusakan...' }}">{{ old("items.{$item->id}.alasan", $item->alasan) }}</textarea>
                                </div>

                                <div class="mt-2 metode-wrap-{{ $item->id }}"
                                    style="display: {{ $kondisi == 'Hilang' ? 'block' : 'none' }};">
                                    <select class="form-select form-select-sm" name="items[{{ $item->id }}][metode_ganti]">
                                        <option value="">-- Metode Ganti --</option>
                                        <option value="Beli Sendiri"
                                            {{ old("items.{$item->id}.metode_ganti", $item->metode_ganti) == 'Beli Sendiri' ? 'selected' : '' }}>
                                            Beli Sendiri</option>
                                        <option value="Potong Bonus"
                                            {{ old("items.{$item->id}.metode_ganti", $item->metode_ganti) == 'Potong Bonus' ? 'selected' : '' }}>
                                            Potong Bonus</option>
                                    </select>
                                    @if ($master && ($master->foto_referensi || $master->link_pembelian))
                                        <div class="small mt-2 p-2 bg-label-info rounded">
                                            Referensi beli: {{ $master->nama_tools }}
                                            @if ($master->link_pembelian)
                                                — <a href="{{ $master->link_pembelian }}" target="_blank" rel="noopener">{{ $master->link_pembelian }}</a>
                                            @endif
                                            @if ($master->foto_referensi)
                                                <div class="mt-1">
                                                    <img src="{{ asset($master->foto_referensi) }}" alt="referensi"
                                                        style="max-width:80px;border-radius:4px;">
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @else
                                @php
                                    $badge = ['Ada' => 'bg-label-success', 'Rusak' => 'bg-label-warning', 'Hilang' => 'bg-label-danger'][$item->kondisi] ?? 'bg-label-secondary';
                                @endphp
                                <span class="badge {{ $badge }}">{{ $item->kondisi ?? 'Belum diisi' }}</span>
                                @if ($item->alasan)
                                    <div class="small text-muted mt-1">{{ $item->alasan }}</div>
                                @endif
                                @if ($item->metode_ganti)
                                    <div class="small text-muted mt-1">Ganti: {{ $item->metode_ganti }}</div>
                                @endif
                            @endif
                        </div>
                        <div class="col-md-3 text-center foto-upload-wrapper" data-item-id="{{ $item->id }}">
                            <div class="foto-preview-container">
                                @if ($item->foto_audit)
                                    <img src="{{ asset($item->foto_audit) }}" alt="foto audit" class="foto-preview-img"
                                        style="width:100%;max-width:100px;aspect-ratio:1/1;object-fit:cover;border-radius:6px;">
                                    <div class="small text-muted mt-1 foto-preview-label">Foto Audit</div>
                                @else
                                    <div class="no-foto-placeholder text-muted small">Belum ada foto</div>
                                @endif
                            </div>
                            <div class="upload-status text-primary small mt-1" style="display:none;"></div>
                            @if ($editable)
                                <input type="file" class="form-control form-control-sm mt-2 foto-audit-input" accept="image/*"
                                    name="items[{{ $item->id }}][foto_audit]" {{ $item->foto_audit ? '' : 'required' }}>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if ($editable)
            <button type="submit" class="btn btn-primary mb-4">Submit Audit</button>
        @endif
    </form>
@endsection()

@push('page-script')
    <script>
        document.querySelectorAll('.kondisi-radio').forEach(function (radio) {
            radio.addEventListener('change', function () {
                var id = this.getAttribute('data-item');
                var alasanWrap = document.querySelector('.alasan-wrap-' + id);
                var metodeWrap = document.querySelector('.metode-wrap-' + id);
                if (alasanWrap) {
                    alasanWrap.style.display = (this.value === 'Rusak' || this.value === 'Hilang') ? 'block' : 'none';
                    var textarea = alasanWrap.querySelector('textarea');
                    if (textarea) textarea.placeholder = this.value === 'Hilang' ? 'Catatan (opsional)...' : 'Alasan kerusakan...';
                }
                if (metodeWrap) metodeWrap.style.display = this.value === 'Hilang' ? 'block' : 'none';
            });
        });

        // AJAX Photo Upload
        const submitBtn = document.querySelector('button[type="submit"]');

        document.querySelectorAll('.foto-audit-input').forEach(function (input) {
            input.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;

                const wrapper = this.closest('.foto-upload-wrapper');
                const itemId = wrapper.getAttribute('data-item-id');
                const statusDiv = wrapper.querySelector('.upload-status');
                const previewContainer = wrapper.querySelector('.foto-preview-container');
                const fileInput = this;

                // Disable input and submit button
                fileInput.disabled = true;
                if (submitBtn) submitBtn.disabled = true;
                statusDiv.style.display = 'block';
                statusDiv.innerHTML = '<span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span> Mengunggah...';

                const formData = new FormData();
                formData.append('foto_audit', file);
                formData.append('_token', '{{ csrf_token() }}');

                fetch(`/tool-audit/item/${itemId}/upload-photo`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update preview
                        previewContainer.innerHTML = `
                            <img src="${data.foto_url}" alt="foto audit" class="foto-preview-img"
                                style="width:100%;max-width:100px;aspect-ratio:1/1;object-fit:cover;border-radius:6px;">
                            <div class="small text-muted mt-1 foto-preview-label">Foto Audit</div>
                        `;
                        // Remove required attribute
                        fileInput.removeAttribute('required');
                        // Clear the input value so it's not uploaded again in the main form submit
                        fileInput.value = '';
                        
                        statusDiv.innerHTML = '<span class="text-success"><i class="mdi mdi-check-circle"></i> Berhasil diunggah</span>';
                    } else {
                        throw new Error('Gagal mengunggah foto.');
                    }
                })
                .catch(error => {
                    console.error(error);
                    let errMsg = 'Gagal mengunggah foto.';
                    if (error.errors && error.errors.foto_audit) {
                        errMsg = error.errors.foto_audit.join(', ');
                    } else if (error.message) {
                        errMsg = error.message;
                    }
                    alert(errMsg);
                    statusDiv.innerHTML = `<span class="text-danger"><i class="mdi mdi-alert-circle"></i> ${errMsg}</span>`;
                })
                .finally(() => {
                    // Re-enable input and submit button
                    fileInput.disabled = false;
                    
                    // Check if any other uploads are in progress before enabling submit button
                    const anyUploading = Array.from(document.querySelectorAll('.upload-status')).some(el => {
                        return el.style.display === 'block' && el.innerHTML.includes('spinner-border');
                    });
                    if (!anyUploading && submitBtn) {
                        submitBtn.disabled = false;
                    }
                });
            });
        });
    </script>
@endpush
