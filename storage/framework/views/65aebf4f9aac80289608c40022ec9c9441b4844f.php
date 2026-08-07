
<?php $__env->startSection('title', 'Preview Product In'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row justify-content-center">
        <div class="col-xl-9 col-12">

            
            <div id="alert-box" class="alert d-none mb-3" role="alert"></div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Preview Product In</h5>
                    <div class="d-flex flex-column gap-2 align-items-end">
                        <div class="d-flex gap-2 align-items-center">
                            <span class="text-muted small fst-italic">
                                <i class="mdi mdi-pencil-outline me-1"></i>Klik nilai untuk mengedit
                            </span>
                            <a href="<?php echo e(url('/product-in')); ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="mdi mdi-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm" id="btn-delete-all"
                            data-id="<?php echo e($product->id); ?>">
                            <i class="mdi mdi-trash-can-outline me-1"></i> Hapus Data
                        </button>
                    </div>
                </div>
                <div class="card-body">

                    
                    <div class="row mb-4">
                        <div class="col-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="fw-semibold text-muted ps-0" style="width:155px">No. Product In</td>
                                    <td>: <span class="fw-bold text-primary"><?php echo e($product->no_product_in ?? '-'); ?></span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted ps-0">No. Delivery Order</td>
                                    <td>:
                                        <span class="inline-edit" data-field="no_do" data-type="text" data-value="<?php echo e($product->no_do); ?>">
                                            <?php echo e($product->no_do ?? '-'); ?>

                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted ps-0">Tanggal</td>
                                    <td>:
                                        <span class="inline-edit" data-field="date" data-type="date" data-value="<?php echo e($product->date); ?>">
                                            <?php echo e($product->date ? \Carbon\Carbon::parse($product->date)->format('d-m-Y') : '-'); ?>

                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="fw-semibold text-muted ps-0" style="width:100px">Supplier</td>
                                    <td>:
                                        <span class="inline-edit" data-field="id_supplier" data-type="select" data-value="<?php echo e($product->id_supplier); ?>">
                                            <?php echo e(optional($product->supp)->supplier ?? '-'); ?>

                                        </span>
                                        <select id="supplier-options" class="d-none">
                                            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($s->id); ?>"><?php echo e($s->supplier); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted ps-0">Tipe</td>
                                    <td>:
                                        <span class="inline-edit" data-field="info" data-type="select"
                                            data-value="<?php echo e($product->info); ?>"
                                            data-options='[{"value":"Lokal","label":"Lokal"},{"value":"Import","label":"Import"}]'>
                                            <span class="badge bg-label-<?php echo e($product->info == 'Import' ? 'warning' : 'info'); ?>">
                                                <?php echo e($product->info ?? '-'); ?>

                                            </span>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-1 text-muted small mt-1">
                        <i class="mdi mdi-account-outline"></i>
                        Dibuat oleh: <span class="fw-semibold text-body ms-1"><?php echo e(optional($product->creator)->name ?? '-'); ?></span>
                    </div>

                    <hr>

                    
                    <select id="product-options" class="d-none">
                        <?php $__currentLoopData = $detProduct; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($dp->id); ?>">
                                <?php echo e($dp->product->commodity); ?> (<?php echo e($dp->product->detail_desc); ?>) || <?php echo e($dp->replacement); ?> - <?php echo e($dp->product->go == 'Genuine' ? 'G' : 'R'); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    
                    <h6 class="fw-semibold mb-3">Daftar Barang</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" id="items-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:36px">No.</th>
                                    <th>Produk / Replacement</th>
                                    <th class="text-center" style="width:110px">Qty</th>
                                    <th class="text-center" style="width:120px">Warehouse</th>
                                    <th class="text-center" style="width:60px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr data-row-id="<?php echo e($item->id); ?>">
                                        <td class="text-center row-number"><?php echo e($i + 1); ?></td>
                                        <td>
                                            
                                            <span class="inline-edit item-field product-cell"
                                                data-id="<?php echo e($item->id); ?>"
                                                data-field="id_detail_product"
                                                data-type="select-product"
                                                data-value="<?php echo e($item->id_detail_product); ?>">
                                                <span class="fw-semibold"><?php echo e($item->detailProduct->product->commodity ?? '-'); ?></span>
                                                <small class="text-muted d-block"><?php echo e($item->detailProduct->replacement ?? ''); ?></small>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="inline-edit item-field"
                                                data-id="<?php echo e($item->id); ?>"
                                                data-field="qty"
                                                data-type="number"
                                                data-value="<?php echo e($item->qty); ?>">
                                                <?php echo e($item->qty); ?> <?php echo e($item->detailProduct->product->unit ?? ''); ?>

                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="inline-edit item-field"
                                                data-id="<?php echo e($item->id); ?>"
                                                data-field="warehouse"
                                                data-type="select"
                                                data-value="<?php echo e($item->warehouse); ?>"
                                                data-options='[{"value":"BDG","label":"BDG"},{"value":"BKS","label":"BKS"}]'>
                                                <span class="badge bg-label-secondary"><?php echo e($item->warehouse); ?></span>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-outline-danger delete-item"
                                                data-id="<?php echo e($item->id); ?>"
                                                title="Hapus item">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr id="empty-row">
                                        <td colspan="5" class="text-center text-muted">Tidak ada data barang</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="mt-2 mb-3">
                        <button type="button" id="btn-add-row" class="btn btn-outline-primary btn-sm">
                            <i class="mdi mdi-plus me-1"></i> Tambah Barang
                        </button>
                    </div>

                    
                    <div class="d-flex justify-content-end mt-3">
                        <button id="btn-save" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-content-save-outline me-1"></i> Simpan Perubahan
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
<script>
$(function () {
    var updateUrl  = "<?php echo e(route('product-in.logistic-update', $product->id)); ?>";
    var deleteUrl  = "<?php echo e(url('/product-in/detail')); ?>";
    var csrfToken  = "<?php echo e(csrf_token()); ?>";

    // ── Inline Edit: klik span → ganti jadi input/select ──────────────────
    $(document).on('click', '.inline-edit', function (e) {
        if ($(this).find('input, select').length) return;

        var $span   = $(this);
        var type    = $span.data('type');
        var field   = $span.data('field');
        var value   = $span.data('value');
        var options = $span.data('options');
        var $input;

        if (type === 'select-product') {
            $input = $('<select class="form-select form-select-sm">');
            $('#product-options option').each(function () {
                var sel = $(this).val() == value ? 'selected' : '';
                $input.append('<option value="' + $(this).val() + '" ' + sel + '>' + $(this).text() + '</option>');
            });
            $span.html($input);
            $input.select2({ width: '100%', dropdownParent: $span.closest('td') }).trigger('focus');
            return;
        }

        if (type === 'select') {
            $input = $('<select class="form-select form-select-sm">');
            if (field === 'id_supplier') {
                $('#supplier-options option').each(function () {
                    var sel = $(this).val() == value ? 'selected' : '';
                    $input.append('<option value="' + $(this).val() + '" ' + sel + '>' + $(this).text() + '</option>');
                });
            } else {
                $.each(options, function (i, opt) {
                    var sel = opt.value == value ? 'selected' : '';
                    $input.append('<option value="' + opt.value + '" ' + sel + '>' + opt.label + '</option>');
                });
            }
        } else if (type === 'date') {
            $input = $('<input type="date" class="form-control form-control-sm" style="width:150px">').val(value);
        } else if (type === 'number') {
            $input = $('<input type="number" class="form-control form-control-sm text-center" min="1" style="width:75px">').val(value);
        } else {
            $input = $('<input type="text" class="form-control form-control-sm">').val(value ?? $span.text().trim());
        }

        $input.attr('data-field', field);
        $span.html($input);
        $input.trigger('focus');
    });

    // ── Tambah baris baru ──────────────────────────────────────────────────
    var newRowIndex = 0;

    $('#btn-add-row').on('click', function () {
        newRowIndex++;
        var rowNum   = $('#items-table tbody tr').length + 1;
        var uid      = 'new-' + newRowIndex;

        // Build product options
        var productOpts = '';
        $('#product-options option').each(function () {
            productOpts += '<option value="' + $(this).val() + '">' + $(this).text() + '</option>';
        });

        var $row = $(
            '<tr data-new-id="' + uid + '">' +
                '<td class="text-center row-number">' + rowNum + '</td>' +
                '<td>' +
                    '<select class="form-select form-select-sm new-product-select" data-uid="' + uid + '">' +
                        productOpts +
                    '</select>' +
                '</td>' +
                '<td class="text-center">' +
                    '<input type="number" class="form-control form-control-sm text-center new-qty" min="1" value="1" style="width:75px; margin:auto;">' +
                '</td>' +
                '<td class="text-center">' +
                    '<select class="form-select form-select-sm new-warehouse" style="width:90px; margin:auto;">' +
                        '<option value="BDG">BDG</option>' +
                        '<option value="BKS">BKS</option>' +
                    '</select>' +
                '</td>' +
                '<td class="text-center">' +
                    '<button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-new-row">' +
                        '<i class="mdi mdi-close"></i>' +
                    '</button>' +
                '</td>' +
            '</tr>'
        );

        $('#items-table tbody').append($row);

        // Init Select2 pada product select baru
        $row.find('.new-product-select').select2({
            width: '100%',
            dropdownParent: $row.find('td:nth-child(2)'),
        });

        renumberRows();
    });

    // Hapus baris baru (belum disimpan)
    $(document).on('click', '.remove-new-row', function () {
        $(this).closest('tr').remove();
        renumberRows();
    });

    // ── Hapus item ─────────────────────────────────────────────────────────
    $(document).on('click', '.delete-item', function () {
        var $btn = $(this);
        var itemId = $btn.data('id');
        var $row   = $btn.closest('tr');

        if (!confirm('Hapus item ini? Stok akan dikembalikan.')) return;

        $.ajax({
            url:  deleteUrl + '/' + itemId,
            type: 'POST',
            data: { _token: csrfToken, _method: 'DELETE' },
            success: function (res) {
                $row.remove();
                renumberRows();
                showAlert('success', res.message ?? 'Item berhasil dihapus');
            },
            error: function () {
                showAlert('danger', 'Gagal menghapus item.');
            },
        });
    });

    // ── Simpan semua perubahan ─────────────────────────────────────────────
    $('#btn-save').on('click', function () {
        var $btn = $(this).prop('disabled', true).text('Menyimpan...');

        var payload = {
            _token:      csrfToken,
            no_do:       getHeaderVal('no_do'),
            date:        getHeaderVal('date'),
            id_supplier: getHeaderVal('id_supplier'),
            info:        getHeaderVal('info'),
            items:       {},
            new_items:   [],
        };

        // Kumpulkan baris baru
        $('#items-table tbody tr[data-new-id]').each(function () {
            var $row = $(this);
            payload.new_items.push({
                id_detail_product: $row.find('.new-product-select').val(),
                qty:               $row.find('.new-qty').val(),
                warehouse:         $row.find('.new-warehouse').val(),
            });
        });

        $('.item-field').each(function () {
            var $el   = $(this);
            var id    = $el.data('id');
            var field = $el.data('field');
            var val   = $el.find('input, select').length
                ? $el.find('input, select').val()
                : $el.data('value');

            if (!payload.items[id]) payload.items[id] = {};
            payload.items[id][field] = val;
        });

        $.ajax({
            url:  updateUrl,
            type: 'POST',
            data: payload,
            success: function (res) {
                showAlert('success', res.message ?? 'Data berhasil disimpan');
                // Reload halaman agar baris baru dapat id asli dari DB
                if (payload.new_items.length > 0) {
                    setTimeout(function () { window.location.reload(); }, 800);
                    return;
                }
                // Kembalikan semua input ke teks
                $('.inline-edit').each(function () {
                    var $span = $(this);
                    var $input = $span.find('input, select');
                    if (!$input.length) return;
                    var newVal = $input.val();
                    $span.data('value', newVal);
                    if ($input.is('select')) {
                        $span.text($input.find(':selected').text());
                    } else if ($input.attr('type') === 'date') {
                        var d = new Date(newVal);
                        $span.text(
                            String(d.getDate()).padStart(2,'0') + '-' +
                            String(d.getMonth()+1).padStart(2,'0') + '-' +
                            d.getFullYear()
                        );
                    } else {
                        $span.text(newVal);
                    }
                });
            },
            error: function () {
                showAlert('danger', 'Gagal menyimpan, coba lagi.');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="mdi mdi-content-save-outline me-1"></i> Simpan Perubahan');
            },
        });
    });

    // ── Hapus seluruh data product in ─────────────────────────────────────
    $('#btn-delete-all').on('click', function () {
        var productId = $(this).data('id');
        Swal.fire({
            title: 'Hapus data ini?',
            text: 'Seluruh data product in beserta itemnya akan dihapus dan tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url:  '<?php echo e(url("product-in")); ?>/' + productId,
                type: 'POST',
                data: { _token: csrfToken, _method: 'DELETE' },
                success: function (res) {
                    if (res == 1) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Data product in berhasil dihapus.',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                            buttonsStyling: false,
                        }).then(function () {
                            window.location.href = '<?php echo e(url("product-in")); ?>';
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Data gagal dihapus.' });
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan, coba lagi.' });
                },
            });
        });
    });

    // ── Helpers ────────────────────────────────────────────────────────────
    function getHeaderVal(field) {
        var $span  = $('.inline-edit[data-field="' + field + '"]');
        var $input = $span.find('input, select');
        return $input.length ? $input.val() : $span.data('value');
    }

    function renumberRows() {
        $('#items-table tbody tr').each(function (i) {
            $(this).find('.row-number').text(i + 1);
        });
    }

    function showAlert(type, msg) {
        var $box = $('#alert-box');
        $box.removeClass('d-none alert-success alert-danger')
            .addClass('alert-' + type).text(msg);
        setTimeout(function () { $box.addClass('d-none'); }, 3500);
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/warehouse/product-in/preview.blade.php ENDPATH**/ ?>