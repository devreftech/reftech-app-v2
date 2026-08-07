
<?php $__env->startSection('title', 'Maintenance Log - ' . ($machine->unit->brand ?? '') . ' ' . ($machine->unit->unit->sku ?? '')); ?>
<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Maintenance Log /</span>
        <?php echo e($machine->unit->brand ?? ''); ?> <?php echo e($machine->unit->unit->sku ?? ''); ?>

    </h4>

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center py-3">
            <div>
                <p class="mb-0 fw-semibold"><?php echo e($machine->unit->brand ?? '-'); ?> <?php echo e($machine->unit->unit->sku ?? ''); ?></p>
                <p class="mb-0 text-muted" style="font-size:13px;">
                    S/N: <?php echo e($machine->serial ?? '-'); ?>

                    <?php if($machine->tag): ?> &nbsp;|&nbsp; Tag: <?php echo e($machine->tag); ?> <?php endif; ?>
                    <?php if($machine->location): ?> &nbsp;|&nbsp; <?php echo e($machine->location); ?> <?php endif; ?>
                </p>
                <p class="mb-0 text-muted" style="font-size:13px;">
                    Client: <?php echo e($machine->client->company ?? '-'); ?>

                </p>
            </div>
            <a href="<?php echo e(route('service-reports.machine.create', $machine->id)); ?>" class="btn btn-primary waves-effect">
                <i class="mdi mdi-plus me-1"></i> Create
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css"/>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css"/>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
<script>
$(function () {
    $('.datatable-machine-history').DataTable({
        ajax: {
            type: 'GET',
            url: '/db/service-reports/machine/<?php echo e($machine->id); ?>'
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/technician/service-reports/machine-index.blade.php ENDPATH**/ ?>