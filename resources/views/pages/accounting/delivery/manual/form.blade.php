@extends('layouts.sales.app')
@section('title', 'Form Delivery Manual')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Form Delivery Manual</h4>
            <span class="text-muted">Input data surat jalan / pengiriman manual.</span>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-label-secondary"><i class="mdi mdi-arrow-left me-1"></i> Kembali</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ url('/delivery/manual/store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">No Surat Jalan</label>
                        <input type="text" name="no_delivery" class="form-control" placeholder="Nomor Surat Jalan" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Pengiriman</label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Catatan / Keterangan</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Catatan pengiriman"></textarea>
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary"><i class="mdi mdi-content-save me-1"></i> Simpan Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
