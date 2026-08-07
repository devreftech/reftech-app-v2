<form action="<?php echo e(route('upload-po.quotation', $quote->id)); ?>" method="post" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="modal-onboarding modal fade animate__animated" id="uploadPo" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Upload File PO <?php echo e($quote->no_quote); ?></h4>
                        <div class="onboarding-info mb-3">
                            <?php echo e($quote->pic->client->company); ?>

                        </div>
                        <form>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="formFile" class="form-label">Upload PO</label>
                                            <input class="form-control" type="file" id="formFile" name="uploadPO"
                                                accept=".pdf">
                                                <p><span class="fw-bold">Note :</span>File Maximal <span class="fw-bold">3 MB.</span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control form-control-sm" type="text"
                                            placeholder="Put No Purchase Order Here ...." id="po" name="po"
                                            value="">
                                        <label for="po">No Purchase Order</label>
                                    </div>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/quotation/upload-po.blade.php ENDPATH**/ ?>