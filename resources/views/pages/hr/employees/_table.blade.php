@php
    $statusColors = [
        'Tetap' => 'success',
        'Kontrak' => 'info',
        'Probation' => 'warning',
        'Resign' => 'secondary',
        'Perlu Verifikasi' => 'danger',
    ];
@endphp

<div class="card-header border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div class="d-flex align-items-center gap-2">
        <i class="mdi mdi-table text-primary fs-5"></i>
        <h5 class="card-title mb-0 fw-bold">Daftar Karyawan</h5>
        <span class="badge bg-label-secondary rounded-pill font-monospace ms-1" id="tableTotalBadge">
            {{ $employees->total() }} Data
        </span>
    </div>

    <div class="d-flex align-items-center gap-2">
        <div id="bulkActionEmployees" class="d-none align-items-center gap-2 flex-wrap">
            <span class="badge bg-label-primary font-monospace" id="selectedEmployeesCount">0 Terpilih</span>

            {{-- Dropdown Ubah Status Kepegawaian --}}
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle shadow-xs" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-account-switch-outline me-1"></i> Pindah Status
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><h6 class="dropdown-header text-uppercase small">Pilih Status Kepegawaian</h6></li>
                    <li>
                        <a class="dropdown-item btn-bulk-status d-flex align-items-center" href="javascript:void(0);" data-status="Tetap">
                            <span class="status-dot bg-success me-2"></span> Jadikan Karyawan Tetap
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item btn-bulk-status d-flex align-items-center" href="javascript:void(0);" data-status="Kontrak">
                            <span class="status-dot bg-info me-2"></span> Jadikan Karyawan Kontrak
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item btn-bulk-status d-flex align-items-center" href="javascript:void(0);" data-status="Probation">
                            <span class="status-dot bg-warning me-2"></span> Jadikan Masa Probation
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item btn-bulk-status text-danger d-flex align-items-center" href="javascript:void(0);" data-status="Resign">
                            <i class="mdi mdi-account-remove-outline me-2 text-danger"></i> Tandai Resign (Berhenti)
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Tombol Hapus Terpilih --}}
            <button type="button" class="btn btn-sm btn-label-danger shadow-xs" id="btnBulkDeleteEmployees">
                <i class="mdi mdi-trash-can-outline me-1"></i> Hapus
            </button>
        </div>

        @if (request()->hasAny(['search', 'id_department', 'employment_status']))
            <div class="d-flex align-items-center gap-2 small">
                <span class="text-muted"><i class="mdi mdi-information-outline me-1"></i>Filter diterapkan</span>
                <button type="button" class="btn btn-xs btn-label-danger rounded-pill btn-reset-ajax-filter">
                    <i class="mdi mdi-close me-1"></i>Reset
                </button>
            </div>
        @endif
    </div>
</div>

