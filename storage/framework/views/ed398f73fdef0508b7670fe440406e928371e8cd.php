<?php $__env->startSection('title', 'Kanban Board Portal'); ?>
<?php $__env->startSection('content'); ?>
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold mb-0">Kanban Board</h4>
        <?php if(auth()->user()->role === 'Admin'): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBoardModal">
                <i class="mdi mdi-plus me-1"></i> Create New Board
            </button>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $boards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $board): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-label-primary">Kanban Board</span>
                            <?php if(auth()->user()->role === 'Admin'): ?>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <form action="<?php echo e(route('kanban.boards.destroy', $board->id)); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus board ini beserta seluruh kolom dan tugas di dalamnya?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="mdi mdi-trash-can-outline me-1"></i> Delete Board
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h5 class="card-title fw-bold text-primary mb-2"><?php echo e($board->title); ?></h5>
                        <p class="card-text text-muted mb-4 flex-grow-1">
                            <?php echo e($board->description ?: 'No description provided.'); ?>

                        </p>
                        <div class="border-top pt-3 d-flex justify-content-between align-items-center mt-auto">
                            <small class="text-muted">Created by: <?php echo e($board->creator->name); ?></small>
                            <a href="<?php echo e(route('kanban.boards.show', $board->id)); ?>" class="btn btn-sm btn-outline-primary">
                                Open Board <i class="mdi mdi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center py-5">
                <div class="misc-wrapper">
                    <h2 class="mb-2 mx-2">Belum ada papan Kanban</h2>
                    <p class="mb-4 mx-2">Anda belum diundang ke papan Kanban mana pun, silakan hubungi Admin.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if(auth()->user()->role === 'Admin'): ?>
        <!-- Create Board Modal -->
        <div class="modal fade" id="createBoardModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Create New Kanban Board</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?php echo e(route('kanban.boards.store')); ?>" method="POST" id="createBoardForm">
                        <?php echo csrf_field(); ?>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">Board Title</label>
                                <input type="text" class="form-control" id="title" name="title" required placeholder="e.g., Kanban Divisi Teknisi">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2" placeholder="Describe the purpose of this board..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="member_ids" class="form-label">Assign Members</label>
                                <select class="select2 form-select" id="member_ids" name="member_ids[]" multiple="multiple" data-placeholder="Select users who can access this board">
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?> (<?php echo e($user->role); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Board Columns (List of Columns)</label>
                                    <button type="button" class="btn btn-xs btn-outline-primary" id="addColumnBtn">
                                        <i class="mdi mdi-plus"></i> Add Column
                                    </button>
                                </div>
                                <div id="columnInputsContainer">
                                    <div class="input-group mb-2 column-input-item">
                                        <input type="text" class="form-control" name="columns[]" required value="To Do">
                                        <button class="btn btn-outline-danger btn-remove-column" type="button"><i class="mdi mdi-close"></i></button>
                                    </div>
                                    <div class="input-group mb-2 column-input-item">
                                        <input type="text" class="form-control" name="columns[]" required value="In Progress">
                                        <button class="btn btn-outline-danger btn-remove-column" type="button"><i class="mdi mdi-close"></i></button>
                                    </div>
                                    <div class="input-group mb-2 column-input-item">
                                        <input type="text" class="form-control" name="columns[]" required value="Done">
                                        <button class="btn btn-outline-danger btn-remove-column" type="button"><i class="mdi mdi-close"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create Board</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            if ($('.select2').length) {
                $('.select2').each(function() {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        dropdownParent: $this.parent(),
                        placeholder: $this.data('placeholder')
                    });
                });
            }

            // Dynamic columns handling
            $('#addColumnBtn').click(function() {
                var html = `
                    <div class="input-group mb-2 column-input-item">
                        <input type="text" class="form-control" name="columns[]" required placeholder="New Column">
                        <button class="btn btn-outline-danger btn-remove-column" type="button"><i class="mdi mdi-close"></i></button>
                    </div>
                `;
                $('#columnInputsContainer').append(html);
            });

            $(document).on('click', '.btn-remove-column', function() {
                if ($('.column-input-item').length > 1) {
                    $(this).closest('.column-input-item').remove();
                } else {
                    alert('Minimal harus memiliki 1 kolom!');
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/kanban/index.blade.php ENDPATH**/ ?>