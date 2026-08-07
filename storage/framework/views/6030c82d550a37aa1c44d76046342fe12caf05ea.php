<?php $__env->startSection('title', $sellcon->no_contract); ?>

<div class="invoice-print p-4 text-black">
    <div class="container-fluid flex-grow-1 container-p-y">

        
        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
            <div class="mb-xl-0 pb-1">
                <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                    <span class="app-brand-logo demo">
                        <span style="color: var(--bs-primary)">
                            <img src="<?php echo e(asset('/asset')); ?>/logo/Reftech-Log.png" alt="" width="60%">
                        </span>
                    </span>
                </div>
                <p class="mb-1 fw-bolder" style="font-size: 15px">PT Reftech Jaya Optima</p>
                <div style="font-size: 13px">
                    <p class="mb-1">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                    <p class="mb-1">Bandung – Jawa Barat 40218</p>
                    <p class="mb-1">
                        <i class="mdi mdi-phone-outline me-1 mdi-13px"></i>022 54417653
                        &nbsp;&nbsp;
                        <i class="mdi mdi-email-outline me-1 mdi-13px"></i>info@reftech.id
                    </p>
                </div>
            </div>
            <div class="text-end">
                <h3 class="fw-bold">SELLING CONTRACT</h3>
                <div><span class="fw-bolder">#<?php echo e($sellcon->no_contract); ?></span></div>
                <div class="mt-1">
                    <span class="text-muted"><?php echo e(Carbon\Carbon::parse($sellcon->date)->format('d-m-Y')); ?></span>
                </div>
            </div>
        </div>

        <hr>

        
        <div class="mb-4">
            <h6 class="fw-semibold fs-4 mb-3">Quote to :</h6>
            <div class="row">
                <div class="col-2 fw-medium">
                    <p class="mb-1">Company</p>
                    <p class="mb-1">Name PIC</p>
                    <p class="mb-1">Phone</p>
                </div>
                <div class="col-4">
                    <p class="mb-1">: <?php echo e($unitQuote->client?->company ?? '-'); ?></p>
                    <p class="mb-1">: <?php echo e($unitQuote->pic?->name_pic ?? '-'); ?></p>
                    <p class="mb-1">: <?php echo e($unitQuote->client?->phone ?? '-'); ?></p>
                </div>
                <div class="col-3 fw-medium text-end">
                    <p class="mb-1">Seller :</p>
                    <p class="mb-1">Email :</p>
                </div>
                <div class="col-3 text-end">
                    <p class="mb-1">PT Reftech Jaya Optima</p>
                    <p class="mb-1"><?php echo e($unitQuote->client?->email ?? '-'); ?></p>
                </div>
            </div>
        </div>

        <hr>

        
        <div class="mb-4">
            <table class="table table-bordered m-0" style="width: 100%">
                <thead class="table-light border-top text-center">
                    <tr>
                        <th style="width: 3%">No.</th>
                        <th style="width: 52%">Item Description</th>
                        <th style="width: 10%">Qty</th>
                        <th style="width: 18%">Price (IDR)</th>
                        <th style="width: 17%">Amount (IDR)</th>
                    </tr>
                </thead>
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
                ?>
                <tbody>
                    <?php $__currentLoopData = $unitQuote->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr style="font-size: 13px">
                            <td class="align-top text-center"><?php echo e($i + 1); ?></td>
                            <td class="align-top">
                                <?php if($item->type === 'unit' && $item->unit): ?>
                                    <p class="mb-1 fw-semibold" style="font-size: 12px">
                                        <?php echo e($item->label ?: ($item->unit->brand . ' ' . $item->unit->model)); ?>

                                    </p>
                                    <?php $specs = $item->getSpecVisibleArray(); ?>
                                    <?php if(!empty($specs)): ?>
                                        <div style="font-size:10px; color:#555; font-family:Inter,sans-serif; margin-top:2px;">
                                            <?php $__currentLoopData = $specs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($field === 'unit'): ?> <?php continue; ?> <?php endif; ?>
                                                <?php $val = $item->unit->$field ?? null; ?>
                                                <?php if($val && isset($specLabels[$field])): ?>
                                                    <div style="display:flex; padding:1px 0;">
                                                        <span style="color:#888; min-width:110px; flex-shrink:0;"><?php echo e($specLabels[$field]); ?></span>
                                                        <span>: <?php echo e($val); ?><?php echo e($specUnits[$field] ?? ''); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="mb-0 fw-semibold" style="font-size: 12px"><?php echo e($item->label); ?></p>
                                    <?php if($item->description): ?>
                                        <div style="font-size: 10px; color: #555;"><?php echo e($item->description); ?></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="align-top text-center"><?php echo e($item->qty); ?> <?php echo e($item->info_qty ?? 'Unit'); ?></td>
                            <td class="align-top text-end"><?php echo e(number_format($item->price, 0, '', '.')); ?></td>
                            <td class="align-top text-end"><?php echo e(number_format($item->amount, 0, '', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <tr style="height: 8px;"><td colspan="5" style="font-size:1px;"> </td></tr>

                    
                    <?php
                        $afterDisc = $unitQuote->diskon > 0
                            ? $unitQuote->subtotal - $unitQuote->discount_amount
                            : $unitQuote->subtotal;
                    ?>
                    <tr>
                        <td colspan="2" rowspan="2" class="align-top py-4">
                            <span class="fw-semibold">Thanks for your business</span>
                        </td>
                        <td colspan="2" class="text-end py-0">
                            <p class="mb-2">Subtotal :</p>
                            <?php if($unitQuote->diskon > 0): ?>
                                <p class="mb-2">Discount<?php echo e($unitQuote->discount_label ? ' ' . $unitQuote->discount_label : ''); ?> :</p>
                                <p class="mb-2">Subtotal After Discount :</p>
                            <?php endif; ?>
                            <p class="mb-2">Tax <?php echo e($unitQuote->tax ? '(11%)' : ''); ?> :</p>
                        </td>
                        <td class="py-0">
                            <p class="fw-semibold mb-2 text-end">Rp <?php echo e(number_format($unitQuote->subtotal, 0, '', '.')); ?></p>
                            <?php if($unitQuote->diskon > 0): ?>
                                <p class="fw-semibold mb-2 text-end">- Rp <?php echo e(number_format($unitQuote->discount_amount, 0, '', '.')); ?></p>
                                <p class="fw-semibold mb-2 text-end">Rp <?php echo e(number_format($afterDisc, 0, '', '.')); ?></p>
                            <?php endif; ?>
                            <p class="fw-semibold mb-2 text-end"><?php echo e($unitQuote->tax ? 'Rp ' . number_format($unitQuote->tax_amount, 0, '', '.') : '0'); ?></p>
                        </td>
                    </tr>
                    <tr style="font-size: 14px;">
                        <td colspan="2" class="total" style="background-color: #E7FF00">
                            <p class="fw-bold mb-0 text-end">TOTAL PRICE, <?php echo e($unitQuote->tax ? 'INCLUDE' : 'EXCLUDE'); ?> VAT 11% :</p>
                        </td>
                        <td class="total" style="background-color: #E7FF00">
                            <p class="fw-bold mb-0 text-end">Rp <?php echo e(number_format($unitQuote->total, 0, '', '.')); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        
        <div class="mb-4">
            <h5 class="mb-3">Term & Condition</h5>
            <div class="row">
                <div class="col-3 fw-medium termc p-3">
                    <p class="mb-1">Validity Of Quotation</p>
                    <p class="mb-1">Price</p>
                    <p class="mb-1">Delivery Process</p>
                    <p class="mb-1">Payment</p>
                </div>
                <div class="col termc p-3">
                    <p class="mb-1">: <?php echo e($unitQuote->validity ?? '-'); ?></p>
                    <p class="mb-1">: <?php echo e($unitQuote->pricing ?? '-'); ?></p>
                    <p class="mb-1">: <?php echo e($unitQuote->delivery_process ?? '-'); ?></p>
                    <p class="mb-1">: <?php echo e($unitQuote->payment ?? '-'); ?></p>
                </div>
            </div>
        </div>

        
        <div class="row mt-3">
            <div class="col-4 my-5 text-center">
                <p class="fs-normal fw-medium">Authorized By,</p>
                <img src="<?php echo e(asset('/asset')); ?>/contract/sign-irene.jpeg" alt=""
                    style="width: 100px; height: 77px;">
                <p class="pt-3">Mrs. Irene</p>
                <p>PT. Reftech Jaya Optima</p>
            </div>
            <div class="col-4"></div>
            <div class="col-4 my-5 text-center">
                <p class="fs-normal fw-medium">Accepted By Customer,</p>
                <div class="pb-5"></div>
                <p class="pt-5"><?php echo e($unitQuote->pic?->name_pic ?? '-'); ?></p>
                <p><?php echo e($unitQuote->client?->company ?? '-'); ?></p>
            </div>
        </div>

    </div>
</div>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice-print.css" />
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/app-invoice-print.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/accounting/contract/detail-print-unit.blade.php ENDPATH**/ ?>