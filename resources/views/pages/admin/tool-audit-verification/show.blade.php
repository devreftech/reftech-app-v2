@extends('layouts.sales.app')
@section('title', 'Verifikasi - ' . $audit->no_audit)
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $headerBadge = [
            'Submitted' => 'bg-label-warning',
            'Verified' => 'bg-label-success',
            'Rejected' => 'bg-label-danger',
        ][$audit->status_submit] ?? 'bg-label-secondary';
        $canDecide = $audit->status_submit == 'Submitted';
    @endphp

    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Verifikasi Audit Tools /</span> {{ $audit->no_audit }}
        <span class="badge {{ $headerBadge }} align-middle">{{ $audit->status_submit }}</span>
    </h4>
    <p class="text-muted mb-4">
        Teknisi: <strong>{{ $audit->technician->name ?? '-' }}</strong> —
        Periode {{ $audit->period->tahun }} Semester {{ $audit->period->semester }}
        @if ($audit->submitted_at)
            <br>Disubmit: {{ \Carbon\Carbon::parse($audit->submitted_at)->format('d M Y H:i') }}
        @endif
        @if ($audit->verified_at)
            <br>Diproses: {{ \Carbon\Carbon::parse($audit->verified_at)->format('d M Y H:i') }} oleh {{ $audit->verifiedBy->name ?? '-' }}
        @endif
    </p>

    <a href="{{ route('tool-audit-verification.index', ['status' => $audit->status_submit]) }}"
        class="btn btn-outline-secondary btn-sm mb-3">
        <i class="mdi mdi-arrow-left"></i> Kembali
    </a>

    <form action="{{ route('tool-audit-verification.decide', $audit->id) }}" method="post">
        @csrf
        @foreach ($audit->items as $item)
            @php
                $tool = $item->fixedAsset;
                $master = $tool->toolsMaster ?? null;
                $kondisiBadge = ['Ada' => 'bg-label-success', 'Rusak' => 'bg-label-warning', 'Hilang' => 'bg-label-danger'][$item->kondisi] ?? 'bg-label-secondary';
            @endphp
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3 align-items-start">
                        <div class="col-md-3 text-center">
                            @if ($tool && $tool->foto_awal)
                                <img src="{{ asset($tool->foto_awal) }}" alt="foto awal"
                                    style="width:100%;max-width:140px;aspect-ratio:1/1;object-fit:cover;border-radius:6px;">
                            @endif
                            <div class="small text-muted mt-1">Foto Awal (baseline)</div>
                        </div>
                        <div class="col-md-3 text-center">
                            @if ($item->foto_audit)
                                <img src="{{ asset($item->foto_audit) }}" alt="foto audit"
                                    style="width:100%;max-width:140px;aspect-ratio:1/1;object-fit:cover;border-radius:6px;">
                            @endif
                            <div class="small text-muted mt-1">Foto Audit (sekarang)</div>
                        </div>
                        <div class="col-md-3">
                            <h6 class="mb-1">{{ $master->nama_tools ?? '-' }}</h6>
                            <div class="text-muted small mb-2">
                                Qty terdaftar: {{ $tool->qty ?? '-' }} — Qty sekarang: {{ $item->qty_actual ?? '-' }}
                            </div>
                            <span class="badge {{ $kondisiBadge }}">{{ $item->kondisi ?? '-' }}</span>
                            @if ($item->alasan)
                                <div class="small text-muted mt-1">{{ $item->kondisi == 'Hilang' ? 'Catatan' : 'Alasan' }}: {{ $item->alasan }}</div>
                            @endif
                            @if ($item->metode_ganti)
                                <div class="small text-muted mt-1">Ganti: {{ $item->metode_ganti }}</div>
                            @endif
                        </div>
                        <div class="col-md-3">
                            @if ($canDecide)
                                <div class="form-floating form-floating-outline mb-2">
                                    <select class="form-select form-select-sm" name="item_status[{{ $item->id }}]">
                                        <option value="Pending" {{ $item->status_verifikasi_item == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Sesuai" {{ $item->status_verifikasi_item == 'Sesuai' ? 'selected' : '' }}>Sesuai</option>
                                        <option value="Tidak Sesuai" {{ $item->status_verifikasi_item == 'Tidak Sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                                    </select>
                                    <label>Status Item</label>
                                </div>
                                <input type="text" class="form-control form-control-sm" name="item_note[{{ $item->id }}]"
                                    value="{{ $item->catatan_admin_item }}" placeholder="Catatan (opsional)">
                            @else
                                @php
                                    $itemBadge = ['Sesuai' => 'bg-label-success', 'Tidak Sesuai' => 'bg-label-danger'][$item->status_verifikasi_item] ?? 'bg-label-secondary';
                                @endphp
                                <span class="badge {{ $itemBadge }}">{{ $item->status_verifikasi_item }}</span>
                                @if ($item->catatan_admin_item)
                                    <div class="small text-muted mt-1">{{ $item->catatan_admin_item }}</div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if ($canDecide)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="form-floating form-floating-outline mb-3">
                        <textarea class="form-control" name="catatan_admin" id="catatanAdmin" rows="2"
                            placeholder="Catatan buat teknisi..."></textarea>
                        <label for="catatanAdmin">Catatan Admin (wajib kalau Tolak)</label>
                    </div>
                    <button type="submit" name="action" value="verify" class="btn btn-success me-2">
                        <i class="mdi mdi-check"></i> Verifikasi Selesai
                    </button>
                    <button type="submit" name="action" value="reject" class="btn btn-outline-danger">
                        <i class="mdi mdi-close"></i> Tolak / Minta Perbaikan
                    </button>
                </div>
            </div>
        @elseif ($audit->catatan_admin)
            <div class="alert alert-secondary">
                <strong>Catatan Admin:</strong> {{ $audit->catatan_admin }}
            </div>
        @endif
    </form>
@endsection()
