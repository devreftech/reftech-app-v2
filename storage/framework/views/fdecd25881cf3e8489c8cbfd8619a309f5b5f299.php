
<?php $__env->startSection('title', 'Monitoring machine'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row mb-3">
        <div class="col-12 col-md-6 mb-4">
            <h5 class=" mb-2">Data Unit</h5>
        </div>
        <div class="col-6 col-md-3">
            <div class="issue">
                <div class="d-flex justify-content-between mb-2">
                    <h5 class="fw-bold m-0 pt-2">
                        Issue
                    </h5>
                    <a type="button" data-bs-toggle="modal" data-bs-target="#updateIssue">
                        <button type="button" class="btn btn-primary waves-effect waves-light">
                            <?php echo e($monitoring->issue || $monitoring->issue == '-' ? 'Edit' : '+'); ?>

                        </button>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="fw-bold m-0 pt-2">
                    Recomendation
                </h5>
                <a type="button" data-bs-toggle="modal" data-bs-target="#updateRecommendation">
                    <button type="button" class="btn btn-primary waves-effect waves-light">
                        <?php echo e($monitoring->recommendation && $monitoring->recommendation != '-' ? 'Edit' : '+'); ?>

                    </button>
                </a>
            </div>
        </div>
        <div class="col-12 col-md-6 mb-4">
            <div class="unit">
                <div class="card h-px-200">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">Date Issue</div>
                            <div class="col-8">: <?php echo e($monitoring->date); ?></div>
                            <div class="col-4">Location</div>
                            <div class="col-8">: <?php echo e($monitoring->machine->location); ?></div>
                            <div class="col-4">Tag Number</div>
                            <div class="col-8">: <?php echo e($monitoring->machine->tag); ?></div>
                            <div class="col-4">Type / Model </div>
                            <div class="col-8">: <?php echo e($monitoring->machine->unit->brand); ?>

                                <?php echo e($monitoring->machine->unit->unit->sku); ?></div>
                            <div class="col-4">PIC User</div>
                            <div class="col-8">: <?php echo e($monitoring->machine->desc); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="issue">
                <div class="card h-px-200">
                    <div class="card-body">
                        <pre
                            style="font-size: 15px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 250px; overflow-x: auto; white-space: pre-wrap;"><?php echo e($monitoring->issue ?? 'Belum ada Issue'); ?></pre>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-px-200">
                <div class="card-body">
                    <pre
                        style="font-size: 15px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 250px; overflow-x: auto; white-space: pre-wrap;"><?php echo e($monitoring->recommendation && $monitoring->recommendation != '-' ? $monitoring->recommendation : 'Belum ada Recommendation'); ?></pre>
                </div>
            </div>
        </div>
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="fw-bold m-0 pt-2">
                    quotation
                </h5>
                <a href="<?php echo e(route('monitoring.create-quotation', $monitoring->id)); ?>" type="button"
                    class="btn btn-primary waves-effect waves-light">
                    +
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>No. Quote</th>
                                    <th>No. PR</th>
                                    <th>Title</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $quotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e(\Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y')); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('quotation.show', $quote->id)); ?>" class="text-black">
                                                <?php echo e($quote->no_quote); ?>

                                            </a>
                                        </td>
                                        <td><?php echo e($quote->no_pr); ?></td>
                                        <td><?php echo e($quote->title); ?></td>
                                        <td>Rp <?php echo e(number_format($quote->harga_total, 0, ',', '.')); ?></td>
                                        <?php
                                            switch ($quote->status) {
                                                case 20:
                                                    $labelColor = 'secondary';
                                                    $title = 'Send WA / Email';
                                                    break;
                                                case 30:
                                                    $labelColor = 'dark';
                                                    $title = 'Inquiry Accepted';
                                                    break;
                                                case 40:
                                                    $labelColor = 'info';
                                                    $title = 'Progress Follow Up';
                                                    break;
                                                case 60:
                                                    $labelColor = 'primary';
                                                    $title = 'Negotiation / Revisi';
                                                    break;
                                                case 80:
                                                    $labelColor = 'warning';
                                                    $title = 'Hot Prospect';
                                                    break;
                                                case 100:
                                                    $labelColor = 'success';
                                                    $title = 'Done PO';
                                                    break;
                                                case 0:
                                                    $labelColor = 'danger';
                                                    $title = 'Loss';
                                                    break;
                                                default:
                                                    return 0;
                                                    break;
                                            }
                                        ?>
                                        <td>
                                            <span
                                                class="badge bg-label-<?php echo e($labelColor); ?>"><?php echo e($quote->status); ?>%</span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Belum Ada Quote</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="pn mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <h5 class="fw-bold m-0 pt-2">
                        Part Number
                    </h5>
                    <div class="tombol">
                        <a type="button" data-bs-toggle="modal" data-bs-target="#updatePn">
                            <button type="button" class="btn btn-primary waves-effect waves-light">
                                +
                            </button>
                        </a>
                    </div>
                </div>
                <div class="card ">
                    <div class="card-body">
                        <div class="table-responsive text-nowrap mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:40%;">PN</th>
                                        <th>Description</th>
                                        <th>Stock</th>
                                        <th>action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $pn; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $machine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($machine->pn); ?></td>
                                            <td><?php echo e($machine->desc); ?></td>
                                            <td><?php echo e($machine->stock); ?></td>
                                            <td>
                                                <a href="#" data-id="<?php echo e($machine->id); ?>"
                                                    data-monitoring="<?php echo e($monitoring->id); ?>"
                                                    class="btn btn-sm btn-label-danger delete-pn">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mainlog mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <h5 class="fw-bold m-0 pt-2">
                        Maintenance Log
                    </h5>
                </div>
                <div class="card ">
                    <div class="card-body">
                        <?php if($monitoring->mainlog): ?>
                            <a type="button" class="w-100" data-bs-toggle="modal" data-bs-target="#plusMainlog">
                                <button type="button" class="btn btn-primary waves-effect waves-light w-100">
                                    + Maintenance Log
                                </button>
                            </a>
                        <?php else: ?>
                            <?php echo e($monitoring->mainlog->desc); ?>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="fw-bold m-0 pt-2">
                    Activity Timeline
                </h5>
                <div class="tombol">
                    <a href="#" data-id="<?php echo e($monitoring->id); ?>" class="btn btn-warning arsip-mon">Arsip</a>
                    <a type="button" data-bs-toggle="modal" data-bs-target="#updateStatus">
                        <button type="button" class="btn btn-primary waves-effect waves-light">
                            Update Status
                        </button>
                    </a>
                </div>
            </div>
            <div class="card h-auto">
                <div class="card-body">
                    <?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            switch ($item->status) {
                                case '0':
                                    $stat = 'Monitoring Created';
                                    $label = 'bg-label-dark';
                                    break;
                                case '1':
                                    $stat = 'Process FU to User';
                                    $label = 'bg-label-info';
                                    break;
                                case '2':
                                    $stat = 'Send Inquiry';
                                    $label = 'bg-label-warning';
                                    break;
                                case '3':
                                    $stat = 'Hold By User';
                                    $label = 'bg-label-danger';
                                    break;
                                case '4':
                                    $stat = 'Done';
                                    $label = 'bg-label-success';
                                    break;
                                case '5':
                                    $stat = 'Archived';
                                    $label = 'bg-label-dark';
                                    break;

                                default:
                                    # code...
                                    break;
                            }
                        ?>
                        <div class="d-flex justify-content-between">
                            <h5 class="badge rounded-pill <?php echo e($label); ?> fs-5 fw-5">
                                <?php echo e($stat); ?>

                            </h5>
                            <h6>
                                <?php echo e($item->pic->name); ?>

                            </h6>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p><?php echo e($item->desc); ?></p>
                            <p><?php echo e($item->date); ?></p>
                        </div>
                        <hr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('components.modal.monitoring.client.mainlog', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.monitoring.client.issueDet', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.monitoring.client.recommendation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.monitoring.client.status', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.monitoring.client.pn', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-client-daily.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-issue-client-monitoring.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/forms-selects.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        $(document).on('click', '.delete-pn', function() {
            var id = $(this).data('id');
            var monitoring = $(this).data('monitoring');
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
                        'url': '<?php echo e(url('monitoring-client')); ?>/fajarPaper-deletePN/' + id,
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
                                    window.location.href =
                                        '/monitoring-client/fajarPaper/' + monitoring;
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
        $(document).on('click', '.arsip-mon', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, archive it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('monitoring-client')); ?>/fajarPaper-arsipStatus/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'PATCH',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Archived!",
                                    text: "Your file has been archived.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href =
                                        '/monitoring-client/fajarPaper-archive';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Archive!'
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

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/monitoring/client/detail.blade.php ENDPATH**/ ?>