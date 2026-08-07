<form action="{{ @$users ? route('update.target', @$users->id) : route('employee.store') }}" method="post"
    enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf

    @if (@$users)
        @method('patch')
    @endif
    <div class="modal animate__animated animate__fadeIn" id="{{ 'updateTarget-' . @$users->id }}" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">
                        {{ 'Update Target ' . @$users->name }}
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2 mt-1">
                    <div class="row mt-2 gy-4">
                        <h5 class="text-muted mb-0">
                            Target
                        </h5>
                        <div class="col-6 mt-2">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="number" id="leads" name="leads"
                                    value="{{ old('leads', @$users->target[0]->leads ?? '') }}" placeholder="61256996" />
                                <label for="leads">New Leads</label>
                            </div>
                        </div>
                        <div class="col-6 mt-2">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="number" id="dc" name="dc"
                                    value="{{ old('dc', @$users->target[0]->dc ?? '') }}" placeholder="61256996" />
                                <label for="dc">
                                    @if (@$users)
                                        {{ $users->id == '1' ? 'New Leads' : 'Daily Call' }}
                                    @else
                                    Daily Call
                                    @endif
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="number" id="crm" name="crm"
                                    value="{{ old('crm', @$users->target[0]->crm ?? '') }}" placeholder="61256996" />
                                <label for="crm">CRM</label>
                            </div>
                        </div>
                        @if (@$users->detail[0]->area != 'Bandung')
                            <div class="col-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="number" id="visit" name="visit"
                                        value="{{ old('visit', @$users->target[0]->visit ?? '') }}"
                                        placeholder="61256996" />
                                    <label for="visit">Visit</label>
                                </div>
                            </div>
                        @endif
                        <div class="col-6">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="number" id="quote" name="quote"
                                    value="{{ old('quote', @$users->target[0]->quote ?? '') }}"
                                    placeholder="61256996" />
                                <label for="quote">Quotation</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="total-label">Target Total</label>
                            <div class="input-group form-floating form-floating-outline" data-total="1">
                                <span class="input-group-text">Rp. </span>
                                <input type="text" class="form-control total-label" id="total-label" data-id="1"
                                    min="12" placeholder="Put total Here" data-type="currency"
                                    pattern="^[1-9]\d{0,2}(\.\d{3})*$" @focus="focused = true" @blur="focused = false"
                                    value="{{ old('total', @$users->target[0]->total ? number_format($users->target[0]->total, 0, '', '.') : '') }}">
                                <input class="form-control total" type="number" name="semuanya" id="semuanya"
                                    value="{{ old('total', @$users->target[0]->total ?? '') }}" hidden>
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
