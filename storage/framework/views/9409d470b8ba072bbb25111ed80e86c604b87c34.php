<?php
    $monthKeyModal = $getPOModal[$item]['monthKey'] ?? $item;
    $modalDataList = $getPOModal[$item]['data'] ?? [];
?>
<div class="modal animate__animated animate__fadeIn" id="overviewPO<?php echo e($monthKeyModal); ?>" tabindex="-1"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="exampleModalLabel5"> Total PO
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-4">
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>PO No.</th>
                                    <th>Company</th>
                                    <th>Title</th>
                                    <th>PO Date</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <?php
                                    $totalP = 0;
                                    $key = 0;
                                ?>
                                <?php $__empty_1 = true; $__currentLoopData = $modalDataList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $quoteData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $totalQ = $quoteData['nett'];
                                        $totalP += $totalQ;
                                        $isUnit = ($quoteData['source'] ?? 'quotation') === 'unit_quotation';
                                        $quoteObj = $isUnit ? null : \App\Models\Quotation::where('id', $quoteData['id'])->first();
                                    ?>
                                    <tr>
                                        <td class="fw-medium">
                                            <?php if($isUnit): ?>
                                                <a class="text-black"
                                                    href="<?php echo e(route('unit-quotation.show', $quoteData['id'])); ?>"><?php echo e($quoteData['no_quote']); ?></a>
                                            <?php else: ?>
                                                <a class="text-black"
                                                    href="<?php echo e(route('quotation.show', $quoteObj->id)); ?>"><?php echo e($quoteObj->no_quote); ?></a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($isUnit ? $quoteData['company'] : ($quoteObj->pic->client->company ?? 'Client Di Hapus')); ?></td>
                                        <td><?php echo e($isUnit ? $quoteData['title'] : $quoteObj->title); ?></td>
                                        <td><?php echo e(\Carbon\Carbon::parse($isUnit ? $quoteData['estimated_date'] : $quoteObj->estimated_date)->format('d-m-Y')); ?></td>
                                        <td class="text-end">Rp
                                            <?php echo e(number_format($isUnit ? $quoteData['nett'] : $quoteObj->nett, 0, '', '.')); ?></td>
                                    </tr>
                                    <?php
                                        $key++;
                                    ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <td colspan="5" class="text-center">Kamu belum punya quotation</td>
                                <?php endif; ?>
                                <tr class="bg-label-secondary">
                                    <td colspan="3">
                                    </td>
                                    <td><strong>Total</strong></td>
                                    <td class="text-end"><strong>Rp <?php echo e(number_format($totalP, 0, '', '.')); ?></strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/overview/totalPo.blade.php ENDPATH**/ ?>