@extends('layouts.sales.app')
@section('title', 'Edit - ' . $audit->no_audit)
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $headerBadge = [
            'Submitted' => 'bg-label-warning',
            'Verified' => 'bg-label-success',
            'Rejected' => 'bg-label-danger',
        ][$audit->status_submit] ?? 'bg-label-secondary';
    @endphp

    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Verifikasi Audit Tools / Edit /</span> {{ $audit->no_audit }}
        <span class="badge {{ $headerBadge }} align-middle">{{ $audit->status_submit }}</span>
    </h4>
    <p class="text-muted mb-4">
        Teknisi: <strong>{{ $audit->technician->name ?? '-' }}</strong> —
        Periode {{ $audit->period->tahun }} Semester {{ $audit->period->semester }}
        <br><span class="text-warning">Admin sedang mengubah data self-audit yang disubmit teknisi.</span>
    </p>

    <a href="{{ route('tool-audit-verification.show', $audit->id) }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="mdi mdi-arrow-left"></i> Kembali
    </a>

    <form action="{{ route('tool-audit-verification.update', $audit->id) }}" method="post" enctype="multipart/form-data">
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
                            <div class="text-muted small mb-2">Qty terdaftar: {{ $tool->qty ?? '-' }}</div>

                            <div class="form-floating form-floating-outline mb-2">
                                <input type="number" class="form-control" name="items[{{ $item->id }}][qty_actual]"
                                    value="{{ old("items.{$item->id}.qty_actual", $item->qty_actual) }}" min="0" required>
                                <label>Qty Sekarang</label>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            @if ($item->foto_audit)
                                <img src="{{ asset($item->foto_audit) }}" alt="foto audit"
                                    style="width:100%;max-width:100px;aspect-ratio:1/1;object-fit:cover;border-radius:6px;">
                                <div class="small text-muted mt-1">Foto Audit saat ini</div>
                            @else
                                <div class="text-muted small">Belum ada foto</div>
                            @endif
                            <input type="file" class="form-control form-control-sm mt-2" accept="image/*"
                                name="items[{{ $item->id }}][foto_audit]">
                            <div class="small text-muted mt-1">Kosongkan kalau tidak ganti foto.</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-warning mb-4">
            <i class="mdi mdi-content-save"></i> Simpan Perubahan
        </button>
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
    </script>
@endpush
