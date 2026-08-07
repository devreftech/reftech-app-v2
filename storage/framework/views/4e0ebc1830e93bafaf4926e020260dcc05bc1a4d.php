<form action="<?php echo e(@$leads ? route('leads.update', @$leads->id) : route('leads.store')); ?>" method="post"
    enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>

    <?php if(@$leads): ?>
        <?php echo method_field('patch'); ?>
    <?php endif; ?>
    <div class="modal fade"
        id="<?php echo e(@$leads ? 'updateLeads' . strval(@$leads->id) : 'createLeads'); ?>" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5"><?php echo e(@$leads ? 'Update Data' : 'Create New'); ?> Leads
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
                                    placeholder="PT xxxxxxx" value="<?php echo e(old('company', @$leads->company ?? '')); ?>">
                                <label for="company">Company</label>
                            </div>
                        </div>
                        <div class="col mb-2 d-flex align-items-center">
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="ru" id="ruUser" value="User"
                                    autocomplete="off" required
                                    <?php echo e(old('ru', @$leads->ru) == 'User' ? 'checked' : ''); ?>>
                                <label class="btn btn-outline-primary" for="ruUser">User</label>

                                <input type="radio" class="btn-check" name="ru" id="ruReseller" value="Reseller"
                                    autocomplete="off" required
                                    <?php echo e(old('ru', @$leads->ru) == 'Reseller' ? 'checked' : ''); ?>>
                                <label class="btn btn-outline-primary" for="ruReseller">Reseller</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="phone" id="phoneAnimation" class="form-control" name="phone"
                                    placeholder="081xxxxx" value="<?php echo e(old('phone', @$leads->phone ?? '')); ?>">
                                <label for="phoneAnimation">Phone</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="unitsiteAnimation" class="form-control" name="unit"
                                    placeholder="xxx-21" value="<?php echo e(old('unit', @$leads->unit ?? '')); ?>">
                                <label for="unitsiteAnimation">Unit</label>
                            </div>
                        </div>
                        <?php if(Auth::user()->id == '1' || Auth::user()->id == '16' || Auth::user()->id == '23'): ?>
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" id="selectVia" aria-label="Default select example"
                                        name="info">
                                        <option disabled>----- Choose Via -----</option>
                                        <option value="Reftech"
                                            <?php echo e(old('info', @$leads->info) == 'Reftech' ? 'selected' : ''); ?>>
                                            Reftech
                                        </option>
                                        <option value="Kojisha"
                                            <?php echo e(old('info', @$leads->info) == 'Kojisha' ? 'selected' : ''); ?>>Kojisha
                                        </option>
                                    </select>
                                    <label for="selectVia">Via</label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select" id="selectSource" aria-label="Default select example"
                                    name="source">
                                    <option value="" disabled>----- Choose Source -----</option>
                                    <option value="Direct Whatsapp"
                                        <?php echo e(old('source', @$leads->source) == 'Direct Whatsapp' ? 'selected' : ''); ?>>
                                        Direct Whatsapp
                                    </option>
                                    <option value="Canvasing"
                                        <?php echo e(old('source', @$leads->source) == 'Canvasing' ? 'selected' : ''); ?>>
                                        Canvasing
                                    </option>
                                    <option value="Instagram"
                                        <?php echo e(old('source', @$leads->source) == 'Instagram' ? 'selected' : ''); ?>>
                                        Instagram
                                    </option>
                                    <option value="LinkedIn"
                                        <?php echo e(old('source', @$leads->source) == 'LinkedIn' ? 'selected' : ''); ?>>LinkedIn
                                    </option>
                                    <option value="Other"
                                        <?php echo e(old('source', @$leads->source) == 'Other' ? 'selected' : ''); ?>>Other
                                    </option>
                                </select>
                                <label for="selectSource">Source</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select id="selectArea" class="select2 form-select" name="area">
                                    <option value=""></option>
                                    <?php $selectedArea = old('area', @$leads->area ?? ''); ?>
                                    <?php if($selectedArea): ?>
                                        <option value="<?php echo e($selectedArea); ?>" selected><?php echo e($selectedArea); ?></option>
                                    <?php endif; ?>
                                </select>
                                <label for="selectArea">Area</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-12 mb-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <textarea class="form-control h-px-100" name="address" id="addressTextarea1"
                                    placeholder="Contoh: Jl Taman Kopo Indah 5 Kota..."><?php echo e(old('address', @$leads->address ?? '')); ?></textarea>
                                <label for="addressTextarea1">Office / Factory Address</label>
                            </div>
                        </div>
                    </div>
                    <?php if(!empty($leads)): ?>
                        <div class="row g-2 mb-3">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="email" class="form-control" name="email"
                                        placeholder="xxxx@xxx.xx" value="<?php echo e(old('email', @$leads->email ?? '')); ?>">
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" id="selectMobile" aria-label="Default select example"
                                        name="mobile">
                                        <option disabled>----- Choose Mobile -----</option>
                                        <option value="WA"
                                            <?php echo e(old('mobile', @$leads->mobile) == 'WA' ? 'selected' : ''); ?>>
                                            WhatsApp</option>
                                        <option value="Phone Office"
                                            <?php echo e(old('mobile', @$leads->mobile) == 'Phone Office' ? 'selected' : ''); ?>>Phone
                                            Office</option>
                                    </select>
                                    <label for="selectMobile">Mobile</label>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="npwp" value="<?php echo e(old('npwp', @$leads->npwp ?? '')); ?>">
                        <input type="hidden" name="subAddress" value="<?php echo e(old('subAddress', @$leads->subAddress ?? '')); ?>">
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
            $('#selectSource').select2({
                placeholder: '----- Choose Source -----',
                width: '100%',
                dropdownParent: $('#selectSource').closest('.modal')
            });

            $('#selectArea').select2({
                placeholder: 'Area',
                width: '100%',
                dropdownParent: $('#selectArea').closest('.modal'),
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

            <?php if($errors->any() && old('company') !== null): ?>
                var leadsModalEl = document.getElementById('<?php echo e(@$leads ? 'updateLeads' . strval(@$leads->id) : 'createLeads'); ?>');
                if (leadsModalEl) {
                    bootstrap.Modal.getOrCreateInstance(leadsModalEl).show();
                }
            <?php endif; ?>
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sales/clients/leads/form.blade.php ENDPATH**/ ?>