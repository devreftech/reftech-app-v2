<form action="{{ @$product ? route('unit.update', @$product->id) : route('unit-global.store') }}" method="post"
    enctype="multipart/form-data">
    @csrf
    @if (@$product)
        @method('patch')
    @endif
    <div class="modal animate__animated animate__fadeIn"
        id="{{ @$product ? 'updateProduct-' . @$product->id : 'createProduct' }}" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        {{ @$product ? 'Update Unit' : 'Create Unit' }}
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

                    {{-- SKU & Kategori --}}
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="sku" class="form-control" name="sku"
                                    placeholder="W XXX" value="{{ old('sku', @$product->sku ?? '') }}"
                                    autocomplete="off">
                                <label for="sku">SKU</label>
                            </div>
                            <div id="sku-check-result" class="mt-1" style="min-height:24px;font-size:0.82rem;"></div>
                        </div>
                        <div class="col-8">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="unit-category" name="unit">
                                    <option value="" disabled {{ !@$product ? 'selected' : '' }}>-- Pilih Kategori --</option>
                                    <option value="PISTON COMPRESSOR"
                                        {{ @$product->unit == 'PISTON COMPRESSOR' ? 'selected' : '' }}>Piston Compressor</option>
                                    <option value="AIR COMPRESSOR SCREW"
                                        {{ @$product->unit == 'AIR COMPRESSOR SCREW' ? 'selected' : '' }}>Air Compressor Screw</option>
                                    <option value="REFRIGERANT AIR DRYER"
                                        {{ @$product->unit == 'REFRIGERANT AIR DRYER' ? 'selected' : '' }}>Refrigerant Air Dryer</option>
                                    <option value="DESICANT DRYER"
                                        {{ @$product->unit == 'DESICANT DRYER' ? 'selected' : '' }}>Desiccant Dryer</option>
                                    <option value="FILTRATION SYSTEM"
                                        {{ @$product->unit == 'FILTRATION SYSTEM' ? 'selected' : '' }}>Filtration System</option>
                                    <option value="BOOSTER COMPRESSOR"
                                        {{ @$product->unit == 'BOOSTER COMPRESSOR' ? 'selected' : '' }}>Booster Compressor</option>
                                    <option value="AIR RECEIVER TANK"
                                        {{ @$product->unit == 'AIR RECEIVER TANK' ? 'selected' : '' }}>Air Receiver Tank</option>
                                </select>
                                <label>Kategori</label>
                            </div>
                        </div>
                    </div>

                    {{-- FIELD GRUP: COMPRESSOR (Piston & Screw) --}}
                    <div class="fields-compressor" style="display:none;">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="brand"
                                        placeholder="Brand" value="{{ old('brand', @$product->brand ?? '') }}">
                                    <label>Brand</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" id="select-type-unit" name="type_unit">
                                        <option value="" disabled selected>-- Type Compressor --</option>
                                        <option value="Oil-injected"
                                            {{ @$product->type_unit == 'Oil-injected' ? 'selected' : '' }}>Oil-injected</option>
                                        <option value="Oil-free Compressor"
                                            {{ @$product->type_unit == 'Oil-free Compressor' ? 'selected' : '' }}>Oil-free Compressor</option>
                                    </select>
                                    <label>Type Compressor</label>
                                </div>
                            </div>
                        </div>

                        {{-- Generation: muncul hanya untuk Oil-injected + AIR COMPRESSOR SCREW --}}
                        <div class="row g-2 mb-3" id="field-generation" style="display:none;">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="generation" id="select-generation">
                                        <option value="">-- Model Generation --</option>
                                        <option value="old"
                                            {{ @$product->generation == 'old' ? 'selected' : '' }}>Old Model</option>
                                        <option value="new"
                                            {{ @$product->generation == 'new' ? 'selected' : '' }}>New Model</option>
                                    </select>
                                    <label>Model Generation</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="model"
                                        placeholder="Model" value="{{ old('model', @$product->model ?? '') }}">
                                    <label>Model</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="desc"
                                        placeholder="Short Description" value="{{ old('desc', @$product->desc ?? '') }}">
                                    <label>Short Description</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="bar"
                                        placeholder="Max. Working Pressure" value="{{ old('bar', @$product->bar ?? '') }}">
                                    <span class="input-group-text">Bar</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="air_cap"
                                        placeholder="Air Capacity" value="{{ old('air_cap', @$product->air_cap ?? '') }}">
                                    <span class="input-group-text">m³/min</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="power">
                                        <option value="" disabled {{ !@$product->power ? 'selected' : '' }}>-- Motor Power --</option>
                                        <option value="7,5kW | 10Hp"   {{ @$product->power == '7,5kW | 10Hp'   ? 'selected' : '' }}>7,5kW | 10Hp</option>
                                        <option value="11kW | 15Hp"    {{ @$product->power == '11kW | 15Hp'    ? 'selected' : '' }}>11kW | 15Hp</option>
                                        <option value="15kW | 20Hp"    {{ @$product->power == '15kW | 20Hp'    ? 'selected' : '' }}>15kW | 20Hp</option>
                                        <option value="18kW | 25Hp"    {{ @$product->power == '18kW | 25Hp'    ? 'selected' : '' }}>18kW | 25Hp</option>
                                        <option value="22kW | 30Hp"    {{ @$product->power == '22kW | 30Hp'    ? 'selected' : '' }}>22kW | 30Hp</option>
                                        <option value="30kW | 40Hp"    {{ @$product->power == '30kW | 40Hp'    ? 'selected' : '' }}>30kW | 40Hp</option>
                                        <option value="37kW | 50Hp"    {{ @$product->power == '37kW | 50Hp'    ? 'selected' : '' }}>37kW | 50Hp</option>
                                        <option value="55kW | 75Hp"    {{ @$product->power == '55kW | 75Hp'    ? 'selected' : '' }}>55kW | 75Hp</option>
                                        <option value="75kW | 100Hp"   {{ @$product->power == '75kW | 100Hp'   ? 'selected' : '' }}>75kW | 100Hp</option>
                                        <option value="90kW | 120Hp"   {{ @$product->power == '90kW | 120Hp'   ? 'selected' : '' }}>90kW | 120Hp</option>
                                        <option value="110kW | 147Hp"  {{ @$product->power == '110kW | 147Hp'  ? 'selected' : '' }}>110kW | 147Hp</option>
                                        <option value="132kW | 177Hp"  {{ @$product->power == '132kW | 177Hp'  ? 'selected' : '' }}>132kW | 177Hp</option>
                                        <option value="160kW | 214Hp"  {{ @$product->power == '160kW | 214Hp'  ? 'selected' : '' }}>160kW | 214Hp</option>
                                    </select>
                                    <label>Motor Power</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="voltage">
                                        <option value="" disabled {{ !@$product->voltage ? 'selected' : '' }}>-- Rated Voltage --</option>
                                        <option value="220V/50Hz/1Phase" {{ @$product->voltage == '220V/50Hz/1Phase' ? 'selected' : '' }}>220V/50Hz/1Phase</option>
                                        <option value="380V/50Hz/3Phase" {{ @$product->voltage == '380V/50Hz/3Phase' ? 'selected' : '' }}>380V/50Hz/3Phase</option>
                                        <option value="400V/50Hz/3Phase" {{ @$product->voltage == '400V/50Hz/3Phase' ? 'selected' : '' }}>400V/50Hz/3Phase</option>
                                    </select>
                                    <label>Rated Voltage</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="connect">
                                        <option value="" disabled {{ !@$product->connect ? 'selected' : '' }}>-- Drive --</option>
                                        <option value="Direct Drive" {{ @$product->connect == 'Direct Drive' ? 'selected' : '' }}>Direct Drive</option>
                                        <option value="Belt"         {{ @$product->connect == 'Belt'         ? 'selected' : '' }}>Belt</option>
                                        <option value="Gear"         {{ @$product->connect == 'Gear'         ? 'selected' : '' }}>Gear</option>
                                    </select>
                                    <label>Drive</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="cooling">
                                        <option value="" disabled {{ !@$product->cooling ? 'selected' : '' }}>-- Cooling Method --</option>
                                        <option value="Air-Cooled"   {{ @$product->cooling == 'Air-Cooled'   ? 'selected' : '' }}>Air-Cooled</option>
                                        <option value="Water-Cooled" {{ @$product->cooling == 'Water-Cooled' ? 'selected' : '' }}>Water-Cooled</option>
                                    </select>
                                    <label>Cooling Method</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="exhaust"
                                        placeholder="Discharge Connection" value="{{ old('exhaust', @$product->exhaust ?? '') }}">
                                    <label>Discharge Connection</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="weight"
                                        placeholder="Weight" value="{{ old('weight', @$product->weight ?? '') }}">
                                    <span class="input-group-text">Kg</span>
                                </div>
                            </div>
                        </div>
                        @php
                            $dimParts = array_map('trim', explode('x', @$product->dimension ?? ''));
                        @endphp
                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1">Dimension (mm) — L × W × H</label>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control dim-input" id="dim_l"
                                        placeholder="Length" value="{{ old('dim_l', $dimParts[0] ?? '') }}">
                                    <label>Length (L)</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control dim-input" id="dim_w"
                                        placeholder="Width" value="{{ old('dim_w', $dimParts[1] ?? '') }}">
                                    <label>Width (W)</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control dim-input" id="dim_h"
                                        placeholder="Height" value="{{ old('dim_h', $dimParts[2] ?? '') }}">
                                    <label>Height (H)</label>
                                </div>
                            </div>
                            <input type="hidden" name="dimension" id="dim_combined"
                                value="{{ old('dimension', @$product->dimension ?? '') }}">
                        </div>
                    </div>

                    {{-- FIELD GRUP: BOOSTER COMPRESSOR --}}
                    <div class="fields-booster" style="display:none;">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="brand"
                                        placeholder="Brand" value="{{ old('brand', @$product->brand ?? '') }}">
                                    <label>Brand</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="model"
                                        placeholder="Model / Bare" value="{{ old('model', @$product->model ?? '') }}">
                                    <label>Model / Bare</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="inlet_pressure"
                                        placeholder="Inlet Pressure" value="{{ old('inlet_pressure', @$product->inlet_pressure ?? '') }}">
                                    <span class="input-group-text">Bar</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="outlet_pressure"
                                        placeholder="Outlet Pressure" value="{{ old('outlet_pressure', @$product->outlet_pressure ?? '') }}">
                                    <span class="input-group-text">Bar</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="inlet_cap"
                                        placeholder="Inlet Capacity (Low Pressure)" value="{{ old('inlet_cap', @$product->inlet_cap ?? '') }}">
                                    <span class="input-group-text">m³/min</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="outlet_cap"
                                        placeholder="Outlet Capacity (High Pressure)" value="{{ old('outlet_cap', @$product->outlet_cap ?? '') }}">
                                    <span class="input-group-text">m³/min</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="power">
                                        <option value="" disabled {{ !@$product->power ? 'selected' : '' }}>-- Motor Power --</option>
                                        <option value="7,5kW | 10Hp"   {{ @$product->power == '7,5kW | 10Hp'   ? 'selected' : '' }}>7,5kW | 10Hp</option>
                                        <option value="11kW | 15Hp"    {{ @$product->power == '11kW | 15Hp'    ? 'selected' : '' }}>11kW | 15Hp</option>
                                        <option value="15kW | 20Hp"    {{ @$product->power == '15kW | 20Hp'    ? 'selected' : '' }}>15kW | 20Hp</option>
                                        <option value="18kW | 25Hp"    {{ @$product->power == '18kW | 25Hp'    ? 'selected' : '' }}>18kW | 25Hp</option>
                                        <option value="22kW | 30Hp"    {{ @$product->power == '22kW | 30Hp'    ? 'selected' : '' }}>22kW | 30Hp</option>
                                        <option value="30kW | 40Hp"    {{ @$product->power == '30kW | 40Hp'    ? 'selected' : '' }}>30kW | 40Hp</option>
                                        <option value="37kW | 50Hp"    {{ @$product->power == '37kW | 50Hp'    ? 'selected' : '' }}>37kW | 50Hp</option>
                                        <option value="55kW | 75Hp"    {{ @$product->power == '55kW | 75Hp'    ? 'selected' : '' }}>55kW | 75Hp</option>
                                        <option value="75kW | 100Hp"   {{ @$product->power == '75kW | 100Hp'   ? 'selected' : '' }}>75kW | 100Hp</option>
                                        <option value="90kW | 120Hp"   {{ @$product->power == '90kW | 120Hp'   ? 'selected' : '' }}>90kW | 120Hp</option>
                                        <option value="110kW | 147Hp"  {{ @$product->power == '110kW | 147Hp'  ? 'selected' : '' }}>110kW | 147Hp</option>
                                        <option value="132kW | 177Hp"  {{ @$product->power == '132kW | 177Hp'  ? 'selected' : '' }}>132kW | 177Hp</option>
                                        <option value="160kW | 214Hp"  {{ @$product->power == '160kW | 214Hp'  ? 'selected' : '' }}>160kW | 214Hp</option>
                                    </select>
                                    <label>Motor Power</label>
                                </div>
                            </div>
                        </div>
                        @php
                            $dimPartsBst = array_map('trim', explode('x', @$product->dimension ?? ''));
                        @endphp
                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1">Dimension (mm) — L × W × H</label>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control dim-input-bst" id="dim_l_bst"
                                        placeholder="Length" value="{{ old('dim_l_bst', $dimPartsBst[0] ?? '') }}">
                                    <label>Length (L)</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control dim-input-bst" id="dim_w_bst"
                                        placeholder="Width" value="{{ old('dim_w_bst', $dimPartsBst[1] ?? '') }}">
                                    <label>Width (W)</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control dim-input-bst" id="dim_h_bst"
                                        placeholder="Height" value="{{ old('dim_h_bst', $dimPartsBst[2] ?? '') }}">
                                    <label>Height (H)</label>
                                </div>
                            </div>
                            <input type="hidden" name="dimension" id="dim_combined_bst"
                                value="{{ old('dimension', @$product->dimension ?? '') }}">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="weight"
                                        placeholder="Weight" value="{{ old('weight', @$product->weight ?? '') }}">
                                    <span class="input-group-text">Kg</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FIELD GRUP: REFRIGERANT AIR DRYER --}}
                    <div class="fields-ref-dryer" style="display:none;">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="brand"
                                        placeholder="Brand" value="{{ old('brand', @$product->brand ?? '') }}">
                                    <label>Brand</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="model"
                                        placeholder="Model" value="{{ old('model', @$product->model ?? '') }}">
                                    <label>Model</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="desc"
                                        placeholder="Short Description" value="{{ old('desc', @$product->desc ?? '') }}">
                                    <label>Short Description</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="air_cap"
                                        placeholder="FAD / Air Capacity" value="{{ old('air_cap', @$product->air_cap ?? '') }}">
                                    <label>FAD / Air Capacity</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="voltage">
                                        <option value="" disabled {{ !@$product->voltage ? 'selected' : '' }}>-- Rated Voltage --</option>
                                        <option value="220v/50Hz/1phase" {{ @$product->voltage == '220v/50Hz/1phase' ? 'selected' : '' }}>220v/50Hz/1phase</option>
                                        <option value="380v/50Hz/3phase" {{ @$product->voltage == '380v/50Hz/3phase' ? 'selected' : '' }}>380v/50Hz/3phase</option>
                                    </select>
                                    <label>Rated Voltage</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="power"
                                        placeholder="Consumption Power" value="{{ old('power', @$product->power ?? '') }}">
                                    <span class="input-group-text">kW</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="bar"
                                        placeholder="Pressure" value="{{ old('bar', @$product->bar ?? '') }}">
                                    <span class="input-group-text">Bar</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="refrigerant_type"
                                        placeholder="Refrigerant Type" value="{{ old('refrigerant_type', @$product->refrigerant_type ?? 'R410') }}">
                                    <label>Refrigerant Type</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="pdp"
                                        placeholder="PDP" value="{{ old('pdp', @$product->pdp ?? '3 °C') }}">
                                    <label>PDP</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="exhaust"
                                        placeholder="Connection" value="{{ old('exhaust', @$product->exhaust ?? '') }}">
                                    <label>Connection</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="weight"
                                        placeholder="Weight" value="{{ old('weight', @$product->weight ?? '') }}">
                                    <span class="input-group-text">Kg</span>
                                </div>
                            </div>
                        </div>
                        @php
                            $dimPartsDry = array_map('trim', explode('x', @$product->dimension ?? ''));
                        @endphp
                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1">Dimension (mm) — L × W × H</label>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control dim-input-dry" id="dim_l_dry"
                                        placeholder="Length" value="{{ old('dim_l_dry', $dimPartsDry[0] ?? '') }}">
                                    <label>Length (L)</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control dim-input-dry" id="dim_w_dry"
                                        placeholder="Width" value="{{ old('dim_w_dry', $dimPartsDry[1] ?? '') }}">
                                    <label>Width (W)</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control dim-input-dry" id="dim_h_dry"
                                        placeholder="Height" value="{{ old('dim_h_dry', $dimPartsDry[2] ?? '') }}">
                                    <label>Height (H)</label>
                                </div>
                            </div>
                            <input type="hidden" name="dimension" id="dim_combined_dry"
                                value="{{ old('dimension', @$product->dimension ?? '') }}">
                        </div>
                    </div>

                    {{-- FIELD GRUP: DESICCANT DRYER --}}
                    <div class="fields-desiccant" style="display:none;">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="brand"
                                        placeholder="Brand" value="{{ old('brand', @$product->brand ?? '') }}">
                                    <label>Brand</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="model"
                                        placeholder="Model" value="{{ old('model', @$product->model ?? '') }}">
                                    <label>Model</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="desc"
                                        placeholder="Short Description" value="{{ old('desc', @$product->desc ?? 'MODULAR DESICCANT DRYER') }}">
                                    <label>Short Description</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="air_cap"
                                        placeholder="FAD / Air Capacity" value="{{ old('air_cap', @$product->air_cap ?? '') }}">
                                    <label>FAD / Air Capacity</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="voltage">
                                        <option value="" disabled {{ !@$product->voltage ? 'selected' : '' }}>-- Rated Voltage --</option>
                                        <option value="220v/50Hz/1phase" {{ (@$product->voltage ?? '220v/50Hz/1phase') == '220v/50Hz/1phase' ? 'selected' : '' }}>220v/50Hz/1phase</option>
                                        <option value="380v/50Hz/3phase" {{ @$product->voltage == '380v/50Hz/3phase' ? 'selected' : '' }}>380v/50Hz/3phase</option>
                                    </select>
                                    <label>Rated Voltage</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="bar"
                                        placeholder="Pressure" value="{{ old('bar', @$product->bar ?? '') }}">
                                    <span class="input-group-text">Bar</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="pdp"
                                        placeholder="PDP" value="{{ old('pdp', @$product->pdp ?? '-40 °C') }}">
                                    <label>PDP</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="exhaust"
                                        placeholder="Connection" value="{{ old('exhaust', @$product->exhaust ?? '') }}">
                                    <label>Connection</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="weight"
                                        placeholder="Weight" value="{{ old('weight', @$product->weight ?? '') }}">
                                    <span class="input-group-text">Kg</span>
                                </div>
                            </div>
                        </div>
                        @php
                            $dimPartsDes = array_map('trim', explode('x', @$product->dimension ?? ''));
                        @endphp
                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <label class="form-label text-muted small mb-1">Dimension (mm) — L × W × H</label>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control dim-input-des" id="dim_l_des"
                                        placeholder="Length" value="{{ old('dim_l_des', $dimPartsDes[0] ?? '') }}">
                                    <label>Length (L)</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control dim-input-des" id="dim_w_des"
                                        placeholder="Width" value="{{ old('dim_w_des', $dimPartsDes[1] ?? '') }}">
                                    <label>Width (W)</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control dim-input-des" id="dim_h_des"
                                        placeholder="Height" value="{{ old('dim_h_des', $dimPartsDes[2] ?? '') }}">
                                    <label>Height (H)</label>
                                </div>
                            </div>
                            <input type="hidden" name="dimension" id="dim_combined_des"
                                value="{{ old('dimension', @$product->dimension ?? '') }}">
                        </div>
                    </div>

                    {{-- FIELD GRUP: FILTRATION SYSTEM --}}
                    <div class="fields-filtration" style="display:none;">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="brand"
                                        placeholder="Brand" value="{{ old('brand', @$product->brand ?? '') }}">
                                    <label>Brand</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="model"
                                        placeholder="Model" value="{{ old('model', @$product->model ?? '') }}">
                                    <label>Model</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="material"
                                        placeholder="Element Model" value="{{ old('material', @$product->material ?? '') }}">
                                    <label>Element Model</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="desc"
                                        placeholder="Short Description" value="{{ old('desc', @$product->desc ?? 'COMPRESSED FILTRATION SYSTEM') }}">
                                    <label>Short Description</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="air_cap"
                                        placeholder="FAD" value="{{ old('air_cap', @$product->air_cap ?? '') }}">
                                    <span class="input-group-text">m³/min</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="bar"
                                        placeholder="Pressure" value="{{ old('bar', @$product->bar ?? '') }}">
                                    <span class="input-group-text">Bar</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="exhaust"
                                        placeholder="Connection" value="{{ old('exhaust', @$product->exhaust ?? '') }}">
                                    <label>Connection</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="filtration"
                                        placeholder="Filtration" value="{{ old('filtration', @$product->filtration ?? '') }}">
                                    <span class="input-group-text">µm</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="oil_content"
                                        placeholder="Oil Content" value="{{ old('oil_content', @$product->oil_content ?? '') }}">
                                    <span class="input-group-text">ppm</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="grade">
                                        <option value="" disabled {{ !@$product->grade ? 'selected' : '' }}>-- Grade --</option>
                                        <option value="Pre-Filter"         {{ @$product->grade == 'Pre-Filter'         ? 'selected' : '' }}>Pre-Filter</option>
                                        <option value="After-Filter"       {{ @$product->grade == 'After-Filter'       ? 'selected' : '' }}>After-Filter</option>
                                        <option value="Particle-Filter"    {{ @$product->grade == 'Particle-Filter'    ? 'selected' : '' }}>Particle-Filter</option>
                                        <option value="Activated-Carbon"   {{ @$product->grade == 'Activated-Carbon'   ? 'selected' : '' }}>Activated-Carbon</option>
                                    </select>
                                    <label>Grade</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="connect">
                                        <option value="Integrated" {{ (@$product->connect ?? 'Integrated') == 'Integrated' ? 'selected' : '' }}>Integrated</option>
                                        <option value="Not Include" {{ @$product->connect == 'Not Include' ? 'selected' : '' }}>Not Include</option>
                                    </select>
                                    <label>Drain</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FIELD GRUP: AIR RECEIVER TANK --}}
                    <div class="fields-tank" style="display:none;">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="capacity"
                                        placeholder="Capacity" value="{{ old('capacity', @$product->capacity ?? '') }}">
                                    <span class="input-group-text">Liter</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="material"
                                        placeholder="Material" value="{{ old('material', @$product->material ?? '') }}">
                                    <label>Material</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="dimension"
                                        placeholder="Dimension" value="{{ old('dimension', @$product->dimension ?? '') }}">
                                    <label>Dimension</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="type_unit">
                                        <option value="" disabled {{ !@$product->type_unit ? 'selected' : '' }}>-- Tipe --</option>
                                        <option value="Vertical"   {{ @$product->type_unit == 'Vertical'   ? 'selected' : '' }}>Vertical</option>
                                        <option value="Horizontal" {{ @$product->type_unit == 'Horizontal' ? 'selected' : '' }}>Horizontal</option>
                                    </select>
                                    <label>Tipe</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="bar"
                                        placeholder="Working Pressure" value="{{ old('bar', @$product->bar ?? '10') }}">
                                    <span class="input-group-text">Bar</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" class="form-control" name="test_pressure"
                                        placeholder="Test Pressure" value="{{ old('test_pressure', @$product->test_pressure ?? '15') }}">
                                    <span class="input-group-text">Bar</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="grade"
                                        placeholder="T Plate" value="{{ old('grade', @$product->grade ?? '') }}">
                                    <label>T Plate</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="cooling">
                                        <option value="Disnaker" {{ (@$product->cooling ?? 'Disnaker') == 'Disnaker' ? 'selected' : '' }}>Disnaker</option>
                                        <option value="No" {{ @$product->cooling == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                    <label>Certification</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Note (semua kategori) --}}
                    <div class="row g-2 mb-3 fields-all" style="display:none;">
                        <div class="col">
                            <div class="form-floating form-floating-outline">
                                <textarea class="form-control h-px-100" name="note"
                                    placeholder="Note">{{ old('note', @$product->note ?? '') }}</textarea>
                                <label>Note</label>
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

