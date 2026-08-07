    @extends('layouts.sales.app')
    @section('title', 'Create Quotation')
    @section('content')
        @php
            $id = 0;
            $dataDetail = 0;
        @endphp
        <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework"
            action="{{ @$quotation ? route('quotation.update', $quotation->id) : route('quotation.store') }}" method="post"
            enctype="multipart/form-data">
            @csrf
            @if (@$quotation)
                @method('patch')
            @endif
            <div class="form-floating mb-3">
                @if (Auth::user()->code == 'YH')
                    <input type="text" class="form-control fw-bold fs-3" id="floatingInputFilled"
                        aria-describedby="floatingInputFilledHelp" name="no_quote"
                        value="{{ old('no_quote', @$quotation->no_quote ? $quotation->no_quote : $formattedNumberQ . '-P-BDG-RJO-' . Auth::user()->code . '-' . $formattedMonthNow . '-' . \Carbon\Carbon::now()->year) }}">
                @else
                    <input type="text" class="form-control fw-bold fs-3" id="floatingInputFilled"
                        aria-describedby="floatingInputFilledHelp" name="no_quote"
                        value="{{ old('no_quote', @$quotation->no_quote ? $quotation->no_quote : $formattedNumberQ . '-P/BDG/RJO-' . Auth::user()->code . '/' . $formattedMonthNow . '/' . \Carbon\Carbon::now()->year) }}">
                @endif
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
                            <div class="col-12 col-lg-4 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="select2Basic" class="select2 form-select form-select-lg invoice-item-client"
                                        data-allow-clear="true" name="id_pic" {{ @$quotation ? 'disabled' : '' }}>
                                        <option value=""> ---- Choose Company Here ---- </option>
                                        @foreach ($pic as $charge)
                                            <option value="{{ $charge->id }}"
                                                data-role="{{ $charge->role }}"
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
                            <div class="col-12 col-lg-2">
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
                                        <option value="Project" {{ @$quote->type == 'Project' ? 'selected' : '' }}>Project
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
                        @if (@$dquotation)
                            <div class="mb-3" data-repeater-list="group-a">
                                @foreach ($dquotation as $quote)
                                    @php
                                        $id++;
                                        $dataDetail++;
                                    @endphp
                                    <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                                        <div class="d-flex border border-secondary-subtle rounded-3 position-relative pe-0 mb-3 shadow-xs item-row-card" style="transition: all 0.25s ease; background-color: #fdfefe;">
                                            <div class="row w-100 p-3 align-items-start">
                                                <!-- Product Select -->
                                                <div class="col-md-5 col-12 mb-md-0 mb-3">
                                                    <label for="product" class="mb-2 fw-semibold text-secondary">Product</label>
                                                    <div class="form-floating form-floating-outline mb-2">
                                                        <select id="product-{{ $id }}"
                                                            class="form-select invoice-item-product"
                                                            data-allow-clear="true" name="product[]"
                                                            data-id="{{ $id }}">
                                                            <option value="">---- Choose Part Number Here ----</option>
                                                            @if (!empty($quote->id_equivalent) && !empty($quote->equivalent))
                                                                <option value="{{ $quote->id_equivalent }}"
                                                                    data-replacement="{{ $quote->id_equivalent }}"
                                                                    data-commodity="{{ $quote->equivalent->id_product }}"
                                                                    data-unit="{{ $quote->equivalent->product->unit ?? 'Pcs' }}"
                                                                    selected>
                                                                    {{ $quote->equivalent->brand }} {{ $quote->equivalent->pn }}
                                                                    ({{ $quote->equivalent->product->detail_desc ?? '' }})
                                                                    ||
                                                                    {{ $quote->equivalent->product->go ?? '' }}
                                                                </option>
                                                            @endif
                                                        </select>
                                                        <label for="product-{{ $id }}">Product Part Number</label>
                                                    </div>
                                                    <textarea class="form-control invoice-item-detail-product" rows="2" id="detailProduct-{{ $id }}"
                                                        placeholder="Detail Product. Example: Kaeser ASD" name="detail_product[]">{{ old('detail_product[]', $quote->detail_product) }}</textarea>
                                                </div>
                                                
                                                <!-- Price & Badges -->
                                                <div class="col-md-3 col-12 mb-md-0 mb-3">
                                                    <label class="mb-2 fw-semibold text-secondary">Price</label>
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
                                                    <div class="d-flex justify-content-between mt-2 flex-wrap gap-1">
                                                        <span class="badge bg-label-info d-flex align-items-center py-1.5 px-2.5 rounded-pill">
                                                            <i class="mdi mdi-cube-outline me-1 fs-6"></i>
                                                            Stock: <strong class="info-stock-label ms-1" id="info-stock-{{ $id }}">-</strong>
                                                        </span>
                                                        <span class="badge bg-label-warning d-flex align-items-center py-1.5 px-2.5 rounded-pill">
                                                            <i class="mdi mdi-scale me-1 fs-6"></i>
                                                            Weight: <strong class="info-weight-label mx-1" id="info-weight-{{ $id }}">-</strong> g
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Qty / Unit -->
                                                <div class="col-md-2 col-12 mb-md-0 mb-3">
                                                    <label class="mb-2 fw-semibold text-secondary">Qty / Unit</label>
                                                    <div class="input-group input-group-merge">
                                                        <input type="number" class="form-control invoice-item-qty"
                                                            placeholder="Qty" name="qty[]" id="qty-{{ $dataDetail }}"
                                                            data-id="{{ $dataDetail }}" min="1"
                                                            value="{{ old('qty[]', $quote->qty) }}">
                                                        <input type="text" class="input-group-text px-2 bg-light text-secondary invoice-item-info text-center"
                                                            id="info-qty-{{ $dataDetail }}" data-id="{{ $dataDetail }}" name="info_qty[]" readonly
                                                            value="{{ old('info_qty[]', $quote->info_qty ?? 'Pcs') }}" style="max-width: 65px; border-left: 0; font-size: 0.85rem; font-weight: 600;">
                                                    </div>
                                                </div>
                                                
                                                <!-- Discount -->
                                                <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                    <label class="mb-2 fw-semibold text-secondary">Disc</label>
                                                    <div class="input-group input-group-merge">
                                                        <input type="number" class="form-control invoice-item-disc text-center"
                                                            placeholder="0" name="disc[]" id="disc-{{ $dataDetail }}"
                                                            data-id="{{ $dataDetail }}" min="0"
                                                            value="{{ old('disc[]', $quote->disc ?? '0') }}" style="padding-left: 5px; padding-right: 5px;">
                                                        <span class="input-group-text px-1 bg-light text-secondary font-semibold" style="font-size: 0.85rem;">%</span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Amount -->
                                                <div class="col-md-1 col-12 text-md-end pe-0 mb-md-0 mb-3">
                                                    <label class="mb-2 fw-semibold text-secondary d-block">Amount</label>
                                                    <div class="d-flex align-items-center justify-content-md-end h-px-40 mt-1">
                                                        <span class="fw-bold text-primary amount-label" id="amount-label-{{ $id }}" data-id="{{ $id }}" style="font-size: 1rem;">
                                                            {{ old('amount[]', 'Rp ' . number_format($quote->amount, 0, '', '.')) }}
                                                        </span>
                                                    </div>
                                                    <input type="number" class="form-control invoice-item-amount"
                                                        name="amount[]" id="amount-{{ $id }}" data-id="1"
                                                        value="{{ old('amount[]', $quote->amount) }}" hidden>
                                                </div>
                                            </div>
                                            
                                            <!-- Trash Button -->
                                            <div class="d-flex align-items-center justify-content-center border-start px-2 bg-light" style="background-color: rgba(255, 76, 81, 0.03) !important;">
                                                <button type="button" class="btn btn-icon btn-outline-danger btn-sm border-0 rounded-circle waves-effect btn-del"
                                                    data-id="{{ $id }}" data-repeater-delete="" style="transition: all 0.2s ease;">
                                                    <i class="mdi mdi-trash-can-outline fs-4"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mb-2" data-repeater-list="group-a">
                                <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                                    <div class="d-flex border border-secondary-subtle rounded-3 position-relative pe-0 mb-3 shadow-xs item-row-card" style="transition: all 0.25s ease; background-color: #fdfefe;">
                                        <div class="row w-100 p-3 align-items-start">
                                            <!-- Product Select -->
                                            <div class="col-md-5 col-12 mb-md-0 mb-3">
                                                <label for="product" class="mb-2 fw-semibold text-secondary">Product</label>
                                                <div class="form-floating form-floating-outline mb-2">
                                                    <select id="product-{{ $id }}"
                                                        class="form-select invoice-item-product"
                                                        data-allow-clear="true" name="product[]" data-id="1">
                                                        <option value=""> ---- Choose Part Number Here ---- </option>
                                                    </select>
                                                    <label for="product-{{ $id }}">Product Part Number</label>
                                                </div>
                                                <textarea class="form-control invoice-item-detail-product" rows="2" id="detailProduct-1"
                                                    placeholder="Detail Product. Example: Kaeser ASD" name="detail_product[]"></textarea>
                                            </div>
                                            
                                            <!-- Price & Badges -->
                                            <div class="col-md-3 col-12 mb-md-0 mb-3">
                                                <label class="mb-2 fw-semibold text-secondary">Price</label>
                                                <div class="input-group" data-price="1">
                                                    <span class="input-group-text">Rp. </span>
                                                    <input type="text" class="form-control invoice-item-price-label"
                                                        id="priceLabel-1" data-id="1" name="harga"
                                                        placeholder="Put Price Here" data-type="currency" min="0"
                                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                                        @blur="focused = false" value="{{ old('price[]') }}">
                                                    <input class="form-control invoice-item-price" type="number"
                                                        name="price[]" id="price-1" value="{{ old('price[]') }}"
                                                        hidden>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 flex-wrap gap-1">
                                                    <span class="badge bg-label-info d-flex align-items-center py-1.5 px-2.5 rounded-pill">
                                                        <i class="mdi mdi-cube-outline me-1 fs-6"></i>
                                                        Stock: <strong class="info-stock-label ms-1" id="info-stock-1">-</strong>
                                                    </span>
                                                    <span class="badge bg-label-warning d-flex align-items-center py-1.5 px-2.5 rounded-pill">
                                                        <i class="mdi mdi-scale me-1 fs-6"></i>
                                                        Weight: <strong class="info-weight-label mx-1" id="info-weight-1">-</strong> g
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <!-- Qty & Unit -->
                                            <div class="col-md-2 col-12 mb-md-0 mb-3">
                                                <label class="mb-2 fw-semibold text-secondary">Qty / Unit</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="number" class="form-control invoice-item-qty"
                                                        placeholder="Qty" name="qty[]" id="qty-1" data-id="1"
                                                        min="1" value="{{ old('qty[]') }}">
                                                    <input type="text" class="input-group-text px-2 bg-light text-secondary invoice-item-info text-center"
                                                        id="info-qty-1" data-id="1" name="info_qty[]" readonly
                                                        value="{{ old('info_qty[]', 'Pcs') }}" style="max-width: 65px; border-left: 0; font-size: 0.85rem; font-weight: 600;">
                                                </div>
                                            </div>
                                            
                                            <!-- Discount -->
                                            <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                <label class="mb-2 fw-semibold text-secondary">Disc</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="number" class="form-control invoice-item-disc text-center"
                                                        placeholder="0" name="disc[]" id="disc-1" data-id="1"
                                                        min="0" value="{{ old('disc[]', '0') }}" style="padding-left: 5px; padding-right: 5px;">
                                                    <span class="input-group-text px-1 bg-light text-secondary font-semibold" style="font-size: 0.85rem;">%</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Amount -->
                                            <div class="col-md-1 col-12 text-md-end pe-0 mb-md-0 mb-3">
                                                <label class="mb-2 fw-semibold text-secondary d-block">Amount</label>
                                                <div class="d-flex align-items-center justify-content-md-end h-px-40 mt-1">
                                                    <span class="fw-bold text-primary amount-label" id="amount-label-1" data-id="1" style="font-size: 1rem;">
                                                        {{ old('amount[]', 'Rp 0') }}
                                                    </span>
                                                </div>
                                                <input type="number" class="form-control invoice-item-amount"
                                                    name="amount[]" id="amount-1" data-id="1"
                                                    value="{{ old('amount[]') }}" hidden>
                                            </div>
                                        </div>
                                        
                                        <!-- Trash Button -->
                                        <div class="d-flex align-items-center justify-content-center border-start px-2 bg-light" style="background-color: rgba(255, 76, 81, 0.03) !important;">
                                            <button type="button" class="btn btn-icon btn-outline-danger btn-sm border-0 rounded-circle waves-effect btn-del"
                                                data-id="1" data-repeater-delete="" style="transition: all 0.2s ease;">
                                                <i class="mdi mdi-trash-can-outline fs-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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
                                                                <div class="card border border-secondary-subtle shadow-sm mb-3 mt-5">
                                    <div class="card-header bg-transparent border-bottom py-3">
                                        <h5 class="mb-0 fw-semibold text-primary"><i class="mdi mdi-calculator me-2"></i>Financial Summary</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <!-- Hidden inputs for validation / compatibility -->
                                        <input type="number" name="weight" class="info-weight-total-label" value="{{ old('weight', @$quotation->weight ?? '0') }}" hidden>

                                        <!-- Sub Total Row -->
                                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                                            <span class="text-secondary fw-medium">Sub Total</span>
                                            <div class="text-end">
                                                @if (@$dquotation)
                            <div class="mb-3" data-repeater-list="group-a">
                                @foreach ($dquotation as $quote)
                                    @php
                                        $id++;
                                        $dataDetail++;
                                    @endphp
                                    <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                                        <div class="d-flex border border-secondary-subtle rounded-3 position-relative pe-0 mb-3 shadow-xs item-row-card" style="transition: all 0.25s ease; background-color: #fdfefe;">
                                            <div class="row w-100 p-3 align-items-start">
                                                <!-- Product Select -->
                                                <div class="col-md-5 col-12 mb-md-0 mb-3">
                                                    <label for="product" class="mb-2 fw-semibold text-secondary">Product</label>
                                                    <div class="form-floating form-floating-outline mb-2">
                                                        <select id="product-{{ $id }}"
                                                            class="form-select invoice-item-product"
                                                            data-allow-clear="true" name="product[]"
                                                            data-id="{{ $id }}">
                                                            <option value="">---- Choose Part Number Here ----</option>
                                                            @if (!empty($quote->id_equivalent) && !empty($quote->equivalent))
                                                                <option value="{{ $quote->id_equivalent }}"
                                                                    data-replacement="{{ $quote->id_equivalent }}"
                                                                    data-commodity="{{ $quote->equivalent->id_product }}"
                                                                    data-unit="{{ $quote->equivalent->product->unit ?? 'Pcs' }}"
                                                                    selected>
                                                                    {{ $quote->equivalent->brand }} {{ $quote->equivalent->pn }}
                                                                    ({{ $quote->equivalent->product->detail_desc ?? '' }})
                                                                    ||
                                                                    {{ $quote->equivalent->product->go ?? '' }}
                                                                </option>
                                                            @endif
                                                        </select>
                                                        <label for="product-{{ $id }}">Product Part Number</label>
                                                    </div>
                                                    <textarea class="form-control invoice-item-detail-product" rows="2" id="detailProduct-{{ $id }}"
                                                        placeholder="Detail Product. Example: Kaeser ASD" name="detail_product[]">{{ old('detail_product[]', $quote->detail_product) }}</textarea>
                                                </div>
                                                
                                                <!-- Price & Badges -->
                                                <div class="col-md-3 col-12 mb-md-0 mb-3">
                                                    <label class="mb-2 fw-semibold text-secondary">Price</label>
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
                                                    <div class="d-flex justify-content-between mt-2 flex-wrap gap-1">
                                                        <span class="badge bg-label-info d-flex align-items-center py-1.5 px-2.5 rounded-pill">
                                                            <i class="mdi mdi-cube-outline me-1 fs-6"></i>
                                                            Stock: <strong class="info-stock-label ms-1" id="info-stock-{{ $id }}">-</strong>
                                                        </span>
                                                        <span class="badge bg-label-warning d-flex align-items-center py-1.5 px-2.5 rounded-pill">
                                                            <i class="mdi mdi-scale me-1 fs-6"></i>
                                                            Weight: <strong class="info-weight-label mx-1" id="info-weight-{{ $id }}">-</strong> g
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Qty / Unit -->
                                                <div class="col-md-2 col-12 mb-md-0 mb-3">
                                                    <label class="mb-2 fw-semibold text-secondary">Qty / Unit</label>
                                                    <div class="input-group input-group-merge">
                                                        <input type="number" class="form-control invoice-item-qty"
                                                            placeholder="Qty" name="qty[]" id="qty-{{ $dataDetail }}"
                                                            data-id="{{ $dataDetail }}" min="1"
                                                            value="{{ old('qty[]', $quote->qty) }}">
                                                        <input type="text" class="input-group-text px-2 bg-light text-secondary invoice-item-info text-center"
                                                            id="info-qty-{{ $dataDetail }}" data-id="{{ $dataDetail }}" name="info_qty[]" readonly
                                                            value="{{ old('info_qty[]', $quote->info_qty ?? 'Pcs') }}" style="max-width: 65px; border-left: 0; font-size: 0.85rem; font-weight: 600;">
                                                    </div>
                                                </div>
                                                
                                                <!-- Discount -->
                                                <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                    <label class="mb-2 fw-semibold text-secondary">Disc</label>
                                                    <div class="input-group input-group-merge">
                                                        <input type="number" class="form-control invoice-item-disc text-center"
                                                            placeholder="0" name="disc[]" id="disc-{{ $dataDetail }}"
                                                            data-id="{{ $dataDetail }}" min="0"
                                                            value="{{ old('disc[]', $quote->disc ?? '0') }}" style="padding-left: 5px; padding-right: 5px;">
                                                        <span class="input-group-text px-1 bg-light text-secondary font-semibold" style="font-size: 0.85rem;">%</span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Amount -->
                                                <div class="col-md-1 col-12 text-md-end pe-0 mb-md-0 mb-3">
                                                    <label class="mb-2 fw-semibold text-secondary d-block">Amount</label>
                                                    <div class="d-flex align-items-center justify-content-md-end h-px-40 mt-1">
                                                        <span class="fw-bold text-primary amount-label" id="amount-label-{{ $id }}" data-id="{{ $id }}" style="font-size: 1rem;">
                                                            {{ old('amount[]', 'Rp ' . number_format($quote->amount, 0, '', '.')) }}
                                                        </span>
                                                    </div>
                                                    <input type="number" class="form-control invoice-item-amount"
                                                        name="amount[]" id="amount-{{ $id }}" data-id="1"
                                                        value="{{ old('amount[]', $quote->amount) }}" hidden>
                                                </div>
                                            </div>
                                            
                                            <!-- Trash Button -->
                                            <div class="d-flex align-items-center justify-content-center border-start px-2 bg-light" style="background-color: rgba(255, 76, 81, 0.03) !important;">
                                                <button type="button" class="btn btn-icon btn-outline-danger btn-sm border-0 rounded-circle waves-effect btn-del"
                                                    data-id="{{ $id }}" data-repeater-delete="" style="transition: all 0.2s ease;">
                                                    <i class="mdi mdi-trash-can-outline fs-4"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                                                    <span class="fs-5 fw-bold text-dark subtotal-label" id="subtotal-label">
                                                        {{ old('subtotal', @$quotation->subtotal ? 'Rp ' . number_format(@$quotation->subtotal, 0, '', '.') : 'Rp 0') }}
                                                    </span>
                                                    <input type="number" id="subtotal" name="subtotal"
                                                        value="{{ old('subtotal', @$quotation->subtotal ?? '') }}" hidden>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Tax Row (Toggle Switch) -->
                                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                                            <span class="text-secondary fw-medium">Tax (PPN 11%)</span>
                                            <div class="d-flex align-items-center gap-2">
                                                <span id="tax-amount-label" class="text-muted small fw-semibold me-1">Rp 0</span>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input" type="checkbox" id="tax-toggle" {{ @$quotation->tax == '11' ? 'checked' : '' }}>
                                                    <input type="number" name="tax" id="tax" value="{{ old('tax', @$quotation->tax ?? '0') }}" hidden>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Shipping Row -->
                                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                                            <span class="text-secondary fw-medium">Shipping</span>
                                            <div class="input-group input-group-sm" style="width: 180px;">
                                                <span class="input-group-text bg-transparent">Rp</span>
                                                <input type="text" id="shipping-label" class="form-control text-end"
                                                    placeholder="0" data-type="currency"
                                                    value="{{ old('shipping', @$quotation->shipping ? number_format(@$quotation->shipping, 0, '', '.') : '0') }}">
                                                <input type="number" name="shipping" id="shipping"
                                                    value="{{ old('shipping', @$quotation->shipping ?? '0') }}" hidden>
                                            </div>
                                        </div>

                                        <!-- Discount Row -->
                                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                                            <span class="text-secondary fw-medium">Discount</span>
                                            <div class="input-group input-group-sm" style="width: 180px;">
                                                <span class="input-group-text bg-transparent">Rp</span>
                                                <input type="text" id="diskon-label" class="form-control text-end"
                                                    placeholder="0" data-type="currency"
                                                    value="{{ old('diskon', @$quotation->diskon ? number_format(@$quotation->diskon, 0, '', '.') : '0') }}">
                                                <input type="number" name="diskon" id="diskon"
                                                    value="{{ old('diskon', @$quotation->diskon ?? '0') }}" hidden>
                                            </div>
                                        </div>

                                        <!-- Total Row (Highlighted) -->
                                        <div class="d-flex align-items-center justify-content-between p-3 rounded-bottom" style="background: linear-gradient(135deg, rgba(102, 108, 255, 0.08) 0%, rgba(102, 108, 255, 0.03) 100%);">
                                            <input type="number" id="totalNoTax" name="total_no_tax"
                                                value="{{ old('total_no_tax', @$quotation->total_no_tax ?? '') }}" hidden>
                                            <span class="text-primary fw-bold fs-5">Total</span>
                                            <div class="text-end">
                                                @if (@$dquotation)
                            <div class="mb-3" data-repeater-list="group-a">
                                @foreach ($dquotation as $quote)
                                    @php
                                        $id++;
                                        $dataDetail++;
                                    @endphp
                                    <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                                        <div class="d-flex border border-secondary-subtle rounded-3 position-relative pe-0 mb-3 shadow-xs item-row-card" style="transition: all 0.25s ease; background-color: #fdfefe;">
                                            <div class="row w-100 p-3 align-items-start">
                                                <!-- Product Select -->
                                                <div class="col-md-5 col-12 mb-md-0 mb-3">
                                                    <label for="product" class="mb-2 fw-semibold text-secondary">Product</label>
                                                    <div class="form-floating form-floating-outline mb-2">
                                                        <select id="product-{{ $id }}"
                                                            class="form-select invoice-item-product"
                                                            data-allow-clear="true" name="product[]"
                                                            data-id="{{ $id }}">
                                                            <option value="">---- Choose Part Number Here ----</option>
                                                            @if (!empty($quote->id_equivalent) && !empty($quote->equivalent))
                                                                <option value="{{ $quote->id_equivalent }}"
                                                                    data-replacement="{{ $quote->id_equivalent }}"
                                                                    data-commodity="{{ $quote->equivalent->id_product }}"
                                                                    data-unit="{{ $quote->equivalent->product->unit ?? 'Pcs' }}"
                                                                    selected>
                                                                    {{ $quote->equivalent->brand }} {{ $quote->equivalent->pn }}
                                                                    ({{ $quote->equivalent->product->detail_desc ?? '' }})
                                                                    ||
                                                                    {{ $quote->equivalent->product->go ?? '' }}
                                                                </option>
                                                            @endif
                                                        </select>
                                                        <label for="product-{{ $id }}">Product Part Number</label>
                                                    </div>
                                                    <textarea class="form-control invoice-item-detail-product" rows="2" id="detailProduct-{{ $id }}"
                                                        placeholder="Detail Product. Example: Kaeser ASD" name="detail_product[]">{{ old('detail_product[]', $quote->detail_product) }}</textarea>
                                                </div>
                                                
                                                <!-- Price & Badges -->
                                                <div class="col-md-3 col-12 mb-md-0 mb-3">
                                                    <label class="mb-2 fw-semibold text-secondary">Price</label>
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
                                                    <div class="d-flex justify-content-between mt-2 flex-wrap gap-1">
                                                        <span class="badge bg-label-info d-flex align-items-center py-1.5 px-2.5 rounded-pill">
                                                            <i class="mdi mdi-cube-outline me-1 fs-6"></i>
                                                            Stock: <strong class="info-stock-label ms-1" id="info-stock-{{ $id }}">-</strong>
                                                        </span>
                                                        <span class="badge bg-label-warning d-flex align-items-center py-1.5 px-2.5 rounded-pill">
                                                            <i class="mdi mdi-scale me-1 fs-6"></i>
                                                            Weight: <strong class="info-weight-label mx-1" id="info-weight-{{ $id }}">-</strong> g
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Qty / Unit -->
                                                <div class="col-md-2 col-12 mb-md-0 mb-3">
                                                    <label class="mb-2 fw-semibold text-secondary">Qty / Unit</label>
                                                    <div class="input-group input-group-merge">
                                                        <input type="number" class="form-control invoice-item-qty"
                                                            placeholder="Qty" name="qty[]" id="qty-{{ $dataDetail }}"
                                                            data-id="{{ $dataDetail }}" min="1"
                                                            value="{{ old('qty[]', $quote->qty) }}">
                                                        <input type="text" class="input-group-text px-2 bg-light text-secondary invoice-item-info text-center"
                                                            id="info-qty-{{ $dataDetail }}" data-id="{{ $dataDetail }}" name="info_qty[]" readonly
                                                            value="{{ old('info_qty[]', $quote->info_qty ?? 'Pcs') }}" style="max-width: 65px; border-left: 0; font-size: 0.85rem; font-weight: 600;">
                                                    </div>
                                                </div>
                                                
                                                <!-- Discount -->
                                                <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                    <label class="mb-2 fw-semibold text-secondary">Disc</label>
                                                    <div class="input-group input-group-merge">
                                                        <input type="number" class="form-control invoice-item-disc text-center"
                                                            placeholder="0" name="disc[]" id="disc-{{ $dataDetail }}"
                                                            data-id="{{ $dataDetail }}" min="0"
                                                            value="{{ old('disc[]', $quote->disc ?? '0') }}" style="padding-left: 5px; padding-right: 5px;">
                                                        <span class="input-group-text px-1 bg-light text-secondary font-semibold" style="font-size: 0.85rem;">%</span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Amount -->
                                                <div class="col-md-1 col-12 text-md-end pe-0 mb-md-0 mb-3">
                                                    <label class="mb-2 fw-semibold text-secondary d-block">Amount</label>
                                                    <div class="d-flex align-items-center justify-content-md-end h-px-40 mt-1">
                                                        <span class="fw-bold text-primary amount-label" id="amount-label-{{ $id }}" data-id="{{ $id }}" style="font-size: 1rem;">
                                                            {{ old('amount[]', 'Rp ' . number_format($quote->amount, 0, '', '.')) }}
                                                        </span>
                                                    </div>
                                                    <input type="number" class="form-control invoice-item-amount"
                                                        name="amount[]" id="amount-{{ $id }}" data-id="1"
                                                        value="{{ old('amount[]', $quote->amount) }}" hidden>
                                                </div>
                                            </div>
                                            
                                            <!-- Trash Button -->
                                            <div class="d-flex align-items-center justify-content-center border-start px-2 bg-light" style="background-color: rgba(255, 76, 81, 0.03) !important;">
                                                <button type="button" class="btn btn-icon btn-outline-danger btn-sm border-0 rounded-circle waves-effect btn-del"
                                                    data-id="{{ $id }}" data-repeater-delete="" style="transition: all 0.2s ease;">
                                                    <i class="mdi mdi-trash-can-outline fs-4"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                                                    <span class="fs-4 fw-bold text-primary harga-total-label" id="hargaTotalLabel">
                                                        {{ old('harga_total', @$quotation->harga_total ? 'Rp ' . number_format(@$quotation->harga_total, 0, '', '.') : 'Rp 0') }}
                                                    </span>
                                                    <input type="number" id="hargaTotal" name="harga_total"
                                                        value="{{ old('harga_total', @$quotation->harga_total ?? '') }}" hidden>
                                                @endif
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
        <style>
            .item-row-card {
                transition: all 0.25s ease-in-out;
            }
            .item-row-card:hover {
                border-color: #666cff !important;
                box-shadow: 0 4px 12px rgba(102, 108, 255, 0.08) !important;
            }
            .btn-del:hover {
                background-color: rgba(255, 76, 81, 0.1) !important;
            }
        </style>
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

                                // 1. Delegated Change handler for Product dropdown
                $(document).on('change', '.invoice-item-product', function(ev) {
                    var selectedOpt = $(this).find(':selected');
                    var replacementId = selectedOpt.data('replacement');
                    if (!replacementId) return;
                    var Url = '/quotation/sparepart/' + replacementId;
                    var commodity = selectedOpt.data('commodity');
                    var id = $(this).data('id');
                    var unit = selectedOpt.data('unit');

                    // Set unit value automatically
                    $(`#info-qty-${id}`).val(unit || 'Pcs');

                    $.ajax({
                        url: '/product-in/replacement/' + commodity,
                        type: 'GET',
                        success: function(response) {
                            if (response && response.length > 0) {
                                $(`#info-stock-${id}`).text(response[0].stock);
                                $(`#info-weight-${id}`).text(response[0].weight);
                            }
                            recalculateTotals();
                        }
                    });

                    $.ajax({
                        url: Url,
                        type: 'GET',
                        success: function(response) {
                            if (response && response.length > 0) {
                                $(`#detailProduct-${id}`).val(response[0].detail);
                                $(`#priceLabel-${id}`).val(formatPrice(response[0].price));
                                $(`#price-${id}`).val(response[0].price);
                                
                                var price = response[0].price;
                                var disc = isNaN(parseInt($(`#disc-${id}`).val())) ? 0 : parseInt($(`#disc-${id}`).val());
                                $(`#qty-${id}`).val(1);
                                
                                var amount = price * 1;
                                var discountedAmount = amount - (amount * disc / 100);
                                
                                $(`#amount-${id}`).val(discountedAmount);
                                $(`#amount-label-${id}`).html(`${formatter.format(discountedAmount)}`);
                            }
                            recalculateTotals();
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                });

                // 2. Delegated Price Formatting keyup handler
                $(document).on('keyup', '.invoice-item-price-label', function() {
                    var input = $(this);
                    var id = input.data('id');
                    var input_val = input.val();

                    if (input_val === "") return;

                    var original_len = input_val.length;
                    var caret_pos = input.prop("selectionStart");

                    input_val = formatNumber(input_val);
                    input.val(input_val);
                    
                    var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                    $(`#price-${id}`).val(isNaN(nomorInt) ? 0 : nomorInt);

                    var updated_len = input_val.length;
                    caret_pos = updated_len - original_len + caret_pos;
                    input[0].setSelectionRange(caret_pos, caret_pos);
                });

                // Custom Select2 rendering for Client to display beautiful role badges (Customer / Leads)
                if ($('#select2Basic').length) {
                    if ($('#select2Basic').hasClass("select2-hidden-accessible")) {
                        $('#select2Basic').select2('destroy');
                    }
                    $('#select2Basic').select2({
                        placeholder: ' ---- Choose Company Here ---- ',
                        allowClear: true,
                        width: '100%',
                        templateResult: formatClientOption,
                        templateSelection: formatClientSelection,
                        escapeMarkup: function(markup) {
                            return markup;
                        }
                    });
                }

                function formatClientOption(state) {
                    if (!state.id) {
                        return state.text;
                    }
                    var role = $(state.element).data('role') || 'Leads';
                    var badgeClass = (role === 'Customers') ? 'bg-label-success' : 'bg-label-warning';
                    var roleLabel = (role === 'Customers') ? 'Customer' : 'Leads';
                    return '<span class="d-flex align-items-center"><span class="badge ' + badgeClass + ' me-2">' + roleLabel + '</span>' + state.text + '</span>';
                }

                function formatClientSelection(state) {
                    if (!state.id) {
                        return state.text;
                    }
                    var role = $(state.element).data('role') || 'Leads';
                    var badgeClass = (role === 'Customers') ? 'bg-label-success' : 'bg-label-warning';
                    var roleLabel = (role === 'Customers') ? 'Customer' : 'Leads';
                    return '<span class="d-flex align-items-center"><span class="badge ' + badgeClass + ' me-2">' + roleLabel + '</span>' + state.text + '</span>';
                }

                function initializeSelect2Product() {
                    $('.invoice-item-product').not('.select2-hidden-accessible').select2({
                        placeholder: ' ---- Choose Part Number Here ---- ',
                        allowClear: true,
                        width: '100%',
                        ajax: {
                            url: '{{ route('quotation.products.search') }}',
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    q: params.term
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data
                                };
                            },
                            cache: true
                        }
                    }).on('select2:select', function (e) {
                        var data = e.params.data;
                        $(this).find('option:selected')
                            .attr('data-replacement', data.replacement)
                            .data('replacement', data.replacement)
                            .attr('data-commodity', data.commodity)
                            .data('commodity', data.commodity)
                            .attr('data-unit', data.unit)
                            .data('unit', data.unit);
                        $(this).trigger('change');
                    });
                }

                // 3. Delegated Logic for item amount calculation (keyup/change/click on price-label, qty, disc)
                $(document).on('keyup change click', '.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc', function() {
                    var id = $(this).data('id');
                    var valHarga = isNaN(parseInt($(`#price-${id}`).val())) ? 0 : parseInt($(`#price-${id}`).val());
                    var disc = isNaN(parseInt($(`#disc-${id}`).val())) ? 0 : parseInt($(`#disc-${id}`).val());
                    var qty = isNaN(parseInt($(`#qty-${id}`).val())) ? 0 : parseInt($(`#qty-${id}`).val());
                    
                    var amount = valHarga * qty;
                    var discountedAmount = amount - (amount * disc / 100);
                    
                    $(`#amount-${id}`).val(discountedAmount);
                    $(`#amount-label-${id}`).html(`${formatter.format(discountedAmount)}`);
                    
                    recalculateTotals();
                });

                // 4. Delegated handler for general total calculations
                $(document).on('keyup change', '#subtotal, #tax, #shipping-label, #diskon-label', function() {
                    recalculateTotals();
                });

                // 4.1. Tax toggle switch change handler
                $(document).on('change', '#tax-toggle', function() {
                    var isChecked = $(this).is(':checked');
                    $('#tax').val(isChecked ? '11' : '0').trigger('change');
                });

                // 5. Recalculate totals on repeater added/deleted events
                $(document).on('repeater:added', function() {
                    initializeSelect2Product();
                });

                $(document).on('repeater:deleted', function() {
                    recalculateTotals();
                });

                // 6. Central Recalculation function
                function recalculateTotals() {
                    var sTotal = 0;
                    $('.invoice-item-amount').each(function() {
                        if ($(this).closest('[data-repeater-item]').is(':visible')) {
                            var val = parseInt($(this).val());
                            if (!isNaN(val)) {
                                sTotal += val;
                            }
                        }
                    });
                    
                    $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                    $('#subtotal').val(sTotal);

                    var shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($('#shipping').val());
                    var discount = isNaN(parseInt($('#diskon').val())) ? 0 : parseInt($('#diskon').val());
                    var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val());
                    
                    var dTotal = sTotal - discount;
                    var taxAmount = parseInt(dTotal * tax / 100);
                    $('#tax-amount-label').html(`${formatter.format(taxAmount)}`);

                    var hTotal = parseInt(dTotal + taxAmount + shipping);
                    var noTax = parseInt(dTotal + shipping);

                    $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                    $('#hargaTotal').val(hTotal);
                    $('#totalNoTax').val(noTax);
                    
                    // Recalculate total weight
                    var weightTotal = 0;
                    $('.info-weight-label').each(function() {
                        if ($(this).closest('[data-repeater-item]').is(':visible')) {
                            var w = parseInt($(this).text());
                            if (!isNaN(w)) {
                                weightTotal += w;
                            }
                        }
                    });
                    $(`.info-weight-total-label`).val(weightTotal + ' g');
                }
                initializeSelect2Product();
            })
        </script>
    @endpush
