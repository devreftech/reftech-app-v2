<?php $__env->startSection('title', 'Invoice'); ?>
<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Accounting /</span> Invoice
    </h4>

    <div class="card">
        <div class="card-header py-2">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="invoice-tab-nav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab == 'request' ? 'active' : ''); ?>" data-bs-toggle="tab"
                        data-bs-target="#tab-invoice-request" type="button">
                        <i class="mdi mdi-file-document-edit-outline me-1"></i>Request
                        <span class="badge rounded-pill bg-danger ms-1" id="badge-invoice-request">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab == 'reftech' ? 'active' : ''); ?>" data-bs-toggle="tab"
                        data-bs-target="#tab-invoice-reftech" type="button">
                        <i class="mdi mdi-file-document-outline me-1"></i>Invoice Reftech
                        <span class="badge rounded-pill bg-primary ms-1" id="badge-invoice-reftech">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab == 'kojisha' ? 'active' : ''); ?>" data-bs-toggle="tab"
                        data-bs-target="#tab-invoice-kojisha" type="button">
                        <i class="mdi mdi-file-document-multiple-outline me-1"></i>Invoice Kojisha
                        <span class="badge rounded-pill bg-info ms-1" id="badge-invoice-kojisha">-</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content">

                
                <div class="tab-pane fade <?php echo e($activeTab == 'request' ? 'show active' : ''); ?>" id="tab-invoice-request">
                    <div class="table-responsive">
                        <table id="tbl-invoice-request" class="datatable-request-invoice table table-bordered"
                            data-badge="badge-invoice-request">
                            <thead>
                                <tr>
                                    <th class="text-center">Quotation No.</th>
                                    <th class="text-center">No PO</th>
                                    <th class="text-center">Company</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Total Price</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Sales</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade <?php echo e($activeTab == 'reftech' ? 'show active' : ''); ?>" id="tab-invoice-reftech">
                    <div class="table-responsive">
                        <table id="tbl-invoice-reftech" class="datatable-invoice-reftech table table-bordered"
                            data-badge="badge-invoice-reftech">
                            <thead>
                                <tr>
                                    <th class="text-center">Invoice No.</th>
                                    <th class="text-center">No PO</th>
                                    <th class="text-center">Company</th>
                                    <th class="text-center">PPN</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Total Invoice</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Sales</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade <?php echo e($activeTab == 'kojisha' ? 'show active' : ''); ?>" id="tab-invoice-kojisha">
                    <div class="table-responsive">
                        <table id="tbl-invoice-kojisha" class="datatable-invoice-kojisha table table-bordered"
                            data-badge="badge-invoice-kojisha">
                            <thead>
                                <tr>
                                    <th class="text-center">Invoice No.</th>
                                    <th class="text-center">No PO</th>
                                    <th class="text-center">Company</th>
                                    <th class="text-center">PPN</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Total Invoice</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Sales</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
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
    <script src="<?php echo e(asset('assets')); ?>/includes/table-request-po.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-invoice-reftech.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-invoice-kojisha.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        $('#invoice-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
        });

        // Badge count per tab
        $(document).on('draw.dt', function (e) {
            var $tbl = $(e.target);
            var badgeId = $tbl.data('badge');
            if (!badgeId) return;
            var api = $tbl.DataTable();
            $('#' + badgeId).text(api.page.info().recordsTotal);
        });

        // Re-init tooltip (avatar Sales) tiap redraw ajax
        $(document).on('draw.dt', function (e) {
            $(e.target).find('[data-bs-toggle="tooltip"]').tooltip();
        });

        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/accounting/invoice/index.blade.php ENDPATH**/ ?>