@extends('layouts.sales.app')
@section('title', 'Verifikasi Audit Tools')
@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <h4 class="fw-bold py-3 mb-4">
        Verifikasi Audit Tools
    </h4>

    <ul class="nav nav-pills mb-3">
        @foreach (['Submitted' => 'Menunggu Verifikasi', 'Verified' => 'Sudah Diverifikasi', 'Rejected' => 'Ditolak'] as $key => $label)
            <li class="nav-item">
                <a class="nav-link {{ $status == $key ? 'active' : '' }}"
                    href="{{ route('tool-audit-verification.index', ['status' => $key]) }}">{{ $label }}</a>
            </li>
        @endforeach
    </ul>

    <div class="card mb-3">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>No Audit</th>
                        <th>Teknisi</th>
                        <th>Periode</th>
                        <th>Total Tools</th>
                        <th>Ada</th>
                        <th>Rusak</th>
                        <th>Hilang</th>
                        <th>Disubmit</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($audits as $audit)
                        <tr>
                            <td>{{ $audit->no_audit }}</td>
                            <td>{{ $audit->technician->name ?? '-' }}</td>
                            <td>{{ $audit->period->tahun }} - Semester {{ $audit->period->semester }}</td>
                            <td>{{ $audit->total_tools }}</td>
                            <td>{{ $audit->total_ada }}</td>
                            <td>{{ $audit->total_rusak }}</td>
                            <td>{{ $audit->total_hilang }}</td>
                            <td>{{ $audit->submitted_at ? \Carbon\Carbon::parse($audit->submitted_at)->format('d M Y H:i') : '-' }}</td>
                            <td>
                                <a href="{{ route('tool-audit-verification.show', $audit->id) }}" class="btn btn-sm btn-primary">
                                    {{ $status == 'Submitted' ? 'Verifikasi' : 'Lihat' }}
                                </a>
                                @if (Auth::user()->role == 'Admin')
                                    <a href="{{ route('tool-audit-verification.edit', $audit->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="mdi mdi-pencil"></i> Edit
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection()
