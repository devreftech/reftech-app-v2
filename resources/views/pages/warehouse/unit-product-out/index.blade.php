@extends('layouts.sales.app')
@section('title', 'Unit Product Out')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y p-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Barang Keluar Unit</h4>
                <p class="text-muted mb-0 small">Pelepasan unit baru (terjual) maupun disposal unit second (aset
                    keluar).</p>
            </div>
            <a href="{{ route('unit-product-out.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus me-1"></i> Input Unit Keluar
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Riwayat Barang Keluar Unit</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>No Transaksi</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th>Jumlah Item</th>
                            <th>Total Harga Jual</th>
                            <th>Total Selisih (Gain/Loss)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($unitProductOuts as $item)
                            <tr>
                                <td>{{ $item->no_transaksi }}</td>
                                <td>{{ $item->customer ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                                <td>{{ $item->detail->count() }}</td>
                                <td>Rp {{ number_format($item->detail->sum('harga_jual'), 0, ',', '.') }}</td>
                                <td class="{{ $item->detail->sum('selisih') < 0 ? 'text-danger' : 'text-success' }}">
                                    Rp {{ number_format($item->detail->sum('selisih'), 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
