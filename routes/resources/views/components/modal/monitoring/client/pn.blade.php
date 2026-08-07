<div class="modal animate__animated animate__fadeIn" id="updatePn" tabindex="-1"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="exampleModalLabel5"> Update Status
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('monitoring.fajarPaper-updatePN', $monitoring->id) }}"
                    method="post" enctype="multipart/form-data" id="myForm">
                    @method('PATCH')
                    @csrf
                    <div class="row mb-3">
                        <div class="col-12 mb-3">
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="Text" class="form-control" name="pn" id="formLocation">
                                <label for="formLocation">Part Number</label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-floating form-floating-outline mb-3">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="desc"
                                    placeholder="Status Description here..."></textarea>
                                <label for="exampleFormControlTextarea1">Status Description</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating form-floating-outline mb-3">
                            <input type="Text" class="form-control" name="stock" id="formLocation">
                            <label for="formLocation">Stock</label>
                        </div>
                    </div>
                    <div class="float-end">
                        <button :disabled="focused" type="submit" class="btn btn-lg btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
