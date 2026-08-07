<?php $__env->startSection('title', 'Detail Pembayaran'); ?>
<?php $__env->startSection('content'); ?>

<?php
    $clientCompany = $isUnitQuotation ? $quote->client->company : $quote->pic->client->company;
    $clientNpwp = $isUnitQuotation ? $quote->client->npwp : $quote->pic->client->npwp;
    $clientAddress = $isUnitQuotation ? $quote->client->address : $quote->pic->client->address;
    $clientInfo = $isUnitQuotation ? $quote->client->info : $quote->pic->client->info;

    if ($payment->level == 0) {
        if ($payment->file == null) {
            $statusColor = 'danger'; $statusIcon = 'mdi-clock-outline'; $statusText = 'Menunggu Pembayaran';
        } else {
            $statusColor = 'warning'; $statusIcon = 'mdi-eye-check-outline'; $statusText = 'Menunggu Verifikasi';
        }
    } else {
        $statusColor = 'success'; $statusIcon = 'mdi-check-circle-outline'; $statusText = 'Terverifikasi';
    }

    $netAmount = $payment->amount - ($payment->pph ?? 0) - ($payment->cost ?? 0);
?>


<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="mdi mdi-cash-check text-primary"></i> Detail Pembayaran
            <span class="badge bg-label-<?php echo e($statusColor); ?> rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                <i class="mdi <?php echo e($statusIcon); ?>"></i> <?php echo e($statusText); ?>

            </span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 13px;">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('payment_index.payment')); ?>">Payment Received</a></li>
                <li class="breadcrumb-item active">#RCPT-<?php echo e($payment->id); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('payment_index.payment')); ?>" class="btn btn-sm btn-label-secondary rounded-pill px-3">
            <i class="mdi mdi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>


