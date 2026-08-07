<form action="{{ route('pic.cust.store', $customers->id) }}" method="post"
{{-- {{ @$pic ? route('pic.update', @$pic->$id) : route('pic.store') }} --}}

    enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf
    <div class="modal animate__animated animate__fadeIn"
        id="{{ 'createPic' }}" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">{{'Create New ' }} Pic
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
                    <div class="divider divider-dark mx-3">
                        <div class="divider-text"><span class="fw-semibold">Personal In Charge</span></div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="nameAnimation" class="form-control" name="namePic"
                                    placeholder="xxxxxxx xxxxxxxx"
                                    value="{{ old('namePic') }}">
                                <label for="nameAnimation">Name</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="positionAnimation" class="form-control" name="position"
                                    placeholder="example: CEO"
                                    value="{{ old('position') }}">
                                <label for="positionAnimation">Position</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="emailPicAnimation" class="form-control" name="emailPic"
                                    placeholder="xxxxxxxx@xxx.xx"
                                    value="{{ old('emailPic') }}">
                                <label for="emailPicAnimation">Email PIC</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="phone" id="phonePicAnimation" class="form-control" name="phonePic"
                                    placeholder="08xxxxxxxxxx"
                                    value="{{ old('phonePic') }}">
                                <label for="phonePicAnimation">Phone PIC</label>
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