@push('script')
<script>
(function () {
    var COMPRESSOR  = ['PISTON COMPRESSOR', 'AIR COMPRESSOR SCREW'];
    var FILTRATION  = ['FILTRATION SYSTEM'];
    var TANK        = ['AIR RECEIVER TANK'];
    var BOOSTER     = ['BOOSTER COMPRESSOR'];

    function showFields(category) {
        $('.fields-compressor, .fields-booster, .fields-ref-dryer, .fields-desiccant, .fields-filtration, .fields-tank, .fields-all').hide();
        $('#field-generation').hide();
        if (category === '') return;
        $('.fields-all').show();
        if (COMPRESSOR.includes(category)) {
            $('.fields-compressor').show();
            updateGenerationVisibility(category);
        } else if (BOOSTER.includes(category)) {
            $('.fields-booster').show();
        } else if (category === 'REFRIGERANT AIR DRYER') {
            $('.fields-ref-dryer').show();
        } else if (category === 'DESICANT DRYER') {
            $('.fields-desiccant').show();
        } else if (FILTRATION.includes(category)) {
            $('.fields-filtration').show();
        } else if (TANK.includes(category)) {
            $('.fields-tank').show();
        }
    }

    function updateGenerationVisibility(category) {
        var typeUnit = $('#select-type-unit').val();
        if (category === 'AIR COMPRESSOR SCREW' && typeUnit === 'Oil-injected') {
            $('#field-generation').show();
        } else {
            $('#field-generation').hide();
            $('#select-generation').val('');
        }
    }

    $(document).on('change', '#unit-category', function () {
        showFields($(this).val());
    });

    $(document).on('change', '#select-type-unit', function () {
        var category = $('#unit-category').val();
        updateGenerationVisibility(category);
    });

    // Live check SKU
    var skuTimer = null;
    $(document).on('input', '#sku', function () {
        var sku = $(this).val().trim();
        var $result = $('#sku-check-result');

        clearTimeout(skuTimer);
        $result.html('');

        if (sku.length < 2) return;

        skuTimer = setTimeout(function () {
            $.get('{{ route('unit-global.check-sku') }}', { sku: sku }, function (res) {
                if (res.exists) {
                    var detailUrl = '{{ url('/unit-global') }}/' + res.data.id;
                    $result.html(
                        '<span class="text-danger fw-semibold">' +
                        '<i class="mdi mdi-alert-circle-outline me-1"></i>SKU sudah terdaftar' +
                        ' — ' + (res.data.brand ?? '') + ' ' + (res.data.unit ?? '') +
                        ' <a href="' + detailUrl + '" target="_blank" class="text-primary">[Lihat Data]</a>' +
                        '</span>'
                    );
                } else {
                    $result.html(
                        '<span class="text-success fw-semibold">' +
                        '<i class="mdi mdi-check-circle-outline me-1"></i>SKU tersedia' +
                        '</span>'
                    );
                }
            });
        }, 400);
    });

    // Sebelum submit: disable input di section yang hidden + gabung dimension
    $(document).on('submit', 'form', function () {
        // Disable semua input dalam section yang sedang disembunyikan
        $('.fields-compressor, .fields-booster, .fields-ref-dryer, .fields-desiccant, .fields-filtration, .fields-tank').each(function () {
            if ($(this).is(':hidden')) {
                $(this).find('input, select, textarea').prop('disabled', true);
            }
        });

        if ($('#field-generation').is(':hidden')) {
            $('#select-generation').prop('disabled', true);
        }

        // Gabungkan L x W x H ke field dimension (compressor)
        var l = $('#dim_l').val().trim();
        var w = $('#dim_w').val().trim();
        var h = $('#dim_h').val().trim();
        if (l || w || h) {
            $('#dim_combined').val(l + ' x ' + w + ' x ' + h + ' mm');
        }

        // Gabungkan L x W x H ke field dimension (booster)
        var lb = $('#dim_l_bst').val().trim();
        var wb = $('#dim_w_bst').val().trim();
        var hb = $('#dim_h_bst').val().trim();
        if (lb || wb || hb) {
            $('#dim_combined_bst').val(lb + ' x ' + wb + ' x ' + hb + ' mm');
        }

        // Gabungkan L x W x H ke field dimension (ref dryer)
        var ld = $('#dim_l_dry').val().trim();
        var wd = $('#dim_w_dry').val().trim();
        var hd = $('#dim_h_dry').val().trim();
        if (ld || wd || hd) {
            $('#dim_combined_dry').val(ld + ' x ' + wd + ' x ' + hd + ' mm');
        }

        // Gabungkan L x W x H ke field dimension (desiccant)
        var ldes = $('#dim_l_des').val().trim();
        var wdes = $('#dim_w_des').val().trim();
        var hdes = $('#dim_h_des').val().trim();
        if (ldes || wdes || hdes) {
            $('#dim_combined_des').val(ldes + ' x ' + wdes + ' x ' + hdes + ' mm');
        }
    });

    // Saat edit — tampilkan field sesuai data yang sudah ada
    @if (@$product)
        showFields('{{ @$product->unit }}');
        @if (@$product->unit == 'AIR COMPRESSOR SCREW' && @$product->type_unit == 'Oil-injected')
            $('#field-generation').show();
        @endif
        @if (@$product->unit == 'DESICANT DRYER')
            // voltage default '220v/50Hz/1phase' already pre-selected via PHP; nothing extra needed
        @endif
    @endif
})();
</script>
@endpush
