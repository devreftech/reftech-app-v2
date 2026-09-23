@extends('layouts.sales.app')
@section('title', 'Developer Command Center')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card clean-card mb-4 p-3" style="border-color: rgba(99, 102, 241, 0.35); background: #ffffff;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-dark fs-7 px-3 py-2 rounded-pill">
                        <i class="mdi mdi-code-tags-check me-1 text-primary"></i> Developer Mode
                    </span>
                    <small class="text-muted fw-semibold d-none d-sm-inline">Sudut Pandang Sistem &amp; Telemetry</small>
                </div>
                <div class="d-flex flex-wrap gap-1">
                    <a href="{{ url('/?view=sales') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 waves-effect">
                        <i class="mdi mdi-chart-line me-1"></i> Business / Sales
                    </a>
                    <a href="{{ route('developer.maintenance.index') }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 waves-effect">
                        <i class="mdi mdi-shield-lock-outline me-1"></i> Maintenance Mode
                    </a>
                    <a href="{{ route('developer.mailbox.index') }}" class="btn btn-sm btn-outline-info rounded-pill px-3 waves-effect">
                        <i class="mdi mdi-email-sync-outline me-1"></i> Mailbox
                    </a>
                </div>
            </div>
        </div>

        @include('pages.developer.dashboard_content')
    </div>
@endsection