<div class="table-responsive text-nowrap">
    <table class="table employee-table mb-0">
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">
                    <input type="checkbox" class="form-check-input" id="checkAllEmployees" title="Pilih Semua">
                </th>
                <th style="min-width: 240px;">Karyawan</th>
                <th style="min-width: 140px;">NIK & Kontak</th>
                <th style="min-width: 180px;">Posisi & Departemen</th>
                <th style="min-width: 150px;">Status Kepegawaian</th>
                <th style="min-width: 150px;">Masa Kerja / Masuk</th>
                <th class="text-center" style="width: 120px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($employees as $employee)
                @php
                    $user = $employee->user;
                    $displayName = $user?->name ?? ($employee->nik ? 'Karyawan ' . $employee->nik : 'Karyawan #' . $employee->id);
                    
                    // Generate initials & pastel badge color
                    $nameParts = array_filter(explode(' ', trim($displayName)));
                    $initials = '';
                    foreach (array_slice($nameParts, 0, 2) as $part) {
                        $initials .= strtoupper(substr($part, 0, 1));
                    }
                    if (empty($initials)) {
                        $initials = 'KR';
                    }
                    $colorPalette = ['primary', 'success', 'warning', 'info', 'danger'];
                    $paletteColor = $colorPalette[abs(crc32($displayName)) % count($colorPalette)];

                    $status = $employee->employment_status;
                    $badgeColor = $statusColors[$status] ?? 'secondary';
                @endphp
                <tr class="employee-row">
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input employee-checkbox" value="{{ $employee->id }}" data-name="{{ $displayName }}">
                    </td>
                    {{-- Karyawan (Avatar, Nama, Email) --}}
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3 flex-shrink-0">
                                @if ($user?->image && file_exists(public_path($user->image)))
                                    <img src="{{ asset($user->image) }}" alt="{{ $displayName }}" class="avatar-user-img"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                    <span class="avatar-initial rounded-circle bg-label-{{ $paletteColor }} fw-semibold shadow-xs" style="display:none;">
                                        {{ $initials }}
                                    </span>
                                @else
                                    <span class="avatar-initial rounded-circle bg-label-{{ $paletteColor }} fw-semibold shadow-xs">
                                        {{ $initials }}
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex flex-column" style="min-width: 0;">
                                <a href="{{ route('employees.show', $employee->id) }}" 
                                   class="fw-bold text-heading text-truncate text-hover-primary mb-0" 
                                   title="{{ $displayName }}">
                                    {{ $displayName }}
                                </a>
                                @if ($user?->email)
                                    <small class="text-muted text-truncate d-flex align-items-center mt-1" title="{{ $user->email }}">
                                        <i class="mdi mdi-email-outline me-1 fs-6 text-secondary"></i>
                                        <span>{{ $user->email }}</span>
                                    </small>
                                @else
                                    <span class="badge bg-label-secondary font-monospace mt-1 align-self-start" style="font-size: 0.7rem;">
                                        <i class="mdi mdi-account-off-outline me-1"></i>Tanpa Akun User
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- NIK & Kontak --}}
                    <td>
                        @if ($employee->nik)
                            <span class="badge bg-light text-dark border font-monospace px-2 py-1 mb-1 d-inline-flex align-items-center">
                                <i class="mdi mdi-card-account-details-outline me-1 text-primary"></i>
                                {{ $employee->nik }}
                            </span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif

                        @if ($employee->phone)
                            <small class="text-muted d-block mt-1">
                                <i class="mdi mdi-phone-outline me-1 text-secondary"></i>{{ $employee->phone }}
                            </small>
                        @endif
                    </td>

                    {{-- Posisi & Departemen --}}
                    <td>
                        <div class="fw-semibold text-heading mb-1 text-truncate" style="max-width: 200px;" title="{{ $employee->position?->name ?? 'Belum ditentukan' }}">
                            {{ $employee->position?->name ?? 'Belum ditentukan' }}
                        </div>
                        @if ($employee->department)
                            <span class="badge bg-label-primary px-2 py-1 font-weight-normal" style="font-size: 0.75rem;">
                                <i class="mdi mdi-domain me-1"></i>{{ $employee->department->name }}
                            </span>
                        @else
                            <span class="text-muted small">Tanpa Departemen</span>
                        @endif
                    </td>

                    {{-- Status Kepegawaian & Izin Absensi --}}
                    <td>
                        <span class="badge rounded-pill bg-label-{{ $badgeColor }} px-3 py-1 fw-semibold d-inline-flex align-items-center">
                            <span class="status-dot bg-{{ $badgeColor }}"></span>
                            {{ $status }}
                        </span>
                        <div class="mt-1">
                            @if ($employee->can_online_attendance)
                                <span class="badge bg-label-success d-inline-flex align-items-center" style="font-size: 0.68rem;" title="Karyawan diizinkan melakukan absensi online via portal / navbar">
                                    <i class="mdi mdi-clock-check-outline me-1"></i>Absen Online
                                </span>
                            @else
                                <span class="badge bg-label-secondary d-inline-flex align-items-center" style="font-size: 0.68rem;" title="Karyawan tidak diwajibkan / dinonaktifkan dari absensi online">
                                    <i class="mdi mdi-clock-remove-outline me-1"></i>Bebas Absen
                                </span>
                            @endif
                        </div>
                    </td>

                    {{-- Masa Kerja / Tanggal Masuk --}}
                    <td>
                        @if ($employee->join_date)
                            <div class="fw-medium text-heading">
                                <i class="mdi mdi-calendar-check-outline me-1 text-primary"></i>
                                {{ $employee->join_date->translatedFormat('d M Y') }}
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="mdi mdi-clock-outline me-1"></i>{{ $employee->join_date->diffForHumans(null, true) }}
                            </small>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            <a href="{{ route('employees.show', $employee->id) }}" 
                               class="btn btn-sm btn-icon btn-label-info rounded-pill" 
                               title="Lihat Detail"
                               data-bs-toggle="tooltip">
                                <i class="mdi mdi-eye-outline fs-6"></i>
                            </a>
                            <a href="{{ route('employees.edit', $employee->id) }}" 
                               class="btn btn-sm btn-icon btn-label-primary rounded-pill" 
                               title="Edit Data"
                               data-bs-toggle="tooltip">
                                <i class="mdi mdi-pencil-outline fs-6"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-sm btn-icon btn-label-danger rounded-pill btn-delete-employee" 
                                    data-id="{{ $employee->id }}"
                                    data-name="{{ $displayName }}"
                                    title="Hapus Karyawan"
                                    data-bs-toggle="tooltip">
                                <i class="mdi mdi-trash-can-outline fs-6"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <div class="avatar avatar-xl bg-label-secondary mb-3 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                <i class="mdi mdi-account-search-outline fs-1 text-muted"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-heading">Tidak Ada Data Karyawan Ditemukan</h6>
                            <p class="text-muted small mb-3" style="max-width: 360px;">
                                @if (request()->hasAny(['search', 'id_department', 'employment_status']))
                                    Tidak ada data karyawan yang cocok dengan kriteria pencarian atau filter yang diterapkan.
                                @else
                                    Belum ada data karyawan di sistem HR saat ini.
                                @endif
                            </p>
                            @if (request()->hasAny(['search', 'id_department', 'employment_status']))
                                <button type="button" class="btn btn-outline-primary btn-sm btn-reset-ajax-filter">
                                    <i class="mdi mdi-refresh me-1"></i> Reset Semua Filter
                                </button>
                            @else
                                <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">
                                    <i class="mdi mdi-account-plus-outline me-1"></i> Tambah Karyawan Pertama
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Card Footer & Pagination --}}
<div class="card-footer border-top py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
    <div class="text-muted small">
        @if ($employees->total() > 0)
            Menampilkan <span class="fw-semibold text-heading">{{ $employees->firstItem() }}</span> sampai 
            <span class="fw-semibold text-heading">{{ $employees->lastItem() }}</span> dari 
            <span class="fw-semibold text-heading">{{ $employees->total() }}</span> data karyawan
        @else
            Tidak ada data untuk ditampilkan
        @endif
    </div>
    @if ($employees->hasPages())
        <div class="pagination-wrapper">
            {{ $employees->links() }}
        </div>
    @endif
</div>
