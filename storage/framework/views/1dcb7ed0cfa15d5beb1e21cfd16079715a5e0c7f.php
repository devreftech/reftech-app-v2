<?php $__env->startSection('title', 'Invoice'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row invoice-preview">
        
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    <?php if($quote->pic->client->info == 'Reftech'): ?>
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                            <div class="mb-xl-0 pb-1">
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img class="text-md"
                                                src="<?php echo e(url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png')); ?>"
                                                alt="" srcset="" width="60%">
                                        </span>
                                    </span>
                                </div>
                                <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                                <div style="font-size: 10px">
                                    <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                    <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                    <p class="mb-1">
                                        <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                                        <?php echo e('  |  '); ?><i
                                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                                    </p>
                                    <p class="mb-1">
                                    </p>
                                </div>
                            </div>
                            <div class="text-end">
                                <h3 class="fw-bold">QUOTATION</h3>
                                <div>
                                    <span class="fw-bolder">#<?php echo e($quote->no_quote); ?></span>
                                </div>
                                <div class="mt-1">
                                    <span
                                        class="text-muted"><?php echo e($quote->status == '25' ? 'DRAFT' : ($quote->status == '50' ? 'SEND' : ($quote->status == '75' ? 'NEGOTIATION' : ($quote->status == '100' ? 'DONE PO' : ($quote->status == '0' ? 'LOSS' : ''))))); ?></span>
                                </div>
                                <div class="mt-1">
                                    <span
                                        class="text-muted"><?php echo e(Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y')); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                            <div class="mb-xl-0 pb-1">
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img class="text-md" src="<?php echo e(asset('/asset')); ?>/logo/Kojisha-Log.png"
                                                alt="" srcset="" width="60%">
                                        </span>
                                    </span>
                                </div>
                                <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                                <div style="font-size: 10px">
                                    <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                                    <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                    <p class="mb-1">
                                        <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62 812-1000-0997
                                        <?php echo e(' | '); ?><i
                                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>info@kojisha.com
                                    </p>
                                </div>
                            </div>
                            <div class="text-end">
                                <h3 class="fw-bold">QUOTATION</h3>
                                <div>
                                    <span class="fw-bolder">#<?php echo e($quote->no_quote); ?></span>
                                </div>
                                <div class="mt-1">
                                    <span
                                        class="text-muted"><?php echo e($quote->status == '25' ? 'DRAFT' : ($quote->status == '50' ? 'SEND' : ($quote->status == '75' ? 'NEGOTIATION' : ($quote->status == '100' ? 'DONE PO' : ($quote->status == '0' ? 'LOSS' : ''))))); ?></span>
                                </div>
                                <div class="mt-1">
                                    <span
                                        class="text-muted"><?php echo e(Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y')); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <hr class="my-0">
                <div class="card-body mb-3">
                    <div class="row">
                        <div class="col-6">
                            <h6 class="fw-semibold fs-4 mb-3">Quote To:</h6>
                        </div>
                        <div class="col-6 mb-2">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2 fw-medium">
                            <p class="mb-1">Company </p>
                            <p class="mb-1">Name PIC</p>
                            <p class="mb-1">Phone </p>
                        </div>
                        <div class="col-4">
                            <p class="mb-1">: <?php echo e($quote->pic->client->company); ?></p>
                            <p class="mb-1">: <?php echo e($quote->pic->name_pic); ?></p>
                            <p class="mb-1">: <?php echo e($quote->pic->client->phone); ?></p>
                        </div>
                        <div class="col-3 fw-medium text-end">
                            <p class="mb-1">Sales :</p>
                            <p class="mb-1">No PR :</p>
                            <p class="mb-1">Email :</p>
                        </div>
                        <div class="col-3 text-end">
                            <p class="mb-1">
                                <?php echo e($quote->pic->client->info == 'Reftech' ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia'); ?>

                            </p>
                            <p class="mb-1"> <?php echo e($quote->no_pr ?? '-'); ?></p>
                            <p class="mb-1"> <?php echo e($quote->pic->client->email); ?></p>
                        </div>
                    </div>
                </div>
                <?php if($quote->type == 'Sparepart'): ?>
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead class="table-light border-top">
                                <tr>
                                    <th>No.</th>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Discount</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $no = 0;
                                ?>
                                <?php $__currentLoopData = $dquote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $no++;
                                    ?>
                                    <tr style="font-size: 13px">
                                        <td class="align-top"><?php echo e($no); ?></td>
                                        <td class="text-nowrap align-top">
                                            <p class="mb-0 fw-semibold" style="font-size: 12px">
                                                <?php echo e($product->equivalent->brand); ?> <?php echo e($product->equivalent->pn); ?>

                                            </p>
                                            <pre class="mb-0"
                                                style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($product->detail_product); ?></pre>
                                        </td>
                                        <td class="align-top text-end">RP <?php echo e(number_format($product->price, 0, '', '.')); ?>

                                        </td>
                                        <td class="align-top"><?php echo e($product->qty); ?> <?php echo e($product->info_qty); ?> </td>
                                        <td class="align-top"><?php echo e($product->disc); ?>%</td>
                                        <td class="align-top text-end">RP <?php echo e(number_format($product->amount, 0, '', '.')); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td colspan="3" class="align-top px-4 py-5">
                                        <span>Thanks for your business</span>
                                    </td>
                                    <td colspan="2" class="text-end px-4 py-5">
                                        <p class="mb-2">Subtotal:</p>
                                        <p class="mb-2">Tax <?php echo e($quote->tax == '11' ? '(11%)' : ''); ?>:</p>
                                        <p class="mb-2">Discount Quote:</p>
                                        <p class="mb-2">Shipping Cost:</p>
                                        <p class="mb-0">Total:</p>
                                    </td>
                                    <td colspan="2" class="px-4 py-5">
                                        <p class="fw-semibold mb-2 text-end">RP
                                            <?php echo e(number_format($quote->subtotal, 0, '', '.')); ?></p>
                                        <p class="fw-semibold mb-2 text-end">
                                            <?php echo e($tax == '0' ? '0' : 'RP ' . number_format($tax, 0, '', '.')); ?></p>
                                        <p class="fw-semibold mb-2 text-end">RP
                                            <?php echo e(number_format($quote->diskon, 0, '', '.')); ?>

                                        </p>
                                        <p class="fw-semibold mb-2 text-end">RP
                                            <?php echo e(number_format($quote->shipping, 0, '', '.')); ?></p>
                                        <p class="fw-semibold mb-0 text-end">RP
                                            <?php echo e(number_format($quote->harga_total, 0, '', '.')); ?></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body">
                        <h5 class="my-4">Term & Condition</h5>
                        <div class="row">
                            <div class="col-3 fw-medium">
                                <p class="mb-1">Validity Of Quotation</p>
                                <p class="mb-1">Price </p>
                                <p class="mb-1">Delivery Process </p>
                                <p class="mb-1">Payment </p>
                                <p class="mb-1">Note </p>
                            </div>
                            <div class="col">
                                <p class="mb-1">: <?php echo e($quote->termncon[0]->validity); ?></p>
                                <p class="mb-1">: <?php echo e($quote->termncon[0]->pricing); ?></p>
                                <p class="mb-1">: <?php echo e($quote->termncon[0]->delivery_process); ?></p>
                                <p class="mb-1">: <?php echo e($quote->termncon[0]->payment); ?></p>
                                <p class="mb-1">: <?php echo e($quote->termncon[0]->note); ?></p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered m-0">
                            <thead class="table-light border-top">
                                <tr>
                                    <th style="width: 1%">No.</th>
                                    <th style="width: 50%">Item Description</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $abjad = 64;
                                ?>
                                <?php $__currentLoopData = $subQuote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subJudul): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $no = 0;
                                        $abjad++;
                                    ?>
                                    <tr style="font-size: 13px border-bottom:none !important;" class="border-top">
                                        <td class="align-top"
                                            style="border-bottom:none !important; background-color: #f0f0f0;">
                                            <p class="fw-bold mb-0"><?php echo e(chr($abjad)); ?></p>
                                        </td>
                                        <td class="text-nowrap align-top" colspan="4"
                                            style="border-bottom:none !important; background-color: #f0f0f0;">
                                            <p class="fw-bold mb-0"><?php echo e($subJudul->subtitle); ?></p>
                                        </td>
                                    </tr>
                                    <?php $__currentLoopData = $subJudul->detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr
                                            style="font-size: 13px; border-bottom:none !important; border-top:none !important;">
                                            <td class="align-top py-1" style="border-bottom:none !important;">
                                                <?php
                                                    $no++;
                                                ?>
                                                <p class="mb-1"><?php echo e($no); ?></p>
                                            </td>
                                            <td class="text-nowrap align-top py-1" style="border-bottom:none !important;">
                                                <p class="mb-1"><?php echo e($product->product); ?></p>
                                                <?php if($product->detail != '-'): ?>
                                                    <pre class="mb-0"
                                                        style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($product->detail); ?></pre>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-top py-1" style="border-bottom:none !important;">
                                                <p class="mb-0"><?php echo e($product->qty); ?> <?php echo e($product->info_qty); ?></p>
                                            </td>
                                            <td class="align-top py-1 text-end" style="border-bottom:none !important;">
                                                <p class="mb-0">RP <?php echo e(number_format($product->price, 0, '', '.')); ?></p>
                                            </td>
                                            <td class="align-top py-1 text-end" style="border-bottom:none !important;">
                                                <p class="mb-0">RP <?php echo e(number_format($product->amount, 0, '', '.')); ?></p>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td colspan="2" class="align-top px-4 py-5">
                                        <span>Thanks for your business</span>
                                    </td>
                                    <td colspan="2" class="text-end px-4 py-5">
                                        <p class="mb-2">Subtotal:</p>
                                        <p class="mb-2">Tax <?php echo e($quote->tax == '11' ? '(11%)' : ''); ?>:</p>
                                        <p class="mb-2">Discount Quote:</p>
                                        <p class="mb-2">Shipping Cost:</p>
                                        <p class="mb-0">Total:</p>
                                    </td>
                                    <td class="px-4 py-5">
                                        <p class="fw-semibold mb-2 text-end">RP
                                            <?php echo e(number_format($quote->subtotal, 0, '', '.')); ?></p>
                                        <p class="fw-semibold mb-2 text-end">
                                            <?php echo e($tax == '0' ? '0' : 'RP ' . number_format($tax, 0, '', '.')); ?></p>
                                        <p class="fw-semibold mb-2 text-end">RP
                                            <?php echo e(number_format($quote->diskon, 0, '', '.')); ?>

                                        </p>
                                        <p class="fw-semibold mb-2 text-end">RP
                                            <?php echo e(number_format($quote->shipping, 0, '', '.')); ?></p>
                                        <p class="fw-semibold mb-0 text-end">RP
                                            <?php echo e(number_format($quote->harga_total, 0, '', '.')); ?></p>
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body mt-2">
                        <h5>Note :</h5>
                        <pre class="mb-0"
                            style="font-size: 16px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap; text-align: justify;"><?php echo e($quote->termncon[0]->note); ?></pre>
                    </div>
                    <div class="card-body mt-2">
                        <h5 class="my-4">Term & Condition</h5>
                        <div class="row">
                            <div class="col-3 fw-medium">
                                <p class="mb-1">Validity Of Quotation</p>
                                <p class="mb-1">Price </p>
                                <p class="mb-1">Delivery Process </p>
                                <p class="mb-1">Payment </p>
                                <p class="mb-1">Warranty </p>
                            </div>
                            <div class="col">
                                <p class="mb-1">: <?php echo e($quote->termncon[0]->validity); ?></p>
                                <p class="mb-1">: <?php echo e($quote->termncon[0]->pricing); ?></p>
                                <p class="mb-1">: <?php echo e($quote->termncon[0]->delivery_process); ?></p>
                                <p class="mb-1">: <?php echo e($quote->termncon[0]->payment); ?></p>
                                <p class="mb-1">: <?php echo e($quote->termncon[0]->warranty); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    <button type="button" class="btn btn-primary d-grid w-100 waves-effect mb-3" data-bs-toggle="modal"
                        data-bs-target="#acceptInvoice-<?php echo e($quote->id); ?>">
                        Accept
                    </button>
                    <a href="#" class="btn btn-outline-danger d-grid w-100 mb-3 waves-effect delete-contract"
                        data-id="<?php echo e($quote->id); ?>">Reject</a>
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-1 text-muted fw-semibold" style="font-size: 14px;">No. PO</p>
                        <p class="mb-0 fw-bold style="font-size: 16px;"><?php echo e($invoice->no_po ?? '-'); ?></p>
                    </div>
                    <a href="<?php echo e(route('download-po.quotation', $quote->id)); ?>"
                        class="btn btn-primary d-grid w-100 waves-effect mb-3"> Download PO</a>
                    <button type="button" class="btn btn-secondary w-100 waves-effect waves-light mb-3"
                        data-bs-toggle="modal" data-bs-target="#detailPayment"> Detail Payment </button>
                    <h5>Remaining : Rp <?php echo e(number_format($remaining, 0, '.', ',')); ?></h5>
                </div>
            </div>
            
        </div>
        <?php echo $__env->make('components.modal.invoice.accept', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('components.modal.quotation.detail-payment', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php $__env->stopSection(); ?>
    <?php $__env->startPush('after-style'); ?>
        <!-- Page CSS -->
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice.css" />
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('after-script'); ?>
        <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('page-script'); ?>
        <script src="<?php echo e(asset('assets')); ?>/js/forms-selects.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('script'); ?>
        <script>
            // $(document).on('click', '.delete-contract', function() {
            //     var id = $(this).data('id');
            //     var quoteId = $(this).data('quote');
            //     Swal.fire({
            //         title: "Are you sure?",
            //         text: "You won't be able to revert this!",
            //         icon: "warning",
            //         showCancelButton: true,
            //         confirmButtonText: "Yes, delete it!",
            //         customClass: {
            //             confirmButton: "btn btn-primary me-3 waves-effect waves-light",
            //             cancelButton: "btn btn-label-secondary waves-effect",
            //         },
            //         buttonsStyling: false,
            //     }).then(function(result) {
            //         if (result.value) {
            //             $.ajax({
            //                 'url': '<?php echo e(url('contract')); ?>/' + id,
            //                 'type': 'POST',
            //                 'data': {
            //                     '_method': 'DELETE',
            //                     '_token': '<?php echo e(csrf_token()); ?>'
            //                 },
            //                 success: function(response) {
            //                     if (response == 1) {
            //                         Swal.fire({
            //                             icon: "success",
            //                             title: "Deleted!",
            //                             text: "Your file has been deleted.",
            //                             customClass: {
            //                                 confirmButton: "btn btn-success waves-effect",
            //                             },
            //                         })
            //                         window.setTimeout(function() {
            //                             window.location.href = '/quotation/' + quoteId;
            //                         }, 2000);
            //                     } else {
            //                         Swal.fire({
            //                             icon: 'error',
            //                             title: 'Oops...',
            //                             text: 'Data Failed to Delete!'
            //                         });
            //                     }
            //                 }
            //             });
            //         } else if (result.dismiss === Swal.DismissReason.cancel) {
            //             Swal.fire({
            //                 title: "Cancelled",
            //                 text: "Your imaginary file is safe :)",
            //                 icon: "error",
            //                 customClass: {
            //                     confirmButton: "btn btn-success waves-effect",
            //                 },
            //             });
            //         }
            //     });
            // });
            $('#backButton').click(function() {
                window.history.back();
            });
        </script>
    <?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/accounting/invoice/before-accept.blade.php ENDPATH**/ ?>