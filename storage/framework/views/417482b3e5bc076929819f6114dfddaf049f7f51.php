<form action="<?php echo e(route('sales-order.dokumentasi', $schedule->id)); ?>" method="post" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="modal-onboarding modal fade animate__animated" id="dokumentasi-<?php echo e($schedule->id); ?>" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content">
                        <h4 class="onboarding-title text-center mb-4"> Dokumentasi
                            <?php echo e(@$schedule->order->quote->invoice[0]->no_po ?? @$schedule->order->quote->pic->client->customer); ?>

                        </h4>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    id="suratJalanSwitch" name="SJ" <?php echo e($schedule->SJ == '1' ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="suratJalanSwitch">Surat
                                                    Jalan</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    id="beritaAcaraSwitch" name="BA" <?php echo e($schedule->BA == '1' ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="beritaAcaraSwitch">Berita
                                                    Acara</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-floating form-floating-outline">
                                                <textarea class="form-control h-100" id="exampleFormControlTextarea1" name="note" placeholder="Note Schedule here..."><?php echo e(@$schedule->note_doc); ?></textarea>
                                                <label for="exampleFormControlTextarea1">Note Document</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/pending/jadwal/dokumentasi.blade.php ENDPATH**/ ?>