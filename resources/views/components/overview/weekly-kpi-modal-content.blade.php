@php
    $category = $detail['category'] ?? 'empty';
    $items = $detail['items'] ?? collect([]);
    $totalCount = $detail['total_count'] ?? count($items);
    $totalNominal = $detail['total_nominal'] ?? 0;
    $weekLabel = ($week && (int)$week >= 1 && (int)$week <= 5) ? 'Minggu ' . $week . ' (W' . $week . ')' : 'Semua Minggu (Total Bulan)';
@endphp

<!-- Header Information Banner -->
<div class="p-3 mb-3 rounded-3 bg-lighter border d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <span class="badge bg-primary px-2.5 py-1">
                <i class="mdi mdi-calendar-range me-1"></i>{{ $weekLabel }}
            </span>
            <span class="badge bg-label-secondary px-2.5 py-1">
                <i class="mdi mdi-calendar-month-outline me-1"></i>{{ $monthLabel }}
            </span>
            @if(isset($user))
                <span class="badge bg-label-info px-2.5 py-1">
                    <i class="mdi mdi-account-tie-outline me-1"></i>{{ $user->name }}
                </span>
            @endif
        </div>
        <h5 class="fw-bold text-dark mb-0">{{ $rowName }}</h5>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="text-end">
            <small class="text-muted text-uppercase fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.05em;">Jumlah Data</small>
            <span class="h5 mb-0 fw-bold text-primary">{{ $totalCount }}</span>
        </div>
        @if(in_array($category, ['quotation', 'po']))
            <div class="border-start ps-3 text-end">
                <small class="text-muted text-uppercase fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.05em;">Total Nominal</small>
                <span class="h5 mb-0 fw-bold text-success">Rp {{ number_format($totalNominal, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>
</div>

@if($totalCount === 0)
    <div class="text-center py-5">
        <div class="avatar avatar-lg bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
            <i class="mdi mdi-folder-open-outline fs-2 text-muted"></i>
        </div>
        <h6 class="fw-semibold text-dark mb-1">Tidak Ada Data Rincian</h6>
        <p class="text-muted small mb-0">Tidak ditemukan rincian data untuk kategori <strong>{{ $rowName }}</strong> pada {{ $weekLabel }}.</p>
    </div>
@else
    <div class="table-responsive" style="max-height: 480px;">
        <table class="table table-hover table-striped align-middle border mb-0" style="font-size: 0.86rem;">
            <thead class="table-light sticky-top" style="z-index: 2;">
                @if($category === 'quotation')
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th style="width: 170px;">No. Quotation</th>
                        <th style="width: 100px;" class="text-center">Tipe</th>
                        <th>Perusahaan / Customer</th>
                        <th style="width: 110px;" class="text-center">Tanggal</th>
                        <th style="width: 150px;" class="text-end">Nominal</th>
                        <th style="width: 110px;" class="text-center">Status</th>
                        <th style="width: 70px;" class="text-center">Aksi</th>
                    </tr>
                @elseif($category === 'po')
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th style="width: 160px;">No. Quotation</th>
                        <th style="width: 150px;">No. PO</th>
                        <th style="width: 95px;" class="text-center">Tipe</th>
                        <th>Perusahaan / Customer</th>
                        <th style="width: 110px;" class="text-center">Tgl PO</th>
                        <th style="width: 140px;" class="text-end">Nominal</th>
                        <th style="width: 100px;" class="text-center">Status</th>
                        <th style="width: 70px;" class="text-center">Aksi</th>
                    </tr>
                @elseif($category === 'activity')
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th style="width: 240px;">Perusahaan / Customer</th>
                        <th style="width: 130px;" class="text-center">Aktivitas</th>
                        <th style="width: 110px;" class="text-center">Tanggal</th>
                        <th style="width: 100px;" class="text-center">Status</th>
                        <th>Catatan / Hasil</th>
                        <th style="width: 70px;" class="text-center">Aksi</th>
                    </tr>
                @elseif($category === 'client')
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th style="width: 260px;">Perusahaan / Client</th>
                        <th>Area / Alamat</th>
                        <th style="width: 150px;">Telepon / PIC</th>
                        <th style="width: 110px;" class="text-center">Tgl Dibuat</th>
                        <th style="width: 110px;" class="text-center">Sumber</th>
                        <th style="width: 70px;" class="text-center">Aksi</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @foreach($items as $idx => $item)
                    @if($category === 'quotation')
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $idx + 1 }}</td>
                            <td>
                                <a href="{{ $item['url'] }}" target="_blank" class="fw-bold text-primary text-decoration-none">
                                    {{ $item['no_quote'] }}
                                </a>
                                @if(!empty($item['title']) && $item['title'] !== '-')
                                    <div class="text-muted small text-truncate" style="max-width: 200px;" title="{{ $item['title'] }}">
                                        {{ $item['title'] }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $typeVal = $item['type'] ?? 'Parts';
                                    $typeBadge = 'bg-label-secondary';
                                    if ($typeVal === 'Unit') $typeBadge = 'bg-label-primary';
                                    elseif ($typeVal === 'Parts') $typeBadge = 'bg-label-warning';
                                    elseif ($typeVal === 'Project' || $typeVal === 'Piping') $typeBadge = 'bg-label-info';
                                    elseif ($typeVal === 'Service' || $typeVal === 'Air Audit') $typeBadge = 'bg-label-success';
                                    elseif ($typeVal === 'Rental') $typeBadge = 'bg-label-dark';
                                @endphp
                                <span class="badge {{ $typeBadge }} rounded-pill px-2.5 py-0.5 fw-semibold">{{ $typeVal }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $item['company'] }}</span>
                            </td>
                            <td class="text-center text-nowrap text-muted">{{ $item['date'] }}</td>
                            <td class="text-end fw-bold text-dark text-nowrap">
                                Rp {{ number_format($item['nominal'], 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @php
                                    $st = strtolower($item['status']);
                                    $badgeClass = 'bg-label-primary';
                                    if (str_contains($st, 'po') || str_contains($st, 'deal') || str_contains($st, 'done')) {
                                        $badgeClass = 'bg-label-success';
                                    } elseif (str_contains($st, 'loss') || str_contains($st, 'reject') || str_contains($st, 'cancel')) {
                                        $badgeClass = 'bg-label-danger';
                                    } elseif (str_contains($st, 'process') || str_contains($st, 'draft') || str_contains($st, 'pending')) {
                                        $badgeClass = 'bg-label-warning';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }} rounded-pill px-2 py-1">{{ $item['status'] }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ $item['url'] }}" target="_blank" class="btn btn-icon btn-xs btn-label-primary rounded-circle" title="Lihat Quotation">
                                    <i class="mdi mdi-open-in-new"></i>
                                </a>
                            </td>
                        </tr>
                    @elseif($category === 'po')
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $idx + 1 }}</td>
                            <td>
                                <a href="{{ $item['url'] }}" target="_blank" class="fw-bold text-primary text-decoration-none">
                                    {{ $item['no_quote'] }}
                                </a>
                            </td>
                            <td class="fw-semibold text-dark">{{ $item['no_po'] }}</td>
                            <td class="text-center">
                                @php
                                    $typeVal = $item['type'] ?? 'Parts';
                                    $typeBadge = 'bg-label-secondary';
                                    if ($typeVal === 'Unit') $typeBadge = 'bg-label-primary';
                                    elseif ($typeVal === 'Parts') $typeBadge = 'bg-label-warning';
                                    elseif ($typeVal === 'Project' || $typeVal === 'Piping') $typeBadge = 'bg-label-info';
                                    elseif ($typeVal === 'Service' || $typeVal === 'Air Audit') $typeBadge = 'bg-label-success';
                                    elseif ($typeVal === 'Rental') $typeBadge = 'bg-label-dark';
                                @endphp
                                <span class="badge {{ $typeBadge }} rounded-pill px-2.5 py-0.5 fw-semibold">{{ $typeVal }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $item['company'] }}</span>
                                @if(!empty($item['title']) && $item['title'] !== '-')
                                    <div class="text-muted small text-truncate" style="max-width: 220px;" title="{{ $item['title'] }}">
                                        {{ $item['title'] }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center text-nowrap text-muted">{{ $item['date'] }}</td>
                            <td class="text-end fw-bold text-success text-nowrap">
                                Rp {{ number_format($item['nominal'], 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-success rounded-pill px-2 py-1">{{ $item['status'] }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ $item['url'] }}" target="_blank" class="btn btn-icon btn-xs btn-label-success rounded-circle" title="Lihat PO">
                                    <i class="mdi mdi-open-in-new"></i>
                                </a>
                            </td>
                        </tr>
                    @elseif($category === 'activity')
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $idx + 1 }}</td>
                            <td>
                                <a href="{{ $item['client_url'] }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                                    {{ $item['company'] }}
                                </a>
                            </td>
                            <td class="text-center">
                                @php
                                    $actClass = 'bg-label-primary';
                                    if ($item['name'] === 'Daily Call') $actClass = 'bg-label-info';
                                    elseif ($item['name'] === 'Visit') $actClass = 'bg-label-warning';
                                    elseif (str_contains(strtolower($item['name']), 'crm')) $actClass = 'bg-label-success';
                                @endphp
                                <span class="badge {{ $actClass }} rounded-pill px-2 py-0.5">{{ $item['name'] }}</span>
                            </td>
                            <td class="text-center text-nowrap text-muted">{{ $item['date'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-label-success rounded-pill px-2 py-0.5">{{ $item['status'] }}</span>
                            </td>
                            <td>
                                <div class="text-muted small" style="max-width: 320px; line-height: 1.35;">
                                    {{ $item['note'] }}
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="{{ $item['client_url'] }}" target="_blank" class="btn btn-icon btn-xs btn-label-primary rounded-circle" title="Lihat Client">
                                    <i class="mdi mdi-open-in-new"></i>
                                </a>
                            </td>
                        </tr>
                    @elseif($category === 'client')
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $idx + 1 }}</td>
                            <td>
                                <a href="{{ $item['url'] }}" target="_blank" class="fw-bold text-primary text-decoration-none">
                                    {{ $item['company'] }}
                                </a>
                            </td>
                            <td class="text-muted">{{ $item['address'] }}</td>
                            <td class="text-muted">{{ $item['phone'] }}</td>
                            <td class="text-center text-nowrap text-muted">{{ $item['date'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-label-primary rounded-pill px-2 py-0.5">{{ $item['source'] }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ $item['url'] }}" target="_blank" class="btn btn-icon btn-xs btn-label-primary rounded-circle" title="Lihat Leads">
                                    <i class="mdi mdi-open-in-new"></i>
                                </a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@endif
