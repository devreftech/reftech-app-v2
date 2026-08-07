<?php $__env->startSection('title', 'Part Inquiry Detail'); ?>
<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"><a href="<?php echo e(route('part-inquiry.index')); ?>">Part Inquiry</a> /</span>
        <span id="viewTitle"><?php echo e($serial->brand); ?> — <?php echo e($serial->pn); ?></span>
    </h4>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row mb-4">
        
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Product</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="40%">SKU</td>
                            <td>: <strong>
                                <a href="<?php echo e(route('product.show', $serial->product->id)); ?>">
                                    <?php echo e($serial->product->commodity); ?>

                                </a>
                            </strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Genuine / OEM</td>
                            <td>: <?php echo e($serial->product->go); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Description</td>
                            <td>: <?php echo e($serial->product->description); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Equivalent</h6>
                    <button class="btn btn-sm btn-label-primary" data-bs-toggle="modal" data-bs-target="#editEquivalentModal">
                        <i class="mdi mdi-pencil-outline me-1"></i> Edit Equivalent
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="40%">Brand</td>
                            <td>: <strong id="viewBrand"><?php echo e($serial->brand); ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Part Number</td>
                            <td>: <strong id="viewPn">
                                <?php if(!$serial->pn || $serial->pn === '-'): ?>
                                    <span class="badge bg-label-warning">PN Pending</span>
                                <?php else: ?>
                                    <?php echo e($serial->pn); ?>

                                <?php endif; ?>
                            </strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Harga Jual</td>
                            <td>: <strong class="text-success" id="viewPrice">Rp <?php echo e(number_format($serial->price, 0, ',', '.')); ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Harga Vendor</h6>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addVendorPrice">
                <i class="mdi mdi-plus me-1"></i> Tambah Harga Vendor
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Harga USD ($)</th>
                            <th>Harga Modal (IDR)</th>
                            <th>Tanggal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $vendorPrices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($vp->supplier->supplier ?? '-'); ?></td>
                                <td><?php echo e($vp->price_usd > 0 ? '$ ' . number_format($vp->price_usd, 2) : '-'); ?></td>
                                <td><strong>Rp <?php echo e(number_format($vp->price_idr, 0, ',', '.')); ?></strong></td>
                                <td><?php echo e(\Carbon\Carbon::parse($vp->date)->format('d M Y')); ?></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-label-danger delete-vendor-price"
                                        data-id="<?php echo e($vp->id); ?>">
                                        <i class="mdi mdi-delete-outline"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Belum ada data harga vendor.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="addVendorPrice" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="<?php echo e(route('part-inquiry.vendor.store', $serial->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Harga Vendor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select select2-supplier-modal" name="id_supplier" required style="width:100%">
                                <option value="" disabled selected>-- Pilih Supplier --</option>
                                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->info ?: '-'); ?> | <?php echo e($supplier->code ?: '-'); ?> | <?php echo e($supplier->supplier); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga USD ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="price_usd"
                                    placeholder="0.00" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga Modal (IDR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="modal_price_idr_display" placeholder="0" inputmode="numeric">
                                <input type="hidden" id="modal_price_idr" name="price_idr">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date"
                                value="<?php echo e(now()->toDateString()); ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <div class="modal fade" id="editEquivalentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Equivalent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editEqBrand" value="<?php echo e($serial->brand); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Part Number (PN)</label>
                        <input type="text" class="form-control" id="editEqPn"
                            value="<?php echo e($serial->pn !== '-' ? $serial->pn : ''); ?>" placeholder="HU718/5x">
                        <small class="text-muted">Kosongkan jika PN belum didapat dari vendor</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Jual (IDR) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="editEqPriceDisplay" placeholder="0" inputmode="numeric">
                            <input type="hidden" id="editEqPrice" value="<?php echo e($serial->price); ?>">
                        </div>
                    </div>
                    <div id="editEqError" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveEditEquivalent">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/cleavejs/cleave.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
<script>
    function formatSupplierOption(state) {
        if (!state.id) return state.text;
        var parts = state.text.split(' | ');
        var info = parts[0] || '-';
        var rest = parts.slice(1).join(' | ');
        var badgeColor = info === 'Lokal' ? 'success' : (info === 'Import' ? 'info' : 'secondary');
        return $('<span><span class="badge bg-label-' + badgeColor + ' me-1">' + info + '</span>' + rest + '</span>');
    }

    $('.select2-supplier-modal').select2({
        placeholder: '-- Pilih Supplier --',
        dropdownParent: $('#addVendorPrice'),
        width: '100%',
        templateResult: formatSupplierOption,
        templateSelection: formatSupplierOption,
    });

    var cleaveModalPriceIdr = new Cleave('#modal_price_idr_display', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        delimiter: '.',
        numeralDecimalMark: ',',
        numeralDecimalScale: 0,
        onValueChanged: function (e) {
            $('#modal_price_idr').val(e.target.rawValue);
        }
    });

    var cleaveEditPrice = new Cleave('#editEqPriceDisplay', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        delimiter: '.',
        numeralDecimalMark: ',',
        numeralDecimalScale: 0,
    });
    cleaveEditPrice.setRawValue($('#editEqPrice').val());

    $('#editEquivalentModal').on('show.bs.modal', function () {
        $('#editEqError').addClass('d-none').text('');
    });

    $('#saveEditEquivalent').on('click', function () {
        var brand = $('#editEqBrand').val().trim();
        var pn    = $('#editEqPn').val().trim();
        var price = cleaveEditPrice.getRawValue();

        if (!brand || price === '') {
            $('#editEqError').removeClass('d-none').text('Brand dan Harga Jual wajib diisi.');
            return;
        }

        $.ajax({
            url: '<?php echo e(route("part-inquiry.equivalent.update", $serial->id)); ?>',
            type: 'PATCH',
            data: { _token: '<?php echo e(csrf_token()); ?>', brand: brand, pn: pn, price: price },
            success: function (res) {
                if (res.success) {
                    $('#viewBrand').text(res.data.brand);
                    $('#viewPn').html(
                        (!res.data.pn || res.data.pn === '-')
                            ? '<span class="badge bg-label-warning">PN Pending</span>'
                            : res.data.pn
                    );
                    $('#viewPrice').text('Rp ' + parseInt(res.data.price).toLocaleString('id-ID'));
                    $('#viewTitle').text(res.data.brand + ' — ' + res.data.pn);
                    $('#editEquivalentModal').modal('hide');
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message : 'Gagal menyimpan, coba lagi.';
                $('#editEqError').removeClass('d-none').text(msg);
            }
        });
    });

    $(document).on('click', '.delete-vendor-price', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        if (confirm('Hapus harga vendor ini?')) {
            $.ajax({
                url: '/part-inquiry/vendor/' + id + '/delete',
                type: 'DELETE',
                data: { _token: '<?php echo e(csrf_token()); ?>' },
                success: function (res) {
                    if (res == 1) location.reload();
                }
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/warehouse/part-inquiry/show.blade.php ENDPATH**/ ?>