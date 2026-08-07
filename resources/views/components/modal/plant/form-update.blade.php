<form action="{{ route('plant.crm.update', $plant->id) }}" method="post">
    @csrf
    @method('patch')
    <div class="modal animate__animated animate__fadeIn" id="{{ 'updatePlant-' . strval($plant->id) }}"
        tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ 'Update ' }}Plant</h4>
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
                        <div class="col-12 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="namePlantAnimation-{{ $plant->id }}" class="form-control"
                                    name="namePlant" placeholder="example: Plant 2 - Cikarang"
                                    value="{{ old('namePlant', $plant->name) }}">
                                <label for="namePlantAnimation-{{ $plant->id }}">Nama Plant</label>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-floating form-floating-outline">
                                <textarea id="addressPlantAnimation-{{ $plant->id }}" class="form-control" name="addressPlant"
                                    style="height: 100px" placeholder="Alamat lengkap plant">{{ old('addressPlant', $plant->address) }}</textarea>
                                <label for="addressPlantAnimation-{{ $plant->id }}">Alamat Plant</label>
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
