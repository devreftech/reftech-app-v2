<?php $__env->startSection('title', $quote->no_quote . ($quote->client?->company ? ' - ' . $quote->client->company : '')); ?>
<div class="invoice-print">
    <div class="container-fluid flex-grow-1">

        
        <div class="header-row d-flex justify-content-between align-items-start mb-0 pb-1" style="display:flex !important; flex-direction:row !important; justify-content:space-between !important; align-items:flex-start !important;">
            <div class="pb-1">
                <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
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
            <div class="text-end" style="align-self: flex-start;">
                <h3 class="fw-bold mb-1 mt-0" style="letter-spacing:2px; color:#696cff; margin-top:0 !important; line-height:1.1;">QUOTATION</h3>
                <p class="mb-1 fw-bold text-dark" style="font-size:16px;">#<?php echo e($quote->no_quote); ?></p>
                <p class="mb-1 fw-bold" style="font-size:13px; color:#0f172a !important;">
                    <i class="mdi mdi-calendar-blank-outline me-1 text-primary"></i><?php echo e($quote->date?->format('d-m-Y')); ?>

                </p>
                <?php if($quote->no_pr): ?>
                    <p class="mb-0" style="font-size:11px; color:#888;">No. PR: <?php echo e($quote->no_pr); ?></p>
                <?php endif; ?>
            </div>
        </div>

        
        <div style="height:3px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:14px 0 18px;"></div>

        
        <div style="display:flex !important; display:-webkit-flex !important; align-items:stretch !important; gap:16px; margin-bottom:18px; font-size:12px;">
            <div style="flex:1; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:12px 16px; background:#fafafa;">
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
            <div style="min-width:250px; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:12px 16px; background:#fafafa;">
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
                'bar'=>' Bar','air_cap'=>' m³/min',
                'filtration'=>' µm','oil_content'=>' ppm',
                'test_pressure'=>' Bar','inlet_pressure'=>' Bar','outlet_pressure'=>' Bar',
                'inlet_cap'=>' m³/min','outlet_cap'=>' m³/min',
                'weight'=>' Kg','capacity'=>' Liter',
            ];
            $specLabelsOverride = [
                'AIR RECEIVER TANK' => [
                    'bar'     => 'Max. Pressure',
                    'grade'   => 'T Plate',
                    'cooling' => 'Certification',
                ],
                'FILTRATION SYSTEM' => [
                    'air_cap'  => 'Flowrate',
                    'material' => 'Element',
                    'connect'  => 'Drain',
                ],
            ];
            $hasDisc = $quote->details->where('disc', '>', 0)->count() > 0;
        ?>

        <table class="table table-bordered m-0 mb-3" style="width:100%; font-size:12px;">
            <thead style="font-size:11px; background:#eeeeff; color:#3d3d8f;">
                <tr>
                    <th class="text-center" style="width:4%; padding:10px 8px; font-weight:700; border-color:#d0d0ff;">No.</th>
                    <th class="text-center" style="width:<?php echo e($hasDisc ? '43%' : '48%'); ?>; padding:10px 10px; font-weight:700; border-color:#d0d0ff;">Item Description</th>
                    <th class="text-center" style="width:9%; padding:10px 8px; font-weight:700; border-color:#d0d0ff;">Qty</th>
                    <th class="text-center" style="width:18%; padding:10px 10px; font-weight:700; border-color:#d0d0ff;">Price (IDR)</th>
                    <?php if($hasDisc): ?>
                        <th class="text-center" style="width:6%; padding:10px 6px; font-weight:700; border-color:#d0d0ff;">Disc</th>
                    <?php endif; ?>
                    <th class="text-center" style="width:<?php echo e($hasDisc ? '20%' : '21%'); ?>; padding:10px 10px; font-weight:700; border-color:#d0d0ff;">Total (IDR)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $itemNo = 1;
                    $headerCount = 0;
                ?>
                <?php $__currentLoopData = $quote->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($item->type === 'header' || $item->type === 'heading'): ?>
                        <?php
                            $lbl = trim($item->label ?? '');
                            if (!preg_match('/^[A-Z0-9][\.\)]/i', $lbl)) {
                                $lbl = chr(65 + ($headerCount % 26)) . '. ' . $lbl;
                            }
                            $headerCount++;
                        ?>
                        <tr class="table-section-header" style="background:#f4f4fe;">
                            <td colspan="<?php echo e($hasDisc ? 6 : 5); ?>" style="padding:7px 12px; font-weight:700; font-size:11px; color:#3d3d8f; border-color:#d0d0ff; text-transform:uppercase; letter-spacing:.5px;">
                                <i class="mdi mdi-bookmark-outline me-1"></i><?php echo e($lbl); ?>

                            </td>
                        </tr>
                    <?php else: ?>
                        <tr style="background:#fff; color:#111;">
                            <td class="align-top text-center" style="padding:8px 8px; color:#111; font-weight:600;"><?php echo e($itemNo++); ?></td>
                            <td class="align-top" style="padding:8px 10px;">
                                <?php if($item->type === 'unit' && $item->unit): ?>
                                    <p class="mb-1 fw-bold" style="font-size:12px; color:#111;">
                                        <?php echo e($item->label ?: ($item->unit->brand . ' ' . $item->unit->sku . ($item->unit->model ? ' — ' . $item->unit->model : ''))); ?>

                                    </p>
                                    <?php
                                        $specs = $item->getSpecVisibleArray();
                                        $category = $item->unit->unit ?? '';
                                        $catOverride = $specLabelsOverride[$category] ?? [];
                                    ?>
                                    <?php if(!empty($specs)): ?>
                                        <div style="font-size:10px; color:#222; margin-top:3px;">
                                            <?php $__currentLoopData = $specs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($field === 'unit'): ?> <?php continue; ?> <?php endif; ?>
                                                <?php
                                                    $val = $item->unit->$field ?? null;
                                                    $label = $catOverride[$field] ?? $specLabels[$field] ?? $field;
                                                ?>
                                                <?php if($val && isset($specLabels[$field])): ?>
                                                    <div style="display:flex; padding:1px 0;">
                                                        <span style="color:#444; font-weight:600; min-width:110px; flex-shrink:0;"><?php echo e($label); ?></span>
                                                        <span style="color:#111; font-weight:500;">: <?php echo e($val); ?><?php echo e($specUnits[$field] ?? ''); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>
                                <?php elseif($item->id_equivalent && $item->equivalent): ?>
                                    <p class="mb-1 fw-bold" style="font-size:12px; color:#111;">
                                        <?php echo e(preg_replace('/^[\s\-\*\•]+/u', '', trim(($item->equivalent->brand ?? '') . ($item->equivalent->pn ? ' — ' . $item->equivalent->pn : '')) ?: $item->label)); ?>

                                    </p>
                                    <?php if(optional($item->equivalent->product)->description): ?>
                                        <p class="mb-0" style="font-size:10px; color:#333;"><?php echo e(preg_replace('/^[\s\-\*\•]+/u', '', $item->equivalent->product->description)); ?></p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="mb-0 fw-bold" style="font-size:12px; color:#111;"><?php echo e(preg_replace('/^[\s\-\*\•]+/u', '', $item->label)); ?></p>
                                    <?php if($item->description): ?>
                                        <?php
                                            $descLines = explode("\n", str_replace("\r", "", $item->description));
                                        ?>
                                        <div style="font-size:10px; color:#333; margin-top:3px; line-height:1.4;">
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
                                                            <span style="flex-shrink:0; min-width:14px; color:#555; font-weight:600;"><?php echo e($dMatches[1]); ?></span>
                                                            <span style="flex:1;"><?php echo e($dMatches[2]); ?></span>
                                                        </div>
                                                    <?php else: ?>
                                                        <div style="margin-bottom:2px; font-weight:600; color:#111;"><?php echo e($dLine); ?></div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="align-top text-center" style="padding:8px 8px; color:#222; font-weight:500;"><?php echo e((int) $item->qty); ?> <?php echo e($item->info_qty ?? 'Unit'); ?></td>
                            <td class="align-top text-end" style="padding:8px 10px; color:#111; font-weight:500;"><?php echo e(number_format($item->price, 0, '', '.')); ?></td>
                            <?php if($hasDisc): ?>
                                <td class="align-top text-center" style="padding:8px 6px; color:#111;"><?php echo e($item->disc > 0 ? (int) $item->disc . '%' : '-'); ?></td>
                            <?php endif; ?>
                            <td class="align-top text-end" style="padding:8px 10px; font-weight:700; color:#111;"><?php echo e(number_format($item->amount, 0, '', '.')); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        
        <?php
            $afterDisc = $quote->diskon > 0
                ? $quote->subtotal - $quote->discount_amount
                : $quote->subtotal;
        ?>
        <div class="d-flex justify-content-end mb-3" style="page-break-inside: avoid !important; break-inside: avoid !important;">
            <div style="min-width:270px; font-size:11px; border:1px solid #d0d0ff; border-left:4px solid #696cff; border-radius:6px; overflow:hidden; background:#fff;">
                    <table style="width:100%; border-collapse:collapse; font-size:11px;">
                        <tr>
                            <td style="padding:5px 14px 5px 12px; color:#555;">Subtotal</td>
                            <td style="padding:5px 12px 5px 0; text-align:right; font-weight:600; color:#222;">Rp <?php echo e(number_format($quote->subtotal, 0, '', '.')); ?></td>
                        </tr>
                        <?php if($quote->diskon > 0): ?>
                            <tr style="border-top:1px solid #eeeeff;">
                                <td style="padding:5px 14px 5px 12px; color:#555;">Discount<?php echo e($quote->discount_label ? ' ' . $quote->discount_label : ''); ?></td>
                                <td style="padding:5px 12px 5px 0; text-align:right; font-weight:600; color:#dc3545;">- Rp <?php echo e(number_format($quote->discount_amount, 0, '', '.')); ?></td>
                            </tr>
                            <tr style="border-top:1px solid #eeeeff;">
                                <td style="padding:5px 14px 5px 12px; color:#555;">After Discount</td>
                                <td style="padding:5px 12px 5px 0; text-align:right; font-weight:600; color:#222;">Rp <?php echo e(number_format($afterDisc, 0, '', '.')); ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr style="border-top:1px solid #eeeeff;">
                            <td style="padding:5px 14px 5px 12px; color:#555;">Tax <?php echo e($quote->tax ? '(12%)' : ''); ?></td>
                            <td style="padding:5px 12px 5px 0; text-align:right; font-weight:600; color:#222;">
                                <?php echo e($quote->tax ? 'Rp ' . number_format($quote->tax_amount, 0, '', '.') : '-'); ?>

                            </td>
                        </tr>
                        <?php if($quote->shipping > 0): ?>
                            <tr style="border-top:1px solid #eeeeff;">
                                <td style="padding:5px 14px 5px 12px; color:#555;">Shipping Cost</td>
                                <td style="padding:5px 12px 5px 0; text-align:right; font-weight:600; color:#222;">Rp <?php echo e(number_format($quote->shipping, 0, '', '.')); ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr style="border-top:2px solid #d0d0ff; background:#f0f0ff;">
                            <td style="padding:7px 14px 7px 12px; font-weight:700; font-size:12px; color:#3d3d8f;">TOTAL PRICE</td>
                            <td style="padding:7px 12px 7px 0; text-align:right; font-weight:700; font-size:12px; color:#696cff;">Rp <?php echo e(number_format($quote->total, 0, '', '.')); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            
            <?php if($quote->note): ?>
            <div style="border:1px solid #e0e0e0; border-left:3px solid #696cff; border-radius:6px; padding:10px 14px; font-size:11px; color:#333; margin-bottom:14px; background:#fafafa; page-break-inside: avoid !important; break-inside: avoid !important;">
                <p class="mb-1 fw-semibold" style="font-size:10px; color:#888; text-transform:uppercase; letter-spacing:.5px;">Remarks</p>
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

            
            <div style="border:1px solid #e0e0e0; border-radius:6px; padding:12px 16px; font-size:11px; background:#fff; margin-bottom:16px; page-break-inside: avoid !important; break-inside: avoid !important;">
                <p class="mb-2 fw-semibold" style="font-size:10px; text-transform:uppercase; letter-spacing:.5px; color:#888;">Term &amp; Condition</p>
                <table style="width:100%; border-collapse:collapse; font-size:11px;">
                    <tr>
                        <td style="width:150px; padding:3px 0; color:#555; vertical-align:top;">Validity of Quotation</td>
                        <td style="padding:3px 0; color:#222; vertical-align:top;">: <?php echo e($quote->validity ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:3px 0; color:#555; vertical-align:top;">Price</td>
                        <td style="padding:3px 0; color:#222; vertical-align:top;">: <?php echo e($quote->pricing ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:3px 0; color:#555; vertical-align:top;">Payment</td>
                        <td style="padding:3px 0; color:#222; vertical-align:top;">: <?php echo e($quote->payment ?? '-'); ?></td>
                    </tr>
                    <?php if($quote->warranty): ?>
                        <tr>
                            <td style="padding:3px 0; color:#555; vertical-align:top;">Warranty</td>
                            <td style="padding:3px 0; color:#222; vertical-align:top;">: <?php echo e($quote->warranty); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php
                        $deliveryLines = array_filter(preg_split('/\r?\n/', $quote->delivery_process ?? '-'), fn($l) => trim($l) !== '');
                        $deliveryText = count($deliveryLines) > 1
                            ? implode("\n", array_map(fn($l) => '• ' . trim($l), $deliveryLines))
                            : ($quote->delivery_process ?? '-');
                    ?>
                    <tr>
                        <td style="padding:3px 0; color:#555; vertical-align:top;">Delivery Process</td>
                        <td style="padding:3px 0; color:#222; vertical-align:top;">
                            <div style="display:flex; align-items:flex-start;">
                                <span style="flex-shrink:0;">:&nbsp;</span>
                                <span style="white-space:pre-line;"><?php echo e($deliveryText); ?></span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            

            
            <div style="border-top:2px solid #eeeeff; padding-top:14px; margin-top:12px;">
                <p class="text-center mb-2" style="font-size:11px; color:#aaa; font-style:italic;">
                    Thank you for your business. We look forward to your continued partnership.
                </p>
                <div class="d-flex justify-content-between align-items-end" style="font-size:12px; color:#555;">
                    <div>
                        <p class="mb-0 fw-bold" style="font-size:11px; color:#696cff; text-transform:uppercase; letter-spacing:.5px;">Compressed Air Solution :</p>
                        <p class="mb-0 fw-medium" style="font-size:11px; color:#444;">
                            Sales &nbsp;|&nbsp; Rental &nbsp;|&nbsp; Maintenance &nbsp;|&nbsp; Air Audit &nbsp;|&nbsp; Installation
                        </p>
                    </div>
                    <div class="text-end" style="font-size:11px; color:#aaa;">
                        <p class="mb-0 fw-semibold" style="color:#696cff; font-size:12px;">PT Reftech Jaya Optima</p>
                        <p class="mb-0" style="color:#666; font-weight:500;">www.reftech.id/quotation &nbsp;|&nbsp; <?php echo e($quote->date?->format('d F Y')); ?></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php $__env->startPush('after-style'); ?>
    <style>
        @page {
            size: A4 portrait !important;
            margin: 15mm 15mm 15mm 15mm !important;
        }
        @media print {
            @page {
                size: A4 portrait !important;
                margin: 15mm 15mm 15mm 15mm !important;
            }
            html, body, .layout-wrapper, .layout-container, .layout-page, .content-wrapper, .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
                background: #fff !important;
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: none !important;
                position: static !important;
                overflow: visible !important;
                height: auto !important;
                min-height: auto !important;
            }
            .layout-menu, .layout-navbar, .content-backdrop, footer, .layout-menu-toggle {
                display: none !important;
            }
            .invoice-print {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            .table thead {
                display: table-header-group !important;
            }
            .table tbody tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .table-section-header {
                page-break-after: avoid !important;
                break-after: avoid !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .keep-together {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .signature-section {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
        @media screen {
            .invoice-print {
                max-width: 920px;
                margin: 24px auto;
                padding: 1.2cm 1.5cm !important;
                background: #fff;
                box-shadow: 0 4px 24px rgba(0,0,0,0.07);
                border-radius: 8px;
            }
            .container-fluid {
                padding: 0 !important;
            }
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script>
        document.title = <?php echo json_encode($quote->no_quote . ($quote->client?->company ? ' - ' . $quote->client->company : ''), 15, 512) ?>;
    </script>
    <script src="<?php echo e(asset('assets')); ?>/js/app-invoice-print.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/unit-quotation/print.blade.php ENDPATH**/ ?>