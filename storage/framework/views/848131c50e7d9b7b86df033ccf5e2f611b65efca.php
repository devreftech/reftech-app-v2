<?php $__env->startSection('title', 'BAST'); ?>
<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center py-3 mb-1">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Accounting /</span> BAST
        </h4>
        <button type="button" class="btn btn-primary btn-sm" id="btnCreateBast">
            <i class="mdi mdi-plus"></i> Buat BAST Manual
        </button>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="table-responsive">
                    <table class="table table-striped mb-0" id="bastTable">
                        <thead>
                            <tr>
                                <th>No. BAST</th>
                                <th>Customer</th>
                                <th>Pekerjaan</th>
                                <th>Tgl Pekerjaan</th>
                                <th>No. PO/Kontrak</th>
                                <th>Dibuat oleh</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $basts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bast): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo e($bast->no_bast); ?></td>
                                    <td><?php echo e($bast->customer_name); ?></td>
                                    <td><?php echo e($bast->work_title); ?></td>
                                    <td><?php echo e($bast->work_date->format('d-m-Y')); ?></td>
                                    <td><?php echo e($bast->po_number ?: '-'); ?></td>
                                    <td><?php echo e($bast->creator->name ?? '-'); ?></td>
                                    <td class="text-end">
                                        <a href="<?php echo e(route('bast.print', $bast->id)); ?>" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-printer-outline"></i> Print
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-bast"
                                            data-id="<?php echo e($bast->id); ?>">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-bast"
                                            data-id="<?php echo e($bast->id); ?>" data-no="<?php echo e($bast->no_bast); ?>">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('components.modal.bast.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable) {
                $('#bastTable').DataTable({
                    order: [],
                    columnDefs: [{
                        orderable: false,
                        targets: -1
                    }],
                    language: {
                        emptyTable: 'Belum ada BAST yang dibuat.',
                        zeroRecords: 'Data tidak ditemukan.'
                    },
                });
            }
        });

        $('#btnCreateBast').on('click', function() {
            window.openBastModal({});
        });

        $(document).on('click', '.btn-edit-bast', function() {
            const id = $(this).data('id');
            $.get(`<?php echo e(url('/bast')); ?>/${id}/edit-data`, function(response) {
                const b = response.bast;
                window.openBastModal({
                    bastId: b.id,
                    entity: b.entity,
                    customerName: b.customer_name,
                    workTitle: b.work_title,
                    poNumber: b.po_number,
                    workDate: b.work_date,
                    testRunningResult: b.test_running_result,
                    units: b.units,
                });
            });
        });

        $(document).on('click', '.btn-delete-bast', function() {
            const id = $(this).data('id');
            const no = $(this).data('no');
            if (!confirm(`Hapus BAST ${no}? Tindakan ini tidak bisa dibatalkan.`)) return;

            $.ajax({
                url: `<?php echo e(url('/bast')); ?>/${id}`,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '<?php echo e(csrf_token()); ?>'
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function() {
                    window.location.reload();
                }
            });
        });

        $(document).on('bast:saved', function() {
            window.location.reload();
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/accounting/bast/index.blade.php ENDPATH**/ ?>