@php
    $department = $department ?? null;
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
        <label class="form-label">Nama Departemen <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name', $department?->name) }}" class="form-control" required>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Kode</label>
        <input type="text" name="code" value="{{ old('code', $department?->code) }}" class="form-control" maxlength="20" placeholder="Contoh: FIN, SLS">
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Induk Departemen (opsional)</label>
        <select name="parent_id" class="form-select">
            <option value="">- Tidak ada -</option>
            @foreach ($parentOptions as $option)
                <option value="{{ $option->id }}" @selected(old('parent_id', $department?->parent_id) == $option->id)>
                    {{ $option->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label d-block">Status</label>
        <div class="form-check form-switch mt-2">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                @checked(old('is_active', $department?->is_active ?? true))>
            <label class="form-check-label">Aktif</label>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="mdi mdi-content-save-outline me-1"></i> Simpan
    </button>
    <a href="{{ route('departments.index') }}" class="btn btn-label-secondary">Batal</a>
</div>
