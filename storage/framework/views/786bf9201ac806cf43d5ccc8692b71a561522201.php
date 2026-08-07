<?php
    $uid = @$serial ? $serial->id : 'new-' . $product->id;
?>
<form action="<?php echo e(@$serial ? route('product.equivalent.update', $serial->id): route('product.equivalent', $product->id)); ?>" method="post" enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>

    <?php if(@$serial): ?>
        <?php echo method_field('patch'); ?>
    <?php endif; ?>
    <div class="modal animate__animated animate__fadeIn" id="<?php echo e(@$serial ? 'editEquivalent-'.$serial->id : 'createEquivalent-'.$product->id); ?>" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5-<?php echo e($uid); ?>"> Create Equivalent
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
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="image-<?php echo e($uid); ?>" class="form-control" name="image"
                                    placeholder=" Example : https://drive.google.com/drive/folders/**********" value="<?php echo e(old('image', @$serial->image ?? '')); ?>">
                                <label for="image-<?php echo e($uid); ?>">Image ( Link GDrive )</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="brand-<?php echo e($uid); ?>" class="form-control" name="brand"
                                    placeholder="xxx x xxx x xxx x" value="<?php echo e(old('brand', @$serial->brand ?? '')); ?>">
                                <label for="brand-<?php echo e($uid); ?>">Brand</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="pn-<?php echo e($uid); ?>" class="form-control" name="pn"
                                    placeholder="xxxx@xxx.xx" value="<?php echo e(old('pn', @$serial->pn ?? '')); ?>">
                                <label for="pn-<?php echo e($uid); ?>">Part Number</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="input-group form-floating form-floating-outline" data-price="1">
                                <span class="input-group-text">Rp. </span>
                                <input type="text" class="form-control invoice-item-price-label" id="price-label-<?php echo e($uid); ?>"
                                    data-id="<?php echo e(@$serial ? $serial->id : '0'); ?>" min="0" placeholder="Put Price Here" data-type="currency"
                                    pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true" @blur="focused = false"
                                    value="<?php echo e(old('price', @$serial->price ? number_format($serial->price, 0, ',', '.') : '')); ?>">
                                <input class="form-control invoice-item-price" type="number" name="price"
                                    id="price-<?php echo e(@$serial ? $serial->id : '0'); ?>" value="<?php echo e(old('price', @$serial->price ?? '')); ?>" hidden>
                            </div>
                        </div>
                    </div>
                    <input type="text" id="detail-<?php echo e($uid); ?>" class="form-control" name="detail"
                        placeholder="xxxx@xxx.xx" value="<?php echo e(old('detail', @$serial->detail ?? '')); ?>" hidden>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary waves-effect"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/warehouse/equivalent/form.blade.php ENDPATH**/ ?>