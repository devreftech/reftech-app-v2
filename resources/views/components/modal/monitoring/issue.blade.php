<div class="modal animate__animated animate__fadeIn" id="editIssue-{{ $comp['id'] }}" tabindex="-1"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="exampleModalLabel5"> Edit Monitoring {{ $comp['date'] }}
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('service-manager-daily.issue-update', [$comp['id'], $months]) }}" method="post"
                    enctype="multipart/form-data" id="myForm">
                    @method('PATCH')
                    @csrf
                    @if ($comp['unit'] != 'REFRIGERANT AIR DRYER')
                        <div class="daily-compressor">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Condition</label>
                                    <select id="conditionSelect" name="condition" class="form-select">
                                        <option value="Running" {{ $comp['condition'] == 'Running' ? 'selected' : '' }}>
                                            Running</option>
                                        <option value="Stand By"
                                            {{ $comp['condition'] == 'Stand By' ? 'selected' : '' }}>Stand By</option>
                                        <option value="Off" {{ $comp['condition'] == 'Off' ? 'selected' : '' }}>Off
                                        </option>
                                    </select>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Oil Level</label>
                                    <select id="offDisable" name="oil" class="form-select offDisable">
                                        <option value="OK" {{ $comp['oil_level'] == 'OK' ? 'selected' : '' }}>OK
                                        </option>
                                        <option value="Kurang" {{ $comp['oil_level'] == 'Kurang' ? 'selected' : '' }}>
                                            Kurang</option>
                                    </select>
                                </div>
                                <div class="col col-lg-3">
                                    <label for="defaultInput" class="form-label">Running</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="running"
                                            type="text" placeholder="Hr" min="1"
                                            oninput="validateInput(event)"
                                            value="{{ (int) str_replace('.', '', strtok($comp['running'], ' ')) }}">
                                        <span class="input-group-text">Hours</span>
                                    </div>
                                </div>
                                <div class="col col-lg-3">
                                    <label for="defaultInput" class="form-label">Loading Hr</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="loading"
                                            type="text" placeholder="Hr" min="1"
                                            oninput="validateInput(event)"
                                            value="{{ (int) str_replace('.', '', strtok($comp['loading'], ' ')) }}">
                                        <span class="input-group-text">Hours</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-lg-6">
                                    <label for="defaultInput" class="form-label">Pressure</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="pressure"
                                            type="text" placeholder="Bar" oninput="validateInput(event)"
                                            value="{{ (int) str_replace('.', '', subject: strtok($comp['pressure'], ' ')) }}">
                                        <span class="input-group-text">Bar</span>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultInput" class="form-label">Temperature</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="temperature"
                                            type="text" placeholder="°C" oninput="validateInput(event)"
                                            value="{{ (int) str_replace('.', '', subject: strtok($comp['temp'], ' ')) }}">
                                        <span class="input-group-text">°C</span>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Cek Kebocoran</label>
                                    <select id="offDisable" name="leak" class="form-select offDisable">
                                        <option value="">---------------</option>
                                        <option value="Ada" {{ $comp['leak'] == 'Ada' ? 'selected' : '' }}>Ada
                                        </option>
                                        <option value="Tidak Ada" {{ $comp['leak'] == 'Tidak Ada' ? 'selected' : '' }}>
                                            Tidak Ada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-floating form-floating-outline mb-3">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="issue"
                                    placeholder="Comments here...">{{ $comp['issue'] }}</textarea>
                                <label for="exampleFormControlTextarea1">Issue</label>
                            </div>
                        </div>
                    @else
                        <div class="daily-compressor">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Condition</label>
                                    <select id="conditionSelect" name="condition" class="form-select">
                                        <option value="Running"
                                            {{ $comp['condition'] == 'Running' ? 'selected' : '' }}>Running</option>
                                        <option value="Stand By"
                                            {{ $comp['condition'] == 'Stand By' ? 'selected' : '' }}>Stand By</option>
                                        <option value="Off" {{ $comp['condition'] == 'Off' ? 'selected' : '' }}>Off
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label for="defaultInput" class="form-label">Dew Point</label>
                                    <div class="input-group input-group-merge">
                                        <input id="offDisable" class="form-control offDisable" name="dew"
                                            type="text" placeholder="Dew Point"
                                            value="{{ (int) str_replace('.', '', strtok($comp['dew'], ' ')) }}">
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Auto Drain</label>
                                    <select id="offDisable" name="drain" class="form-select offDisable">
                                        <option value="Ok" {{ $comp['drain'] == 'Ok' ? 'selected' : '' }}>Ok
                                        </option>
                                        <option value="Not Ok" {{ $comp['drain'] == 'Not Ok' ? 'selected' : '' }}>Not
                                            Ok</option>
                                    </select>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Cek Kebocoran</label>
                                    <select id="offDisable" name="leak" class="form-select offDisable">
                                        <option value="">---------------</option>
                                        <option value="Ada" {{ $comp['leak'] == 'Ada' ? 'selected' : '' }}>Ada
                                        </option>
                                        <option value="Tidak Ada"
                                            {{ $comp['leak'] == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="defaultInput" class="form-label">Temperature In</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="temperature_in"
                                            type="text" placeholder="°C" oninput="validateInput(event)"
                                            value="{{ (int) str_replace('.', '', strtok($comp['temp'], ' ')) }}">
                                        <span class="input-group-text">°C</span>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label for="defaultInput" class="form-label">Temperature Out</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable"
                                            name="temperature_out" type="text" placeholder="°C"
                                            oninput="validateInput(event)"
                                            value="{{ (int) str_replace('.', '', strtok($comp['temp_out'], ' ')) }}">
                                        <span class="input-group-text">°C</span>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="defaultSelect" class="form-label">Cek Fan Kondensor</label>
                                    <select id="offDisable" name="fan" class="form-select offDisable">
                                        <option value="Ok" {{ $comp['fan'] == 'Ok' ? 'selected' : '' }}>Ok
                                        </option>
                                        <option value="Not Ok" {{ $comp['fan'] == 'Not Ok' ? 'selected' : '' }}>Not Ok
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-floating form-floating-outline mb-3">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="issue"
                                    placeholder="Comments here...">{{ $comp['issue'] }}</textarea>
                                <label for="exampleFormControlTextarea1">Issue</label>
                            </div>
                        </div>
                    @endif
                    {{-- <hr>
                        <h5 class="mb-3">MAINTENANCE LOG</h5>
                        <div class="row mb-3">
                            <div class="col-12 col-lg-6">
                                <label for="defaultInput" class="form-label">Maintenance Description</label>
                                <div class="input-group input-group-merge">
                                    <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="main_desc"></textarea>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <label for="defaultInput" class="form-label">Next Maintenance Planned</label>
                                <div class="input-group input-group-merge">
                                    <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="main_next"></textarea>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <label for="defaultInput" class="form-label">Vendor</label>
                                <div class="input-group input-group-merge">
                                    <input id="defaultInput" class="form-control" name="technician" type="text"
                                        placeholder="Vendor">
                                </div>
                            </div>
                        </div> --}}
                    <div class="float-end">
                        <button :disabled="focused" type="submit" class="btn btn-lg btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
