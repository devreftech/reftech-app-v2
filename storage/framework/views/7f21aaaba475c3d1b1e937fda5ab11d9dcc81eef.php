
<?php $__env->startSection('title', 'Create Service Reports'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid flex-grow-1 container-p-y">
    <style>
        .form-section-card {
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            box-shadow: 0 4px 16px rgba(0,0,0,0.02);
            transition: all 0.2s ease-in-out;
        }
        .form-section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-section-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        .bg-icon-primary { background: rgba(99, 102, 241, 0.12); color: #6366f1; }
        .bg-icon-info { background: rgba(14, 165, 233, 0.12); color: #0ea5e9; }
        .bg-icon-success { background: rgba(16, 185, 129, 0.12); color: #10b981; }

        .service-no-badge {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            border-radius: 12px;
            padding: 0.6rem 1.2rem;
            font-family: monospace;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.05em;
        }
    </style>

    <!-- Modern Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="mdi mdi-file-document-edit-outline text-primary me-2"></i><?php echo e(@$report ? 'Edit Service Report' : 'Create Service Report'); ?>

            </h2>
            <p class="text-muted mb-0 small">Lengkapi formulir laporan servis teknisi di bawah ini dengan lengkap & akurat.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
            <span class="text-muted small fw-semibold">No. Service:</span>
            <div class="service-no-badge shadow-sm">
                <?php echo e(@$report ? $report->no_service : ($formattedNumberS . '-S/RJO-' . Auth::user()->code . '/' . $formattedMonthNow . '/' . \Carbon\Carbon::now()->year)); ?>

            </div>
        </div>
    </div>

    <form action="<?php echo e(@$report ? route('service-reports.update', @$report->id) : route('service-reports.store')); ?>"
        method="post" id="serviceReports" name="service-reports">
        <?php echo csrf_field(); ?>
        <?php if(@$report): ?>
            <?php echo method_field('PATCH'); ?>
        <?php endif; ?>
        <input type="hidden" name="no_service" value="<?php echo e(@$report ? $report->no_service : ($formattedNumberS . '-S/RJO-' . Auth::user()->code . '/' . $formattedMonthNow . '/' . \Carbon\Carbon::now()->year)); ?>">

        <?php if($errors->any()): ?>
            <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="d-flex align-items-center mb-2 fw-bold">
                    <i class="mdi mdi-alert-circle-outline me-2 mdi-20px"></i> Mohon koreksi inputan berikut:
                </div>
                <ul class="mb-0 ps-3">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        
        <div class="card form-section-card mb-4">
            <div class="card-header bg-transparent border-bottom p-4">
                <div class="form-section-title">
                    <div class="form-section-icon bg-icon-primary">
                        <i class="mdi mdi-domain"></i>
                    </div>
                    <div>
                        <span>Pelanggan & Unit Mesin</span>
                        <small class="text-muted d-block fw-normal" style="font-size: 0.8rem;">Pilih sales, klien, PIC penanggung jawab, dan unit mesin terkait.</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if(isset($isInternalStock) && $isInternalStock): ?>
                    <div class="alert alert-info border-0 mb-4 py-2" style="border-radius: 10px;">
                        <i class="mdi mdi-information-outline me-1"></i> Unit internal Reftech — Sales/Client/PIC dilewati, langsung menggunakan Machine yang sudah dipilih.
                    </div>
                    <input type="hidden" name="id_sales" value="">
                    <input type="hidden" name="client" value="">
                    <input type="hidden" name="id_pic" value="<?php echo e($selectedPICId); ?>">
                    <input type="text" name="technician" value="<?php echo e(Auth::user()->id); ?>" hidden>
                <?php endif; ?>

                <div class="row g-3">
                    <?php if(!isset($isInternalStock) || !$isInternalStock): ?>
                        <div class="col-12 col-md-3">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select invoice-item-sales" data-allow-clear="true"
                                    name="id_sales" id="selectSales" <?php echo e(@$reports ? 'disabled' : ''); ?>>
                                    <option selected disabled>----- Select Sales -----</option>
                                    <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option data-id="<?php echo e($sale->id); ?>" value="<?php echo e($sale->id); ?>"
                                            <?php echo e((isset($selectedSalesId) && $selectedSalesId == $sale->id) || (@$report && isset($report->pic->client->sales) && $report->pic->client->sales->id == $sale->id) ? 'selected' : ''); ?>>
                                            <?php echo e($sale->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <label for="selectSales">Sales Representative</label>
                            </div>
                            <input type="text" name="technician" value="<?php echo e(Auth::user()->id); ?>" hidden>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-floating form-floating-outline">
                                <select id="client-dropdown" class="select2 form-select invoice-item-client" data-id="1"
                                    data-allow-clear="true" name="client" disabled>
                                    <option selected disabled> ---- Choose Client ---- </option>
                                    <?php if(@$report && isset($report->pic->client)): ?>
                                        <option data-id="<?php echo e($report->pic->client->id); ?>" value="<?php echo e($report->pic->client->id); ?>"
                                            selected>
                                            <?php echo e($report->pic->client->company); ?></option>
                                    <?php endif; ?>
                                </select>
                                <label for="client-dropdown">Client / Company</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-floating form-floating-outline">
                                <select id="pic-dropdown" class="select2 form-select invoice-item-pic" data-id="1"
                                    data-allow-clear="true" name="id_pic" disabled>
                                    <option selected disabled> ---- Choose PIC ---- </option>
                                    <?php if(@$report && isset($report->pic)): ?>
                                        <option data-id="<?php echo e($report->pic->id); ?>" value="<?php echo e($report->pic->id); ?>" selected>
                                            <?php echo e($report->pic->name_pic); ?></option>
                                    <?php endif; ?>
                                </select>
                                <label for="pic-dropdown">PIC Klien</label>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-12 col-md-3">
                        <div class="form-floating form-floating-outline">
                            <select id="machine-dropdown" class="select2 form-select invoice-item-machine" data-id="1"
                                data-allow-clear="true" name="machine"
                                <?php echo e(isset($isInternalStock) && $isInternalStock ? '' : 'disabled'); ?>>
                                <option selected disabled> ---- Choose Machine ---- </option>
                                <?php if(isset($isInternalStock) && $isInternalStock && isset($machine)): ?>
                                    <option value="<?php echo e($machine->id); ?>" data-unit-category="<?php echo e(optional(optional($machine->unit)->unit)->unit ?? ''); ?>" selected>
                                        <?php echo e(optional($machine->unit)->brand ?? '-'); ?> <?php echo e(optional($machine->unit)->pn ?? ''); ?> ||
                                        <?php echo e($machine->location); ?> - <?php echo e($machine->tag); ?> - <?php echo e($machine->serial); ?>

                                    </option>
                                <?php elseif(@$report && isset($report->machine)): ?>
                                    <option data-id="<?php echo e($report->machine->id); ?>" value="<?php echo e($report->machine->id); ?>" data-unit-category="<?php echo e(optional(optional(optional($report->machine)->unit)->unit)->unit ?? ''); ?>"
                                        selected>
                                        <?php echo e(optional($report->machine->unit)->brand ?? '-'); ?> <?php echo e(optional($report->machine->unit)->pn ?? ''); ?> ||
                                        <?php echo e($report->machine->location); ?> - <?php echo e($report->machine->tag); ?> -
                                        <?php echo e($report->machine->serial); ?>

                                    </option>
                                <?php endif; ?>
                            </select>
                            <label for="machine-dropdown">Unit Mesin</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card form-section-card mb-4">
            <div class="card-header bg-transparent border-bottom p-4">
                <div class="form-section-title">
                    <div class="form-section-icon bg-icon-info">
                        <i class="mdi mdi-tune-vertical-variant"></i>
                    </div>
                    <div>
                        <span>Parameter & Jenis Pekerjaan</span>
                        <small class="text-muted d-block fw-normal" style="font-size: 0.8rem;">Tentukan tanggal servis, jenis laporan, jam kerja mesin (running/load), dan job description.</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="service-type-select" aria-label="Service Type" name="type">
                                <option selected="" disabled>---- Choose Service Type ----</option>
                                <option value="Visit" <?php echo e(@$report->type == 'Visit' ? 'Selected' : ''); ?>>Visit</option>
                                <option value="Service" <?php echo e(@$report->type == 'Service' ? 'Selected' : ''); ?>>Service</option>
                                <option value="General" <?php echo e(@$report->type == 'General' ? 'Selected' : ''); ?>>General Check</option>
                                <option value="Rental" <?php echo e(@$report->type == 'Rental' ? 'Selected' : ''); ?>>Rental</option>
                                <option value="Cleaning" <?php echo e(@$report->type == 'Cleaning' ? 'Selected' : ''); ?>>Cleaning</option>
                                <option value="Commissioning" <?php echo e(@$report->type == 'Commissioning' ? 'Selected' : ''); ?>>Commissioning</option>
                            </select>
                            <label for="service-type-select">Service Type</label>
                        </div>
                    </div>

                    <div class="col-12 col-md-4" id="pm-level-container" style="display: none;">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="pm-level-select" name="pm_level">
                                <option value="" selected>---- Select PM Level ----</option>
                                <option value="PM1" <?php echo e(@$report->pm_level == 'PM1' ? 'Selected' : ''); ?>>PM1 (Minor Service)</option>
                                <option value="PM2" <?php echo e(@$report->pm_level == 'PM2' ? 'Selected' : ''); ?>>PM2 (Major Service)</option>
                                <option value="PM3" <?php echo e(@$report->pm_level == 'PM3' ? 'Selected' : ''); ?>>PM3</option>
                                <option value="PM4" <?php echo e(@$report->pm_level == 'PM4' ? 'Selected' : ''); ?>>PM4</option>
                                <option value="Troubleshooting" <?php echo e(@$report->pm_level == 'Troubleshooting' ? 'Selected' : ''); ?>>Troubleshooting / Repair</option>
                            </select>
                            <label for="pm-level-select">PM Level (Air Compressor Screw)</label>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="date" name="date" id="date"
                                value="<?php echo e($report->date ?? now()->format('Y-m-d')); ?>">
                            <label for="date">Tanggal Pengerjaan</label>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control input-numeric" id="running" name="running"
                                placeholder="Running Hours..." value="<?php echo e(old('running', @$report->running ?? '')); ?>">
                            <label for="running">Running Hours (Jam)</label>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control input-numeric" id="load" name="load"
                                placeholder="Load Hours..." value="<?php echo e(old('load', @$report->load ?? '')); ?>">
                            <label for="load">Load Hours (Jam)</label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="jobdesc" name="jobdesc"
                                placeholder="Scope of Work..."
                                value="<?php echo e(old('jobdesc', @$report->jobdesc ?? '')); ?>">
                            <label for="jobdesc">Job Description (Ringkasan Tugas)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card form-section-card mb-4">
            <div class="card-header bg-transparent border-bottom p-4">
                <div class="form-section-title">
                    <div class="form-section-icon bg-icon-success">
                        <i class="mdi mdi-notebook-edit-outline"></i>
                    </div>
                    <div>
                        <span>Temuan, Catatan & Rekomendasi Teknisi</span>
                        <small class="text-muted d-block fw-normal" style="font-size: 0.8rem;">Tuliskan hasil temuan kondisi lapangan dan saran perbaikan untuk pelanggan.</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="description" name="desc" placeholder="Detail temuan..."
                                style="min-height: 110px;"><?php echo e(old('desc', @$report->desc ?? '')); ?></textarea>
                            <label for="description">Detail Temuan & Keterangan Servis (Description)</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="recomendation" name="recomendation" placeholder="Rekomendasi teknisi..."
                                style="min-height: 110px;"><?php echo e(old('recomendation', @$report->recomendation ?? '')); ?></textarea>
                            <label for="recomendation">Rekomendasi Perbaikan / Part Replacement</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Submit Actions Bar -->
        <div class="d-flex justify-content-end align-items-center gap-2 mb-5 p-3 bg-white border rounded-3 shadow-sm">
            <a href="<?php echo e(route('service-reports.index')); ?>" class="btn btn-outline-secondary px-4 wave-effect" style="border-radius: 10px;">
                <i class="mdi mdi-arrow-left me-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary px-4 shadow-sm wave-effect" style="border-radius: 10px; font-weight: 600;">
                <i class="mdi mdi-content-save-outline me-1"></i> Save
            </button>
        </div>
    </form>
    <?php echo $__env->make('components.modal.machine.form-technician', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        #image-preview img {
            max-width: 150px;
            margin-left: 16px;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
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
            var selectedMachineId = '<?php echo e($selectedMachineId ?? $report->id_machine ?? ''); ?>';
            var selectedSalesId = '<?php echo e($selectedSalesId ?? (isset($report->pic->client) ? $report->pic->client->id_sales : '')); ?>';
            var selectedClientId = '<?php echo e($selectedClientId ?? (isset($report->pic) ? $report->pic->id_client : '')); ?>';
            var selectedPICId = '<?php echo e($selectedPICId ?? $report->id_pic ?? ''); ?>';
            var isInternalStock = <?php echo e(isset($isInternalStock) && $isInternalStock ? 'true' : 'false'); ?>;
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
            $('#selectSales').on('change', function() {
                var salesId = $(this).find(':selected').data('id');
                var Url = '/client/dropdown/' + salesId;

                $.ajax({
                    url: Url,
                    type: 'GET',
                    success: function(response) {
                        var clientDropdown = $('#client-dropdown');
                        clientDropdown.empty();
                        clientDropdown.append(
                            '<option selected="" disabled> ---- Choose Client Here ---- </option>'
                        );

                        $.each(response, function(key, value) {
                            var option = $('<option></option>').attr('value', value.id)
                                .text(value.company);
                            clientDropdown.append(option);
                        });

                        clientDropdown.prop('disabled', false);

                        if (selectedClientId) {
                            clientDropdown.val(selectedClientId).trigger('change');
                        }
                    }
                });
            });

            $('#client-dropdown').on('change', function() {
                var clientId = $(this).find(':selected').val();
                var Url = '/pic/dropdown/' + clientId;

                $.ajax({
                    url: Url,
                    type: 'GET',
                    success: function(response) {
                        var picDropdown = $('#pic-dropdown');
                        picDropdown.empty();
                        picDropdown.append(
                            '<option selected="" disabled> ---- Choose PIC Here ---- </option>'
                        );

                        $.each(response, function(key, value) {
                            var option = $('<option></option>').attr('value', value.id)
                                .text(value.name_pic);
                            picDropdown.append(option);
                        });

                        picDropdown.prop('disabled', false);

                        if (selectedPICId) {
                            picDropdown.val(selectedPICId).trigger('change');
                        }
                    }
                });
            });

            $('#pic-dropdown').on('change', function() {
                var clientId = $(this).find(':selected').val();
                var Url = '/machine/dropdown/' + clientId;

                $.ajax({
                    url: Url,
                    type: 'GET',
                    success: function(response) {
                        var machineDropdown = $('#machine-dropdown');
                        machineDropdown.empty();
                        machineDropdown.append(
                            '<option selected="" disabled> ---- Choose Machine Here ---- </option>'
                        );

                        $.each(response, function(key, value) {
                            var option = $('<option></option>').attr('value', value.id)
                                .attr('data-unit-category', value.unit_category || '')
                                .text(value.brand + " " + value.model +
                                    " || " + value.location + " - " + value.tag +
                                    " - " + value.serial);
                            machineDropdown.append(option);
                        });

                        machineDropdown.prop('disabled', false);

                        if (selectedMachineId) {
                            machineDropdown.val(selectedMachineId).trigger('change');
                        }
                    }
                });
            });

            function checkPmLevelVisibility() {
                var serviceType = $('#service-type-select').val();
                var selectedOption = $('#machine-dropdown').find(':selected');
                var unitCategory = selectedOption.data('unit-category') || '';

                // Handle pre-filled machine option or dynamically selected option
                if (serviceType === 'Service' && unitCategory.toUpperCase().includes('AIR COMPRESSOR SCREW')) {
                    $('#pm-level-container').slideDown(200);
                } else {
                    $('#pm-level-container').slideUp(200);
                }
            }

            $('#service-type-select, #machine-dropdown').on('change', function() {
                checkPmLevelVisibility();
            });

            // Initial check on load (edit mode / pre-selected)
            setTimeout(function() {
                checkPmLevelVisibility();
            }, 500);

            // Trigger change event to pre-select dependent dropdowns in order
            // (dilewati untuk unit internal Reftech — Machine sudah langsung dipilih dari server)
            if (selectedSalesId && !isInternalStock) {
                $('#selectSales').trigger('change');
            }

            $('#serviceReports').on('submit', function(e) {
                e.preventDefault();
                var form = this;

                Swal.fire({
                    title: 'Apakah kamu sudah benar dalam pembuatan service report ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect',
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/technician/service-reports/form.blade.php ENDPATH**/ ?>