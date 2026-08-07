    
    <?php $__env->startSection('title', $invoice->no_invoice); ?>
    <div class="invoice-print p-4">
        <div class="container-fluid flex-grow-1 container-p-y">
            <?php if($quote->pic->client->info == 'Reftech'): ?>
                <div
                    class="d-flex justify-content-<?php echo e($quote->tax == 0 ? 'end' : 'between'); ?> flex-xl-row flex-md-column flex-sm-row flex-column">
                    <?php if($quote->tax != 0): ?>
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img class="text-md" src="<?php echo e(asset('/asset')); ?>/logo/Reftech-Log.png"
                                            alt="" srcset="" width="60%">
                                    </span>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="info">
                                    <p class="mb-1 fw-bolder">Office Address :</p>
                                    <div style="font-size: 10px">
                                        <p class="mb-1">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                                        <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                        <p class="mb-1">
                                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                                            54417653
                                            <?php echo e('   '); ?><i
                                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>accounting@reftech.id
                                        </p>
                                        <p class="mb-1">
                                        </p>
                                    </div>
                                </div>
                                <div class="npwp_add">
                                    <p class="mb-1 fw-bolder">NPWP Address :</p>
                                    <pre
                                        style="font-size: 10px; font-family: Inter, sans-serif; max-width: 250px; overflow-x: auto; white-space: pre-wrap;">Komp. Negla Kencana Residence Blok B, No.2 Pasanggrahan, Ujung Berung Kota Bandung - Jawa Barat 40199</pre>
                                    <p class="mb-1 text-black fw-medium p-1"
                                        style="background-color: rgb(224, 221, 255); font-size :10px;">
                                        NPWP : 73.728.571.8-429.000</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="text-end">
                        <h1 class="fw-bold" style="color: #2529fa; letter-spacing: 2px;">INVOICE</h1>
                        <div>
                            <span class="fw-bolder" style="font-size:18px"><?php echo e($invoice->no_invoice); ?></span>
                        </div>
                        <div class="mt-1">
                            <span class="fw-medium"><?php echo e(Carbon\Carbon::parse($invoice->date)->format('d-m-Y')); ?></span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div
                    class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column <?php echo e($quote->tax == 0 ? 'float-end' : ''); ?>">
                    <?php if($quote->tax != 0): ?>
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img class="text-md" src="<?php echo e(asset('/asset')); ?>/logo/Logo-update-size.png"
                                            alt="" srcset="" width="60%">
                                    </span>
                                </span>
                            </div>

                            <div class="d-flex justify-content-between">
                                <div class="info">
                                    <p class="mb-1 fw-bolder">Office Address :</p>
                                    <div style="font-size: 10px">
                                        <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                                        <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                        <p class="mb-1">
                                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62
                                            812-1000-0997
                                            <?php echo e(' | '); ?><i
                                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                                    </div>
                                </div>
                                <div class="npwp_add">
                                    <p class="mb-1 fw-bolder">NPWP Address :</p>
                                    <pre
                                        style="font-size: 10px; font-family: Inter, sans-serif; max-width: 250px; overflow-x: auto; white-space: pre-wrap;">Jl. Nancep No. 45, Setu Cisaat RT. 001 RW. 003 Cibening, Setu</pre>
                                    </p>
                                    <p class="mb-1 text-black fw-medium p-1"
                                        style="background-color: rgb(255, 235, 221)">
                                        NPWP : 96.484.859.2-413.000</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="text-end">
                        <h1 class="fw-bold" style="color: #696cff; letter-spacing: 2px;">INVOICE</h1>
                        <div>
                            <span class="fw-bolder">#<?php echo e($invoice->no_invoice); ?></span>
                        </div>
                        <div class="mt-1">
                            <span class="text-muted"><?php echo e(Carbon\Carbon::parse($invoice->date)->format('d-m-Y')); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <hr>
            <h5>Invoice To</h5>
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" style="border: 1px solid black;">
                    <tr>
                        <td rowspan="3" style="vertical-align: top; width: 60%;">
                            <div class="row">
                                <div class="col-2 fw-medium">
                                    <p class="mb-1">Bill To </p>
                                </div>
                                <div class="col-10">
                                    <p class="mb-1 fw-bolder">: <?php echo e($quote->pic->client->company); ?></p>
                                </div>
                                <div class="col-2 fw-medium">
                                    <p class="mb-1">PIC </p>
                                </div>
                                <div class="col-10">
                                    <p class="mb-1 fw-bolder">: <?php echo e($quote->pic->name_pic); ?></p>
                                </div>
                                <div class="col-2 fw-medium">
                                    <p class="mb-1">NPWP </p>
                                </div>
                                <div class="col-10">
                                    <p class="mb-1">: <?php echo e($quote->pic->client->npwp); ?></p>
                                </div>
                                <div class="col-2 fw-medium">
                                    <p class="mb-1">Phone </p>
                                </div>
                                <div class="col-10">
                                    <p class="mb-1">: <?php echo e($quote->pic->client->phone); ?></p>
                                </div>
                                <div class="col-2 fw-medium">
                                    <p class="mb-1">Address</p>
                                </div>
                                <div class="col-10">
                                    <?php if($invoice->invoiceTo == '1'): ?>
                                        <pre
                                            style="font-size: 13px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: <?php echo e($quote->pic->client->address); ?></pre>
                                    <?php else: ?>
                                        <pre
                                            style="font-size: 13px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: <?php echo e($quote->pic->client->subAddress); ?></pre>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p>Purchase Order</p>
                        </td>
                        <td>
                            <p class="fs-6 text-black fw-bold m-0"><?php echo e($invoice->no_po); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style=" background-color: #F9F9F9;" class="text-center">
                            <p class="fs-6 text-black fw-bold m-0">Term of Payment</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center" style="height: 10px">
                            <pre class="mb-0"
                                style="font-size: 13px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($invoice->term); ?></pre>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="mb-2">
                <?php if($quote->type == 'Sparepart'): ?>
                    <?php
                        $hasDisc     = $dquote->where('disc', '>', 0)->count() > 0;
                        $labelColspan = $hasDisc ? 3 : 2;
                    ?>
                    <table class="table table-bordered m-0"
                        style="border: 1px solid rgb(60, 60, 60); border-collapse: collapse;">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 1%">No.</th>
                                <th style="width: 35%">Item Description</th>
                                <th style="width: 15%">Price</th>
                                <th style="width: 10%">Qty</th>
                                <?php if($hasDisc): ?>
                                    <th style="width: 4%">Disc</th>
                                <?php endif; ?>
                                <?php if($quote->tax != 0): ?>
                                    <th style="width: 15%">DPP</th>
                                <?php endif; ?>
                                <th style="width: 25%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $totalPph = $invoice->pph ?? 0;
                                $no = 1;
                            ?>
                            <?php $__currentLoopData = $dquote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr style="font-size: 13px; border: none;">
                                    <td class="align-top" style="padding-bottom: 0px;">
                                        <p>
                                            <?php echo e($no); ?>

                                        </p>
                                        <?php
                                            $no++;
                                            $pph = ($product->amount * $product->pph) / 100;
                                            $totalPph += $pph;
                                            $dpp = ($product->amount * 11) / 12;
                                        ?>
                                    </td>
                                    <td class="text-wrap align-top" style="padding-bottom: 0px;">
                                        <p class="mb-0 fw-semibold" style="font-size: 12px">
                                            <?php echo e($product->equivalent->brand); ?> <?php echo e($product->equivalent->pn); ?>

                                        </p>
                                        <?php if($product->view == '1'): ?>
                                            <a href="<?php echo e($product->equivalent->image); ?>" target="_blank"
                                                class=" underline-line">Description Click Here</a>
                                        <?php else: ?>
                                            <pre class="mb-0"
                                                style="font-size: 13px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($product->detail_product); ?></pre>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-top text-end" style="padding-bottom: 0px;">
                                        <p>
                                            <?php echo e(number_format($product->price, 0, '', '.')); ?>

                                        </p>
                                    </td>
                                    <td class="align-top" style="padding-bottom: 0px;">
                                        <p>
                                            <?php echo e($product->qty); ?> <?php echo e($product->info_qty); ?>

                                        </p>
                                    </td>
                                    <?php if($hasDisc): ?>
                                        <td class="align-top">
                                            <p>
                                                <?php echo e($product->disc); ?> %
                                            </p>
                                        </td>
                                    <?php endif; ?>
                                    <?php if($quote->tax != 0): ?>
                                        <td class="align-top text-end" style="padding-bottom: 0px;">
                                            <p>
                                                <?php echo e(number_format($dpp, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    <?php endif; ?>
                                    <td class="align-top text-end" style="padding-bottom: 0px;">
                                        <p>
                                            <?php echo e(number_format($product->amount, 0, '', '.')); ?>

                                        </p>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <tr class="fw-medium" style="font-size: 13px">
                                <td colspan="<?php echo e($quote->tax != 0 ? '3' : '2'); ?>" rowspan="9" id="dynamicRows"
                                    style="border-bottom :none !important;">
                                </td>
                                <td colspan="<?php echo e($labelColspan); ?>" id="price" class="text-end pl-4 py-0"
                                    style="padding-right: 10px !important;">
                                    <p class="m-0">
                                        <?php echo e($quote->tax != 0 || $invoice->pph != 0 || $quote->shipping != 0 ? 'Subtotal' : 'Total'); ?>

                                    </p>
                                </td>
                                <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                    <p class="text-end m-0">RP
                                        <?php echo e(number_format($quote->subtotal, 0, '', '.')); ?></p>
                                </td>
                            </tr>
                            <?php
                                if ($quote->pic->client->info == 'Reftech') {
                                    $bgColor = 'rgb(224, 248, 248)';
                                } else {
                                    $bgColor = 'rgb(255, 232, 210)';
                                }
                            ?>

                            <?php if($invoice->type == 'CT'): ?>
                                <?php if($quote->diskon != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($quote->diskon, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Total After Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($afterDisc, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                    <?php if($quote->tax != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" id="price" class="text-end pl-4 py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">
                                                    DPP Atas PPN
                                                </p>
                                            </td>
                                            <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <?php
                                                    $dpp = ($afterDisc * 11) / 12;
                                                ?>
                                                <p class="text-end m-0">RP
                                                    <?php echo e(number_format($dpp, 0, '', '.')); ?></p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if($quote->tax != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" id="price" class="text-end pl-4 py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">
                                                    DPP Atas PPN
                                                </p>
                                            </td>
                                            <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <?php
                                                    $dpp = ($quote->subtotal * 11) / 12;
                                                ?>
                                                <p class="text-end m-0">RP
                                                    <?php echo e(number_format($dpp, 0, '', '.')); ?></p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if($quote->tax != 0 || $totalPph > 0): ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT <?php echo e($quote->tax == '11' ? '12%' : ''); ?></p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                <?php echo e($tax == '0' ? '0' : 'RP ' . number_format($tax, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                    <?php if($totalPph > 0): ?>
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">PPH</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    <?php echo e($totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if($quote->shipping != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Shipping Cost</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($quote->shipping, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if($quote->tax != 0 || $totalPph > 0 || $quote->shipping != 0): ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px;">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">TOTAL</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                <?php echo e('RP ' . number_format($quote->harga_total - $totalPph, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php elseif($invoice->type == 'DP'): ?>
                                <?php
                                    $amount1 = $payments[0]->amount / (1 + $quote->tax / 100);
                                    $vat = $amount1 * ($quote->tax / 100);
                                    $totalwithpph = $payments[0]->amount - $totalPph;
                                ?>
                                <?php if($quote->diskon != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($quote->diskon, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Total After Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($afterDisc, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0 px-0">
                                        <p class="m-0"
                                            style="background-color: yellow; padding-left:20px; padding-right:10px;">
                                            <?php echo e($payments[0]->note); ?>

                                            <?php echo e($payments[0]->percent); ?>%:</p>
                                    </td>
                                    <td class="px-0 py-0" style="padding-left: 0 !important;">
                                        <p class="fw-medium m-0 text-end"
                                            style="background-color: yellow; padding-right:20px;">
                                            RP
                                            <?php echo e(number_format($amount1, 0, '', '.')); ?></p>
                                    </td>
                                </tr>
                                <?php if($quote->tax != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" id="price" class="text-end pl-4 py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">
                                                DPP Atas PPN
                                            </p>
                                        </td>
                                        <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <?php
                                                $dpp = ($amount1 * 11) / 12;
                                            ?>
                                            <p class="text-end m-0">RP
                                                <?php echo e(number_format($dpp, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT <?php echo e($quote->tax == '11' ? '12%' : ''); ?></p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                <?php echo e($vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                    <?php if($totalPph > 0): ?>
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">PPH</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    <?php echo e($totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if($quote->shipping != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    <?php echo e(number_format($quote->shipping, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">Total Include VAT</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                Rp <?php echo e(number_format($totalwithpph, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php if($quote->shipping != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    <?php echo e(number_format($quote->shipping, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-left:20px; padding-right:10px;">
                                            <p class="m-0">Total</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                <?php echo e(number_format($payments[0]->amount, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php elseif($invoice->type == 'BP'): ?>
                                <?php
                                    $amount1 = $payments[0]->amount / (1 + $quote->tax / 100);
                                    $amount2 = $payments[1]?->amount / (1 + $quote->tax / 100);
                                    $vat = $amount2 * ($quote->tax / 100);
                                ?>
                                <?php if($quote->diskon != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($quote->diskon, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Total After Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($afterDisc, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0" style="padding-right: 10px !important;">
                                        <p class="m-0">
                                            <?php echo e($payments[0]->note); ?>

                                            <?php echo e($payments[0]->percent); ?>%:</p>
                                    </td>
                                    <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                        <p class="m-0 text-end">
                                            RP
                                            <?php echo e(number_format($amount1, 0, '', '.')); ?></p>
                                    </td>
                                </tr>
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0 px-0">
                                        <p class="m-0"
                                            style="background-color: yellow; padding-left:20px; padding-right:10px;">
                                            <?php echo e($payments[1]?->note); ?>

                                            <?php echo e($payments[1]?->percent); ?>%:</p>
                                    </td>
                                    <td class="px-0 py-0" style="padding-left: 0 !important;">
                                        <p class="m-0 text-end" style="background-color: yellow; padding-right:20px;">
                                            RP
                                            <?php echo e(number_format($amount2, 0, '', '.')); ?></p>
                                    </td>
                                </tr>
                                <?php if($totalPph > 0): ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">PPH</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                <?php echo e($totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php
                                    $totalwithpph = $payments[1]?->amount - $totalPph;
                                ?>
                                <?php if($quote->tax != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" id="price" class="text-end pl-4 py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">
                                                DPP Atas PPN
                                            </p>
                                        </td>
                                        <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <?php
                                                $dpp = ($amount2 * 11) / 12;
                                            ?>
                                            <p class="text-end m-0">RP
                                                <?php echo e(number_format($dpp, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT <?php echo e($quote->tax == '11' ? '12%' : ''); ?></p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                <?php echo e($vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                    <?php if($quote->shipping != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    <?php echo e(number_format($quote->shipping, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">Total Include VAT</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                Rp <?php echo e(number_format($totalwithpph, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php if($quote->shipping != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    <?php echo e(number_format($quote->shipping, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-left:20px; padding-right:10px;">
                                            <p class="m-0">Total</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                <?php echo e(number_format($payments[1]?->amount, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if($quote->diskon != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($quote->diskon, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Total After Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($afterDisc, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $amount = $pay->amount / (1 + $quote->tax / 100);
                                        $vat = $amount * ($quote->tax / 100);
                                        $payamount = $pay->amount;
                                        $totalwithpph = $pay->amount - $totalPph;
                                    ?>
                                    <?php if(count($payments) > 1 || $invoice->type != 'CT'): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0 px-0">
                                                <p class="m-0"
                                                    style="<?php echo e($loop->last ? 'background-color: yellow;' : ''); ?> padding-left:20px; padding-right:10px;">
                                                    <?php echo e($pay->note); ?>

                                                    <?php echo e($pay->percent); ?>%:</p>
                                            </td>
                                            <td class="px-0 py-0" style="padding-left: 0 !important;">
                                                <p class="fw-medium m-0 text-end"
                                                    style="<?php echo e($loop->last ? 'background-color: yellow;' : ''); ?> padding-right:20px;">
                                                    RP
                                                    <?php echo e(number_format($amount, 0, '', '.')); ?></p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if($quote->tax != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" id="price" class="text-end pl-4 py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">
                                                DPP Atas PPN
                                            </p>
                                        </td>
                                        <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <?php
                                                $dpp = ($amount * 11) / 12;
                                            ?>
                                            <p class="text-end m-0">RP
                                                <?php echo e(number_format($dpp, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT <?php echo e($quote->tax == '11' ? '12%' : ''); ?></p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                <?php echo e($vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                    <?php if($totalPph > 0): ?>
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">PPH</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    <?php echo e($totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if($quote->shipping != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    <?php echo e(number_format($quote->shipping, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">Total Include VAT</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                Rp <?php echo e(number_format($totalwithpph, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php if($quote->shipping != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    <?php echo e(number_format($quote->shipping, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-left:20px; padding-right:10px;">
                                            <p class="m-0">Total</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                <?php echo e(number_format($payamount, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <?php
                        $hasDisc        = $subQuote->flatMap(fn($s) => $s->detail)->where('disc', '>', 0)->count() > 0;
                        $labelColspan   = $hasDisc ? 2 : 1;
                        $subtitleColspan = ($quote->tax != 0 ? 6 : 5) - ($hasDisc ? 0 : 1);
                    ?>
                    <table class="table table-bordered m-0"
                        style="border: 1px solid rgb(60, 60, 60); border-collapse: collapse;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 1%">No.</th>
                                <th style="width: 35%">Item Description</th>
                                <th style="width: 15%">Price</th>
                                <th>Qty</th>
                                <?php if($hasDisc): ?>
                                    <th>Disc</th>
                                <?php endif; ?>
                                <?php if($quote->tax != 0): ?>
                                    <th style="width: 15%">DPP</th>
                                <?php endif; ?>
                                <th style="width: 25%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                             <?php
                                 $totalPph = $invoice->pph ?? 0;
                             ?>
                            <?php
                                $abjad = 64;
                            ?>
                            <?php $__currentLoopData = $subQuote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subJudul): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $no = 0;
                                    $abjad++;
                                ?>
                                <tr style="font-size: 13px border-bottom:none !important;" class="border-top">
                                    <td class="align-top"
                                        style="border-bottom:none !important; background-color: #f0f0f0;">
                                        <p class="fw-bold mb-0"><?php echo e(chr($abjad)); ?></p>
                                    </td>
                                    <td class="text-nowrap align-top" colspan="<?php echo e($subtitleColspan); ?>"
                                        style="border-bottom:none !important; background-color: #f0f0f0;">
                                        <p class="fw-bold mb-0"><?php echo e($subJudul->subtitle); ?></p>
                                    </td>
                                </tr>
                                <?php $__currentLoopData = $subJudul->detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $no++;
                                        $pph = ($product->amount * $product->pph) / 100;
                                        $totalPph += $pph;
                                        $dpp = ($product->amount * 11) / 12;
                                    ?>
                                    <tr style="font-size: 13px; border-bottom:none !important;">
                                        <td class="align-top" style="border-bottom:none !important;">
                                            <p class="mb-1"><?php echo e($no); ?></p>
                                        </td>
                                        <td class="text-nowrap align-top"
                                            style="border-bottom:none !important;">
                                            <pre class="mb-0"
                                                style="font-size: 13px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($product->product); ?></pre>
                                        </td>
                                        <td class="align-top text-end" style="padding-bottom: 0px;">
                                            <p>
                                                <?php echo e(number_format($product->price, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                        <td class="align-top" style="border-bottom:none !important;">
                                            <p class="mb-0"><?php echo e($product->qty); ?> <?php echo e($product->info_qty); ?></p>
                                        </td>
                                        <?php if($hasDisc): ?>
                                            <td class="align-top" style="border-bottom:none !important;">
                                                <p class="mb-0"><?php echo e($product->disc); ?> %</p>
                                            </td>
                                        <?php endif; ?>
                                        <?php if($quote->tax != 0): ?>
                                            <td class="align-top text-end" style="padding-bottom: 0px;">
                                                <p>
                                                    <?php echo e(number_format($dpp, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        <?php endif; ?>
                                        <td class="align-top text-end" style="border-bottom:none !important;">
                                            <p class="mb-0">RP <?php echo e(number_format($product->amount, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <tr class="fw-medium" style="font-size: 13px">
                                <td colspan="<?php echo e($quote->tax != 0 ? '4' : '3'); ?>" rowspan="9" id="dynamicRows"
                                    style="border-bottom :none !important;">
                                </td>
                                <td colspan="<?php echo e($labelColspan); ?>" id="price" class="text-end pl-4 py-0"
                                    style="padding-right: 10px !important;">
                                    <p class="m-0">
                                        <?php echo e($quote->tax != 0 || $invoice->pph != 0 || $quote->shipping != 0 ? 'Subtotal' : 'Total'); ?>

                                    </p>
                                </td>
                                <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                    <p class="text-end m-0">RP
                                        <?php echo e(number_format($quote->subtotal, 0, '', '.')); ?></p>
                                </td>
                            </tr>
                            <?php
                                if ($quote->pic->client->info == 'Reftech') {
                                    $bgColor = 'rgb(224, 248, 248)';
                                } else {
                                    $bgColor = 'rgb(255, 232, 210)';
                                }
                            ?>
                            <?php if($invoice->type == 'CT'): ?>
                                <?php if($quote->diskon != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($quote->diskon, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Total After Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($afterDisc, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                    <?php if($quote->tax != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" id="price" class="text-end pl-4 py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">
                                                    DPP Atas PPN
                                                </p>
                                            </td>
                                            <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <?php
                                                    $dpp = ($afterDisc * 11) / 12;
                                                ?>
                                                <p class="text-end m-0">RP
                                                    <?php echo e(number_format($dpp, 0, '', '.')); ?></p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if($quote->tax != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" id="price" class="text-end pl-4 py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">
                                                    DPP Atas PPN
                                                </p>
                                            </td>
                                            <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <?php
                                                    $dpp = ($quote->subtotal * 11) / 12;
                                                ?>
                                                <p class="text-end m-0">RP
                                                    <?php echo e(number_format($dpp, 0, '', '.')); ?></p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if($quote->tax != 0 || $totalPph > 0): ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT <?php echo e($quote->tax == '11' ? '12%' : ''); ?></p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                <?php echo e($tax == '0' ? '0' : 'RP ' . number_format($tax, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                    <?php if($totalPph > 0): ?>
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">PPH</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    <?php echo e($totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if($quote->shipping != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Shipping Cost</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($quote->shipping, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if($quote->tax != 0 || $totalPph > 0 || $quote->shipping != 0): ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">TOTAL</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                <?php echo e('RP ' . number_format($quote->harga_total - $totalPph, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php elseif($invoice->type == 'DP'): ?>
                                <?php
                                    $amount1 = $payments[0]->amount / (1 + $quote->tax / 100);
                                    $vat = $amount1 * ($quote->tax / 100);
                                    $totalwithpph = $payments[0]->amount - $totalPph;
                                ?>
                                <?php if($quote->diskon != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($quote->diskon, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Total After Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($afterDisc, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0 px-0">
                                        <p class="m-0"
                                            style="background-color: yellow; padding-left:20px; padding-right:10px;">
                                            <?php echo e($payments[0]->note); ?>

                                            <?php echo e($payments[0]->percent); ?>%:</p>
                                    </td>
                                    <td class="px-0 py-0" style="padding-left: 0 !important;">
                                        <p class="fw-medium m-0 text-end"
                                            style="background-color: yellow; padding-right:20px;">
                                            RP
                                            <?php echo e(number_format($amount1, 0, '', '.')); ?></p>
                                    </td>
                                </tr>
                                <?php if($quote->tax != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" id="price" class="text-end pl-4 py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">
                                                DPP Atas PPN
                                            </p>
                                        </td>
                                        <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <?php
                                                $dpp = ($amount1 * 11) / 12;
                                            ?>
                                            <p class="text-end m-0">RP
                                                <?php echo e(number_format($dpp, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT <?php echo e($quote->tax == '11' ? '12%' : ''); ?></p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                <?php echo e($vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                    <?php if($totalPph > 0): ?>
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">PPH</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    <?php echo e($totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if($quote->shipping != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    <?php echo e(number_format($quote->shipping, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">Total Include VAT</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                Rp <?php echo e(number_format($totalwithpph, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php if($quote->shipping != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    <?php echo e(number_format($quote->shipping, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-left:20px; padding-right:10px;">
                                            <p class="m-0">Total</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                <?php echo e(number_format($payments[0]->amount, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php elseif($invoice->type == 'BP'): ?>
                                <?php
                                    $amount1 = $payments[0]->amount / (1 + $quote->tax / 100);
                                    $amount2 = $payments[1]?->amount / (1 + $quote->tax / 100);
                                    $vat = $amount2 * ($quote->tax / 100);
                                ?>
                                <?php if($quote->diskon != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($quote->diskon, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Total After Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                <?php echo e(number_format($afterDisc, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0" style="padding-right: 10px !important;">
                                        <p class="m-0">
                                            <?php echo e($payments[0]->note); ?>

                                            <?php echo e($payments[0]->percent); ?>%:</p>
                                    </td>
                                    <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                        <p class="m-0 text-end">
                                            RP
                                            <?php echo e(number_format($amount1, 0, '', '.')); ?></p>
                                    </td>
                                </tr>
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0 px-0">
                                        <p class="m-0"
                                            style="background-color: yellow; padding-left:20px; padding-right:10px;">
                                            <?php echo e($payments[1]?->note); ?>

                                            <?php echo e($payments[1]?->percent); ?>%:</p>
                                    </td>
                                    <td class="px-0 py-0" style="padding-left: 0 !important;">
                                        <p class="m-0 text-end" style="background-color: yellow; padding-right:20px;">
                                            RP
                                            <?php echo e(number_format($amount2, 0, '', '.')); ?></p>
                                    </td>
                                </tr>
                                <?php if($totalPph > 0): ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">PPH</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                <?php echo e($totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php
                                    $totalwithpph = $payments[1]?->amount - $totalPph;
                                ?>
                                <?php if($quote->tax != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" id="price" class="text-end pl-4 py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">
                                                DPP Atas PPN
                                            </p>
                                        </td>
                                        <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <?php
                                                $dpp = ($amount2 * 11) / 12;
                                            ?>
                                            <p class="text-end m-0">RP
                                                <?php echo e(number_format($dpp, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT <?php echo e($quote->tax == '11' ? '12%' : ''); ?></p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                <?php echo e($vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                    <?php if($quote->shipping != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    <?php echo e(number_format($quote->shipping, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">Total Include VAT</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                Rp <?php echo e(number_format($totalwithpph, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php if($quote->shipping != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    <?php echo e(number_format($quote->shipping, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-left:20px; padding-right:10px;">
                                            <p class="m-0">Total</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                <?php echo e(number_format($payments[1]?->amount, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php
                                    $payAmountVal = $payments[0]->amount ?? 0;
                                    $taxRate = $quote->tax ?? 0;
                                    $amount = $taxRate != 0 ? $payAmountVal / (1 + $taxRate / 100) : $payAmountVal;
                                    $vat = $amount * ($taxRate / 100);
                                    $payamount = $payAmountVal;
                                    $totalwithpph = $payAmountVal - $totalPph;
                                ?>
                                <?php if($quote->tax != 0): ?>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" id="price" class="text-end pl-4 py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">
                                                DPP Atas PPN
                                            </p>
                                        </td>
                                        <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <?php
                                                $dpp = ($amount * 11) / 12;
                                            ?>
                                            <p class="text-end m-0">RP
                                                <?php echo e(number_format($dpp, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT <?php echo e($quote->tax == '11' ? '12%' : ''); ?></p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                <?php echo e($vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.')); ?></p>
                                        </td>
                                    </tr>
                                    <?php if($totalPph > 0): ?>
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">PPH</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    <?php echo e($totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if($quote->shipping != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    <?php echo e(number_format($quote->shipping, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">Total Include VAT</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                Rp <?php echo e(number_format($totalwithpph, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php if($quote->shipping != 0): ?>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    <?php echo e(number_format($quote->shipping, 0, '', '.')); ?>

                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="<?php echo e($labelColspan); ?>" class="text-end py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-left:20px; padding-right:10px;">
                                            <p class="m-0">Total</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: <?php echo e($bgColor); ?>; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                <?php echo e(number_format($payamount, 0, '', '.')); ?>

                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <?php if($invoice->type == 'CT'): ?>
                <p class="fw-medium mt-2 p-2" style="background-color: rgb(248, 248, 248); width:70%;"> Say
                    amount: #
                    <?php echo e($fullPrice); ?> Rupiah</p>
            <?php elseif($invoice->type == 'DP'): ?>
                <p class="fw-medium mt-2 p-2" style="background-color: rgb(248, 248, 248); width:70%;"> Say
                    amount: #
                    <?php echo e($priceDp); ?> Rupiah</p>
            <?php elseif($invoice->type == 'BP'): ?>
                <p class="fw-medium mt-2 p-2" style="background-color: rgb(248, 248, 248); width:70%;"> Say
                    amount: #
                    <?php echo e($priceBp); ?> Rupiah</p>
            <?php endif; ?>
            <div class="row">
                <div class="col-7">
                    <p class="mt-4 fw-bold fs-6">Payment by Transfer or Giro shall be made in Full amount to :</p>
                    <div class="row">
                        <div class="col-3 fw-medium">
                            <p class="mb-1">Payable to</p>
                            <p class="mb-1">Acc Name </p>
                            <p class="mb-1">Acc No. </p>
                            <p class="mb-1">Swift Code </p>
                        </div>
                        <?php if($quote->pic->client->info == 'Reftech' && $invoice->quote->tax == 0): ?>
                            <div class="col">
                                <p class="mb-1">: Bank BCA (IDR)</p>
                                <p class="mb-1">: ARIEP RACHMAN</p>
                                <p class="mb-1">: 166 - 2242 - 271</p>
                                <p class="mb-1">: -</p>
                            </div>
                        <?php elseif($quote->pic->client->info == 'Reftech' && $invoice->quote->tax > 0): ?>
                            <div class="col">
                                <p class="mb-1">: Bank BCA (IDR)</p>
                                <p class="mb-1">: PT. REFTECH JAYA OPTIMA</p>
                                <p class="mb-1">: 008 - 6289 - 789</p>
                                <p class="mb-1">: CENAIDJA</p>
                            </div>
                        <?php elseif($quote->pic->client->info == 'Kojisha' && $invoice->quote->tax == 0): ?>
                            <div class="col">
                                <p class="mb-1">: Bank BCA (IDR)</p>
                                <p class="mb-1">: REGITA DWI MELINDA</p>
                                <p class="mb-1">: 1560239137</p>
                                <p class="mb-1">: - </p>
                            </div>
                        <?php elseif($quote->pic->client->info == 'Kojisha' && $invoice->quote->tax > 0): ?>
                            <div class="col">
                                <p class="mb-1">: Bank BCA (IDR)</p>
                                <p class="mb-1">: KOJISHA INNOTIV INDONESIA PT</p>
                                <p class="mb-1">: 5223876543</p>
                                <p class="mb-1">: - </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col"></div>
                <?php if($quote->pic->client->info == 'Reftech'): ?>
                    <div class="col-4 mt-4 text-center">
                        <p class="<?php echo e($quote->tax != 0 ? 'mb-0' : 'mb-3'); ?>">Bandung,
                            <?php echo e(Carbon\Carbon::parse($invoice->date)->locale('ID')->translatedFormat('d F Y')); ?></p>
                        <?php if($quote->tax != 0): ?>
                            <p class="fs-normal fw-bolder">PT Reftech Jaya Optima</p>
                        <?php endif; ?>
                        <?php if(isset($invoice->sign)): ?>
                            <img src="<?php echo e(url('') . '/' . $invoice->sign); ?>" alt="" srcset=""
                                height="77">
                        <?php else: ?>
                            <div style="padding: 40px 0;"></div>
                        <?php endif; ?>
                        <p class="pt-3 fw-bolder mb-0">Ariep Rachman</p>
                        <p>Director</p>
                    </div>
                <?php else: ?>
                    <div class="col-4 mt-4 text-center">
                        <p class="mb-0">Bekasi, <?php echo e(Carbon\Carbon::parse($invoice->date)->format('d F Y')); ?></p>
                        <?php if($quote->tax != 0): ?>
                            <p class="fs-normal fw-bolder">PT Kojisha Innotiv Indonesia </p>
                        <?php endif; ?>
                        <?php if(isset($invoice->sign)): ?>
                            <img src="<?php echo e(url('') . '/' . $invoice->sign); ?>" alt="" srcset=""
                                height="77">
                        <?php else: ?>
                            <div style="padding: 40px 0;"></div>
                        <?php endif; ?>
                        <p class="pt-3 fw-bolder mb-0">Dedeh Sulastri</p>
                        <p>Director</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php $__env->startPush('after-style'); ?>
        <!-- Page CSS -->
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice-print-header.css" />
        <link rel="stylesheet" href="style.css">
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('after-script'); ?>
        <script src="<?php echo e(asset('assets')); ?>/js/app-invoice-print.js"></script>
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('script'); ?>
        <script>
            $(document).ready(function() {
                // Ambil tinggi dari elemen <pre>
                var preHeight = $('#notePre').outerHeight();
                // Atur tinggi elemen <p> menjadi sama dengan tinggi elemen <pre>
                $('#noteParagraph').css('height', preHeight + 'px');
            });
        </script>
    <?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/accounting/invoice/detail-print.blade.php ENDPATH**/ ?>