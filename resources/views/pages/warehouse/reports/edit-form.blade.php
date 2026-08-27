<form id="formEditReport" action="" method="post">
    @csrf
    @method('PATCH')
    <div class="modal animate__animated animate__fadeIn" id="editReport" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Report</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="number" id="editYear" class="form-control" name="year"
                                    placeholder="Write Year Here.....">
                                <label for="editYear">Years</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="editSemester" name="semester">
                                    <option value="1">Semester 1</option>
                                    <option value="2">Semester 2</option>
                                </select>
                                <label for="editSemester">Semester</label>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <label for="editTargetLabel">Target Tim (Rp)</label>
                            <div class="input-group form-floating form-floating-outline">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control edit-total-label" id="editTargetLabel"
                                    placeholder="Target penjualan tim untuk semester ini">
                                <input type="number" class="form-control" name="target" id="editTarget" hidden>
                            </div>
                            <small class="text-muted">Opsional &mdash; dipakai untuk hitung pencapaian target di halaman Overview.</small>
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
