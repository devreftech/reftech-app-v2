<form action="<?php echo e(@$existing ? route('existing.update', @$existing->id) : route('existing.store')); ?>" method="post"
    enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>

    <?php if(@$existing): ?>
        <?php echo method_field('patch'); ?>
    <?php endif; ?>
    <div class="modal animate__animated animate__fadeIn"
        id="<?php echo e(@$existing ? 'updateExisting' . strval(@$existing->id) : 'createExisting'); ?>" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5"><?php echo e(@$existing ? 'Update Data' : 'Create New'); ?>

                        Existing
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
                                <input type="text" id="company" class="form-control" name="company"
                                    placeholder="Mr/Mss xxxx" value="<?php echo e(old('company', @$existing->company ?? '')); ?>">
                                <label for="company">Company</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectVia" aria-label="Default select example"
                                    name="info">
                                    <option disabled>----- Choose Via -----</option>
                                    <option value="Reftech"
                                        <?php echo e(old('info', @$existing->info) == 'Reftech' ? 'selected' : ''); ?>>
                                        Reftech
                                    </option>
                                    <option value="Kojisha"
                                        <?php echo e(old('info', @$existing->info) == 'Kojisha' ? 'selected' : ''); ?>>Kojisha
                                    </option>
                                </select>
                                <label for="selectSource">Via</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="email" class="form-control" name="email"
                                    placeholder="xxxx@xxx.xx" value="<?php echo e(old('email', @$existing->email ?? '')); ?>">
                                <label for="email">Email</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="phone" id="phoneAnimation" class="form-control" name="phone"
                                    placeholder="081xxxxx" value="<?php echo e(old('phone', @$existing->phone ?? '')); ?>">
                                <label for="phoneAnimation">Phone</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="unitsiteAnimation" class="form-control" name="unit"
                                    placeholder="XXX-21" value="<?php echo e(old('unit', @$existing->unit ?? '')); ?>">
                                <label for="unitsiteAnimation">Unit</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectR/U" aria-label="Default select example"
                                    name="ru">
                                    <option disabled>----- Choose R/U -----</option>
                                    <option value="User" <?php echo e(old('ru', @$existing->ru) == 'User' ? 'selected' : ''); ?>>
                                        User
                                    </option>
                                    <option value="Reseller"
                                        <?php echo e(old('ru', @$existing->ru) == 'Reseller' ? 'selected' : ''); ?>>Reseller
                                    </option>
                                </select>
                                <label for="selectSource">R/U</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectSource" aria-label="Default select example"
                                    name="source">
                                    <option disabled>----- Choose Source -----</option>
                                    <option value="IG"
                                        <?php echo e(old('source', @$existing->source) == 'IG' ? 'selected' : ''); ?>>Instagram
                                    </option>
                                    <option value="LinkedIn"
                                        <?php echo e(old('source', @$existing->source) == 'LinkedIn' ? 'selected' : ''); ?>>
                                        LinkedIn
                                    </option>
                                    <option value="Website"
                                        <?php echo e(old('source', @$existing->source) == 'Website' ? 'selected' : ''); ?>>Website
                                    </option>
                                    <option value="Iklan"
                                        <?php echo e(old('source', @$existing->source) == 'Iklan' ? 'selected' : ''); ?>>Iklan
                                    </option>
                                    <option value="Google"
                                        <?php echo e(old('source', @$existing->source) == 'Google' ? 'selected' : ''); ?>>Google
                                    </option>
                                    <option value="Other"
                                        <?php echo e(old('source', @$existing->source) == 'Other' ? 'selected' : ''); ?>>Other
                                    </option>
                                </select>
                                <label for="selectSource">Source</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectMobile" aria-label="Default select example"
                                    name="mobile">
                                    <option disabled>----- Choose Mobile -----</option>
                                    <option value="WA"
                                        <?php echo e(old('mobile', @$existing->mobile) == 'WA' ? 'selected' : ''); ?>>
                                        WhatsApp</option>
                                    <option value="Phone Office"
                                        <?php echo e(old('mobile', @$existing->mobile) == 'Phone Office' ? 'selected' : ''); ?>>
                                        Phone
                                        Office</option>
                                </select>
                                <label for="selectMobile">Mobile</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-12 mb-2">
                            <div class="form-floating form-floating-outline">
                                <select id="selectAreaExisting<?php echo e(@$existing->id ?? 'Create'); ?>" class="select2 form-select select-area-existing" name="area">
                                    <option value=""></option>
                                    <?php $selectedArea = old('area', @$existing->area ?? ''); ?>
                                    <?php if($selectedArea): ?>
                                        <option value="<?php echo e($selectedArea); ?>" selected><?php echo e($selectedArea); ?></option>
                                    <?php endif; ?>
                                </select>
                                <label for="selectAreaExisting<?php echo e(@$existing->id ?? 'Create'); ?>">Area</label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="npwp" value="<?php echo e(old('npwp', @$existing->npwp ?? '')); ?>">
                    <div class="row g-2 mb-3">
                        <div class="col-12 mb-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <textarea class="form-control h-px-100" name="address" id="addressTextarea1"
                                    placeholder="Contoh: Jl Taman Kopo Indah 5 Kota..."><?php echo e(old('address', @$existing->address ?? '')); ?></textarea>
                                <label for="addressTextarea1">Office / Factory Address</label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="subAddress" value="<?php echo e(old('subAddress', @$existing->subAddress ?? '')); ?>">
                    <?php if(empty($existing)): ?>
                        <div class="divider divider-dark mx-3">
                            <div class="divider-text"><span class="fw-semibold">Personal In Charge</span></div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="nameAnimation" class="form-control" name="namePic"
                                        placeholder="xxxxxxx xxxxxxxx"
                                        value="<?php echo e(old('namePic', @$existing->pic->name_pic ?? '')); ?>">
                                    <label for="nameAnimation">Name</label>
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="positionAnimation" class="form-control" name="position"
                                        placeholder="example: CEO"
                                        value="<?php echo e(old('position', @$existing->pic->position ?? '')); ?>">
                                    <label for="positionAnimation">Position</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="emailPicAnimation" class="form-control" name="emailPic"
                                        placeholder="xxxxxxxx@xxx.xx"
                                        value="<?php echo e(old('emailPic', @$existing->pic->email_pic ?? '')); ?>">
                                    <label for="emailPicAnimation">Email PIC</label>
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="phone" id="phonePicAnimation" class="form-control" name="phonePic"
                                        placeholder="08xxxxxxxxxx"
                                        value="<?php echo e(old('phonePic', @$existing->pic->phone_pic ?? '')); ?>">
                                    <label for="phonePicAnimation">Phone PIC</label>
                                </div>
                            </div>
                        </div>
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

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script>
        $(function () {
            $('.select-area-existing').each(function () {
                var $this = $(this);
                $this.select2({
                    placeholder: 'Area',
                    width: '100%',
                    dropdownParent: $this.closest('.modal'),
                    minimumInputLength: 2,
                    language: {
                        inputTooShort: function () { return 'Ketik minimal 2 karakter...'; },
                        searching: function () { return 'Mencari...'; },
                        noResults: function () { return 'Kota/Kabupaten tidak ditemukan'; }
                    },
                    ajax: {
                        url: '<?php echo e(route('kota.search')); ?>',
                        dataType: 'json',
                        delay: 300,
                        data: function (params) { return { q: params.term }; },
                        processResults: function (data) { return { results: data }; },
                        cache: true
                    }
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sales/existing/form.blade.php ENDPATH**/ ?>