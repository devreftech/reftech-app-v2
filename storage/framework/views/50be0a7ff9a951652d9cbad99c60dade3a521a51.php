<form
    action="<?php echo e(@$serial ? route('product.equivalent.update', $serial->id) : route('product.equivalent', $product->id)); ?>"
    method="post" enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>

    <?php if(@$serial): ?>
        <?php echo method_field('patch'); ?>
    <?php endif; ?>
    <div class="modal animate__animated animate__fadeIn"
        id="<?php echo e(@$serial ? 'editEquivalent-' . $serial->id : 'createEquivalent-' . $product->id); ?>" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5"> Create Equivalent
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
                                <input type="text" id="image" class="form-control" name="image"
                                    placeholder=" Example : https://drive.google.com/drive/folders/**********"
                                    value="<?php echo e(old('image', @$serial->image ?? '')); ?>">
                                <label for="image">Image ( Link GDrive )</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="brand" class="form-control" name="brand"
                                    placeholder="xxx x xxx x xxx x" value="<?php echo e(old('brand', @$serial->brand ?? '')); ?>">
                                <label for="brand">Brand</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="pn" class="form-control" name="pn"
                                    placeholder="Type" value="<?php echo e(old('pn', @$serial->pn ?? '')); ?>">
                                <label for="pn">Type</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="bar" class="form-control" name="bar"
                                    placeholder="Type" value="<?php echo e(old('bar', @$serial->bar ?? '')); ?>">
                                <label for="bar">Bar</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="air_cap" class="form-control" name="air_cap"
                                    placeholder="Type" value="<?php echo e(old('air_cap', @$serial->air_cap ?? '')); ?>">
                                <label for="air_cap">Air Capacity</label>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating form-floating-outline mb-4">
                                <textarea class="form-control h-px-100" name="detail" id="detailTextarea1"
                                    placeholder="Detail Description Of Equivalent"><?php echo e(old('detail', @$serial->detail ?? '')); ?></textarea>
                                <label for="detailTextarea1">Detail</label>
                            </div>
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
    </div>
</form>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/warehouse/equivalent/form-global.blade.php ENDPATH**/ ?>