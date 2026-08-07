<?php use Illuminate\Support\Facades\Storage; ?>

<?php $__env->startSection('title', 'Invoice ' . ($invoice->no_invoice ?? '#' . $invoice->id)); ?>
<?php $__env->startSection('content'); ?>
    <div class="d-flex align-items-center justify-content-between py-3 mb-4">
        <h4 class="fw-bold m-0">
            <span class="text-muted fw-light">
                <a href="<?php echo e(route('invoice.index')); ?>" class="text-muted">Accounting / Invoice</a> /
            </span>
            <?php echo e($invoice->no_invoice ?? '#' . $invoice->id); ?>

        </h4>
        <div class="d-flex align-items-center gap-2">
            <?php if(isset($pendingPO) && $pendingPO): ?>
                <a href="<?php echo e(route('pending-po.show', $pendingPO->id)); ?>"
                   target="_blank"
                   class="btn btn-outline-info shadow-sm waves-effect"
                   title="Buka Halaman Sales Order untuk PO ini">
                    <i class="mdi mdi-clipboard-text-outline me-1"></i> Sales Order
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('pending-po.sales-order')); ?>"
                   target="_blank"
                   class="btn btn-outline-secondary shadow-sm waves-effect"
                   title="Buka Daftar Sales Order">
                    <i class="mdi mdi-clipboard-text-outline me-1"></i> Sales Order
                </a>
            <?php endif; ?>

            <?php if(in_array(Auth::user()->role, ['Admin', 'Accounting', 'Finance Manager', 'Finance'])): ?>
                <?php if(isset($monitoringTask) && $monitoringTask): ?>
                    <a href="<?php echo e(route('kanban.monitoring-document')); ?>?task_id=<?php echo e($monitoringTask->id); ?>"
                       target="_blank"
                       class="btn btn-outline-primary shadow-sm waves-effect"
                       title="Buka Papan Monitoring Document & Otomatis Buka Card Detail">
                        <i class="mdi mdi-text-box-search-outline me-1"></i> Monitoring Document
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('kanban.monitoring-document')); ?>"
                       target="_blank"
                       class="btn btn-outline-secondary shadow-sm waves-effect"
                       title="Buka Papan Monitoring Document">
                        <i class="mdi mdi-view-dashboard-outline me-1"></i> Monitoring Document
                    </a>
                <?php endif; ?>

                <?php if(isset($bast) && $bast): ?>
                    <button type="button" class="btn btn-outline-success shadow-sm waves-effect" onclick="switchToBastTab()" title="Lihat Berita Acara Serah Terima">
                        <i class="mdi mdi-certificate-outline me-1"></i> BAST (<?php echo e($bast->no_bast); ?>)
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-success shadow-sm waves-effect" data-bs-toggle="modal" data-bs-target="#modalCreateBast" title="Buat Berita Acara Serah Terima Baru">
                        <i class="mdi mdi-plus me-1"></i> BAST
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="nav-align-top mb-4">
        <ul class="nav nav-tabs nav-fill shadow-sm rounded border-0 mb-4" role="tablist" style="background:#fff; padding: 5px;">
            <li class="nav-item">
                <button type="button" class="nav-link active fw-bold py-2.5 fs-6" role="tab" data-bs-toggle="tab" data-bs-target="#tab-invoice" aria-controls="tab-invoice" aria-selected="true">
                    <i class="mdi mdi-receipt-text-outline me-2 fs-5 text-primary"></i> Faktur Penjualan (Invoice)
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link fw-bold py-2.5 fs-6" role="tab" data-bs-toggle="tab" data-bs-target="#tab-delivery" aria-controls="tab-delivery" aria-selected="false">
                    <i class="mdi mdi-truck-delivery-outline me-2 fs-5 text-primary"></i> Delivery Order (Surat Jalan)
                    <?php
                        $delCount = $quote->deliveries ? $quote->deliveries->count() : 0;
                    ?>
                    <?php if($delCount > 0): ?>
                        <span class="badge rounded-pill bg-primary ms-1" style="font-size:11px;"><?php echo e($delCount); ?></span>
                    <?php endif; ?>
                </button>
            </li>
            <?php if(isset($bast) && $bast): ?>
                <li class="nav-item">
                    <button type="button" class="nav-link fw-bold py-2.5 fs-6" id="btn-tab-bast" role="tab" data-bs-toggle="tab" data-bs-target="#tab-bast" aria-controls="tab-bast" aria-selected="false">
                        <i class="mdi mdi-certificate-outline me-2 fs-5 text-success"></i> Berita Acara (BAST)
                        <span class="badge bg-success ms-1" style="font-size:10px;"><?php echo e($bast->no_bast); ?></span>
                    </button>
                </li>
            <?php endif; ?>
        </ul>

        <div class="tab-content p-0 bg-transparent border-0 shadow-none">
            
            <div class="tab-pane fade show active" id="tab-invoice" role="tabpanel">
                <div class="row invoice-preview">
                    
                    <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                        <div class="d-flex justify-content-end mb-2">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Invoice language toggle">
                                <button type="button" class="btn btn-primary invoice-lang-btn active" data-lang="id">ID</button>
                                <button type="button" class="btn btn-outline-primary invoice-lang-btn" data-lang="en">EN</button>
                            </div>
                        </div>
            <div class="card invoice-preview-card" style="position: relative; overflow: hidden;">

                
                <?php if($invoice->status_p): ?>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-35deg); font-size: 160px; font-weight: 900; color: rgba(40, 167, 69, 0.10); pointer-events: none; z-index: 0; letter-spacing: 12px; white-space: nowrap; user-select: none;">
                        PAID
                    </div>
                <?php else: ?>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-35deg); font-size: 140px; font-weight: 900; color: rgba(220, 53, 69, 0.10); pointer-events: none; z-index: 0; letter-spacing: 12px; white-space: nowrap; user-select: none;">
                        UNPAID
                    </div>
                <?php endif; ?>

                
                <div class="card-body p-4" style="position: relative; z-index: 1;">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column <?php echo e(!$quote->tax ? 'justify-content-end' : ''); ?> gap-3 mb-0">
                        <?php if($quote->tax): ?>
                            <div class="mb-xl-0 pb-1">
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                                    <span class="app-brand-logo demo">
                                        <img src="<?php echo e(asset('/asset')); ?>/logo/Reftech-Log.png" alt="Reftech Logo" width="180">
                                    </span>
                                </div>
                                <div class="d-flex flex-row align-items-start gap-4 mt-2" style="font-size: 11px;">
                                    <div class="info" style="max-width: 260px;">
                                        <p class="mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                                            <i class="mdi mdi-office-building-outline me-1 text-primary"></i><span class="i18n" data-en="Office Address :">Alamat Kantor :</span>
                                        </p>
                                        <p class="mb-1 text-muted" style="line-height: 1.4;">Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                                        <p class="mb-0 text-muted">
                                            <i class="mdi mdi-phone-outline me-1 text-primary"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1 text-primary"></i>accounting@reftech.id
                                        </p>
                                    </div>
                                    <div class="npwp_add" style="max-width: 280px;">
                                        <p class="mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                                            <i class="mdi mdi-file-document-outline me-1 text-primary"></i><span class="i18n" data-en="NPWP Address :">Alamat NPWP :</span>
                                        </p>
                                        <p class="mb-1 text-muted" style="line-height: 1.4;">Komp. Negia Kencana Residence Blok B, No.2 Pasanggrahan, Ujung Berung Kota Bandung - Jawa Barat 40199</p>
                                        <div class="px-2 py-0.5 rounded-0" style="background:#eef0ff; border:1px solid #d0d0ff; font-size:10.5px; font-weight:600; color:#3d3d8f; display:inline-block; border-radius:0 !important;">
                                            <i class="mdi mdi-card-account-details-outline me-1"></i>NPWP: 73.728.571.8-429.000
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mb-xl-0 pb-1">
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                                    <span class="app-brand-logo demo">
                                        <img src="<?php echo e(asset('/asset')); ?>/logo/Reftech-Log.png" alt="Reftech Logo" width="180">
                                    </span>
                                </div>
                                <p class="mb-1 fw-bold text-dark" style="font-size:14px;">PT Reftech Jaya Optima</p>
                                <p class="mb-0 text-muted" style="font-size:11px;">Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                            </div>
                        <?php endif; ?>

                        <div class="text-end">
                            <h1 class="fw-bold" style="color: #2529fa; letter-spacing: 2px;">INVOICE</h1>
                            <p class="mb-1 fw-bold text-dark" style="font-size:14px;">#<?php echo e($invoice->no_invoice); ?></p>
                            <p class="mb-1 text-muted small"><?php echo e($invoice->date ? \Carbon\Carbon::parse($invoice->date)->format('d F Y') : '-'); ?></p>
                            <?php
                                $hasProof = $payments->whereNotNull('file')->where('level', 0)->isNotEmpty();
                                if ($invoice->status_p) {
                                    $warna  = 'bg-label-success text-success';
                                    $textId = 'Terverifikasi';
                                    $textEn = 'Verified';
                                } elseif ($hasProof) {
                                    $warna  = 'bg-label-warning text-warning';
                                    $textId = 'Menunggu Verifikasi';
                                    $textEn = 'Awaiting Verification';
                                } else {
                                    $warna  = 'bg-label-dark text-dark';
                                    $textId = 'Menunggu Pembayaran';
                                    $textEn = 'Waiting Payment';
                                }
                            ?>
                            <div class="mt-1">
                                <span class="badge <?php echo e($warna); ?> px-3 py-1 fs-6 fw-bold i18n" data-en="<?php echo e($textEn); ?>"><?php echo e($textId); ?></span>
                            </div>
                        </div>
                    </div>

                    <div style="height:2px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:16px 0 18px;"></div>

                    
                    <div style="display:flex !important; align-items:stretch !important; gap:14px; margin-bottom:18px; font-size:12px;">
                        
                        <div style="flex:1.4; display:flex; flex-direction:column; align-self:stretch; border:1px solid #e0e0e0; border-left:4px solid #696cff; border-radius:4px; padding:12px 16px; background:#fcfcfc;">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1" style="border-bottom: 1px dashed #e4e4e4;">
                                <span class="fw-bold text-uppercase" style="font-size:10.5px; letter-spacing:0.6px; color:#696cff;">
                                    <i class="mdi mdi-domain me-1"></i>Invoice To
                                </span>
                                <?php if($quote->client?->npwp): ?>
                                    <span class="px-2 py-0.5 rounded" style="font-size:10px; font-weight:600; background:#f0f2ff; color:#43497a; border:1px solid #d5d9ff;">
                                        <i class="mdi mdi-card-account-details-outline me-1"></i>NPWP: <?php echo e($quote->client->npwp); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <p class="mb-2 fw-bold text-dark" style="font-size:14px; line-height:1.3;">
                                <?php echo e($quote->client?->company ?? '-'); ?>

                            </p>

                            <?php
                                $picName = $quote->pic?->name_pic ?? $quote->attn;
                                $targetAddress = $invoice->invoiceTo == '1' ? ($quote->client?->address ?? '-') : ($quote->client?->subAddress ?? '-');
                            ?>

                            <div style="display:grid; grid-template-columns: auto 1fr; gap:4px 12px; font-size:11.5px; color:#333;">
                                <?php if($picName): ?>
                                    <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-account-outline me-1 text-primary"></i>Attn / PIC</span>
                                    <span class="fw-medium text-dark">
                                        : <?php echo e($picName); ?>

                                        <?php if($quote->pic?->phone_pic): ?>
                                            <span class="text-muted ms-1">(<?php echo e($quote->pic->phone_pic); ?>)</span>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>

                                <?php if($quote->client?->phone): ?>
                                    <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-phone-in-talk-outline me-1 text-primary"></i>Office Phone</span>
                                    <span class="fw-medium text-dark">: <?php echo e($quote->client->phone); ?></span>
                                <?php endif; ?>

                                <?php if($targetAddress && $targetAddress !== '-'): ?>
                                    <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-map-marker-outline me-1 text-primary"></i>Address</span>
                                    <span class="fw-medium text-dark" style="line-height:1.4;">: <?php echo e($targetAddress); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <div style="min-width:240px; flex:1; display:flex; flex-direction:column; align-self:stretch; border:1px solid #e0e0e0; border-left:4px solid #8592a3; border-radius:4px; padding:12px 16px; background:#fcfcfc;">
                            <div class="mb-2 pb-1" style="border-bottom: 1px dashed #e4e4e4;">
                                <span class="fw-bold text-uppercase" style="font-size:10.5px; letter-spacing:0.6px; color:#566a7f;">
                                    <i class="mdi mdi-file-document-outline me-1"></i>Payment Terms &amp; Info
                                </span>
                            </div>
                            
                            <?php
                                $tempoPayRec = $payments->firstWhere('type', 'Tempo') ?? $payments->first();
                                $dueDateDisplay = $tempoPayRec?->due_date ? \Carbon\Carbon::parse($tempoPayRec->due_date) : null;
                            ?>

                            <div style="font-size:11.5px; color:#333;" class="my-auto">
                                <div class="d-flex align-items-center mb-1.5 pb-1" style="border-bottom:1px dashed #f0f0f0;">
                                    <span class="text-muted" style="min-width:110px;"><i class="mdi mdi-clipboard-text-outline me-1 text-primary"></i>PO No</span>
                                    <span class="fw-bold text-dark">: <?php echo e($quote->po_number ?? '-'); ?></span>
                                </div>
                                <?php if($dueDateDisplay): ?>
                                    <div class="d-flex align-items-center mb-1.5 pb-1" style="border-bottom:1px dashed #f0f0f0;">
                                        <span class="text-muted" style="min-width:110px;"><i class="mdi mdi-calendar-clock me-1 text-warning"></i><span class="i18n" data-en="Due Date">Jatuh Tempo</span></span>
                                        <span class="fw-bold text-warning">: <?php echo e($dueDateDisplay->format('d F Y')); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="mt-2">
                                    <div class="fw-medium text-dark mb-1">
                                        <i class="mdi mdi-clock-outline me-1 text-primary"></i>Term of Payment :
                                    </div>
                                    <div class="ps-2 ms-1" style="border-left:3px solid #696cff; margin-top:4px;">
                                        <div class="fw-bold text-dark ps-2" style="font-size:11.5px; line-height:1.45; white-space:pre-line;"><i class="mdi mdi-chevron-right text-primary me-1" style="font-size:13px;"></i><?php echo e($invoice->term ?? $quote->payment_method ?? '-'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                
                    <?php
                        $specLabels = [
                            'brand'=>'Brand','model'=>'Model','type_unit'=>'Type',
                            'bar'=>'Max Pressure','air_cap'=>'Air Capacity','power'=>'Motor Power',
                            'voltage'=>'Voltage','connect'=>'Drive','cooling'=>'Cooling Method',
                            'exhaust'=>'Connection','refrigerant_type'=>'Refrigerant Type','pdp'=>'PDP',
                            'filtration'=>'Filtration','oil_content'=>'Oil Content','grade'=>'Grade',
                            'capacity'=>'Capacity','material'=>'Material','test_pressure'=>'Test Pressure',
                            'inlet_pressure'=>'Inlet Pressure','outlet_pressure'=>'Outlet Pressure',
                            'inlet_cap'=>'Inlet Capacity (LP)','outlet_cap'=>'Outlet Capacity (HP)',
                            'dimension'=>'Dimension','weight'=>'Weight',
                        ];
                        $specUnits = [
                            'bar'=>' Bar','air_cap'=>' m³/min','test_pressure'=>' Bar',
                            'inlet_pressure'=>' Bar','outlet_pressure'=>' Bar',
                            'inlet_cap'=>' m³/min','outlet_cap'=>' m³/min',
                            'weight'=>' Kg','capacity'=>' Liter',
                        ];
                        $hasDisc = $quote->details->where('disc', '>', 0)->count() > 0;
                        $colCount = 5 + ($hasDisc ? 1 : 0) + ($quote->tax ? 1 : 0);
                    ?>
                    <div class="table-responsive rounded border mb-3">
                        <table class="table table-bordered m-0" style="width:100%; font-size:12px;">
                            <thead style="font-size:11px; background:#eeeeff; color:#3d3d8f;">
                                <tr>
                                    <th class="text-center align-middle py-2" style="width:4%; font-weight:700; border-color:#d0d0ff;">No.</th>
                                    <th class="text-center align-middle py-2" style="font-weight:700; border-color:#d0d0ff;"><span class="i18n" data-en="DESCRIPTION">DESKRIPSI</span></th>
                                    <th class="text-center align-middle py-2" style="width:10%; font-weight:700; border-color:#d0d0ff;">Qty</th>
                                    <th class="text-center align-middle py-2" style="width:18%; font-weight:700; border-color:#d0d0ff;"><span class="i18n" data-en="PRICE (IDR)">HARGA (IDR)</span></th>
                                    <?php if($hasDisc): ?>
                                        <th class="text-center align-middle py-2" style="width:7%; font-weight:700; border-color:#d0d0ff;">Disc</th>
                                    <?php endif; ?>
                                    <?php if($quote->tax): ?>
                                        <th class="text-center align-middle py-2" style="width:15%; font-weight:700; border-color:#d0d0ff;">DPP (IDR)</th>
                                    <?php endif; ?>
                                    <th class="text-center align-middle py-2" style="width:18%; font-weight:700; border-color:#d0d0ff;"><span class="i18n" data-en="TOTAL PRICE (IDR)">TOTAL HARGA (IDR)</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $itemNo = 1;
                                    $headerCount = 0;
                                ?>
                                <?php $__currentLoopData = $quote->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($item->type === 'header' || $item->type === 'heading'): ?>
                                        <?php
                                            $lbl = trim($item->label ?? '');
                                            if (!preg_match('/^[A-Z0-9][\.\)]/i', $lbl)) {
                                                $lbl = chr(65 + ($headerCount % 26)) . '. ' . $lbl;
                                            }
                                            $headerCount++;
                                        ?>
                                        <tr style="background:#f0f0ff;">
                                            <td colspan="<?php echo e($colCount); ?>" class="fw-bold text-primary text-uppercase px-3" style="padding: 5px 10px; font-size:11.5px; border-top:1px solid #d0d0ff; border-bottom:1px solid #d0d0ff;">
                                                <i class="mdi mdi-bookmark-outline me-1"></i><?php echo e($lbl); ?>

                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $dpp = $quote->tax ? ($item->amount * 11 / 12) : 0; ?>
                                        <tr style="font-size: 12px">
                                            <td class="text-center align-top py-2"><?php echo e($itemNo++); ?></td>
                                            <td class="align-top py-2">
                                                <?php if($item->type === 'unit' && $item->unit): ?>
                                                    <p class="mb-1 fw-semibold" style="font-size: 12px">
                                                        <?php echo e($item->label ?: ($item->unit->brand . ' ' . $item->unit->sku . ($item->unit->model ? ' — ' . $item->unit->model : ''))); ?>

                                                    </p>
                                                    <?php $specs = $item->getSpecVisibleArray(); ?>
                                                    <?php if(!empty($specs)): ?>
                                                        <div class="spec-detail-rows" style="font-size:11px; color:#777; margin-top:4px; <?php echo e($invoice->show_spec ? '' : 'display:none;'); ?>">
                                                            <?php $__currentLoopData = $specs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php if($field === 'unit'): ?> <?php continue; ?> <?php endif; ?>
                                                                <?php $val = $item->unit->$field ?? null; ?>
                                                                <?php if($val && isset($specLabels[$field])): ?>
                                                                    <div style="display:flex; padding:1px 0;">
                                                                        <span style="min-width:110px; flex-shrink:0;"><?php echo e($specLabels[$field]); ?></span>
                                                                        <span>: <?php echo e($val); ?><?php echo e($specUnits[$field] ?? ''); ?></span>
                                                                    </div>
                                                                <?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php elseif($item->type === 'equivalent' || $item->type === 'sparepart' || $item->id_equivalent || $item->equivalent): ?>
                                                     <?php if($item->equivalent): ?>
                                                         <?php
                                                             $brandPn = trim(($item->equivalent->brand ?? '') . ($item->equivalent->pn ? ' - ' . $item->equivalent->pn : ''));
                                                             $subDesc = $item->label;
                                                             if (empty($subDesc) || $subDesc === $brandPn) {
                                                                 $subDesc = optional($item->equivalent->product)->description ?? optional($item->equivalent->product)->name;
                                                             }
                                                         ?>
                                                         <p class="mb-0 fw-bold text-dark" style="font-size: 12px"><?php echo e($brandPn ?: $item->label); ?></p>
                                                         <?php if($subDesc && $subDesc !== $brandPn): ?>
                                                             <div style="font-size: 12px; color: #333333; font-weight: 500; margin-top: 2px; line-height: 1.4;"><?php echo e($subDesc); ?></div>
                                                         <?php endif; ?>
                                                     <?php else: ?>
                                                         <p class="mb-0 fw-bold text-dark" style="font-size: 12px"><?php echo e($item->label); ?></p>
                                                     <?php endif; ?>
                                                <?php else: ?>
                                                    <p class="mb-0 fw-bold text-dark" style="font-size: 12px"><?php echo e($item->label); ?></p>
                                                <?php endif; ?>
                                                <?php if($item->description): ?>
                                                     <div style="font-size: 11px; color: #444; white-space: pre-line; margin-top: 3px; line-height: 1.4;"><?php echo e($item->description); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center align-top py-2">
                                                <?php echo e((float) $item->qty); ?> <?php echo e($item->info_qty ?? 'Unit'); ?>

                                                <?php if($item->remaining_qty <= 0): ?>
                                                    <div><span class="badge bg-label-success mt-1" style="font-size:9.5px;"><span class="i18n" data-en="Fully Delivered">Terkirim Semua</span></span></div>
                                                <?php elseif($item->delivered_qty > 0): ?>
                                                    <div><span class="badge bg-label-warning mt-1" style="font-size:9.5px;"><span class="i18n" data-en="Remaining <?php echo e($item->remaining_qty); ?>">Sisa <?php echo e($item->remaining_qty); ?></span></span></div>
                                                <?php else: ?>
                                                    <div><span class="badge bg-label-secondary mt-1" style="font-size:9.5px;"><span class="i18n" data-en="Not Delivered Yet">Belum Dikirim</span></span></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end align-top py-2"><?php echo e(number_format($item->price, 0, '', '.')); ?></td>
                                            <?php if($hasDisc): ?>
                                                <td class="text-center align-top py-2"><?php echo e($item->disc > 0 ? (float) $item->disc . '%' : '-'); ?></td>
                                            <?php endif; ?>
                                            <?php if($quote->tax): ?>
                                                <td class="text-end align-top py-2"><?php echo e(number_format($dpp, 0, '', '.')); ?></td>
                                            <?php endif; ?>
                                            <td class="text-end align-top py-2 fw-semibold"><?php echo e(number_format($item->amount, 0, '', '.')); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <?php
                        $afterDisc = $quote->diskon > 0
                            ? $quote->subtotal - $quote->discount_amount
                            : $quote->subtotal;
                    ?>
                    <div class="d-flex justify-content-end mb-3">
                        <div style="min-width:280px; font-size:12px; border:1px solid #d0d0ff; border-left:4px solid #696cff; border-radius:6px; overflow:hidden; background:#fff;">
                            <table style="width:100%; border-collapse:collapse;">
                                <tr>
                                    <td style="padding:6px 16px 6px 14px; color:#555;">Subtotal</td>
                                    <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp <?php echo e(number_format($quote->subtotal, 0, '', '.')); ?></td>
                                </tr>
                                <?php if($quote->diskon > 0): ?>
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;">Discount<?php echo e($quote->discount_label ? ' ' . $quote->discount_label : ''); ?></td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#dc3545;">- Rp <?php echo e(number_format($quote->discount_amount, 0, '', '.')); ?></td>
                                    </tr>
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;">After Discount</td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp <?php echo e(number_format($afterDisc, 0, '', '.')); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if($quote->tax): ?>
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;"><span class="i18n" data-en="DPP on PPN">DPP Atas PPN</span></td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp <?php echo e(number_format($afterDisc * 11 / 12, 0, '', '.')); ?></td>
                                    </tr>
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;">PPN 12%</td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp <?php echo e(number_format($quote->tax_amount, 0, '', '.')); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if($quote->shipping > 0): ?>
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;">Shipping Cost</td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp <?php echo e(number_format($quote->shipping, 0, '', '.')); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if($totalPph > 0): ?>
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;">PPH 23</td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#dc3545;">- Rp <?php echo e(number_format($totalPph, 0, '', '.')); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php
                                    $showTagihanBreakdown = in_array($invoice->type, ['DP', 'BP', 'Balance Payment', 'Down Payment']) || floatval($invoice->percent) < 100;
                                ?>
                                <tr style="border-top:2px solid #d0d0ff; background:<?php echo e(!$showTagihanBreakdown ? 'yellow' : '#f0f0ff'); ?>;">
                                    <td style="padding:9px 16px 9px 14px; font-weight:700; font-size:13px; color:<?php echo e(!$showTagihanBreakdown ? '#000' : '#3d3d8f'); ?>;">TOTAL</td>
                                    <td style="padding:9px 14px 9px 0; text-align:right; font-weight:700; font-size:13px; color:<?php echo e(!$showTagihanBreakdown ? '#000' : '#696cff'); ?>;">Rp <?php echo e(number_format($showTagihanBreakdown ? $quote->total : $totalAfterPph, 0, '', '.')); ?></td>
                                </tr>
                                <?php if($showTagihanBreakdown): ?>
                                    <?php if(in_array($invoice->type, ['BP', 'Balance Payment'])): ?>
                                        <?php
                                            $dpInvoices = isset($allInvoices) ? $allInvoices->reject(fn($i) => $i->id == $invoice->id) : collect();
                                            $dpPercent  = $dpInvoices->sum(fn($i) => floatval($i->percent));
                                            if ($dpPercent <= 0 && floatval($invoice->percent) < 100) {
                                                $dpPercent = 100 - floatval($invoice->percent);
                                            }
                                            $dpAmount = round($quote->total * $dpPercent / 100);
                                        ?>
                                        <?php if($dpAmount > 0): ?>
                                            <tr style="border-top:1px solid #eeeeff;">
                                                <td style="padding:6px 16px 6px 14px; color:#555;"><span class="i18n" data-en="DP ALREADY PAID (<?php echo e($dpPercent); ?>%)">DP TELAH DIBAYAR (<?php echo e($dpPercent); ?>%)</span></td>
                                                <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#dc3545;">Rp <?php echo e(number_format($dpAmount, 0, '', '.')); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php
                                        $billingType = in_array($invoice->type, ['DP', 'Down Payment']) ? 'DP' : (in_array($invoice->type, ['BP', 'Balance Payment']) ? 'BP' : strtoupper($invoice->type));
                                        $billingLabelId = 'TAGIHAN ' . $billingType . ' (' . floatval($invoice->percent) . '%)';
                                        $billingLabelEn = 'AMOUNT DUE - ' . $billingType . ' (' . floatval($invoice->percent) . '%)';
                                    ?>
                                    <tr style="border-top:2px solid #e6c300; background:yellow;">
                                        <td style="padding:8px 16px 8px 14px; font-weight:800; font-size:12.5px; color:#000;">
                                            <span class="i18n" data-en="<?php echo e($billingLabelEn); ?>"><?php echo e($billingLabelId); ?></span>
                                        </td>
                                        <td style="padding:8px 14px 8px 0; text-align:right; font-weight:800; font-size:13px; color:#000;">Rp <?php echo e(number_format($totalAfterPph, 0, '', '.')); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    
                    <div class="p-3 rounded-0 mb-4" style="background:#f0f2ff; border: 1px dashed #696cff; border-radius:0 !important;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-cash-multiple text-primary fs-5"></i>
                            <span class="fw-bold text-primary" style="font-size:12px;"><span class="i18n" data-en="In Words :">Terbilang :</span></span>
                            <span class="fw-bold text-dark i18n" style="font-size:12.5px;" data-en="# <?php echo e($terbilangEn); ?> Rupiah"># <?php echo e($terbilang); ?> Rupiah</span>
                        </div>
                    </div>

                    
                    <div class="row pt-2 align-items-end">
                        <div class="col-md-7">
                            <div class="p-3 rounded-0 border" style="background:#fafafa; font-size:11.5px; border-radius:0 !important;">
                                <p class="fw-bold mb-2 text-dark" style="font-size:12px;">
                                    <i class="mdi mdi-bank-outline me-1 text-primary"></i><span class="i18n" data-en="Payment : Bank Transfer / Giro">Pembayaran : Transfer / Giro</span>
                                </p>
                                <table style="width:100%; border-collapse:collapse;">
                                    <?php if($quote->tax): ?>
                                        <tr>
                                            <td style="padding:2px 0; color:#555; width:90px;">Bank Name</td>
                                            <td style="padding:2px 0; font-weight:600; color:#111;">: Bank BCA (IDR)</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:2px 0; color:#555;">Acc Name</td>
                                            <td style="padding:2px 0; font-weight:700; color:#696cff;">: PT. REFTECH JAYA OPTIMA</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:2px 0; color:#555;">Acc No.</td>
                                            <td style="padding:2px 0; font-weight:700; color:#111;">: 008 - 6289 - 789</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:2px 0; color:#555;">Swift Code</td>
                                            <td style="padding:2px 0; font-weight:500; color:#333;">: CENAIDJA</td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td style="padding:2px 0; color:#555; width:90px;">Bank Name</td>
                                            <td style="padding:2px 0; font-weight:600; color:#111;">: Bank BCA (IDR)</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:2px 0; color:#555;">Acc Name</td>
                                            <td style="padding:2px 0; font-weight:700; color:#696cff;">: ARIEP RACHMAN</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:2px 0; color:#555;">Acc No.</td>
                                            <td style="padding:2px 0; font-weight:700; color:#111;">: 166 - 2242 - 271</td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-5 text-center mt-3 mt-md-0">
                            <?php
                                $signDateBase = $invoice->date ? \Carbon\Carbon::parse($invoice->date) : \Carbon\Carbon::now();
                                $signDateId   = $signDateBase->copy()->locale('id')->translatedFormat('d F Y');
                                $signDateEn   = $signDateBase->copy()->locale('en')->translatedFormat('d F Y');
                            ?>
                            <p class="mb-1 text-muted" style="font-size:11.5px;">Bandung, <span class="i18n" data-en="<?php echo e($signDateEn); ?>"><?php echo e($signDateId); ?></span></p>
                            <?php if($quote->tax): ?>
                                <p class="fw-bold mb-1 text-dark" style="font-size:12px;">PT. Reftech Jaya Optima</p>
                            <?php endif; ?>
                            <?php if(isset($invoice->sign)): ?>
                                <div class="my-2">
                                    <img src="<?php echo e(url('') . '/' . $invoice->sign); ?>" alt="Signature" height="70">
                                </div>
                            <?php else: ?>
                                <div style="padding: 30px 0;"></div>
                            <?php endif; ?>
                            <p class="mb-0 fw-bold text-dark" style="font-size:13px; border-bottom:1px solid #ddd; display:inline-block; padding-bottom:2px;">Ariep Rachman</p>
                            <p class="mb-0 text-muted" style="font-size:11px;">Director</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        

        
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">

            
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body d-grid gap-2 p-3">
                    <div class="btn-group w-100">
                        <a href="<?php echo e(route('invoice.show_unit.print', $invoice->id)); ?>" target="_blank"
                           class="btn btn-primary waves-effect fw-medium invoice-print-link">
                            <i class="mdi mdi-printer-outline me-1"></i> Print / Download
                        </a>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item invoice-print-link" href="<?php echo e(route('invoice.show_unit.print', $invoice->id)); ?>" target="_blank">
                                    <i class="mdi mdi-file-document-outline me-1"></i> Invoice Print
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo e(route('invoice.unit.label_detail', $invoice->id)); ?>">
                                    <i class="mdi mdi-package-variant-closed me-1"></i> Label Sampul
                                </a>
                            </li>
                        </ul>
                    </div>

                    <?php if(Auth::user()->role !== 'Sales'): ?>
                    <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                        <label class="form-check-label text-dark small mb-0 fw-medium" for="toggle-spec">
                            <i class="mdi mdi-text-box-search-outline me-1 text-primary"></i>Tampilkan Spek
                        </label>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="toggle-spec"
                                data-id="<?php echo e($invoice->id); ?>"
                                <?php echo e($invoice->show_spec ? 'checked' : ''); ?>>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <button class="btn btn-label-secondary flex-grow-1 waves-effect" id="backButton">
                            <i class="mdi mdi-arrow-left me-1"></i>Back
                        </button>
                        <a href="<?php echo e(route('unit-quotation.show', $quote->id)); ?>"
                           class="btn btn-label-info flex-grow-1 waves-effect">
                            <i class="mdi mdi-file-eye-outline me-1"></i>Quotation
                        </a>
                    </div>
                </div>
            </div>

            
            <?php if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting'): ?>
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header py-2 px-3 bg-light border-bottom">
                        <small class="text-uppercase text-muted fw-bold" style="font-size:10px; letter-spacing:0.5px;">Invoice Settings</small>
                    </div>
                    <div class="card-body d-grid gap-2 p-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 waves-effect text-start"
                            data-bs-toggle="modal" data-bs-target="#changeDate">
                            <i class="mdi mdi-calendar-edit me-1 text-primary"></i> Change Date
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 waves-effect text-start"
                            data-bs-toggle="modal" data-bs-target="#editInvoiceModal">
                            <i class="mdi mdi-pencil-outline me-1 text-primary"></i> Edit No Invoice / Term
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm w-100 waves-effect text-start"
                            data-bs-toggle="modal" data-bs-target="#dueDate">
                            <i class="mdi mdi-calendar-clock me-1 text-warning"></i> Set / Edit Due Date
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-header py-2 px-3 bg-light border-bottom">
                    <small class="text-uppercase text-muted fw-bold" style="font-size:10px; letter-spacing:0.5px;">Invoice Information</small>
                </div>
                <div class="card-body d-grid gap-2 p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">No. Invoice</span>
                        <span class="fw-bold small text-primary">#<?php echo e($invoice->no_invoice); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Tanggal</span>
                        <span class="fw-semibold small"><?php echo e($invoice->date ? \Carbon\Carbon::parse($invoice->date)->format('d M Y') : '-'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Jatuh Tempo</span>
                        <?php if($dueDateDisplay): ?>
                            <span class="fw-bold small text-warning"><i class="mdi mdi-calendar-clock me-1"></i><?php echo e($dueDateDisplay->format('d M Y')); ?></span>
                        <?php else: ?>
                            <span class="badge bg-label-secondary" style="font-size:10px;">Belum Di-set</span>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">No. PO</span>
                        <span class="fw-semibold small"><?php echo e($quote->po_number ?? '-'); ?></span>
                    </div>
                    <?php if($quote->po_file): ?>
                        <a href="<?php echo e(Storage::url($quote->po_file)); ?>" target="_blank"
                           class="btn btn-outline-success btn-sm w-100 waves-effect">
                            <i class="mdi mdi-file-pdf-box me-1"></i> Lihat File PO
                        </a>
                    <?php endif; ?>
                    <?php if($allInvoices->count() > 1): ?>
                        <hr class="my-1">
                        <?php $__currentLoopData = $allInvoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('invoice.show_unit', $inv->id)); ?>"
                               class="btn btn-sm <?php echo e($inv->id == $invoice->id ? 'btn-primary' : 'btn-outline-secondary'); ?> w-100 waves-effect">
                                <span class="badge <?php echo e($inv->type === 'DP' ? 'bg-warning' : 'bg-info'); ?> me-1"><?php echo e($inv->type); ?></span>
                                <?php echo e($inv->no_invoice ?? 'Pending'); ?>

                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>

            
            <?php if(Auth::user()->role != 'Sales'): ?>
            <div class="card mb-3">
                <div class="card-header py-2 px-3">
                    <small class="text-uppercase text-muted fw-semibold">Tax / PPH</small>
                </div>
                <div class="card-body d-grid gap-2">
                    <?php $pphPerItem = $quote->details->sum(fn($d) => ($d->amount * $d->pph) / 100); ?>
                    <?php if($pphPerItem > 0): ?>
                        <a href="#" class="btn btn-danger w-100 waves-effect delete-pph-unit"
                           data-id="<?php echo e($invoice->id); ?>">Delete PPH 23</a>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline-info w-100 waves-effect"
                            data-bs-toggle="modal" data-bs-target="#modalAddPph">Input PPH 23</button>
                    <?php endif; ?>
                    <?php if(($invoice->pph ?? 0) > 0): ?>
                        <a href="#" class="btn btn-danger w-100 waves-effect delete-pph-manual-unit"
                           data-id="<?php echo e($invoice->id); ?>">Delete PPH Manual</a>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline-secondary w-100 waves-effect"
                            data-bs-toggle="modal" data-bs-target="#modalAddPphManual">Input PPH Manual</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting'): ?>
                <div class="card mb-3">
                    <div class="card-header py-2 px-3">
                        <small class="text-uppercase text-muted fw-semibold">Hand Sign</small>
                    </div>
                    <div class="card-body">
                        <?php if(isset($invoice->sign)): ?>
                            <a href="#" class="btn btn-danger w-100 waves-effect delete-hand-sign-unit"
                               data-id="<?php echo e($invoice->id); ?>">Delete Hand Sign</a>
                        <?php else: ?>
                            <a href="#" class="btn btn-outline-secondary w-100 waves-effect input-hand-sign-unit"
                               data-id="<?php echo e($invoice->id); ?>">Input Hand Sign</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            
            <div class="card mb-3">
                <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between">
                    <small class="text-uppercase text-muted fw-semibold">Payment</small>
                    <?php if($payments->isNotEmpty()): ?>
                        <span class="badge bg-label-success small">Rp <?php echo e(number_format($payments->sum('amount'), 0, '', '.')); ?></span>
                    <?php endif; ?>
                </div>

                
                <div class="card-body p-0">
                    <?php $__currentLoopData = $allInvoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $invTotal = $quote->total;
                            if ($inv->type === 'DP' && $inv->term) {
                                $pct      = floatval(filter_var($inv->term, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
                                $invTotal = round($quote->total * $pct / 100);
                            } elseif ($inv->type === 'BP') {
                                $dpInv    = $allInvoices->firstWhere('type', 'DP');
                                $pct      = $dpInv?->term ? floatval(filter_var($dpInv->term, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)) : 0;
                                $invTotal = $quote->total - round($quote->total * $pct / 100);
                            }
                        ?>
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                            <div>
                                <p class="mb-0 small fw-semibold">
                                    <span class="badge <?php echo e($inv->type === 'DP' ? 'bg-warning' : ($inv->type === 'BP' ? 'bg-info' : 'bg-primary')); ?> me-1" style="font-size:10px"><?php echo e($inv->type); ?></span>
                                    Rp <?php echo e(number_format($invTotal, 0, '', '.')); ?>

                                </p>
                                <p class="mb-0 text-muted" style="font-size:11px"><?php echo e($inv->no_invoice ?? 'Belum diterbitkan'); ?></p>
                            </div>
                            <?php if($inv->status_p): ?>
                                <span class="badge bg-label-success" style="font-size:10px">Verified</span>
                            <?php else: ?>
                                <span class="badge bg-label-warning" style="font-size:10px">Unpaid</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
                <?php if($payments->isNotEmpty()): ?>
                <div class="border-top">
                    <div class="px-3 pt-2 pb-1">
                        <small class="text-uppercase text-muted fw-semibold" style="font-size:10px">Payment Received</small>
                    </div>
                    <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex align-items-start justify-content-between px-3 py-2 border-bottom" id="pay-row-<?php echo e($pay->id); ?>">
                        <div>
                            <p class="mb-0 fw-semibold small">
                                Rp <?php echo e(number_format($pay->amount, 0, '', '.')); ?>

                                <?php if($pay->type): ?>
                                    <span class="badge bg-label-primary ms-1" style="font-size:10px"><?php echo e($pay->type); ?></span>
                                <?php endif; ?>
                            </p>
                            <?php if($pay->method): ?>
                                <p class="mb-0 text-muted" style="font-size:11px"><?php echo e($pay->method); ?></p>
                            <?php endif; ?>
                            <?php if($pay->note): ?>
                                <p class="mb-0 text-muted" style="font-size:11px"><?php echo e($pay->note); ?></p>
                            <?php endif; ?>
                            <div class="mt-1 d-flex flex-wrap gap-1">
                                <?php if($pay->file): ?>
                                    <a href="<?php echo e(asset($pay->file)); ?>" target="_blank"
                                       class="badge bg-label-success text-decoration-none" style="font-size:10px">
                                        <i class="mdi mdi-file-check-outline"></i> Bukti Transfer
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-label-warning" style="font-size:10px">Belum ada bukti</span>
                                <?php endif; ?>
                                <?php if($pay->level == 1): ?>
                                    <span class="badge bg-label-success" style="font-size:10px">
                                        <i class="mdi mdi-check-circle-outline"></i> Paid
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="d-flex gap-1 ms-2">
                        <?php if(!$pay->file && Auth::user()->role === 'Sales'): ?>
                            <button type="button" class="btn btn-sm btn-icon btn-outline-success btn-upload-proof-inv"
                                data-id="<?php echo e($pay->id); ?>" title="Upload Bukti">
                                <i class="mdi mdi-upload"></i>
                            </button>
                        <?php endif; ?>
                        <?php if($pay->file && $pay->level == 0 && Auth::user()->role === 'Sales'): ?>
                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete-proof"
                                data-id="<?php echo e($pay->id); ?>" title="Hapus Bukti Transfer">
                                <i class="mdi mdi-file-remove-outline"></i>
                            </button>
                        <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>

                
                <div class="card-footer p-3 d-grid gap-2">
                    <?php if($quote->status === 'po_received' && Auth::user()->role === 'Sales'): ?>
                        <button type="button" class="btn btn-outline-success w-100 waves-effect"
                            data-bs-toggle="modal" data-bs-target="#modalAddPayment">
                            <i class="mdi mdi-cash-plus me-1"></i> Tambah Payment
                        </button>
                    <?php endif; ?>
                    <?php if(in_array(Auth::user()->role, ['Admin', 'Accounting', 'Finance'])): ?>
                        <?php if(!$invoice->status_p): ?>
                            <?php if($payments->isNotEmpty()): ?>
                                <button type="button" class="btn btn-primary w-100 waves-effect"
                                    data-bs-toggle="modal" data-bs-target="#confirmPayment">Confirm Payment</button>
                            <?php else: ?>
                                <div class="alert alert-warning p-2 mb-0" style="font-size:11px; border-radius:0 !important;">
                                    <i class="mdi mdi-alert-circle-outline me-1"></i> Menunggu Sales menambahkan data Payment.
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="#" class="btn btn-danger w-100 waves-effect undo-payment-unit"
                               data-id="<?php echo e($invoice->id); ?>">Undo Confirm Payment</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>


        </div>
    </div>
    </div> 

    
    <div class="tab-pane fade" id="tab-delivery" role="tabpanel">
        <div class="row">
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">

                
                <?php if(isset($pendingPO) && $pendingPO): ?>
                    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid #e2e8f0 !important; border-radius: 12px; background: #ffffff;">
                        <div class="card-body p-4">
                            
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-3 border-bottom" style="border-color: #f1f5f9 !important;">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="background: #f1f5f9; color: #475569; width: 38px; height: 38px;">
                                        <i class="mdi mdi-truck-fast-outline fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-0.5">
                                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 14.5px;">Instruksi Pengiriman & Alamat</h6>
                                            <span class="badge bg-label-primary font-monospace" style="font-size: 10.5px; padding: 2px 7px;"><?php echo e($pendingPO->no_pending ?: '#' . $pendingPO->id); ?></span>
                                        </div>
                                        <small class="text-muted" style="font-size: 11.5px;">Instruksi pengiriman dari Sales Order</small>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <?php if($pendingPO->combine_shipping_and_parts): ?>
                                        <span class="badge rounded-pill bg-label-success px-3 py-1.5 fw-semibold" style="font-size: 11px; border: 1px solid #bbf7d0;">
                                            <i class="mdi mdi-link-variant me-1"></i> Barang & Part Digabung
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-label-danger px-3 py-1.5 fw-semibold" style="font-size: 11px; border: 1px solid #fecaca;">
                                            <i class="mdi mdi-link-variant-off me-1"></i> Barang & Part Dipisah
                                        </span>
                                    <?php endif; ?>

                                    <?php if($pendingPO->ekspidisi): ?>
                                        <span class="badge rounded-pill bg-label-info px-3 py-1.5 fw-semibold" style="font-size: 11px; border: 1px solid #bae6fd;">
                                            <i class="mdi mdi-truck-outline me-1"></i> Ekspedisi: <?php echo e($pendingPO->ekspidisi); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            
                            <div class="row g-3" style="font-size: 12px;">
                                
                                <div class="col-md-6">
                                    <div class="p-3.5 rounded-3 h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom" style="border-color: #e2e8f0 !important;">
                                            <span class="fw-bold text-dark text-uppercase small" style="font-size: 11px; letter-spacing: 0.4px;">
                                                <i class="mdi mdi-map-marker-outline text-danger me-1 fs-6"></i> Alamat Pengiriman Barang / Unit
                                            </span>
                                            <?php if(($pendingPO->shipping_address_type ?? 'customer') === 'customer'): ?>
                                                <span class="badge bg-white text-muted border" style="font-size: 9.5px; font-weight: 500;">Sesuai Customer</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark" style="font-size: 9.5px; font-weight: 600;">Manual</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="mb-2 text-dark fw-medium" style="line-height: 1.5; font-size: 12.5px;">
                                            <?php if(($pendingPO->shipping_address_type ?? 'customer') === 'customer'): ?>
                                                <?php echo e($quote->client->address ?? '-'); ?>

                                            <?php else: ?>
                                                <?php echo e($pendingPO->shipping_address_manual ?: ($quote->client->address ?? '-')); ?>

                                            <?php endif; ?>
                                        </p>
                                        <?php if($pendingPO->shipping_recipient): ?>
                                            <div class="pt-2 mt-2 border-top text-muted d-flex align-items-center justify-content-between" style="border-color: #e2e8f0 !important; font-size: 11.5px;">
                                                <span><i class="mdi mdi-account-outline me-1 text-primary"></i>Penerima: <strong class="text-dark"><?php echo e($pendingPO->shipping_recipient->name_pic); ?></strong></span>
                                                <?php if($pendingPO->shipping_recipient->phone): ?>
                                                    <span class="text-primary fw-semibold"><i class="mdi mdi-phone-outline me-1"></i><?php echo e($pendingPO->shipping_recipient->phone); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="col-md-6">
                                    <div class="p-3.5 rounded-3 h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom" style="border-color: #e2e8f0 !important;">
                                            <span class="fw-bold text-dark text-uppercase small" style="font-size: 11px; letter-spacing: 0.4px;">
                                                <i class="mdi mdi-file-document-outline text-primary me-1 fs-6"></i> Alamat Pengiriman Dokumen
                                            </span>
                                            <?php if(($pendingPO->doc_address_type ?? 'customer') === 'customer'): ?>
                                                <span class="badge bg-white text-muted border" style="font-size: 9.5px; font-weight: 500;">Sesuai Customer</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark" style="font-size: 9.5px; font-weight: 600;">Manual</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="mb-2 text-dark fw-medium" style="line-height: 1.5; font-size: 12.5px;">
                                            <?php if(($pendingPO->doc_address_type ?? 'customer') === 'customer'): ?>
                                                <?php echo e($quote->client->address ?? '-'); ?>

                                            <?php else: ?>
                                                <?php echo e($pendingPO->doc_address_manual ?: ($quote->client->address ?? '-')); ?>

                                            <?php endif; ?>
                                        </p>
                                        <?php if($pendingPO->doc_recipient): ?>
                                            <div class="pt-2 mt-2 border-top text-muted d-flex align-items-center justify-content-between" style="border-color: #e2e8f0 !important; font-size: 11.5px;">
                                                <span><i class="mdi mdi-account-outline me-1 text-primary"></i>Penerima: <strong class="text-dark"><?php echo e($pendingPO->doc_recipient->name_pic); ?></strong></span>
                                                <?php if($pendingPO->doc_recipient->phone): ?>
                                                    <span class="text-primary fw-semibold"><i class="mdi mdi-phone-outline me-1"></i><?php echo e($pendingPO->doc_recipient->phone); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="mdi mdi-chart-box-outline me-2 text-primary fs-5"></i> Progress Pengiriman Item (Shipment Tracker)
                        </h6>
                        <?php
                            $totalOrdered = $quote->details->where('type', '!=', 'header')->sum('qty');
                            $totalRemaining = $quote->details->where('type', '!=', 'header')->sum('remaining_qty');
                            $totalDelivered = max(0, $totalOrdered - $totalRemaining);
                            $percentDelivered = $totalOrdered > 0 ? round(($totalDelivered / $totalOrdered) * 100) : 0;
                        ?>
                        <span class="badge <?php echo e($totalRemaining == 0 ? 'bg-success' : ($totalDelivered > 0 ? 'bg-warning' : 'bg-secondary')); ?> py-1.5 px-3 fs-7">
                            <?php echo e($totalRemaining == 0 ? 'Terkirim Semua (100%)' : ($totalDelivered > 0 ? "Terkirim Parsial ({$percentDelivered}%)" : 'Belum Ada Pengiriman')); ?>

                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size:12px;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width:5%;">No</th>
                                        <th>Deskripsi Barang / Sparepart</th>
                                        <th class="text-center" style="width:12%;">Qty Pesan</th>
                                        <th class="text-center" style="width:12%;">Terkirim</th>
                                        <th class="text-center" style="width:12%;">Sisa Qty</th>
                                        <th class="text-center" style="width:18%;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $itemNo = 1; ?>
                                    <?php $__currentLoopData = $quote->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($item->type === 'header'): ?>
                                            <tr style="background:#f4f4fe;">
                                                <td colspan="6" class="fw-bold text-uppercase py-2 px-3 text-primary" style="font-size:11px; letter-spacing:0.5px;">
                                                    <i class="mdi mdi-bookmark-outline me-1"></i> <?php echo e($item->label); ?>

                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php
                                                if ($item->id_equivalent && $item->equivalent) {
                                                    $spParts = array_filter([
                                                        $item->equivalent->brand ?? '',
                                                        $item->equivalent->pn ?? '',
                                                        $item->label ?: optional($item->equivalent->product)->description ?: $item->description
                                                    ]);
                                                    $itemDescStr = implode(' — ', $spParts);
                                                } elseif ($item->type === 'unit' && $item->unit) {
                                                    $itemDescStr = $item->label ?: trim($item->unit->brand . ' ' . $item->unit->sku . ($item->unit->model ? ' — ' . $item->unit->model : ''));
                                                } else {
                                                    $itemDescStr = $item->label ?: $item->description;
                                                }
                                                $deliveredQty = max(0, $item->qty - $item->remaining_qty);
                                            ?>
                                            <tr>
                                                <td class="text-center fw-semibold"><?php echo e($itemNo++); ?></td>
                                                <td class="fw-semibold text-dark"><?php echo e($itemDescStr); ?></td>
                                                <td class="text-center fw-bold"><?php echo e((float)$item->qty); ?> <?php echo e($item->info_qty); ?></td>
                                                <td class="text-center text-success fw-bold"><?php echo e((float)$deliveredQty); ?> <?php echo e($item->info_qty); ?></td>
                                                <td class="text-center <?php echo e($item->remaining_qty > 0 ? 'text-danger' : 'text-muted'); ?> fw-bold"><?php echo e((float)$item->remaining_qty); ?> <?php echo e($item->info_qty); ?></td>
                                                <td class="text-center">
                                                    <?php if($item->remaining_qty == 0): ?>
                                                        <span class="badge bg-label-success"><i class="mdi mdi-check-circle-outline me-1"></i>Terkirim Semua</span>
                                                    <?php elseif($deliveredQty > 0): ?>
                                                        <span class="badge bg-label-warning"><i class="mdi mdi-clock-outline me-1"></i>Sisa <?php echo e((float)$item->remaining_qty); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-label-secondary"><i class="mdi mdi-truck-outline me-1"></i>Belum Dikirim</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="mdi mdi-history me-2 text-primary fs-5"></i> Riwayat Surat Jalan Terbuat (<?php echo e($quote->deliveries->count()); ?>)
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <?php if($quote->deliveries->isEmpty()): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="mdi mdi-truck-delivery-outline fs-1 text-light d-block mb-2"></i>
                                <p class="mb-1 fw-semibold">Belum Ada Surat Jalan Terbuat</p>
                                <p class="small text-muted mb-0">Gunakan tombol <strong>"Buat Surat Jalan Baru"</strong> pada menu sebelah kanan untuk menerbitkan Surat Jalan.</p>
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php $__currentLoopData = $quote->deliveries->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $del): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="border rounded p-3 bg-white hover-shadow transition-all">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <h6 class="fw-bold mb-0 text-primary">Surat Jalan #<?php echo e($del->id); ?></h6>
                                                    <span class="badge <?php echo e(strtolower($del->type) === 'ekspedisi' ? 'bg-label-info' : 'bg-label-primary'); ?>">
                                                        <i class="mdi <?php echo e(strtolower($del->type) === 'ekspedisi' ? 'mdi-package-variant-closed' : 'mdi-account-hard-hat'); ?> me-1"></i>
                                                        <?php echo e(ucfirst($del->type ?? 'Ekspedisi')); ?>

                                                    </span>
                                                </div>
                                                <p class="mb-0 text-muted small">
                                                    <i class="mdi mdi-calendar-outline me-1"></i>Tanggal: <?php echo e(\Carbon\Carbon::parse($del->date)->format('d F Y')); ?>

                                                    &nbsp;|&nbsp;
                                                    <i class="mdi mdi-map-marker-outline me-1"></i>Alamat: <?php echo e($quote->client ? ($del->destination == '1' ? $quote->client->address : $quote->client->subAddress) : '-'); ?>

                                                </p>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2.5" data-bs-toggle="modal" data-bs-target="#modal-delivery-preview-<?php echo e($del->id); ?>">
                                                    <i class="mdi mdi-eye-outline me-1"></i> Detail
                                                </button>
                                                <a href="<?php echo e(route('print.delivery', $del->id)); ?>" target="_blank" class="btn btn-sm btn-primary py-1 px-2.5">
                                                    <i class="mdi mdi-printer-outline me-1"></i> Cetak SJ
                                                </a>
                                                <?php if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting'): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 delete-delivery" data-id="<?php echo e($del->id); ?>" title="Hapus Surat Jalan">
                                                        <i class="mdi mdi-delete-outline"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if($del->detail && $del->detail->isNotEmpty()): ?>
                                            <?php
                                                $itemCountInDel = $del->detail->where('type', '!=', 'header')->count();
                                                $totalQtyInDel = $del->detail->where('type', '!=', 'header')->sum('qty');
                                            ?>
                                            <div class="border-top pt-2 mt-2 d-flex align-items-center justify-content-between text-muted small" style="font-size:11.5px;">
                                                <span>
                                                    <i class="mdi mdi-cube-outline me-1 text-primary"></i>Total Item Dikirim: <strong class="text-dark"><?php echo e($itemCountInDel); ?> Jenis Barang</strong> (Total Qty: <?php echo e((float)$totalQtyInDel); ?>)
                                                </span>
                                                <button type="button" class="btn btn-xs btn-label-primary py-0.5 px-2 rounded" data-bs-toggle="modal" data-bs-target="#modal-delivery-preview-<?php echo e($del->id); ?>">
                                                    <i class="mdi mdi-text-box-search-outline me-1"></i>Lihat Rincian Item
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            
            <div class="col-xl-3 col-md-4 col-12">
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom py-2 px-3">
                        <small class="text-uppercase text-muted fw-bold">Delivery Quick Actions</small>
                    </div>
                    <div class="card-body p-3 d-grid gap-2">
                        <?php if($quote->status === 'po_received' && $totalRemaining > 0 && (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')): ?>
                            <button type="button" class="btn btn-success d-grid w-100 shadow-sm py-2" data-bs-toggle="modal" data-bs-target="#modalSJUnit">
                                <span class="d-flex align-items-center justify-content-center gap-1 fw-bold fs-6">
                                    <i class="mdi mdi-truck-delivery-outline fs-5"></i> Buat Surat Jalan Baru
                                </span>
                            </button>
                        <?php elseif($totalRemaining == 0): ?>
                            <div class="alert alert-success p-2 mb-0 text-center" style="font-size:12px;">
                                <i class="mdi mdi-check-circle fs-5 d-block mb-1"></i>
                                <strong>Pengiriman Selesai</strong><br>Seluruh item telah berhasil dikirim.
                            </div>
                        <?php endif; ?>
                        <a href="<?php echo e(route('invoice.index')); ?>" class="btn btn-outline-secondary w-100 py-2">
                            <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div> 

    
    <?php if(isset($bast) && $bast): ?>
        <div class="tab-pane fade" id="tab-bast" role="tabpanel">
            <div class="row">
                <div class="col-xl-9 col-md-8 col-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light py-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-certificate-outline text-success fs-3"></i>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark">Berita Acara Serah Terima (BAST)</h5>
                                    <small class="text-muted">No. BAST: <strong class="text-primary"><?php echo e($bast->no_bast); ?></strong> &bull; Entitas: <?php echo e($bast->entity); ?></small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('bast.print', $bast->id)); ?>" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="mdi mdi-printer-outline me-1"></i> Cetak BAST
                                </a>
                                <?php if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting'): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-bast" data-id="<?php echo e($bast->id); ?>">
                                        <i class="mdi mdi-delete-outline me-1"></i> Hapus BAST
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-4 pb-3 border-bottom" style="font-size: 13px;">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded border h-100">
                                        <span class="text-uppercase text-muted fw-bold small d-block mb-1">Pelaksana Pekerjaan</span>
                                        <strong class="text-dark fs-6 d-block mb-1">PT <?php echo e($bast->entity == 'Kojisha' ? 'Kojisha' : 'Reftech Jaya Optima'); ?></strong>
                                        <p class="mb-0 text-muted" style="line-height:1.4;">Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded border h-100">
                                        <span class="text-uppercase text-muted fw-bold small d-block mb-1">Penerima Pekerjaan (Customer)</span>
                                        <strong class="text-dark fs-6 d-block mb-1"><?php echo e($bast->customer_name); ?></strong>
                                        <p class="mb-1 text-muted" style="line-height:1.4;"><i class="mdi mdi-clipboard-outline me-1"></i>Pekerjaan: <strong><?php echo e($bast->work_title); ?></strong></p>
                                        <p class="mb-0 text-muted" style="line-height:1.4;"><i class="mdi mdi-calendar-clock-outline me-1"></i>Tanggal Pekerjaan: <?php echo e(\Carbon\Carbon::parse($bast->work_date)->format('d F Y')); ?></p>
                                        <?php if($bast->po_number): ?>
                                            <div class="mt-2 pt-2 border-top">
                                                <span class="fw-semibold">No PO / Ref:</span> <span class="badge bg-label-primary"><?php echo e($bast->po_number); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-2 text-dark"><i class="mdi mdi-format-list-bulleted me-1 text-success"></i> Rincian Unit / Barang Serah Terima</h6>
                            <div class="table-responsive border rounded mb-3">
                                <table class="table table-sm table-striped table-hover m-0" style="font-size: 12.5px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:6%;">No</th>
                                            <th>Nama Unit / Barang</th>
                                            <th>No. Seri / Serial Number</th>
                                            <th class="text-center" style="width:15%;">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($bast->units && $bast->units->isNotEmpty()): ?>
                                            <?php $__currentLoopData = $bast->units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td class="text-center align-middle"><?php echo e($idx + 1); ?></td>
                                                    <td class="align-middle fw-medium text-dark"><?php echo e($u->unit_name); ?></td>
                                                    <td class="align-middle text-muted"><?php echo e($u->serial_no ?: '-'); ?></td>
                                                    <td class="text-center align-middle fw-bold text-success"><?php echo e($u->qty); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-3 text-muted">Tidak ada unit terdaftar.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if($bast->test_running_result): ?>
                                <div class="p-3 bg-light rounded border mt-3">
                                    <h6 class="fw-bold mb-1 text-dark" style="font-size:12.5px;"><i class="mdi mdi-check-decagram-outline me-1 text-success"></i> Hasil Test Running & Catatan Serah Terima</h6>
                                    <p class="mb-0 text-muted" style="white-space: pre-line; font-size:12px;"><?php echo e($bast->test_running_result); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div class="col-xl-3 col-md-4 col-12">
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom py-2 px-3">
                            <small class="text-uppercase text-muted fw-bold">Aksi BAST</small>
                        </div>
                        <div class="card-body p-3 d-grid gap-2">
                            <a href="<?php echo e(route('bast.print', $bast->id)); ?>" target="_blank" class="btn btn-primary d-grid w-100 shadow-sm py-2">
                                <span class="d-flex align-items-center justify-content-center gap-1 fw-bold fs-6">
                                    <i class="mdi mdi-printer-outline fs-5"></i> Cetak BAST
                                </span>
                            </a>
                            <a href="<?php echo e(route('invoice.index')); ?>" class="btn btn-outline-secondary w-100 py-2">
                                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div> 
</div> 

    
    <?php if($quote->deliveries && $quote->deliveries->isNotEmpty()): ?>
        <?php $__currentLoopData = $quote->deliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $del): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="modal fade" id="modal-delivery-preview-<?php echo e($del->id); ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light py-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-truck-delivery-outline text-primary fs-4"></i>
                                <div>
                                    <h5 class="modal-title fw-bold mb-0">Surat Jalan #<?php echo e($del->id); ?></h5>
                                    <small class="text-muted">Jenis: <?php echo e(ucfirst($del->type ?? 'Ekspedisi')); ?> &bull; Tanggal: <?php echo e(\Carbon\Carbon::parse($del->date)->format('d F Y')); ?></small>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            
                            <div class="row g-3 mb-4 pb-3 border-bottom" style="font-size: 12px;">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded border h-100">
                                        <span class="text-uppercase text-muted fw-bold small d-block mb-1">Pengirim (Shipper)</span>
                                        <strong class="text-dark fs-6 d-block mb-1">PT Reftech Jaya Optima</strong>
                                        <p class="mb-0 text-muted" style="line-height:1.4;">Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded border h-100">
                                        <span class="text-uppercase text-muted fw-bold small d-block mb-1">Penerima (Deliver To)</span>
                                        <strong class="text-dark fs-6 d-block mb-1"><?php echo e($quote->client->company ?? '-'); ?></strong>
                                        <p class="mb-0 text-muted" style="line-height:1.4;">
                                            <i class="mdi mdi-map-marker-outline me-1"></i><?php echo e($quote->client ? ($del->destination == '1' ? $quote->client->address : $quote->client->subAddress) : '-'); ?>

                                        </p>
                                        <div class="mt-2 pt-2 border-top">
                                            <span class="fw-semibold">PO / Quote No:</span> <span class="badge bg-label-primary"><?php echo e($quote->po_number ?: $quote->no_quote); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <h6 class="fw-bold mb-2 text-dark"><i class="mdi mdi-format-list-bulleted me-1 text-primary"></i> Daftar Item yang Dikirim</h6>
                            <div class="table-responsive border rounded mb-3">
                                <table class="table table-sm table-striped table-hover m-0" style="font-size: 12px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:6%;">No</th>
                                            <th>Deskripsi Barang</th>
                                            <th class="text-center" style="width:20%;">Qty Dikirim</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $itemNoModal = 1; ?>
                                        <?php if($del->detail && $del->detail->isNotEmpty()): ?>
                                            <?php $__currentLoopData = $del->detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(($dt->type ?? 'item') === 'header'): ?>
                                                    <tr style="background:#f0f0ff;">
                                                        <td colspan="3" class="fw-bold text-uppercase py-1.5 px-3 text-primary" style="font-size:11px;">
                                                            <i class="mdi mdi-bookmark-outline me-1"></i> <?php echo e($dt->desc); ?>

                                                        </td>
                                                    </tr>
                                                <?php else: ?>
                                                    <tr>
                                                        <td class="text-center align-middle"><?php echo e($itemNoModal++); ?></td>
                                                        <td class="align-middle fw-medium text-dark"><?php echo e($dt->desc); ?></td>
                                                        <td class="text-center align-middle fw-bold text-primary"><?php echo e((float)$dt->qty); ?> <?php echo e($dt->info_qty); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center py-3 text-muted">Belum ada detail barang.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                            <a href="<?php echo e(route('print.delivery', $del->id)); ?>" target="_blank" class="btn btn-primary">
                                <i class="mdi mdi-printer-outline me-1"></i> Cetak Surat Jalan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    
    <?php if(($quote->status === 'po_received') && (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')): ?>
        <div class="modal fade" id="modalSJUnit" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form action="<?php echo e(route('unit-quotation.storeDelivery', $quote->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id_invoice" value="<?php echo e($invoice->id); ?>">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Buat Surat Jalan — <?php echo e($quote->no_quote); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" name="date"
                                        value="<?php echo e(\Carbon\Carbon::today()->toDateString()); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tujuan / Alamat</label>
                                    <select class="form-select" name="destination" required>
                                        <?php if($quote->client): ?>
                                            <option value="1"><?php echo e($quote->client->address); ?></option>
                                            <?php if($quote->client->subAddress): ?>
                                                <option value="2"><?php echo e($quote->client->subAddress); ?></option>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jenis Pengiriman</label>
                                    <select class="form-select" name="type">
                                        <option value="Ekspedisi">Ekspedisi</option>
                                        <option value="Teknisi">Teknisi</option>
                                    </select>
                                </div>
                            </div>

                            <label class="form-label fw-semibold">Item yang Dikirim</label>
                            <p class="text-muted mb-2" style="font-size:11.5px;">
                                Centang item yang dikirim kali ini. Qty default = sisa yang belum terkirim, bisa dikurangi kalau cuma kirim sebagian.
                            </p>
                            <div class="table-responsive border rounded">
                                <table class="table table-sm table-bordered m-0" style="font-size:12px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:5%"></th>
                                            <th>Description</th>
                                            <th class="text-center" style="width:15%">Sisa</th>
                                            <th class="text-center" style="width:20%">Qty Dikirim</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $quote->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                if ($item->id_equivalent && $item->equivalent) {
                                                    $spParts = array_filter([
                                                        $item->equivalent->brand ?? '',
                                                        $item->equivalent->pn ?? '',
                                                        $item->label ?: optional($item->equivalent->product)->description ?: $item->description
                                                    ]);
                                                    $itemDisplayLabel = implode(' — ', $spParts);
                                                } elseif ($item->type === 'unit' && $item->unit) {
                                                    $itemDisplayLabel = $item->label ?: trim($item->unit->brand . ' ' . $item->unit->sku . ($item->unit->model ? ' — ' . $item->unit->model : ''));
                                                } else {
                                                    $itemDisplayLabel = $item->label ?: $item->description;
                                                }
                                            ?>
                                            <?php if($item->type === 'header'): ?>
                                                <tr style="background:#f0f0ff;">
                                                    <td colspan="4" class="fw-bold text-uppercase py-1 px-2 text-primary" style="font-size:11px;"><?php echo e($item->label); ?></td>
                                                </tr>
                                            <?php elseif($item->remaining_qty > 0): ?>
                                                <tr>
                                                    <td class="text-center align-middle">
                                                        <input class="form-check-input item-check" type="checkbox" name="item_ids[]"
                                                            value="<?php echo e($item->id); ?>" data-target="qty-<?php echo e($item->id); ?>" checked>
                                                    </td>
                                                    <td class="align-middle fw-medium"><?php echo e($itemDisplayLabel); ?></td>
                                                    <td class="text-center align-middle"><?php echo e($item->remaining_qty); ?> <?php echo e($item->info_qty); ?></td>
                                                    <td class="align-middle">
                                                        <input type="number" step="any" min="0" max="<?php echo e($item->remaining_qty); ?>"
                                                            value="<?php echo e($item->remaining_qty); ?>" name="qty[<?php echo e($item->id); ?>]"
                                                            id="qty-<?php echo e($item->id); ?>" class="form-control form-control-sm">
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <tr class="text-muted">
                                                    <td class="text-center align-middle">
                                                        <input type="checkbox" class="form-check-input" disabled>
                                                    </td>
                                                    <td class="align-middle text-decoration-line-through"><?php echo e($itemDisplayLabel); ?></td>
                                                    <td class="text-center align-middle">0</td>
                                                    <td class="align-middle"><span class="badge bg-label-success">Terkirim Semua</span></td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Buat Surat Jalan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="modal fade" id="modalAddPph" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="<?php echo e(route('invoice.unit.pph', $invoice->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Add PPH 23 — <?php echo e($invoice->no_invoice); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <?php $__currentLoopData = $quote->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="row g-2 mb-3 align-items-center">
                                <div class="col-8">
                                    <p class="mb-0 fw-medium" style="font-size: 13px">
                                        <?php if($detail->type === 'unit' && $detail->unit): ?>
                                            <?php echo e($detail->label ?: ($detail->unit->brand . ' ' . $detail->unit->model)); ?>

                                        <?php else: ?>
                                            <?php echo e($detail->label); ?>

                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-4">
                                    <div class="input-group input-group-merge">
                                        <input type="number" class="form-control" name="pph[<?php echo e($i); ?>]"
                                               value="<?php echo e($detail->pph); ?>" placeholder="2" min="0" max="100" step="0.1">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal-onboarding modal fade animate__animated" id="modalAddPphManual" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <form action="<?php echo e(route('invoice.unit.pph_manual', $invoice->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="onboarding-content mb-0">
                            <h4 class="onboarding-title text-body">Add PPH Manual</h4>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <div class="input-group">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="text" class="form-control invoice-item-pph-manual-label"
                                                id="pphManualLabel" name="pphLabel" placeholder="Put PPH Here"
                                                data-type="currency" value="<?php echo e(old('pph')); ?>">
                                            <input class="form-control invoice-item-pph-manual" type="number"
                                                name="pph" id="pphManual" value="<?php echo e(old('pph')); ?>" hidden>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="confirmPayment" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="<?php echo e(route('invoice.confirm_payment_unit', $invoice->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Catatan pembayaran..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary waves-effect">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="modalAddPayment" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="mdi mdi-cash-plus me-1"></i> Tambah Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo e(route('unit-quotation.add-payment', $quote->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipe Payment <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" id="inv-add-payment-type" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="DP">DP (Down Payment)</option>
                                <option value="BP">BP (Balance Payment)</option>
                                <option value="CBD">CBD</option>
                                <option value="COD">COD</option>
                                <option value="Tempo">Tempo</option>
                            </select>
                        </div>
                        <div class="mb-3" id="inv-tempo-group" style="display:none">
                            <label class="form-label fw-semibold">Tempo (hari)</label>
                            <input type="number" class="form-control" name="tempo" min="1" placeholder="misal: 30">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Metode <span class="text-danger">*</span></label>
                            <select class="form-select" name="method" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="Transfer">Transfer</option>
                                <option value="Cash">Cash</option>
                                <option value="Giro">Giro</option>
                                <option value="Escrow">Escrow</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="amount" required min="1" placeholder="Masukkan jumlah yang diterima">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Persentase (%)</label>
                            <input type="number" class="form-control" name="percent" min="1" max="100" placeholder="opsional, misal: 50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan</label>
                            <input type="text" class="form-control" name="note" placeholder="opsional">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="modalUploadBukti" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Bukti Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formUploadBuktiInv" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <input type="file" class="form-control" name="file" accept="image/*,.pdf" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <?php if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting'): ?>
        <div class="modal fade" id="editInvoiceModal" tabindex="-1" aria-labelledby="editInvoiceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editInvoiceModalLabel">Edit No Invoice & Term of Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="<?php echo e(route('invoice.update', $invoice->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="invoiceNumber" class="form-label">No Invoice</label>
                                <input type="text" class="form-control" id="invoiceNumber" name="invoice" value="<?php echo e(old('invoice', $invoice->no_invoice)); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="termPayment" class="form-label">Term of Payment</label>
                                <textarea class="form-control" id="termPayment" name="payment" rows="4" required><?php echo e(old('payment', $invoice->term ?? $quote->payment_method)); ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php echo $__env->make('components.modal.invoice.date', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('components.modal.invoice.due-date', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
<script>
    // Invoice language toggle (ID / EN) — swaps label text within the invoice card,
    // and carries the chosen language over to the Print/Download links via ?lang=
    function setInvoicePrintLinkLang(lang) {
        $('.invoice-print-link').each(function () {
            var url = new URL($(this).attr('href'), window.location.origin);
            url.searchParams.set('lang', lang);
            $(this).attr('href', url.toString());
        });
    }

    $(document).on('click', '.invoice-lang-btn', function () {
        var lang = $(this).data('lang');

        $('.invoice-lang-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('active btn-primary');

        $('.invoice-preview-card .i18n').each(function () {
            var $el = $(this);
            if ($el.data('idText') === undefined) {
                $el.data('idText', $el.text());
            }
            $el.text(lang === 'en' ? ($el.data('en') || $el.data('idText')) : $el.data('idText'));
        });

        setInvoicePrintLinkLang(lang);
    });

    setInvoicePrintLinkLang('id');

    $('#backButton').click(function () { window.history.back(); });

    // Buat Surat Jalan: nonaktifkan qty saat item di-uncheck
    $(document).on('change', '.item-check', function () {
        var $qty = $('#' + $(this).data('target'));
        $qty.prop('disabled', !this.checked);
    });

    // Toggle spesifikasi
    $('#toggle-spec').on('change', function () {
        var id      = $(this).data('id');
        var showing = $(this).is(':checked');

        $.post('/invoice/unit/' + id + '/toggle-spec', { _token: '<?php echo e(csrf_token()); ?>' });

        if (showing) {
            $('.spec-detail-rows').show();
        } else {
            $('.spec-detail-rows').hide();
        }
    });

    // PPH Manual format
    $(".invoice-item-pph-manual-label").on('keyup', function () {
        var nomorInt = parseInt($(this).val().replace(/\./g, ''), 10);
        if (!isNaN(nomorInt)) {
            $(this).val(nomorInt.toLocaleString('id-ID'));
            $("#pphManual").val(nomorInt);
        }
    });

    // Delete PPH 23
    $(document).on('click', '.delete-pph-unit', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus PPH 23?',
            text: 'Semua nilai PPH per item akan di-reset ke 0.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/invoice/unit/' + id + '/pph/delete',
                    type: 'POST',
                    data: { '_method': 'PATCH', '_token': '<?php echo e(csrf_token()); ?>' },
                    success: function () { location.reload(); },
                });
            }
        });
    });

    // Delete PPH Manual
    $(document).on('click', '.delete-pph-manual-unit', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus PPH Manual?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/invoice/unit/' + id + '/pph-manual/delete',
                    type: 'POST',
                    data: { '_method': 'PATCH', '_token': '<?php echo e(csrf_token()); ?>' },
                    success: function () { location.reload(); },
                });
            }
        });
    });

    // Undo confirm payment
    $(document).on('click', '.undo-payment-unit', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $.ajax({
            url: '/invoice/unit/' + id + '/payment/undo',
            type: 'POST',
            data: { '_method': 'PATCH', '_token': '<?php echo e(csrf_token()); ?>' },
            success: function () { location.reload(); },
        });
    });

    // Input Hand Sign
    $(document).on('click', '.input-hand-sign-unit', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Input Hand Sign?',
            text: 'Tanda tangan akan otomatis ditambahkan ke invoice.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, input!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/invoice/unit/' + id + '/sign',
                    type: 'POST',
                    data: { '_token': '<?php echo e(csrf_token()); ?>' },
                    success: function (response) {
                        if (response == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Hand sign berhasil ditambahkan.',
                                customClass: { confirmButton: 'btn btn-success waves-effect' },
                            });
                            setTimeout(function () { location.reload(); }, 1500);
                        }
                    },
                });
            }
        });
    });

    // Add Payment — toggle Tempo field
    $('#inv-add-payment-type').on('change', function () {
        if ($(this).val() === 'Tempo') {
            $('#inv-tempo-group').show().find('input').prop('required', true);
        } else {
            $('#inv-tempo-group').hide().find('input').prop('required', false).val('');
        }
    });

    // Upload Bukti — set action URL dinamis lalu buka modal
    var $uploadBtn = null;
    $(document).on('click', '.btn-upload-proof-inv', function () {
        var id = $(this).data('id');
        $uploadBtn = $(this);
        $('#formUploadBuktiInv').data('payment-id', id).attr('action', '/unit-quotation/payment/' + id + '/proof');
        $('#modalUploadBukti').modal('show');
    });

    // Intercept submit → AJAX (biar response JSON tidak tampil di browser)
    $('#formUploadBuktiInv').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        var url      = $(this).attr('action');
        var payId    = $(this).data('payment-id');
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                $('#modalUploadBukti').modal('hide');
                $('#formUploadBuktiInv')[0].reset();
                if (res.success) {
                    var $row = $('#pay-row-' + payId);
                    $row.find('.badge.bg-label-warning').replaceWith(
                        '<a href="' + res.file_url + '" target="_blank" class="badge bg-label-success text-decoration-none" style="font-size:10px">' +
                        '<i class="mdi mdi-file-check-outline"></i> Bukti Transfer</a>'
                    );
                    if ($uploadBtn) $uploadBtn.remove();
                    $row.find('.d-flex.gap-1.ms-2').append(
                        '<button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete-proof" data-id="' + payId + '" title="Hapus Bukti Transfer"><i class="mdi mdi-file-remove-outline"></i></button>'
                    );
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Bukti transfer berhasil diupload.', timer: 1500, showConfirmButton: false })
                        .then(function () { window.location.reload(); });
                }
            },
            error: function () {
                $('#modalUploadBukti').modal('hide');
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal upload. Cek format dan ukuran file.' });
            }
        });
    });

    // Hapus Bukti Transfer
    $(document).on('click', '.btn-delete-proof', function () {
        var id   = $(this).data('id');
        var $btn = $(this);
        Swal.fire({
            title: 'Hapus bukti transfer?',
            text: 'File bukti transfer akan dihapus, payment tetap ada.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-2 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '/unit-quotation/payment/' + id + '/proof',
                type: 'POST',
                data: { _method: 'DELETE', _token: '<?php echo e(csrf_token()); ?>' },
                success: function (res) {
                    if (res.success) {
                        var $row = $btn.closest('[id^="pay-row-"]');
                        $row.find('.badge.bg-label-success')
                            .replaceWith('<span class="badge bg-label-warning" style="font-size:10px">Belum ada bukti</span>');
                        $btn.remove();
                        $row.find('.d-flex.gap-1.ms-2').prepend(
                            '<button type="button" class="btn btn-sm btn-icon btn-outline-success btn-upload-proof-inv" data-id="' + id + '" title="Upload Bukti"><i class="mdi mdi-upload"></i></button>'
                        );
                        Swal.fire({ icon: 'success', title: 'Dihapus', text: 'Bukti transfer berhasil dihapus.', timer: 1500, showConfirmButton: false });
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan.' });
                }
            });
        });
    });

    // Delete Hand Sign
    $(document).on('click', '.delete-hand-sign-unit', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Hand Sign?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/invoice/unit/' + id + '/del-sign',
                    type: 'POST',
                    data: { '_method': 'DELETE', '_token': '<?php echo e(csrf_token()); ?>' },
                    success: function (response) {
                        if (response == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus!',
                                customClass: { confirmButton: 'btn btn-success waves-effect' },
                            });
                            setTimeout(function () { location.reload(); }, 1500);
                        }
                    },
                });
            }
        });
    });

    // Delete Delivery / Surat Jalan
    $(document).on('click', '.delete-delivery', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Surat Jalan #' + id + '?',
            text: 'Item yang ada di Surat Jalan ini akan dikembalikan ke sisa Qty pengiriman.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/delivery/' + id,
                    type: 'POST',
                    data: { '_method': 'DELETE', '_token': '<?php echo e(csrf_token()); ?>' },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Surat Jalan Dihapus!',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                        });
                        setTimeout(function () {
                            window.location.hash = 'tab-delivery';
                            location.reload();
                        }, 1200);
                    },
                    error: function () {
                        Swal.fire('Oops...', 'Gagal menghapus Surat Jalan.', 'error');
                    }
                });
            }
        });
    });

    // Auto switch tab if URL hash exists (e.g. #tab-delivery)
    function activateTabFromHash() {
        if (window.location.hash) {
            var activeTab = document.querySelector(`button[data-bs-target="${window.location.hash}"]`);
            if (activeTab) {
                var tab = new bootstrap.Tab(activeTab);
                tab.show();
            }
        }
    }
    activateTabFromHash();

    // Sync URL hash when switching tabs
    $(document).on('shown.bs.tab', 'button[data-bs-toggle="tab"]', function (e) {
        var target = $(e.target).attr('data-bs-target');
        if (target) {
            history.replaceState(null, null, target);
        }
    });

    // Switch to BAST tab
    window.switchToBastTab = function() {
        var btn = document.getElementById('btn-tab-bast');
        if (btn) {
            var tab = new bootstrap.Tab(btn);
            tab.show();
        }
    };

    // BAST Form Submit
    $('#formCreateBast').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: '<?php echo e(route("bast.store")); ?>',
            type: 'POST',
            data: formData,
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'BAST Berhasil Dibuat!',
                    text: res.message || '',
                    customClass: { confirmButton: 'btn btn-success waves-effect' },
                });
                setTimeout(function () {
                    window.location.hash = 'tab-bast';
                    location.reload();
                }, 1200);
            },
            error: function (xhr) {
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal membuat BAST.';
                Swal.fire('Oops...', msg, 'error');
            }
        });
    });

    // Add BAST Unit Row
    $(document).on('click', '#btnAddBastUnitRow', function () {
        var rowIdx = $('#tableBastUnits tbody tr').length;
        var newRow = `<tr>
            <td><input type="text" name="units[${rowIdx}][unit_name]" class="form-control form-control-sm" placeholder="Nama Unit / Barang" required></td>
            <td><input type="text" name="units[${rowIdx}][serial_no]" class="form-control form-control-sm" placeholder="No Seri (Opsional)"></td>
            <td><input type="number" name="units[${rowIdx}][qty]" class="form-control form-control-sm text-center" value="1" min="1"></td>
            <td class="text-center"><button type="button" class="btn btn-xs btn-outline-danger remove-bast-unit-row"><i class="mdi mdi-delete-outline"></i></button></td>
        </tr>`;
        $('#tableBastUnits tbody').append(newRow);
    });

    // Remove BAST Unit Row
    $(document).on('click', '.remove-bast-unit-row', function () {
        if ($('#tableBastUnits tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            Swal.fire('Info', 'Minimal harus ada 1 unit barang.', 'info');
        }
    });

    // Delete BAST
    $(document).on('click', '.delete-bast', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus BAST ini?',
            text: 'Data Berita Acara Serah Terima akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/bast/' + id,
                    type: 'POST',
                    data: { '_method': 'DELETE', '_token': '<?php echo e(csrf_token()); ?>' },
                    success: function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'BAST Dihapus!',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                        });
                        setTimeout(function () {
                            window.location.hash = 'tab-invoice';
                            location.reload();
                        }, 1200);
                    },
                    error: function () {
                        Swal.fire('Oops...', 'Gagal menghapus BAST.', 'error');
                    }
                });
            }
        });
    });
</script>


<?php if(!isset($bast) || !$bast): ?>
    <div class="modal fade" id="modalCreateBast" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="formCreateBast">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id_kanban_task" value="<?php echo e($monitoringTask ? $monitoringTask->id : ''); ?>">
                <input type="hidden" name="id_quotation" value="<?php echo e($quote->id); ?>">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light py-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-certificate-outline text-success fs-4"></i>
                            <h5 class="modal-title fw-bold mb-0">Buat Berita Acara Serah Terima (BAST)</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Entitas Perusahaan</label>
                                <select name="entity" class="form-select" required>
                                    <option value="Reftech" selected>Reftech</option>
                                    <option value="Kojisha">Kojisha</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Pekerjaan</label>
                                <input type="date" name="work_date" class="form-control" value="<?php echo e(\Carbon\Carbon::today()->toDateString()); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nomor PO / Ref</label>
                                <input type="text" name="po_number" class="form-control" value="<?php echo e($quote->po_number ?: $quote->no_quote); ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Customer (Penerima)</label>
                                <input type="text" name="customer_name" class="form-control" value="<?php echo e($quote->client->company ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Pekerjaan (Title)</label>
                                <input type="text" name="work_title" class="form-control" value="<?php echo e($quote->title ?: ($quote->no_quote ?? '')); ?>" required>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-2 text-dark mt-4"><i class="mdi mdi-format-list-bulleted me-1 text-success"></i> Rincian Unit / Barang</h6>
                        <div class="table-responsive border rounded mb-3">
                            <table class="table table-sm align-middle m-0" id="tableBastUnits">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Unit / Barang</th>
                                        <th style="width:30%;">No. Seri (Serial Number)</th>
                                        <th style="width:15%;" class="text-center">Qty</th>
                                        <th style="width:8%;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($quote->details && $quote->details->isNotEmpty()): ?>
                                        <?php $__currentLoopData = $quote->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $unitName = '';
                                                if ($item->details_type === 'Unit' && $item->unit) {
                                                    $unitName = trim(($item->unit->brand ? $item->unit->brand->name . ' ' : '') . $item->unit->name);
                                                } else {
                                                    $unitName = $item->label ?: $item->description;
                                                }
                                            ?>
                                            <tr>
                                                <td>
                                                    <input type="text" name="units[<?php echo e($idx); ?>][unit_name]" class="form-control form-control-sm" value="<?php echo e($unitName); ?>" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="units[<?php echo e($idx); ?>][serial_no]" class="form-control form-control-sm" placeholder="S/N (Opsional)">
                                                </td>
                                                <td>
                                                    <input type="number" name="units[<?php echo e($idx); ?>][qty]" class="form-control form-control-sm text-center" value="<?php echo e((int)($item->qty ?? 1)); ?>" min="1">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-xs btn-outline-danger remove-bast-unit-row"><i class="mdi mdi-delete-outline"></i></button>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <tr>
                                            <td><input type="text" name="units[0][unit_name]" class="form-control form-control-sm" placeholder="Nama Unit" required></td>
                                            <td><input type="text" name="units[0][serial_no]" class="form-control form-control-sm" placeholder="S/N"></td>
                                            <td><input type="number" name="units[0][qty]" class="form-control form-control-sm text-center" value="1" min="1"></td>
                                            <td class="text-center"><button type="button" class="btn btn-xs btn-outline-danger remove-bast-unit-row"><i class="mdi mdi-delete-outline"></i></button></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-xs btn-outline-success mb-3" id="btnAddBastUnitRow">
                            <i class="mdi mdi-plus me-1"></i> Tambah Baris Unit
                        </button>

                        <div class="mb-2">
                            <label class="form-label fw-semibold">Hasil Test Running / Catatan Serah Terima</label>
                            <textarea name="test_running_result" class="form-control" rows="3" placeholder="Contoh: Unit telah terpasang, dites running dengan hasil baik & berfungsi normal."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success fw-bold">
                            <i class="mdi mdi-check me-1"></i> Simpan BAST
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/accounting/invoice/detail-unit.blade.php ENDPATH**/ ?>