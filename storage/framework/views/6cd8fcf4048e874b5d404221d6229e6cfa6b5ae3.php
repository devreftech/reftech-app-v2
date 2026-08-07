<?php
    $user = (isset($sales) ? $sales->firstWhere('id', $overview['salesId']) : null) ?? \App\Models\User::find($overview['salesId']);
    $userTargetObj = $user?->latestTarget ?? (isset($user?->target) && count($user->target) > 0 ? $user->target[0] : null);
    $tLeads = $userTargetObj?->leads ?? 0;
    $tDc    = $userTargetObj?->dc ?? 0;
    $tCrm   = $userTargetObj?->crm ?? 0;
    $tQuote = $userTargetObj?->quote ?? 0;
?>
<div class="modal animate__animated animate__fadeIn" id="overview-sales-<?php echo e($overview['salesId']); ?>" tabindex="-1"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="exampleModalLabel5"> Report
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-4">
                    <h5 class="card-header">Assigned: <?php echo e($overview['sales']); ?></h5>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th>Week 1</th>
                                    <th>Week 2</th>
                                    <th>Week 3</th>
                                    <th>Week 4</th>
                                    <th>Week 5</th>
                                    <th>Total</th>
                                    <th>Presentase</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <?php if($overview['salesId'] == 1 || $overview['salesId'] == 2 || $overview['salesId'] == 32): ?>
                                    <tr>
                                        <td>
                                            <strong>New Leads</strong>
                                        </td>
                                        <?php
                                            $totalLeadsFullWeek = 0;
                                        ?>
                                        <?php $__currentLoopData = $overview['leads']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td><?php echo e($week); ?></td>
                                            <?php
                                                $totalLeadsFullWeek += $week;
                                            ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <td><?php echo e($totalLeadsFullWeek); ?></td>
                                        <td>
                                            <?php
                                                $denomLeads = $tLeads + ($tLeads / 4);
                                            ?>
                                            <?php echo e($denomLeads > 0 ? round(($totalLeadsFullWeek / $denomLeads) * 100) : 0); ?> %
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Daily Call</strong>
                                        </td>
                                        <?php
                                            $totalDCFullWeek = 0;
                                        ?>
                                        <?php $__currentLoopData = $overview['dc']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td><?php echo e($week); ?></td>
                                            <?php
                                                $totalDCFullWeek += $week;
                                            ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <td><?php echo e($totalDCFullWeek); ?></td>
                                        <td>
                                            <?php
                                                $denomDc = $tDc + ($tDc / 4);
                                            ?>
                                            <?php echo e($denomDc > 0 ? round(($totalDCFullWeek / $denomDc) * 100) : 0); ?> %
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td>
                                        <strong>CRM</strong>
                                    </td>
                                    <?php
                                        $totalCRMFullWeek = 0;
                                    ?>
                                    <?php $__currentLoopData = $overview['crm']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td><?php echo e($week); ?></td>
                                        <?php
                                            $totalCRMFullWeek += $week;
                                        ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <td><?php echo e($totalCRMFullWeek); ?></td>
                                    <td>
                                        <?php
                                            $denomCrm = $tCrm + ($tCrm / 4);
                                        ?>
                                        <?php echo e($denomCrm > 0 ? round(($totalCRMFullWeek / $denomCrm) * 100) : 0); ?> %
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Quotation</strong>
                                    </td>
                                    <?php
                                        $totalQuoteFullWeek = 0;
                                    ?>
                                    <?php $__currentLoopData = $overview['quote']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td><?php echo e($week); ?></td>
                                        <?php
                                            $totalQuoteFullWeek += $week;
                                        ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <td><?php echo e($totalQuoteFullWeek); ?></td>
                                    <td>
                                        <?php
                                            $denomQuote = $tQuote + ($tQuote / 4);
                                        ?>
                                        <?php echo e($denomQuote > 0 ? round(($totalQuoteFullWeek / $denomQuote) * 100) : 0); ?> %
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Purchase Order</strong>
                                    </td>
                                    <?php
                                        $totalPoFullWeek = 0;
                                    ?>
                                    <?php $__currentLoopData = $overview['po']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td><?php echo e($week); ?></td>
                                        <?php
                                            $totalPoFullWeek += $week;
                                        ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <td><?php echo e($totalPoFullWeek); ?></td>
                                    <td>
                                        <?php
                                            $denomPo = $tQuote + ($tQuote / 4);
                                        ?>
                                        <?php echo e($denomPo > 0 ? round(($totalPoFullWeek / $denomPo) * 100) : 0); ?> %
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <p class="fw-semibold m-0"> Total Quotation</p>
                                        <p class="text-muted m-0"><?php echo e($totalQuoteFullWeek); ?></p>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <p class="fw-semibold m-0"> Total PO</p>
                                        <p class="text-muted m-0"><?php echo e($totalPoFullWeek); ?></p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/overview.blade.php ENDPATH**/ ?>