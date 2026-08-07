
<?php $__env->startSection('title', 'Tools - ' . $technician->name); ?>
<?php $__env->startSection('content'); ?>
    <style>
        #toolMasterList .btn-pick-tool-master:last-child {
            border-bottom: 0;
        }

        #toolMasterList .btn-pick-tool-master:hover {
            background: #f6f6f7;
        }
    </style>
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Management Tools /</span> <?php echo e($technician->name); ?>

    </h4>
    <p class="text-muted mb-4"><?php echo e($technician->code ?? '-'); ?></p>

    <a href="<?php echo e(route('tool-assignment.index')); ?>" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="mdi mdi-arrow-left"></i> Kembali ke Daftar Teknisi
    </a>
    <button type="button" class="btn btn-primary btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addToolModal">
        <i class="mdi mdi-plus"></i> Tambah Tools
    </button>

    <div class="card mb-3">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Foto Awal</th>
                        <th>Nama Tools</th>
                        <th>Qty</th>
                        <th>Tanggal Serah Terima</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $tools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <?php if($tool->foto_awal): ?>
                                    <img src="<?php echo e(asset($tool->foto_awal)); ?>" alt="foto"
                                        style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($tool->toolsMaster->nama_tools ?? '-'); ?></td>
                            <td><?php echo e($tool->qty); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($tool->tanggal_serah_terima)->format('d M Y')); ?></td>
                            <td>
                                <?php if($tool->status_tools == 'Aktif'): ?>
                                    <span class="badge bg-label-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-label-secondary">Retired</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-inline-block">
                                    <a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end m-0">
                                        <li><a href="javascript:;" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#editTool-<?php echo e($tool->id); ?>">Edit</a></li>
                                        <li><a href="javascript:;" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#transferTool-<?php echo e($tool->id); ?>">Transfer PIC</a></li>
                                        <div class="dropdown-divider"></div>
                                        <li>
                                            <form action="<?php echo e(route('tool-assignment.retire', $tool->id)); ?>" method="post"
                                                onsubmit="return confirm('<?php echo e($tool->status_tools == 'Aktif' ? 'Retired-kan' : 'Aktifkan kembali'); ?> tools ini?');">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit"
                                                    class="dropdown-item <?php echo e($tool->status_tools == 'Aktif' ? 'text-danger' : ''); ?>">
                                                    <?php echo e($tool->status_tools == 'Aktif' ? 'Retired-kan' : 'Aktifkan Kembali'); ?>

                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada tools yang di-assign ke teknisi ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <form id="addToolForm" action="<?php echo e(route('tool-assignment.store', $technician->id)); ?>" method="post"
        enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id_tools_master" id="id_tools_master_input">
        <div class="modal fade" id="addToolModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Tools untuk <?php echo e($technician->name); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        
                        <div id="toolPickStep">
                            <input type="text" class="form-control mb-3" id="searchToolMaster"
                                placeholder="Cari Nama Tools / Kategori...">
                            <div id="toolMasterList" style="max-height:350px; overflow-y:auto; border:1px solid #d8d8dd; border-radius:.5rem; overflow-x:hidden;">
                                <?php $__empty_1 = true; $__currentLoopData = $toolMasters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $master): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <button type="button" class="btn-pick-tool-master"
                                        data-id="<?php echo e($master->id); ?>" data-name="<?php echo e($master->nama_tools); ?>"
                                        data-search="<?php echo e(strtolower($master->nama_tools . ' ' . $master->kategori)); ?>"
                                        style="display:block; width:100%; text-align:left; background:#fff; border:0; border-bottom:1px solid #eaeaec; padding:.7rem 1rem; cursor:pointer;">
                                        <div style="display:flex; justify-content:space-between;">
                                            <strong><?php echo e($master->nama_tools); ?></strong>
                                            <?php if($master->kategori): ?>
                                                <small class="text-muted"><?php echo e($master->kategori); ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <?php if($master->spesifikasi): ?>
                                            <div class="text-muted" style="font-size:12px;">
                                                <?php echo e(Str::limit($master->spesifikasi, 100)); ?></div>
                                        <?php endif; ?>
                                    </button>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <p class="text-muted text-center py-3 mb-0">Belum ada Master Tools. Tambahkan
                                        dulu di menu Tool Master.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <div id="toolFormStep" class="d-none">
                            <div
                                class="alert alert-light border d-flex justify-content-between align-items-center py-2 mb-3">
                                <div><i class="mdi mdi-check-circle text-success me-1"></i>
                                    <strong id="pickedToolName"></strong>
                                </div>
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary btn-change-tool-master">Ganti</button>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control" name="qty" min="1" value="1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Serah Terima</label>
                                <input type="date" class="form-control" name="tanggal_serah_terima"
                                    value="<?php echo e(now()->format('Y-m-d')); ?>" required>
                                <small class="text-muted">Bisa dimundurkan kalau tools ini sudah lama dipegang
                                    teknisi.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Foto Serah Terima (Baseline)</label>
                                <input type="file" class="form-control" name="foto_awal" accept="image/*" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan (opsional)</label>
                                <textarea class="form-control" name="desc" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary d-none" id="btnSubmitTool">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    
    <?php $__currentLoopData = $tools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <form action="<?php echo e(route('tool-assignment.update', $tool->id)); ?>" method="post" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('patch'); ?>
            <div class="modal fade" id="editTool-<?php echo e($tool->id); ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit <?php echo e($tool->toolsMaster->nama_tools ?? '-'); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control" name="qty" min="1"
                                    value="<?php echo e($tool->qty); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Serah Terima</label>
                                <input type="date" class="form-control" name="tanggal_serah_terima"
                                    value="<?php echo e(\Carbon\Carbon::parse($tool->tanggal_serah_terima)->format('Y-m-d')); ?>"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ganti Foto Serah Terima (opsional)</label>
                                <input type="file" class="form-control" name="foto_awal" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan</label>
                                <textarea class="form-control" name="desc" rows="2"><?php echo e($tool->desc); ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form action="<?php echo e(route('tool-assignment.transfer', $tool->id)); ?>" method="post">
            <?php echo csrf_field(); ?>
            <div class="modal fade" id="transferTool-<?php echo e($tool->id); ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Transfer PIC — <?php echo e($tool->toolsMaster->nama_tools ?? '-'); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Pindahkan ke Teknisi</label>
                                <select class="form-select" name="id_pic" required>
                                    <option value="" disabled selected>-- Pilih Teknisi --</option>
                                    <?php $__currentLoopData = $otherTechnicians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $other): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($other->id); ?>"><?php echo e($other->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Transfer</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php $__env->startPush('script'); ?>
        <script>
            function showToolPickStep() {
                $('#toolPickStep').removeClass('d-none');
                $('#toolFormStep').addClass('d-none');
                $('#btnSubmitTool').addClass('d-none');
            }

            function showToolFormStep(name) {
                $('#toolPickStep').addClass('d-none');
                $('#toolFormStep').removeClass('d-none');
                $('#btnSubmitTool').removeClass('d-none');
                $('#pickedToolName').text(name);
            }

            $(function() {
                $('#addToolModal').on('show.bs.modal', function() {
                    $('#addToolForm')[0].reset();
                    $('#id_tools_master_input').val('');
                    $('#searchToolMaster').val('');
                    $('#toolMasterList .btn-pick-tool-master').show();
                    showToolPickStep();
                });

                $('#searchToolMaster').on('keyup', function() {
                    var kw = $(this).val().toLowerCase();
                    $('#toolMasterList .btn-pick-tool-master').each(function() {
                        var text = $(this).data('search') || '';
                        $(this).toggle(text.toString().indexOf(kw) !== -1);
                    });
                });

                $(document).on('click', '.btn-pick-tool-master', function() {
                    $('#id_tools_master_input').val($(this).data('id'));
                    showToolFormStep($(this).data('name'));
                });

                $(document).on('click', '.btn-change-tool-master', function() {
                    showToolPickStep();
                });

                $('#addToolForm').on('submit', function(e) {
                    if (!$('#id_tools_master_input').val()) {
                        e.preventDefault();
                        showToolPickStep();
                    }
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/technician/tool-assignment/show.blade.php ENDPATH**/ ?>