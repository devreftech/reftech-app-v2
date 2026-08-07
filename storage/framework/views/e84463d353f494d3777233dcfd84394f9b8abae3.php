<form action="<?php echo e(@$customer ? route('update.salon', @$customer->id) : route('store.salon')); ?>" method="post"
    enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="modal-onboarding modal fade animate__animated" id="customer" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">Customer Care</h4>
                        <form>
                            <div class="row align-items-center">
                                <h5 class="text-center mb-3">Target 5.0</h5>
                                <div class="col-4 mb-3">
                                    <h5 class="text-start m-0"><?php echo e($salesID == 16 ? 'Airend Center' : 'Part Compressor'); ?></h5>
                                </div>
                                <div class="col-8 mb-3">
                                    <input class="form-control form-control-lg" type="text" placeholder="Target"
                                        id="airendCustomer" name="airend" oninput="validateFloatInputCustomer(this)"
                                        maxlength="4"
                                        value="<?php echo e(@$customer->airend ? str_replace('.', ',', $customer->airend) : '0'); ?>">
                                </div>
                                <?php if($salesID == 16): ?>
                                    <div class="col-4 mb-3">
                                        <h5 class="text-start m-0">Kojisha</h5>
                                    </div>
                                    <div class="col-8 mb-3">
                                        <input class="form-control form-control-lg" type="text" placeholder="Target"
                                            id="kojishaCustomer" name="kojisha"
                                            oninput="validateFloatInputCustomer(this)" maxlength="4"
                                            value="<?php echo e(@$customer->kojisha ? str_replace('.', ',', $customer->kojisha) : '0'); ?>">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <div class="card text-center bg-label-secondary">
                                            <div class="card-body">
                                                <input type="text" name="average" id="averageCustomer"
                                                    value="<?php echo e(@$customer->average ? str_replace('.', ',', $customer->average) : '0'); ?>"
                                                    hidden>
                                                <h5>Average</h5>
                                                <p id="averageCustomerText">
                                                    <?php echo e(@$customer->average ? str_replace('.', ',', $customer->average) : '0'); ?>

                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <input type="text" name="type" id="type" value="Customer" hidden>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/onlineSales/customer.blade.php ENDPATH**/ ?>