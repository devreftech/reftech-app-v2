<form action="{{ route('supplier.pic.update', $pic->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('patch')
    <div class="modal animate__animated animate__fadeIn" id="{{ 'updateSupplierPic-' . strval($pic->id) }}"
        tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update PIC</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="{{ 'picNameUpdate-' . $pic->id }}" class="form-control"
                                    name="namePic" placeholder="xxxxxxx xxxxxxxx"
                                    value="{{ old('namePic', $pic->name_pic) }}">
                                <label for="{{ 'picNameUpdate-' . $pic->id }}">Name</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="{{ 'picPositionUpdate-' . $pic->id }}" class="form-control"
                                    name="position" placeholder="example: Sales"
                                    value="{{ old('position', $pic->position) }}">
                                <label for="{{ 'picPositionUpdate-' . $pic->id }}">Position</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="{{ 'picEmailUpdate-' . $pic->id }}" class="form-control"
                                    name="emailPic" placeholder="xxxxxxxx@xxx.xx"
                                    value="{{ old('emailPic', $pic->email_pic) }}">
                                <label for="{{ 'picEmailUpdate-' . $pic->id }}">Email PIC</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="{{ 'picPhoneUpdate-' . $pic->id }}" class="form-control"
                                    name="phonePic" placeholder="08xxxxxxxxxx"
                                    value="{{ old('phonePic', $pic->phone_pic) }}">
                                <label for="{{ 'picPhoneUpdate-' . $pic->id }}">Phone PIC</label>
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
