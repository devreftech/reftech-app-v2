<form action="{{ route('monitoring.fajarPaper-addMainlog', $monitoring->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal animate__animated animate__fadeIn" id="plusMainlog" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Link Maintenance Log
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
                        <div class="col-12 mb-3">
                            <div class="form-floating form-floating-outline mb-2">
                                <select class="select2 form-select" data-allow-clear="true" name="mainlog"
                                    data-id="1">
                                    <option> ---- Choose Mainlog Here ---- </option>
                                    @foreach ($maintenance as $mainlog)
                                        <option value="{{ $mainlog->id }}">
                                            {{ $mainlog->desc }} ( {{\Carbon\Carbon::parse($mainlog->date)->format('d-m-Y')}} ) - {{$mainlog->teknisi->name}}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="Unit" class="mb-2">Unit</label>
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
