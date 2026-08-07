<div class="modal animate__animated animate__fadeIn" id="addMainLog" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="exampleModalLabel5"> Maintenance Log
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('store.daily-mainlog', $monitoring->id ?? '0') }}" method="post"
                    enctype="multipart/form-data" id="myForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-12 col-lg-6">
                            <label for="defaultInput" class="form-label">Maintenance Description</label>
                            <div class="input-group input-group-merge">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="main_desc"></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label for="defaultInput" class="form-label">Next Maintenance Planned</label>
                            <div class="input-group input-group-merge">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="main_next"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="defaultInput" class="form-label">Vendor</label>
                            <div class="input-group input-group-merge">
                                <input id="defaultInput" class="form-control" name="technici" type="text"
                                    placeholder="Vendor" value="{{Auth::user()->name}}" disabled>
                                <input id="defaultInput" class="form-control" name="technician" type="text"
                                    placeholder="Vendor" value="{{Auth::user()->name}}" hidden>
                            </div>
                        </div>
                    </div>
                    <div class="float-end">
                        <a href="{{ route('index.daily-monitoring', $machine->id) }}" type="button"
                            class="btn btn-lg btn-outline-secondary">
                            Back
                        </a>
                        <button :disabled="focused" type="submit" class="btn btn-lg btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
