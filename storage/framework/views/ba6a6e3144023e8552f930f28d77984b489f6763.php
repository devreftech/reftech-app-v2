<form action="<?php echo e(route('delivery.store', $invoice->id)); ?>" method="post" enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>

    
    <div class="modal animate__animated animate__fadeIn" id="doEkspedisi" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">
                        <?php echo e('Create Delivery Order Ekspedisi' . $invoice->no_invoice); ?>

                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="row g-2 my-3">
                        <div class="col-8">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select" id="selectAddress"
                                    aria-label="Default select example" name="destination" data-allow-clear="true">
                                    <option value="1"
                                        <?php echo e(old('address', $invoice->invoiceTo) == '1' ? 'selected' : ''); ?>>
                                        <?php echo e($quote->pic->client->address); ?>

                                    </option>
                                    <option value="2"
                                        <?php echo e(old('address', $invoice->invoiceTo) == '2' ? 'selected' : ''); ?>>
                                        <?php echo e($quote->pic->client->subAddress); ?></option>
                                </select>
                                <label for="selectAddress">Choose Address</label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="date" id="html5-date-input" name="date"
                                    value="<?php echo e(@$invoice->date); ?>">
                                <label for="html5-date-input">Date</label>
                            </div>
                            <input type="text" name="type" id="type" value="ekspedisi" hidden>
                            <input type="number" name="id_invoice" id="id_invoice" value="<?php echo e($invoice->id); ?>" hidden>
                        </div>
                    </div>
                    <?php if($invoice->quote->type == 'Sparepart'): ?>
                        <?php
                            $i = 0;
                        ?>
                        <?php $__empty_1 = true; $__currentLoopData = $dquote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <hr>
                            <div class="row g-2 mb-3">
                                <div class="col-1"></div>
                                <div class="col-3 mb-2 d-flex align-item-center">
                                    <p style="margin: auto"><?php echo e($product->equivalent->brand); ?>

                                        <?php echo e($product->equivalent->pn); ?>

                                    </p>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="form-floating form-floating-outline mb-3">
                                        <div class="input-group">
                                            <input type="number" id="stock" class="form-control" name="qty[]"
                                                value="0" max="<?php echo e($product->qty); ?>" min="0">
                                            <span class="input-group-text"
                                                id="basic-addon43"><?php echo e($product->info_qty); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <p>max : <?php echo e($product->qty); ?></p>
                                </div>
                            </div>
                            <?php
                                $i++;
                            ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p> Anda Belum memiliki pn. </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/accounting/delivery/create-ekspedisi.blade.php ENDPATH**/ ?>