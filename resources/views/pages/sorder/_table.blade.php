<div class="table-responsive">
    <table class="table table-hover align-middle datatable-sorder mb-0 w-100" id="{{ $tableId }}">
        <thead class="table-light">
            <tr>
                <th class="col-cb" style="width: 36px;">
                    <input type="checkbox" class="form-check-input bulk-check-all" data-table="{{ $tableId }}" title="Pilih Semua">
                </th>
                <th class="text-uppercase fw-bold text-secondary text-nowrap col-so-po" style="font-size: 0.75rem; letter-spacing: 0.5px;">SO & PO</th>
                <th class="text-uppercase fw-bold text-secondary text-nowrap col-date" style="font-size: 0.75rem; letter-spacing: 0.5px;">Date</th>
                <th class="text-uppercase fw-bold text-secondary col-customer" style="font-size: 0.75rem; letter-spacing: 0.5px;">Customer</th>
                <th class="text-center text-uppercase fw-bold text-secondary col-desc" style="font-size: 0.75rem; letter-spacing: 0.5px;">Item</th>
                <th class="text-center text-uppercase fw-bold text-secondary text-nowrap col-status" style="font-size: 0.75rem; letter-spacing: 0.5px;">Status</th>
                <th class="text-center text-uppercase fw-bold text-secondary text-nowrap col-sales" style="font-size: 0.75rem; letter-spacing: 0.5px;">Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orderList as $order)
                <tr class="table-row-hover bulk-row" data-id="{{ $order->id }}" data-status="{{ $order->status ?? 0 }}" data-no-pending="{{ $order->no_pending }}" data-parent-id="{{ $order->parent_id ?? '' }}" data-client="{{ $order->company ?? '-' }}">
                    <td class="col-cb" style="width:36px;">
                        <input type="checkbox" class="form-check-input bulk-row-check" value="{{ $order->id }}" data-no="{{ $order->no_pending }}" data-client="{{ $order->company ?? '-' }}" title="Pilih">
                    </td>
                    <td class="col-so-po">
                        <div class="d-flex align-items-center flex-wrap gap-1">
                            <a href="{{ $order->detail_route ?? route('pending-po.show', $order->id) }}" class="fw-semibold text-primary d-inline-block text-truncate text-decoration-none" style="max-width: 140px; font-size: 0.85rem;" title="{{ $order->no_pending }}">
                                {{ $order->no_pending }}
                            </a>
                            @if (!empty($order->is_parent))
                                <span class="badge bg-label-info px-1 py-0 rounded cursor-pointer view-linked-group" data-id="{{ $order->id }}" data-bs-toggle="tooltip" title="SO Induk: {{ $order->linked_children_count }} SO terkait (Klik untuk detail)">
                                    <i class="mdi mdi-link-variant" style="font-size: 0.75rem;"></i> +{{ $order->linked_children_count }}
                                </span>
                            @elseif (!empty($order->is_child))
                                <span class="badge bg-label-secondary px-1 py-0 rounded cursor-pointer view-linked-group" data-id="{{ $order->id }}" data-bs-toggle="tooltip" title="Terkait ke: {{ $order->parent_no_pending ?? 'SO Induk' }} (Klik untuk detail)">
                                    <i class="mdi mdi-link-variant" style="font-size: 0.75rem;"></i> Linked
                                </span>
                            @endif
                        </div>
                        @if (!empty($order->no_po) && $order->no_po !== '-')
                            <span class="text-muted d-block small text-truncate" style="max-width: 165px; font-size: 0.78rem;" title="PO: {{ $order->no_po }}">
                                PO: {{ $order->no_po }}
                            </span>
                        @endif
                    </td>
                    <td data-order="{{ $order->date_timestamp }}" class="text-nowrap col-date">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-calendar-blank-outline me-1 text-muted" style="font-size: 0.95rem;"></i>
                            <span class="fw-medium text-body small">{{ $order->formatted_date }}</span>
                        </div>
                    </td>
                    <td class="col-customer">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xs me-2 flex-shrink-0">
                                <span class="avatar-initial rounded-circle bg-label-secondary text-primary fw-bold" style="font-size: 0.65rem;">
                                    {{ strtoupper(substr($order->company ?? 'C', 0, 2)) }}
                                </span>
                            </div>
                            <span class="fw-semibold text-dark text-truncate d-block" style="max-width: 240px;" title="{{ $order->company }}">
                                {{ $order->company }}
                            </span>
                        </div>
                    </td>
                    <td class="text-center col-desc">
                        @php
                            $itemCount = !empty($order->items_list) ? count($order->items_list) : 0;
                        @endphp
                        @if ($itemCount > 0)
                            <button type="button" class="btn-item-preview d-inline-flex align-items-center gap-1 btn-so-item-preview" 
                                data-id="{{ $order->id }}"
                                data-bs-toggle="tooltip" title="Klik untuk membuka slide rincian item">
                                <i class="mdi mdi-package-variant-closed font-14 text-primary"></i>
                                <span class="fw-semibold">{{ $itemCount }} item</span>
                            </button>
                            <span class="d-none search-text">{{ $order->items_search_text ?? $order->title }}</span>
                        @else
                            <span class="text-secondary small d-block" style="line-height: 1.4;" title="{{ $order->title ?? '-' }}">
                                {{ $order->title ?? '-' }}
                            </span>
                        @endif
                    </td>
                    <td class="text-center text-nowrap col-status">
                        <span class="badge {{ $order->progress_badge ?? 'bg-label-primary' }} rounded-pill px-2 py-1 d-inline-flex align-items-center">
                            <span class="status-dot me-1"></span>
                            {{ $order->progress_label ?? 'New PO' }}
                        </span>
                    </td>
                    <td class="text-center text-nowrap col-sales">
                        <div class="avatar avatar-xs d-inline-block position-relative" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="{{ $order->sales_name }}">
                            <img src="{{ $order->sales_avatar }}" alt="Avatar" class="rounded-circle shadow-xs" style="width: 30px; height: 30px; object-fit: cover; border: 1.5px solid #fff;">
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
