<form action="<?php echo e(route('product-in.update', $product->id)); ?>" method="post" enctype="multipart/form-data">
    
    <?php if(@$product): ?>
        <?php echo method_field('PATCH'); ?>
    <?php endif; ?>
    <?php echo csrf_field(); ?>
    <div class="modal animate__animated animate__fadeIn" id="<?php echo e('editPrice-' . $product->id); ?>" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">
                        <?php echo e('Edit Price ' . $product->invoice); ?>

                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2 mt-1">
                    <div class="row mt-2 gy-4">
                        <?php $__currentLoopData = $detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-6 col-lg-4 mb-3">
                                <div class="text-nowrap align-top">
                                    <p class="mb-0 fw-semibold" style="font-size: 16px">
                                        <?php echo e($item->detailProduct->replacement); ?>

                                    </p>
                                    <pre class="mb-0"
                                        style="font-size: 12px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($products->detailProduct->product->commodity); ?></pre>
                                </div>
                            </div>
                            <div class="col-6 col-lg-8 mb-3">
                                <div class="input-group" data-price="<?php echo e($item->id); ?>">
                                    <span class="input-group-text">Rp. </span>
                                    <input type="text" class="form-control invoice-item-modal-label" id="modal-label"
                                        data-id="<?php echo e($item->id); ?>" min="0" placeholder="Put modal Here" data-type="currency"
                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                        @blur="focused = false" value="<?php echo e(old('modal['.$item->id.']', @$item->modal ? number_format($item->modal, 0, '', '.') : '')); ?>">
                                    <input class="form-control invoice-item-modal" type="number" name="modal[<?php echo e($item->id); ?>]"
                                        id="modal-<?php echo e($item->id); ?>" value="<?php echo e(old('modal['.$item->id.']', @$item->modal ? $item->modal : '')); ?>" hidden>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/product/edit-price.blade.php ENDPATH**/ ?>