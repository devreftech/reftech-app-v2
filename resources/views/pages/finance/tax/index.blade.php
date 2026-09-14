@extends('layouts.sales.app')
@section('title', 'Laporan Perpajakan (Tax Management)')
@section('no-container') @endsection
@section('content')
<div class="container-fluid px-4 py-3">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Perpajakan /</span> Laporan Pajak (PPN &amp; PPh)
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-calculator-variant-outline me-1"></i> Rekapitulasi Pajak Keluaran (Sales), Pajak Masukan (Purchase), Komparasi Kurang/Lebih Bayar &amp; Pemotongan PPh
            </p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-label-secondary btn-sm">
                <i class="mdi mdi-printer me-1"></i> Cetak Laporan
            </button>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('finance.tax.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold small">Tahun Pajak</label>
                    <select name="year" class="form-select select2">
                        @foreach ($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold small">Masa Pajak (Bulan)</label>
                    <select name="month" class="form-select select2">
                        <option value="all" {{ $month === 'all' ? 'selected' : '' }}>Semua Bulan (Setahun)</option>
                        @php
                            $months = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp
                        @foreach ($months as $num => $name)
                            <option value="{{ $num }}" {{ (string)$month === (string)$num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold small">Entitas / Bendera</label>
                    <select name="flag" class="form-select select2">
                        <option value="all" {{ $flag === 'all' ? 'selected' : '' }}>Semua Entitas</option>
                        <option value="reftech" {{ $flag === 'reftech' ? 'selected' : '' }}>PT. Reftech Jaya Optima</option>
                        <option value="kojisha" {{ $flag === 'kojisha' ? 'selected' : '' }}>PT. Kojisha Innotiv Indonesia</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-filter-outline me-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Metric Cards --}}
    <div class="row g-3 mb-4">
        {{-- PPN Keluaran --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 5px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-primary small" style="font-size: 11px;">
                            <i class="mdi mdi-arrow-top-right me-1"></i> PPN Keluaran (Sales)
                        </span>
                        <span class="badge bg-label-primary rounded-pill">{{ count($outputVatList) }} Faktur</span>
                    </div>
                    <h4 class="fw-bolder text-primary mb-1">Rp {{ number_format($totalOutputPpn, 0, ',', '.') }}</h4>
                    <small class="text-muted d-block" style="font-size: 11px;">DPP: Rp {{ number_format($totalOutputDpp, 0, ',', '.') }}</small>
                </div>
            </div>
        </div>

        {{-- PPN Masukan --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 5px solid #22c55e !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-success small" style="font-size: 11px;">
                            <i class="mdi mdi-arrow-bottom-left me-1"></i> PPN Masukan (Purchase)
                        </span>
                        <span class="badge bg-label-success rounded-pill">{{ count($inputVatList) }} Faktur</span>
                    </div>
                    <h4 class="fw-bolder text-success mb-1">Rp {{ number_format($totalInputPpn, 0, ',', '.') }}</h4>
                    <small class="text-muted d-block" style="font-size: 11px;">DPP: Rp {{ number_format($totalInputDpp, 0, ',', '.') }}</small>
                </div>
            </div>
        </div>

        {{-- Net PPN --}}
        <div class="col-12 col-sm-6 col-xl-3">
            @php
                $isKurangBayar = $netPpn >= 0;
                $netBg = $isKurangBayar ? 'linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%)' : 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)';
                $netBorder = $isKurangBayar ? '#f97316' : '#0284c7';
                $netColor = $isKurangBayar ? 'text-warning' : 'text-info';
                $netStatusText = $isKurangBayar ? 'PPN Kurang Bayar (KB)' : 'PPN Lebih Bayar (LB)';
            @endphp
            <div class="card border-0 shadow-sm h-100" style="background: {{ $netBg }}; border-left: 5px solid {{ $netBorder }} !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold small text-dark" style="font-size: 11px;">
                            <i class="mdi mdi-scale-balance me-1"></i> Net PPN Masa
                        </span>
                        <span class="badge {{ $isKurangBayar ? 'bg-warning' : 'bg-info' }} rounded-pill" style="font-size: 10px;">
                            {{ $isKurangBayar ? 'Kurang Bayar' : 'Lebih Bayar' }}
                        </span>
                    </div>
                    <h4 class="fw-bolder mb-1 text-dark">Rp {{ number_format(abs($netPpn), 0, ',', '.') }}</h4>
                    <small class="text-muted d-block" style="font-size: 11px;">
                        {{ $isKurangBayar ? 'Wajib disetor ke Kas Negara' : 'Dapat dikompensasi ke masa berikutnya' }}
                    </small>
                </div>
            </div>
        </div>

        {{-- PPh Withholding --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); border-left: 5px solid #a855f7 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold small" style="color: #7e22ce; font-size: 11px;">
                            <i class="mdi mdi-file-percent-outline me-1"></i> PPh Terpotong (Prepaid)
                        </span>
                        <span class="badge rounded-pill" style="background-color: #f3e8ff; color: #7e22ce;">{{ count($pphPayments) }} Bukpot</span>
                    </div>
                    <h4 class="fw-bolder mb-1" style="color: #7e22ce;">Rp {{ number_format($totalPph, 0, ',', '.') }}</h4>
                    <small class="text-muted d-block" style="font-size: 11px;">PPh 23 dipotong oleh Customer</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Section --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom p-2 bg-light">
            <ul class="nav nav-pills card-header-pills" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-ppn-keluaran" type="button" role="tab">
                        <i class="mdi mdi-arrow-top-right me-1"></i> Pajak Keluaran (PPN Sales)
                        <span class="badge bg-primary text-white rounded-pill ms-1">{{ count($outputVatList) }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-ppn-masukan" type="button" role="tab">
                        <i class="mdi mdi-arrow-bottom-left me-1"></i> Pajak Masukan (PPN Pembelian)
                        <span class="badge bg-success text-white rounded-pill ms-1">{{ count($inputVatList) }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-pph" type="button" role="tab">
                        <i class="mdi mdi-file-percent-outline me-1"></i> Pemotongan PPh 23
                        <span class="badge bg-label-secondary rounded-pill ms-1">{{ count($pphPayments) }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content p-0">
                {{-- TAB 1: PPN KELUARAN --}}
                <div class="tab-pane fade show active" id="tab-ppn-keluaran" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Tanggal</th>
                                    <th>No. Invoice</th>
                                    <th>No. PO / Ref</th>
                                    <th>Customer / Wajib Pajak</th>
                                    <th>NPWP</th>
                                    <th class="text-end">DPP (Rp)</th>
                                    <th class="text-end">PPN 11% (Rp)</th>
                                    <th class="text-end">Total Gross (Rp)</th>
                                    <th class="text-center">Entitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($outputVatList as $idx => $row)
                                    <tr>
                                        <td class="text-muted">{{ $idx + 1 }}</td>
                                        <td>{{ Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                                        <td class="fw-semibold text-primary">
                                            <a href="{{ route('payment_detail.invoice', $row->id) }}" class="text-primary text-decoration-none">
                                                {{ $row->no_invoice }}
                                            </a>
                                        </td>
                                        <td>{{ $row->no_po ?: ($row->no_quote ?: '-') }}</td>
                                        <td class="fw-semibold text-dark">{{ $row->company }}</td>
                                        <td><small class="text-muted">{{ $row->npwp ?: '-' }}</small></td>
                                        <td class="text-end">{{ number_format($row->dpp, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold text-primary">{{ number_format($row->ppn, 0, ',', '.') }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($row->gross, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ strtolower($row->bendera) == 'kojisha' ? 'bg-label-warning' : 'bg-label-info' }} rounded-pill" style="font-size: 10px;">
                                                {{ $row->bendera ?: 'Reftech' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-muted">
                                            Tidak ada transaksi PPN Keluaran pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="6" class="text-end">TOTAL PPN KELUARAN:</td>
                                    <td class="text-end">Rp {{ number_format($totalOutputDpp, 0, ',', '.') }}</td>
                                    <td class="text-end text-primary fs-6">Rp {{ number_format($totalOutputPpn, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($totalOutputGross, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: PPN MASUKAN --}}
                <div class="tab-pane fade" id="tab-ppn-masukan" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Tanggal</th>
                                    <th>No. Faktur / Invoice</th>
                                    <th>No. Penerimaan (GR)</th>
                                    <th>Supplier / Vendor</th>
                                    <th>NPWP</th>
                                    <th class="text-end">DPP (Rp)</th>
                                    <th class="text-end">PPN Masukan (Rp)</th>
                                    <th class="text-end">Total Pembelian (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($inputVatList as $idx => $row)
                                    <tr>
                                        <td class="text-muted">{{ $idx + 1 }}</td>
                                        <td>{{ Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                                        <td class="fw-semibold text-success">
                                            {{ $row->no_invoice ?: '-' }}
                                        </td>
                                        <td>{{ $row->no_product_in ?: '-' }}</td>
                                        <td class="fw-semibold text-dark">{{ $row->supplier_name }}</td>
                                        <td><small class="text-muted">{{ $row->npwp ?: '-' }}</small></td>
                                        <td class="text-end">{{ number_format($row->dpp, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold text-success">{{ number_format($row->ppn, 0, ',', '.') }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($row->gross, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            Tidak ada transaksi PPN Masukan pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="6" class="text-end">TOTAL PPN MASUKAN:</td>
                                    <td class="text-end">Rp {{ number_format($totalInputDpp, 0, ',', '.') }}</td>
                                    <td class="text-end text-success fs-6">Rp {{ number_format($totalInputPpn, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($totalInputGross, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- TAB 3: PPh 23 --}}
                <div class="tab-pane fade" id="tab-pph" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Tanggal</th>
                                    <th>Customer / Pemotong</th>
                                    <th>Rekening Bank Penerima</th>
                                    <th>Keterangan / Ref</th>
                                    <th class="text-end">Nominal Pembayaran (Rp)</th>
                                    <th class="text-end">PPh 23 Dipotong (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pphPayments as $idx => $p)
                                    <tr>
                                        <td class="text-muted">{{ $idx + 1 }}</td>
                                        <td>{{ Carbon\Carbon::parse($p['date'])->format('d/m/Y') }}</td>
                                        <td class="fw-semibold text-dark">{{ $p['client'] }}</td>
                                        <td>{{ $p['bank'] }}</td>
                                        <td>{{ $p['note'] }}</td>
                                        <td class="text-end">{{ number_format($p['amount'], 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold" style="color: #7e22ce;">{{ number_format($p['pph'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            Tidak ada pemotongan PPh pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="6" class="text-end">TOTAL PPh 23 TERPOTONG:</td>
                                    <td class="text-end fs-6" style="color: #7e22ce;">Rp {{ number_format($totalPph, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
