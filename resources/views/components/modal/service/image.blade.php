<form action="{{ route('service-reports.image', $service->id) }}" method="post" enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf
    <div class="modal modal-xl animate__animated animate__fadeIn" id="inputImage" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="exampleModalLabel5">Input Image {{ $service->no_service }} -
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
                                <label for="formFileMultiplePict" class="form-label">Picture</label>
                                <input class="form-control" type="file" id="formFileMultiplePict" name="image[]"
                                    multiple="" accept="image/*">
                                <div class="row" id="photo-preview">
                                    @php
                                        $i = 1;
                                    @endphp
                                    <div id="dynamicInputsPhotoContainer">
                                    </div>
                                    @if (@$image)
                                        @foreach ($image as $item)
                                            <div class="photo-container">
                                                <img src="{{ url('') . '/' . $item->picture }}" alt=""
                                                    srcset="">
                                                <p>Photo {{ $i }} - {{ $item->keterangan }}</p>
                                            </div>
                                            <div id="dynamicInputsPhotoContainer">
                                                <input class="form-control mb-2" type="text" name="description[]"
                                                    placeholder="Deskripsi untuk File {{ $i }}"
                                                    value="{{ @$item->keterangan }}">
                                            </div>
                                            @php
                                                $i++;
                                            @endphp
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-12 mb-3">
                            <div id="dynamicInputsPhotoContainer">
                                @php
                                    $i = 1;
                                @endphp
                                @if (@$image)
                                    @foreach ($image as $item)
                                        <input class="form-control mb-2" type="text" name="description[]"
                                            placeholder="Deskripsi untuk File {{ $i }}"
                                            value="{{ @$item->keterangan }}">
                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach
                                @endif
                                <!-- Elemen input dinamis akan ditambahkan di sini -->
                            </div>
                        </div> --}}
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
