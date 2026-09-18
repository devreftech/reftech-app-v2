@php
    $myPendingAudit = null;
    if (Auth::check()) {
        try {
            $myPendingAudit = \App\Models\ToolAudit::with('period')
                ->where('id_technician', Auth::id())
                ->whereIn('status_submit', ['Draft', 'Rejected'])
                ->whereHas('period', function ($q) {
                    $q->where('status', 'Open');
                })
                ->latest('id')
                ->first();
        } catch (\Throwable $e) {
            $myPendingAudit = null;
        }
    }
@endphp

@if ($myPendingAudit && !request()->is('tool-audit*'))
    <div class="container-fluid px-3 px-md-4 pt-3 pb-0" id="tool-audit-alert-banner">
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-2 py-2 px-3 mb-0"
            role="alert"
            style="border-left: 4px solid #ffab00 !important; background: linear-gradient(135deg, rgba(255, 171, 0, 0.18) 0%, rgba(255, 171, 0, 0.08) 100%);">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-warning text-dark text-uppercase font-11 fw-bold d-inline-flex align-items-center">
                    <i class="mdi mdi-tools me-1"></i> Self-Audit Tools
                </span>
                <span class="font-13 text-heading">
                    Periode <strong>{{ $myPendingAudit->period->period_title }}</strong> sedang berlangsung (Batas akhir: <strong class="text-danger">{{ \Carbon\Carbon::parse($myPendingAudit->period->tanggal_selesai)->format('d M Y') }}</strong>).
                    <span class="text-muted ms-1 d-none d-md-inline">Terdapat <strong>{{ $myPendingAudit->total_tools }} tools aktif</strong> yang wajib Anda laporkan kondisi fisiknya.</span>
                </span>
            </div>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <a href="{{ route('tool-audit.show', $myPendingAudit->id) }}" class="btn btn-sm btn-primary waves-effect px-3 py-1 font-12 fw-semibold shadow-xs">
                    <i class="mdi mdi-clipboard-edit-outline me-1"></i> Mulai Isi Audit
                </a>
                <button type="button" class="btn-close position-relative p-1 ms-1" data-bs-dismiss="alert" aria-label="Close" style="top: 0; right: 0;"></button>
            </div>
        </div>
    </div>
@endif
