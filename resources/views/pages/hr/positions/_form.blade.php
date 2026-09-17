@php
    $position = $position ?? null;
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label">Nama Posisi <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name', $position?->name) }}" class="form-control" required>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Departemen (opsional)</label>
        <select name="id_department" class="form-select">
            <option value="">- Belum ditentukan -</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}" @selected(old('id_department', $position?->id_department) == $department->id)>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Level <span class="text-danger">*</span></label>
        <input type="number" name="level" value="{{ old('level', $position?->level ?? 1) }}" class="form-control" min="1" max="20" required>
        <div class="form-text">Dipakai untuk approval berjenjang — makin tinggi angkanya, makin tinggi wewenangnya.</div>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label d-block">Status</label>
        <div class="form-check form-switch mt-2">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                @checked(old('is_active', $position?->is_active ?? true))>
            <label class="form-check-label">Aktif</label>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="mdi mdi-content-save-outline me-1"></i> Simpan
    </button>
    <a href="{{ route('positions.index') }}" class="btn btn-label-secondary">Batal</a>
</div>
