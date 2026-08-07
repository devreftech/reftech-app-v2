<form action="" method="post" enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf
    <div class="modal animate__animated animate__fadeIn" id="detailNotulen{{$notulen->id}}" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Detail Notulen
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card-body">
                                <div class="row mb-1">
                                    <div class="col-4">
                                        Mention To
                                    </div>
                                    <div class="col-8">
                                        : {{ $notulen->name }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-4">
                                        Title
                                    </div>
                                    <div class="col-8">
                                        : {{ $notulen->title }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-4">
                                        Date
                                    </div>
                                    <div class="col-8">
                                        : {{ $notulen->date->format('d-m-Y') }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-4">
                                        Description
                                    </div>
                                    <div class="col-8">
                                        <pre class="mb-0"
                                            style="font-size: 15px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $notulen->desc }}</pre>
                                    </div>
                                </div>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
