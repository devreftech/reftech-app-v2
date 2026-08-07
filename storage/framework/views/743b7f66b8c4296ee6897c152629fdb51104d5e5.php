
<?php $__env->startSection('title', 'Selling Contract'); ?>
<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Accounting /</span> Selling Contract
    </h4>

    <div class="card">
        <div class="card-header py-2">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="contract-tab-nav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-request" type="button">
                        <i class="mdi mdi-file-clock-outline me-1"></i>Request
                        <?php if($requestContract >= 1): ?>
                            <span class="badge rounded-pill bg-danger ms-1"><?php echo e($requestContract); ?></span>
                        <?php endif; ?>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-selling" type="button">
                        <i class="mdi mdi-file-sign me-1"></i>Selling Contract
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-order" type="button">
                        <i class="mdi mdi-file-check-outline me-1"></i>Confirm Order
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content p-0">

            
            <div class="tab-pane fade show active" id="tab-request" role="tabpanel">
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <label class="form-label mb-0 fw-medium">Tahun:</label>
                    <select id="filter-year-request" class="form-select form-select-sm" style="width:auto">
                        <option value="all">Semua</option>
                        <?php for($y = now()->year; $y >= 2022; $y--): ?>
                            <option value="<?php echo e($y); ?>" <?php echo e($y == now()->year ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-request-contract table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>No. Contract</th>
                                <th>Company</th>
                                <th>Total Price</th>
                                <th>Date</th>
                                <th>Sales</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            
            <div class="tab-pane fade" id="tab-selling" role="tabpanel">
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <label class="form-label mb-0 fw-medium">Tahun:</label>
                    <select id="filter-year-selling" class="form-select form-select-sm" style="width:auto">
                        <option value="all">Semua</option>
                        <?php for($y = now()->year; $y >= 2022; $y--): ?>
                            <option value="<?php echo e($y); ?>" <?php echo e($y == now()->year ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                        <?php endfor; ?>
                    </select>
                    <label class="form-label mb-0 fw-medium ms-2">PPN:</label>
                    <select id="filter-tax-selling" class="form-select form-select-sm" style="width:auto">
                        <option value="all">Semua</option>
                        <option value="ppn">PPN</option>
                        <option value="non-ppn">Non PPN</option>
                    </select>
                </div>
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-selling-contract-tab table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Selling No.</th>
                                <th>Company</th>
                                <th>Total Price</th>
                                <th>Date</th>
                                <th>Sales</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            
            <div class="tab-pane fade" id="tab-order" role="tabpanel">
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <label class="form-label mb-0 fw-medium">Tahun:</label>
                    <select id="filter-year-order" class="form-select form-select-sm" style="width:auto">
                        <option value="all">Semua</option>
                        <?php for($y = now()->year; $y >= 2022; $y--): ?>
                            <option value="<?php echo e($y); ?>" <?php echo e($y == now()->year ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                        <?php endfor; ?>
                    </select>
                    <label class="form-label mb-0 fw-medium ms-2">PPN:</label>
                    <select id="filter-tax-order" class="form-select form-select-sm" style="width:auto">
                        <option value="all">Semua</option>
                        <option value="ppn">PPN</option>
                        <option value="non-ppn">Non PPN</option>
                    </select>
                </div>
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-confirm-order-tab table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Order No.</th>
                                <th>Company</th>
                                <th>Total Price</th>
                                <th>Date</th>
                                <th>Sales</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>

    
    <?php $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($contract->id_unit_quotation): ?>
            <?php $result = $formattedNumberSC ?? str_pad(1, 3, '0', STR_PAD_LEFT); ?>
            <?php echo $__env->make('components.modal.accounting.accept-contract-unit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php else: ?>
            <?php
                $result = '';
                if ($contract->type == 'Selling' && $contract->quotation?->tax == '0') {
                    $sellingNonTax = $contract;
                } elseif ($contract->type == 'Selling' && $contract->quotation?->tax == '11') {
                    $sellingTax = $contract;
                } elseif ($contract->type == 'Order' && $contract->quotation?->tax == '0') {
                    $orderNonTax = $contract;
                } elseif ($contract->type == 'Order' && $contract->quotation?->tax == '11') {
                    $orderTax = $contract;
                }
                if (isset($sellingTax))      $result = $formattedNumberSP;
                elseif (isset($sellingNonTax)) $result = $formattedNumberSNP;
                elseif (isset($orderTax))      $result = $formattedNumberCP;
                elseif (isset($orderNonTax))   $result = $formattedNumberCNP;
            ?>
            <?php echo $__env->make('components.modal.accounting.accept-contract', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-request-contract.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-selling-contract-tab.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-confirm-order-tab.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/accounting/contract/index.blade.php ENDPATH**/ ?>