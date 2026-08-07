<div class="table-responsive text-nowrap border rounded">
    <table class="table table-bordered datatable-project mb-0" id="{{ $tableId }}">
        <thead>
            <tr class="table-light">
                <th>No SO</th>
                <th>No PO</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Area</th>
                <th>Title / Project Name</th>
                <th class="text-center">Status</th>
                <th class="text-center">Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projectList as $project)
                <tr>
                    <td>
                        <a href="{{ $project->detail_route ?? route('project-monitoring.show', $project->id) }}" class="fw-semibold text-primary">
                            {{ $project->no_pending }}
                        </a>
                    </td>
                    <td>
                        {{ $project->no_po ?? '-' }}
                    </td>
                    <td>
                        <span class="text-muted">{{ $project->order_date ? \Carbon\Carbon::parse($project->order_date)->format('d-m-Y') : '-' }}</span>
                    </td>
                    <td>{{ $project->company }}</td>
                    <td>{{ $project->area ?? '-' }}</td>
                    <td class="text-wrap" style="min-width: 250px;">{{ $project->title }}</td>
                    <td class="text-center">
                        @if ($project->status == 6)
                            <span class="badge bg-success">Done</span>
                        @elseif($project->status == 0)
                            <span class="badge bg-secondary">New</span>
                        @elseif($project->status == 9)
                            <span class="badge bg-danger">Delayed</span>
                        @else
                            @php
                                $step = $project->project_status_step ?? 1;
                                $cat = $project->project_category ?? 'Service PM';

                                $statusLabel = 'In Progress';
                                $badgeClass = 'bg-primary';

                                if ($step == 1) {
                                    $badgeClass = 'bg-warning';
                                    if (in_array($cat, ['Rental', 'Unit'])) {
                                        $statusLabel = 'Check Unit';
                                    } elseif ($cat === 'Piping') {
                                        $statusLabel = 'Check Material';
                                    } else {
                                        $statusLabel = 'Check Parts';
                                    }
                                } elseif ($step == 2) {
                                    $badgeClass = 'bg-info';
                                    if (in_array($cat, ['Rental', 'Unit'])) {
                                        $statusLabel = 'Jadwal Pickup';
                                    } elseif ($cat === 'Piping') {
                                        $statusLabel = 'Kirim Material';
                                    } else {
                                        $statusLabel = 'Waiting Schedule';
                                    }
                                } elseif ($step == 3) {
                                    if ($cat === 'Rental') {
                                        $statusLabel = 'Commissioning';
                                    } elseif ($cat === 'Unit') {
                                        $statusLabel = 'Jadwal Commissioning';
                                    } else {
                                        $statusLabel = 'In Progress';
                                    }
                                } elseif ($step == 4) {
                                    if ($cat === 'Rental') {
                                        $statusLabel = 'Pickup Kembali Unit';
                                    } elseif ($cat === 'Piping') {
                                        $statusLabel = 'Commissioning';
                                    } else {
                                        $statusLabel = 'In Progress';
                                    }
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="avatar avatar-sm d-inline-block" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="{{ $project->sales_name }}">
                            <img src="{{ $project->sales_image ? asset($project->sales_image) : asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
