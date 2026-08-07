<div class="modal animate__animated animate__fadeIn" id="createTemplate" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="exampleModalLabel5"> Create Template Machine
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('template.store') }}" method="post" enctype="multipart/form-data"
                    id="myForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-12 mb-3">
                            <div class="form-floating form-floating-outline mb-3">
                                <input class="form-control" type="text" name="brand" id="brand"
                                    placeholder="Type Brand Here...">
                                <label for="brand">Brand</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline mb-3">
                                <input class="form-control" type="text" name="sku" id="sku"
                                    placeholder="Type Machine Here...">
                                <label for="Machine">Machine</label>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-floating form-floating-outline mb-3">
                                <input class="form-control" type="number" name="kw" id="kw"
                                    placeholder="Type KW Here...">
                                <label for="kw">kw</label>
                            </div>
                        </div>
                        <div class="col fs-2 text-black text-center">/</div>
                        <div class="col-5">
                            <div class="form-floating form-floating-outline mb-3">
                                <input class="form-control" type="number" name="hp" id="hp"
                                    placeholder="Type Hp Here...">
                                <label for="Hp">Hp</label>
                            </div>
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
