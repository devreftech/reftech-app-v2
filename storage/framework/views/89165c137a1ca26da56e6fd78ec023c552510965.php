<?php $__env->startSection('title', 'Detail SUO'); ?>
<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">SUO /</span> <?php echo e($suo->no_suo); ?>

    </h4>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible mb-3"><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if(session('info')): ?>
        <div class="alert alert-info alert-dismissible mb-3"><?php echo e(session('info')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="row">
        
        <div class="col-xl-9 col-md-8 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h5 class="mb-1 fw-bold"><?php echo e($suo->company); ?></h5>
                            <p class="mb-0 text-muted">PIC: <?php echo e($suo->pic); ?></p>
                            <p class="mb-0 text-muted">Alamat: <?php echo e($suo->address); ?></p>
                            <?php if($suo->notes): ?>
                                <p class="mb-0 text-muted mt-1"><em><?php echo e($suo->notes); ?></em></p>
                            <?php endif; ?>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-label-primary fs-6 fw-bold"><?php echo e($suo->no_suo); ?></span>
                            <br>
                            <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($suo->created_at)->format('d-m-Y')); ?></small>
                            <br>
                            <span class="badge mt-1
                                <?php if($suo->status == 'draft'): ?> bg-secondary
                                <?php elseif($suo->status == 'submitted'): ?> bg-warning
                                <?php elseif($suo->status == 'confirmed'): ?> bg-info
                                <?php elseif($suo->status == 'goods_out'): ?> bg-success
                                <?php elseif($suo->status == 'converted'): ?> bg-primary
                                <?php endif; ?>">
                                <?php echo e(strtoupper($suo->status)); ?>

                            </span>
                            <?php if($suo->no_invoice_booking): ?>
                                <p class="mt-2 mb-0 fw-semibold text-success" style="font-size:12px;">
                                    Invoice Booking: <?php echo e($suo->no_invoice_booking); ?>

                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="table-responsive">
                    <table class="table table-bordered m-0">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Nama Item / Part</th>
                                <th class="text-center">Qty</th>
                                <th>Satuan</th>
                                <th>Catatan</th>
                                <?php if(in_array($suo->status, ['confirmed','goods_out','converted'])): ?>
                                    <th class="text-center">Stok</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $suo->detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($i + 1); ?></td>
                                    <td class="fw-semibold"><?php echo e($item->item_name); ?></td>
                                    <td class="text-center"><?php echo e($item->qty); ?></td>
                                    <td><?php echo e($item->unit ?? '-'); ?></td>
                                    <td><?php echo e($item->notes ?? '-'); ?></td>
                                    <?php if(in_array($suo->status, ['confirmed','goods_out','converted'])): ?>
                                        <td class="text-center">
                                            <?php if($item->stock_status == 'ready'): ?>
                                                <span class="badge bg-success">Ready</span>
                                            <?php elseif($item->stock_status == 'not_ready'): ?>
                                                <span class="badge bg-danger">Not Ready</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">-</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                
                <?php if($quotation && $quotationDetail->count()): ?>
                    <div class="card-body border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">
                                <i class="mdi mdi-file-document-check-outline me-1 text-primary"></i>
                                Item Penawaran
                            </h6>
                            <a href="<?php echo e(route('quotation.show', $quotation->id)); ?>" class="btn btn-sm btn-outline-primary">
                                <i class="mdi mdi-eye-outline me-1"></i> Lihat Penawaran
                            </a>
                        </div>
                        <p class="text-muted mb-2" style="font-size:12px;">
                            No. Penawaran: <strong><?php echo e($quotation->no_quote); ?></strong>
                            &nbsp;|&nbsp; <?php echo e($quotation->title); ?>

                        </p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" style="font-size:12px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Item / Part</th>
                                        <th class="text-center">Qty</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $quotationDetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($i + 1); ?></td>
                                            <td class="fw-semibold"><?php echo e($item->detail_product); ?></td>
                                            <td class="text-center"><?php echo e($item->qty); ?></td>
                                            <td><?php echo e($item->info_qty ?? '-'); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                
                <?php if($role == 'Logistic' && $suo->status == 'submitted'): ?>
                    <div class="card-body border-top">
                        <h6 class="fw-bold mb-3">Cek Ketersediaan Stok</h6>
                        <form action="<?php echo e(route('suo.checkStock', $suo->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <table class="table table-sm table-bordered mb-3">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qty</th>
                                        <th>Status Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $suo->detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($item->item_name); ?></td>
                                            <td class="text-center"><?php echo e($item->qty); ?> <?php echo e($item->unit); ?></td>
                                            <td>
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="stock_status[<?php echo e($item->id); ?>]"
                                                            value="ready" required>
                                                        <label class="form-check-label text-success fw-semibold">Ready</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="stock_status[<?php echo e($item->id); ?>]"
                                                            value="not_ready">
                                                        <label class="form-check-label text-danger fw-semibold">Not Ready</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-check me-1"></i> Simpan & Teruskan ke Accounting
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="col-xl-3 col-md-4 col-12">
            <div class="card">
                <div class="card-body d-grid gap-2">

                    
                    <?php if($role == 'Sales' && !$suo->id_quotation): ?>
                        <button type="button" class="btn btn-outline-primary waves-effect"
                            data-bs-toggle="modal" data-bs-target="#modalLinkQuotation">
                            <i class="mdi mdi-link-variant me-1"></i> Hubungkan ke Penawaran
                        </button>
                    <?php endif; ?>

                    
                    <?php if(($role == 'Sales' || $role == 'Admin') && $suo->status == 'goods_out' && !$suo->id_quotation): ?>
                        <div class="alert alert-success p-2 mb-0" style="font-size:12px;">
                            Barang sudah keluar. Silahkan buat penawaran untuk melanjutkan proses.
                        </div>
                        <a href="<?php echo e(route('suo.convert', $suo->id)); ?>" class="btn btn-primary waves-effect">
                            <i class="mdi mdi-file-document-plus-outline me-1"></i> Buat Penawaran
                        </a>
                    <?php endif; ?>

                    <?php if($suo->id_quotation): ?>
                        <div class="alert alert-primary p-2 mb-0" style="font-size:12px;">
                            SUO sudah terhubung ke penawaran.
                        </div>
                        <a href="<?php echo e(route('quotation.show', $suo->id_quotation)); ?>" class="btn btn-outline-primary">
                            <i class="mdi mdi-eye-outline me-1"></i> Lihat Penawaran
                        </a>
                        <?php if($invoice): ?>
                            <a href="<?php echo e(url('invoice/' . $invoice->id)); ?>" class="btn btn-outline-success">
                                <i class="mdi mdi-file-document-outline me-1"></i> Lihat Invoice
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    
                    <?php if(($role == 'Admin' || $role == 'Accounting') && $suo->status == 'confirmed' && !$suo->no_invoice_booking): ?>
                        <button class="btn btn-success waves-effect" id="btn-approve"
                            data-bs-toggle="modal" data-bs-target="#modalApprove">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Approve & Booking Invoice
                        </button>
                    <?php endif; ?>

                    <?php if(($role == 'Admin' || $role == 'Accounting') && $suo->status == 'confirmed' && $suo->no_invoice_booking): ?>
                        <div class="alert alert-success p-2 mb-0" style="font-size:12px;">
                            Invoice dibooked: <strong><?php echo e($suo->no_invoice_booking); ?></strong>
                        </div>
                        <button class="btn btn-info waves-effect" data-bs-toggle="modal" data-bs-target="#modalSJ">
                            <i class="mdi mdi-truck-delivery-outline me-1"></i> Buat Surat Jalan
                        </button>
                    <?php endif; ?>

                    <?php if($suo->deliveries->count() > 0): ?>
                        <?php $__currentLoopData = $suo->deliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(url('delivery/' . $d->id)); ?>" class="btn btn-outline-info btn-sm">
                                <i class="mdi mdi-file-document-outline me-1"></i> Lihat SJ #<?php echo e($d->id); ?>

                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                    <a href="javascript:history.back()" class="btn btn-outline-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            
            <div class="card mt-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 fw-bold" style="font-size:13px;">Riwayat SUO</h6>
                </div>
                <div class="card-body py-3">
                    <ul class="list-unstyled mb-0" style="position:relative;">

                        
                        <li class="d-flex gap-3 mb-3">
                            <div class="flex-shrink-0 mt-1">
                                <span class="badge bg-primary rounded-circle p-1" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;">
                                    <i class="mdi mdi-pencil-outline" style="font-size:12px;"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0 fw-semibold" style="font-size:12px;">Dibuat oleh Sales</p>
                                <p class="mb-0 text-muted" style="font-size:11px;"><?php echo e($suo->sales->name ?? '-'); ?></p>
                                <p class="mb-0 text-muted" style="font-size:11px;"><?php echo e(\Carbon\Carbon::parse($suo->created_at)->format('d M Y, H:i')); ?></p>
                            </div>
                        </li>

                        
                        <?php if($suo->confirmed_at): ?>
                            <li class="d-flex gap-3 mb-3">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="badge bg-warning rounded-circle p-1" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;">
                                        <i class="mdi mdi-clipboard-check-outline" style="font-size:12px;"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold" style="font-size:12px;">Dicek oleh Logistik</p>
                                    <p class="mb-0 text-muted" style="font-size:11px;"><?php echo e($suo->confirmedBy->name ?? '-'); ?></p>
                                    <p class="mb-0 text-muted" style="font-size:11px;"><?php echo e(\Carbon\Carbon::parse($suo->confirmed_at)->format('d M Y, H:i')); ?></p>
                                </div>
                            </li>
                        <?php else: ?>
                            <li class="d-flex gap-3 mb-3 opacity-50">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="badge bg-secondary rounded-circle p-1" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;">
                                        <i class="mdi mdi-clipboard-check-outline" style="font-size:12px;"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold" style="font-size:12px;">Menunggu cek Logistik</p>
                                </div>
                            </li>
                        <?php endif; ?>

                        
                        <?php if($suo->approved_at): ?>
                            <li class="d-flex gap-3 mb-0">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="badge bg-success rounded-circle p-1" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;">
                                        <i class="mdi mdi-check-circle-outline" style="font-size:12px;"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold" style="font-size:12px;">Diapprove oleh Accounting</p>
                                    <p class="mb-0 text-muted" style="font-size:11px;"><?php echo e($suo->approvedBy->name ?? '-'); ?></p>
                                    <p class="mb-0 text-muted" style="font-size:11px;"><?php echo e(\Carbon\Carbon::parse($suo->approved_at)->format('d M Y, H:i')); ?></p>
                                    <?php if($suo->no_invoice_booking): ?>
                                        <p class="mb-0 text-success fw-semibold" style="font-size:11px;">Invoice: <?php echo e($suo->no_invoice_booking); ?></p>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php else: ?>
                            <li class="d-flex gap-3 mb-0 opacity-50">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="badge bg-secondary rounded-circle p-1" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;">
                                        <i class="mdi mdi-check-circle-outline" style="font-size:12px;"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold" style="font-size:12px;">Menunggu approve Accounting</p>
                                </div>
                            </li>
                        <?php endif; ?>

                    </ul>
                </div>
            </div>
        </div>
    </div>

    
    <?php if(($role == 'Admin' || $role == 'Accounting') && $suo->status == 'confirmed' && !$suo->no_invoice_booking): ?>
    <div class="modal fade" id="modalApprove" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve & Booking Invoice — <?php echo e($suo->no_suo); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size:13px;"><?php echo e($suo->company); ?></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">No Invoice Booking</label>
                        <input type="text" class="form-control" id="inputNoInvoiceBooking"
                            placeholder="Memuat nomor...">
                        <small class="text-danger" id="lastNoBooking"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success waves-effect" id="btn-approve-confirm">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Konfirmasi & Approve
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if($role == 'Sales' && !$suo->id_quotation): ?>
    <div class="modal fade" id="modalLinkQuotation" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hubungkan ke Penawaran — <?php echo e($suo->no_suo); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-3" id="searchQuotationLink"
                        placeholder="Cari No. Penawaran / Judul / Perusahaan...">
                    <div id="listQuotationLink" style="max-height:400px; overflow-y:auto;">
                        <p class="text-muted text-center py-3">Memuat...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if(($role == 'Admin' || $role == 'Accounting') && $suo->status == 'confirmed' && $suo->no_invoice_booking): ?>
        <div class="modal fade" id="modalSJ" tabindex="-1">
            <div class="modal-dialog">
                <form action="<?php echo e(route('suo.storeDelivery', $suo->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Buat Surat Jalan — <?php echo e($suo->no_suo); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="date"
                                    value="<?php echo e(\Carbon\Carbon::today()->toDateString()); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tujuan / Alamat</label>
                                <select class="form-select" name="destination" required>
                                    <?php if($client): ?>
                                        <option value="1" <?php echo e($suo->address == $client->address ? 'selected' : ''); ?>>
                                            <?php echo e($client->address); ?>

                                        </option>
                                        <?php if($client->subAddress): ?>
                                            <option value="2" <?php echo e($suo->address == $client->subAddress ? 'selected' : ''); ?>>
                                                <?php echo e($client->subAddress); ?>

                                            </option>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <option value="1" selected><?php echo e($suo->address); ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Pengiriman</label>
                                <select class="form-select" name="type">
                                    <option value="Ekspedisi">Ekspedisi</option>
                                    <option value="Teknisi">Teknisi</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Buat Surat Jalan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css"/>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
