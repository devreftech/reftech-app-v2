<div class="modal-onboarding modal modal-lg fade animate__animated" id="detailPending-<?php echo e($pending->id); ?>" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="onboarding-content mb-0">
                    <h3 class="onboarding-title"> Detail Pending Of <?php echo e($pending->quote->pic->client->company); ?>

                    </h3>
                    
                    <form>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <h5>Client Information</h5>
                                <div class="row">
                                    <div class="col-4">
                                        <p>Sales </p>
                                        <p>Client </p>
                                        <p>PIC </p>
                                        <p>R/K </p>
                                    </div>
                                    <div class="col-8">
                                        <p>: <?php echo e($pending->quote->sales->name); ?></p>
                                        <p>: <?php echo e($pending->quote->pic->client->company); ?></p>
                                        <p>: <?php echo e($pending->quote->pic->name_pic); ?></p>
                                        <p>: <?php echo e($pending->quote->pic->client->info); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <h5>Invoice Information</h5>
                                <div class="row">
                                    <div class="col-4">
                                        <p>No Invoice </p>
                                        <p>No PO </p>
                                        <p>PO Date </p>
                                    </div>
                                    <div class="col-8">
                                        <p>: <?php echo e($pending->quote->invoice->first()->no_invoice ?? 'Belum ada invoice'); ?></p>
                                        <p>: <?php echo e($pending->quote->invoice->first()->no_po ?? 'Belum ada invoice'); ?></p>
                                        <p>: <?php echo e($pending->quote->po_date); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">

                                <div class="table-responsive">
                                    <table class="table m-0">
                                        <thead class="table-light border-top">
                                            <tr>
                                                <th>No.</th>
                                                <th>Item</th>
                                                <th>Qty</th>
                                                <th>Note</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $no = 0;
                                            ?>
                                            <?php $__empty_1 = true; $__currentLoopData = $pending->detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <?php
                                                    $no++;
                                                ?>
                                                <tr>
                                                    <td><?php echo e($no); ?></td>
                                                    <td>
                                                        <p>
                                                            <?php echo e($item->replacement->replacement); ?>

                                                        </p>
                                                        <p>
                                                            <?php echo e($item->desc); ?>

                                                        </p>
                                                    </td>
                                                    <td>
                                                        <?php echo e($item->qty); ?>

                                                        <?php echo e($item->replacement->product->info_qty); ?>

                                                    </td>
                                                    <td><?php echo e($item->note); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="3">Barang belum di cek</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/pending/detail.blade.php ENDPATH**/ ?>