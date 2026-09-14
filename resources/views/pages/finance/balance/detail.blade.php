@extends('layouts.sales.app')
@section('title', 'Laporan Neraca - ' . $startString . ' s/d ' . $endString)
@section('content')
    @php
        $periodYear = \Carbon\Carbon::parse($startDate)->year;
        $printUrl = $month
            ? route('expense-balance.print-bulan', [$periodYear, $month])
            : route('expense-balance.print-tahun', [$periodYear]);

        $totalKasBank = $totalKasBank ?? (($bank->saldo ?? 0) + ($capPalembang ?? 425000000) + ($modPalembang ?? 575000000));
        $totalLancar = $totalKasBank + $piutang + $asset + $ppnMas;
        $totalTetap = $totalFixed - $grandTotalPenyusutan;
        $totalAktiva = $totalLancar + $totalTetap;

        if (@$month) {
            $ekuitas = 250000000 + $labaTahunTahun - $prive - $labaBulanIni;
            $totalekuitas = $ekuitas + $labaBulanIni;
            $sebelumnya = $labaTahunTahun - $labaBulanIni;
        } else {
            $ekuitas = 250000000 + $labaTahunTahun - $prive - $labaTahunIni;
            $totalekuitas = $ekuitas + $labaTahunIni;
            $sebelumnya = $labaTahunTahun - $labaTahunIni;
        }
        $totalKewajiban = $ppnKel ?? 0;
        $ekujiban = $totalekuitas + $totalKewajiban;
        $selisih = $totalAktiva - $ekujiban;
        $isBalanced = round($totalAktiva) === round($ekujiban);
    @endphp

    {{-- Top Header Action Bar --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center py-3 mb-3 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 fs-6">
                    <li class="breadcrumb-item"><a href="{{ route('finance.statement.index') }}">Finance</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('finance.statement.index') }}?tab=balance#tab-balance">Statement</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Neraca (Balance Sheet)</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="mdi mdi-scale-balance text-primary"></i> Laporan Posisi Keuangan (Neraca)
                <span class="badge bg-label-primary fs-6">{{ $startString }} &ndash; {{ $endString }}</span>
            </h4>
            <p class="text-muted mb-0 small">
                Ringkasan menyeluruh posisi aktiva (harta), kewajiban (hutang), dan ekuitas (modal) PT. Reftech Jaya Optima.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Layout Toggle (Skontro vs Stafel) --}}
            <div class="btn-group shadow-sm" role="group" aria-label="Layout Switcher">
                <button type="button" class="btn btn-outline-primary active btn-sm d-flex align-items-center gap-1" id="btnModeSkontro" onclick="setBalanceLayout('skontro')">
                    <i class="mdi mdi-view-column-outline"></i> 2 Kolom (Skontro)
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="btnModeStafel" onclick="setBalanceLayout('stafel')">
                    <i class="mdi mdi-view-agenda-outline"></i> 1 Kolom (Stafel)
                </button>
            </div>

            {{-- Period Quick Jump --}}
            <div class="dropdown">
                <button class="btn btn-label-secondary btn-sm dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-calendar-range me-1"></i> Ganti Periode
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="max-height: 320px; overflow-y: auto;">
                    <li class="dropdown-header text-uppercase small fw-bold">Tahun {{ $periodYear }}</li>
                    @for($m = 1; $m <= 12; $m++)
                        @php
                            $monthName = \Carbon\Carbon::create($periodYear, $m, 1)->translatedFormat('F');
                            $isCurrent = (int)$month === $m;
                        @endphp
                        <li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center {{ $isCurrent ? 'active fw-bold' : '' }}" 
                               href="{{ route('expense-balance.detail-bulan', [$periodYear, str_pad($m, 2, '0', STR_PAD_LEFT)]) }}">
                                <span>Bulan {{ $monthName }}</span>
                                @if($isCurrent) <i class="mdi mdi-check small"></i> @endif
                            </a>
                        </li>
                    @endfor
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item fw-semibold" href="{{ route('expense-balance.detail-tahun', [$periodYear]) }}">
                            <i class="mdi mdi-calendar-blank-outline me-1"></i> Seluruh Tahun {{ $periodYear }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Back Button --}}
            <a href="{{ route('finance.statement.index') }}?tab=balance#tab-balance" class="btn btn-label-secondary btn-sm shadow-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>

            {{-- Print Button --}}
            <a href="{{ $printUrl }}" target="_blank" class="btn btn-primary btn-sm shadow-sm">
                <i class="mdi mdi-printer-outline me-1"></i> Cetak / Print A4
            </a>
        </div>
    </div>

    {{-- Executive KPI Metrics Cards --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Kas & Bank --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%); border-left: 4px solid #03c3ec !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Total Kas &amp; Bank</span>
                        <div class="avatar avatar-sm bg-label-info rounded">
                            <i class="mdi mdi-bank fs-5 text-info"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-dark mb-1">Rp {{ number_format($totalKasBank, 0, ',', '.') }}</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        <span class="text-info fw-semibold">Pusat</span>: Rp {{ number_format($bank->saldo ?? 0, 0, ',', '.') }} | 
                        <span class="text-success fw-semibold">Palembang</span>: Rp {{ number_format($capPalembang + $modPalembang, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Aktiva Lancar --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f5f3ff 100%); border-left: 4px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Total Aktiva Lancar</span>
                        <div class="avatar avatar-sm bg-label-primary rounded">
                            <i class="mdi mdi-lightning-bolt fs-5 text-primary"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-primary mb-1">Rp {{ number_format($totalLancar, 0, ',', '.') }}</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        Kas, Piutang (Rp {{ number_format($piutang, 0, ',', '.') }}), Persediaan (Rp {{ number_format($asset, 0, ',', '.') }})
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Aktiva Tetap Net --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border-left: 4px solid #71dd37 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Aktiva Tetap (Net)</span>
                        <div class="avatar avatar-sm bg-label-success rounded">
                            <i class="mdi mdi-office-building fs-5 text-success"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-success mb-1">Rp {{ number_format($totalTetap, 0, ',', '.') }}</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        Perolehan: Rp {{ number_format($totalFixed ?? 0, 0, ',', '.') }} - Penys.: Rp {{ number_format($grandTotalPenyusutan ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Total Seluruh Aktiva --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                <div class="card-body p-3 text-white">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white-50 fw-semibold small text-uppercase">Total Seluruh Aktiva</span>
                        <div class="avatar avatar-sm bg-white bg-opacity-10 rounded text-white">
                            <i class="mdi mdi-scale-balance fs-5"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-white mb-1">Rp {{ number_format($totalAktiva, 0, ',', '.') }}</h5>
                    <div class="text-white-50 small" style="font-size: 11px;">
                        Aktiva Lancar + Aktiva Tetap Net
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Balance Sheet Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 report-preview">
            @include('pages.finance.balance._report', ['isPrintMode' => false])
        </div>
    </div>
@endsection

@push('after-script')
    <script>
        function setBalanceLayout(mode) {
            const colAktiva = document.getElementById('colAktiva');
            const colPasiva = document.getElementById('colPasiva');
            const btnSkontro = document.getElementById('btnModeSkontro');
            const btnStafel = document.getElementById('btnModeStafel');

            if (!colAktiva || !colPasiva) return;

            if (mode === 'stafel') {
                colAktiva.className = 'col-12 balance-col mb-4';
                colPasiva.className = 'col-12 balance-col';
                btnStafel.classList.add('active');
                btnSkontro.classList.remove('active');
                localStorage.setItem('reftech_balance_layout', 'stafel');
            } else {
                colAktiva.className = 'col-md-6 balance-col';
                colPasiva.className = 'col-md-6 balance-col';
                btnSkontro.classList.add('active');
                btnStafel.classList.remove('active');
                localStorage.setItem('reftech_balance_layout', 'skontro');
            }
        }

        // Restore layout preference if saved
        document.addEventListener('DOMContentLoaded', function() {
            const savedLayout = localStorage.getItem('reftech_balance_layout');
            if (savedLayout === 'stafel') {
                setBalanceLayout('stafel');
            }
        });
    </script>
@endpush
