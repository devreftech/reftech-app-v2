<form action="<?php echo e(route('pending-po.productEdit', $pending->id)); ?>" method="post" enctype="multipart/form-data">
    <?php echo method_field('PATCH'); ?>
    <?php echo csrf_field(); ?>
    <div class="modal-onboarding modal fade animate__animated" id="productEdit" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">

                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">
                            <?php echo e($pending->quote->invoice[0]?->no_invoice ?? $pending->quote->pic->client->company); ?></h4>
                        <form>
                            <div class="card">
                                <div class="table-responsive text-nowrap h-100">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">No</th>
                                                <th style="width: 25%">Item</th>
                                                
                                                <th>Qty</th>
                                                <th style="width: 15%">Status</th>
                                                <th style="width: 10%">BDG</th>
                                                <th style="width: 10%">BKS</th>
                                                <th style="width: 20%">Note</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <?php
                                                $no = 1;
                                            ?>
                                            <?php $__currentLoopData = $detQuotation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($no); ?></td>
                                                    
                                                    <?php
                                                        $bdgStock = $item->equivalent->product->stock ?? 0;
                                                        $bksStock = $item->equivalent->product->warehouse_stock ?? 0;
                                                        $totalStock = $bdgStock + $bksStock;
                                                        
                                                        // Default selection logic:
                                                        $selectedStatus = $item->status;
                                                        if ($item->status == '1' || is_null($item->status)) {
                                                            $selectedStatus = $totalStock >= $item->qty ? '2' : '3';
                                                        }

                                                        // Auto allocation logic:
                                                        $defaultBdg = $item->bdg;
                                                        $defaultBks = $item->bks;
                                                        if (($item->status == '1' || is_null($item->status)) && ($item->bdg == 0 && $item->bks == 0)) {
                                                            if ($bdgStock >= $item->qty) {
                                                                $defaultBdg = $item->qty;
                                                                $defaultBks = 0;
                                                            } elseif ($totalStock >= $item->qty) {
                                                                $defaultBdg = $bdgStock;
                                                                $defaultBks = $item->qty - $bdgStock;
                                                            } else {
                                                                $defaultBdg = $bdgStock;
                                                                $defaultBks = $bksStock;
                                                            }
                                                        }
                                                        
                                                        $title = 'BDG (' . $bdgStock . ') | BKS (' . $bksStock . ')';
                                                    ?>
                                                    <td class="text-start">
                                                        <pre class="mb-0"
                                                            style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e($title); ?>"><?php echo e($item->equivalent->product->go == 'Genuine' ? 'G' : 'R'); ?> - <?php echo e($item->equivalent->brand); ?> <?php echo e($item->equivalent->pn); ?></pre>
                                                    </td>
 
                                                    <td><?php echo e($item->qty); ?> <?php echo e($item->info_qty); ?></td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <select class="form-select" tabindex="0" id="statusChange"
                                                                name="status[]">
                                                                <option value="1"
                                                                    <?php echo e($selectedStatus == '1' ? 'selected' : ''); ?>>
                                                                    On Check
                                                                </option>
                                                                <option value="2"
                                                                    <?php echo e($selectedStatus == '2' ? 'selected' : ''); ?>>
                                                                    Ready Stock
                                                                </option>
                                                                <option value="3"
                                                                    <?php echo e($selectedStatus == '3' ? 'selected' : ''); ?>>
                                                                    Kurang
                                                                </option>
                                                                <option value="4"
                                                                    <?php echo e($selectedStatus == '4' ? 'selected' : ''); ?>>
                                                                    Pre-Order
                                                                </option>
                                                                <option value="5"
                                                                    <?php echo e($selectedStatus == '5' ? 'selected' : ''); ?>>
                                                                    Delivery Process
                                                                </option>
                                                                <option value="6"
                                                                    <?php echo e($selectedStatus == '6' ? 'selected' : ''); ?>>
                                                                    Done
                                                                </option>
                                                                <option value="7"
                                                                    <?php echo e($selectedStatus == '7' ? 'selected' : ''); ?>>
                                                                    Cancel
                                                                </option>
                                                            </select>
                                                            <label for="statusChange">Status</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="number" class="form-control"
                                                                id="exampleFormControlinput1" name="bdg[]"
                                                                placeholder="Stock..."
                                                                value="<?php echo e($defaultBdg); ?>"></input>
                                                            <label for="exampleFormControlinput1">Bandung</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="number" class="form-control"
                                                                id="exampleFormControlinput1" name="bks[]"
                                                                placeholder="Stock..."
                                                                value="<?php echo e($defaultBks); ?>"></input>
                                                            <label for="exampleFormControlTextarea1">Bekasi</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <textarea class="form-control" id="exampleFormControlTextarea1" name="note[]" placeholder="Comments here..."><?php echo e(@$item->note); ?></textarea>
                                                            <label for="exampleFormControlTextarea1">Note</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                    $no++;
                                                ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/pending/product.blade.php ENDPATH**/ ?>