<div class="table-responsive text-nowrap border rounded">
    <table class="table table-bordered datatable-sorder mb-0" id="<?php echo e($tableId); ?>">
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
            <?php $__currentLoopData = $orderList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <a href="<?php echo e($order->detail_route ?? route('pending-po.show', $order->id)); ?>" class="fw-semibold text-primary">
                            <?php echo e($order->no_pending); ?>

                        </a>
                    </td>
                    <td>
                        <?php echo e($order->no_po ?? '-'); ?>

                    </td>
                    <td>
                        <span class="text-muted"><?php echo e($order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d-m-Y') : '-'); ?></span>
                    </td>
                    <td><?php echo e($order->company); ?></td>
                    <td class="text-wrap" style="min-width: 250px;"><?php echo e($order->title); ?></td>
                    <td class="text-center">
                        <?php if($order->status == 0): ?>
                            <span class="badge bg-secondary">New PO</span>
                        <?php elseif($order->status == 1): ?>
                            <span class="badge bg-warning">On Check</span>
                        <?php elseif($order->status == 2): ?>
                            <span class="badge bg-info">Ready Stock</span>
                        <?php elseif($order->status == 3): ?>
                            <span class="badge bg-danger">Kurang</span>
                        <?php elseif($order->status == 4): ?>
                            <span class="badge bg-primary">Pre-delivery</span>
                        <?php elseif($order->status == 5): ?>
                            <span class="badge bg-info">Delivery Process</span>
                        <?php elseif($order->status == 6): ?>
                            <span class="badge bg-success">Done</span>
                        <?php elseif($order->status == 8): ?>
                            <span class="badge bg-warning">Return</span>
                        <?php elseif($order->status == 9): ?>
                            <span class="badge bg-danger">Delayed</span>
                        <?php else: ?>
                            <span class="badge bg-primary">In Progress</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="avatar avatar-sm d-inline-block" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="<?php echo e($order->sales_name); ?>">
                            <img src="<?php echo e($order->sales_image ? asset($order->sales_image) : asset('assets/img/avatars/1.png')); ?>" alt="Avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sorder/_table.blade.php ENDPATH**/ ?>