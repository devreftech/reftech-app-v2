<?php use Illuminate\Support\Facades\Storage; ?>

<?php $__env->startSection('title', 'Request Invoice — Unit Quotation'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row invoice-preview">
        
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body p-4">
                    
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column mb-0">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img src="<?php echo e(asset('/asset')); ?>/logo/Reftech-Log.png" alt="" width="60%">
                                    </span>
                                </span>
                            </div>
                            <p class="mb-1 fw-bolder" style="font-size: 15px">PT Reftech Jaya Optima</p>
                            <div style="font-size: 12px; color: #555;">
                                <p class="mb-0">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                                <p class="mb-0">Bandung – Jawa Barat 40218</p>
                                <p class="mb-0"><i class="mdi mdi-phone-outline me-1" style="font-size:11px;"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1" style="font-size:11px;"></i>info@reftech.id &nbsp;|&nbsp; <i class="mdi mdi-web me-1" style="font-size:11px;"></i>www.reftech.id</p>
                                <p class="mb-0 mt-1" style="font-size:10.5px; color:#444; font-weight:500;">
                                    <i class="mdi mdi-certificate-outline me-1 text-primary"></i><span class="fw-bold" style="color:#696cff;">ISO Certified:</span> ISO 9001:2015 &nbsp;|&nbsp; ISO 14001:2015 &nbsp;|&nbsp; ISO 45001:2018
                                </p>
                            </div>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold mb-1" style="letter-spacing:2px; color:#696cff;">INVOICE REQUEST</h3>
                            <p class="mb-1 fw-semibold" style="font-size:14px;">#<?php echo e($quote->no_quote); ?></p>
                            <div class="mb-1">
                                <span class="badge bg-warning px-3 py-1 fs-6">Unit Quotation</span>
                            </div>
                            <p class="mb-0 text-muted" style="font-size:12px;"><?php echo e($quote->date?->format('d F Y')); ?></p>
                            <?php if($quote->no_pr): ?>
                                <p class="mb-0 text-muted" style="font-size:11px;">No. PR: <?php echo e($quote->no_pr); ?></p>
                            <?php endif; ?>
                            <?php if($quote->type || $quote->week): ?>
                                <p class="mb-0 text-muted" style="font-size:11px;">
                                    Type: <?php echo e($quote->type); ?><?php echo e($quote->type && $quote->week ? ' | ' : ''); ?><?php echo e($quote->week ? 'Week ' . $quote->week : ''); ?>

                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div style="height:3px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:14px 0 16px;"></div>

                    
                    <div style="display:flex !important; align-items:stretch !important; gap:12px; margin-bottom:16px; font-size:12px;">
                        <div style="flex:1; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                            <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Quote To</p>
                            <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;"><?php echo e($quote->client?->company ?? '-'); ?></p>
                            <?php
                                $contactParts = [];
                                if ($quote->pic?->name_pic) {
                                    $contactParts[] = '<i class="mdi mdi-account-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($quote->pic->name_pic) . '</span>';
                                }
                                if ($quote->pic?->phone_pic) {
                                    $contactParts[] = '<i class="mdi mdi-phone-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($quote->pic->phone_pic) . '</span>';
                                }
                                if ($quote->client?->email) {
                                    $contactParts[] = '<i class="mdi mdi-email-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($quote->client->email) . '</span>';
                                }
                            ?>
                            <?php if(count($contactParts) > 0): ?>
                                <p class="mb-1" style="font-size:11.5px; color:#333;">
                                    <?php echo implode(' &nbsp;|&nbsp; ', $contactParts); ?>

                                </p>
                            <?php endif; ?>
                            <?php if($quote->address || $quote->plant): ?>
                                <p class="mb-0" style="font-size:11.5px; color:#222;">
                                    <i class="mdi mdi-map-marker-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;"><?php echo e($quote->address ?? $quote->plant?->address); ?> <?php echo e($quote->plant ? '(' . $quote->plant->name . ')' : ''); ?></span>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div style="min-width:240px; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                            <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Prepared By</p>
                            <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;"><?php echo e($quote->sales?->name ?? 'Sales Engineer'); ?></p>
                            <p class="mb-1 fw-medium" style="font-size:11.5px; color:#444;">
                                <i class="mdi mdi-briefcase-outline me-1" style="font-size:11px; color:#444;"></i><?php echo e($quote->sales?->title ?? 'Sales Engineer'); ?>

                            </p>
                            <?php if($quote->sales?->email || $quote->sales?->phone): ?>
                                <p class="mb-0" style="font-size:11.5px; color:#222;">
                                    <?php if($quote->sales?->phone): ?>
                                        <i class="mdi mdi-phone-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;"><?php echo e($quote->sales->phone); ?></span>
                                    <?php endif; ?>
                                    <?php if($quote->sales?->phone && $quote->sales?->email): ?> &nbsp;|&nbsp; <?php endif; ?>
                                    <?php if($quote->sales?->email): ?>
                                        <i class="mdi mdi-email-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;"><?php echo e($quote->sales->email); ?></span>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
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
                    ?>
                    <div class="table-responsive rounded border mb-3">
                        <table class="table table-bordered m-0" style="width:100%; font-size:12px;">
                            <thead style="font-size:11px; background:#eeeeff; color:#3d3d8f;">
                                <tr>
                                    <th class="text-center py-2" style="width:4%; font-weight:700; border-color:#d0d0ff;">No.</th>
                                    <th class="text-center py-2" style="width:<?php echo e($hasDisc ? '44%' : '49%'); ?>; font-weight:700; border-color:#d0d0ff;">DESKRIPSI</th>
                                    <th class="text-center py-2" style="width:10%; font-weight:700; border-color:#d0d0ff;">Qty</th>
                                    <th class="text-center py-2" style="width:18%; font-weight:700; border-color:#d0d0ff;">HARGA (IDR)</th>
                                    <?php if($hasDisc): ?>
                                        <th class="text-center py-2" style="width:7%; font-weight:700; border-color:#d0d0ff;">Disc</th>
                                    <?php endif; ?>
                                    <th class="text-center py-2" style="width:<?php echo e($hasDisc ? '17%' : '19%'); ?>; font-weight:700; border-color:#d0d0ff;">TOTAL HARGA (IDR)</th>
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
                                            <td colspan="<?php echo e($hasDisc ? '6' : '5'); ?>" class="fw-bold text-primary text-uppercase px-3" style="padding: 5px 10px; font-size:11.5px; border-top:1px solid #d0d0ff; border-bottom:1px solid #d0d0ff;">
                                                <i class="mdi mdi-bookmark-outline me-1"></i><?php echo e($lbl); ?>

                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr style="font-size: 12px">
                                            <td class="text-center align-top py-2"><?php echo e($itemNo++); ?></td>
                                            <td class="align-top py-2">
                                                <?php if($item->type === 'unit' && $item->unit): ?>
                                                    <p class="mb-1 fw-semibold" style="font-size: 12px">
                                                        <?php echo e($item->label ?: ($item->unit->brand . ' ' . $item->unit->sku . ($item->unit->model ? ' — ' . $item->unit->model : ''))); ?>

                                                    </p>
                                                    <?php $specs = $item->getSpecVisibleArray(); ?>
                                                    <?php if(!empty($specs)): ?>
                                                        <div style="font-size:11px; color:#777; margin-top:4px;">
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
                                                    <div class="text-muted" style="font-size:11px; white-space:pre-line; margin-top:2px;"><?php echo e($item->description); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center align-top py-2"><?php echo e((float) $item->qty); ?> <?php echo e($item->info_qty ?? 'Unit'); ?></td>
                                            <td class="text-end align-top py-2"><?php echo e(number_format($item->price, 0, '', '.')); ?></td>
                                            <?php if($hasDisc): ?>
                                                <td class="text-center align-top py-2"><?php echo e($item->disc > 0 ? (float) $item->disc . '%' : '-'); ?></td>
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
                        <div style="min-width:270px; font-size:12px; border:1px solid #d0d0ff; border-left:4px solid #696cff; border-radius:6px; overflow:hidden; background:#fff;">
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
                                <tr style="border-top:1px solid #eeeeff;">
                                    <td style="padding:6px 16px 6px 14px; color:#555;">Tax <?php echo e($quote->tax ? '(11%)' : ''); ?></td>
                                    <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">
                                        <?php echo e($quote->tax ? 'Rp ' . number_format($quote->tax_amount, 0, '', '.') : '-'); ?>

                                    </td>
                                </tr>
                                <?php if($quote->shipping > 0): ?>
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;">Shipping Cost</td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp <?php echo e(number_format($quote->shipping, 0, '', '.')); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr style="border-top:2px solid #d0d0ff; background:#f0f0ff;">
                                    <td style="padding:9px 16px 9px 14px; font-weight:700; font-size:13px; color:#3d3d8f;">TOTAL PRICE</td>
                                    <td style="padding:9px 14px 9px 0; text-align:right; font-weight:700; font-size:13px; color:#696cff;">Rp <?php echo e(number_format($quote->total, 0, '', '.')); ?></td>
                                </tr>
                                <?php
                                    $rawType   = $invoice->type ?: ($quote->payment_method ?? 'Payment');
                                    $typeLabel = match($rawType) {
                                        'Balance Payment', 'BP' => 'BP',
                                        'Down Payment', 'DP'    => 'DP',
                                        'Down Payment 2'        => 'DP 2',
                                        'Down Payment 3'        => 'DP 3',
                                        'Cash / Full Payment', 'CT' => 'CT',
                                        default => str_replace(['Balance Payment', 'Down Payment'], ['BP', 'DP'], $rawType)
                                    };
                                ?>
                                <?php if($quote->payment_method === 'DP & BP' || $invoice->percent < 100 || $invoice->type): ?>
                                    <?php
                                        $pct = floatval($invoice->percent ?? 100);
                                        $amt = round($quote->total * $pct / 100);
                                    ?>
                                    <tr style="border-top:1.5px solid #696cff; background:#e8ebff;">
                                        <td style="padding:8px 16px 8px 14px; font-weight:700; font-size:12px; color:#3d3d8f;">
                                            TAGIHAN <?php echo e(strtoupper($typeLabel)); ?> (<?php echo e($pct); ?>%)
                                        </td>
                                        <td style="padding:8px 14px 8px 0; text-align:right; font-weight:700; font-size:12px; color:#696cff;">Rp <?php echo e(number_format($amt, 0, '', '.')); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">

            
            <div class="card mb-3">
                <div class="card-header fw-semibold">Terbitkan Invoice</div>
                <div class="card-body">
                    <?php
                        $invPercent = floatval($invoice->percent ?? 100);
                        $invAmount  = round($quote->total * $invPercent / 100);
                    ?>

                    
                    <div class="mb-3 p-2 rounded" style="background: #f0f4ff;">
                        <div class="small text-muted">
                            <?php echo e($typeLabel); ?> (<?php echo e($invPercent); ?>%)
                        </div>
                        <div class="fw-bold">Rp <?php echo e(number_format($invAmount, 0, ',', '.')); ?></div>
                    </div>

                    <form action="<?php echo e(route('accept.unit', $invoice->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Tanggal Invoice</label>
                            <input type="date" name="invoice_date" class="form-control form-control-sm"
                                   value="<?php echo e(now()->toDateString()); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Term of Payment</label>
                            <input type="text" name="term" class="form-control form-control-sm"
                                   placeholder="misal: Cash, NET 30, NET 60..." required>
                        </div>

                        <?php $__currentLoopData = $allInvoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">
                                    No. Invoice
                                    <?php if($quote->payment_method === 'DP & BP' || $allInvoices->count() > 1): ?>
                                        <span class="badge <?php echo e($inv->type === 'DP' ? 'bg-warning text-dark' : 'bg-info'); ?> ms-1"><?php echo e($inv->type); ?></span>
                                    <?php endif; ?>
                                </label>
                                <input type="text" name="no_invoice_<?php echo e($inv->id); ?>"
                                       class="form-control form-control-sm"
                                       value="<?php echo e($nextNumbers[$inv->id] ?? ''); ?>" required>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <button type="submit" class="btn btn-primary d-grid w-100 waves-effect mb-2">
                            Terbitkan Invoice
                        </button>
                    </form>
                    <button type="button" class="btn btn-outline-danger d-grid w-100 waves-effect mb-2"
                            data-bs-toggle="modal" data-bs-target="#rejectInvoiceModal">
                        Reject
                    </button>
                    <button class="btn btn-outline-secondary d-grid w-100 waves-effect" id="backButton">
                        Kembali
                    </button>
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-body">
                    <p class="mb-1 text-muted fw-semibold small">No. PO</p>
                    <p class="mb-3 fw-bold"><?php echo e($quote->po_number); ?></p>
                    <p class="mb-1 text-muted fw-semibold small">Payment Method</p>
                    <p class="mb-3 fw-bold"><?php echo e($typeLabel); ?> <?php echo e(floatval($invoice->percent ?? 100)); ?>%</p>
                    <?php if($quote->po_file): ?>
                        <a href="<?php echo e(Storage::url($quote->po_file)); ?>" target="_blank"
                           class="btn btn-outline-primary d-grid w-100 waves-effect">
                            <i class="mdi mdi-file-pdf-box me-1"></i> Lihat File PO
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectInvoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="<?php echo e(route('reject.unit', $invoice->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Pengajuan Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">Pengajuan invoice untuk quote <strong><?php echo e($quote->no_quote); ?></strong>
                            akan di-reject dan tidak akan muncul lagi di halaman Request Invoice.</p>
                        <div class="mb-3">
                            <label class="form-label">Alasan Reject (opsional)</label>
                            <textarea name="reason" class="form-control" rows="3"
                                placeholder="Contoh: PO tidak sesuai, data belum lengkap, dll."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
<script>
    $('#backButton').click(function () {
        window.history.back();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/accounting/invoice/before-accept-unit.blade.php ENDPATH**/ ?>