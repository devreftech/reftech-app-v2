<?php $__env->startSection('title', 'Detail Unit Quotation'); ?>
<?php $__env->startSection('content'); ?>

<?php
    use Illuminate\Support\Facades\Storage;
    $statusMap = [
        'draft'        => ['label' => 'DRAFT',        'color' => 'secondary', 'solid' => true],
        'sent'         => ['label' => 'SENT',          'color' => 'info',      'solid' => true],
        'negotiation'  => ['label' => 'NEGOTIATION',   'color' => 'warning',   'solid' => true],
        'revision'     => ['label' => 'REVISI',        'color' => 'primary',   'solid' => true],
        'hot_prospect' => ['label' => 'HOT PROSPECT',  'color' => 'danger',    'solid' => true],
        'po_received'  => ['label' => 'PO RECEIVED',   'color' => 'success',   'solid' => true],
        'loss'         => ['label' => 'LOSS',          'color' => 'dark',      'solid' => true],
    ];
    $st = $statusMap[$quote->status] ?? ['label' => strtoupper($quote->status), 'color' => 'secondary'];
?>


<?php if($allVersions->count() > 1): ?>
<div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
    <span class="text-muted small fw-semibold">Versi:</span>
    <?php $__currentLoopData = $allVersions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $vLabel = $v->revision_number === 0 ? $v->no_quote : 'Revisi ' . $v->revision_number; ?>
        <?php if($v->id === $quote->id): ?>
            <span class="badge bg-primary"><?php echo e($vLabel); ?></span>
        <?php else: ?>
            <a href="<?php echo e(route('unit-quotation.show', $v->id)); ?>"
               class="badge bg-label-secondary text-decoration-none"><?php echo e($vLabel); ?></a>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<div class="row invoice-preview">

    
    <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
        <div class="card invoice-preview-card mb-3 shadow-sm border-0">
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
                        <h3 class="fw-bold mb-1" style="letter-spacing:2px; color:#696cff;">QUOTATION</h3>
                        <p class="mb-1 fw-bold text-dark" style="font-size:16px;">#<?php echo e($quote->no_quote); ?></p>
                        <p class="mb-1 fw-bold" style="font-size:13px; color:#0f172a !important;">
                            <i class="mdi mdi-calendar-blank-outline me-1 text-primary"></i><?php echo e($quote->date?->format('d-m-Y')); ?>

                        </p>
                        <div class="mb-1 mt-1">
                            <span class="badge bg-<?php echo e($st['color']); ?> px-3 py-1 fs-6"><?php echo e($st['label']); ?></span>
                        </div>
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
                        <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;">
                            <?php echo e($quote->client?->company ?? '-'); ?>

                            <?php if($quote->plant): ?>
                                <span class="badge bg-label-primary ms-1" style="font-size:9.5px; vertical-align:middle;"><?php echo e(strtoupper($quote->plant->name)); ?></span>
                            <?php endif; ?>
                        </p>
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
                            <div class="mb-0" style="display:flex; align-items:flex-start; font-size:11.5px; color:#222;">
                                <i class="mdi mdi-map-marker-outline me-1" style="font-size:11px; color:#444; line-height:1.4; flex-shrink:0;"></i><span style="font-weight:500; line-height:1.4;"><?php echo e($quote->address ?? $quote->plant?->address); ?> <?php echo e($quote->plant ? '(' . $quote->plant->name . ')' : ''); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="min-width:240px; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                        <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Prepared By</p>
                        <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;"><?php echo e($quote->sales?->name ?? 'Alifya Syahrani'); ?></p>
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

                <p class="mb-3" style="font-size:12px; color:#777; font-style:italic;">
                    Dear Sir/Madam, Please find bellow our price quotation for the following :
                </p>

                
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
                                <th class="text-center py-2" style="width:<?php echo e($hasDisc ? '44%' : '49%'); ?>; font-weight:700; border-color:#d0d0ff;">Item Description</th>
                                <th class="text-center py-2" style="width:10%; font-weight:700; border-color:#d0d0ff;">Qty</th>
                                <th class="text-center py-2" style="width:18%; font-weight:700; border-color:#d0d0ff;">Price (IDR)</th>
                                <?php if($hasDisc): ?>
                                    <th class="text-center py-2" style="width:7%; font-weight:700; border-color:#d0d0ff;">Disc</th>
                                <?php endif; ?>
                                <th class="text-center py-2" style="width:<?php echo e($hasDisc ? '17%' : '19%'); ?>; font-weight:700; border-color:#d0d0ff;">Total (IDR)</th>
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
                                                         $subDesc = preg_replace('/^[\s\-\*\•]+/u', '', $item->label);
                                                         if (empty($subDesc) || $subDesc === $brandPn) {
                                                             $subDesc = optional($item->equivalent->product)->description ?? optional($item->equivalent->product)->name;
                                                         }
                                                         $prod = optional($item->equivalent)->product;
                                                         $stkBdg = (int) ($prod->stock ?? 0);
                                                         $stkBks = (int) ($prod->warehouse_stock ?? 0);
                                                         $stkPend = (int) ($prod->pending_stock ?? 0);
                                                         $totalStk = $stkBdg + $stkBks;
                                                         $popoverContent = "<div class='text-start small p-1'><div><span class='badge bg-label-primary me-1'>BDG: $stkBdg</span> Stok Bandung</div><div class='mt-1'><span class='badge bg-label-info me-1'>BKS: $stkBks</span> Stok Bekasi</div><div class='mt-1'><span class='badge bg-label-warning me-1'>Pend: $stkPend</span> Pending PO</div></div>";
                                                     ?>
                                                     <div class="d-inline-flex align-items-center flex-wrap gap-1">
                                                         <p class="mb-0 fw-bold text-dark" style="font-size: 12px"><?php echo e($brandPn ?: preg_replace('/^[\s\-\*\•]+/u', '', $item->label)); ?></p>
                                                         <?php if($prod): ?>
                                                             <span class="badge bg-label-info ms-2 cursor-pointer stock-popover"
                                                                   data-bs-toggle="tooltip"
                                                                   data-bs-placement="top"
                                                                   data-bs-html="true"
                                                                   title="<b>BDG:</b> <?php echo e($stkBdg); ?> &nbsp;|&nbsp; <b>BKS:</b> <?php echo e($stkBks); ?> &nbsp;|&nbsp; <b>Pend:</b> <?php echo e($stkPend); ?>"
                                                                   data-bs-trigger="hover focus"
                                                                   style="font-size: 9.5px; font-weight: 600; padding: 2px 6px;">
                                                                 <i class="mdi mdi-cube-outline me-1"></i>Stok: <?php echo e($totalStk); ?>

                                                             </span>
                                                         <?php endif; ?>
                                                     </div>
                                                     <?php if($subDesc && $subDesc !== $brandPn): ?>
                                                         <div style="font-size: 12px; color: #333333; font-weight: 500; margin-top: 2px; line-height: 1.4;"><?php echo e(preg_replace('/^[\s\-\*\•]+/u', '', $subDesc)); ?></div>
                                                     <?php endif; ?>
                                                 <?php else: ?>
                                                     <p class="mb-0 fw-bold text-dark" style="font-size: 12px"><?php echo e(preg_replace('/^[\s\-\*\•]+/u', '', $item->label)); ?></p>
                                                 <?php endif; ?>
                                            <?php else: ?>
                                                <p class="mb-0 fw-bold text-dark" style="font-size: 12px"><?php echo e(preg_replace('/^[\s\-\*\•]+/u', '', $item->label)); ?></p>
                                                <?php if($item->description): ?>
                                                     <?php
                                                         $descLines = explode("\n", str_replace("\r", "", $item->description));
                                                     ?>
                                                     <div class="text-muted" style="font-size:11px; margin-top:3px; line-height:1.4;">
                                                         <?php $__currentLoopData = $descLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dLine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                             <?php
                                                                 $trimmedDLine = trim($dLine);
                                                             ?>
                                                             <?php if(empty($trimmedDLine)): ?>
                                                                 <div style="height:2px;"></div>
                                                             <?php else: ?>
                                                                 <?php
                                                                     $hasBullet = preg_match('/^([•\-\*]|\d+[\.\)])\s*(.*)/u', $trimmedDLine, $dMatches);
                                                                 ?>
                                                                 <?php if($hasBullet && !empty($dMatches[1]) && !empty($dMatches[2])): ?>
                                                                     <div style="display:flex; align-items:flex-start; margin-bottom:2px;">
                                                                         <span style="flex-shrink:0; min-width:14px; color:#696cff; font-weight:600;"><?php echo e($dMatches[1]); ?></span>
                                                                         <span style="flex:1;"><?php echo e($dMatches[2]); ?></span>
                                                                     </div>
                                                                 <?php else: ?>
                                                                     <div style="margin-bottom:2px; font-weight:600; color:#222;"><?php echo e($dLine); ?></div>
                                                                 <?php endif; ?>
                                                             <?php endif; ?>
                                                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                     </div>
                                                 <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-top py-2"><?php echo e((int) $item->qty); ?> <?php echo e($item->info_qty ?? 'Unit'); ?></td>
                                        <td class="text-end align-top py-2"><?php echo e(number_format($item->price, 0, '', '.')); ?></td>
                                        <?php if($hasDisc): ?>
                                            <td class="text-center align-top py-2"><?php echo e($item->disc > 0 ? (int) $item->disc . '%' : '-'); ?></td>
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
                                <td style="padding:6px 16px 6px 14px; color:#555;">Tax <?php echo e($quote->tax ? '(12%)' : ''); ?></td>
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
                        </table>
                    </div>
                </div>

                
                <?php if($quote->note): ?>
                <div style="border:1px solid #e0e0e0; border-left:3px solid #696cff; border-radius:6px; padding:10px 14px; font-size:11px; color:#333; margin-bottom:14px; background:#fafafa;">
                    <p class="mb-1 fw-semibold text-uppercase" style="font-size:10px; color:#888; letter-spacing:.5px;">Remarks / Note</p>
                    <?php
                        $noteLines = explode("\n", str_replace("\r", "", $quote->note));
                    ?>
                    <div style="font-size:11px; color:#222; line-height:1.5;">
                        <?php $__currentLoopData = $noteLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $trimmed = trim($line);
                            ?>
                            <?php if(empty($trimmed)): ?>
                                <div style="height:3px;"></div>
                            <?php else: ?>
                                <?php
                                    $hasBullet = preg_match('/^([•\-\*]|\d+[\.\)])\s*(.*)/u', $trimmed, $matches);
                                ?>
                                <?php if($hasBullet && !empty($matches[1]) && !empty($matches[2])): ?>
                                    <div style="display:flex; align-items:flex-start; margin-bottom:3px;">
                                        <span style="flex-shrink:0; min-width:20px; color:#696cff; font-weight:600;"><?php echo e($matches[1]); ?></span>
                                        <span style="flex:1;"><?php echo e($matches[2]); ?></span>
                                    </div>
                                <?php else: ?>
                                    <div style="margin-bottom:3px;"><?php echo e($line); ?></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                
                <div style="border:1px solid #e0e0e0; border-radius:6px; padding:12px 16px; font-size:12px; background:#fff; margin-bottom:16px;">
                    <p class="mb-2 fw-semibold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#888;">Term &amp; Condition</p>
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="width:160px; padding:3px 0; color:#555; vertical-align:top;">Validity of Quotation</td>
                            <td style="padding:3px 0; vertical-align:top;">: <?php echo e($quote->validity ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0; color:#555; vertical-align:top;">Price</td>
                            <td style="padding:3px 0; vertical-align:top;">: <?php echo e($quote->pricing ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0; color:#555; vertical-align:top;">Delivery Process</td>
                            <td style="padding:3px 0; vertical-align:top; white-space:pre-line;">: <?php echo e($quote->delivery_process ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0; color:#555; vertical-align:top;">Payment</td>
                            <td style="padding:3px 0; vertical-align:top;">: <?php echo e($quote->payment ?? '-'); ?></td>
                        </tr>
                        <?php if(!empty($quote->warranty)): ?>
                        <tr>
                            <td style="padding:3px 0; color:#555; vertical-align:top;">Warranty</td>
                            <td style="padding:3px 0; vertical-align:top;">: <?php echo e($quote->warranty); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>

                
                <div class="p-2 text-center rounded" style="background:#f4f4fe; border:1px solid #e0e0ff;">
                    <p class="mb-0 fw-bold" style="font-size:11px; color:#3d3d8f; letter-spacing:0.5px;">
                        COMPRESSED AIR SOLUTION : Sales &nbsp;|&nbsp; Rental &nbsp;|&nbsp; Maintenance &nbsp;|&nbsp; Air Audit &nbsp;|&nbsp; Installation
                    </p>
                </div>
            </div>
        </div>

        
        <?php echo $__env->make('pages.unit-quotation.components.activity-timeline', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </div>
    
    <div class="col-xl-3 col-md-4 col-12 invoice-actions">

        
        <?php
            $sellingContract          = $contracts->where('type', 'Selling')->where('level', '1')->first();
            $requestedSellingContract = $contracts->where('type', 'Selling')->where('level', '0')->first();
            $issuedInvoices  = $invoices->filter(fn($i) => !is_null($i->no_invoice));
            $pendingInvoices = $invoices->filter(fn($i) => is_null($i->no_invoice));
            $issuedTotal     = $issuedInvoices->sum(fn($i) => round($quote->total * floatval($i->percent ?? 100) / 100));
            $remaining       = $quote->total - $issuedTotal;
        ?>
        <?php if(!in_array(Auth::user()->role, ['Accounting', 'Admin'])): ?>
        <div class="card mb-3 border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-primary bg-gradient py-3 px-4 d-flex align-items-center justify-content-between text-white">
                <h6 class="card-title mb-0 fw-bold text-white d-flex align-items-center">
                    <i class="mdi mdi-lightning-bolt-outline me-2 fs-5"></i> Quick Actions
                </h6>
                <span class="badge bg-white text-primary fw-semibold" style="font-size: 10px;">CONTROLS</span>
            </div>

            <div class="card-body p-3">
                
                <div class="mb-3">
                    <a href="<?php echo e(route('unit-quotation.print', $quote->id)); ?>" target="_blank"
                       class="btn btn-primary d-grid w-100 shadow-sm py-2"
                       style="background: linear-gradient(135deg, #696cff 0%, #3f42db 100%); border: none;">
                        <span class="d-flex align-items-center justify-content-center gap-1 fw-bold fs-6">
                            <i class="mdi mdi-printer-outline fs-5"></i> Print / Download PDF
                        </span>
                    </a>
                </div>

                
                <?php if(($quote->status !== 'po_received') && (Auth::user()->role === 'Sales' || Auth::user()->role === 'Admin')): ?>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <a href="<?php echo e(route('unit-quotation.edit', $quote->id)); ?>"
                           class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1">
                            <i class="mdi mdi-pencil-outline"></i> Edit
                        </a>
                    </div>
                    <div class="col-6">
                        <?php if(Auth::user()->role === 'Sales'): ?>
                            <form action="<?php echo e(route('unit-quotation.revise', $quote->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-outline-info w-100 d-flex align-items-center justify-content-center gap-1"
                                    onclick="return confirm('Buat revisi dari quotation ini?')">
                                    <i class="mdi mdi-file-replace-outline"></i> Revisi
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                
                <?php if((Auth::user()->role === 'Sales' && $quote->status !== 'po_received') || $quote->po_file || $sellingContract || $requestedSellingContract || (Auth::user()->role === 'Admin' || Auth::user()->role === 'Accounting')): ?>
                <div class="p-3 rounded-3 mb-3 bg-white border shadow-sm">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-primary" style="font-size: 10px; letter-spacing: 0.5px;">
                            <i class="mdi mdi-file-document-check-outline me-1"></i> PO &amp; Contract
                        </span>
                        <?php if($quote->po_file): ?>
                            <span class="badge bg-label-success" style="font-size:9px;">PO ATTACHED</span>
                        <?php endif; ?>
                    </div>
                    
                    
                    <?php if(Auth::user()->role === 'Sales' && $quote->status !== 'po_received'): ?>
                        <button type="button" class="btn btn-sm btn-label-success d-flex align-items-center justify-content-center w-100 mb-2 btn-upload-po-unit fw-semibold"
                            data-npwp="<?php echo e($quote->client->npwp ?? ''); ?>"
                            data-client-url="<?php echo e($quote->client->role == 'Leads' ? route('detail.leads', $quote->client->id) : route('existing.show', $quote->client->id)); ?>">
                            <i class="mdi mdi-file-upload-outline me-1"></i> Upload PO
                        </button>
                    <?php endif; ?>
                    <?php if($quote->po_file): ?>
                        <a href="#" onclick="openPdfViewer('<?php echo e(Storage::url($quote->po_file)); ?>', 'File PO <?php echo e($quote->no_quote ?? ''); ?>'); return false;"
                           class="btn btn-sm btn-label-secondary d-flex align-items-center justify-content-center w-100 mb-2 fw-semibold">
                            <i class="mdi mdi-file-pdf-box text-danger me-1"></i> Lihat File PO
                        </a>
                    <?php endif; ?>

                    
                    <div class="row g-2 mt-1">
                        
                        <div class="col-6">
                            <?php if($sellingContract): ?>
                                <div class="btn-group w-100">
                                    <a class="btn btn-sm btn-label-primary fw-semibold text-truncate" href="<?php echo e(route('contract.show', $sellingContract->id)); ?>" title="Lihat Kontrak">
                                        <i class="mdi mdi-file-document-outline me-1"></i> Kontrak
                                    </a>
                                    <a class="btn btn-sm btn-outline-primary fw-semibold px-2" target="_blank" href="<?php echo e(route('contract.print', $sellingContract->id)); ?>" title="Unduh Kontrak">
                                        <i class="mdi mdi-download"></i>
                                    </a>
                                </div>
                            <?php elseif($requestedSellingContract): ?>
                                <div class="p-2 rounded-2 bg-warning-subtle text-warning-emphasis text-center" style="font-size: 10.5px;" title="Menunggu Accounting buat kontrak">
                                    <i class="mdi mdi-clock-outline me-1"></i> Wait Kontrak
                                </div>
                            <?php elseif(Auth::user()->role === 'Sales' && $quote->status !== 'po_received'): ?>
                                <a href="#" data-id="<?php echo e($quote->id); ?>" class="btn btn-sm btn-label-primary d-flex align-items-center justify-content-center w-100 fw-semibold px-1 text-truncate request-selling-unit" title="Request Selling Contract">
                                    <i class="mdi mdi-file-sign me-1"></i> Selling Contract
                                </a>
                            <?php elseif(Auth::user()->role === 'Admin' || Auth::user()->role === 'Accounting'): ?>
                                <button type="button" class="btn btn-sm btn-label-primary d-flex align-items-center justify-content-center w-100 fw-semibold px-1 text-truncate"
                                    data-bs-toggle="modal" data-bs-target="#modalSellingContractUnit" title="Create Selling Contract">
                                    <i class="mdi mdi-file-plus-outline me-1"></i> Selling Contract
                                </button>
                            <?php endif; ?>
                        </div>

                        
                        <div class="col-6">
                            <?php if($quote->suo): ?>
                                <a class="btn btn-sm btn-outline-info d-flex align-items-center justify-content-center w-100 fw-semibold px-1 text-truncate"
                                    href="<?php echo e(route('suo.show', $quote->suo->id)); ?>" title="Lihat SUO (<?php echo e($quote->suo->no_suo); ?>)">
                                    <i class="mdi mdi-eye-outline me-1"></i> SUO (<?php echo e($quote->suo->no_suo); ?>)
                                </a>
                            <?php elseif($quote->status !== 'po_received'): ?>
                                <?php if(Auth::user()->role === 'Sales' || Auth::user()->role === 'Admin'): ?>
                                    <a href="#" data-id="<?php echo e($quote->id); ?>"
                                        class="btn btn-sm btn-outline-dark d-flex align-items-center justify-content-center w-100 fw-semibold px-1 text-truncate ajukan-suo-unit" title="Ajukan SUO">
                                        <i class="mdi mdi-truck-fast-outline me-1"></i> Ajukan SUO
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                
                <?php if($invoices->isNotEmpty()): ?>
                <div class="rounded-3 mb-3 overflow-hidden" style="border: 1px solid #dde1ff;">
                    
                    <div class="d-flex align-items-center justify-content-between px-3 py-2" style="background: linear-gradient(90deg, #696cff 0%, #9c9eff 100%);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-receipt-text-outline text-white" style="font-size:15px;"></i>
                            <span class="fw-bold text-white" style="font-size:11.5px; letter-spacing:0.3px;">Billing & Invoices</span>
                        </div>
                        <span class="badge bg-white text-primary fw-bold" style="font-size:9.5px;"><?php echo e($invoices->count()); ?> Invoice</span>
                    </div>

                    
                    <?php
                        $progressPct = $quote->total > 0 ? min(100, round($issuedTotal / $quote->total * 100)) : 0;
                    ?>
                    <div class="px-3 pt-2 pb-1" style="background:#f6f7ff;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size:10px; color:#666;">Billed</span>
                            <span style="font-size:10px; font-weight:600; color:#696cff;"><?php echo e($progressPct); ?>%</span>
                        </div>
                        <div style="height:5px; background:#e0e0f0; border-radius:3px; overflow:hidden;">
                            <div style="width:<?php echo e($progressPct); ?>%; height:100%; background:linear-gradient(90deg,#696cff,#9c9eff); border-radius:3px; transition:width .4s;"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span style="font-size:9.5px; color:#888;">Rp <?php echo e(number_format($issuedTotal, 0, '', '.')); ?></span>
                            <span style="font-size:9.5px; color:#888;">/ Rp <?php echo e(number_format($quote->total, 0, '', '.')); ?></span>
                        </div>
                    </div>

                    
                    <div class="px-2 pt-1 pb-2" style="background:#fff;">
                        <?php if($issuedInvoices->isNotEmpty()): ?>
                            <?php $__currentLoopData = $issuedInvoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $invAmount = round($quote->total * floatval($inv->percent ?? 100) / 100);
                                    $isPaid = $inv->status_p;
                                    $badgeColor = $isPaid ? '#28a745' : '#696cff';
                                    $badgeBg    = $isPaid ? '#e8f8ed' : '#eef0ff';
                                ?>
                                <a href="<?php echo e(route('invoice.show_unit', $inv->id)); ?>"
                                   class="d-flex align-items-center justify-content-between text-decoration-none rounded-2 px-2 py-2 mb-1"
                                   style="background:#f9f9ff; border:1px solid #e5e5ff; transition:background .15s;"
                                   onmouseover="this.style.background='#eef0ff'" onmouseout="this.style.background='#f9f9ff'">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:28px; height:28px; background:#696cff20; border-radius:6px; display:flex; align-items:center; justify-content:center;">
                                            <i class="mdi mdi-file-document-outline" style="font-size:14px; color:#696cff;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size:11.5px; font-weight:700; color:#222;">#<?php echo e($inv->no_invoice); ?></div>
                                            <div style="font-size:10px; color:#888;">Rp <?php echo e(number_format($invAmount, 0, '', '.')); ?></div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <?php if($inv->type !== 'CT'): ?>
                                            <?php
                                                $shortType = match($inv->type) {
                                                    'Balance Payment' => 'BP',
                                                    'Down Payment'    => 'DP',
                                                    default           => str_replace(['Balance Payment', 'Down Payment'], ['BP', 'DP'], $inv->type)
                                                };
                                            ?>
                                            <span style="font-size:9.5px; font-weight:700; padding:2px 6px; border-radius:4px; background:#eef0ff; color:#696cff;"><?php echo e($shortType); ?></span>
                                        <?php endif; ?>
                                        <span style="font-size:9.5px; font-weight:600; padding:2px 7px; border-radius:4px; background:<?php echo e($badgeBg); ?>; color:<?php echo e($badgeColor); ?>;">
                                            <?php echo e($isPaid ? 'Paid' : 'Unpaid'); ?>

                                        </span>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <div class="d-flex align-items-center gap-2 px-1 py-2 text-muted" style="font-size:11px;">
                                <i class="mdi mdi-clock-outline text-warning"></i>
                                <span>Menunggu invoice diterbitkan</span>
                            </div>
                        <?php endif; ?>

                        <?php if($pendingInvoices->isNotEmpty()): ?>
                            <div class="d-flex align-items-center gap-2 px-2 py-1 rounded-2 mt-1" style="background:#fff8e1; border:1px solid #ffe57f; font-size:11px;">
                                <i class="mdi mdi-clock-sand text-warning" style="font-size:14px;"></i>
                                <span style="color:#8a6800; font-weight:500;"><?php echo e($pendingInvoices->count()); ?> invoice menunggu terbit</span>
                            </div>
                        <?php endif; ?>

                        <?php if($issuedInvoices->isNotEmpty() && $pendingInvoices->isEmpty() && $remaining > 0): ?>
                            <?php if(Auth::user()->role === 'Sales' || Auth::user()->role === 'Admin'): ?>
                                <button type="button"
                                    class="btn btn-sm w-100 mt-2 fw-semibold"
                                    style="background:#f0f2ff; color:#696cff; border:1.5px dashed #696cff; font-size:11.5px;"
                                    data-bs-toggle="modal" data-bs-target="#modalRequestNextInvoice">
                                    <i class="mdi mdi-plus-circle-outline me-1"></i> Ajukan Invoice Selanjutnya
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                
                <?php if(Auth::user()->role === 'Sales' && $quote->status !== 'po_received'): ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center w-100 py-1.5"
                        data-bs-toggle="modal" data-bs-target="#modalChangeStatus">
                        <i class="mdi mdi-swap-horizontal me-1"></i> Change Status
                    </button>
                <?php endif; ?>

                
                <?php if($quote->status === 'po_received' && $pendingPo): ?>
                    <button type="button"
                        class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center w-100 mt-2 fw-semibold"
                        data-bs-toggle="modal" data-bs-target="#convertPoUnit">
                        <i class="mdi mdi-truck-delivery-outline me-1"></i> Post to Sales Order
                    </button>
                <?php endif; ?>
            </div>

            
            <?php if(Auth::user()->role === 'Sales' || Auth::user()->role === 'Admin' || Auth::user()->role === 'Accounting'): ?>
            <div class="card-footer bg-light-subtle pt-2 pb-3 px-3 border-top">
                <?php if($quote->status === 'po_received'): ?>
                    <?php if($quote->cancel_request): ?>
                        
                        <?php if(Auth::user()->role === 'Accounting' || Auth::user()->role === 'Admin'): ?>
                            <p class="text-muted small mb-2 text-center" style="font-size: 11px;">
                                <i class="mdi mdi-alert-circle-outline text-warning me-1"></i>
                                Sales mengajukan pembatalan PO
                            </p>
                            <div class="row g-2">
                                <div class="col-6">
                                    <form action="<?php echo e(route('unit-quotation.approve-cancel', $quote->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-success w-100"
                                            onclick="return confirm('Setujui pembatalan PO ini?')">
                                            <i class="mdi mdi-check"></i> Setuju
                                        </button>
                                    </form>
                                </div>
                                <div class="col-6">
                                    <form action="<?php echo e(route('unit-quotation.reject-cancel', $quote->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                            <i class="mdi mdi-close"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="d-flex align-items-center gap-2 px-1" style="font-size: 11px;">
                                <i class="mdi mdi-clock-outline text-warning"></i>
                                <span class="text-muted">Menunggu persetujuan Accounting</span>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        
                        <?php if(Auth::user()->role === 'Sales' || Auth::user()->role === 'Admin'): ?>
                            <form action="<?php echo e(route('unit-quotation.cancel-po', $quote->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center w-100"
                                    onclick="return confirm('Batalkan PO untuk penawaran ini? Tindakan ini tidak bisa dibatalkan.')">
                                    <i class="mdi mdi-cancel me-1"></i> Cancel PO
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if(Auth::user()->role === 'Sales' || Auth::user()->role === 'Admin'): ?>
                    <a href="#" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center w-100 delete-quote"
                        data-id="<?php echo e($quote->id); ?>">
                        <i class="mdi mdi-trash-can-outline me-1"></i> Delete Quotation
                    </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php elseif($quote->status === 'po_received' && $quote->cancel_request): ?>
        
        <div class="card mb-3 border-danger shadow-sm">
            <div class="card-header bg-danger text-white py-2 px-3">
                <h6 class="card-title mb-0 text-white fw-bold d-flex align-items-center">
                    <i class="mdi mdi-alert-circle-outline me-2"></i> Pengajuan Pembatalan PO
                </h6>
            </div>
            <div class="card-body p-3 text-center">
                <p class="text-muted small mb-3" style="font-size: 12px;">
                    Sales (<strong><?php echo e($quote->sales?->name); ?></strong>) mengajukan pembatalan PO untuk penawaran ini.
                </p>
                <div class="row g-2">
                    <div class="col-6">
                        <form action="<?php echo e(route('unit-quotation.approve-cancel', $quote->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-sm btn-success w-100 fw-bold"
                                onclick="return confirm('Setujui pembatalan PO ini?')">
                                <i class="mdi mdi-check"></i> Setuju
                            </button>
                        </form>
                    </div>
                    <div class="col-6">
                        <form action="<?php echo e(route('unit-quotation.reject-cancel', $quote->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100 fw-bold">
                                <i class="mdi mdi-close"></i> Tolak
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($payments->isNotEmpty() || $quote->status === 'po_received'): ?>
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h5 class="mb-0">Payment</h5>
                <?php $paidTotal = $payments->sum('amount'); ?>
                <?php if($payments->isNotEmpty()): ?>
                    <span class="badge bg-label-success">Rp <?php echo e(number_format($paidTotal, 0, '', '.')); ?></span>
                <?php endif; ?>
            </div>
            <?php if($payments->isNotEmpty()): ?>
            <div class="card-body p-0">
                <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="d-flex align-items-start justify-content-between px-3 py-2 border-bottom" id="pay-row-<?php echo e($pay->id); ?>">
                    <div>
                        <p class="mb-0 fw-semibold small">Rp <?php echo e(number_format($pay->amount, 0, '', '.')); ?>

                            <?php if($pay->percent): ?>
                                <span class="text-muted small">(<?php echo e($pay->percent); ?>%)</span>
                            <?php endif; ?>
                            <?php if($pay->type): ?>
                                <span class="badge bg-label-primary ms-1 small"><?php echo e($pay->type); ?></span>
                            <?php endif; ?>
                        </p>
                        <?php if($pay->method): ?>
                            <p class="mb-0 text-muted small"><?php echo e($pay->method); ?></p>
                        <?php endif; ?>
                        <?php if($pay->note): ?>
                            <p class="mb-0 text-muted small"><?php echo e($pay->note); ?></p>
                        <?php endif; ?>
                        <div class="mt-1">
                            <?php if($pay->file): ?>
                                <a href="#" onclick="openPdfViewer('<?php echo e(asset($pay->file)); ?>', 'Bukti Transfer'); return false;"
                                   class="badge bg-label-success text-decoration-none me-1">
                                    <i class="mdi mdi-file-check-outline"></i> Bukti Transfer
                                </a>
                            <?php else: ?>
                                <span class="badge bg-label-warning">Belum ada bukti</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex gap-1 ms-2">
                        <?php if(!$pay->file): ?>
                            <button type="button" class="btn btn-sm btn-icon btn-outline-success btn-upload-proof"
                                data-id="<?php echo e($pay->id); ?>" title="Upload Bukti Transfer">
                                <i class="mdi mdi-upload"></i>
                            </button>
                        <?php endif; ?>
                        <?php if(Auth::user()->role === 'Admin' || Auth::user()->role === 'Accounting'): ?>
                            <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-delete-payment"
                                data-id="<?php echo e($pay->id); ?>" title="Hapus">
                                <i class="mdi mdi-delete-outline"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
            <?php if($quote->status === 'po_received' && Auth::user()->role === 'Sales'): ?>
            <div class="card-footer p-3">
                <button type="button" class="btn btn-outline-success d-flex align-items-center justify-content-center w-100 waves-effect"
                    data-bs-toggle="modal" data-bs-target="#modalAddPayment">
                    <i class="mdi mdi-cash-plus me-1"></i> Tambah Payment
                </button>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        
        <?php
            $lastHistory = $quote->statusHistory->sortByDesc('id')->first();
            $statusKey   = $lastHistory ? $lastHistory->status : $quote->status;
            $currHst     = $hstMap[$statusKey] ?? ['label' => ucfirst(str_replace('_',' ',$statusKey)), 'color' => 'secondary', 'icon' => 'mdi-circle-outline'];
            
            $statusTitle = $currHst['label'];
            $statusColor = $currHst['color'];
            $statusIcon  = $currHst['icon'];
            $statusNote  = $lastHistory?->note;
            $statusTime  = $lastHistory?->created_at;
        ?>
        <div class="card mb-3 border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-light border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi <?php echo e($statusIcon); ?> text-<?php echo e($statusColor); ?> fs-5"></i>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 13px;">Latest Status</h6>
                </div>
                <span class="badge bg-<?php echo e($statusColor); ?> px-2.5 py-1 text-uppercase fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                    <?php echo e($statusTitle); ?>

                </span>
            </div>
            <div class="card-body p-3">
                <div class="p-3 rounded-3 mb-3" style="background: rgba(var(--bs-<?php echo e($statusColor); ?>-rgb), 0.08); border: 1px dashed rgba(var(--bs-<?php echo e($statusColor); ?>-rgb), 0.3);">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="fw-bold text-<?php echo e($statusColor); ?>" style="font-size: 13px;">
                            <i class="mdi <?php echo e($statusIcon); ?> me-1"></i><?php echo e($statusTitle); ?>

                        </span>
                        <?php if($statusTime): ?>
                            <small class="text-muted" style="font-size: 10px;" title="<?php echo e($statusTime->format('d M Y H:i')); ?>">
                                <i class="mdi mdi-clock-outline me-1"></i><?php echo e($statusTime->diffForHumans()); ?>

                            </small>
                        <?php endif; ?>
                    </div>
                    <?php if($statusNote): ?>
                        <div class="mt-2 text-dark small" style="white-space: pre-wrap; line-height: 1.5; font-size: 12px;">
                            <i class="mdi mdi-text-subject me-1 text-muted"></i><?php echo e($statusNote); ?>

                        </div>
                    <?php else: ?>
                        <div class="mt-1 text-muted small" style="font-size: 11px; font-style: italic;">
                            No additional note provided for this status update.
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="d-flex flex-column gap-2" style="font-size: 12px;">
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                        <span class="text-muted"><i class="mdi mdi-account-outline me-1"></i> Sales Person</span>
                        <span class="fw-semibold text-dark"><?php echo e($quote->sales?->name ?? '-'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                        <span class="text-muted"><i class="mdi mdi-calendar-outline me-1"></i> Issue Date</span>
                        <span class="fw-semibold text-dark"><?php echo e($quote->date?->format('d M Y') ?? '-'); ?></span>
                    </div>
                    <?php if($quote->no_pr): ?>
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted"><i class="mdi mdi-pound me-1"></i> PR Number</span>
                            <span class="fw-semibold text-dark"><?php echo e($quote->no_pr); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if($quote->po_number): ?>
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted"><i class="mdi mdi-clipboard-text-outline me-1"></i> PO Number</span>
                            <span class="fw-semibold text-success"><?php echo e($quote->po_number); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if($quote->payment_method): ?>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="mdi mdi-credit-card-outline me-1"></i> Payment Method</span>
                            <span class="fw-semibold text-dark"><?php echo e($quote->payment_method); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>



    </div>
    

</div>


<div class="modal fade" id="modalUploadPO" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="mdi mdi-upload me-1"></i> Upload Purchase Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('unit-quotation.upload-po', $quote->id)); ?>" method="POST"
                  enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. PO <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="po_number"
                               placeholder="Masukkan nomor PO dari customer"
                               value="<?php echo e(old('po_number', $quote->po_number)); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select" name="payment_method" id="select-payment-method" required>
                            <option value="" disabled selected>-- Pilih Metode Pembayaran --</option>
                            <option value="CBD">CBD (Cash Before Delivery)</option>
                            <option value="COD">COD (Cash On Delivery)</option>
                            <option value="DP 50% & Pelunasan NET 30">DP 50% &amp; Pelunasan NET 30</option>
                            <option value="DP 30% & Pelunasan NET 30">DP 30% &amp; Pelunasan NET 30</option>
                            <option value="Tempo">Tempo</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="tempo-days-group">
                        <label class="form-label fw-semibold">Jangka Tempo (hari) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="input-tempo-days"
                                   min="1" placeholder="misal: 30">
                            <span class="input-group-text">Hari</span>
                        </div>
                        <div class="form-text text-muted">Masukkan jumlah hari jangka tempo pembayaran.</div>
                    </div>
                    
                    <input type="hidden" name="payment_method_final" id="input-payment-method-final">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Invoice Pertama <span class="text-danger">*</span></label>
                        <select class="form-select" name="invoice_type" id="select-invoice-type" required>
                            <option value="" disabled selected>-- Pilih --</option>
                            <option value="DP">Down Payment (DP)</option>
                            <option value="CT">Full Payment</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="dp-percent-group">
                        <label class="form-label fw-semibold">Persentase DP <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="dp_percent" id="dp-percent-input"
                                   min="1" max="99" value="50" placeholder="50">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text" id="dp-amount-preview">DP: Rp ... | Sisa: Rp ...</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File PO (PDF) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="po_file" accept=".pdf" required>
                        <div class="form-text">Maksimal 5MB, format PDF.</div>
                    </div>
                    <div class="alert alert-info mb-0 py-2">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Status quotation akan otomatis berubah ke <strong>PO Received</strong> setelah upload.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-upload me-1"></i> Upload & Konfirmasi PO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php if(!isset($sellingContract) || !$sellingContract): ?>
<div class="modal fade" id="modalSellingContractUnit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo e(route('unit-quotation.selling-contract', $quote->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header border-0">
                    <h5 class="modal-title">Create Selling Contract</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <h5 class="mb-1"><?php echo e($quote->no_quote); ?></h5>
                    <p class="text-muted mb-3"><?php echo e($quote->client?->company); ?></p>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold">No. Selling Contract</label>
                        <input type="text" class="form-control" name="no_contract"
                            value="<?php echo e($formattedNumberSC); ?>/<?php echo e($quote->tax ? 'P' : 'NP'); ?>/SELLCTX/RJO/<?php echo e($thisYear); ?>"
                            required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary waves-effect">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>


<?php if(isset($issuedInvoices) && $issuedInvoices->isNotEmpty() && isset($remaining) && $remaining > 0): ?>
<div class="modal fade" id="modalRequestNextInvoice" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="mdi mdi-plus-circle-outline me-1"></i> Ajukan Invoice Selanjutnya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('unit-quotation.request-next-invoice', $quote->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Sisa yang belum ditagih</label>
                        <div class="form-control bg-light fw-bold">Rp <?php echo e(number_format($remaining, 0, ',', '.')); ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan Invoice <span class="text-danger">*</span></label>
                        <select class="form-select" name="label" id="next-inv-label" required>
                            <option value="Balance Payment">Balance Payment</option>
                            <option value="Down Payment 2">Down Payment 2</option>
                            <option value="Down Payment 3">Down Payment 3</option>
                            <option value="Pelunasan">Pelunasan</option>
                            <option value="__custom__">Lainnya (isi sendiri)...</option>
                        </select>
                        <input type="text" class="form-control mt-2 d-none" id="next-inv-label-custom"
                               placeholder="Tulis keterangan invoice..." maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Persentase dari sisa <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="percent" id="next-inv-percent"
                                   min="1" max="100" value="100" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text" id="next-inv-amount">
                            = Rp <?php echo e(number_format($remaining, 0, ',', '.')); ?>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-send-outline me-1"></i> Ajukan Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>


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
                        <select class="form-select" name="type" id="add-payment-type" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="DP">DP (Down Payment)</option>
                            <option value="BP">BP (Balance Payment)</option>
                            <option value="CBD">CBD (Cash Before Delivery)</option>
                            <option value="COD">COD (Cash On Delivery)</option>
                            <option value="Tempo">Tempo</option>
                        </select>
                    </div>
                    <div class="mb-3" id="tempo-group" style="display:none;">
                        <label class="form-label fw-semibold">Jangka Tempo (hari) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="tempo" min="1" placeholder="misal: 30">
                            <span class="input-group-text">hari</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-select" name="method" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="Transfer">Transfer</option>
                            <option value="Cash">Cash</option>
                            <option value="Giro">Giro</option>
                            <option value="Escrow">Escrow</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah (IDR) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="amount"
                                   min="1" step="1" placeholder="Masukkan jumlah yang diterima" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Persentase <span class="text-muted small">(opsional)</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="percent"
                                   min="1" max="100" placeholder="misal 50">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan <span class="text-muted small">(opsional)</span></label>
                        <input type="text" class="form-control" name="note"
                               placeholder="misal: Down Payment, Pelunasan...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-check me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="modalUploadProof" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="mdi mdi-upload me-1"></i> Upload Bukti Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">File Bukti <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="proof-file-input"
                           accept=".pdf,.jpg,.jpeg,.png">
                    <div class="form-text">PDF / JPG / PNG, maks 5MB</div>
                </div>
                <div id="proof-upload-msg"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btn-do-upload-proof">
                    <i class="mdi mdi-upload me-1"></i> Upload
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalChangeStatus" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('unit-quotation.change-status', $quote->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-floating form-floating-outline">
                        <select class="form-select" name="status">
                            <option value="sent"         <?php echo e($quote->status === 'sent'         ? 'selected' : ''); ?>>Sent</option>
                            <option value="negotiation"  <?php echo e($quote->status === 'negotiation'  ? 'selected' : ''); ?>>Negotiation</option>
                            <option value="revision"     <?php echo e($quote->status === 'revision'     ? 'selected' : ''); ?>>Revisi</option>
                            <option value="hot_prospect" <?php echo e($quote->status === 'hot_prospect' ? 'selected' : ''); ?>>Hot Prospect</option>
                            <option value="po_received"  <?php echo e($quote->status === 'po_received'  ? 'selected' : ''); ?>>PO Received</option>
                            <option value="loss"         <?php echo e($quote->status === 'loss'         ? 'selected' : ''); ?>>Loss</option>
                        </select>
                        <label>Status</label>
                    </div>
                    <div class="form-floating form-floating-outline mt-3">
                        <textarea class="form-control" name="note" style="height:80px" placeholder="Note (optional)"></textarea>
                        <label>Note (optional)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php echo $__env->make('components.modal.viewer.pdf', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php if($quote->status === 'po_received' && $pendingPo): ?>
    <?php echo $__env->make('components.modal.unit-quotation.convert-po', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice.css" />
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script'); ?>
<script>
    var totalQuote = <?php echo e($quote->total); ?>;

    
    <?php if(session('open_convert_po') && $pendingPo): ?>
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('convertPoUnit');
        if (modalEl) {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
    <?php endif; ?>

    function updateDpPreview() {
        var pct = parseFloat($('#dp-percent-input').val()) || 50;
        pct = Math.min(99, Math.max(1, pct));
        var dpAmt  = Math.round(totalQuote * pct / 100);
        var remAmt = totalQuote - dpAmt;
        $('#dp-amount-preview').text(
            'DP: Rp ' + dpAmt.toLocaleString('id-ID') +
            ' | Sisa: Rp ' + remAmt.toLocaleString('id-ID')
        );
    }

    // Add Payment modal — show Tempo field only when type=Tempo
    $('#add-payment-type').on('change', function () {
        if ($(this).val() === 'Tempo') {
            $('#tempo-group').show().find('input').prop('required', true);
        } else {
            $('#tempo-group').hide().find('input').prop('required', false).val('');
        }
    });

    // Upload PO modal — Payment Method dropdown logic
    $('#select-payment-method').on('change', function () {
        var val = $(this).val();
        var $tempoDays = $('#tempo-days-group');
        var $tempoDaysInput = $('#input-tempo-days');
        if (val === 'Tempo') {
            $tempoDays.removeClass('d-none');
            $tempoDaysInput.prop('required', true);
        } else {
            $tempoDays.addClass('d-none');
            $tempoDaysInput.prop('required', false).val('');
        }
    });

    // Before submit Upload PO: build final payment_method value
    $('#modalUploadPO form').on('submit', function (e) {
        var method = $('#select-payment-method').val();
        var finalValue = method;

        if (method === 'Tempo') {
            var days = parseInt($('#input-tempo-days').val());
            if (!days || days < 1) {
                e.preventDefault();
                $('#input-tempo-days').focus();
                alert('Masukkan jumlah hari untuk metode Tempo.');
                return false;
            }
            finalValue = 'Tempo ' + days + ' Hari';
        }

        // Set nilai final ke hidden input & rename agar server membaca dari sini
        $('#select-payment-method').prop('name', '');
        $('input[name="payment_method_final"]').attr('name', 'payment_method').val(finalValue);
    });

    // Invoice type toggle — show DP% only when DP selected
    $('#select-invoice-type').on('change', function () {
        var $dpGroup = $('#dp-percent-group');
        var $dpInput = $('#dp-percent-input');
        if ($(this).val() === 'DP') {
            $dpGroup.removeClass('d-none');
            $dpInput.prop('required', true);
            updateDpPreview();
        } else {
            $dpGroup.addClass('d-none');
            $dpInput.prop('required', false);
        }
    });

    $('#dp-percent-input').on('input', updateDpPreview);

    // Next invoice — label custom toggle
    $('#next-inv-label').on('change', function () {
        var $custom = $('#next-inv-label-custom');
        if ($(this).val() === '__custom__') {
            $custom.removeClass('d-none').prop('required', true);
        } else {
            $custom.addClass('d-none').prop('required', false).val('');
        }
    });

    // Before submit next invoice: swap __custom__ label
    $('#modalRequestNextInvoice form').on('submit', function () {
        var $sel = $('#next-inv-label');
        if ($sel.val() === '__custom__') {
            var custom = $('#next-inv-label-custom').val().trim();
            if (!custom) { $('#next-inv-label-custom').focus(); return false; }
            $sel.append('<option value="' + custom + '" selected></option>');
            $sel.val(custom);
        }
    });

    // Request Selling Contract (Sales)
    $(document).on('click', '.request-selling-unit', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Request Selling Contract?',
            text: 'Permintaan akan dikirim ke accounting untuk diproses.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Request',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.post('<?php echo e(url('unit-quotation')); ?>/' + id + '/request-selling-contract', {
                    _token: '<?php echo e(csrf_token()); ?>'
                }, function (response) {
                    if (response == 1) {
                        Swal.fire({
                            icon: 'success', title: 'Requested!',
                            text: 'Permintaan Selling Contract telah dikirim.',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                            buttonsStyling: false,
                        }).then(function () { location.reload(); });
                    }
                });
            }
        });
    });

    // Next invoice modal — live amount preview
    <?php if(isset($remaining) && $remaining > 0): ?>
    var remainingAmount = <?php echo e($remaining); ?>;
    $('#next-inv-percent').on('input', function () {
        var pct = parseFloat($(this).val()) || 100;
        pct = Math.min(100, Math.max(1, pct));
        var amt = Math.round(remainingAmount * pct / 100);
        $('#next-inv-amount').text('= Rp ' + amt.toLocaleString('id-ID'));
    });
    <?php endif; ?>

    // Upload Proof Payment
    var currentProofPaymentId = null;
    $(document).on('click', '.btn-upload-proof', function () {
        currentProofPaymentId = $(this).data('id');
        $('#proof-file-input').val('');
        $('#proof-upload-msg').html('');
        new bootstrap.Modal(document.getElementById('modalUploadProof')).show();
    });

    $('#btn-do-upload-proof').on('click', function () {
        var file = $('#proof-file-input')[0].files[0];
        if (!file) {
            $('#proof-upload-msg').html('<div class="alert alert-warning py-2">Pilih file terlebih dahulu.</div>');
            return;
        }
        var fd = new FormData();
        fd.append('file', file);
        fd.append('_token', '<?php echo e(csrf_token()); ?>');
        $(this).prop('disabled', true).text('Uploading...');
        $.ajax({
            url: '/unit-quotation/payment/' + currentProofPaymentId + '/proof',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (res) {
                $('#proof-upload-msg').html('<div class="alert alert-success py-2">Berhasil diupload!</div>');
                setTimeout(function () { location.reload(); }, 1000);
            },
            error: function () {
                $('#proof-upload-msg').html('<div class="alert alert-danger py-2">Gagal upload. Cek ukuran/format file.</div>');
                $('#btn-do-upload-proof').prop('disabled', false).text('Upload');
            }
        });
    });

    // Delete Payment
    $(document).on('click', '.btn-delete-payment', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus payment ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/unit-quotation/payment/' + id,
                    type: 'POST',
                    data: { _method: 'DELETE', _token: '<?php echo e(csrf_token()); ?>' },
                    success: function (res) {
                        if (res == 1) {
                            $('#pay-row-' + id).fadeOut(300, function () { $(this).remove(); });
                        }
                    }
                });
            }
        });
    });

    $(document).on('click', '.delete-quote', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.post('<?php echo e(url('unit-quotation')); ?>/' + id, {
                    _method: 'DELETE',
                    _token: '<?php echo e(csrf_token()); ?>'
                }, function (response) {
                    if (response == 1) {
                        Swal.fire({
                            icon: 'success', title: 'Deleted!', text: 'Quotation has been deleted.',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                        }).then(function () {
                            window.location.href = '<?php echo e(route('quotation.index')); ?>';
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '.ajukan-suo-unit', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Ajukan SUO dari penawaran ini?',
            text: 'SUO baru akan dibuat otomatis berisi item dari penawaran unit ini.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ajukan SUO',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                cancelButton: 'btn btn-outline-secondary waves-effect'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo e(url("suo/from-unit-quotation")); ?>/' + id,
                    data: { _token: '<?php echo e(csrf_token()); ?>' },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'SUO dibuat!',
                                text: 'SUO berhasil diajukan dari penawaran unit ini.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function () {
                                window.location.href = '/suo/' + response.suo_id;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal mengajukan SUO.'
                            });
                        }
                    },
                    error: function (xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal mengajukan SUO.';
                        Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                    }
                });
            }
        });
    });

    // Tambah komentar baru
    $('#form-add-comment').on('submit', function (e) {
        e.preventDefault();
        var text = $('#new-comment-text').val().trim();
        if (!text) return;
        $('#btn-submit-comment').prop('disabled', true);
        $.ajax({
            type: 'POST',
            url: '<?php echo e(route('unit-quotation.storeComment', $quote->id)); ?>',
            data: { comment: text, _token: '<?php echo e(csrf_token()); ?>' },
            success: function () {
                location.reload();
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal mengirim komentar.' });
                $('#btn-submit-comment').prop('disabled', false);
            }
        });
    });

    // Edit komentar
    $(document).on('click', '.btn-edit-comment', function () {
        var $item = $(this).closest('.timeline-item');
        var $p = $item.find('.comment-text');
        var currentText = $p.text();

        Swal.fire({
            title: 'Edit Komentar',
            input: 'textarea',
            inputValue: currentText,
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed && result.value.trim()) {
                var id = $item.data('comment-id');
                $.ajax({
                    type: 'POST',
                    url: '<?php echo e(url('unit-quotation/comments')); ?>/' + id + '/update',
                    data: { comment: result.value.trim(), _token: '<?php echo e(csrf_token()); ?>' },
                    success: function () {
                        location.reload();
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal mengubah komentar.' });
                    }
                });
            }
        });
    });

    // Hapus komentar
    $(document).on('click', '.btn-delete-comment', function () {
        var $item = $(this).closest('.timeline-item');
        var id = $item.data('comment-id');
        Swal.fire({
            title: 'Hapus komentar ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo e(url('unit-quotation/comments')); ?>/' + id,
                    data: { _method: 'DELETE', _token: '<?php echo e(csrf_token()); ?>' },
                    success: function () {
                        location.reload();
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menghapus komentar.' });
                    }
                });
            }
        });
    });

    // Filter Pills Handler for Activity Feed (Status, Comment, Revision)
    $(document).on('click', '.filter-pill', function() {
        $('.filter-pill').removeClass('btn-primary active').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('btn-primary active');
        
        var filter = $(this).data('filter');
        if (filter === 'all') {
            $('.timeline-feed-item').fadeIn(200);
        } else if (filter === 'status') {
            $('.timeline-feed-item.item-status').fadeIn(200);
            $('.timeline-feed-item.item-comment, .timeline-feed-item.item-revision').hide();
        } else if (filter === 'comment') {
            $('.timeline-feed-item.item-comment').fadeIn(200);
            $('.timeline-feed-item.item-status, .timeline-feed-item.item-revision').hide();
        } else if (filter === 'revision') {
            $('.timeline-feed-item.item-revision').fadeIn(200);
            $('.timeline-feed-item.item-status, .timeline-feed-item.item-comment').hide();
        }
    });

    // Initialize Tooltips for Stock Badges
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    }
    if (typeof $.fn.tooltip !== 'undefined') {
        $('[data-bs-toggle="tooltip"]').tooltip({ html: true });
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/unit-quotation/detail.blade.php ENDPATH**/ ?>