<div class="modal fade" id="formHelpdesk" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buat Tiket Helpdesk</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('helpdesk.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="helpdeskTitle" class="form-control" name="title"
                                    placeholder="Judul singkat tiket" required>
                                <label for="helpdeskTitle">Judul</label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-floating form-floating-outline">
                                <textarea class="form-control" name="description" id="helpdeskDescription" style="height: 150px"
                                    placeholder="Jelaskan bug atau permintaan fitur secara detail" required></textarea>
                                <label for="helpdeskDescription">Deskripsi</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
