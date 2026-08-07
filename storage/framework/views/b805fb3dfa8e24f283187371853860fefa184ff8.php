<?php $__env->startSection('title', 'Ongkir Logistik'); ?>
<?php $__env->startSection('content'); ?>
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-expense-ongkir table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>No Pending</th>
                        <th>Title</th>
                        <th>Kurir</th>
                        <th>No Track</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <form id="formPostOngkir" method="post" action="">
        <?php echo csrf_field(); ?>
        <div class="modal fade" id="postOngkirModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Posting Ongkir ke Finance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3" id="ongkir-info-text"></p>
                        <div class="form-floating form-floating-outline mb-3">
                            <select class="form-select" id="id_bank" name="id_bank" required>
                                <option value="">---- Pilih Kas/Bank ----</option>
                                <?php $__currentLoopData = $bank; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($b->id); ?>"><?php echo e($b->bank); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <label for="id_bank">Sumber Kas/Bank</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-3">
                            <select class="form-select" id="id_account" name="id_account" required>
                                <option value="">---- Pilih Account ----</option>
                                <?php $__currentLoopData = $account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($a->id); ?>"><?php echo e($a->code); ?> - <?php echo e($a->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <label for="id_account">Kategori Beban (Account)</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-3">
                            <input type="text" class="form-control" id="memo" name="memo" placeholder="Memo">
                            <label for="memo">Memo</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Posting</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-advanced.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-expense-ongkir.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/expense/index-ongkir.blade.php ENDPATH**/ ?>