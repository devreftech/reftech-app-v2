<?php $__env->startSection('title', 'Edit - ' . $audit->no_audit); ?>
<?php $__env->startSection('content'); ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php
        $headerBadge = [
            'Submitted' => 'bg-label-warning',
            'Verified' => 'bg-label-success',
            'Rejected' => 'bg-label-danger',
        ][$audit->status_submit] ?? 'bg-label-secondary';
    ?>

    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Verifikasi Audit Tools / Edit /</span> <?php echo e($audit->no_audit); ?>

        <span class="badge <?php echo e($headerBadge); ?> align-middle"><?php echo e($audit->status_submit); ?></span>
    </h4>
    <p class="text-muted mb-4">
        Teknisi: <strong><?php echo e($audit->technician->name ?? '-'); ?></strong> —
        Periode <?php echo e($audit->period->tahun); ?> Semester <?php echo e($audit->period->semester); ?>

        <br><span class="text-warning">Admin sedang mengubah data self-audit yang disubmit teknisi.</span>
    </p>

    <a href="<?php echo e(route('tool-audit-verification.show', $audit->id)); ?>" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="mdi mdi-arrow-left"></i> Kembali
    </a>

    <form action="<?php echo e(route('tool-audit-verification.update', $audit->id)); ?>" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php $__currentLoopData = $audit->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $tool = $item->fixedAsset;
                $master = $tool->toolsMaster ?? null;
                $kondisi = old("items.{$item->id}.kondisi", $item->kondisi);
            ?>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3 align-items-start">
                        <div class="col-md-2 text-center">
                            <?php if($tool && $tool->foto_awal): ?>
                                <img src="<?php echo e(asset($tool->foto_awal)); ?>" alt="foto awal"
                                    style="width:100%;max-width:100px;aspect-ratio:1/1;object-fit:cover;border-radius:6px;">
                                <div class="small text-muted mt-1">Foto Awal</div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-1"><?php echo e($master->nama_tools ?? '-'); ?></h6>
                            <div class="text-muted small mb-2">Qty terdaftar: <?php echo e($tool->qty ?? '-'); ?></div>

                            <div class="form-floating form-floating-outline mb-2">
                                <input type="number" class="form-control" name="items[<?php echo e($item->id); ?>][qty_actual]"
                                    value="<?php echo e(old("items.{$item->id}.qty_actual", $item->qty_actual)); ?>" min="0" required>
                                <label>Qty Sekarang</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="btn-group w-100 kondisi-group" data-item="<?php echo e($item->id); ?>" role="group">
                                <input type="radio" class="btn-check kondisi-radio" data-item="<?php echo e($item->id); ?>"
                                    name="items[<?php echo e($item->id); ?>][kondisi]" id="ada-<?php echo e($item->id); ?>" value="Ada"
                                    <?php echo e($kondisi == 'Ada' ? 'checked' : ''); ?> autocomplete="off">
                                <label class="btn btn-outline-success btn-sm" for="ada-<?php echo e($item->id); ?>">Ada</label>

                                <input type="radio" class="btn-check kondisi-radio" data-item="<?php echo e($item->id); ?>"
                                    name="items[<?php echo e($item->id); ?>][kondisi]" id="rusak-<?php echo e($item->id); ?>" value="Rusak"
                                    <?php echo e($kondisi == 'Rusak' ? 'checked' : ''); ?> autocomplete="off">
                                <label class="btn btn-outline-warning btn-sm" for="rusak-<?php echo e($item->id); ?>">Rusak</label>

                                <input type="radio" class="btn-check kondisi-radio" data-item="<?php echo e($item->id); ?>"
                                    name="items[<?php echo e($item->id); ?>][kondisi]" id="hilang-<?php echo e($item->id); ?>" value="Hilang"
                                    <?php echo e($kondisi == 'Hilang' ? 'checked' : ''); ?> autocomplete="off">
                                <label class="btn btn-outline-danger btn-sm" for="hilang-<?php echo e($item->id); ?>">Hilang</label>
                            </div>

                            <div class="mt-2 alasan-wrap-<?php echo e($item->id); ?>"
                                style="display: <?php echo e(in_array($kondisi, ['Rusak', 'Hilang']) ? 'block' : 'none'); ?>;">
                                <textarea class="form-control form-control-sm" name="items[<?php echo e($item->id); ?>][alasan]"
                                    placeholder="<?php echo e($kondisi == 'Hilang' ? 'Catatan (opsional)...' : 'Alasan kerusakan...'); ?>"><?php echo e(old("items.{$item->id}.alasan", $item->alasan)); ?></textarea>
                            </div>

                            <div class="mt-2 metode-wrap-<?php echo e($item->id); ?>"
                                style="display: <?php echo e($kondisi == 'Hilang' ? 'block' : 'none'); ?>;">
                                <select class="form-select form-select-sm" name="items[<?php echo e($item->id); ?>][metode_ganti]">
                                    <option value="">-- Metode Ganti --</option>
                                    <option value="Beli Sendiri"
                                        <?php echo e(old("items.{$item->id}.metode_ganti", $item->metode_ganti) == 'Beli Sendiri' ? 'selected' : ''); ?>>
                                        Beli Sendiri</option>
                                    <option value="Potong Bonus"
                                        <?php echo e(old("items.{$item->id}.metode_ganti", $item->metode_ganti) == 'Potong Bonus' ? 'selected' : ''); ?>>
                                        Potong Bonus</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <?php if($item->foto_audit): ?>
                                <img src="<?php echo e(asset($item->foto_audit)); ?>" alt="foto audit"
                                    style="width:100%;max-width:100px;aspect-ratio:1/1;object-fit:cover;border-radius:6px;">
                                <div class="small text-muted mt-1">Foto Audit saat ini</div>
                            <?php else: ?>
                                <div class="text-muted small">Belum ada foto</div>
                            <?php endif; ?>
                            <input type="file" class="form-control form-control-sm mt-2" accept="image/*"
                                name="items[<?php echo e($item->id); ?>][foto_audit]">
                            <div class="small text-muted mt-1">Kosongkan kalau tidak ganti foto.</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <button type="submit" class="btn btn-warning mb-4">
            <i class="mdi mdi-content-save"></i> Simpan Perubahan
        </button>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('page-script'); ?>
    <script>
        document.querySelectorAll('.kondisi-radio').forEach(function (radio) {
            radio.addEventListener('change', function () {
                var id = this.getAttribute('data-item');
                var alasanWrap = document.querySelector('.alasan-wrap-' + id);
                var metodeWrap = document.querySelector('.metode-wrap-' + id);
                if (alasanWrap) {
                    alasanWrap.style.display = (this.value === 'Rusak' || this.value === 'Hilang') ? 'block' : 'none';
                    var textarea = alasanWrap.querySelector('textarea');
                    if (textarea) textarea.placeholder = this.value === 'Hilang' ? 'Catatan (opsional)...' : 'Alasan kerusakan...';
                }
                if (metodeWrap) metodeWrap.style.display = this.value === 'Hilang' ? 'block' : 'none';
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/admin/tool-audit-verification/edit.blade.php ENDPATH**/ ?>