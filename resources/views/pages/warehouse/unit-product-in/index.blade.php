@extends('layouts.sales.app')
@section('title', 'Unit Product In')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y p-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Barang Masuk Unit</h4>
                <p class="text-muted mb-0 small">Satu pintu masuk semua transaksi unit: beli baru, beli second, atau
                    trade-in.</p>
            </div>
            <a href="{{ route('unit-product-in.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus me-1"></i> Input Manual
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Card "PO Unit Menunggu Diterima (Goods Receipt)" dihapus — sudah terwakili
             oleh /product-in (role Logistic) tab Menunggu Penerimaan > sub-tab Non-PR,
             yang tombol GR-nya juga ngarah ke form yang sama (unit-product-in.goods-receipt-form). --}}

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Riwayat Barang Masuk Unit</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>No Transaksi</th>
                            <th>No PO</th>
                            <th>Tipe Transaksi</th>
                            <th>Supplier / Customer</th>
                            <th>Tanggal</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($unitProductIns as $item)
                            <tr>
                                <td>{{ $item->no_transaksi }}</td>
                                <td>
                                    @if ($item->po)
                                        <a href="{{ route('purchase.show', $item->po->id) }}">{{ $item->po->no_po }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($item->transaction_type)
                                        @case('purchase_new')
                                            <span class="badge bg-label-success">Beli Unit Baru</span>
                                        @break

                                        @case('purchase_used')
                                            <span class="badge bg-label-warning">Beli Unit Second</span>
                                        @break

                                        @case('trade_in')
                                            <span class="badge bg-label-info">Trade-In</span>
                                        @break
                                    @endswitch
                                </td>
                                <td>{{ $item->supplier->supplier ?? $item->id_customer ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                                <td>{{ $item->note }}</td>
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
