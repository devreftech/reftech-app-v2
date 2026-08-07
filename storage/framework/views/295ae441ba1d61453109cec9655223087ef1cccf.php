<form action="<?php echo e(route('service-reports.image', $service->id)); ?>" method="post" enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>
    <div class="modal modal-xl animate__animated animate__fadeIn" id="inputImage" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="exampleModalLabel5">Input Image <?php echo e($service->no_service); ?> -
                        <?php echo e($service->pic->client->company); ?>

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
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="formFileMultiplePict" class="form-label">Picture</label>
                                <input class="form-control" type="file" id="formFileMultiplePict" name="image[]"
                                    multiple="" accept="image/*">
                                <div class="row" id="photo-preview">
                                    <?php
                                        $i = 1;
                                    ?>
                                    <div id="dynamicInputsPhotoContainer">
                                    </div>
                                    <?php if(@$image): ?>
                                        <?php $__currentLoopData = $image; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="photo-container">
                                                <img src="<?php echo e(url('') . '/' . $item->picture); ?>" alt=""
                                                    srcset="">
                                                <p>Photo <?php echo e($i); ?> - <?php echo e($item->keterangan); ?></p>
                                            </div>
                                            <div id="dynamicInputsPhotoContainer">
                                                <input class="form-control mb-2" type="text" name="description[]"
                                                    placeholder="Deskripsi untuk File <?php echo e($i); ?>"
                                                    value="<?php echo e(@$item->keterangan); ?>">
                                            </div>
                                            <?php
                                                $i++;
                                            ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </div>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/service/image.blade.php ENDPATH**/ ?>