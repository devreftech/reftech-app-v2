<?php $__env->startSection('title', 'Helpdesk'); ?>
<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Helpdesk</h4>
            <span class="text-muted">Laporkan bug atau ajukan permintaan fitur ke tim developer.</span>
        </div>
        <button type="button" class="btn btn-primary" data-bs-target="#formHelpdesk" data-bs-toggle="modal">
            <i class="mdi mdi-plus me-sm-1"></i> Buat Tiket
        </button>
    </div>

    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <?php if(Auth::user()->role == 'Admin'): ?>
                <table class="datatable-helpdesk-admin table table-striped">
                    <thead>
                        <tr>
                            <th>No Ticket</th>
                            <th>Requester</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            <?php else: ?>
                <table class="datatable-helpdesk table table-striped">
                    <thead>
                        <tr>
                            <th>No Ticket</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <?php echo $__env->make('components.modal.helpdesk.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.helpdesk.detail', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <?php if(Auth::user()->role == 'Admin'): ?>
        <script src="<?php echo e(asset('assets')); ?>/includes/table-helpdesk-admin.js"></script>
    <?php else: ?>
        <script src="<?php echo e(asset('assets')); ?>/includes/table-helpdesk.js"></script>
    <?php endif; ?>
<?php $__env->stopPush(); ?>

<?php if(Auth::user()->role == 'Admin'): ?>
    <?php $__env->startPush('script'); ?>
        <script>
            function submitHelpdeskStatus(id, status, note) {
                $.ajax({
                    url: '<?php echo e(url('helpdesk/status')); ?>/' + id,
                    type: 'POST',
                    data: {
                        '_method': 'PATCH',
                        '_token': '<?php echo e(csrf_token()); ?>',
                        'status': status,
                        'note': note || '',
                    },
                    success: function(response) {
                        if (response == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Status diperbarui!',
                                customClass: {
                                    confirmButton: 'btn btn-success waves-effect',
                                },
                            });
                            window.setTimeout(function() {
                                window.location.href = '/helpdesk';
                            }, 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Gagal memperbarui status tiket.'
                            });
                        }
                    }
                });
            }

            $(document).on('click', '.button-helpdesk-status', function() {
                var id = $('#detailHelpdesk').attr('data-id');
                var status = $(this).data('status');
                var $detailModalEl = document.getElementById('detailHelpdesk');
                var detailModal = bootstrap.Modal.getInstance($detailModalEl);

                // Bootstrap's modal focus-trap fights SweetAlert2's input for
                // focus (the textarea looks unclickable/uneditable) unless the
                // underlying modal is fully hidden first.
                $($detailModalEl).one('hidden.bs.modal', function() {
                    if (status === 'Resolved') {
                        Swal.fire({
                            title: 'Selesaikan Tiket',
                            input: 'textarea',
                            inputLabel: 'Keterangan Penyelesaian',
                            inputPlaceholder: 'Jelaskan bagaimana tiket ini diselesaikan...',
                            inputAttributes: { style: 'height: 120px' },
                            showCancelButton: true,
                            confirmButtonText: 'Selesaikan',
                            customClass: {
                                confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                                cancelButton: 'btn btn-label-secondary waves-effect',
                            },
                            buttonsStyling: false,
                            inputValidator: function(value) {
                                if (!value) {
                                    return 'Keterangan penyelesaian wajib diisi';
                                }
                            },
                        }).then(function(result) {
                            if (!result.value) return;
                            submitHelpdeskStatus(id, status, result.value);
                        });
                    } else {
                        Swal.fire({
                            title: 'Ubah status tiket menjadi "' + status + '"?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, ubah',
                            customClass: {
                                confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                                cancelButton: 'btn btn-label-secondary waves-effect',
                            },
                            buttonsStyling: false,
                        }).then(function(result) {
                            if (!result.value) return;
                            submitHelpdeskStatus(id, status, null);
                        });
                    }
                });
                if (detailModal) detailModal.hide();
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/helpdesk/index.blade.php ENDPATH**/ ?>