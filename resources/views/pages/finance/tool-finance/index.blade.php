@extends('layouts.sales.app')
@section('title', 'Kelengkapan Data Finance - Tools')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        Kelengkapan Data Finance — Tools
    </h4>
    <p class="text-muted mb-3">
        Tools yang sudah di-assign ke teknisi (lewat Data Tools/Management Tools per Teknisi) belum otomatis punya
        data akuntansi (akun aktiva, harga beli, dst). Lengkapi per item lewat tombol "Lengkapi Data" di bawah —
        ini akan membuka form edit Fixed Asset standar, data assignment ke teknisi tidak akan berubah.
    </p>

    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $status == 'belum' ? 'active' : '' }}"
                href="{{ route('tool-finance.index', ['status' => 'belum']) }}">
                Belum Lengkap <span class="badge bg-label-danger ms-1">{{ $countBelum }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'sudah' ? 'active' : '' }}"
                href="{{ route('tool-finance.index', ['status' => 'sudah']) }}">
                Sudah Lengkap <span class="badge bg-label-success ms-1">{{ $countSudah }}</span>
            </a>
        </li>
    </ul>

    <div class="card mb-3">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Nama Tools</th>
                        <th>Teknisi</th>
                        <th>Qty</th>
                        <th>Tanggal Serah Terima</th>
                        <th>Harga Beli</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tools as $tool)
                        <tr>
                            <td>{{ $tool->toolsMaster->nama_tools ?? '-' }}</td>
                            <td>{{ $tool->pic->name ?? '-' }}</td>
                            <td>{{ $tool->qty }}</td>
                            <td>{{ $tool->tanggal_serah_terima ? \Carbon\Carbon::parse($tool->tanggal_serah_terima)->format('d M Y') : '-' }}</td>
                            <td>{{ $tool->total ? 'Rp ' . number_format($tool->total, 0, ',', '.') : '-' }}</td>
                            <td>
                                <a href="{{ route('fixed.edit', $tool->id) }}" class="btn btn-sm btn-primary">
                                    {{ $status == 'belum' ? 'Lengkapi Data' : 'Lihat / Edit' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                {{ $status == 'belum' ? 'Semua tools sudah lengkap data finance-nya.' : 'Belum ada yang lengkap.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection()
