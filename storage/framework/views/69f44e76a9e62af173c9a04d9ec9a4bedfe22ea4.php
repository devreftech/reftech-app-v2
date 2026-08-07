
<?php $__env->startSection('title', 'Sales Invoice AR'); ?>
<?php $__env->startSection('no-container'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-0" style="width: calc(100% - 10px); margin-right:5px;margin-left:5px;">
        <div class="d-flex flex-wrap align-items-center justify-content-between py-3 mb-2">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-normal">Account Recieveable /</span> Sales Invoice</h4>
            <div class="d-flex align-items-center gap-2">
                <label class="form-label mb-0 text-muted" style="white-space:nowrap;">Filter Tahun:</label>
                <select class="form-select form-select-sm" id="invoice-year-filter" style="max-width:150px;">
                    <option value="all">Semua Tahun</option>
                    <?php for($y = now()->year; $y >= 2022; $y--): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e($y == now()->year ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-2">
                <ul class="nav nav-tabs card-header-tabs border-0 m-0 flex-nowrap overflow-auto" id="invoice-ar-tab-nav" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" id="nav-inv-general" role="tab"
                            data-bs-toggle="tab" data-bs-target="#navs-pills-top-general" aria-controls="navs-pills-top-general"
                            aria-selected="true">
                            <i class="mdi mdi-view-list-outline me-1"></i>General
                            <span class="badge rounded-pill bg-primary ms-1" id="badge-inv-general">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" id="nav-inv-reftech" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-reftech" aria-controls="navs-pills-top-reftech"
                            aria-selected="false" tabindex="-1">
                            <i class="mdi mdi-file-document-outline me-1"></i>Reftech
                            <span class="badge rounded-pill bg-info ms-1" id="badge-inv-reftech">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" id="nav-inv-kojisha" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-kojisha" aria-controls="navs-pills-top-kojisha"
                            aria-selected="false" tabindex="-1">
                            <i class="mdi mdi-file-document-multiple-outline me-1"></i>Kojisha
                            <span class="badge rounded-pill bg-info ms-1" id="badge-inv-kojisha">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" id="nav-inv-ahmad" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-ahmad" aria-controls="navs-pills-top-ahmad" aria-selected="false"
                            tabindex="-1">
                            <i class="mdi mdi-account-outline me-1"></i>Ahmad
                            <span class="badge rounded-pill bg-secondary ms-1" id="badge-inv-ahmad">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" id="nav-inv-rayi" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-rayi" aria-controls="navs-pills-top-rayi" aria-selected="false"
                            tabindex="-1">
                            <i class="mdi mdi-account-outline me-1"></i>Rayi
                            <span class="badge rounded-pill bg-secondary ms-1" id="badge-inv-rayi">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" id="nav-inv-escrow" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-escrow" aria-controls="navs-pills-top-escrow" aria-selected="false"
                            tabindex="-1">
                            <i class="mdi mdi-bank-outline me-1"></i>Escrow
                            <span class="badge rounded-pill bg-warning ms-1" id="badge-inv-escrow">-</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    <div class="tab-pane fade show active p-3" id="navs-pills-top-general" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-ar table table-bordered" data-badge="badge-inv-general">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>No PO.</th>
                                        <th>Company</th>
                                        <th>Total Invoice</th>
                                        <th>Advance Payment</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-reftech" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-reftech table table-bordered" data-badge="badge-inv-reftech">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>No PO.</th>
                                        <th>Company</th>
                                        <th>Total Invoice</th>
                                        <th>Advance Payment</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                        <th>Sales</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-kojisha" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-kojisha table table-bordered" data-badge="badge-inv-kojisha">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>No PO.</th>
                                        <th>Company</th>
                                        <th>Total Invoice</th>
                                        <th>Advance Payment</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                        <th>Sales</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-ahmad" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-ahmad table table-bordered" data-badge="badge-inv-ahmad">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>No PO.</th>
                                        <th>Company</th>
                                        <th>Total Invoice</th>
                                        <th>Advance Payment</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                        <th>VAT</th>
                                        <th>Sales</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-rayi" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-rayi table table-bordered" data-badge="badge-inv-rayi">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>No PO.</th>
                                        <th>Company</th>
                                        <th>Total Invoice</th>
                                        <th>Advance Payment</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                        <th>VAT</th>
                                        <th>Sales</th>
                                        <th>Flag</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-escrow" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-escrow table table-bordered" data-badge="badge-inv-escrow">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Nominal</th>
                                        <th>Fee</th>
                                        <th>Sales</th>
                                        <th>Flag</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
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
    <script src="<?php echo e(asset('assets')); ?>/includes/table-ar-sales-invoice.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-ar-sales-invoice-reftech.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-ar-sales-invoice-kojisha.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-ar-sales-invoice-ahmad.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-ar-sales-invoice-rayi.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-ar-sales-invoice-escrow.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        window.invoiceYearFilter = $('#invoice-year-filter').val() || 'all';
        window.invoiceDataTables = window.invoiceDataTables || {};

        $('#invoice-year-filter').on('change', function () {
            window.invoiceYearFilter = $(this).val();
            Object.values(window.invoiceDataTables).forEach(function (dt) {
                dt.ajax.reload();
            });
        });

        $(document).on('draw.dt', function (e) {
            var $tbl = $(e.target);
            var badgeId = $tbl.data('badge');
            if (!badgeId) return;
            var api = $tbl.DataTable();
            $('#' + badgeId).text(api.page.info().recordsTotal);
        });

        $('#invoice-ar-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/accounting/payment/index-invoice.blade.php ENDPATH**/ ?>