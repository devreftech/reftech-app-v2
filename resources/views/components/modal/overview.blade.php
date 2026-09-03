@php
    // Modal Info pada card Sales Overview (dashboard admin).
    // Isi "Rekap KPI Mingguan" dimuat lewat AJAX saat modal dibuka
    // (lihat handler show.bs.modal di pages/sales/dashboard.blade.php).
    $wkDate = \Carbon\Carbon::now()->format('m-Y');
@endphp
<div class="modal animate__animated animate__fadeIn" id="overview-sales-{{ $overview['salesId'] }}" tabindex="-1"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mb-0">Rekap KPI Mingguan &mdash; {{ $overview['sales'] }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="weekly-kpi-mount"
                    data-url="{{ route('detail-overview.weekly-kpi', ['sales' => $overview['salesId'], 'date' => $wkDate]) }}">
                    <div class="text-center text-muted py-5">
                        <div class="spinner-border text-primary mb-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div>Memuat rekap KPI mingguan&hellip;</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
