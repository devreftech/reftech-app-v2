
<?php $__env->startSection('title', 'Sales Target Management'); ?>

<?php $__env->startPush('after-style'); ?>
<style>
    .year-tab { cursor: pointer; transition: all .15s; }
    .year-tab.active { background: #696cff !important; color: #fff !important; border-color: #696cff !important; }
    .input-annual { min-width: 180px; }
    .col-auto-val { color: #566a7f; font-size: .85rem; white-space: nowrap; text-align: right; display: block; }
    .col-num-header { text-align: right; }
    .trend-up   { color: #71dd37; }
    .trend-down { color: #ff3e1d; }
    .trend-flat { color: #a8aaae; }
    .history-table th { font-size: .78rem; background: #f5f5f9; }
    .history-table td { font-size: .82rem; vertical-align: middle; }
    tfoot.total-row td { background: #f0f0ff; font-weight: 600; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl flex-grow-1 container-p-y">

    
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Sales Target Management</h4>
            <p class="text-muted mb-0">Set target tahunan per sales — semester, bulanan & kontribusi dihitung otomatis</p>
        </div>
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-add-year">
            <i class="mdi mdi-plus me-1"></i>Tambah Tahun
        </button>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="mdi mdi-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    
    <div class="d-flex flex-wrap gap-2 mb-4">
        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $growth      = $yearGrowth[$y] ?? null;
                $isActive    = $y == $currentYear;
                $growthColor = $growth === null ? '' : ($growth >= 0 ? 'text-success' : 'text-danger');
                $growthIcon  = $growth === null ? '' : ($growth > 0 ? '↑' : ($growth < 0 ? '↓' : '→'));
            ?>
            <a href="<?php echo e(route('sales-target.index', ['year' => $y])); ?>"
               class="btn btn-sm btn-outline-secondary year-tab <?php echo e($isActive ? 'active' : ''); ?>"
               style="line-height:1.2; padding-top:6px; padding-bottom:6px;">
                <div><?php echo e($y); ?></div>
                <?php if($growth !== null): ?>
                    <div class="small <?php echo e($isActive ? 'text-white opacity-75' : $growthColor); ?>" style="font-size:.7rem">
                        <?php echo e($growthIcon); ?> <?php echo e($growth > 0 ? '+' : ''); ?><?php echo e($growth); ?>%
                    </div>
                <?php else: ?>
                    <div class="small text-muted" style="font-size:.7rem">—</div>
                <?php endif; ?>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php
        $s1 = $semesterRecords['1'] ?? null;
        $s2 = $semesterRecords['2'] ?? null;
        $existingAnnualAggregate = (($s1->target ?? 0) + ($s2->target ?? 0));
        $hasPerSalesHistory = $yearTargets->isNotEmpty();
    ?>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                Target Agregat Tim — <?php echo e($currentYear); ?>

                <span class="badge bg-label-secondary ms-2 fw-normal" style="font-size:.75rem">tanpa breakdown per-sales</span>
            </h5>
            <small class="text-muted">
                Gunakan ini untuk tahun historis (2024, 2025) atau jika target tim tidak perlu dipecah per individu.
                <?php if($hasPerSalesHistory): ?>
                    <span class="text-warning ms-2"><i class="mdi mdi-alert-outline"></i> Tahun ini sudah ada target per-sales — menyimpan agregat akan menimpa nilai semester.</span>
                <?php endif; ?>
            </small>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('sales-target.save-aggregate', $currentYear)); ?>" method="POST" id="form-aggregate">
                <?php echo csrf_field(); ?>
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Target / Tahun <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="agg-display"
                                value="<?php echo e($existingAnnualAggregate > 0 ? number_format($existingAnnualAggregate, 0, ',', '.') : ''); ?>"
                                placeholder="20.700.000.000" autocomplete="off">
                            <input type="hidden" name="target_annual" id="agg-hidden"
                                value="<?php echo e($existingAnnualAggregate > 0 ? $existingAnnualAggregate : ''); ?>">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label text-muted small">Semester 1 (÷2)</label>
                        <div class="fw-semibold text-end" id="agg-s1">
                            <?php echo e($existingAnnualAggregate > 0 ? 'Rp '.number_format(intval($existingAnnualAggregate/2),0,',','.') : '—'); ?>

                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label text-muted small">Semester 2 (÷2)</label>
                        <div class="fw-semibold text-end" id="agg-s2">
                            <?php echo e($existingAnnualAggregate > 0 ? 'Rp '.number_format(intval($existingAnnualAggregate/2),0,',','.') : '—'); ?>

                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label text-muted small">/ Bulan (÷12)</label>
                        <div class="text-muted text-end" id="agg-monthly">
                            <?php echo e($existingAnnualAggregate > 0 ? 'Rp '.number_format(intval($existingAnnualAggregate/12),0,',','.') : '—'); ?>

                        </div>
                    </div>
                    <div class="col-6 col-md-2 d-flex justify-content-end align-items-end">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="mdi mdi-content-save-outline me-1"></i>Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <form action="<?php echo e(route('sales-target.save-year', $currentYear)); ?>" method="POST" id="form-targets">
        <?php echo csrf_field(); ?>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0">Target Tim — <?php echo e($currentYear); ?></h5>
                    <small class="text-muted">Isi target tahunan per sales. Kolom lain dihitung otomatis.</small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-content-save-outline me-1"></i>Simpan Target <?php echo e($currentYear); ?>

                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0" id="target-table">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="min-width:160px">Sales</th>
                            <th style="min-width:200px">
                                Target / Tahun
                                <span class="text-muted fw-normal small">(input)</span>
                            </th>
                            <th class="text-end" style="min-width:160px">Semester 1 <span class="text-muted fw-normal small">(÷2)</span></th>
                            <th class="text-end" style="min-width:160px">Semester 2 <span class="text-muted fw-normal small">(÷2)</span></th>
                            <th class="text-end" style="min-width:140px">/ Bulan <span class="text-muted fw-normal small">(÷12)</span></th>
                            <th class="text-end" style="min-width:100px">% Kontribusi</th>
                            <th class="pe-4 text-center" style="min-width:80px">Histori</th>
                        </tr>
                    </thead>
                    <?php
                        $ecIds      = [16, 23];
                        $ecMembers  = $salesUsers->filter(fn($u) => in_array($u->id, $ecIds))->values();
                        $regSales   = $salesUsers->filter(fn($u) => !in_array($u->id, $ecIds))->values();
                        $ecAnnual   = $ecMembers->sum(fn($u) => $yearTargets[$u->id] ?? 0);
                    ?>
                    <tbody>
                        
                        <?php $__currentLoopData = $regSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $savedAnnual = $yearTargets[$user->id] ?? 0; ?>
                            <tr data-user-id="<?php echo e($user->id); ?>">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if($user->image): ?>
                                            <img src="<?php echo e(url('') . '/' . $user->image); ?>"
                                                class="rounded-circle" width="32" height="32"
                                                style="object-fit:cover" alt="<?php echo e($user->name); ?>">
                                        <?php else: ?>
                                            <span class="avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center fw-bold"
                                                style="width:32px;height:32px;font-size:13px;flex-shrink:0">
                                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                            </span>
                                        <?php endif; ?>
                                        <span class="fw-semibold" style="font-size:.88rem"><?php echo e($user->name); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control input-annual"
                                            value="<?php echo e($savedAnnual > 0 ? number_format($savedAnnual, 0, ',', '.') : ''); ?>"
                                            placeholder="0" autocomplete="off">
                                        <input type="hidden" name="targets[<?php echo e($user->id); ?>]"
                                            class="input-annual-hidden" value="<?php echo e($savedAnnual); ?>">
                                    </div>
                                </td>
                                <td class="text-end"><span class="col-auto-val col-s1"><?php echo e($savedAnnual > 0 ? 'Rp '.number_format(intval($savedAnnual/2),0,',','.') : '—'); ?></span></td>
                                <td class="text-end"><span class="col-auto-val col-s2"><?php echo e($savedAnnual > 0 ? 'Rp '.number_format(intval($savedAnnual/2),0,',','.') : '—'); ?></span></td>
                                <td class="text-end"><span class="col-auto-val col-monthly"><?php echo e($savedAnnual > 0 ? 'Rp '.number_format(intval($savedAnnual/12),0,',','.') : '—'); ?></span></td>
                                <td class="text-end"><span class="col-auto-val col-pct fw-semibold">
                                    <?php echo e(($teamTargetThisYear > 0 && $savedAnnual > 0) ? number_format($savedAnnual / $teamTargetThisYear * 100, 1).'%' : '—'); ?>

                                </span></td>
                                <td class="pe-4 text-center">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-secondary btn-history"
                                        data-user-id="<?php echo e($user->id); ?>" data-user-name="<?php echo e($user->name); ?>">
                                        <i class="mdi mdi-history"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        
                        <?php if($ecMembers->count() > 0): ?>
                        <tr class="table-active" id="row-ec-group">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar-initial rounded-circle bg-label-warning d-flex align-items-center justify-content-center"
                                        style="width:32px;height:32px;font-size:13px;flex-shrink:0">
                                        <i class="mdi mdi-account-group" style="font-size:15px"></i>
                                    </span>
                                    <div>
                                        <div class="fw-bold" style="font-size:.88rem">Tim E-Commerce</div>
                                        <small class="text-muted"><?php echo e($ecMembers->pluck('name')->join(' & ')); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-warning" id="ec-group-annual">
                                    <?php echo e($ecAnnual > 0 ? 'Rp '.number_format($ecAnnual,0,',','.') : '—'); ?>

                                </span>
                            </td>
                            <td class="text-end"><span class="col-auto-val" id="ec-group-s1"><?php echo e($ecAnnual > 0 ? 'Rp '.number_format(intval($ecAnnual/2),0,',','.') : '—'); ?></span></td>
                            <td class="text-end"><span class="col-auto-val" id="ec-group-s2"><?php echo e($ecAnnual > 0 ? 'Rp '.number_format(intval($ecAnnual/2),0,',','.') : '—'); ?></span></td>
                            <td class="text-end"><span class="col-auto-val" id="ec-group-monthly"><?php echo e($ecAnnual > 0 ? 'Rp '.number_format(intval($ecAnnual/12),0,',','.') : '—'); ?></span></td>
                            <td class="text-end"><span class="col-auto-val fw-semibold" id="ec-group-pct">
                                <?php echo e(($teamTargetThisYear > 0 && $ecAnnual > 0) ? number_format($ecAnnual / $teamTargetThisYear * 100, 1).'%' : '—'); ?>

                            </span></td>
                            <td></td>
                        </tr>

                        
                        <?php $__currentLoopData = $ecMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $savedAnnual = $yearTargets[$user->id] ?? 0; ?>
                            <tr data-user-id="<?php echo e($user->id); ?>" data-ec-member="true" class="ec-member-row">
                                <td class="ps-5" style="border-left: 3px solid #ffab00;">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted">└</span>
                                        <?php if($user->image): ?>
                                            <img src="<?php echo e(url('') . '/' . $user->image); ?>"
                                                class="rounded-circle" width="28" height="28"
                                                style="object-fit:cover" alt="<?php echo e($user->name); ?>">
                                        <?php else: ?>
                                            <span class="avatar-initial rounded-circle bg-label-warning d-flex align-items-center justify-content-center fw-bold"
                                                style="width:28px;height:28px;font-size:12px;flex-shrink:0">
                                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                            </span>
                                        <?php endif; ?>
                                        <span style="font-size:.85rem"><?php echo e($user->name); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control input-annual ec-input"
                                            value="<?php echo e($savedAnnual > 0 ? number_format($savedAnnual, 0, ',', '.') : ''); ?>"
                                            placeholder="0" autocomplete="off">
                                        <input type="hidden" name="targets[<?php echo e($user->id); ?>]"
                                            class="input-annual-hidden" value="<?php echo e($savedAnnual); ?>">
                                    </div>
                                </td>
                                <td class="text-end"><span class="col-auto-val col-s1"><?php echo e($savedAnnual > 0 ? 'Rp '.number_format(intval($savedAnnual/2),0,',','.') : '—'); ?></span></td>
                                <td class="text-end"><span class="col-auto-val col-s2"><?php echo e($savedAnnual > 0 ? 'Rp '.number_format(intval($savedAnnual/2),0,',','.') : '—'); ?></span></td>
                                <td class="text-end"><span class="col-auto-val col-monthly"><?php echo e($savedAnnual > 0 ? 'Rp '.number_format(intval($savedAnnual/12),0,',','.') : '—'); ?></span></td>
                                <td class="text-end"><span class="col-auto-val col-pct">
                                    <?php echo e(($teamTargetThisYear > 0 && $savedAnnual > 0) ? number_format($savedAnnual / $teamTargetThisYear * 100, 1).'%' : '—'); ?>

                                </span></td>
                                <td class="pe-4 text-center">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-secondary btn-history"
                                        data-user-id="<?php echo e($user->id); ?>" data-user-name="<?php echo e($user->name); ?>">
                                        <i class="mdi mdi-history"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="total-row">
                        <tr>
                            <td class="ps-4">Total Tim</td>
                            <td>
                                <span class="text-primary" id="total-annual">
                                    <?php echo e($teamTargetThisYear > 0 ? 'Rp '.number_format($teamTargetThisYear,0,',','.') : '—'); ?>

                                </span>
                            </td>
                            <td class="text-end"><span id="total-s1" class="col-auto-val"><?php echo e($teamTargetThisYear > 0 ? 'Rp '.number_format(intval($teamTargetThisYear/2),0,',','.') : '—'); ?></span></td>
                            <td class="text-end"><span id="total-s2" class="col-auto-val"><?php echo e($teamTargetThisYear > 0 ? 'Rp '.number_format(intval($teamTargetThisYear/2),0,',','.') : '—'); ?></span></td>
                            <td class="text-end"><span id="total-monthly" class="col-auto-val"><?php echo e($teamTargetThisYear > 0 ? 'Rp '.number_format(intval($teamTargetThisYear/12),0,',','.') : '—'); ?></span></td>
                            <td class="text-end text-muted small">100%</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </form>

    
    <div class="alert alert-info d-flex align-items-start mt-4" role="alert">
        <i class="mdi mdi-information-outline me-3 mt-1 fs-5 flex-shrink-0"></i>
        <div class="small">
            <strong>Target / Semester</strong> dan <strong>Target Laporan Overview</strong> akan diupdate otomatis saat kamu simpan.
            Untuk data 2024 &amp; 2025 yang belum ada histori per-sales, target semester tetap bisa dilihat di halaman laporan.
        </div>
    </div>
</div>


<div class="modal fade" id="modal-history" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-history me-2"></i>
                    Histori Target — <span id="history-sales-name">—</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table history-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Tahun</th>
                                <th>Target / Tahun</th>
                                <th>Semester 1 &amp; 2</th>
                                <th>/ Bulan</th>
                                <th>% Kontribusi</th>
                                <th class="pe-4">Tren</th>
                            </tr>
                        </thead>
                        <tbody id="history-tbody">
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada histori.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-add-year" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:360px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Tahun Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('sales-target.add-year')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <label for="input-new-year" class="form-label">Tahun <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="input-new-year" name="year"
                        min="2020" max="2099" placeholder="<?php echo e(date('Y') + 1); ?>" required>
                    <div class="form-text">Akan membuat Semester 1 &amp; 2 untuk tahun tersebut.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-plus me-1"></i>Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-script'); ?>
<script>
(function () {
    // ── Histori data dari server ──────────────────────────────────────────────
    const allHistories = <?php echo json_encode($allHistories, 15, 512) ?>;
    const teamByYear   = <?php echo json_encode($teamTargetByYear, 15, 512) ?>;

    // ── Aggregate form auto-calc ──────────────────────────────────────────────
    const aggDisplay = document.getElementById('agg-display');
    if (aggDisplay) {
        aggDisplay.addEventListener('input', function () {
            const raw = parseRaw(this.value);
            this.value = raw > 0 ? raw.toLocaleString('id-ID') : '';
            document.getElementById('agg-hidden').value   = raw || '';
            document.getElementById('agg-s1').textContent = raw > 0 ? fmt(Math.floor(raw / 2))  : '—';
            document.getElementById('agg-s2').textContent = raw > 0 ? fmt(Math.floor(raw / 2))  : '—';
            document.getElementById('agg-monthly').textContent = raw > 0 ? fmt(Math.floor(raw / 12)) : '—';
        });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    function parseRaw(val) {
        return parseInt(String(val).replace(/\./g, '').replace(/,/g, '').replace(/[^0-9]/g, '')) || 0;
    }

    function fmt(num) {
        if (!num || num === 0) return '—';
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    function pct(part, total) {
        if (!total || total === 0) return '—';
        return (part / total * 100).toFixed(1) + '%';
    }

    // ── Auto-calc: update semua baris + E-Commerce group + total ────────────
    const ecIds = [16, 23];

    function recalcAll() {
        let total   = 0;
        let ecTotal = 0;

        // Sum semua input (termasuk E-Commerce members)
        document.querySelectorAll('.input-annual').forEach(inp => {
            const val = parseRaw(inp.value);
            total += val;
            const userId = parseInt(inp.closest('tr').dataset.userId);
            if (ecIds.includes(userId)) ecTotal += val;
        });

        // Update baris sales biasa & E-Commerce sub-rows
        document.querySelectorAll('#target-table tbody tr[data-user-id]').forEach(row => {
            const inp = row.querySelector('.input-annual');
            if (!inp) return;
            const val = parseRaw(inp.value);
            row.querySelector('.col-s1').textContent      = val > 0 ? fmt(Math.floor(val / 2))  : '—';
            row.querySelector('.col-s2').textContent      = val > 0 ? fmt(Math.floor(val / 2))  : '—';
            row.querySelector('.col-monthly').textContent = val > 0 ? fmt(Math.floor(val / 12)) : '—';
            row.querySelector('.col-pct').textContent     = val > 0 ? pct(val, total) : '—';
        });

        // Update E-Commerce group row
        const ecGroup = document.getElementById('row-ec-group');
        if (ecGroup) {
            document.getElementById('ec-group-annual').textContent  = ecTotal > 0 ? fmt(ecTotal)                  : '—';
            document.getElementById('ec-group-s1').textContent      = ecTotal > 0 ? fmt(Math.floor(ecTotal / 2))  : '—';
            document.getElementById('ec-group-s2').textContent      = ecTotal > 0 ? fmt(Math.floor(ecTotal / 2))  : '—';
            document.getElementById('ec-group-monthly').textContent = ecTotal > 0 ? fmt(Math.floor(ecTotal / 12)) : '—';
            document.getElementById('ec-group-pct').textContent     = ecTotal > 0 ? pct(ecTotal, total) : '—';
        }

        // Update total row
        document.getElementById('total-annual').textContent  = total > 0 ? fmt(total)                  : '—';
        document.getElementById('total-s1').textContent      = total > 0 ? fmt(Math.floor(total / 2))  : '—';
        document.getElementById('total-s2').textContent      = total > 0 ? fmt(Math.floor(total / 2))  : '—';
        document.getElementById('total-monthly').textContent = total > 0 ? fmt(Math.floor(total / 12)) : '—';
    }

    // ── Input listeners ───────────────────────────────────────────────────────
    document.querySelectorAll('.input-annual').forEach(inp => {
        inp.addEventListener('input', function () {
            const raw = parseRaw(this.value);
            this.value = raw > 0 ? raw.toLocaleString('id-ID') : '';
            this.closest('tr').querySelector('.input-annual-hidden').value = raw || '';
            recalcAll();
        });
    });

    // ── Modal Histori ─────────────────────────────────────────────────────────
    document.querySelectorAll('.btn-history').forEach(btn => {
        btn.addEventListener('click', function () {
            const userId   = this.dataset.userId;
            const userName = this.dataset.userName;
            document.getElementById('history-sales-name').textContent = userName;

            const tbody    = document.getElementById('history-tbody');
            const rows     = allHistories[userId] ?? [];

            if (rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Belum ada histori target untuk sales ini.</td></tr>';
            } else {
                let html = '';
                let prevAnnual = null;

                rows.forEach((h, idx) => {
                    const annual    = parseInt(h.target_annual);
                    const teamTotal = parseInt(teamByYear[h.year] ?? 0);
                    const semester  = Math.floor(annual / 2);
                    const monthly   = Math.floor(annual / 12);
                    const pctVal    = teamTotal > 0 ? (annual / teamTotal * 100).toFixed(1) + '%' : '—';

                    let trend = '';
                    if (prevAnnual !== null) {
                        const diff = annual - prevAnnual;
                        const diffPct = prevAnnual > 0 ? ((diff / prevAnnual) * 100).toFixed(1) : 0;
                        if (diff > 0) {
                            trend = `<span class="trend-up"><i class="mdi mdi-trending-up"></i> +${diffPct}%</span>`;
                        } else if (diff < 0) {
                            trend = `<span class="trend-down"><i class="mdi mdi-trending-down"></i> ${diffPct}%</span>`;
                        } else {
                            trend = `<span class="trend-flat"><i class="mdi mdi-minus"></i> Tetap</span>`;
                        }
                    }

                    html += `<tr>
                        <td class="ps-4 fw-semibold">${h.year}</td>
                        <td class="text-success fw-semibold">Rp ${annual.toLocaleString('id-ID')}</td>
                        <td class="col-auto-val">Rp ${semester.toLocaleString('id-ID')}</td>
                        <td class="col-auto-val">Rp ${monthly.toLocaleString('id-ID')}</td>
                        <td>${pctVal}</td>
                        <td class="pe-4">${trend || '<span class="text-muted">—</span>'}</td>
                    </tr>`;
                    prevAnnual = annual;
                });
                tbody.innerHTML = html;
            }

            new bootstrap.Modal(document.getElementById('modal-history')).show();
        });
    });
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/admin/sales-target.blade.php ENDPATH**/ ?>