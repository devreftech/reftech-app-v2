@extends('layouts.sales.app')
@section('title', 'Quick Setup Forecast Mesin')

@push('style')
<style>
    /* Styling for unified tab controls */
    .sales-tab-nav {
        border-radius: 12px;
        background: #f8fafc;
        padding: 0.5rem;
        border: 1px solid #e2e8f0;
    }
    .sales-tab-nav .nav-link {
        border-radius: 8px;
        font-weight: 600;
        color: #64748b;
        padding: 0.6rem 1.2rem;
        transition: all 0.2s ease;
    }
    .sales-tab-nav .nav-link.active {
        background-color: #6366f1 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }
    .sales-tab-nav .nav-link:hover:not(.active) {
        background-color: #f1f5f9;
        color: #334155;
    }
    
    /* Table styling */
    .datatable-card {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(229, 231, 235, 0.7);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01);
    }
    .table-setup-manual th {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        background-color: #f8fafc !important;
        color: #475569 !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 1rem !important;
    }
    .table-setup-manual td {
        padding: 0.8rem 1rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: middle !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Forecast /</span> Quick Setup Forecast Mesin</h4>
            <p class="text-muted mb-0">Atur jadwal rencana servis dan jenis PM unit Air Compressor per sales rep.</p>
        </div>
        @if(in_array(Auth::user()->role, ['Admin', 'Sales Manager']))
        <div class="d-flex gap-2">
            <!-- Generate Current Year -->
            <form action="{{ route('forecast.generate-default') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melakukan generate otomatis jadwal forecast untuk tahun ini ({{ date('Y') }})? Ini akan menyusun default kueri PM1-PM1-PM2 secara otomatis pada semua mesin kompresor yang aktif.')">
                @csrf
                <input type="hidden" name="year" value="{{ date('Y') }}">
                <button type="submit" class="btn btn-outline-primary px-3" style="border-radius: 10px; font-weight: 600;">
                    <i class="mdi mdi-creation me-1"></i> Generate Forecast {{ date('Y') }}
                </button>
            </form>

            <!-- Generate Next Year -->
            <form action="{{ route('forecast.generate-default') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melakukan generate otomatis jadwal forecast untuk tahun depan ({{ date('Y') + 1 }})? Ini akan menyusun default kueri PM1-PM1-PM2 secara otomatis pada semua mesin kompresor yang aktif.')">
                @csrf
                <input type="hidden" name="year" value="{{ date('Y') + 1 }}">
                <button type="submit" class="btn btn-outline-success px-3" style="border-radius: 10px; font-weight: 600;">
                    <i class="mdi mdi-creation me-1"></i> Generate Forecast {{ date('Y') + 1 }}
                </button>
            </form>
        </div>
        @endif
    </div>

    @if(session('message'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        <i class="mdi mdi-alert-circle-outline me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ route('forecast.setup.store') }}" method="POST" id="formSetupForecast">
        @csrf
        
        <!-- Header Save Bar -->
        <div class="card mb-4 bg-light border-0 shadow-none">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <span class="fw-semibold text-secondary">
                    <i class="mdi mdi-alert-circle-outline me-1"></i> 
                    Catatan: Halaman ini dioptimalkan dengan filter pencarian ringan untuk performa load instan.
                </span>
                <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px; font-weight: 600;">
                    <i class="mdi mdi-content-save-outline me-1"></i> Simpan Semua Pengaturan
                </button>
            </div>
        </div>

        <!-- Sales Navigation Tabs -->
        <ul class="nav nav-pills mb-4 sales-tab-nav d-flex gap-2" role="tablist">
            @foreach($salesUsers as $index => $user)
            <li class="nav-item">
                <button type="button" class="nav-link {{ $index == 0 ? 'active' : '' }}" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sales-{{ $user->id }}">
                    <i class="mdi mdi-account-tie me-1"></i> {{ $user->name }}
                </button>
            </li>
            @endforeach
        </ul>

        <!-- Tab Content -->
        <div class="tab-content p-0" style="background: transparent; border: none; box-shadow: none;">
            @foreach($salesUsers as $index => $user)
            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="tab-sales-{{ $user->id }}" role="tabpanel">
                <div class="card datatable-card border-0 shadow-sm">
                    <div class="card-header pb-2 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <h5 class="card-title fw-bold mb-0" style="color: #374151;">Populasi Unit - {{ $user->name }}</h5>
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-magnify text-muted fs-4"></i>
                            <input type="text" class="form-control form-control-sm search-machine-setup" placeholder="Cari client, model, serial..." style="width: 260px; border-radius: 8px; font-weight: 500;">
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover table-bordered align-middle m-0 table-setup-manual" style="font-size: 0.85rem;">
                            <thead>
                                <tr>
                                    <th>Customer / Detail Mesin</th>
                                    <th>Status & Tipe</th>
                                    <th>Visit 1 (PM & Tanggal)</th>
                                    <th>Visit 2 (PM & Tanggal)</th>
                                    <th>Visit 3 (PM & Tanggal)</th>
                                    <th>Visit 4 (PM & Tanggal - Opsional)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $userMachines = $groupedMachines->get($user->id, collect());
                                    $userClients = $userMachines->groupBy('id_client');
                                @endphp
                                @forelse($userClients as $clientId => $clientMachines)
                                @php
                                    $firstMachine = $clientMachines->first();
                                    $client = $firstMachine->client;
                                    
                                    // Calculate total forecast nominal for this client in the current calendar year
                                    $clientTotalNominal = 0;
                                    foreach ($clientMachines as $m) {
                                        $clientTotalNominal += $m->getForecastNominal(date('Y'));
                                    }
                                @endphp
                                <tr class="client-row" data-client-id="{{ $clientId }}">
                                    <td>
                                        <a href="{{ route('existing.show', $clientId) }}" class="fw-bold text-primary d-block" style="font-size: 0.95rem;" target="_blank">
                                            {{ $client->company ?? 'No Company' }}
                                        </a>
                                        <div class="mt-1">
                                            <span class="badge bg-label-info fw-bold py-1 px-2" style="font-size: 0.75rem;">
                                                Total Forecast {{ date('Y') }}: Rp {{ number_format($clientTotalNominal, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        @if($clientMachines->count() > 1)
                                        <div class="mt-2">
                                            <label class="form-label mb-1 text-muted fw-bold" style="font-size: 0.75rem; color: #4b5563 !important;">Pilih Unit ({{ $clientMachines->count() }}):</label>
                                            <select class="form-select form-select-sm select-machine-switcher" onchange="switchMachineInputs('{{ $clientId }}', this.value)" style="border-radius: 6px; font-weight: 600; background-color: #f1f5f9;">
                                                @foreach($clientMachines as $m)
                                                    <option value="{{ $m->id }}">
                                                        {{ $m->unit->brand ?? '-' }} {{ $m->unit->unit->model ?? '-' }} (S/N: {{ $m->serial ?: '-' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @else
                                        <small class="text-secondary font-mono d-block mt-2">
                                            <strong>{{ $firstMachine->unit->brand ?? '-' }} {{ $firstMachine->unit->unit->model ?? '-' }}</strong>
                                            <br>S/N: {{ $firstMachine->serial ?: '-' }} | kW: {{ $firstMachine->unit->unit->power ?? '-' }}
                                        </small>
                                        @endif
                                    </td>

                                    <!-- Status & Tipe -->
                                    <td>
                                        @foreach($clientMachines as $mIdx => $m)
                                        <div class="machine-wrapper-{{ $clientId }} machine-inputs-{{ $m->id }}" style="{{ $mIdx == 0 ? '' : 'display: none;' }}">
                                            <select class="form-select form-select-sm mb-1" name="machines[{{ $m->id }}][is_forecasted]" style="width: 135px; border-radius: 6px;">
                                                <option value="1" {{ $m->is_forecasted ? 'selected' : '' }}>Forecast Aktif</option>
                                                <option value="0" {{ !$m->is_forecasted ? 'selected' : '' }}>Non-Aktif</option>
                                            </select>
                                            <select class="form-select form-select-sm" name="machines[{{ $m->id }}][forecast_type]" style="width: 135px; border-radius: 6px;">
                                                <option value="parts" {{ $m->forecast_type == 'parts' ? 'selected' : '' }}>Parts Only</option>
                                                <option value="regular_service" {{ $m->forecast_type == 'regular_service' ? 'selected' : '' }}>Regular Service</option>
                                                <option value="contract" {{ $m->forecast_type == 'contract' ? 'selected' : '' }}>Service Contract</option>
                                            </select>
                                            <input type="hidden" name="machines[{{ $m->id }}][last_service_date]" value="{{ $m->last_service_date }}">
                                        </div>
                                        @endforeach
                                    </td>

                                    <!-- Visit 1 -->
                                    <td>
                                        @foreach($clientMachines as $mIdx => $m)
                                        <div class="machine-wrapper-{{ $clientId }} machine-inputs-{{ $m->id }}" style="{{ $mIdx == 0 ? '' : 'display: none;' }}">
                                            <select class="form-select form-select-sm mb-1" name="machines[{{ $m->id }}][visit_1_type]" style="width: 130px; border-radius: 6px;">
                                                <option value="" {{ is_null($m->visit_1_type) ? 'selected' : '' }}>-- Jenis PM --</option>
                                                <option value="PM1" {{ $m->visit_1_type == 'PM1' ? 'selected' : '' }}>PM1 (Minor)</option>
                                                <option value="PM2" {{ $m->visit_1_type == 'PM2' ? 'selected' : '' }}>PM2 (Major)</option>
                                            </select>
                                            <input type="date" class="form-control form-control-sm" 
                                                   name="machines[{{ $m->id }}][visit_1_date]" 
                                                   value="{{ $m->visit_1_date ? \Carbon\Carbon::parse($m->visit_1_date)->format('Y-m-d') : '' }}"
                                                   style="width: 130px; border-radius: 6px;">
                                            @if($m->visit_1_date)
                                                @php
                                                    $v1Month = \Carbon\Carbon::parse($m->visit_1_date)->format('Y-m');
                                                    $hasV1Po = isset($clientWonMonths[$clientId][$v1Month]);
                                                @endphp
                                                @if($hasV1Po)
                                                    <span class="badge bg-label-success mt-1 d-block text-center" style="font-size: 0.7rem; padding: 0.25em 0.5em;"><i class="mdi mdi-check-decagram me-1"></i> PO Realized</span>
                                                @else
                                                    <span class="badge bg-label-secondary mt-1 d-block text-center text-muted" style="font-size: 0.7rem; padding: 0.25em 0.5em; background-color: #f1f5f9;"><i class="mdi mdi-clock-outline me-1"></i> Belum PO</span>
                                                @endif
                                            @endif
                                        </div>
                                        @endforeach
                                    </td>

                                    <!-- Visit 2 -->
                                    <td>
                                        @foreach($clientMachines as $mIdx => $m)
                                        <div class="machine-wrapper-{{ $clientId }} machine-inputs-{{ $m->id }}" style="{{ $mIdx == 0 ? '' : 'display: none;' }}">
                                            <select class="form-select form-select-sm mb-1" name="machines[{{ $m->id }}][visit_2_type]" style="width: 130px; border-radius: 6px;">
                                                <option value="" {{ is_null($m->visit_2_type) ? 'selected' : '' }}>-- Jenis PM --</option>
                                                <option value="PM1" {{ $m->visit_2_type == 'PM1' ? 'selected' : '' }}>PM1 (Minor)</option>
                                                <option value="PM2" {{ $m->visit_2_type == 'PM2' ? 'selected' : '' }}>PM2 (Major)</option>
                                            </select>
                                            <input type="date" class="form-control form-control-sm" 
                                                   name="machines[{{ $m->id }}][visit_2_date]" 
                                                   value="{{ $m->visit_2_date ? \Carbon\Carbon::parse($m->visit_2_date)->format('Y-m-d') : '' }}"
                                                   style="width: 130px; border-radius: 6px;">
                                            @if($m->visit_2_date)
                                                @php
                                                    $v2Month = \Carbon\Carbon::parse($m->visit_2_date)->format('Y-m');
                                                    $hasV2Po = isset($clientWonMonths[$clientId][$v2Month]);
                                                @endphp
                                                @if($hasV2Po)
                                                    <span class="badge bg-label-success mt-1 d-block text-center" style="font-size: 0.7rem; padding: 0.25em 0.5em;"><i class="mdi mdi-check-decagram me-1"></i> PO Realized</span>
                                                @else
                                                    <span class="badge bg-label-secondary mt-1 d-block text-center text-muted" style="font-size: 0.7rem; padding: 0.25em 0.5em; background-color: #f1f5f9;"><i class="mdi mdi-clock-outline me-1"></i> Belum PO</span>
                                                @endif
                                            @endif
                                        </div>
                                        @endforeach
                                    </td>

                                    <!-- Visit 3 -->
                                    <td>
                                        @foreach($clientMachines as $mIdx => $m)
                                        <div class="machine-wrapper-{{ $clientId }} machine-inputs-{{ $m->id }}" style="{{ $mIdx == 0 ? '' : 'display: none;' }}">
                                            <select class="form-select form-select-sm mb-1" name="machines[{{ $m->id }}][visit_3_type]" style="width: 130px; border-radius: 6px;">
                                                <option value="" {{ is_null($m->visit_3_type) ? 'selected' : '' }}>-- Jenis PM --</option>
                                                <option value="PM1" {{ $m->visit_3_type == 'PM1' ? 'selected' : '' }}>PM1 (Minor)</option>
                                                <option value="PM2" {{ $m->visit_3_type == 'PM2' ? 'selected' : '' }}>PM2 (Major)</option>
                                            </select>
                                            <input type="date" class="form-control form-control-sm" 
                                                   name="machines[{{ $m->id }}][visit_3_date]" 
                                                   value="{{ $m->visit_3_date ? \Carbon\Carbon::parse($m->visit_3_date)->format('Y-m-d') : '' }}"
                                                   style="width: 130px; border-radius: 6px;">
                                            @if($m->visit_3_date)
                                                @php
                                                    $v3Month = \Carbon\Carbon::parse($m->visit_3_date)->format('Y-m');
                                                    $hasV3Po = isset($clientWonMonths[$clientId][$v3Month]);
                                                @endphp
                                                @if($hasV3Po)
                                                    <span class="badge bg-label-success mt-1 d-block text-center" style="font-size: 0.7rem; padding: 0.25em 0.5em;"><i class="mdi mdi-check-decagram me-1"></i> PO Realized</span>
                                                @else
                                                    <span class="badge bg-label-secondary mt-1 d-block text-center text-muted" style="font-size: 0.7rem; padding: 0.25em 0.5em; background-color: #f1f5f9;"><i class="mdi mdi-clock-outline me-1"></i> Belum PO</span>
                                                @endif
                                            @endif
                                        </div>
                                        @endforeach
                                    </td>

                                    <!-- Visit 4 -->
                                    <td>
                                        @foreach($clientMachines as $mIdx => $m)
                                        <div class="machine-wrapper-{{ $clientId }} machine-inputs-{{ $m->id }}" style="{{ $mIdx == 0 ? '' : 'display: none;' }}">
                                            <select class="form-select form-select-sm mb-1" name="machines[{{ $m->id }}][visit_4_type]" style="width: 130px; border-radius: 6px;">
                                                <option value="" {{ is_null($m->visit_4_type) ? 'selected' : '' }}>-- Jenis PM --</option>
                                                <option value="PM1" {{ $m->visit_4_type == 'PM1' ? 'selected' : '' }}>PM1 (Minor)</option>
                                                <option value="PM2" {{ $m->visit_4_type == 'PM2' ? 'selected' : '' }}>PM2 (Major)</option>
                                            </select>
                                            <input type="date" class="form-control form-control-sm" 
                                                   name="machines[{{ $m->id }}][visit_4_date]" 
                                                   value="{{ $m->visit_4_date ? \Carbon\Carbon::parse($m->visit_4_date)->format('Y-m-d') : '' }}"
                                                   style="width: 130px; border-radius: 6px;">
                                            @if($m->visit_4_date)
                                                @php
                                                    $v4Month = \Carbon\Carbon::parse($m->visit_4_date)->format('Y-m');
                                                    $hasV4Po = isset($clientWonMonths[$clientId][$v4Month]);
                                                @endphp
                                                @if($hasV4Po)
                                                    <span class="badge bg-label-success mt-1 d-block text-center" style="font-size: 0.7rem; padding: 0.25em 0.5em;"><i class="mdi mdi-check-decagram me-1"></i> PO Realized</span>
                                                @else
                                                    <span class="badge bg-label-secondary mt-1 d-block text-center text-muted" style="font-size: 0.7rem; padding: 0.25em 0.5em; background-color: #f1f5f9;"><i class="mdi mdi-clock-outline me-1"></i> Belum PO</span>
                                                @endif
                                            @endif
                                        </div>
                                        @endforeach
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada unit terdaftar untuk sales ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Bottom Save Bar -->
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow" style="border-radius: 12px; font-weight: 600;">
                <i class="mdi mdi-content-save-outline me-1"></i> Simpan Semua Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection

@push('script')
<script>
    // Global function to toggle machine setting input columns for multi-unit clients
    function switchMachineInputs(clientId, machineId) {
        $('.machine-wrapper-' + clientId).hide();
        $('.machine-inputs-' + machineId).show();
    }

    $(document).ready(function() {
        // Client-side lightweight search for each tab's table
        $('.search-machine-setup').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            var tableBody = $(this).closest('.card').find('table tbody');
            
            tableBody.find('tr').each(function() {
                // Skip empty placeholder row if present
                if ($(this).hasClass('no-result-row')) {
                    return;
                }
                
                var text = $(this).text().toLowerCase();
                if (text.indexOf(value) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            
            // Handle "no results" row dynamically
            var visibleRows = tableBody.find('tr:not(.no-result-row):visible').length;
            var noResultRow = tableBody.find('.no-result-row');
            
            if (visibleRows === 0) {
                if (noResultRow.length === 0) {
                    tableBody.append(`
                        <tr class="no-result-row">
                            <td colspan="6" class="text-center text-muted py-4 no-result-row">Data mesin tidak ditemukan untuk pencarian "${$(this).val()}"</td>
                        </tr>
                    `);
                }
            } else {
                tableBody.find('.no-result-row').remove();
            }
        });
    });
</script>
@endpush
