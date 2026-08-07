<form action="{{ @$libs ? route('library.update', $libs->id) : route('library.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (@$libs)
        @method('PATCH')
    @endif
    <div class="modal-onboarding modal fade animate__animated" id="formLibrary{{@$libs->id ?? ''}}" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">Create {{$type}}</h4>
                        <form>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="Name" class="form-control" name="name"
                                            placeholder="Put Name Here....." value="{{@$libs->name}}">
                                        <label for="Name">Name</label>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="Link" class="form-control" name="link"
                                            placeholder="Put Link Google Drive Here....." value="{{@$libs->link}}">
                                        <label for="Link">Link</label>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="Model" class="form-control" name="model"
                                            placeholder="Put Model Here....." value="{{@$libs->models}}">
                                        <label for="Model">Model</label>
                                    </div>
                                </div>
                                <input type="hidden" name="type" value="{{$type}}">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
