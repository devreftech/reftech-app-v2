<div class="table-responsive">
    <table class="table table-hover align-middle datatable-project mb-0 w-100" id="{{ $tableId }}">
        <thead class="table-light">
            <tr>
                <th class="col-cb" style="width: 36px;">
                    <input type="checkbox" class="form-check-input bulk-check-all" data-table="{{ $tableId }}" title="Pilih Semua">
                </th>
                <th class="text-uppercase fw-bold text-secondary text-nowrap col-so-po" style="font-size: 0.75rem; letter-spacing: 0.5px;">SO & PO</th>
                <th class="text-uppercase fw-bold text-secondary text-nowrap col-date" style="font-size: 0.75rem; letter-spacing: 0.5px;">Date</th>
                <th class="text-uppercase fw-bold text-secondary col-customer" style="font-size: 0.75rem; letter-spacing: 0.5px;">Customer</th>
                <th class="text-uppercase fw-bold text-secondary text-nowrap col-area" style="font-size: 0.75rem; letter-spacing: 0.5px;">Area</th>
                <th class="text-center text-uppercase fw-bold text-secondary col-desc" style="font-size: 0.75rem; letter-spacing: 0.5px;">Item</th>
                <th class="text-center text-uppercase fw-bold text-secondary text-nowrap col-status" style="font-size: 0.75rem; letter-spacing: 0.5px;">Status</th>
                <th class="text-center text-uppercase fw-bold text-secondary text-nowrap col-sales" style="font-size: 0.75rem; letter-spacing: 0.5px;">Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projectList as $project)
                <tr class="table-row-hover bulk-row" data-id="{{ $project->id }}" data-status="{{ $project->status ?? 0 }}" data-no-pending="{{ $project->no_pending }}" data-parent-id="{{ $project->parent_id ?? '' }}" data-client="{{ $project->company ?? '-' }}">
                    <td class="col-cb" style="width:36px;">
                        <input type="checkbox" class="form-check-input bulk-row-check" value="{{ $project->id }}" data-no="{{ $project->no_pending }}" data-client="{{ $project->company ?? '-' }}" title="Pilih">
                    </td>
                    <td class="col-so-po">
                        <div class="d-flex align-items-center flex-wrap gap-1">
                            <a href="{{ $project->detail_route ?? route('project-monitoring.show', $project->id) }}" class="fw-semibold text-primary d-inline-block text-truncate text-decoration-none" style="max-width: 140px; font-size: 0.85rem;" title="{{ $project->no_pending }}">
                                {{ $project->no_pending }}
                            </a>
                            @if (!empty($project->is_parent))
                                <span class="badge bg-label-info px-1 py-0 rounded cursor-pointer view-linked-group" data-id="{{ $project->id }}" data-bs-toggle="tooltip" title="SO Induk: {{ $project->linked_children_count }} SO terkait (Klik untuk detail)">
                                    <i class="mdi mdi-link-variant" style="font-size: 0.75rem;"></i> +{{ $project->linked_children_count }}
                                </span>
                            @elseif (!empty($project->is_child))
                                <span class="badge bg-label-secondary px-1 py-0 rounded cursor-pointer view-linked-group" data-id="{{ $project->id }}" data-bs-toggle="tooltip" title="Terkait ke: {{ $project->parent_no_pending ?? 'SO Induk' }} (Klik untuk detail)">
                                    <i class="mdi mdi-link-variant" style="font-size: 0.75rem;"></i> Linked
                                </span>
                            @endif
                        </div>
                        @if (!empty($project->no_po) && $project->no_po !== '-')
                            <span class="text-muted d-block small text-truncate" style="max-width: 165px; font-size: 0.78rem;" title="PO: {{ $project->no_po }}">
                                PO: {{ $project->no_po }}
                            </span>
                        @endif
                    </td>
                    <td data-order="{{ $project->date_timestamp ?? 0 }}" class="text-nowrap col-date">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-calendar-blank-outline me-1 text-muted" style="font-size: 0.95rem;"></i>
                            <span class="fw-medium text-body small">{{ $project->formatted_date ?? ($project->date ? date('d-m-Y', strtotime($project->date)) : '-') }}</span>
                        </div>
                    </td>
                    <td class="col-customer">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xs me-2 flex-shrink-0">
                                <span class="avatar-initial rounded-circle bg-label-secondary text-primary fw-bold" style="font-size: 0.65rem;">
                                    {{ strtoupper(substr($project->company ?? 'C', 0, 2)) }}
                                </span>
                            </div>
                            <span class="fw-semibold text-dark text-truncate d-block" style="max-width: 220px;" title="{{ $project->company }}">
                                {{ $project->company }}
                            </span>
                        </div>
                    </td>
                    <td class="text-nowrap col-area">
                        @if(!empty($project->area) && $project->area !== '-')
                            <span class="badge bg-label-secondary text-body px-2 py-1 rounded-pill">
                                <i class="mdi mdi-map-marker-outline text-danger me-1"></i>{{ $project->area }}
                            </span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td class="text-center col-desc">
                        @php
                            $itemCount = !empty($project->items_list) ? count($project->items_list) : 0;
                        @endphp
                        @if ($itemCount > 0)
                            <button type="button" class="btn-item-preview d-inline-flex align-items-center gap-1 btn-so-item-preview" 
                                data-id="{{ $project->id }}"
                                data-bs-toggle="tooltip" title="Klik untuk membuka slide rincian item">
                                <i class="mdi mdi-package-variant-closed font-14 text-primary"></i>
                                <span class="fw-semibold">{{ $itemCount }} item</span>
                            </button>
                            <span class="d-none search-text">{{ $project->items_search_text ?? $project->title }}</span>
                        @else
                            <span class="text-secondary small d-block" style="line-height: 1.4;" title="{{ $project->title ?? '-' }}">
                                {{ $project->title ?? '-' }}
                            </span>
                        @endif
                    </td>
                    <td class="text-center text-nowrap col-status">
                        <span class="badge {{ $project->progress_badge ?? 'bg-label-primary' }} rounded-pill px-2 py-1 d-inline-flex align-items-center">
                            <span class="status-dot me-1"></span>
                            {{ $project->progress_label ?? 'In Progress' }}
                        </span>
                    </td>
                    <td class="text-center text-nowrap col-sales">
                        <div class="avatar avatar-xs d-inline-block position-relative" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="{{ $project->sales_name }}">
                            <img src="{{ $project->sales_avatar ?? ($project->sales_image ? asset($project->sales_image) : asset('assets/img/avatars/1.png')) }}" alt="Avatar" class="rounded-circle shadow-xs" style="width: 30px; height: 30px; object-fit: cover; border: 1.5px solid #fff;">
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
