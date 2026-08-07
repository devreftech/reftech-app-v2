
<?php $__env->startSection('title', 'Create Service Reports'); ?>
<?php $__env->startSection('content'); ?>
    <form action="<?php echo e(route('store.daily-monitoring-reports', [$monitoring->id, $machine->id])); ?>" method="post" enctype="multipart/form-data" id="serviceReports"
        name="service-reports">
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
                <h5 class="mb-0">Form Service Report</h5>
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
                                <option selected>----- Select Fajar Paper | Pic || Sales -----</option>
                                <?php $__currentLoopData = $pic; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $charge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option data-id="<?php echo e($charge->client->id); ?>" value="<?php echo e($charge->id); ?>">
                                        <?php echo e($charge->client->company); ?> | <?php echo e($charge->name_pic); ?> ||
                                        <?php echo e($charge->client->sales->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <label for="select2Basic">Client</label>
                        </div>
                        <input type="text" name="technician" id="" value="<?php echo e(Auth::user()->id); ?>" hidden>
                        <input type="number" name="monitoring" id="" value="<?php echo e($monitoring->id); ?>" hidden>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="exampleFormControlSelect1" aria-label="Default select example"
                                name="type">
                                <option selected="" disabled>---- Choose Service Type ----</option>
                                <option value="Visit" <?php echo e(@$report->type == 'Visit' ? 'Selected' : ''); ?>>Visit</option>
                                <option value="Service" <?php echo e(@$report->type == 'Service' ? 'Selected' : ''); ?>>Service
                                </option>
                                <option value="General" <?php echo e(@$report->type == 'General' ? 'Selected' : ''); ?>>General Check
                                </option>
                                <option value="Cleaning" <?php echo e(@$report->type == 'Cleaning' ? 'Selected' : ''); ?>>Cleaning
                                </option>
                            </select>
                            <label for="exampleFormControlSelect1">Service Type</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="date" name="date" id="date"
                                value="<?php echo e(now()->format('Y-m-d')); ?>">
                            
                            <label for="date">Date</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control input-numeric" id="machine" name="machine"
                                placeholder="Type machine Here..." value="<?php echo e($machine->unit->brand); ?> <?php echo e($machine->unit->unit->sku); ?> || <?php echo e($machine->location); ?> - <?php echo e($machine->tag); ?> - <?php echo e($machine->unit->serial); ?>" disabled>
                            <label for="basic-default-fullname">Machine</label>
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-6">

                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control input-numeric" id="running" name="running"
                                placeholder="Type Running Here..." value="<?php echo e($runningNumericValue); ?>">
                            <label for="basic-default-fullname">Running</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control input-numeric" id="load" name="load"
                                placeholder="Type Load Here..." value="<?php echo e($loadingNumericValue); ?>">
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
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">Save changes</button>
                        <a href="<?php echo e(route('service-reports.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
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
            $('#selectPic').on('change', function() {
                var clientId = $(this).find(':selected').data('id');
                var Url = '/machine/dropdown/' + clientId;

                $.ajax({
                    url: Url,
                    type: 'GET',
                    success: function(response) {
                        // Clear and populate the machine dropdown
                        var machineDropdown = $('#machine-dropdown');
                        machineDropdown.empty();
                        machineDropdown.append(
                            '<option selected="" disabled> ---- Choose Machine Here ---- </option>'
                            );

                        $.each(response, function(key, value) {
                            var option = $('<option></option>').attr('value', value.id)
                                .text(value.brand + " " + value.sku + " " + value.sn +
                                    " || " + value.location + " - " + value.tag +
                                    " - " + value.serial);
                            if (value.id == selectedMachineId) {
                                option.attr('selected', 'selected');
                            }
                            machineDropdown.append(option);
                        });

                        // Enable the machine dropdown
                        machineDropdown.prop('disabled', false);
                    }
                });
            });

            // Trigger change event if updating to pre-select the machine
            if (selectedMachineId) {
                $('#selectPic').trigger('change');
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/monitoring/form-service-reports.blade.php ENDPATH**/ ?>