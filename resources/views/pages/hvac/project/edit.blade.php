@extends('layouts.sales.app')
@section('title', 'Edit Proyek HVAC - ' . $project->project_code)

@push('after-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d9dee3;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        color: #495057;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-dropdown {
        border: 1px solid #d9dee3;
        border-radius: 0.375rem;
        box-shadow: 0 4px 16px rgba(0,0,0,.12);
        z-index: 1060;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #d9dee3;
        border-radius: 0.25rem;
        padding: 6px 10px;
    }
    .select2-results__option {
        padding: 8px 12px;
    }
    .select2-container--default .select2-results__option--highlighted {
        background-color: #696cff;
        color: #fff;
    }
</style>
@endpush

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <a href="{{ route('hvac.project.show', $project->id) }}" class="text-muted"><i class="mdi mdi-arrow-left"></i></a>
                Edit Proyek: {{ $project->project_code }}
            </h4>
            <p class="text-muted mb-0">Ubah detail header proyek dan parameter lingkungan desain.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terdapat kesalahan input:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold"><i class="mdi mdi-folder-edit-outline text-primary me-2"></i>Edit Informasi Proyek</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('hvac.project.update', $project->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Kode Proyek</label>
                                <input type="text" class="form-control bg-light" value="{{ $project->project_code }}" readonly>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Nama Proyek / Gedung <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="project_name" value="{{ old('project_name', $project->project_name) }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Status Proyek</label>
                                <select name="status" class="form-select">
                                    <option value="draft" {{ $project->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="calculated" {{ $project->status == 'calculated' ? 'selected' : '' }}>Calculated</option>
                                    <option value="approved" {{ $project->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="quoted" {{ $project->status == 'quoted' ? 'selected' : '' }}>Quoted</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Nama Klien / Customer <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="mdi mdi-office-building-outline"></i></span>
                                    <input type="text" class="form-control" name="customer_name" id="customer_name" value="{{ old('customer_name', $project->customer_name ?: $project->client?->company) }}" required>
                                </div>
                                <small class="text-muted">Nama perusahaan atau nama klien / customer proyek.</small>
                                @if($project->id_client)
                                    <input type="hidden" name="id_client" value="{{ $project->id_client }}">
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Sales In-Charge</label>
                                <select name="id_sales" class="form-select">
                                    <option value="">-- Pilih Sales --</option>
                                    @foreach($salesList as $s)
                                        <option value="{{ $s->id }}" {{ old('id_sales', $project->id_sales) == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }} ({{ $s->role }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Lokasi / Site Proyek</label>
                                <input type="text" class="form-control" name="location" value="{{ old('location', $project->location) }}">
                            </div>

                            <div class="col-12"><hr class="my-2"></div>

                            <div class="col-12">
                                <h6 class="fw-bold text-dark mb-1"><i class="mdi mdi-weather-partly-cloudy text-info me-2"></i>Kondisi Desain Udara Luar (Ambient Outdoor)</h6>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Preset Kota Referensi</label>
                                <select id="cityPreset" class="form-select">
                                    <option value="">-- Pilih Preset Kota --</option>
                                    @foreach($cities as $ct)
                                        <option value="{{ $ct->id }}" data-temp="{{ $ct->outdoor_db_temp }}" data-rh="{{ $ct->outdoor_rh_percent }}">
                                            {{ $ct->city_name }} ({{ $ct->outdoor_db_temp }}°C / {{ $ct->outdoor_rh_percent }}% RH)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Outdoor Dry Bulb Temp (°C) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.1" name="design_outdoor_temp" id="design_outdoor_temp" class="form-control" value="{{ old('design_outdoor_temp', $project->design_outdoor_temp) }}" required>
                                    <span class="input-group-text">°C</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Outdoor Relative Humidity (%) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.1" name="design_outdoor_rh" id="design_outdoor_rh" class="form-control" value="{{ old('design_outdoor_rh', $project->design_outdoor_rh) }}" required>
                                    <span class="input-group-text">% RH</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan / Scope Pekerjaan</label>
                                <textarea name="notes" rows="3" class="form-control">{{ old('notes', $project->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('hvac.project.show', $project->id) }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="mdi mdi-check me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('after-script')
    <script>
        document.getElementById('cityPreset')?.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected.value) {
                document.getElementById('design_outdoor_temp').value = selected.dataset.temp;
                document.getElementById('design_outdoor_rh').value = selected.dataset.rh;
            }
        });
    </script>
    @endpush
@endsection
