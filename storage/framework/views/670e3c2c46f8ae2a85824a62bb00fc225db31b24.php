<?php $__env->startSection('title', 'Tambah Riwayat Perawatan Kendaraan'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-xl-8 col-md-10 col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-1"><?php echo e($fixed->code); ?></h4>
                    <p class="text-muted mb-4">
                        <?php echo e($fixed->jenis_kendaraan ?: '-'); ?>

                        <?php if($fixed->merk_model): ?> — <?php echo e($fixed->merk_model); ?> <?php endif; ?>
                        <?php if($fixed->plat_nomor): ?> (<?php echo e($fixed->plat_nomor); ?>) <?php endif; ?>
                    </p>
                    <form action="<?php echo e(route('fixed.maintenance.store', $fixed->id)); ?>" method="post">
                        <?php echo csrf_field(); ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="jenis" id="jenisInput" required>
                                        <option value="">Pilih Jenis</option>
                                        <option value="Servis">Servis</option>
                                        <option value="STNK & Pajak">STNK & Pajak</option>
                                        <option value="Ganti Kaleng">Ganti Kaleng</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                    <label for="jenisInput">Jenis Perawatan</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="date" class="form-control" name="tanggal" id="tanggalInput" required>
                                    <label for="tanggalInput">Tanggal</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="date" class="form-control" name="tanggal_jatuh_tempo" id="tanggalJatuhTempoInput">
                                    <label for="tanggalJatuhTempoInput">Jatuh Tempo Berikutnya (opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control rupiah-input" id="biayaDisplay"
                                        placeholder="0" autocomplete="off">
                                    <input type="hidden" name="biaya" id="biayaRaw">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" name="catatan" id="catatanInput" rows="2"
                                        placeholder="Catatan"></textarea>
                                    <label for="catatanInput">Catatan (opsional)</label>
                                </div>
                            </div>
                        </div>
                        <div class="float-end mt-4">
                            <a href="<?php echo e(route('fixed.show', $fixed->id)); ?>" class="btn btn-lg btn-outline-secondary">
                                Back
                            </a>
                            <button type="submit" class="btn btn-lg btn-primary">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('script'); ?>
    <script>
        document.getElementById('biayaDisplay')?.addEventListener('input', function() {
            var raw = this.value.replace(/\D/g, '');
            this.value = raw ? String(parseInt(raw)).replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
            document.getElementById('biayaRaw').value = raw || '';
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/fixed/form-maintenance.blade.php ENDPATH**/ ?>