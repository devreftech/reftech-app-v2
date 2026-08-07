<?php $__env->startSection('title', 'Detail Leads'); ?>
<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Clients / Leads /</span> Details <?php echo e($existing->company); ?>

    </h4>

    <div class="card border">
        <div class="card-header py-2">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="leads-detail-tab-nav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-leads-detail"
                        type="button">
                        <i class="menu-icon tf-icons mdi mdi-account-details-outline me-1"></i>Detail
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-leads-crm" type="button">
                        <i class="menu-icon tf-icons mdi mdi-phone-outline me-1"></i>Daily Call
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-leads-quotation"
                        type="button">
                        <i class="menu-icon tf-icons mdi mdi-file-document-outline me-1"></i>Quotation
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-leads-service"
                        type="button">
                        <i class="menu-icon tf-icons mdi mdi-wrench-outline me-1"></i>Service
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content p-0">

                
                <div class="tab-pane fade show active" id="tab-leads-detail" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">Details</h5>
                                    <div class="d-flex gap-2">
                                        <a type="button" data-bs-toggle="modal"
                                            data-bs-target="#updateLeads<?php echo e($existing->id); ?>">
                                            <button type="button" class="btn btn-sm btn-label-primary">Edit</button>
                                        </a>
                                        <a href="#" data-id="<?php echo e($existing->id); ?>"
                                            class="btn btn-sm btn-label-danger delete-leads">Delete</a>
                                        <a href="#" data-id="<?php echo e($existing->id); ?>"
                                            class="btn btn-sm btn-label-info convert-customers">Convert Cust</a>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">Office / Factory</div>
                                    <div class="col-8"><?php echo e($existing->address); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">Area</div>
                                    <div class="col-8"><?php echo e($existing->area); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">Phone</div>
                                    <div class="col-8"><?php echo e($existing->phone); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">Email</div>
                                    <div class="col-8"><?php echo e($existing->email); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">Unit</div>
                                    <div class="col-8"><?php echo e($existing->unit); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">Mobile</div>
                                    <div class="col-8"><?php echo e($existing->mobile); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">R/U</div>
                                    <div class="col-8"><?php echo e($existing->ru); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">Source</div>
                                    <div class="col-8"><?php echo e($existing->source); ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-4 text-muted">Assigned</div>
                                    <div class="col-8"><?php echo e($existing->sales->name); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">PIC</h5>
                                    <a type="button" data-bs-toggle="modal" data-bs-target="#createPic">
                                        <button type="button" class="btn btn-primary">
                                            + New PIC
                                        </button>
                                    </a>
                                </div>
                                <div class="card-datatable table-responsive pt-0">
                                    <table
                                        class="datatable-pic-client<?php echo e(Auth::user()->role == 'Sales' ? '-sales' : ''); ?> table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Position</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="card border shadow-none mb-0">
                                <div class="card-header bg-lighter py-2 d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-primary">
                                        <i class="mdi mdi-file-certificate-outline me-1"></i>NPWP & Tax Details
                                    </h6>
                                    <a type="button" data-bs-toggle="modal" data-bs-target="#editNpwpDetails">
                                        <button type="button" class="btn btn-sm btn-label-primary">Edit</button>
                                    </a>
                                </div>
                                <div class="card-body py-3">
                                    <div class="row mb-2">
                                        <div class="col-md-2 text-muted fw-medium">No. NPWP</div>
                                        <div class="col-md-10 fw-semibold"><?php echo e($existing->npwp ?? '-'); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2 text-muted fw-medium">Alamat NPWP</div>
                                        <div class="col-md-10"><?php echo e($existing->subAddress ?? '-'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 my-3">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">Plant</h5>
                                    <a type="button" data-bs-toggle="modal" data-bs-target="#createPlant">
                                        <button type="button" class="btn btn-primary">
                                            + Tambah Plant
                                        </button>
                                    </a>
                                </div>
                                <?php $__empty_1 = true; $__currentLoopData = $plants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div
                                        class="d-flex justify-content-between align-items-start py-2 <?php echo e(!$loop->last ? 'border-bottom' : ''); ?>">
                                        <div>
                                            <p class="fw-medium mb-1"><?php echo e($plant->name); ?></p>
                                            <p class="text-muted mb-0"><?php echo e($plant->address); ?></p>
                                        </div>
                                        <div class="d-flex gap-2 flex-shrink-0 ms-2">
                                            <a type="button" data-bs-toggle="modal"
                                                data-bs-target="#updatePlant-<?php echo e($plant->id); ?>">
                                                <button type="button" class="btn btn-sm btn-label-primary">Edit</button>
                                            </a>
                                            <a href="#" data-id="<?php echo e($plant->id); ?>"
                                                class="btn btn-sm btn-label-danger delete-plant">Delete</a>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <p class="text-center text-muted mb-0">Belum ada Plant.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-leads-quotation" role="tabpanel">
                    <div class="border rounded p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Quotation Aktif / Berjalan</h5>
                            <a href="<?php echo e(route('quotation.create')); ?>" type="button" class="btn btn-primary">
                                + New Quotation
                            </a>
                        </div>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-quotation-active table table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Quote No.</th>
                                        <th>Total Price</th>
                                        <th>Description</th>
                                        <th>Date Quotation</th>
                                        <th>Status</th>
                                        <th>Date Expired</th>
                                        <th>Stats</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="border rounded p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Quotation Loss</h5>
                        </div>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-quotation-loss table table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Quote No.</th>
                                        <th>Total Price</th>
                                        <th>Description</th>
                                        <th>Date Quotation</th>
                                        <th>Status</th>
                                        <th>Date Expired</th>
                                        <th>Stats</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="border rounded p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Quotation Archive (Done PO)</h5>
                        </div>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-quotation-archive table table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Quote No.</th>
                                        <th>Total Price</th>
                                        <th>Description</th>
                                        <th>Date Quotation</th>
                                        <th>Status</th>
                                        <th>Date Expired</th>
                                        <th>Stats</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>



                
                <div class="tab-pane fade" id="tab-leads-crm" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12 my-3">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">Daily Call History</h5>
                                    <div class="d-flex gap-2">
                                        <?php
                                            $emailPic = 0;
                                        ?>

                                        <?php $__currentLoopData = $charge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                if ($pic->email_pic != null && $pic->email_pic != '-') {
                                                    $emailPic++;
                                                }
                                            ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#createAction<?php echo e($leads->id); ?>">
                                            + New Action
                                        </button>
                                    </div>
                                </div>
                                <?php if($activityTimeline->count()): ?>
                                    <ul class="timeline mb-0 ms-1" id="crmHistoryTimeline">
                                        <?php $__currentLoopData = $activityTimeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="timeline-item timeline-item-transparent clearfix crm-history-item <?php if($index >= 10): ?> d-none <?php endif; ?>">
                                                <span
                                                    class="timeline-point timeline-point-<?php echo e($history['color']); ?>"></span>
                                                <div class="timeline-event">
                                                    <div class="timeline-header mb-1">
                                                        <h6 class="mb-0">
                                                            <?php echo e($history['title']); ?>

                                                            <?php if($history['no_quote']): ?>
                                                                 <a href="<?php echo e($history['url']); ?>"
                                                                    class="ms-1"><?php echo e($history['no_quote']); ?></a>
                                                            <?php endif; ?>
                                                        </h6>
                                                        <small class="text-muted">
                                                            <?php echo e($history['date']->diffInDays(\Carbon\Carbon::now()) > 7 ? $history['date']->format('d M Y') : $history['date']->diffForHumans()); ?>

                                                        </small>
                                                    </div>
                                                    <span
                                                        class="badge bg-label-<?php echo e($history['color']); ?> mb-2"><?php echo e($history['category']); ?></span>
                                                    <p class="mb-0">
                                                        <span class="fw-medium"><?php echo e($history['status']); ?></span>
                                                        <?php if($history['note']): ?>
                                                            — <?php echo e($history['note']); ?>

                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                    <?php if($activityTimeline->count() > 10): ?>
                                        <div class="text-center mt-3">
                                            <button type="button" class="btn btn-label-primary btn-sm"
                                                id="crmHistoryLoadMore">
                                                Load More
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="text-center text-muted mb-0">Belum ada Daily Call History.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if(optional(Auth::user()->detail->first())->area == 'Bekasi' ||
                                optional(Auth::user()->detail->first())->area == 'Jabodetabek' ||
                                (optional(Auth::user()->detail->first())->area == 'Jawa Barat' && Auth::user()->role == 'Sales')): ?>
                            <div class="col-md-12 my-3">
                                <div class="border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-bold mb-0">Visit History</h5>
                                        <a type="button" data-bs-toggle="modal" data-bs-target="#createActionVisit">
                                            <button type="button" class="btn btn-primary">
                                                + New Action
                                            </button>
                                        </a>
                                    </div>
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                    <th>Status</th>
                                                    <th>note</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                <?php $__empty_1 = true; $__currentLoopData = $visit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visits): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo e(\Carbon\Carbon::parse($visits->date)->format('d-m-Y')); ?>

                                                        </td>
                                                        <td>
                                                            <?php echo e($visits->action); ?>

                                                        </td>
                                                        <td>
                                                            <?php echo e($visits->status); ?>

                                                        </td>
                                                        <td>
                                                            <?php echo e($visits->note); ?>

                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center">
                                                            Kamu belum punya Visit.
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-leads-service" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12 my-3">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">Machine</h5>
                                    <a type="button" data-bs-toggle="modal" data-bs-target="#createMachine">
                                        <button type="button" class="btn btn-primary">
                                            + Create New machine
                                        </button>
                                    </a>
                                </div>
                                <div class="card-datatable table-responsive pt-0">
                                    <table class="datatable-machine-client table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>ID</th>
                                                <th>Category</th>
                                                <th>Brand</th>
                                                <th>Type</th>
                                                <th>SN</th>
                                                <th>Tag</th>
                                                <th>Location</th>
                                                <th>Service Report</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 my-3">
                            <div class="border rounded p-3 bg-white">
                                <div class="mb-3">
                                    <h5 class="fw-bold mb-1">Riwayat Laporan Servis</h5>
                                    <p class="text-muted small mb-0">Pilih kategori riwayat laporan di bawah untuk melihat detail servis teknisi.</p>
                                </div>

                                
                                <ul class="nav nav-pills nav-fill mb-3 border-bottom pb-2" id="service-subtabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active fw-semibold" id="subtab-service-btn" data-bs-toggle="pill" data-bs-target="#subtab-service" type="button" role="tab" aria-controls="subtab-service" aria-selected="true">
                                            <i class="mdi mdi-wrench-outline me-1"></i>Service
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link fw-semibold" id="subtab-visit-btn" data-bs-toggle="pill" data-bs-target="#subtab-visit" type="button" role="tab" aria-controls="subtab-visit" aria-selected="false">
                                            <i class="mdi mdi-map-marker-path me-1"></i>Visit
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link fw-semibold" id="subtab-general-btn" data-bs-toggle="pill" data-bs-target="#subtab-general" type="button" role="tab" aria-controls="subtab-general" aria-selected="false">
                                            <i class="mdi mdi-clipboard-check-outline me-1"></i>General Check
                                        </button>
                                    </li>
                                </ul>

                                
                                <div class="tab-content p-0" id="service-subtabs-content">
                                    
                                    <div class="tab-pane fade show active" id="subtab-service" role="tabpanel" aria-labelledby="subtab-service-btn">
                                        <div class="card-datatable table-responsive pt-0">
                                            <table class="datatable-service-history table table-bordered w-100" id="dataTableServiceHistory">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>ID</th>
                                                        <th>No Service</th>
                                                        <th>Unit</th>
                                                        <th>Teknisi</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>

                                    
                                    <div class="tab-pane fade" id="subtab-visit" role="tabpanel" aria-labelledby="subtab-visit-btn">
                                        <div class="card-datatable table-responsive pt-0">
                                            <table class="datatable-visit-history table table-bordered w-100" id="dataTableServiceVisitHistory">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>ID</th>
                                                        <th>No Service</th>
                                                        <th>Unit</th>
                                                        <th>Teknisi</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>

                                    
                                    <div class="tab-pane fade" id="subtab-general" role="tabpanel" aria-labelledby="subtab-general-btn">
                                        <div class="card-datatable table-responsive pt-0">
                                            <table class="datatable-general-history table table-bordered w-100" id="dataTableGeneralHistory">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>ID</th>
                                                        <th>No Service</th>
                                                        <th>Unit</th>
                                                        <th>Teknisi</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php echo $__env->make('pages.sales.clients.leads.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.pic.leads.form-create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.machine.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.req-visit.form-create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.plant.form-create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('pages.sales.activities.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('pages.sales.activities.form-visit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php $__currentLoopData = $charge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('components.modal.pic.leads.form-update', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $machines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $machine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('components.modal.machine.form-edit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $plants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('components.modal.plant.form-update', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="modal fade" id="machineReportsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="machineReportsModalTitle">Service Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="machineReportsList">
                        <p class="text-center text-muted mb-0">Memuat...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="<?php echo e(route('leads.update', $existing->id)); ?>" method="post">
        <?php echo csrf_field(); ?>
        <?php echo method_field('patch'); ?>
        <input type="hidden" name="company" value="<?php echo e($existing->company); ?>">
        <input type="hidden" name="email" value="<?php echo e($existing->email); ?>">
        <input type="hidden" name="phone" value="<?php echo e($existing->phone); ?>">
        <input type="hidden" name="ru" value="<?php echo e($existing->ru); ?>">
        <input type="hidden" name="unit" value="<?php echo e($existing->unit); ?>">
        <input type="hidden" name="source" value="<?php echo e($existing->source); ?>">
        <input type="hidden" name="mobile" value="<?php echo e($existing->mobile); ?>">
        <input type="hidden" name="address" value="<?php echo e($existing->address); ?>">
        <input type="hidden" name="area" value="<?php echo e($existing->area); ?>">
        <?php if(Auth::user()->id == 1 || Auth::user()->id == 16): ?>
            <input type="hidden" name="info" value="<?php echo e($existing->info); ?>">
        <?php endif; ?>

        <div class="modal fade" id="editNpwpDetails" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit NPWP & Tax Details</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-12 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="npwpInput" class="form-control npwp-number-only" name="npwp"
                                        placeholder="16 Digit No. NPWP" value="<?php echo e(old('npwp', $existing->npwp)); ?>" 
                                        inputmode="numeric" pattern="\d{16}" minlength="16" maxlength="16"
                                        title="No. NPWP harus persis 16 digit angka" required>
                                    <label for="npwpInput">No. NPWP (16 Digit)</label>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" name="subAddress" id="subAddressInput"
                                        placeholder="Alamat NPWP"><?php echo e(old('subAddress', $existing->subAddress)); ?></textarea>
                                    <label for="subAddressInput">Alamat NPWP</label>
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
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-quotation-client.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-machine-client.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-pic-client.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-pic-client-sales.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-service-history.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-general-history.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-visit-history.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/forms-selects.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script'); ?>
    <script>
        // Re-adjust DataTables column widths when switching tabs
        $('#leads-detail-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
            $.fn.dataTable.tables({
                visible: true,
                api: true
            }).columns.adjust().responsive.recalc();
        });

        $(document).on('click', '#crmHistoryLoadMore', function() {
            $('#crmHistoryTimeline .crm-history-item.d-none').removeClass('d-none');
            $(this).parent().remove();
        });

        $(document).on('click', '.delete-pic', function() {
            var id = $(this).data('id');
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
                        'url': '<?php echo e(url('pic')); ?>/' + id,
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
                                    location.reload();
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

        $(document).on('click', '.delete-machine', function() {
            var id = $(this).data('id');
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
                        'url': '<?php echo e(url('machine')); ?>/' + id,
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
                                    location.reload();
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

        $(document).on('click', '.delete-plant', function() {
            var id = $(this).data('id');
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
                        'url': '<?php echo e(url('plant')); ?>/' + id,
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
                                    location.reload();
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

        $(document).on('click', '.delete-leads', function() {
            var id = $(this).data('id');
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
                        'url': '<?php echo e(url('leads')); ?>/' + id,
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
                                    window.location.href = '/leads';
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

        $(document).on('click', '.convert-customers', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Convert it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('leads')); ?>/convert/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your file has been converted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/existing/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Convert!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Convert is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });

        $(document).on('input', '.npwp-number-only', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
        });

        // Re-adjust DataTables column widths when switching subtabs inside Tab Service
        $('#service-subtabs button[data-bs-toggle="pill"]').on('shown.bs.tab', function() {
            $.fn.dataTable.tables({
                visible: true,
                api: true
            }).columns.adjust().responsive.recalc();
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sales/clients/leads/detail.blade.php ENDPATH**/ ?>