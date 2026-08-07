@extends('layouts.sales.app')
@section('title', 'Maintenance Log - ' . ($machine->unit->brand ?? '') . ' ' . ($machine->unit->unit->sku ?? ''))
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Maintenance Log /</span>
        {{ $machine->unit->brand ?? '' }} {{ $machine->unit->unit->sku ?? '' }}
    </h4>

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center py-3">
            <div>
                <p class="mb-0 fw-semibold">{{ $machine->unit->brand ?? '-' }} {{ $machine->unit->unit->sku ?? '' }}</p>
                <p class="mb-0 text-muted" style="font-size:13px;">
                    S/N: {{ $machine->serial ?? '-' }}
                    @if ($machine->tag) &nbsp;|&nbsp; Tag: {{ $machine->tag }} @endif
                    @if ($machine->location) &nbsp;|&nbsp; {{ $machine->location }} @endif
                </p>
                <p class="mb-0 text-muted" style="font-size:13px;">
                    Client: {{ $machine->client->company ?? '-' }}
                </p>
            </div>
            <a href="{{ route('service-reports.machine.create', $machine->id) }}" class="btn btn-primary waves-effect">
                <i class="mdi mdi-plus me-1"></i> create
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-machine-history table table-striped">
                <thead>
                    <tr>
                        <th>Service Report</th>
                        <th>Service Type</th>
                        <th>Job Description</th>
                        <th>Date</th>
                        <th>Technician</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css"/>
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css"/>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('script')
<script>
$(function () {
    $('.datatable-machine-history').DataTable({
        ajax: {
            type: 'GET',
            url: '/db/service-reports/machine/{{ $machine->id }}'
        },
        columns: [
            { data: 'no_service' },
            { data: 'type' },
            { data: 'jobdesc' },
            { data: 'date' },
            { data: 'technician' },
        ],
        columnDefs: [
            {
                targets: 0,
                render: function (data, type, full) {
                    var url = route('service-reports.show', full.id);
                    return '<a href="' + url + '" class="fw-semibold text-primary">' + (data ?? '-') + '</a>';
                }
            },
            {
                targets: 2,
                render: function (data) {
                    if (!data) return '-';
                    return data.length > 60
                        ? '<span title="' + data + '">' + data.substring(0, 60) + '...</span>'
                        : data;
                }
            },
            {
                targets: 3,
                className: 'text-center',
                render: function (data) {
                    if (!data) return '-';
                    var d = new Date(data);
                    return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear();
                }
            },
        ],
        order: [[3, 'desc']],
        dom:
            '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
            '<"table-responsive"t>' +
            '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    });
});
</script>
@endpush
