<form action="<?php echo e(@$product ? route('product.update', @$product->id) : route('product.store')); ?>" method="post"
    enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>

    <?php if(@$product): ?>
        <?php echo method_field('patch'); ?>
    <?php endif; ?>
    <div class="modal animate__animated animate__fadeIn"
        id="<?php echo e(@$product ? 'updateProduct-' . @$product->id : 'createProduct'); ?>" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5-product-<?php echo e(@$product->id ?? 'new'); ?>">
                        <?php echo e(@$product ? 'Update Product' . @$product->commodity : 'Create Product'); ?>

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
                                <input type="text" id="commodity" class="form-control" name="commodity"
                                    placeholder="W XXX" value="<?php echo e(old('commodity', @$product->commodity ?? '')); ?>">
                                <label for="commodity">Commodity</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="dimension" class="form-control" name="dimension"
                                    placeholder="xxx x xxx x xxx x"
                                    value="<?php echo e(old('dimension', @$product->dimension ?? '')); ?>">
                                <label for="dimension">Dismension</label>
                            </div>
                        </div>
                    </div>
                    <input type="text" id="type" class="form-control" name="type"
                    placeholder="W XXX" value="<?php echo e(old('type', @$product->type ?? 'Sparepart')); ?>" hidden>
                    <div class="row g-2 mb-3">
                        <div class="col-6 col-md-3 mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select invoice-item-info" id="unit"
                                    aria-label="Default select example" name="unit">
                                    <option disabled>----- Info Quantity -----</option>
                                    <option value="Pcs" <?php echo e(@$product->unit == 'Pcs' ? 'selected' : ''); ?>>Pcs
                                    </option>
                                    <option value="Set" <?php echo e(@$product->unit == 'Set' ? 'selected' : ''); ?>>Set
                                    </option>
                                    <option value="Pail" <?php echo e(@$product->unit == 'Pail' ? 'selected' : ''); ?>>Pail
                                    </option>
                                    <option value="Unit" <?php echo e(@$product->unit == 'Unit' ? 'selected' : ''); ?>>Unit
                                    </option>
                                    <option value="Lot" <?php echo e(@$product->unit == 'Lot' ? 'selected' : ''); ?>>Lot
                                    </option>
                                </select>
                                <label for="exampleFormControlSelect1">Unit</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-2 ">
                            <div class="input-group h-100">
                                <input type="number" name="weight" class="form-control" placeholder="Put Weight Here" aria-label="Put Weight Here" aria-describedby="basic-addon43" value="<?php echo e(@$product->weight ?? ''); ?>">
                                <span class="input-group-text" id="basic-addon43">gr</span>
                              </div>
                        </div>
                        <div class="col-21 col-md-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="category" aria-label="Default select example"
                                    name="category">
                                    <option disabled>----- Choose Category -----</option>
                                    <option value="Consumable Part"
                                        <?php echo e(@$product->category == 'Consumable Part' ? 'selected' : ''); ?>>
                                        Consumable Part
                                    </option>
                                    <option value="Non Consumable Part"
                                        <?php echo e(@$product->category == 'Non Consumable Part' ? 'selected' : ''); ?>>
                                        Non Consumable Part
                                    </option>
                                    <option value="Unit"
                                        <?php echo e(@$product->category == 'Unit' ? 'selected' : ''); ?>>
                                        Unit
                                    </option>
                                    <option value="Jasa"
                                        <?php echo e(@$product->category == 'Jasa' ? 'selected' : ''); ?>>
                                        Jasa
                                    </option>
                                </select>
                                <label for="selectSource">Category</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectG/O" aria-label="Default select example"
                                    name="go">
                                    <option disabled>----- Choose G/O -----</option>
                                    <option value="Genuine" <?php echo e(@$product->go == 'Genuine' ? 'selected' : ''); ?>>
                                        Genuine
                                    </option>
                                    <option value="Replacement" <?php echo e(@$product->go == 'Replacement' ? 'selected' : ''); ?>>
                                        Replacement
                                    </option>
                                </select>
                                <label for="selectG/O">Genuine/Replacement
                                </label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="detail_desc" class="form-control" name="detail_desc"
                                    placeholder="Short Description"
                                    value="<?php echo e(old('detail_desc', @$product->detail_desc ?? '')); ?>">
                                <label for="detail_desc">Short Descroption</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <textarea type="text" id="description" class="form-control h-px-100" name="description" placeholder="xxxxxxx"
                                    cols="30" rows="10"><?php echo e(old('description', @$product->description ?? '')); ?></textarea>
                                <label for="description">Description</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col g-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <textarea class="form-control h-px-100" name="note" id="noteTextarea1"
                                    placeholder="Contoh: Jl Taman Kopo Indah 5 Kota..."><?php echo e(old('note', @$product->note ?? '')); ?></textarea>
                                <label for="noteTextarea1">note</label>
                            </div>
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
</form>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/warehouse/product/form.blade.php ENDPATH**/ ?>