@extends('layouts.sales.app')
@section('title', 'Audit Tools')
@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <h4 class="fw-bold py-3 mb-4">
        Audit Tools
    </h4>
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>No Audit</th>
                                <th>Periode</th>
                                <th>Window</th>
                                <th>Total Tools</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($audits as $audit)
                                <tr>
                                    <td>{{ $audit->no_audit }}</td>
                                    <td>{{ $audit->period->tahun }} - Semester {{ $audit->period->semester }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($audit->period->tanggal_mulai)->format('d M') }} -
                                        {{ \Carbon\Carbon::parse($audit->period->tanggal_selesai)->format('d M Y') }}
                                    </td>
                                    <td>{{ $audit->total_tools }}</td>
                                    <td>
                                        @php
                                            $badge = [
                                                'Draft' => 'bg-label-secondary',
                                                'Submitted' => 'bg-label-warning',
                                                'Verified' => 'bg-label-success',
                                                'Rejected' => 'bg-label-danger',
                                            ][$audit->status_submit] ?? 'bg-label-secondary';
                                        @endphp
                                        <span class="badge {{ $badge }}">{{ $audit->status_submit }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('tool-audit.show', $audit->id) }}" class="btn btn-sm btn-primary">
                                            {{ in_array($audit->status_submit, ['Draft', 'Rejected']) ? 'Isi Audit' : 'Lihat' }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        Belum ada periode audit yang aktif untuk kamu. Audit tools dibuka otomatis di 10 hari terakhir bulan Juni & Desember.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection()
