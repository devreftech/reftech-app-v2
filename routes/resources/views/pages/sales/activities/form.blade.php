<form action="{{route('action.leads',$leads->id)}}" method="post" enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf
    <div class="modal animate__animated animate__fadeIn" id="createAction{{$leads->id}}" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Create Daily Call</h4>
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
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="bs-datepicker-date" placeholder="MM/DD/YYYY"
                                    class="form-control" name="date" value="{{ \Carbon\Carbon::today()->format('d/m/Y') }}"
                                    disabled>
                                <label for="bs-datepicker-date">Date</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="date" id="follow_up" name="follow_up">
                                <label for="follow_up">Next Follow Up</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectAction" aria-label="Default select example" name="action">
                                    <option disabled>----- Choose Action -----</option>
                                    <option value="Phone Office">Phone Office</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                </select>
                                <label for="selectAction">Action</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectWeek" aria-label="Default select example" name="week">
                                    <option disabled>----- Choose Week -----</option>
                                    <option value="1">Week 1</option>
                                    <option value="2">Week 2</option>
                                    <option value="3">Week 3</option>
                                    <option value="4">Week 4</option>
                                    <option value="5">Week 5</option>
                                </select>
                                <label for="selectWeek">Week</label>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="scheduleAnimation" class="form-control" name="note"
                                    placeholder="Put Your Note Here...." value="-">
                                <label for="scheduleAnimation">Note</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectStatus" aria-label="Default select example"
                                    name="status">
                                    <option disabled>----- Choose Status -----</option>
                                    <option value="Responded">Responded</option>
                                    <option value="Not Respon">Not Responded</option>
                                </select>
                                <label for="selectStatus">Status</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectStatus" aria-label="Default select example"
                                    name="issues">
                                    @foreach ($issue as $issues)
                                        <option value="{{ $issues->id }}" {{ $issues->id == $leads->id_issues ? 'selected' : '' }}>{{ $issues->issue }}</option>
                                    @endforeach
                                </select>
                                <label for="selectStatus">Status</label>
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
