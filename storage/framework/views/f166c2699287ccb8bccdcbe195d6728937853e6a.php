<form action="" method="post" enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>
    <div class="modal animate__animated animate__zoomIn" id="createVisits" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Create New Visits</h4>
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
                                <select class="form-select" id="selectSales" name="sales"
                                    aria-label="Default select example">
                                    <option disabled>----- Choose Sales -----</option>
                                    <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $saless): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($saless->id); ?>"><?php echo e($saless->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <label for="selectSales">Sales</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="pic" id="picAnimation" class="form-control" name="company"
                                    placeholder="Mr/Mss xxxx">
                                <label for="picAnimation">Company</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="email" id="emailAnimation" class="form-control" name="email"
                                    placeholder="xxxx@xxx.xx">
                                <label for="emailAnimation">Email</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="phone" id="phoneAnimation" class="form-control" name="phone"
                                    placeholder="081xxxxx">
                                <label for="phoneAnimation">Phone</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="websiteAnimation" class="form-control" name="web"
                                    placeholder="xxxxxxxxx.com">
                                <label for="websiteAnimation">Website</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectSource" aria-label="Default select example"
                                    name="source">
                                    <option disabled>----- Choose Source -----</option>
                                    <option value="IG">Instagram</option>
                                    <option value="LinkedIn">LinkedIn</option>
                                    <option value="Website">Website</option>
                                    <option value="Iklan">Iklan</option>
                                </select>
                                <label for="selectSource">Source</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectMobile" aria-label="Default select example"
                                    name="mobile">
                                    <option disabled>----- Choose Mobile -----</option>
                                    <option value="WA">WhatsApp</option>
                                    <option value="Phone Office">Phone Office</option>
                                </select>
                                <label for="selectMobile">Mobile</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectMachine" aria-label="Default select example"
                                    name="dcompressor">
                                    <option disabled>----- Choose Machine -----</option>
                                    <?php $__currentLoopData = $dcompressor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compressor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($compressor->id); ?>"><?php echo e($compressor->serial_number); ?> ||
                                            <?php echo e($compressor->compressor->compressor_brand); ?>,Type
                                            <?php echo e($compressor->compressor->series); ?> , <?php echo e($compressor->hp); ?> HP
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <label for="selectMachine">Machine</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="areaAnimation" class="form-control"
                                    placeholder="Contoh: Bandung" name="area">
                                <label for="areaAnimation">Area</label>
                            </div>
                        </div>
                        <div class="col"></div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <textarea class="form-control h-px-100" name="address" id="addressTextarea1"
                                    placeholder="Contoh: Jl Taman Kopo Indah 5 Kota..."></textarea>
                                <label for="addressTextarea1">Address</label>
                            </div>
                        </div>
                    </div>
                    <div class="divider divider-dark mx-3">
                        <div class="divider-text"><span class="fw-semibold">Personal In Charge</span></div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="nameAnimation" class="form-control" name="namePic"
                                    placeholder="xxxxxxx xxxxxxxx">
                                <label for="nameAnimation">Name</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="positionAnimation" class="form-control" name="position"
                                    placeholder="example: CEO">
                                <label for="positionAnimation">Position</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="emailPicAnimation" class="form-control" name="emailPic"
                                    placeholder="xxxxxxxx@xxx.xx">
                                <label for="emailPicAnimation">Email PIC</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="phone" id="phonePicAnimation" class="form-control" name="phonePic"
                                    placeholder="08xxxxxxxxxx">
                                <label for="phonePicAnimation">Phone PIC</label>
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


<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/bootstrap-select/bootstrap-select.css" />
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/forms-selects.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sales/visits/form.blade.php ENDPATH**/ ?>