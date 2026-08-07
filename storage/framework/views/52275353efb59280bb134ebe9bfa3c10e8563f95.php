
<?php $__env->startSection('title', 'Form Audit Tools'); ?>
<?php $__env->startSection('content'); ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
    <div class="card mb-4">
        <form action="<?php echo e(route('audit-tools.update', $audit->id)); ?>" id="" method="post"
            enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('patch'); ?>
            <div class="card-header">
                <h4>Form Audit : <?php echo e($audit->technician->name); ?></h4>
                <h5>Month <?php echo e(now()->format('F')); ?></h5>
            </div>
            <div class="card-body pt-2 mt-1">
                <div class="row mt-2 gy-4">
                    <div class="col-8">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="noAudit" aria-describedby="noAuditHelp"
                                name="no_audit"
                                value="<?php echo e($audit->technician->code . '/' . $formattedMonthNow . '/' . \Carbon\Carbon::now()->year); ?>">
                            <label for="noAudit">Number Audit</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-floating form-floating-outline mb-4">
                            <input class="form-control" type="date" id="date" name="date"
                                value="<?php echo e(now()->format('Y-m-d')); ?>">
                            <label for="date">Date</label>
                        </div>
                    </div>
                </div>
                <h5>Tools</h5>
                <?php
                    $no = 0;
                    $asment = 0;
                ?>
                <?php $__currentLoopData = $tools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $no++;
                    ?>
                    <hr>
                    <div class="row gy-4">
                        <div class="col-1">
                            <p class="text-nowrap">
                                <?php echo e($no); ?>

                            </p>
                        </div>
                        <div class="col-8 col-lg-3">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" id="tools" name="tools[]"
                                    value="<?php echo e($tool->tools); ?>" placeholder="Type Your Tools Here..." />
                                <label for="tools">Tools</label>
                            </div>
                        </div>
                        <div class="col-3 col-lg-1">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="number" id="qty" name="qty[]"
                                    value="<?php echo e($tool->qty); ?>" />
                                <label for="qty">Quantity</label>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" id="desc" name="desc[]"
                                    value="<?php echo e($tool->desc); ?>" placeholder="Type Your Description Here..." />
                                <label for="desc">Description</label>
                            </div>
                        </div>
                        <div class="col-6 col-lg-4">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="assesment[<?php echo e($asment); ?>]"
                                    id="assesment1" value="Ada" <?php echo e($tool->assesment == 'Ada' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="assesment1">Ada</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="assesment[<?php echo e($asment); ?>]"
                                    id="assesment2" value="Tidak Lengkap">
                                <label class="form-check-label" for="assesment2">Tidak Lengkap</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="assesment[<?php echo e($asment); ?>]"
                                    id="assesment3" value="Hilang" <?php echo e($tool->assesment == 'Hilang' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="assesment3">Hilang</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" id="note" name="note[]"
                                    value="<?php echo e($tool->note); ?>" placeholder="Barang A Hilang" />
                                <label for="note">Note</label>
                            </div>
                        </div>
                    </div>
                    <?php
                        $asment++;
                    ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="row mt-2 gy-4">
                    <div class="col-md-4"></div>
                    <div class="col-6 col-md-4">
                        <div class="form-floating form-floating-outline mb-4">
                            <select class="form-select" id="exampleFormControlSelect1" aria-label="Default select example" name="status">
                                <option value="OK" <?php echo e($audit->status == 'OK' ? 'selected' : ''); ?>>OK</option>
                                <option value="Not OK" <?php echo e($audit->status == 'Not OK' ? 'selected' : ''); ?>>Not OK</option>
                            </select>
                            <label for="exampleFormControlSelect1">Status</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="text" id="note" name="noteD"
                                value="<?php echo e($audit->note); ?>" placeholder="Barang A Hilang" />
                            <label for="noteD">Detail Note</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">Save changes</button>
                    <a href="<?php echo e(route('audit-tools.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/technician/audit-tools/form.blade.php ENDPATH**/ ?>