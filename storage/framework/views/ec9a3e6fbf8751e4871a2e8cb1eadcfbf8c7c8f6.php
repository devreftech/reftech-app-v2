
<?php $__env->startSection('title', 'My Leads'); ?>
<?php $__env->startSection('content'); ?>

    <?php if(Session::has('message')): ?>
        <div class="bs-toast toast toast-placement-ex m-2 fade top-0 end-0 hide" role="alert" aria-live="assertive"
            aria-atomic="true" data-bs-delay="2000">
            <div class="toast-header">
                <i class="mdi mdi-home me-2 text-success"></i>
                <div class="me-auto fw-semibold">Successfully</div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"><?php echo e(Session::get('message')); ?></div>
        </div>
    <?php endif; ?>

    <!-- Header Banner Card -->
    <div class="card clean-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-label-success fs-6 px-3 py-2">
                            <i class="mdi mdi-account-check-outline me-1"></i> Directory Customer
                        </span>
                    </div>
                    <h4 class="fw-bold mb-0 text-dark">Customer by Status</h4>
                    <small class="text-muted">
                        Kelola data pelanggan aktif berdasarkan status keaktifannya.
                    </small>
                </div>

                <div class="d-flex align-items-center flex-wrap gap-2">
                    <label class="form-label mb-0 text-muted" style="white-space:nowrap;">Filter Tipe:</label>
                    <select class="form-select form-select-sm" id="ru-type-filter" style="max-width:180px;">
                        <option value="">Semua Tipe</option>
                        <option value="User">User</option>
                        <option value="Reseller">Reseller</option>
                    </select>

                    <?php if(in_array(Auth::user()->role, ['Admin', 'Sales Manager', 'Accounting', 'ServiceM', 'Finance Manager'])): ?>
                    <label class="form-label mb-0 text-muted ms-2" style="white-space:nowrap;">Filter Sales:</label>
                    <select class="form-select form-select-sm" id="admin-sales-filter" style="max-width:220px;">
                        <option value="">Semua Sales</option>
                        <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-minimalist mb-4">
        <div class="card-header card-minimalist-header py-2">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0 flex-nowrap overflow-auto" id="crm-tab-nav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-active" type="button">
                        <i class="mdi mdi-check-circle-outline me-1"></i>Active
                        <span class="badge rounded-pill bg-success ms-1" id="badge-active">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-non-active" type="button">
                        <i class="mdi mdi-close-circle-outline me-1"></i>Non Active
                        <span class="badge rounded-pill bg-warning ms-1" id="badge-non-active">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-bangkrupt" type="button">
                        <i class="mdi mdi-alert-circle-outline me-1"></i>Bangkrupt
                        <span class="badge rounded-pill bg-danger ms-1" id="badge-bangkrupt">-</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content border-0 p-0 m-0">
                
                <div class="tab-pane fade show active p-3" id="tab-active">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-customers-active table table-bordered" data-badge="badge-active">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Company</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Area</th>
                                    <th class="text-center">Last Contact</th>
                                    <th class="text-center">Next FU</th>
                                    <th class="text-center">Flag</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade p-3" id="tab-non-active">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-customers-non-active table table-bordered" data-badge="badge-non-active">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Company</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Area</th>
                                    <th class="text-center">Last Contact</th>
                                    <th class="text-center">Next FU</th>
                                    <th class="text-center">Flag</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade p-3" id="tab-bangkrupt">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-customers-bangkrupt table table-bordered" data-badge="badge-bangkrupt">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Company</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Area</th>
                                    <th class="text-center">Last Contact</th>
                                    <th class="text-center">Next FU</th>
                                    <th class="text-center">Flag</th>
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
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .card-minimalist {
            border: 1px solid #e0e2e8 !important;
            box-shadow: none !important;
            border-radius: 12px;
        }
        .card-minimalist-header {
            border-bottom: 1px solid #e0e2e8 !important;
            background-color: #fafbfe;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }
        .nav-tabs .nav-link {
            border-radius: 6px 6px 0 0;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            border-color: #e0e2e8 #e0e2e8 #fff !important;
            background-color: #ffffff;
            font-weight: 600;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-advanced.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-customer-by-status.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            $(document).on('change', '.status-dropdown', function() {
                var selectedValue = $(this).val();
                var rowId = $(this).data('id');
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                console.log('id = ' + rowId);

                $.ajax({
                    type: 'POST',
                    url: '/existing/update-status/' + rowId,
                    data: {
                        status: selectedValue,
                        _token: csrfToken
                    },
                    success: function(response) {
                        console.log('Perubahan status berhasil dikirim ke server');
                        if (window.dtCustomerActive) window.dtCustomerActive.ajax.reload();
                        if (window.dtCustomerNonActive) window.dtCustomerNonActive.ajax.reload();
                        if (window.dtCustomerBangkrupt) window.dtCustomerBangkrupt.ajax.reload();
                    },
                    error: function(error) {
                        console.error('Gagal mengirim permintaan ke server:', error);
                    }
                });
            });
        });

        $(document).on('click', '.delete-data-leads', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('leads')); ?>/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sales/existing/indexByStatus.blade.php ENDPATH**/ ?>