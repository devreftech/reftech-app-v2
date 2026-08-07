@extends('layouts.sales.app')
@section('title', 'Goods Receipt Unit')
@section('content')
    <form action="{{ route('unit-product-in.store-goods-receipt', $purchase->id) }}" method="post">
        @csrf
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Terima Barang — PO {{ $purchase->no_po }}</h5>
                <p class="text-muted small mb-0">Supplier: {{ $purchase->company }} — diterima utuh sekaligus sesuai qty PO.</p>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12 col-lg-4">
                        <div class="form-floating form-floating-outline mb-2">
                            <input class="form-control" type="date" name="date" required
                                value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                            <label>Tanggal Terima</label>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty PO</th>
                                <th>Harga</th>
                                <th>Serial Number Diterima</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail as $item)
                                <tr>
                                    <td>
                                        {{ $item->product }}
                                        <input type="hidden" name="detail_id[]" value="{{ $item->id }}">
                                    </td>
                                    <td>{{ $item->qty }} {{ $item->info_qty }}</td>
                                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td>
                                        <input type="text" class="form-control" name="serial_number[]"
                                            placeholder="Serial number unit fisik" required>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="float-end mt-3">
                    <a href="{{ route('unit-product-in.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Penerimaan</button>
                </div>
            </div>
        </div>
    </form>
@endsection
