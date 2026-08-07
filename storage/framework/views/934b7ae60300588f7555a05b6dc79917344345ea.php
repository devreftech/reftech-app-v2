<div class="modal-onboarding modal modal-lg fade animate__animated" id="detailPayment" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="onboarding-content mb-0">
                    <h4 class="onboarding-title text-body"> Detail Payment of <?php echo e($quote->no_quote); ?></h4>
                    <div class="onboarding-info mb-3">
                        <?php echo e($quote->pic->client->company); ?>

                    </div>
                    <div class="row mb-4 text-nowrap">
                        <table class="table table-bordered">
                            <?php
                                $totalAmount = 0;
                                $remaining = $quote->harga_total;
                            ?>
                            <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        
                                        <?php if($payment->file != null): ?>
                                            <a href="<?php echo e(url($payment->file)); ?>"
                                                class="btn btn-sm btn-primary d-grid w-100 waves-effect"
                                                target="_blank">Pay
                                                Image</a>
                                        <?php else: ?>
                                            <input class="form-control" type="file" id="upload-photo" name="file"
                                                accept=".jpg,.jpeg,.png" data-id="<?php echo e($payment->id); ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <p>Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></p>
                                    </td>
                                    <td>
                                        <p><?php echo e($payment->note); ?></p>
                                    </td>
                                    <?php if(Auth::user()->role == 'Sales'): ?>
                                        <td>
                                            <?php if($payment->level == 0): ?>
                                                <p>Not Confirmed</p>
                                            <?php else: ?>
                                                <p>CONFIRMED!!</p>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="#" data-id="<?php echo e($payment->id); ?>"
                                                data-quote="<?php echo e($quote->id); ?>"
                                                class="btn btn-sm btn-label-danger delete-payments waves-effect">
                                                <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline m-0"></i>
                                            </a>
                                            <?php if($payment->file != null): ?>
                                                <button type="button" class="btn btn-sm btn-label-primary copy-link"
                                                    data-link="<?php echo e(route('payment_detail.payment', $payment->id)); ?>">
                                                    Copy Link
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    <?php elseif(Auth::user()->role == 'Admin'): ?>
                                        <td>
                                            <?php if($payment->level == 0): ?>
                                                <a href="#" data-id="<?php echo e($payment->id); ?>"
                                                    
                                                    data-quote="<?php echo e($quote->id); ?>"
                                                    class="btn btn-sm btn-label-success confirm-payments waves-effect">
                                                    <i
                                                        class="menu-icon tf-icons mdi mdi-14px mdi-check-outline m-0"></i>
                                                </a>
                                            <?php else: ?>
                                                <p>CONFIRMED!!</p>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                                <?php
                                    $totalAmount += $payment->amount;
                                    $remaining = $quote->harga_total - $totalAmount;
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </table>
                        
                        <hr>
                        <div class="col-6">
                            <h5 class="text-start"> Pay Remaining</h5>
                        </div>
                        <div class="col-6">
                            <h5>: Rp <?php echo e(number_format($remaining, 0, ',', '.')); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->startPush('script'); ?>
    <script>
        $('#upload-photo').on('change', function() {
            let id = $(this).data('id');
            let file = this.files[0];
            if (!file) return;

            let formData = new FormData();
            formData.append("file", file);
            formData.append("_token", "<?php echo e(csrf_token()); ?>");

            $.ajax({
                url: "/quotation/" + id + "/proof_payment",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        // langsung refresh halaman
                        location.reload();
                    } else {
                        alert("Upload gagal: " + res.message);
                    }
                },
                error: function(xhr) {
                    alert("Terjadi kesalahan: " + xhr.responseText);
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.copy-link').forEach(button => {
                button.addEventListener('click', function() {
                    const link = this.getAttribute('data-link');

                    navigator.clipboard.writeText(link)
                        .then(() => {
                            // Bootstrap alert kecil dan auto hilang
                            const alert = document.createElement('div');
                            alert.className =
                                'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                            alert.role = 'alert';
                            alert.innerHTML = `
                        Link berhasil disalin ke clipboard!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                            document.body.appendChild(alert);

                            setTimeout(() => {
                                alert.classList.remove('show');
                                alert.remove();
                            }, 2000);
                        })
                        .catch(err => {
                            alert('Gagal menyalin link: ' + err);
                        });
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/quotation/detail-payment.blade.php ENDPATH**/ ?>