<script>
function renderQuotationLinkList(data) {
    window.__quotationLinkData = window.__quotationLinkData || data;
    var $list = $('#listQuotationLink');
    if (!data.length) {
        $list.html('<p class="text-muted text-center py-3">Tidak ada penawaran yang bisa dihubungkan.</p>');
        return;
    }
    var html = '<div class="list-group">';
    data.forEach(function (q) {
        html += '<button type="button" class="list-group-item list-group-item-action btn-pick-quotation" data-id="' + q.id + '" data-no="' + (q.no_quote || '-') + '">'
            + '<div class="d-flex justify-content-between"><strong>' + (q.no_quote || '-') + '</strong>'
            + '<small class="text-muted">' + new Date(q.created_at).toLocaleDateString('id-ID') + '</small></div>'
            + '<div style="font-size:12px;">' + (q.title || '') + '</div>'
            + '<div class="text-muted" style="font-size:12px;">' + (q.company || '') + '</div>'
            + '</button>';
    });
    html += '</div>';
    $list.html(html);
}

$(function () {
    // Load list penawaran saat modal Hubungkan ke Penawaran dibuka
    $('#modalLinkQuotation').on('show.bs.modal', function () {
        window.__quotationLinkData = null;
        $('#searchQuotationLink').val('');
        $('#listQuotationLink').html('<p class="text-muted text-center py-3">Memuat...</p>');
        $.get('<?php echo e(route('suo.linkableQuotations', $suo->id)); ?>', function (res) {
            window.__quotationLinkData = res.data;
            renderQuotationLinkList(res.data);
        });
    });

    $('#searchQuotationLink').on('keyup', function () {
        var kw = $(this).val().toLowerCase();
        var filtered = (window.__quotationLinkData || []).filter(function (q) {
            return (q.no_quote || '').toLowerCase().indexOf(kw) !== -1
                || (q.title || '').toLowerCase().indexOf(kw) !== -1
                || (q.company || '').toLowerCase().indexOf(kw) !== -1;
        });
        renderQuotationLinkList(filtered);
    });

    $(document).on('click', '.btn-pick-quotation', function () {
        var idQuotation = $(this).data('id');
        var noQuote = $(this).data('no');
        Swal.fire({
            icon: 'question',
            title: 'Hubungkan ke penawaran ' + noQuote + '?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hubungkan',
            cancelButtonText: 'Batal',
            buttonsStyling: false,
            customClass: { confirmButton: 'btn btn-primary waves-effect me-2', cancelButton: 'btn btn-outline-secondary waves-effect' }
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '<?php echo e(route('suo.linkQuotation', $suo->id)); ?>',
                type: 'POST',
                data: { _token: '<?php echo e(csrf_token()); ?>', id_quotation: idQuotation },
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil dihubungkan!',
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: { confirmButton: 'btn btn-primary waves-effect' },
                        }).then(() => location.reload());
                    }
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal menghubungkan.';
                    Swal.fire({ icon: 'error', title: msg, buttonsStyling: false, customClass: { confirmButton: 'btn btn-danger waves-effect' } });
                }
            });
        });
    });

    // Load nomor suggest saat modal dibuka
    $('#modalApprove').on('show.bs.modal', function () {
        $('#inputNoInvoiceBooking').val('Memuat...').prop('disabled', true);
        $('#lastNoBooking').text('');
        $.get('<?php echo e(route('suo.suggestBooking', $suo->id)); ?>', function (res) {
            $('#inputNoInvoiceBooking').val(res.suggested).prop('disabled', false);
            if (res.last) {
                $('#lastNoBooking').text('Last No: ' + res.last);
            }
        });
    });

    // Konfirmasi & approve
    $('#btn-approve-confirm').on('click', function () {
        var noInvoice = $('#inputNoInvoiceBooking').val().trim();
        if (!noInvoice) {
            Swal.fire({ icon: 'warning', title: 'No invoice tidak boleh kosong', buttonsStyling: false, customClass: { confirmButton: 'btn btn-warning waves-effect' } });
            return;
        }
        $(this).prop('disabled', true).text('Menyimpan...');
        $.ajax({
            url: '<?php echo e(route('suo.approve', $suo->id)); ?>',
            type: 'POST',
            data: { _token: '<?php echo e(csrf_token()); ?>', no_invoice_booking: noInvoice },
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Invoice dibooked: ' + res.no_invoice,
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-primary waves-effect' },
                        buttonsStyling: false,
                    }).then(() => location.reload());
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Gagal menyimpan', buttonsStyling: false, customClass: { confirmButton: 'btn btn-danger waves-effect' } });
                $('#btn-approve-confirm').prop('disabled', false).html('<i class="mdi mdi-check-circle-outline me-1"></i> Konfirmasi & Approve');
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/suo/detail.blade.php ENDPATH**/ ?>