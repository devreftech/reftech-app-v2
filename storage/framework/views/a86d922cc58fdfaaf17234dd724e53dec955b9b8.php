<form action="<?php echo e(route('new.position', $users->id)); ?>" method="post" enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>
    <div class="modal animate__animated animate__fadeIn" id="newPosition-<?php echo e($users->id); ?>" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">
                        New Position
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2 mt-1">
                    <div class="row mt-2 gy-4">
                        <h5 class="text-muted mb-0">
                            Employee Data
                        </h5>
                        <div class="col-md-6 mt-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="area" name="area"
                                    placeholder="Put Area here..." value="<?php echo e(old('area', @$detail[0]->area ?? '')); ?>" />
                                <label for="area">Area</label>
                            </div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="date" id="Date" name="date"
                                    value="<?php echo e(old('date', now()->format('Y-m-d'))); ?>">
                                <label for="Date">Date</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="position" name="position"
                                    placeholder="example: Sales Off store" value="<?php echo e(old('position', @$detail[0]->position ?? '')); ?>" />
                                <label for="position">Position</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" id="ddSales"
                                        aria-label="Default select example" name="role">
                                        <option value="Sales" <?php echo e(@$detail[0]->roles == 'Sales' ? 'selected' : ''); ?>>Sales</option>
                                        <option value="Technician" <?php echo e(@$detail[0]->roles == 'Technician' ? 'selected' : ''); ?>>Technician</option>
                                        <option value="Accounting" <?php echo e(@$detail[0]->roles == 'Accounting' ? 'selected' : ''); ?>>Accounting</option>
                                        <option value="Logistic" <?php echo e(@$detail[0]->roles == 'Logistic' ? 'selected' : ''); ?>>Logistic</option>
                                        <option value="Supervisor" <?php echo e(@$detail[0]->roles == 'Supervisor' ? 'selected' : ''); ?>>Supervisor</option>
                                    </select>
                                    <label for="exampleFormControlSelect1">Role select</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-2 gy-4" id="inputTarget" <?php echo e(@$users->role == 'Sales' ? '' : 'hidden="true"'); ?>>
                            <h5 class="text-muted mb-0">
                                Target
                            </h5>
                            <div class="col-6 mt-2">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="number" id="dc" name="dc"
                                        value="<?php echo e(old('dc', @$users->target[0]->dc ?? '')); ?>"
                                        placeholder="61256996" />
                                    <label for="dc">Daily Call</label>
                                </div>
                            </div>
                            <div class="col-6 mt-2">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="number" id="intro" name="intro"
                                        value="<?php echo e(old('intro', @$users->target[0]->intro ?? '')); ?>"
                                        placeholder="61256996" />
                                    <label for="intro">Introduce</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="number" id="quote" name="quote"
                                        value="<?php echo e(old('quote', @$users->target[0]->quote ?? '')); ?>"
                                        placeholder="61256996" />
                                    <label for="quote">Quantity</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="number" id="po" name="po"
                                        value="<?php echo e(old('po', @$users->target[0]->po ?? '')); ?>"
                                        placeholder="61256996" />
                                    <label for="po">Pruchase Order</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="total-label">Target Total</label>
                                <div class="input-group form-floating form-floating-outline" data-total="1">
                                    <span class="input-group-text">Rp. </span>
                                    <input type="text" class="form-control total-label" id="total-label"
                                        data-id="1" min="12" placeholder="Put total Here"
                                        data-type="currency" pattern="^[1-9]\d{0,2}(\.\d{3})*$"
                                        @focus="focused = true" @blur="focused = false"
                                        value="<?php echo e(old('total', @$users->target[0]->total ? number_format($users->target[0]->total, 0, '', '.') : '')); ?>">
                                    <input class="form-control total" type="number" name="total"
                                        id="total"
                                        value="<?php echo e(old('total', @$users->target[0]->total ?? '')); ?>" hidden>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/user/position.blade.php ENDPATH**/ ?>