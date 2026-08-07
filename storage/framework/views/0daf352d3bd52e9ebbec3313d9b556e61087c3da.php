<div class="table-responsive text-nowrap border rounded">
    <table class="table table-bordered datatable-project mb-0" id="<?php echo e($tableId); ?>">
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
            <?php $__currentLoopData = $projectList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <a href="<?php echo e($project->detail_route ?? route('project-monitoring.show', $project->id)); ?>" class="fw-semibold text-primary">
                            <?php echo e($project->no_pending); ?>

                        </a>
                    </td>
                    <td>
                        <?php echo e($project->no_po ?? '-'); ?>

                    </td>
                    <td>
                        <span class="text-muted"><?php echo e($project->order_date ? \Carbon\Carbon::parse($project->order_date)->format('d-m-Y') : '-'); ?></span>
                    </td>
                    <td><?php echo e($project->company); ?></td>
                    <td><?php echo e($project->area ?? '-'); ?></td>
                    <td class="text-wrap" style="min-width: 250px;"><?php echo e($project->title); ?></td>
                    <td class="text-center">
                        <?php if($project->status == 6): ?>
                            <span class="badge bg-success">Done</span>
                        <?php elseif($project->status == 0): ?>
                            <span class="badge bg-secondary">New</span>
                        <?php elseif($project->status == 9): ?>
                            <span class="badge bg-danger">Delayed</span>
                        <?php else: ?>
                            <?php
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
                            ?>
                            <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($statusLabel); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="avatar avatar-sm d-inline-block" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="<?php echo e($project->sales_name); ?>">
                            <img src="<?php echo e($project->sales_image ? asset($project->sales_image) : asset('assets/img/avatars/1.png')); ?>" alt="Avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/project-monitoring/_table.blade.php ENDPATH**/ ?>