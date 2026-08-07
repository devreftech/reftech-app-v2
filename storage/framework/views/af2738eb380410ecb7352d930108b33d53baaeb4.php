<div class="modal animate__animated animate__fadeIn" id="editIssue-<?php echo e($comp['id']); ?>" tabindex="-1"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="exampleModalLabel5"> Edit Monitoring <?php echo e($comp['date']); ?>

                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('service-manager-daily.issue-update', [$comp['id'], $months])); ?>" method="post"
                    enctype="multipart/form-data" id="myForm">
                    <?php echo method_field('PATCH'); ?>
                    <?php echo csrf_field(); ?>
                    <?php if($comp['unit'] != 'REFRIGERANT AIR DRYER'): ?>
                        <div class="daily-compressor">
                            <?php if($errors->any()): ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <div class="row mb-3">
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Condition</label>
                                    <select id="conditionSelect" name="condition" class="form-select">
                                        <option value="Running" <?php echo e($comp['condition'] == 'Running' ? 'selected' : ''); ?>>
                                            Running</option>
                                        <option value="Stand By"
                                            <?php echo e($comp['condition'] == 'Stand By' ? 'selected' : ''); ?>>Stand By</option>
                                        <option value="Off" <?php echo e($comp['condition'] == 'Off' ? 'selected' : ''); ?>>Off
                                        </option>
                                    </select>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Oil Level</label>
                                    <select id="offDisable" name="oil" class="form-select offDisable">
                                        <option value="OK" <?php echo e($comp['oil_level'] == 'OK' ? 'selected' : ''); ?>>OK
                                        </option>
                                        <option value="Kurang" <?php echo e($comp['oil_level'] == 'Kurang' ? 'selected' : ''); ?>>
                                            Kurang</option>
                                    </select>
                                </div>
                                <div class="col col-lg-3">
                                    <label for="defaultInput" class="form-label">Running</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="running"
                                            type="text" placeholder="Hr" min="1"
                                            oninput="validateInput(event)"
                                            value="<?php echo e((int) str_replace('.', '', strtok($comp['running'], ' '))); ?>">
                                        <span class="input-group-text">Hours</span>
                                    </div>
                                </div>
                                <div class="col col-lg-3">
                                    <label for="defaultInput" class="form-label">Loading Hr</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="loading"
                                            type="text" placeholder="Hr" min="1"
                                            oninput="validateInput(event)"
                                            value="<?php echo e((int) str_replace('.', '', strtok($comp['loading'], ' '))); ?>">
                                        <span class="input-group-text">Hours</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-lg-6">
                                    <label for="defaultInput" class="form-label">Pressure</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="pressure"
                                            type="text" placeholder="Bar" oninput="validateInput(event)"
                                            value="<?php echo e((int) str_replace('.', '', subject: strtok($comp['pressure'], ' '))); ?>">
                                        <span class="input-group-text">Bar</span>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultInput" class="form-label">Temperature</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="temperature"
                                            type="text" placeholder="°C" oninput="validateInput(event)"
                                            value="<?php echo e((int) str_replace('.', '', subject: strtok($comp['temp'], ' '))); ?>">
                                        <span class="input-group-text">°C</span>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Cek Kebocoran</label>
                                    <select id="offDisable" name="leak" class="form-select offDisable">
                                        <option value="">---------------</option>
                                        <option value="Ada" <?php echo e($comp['leak'] == 'Ada' ? 'selected' : ''); ?>>Ada
                                        </option>
                                        <option value="Tidak Ada" <?php echo e($comp['leak'] == 'Tidak Ada' ? 'selected' : ''); ?>>
                                            Tidak Ada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-floating form-floating-outline mb-3">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="issue"
                                    placeholder="Comments here..."><?php echo e($comp['issue']); ?></textarea>
                                <label for="exampleFormControlTextarea1">Issue</label>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="daily-compressor">
                            <?php if($errors->any()): ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <div class="row mb-3">
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Condition</label>
                                    <select id="conditionSelect" name="condition" class="form-select">
                                        <option value="Running"
                                            <?php echo e($comp['condition'] == 'Running' ? 'selected' : ''); ?>>Running</option>
                                        <option value="Stand By"
                                            <?php echo e($comp['condition'] == 'Stand By' ? 'selected' : ''); ?>>Stand By</option>
                                        <option value="Off" <?php echo e($comp['condition'] == 'Off' ? 'selected' : ''); ?>>Off
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label for="defaultInput" class="form-label">Dew Point</label>
                                    <div class="input-group input-group-merge">
                                        <input id="offDisable" class="form-control offDisable" name="dew"
                                            type="text" placeholder="Dew Point"
                                            value="<?php echo e((int) str_replace('.', '', strtok($comp['dew'], ' '))); ?>">
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Auto Drain</label>
                                    <select id="offDisable" name="drain" class="form-select offDisable">
                                        <option value="Ok" <?php echo e($comp['drain'] == 'Ok' ? 'selected' : ''); ?>>Ok
                                        </option>
                                        <option value="Not Ok" <?php echo e($comp['drain'] == 'Not Ok' ? 'selected' : ''); ?>>Not
                                            Ok</option>
                                    </select>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Cek Kebocoran</label>
                                    <select id="offDisable" name="leak" class="form-select offDisable">
                                        <option value="">---------------</option>
                                        <option value="Ada" <?php echo e($comp['leak'] == 'Ada' ? 'selected' : ''); ?>>Ada
                                        </option>
                                        <option value="Tidak Ada"
                                            <?php echo e($comp['leak'] == 'Tidak Ada' ? 'selected' : ''); ?>>Tidak Ada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="defaultInput" class="form-label">Temperature In</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="temperature_in"
                                            type="text" placeholder="°C" oninput="validateInput(event)"
                                            value="<?php echo e((int) str_replace('.', '', strtok($comp['temp'], ' '))); ?>">
                                        <span class="input-group-text">°C</span>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label for="defaultInput" class="form-label">Temperature Out</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable"
                                            name="temperature_out" type="text" placeholder="°C"
                                            oninput="validateInput(event)"
                                            value="<?php echo e((int) str_replace('.', '', strtok($comp['temp_out'], ' '))); ?>">
                                        <span class="input-group-text">°C</span>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="defaultSelect" class="form-label">Cek Fan Kondensor</label>
                                    <select id="offDisable" name="fan" class="form-select offDisable">
                                        <option value="Ok" <?php echo e($comp['fan'] == 'Ok' ? 'selected' : ''); ?>>Ok
                                        </option>
                                        <option value="Not Ok" <?php echo e($comp['fan'] == 'Not Ok' ? 'selected' : ''); ?>>Not Ok
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-floating form-floating-outline mb-3">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="issue"
                                    placeholder="Comments here..."><?php echo e($comp['issue']); ?></textarea>
                                <label for="exampleFormControlTextarea1">Issue</label>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="float-end">
                        <button :disabled="focused" type="submit" class="btn btn-lg btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/monitoring/issue.blade.php ENDPATH**/ ?>