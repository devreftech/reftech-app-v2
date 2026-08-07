@extends('layouts.sales.app')
@section('title', 'Surat Jalan SUO')
@section('content')
    <div class="row invoice-preview">
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    {{-- Header perusahaan --}}
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column mb-4">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex align-items-center gap-2 mb-4">
                                <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="" width="120">
                            </div>
                            <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                            <div style="font-size:10px;">
                                <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                <p class="mb-1">
                                    <i class="mdi mdi-phone-outline me-1"></i>022 54417653
                                    &nbsp;<i class="mdi mdi-email-outline me-1"></i>admin@reftech.id
                                </p>
                            </div>
                        </div>
                        <div class="text-end">
                            <h1 class="fw-bold" style="color:blue;">Surat Jalan</h1>
                            <span class="fw-bolder">{{ $suo->no_invoice_booking }}</span><br>
                            <small class="text-muted">{{ $suo->no_suo }}</small>
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- Info pengiriman --}}
                    <div class="row mb-4">
                        <div class="col-6">
                            <p class="mb-1 fw-semibold" style="font-size:11px;color:#888;">DIKIRIM KE</p>
                            <p class="mb-0 fw-bold">{{ $suo->company }}</p>
                            <p class="mb-0">{{ $suo->pic }}</p>
                            <p class="mb-0 text-muted" style="font-size:12px;">
                                @if ($client)
                                    {{ $delivery->destination == '1' ? $client->address : $client->subAddress }}
                                @else
                                    {{ $suo->address }}
                                @endif
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-1 fw-semibold" style="font-size:11px;color:#888;">TANGGAL</p>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($delivery->date)->format('d M Y') }}</p>
                            <p class="mb-1 fw-semibold mt-2" style="font-size:11px;color:#888;">JENIS PENGIRIMAN</p>
                            <p class="mb-0">{{ ucfirst($delivery->type) }}</p>
                            <p class="mb-1 fw-semibold mt-2" style="font-size:11px;color:#888;">DIBUAT OLEH</p>
                            <p class="mb-0">{{ $suo->sales->name ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Tabel item --}}
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" style="font-size:13px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:5%">No.</th>
                                    <th>Nama Item / Part</th>
                                    <th class="text-center" style="width:10%">Qty</th>
                                    <th style="width:12%">Satuan</th>
                                    <th style="width:15%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suo->detail as $i => $item)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="fw-semibold">{{ $item->item_name }}</td>
                                        <td class="text-center">{{ $item->qty }}</td>
                                        <td>{{ $item->unit ?? '-' }}</td>
                                        <td>{{ $item->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($suo->notes)
                        <div class="mt-3">
                            <p class="mb-1 fw-semibold" style="font-size:11px;color:#888;">CATATAN</p>
                            <p class="mb-0" style="font-size:13px;">{{ $suo->notes }}</p>
                        </div>
                    @endif

                    {{-- Tanda tangan --}}
                    <div class="row mt-5">
                        <div class="col-4 text-center">
                            <p class="mb-5" style="font-size:12px;">Pengirim</p>
                            <div style="border-top:1px solid #000;width:80%;margin:0 auto;"></div>
                            <p class="mt-1" style="font-size:11px;">&nbsp;</p>
                        </div>
                        <div class="col-4 text-center">
                            <p class="mb-5" style="font-size:12px;">Penerima</p>
                            <div style="border-top:1px solid #000;width:80%;margin:0 auto;"></div>
                            <p class="mt-1" style="font-size:11px;">&nbsp;</p>
                        </div>
                        <div class="col-4 text-center">
                            <p class="mb-5" style="font-size:12px;">Mengetahui</p>
                            <div style="border-top:1px solid #000;width:80%;margin:0 auto;"></div>
                            <p class="mt-1" style="font-size:11px;">&nbsp;</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar aksi --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card">
                <div class="card-body d-grid gap-2">
                    <div class="btn-group w-100">
                        <a href="{{ route('print.delivery', $delivery->id) }}?format=2"
                            class="btn btn-primary waves-effect" target="_blank">
                            <i class="mdi mdi-printer-outline me-1"></i> Cetak
                        </a>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('print.delivery', $delivery->id) }}?format=2" target="_blank">
                                    <i class="mdi mdi-file-document-outline me-1"></i> SJ Technician
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('print.delivery', $delivery->id) }}?format=1" target="_blank">
                                    <i class="mdi mdi-file-document-outline me-1"></i> SJ Expedition
                                </a>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('suo.show', $suo->id) }}" class="btn btn-outline-secondary waves-effect">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali ke SUO
                    </a>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <p class="mb-1 fw-semibold" style="font-size:11px;color:#888;">NO. SUO</p>
                    <p class="mb-2 fw-bold">{{ $suo->no_suo }}</p>
                    <p class="mb-1 fw-semibold" style="font-size:11px;color:#888;">NO. INVOICE BOOKING</p>
                    <p class="mb-2 fw-bold text-success">{{ $suo->no_invoice_booking }}</p>
                    <p class="mb-1 fw-semibold" style="font-size:11px;color:#888;">STATUS</p>
                    <span class="badge bg-success">Barang Keluar</span>
                </div>
            </div>
        </div>
    </div>
@endsection
