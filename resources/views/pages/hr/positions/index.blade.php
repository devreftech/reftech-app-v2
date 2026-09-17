@extends('layouts.sales.app')
@section('title', 'Posisi / Jabatan')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 mb-3 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-1">
                    <li class="breadcrumb-item text-muted">
                        <i class="mdi mdi-account-tie-outline me-1"></i> HR Management
                    </li>
                    <li class="breadcrumb-item active fw-semibold text-primary">Position</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-heading d-flex align-items-center">
                <span>Posisi / Jabatan</span>
                <span class="badge bg-label-primary rounded-pill ms-2 fs-6 fw-semibold px-2 py-1">
                    {{ $positions->total() }} Total
                </span>
            </h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('positions.create') }}" class="btn btn-primary d-flex align-items-center shadow-sm">
                <i class="mdi mdi-plus-circle-outline me-1 fs-5"></i> Tambah Posisi
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Nama Posisi</th>
                        <th>Departemen</th>
                        <th class="text-center">Level</th>
                        <th class="text-center">Jml. Karyawan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($positions as $position)
                        <tr>
                            <td>{{ $position->name }}</td>
                            <td>{{ $position->department?->name ?? '-' }}</td>
                            <td class="text-center">{{ $position->level }}</td>
                            <td class="text-center">{{ $position->employees_count }}</td>
                            <td>
                                <span class="badge bg-label-{{ $position->is_active ? 'success' : 'secondary' }}">
                                    {{ $position->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('positions.edit', $position->id) }}" class="btn btn-sm btn-icon btn-label-primary" title="Edit">
                                    <i class="mdi mdi-pencil-outline"></i>
                                </a>
                                <form action="{{ route('positions.destroy', $position->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus posisi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Hapus">
                                        <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data posisi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($positions->hasPages())
            <div class="card-footer">
                {{ $positions->links() }}
            </div>
        @endif
    </div>
@endsection
