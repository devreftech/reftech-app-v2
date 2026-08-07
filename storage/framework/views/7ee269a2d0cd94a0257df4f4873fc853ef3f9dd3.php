<?php $__env->startSection('title', 'Kelengkapan Data Finance - Tools'); ?>
<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4">
        Kelengkapan Data Finance — Tools
    </h4>
    <p class="text-muted mb-3">
        Tools yang sudah di-assign ke teknisi (lewat Data Tools/Management Tools per Teknisi) belum otomatis punya
        data akuntansi (akun aktiva, harga beli, dst). Lengkapi per item lewat tombol "Lengkapi Data" di bawah —
        ini akan membuka form edit Fixed Asset standar, data assignment ke teknisi tidak akan berubah.
    </p>

    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link <?php echo e($status == 'belum' ? 'active' : ''); ?>"
                href="<?php echo e(route('tool-finance.index', ['status' => 'belum'])); ?>">
                Belum Lengkap <span class="badge bg-label-danger ms-1"><?php echo e($countBelum); ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo e($status == 'sudah' ? 'active' : ''); ?>"
                href="<?php echo e(route('tool-finance.index', ['status' => 'sudah'])); ?>">
                Sudah Lengkap <span class="badge bg-label-success ms-1"><?php echo e($countSudah); ?></span>
            </a>
        </li>
    </ul>

    <div class="card mb-3">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Nama Tools</th>
                        <th>Teknisi</th>
                        <th>Qty</th>
                        <th>Tanggal Serah Terima</th>
                        <th>Harga Beli</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $tools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($tool->toolsMaster->nama_tools ?? '-'); ?></td>
                            <td><?php echo e($tool->pic->name ?? '-'); ?></td>
                            <td><?php echo e($tool->qty); ?></td>
                            <td><?php echo e($tool->tanggal_serah_terima ? \Carbon\Carbon::parse($tool->tanggal_serah_terima)->format('d M Y') : '-'); ?></td>
                            <td><?php echo e($tool->total ? 'Rp ' . number_format($tool->total, 0, ',', '.') : '-'); ?></td>
                            <td>
                                <a href="<?php echo e(route('fixed.edit', $tool->id)); ?>" class="btn btn-sm btn-primary">
                                    <?php echo e($status == 'belum' ? 'Lengkapi Data' : 'Lihat / Edit'); ?>

                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                <?php echo e($status == 'belum' ? 'Semua tools sudah lengkap data finance-nya.' : 'Belum ada yang lengkap.'); ?>

                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/tool-finance/index.blade.php ENDPATH**/ ?>