    @extends('layouts.sales.app')
    @section('title', 'Edit Quotation')
    @section('content')
        @php
            $id = 0;
            $dataDetail = 0;
        @endphp
        <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework"
            action="{{ route('update-sparepart.quotation', $quotation->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('patch')
            <div class="form-floating mb-3">
                <input type="text" class="form-control fw-bold fs-3" id="floatingInputFilled"
                    aria-describedby="floatingInputFilledHelp" name="no_quote"
                    value="{{ old('no_quote', @$quotation->no_quote ? $quotation->no_quote : $formattedNumberQ . '-P/BDG/RJO-' . Auth::user()->code . '/' . $formattedMonthNow . '/' . \Carbon\Carbon::now()->year) }}">
                <label for="floatingInputFilled">Number Quotation</label>
                <span class="form-floating-focused"></span>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <div class="form-invoice-repeater source-item">
                        <div class="row mb-2">
                            <div class="col-12 col-lg-3 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="select2Basic" class="select2 form-select form-select-lg invoice-item-client"
                                        data-allow-clear="true" name="id_pic" {{ @$quotation ? 'disabled' : '' }}>
                                        <option> ---- Choose Company Here ---- </option>
                                        @foreach ($pic as $charge)
                                            <option value="{{ $charge->id }}"
                                                {{ @$quotation->pic->id_client == $charge->id ? 'selected' : '' }}>
                                                {{ $charge->company }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="select2Basic">Client</label>
                                </div>
                            </div>
                            @if (@$quotation)
                                <input type="text" name="id_pic" id="idPic" value="{{ $quotation->id_pic }}" hidden>
                            @endif
                            <div class="col-12 col-lg-3">
                                <div class="form-floating form-floating-outline mb-2">
                                    <select id="pic-dropdown" class="select2 form-select invoice-item-pic"
                                        data-allow-clear="true" name="pic" disabled>
                                        @if (@$quotation)
                                            <option selected>
                                                {{ $quotation->pic->name_pic }}
                                            </option>
                                        @endif
                                    </select>
                                    <label for="pic-dropdown">Pic</label>
                                </div>
                            </div>
                            @if (@$quotation)
                                <input type="text" name="pic" id="destination" value="{{ $quotation->id_pic }}"
                                    hidden>
                            @endif
                            <div class="col-12 col-lg-2">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="text" placeholder="Put Title Quotation Here ...."
                                        id="title" name="title" value="{{ old('title', @$quotation->title ?? '') }}">
                                    <label for="title">Title Quotation</label>
                                </div>
                            </div>
                            @if (@$quotation)
                                <input type="text" name="destination" id="destination"
                                    value="{{ $quotation->destination }}" hidden>
                            @endif
                            <div class="col-6 col-lg-2">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="date" id="estimatedDate" name="estimated_date"
                                        {{-- {{ @$quotation->estimated_date ? '' : '_label' }}  naikin nanti --}}
                                        value="{{ old('estimated_date', @$quotation->estimated_date ?? now()->format('Y-m-d')) }}"
                                        {{-- {{ @$quotation->estimated_date ? '' : 'disabled' }} --}}>
                                    @if (empty($quotation->estimated_date))
                                        <input type="date" name="estimated_date" id=""
                                            value="{{ now()->format('Y-m-d') }}" hidden>
                                    @endif
                                    <label for="estimatedDate">Quote Date</label>
                                </div>
                            </div>
                            @php
                                $nextMonth = now()->addDays(30);
                            @endphp
                            <div class="col-6 col-lg-2">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="date" id="expiredDate" name="expired_date"
                                        value="{{ old('expired_date', @$quotation->expired_date ?? $nextMonth->format('Y-m-d')) }}">
                                    <label for="expiredDate">Expired Date</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-lg-3">
                                <div class="form-floating form-floating-outline mb-2">
                                    <select id="address-dropdown" class="select2 form-select invoice-item-destination"
                                        data-allow-clear="true" name="destination" disabled>
                                        @if (@$quotation)
                                            <option selected>
                                                {{ $quotation->destination == '1' ? $quotation->pic->client->address : $quotation->pic->client->subAddress }}
                                            </option>
                                        @endif
                                    </select>
                                    <label for="address-dropdown">Destination Address</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="form-floating form-floating-outline mb-4">
                                    <input class="form-control" type="text" placeholder="Put your No PR Here ...."
                                        id="no-pr-input" name="no_pr" value="{{ @$quotation->no_pr ?? '-' }}">
                                    <label for="no-pr-input">No PR</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-4">
                                <div class="form-floating form-floating-outline mb-4">
                                    <select class="form-select" id="Type" aria-label="Default select example"
                                        name="type">
                                        <option disabled>---Type---</option>
                                        <option value="Sparepart" {{ @$quote->type == 'Sparepart' ? 'selected' : '' }}>
                                            Sparepart
                                        </option>
                                        <option value="Unit" {{ @$quote->type == 'Unit' ? 'selected' : '' }}>Unit
                                        </option>
                                        <option value="Rental" {{ @$quote->type == 'Rental' ? 'selected' : '' }}>Rental
                                        </option>
                                        <option value="Service" {{ @$quote->type == 'Service' ? 'selected' : '' }}>Service
                                        </option>
                                    </select>
                                    <label for="exampleFormControlSelect1">Type</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" id="selectWeek" aria-label="Default select example"
                                        name="week">
                                        <option disabled>----- Choose Week -----</option>
                                        <option value="1" {{ @$quote->week == '1' ? 'selected' : '' }}>Week 1
                                        </option>
                                        <option value="2" {{ @$quote->week == '2' ? 'selected' : '' }}>Week 2
                                        </option>
                                        <option value="3" {{ @$quote->week == '3' ? 'selected' : '' }}>Week 3
                                        </option>
                                        <option value="4" {{ @$quote->week == '4' ? 'selected' : '' }}>Week 4
                                        </option>
                                        <option value="5" {{ @$quote->week == '5' ? 'selected' : '' }}>Week 5
                                        </option>
                                    </select>
                                    <label for="selectWeek">Week</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="text" id="assigned" name="id_sales"
                                        value="{{ Auth::user()->name }}" disabled>
                                    <input class="form-control" type="text" id="assigned" name="id_sales"
                                        value="{{ Auth::user()->id }}" hidden>
                                    <label for="assigned">Assigned</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3" data-repeater-list="group-a">
                            @foreach ($dquotation as $quote)
                                @php
                                    $id++;
                                    $dataDetail++;
                                @endphp
                                <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                                    <div class="d-flex border rounded position-relative pe-0">
                                        <div class="row w-100 p-3">
                                            <div class="col-md-6 col-12 mb-md-0 mb-3">
                                                <label for="product" class="mb-2">Product</label>
                                                <div class="form-floating form-floating-outline mb-2">
                                                    <select id="product-{{ $id }}"
                                                        class="select2 form-select invoice-item-product"
                                                        data-allow-clear="true" name="product[]"
                                                        data-id="{{ $id }}">
                                                        <option value="">---- Choose Part Number Here ----
                                                        </option>
                                                        @foreach ($product as $products)
                                                            <option value="{{ $products->id }}"
                                                                data-replacement="{{ $products->id }}"
                                                                {{ $quote->id_equivalent == "{$products->id}" ? 'selected' : '' }}>
                                                                {{ $products->brand }} {{ $products->pn }}
                                                                ({{ $products->detail_desc }})
                                                                ||
                                                                {{ $products->go }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label for="product-{{ $id }}">Product Part
                                                        Number</label>
                                                </div>
                                                <textarea class="form-control invoice-item-detail-product" rows="2" id="detailProduct-{{ $id }}"
                                                    placeholder="Detail Product. Example: Kaeser ASD" name="detail_product[]">{{ old('detail_product[]', $quote->detail_product) }}</textarea>
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title">Price</p>
                                                <div class="input-group" data-price="1">
                                                    <span class="input-group-text">Rp. </span>
                                                    <input type="text" class="form-control invoice-item-price-label"
                                                        id="priceLabel-{{ $id }}"
                                                        data-id="{{ $id }}" min="0"
                                                        placeholder="Put Price Here" data-type="currency"
                                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                                        value="{{ old('price[]', @$quote->price ? number_format(@$quote->price, 0, '', '.') : '') }}">
                                                    <input class="form-control invoice-item-price" type="number"
                                                        name="price[]" id="price-{{ $id }}"
                                                        value="{{ old('price[]', @$quote->price ?? '') }}" hidden>
                                                </div>
                                                <div class="d-flex justify-content-between mb-3">
                                                    <p>Stock : <span class="info-stock-label" id="info-stock-1"></span>
                                                    </p>
                                                    <p>Weight : <span class="info-weight-label" id="info-weight-1"></span>
                                                        g
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title">Qty</p>
                                                <input type="number" class="form-control invoice-item-qty mb-3"
                                                    placeholder="Min 1" name="qty[]" id="qty-{{ $dataDetail }}"
                                                    data-id="{{ $dataDetail }}" min="1"
                                                    value="{{ old('qty[]', $quote->qty) }}">
                                                <div class="form-floating form-floating-outline mb-4">
                                                    <select class="form-select invoice-item-info" id="info-qty-1"
                                                        data-id="1" aria-label="Default select example"
                                                        name="info_qty[]">
                                                        <option>---Info---</option>
                                                        <option value="Pcs"
                                                            {{ $quote->info_qty == 'Pcs' ? 'selected' : '' }}>Pcs
                                                        </option>
                                                        <option value="Set"
                                                            {{ $quote->info_qty == 'Set' ? 'selected' : '' }}>Set
                                                        </option>
                                                        <option value="Pail"
                                                            {{ $quote->info_qty == 'Pail' ? 'selected' : '' }}>Pail
                                                        </option>
                                                        <option value="Unit"
                                                            {{ $quote->info_qty == 'Unit' ? 'selected' : '' }}>Unit
                                                        </option>
                                                        <option value="Lot"
                                                            {{ $quote->info_qty == 'Lot' ? 'selected' : '' }}>Lot
                                                        </option>
                                                        <option value="Meter"
                                                            {{ $quote->info_qty == 'Meter' ? 'selected' : '' }}>Meter
                                                        </option>
                                                        <option value="Can"
                                                            {{ $quote->info_qty == 'Can' ? 'selected' : '' }}>Can
                                                        </option>
                                                        <option value="Hari"
                                                            {{ $quote->info_qty == 'Hari' ? 'selected' : '' }}>Hari
                                                        </option>
                                                        <option value="Bulan"
                                                            {{ $quote->info_qty == 'Bulan' ? 'selected' : '' }}>Bulan
                                                        </option>
                                                        <option value="Kg"
                                                            {{ $quote->info_qty == 'Kg' ? 'selected' : '' }}>Kg
                                                        </option>
                                                        <option value="Tube"
                                                            {{ $quote->info_qty == 'Tube' ? 'selected' : '' }}>Tube
                                                        </option>
                                                        <option value="Titik"
                                                            {{ $quote->info_qty == 'Titik' ? 'selected' : '' }}>Titik
                                                        </option>
                                                        <option value="Batang"
                                                            {{ $quote->info_qty == 'Batang' ? 'selected' : '' }}>Batang
                                                        </option>
                                                    </select>
                                                    <label for="exampleFormControlSelect1">Info</label>
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title">Discount</p>
                                                <input type="number" class="form-control invoice-item-disc"
                                                    placeholder="%" name="disc[]" id="disc-{{ $dataDetail }}"
                                                    data-id="{{ $dataDetail }}" min="0"
                                                    value="{{ old('disc[]', $quote->disc ?? '0') }}">
                                            </div>
                                            <div class="col-md-1 col-12 pe-0">
                                                <p class="mb-2 repeater-title">Amount</p>
                                                <p class="mb-0 amount-label" id="amount-label-{{ $id }}"
                                                    data-id="{{ $id }}">
                                                    {{ old('amount[]', 'RP ' . number_format($quote->amount, 0, '', '.')) }}
                                                </p>
                                                <input type="number" class="form-control invoice-item-amount"
                                                    name="amount[]" id="amount-{{ $id }}" data-id="1"
                                                    value="{{ old('amount[]', $quote->amount) }}" hidden>
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                            <i class="mdi mdi-close cursor-pointer bg-danger text-white btn-del"
                                                data-id="{{ $id }}" data-repeater-delete=""></i>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row">
                            <div class="col-12 mb-2">
                                <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btn-add"
                                    data-repeater-create="">
                                    <i class="mdi mdi-plus me-1"></i> Add Item
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <h5 class="my-4">
                                    Terms & Conditions :
                                </h5>
                                <div class="row mb-3">
                                    <label class="col-sm-4 col-form-label" for="validity">Validity Of Quotation</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="validity" class="form-control form-control-lg"
                                            name="validity"
                                            value="{{ old('validity', @$quotation->termncon[0]->validity ?? '1(one) Month After this Quotation Created') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-4 col-form-label" for="pricing">Price</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="pricing" class="form-control form-control-lg"
                                            name="pricing"
                                            value="{{ old('pricing', @$quotation->termncon[0]->pricing ?? 'Franco FACTORY ( BEKASI )') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-4 col-form-label" for="delivery">Delivery Process</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="delivery" class="form-control form-control-lg"
                                            value="{{ old('delivery_process', @$quotation->termncon[0]->delivery_process ?? 'Ready stock') }}"
                                            name="delivery_process">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-4 col-form-label" for="payment">Payment</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="payment" class="form-control form-control-lg"
                                            value="{{ old('payment', @$quotation->termncon[0]->payment ?? 'Cash Before Delivery') }}"
                                            name="payment">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-4 col-form-label" for="note">Note</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control h-px-100" rows="2" placeholder="Write your note here...." name="note">{{ old('note', @$quotation->termncon[0]->note ?? '-') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2"></div>
                            <div class="col-lg-4">
                                <div class="card shadow-none bg-light text-secondary border border-secondary mt-5 mb-3">
                                    <div class="card-body ">
                                        <div class="row">
                                            <label class="col-sm-4 col-form-label text-sm-start"
                                                for="collapsible-pincode">Sub
                                                Total :</label>
                                            <div class="col-sm-8">
                                                {{-- @if (@$dquotation)
                                                    <input type="number" id="subtotal" class="form-control"
                                                        name="subtotal"
                                                        value="{{ old('subtotal', @$quotation->subtotal ?? '') }}">
                                                @else --}}
                                                    <p class="mb-0 subtotal-label" id="subtotal-label" data-id="1">
                                                        {{ old('subtotal', @$quotation->subtotal ? 'RP ' . number_format(@$quotation->subtotal, 0, '', '.') : '') }}
                                                    </p>
                                                    <input type="number" id="subtotal" class="form-control"
                                                        name="subtotal"
                                                        value="{{ old('subtotal', @$quotation->subtotal ?? '') }}" hidden>
                                                {{-- @endif --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow-none bg-light text-secondary border border-secondary mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <label class="col-sm-4 col-form-label text-sm-start" for="collapsible-tax">Tax
                                                :</label>
                                            <div class="col-sm-8">
                                                <select id="tax" class="form-select form-select-lg"
                                                    style="background: none; border: none;" name="tax">
                                                    <option disabled>-----Select Tax-----</option>
                                                    <option value="0"
                                                        {{ @$quotation->tax == '0' ? 'selected' : '' }}>
                                                        Without Tax</option>
                                                    <option value="11"
                                                        {{ @$quotation->tax == '11' ? 'selected' : '' }}>
                                                        <span> With PPN <small class="text-muted"> 12%</small> </span>
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow-none bg-light text-secondary border border-secondary mb-3">
                                    <div class="card-body ">
                                        <div class="row">
                                            <label class="col-sm-4 col-form-label text-sm-start"
                                                for="collapsible-pincode">Weight Total :</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <input type="text" class="form-control info-weight-total-label"
                                                        id="info-weight-total" style="background: none; border: none;"
                                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$" disabled
                                                        value="{{ old('weight', @$quotation->weight ? $quotation->weight + ' g' : '0 g') }}">
                                                    <input type="number" name="weight" id="info-weight-total"
                                                        value="{{ old('weight', @$quotation->weight ?? '0') }}" hidden>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow-none bg-light text-secondary border border-secondary mb-3">
                                    <div class="card-body ">
                                        <div class="row">
                                            <label class="col-sm-4 col-form-label text-sm-start"
                                                for="collapsible-pincode">Shipping :</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <span class="input-group-text"
                                                        style="background: none; border: none;">Rp.
                                                    </span>
                                                    <input type="text" id="shipping-label" class="form-control"
                                                        placeholder="Shipping Cost Here....." data-type="currency"
                                                        style="background: none; border: none;"
                                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                                        value="{{ old('shipping', @$quotation->shipping ? number_format(@$quotation->shipping, 0, '', '.') : '0') }}">
                                                    <input type="number" name="shipping" id="shipping"
                                                        value="{{ old('shipping', @$quotation->shipping ?? '0') }}"
                                                        hidden>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow-none bg-light text-secondary border border-secondary mb-3">
                                    <div class="card-body ">
                                        <div class="row">
                                            <label class="col-sm-4 col-form-label text-sm-start"
                                                for="collapsible-pincode">Discount :</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <span class="input-group-text"
                                                        style="background: none; border: none;">Rp.
                                                    </span>
                                                    <input type="text" id="diskon-label" class="form-control"
                                                        placeholder="Discount Here....." data-type="currency"
                                                        style="background: none; border: none;"
                                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                                        value="{{ old('diskon', @$quotation->diskon ? number_format(@$quotation->diskon, 0, '', '.') : '0') }}">
                                                    <input type="number" name="diskon" id="diskon"
                                                        value="{{ old('diskon', @$quotation->diskon ?? '0') }}" hidden>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow-none bg-light text-secondary border border-secondary mb-3">
                                    <input type="number" id="totalNoTax" name="total_no_tax"
                                        value="{{ old('total_no_tax', @$quotation->total_no_tax ?? '') }}" hidden>
                                    <div class="card-body ">
                                        <div class="row">
                                            <label class="col-sm-4 col-form-label text-sm-start"
                                                for="collapsible-pincode">Total
                                                :</label>
                                            <div class="col-sm-8">
                                                {{-- @if (@$dquotation)
                                                    <input type="number" id="hargaTotal" class="form-control"
                                                        name="harga_total"
                                                        value="{{ old('harga_total', @$quotation->harga_total ?? '') }}">
                                                @else --}}
                                                    <p class="mb-0 harga-total-label" id="hargaTotalLabel"
                                                        data-id="1">
                                                        {{ old('harga_total', @$quotation->harga_total ? 'RP ' . number_format(@$quotation->harga_total, 0, '', '.') : '') }}
                                                    </p>
                                                    <input type="number" id="hargaTotal" class="form-control"
                                                        name="harga_total"
                                                        value="{{ old('harga_total', @$quotation->harga_total ?? '') }}"
                                                        hidden>
                                                {{-- @endif --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="float-end">
                                    <a href="{{ route('quotation.index') }}" type="button"
                                        class="btn btn-lg btn-outline-secondary">
                                        Back
                                    </a>
                                    <button :disabled="focused" type="submit" class="btn btn-lg btn-primary">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endsection
    @push('after-style')
        <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    @endpush
    @push('after-script')
        <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
        <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
        <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
        <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
        <script src="{{ asset('assets') }}/includes/repeater/jquery-repeater-invoice.js"></script>
        <script src="{{ asset('assets') }}/js/app-invoice-add.js"></script>
    @endpush
    @push('page-script')
        <script src="{{ asset('assets') }}/includes/repeater/repeater-invoice.js"></script>
        <script src="{{ asset('assets') }}/includes/validator/quotation-validation.js"></script>
        <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
    @endpush
    @push('script')
        <script>
            $(() => {
                // Format Integer menjadi Currency ID Rupiah
                let formatter = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                });

                function initializeSelect2Product() {
                    $('.invoice-item-product').select2({
                        placeholder: ' ---- Choose Part Number Here ---- ',
                        allowClear: true,
                        width: '100%',
                    });
                }
                $(document).ready(function() {
                    // Panggil fungsi inisialisasi saat halaman dimuat
                    initializeSelect2Product();

                    // Jika ada elemen dinamis yang ditambahkan, gunakan event listener
                    $(document).on('repeater:added', function() {
                        initializeSelect2Product();
                    });
                });

                function initFormValidation() {
                    const fv = FormValidation.formValidation(formAuthentication, {
                        fields: {
                            title: {
                                validators: {
                                    notEmpty: {
                                        message: "Please enter title",
                                    },
                                    stringLength: {
                                        min: 6,
                                        message: "Name must be more than 6 characters",
                                    },
                                },
                            },
                            "detail_product[]": {
                                selector: '[name="detail_product[]"]',
                                validators: {
                                    notEmpty: {
                                        message: "Please enter detail product",
                                    },
                                    stringLength: {
                                        min: 3,
                                        message: "Area must be more than 3 characters (detail product)",
                                    },
                                },
                            },
                            harga: {
                                validators: {
                                    notEmpty: {
                                        message: "Please enter price",
                                    },
                                    numericInput: {
                                        number: "Please enter a valid number.",
                                    },
                                },
                            },
                            "qty[]": {
                                validators: {
                                    notEmpty: {
                                        message: "Please enter Quantity",
                                    },
                                    numericInput: {
                                        number: "Please enter a valid number.",
                                    },
                                },
                            },
                        },
                        plugins: {
                            trigger: new FormValidation.plugins.Trigger(),
                            bootstrap5: new FormValidation.plugins.Bootstrap5({
                                eleValidClass: "",
                                rowSelector: ".mb-3",
                            }),
                            submitButton: new FormValidation.plugins.SubmitButton(),

                            defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                            autoFocus: new FormValidation.plugins.AutoFocus(),
                        },
                        init: (instance) => {
                            instance.on("plugins.message.placed", function(e) {
                                if (
                                    e.element.parentElement.classList.contains(
                                        "input-group"
                                    )
                                ) {
                                    e.element.parentElement.insertAdjacentElement(
                                        "afterend",
                                        e.messageElement
                                    );
                                }
                            });
                        },
                    });
                }

                // Jquery Dependency
                // formatting  shipping
                $("#shipping-label").on({
                    keyup: function() {
                        formatCurrencyShipping($(this));
                    }
                });
                // Formatting Discount Quotation
                $("#diskon-label").on({
                    keyup: function() {
                        formatCurrencyDiscount($(this));
                    }
                });

                function initializeSelect2Address() {
                    $('.invoice-item-destination').select2({
                        placeholder: ' ---- Choose Destination Here ---- ',
                        allowClear: true,
                        width: '100%',
                    });
                }

                function initializeSelect2PIC() {
                    $('.invoice-item-pic').select2({
                        placeholder: ' ---- Choose PIC Here ---- ',
                        allowClear: true,
                        width: '100%',
                    });
                }

                function formatNumber(n) {
                    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                }

                function formatCurrencyShipping(input) {
                    var input_val = input.val();

                    // don't validate empty input
                    if (input_val === "") {
                        return;
                    }

                    // original length
                    var original_len = input_val.length;

                    // add commas to number
                    // remove all non-digits
                    input_val = formatNumber(input_val);
                    input_val = input_val;

                    // send updated string to input
                    input.val(input_val);
                    var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                    $('#shipping').val(nomorInt);
                }

                function formatCurrencyDiscount(input) {
                    var input_val = input.val();

                    // don't validate empty input
                    if (input_val === "") {
                        return;
                    }

                    // original length
                    var original_len = input_val.length;

                    // add commas to number
                    // remove all non-digits
                    input_val = formatNumber(input_val);
                    input_val = input_val;

                    // send updated string to input
                    input.val(input_val);
                    var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                    $('#diskon').val(nomorInt);
                }

                function formatPrice(num) {
                    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }

                $(`.invoice-item-client`).on('change', function(ev) {
                    var clientId = $(this).val();
                    console.log(clientId);

                    $.ajax({
                        url: '/quotation/client/' + clientId,
                        type: 'GET',
                        success: function(response) {
                            console.log(response);

                            // Mengosongkan dropdown detail produk
                            $(`.invoice-item-destination`).empty();
                            // Mengisi dropdown detail produk dengan hasil yang diterima
                            // $.each(response, function(key, value) {
                            $(`.invoice-item-destination`).append(
                                '<option value="' +
                                1 + '">' + response.address +
                                '</option>' +
                                '<option value="' +
                                2 + '">' + response.subAddress +
                                '</option>'
                            );
                            // Mengaktifkan dropdown detail produk
                            $(`.invoice-item-destination`).prop('disabled', false);
                        }
                    });
                    $.ajax({
                        url: '/quotation/pic/' + clientId,
                        type: 'GET',
                        success: function(response) {
                            console.log(response);

                            // Mengosongkan dropdown detail produk
                            $(`.invoice-item-pic`).empty();
                            // Mengisi dropdown detail produk dengan hasil yang diterima
                            $.each(response, function(key, value) {
                                $(`.invoice-item-pic`).append(
                                    '<option value="' +
                                    value.id + '">' + value.name_pic +
                                    '</option>'
                                );
                            });
                            // Mengaktifkan dropdown detail produk
                            $(`.invoice-item-pic`).prop('disabled', false);
                        }
                    });
                });

                $('.invoice-item-product').on('change', function(ev) {
                    var replacementId = $(this).find(':selected').data('replacement');
                    var Url = '/quotation/sparepart/' + replacementId;
                    var commodity = $(this).find(':selected').data('commodity');
                    var id = $(this).data('id');
                    // console.log('Replacement ID:', replacementId);
                    // console.log('URL:', Url);
                    // console.log('Textarea ID:', id);

                    $.ajax({
                        url: '/product-in/replacement/' + commodity,
                        type: 'GET',
                        success: function(response) {
                            // console.log('AJAX Response:', response);
                            $(`#info-stock-${id}`).text(response[0].stock);
                            $(`#info-weight-${id}`).text(response[0].weight);

                            var weightTotal = 0;

                            $('.info-weight-label').each(() => {
                                weightTotal += parseInt($(`#info-weight-${id}`).text());
                            });
                            console.log('Weight Total : ', weightTotal);
                            $(`.info-weight-total-label`).val(weightTotal + ' g');
                        }
                    });

                    $.ajax({
                        url: Url,
                        type: 'GET',
                        success: function(response) {
                            console.log('Replacement Id : ', replacementId);
                            console.log('URL: ', Url);

                            console.log('AJAX Response:', response);
                            $(`#detailProduct-${id}`).val(response[0].detail);
                            $(`#priceLabel-${id}`).val(formatPrice(response[0].price));
                            $(`#price-${id}`).val(response[0].price);
                            var sTotal = 0,
                                row = 0,
                                amount = 0,
                                hasil = 0,
                                harga = response[0].price,
                                disc = isNaN(parseInt($(`#disc-${id}`).val())) ? 0 : parseInt($(
                                    `#disc-${id}`).val());
                            $(`#qty-${id}`).val(1);
                            // menghitung hasil
                            hasil = harga * $(`#qty-${id}`).val();
                            // menghitung amount
                            amount = (hasil - (hasil * disc / 100));
                            // memasukan data amount dan subtotal
                            $(`#amount-${id}`).val(amount);
                            $(`#amount-label-${id}`).html(`${formatter.format(amount)}`);
                            $('.amount-label').each(() => {
                                row++;
                                sTotal += parseInt($(`#amount-${row}`).val())
                            });
                            $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                            $('#subtotal').val(sTotal);

                            var noTax = 0;
                            var hTotal = 0;
                            var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax')
                                .val());
                            hTotal = parseInt(sTotal - disc + (sTotal * tax / 100));
                            noTax = parseInt(sTotal - disc)
                            $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                            $('#hargaTotal').val(hTotal);
                            $('#totalNoTax').val(noTax);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                });


                $(".invoice-item-price-label").on('keyup', function() {
                    var input = $(this)
                    var id = input.data('id');
                    var input_val = input.val();

                    // original length
                    var original_len = input_val.length;

                    // add commas to number
                    // remove all non-digits
                    input_val = formatNumber(input_val);
                    input_val = input_val;

                    // send updated string to input
                    input.val(input_val);
                    var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                    console.log(id);
                    $(`#price-${id}`).val(nomorInt);
                });

                // Logic amount + Subtotal
                $('.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc').on('keyup change click', function(
                    ev) {
                    // mengambil ID
                    var id = $(this).data('id');
                    // prepare data
                    var sTotal = 0,
                        row = 0,
                        amount = 0,
                        hasil = 0,
                        valHarga = isNaN(parseInt($(`#price-${id}`).val())) ? 0 : parseInt($(`#price-${id}`)
                            .val()),
                        harga = Number(valHarga),
                        disc = isNaN(parseInt($(`#disc-${id}`).val())) ? 0 : parseInt($(`#disc-${id}`).val()),
                        qty = isNaN(parseInt($(`#qty-${id}`).val())) ? 0 : parseInt($(`#qty-${id}`).val());
                    // menghitung hasil
                    console.log(harga);
                    hasil = harga * qty;
                    // menghitung amount
                    amount = (hasil - (hasil * disc / 100));
                    // memasukan data amount dan subtotal
                    $(`#amount-${id}`).val(amount);
                    $(`#amount-label-${id}`).html(`${formatter.format(amount)}`);
                    $('.amount-label').each(() => {
                        row++;
                        sTotal += parseInt($(`#amount-${row}`).val())
                    });
                    $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                    $('#subtotal').val(sTotal);
                });

                // Logic Harga Total
                $('#subtotal, #tax, #shipping-label, #diskon-label, .invoice-item-price-label, .invoice-item-qty, .invoice-item-disc')
                    .on('keyup change',
                        () => {
                            var noTax = 0;
                            var hTotal = 0;
                            var sTotal = isNaN(parseInt($('#subtotal').val())) ? 0 : parseInt($('#subtotal').val());
                            var shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($('#shipping').val());
                            var discount = isNaN(parseInt($('#diskon').val())) ? 0 : parseInt($('#diskon').val());
                            var dTotal = sTotal - discount;
                            var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val());
                            hTotal = parseInt(dTotal + (dTotal * tax / 100) + shipping);
                            noTax = parseInt(dTotal + shipping);
                            console.log(hTotal);
                            $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                            $('#hargaTotal').val(hTotal);
                            $('#totalNoTax').val(noTax);
                        });
                $('.btn-del').on('click', function() {
                    var id = $(this).data('id');
                    // prepare data
                    var sTotal = 0,
                        row = 0,
                        amount = 0,
                        hasil = 0,
                        valHarga = isNaN(parseInt($(`#price-${id}`).val())) ? 0 : parseInt($(`#price-${id}`)
                            .val()),
                        harga = Number(valHarga),
                        disc = isNaN(parseInt($(`#disc-${id}`).val())) ? 0 : parseInt($(`#disc-${id}`).val()),
                        qty = isNaN(parseInt($(`#qty-${id}`).val())) ? 0 : parseInt($(`#qty-${id}`).val());
                    // menghitung hasil
                    console.log(harga);
                    hasil = harga * qty;
                    // menghitung amount
                    amount = (hasil - (hasil * disc / 100));
                    // memasukan data amount dan subtotal
                    $(`#amount-${id}`).val(amount);
                    $(`#amount-label-${id}`).html(`${formatter.format(amount)}`);
                    $('.amount-label:visible').each(() => {
                        row++;
                        if (row === id) {
                            valamount = 0;
                        } else {
                            valamount = parseInt($(`#amount-${row}`).val());
                        }
                        sTotal += valamount;
                    });
                    console.log("ini id:" + id);

                    $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                    $('#subtotal').val(sTotal);

                    var noTax = 0;
                    var hTotal = 0;
                    var sTotal = isNaN(parseInt($('#subtotal').val())) ? 0 : parseInt($('#subtotal').val());
                    var shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($('#shipping').val());
                    var discount = isNaN(parseInt($('#diskon').val())) ? 0 : parseInt($('#diskon').val());
                    var dTotal = sTotal - discount;
                    var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val());
                    hTotal = parseInt(dTotal + (dTotal * tax / 100) + shipping);
                    noTax = parseInt(dTotal + shipping);
                    console.log(hTotal);
                    $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                    $('#hargaTotal').val(hTotal);
                    $('#totalNoTax').val(noTax);
                });

                // Logic Subtotal dan Amount Setelah Tambah Product
                $('.btn-add').on('click', () => {

                    $('.invoice-item-product').on('change', function(ev) {
                        var replacementId = $(this).find(':selected').data('replacement');
                        var Url = '/quotation/sparepart/' + replacementId;
                        var commodity = $(this).find(':selected').data('commodity');
                        var id = $(this).data('id');
                        // console.log('Replacement ID:', replacementId);
                        // console.log('URL:', Url);
                        console.log('Textarea ID:', id);

                        $.ajax({
                            url: '/product-in/replacement/' + commodity,
                            type: 'GET',
                            success: function(response) {
                                console.log('AJAX Response:', response);
                                $(`#info-stock-${id}`).text(response[0].stock);
                                $(`#info-weight-${id}`).text(response[0].weight);

                                var row = 0,
                                    weightTotal = 0;

                                $('.info-weight-label').each(() => {
                                    row++;
                                    weightTotal += parseInt($(`#info-weight-${row}`)
                                        .text());
                                });
                                console.log('Weight Total : ', weightTotal);
                                $(`.info-weight-total-label`).val(weightTotal + ' g');
                            }
                        });
                        $.ajax({
                            url: Url,
                            type: 'GET',

                            success: function(response) {
                                console.log('AJAX Response:', response);
                                $(`#detailProduct-${id}`).val(response[0].detail);
                                $(`#priceLabel-${id}`).val(formatPrice(response[0].price));
                                $(`#price-${id}`).val(response[0].price);
                                var sTotal = 0,
                                    row = 0,
                                    amount = 0,
                                    hasil = 0,
                                    harga = response[0].price,
                                    disc = isNaN(parseInt($(`#disc-${id}`).val())) ? 0 :
                                    parseInt($(
                                        `#disc-${id}`).val());
                                $(`#qty-${id}`).val(1);
                                // menghitung hasil
                                hasil = harga * $(`#qty-${id}`).val();
                                // menghitung amount
                                amount = (hasil - (hasil * disc / 100));
                                // memasukan data amount dan subtotal
                                $(`#amount-${id}`).val(amount);
                                $(`#amount-label-${id}`).html(
                                    `${formatter.format(amount)}`);
                                $('.amount-label').each(() => {
                                    row++;
                                    sTotal += parseInt($(`#amount-${row}`).val())
                                });
                                $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                                $('#subtotal').val(sTotal);

                                var noTax = 0;
                                var hTotal = 0;
                                var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($(
                                        '#tax')
                                    .val());
                                hTotal = parseInt(sTotal - disc + (sTotal * tax / 100));
                                noTax = parseInt(sTotal - disc)
                                $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                                $('#hargaTotal').val(hTotal);
                                $('#totalNoTax').val(noTax);
                            },
                            error: function(xhr, status, error) {
                                console.error('Error:', error);
                            }
                        });
                    });
                    $(".invoice-item-price-label").on('keyup', function() {
                        var input = $(this)
                        var id = input.data('id');
                        var input_val = input.val();

                        // don't validate empty input
                        if (input_val === "") {
                            return;
                        }

                        // original length
                        var original_len = input_val.length;

                        // initial caret position 
                        var caret_pos = input.prop("selectionStart");

                        // add commas to number
                        // remove all non-digits
                        input_val = formatNumber(input_val);
                        input_val = input_val;

                        // send updated string to input
                        input.val(input_val);
                        var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                        console.log(id);
                        $(`#price-${id}`).val(nomorInt);

                        // put caret back in the right position
                        var updated_len = input_val.length;
                        caret_pos = updated_len - original_len + caret_pos;
                        input[0].setSelectionRange(caret_pos, caret_pos);
                    });
                    $('.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc').on(
                        'keyup change click',
                        function(ev) {
                            var id = $(this).data('id');
                            var sTotal = 0,
                                row = 0;
                            var amount = 0,
                                hasil = 0,
                                disc = isNaN(parseInt($(`#disc-${id}`).val())) ? 0 : parseInt($(
                                    `#disc-${id}`).val()),
                                qty = isNaN(parseInt($(`#qty-${id}`).val())) ? 0 : parseInt($(`#qty-${id}`)
                                    .val());
                            hasil = $(`#price-${id}`).val() * qty;
                            amount = (hasil - (hasil * disc / 100));
                            $(`#amount-${id}`).val(amount);
                            $(`#amount-label-${id}`).html(`${formatter.format(amount)}`);
                            $('.amount-label').each(() => {
                                row++;
                                sTotal += parseInt($(`#amount-${row}`).val())
                            })
                            console.log(sTotal + "<total row>" + row);
                            console.log("Anda Sedang berada di Id : " + id);
                            $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                            $('#subtotal').val(sTotal);
                        });

                    // Logic Harga Total
                    $('#subtotal, #tax, #shipping-label, #diskon-label, .invoice-item-price-label, .invoice-item-qty, .invoice-item-disc')
                        .on('keyup change',
                            () => {
                                var noTax = 0;
                                var hTotal = 0;
                                var sTotal = isNaN(parseInt($('#subtotal').val())) ? 0 : parseInt($('#subtotal')
                                    .val());
                                var shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($(
                                    '#shipping').val());
                                var discount = isNaN(parseInt($('#diskon').val())) ? 0 : parseInt($('#diskon')
                                    .val());
                                var dTotal = sTotal - discount;
                                var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val());
                                hTotal = parseInt(dTotal + (dTotal * tax / 100) + shipping);
                                noTax = parseInt(dTotal + shipping);
                                console.log(hTotal);
                                $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                                $('#hargaTotal').val(hTotal);
                                $('#totalNoTax').val(noTax);
                            });

                    $('.btn-del').on('click', function() {
                        var id = $(this).data('id');
                        // prepare data
                        var sTotal = 0,
                            row = 0,
                            amount = 0,
                            hasil = 0,
                            valHarga = isNaN(parseInt($(`#price-${id}`).val())) ? 0 : parseInt($(
                                    `#price-${id}`)
                                .val()),
                            harga = Number(valHarga),
                            disc = isNaN(parseInt($(`#disc-${id}`).val())) ? 0 : parseInt($(
                                `#disc-${id}`).val()),
                            qty = isNaN(parseInt($(`#qty-${id}`).val())) ? 0 : parseInt($(`#qty-${id}`)
                                .val());
                        // menghitung hasil
                        console.log(harga);
                        hasil = harga * qty;
                        // menghitung amount
                        amount = (hasil - (hasil * disc / 100));
                        // memasukan data amount dan subtotal
                        $(`#amount-${id}`).val(amount);
                        $(`#amount-label-${id}`).html(`${formatter.format(amount)}`);
                        $('.amount-label:visible').each(() => {
                            row++;
                            if (row === id) {
                                valamount = 0;
                            } else {
                                valamount = parseInt($(`#amount-${row}`).val());
                            }
                            sTotal += valamount;
                        });
                        console.log("ini id:" + id);

                        $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                        $('#subtotal').val(sTotal);

                        var noTax = 0;
                        var hTotal = 0;
                        var sTotal = isNaN(parseInt($('#subtotal').val())) ? 0 : parseInt($('#subtotal')
                            .val());
                        var shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($(
                            '#shipping').val());
                        var discount = isNaN(parseInt($('#diskon').val())) ? 0 : parseInt($('#diskon')
                            .val());
                        var dTotal = sTotal - discount;
                        var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val());
                        hTotal = parseInt(dTotal + (dTotal * tax / 100) + shipping);
                        noTax = parseInt(dTotal + shipping);
                        console.log(hTotal);
                        $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                        $('#hargaTotal').val(hTotal);
                        $('#totalNoTax').val(noTax);
                    });

                    initializeSelect2Product();
                });

            })
        </script>
    @endpush
