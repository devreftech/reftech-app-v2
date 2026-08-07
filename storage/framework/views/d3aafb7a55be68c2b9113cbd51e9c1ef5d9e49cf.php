 <form action="" method="post" enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>
    <div class="modal modal-xl animate__animated animate__fadeIn" id="createProspect" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Create Prospect
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
                        <div class="col-12 col-md-6">
                            <h6>Company </h6>
                            <div class="row g-2 mb-3">
                                <div class="col-12 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="company" class="form-control" name="company"
                                            placeholder="PT xxxxxxx"
                                            value="<?php echo e(old('company', @$leads->company ?? '')); ?>">
                                        <label for="company">Company</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="email" id="emailAnimation" class="form-control" name="email"
                                            placeholder="company@email.com" value="<?php echo e(old('email', @$leads->email ?? '')); ?>">
                                        <label for="emailAnimation">Email</label>
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="phone" id="phoneAnimation" class="form-control" name="phone"
                                            placeholder="081xxxxx" value="<?php echo e(old('phone', @$leads->phone ?? '')); ?>">
                                        <label for="phoneAnimation">Phone</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-4 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="selectMobile"
                                            aria-label="Default select example" name="mobile">
                                            <option disabled>----- Choose Mobile -----</option>
                                            <option value="WA"
                                                <?php echo e(old('mobile', @$leads->mobile) == 'WA' ? 'selected' : ''); ?>>
                                                WhatsApp</option>
                                            <option value="Phone Office"
                                                <?php echo e(old('mobile', @$leads->mobile) == 'Phone Office' ? 'selected' : ''); ?>>
                                                Phone
                                                Office</option>
                                        </select>
                                        <label for="selectMobile">Mobile</label>
                                    </div>
                                </div>
                                <div class="col-4 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="selectR/U" aria-label="Default select example"
                                            name="ru">
                                            <option disabled>----- Choose R/U -----</option>
                                            <option value="User"
                                                <?php echo e(old('ru', @$leads->ru) == 'User' ? 'selected' : ''); ?>>
                                                User
                                            </option>
                                            <option value="Reseller"
                                                <?php echo e(old('ru', @$leads->ru) == 'Reseller' ? 'selected' : ''); ?>>Reseller
                                            </option>
                                        </select>
                                        <label for="selectR/U">R/U</label>
                                    </div>
                                </div>
                                <div class="col-4 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="selectSource"
                                            aria-label="Default select example" name="source">
                                            <option disabled>----- Choose Source -----</option>
                                            <option value="IG"
                                                <?php echo e(old('source', @$leads->source) == 'IG' ? 'selected' : ''); ?>>Instagram
                                            </option>
                                            <option value="WhatsApp"
                                                <?php echo e(old('source', @$leads->source) == 'WhatsApp' ? 'selected' : ''); ?>>
                                                WhatsApp
                                            </option>
                                            <option value="LinkedIn"
                                                <?php echo e(old('source', @$leads->source) == 'LinkedIn' ? 'selected' : ''); ?>>
                                                LinkedIn
                                            </option>
                                            <option value="Website"
                                                <?php echo e(old('source', @$leads->source) == 'Website' ? 'selected' : ''); ?>>
                                                Website
                                            </option>
                                            <option value="Indotrading"
                                                <?php echo e(old('source', @$leads->source) == 'Indotrading' ? 'selected' : ''); ?>>
                                                Indotrading
                                            </option>
                                            <option value="Tokopedia"
                                                <?php echo e(old('source', @$leads->source) == 'Tokopedia' ? 'selected' : ''); ?>>
                                                Tokopedia
                                            </option>
                                            <option value="OLX"
                                                <?php echo e(old('source', @$leads->source) == 'OLX' ? 'selected' : ''); ?>>OLX
                                            </option>
                                            <option value="Google"
                                                <?php echo e(old('source', @$leads->source) == 'Google' ? 'selected' : ''); ?>>
                                                Google
                                            </option>
                                            <option value="Google Ads"
                                                <?php echo e(old('source', @$leads->source) == 'Google Ads' ? 'selected' : ''); ?>>
                                                Google Ads
                                            </option>
                                            <option value="Meta Ads"
                                                <?php echo e(old('source', @$leads->source) == 'Meta Ads' ? 'selected' : ''); ?>>
                                                Meta Ads
                                            </option>
                                            <option value="Facebook"
                                                <?php echo e(old('source', @$leads->source) == 'Facebook' ? 'selected' : ''); ?>>
                                                Facebook
                                            </option>
                                            <option value="Other"
                                                <?php echo e(old('source', @$leads->source) == 'Other' ? 'selected' : ''); ?>>Other
                                            </option>
                                        </select>
                                        <label for="selectSource">Source</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mb-3" id="domainWrapper" style="display:none;">
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="domainInput" name="source_detail"
                                            list="domainList" maxlength="100" placeholder="example.com"
                                            value="<?php echo e(old('source_detail', '')); ?>">
                                        <label for="domainInput">Website Domain (optional)</label>
                                    </div>
                                    <datalist id="domainList">
                                        <?php $__currentLoopData = $domainList ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($d); ?>"></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </datalist>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col mb-2">
                                    <select id="selectArea" class="form-select" name="area" style="width:100%">
                                        <option value=""></option>
                                        <?php $selectedArea = old('area', @$leads->area ?? ''); ?>
                                        <?php if($selectedArea): ?>
                                            <option value="<?php echo e($selectedArea); ?>" selected><?php echo e($selectedArea); ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-2 mb-3"> 
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline mb-4">
                                        <textarea class="form-control h-px-100" name="address" id="addressTextarea1"
                                            placeholder="Contoh: Jl Taman Kopo Indah 5 Kota..."><?php echo e(old('address', @$leads->address ?? '')); ?></textarea>
                                        <label for="addressTextarea1">Address</label>
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline mb-4">
                                        <textarea class="form-control h-px-100" name="subAddress" id="addressTextarea2"
                                            placeholder="Contoh: Jl Taman Kopo Indah 5 Kota..."><?php echo e(old('subAddress', @$leads->subAddress ?? '')); ?></textarea>
                                        <label for="addressTextarea2">Sub Address</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <h6> PIC </h6>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="nameAnimation" class="form-control" name="namePic"
                                            placeholder="xxxxxxx xxxxxxxx"
                                            value="<?php echo e(old('namePic', @$leads->pic->name_pic ?? '')); ?>">
                                        <label for="nameAnimation">Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="positionAnimation" class="form-control"
                                            name="position" placeholder="example: CEO"
                                            value="<?php echo e(old('position', @$leads->pic->position ?? '')); ?>">
                                        <label for="positionAnimation">Position</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="emailPicAnimation" class="form-control"
                                            name="emailPic" placeholder="xxxxxxxx@xxx.xx"
                                            value="<?php echo e(old('emailPic', @$leads->pic->email_pic ?? '')); ?>">
                                        <label for="emailPicAnimation">Email PIC</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="phone" id="phonePicAnimation" class="form-control"
                                            name="phonePic" placeholder="08xxxxxxxxxx"
                                            value="<?php echo e(old('phonePic', @$leads->pic->phone_pic ?? '')); ?>">
                                        <label for="phonePicAnimation">Phone PIC</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="category"
                                            aria-label="Default select example" name="category">
                                            <option disabled>----- Choose Category -----</option>
                                            <option value="Service Compressor"
                                                <?php echo e(old('category', @$leads->category) == 'Service Compressor' ? 'selected' : ''); ?>>
                                                Service Compressor
                                            </option>
                                            <option value="Rental Compressor"
                                                <?php echo e(old('category', @$leads->category) == 'Rental Compressor' ? 'selected' : ''); ?>>
                                                Rental Compressor
                                            </option>
                                            <option value="Sparepart Compressor"
                                                <?php echo e(old('category', @$leads->category) == 'Sparepart Compressor' ? 'selected' : ''); ?>>
                                                Sparepart Compressor
                                            </option>
                                            <option value="Instalasi Piping"
                                                <?php echo e(old('category', @$leads->category) == 'Instalasi Piping' ? 'selected' : ''); ?>>
                                                Instalasi Piping
                                            </option>
                                            <option value="Air Audit"
                                                <?php echo e(old('category', @$leads->category) == 'Air Audit' ? 'selected' : ''); ?>>
                                                Air Audit
                                            </option>
                                            <option value="Fire System"
                                                <?php echo e(old('category', @$leads->category) == 'Fire System' ? 'selected' : ''); ?>>
                                                Fire System
                                            </option>
                                            <option value="HVAC System"
                                                <?php echo e(old('category', @$leads->category) == 'HVAC System' ? 'selected' : ''); ?>>
                                                HVAC System
                                            </option>
                                            <option value="Unit Baru/Second"
                                                <?php echo e(old('category', @$leads->category) == 'Unit Baru/Second' ? 'selected' : ''); ?>>
                                                Unit Baru/Second
                                            </option>
                                        </select>
                                        <label for="category">Category</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="unit" class="form-control" name="unit"
                                            placeholder="Contoh: KAESER SK 21"
                                            value="<?php echo e(old('unit', @$leads->unit ?? '')); ?>">
                                        <label for="unit">Unit Existing</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating form-floating-outline mb-4">
                                    <textarea class="form-control h-px-100" name="prospect" id="prosp" placeholder="Contoh: Oil Filter ....."></textarea>
                                    <label for="prosp">Prospect</label>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/client/support/form.blade.php ENDPATH**/ ?>