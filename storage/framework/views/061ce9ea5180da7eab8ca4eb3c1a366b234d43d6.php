
<?php $__env->startSection('title', 'Create Service Reports With Request'); ?>
<?php $__env->startSection('content'); ?>
    <form action="<?php echo e(route('req-visit.reports', $visit->id)); ?>"
        method="post" enctype="multipart/form-data" id="serviceReports" name="service-reports">
        <?php echo csrf_field(); ?>
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Form Service Reports</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control fw-bold fs-3" id="floatingInputFilled"
                                aria-describedby="floatingInputFilledHelp" name="no_service" placeholder="No Service"
                                value="<?php echo e($formattedNumberS . '-S/RJO-' . Auth::user()->code . '/' . $formattedMonthNow . '/' . \Carbon\Carbon::now()->year); ?>">
                            <label for="floatingInputFilled">Number Service</label>
                            <span class="form-floating-focused"></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <div class="form-floating form-floating-outline">
                            <select class="select2 form-select form-select-lg invoice-item-pic" data-allow-clear="true"
                                name="id_pic" id="selectPic">
                                <option selected>----- Select Company | Pic || Sales -----</option>
                                <?php $__currentLoopData = $pic; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $charge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option data-id="<?php echo e($charge->client->id); ?>" value="<?php echo e($charge->id); ?>"
                                        <?php echo e(@$report->id_pic == $charge->id ? 'selected' : ''); ?>>
                                        <?php echo e($charge->client->company); ?> | <?php echo e($charge->name_pic); ?> ||
                                        <?php echo e($charge->client->sales->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <label for="select2Basic">PIC</label>
                        </div>
                        <input type="text" name="technician" id="" value="<?php echo e(Auth::user()->id); ?>" hidden>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="exampleFormControlSelect1" aria-label="Default select example"
                                name="type">
                                <option selected="" disabled>---- Choose Service Type ----</option>
                                <option value="Visit" selected>Visit</option>
                                <option value="Service">Service</option>
                                <option value="General">General Check
                                </option>
                            </select>
                            <label for="exampleFormControlSelect1">Service Type</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="date" name="date" id="date"
                                value="<?php echo e($visit->date); ?>">
                            
                            <label for="date">Date</label>
                        </div>
                    </div>
                    <div class="col-8 col-md-6 mb-3">
                        <div class="form-floating form-floating-outline mb-2">
                            <input type="text" name="" id="machine-dropdown" class="form-control"
                                value="<?php echo e($visit->machine->brand); ?> <?php echo e($visit->machine->type); ?>" disabled>
                            <label for="machine-dropdown">Machine</label>
                        </div>
                    </div>
                    <div class="col-4 mb-3">
                        <a type="button" data-bs-toggle="modal" data-bs-target="#createMachine">
                            <button type="button" class="btn btn-primary btn-lg">
                                + Machine
                            </button>
                        </a>
                    </div>
                    <div class="col-12 col-md-2">

                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control input-numeric" id="running" name="running"
                                placeholder="Type Running Here..." value="<?php echo e(old('running', @$report->running ?? '')); ?>">
                            <label for="basic-default-fullname">Running</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control input-numeric" id="load" name="load"
                                placeholder="Type Load Here..." value="<?php echo e(old('load', @$report->load ?? '')); ?>">
                            <label for="basic-default-fullname">Load</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="jobdesc" name="jobdesc"
                                placeholder="Type Job Description Type Here ...."
                                value="<?php echo e(old('jobdesc', @$report->jobdesc ?? '')); ?>">
                            <label for="basic-default-fullname">Job Description</label>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="description" name="desc" placeholder="Description here..."
                                style="min-height: 100px;" value="<?php echo e(old('desc')); ?>"><?php echo e(@$report->desc ?? ''); ?></textarea>
                            <label for="description">Description</label>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="recomendation" name="recomendation" placeholder="Recomendation here..."
                                style="min-height: 100px;" value="<?php echo e(old('recomendation')); ?>"><?php echo e(@$report->recomendation ?? ''); ?></textarea>
                            <label for="recomendation">Recomendation</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="formFileMultiple" class="form-label">Picture</label>
                            <input class="form-control" type="file" id="formFileMultiple" name="image[]"
                                multiple="" accept="image/*">
                            <div class="d-flex justify-content-between" id="image-preview">
                                <?php
                                    $i = 1;
                                ?>
                                <?php if(@$image): ?>
                                    <?php $__currentLoopData = $image; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="image-container">
                                            <img src="<?php echo e(url('') . '/' . $item->picture); ?>" alt=""
                                                srcset="">
                                            <p>Photo <?php echo e($i); ?> - <?php echo e($item->keterangan); ?></p>
                                        </div>
                                        <?php
                                            $i++;
                                        ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div id="dynamicInputsContainer">
                            <?php
                                $i = 1;
                            ?>
                            <?php if(@$image): ?>
                                <?php $__currentLoopData = $image; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <input class="form-control mb-2" type="text" name="description[]"
                                        placeholder="Deskripsi untuk File <?php echo e($i); ?>"
                                        value="<?php echo e(@$item->keterangan); ?>">
                                    <?php
                                        $i++;
                                    ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <!-- Elemen input dinamis akan ditambahkan di sini -->
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">Save changes</button>
                        <a href="<?php echo e(route('service-reports.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php echo $__env->make('components.modal.machine.form-technician', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
    <style>
        #image-preview img {
            max-width: 150px;
            margin-left: 16px;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/forms-selects.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script'); ?>
    <script>
        function initNumericInput() {
            var input = $('.input-numeric')
            for (var i = 0; i < input.length; i++) {
                input[i].addEventListener('input', function() {
                    // Hapus karakter selain angka
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }
        }
        $(document).ready(function() {
            var selectedMachineId = '<?php echo e($report->id_machine ?? ''); ?>';
            initNumericInput();
            $('#formFileMultiple').on('change', function() {
                var files = this.files;
                var dynamicInputsContainer = $('#dynamicInputsContainer');
                console.log(dynamicInputsContainer);

                dynamicInputsContainer.empty();

                for (var i = 0; i < files.length; i++) {
                    var dynamicInput =
                        '<input class="form-control mb-2" type="text" name="description[]" placeholder="Deskripsi untuk File ' +
                        (i +
                            1) + '">';
                    dynamicInputsContainer.append(dynamicInput);
                }

                if (files.length !== 3 && files.length !== 6 && files.length !== 9) {
                    alert('Gambar Wajib Kelipatan 3! 3/6/9 Maksimal 9');
                    this.value = ''; // Menghapus file yang tidak memenuhi syarat
                    dynamicInputsContainer.empty();
                }

                console.log(files);
                const previewContainer = document.getElementById('image-preview');
                previewContainer.innerHTML = '';

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const imageContainer = document.createElement('div');
                        const imageElement = document.createElement('img');
                        const description = document.createElement('p');

                        imageContainer.className =
                            'image-container'; // Tambahkan kelas sesuai kebutuhan
                        imageElement.src = e.target.result;
                        description.textContent = 'Photo ' + (i + 1);

                        imageContainer.appendChild(imageElement);
                        imageContainer.appendChild(description);
                        previewContainer.appendChild(imageContainer);

                    };

                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/coordinator/visit/form.blade.php ENDPATH**/ ?>