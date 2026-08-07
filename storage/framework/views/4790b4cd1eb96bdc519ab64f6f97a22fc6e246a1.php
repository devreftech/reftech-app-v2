<form action="" method="post" enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>
    <div class="modal animate__animated animate__fadeIn" id="detailNotulen<?php echo e($notulen->id); ?>" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Detail Notulen
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card-body">
                                <div class="row mb-1">
                                    <div class="col-4">
                                        Mention To
                                    </div>
                                    <div class="col-8">
                                        : <?php echo e($notulen->name); ?>

                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-4">
                                        Title
                                    </div>
                                    <div class="col-8">
                                        : <?php echo e($notulen->title); ?>

                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-4">
                                        Date
                                    </div>
                                    <div class="col-8">
                                        : <?php echo e($notulen->date->format('d-m-Y')); ?>

                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-4">
                                        Description
                                    </div>
                                    <div class="col-8">
                                        <pre class="mb-0"
                                            style="font-size: 15px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: <?php echo e($notulen->desc); ?></pre>
                                    </div>
                                </div>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/notulen/detail.blade.php ENDPATH**/ ?>