<div class="table-responsive text-nowrap border rounded">
    <table class="table table-bordered datatable-sorder mb-0" id="{{ $tableId }}">
        <thead>
            <tr class="table-light">
                <th>No SO</th>
                <th>No PO</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Part Desc</th>
                <th class="text-center">Status</th>
                <th class="text-center">Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orderList as $order)
                <tr>
                    <td>
                        <a href="{{ $order->detail_route ?? route('pending-po.show', $order->id) }}" class="fw-semibold text-primary">
                            {{ $order->no_pending }}
                        </a>
                    </td>
                    <td>
                        {{ $order->no_po ?? '-' }}
                    </td>
                    <td>
                        <span class="text-muted">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d-m-Y') : '-' }}</span>
                    </td>
                    <td>{{ $order->company }}</td>
                    <td class="text-wrap" style="min-width: 250px;">{{ $order->title }}</td>
                    <td class="text-center">
                        @if ($order->status == 0)
                            <span class="badge bg-secondary">New PO</span>
                        @elseif ($order->status == 1)
                            <span class="badge bg-warning">On Check</span>
                        @elseif ($order->status == 2)
                            <span class="badge bg-info">Ready Stock</span>
                        @elseif ($order->status == 3)
                            <span class="badge bg-danger">Kurang</span>
                        @elseif ($order->status == 4)
                            <span class="badge bg-primary">Pre-delivery</span>
                        @elseif ($order->status == 5)
                            <span class="badge bg-info">Delivery Process</span>
                        @elseif ($order->status == 6)
                            <span class="badge bg-success">Done</span>
                        @elseif ($order->status == 8)
                            <span class="badge bg-warning">Return</span>
                        @elseif ($order->status == 9)
                            <span class="badge bg-danger">Delayed</span>
                        @else
                            <span class="badge bg-primary">In Progress</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="avatar avatar-sm d-inline-block" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="{{ $order->sales_name }}">
                            <img src="{{ $order->sales_image ? asset($order->sales_image) : asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
