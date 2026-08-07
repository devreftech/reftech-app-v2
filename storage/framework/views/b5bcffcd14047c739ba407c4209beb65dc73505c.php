
<?php $__env->startSection('title', 'Service Reports'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row invoice-preview">
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-row flex-column">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img class="text-md"
                                            src="<?php echo e(url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png')); ?>"
                                            alt="" srcset="" width="60%">
                                    </span>
                                </span>
                            </div>
                            <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                            <div style="font-size: 10px">
                                <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                <p class="mb-1">
                                    <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                                    54417653<?php echo e('  |  '); ?><i
                                        class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                                </p>
                            </div>
                        </div>
                        <div>
                            <h3 class="fw-bold">DAILY MONITORING</h3>
                            <div>
                                <span class="fw-bolder"><?php echo e($machine->unit->unit->unit); ?></span>
                            </div>
                            <div class="mt-1">
                                <span class="text-muted"><?php echo e($machine->unit->brand); ?> <?php echo e($machine->unit->unit->sku); ?></span>
                            </div>
                            <div class="mt-1">
                                <span class="text-muted"><?php echo e($machine->tag); ?> - <?php echo e($machine->location); ?></span>
                            </div>
                        </div>
                    </div>
                    <hr class="my-2">
                    

                    <?php if(Auth::user()->role == 'Client'): ?>
                        <h5>Daily Check</h5>
                        <div class="table-responsive text-nowrap mb-4">
                            <?php if($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER'): ?>
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <th>Date</th>
                                        <th>Condition</th>
                                        <th>R Hr.</th>
                                        <th>L Hr.</th>
                                        <th>Press.</th>
                                        <th>Temp. (85°C - 94°C)</th>
                                        <th>Oil Lvl</th>
                                        <th>Kebocoran</th>
                                        <th>PIC</th>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $compressor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <?php if(Auth::user()->role == 'Client'): ?>
                                                    <td>
                                                        <?php echo e($item['date']); ?>

                                                    </td>
                                                <?php else: ?>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button"
                                                                class="btn btn-label-secondary dropdown-toggle waves-effect"
                                                                data-bs-toggle="dropdown"
                                                                aria-expanded="false"><?php echo e($item['date']); ?></button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class=" dropdown-item cursor-pointer"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editIssue-<?php echo e($item['id']); ?>">
                                                                        Update Issue
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class=" dropdown-item cursor-pointer"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editMainlog-<?php echo e($item['id']); ?>">
                                                                        Update Mainlog
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        
                                                    </td>
                                                <?php endif; ?>
                                                <td><?php echo e($item['condition']); ?></td>
                                                <td><?php echo e($item['running']); ?></td>
                                                <td><?php echo e($item['loading']); ?></td>
                                                <td><?php echo e($item['pressure']); ?></td>
                                                <td>
                                                    <?php
                                                        $stringTemp = $item['temp'] ?? ''; // Pastikan nilai tidak null
                                                        $tempNumber = null;

                                                        if (preg_match('/\d+(\.\d+)?/', $stringTemp, $matches)) {
                                                            $tempNumber = (float) $matches[0]; // Gunakan float agar mendukung desimal
                                                        }
                                                    ?>

                                                    <?php if(!is_null($tempNumber) && $tempNumber > 94): ?>
                                                        <p class="mb-0 fw-bold fs-6 text-danger">
                                                            <?php echo e($item['temp']); ?></p>
                                                    <?php else: ?>
                                                        <?php echo e($item['temp']); ?>

                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e($item['oil_level']); ?></td>
                                                <td><?php echo e($item['leak']); ?></td>
                                                <td><?php echo e($item['pic']); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <th>Date</th>
                                        <th>Condition</th>
                                        <th>Temp IN</th>
                                        <th>Temp OUT</th>
                                        <th>Dew P.</th>
                                        <th>Auto Drain</th>
                                        <th>Fan Kondenser</th>
                                        <th>Kebocoran</th>
                                        <th>PIC</th>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $dryer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <?php if(Auth::user()->role == 'Client'): ?>
                                                    <td>
                                                        <?php echo e($item['date']); ?>

                                                    </td>
                                                <?php else: ?>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button"
                                                                class="btn btn-label-secondary dropdown-toggle waves-effect"
                                                                data-bs-toggle="dropdown"
                                                                aria-expanded="false"><?php echo e($item['date']); ?></button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class=" dropdown-item cursor-pointer"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editIssue-<?php echo e($item['id']); ?>">
                                                                        Update Issue
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class=" dropdown-item cursor-pointer"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editMainlog-<?php echo e($item['id']); ?>">
                                                                        Update Mainlog
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        
                                                    </td>
                                                <?php endif; ?>
                                                <td><?php echo e($item['condition']); ?></td>
                                                <td><?php echo e($item['temp']); ?></td>
                                                <td><?php echo e($item['temp_out']); ?></td>
                                                <td>
                                                    <?php if(!is_null($item['dew']) && $item['dew'] > 10): ?>
                                                        <p class="mb-0 fw-bold fs-6 text-danger">
                                                            <?php echo e($item['dew']); ?></p>
                                                    <?php else: ?>
                                                        <?php echo e($item['dew']); ?>

                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e($item['drain']); ?></td>
                                                <td><?php echo e($item['fan']); ?></td>
                                                <td><?php echo e($item['leak']); ?></td>
                                                <td><?php echo e($item['pic']); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php if($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER'): ?>
                            <table class="datatable-compressor table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Condition</th>
                                        <th>R Hr.</th>
                                        <th>L Hr.</th>
                                        <th>Press.</th>
                                        <th>Temp. (85°C - 94°C)</th>
                                        <th>Oil Lvl</th>
                                        <th>Kebocoran</th>
                                        <th>PIC</th>
                                    </tr>
                                </thead>
                            </table>
                        <?php else: ?>
                            <table class="datatable-dryer table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Condition</th>
                                        <th>Temp IN</th>
                                        <th>Temp OUT</th>
                                        <th>Dew P.</th>
                                        <th>Auto Drain</th>
                                        <th>Fan Kondenser</th>
                                        <th>Kebocoran</th>
                                        <th>PIC</th>
                                    </tr>
                                </thead>
                            </table>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if(Auth::user()->level == 1): ?>
                <div class="card invoice-preview-card mb-4">
                    <div class="card-body">
                        <div class="weekly mb-4">
                            <h5>Weekly Check</h5>
                            <div class="table-responsive text-nowrap mb-4">
                                <table class="table table-bordered">
                                    <?php if($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER'): ?>
                                        <thead class="table-light">
                                            <th style="vertical-align: middle;">Week</th>
                                            <th style="vertical-align: middle;">Condition</th>
                                            <th style="vertical-align: middle;">Vibration</th>
                                            <th style="vertical-align: middle;">Voltage</th>
                                            <th style="vertical-align: middle;">Ampere L</th>
                                            <th style="vertical-align: middle;">Cleaning Cooler</th>
                                            <th style="vertical-align: middle;">Cek Coupling / Belt</th>
                                            <th style="vertical-align: middle;">Cleaning Compressor & Area</th>
                                            <th style="vertical-align: middle;">PIC</th>
                                            <th style="vertical-align: middle;">Edit</th>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $noWeek = 1;
                                            ?>
                                            <?php $__empty_1 = true; $__currentLoopData = $weeksoy; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monweek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($noWeek); ?></td>
                                                    <td><?php echo e($monweek['condition']); ?></td>
                                                    <td><?php echo e($monweek['vibration']); ?></td>
                                                    <td><?php echo e($monweek['voltage']); ?></td>
                                                    <td><?php echo e($monweek['ampere']); ?></td>
                                                    <td>
                                                        <?php if($monweek['cooler'] == 1): ?>
                                                            
                                                            cleaning
                                                        <?php else: ?>
                                                            
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($monweek['coupling'] == 1): ?>
                                                            
                                                            Ok
                                                        <?php else: ?>
                                                            
                                                            Not Ok
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($monweek['area'] == 1): ?>
                                                            
                                                            cleaning
                                                        <?php else: ?>
                                                            
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e($monweek['name']); ?></td>
                                                    <td>
                                                        <a href="<?php echo e(route('edit.weekly-monitoring', $monweek['id'])); ?>"
                                                            type="button" class="btn btn-primary">
                                                            Edit
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php
                                                    $noWeek++;
                                                ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="7">Belum Ada Monitoring week</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    <?php else: ?>
                                        <thead class="table-light">
                                            <th>Week</th>
                                            <th>Condition</th>
                                            <th>Voltage</th>
                                            <th>Ampere</th>
                                            <th>Auto Drain</th>
                                            <th>Pre</th>
                                            <th>After</th>
                                            <th>Condensor</th>
                                            <th>PIC</th>
                                            <th>Edit</th>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $noWeek = 1;
                                            ?>
                                            <?php $__currentLoopData = $weeksoy; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monweek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($noWeek); ?></td>
                                                    <td><?php echo e($monweek['condition']); ?></td>
                                                    <td><?php echo e($monweek['voltage']); ?></td>
                                                    <td><?php echo e($monweek['ampere']); ?></td>
                                                    <td><?php echo e($monweek['drain']); ?></td>
                                                    <td><?php echo e($monweek['pre']); ?></td>
                                                    <td><?php echo e($monweek['after']); ?></td>
                                                    <td>
                                                        <?php if($monweek['condensor'] == 1): ?>
                                                            
                                                            cleaning
                                                        <?php else: ?>
                                                            
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e($monweek['name']); ?></td>
                                                    <?php if($monweek['id'] != '-'): ?>
                                                        <td>
                                                            <a href="<?php echo e(route('edit.weekly-monitoring', $monweek['id'])); ?>"
                                                                type="button" class="btn btn-primary">
                                                                Edit
                                                            </a>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                                <?php
                                                    $noWeek++;
                                                ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endif; ?>

            <?php if($machine->unit->unit->unit == 'REFRIGERANT AIR DRYER'): ?>
                <div class="card invoice-preview-card mb-4">
                    <div class="card-body">
                        <div class="monthly mb-4">
                            <h5>Monthly Check</h5>
                            <div class="table-responsive text-nowrap mb-4">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <th>Date</th>
                                        <th>HP (High Pressure)</th>
                                        <th>LP (Low Pressure)</th>
                                        <th>Strainer</th>
                                    </thead>
                                    <tbody>
                                        <?php if($monthly): ?>
                                            <tr>
                                                <td><?php echo e(\Carbon\Carbon::parse($monthly->date)->format('d-m-Y')); ?></td>
                                                <td><?php echo e($monthly->hp); ?></td>
                                                <td><?php echo e($monthly->lp); ?></td>
                                                <td><?php echo e($monthly->strainer); ?></td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endif; ?>

            <div class="card invoice-preview-card mb-4">
                <div class="card-body">
                    <div class="issue mb-4">
                        <table class="datatable-issue table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Issue</th>
                                    <th>Recommendation</th>
                                    <th>Part Number</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>

            <?php if(Auth::user()->level == 1): ?>
                <div class="card invoice-preview-card mb-4">
                    <div class="card-body">
                        <div class="quote mb-4">
                            <h4>Quotattion</h4>
                            <div class="table-responsive text-nowrap mb-4">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>No. Quote</th>
                                            <th>No. PR</th>
                                            <th>Title</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $quotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><?php echo e(\Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y')); ?>

                                                </td>
                                                <td>
                                                    <a href="<?php echo e(route('quotation.show', $quote->id)); ?>"
                                                        class="text-black">
                                                        <?php echo e($quote->no_quote); ?>

                                                    </a>
                                                </td>
                                                <td><?php echo e($quote->no_pr); ?></td>
                                                <td><?php echo e($quote->title); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Belum Ada Quote</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endif; ?>

            <div class="card invoice-preview-card mb-4">
                <div class="card-body">
                    <div class="mainlog mb-4">
                        
                        <div class="d-flex justify-content-between mb-4">
                            <h5>Maintenance Log</h5>
                            <button type="button" class="btn btn-primary d-grid waves-effect" data-bs-toggle="modal"
                                data-bs-target="#addMainLog">+ Mainlog</button>
                        </div>
                        <div class="table-responsive text-nowrap mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Maintenance</th>
                                        <th>PIC</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $mainlog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $main): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td>
                                                <?php echo e(\Carbon\Carbon::parse($main->date)->format('d-m-Y')); ?>

                                            </td>
                                            <td>
                                                <?php echo e($main->desc); ?>

                                            </td>
                                            <td>
                                                <?php echo e($main->teknisi->name); ?>

                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a data-id="<?php echo e($main->id); ?>" data-month="<?php echo e($months); ?>"
                                                        data-machine="<?php echo e($machine->id); ?>"
                                                        class="btn btn-sm btn-label-danger delete-mainlog waves-effect">
                                                        <i
                                                            class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline m-0"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-label-warning waves-effect"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editMainlog-<?php echo e($main->id); ?>">
                                                        <i
                                                            class="menu-icon tf-icons mdi mdi-14px mdi-file-edit-outline m-0"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Belum Ada Mainlog</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <?php if(Auth::user()->level == 1): ?>
                <div class="card invoice-preview-card mb-4">
                    <div class="card-body">
                        <div class="invoice mb-4">
                            <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <hr>
                                <div class="card invoice-preview-card mb-4">
                                    <div class="card-body">
                                        <div
                                            class="d-flex justify-content-between flex-xl-row flex-md-column flex-row flex-column">
                                            <div class="mb-xl-0 pb-1">
                                                <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                                    <span class="app-brand-logo demo">
                                                        <span style="color: var(--bs-primary)">
                                                            <img class="text-md"
                                                                src="<?php echo e(url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png')); ?>"
                                                                alt="" srcset="" width="60%">
                                                        </span>
                                                    </span>
                                                </div>
                                                <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                                                <div style="font-size: 10px">
                                                    <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                                    <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                                    <p class="mb-1">
                                                        <i
                                                            class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                                                        54417653<?php echo e('  |  '); ?><i
                                                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                                                    </p>
                                                </div>
                                            </div>
                                            <div>
                                                <h3 class="fw-bold">SERVICE REPORT</h3>
                                                <div>
                                                    <span class="fw-bolder">#<?php echo e($service->no_service); ?></span>
                                                </div>
                                                <div class="mt-1">
                                                    <span class="text-muted"><?php echo e($service->date); ?></span>
                                                </div>
                                                <div class="mt-1">
                                                    <?php
                                                        $badgeClass = '';
                                                        $label = $service->type;

                                                        switch ($service->type) {
                                                            case 'Visit':
                                                                $badgeClass = 'success';
                                                                break;
                                                            case 'Service':
                                                                $badgeClass = 'danger';
                                                                break;
                                                            case 'General':
                                                                $badgeClass = 'primary';
                                                                $label = 'General Check';
                                                                break;
                                                            default:
                                                                $badgeClass = '';
                                                                break;
                                                        }
                                                    ?>
                                                    <span
                                                        class="badge fs-6 rounded-pill bg-label-<?php echo e($badgeClass); ?>"><?php echo e($label); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row mb-3">
                                            <div class="col-lg-2 col-4 fw-medium">
                                                <p class="mb-1">Customers </p>
                                                <p class="mb-1">Address </p>
                                                <p class="mb-1">PIC </p>
                                            </div>
                                            <div class="col-lg-4 col-8">
                                                <p class="mb-1">: <?php echo e($service->pic->client->company); ?></p>
                                                <p class="mb-1">: <?php echo e($service->pic->client->area); ?></p>
                                                <p class="mb-1">: <?php echo e($service->pic->name_pic); ?></p>
                                            </div>
                                            <div class="col-lg-2 col-4 fw-medium">
                                                <p class="mb-1">Unit Type </p>
                                                <p class="mb-1">Serial Number </p>
                                                <p class="mb-1">Running & Load </p>
                                            </div>
                                            <div class="col-lg-4 col-8">
                                                <p class="mb-1">: <?php echo e($service->machine->unit->brand); ?>

                                                    <?php echo e($service->machine->unit->unit->sku); ?></p>
                                                <p class="mb-1">: <?php echo e($service->machine->unit->unit->sn); ?>

                                                    <?php echo e($service->machine->tag ? '| ' . $service->machine->tag : ''); ?>

                                                    <?php echo e($service->machine->location ? '| ' . $service->machine->location : ''); ?>

                                                </p>
                                                <p class="mb-1">: <?php echo e($service->running); ?> | <?php echo e($service->load); ?></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-2 col-4 fw-medium">
                                                <p class="mb-1">Job Description </p>
                                            </div>
                                            <div class="col-lg-10 col-8 d-flex gap-1">
                                                <p>: </p>
                                                <p class="mb-1"> <?php echo e($service->jobdesc); ?></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6 col-12">
                                                <h5 class="my-2">Description</h5>
                                                <pre class="mb-1"
                                                    style="font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($service->desc); ?>

                            </pre>
                                            </div>
                                            <div class="col-lg-6 col-12">
                                                <h5 class="my-2">Recomendation</h5>
                                                <pre class="mb-1"
                                                    style="font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($service->recomendation); ?></pre>
                                            </div>
                                        </div>
                                        <hr>
                                        <h5 class="my-4">Picture</h5>
                                        <div class="row mb-5">
                                            <?php $__currentLoopData = $service->picture; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $picture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-lg-4 col-12 text-center">
                                                    <img src="<?php echo e(url('') . '/' . $picture->picture); ?>" alt=""
                                                        srcset="" style="max-width : 200px;">
                                                    <p><?php echo e($picture->keterangan); ?></p>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <div class="row mt-5">
                                            <div class="col-4 mt-5 text-center">
                                                <p>PT Reftech Jaya Optima</p>
                                                <?php if(isset($service->technician->sign)): ?>
                                                    <img src="<?php echo e(url('') . '/' . $service->technician->sign); ?>"
                                                        alt="" srcset="" height="100">
                                                <?php else: ?>
                                                    <div class="pb-5"></div>
                                                <?php endif; ?>
                                                <p class="pt-3">( <?php echo e($service->technician->name); ?> )</p>
                                            </div>
                                            <div class="col-4"></div>
                                            <div class="col-4 mt-5 text-center">
                                                <p class=""><?php echo e($service->pic->client->company); ?></p>
                                                <?php if(isset($service->sign_client)): ?>
                                                    <img src="<?php echo e(url('') . '/' . $service->sign_client); ?>" alt=""
                                                        srcset="" height="100">
                                                <?php else: ?>
                                                    <div class="pb-5"></div>
                                                <?php endif; ?>
                                                <p class="pt-3">( <?php echo e($service->pic->name_pic); ?> )</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <div class="row mt-5">
                            <div class="col-4 mt-5 text-center">
                                <p>PT Reftech Jaya Optima</p>
                                <div class="pb-5"></div>
                                <p class="pt-3">Angel Irene</p>
                            </div>
                            <div class="col-4"></div>
                            <div class="col-4 mt-5 text-center">
                                <p>PT Fajar Surya Wisesa</p>
                                <div class="pb-5"></div>
                                <p class="pt-3">..........................................</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="<?php echo e(route('service-manager-daily.print', [request()->route('id'), request()->route('month')])); ?>">
                        Download
                    </a>
                    <button id="buttonShare" data-id="1"
                        class="btn btn-success d-grid w-100 waves-effect mb-3">Bagikan</button>
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
                </div>
            </div>
        </div>
        
        <?php $__currentLoopData = $compressor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('components.modal.monitoring.issue', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php $__currentLoopData = $mainlog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('components.modal.monitoring.mainlog', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('components.modal.monitoring.mainlog-create-service', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('after-style'); ?>
    <!-- Page CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
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
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-compressor-daily.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-dryer-daily.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-daily-issue.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-daily-mainlog.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script'); ?>
    <script>
        $(() => {
            $('#buttonShare').on('click', function() {
                const id = $(this).data('id')
                if (navigator.share) {
                    navigator.share({
                        title: 'Service Reports',
                        text: 'Check out this link!',
                        url: '<?php echo e(route('service-reports.show', ':id')); ?>'.replace(':id', id)
                    }).then(() => {
                        console.log('Thanks for sharing!');
                    }).catch(err => {
                        console.error('Error sharing:', err);
                    });
                } else {
                    alert('Sharing not supported in this browser.');
                }
            });

            $('#backButton').click(function() {
                window.history.back();
            });
        });

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
        $(document).on('click', '.delete-mainlog', function() {
            var id = $(this).data('id');
            var month = $(this).data('month');
            var machine = $(this).data('machine');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('monitoring')); ?>/daily-mainlog/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/service-manager-daily/' +
                                        machine + '/' + month;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/monitoring/service-visitor.blade.php ENDPATH**/ ?>