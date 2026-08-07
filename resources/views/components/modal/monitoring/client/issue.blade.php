<div class="modal animate__animated animate__fadeIn" id="editIssue{{ $monitor->id }}" tabindex="-1"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="exampleModalLabel5"> Edit Issue Monitoring {{ $monitor->tag }} | {{ $monitor->brand }}
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('monitoring.fajarPaper-editIssue', $monitor->id)}}" method="post"
                    enctype="multipart/form-data" id="myForm">
                    @method('PATCH')
                    @csrf
                    <div class="row mb-3">
                        <div class="col-6 mb-3">
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="date" class="form-control" name="date" id="formDate"
                                    value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                                <label for="formDate">Date</label>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="Text" class="form-control" name="" id="formLocation"
                                    value="{{ $monitor->location }}" disabled>
                                <label for="formLocation">Location </label>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="Text" class="form-control" name="" id="formLocation"
                                    value="{{ $monitor->tag }} | {{ $monitor->brand }}"
                                    disabled>
                                <label for="formLocation">Tag | Machine </label>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="Text" class="form-control" name="" id="formLocation"
                                    value="{{ $monitor->name }}" disabled>
                                <label for="formLocation">PIC </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline mb-3">
                                <textarea class="form-control" name="issue" id="formLocation">{{$monitor->issue}}</textarea>
                                <label for="exampleFormControlTextarea1">Issue</label>
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
