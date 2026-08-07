
<?php $__env->startSection('title', 'Monitoring machine'); ?>
<?php $__env->startSection('content'); ?>
    <div class="card mb-3">
        <?php if($machine->monitoring()->whereDate('created_at', Carbon\Carbon::today())->exists()): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Data Monitoring Telah Ada
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <div class="card-body text-center">
                <p>Daily <?php echo e($machine->unit->brand); ?> <?php echo e($machine->unit->unit->sku); ?> Sudah di input!</p>
                <div class="tombol d-flex gap-2 justify-content-center">
                    <a class="btn btn-primary waves-effect" href="<?php echo e(route('create.weekly-monitoring', $machine->id)); ?>">
                        Input Weekly
                    </a>
                    <a href="<?php echo e(route('create.monthly-monitoring', $machine->id)); ?>"
                        class="btn btn-warning waves-effect">Monthly Monitoring</a>
                    <?php if($monitoring->main_desc == null): ?>
                        <button type="button" class="btn btn-secondary d-grid waves-effect" data-bs-toggle="modal"
                            data-bs-target="#addMainLog">Input Maintenance Log</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="card-body">
                <form action="<?php echo e(route('store.daily-monitoring', $machine->id)); ?>" method="post"
                    enctype="multipart/form-data" id="myForm">
                    <?php echo csrf_field(); ?>
                    <?php if($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER'): ?>
                        <h5 class="text-center">DAILY CHECK AIR COMPRESSOR <?php echo e($machine->unit->brand); ?>

                            <?php echo e($machine->unit->unit->sku); ?></h5>
                        <div class="daily-compressor">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="row mb-3">
                                        <div class="col-4 col-lg-2">
                                            Location
                                        </div>
                                        <div class="col-8 col-lg-10">
                                            : <?php echo e($machine->location); ?>

                                        </div>
                                        <div class="col-4 col-lg-2">
                                            Tag Number
                                        </div>
                                        <div class="col-8 col-lg-10">
                                            : <?php echo e($machine->tag); ?>

                                        </div>
                                        <div class="col-4 col-lg-2">
                                            Unit
                                        </div>
                                        <div class="col-8 col-lg-10">
                                            : <?php echo e($machine->unit->brand); ?> <?php echo e($machine->unit->unit->sku); ?>

                                        </div>
                                        <?php if(Auth::user()->code != 'RMD'): ?>
                                            <div class="col-4 col-lg-2">
                                                Date
                                            </div>
                                            <div class="col-8 col-lg-10">
                                                : <?php echo e(\Carbon\Carbon::now()->format('d-m-Y')); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php if(Auth::user()->code == 'RMD'): ?>
                                <div class="row my-3">
                                    <div class="col-4">
                                        <label for="Date">Date</label>
                                        <input class="form-control" type="date" id="Date" name="date"
                                            value="<?php echo e(now()->format('Y-m-d')); ?>">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="row mb-3">
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Condition</label>
                                    <select id="conditionSelect" name="condition" class="form-select">
                                        <option value="Running">Running</option>
                                        <option value="Stand By">Stand By</option>
                                        <option value="Off">Off</option>
                                    </select>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Oil Level</label>
                                    <select id="offDisable" name="oil" class="form-select offDisable">
                                        <option value="OK">OK</option>
                                        <option value="Kurang">Kurang</option>
                                    </select>
                                </div>
                                <div class="col col-lg-3">
                                    <label for="defaultInput" class="form-label">Running</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="running"
                                            type="text" placeholder="Hr" min="1" oninput="validateInput(event)">
                                        <span class="input-group-text">Hours</span>
                                    </div>
                                </div>
                                <div class="col col-lg-3">
                                    <label for="defaultInput" class="form-label">Loading Hr</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="loading"
                                            type="text" placeholder="Hr" min="1" oninput="validateInput(event)">
                                        <span class="input-group-text">Hours</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-lg-6">
                                    <label for="defaultInput" class="form-label">Pressure</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="pressure"
                                            type="text" placeholder="Bar" oninput="validateInput(event)">
                                        <span class="input-group-text">Bar</span>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultInput" class="form-label">Temperature</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="temperature"
                                            type="text" placeholder="°C" oninput="validateInput(event)">
                                        <span class="input-group-text">°C</span>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Cek Kebocoran</label>
                                    <select id="offDisable" name="leak" class="form-select offDisable">
                                        <option value="Ada">Ada</option>
                                        <option value="Tidak Ada">Tidak Ada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-floating form-floating-outline mb-3">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="issue"
                                    placeholder="Comments here..."></textarea>
                                <label for="exampleFormControlTextarea1">Issue</label>
                            </div>
                            <div class="mb-4">
                                <label for="formFile" class="form-label">Input Bukti Photo</label>
                                <input class="form-control" type="file" name="picture" id="formFile"
                                    accept="image/*">
                                <p class="text-muted">Note :Format photo harus ada bukti tanggal pada Photonya</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <h5 class="text-center">DAILY CHECK AIR DRYER <?php echo e($machine->unit->brand); ?>

                            <?php echo e($machine->unit->unit->sku); ?></h5>
                        <div class="daily-compressor">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="row mb-3">
                                        <div class="col-4 col-lg-2">
                                            Location
                                        </div>
                                        <div class="col-8 col-lg-10">
                                            : <?php echo e($machine->location); ?>

                                        </div>
                                        <div class="col-4 col-lg-2">
                                            Tag Number
                                        </div>
                                        <div class="col-8 col-lg-10">
                                            : <?php echo e($machine->tag); ?>

                                        </div>
                                        <div class="col-4 col-lg-2">
                                            Unit
                                        </div>
                                        <div class="col-8 col-lg-10">
                                            : <?php echo e($machine->unit->brand); ?> <?php echo e($machine->unit->unit->sku); ?>

                                        </div>
                                        <?php if(Auth::user()->code != 'RMD'): ?>
                                            <div class="col-4 col-lg-2">
                                                Date
                                            </div>
                                            <div class="col-8 col-lg-10">
                                                : <?php echo e(\Carbon\Carbon::now()->format('d-m-Y')); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php if(Auth::user()->code == 'RMD'): ?>
                                <div class="row my-3">
                                    <div class="col-4">
                                        <label for="Date">Date</label>
                                        <input class="form-control" type="date" id="Date" name="date"
                                            value="<?php echo e(now()->format('Y-m-d')); ?>">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="row mb-3">
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Condition</label>
                                    <select id="conditionSelect" name="condition" class="form-select">
                                        <option value="Running">Running</option>
                                        <option value="Stand By">Stand By</option>
                                        <option value="Off">Off</option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label for="defaultInput" class="form-label">Dew Point</label>
                                    <div class="input-group input-group-merge">
                                        <input id="offDisable" class="form-control offDisable" name="dew"
                                            type="text" placeholder="Dew Point">
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Auto Drain</label>
                                    <select id="offDisable" name="drain" class="form-select offDisable">
                                        <option value="Ok">Ok</option>
                                        <option value="Not Ok">Not Ok</option>
                                    </select>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Cek Kebocoran</label>
                                    <select id="offDisable" name="leak" class="form-select offDisable">
                                        <option value="Ada">Ada</option>
                                        <option value="Tidak Ada">Tidak Ada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="defaultInput" class="form-label">Temperature In</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="temperature_in"
                                            type="text" placeholder="°C" oninput="validateInput(event)">
                                        <span class="input-group-text">°C</span>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label for="defaultInput" class="form-label">Temperature Out</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="temperature_out"
                                            type="text" placeholder="°C" oninput="validateInput(event)">
                                        <span class="input-group-text">°C</span>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="defaultSelect" class="form-label">Cek Fan Kondensor</label>
                                    <select id="offDisable" name="fan" class="form-select offDisable">
                                        <option value="Ok">Ok</option>
                                        <option value="Not Ok">Not Ok</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-floating form-floating-outline mb-3">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="issue"
                                    placeholder="Comments here..."></textarea>
                                <label for="exampleFormControlTextarea1">Issue</label>
                            </div>
                            <div class="mb-4">
                                <label for="formFile" class="form-label">Input Bukti Photo</label>
                                <input class="form-control" type="file" name="picture" id="formFile"
                                    accept="image/*">
                                <p class="text-muted">Note :Format photo harus ada bukti tanggal pada Photonya</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="float-end">
                        <a href="<?php echo e(route('index.daily-monitoring', $machine->id)); ?>" type="button"
                            class="btn btn-lg btn-outline-secondary">
                            Back
                        </a>
                        <button :disabled="focused" type="submit" class="btn btn-lg btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <?php echo $__env->make('components.modal.monitoring.mainlog-create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/toastr/toastr.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-monitoring-machine.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        function validateInput(event) {
            const input = event.target;
            // Izinkan hanya angka dan koma
            input.value = input.value.replace(/[^0-9,]/g, '');
        }
        $('#conditionSelect').on('change', function() {
            var condition = $(this).val();
            var disable = $('.offDisable');
            var number = $('#numberInput');

            if (condition == 'Off') {
                disable.prop('disabled', true);
                // number.prop('disabled', true);
            } else {
                // number.prop('disabled', false);
                disable.prop('disabled', false);
            }
            console.log(number);

            console.log(condition);
        });

        // document.getElementById('myForm').addEventListener('submit', function(e) {
        //     e.preventDefault(); // Mencegah pengiriman form default
        //     const input = document.getElementById('numberInput').value;

        //     if (!/^\d+(,\d+)?$/.test(input)) {
        //         alert('Masukkan angka yang valid (hanya angka dan koma)!');
        //         return;
        //     }

        //     const validValue = parseFloat(input.replace(',', '.')); // Ganti koma dengan titik
        //     console.log('Angka valid yang dikirim:', validValue);
        //     alert(`Nilai yang valid: ${validValue}`);
        // });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/monitoring/monitoring/form.blade.php ENDPATH**/ ?>