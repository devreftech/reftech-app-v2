<form action="<?php echo e(route('pending-po.projectEdit', $pending->id)); ?>" method="post" enctype="multipart/form-data">
    <?php echo method_field('PATCH'); ?>
    <?php echo csrf_field(); ?>
    <div class="modal-onboarding modal fade animate__animated" id="replacementEdit" tabindex="-1" style="display: none;"
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
                                <div class="table text-nowrap h-100" style="height: fit-content">
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
                                                $abjad = 64;
                                            ?>
                                            <?php $__currentLoopData = $subQuote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subJudul): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $no = 1;
                                                    $abjad++;
                                                ?>
                                                <tr style="font-size: 17px border-bottom:none !important;"
                                                    class="border-top">
                                                    <td class="align-top"
                                                        style="border-bottom:none !important; background-color: #f0f0f0;">
                                                        <p class="fw-bold mb-0"><?php echo e(chr($abjad)); ?></p>
                                                    </td>
                                                    <td class="text-nowrap align-top" colspan="6"
                                                        style="border-bottom:none !important; background-color: #f0f0f0;">
                                                        <p class="text-start fw-bold mb-0"><?php echo e($subJudul->subtitle); ?></p>
                                                    </td>
                                                </tr>
                                                <?php $__currentLoopData = $subJudul->detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($no); ?></td>
                                                        <td>
                                                            <div class="form-floating form-floating-outline mb-2">
                                                                <select class="select2 form-select"
                                                                    data-allow-clear="true" name="equivalent[]"
                                                                    data-id="1">
                                                                    <option> ---- Choose Equivalent Here ---- </option>
                                                                    <?php $__currentLoopData = $serial; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $replacement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <option value="<?php echo e($replacement->id); ?>"
                                                                            <?php echo e($product->pending[0]->id_equivalent == $replacement->id ? 'selected' : ''); ?>>
                                                                            <?php echo e($replacement->brand); ?>

                                                                            <?php echo e($replacement->pn); ?> -
                                                                            <?php echo e($replacement->product?->go == 'Replacement' ? 'R' : 'G'); ?>

                                                                        </option>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </select>
                                                                <label for="Equivalent"
                                                                    class="mb-2">Equivalent</label>
                                                            </div>
                                                        </td>

                                                        <td><?php echo e($product->qty); ?> <?php echo e($product->info_qty); ?></td>
                                                        <td>
                                                            <div class="form-floating form-floating-outline">
                                                                <select class="form-select" tabindex="0"
                                                                    id="statusChange" name="status[]">
                                                                    <option value="1"
                                                                        <?php echo e(@$product->pending[0]->status == '1' ? 'selected' : ''); ?>>
                                                                        On Check
                                                                    </option>
                                                                    <option value="2"
                                                                        <?php echo e(@$product->pending[0]->status == '2' ? 'selected' : ''); ?>>
                                                                        Ready Stock
                                                                    </option>
                                                                    <option value="3"
                                                                        <?php echo e(@$product->pending[0]->status == '3' ? 'selected' : ''); ?>>
                                                                        Kurang
                                                                    </option>
                                                                    <option value="4"
                                                                        <?php echo e(@$product->pending[0]->status == '4' ? 'selected' : ''); ?>>
                                                                        Pre-Order
                                                                    </option>
                                                                    <option value="5"
                                                                        <?php echo e(@$product->pending[0]->status == '5' ? 'selected' : ''); ?>>
                                                                        Delivery Process
                                                                    </option>
                                                                    <option value="6"
                                                                        <?php echo e(@$product->pending[0]->status == '6' ? 'selected' : ''); ?>>
                                                                        Done
                                                                    </option>
                                                                    <option value="7"
                                                                        <?php echo e(@$product->pending[0]->status == '7' ? 'selected' : ''); ?>>
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
                                                                    value="<?php echo e(@$product->pending[0]->bdg); ?>"></input>
                                                                <label for="exampleFormControlinput1">Bandung</label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="number" class="form-control"
                                                                    id="exampleFormControlinput1" name="bks[]"
                                                                    placeholder="Stock..."
                                                                    value="<?php echo e(@$product->pending[0]->bks); ?>"></input>
                                                                <label for="exampleFormControlTextarea1">Bekasi</label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-floating form-floating-outline">
                                                                <textarea class="form-control" id="exampleFormControlTextarea1" name="note[]" placeholder="Comments here..."><?php echo e(@$product->pending[0]->note); ?></textarea>
                                                                <label for="exampleFormControlTextarea1">Note</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                        $no++;
                                                    ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer mt-4 border-0">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/pending/project.blade.php ENDPATH**/ ?>