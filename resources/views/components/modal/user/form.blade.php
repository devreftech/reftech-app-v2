<form action="{{ @$users ? route('employee.update', @$users->id) : route('employee.store') }}" method="post"
    enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf

    @if (@$users)
        @method('patch')
    @endif
    <div class="modal animate__animated animate__fadeIn" id="{{ @$users ? 'updateUsers-' . @$users->id : 'createUsers' }}"
        tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">
                        {{ @$users ? 'Update Account ' . @$users->name : 'Create Account' }}
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        @if (@$users)
                            <img src="{{ url('') . '/' . @$users->image }}" alt="user-avatar"
                                class="d-block w-px-120 h-px-120 rounded" id="uploadedAvatar">
                        @else
                            <img src="{{ asset('asset') }}/profile/profile.jpg" alt="user-avatar"
                                class="d-block w-px-120 h-px-120 rounded" id="uploadedAvatar">
                        @endif
                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-3 waves-effect waves-light"
                                tabindex="0">
                                <span class="d-none d-sm-block">Upload new photo</span>
                                <i class="mdi mdi-tray-arrow-up d-block d-sm-none"></i>
                                <input type="file" id="upload" class="account-file-input" name="image"
                                    hidden="" accept="image/png, image/jpeg">
                            </label>
                            <button type="button"
                                class="btn btn-outline-secondary account-image-reset mb-3 waves-effect">
                                <i class="mdi mdi-reload d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Reset</span>
                            </button>

                            <div class="text-muted small">Allowed JPG, GIF or PNG. Max size of 800K</div>
                        </div>
                    </div>
                    <div class="row mt-2 gy-4">
                        <h5 class="text-muted mb-0">
                            Email
                        </h5>
                        <div class="col-md-6 mt-2 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" id="email" name="email"
                                    value="{{ old('email', @$users->email ?? '') }}"
                                    placeholder="john.doe@example.com" />
                                <label for="email">E-mail</label>
                            </div>
                        </div>
                        {{-- @if (empty($users)) --}}
                            <div class="col-md-6 mt-2">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" id="password"
                                            class="form-control"
                                            name="password" autocomplete="current-password" name="password"
                                            placeholder="············" aria-describedby="password">
                                        <label for="password">Password</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer">
                                        <i class="mdi mdi-eye-off-outline"></i>
                                    </span>
                                </div>
                            </div>
                        {{-- @endif --}}
                        {{-- <div class="col-md-6 mt-2"> --}}
                        {{-- <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                                <input type="password" id="password"
                                    class="form-control  @error('password') is-invalid @enderror" name="password"
                                    required autocomplete="current-password" name="password" placeholder="············"
                                    aria-describedby="password">
                                <label for="password">Password</label>
                            </div>
                            <span class="input-group-text cursor-pointer">
                                <i class="mdi mdi-eye-off-outline"></i>
                            </span>
                        </div> --}}
                        <h5 class="text-muted mb-0">
                            Profile
                        </h5>
                        <div class="col-6 mt-2">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="number" id="nip" name="nip"
                                    value="{{ old('nip', @$users->nip ?? '') }}" placeholder="61256996" />
                                <label for="nip">NIP</label>
                            </div>
                        </div>
                        <div class="col-6 mt-2">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="date" id="Date" name="birthday"
                                    value="{{ old('birthday', @$users->birthday ?? now()->format('Y-m-d')) }}">
                                <label for="Date">Birthday Date</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name', @$users->name ?? '') }}" placeholder="john doe" />
                                <label for="name">Name</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating form-floating-outline fv-plugins-icon-container">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">+62</span>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" pattern="[0-9]*"
                                            placeholder="8123094857" id="phone" name="phone"
                                            value="{{ old('phone', @$users->phone ? substr($users->phone, 3) : '') }}">
                                        <label for="phone">Phone</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <textarea class="form-control h-px-100" rows="2" placeholder="Write your note here...." name="address"
                                    id="address">{{ @$users->address }}</textarea>
                                <label for="address">Address</label>
                            </div>
                        </div>
                        <h5 class="text-muted mb-0">
                            Employee Data
                        </h5>
                        @if (empty($users))
                            <div class="col-md-6 mt-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="area" name="area"
                                        placeholder="Put Area here..." value="{{ old('area') }}" />
                                    <label for="area">Area</label>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="date" id="Date" name="date_in"
                                        value="{{ old('date_in', @$users->date_in ?? now()->format('Y-m-d')) }}"
                                        {{ @$users->date_in ? 'disabled' : '' }}>
                                    <label for="Date">Entry Date</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="position" name="position"
                                        placeholder="example: Sales Off store" value="{{ old('position') }}" />
                                    <label for="position">Position</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="ddSales"
                                            aria-label="Default select example" name="role">
                                            <option value="Sales">Sales</option>
                                            <option value="Technician">Technician</option>
                                            <option value="Accounting">Accounting</option>
                                            <option value="Logistic">Logistic</option>
                                            <option value="Supervisor">Supervisor</option>
                                            <option value="Coordinator">Service Coordinator</option>
                                            <option value="ServiceM">Service Admin</option>
                                            <option value="Client">Client</option>
                                        </select>
                                        <label for="exampleFormControlSelect1">Role select</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" id="code" name="code"
                                    value="{{ old('code', @$users->code ?? '') }}" placeholder="example: RZA" />
                                <label for="code">Code</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" id="ddSales" aria-label="Default select example"
                                        name="active">
                                        <option value="1" {{ @$users->active == '1' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ @$users->active == '0' ? 'selected' : '' }}>Non
                                            Active
                                        </option>
                                    </select>
                                    <label for="exampleFormControlSelect1">Active Status</label>
                                </div>
                            </div>
                        </div>

                    </div>
                    @if (empty($users))
                        <div class="row mt-2 gy-4" id="inputTarget">
                            <h5 class="text-muted mb-0">
                                Target
                            </h5>
                            <div class="col-6 mt-2">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="number" id="dc" name="dc"
                                        value="{{ old('dc', @$users->target[0]->dc ?? '') }}"
                                        placeholder="61256996" />
                                    <label for="dc">Daily Call</label>
                                </div>
                            </div>
                            <div class="col-6 mt-2">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="number" id="crm" name="crm"
                                        value="{{ old('crm', @$users->target[0]->crm ?? '') }}"
                                        placeholder="61256996" />
                                    <label for="crm">CRM</label>
                                </div>
                            </div>
                            <div class="col-6 mt-2">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="number" id="leads" name="leads"
                                        value="{{ old('leads', @$users->target[0]->leads ?? '') }}"
                                        placeholder="61256996" />
                                    <label for="leads">Leads</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="number" id="quote" name="quote"
                                        value="{{ old('quote', @$users->target[0]->quote ?? '') }}"
                                        placeholder="61256996" />
                                    <label for="quote">Quotation</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="number" id="po" name="po"
                                        value="{{ old('po', @$users->target[0]->po ?? '') }}"
                                        placeholder="61256996" />
                                    <label for="po">Pruchase Order</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="total-label">Target Total</label>
                                <div class="input-group form-floating form-floating-outline" data-total="1">
                                    <span class="input-group-text">Rp. </span>
                                    <input type="text" class="form-control total-label" id="total-label"
                                        data-id="1" min="12" placeholder="Put total Here"
                                        data-type="currency" pattern="^[1-9]\d{0,2}(\.\d{3})*$"
                                        @focus="focused = true" @blur="focused = false"
                                        value="{{ old('total', @$users->target[0]->total ? number_format($users->target[0]->total, 0, '', '.') : '') }}">
                                    <input class="form-control total" type="number" name="total" id="total"
                                        value="{{ old('total', @$users->target[0]->total ?? '') }}" hidden>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    </div>
</form>
