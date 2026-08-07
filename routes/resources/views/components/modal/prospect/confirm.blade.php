<form action="" method="post" enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf
    <div class="modal modal-xl animate__animated animate__fadeIn" id="confirmProspect{{$prospect->id}}" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Create Prospect
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
                                <h5 class="fw-bold pb-1 mb-3">
                                    Details
                                </h5>
                                <p class="card-text">
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Address
                                    </div>
                                    <div class="col-9">
                                        : {{ $prospect->pic->client->address }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Sub Address
                                    </div>
                                    <div class="col-9">
                                        : {{ $prospect->pic->client->subAddress }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Area
                                    </div>
                                    <div class="col-9">
                                        : {{ $prospect->pic->client->area }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Phone
                                    </div>
                                    <div class="col-9">
                                        : {{ $prospect->pic->client->phone }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Email
                                    </div>
                                    <div class="col-9">
                                        : {{ $prospect->pic->client->email }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Mobile
                                    </div>
                                    <div class="col-9">
                                        : {{ $prospect->pic->client->mobile }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        R/U
                                    </div>
                                    <div class="col-9">
                                        : {{ $prospect->pic->client->ru }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Source
                                    </div>
                                    <div class="col-9">
                                        : {{ $prospect->pic->client->source }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Machine
                                    </div>
                                    <div class="col-9">
                                        : {{ $prospect->pic->client->machine }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        Assigned
                                    </div>
                                    <div class="col-9">
                                        : {{ $prospect->pic->client->sales->name }}
                                    </div>
                                </div>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card-body">
                                <h5 class="fw-bold pb-1 mb-3">
                                    PIC
                                </h5>
                                <p class="card-text">
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Name
                                    </div>
                                    <div class="col-9">
                                        : {{ $prospect->pic->name_pic }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Phone
                                    </div>
                                    <div class="col-9">
                                        : {{ $prospect->pic->phone_pic }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Position
                                    </div>
                                    <div class="col-9">
                                        : {{ $prospect->pic->position }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Email
                                    </div>
                                    <div class="col-9">
                                        : {{ $prospect->pic->email_pic }}
                                    </div>
                                </div>
                                </p>
                                <div class="prospect my-3">
                                    <h5>Prospect</h5>
                                    <div class="row">
                                        <div class="col-3">
                                            Prospect
                                        </div>
                                        <div class="col-9">
                                            : {{ $prospect->kebutuhan }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary waves-effect waves-light" id="withQuote"
                data-id="{{ $prospect->id }}">With Quote</button>
                </div>
            </div>
        </div>
    </div>
</form>
