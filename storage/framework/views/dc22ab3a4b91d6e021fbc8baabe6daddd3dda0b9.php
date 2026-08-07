<form action="<?php echo e(route('sales-order.reschedule', $schedule->id)); ?>" method="post"
    enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>
    <div class="modal animate__animated animate__fadeIn" id="<?php echo e("reschedule-".$schedule->id); ?>" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="exampleModalLabel5">
                        <?php echo e('ReSchedule'); ?> <?php echo e(@$schedule->order->quote->invoice[0]->no_po ?? @$schedule->order->quote->pic->client->customer); ?>

                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="row g-2 mb-3">
                        <div class="col-12">
                            <div class="mb-3">
                                <input class="form-control" type="date" id="date" name="date_schedule"
                                    value="<?php echo e(old('date_schedule', @$schedule->date_schedule ?? now()->format('Y-m-d'))); ?>">
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-floating form-floating-outline">
                                <textarea class="form-control" id="exampleFormControlTextarea1" name="note" placeholder="Note Schedule here..."><?php echo e(@$schedule->note_schedule); ?></textarea>
                                <label for="exampleFormControlTextarea1">Note</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/pending/jadwal/reschedule.blade.php ENDPATH**/ ?>