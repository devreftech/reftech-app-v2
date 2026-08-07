<form action="{{ route('service-reports.image-v2', $service->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal modal-xl animate__animated animate__fadeIn" id="inputImageV2" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="exampleModalLabel5V2">Input Image (Baru) {{ $service->no_service }} -
                        {{ $service->pic->client->company }}
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
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="formFileMultiplePictV2" class="form-label">Picture (bebas jumlah,
                                    otomatis dipotong square)</label>
                                <input class="form-control" type="file" id="formFileMultiplePictV2" name="image[]"
                                    multiple accept="image/*" required>
                                <div class="row mt-3" id="photo-preview-v2"></div>
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
