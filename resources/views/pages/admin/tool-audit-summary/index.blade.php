@extends('layouts.sales.app')
@section('title', 'Summary Audit Tools')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        Summary Audit Tools
    </h4>

    <form method="get" class="mb-4" style="max-width: 320px;">
        <div class="form-floating form-floating-outline">
            <select class="form-select" name="period_id" onchange="this.form.submit()">
                @forelse ($periods as $p)
                    <option value="{{ $p->id }}" {{ $period && $period->id == $p->id ? 'selected' : '' }}>
                        {{ $p->tahun }} - Semester {{ $p->semester }}
                        ({{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M') }} -
                        {{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y') }})
                    </option>
                @empty
                    <option value="">Belum ada periode audit</option>
                @endforelse
            </select>
            <label>Periode</label>
        </div>
    </form>

    @if (!$period)
        <div class="alert alert-secondary">Belum ada periode audit yang pernah digenerate.</div>
    @else
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-2">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold">{{ $summary['total_teknisi'] }}</div>
                        <div class="small text-muted">Teknisi</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold text-secondary">{{ $summary['draft'] }}</div>
                        <div class="small text-muted">Draft</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold text-warning">{{ $summary['submitted'] }}</div>
                        <div class="small text-muted">Menunggu Verifikasi</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold text-success">{{ $summary['verified'] }}</div>
                        <div class="small text-muted">Verified</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold text-danger">{{ $summary['rejected'] }}</div>
                        <div class="small text-muted">Ditolak</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold">{{ $summary['total_tools'] }}</div>
                        <div class="small text-muted">Total Tools</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="card text-center border-success">
                    <div class="card-body py-3">
                        <div class="fs-3 fw-bold text-success">{{ $summary['total_ada'] }}</div>
                        <div class="small text-muted">Ada</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center border-warning">
                    <div class="card-body py-3">
                        <div class="fs-3 fw-bold text-warning">{{ $summary['total_rusak'] }}</div>
                        <div class="small text-muted">Rusak</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center border-danger">
                    <div class="card-body py-3">
                        <div class="fs-3 fw-bold text-danger">{{ $summary['total_hilang'] }}</div>
                        <div class="small text-muted">Hilang</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Teknisi</th>
                            <th>No Audit</th>
                            <th>Status</th>
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
                            @php
                                $badge = [
                                    'Draft' => 'bg-label-secondary',
                                    'Submitted' => 'bg-label-warning',
                                    'Verified' => 'bg-label-success',
                                    'Rejected' => 'bg-label-danger',
                                ][$audit->status_submit] ?? 'bg-label-secondary';
                            @endphp
                            <tr>
                                <td>{{ $audit->technician->name ?? '-' }}</td>
                                <td>{{ $audit->no_audit }}</td>
                                <td><span class="badge {{ $badge }}">{{ $audit->status_submit }}</span></td>
                                <td>{{ $audit->total_tools }}</td>
                                <td>{{ $audit->total_ada }}</td>
                                <td>{{ $audit->total_rusak }}</td>
                                <td>{{ $audit->total_hilang }}</td>
                                <td>{{ $audit->submitted_at ? \Carbon\Carbon::parse($audit->submitted_at)->format('d M Y H:i') : '-' }}</td>
                                <td>
                                    <a href="{{ route('tool-audit-verification.show', $audit->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Belum ada teknisi dengan tools aktif di periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection()
