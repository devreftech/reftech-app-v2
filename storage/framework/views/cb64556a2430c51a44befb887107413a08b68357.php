
<?php $__env->startSection('title', 'Detail Prospect'); ?>
<?php $__env->startSection('content'); ?>
    <h3>
        Prospect <?php echo e($client->company); ?>

    </h3>
    <div class="row invoice-preview">
        
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card-body">
                                <h5 class="fw-bold pb-1 mb-3">
                                    Details
                                </h5>
                                <p class="card-text">
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Address
                                    </div>
                                    <div class="col-9">
                                        : <?php echo e($client->address); ?>

                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Sub Address
                                    </div>
                                    <div class="col-9">
                                        : <?php echo e($client->subAddress); ?>

                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Area
                                    </div>
                                    <div class="col-9">
                                        : <?php echo e($client->area); ?>

                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Phone
                                    </div>
                                    <div class="col-9">
                                        : <?php echo e($client->phone); ?>

                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Email
                                    </div>
                                    <div class="col-9">
                                        : <?php echo e($client->email); ?>

                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Mobile
                                    </div>
                                    <div class="col-9">
                                        : <?php echo e($client->mobile); ?>

                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        R/U
                                    </div>
                                    <div class="col-9">
                                        : <?php echo e($client->ru); ?>

                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Source
                                    </div>
                                    <div class="col-9">
                                        : <?php echo e($client->source); ?>

                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Machine
                                    </div>
                                    <div class="col-9">
                                        : <?php echo e($client->machine); ?>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        Assigned
                                    </div>
                                    <div class="col-9">
                                        : <?php echo e($client->sales->name); ?>

                                    </div>
                                </div>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card-body">
                                <h5 class="fw-bold pb-1 mb-3">
                                    PIC
                                </h5>
                                <p class="card-text">
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Name
                                    </div>
                                    <div class="col-9">
                                        : <?php echo e($pic->name_pic); ?>

                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Phone
                                    </div>
                                    <div class="col-9">
                                        : <?php echo e($pic->phone_pic); ?>

                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Position
                                    </div>
                                    <div class="col-9">
                                        : <?php echo e($pic->position); ?>

                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Email
                                    </div>
                                    <div class="col-9">
                                        : <?php echo e($pic->email_pic); ?>

                                    </div>
                                </div>
                                </p>
                                <div class="prospect my-3">
                                    <h5>Prospect</h5>
                                    <div class="row">
                                        <div class="col-3">
                                            Category
                                        </div>
                                        <div class="col-9">
                                            <pre class="mb-0"
                                                style="font-size: 15px; font-family: Inter; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: <?php echo e($prospect->category); ?></pre>
                                        </div>
                                        <div class="col-3">
                                            Prospect
                                        </div>
                                        <div class="col-9">
                                            <pre class="mb-0"
                                                style="font-size: 15px; font-family: Inter; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: <?php echo e($prospect->kebutuhan); ?></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <h5 class="card-header">Quotation </h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>No Quote</th>
                                <th>Client</th>
                                <th>Status</th>
                                <th>Value</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php if(@$quotation): ?>
                                <?php
                                    if ($quotation->status == '20') {
                                        $color = 'secondary';
                                    } elseif ($quotation->status == '30') {
                                        $color = 'dark';
                                    } elseif ($quotation->status == '40') {
                                        $color = 'info';
                                    } elseif ($quotation->status == '60') {
                                        $color = 'primary';
                                    } elseif ($quotation->status == '80') {
                                        $color = 'warning';
                                    } elseif ($quotation->status == '100') {
                                        $color = 'success';
                                    } elseif ($quotation->status == '0') {
                                        $color = 'danger';
                                    } else {
                                        $color = 'secondary';
                                    }
                                ?>
                                <tr>
                                    <td><?php echo e($quotation->no_quote); ?></td>
                                    <td><?php echo e($quotation->pic->client->company); ?></td>
                                    <td>
                                        <p class="badge bg-label-<?php echo e($color); ?>"><?php echo e($quotation->status); ?></p>
                                    </td>
                                    <td class="text-end">RP <?php echo e(number_format($quotation->nett, 0, '', '.')); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('quotation.show', $quotation->id)); ?>"
                                            class="btn btn-info d-grid w-100 waves-effect">
                                            <span class="mdi mdi-eye-outline"></span>
                                        </a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td class="text-center" colspan="5">
                                        This Prospect Doesn't have quotation yet
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card comment mt-4">
                <div class="card-body" id="viewComment">
                    <ul class="timeline card-timeline mb-0">
                        <?php if(@$prospectComments): ?>
                            <?php
                                // if ($stats->status == '20') {
                                //     $status = 'Send Quotation';
                                //     $color = 'secondary';
                                // } elseif ($stats->status == '30') {
                                //     $status = 'Inquiry Accepted';
                                //     $color = 'dark';
                                // } elseif ($stats->status == '40') {
                                //     $status = 'Progress Follow Up';
                                //     $color = 'info';
                                // } elseif ($stats->status == '60') {
                                //     $status = 'Negotiation / Revisi';
                                //     $color = 'primary';
                                // } elseif ($stats->status == '80') {
                                //     $status = 'Hot Prospect';
                                //     $color = 'warning';
                                // } elseif ($stats->status == '100') {
                                //     $status = 'Done PO';
                                //     $color = 'success';
                                // } elseif ($stats->status == '0') {
                                //     $status = 'Loss';
                                //     $color = 'danger';
                                // } else {
                                //     $status = 'Quotation Created';
                                //     $color = 'secondary';
                                // }
                            ?>
                            <li class="timeline-item timeline-item-transparent clearfix">
                                <div class="timeline-event">
                                    
                                    <?php $__currentLoopData = $prospectComments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="d-flex justify-content-between align-items-center px-2 mb-2<?php echo e($item->id_user == Auth::user()->id ? ' rounded bg-label-primary float-end' : ''); ?>"
                                            style="width : 80%;">
                                            <div class="d-flex align-items-center mb-1">
                                                <img src="<?php echo e(url('') . '/' . $item->user->image); ?>" alt="ini photo"
                                                    style="width: 50px;" class="mx-2 rounded-pill">
                                                <p class="mb-0">
                                                    <span class="fw-medium"><?php echo e($item->user->name); ?></span>:
                                                    <?php $__currentLoopData = $item->mention; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mention): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php echo e($mention->mention->name ? '@' . $mention->mention->name : ''); ?>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php echo e($item->comment); ?>

                                                </p>
                                            </div>
                                            <small
                                                class="text-muted"><?php echo e($item->date->diffInHours(Carbon\Carbon::now()) > 24 ? $item->date->format('d M y h:i:s') : $item->date->diffForHumans()); ?></small>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </li>
                        <?php else: ?>
                            Prospect Have no Comment
                        <?php endif; ?>
                        
                        <form action="<?php echo e(route('add_comment.prospect', $prospect->id)); ?>" method="post"
                            enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-2">
                                    <button type="button" class="btn btn-lg btn-primary waves-effect w-100 mt-3"
                                        data-bs-toggle="modal" data-bs-target="#addMention">@</button>
                                </div>
                                <?php echo $__env->make('components.modal.prospect.add-mention', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <div class="col-10">
                                    <div class="form-floating mt-3">
                                        <input type="text" class="form-control" id="floatingInputFilled"
                                            placeholder="Comment" name="comment"
                                            aria-describedby="floatingInputFilledHelp">
                                        <label for="floatingInputFilled">Comment</label>
                                        <span class="form-floating-focused"></span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit"
                                class="btn btn-primary waves-effect waves-light float-end">Comment</button>
                        </form>

                        
                    </ul>
                </div>
            </div>
        </div>
        
        
        <?php if(in_array(Auth::user()->role, ['Admin', 'Support'])): ?>
            <div class="col-xl-3 col-md-4 col-12 invoice-actions">
                <div class="card">
                    <form action="<?php echo e(route('add_sales.prospect', $prospect->id)); ?>" method="post"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md">
                                    <div
                                        class="form-check custom-option custom-option-icon  <?php echo e(@$prospect->provide == '1' ? 'checked' : ''); ?><?php echo e(@$prospect->quotation ? 'disabled' : ''); ?> h-100">
                                        <label class="form-check-label custom-option-content" for="provideCheck1">
                                            <span class="custom-option-body">
                                                <i class="mdi mdi-file-check-outline"></i>
                                                <span class="custom-option-title"> Provided </span>
                                                <small> Prospect is Provided. </small>
                                            </span>
                                            <input name="provideCheck" class="form-check-input check-provide"
                                                type="radio" value="1" id="provideCheck1"
                                                <?php echo e(@$prospect->provide == '1' ? 'checked' : ''); ?>

                                                <?php echo e(@$prospect->quotation ? 'disabled' : ''); ?>>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div
                                        class="form-check custom-option custom-option-icon  <?php echo e(@$prospect->provide == '0' ? 'checked' : ''); ?> <?php echo e(@$prospect->quotation ? 'disabled' : ''); ?> h-100">
                                        <label class="form-check-label custom-option-content" for="provideCheck2">
                                            <span class="custom-option-body">
                                                <i class="mdi mdi-file-alert-outline"></i>
                                                <span class="custom-option-title"> No Provided </span>
                                                <small> Prospect is No Provided. </small>
                                            </span>
                                            <input name="provideCheck" class="form-check-input check-no-provide"
                                                type="radio" value="0" id="provideCheck2"
                                                <?php echo e(@$prospect->provide == '0' ? 'checked' : ''); ?><?php echo e(@$prospect->quotation ? 'disabled' : ''); ?>>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating form-floating-outline form-sales"
                                <?php echo e(@$prospect->provide == '1' ? '' : 'hidden'); ?>>
                                <select class="form-select" id="selectSales" aria-label="Default select example"
                                    name="sales" <?php echo e(@$prospect->quotation ? 'disabled' : ''); ?>>
                                    <option disabled="">----- Choose Sales -----</option>
                                    <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>"
                                            <?php echo e(@$prospect->id_sales == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <label for="selectSales">Sales</label>
                            </div>
                        </div>
                        <div class="card-footer float-end">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php if(Auth::user()->role == 'Support'): ?>
                <div class="col-xl-3 col-md-4 col-12 invoice-actions">
                    <div class="card">
                        <div class="card-body">
                            <a href="#" class="btn btn-outline-danger d-grid w-100 waves-effect delete-prospect"
                                data-id="<?php echo e($prospect->id); ?>">
                                Delete
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php elseif(Auth::user()->role == 'Sales'): ?>
            <div class="col-xl-3 col-md-4 col-12 invoice-actions">
                <?php if($prospect->level == null): ?>
                    <div class="card">
                        <div class="card-body">
                            <a href="#" class="btn btn-primary d-grid w-100 waves-effect with-quote mb-3"
                                data-id="<?php echo e($prospect->id); ?>">
                                With Quote
                            </a>
                            <a href="#" class="btn btn-danger d-grid w-100 waves-effect without-quote mb-3"
                                data-id="<?php echo e($prospect->id); ?>">
                                No Quote
                            </a>
                            <a href="#" class="btn btn-whatsapp d-grid w-100 waves-effect fu-wa mb-3"
                                data-id="<?php echo e($prospect->id); ?>">
                                On Process Follow UP WA
                            </a>
                            <a href="#" class="btn btn-warning d-grid w-100 waves-effect no-respond mb-3"
                                data-id="<?php echo e($prospect->id); ?>">
                                No Respond
                            </a>
                        </div>
                    </div>
                <?php elseif($prospect->level == 9): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <a href="#" class="btn btn-primary d-grid w-100 waves-effect with-quote mb-4"
                                data-id="<?php echo e($prospect->id); ?>">
                                Create Quote
                            </a>
                            <p class="text-center mb-4">- or -</p>
                            <form action="<?php echo e(route('choose_quotation.prospect', $prospect->id)); ?>" method="post"
                                enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <div class="form-floating form-floating-outline mb-2">
                                    <select class="form-select" id="Type" aria-label="Default select example"
                                        name="id_quotation">
                                        <?php $__empty_1 = true; $__currentLoopData = $allQuotation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <option value="<?php echo e($item->id); ?>">
                                                <?php echo e($item->no_quote); ?> - <?php echo e($item->title); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <option value="" disabled>No Quotation</option>
                                        <?php endif; ?>
                                    </select>
                                    <label for="exampleFormControlSelect1">Choose Quotation</label>
                                </div>
                                <button type="submit"
                                    class="btn btn-primary waves-effect waves-light float-end">Choose</button>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <a href="#" class="btn btn-danger d-grid w-100 waves-effect without-quote mb-3"                             
                                data-id="<?php echo e($prospect->id); ?>">
                                No Quote
                            </a>
                            <a href="#" class="btn btn-warning d-grid w-100 waves-effect no-respond mb-3"
                                data-id="<?php echo e($prospect->id); ?>">
                                No Respond
                            </a>
                            <a href="#" class="btn btn-pinterest d-grid w-100 waves-effect no-provide mb-3"
                                data-id="<?php echo e($prospect->id); ?>">
                                No Provide
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('after-style'); ?>
    <!-- Page CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/bootstrap-select/bootstrap-select.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/form-layouts.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/forms-selects.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script'); ?>
    <script>
        $(document).on('click', '.delete-prospect', function() {
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
                        'url': '<?php echo e(url('prospect')); ?>/' + id,
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
                                    window.location.href = '/prospect';
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
        $(document).on('change', '.check-provide', function() {
            if ($(this).is(':checked')) {
                $('.form-sales').removeAttr('hidden');
                // $('.card-footer').removeAttr('hidden');
            } else {
                $('.form-sales').attr('hidden', 'hidden');
                // $('.card-footer').attr('hidden', 'hidden');
            }
        });
        $(document).on('change', '.check-no-provide', function() {
            if ($(this).is(':checked')) {
                $('.form-sales').attr('hidden', true);
                // $('.card-footer').attr('hidden', true);
            } else {
                $('.form-sales').removeAttr('hidden');
                // $('.card-footer').removeAttr('hidden');
            }
        });
        $(document).on('click', '.with-quote', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure With Quotation?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, With Quotation!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('prospect')); ?>/' + 'with_quotation/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your file has been converted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href =
                                        '/prospect/create_quotation/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed With Quotation!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "You cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.without-quote', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure Without Quotation?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Without Quotation!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('prospect')); ?>/' + 'without_quotation/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your file has been converted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href =
                                        '/prospect/';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed Without Quotation!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "You cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.fu-wa', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure move to Process?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Follow Up!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('prospect')); ?>/' + 'onProcessFU/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your file has been converted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href =
                                        '/prospect/';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed On Process!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "You cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.no-respond', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure No Respond this Prospect?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, No Respond!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('prospect')); ?>/' + 'no_respond/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your file has been converted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href =
                                        '/prospect/';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed Without Quotation!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "You cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.no-provide', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure No Provide this Prospect?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, No Provide!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('prospect')); ?>/' + 'no_provide/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your file has been converted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href =
                                        '/prospect/';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed Without Quotation!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "You cancelled :)",
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

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/support/prospect/detail.blade.php ENDPATH**/ ?>