<form action="<?php echo e(route('purchase-request.store-project', $pending->id)); ?>" method="post" enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>
    <div class="modal-onboarding modal fade animate__animated" id="purchaseReqPrj" tabindex="-1" style="display: none;"
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
                                                <th style="width: 35%">Item</th>
                                                
                                                <th>Qty</th>
                                                <th style="width: 35%">Note</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="text-start">
                                                    <div class="form-floating form-floating-outline mb-2">
                                                        <select class="select2 form-select select-project" data-allow-clear="true"
                                                            name="id_equivalent" data-id="1">
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
                                                        <label for="Equivalent" class="mb-2">Equivalent</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" class="form-control"
                                                            id="exampleFormControlinput1" name="qty"
                                                            placeholder="Stock..." value="<?php echo e(@$item->bdg); ?>"
                                                            min="0"></input>
                                                        <label for="exampleFormControlinput1">Qty</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <textarea class="form-control" id="exampleFormControlTextarea1" name="note" placeholder="Text Note here..."></textarea>
                                                        <label for="exampleFormControlTextarea1">Note</label>
                                                    </div>
                                                </td>
                                            </tr>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/pending/request-project.blade.php ENDPATH**/ ?>