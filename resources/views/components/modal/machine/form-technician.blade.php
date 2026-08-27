<form action="{{ route('store.machine-technician') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal animate__animated animate__fadeIn" id="createMachine" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Create New Machine
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
                        <div class="divider-text"><span class="fw-semibold">Machine</span></div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select form-select-lg select2-machine-client-search"
                                    data-allow-clear="true" name="id_client" id="selectclient">
                                </select>
                                <label for="select2Basic">Client</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="brand" class="form-control" name="brand"
                                    placeholder="Example: Kaeser" value="{{ old('brand') }}">
                                <label for="brand">Brand</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="typeAnimation" class="form-control" name="type"
                                    placeholder="Example: CEO" value="{{ old('type') }}">
                                <label for="typeAnimation">Type</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="serialNumberAnimation" class="form-control"
                                    name="serial_number" placeholder="exampe: CSD-27"
                                    value="{{ old('serial_number') }}">
                                <label for="serialNumberAnimation">Serial Number</label>
                            </div>
                        </div>
                        <div class="col-3 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="phone" id="barAnimation" class="form-control" name="bar"
                                    placeholder="Example : 100" value="{{ old('bar') }}">
                                <label for="barAnimation">Bar</label>
                            </div>
                        </div>
                        <div class="col-3 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="phone" id="runningAnimation" class="form-control" name="running"
                                    placeholder="Example : 100" value="{{ old('running') }}">
                                <label for="runningAnimation">Running Hour</label>
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

<script>
    $(function () {
        // Select2 AJAX — dulu dropdown ini nge-dump SEMUA client (ribuan baris)
        // langsung ke DOM tiap kali modal ke-render, sekarang cuma di-load pas diketik.
        $('#selectclient').select2({
            dropdownParent: $('#createMachine'),
            placeholder: '----- Select Client -----',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: '/db/client/search-all',
                dataType: 'json',
                delay: 300,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (c) {
                            var text = c.company + (c.sales ? ' || ' + c.sales.name : '');
                            return { id: c.id, text: text };
                        })
                    };
                },
            },
        });
    });
</script>
