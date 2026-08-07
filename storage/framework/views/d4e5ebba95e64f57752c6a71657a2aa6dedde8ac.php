<div class="modal-onboarding modal modal-xl fade animate__animated" id="detailOverdue" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="onboarding-content mb-0">
                    <h3 class="onboarding-title"> Detail Of Overdue
                    </h3>
                    <form>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table m-0">
                                        <thead class="table-light border-top">
                                            <tr>
                                                <th>No.</th>
                                                <th>Invoice.</th>
                                                <th>Date</th>
                                                <th>Customer</th>
                                                <th>Overdue</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $no = 0;
                                            ?>
                                            <?php $__empty_1 = true; $__currentLoopData = $overdue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <?php
                                                    $no++;
                                                    $days = \Carbon\Carbon::parse($item->due_date)->diffInDays(\Carbon\Carbon::today(), false);
                                                    if ($item->id_unit_quotation) {
                                                        $uq           = $item->unitQuotation;
                                                        $inv          = \App\Models\Invoice::where('id_unit_quotation', $item->id_unit_quotation)->whereNotNull('no_invoice')->first();
                                                        $invoiceRoute = $inv ? route('invoice.show_unit', $inv->id) : '#';
                                                        $invoiceNo    = $inv?->no_invoice ?? '-';
                                                        $itemDate     = $uq?->created_at?->format('d-m-Y') ?? '-';
                                                        $company      = $uq?->client?->company ?? '-';
                                                        $total        = $item->harga_total;
                                                    } else {
                                                        $inv0         = $item->quotation->invoice->first();
                                                        $invoiceRoute = $inv0 ? route('invoice.show', $inv0->id) : '#';
                                                        $invoiceNo    = $inv0?->no_invoice ?? '-';
                                                        $itemDate     = $item->quotation->po_date ?? '-';
                                                        $company      = $item->quotation->pic->client->company ?? '-';
                                                        $total        = $item->quotation->harga_total;
                                                    }
                                                ?>
                                                <tr>
                                                    <td><?php echo e($no); ?></td>
                                                    <td>
                                                        <a href="<?php echo e($invoiceRoute); ?>" class="text-dark text-decoration-none">
                                                            <?php echo e($invoiceNo); ?>

                                                        </a>
                                                    </td>
                                                    <td><p><?php echo e($itemDate); ?></p></td>
                                                    <td><?php echo e($company); ?></td>
                                                    <td><?php echo e($days); ?> Days Overdue</td>
                                                    <td><?php echo e(number_format($total, 0, ',', '.')); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="3">tidak ada Overdue</td>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/payment/overdue.blade.php ENDPATH**/ ?>