<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="mdi mdi-receipt-text-outline fs-4"></i></span>
                </div>
                <div class="overflow-hidden">
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Receipt No.</small>
                    <span class="fw-bold text-dark text-truncate d-block" style="font-size: 15px;">#RCPT-<?php echo e($payment->id); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-info"><i class="mdi mdi-calendar-check-outline fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Tanggal Pembayaran</small>
                    <span class="fw-bold text-dark" style="font-size: 15px;"><?php echo e(Carbon\Carbon::parse($payment->date)->format('d M Y')); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-success"><i class="mdi mdi-cash-multiple fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Nominal Pembayaran</small>
                    <span class="fw-bold text-success" style="font-size: 15px;">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-secondary"><i class="mdi mdi-tag-outline fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Tipe / Tag</small>
                    <span class="fw-bold text-dark" style="font-size: 15px;"><?php echo e($payment->type); ?> <?php echo e($payment->percent); ?>%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    
    <div class="col-xl-8">
        
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-domain text-primary"></i> Informasi Customer
                </h6>
                
                <?php if($clientInfo == 'Reftech'): ?>
                    <img src="<?php echo e(asset('/asset')); ?>/logo/Reftech-Log.png" alt="Logo" style="height: 28px;">
                <?php else: ?>
                    <img src="<?php echo e(asset('/asset')); ?>/logo/Kojisha-Log.png" alt="Logo" style="height: 28px;">
                <?php endif; ?>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="avatar avatar-lg flex-shrink-0">
                        <span class="avatar-initial rounded bg-primary text-white fw-bold" style="font-size: 18px;">
                            <?php echo e(strtoupper(substr($clientCompany ?? 'C', 0, 1))); ?>

                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold text-dark mb-1"><?php echo e($clientCompany); ?></h5>
                        <p class="text-muted mb-2" style="font-size: 13px;">
                            <i class="mdi mdi-map-marker-outline me-1"></i> <?php echo e($clientAddress); ?>

                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <?php if($clientNpwp): ?>
                                <span class="badge bg-label-info rounded-pill px-3 py-1">
                                    <i class="mdi mdi-card-account-details-outline me-1"></i> NPWP: <?php echo e($clientNpwp); ?>

                                </span>
                            <?php else: ?>
                                <span class="badge bg-label-danger rounded-pill px-3 py-1">
                                    <i class="mdi mdi-alert-circle-outline me-1"></i> NPWP Belum Diisi
                                </span>
                            <?php endif; ?>
                            <a href="<?php echo e($isUnitQuotation ? route('invoice.show_unit', $invoice->id) : route('invoice.show', $invoice->id)); ?>" target="_blank" class="badge bg-label-primary rounded-pill px-3 py-1 text-decoration-none">
                                <i class="mdi mdi-file-document-outline me-1"></i> Invoice: <?php echo e($invoice->no_invoice); ?>

                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-bank-transfer text-primary"></i> Detail Transaksi Pembayaran
                </h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-xs btn-label-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editDate">
                        <i class="mdi mdi-calendar-edit me-1"></i> Edit Tanggal
                    </button>
                    <button type="button" class="btn btn-xs btn-label-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addPPH">
                        <i class="mdi mdi-percent me-1"></i> <?php echo e($payment->pph > 0 ? 'Edit' : 'Add'); ?> PPH
                    </button>
                    <button type="button" class="btn btn-xs btn-label-info rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addCost">
                        <i class="mdi mdi-currency-usd me-1"></i> <?php echo e($payment->cost > 0 ? 'Edit' : 'Add'); ?> Cost
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr style="font-size: 12px;">
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3">Tanggal</th>
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3">Metode</th>
                                <?php if($payment->pph > 0): ?>
                                    <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-end">PPH</th>
                                <?php endif; ?>
                                <?php if($payment->cost > 0): ?>
                                    <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-end">Cost</th>
                                <?php endif; ?>
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-end">Nominal</th>
                                <?php if($payment->pph > 0 || $payment->cost > 0): ?>
                                    <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-end">Nett</th>
                                <?php endif; ?>
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-center">Tag</th>
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-center">Bukti Transfer</th>
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="font-size: 13px;">
                                <td class="align-middle px-3 text-dark">
                                    <?php echo e(\Carbon\Carbon::parse($payment->created_at)->format('d M Y')); ?>

                                </td>
                                <td class="align-middle px-3">
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <i class="mdi mdi-bank-transfer text-primary"></i> <?php echo e($payment->method); ?>

                                    </span>
                                </td>
                                <?php if($payment->pph > 0): ?>
                                    <td class="align-middle px-3 text-end text-muted">Rp <?php echo e(number_format($payment->pph, 0, ',', '.')); ?></td>
                                <?php endif; ?>
                                <?php if($payment->cost > 0): ?>
                                    <td class="align-middle px-3 text-end text-muted">Rp <?php echo e(number_format($payment->cost, 0, ',', '.')); ?></td>
                                <?php endif; ?>
                                <td class="align-middle px-3 text-end fw-semibold text-dark">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></td>
                                <?php if($payment->pph > 0 || $payment->cost > 0): ?>
                                    <td class="align-middle px-3 text-end fw-bold text-primary">Rp <?php echo e(number_format($netAmount, 0, ',', '.')); ?></td>
                                <?php endif; ?>
                                <td class="align-middle px-3 text-center">
                                    <span class="badge bg-label-secondary rounded-pill px-2.5"><?php echo e($payment->type); ?> <?php echo e($payment->percent); ?>%</span>
                                </td>
                                <td class="align-middle px-3 text-center">
                                    <?php if($payment->file): ?>
                                        <a href="<?php echo e(route('view_payment.payment', $payment->id)); ?>" target="_blank" class="btn btn-xs btn-label-primary rounded-pill px-3">
                                            <i class="mdi mdi-eye-outline me-1"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 12px;">Belum diupload</span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle px-3 text-center">
                                    <?php if($payment->level == 0): ?>
                                        <button type="button" class="btn btn-xs btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#confirmPayment">
                                            <i class="mdi mdi-check me-1"></i> Konfirmasi
                                        </button>
                                    <?php else: ?>
                                        <a href="#" class="btn btn-xs btn-label-danger rounded-pill px-3 unconfirm-payment" data-id="<?php echo e($payment->id); ?>">
                                            <i class="mdi mdi-close me-1"></i> Batal Konfirmasi
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <?php if($payment->note): ?>
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="mdi mdi-note-text-outline text-primary"></i> Catatan Pembayaran
                    </h6>
                </div>
                <div class="card-body p-4">
                    <p class="mb-0 text-dark" style="font-size: 13px; line-height: 1.6; white-space: pre-line;"><?php echo e($payment->note); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="col-xl-4">
        
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-shield-check-outline text-primary"></i> Status Pembayaran
                </h6>
            </div>
            <div class="card-body p-4 text-center">
                <div class="avatar avatar-lg mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-<?php echo e($statusColor); ?>">
                        <i class="mdi <?php echo e($statusIcon); ?> fs-3"></i>
                    </span>
                </div>
                <h5 class="fw-bold text-<?php echo e($statusColor); ?> mb-1"><?php echo e($statusText); ?></h5>
                <small class="text-muted">Diperbarui <?php echo e($payment->updated_at ? $payment->updated_at->diffForHumans() : '-'); ?></small>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-calculator-variant-outline text-primary"></i> Rincian Nominal
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3" style="font-size: 13px;">
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                        <span class="text-muted"><i class="mdi mdi-cash me-1.5 text-secondary"></i> Nominal Bruto</span>
                        <span class="fw-bold text-dark">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></span>
                    </div>
                    <?php if($payment->pph > 0): ?>
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted"><i class="mdi mdi-percent me-1.5 text-danger"></i> PPH</span>
                            <span class="fw-semibold text-danger">- Rp <?php echo e(number_format($payment->pph, 0, ',', '.')); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if($payment->cost > 0): ?>
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted"><i class="mdi mdi-minus-circle-outline me-1.5 text-warning"></i> Cost</span>
                            <span class="fw-semibold text-warning">- Rp <?php echo e(number_format($payment->cost, 0, ',', '.')); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if($payment->pph > 0 || $payment->cost > 0): ?>
                        <div class="d-flex justify-content-between align-items-center pt-1">
                            <span class="fw-bold text-primary"><i class="mdi mdi-cash-check me-1.5"></i> Nett Diterima</span>
                            <span class="fw-bold text-primary" style="font-size: 15px;">Rp <?php echo e(number_format($netAmount, 0, ',', '.')); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-history text-primary"></i> Riwayat Aktivitas
                </h6>
            </div>
            <div class="card-body p-4">
                <?php if($activity->count() > 0): ?>
                    <div class="d-flex flex-column gap-0">
                        <?php $__currentLoopData = $activity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $stats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                if ($stats->status == '1') {
                                    $actStatus = 'Payment Dilihat'; $actColor = 'primary'; $actIcon = 'mdi-eye-outline';
                                } elseif ($stats->status == '2') {
                                    $actStatus = 'Payment Diverifikasi'; $actColor = 'success'; $actIcon = 'mdi-check-circle-outline';
                                } elseif ($stats->status == '3') {
                                    $actStatus = 'Verifikasi Dibatalkan'; $actColor = 'danger'; $actIcon = 'mdi-close-circle-outline';
                                } else {
                                    $actStatus = 'Payment Dibuat'; $actColor = 'info'; $actIcon = 'mdi-plus-circle-outline';
                                }
                            ?>
                            <div class="d-flex gap-3 mb-3 position-relative">
                                
                                <?php if(!$loop->last): ?>
                                    <div class="position-absolute" style="left: 15px; top: 32px; bottom: -12px; width: 2px; background: #e7e7e8;"></div>
                                <?php endif; ?>
                                <div class="avatar avatar-sm flex-shrink-0">
                                    <span class="avatar-initial rounded-circle bg-label-<?php echo e($actColor); ?>">
                                        <i class="mdi <?php echo e($actIcon); ?>" style="font-size: 14px;"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-0.5">
                                        <span class="fw-bold text-dark" style="font-size: 12.5px;"><?php echo e($actStatus); ?></span>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 11px;">
                                        <?php echo e($stats->note); ?> <?php echo e($stats->user->name); ?>

                                    </small>
                                    <small class="text-muted" style="font-size: 10.5px;">
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                        <?php echo e($stats->date->diffInHours(Carbon\Carbon::now()) > 24 ? $stats->date->format('d M Y, H:i') : $stats->date->diffForHumans()); ?>

                                    </small>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="mdi mdi-history text-muted fs-1 mb-2 d-block"></i>
                        <small class="text-muted">Belum ada aktivitas tercatat.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('components.modal.payment.date ', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('components.modal.payment.pph', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('components.modal.payment.cost', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('components.modal.payment.confirm', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
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
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }

        $(".invoice-item-pph-label").on('keyup', function() {
            var input = $(this)
            var input_val = input.val();
            var original_len = input_val.length;
            input_val = formatNumber(input_val);
            input_val = input_val;
            input.val(input_val);
            var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
            console.log(nomorInt);
            $(`#pph`).val(nomorInt);
        });

        $(".invoice-item-cost-label").on('keyup', function() {
            var input = $(this)
            var input_val = input.val();
            var original_len = input_val.length;
            input_val = formatNumber(input_val);
            input_val = input_val;
            input.val(input_val);
            var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
            console.log(nomorInt);
            $(`#cost`).val(nomorInt);
        });

        $(document).on('click', '.confirm-payment', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Confirm this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Confirm it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('confirm-payment')); ?>/payment/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Confirmed!",
                                    text: "Your file has been Confirmed.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/payment-detail/payment/' +
                                        id;
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
        $(document).on('click', '.unconfirm-payment', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to UnConfirm this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, UnConfirm it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('unconfirm-payment')); ?>/payment/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "UnConfirmed!",
                                    text: "Your file has been UnConfirmed.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/payment-detail/payment/' +
                                        id;
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
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/accounting/payment/detail-payment.blade.php ENDPATH**/ ?>