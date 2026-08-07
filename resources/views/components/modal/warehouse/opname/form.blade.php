<form action="{{ route('opname.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    {{-- @if (@$product)
        @method('patch')
    @endif --}}
    <div class="modal animate__animated animate__fadeIn" id="createOpname" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5"> Create Stock Opname
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row g-2 mb-3">
                        <div class="col-12 ">
                            <div class="form-floating form-floating-outline mb-3">
                                <select class="form-select invoice-item-info" id="periode"
                                    aria-label="Default select example" name="periode">
                                    <option disabled>----- Info Periode -----</option>
                                    <option value="1">
                                        Periode Quarter 1
                                    </option>
                                    <option value="2">
                                        Periode Quarter 2
                                    </option>
                                    <option value="3">
                                        Periode Quarter 3
                                    </option>
                                    <option value="4">
                                        Periode Quarter 4
                                    </option>
                                </select>
                                <label for="exampleFormControlSelect1">Periode</label>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" name="note" id="note" placeholder="Text Note Here..."></textarea>
                                    <label for="note">Note</label>
                                </div>
                            </div>
                            <p class="text-muted">*note bisa dikosongkan</p>
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
    </div>
</form>
