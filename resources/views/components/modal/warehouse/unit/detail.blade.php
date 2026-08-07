<div class="modal-onboarding modal modal-lg fade animate__animated" id="detailUnit-{{ $unitr->id }}" tabindex="-1"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="onboarding-content mb-0">
                    <div class="row mb-4">
                        <div class="col-6 text-start border-right">
                            <h4 class="onboarding-title text-center text-body border-top border-bottom">Informasi Unit
                            </h4>
                            <div class="row">
                                <div class="col-4">
                                    Unit
                                </div>
                                <div class="col-8">
                                    : {{ $unitr->unit->unit->unit }}
                                </div>
                                <div class="col-4">
                                    Brand & Type
                                </div>
                                <div class="col-8">
                                    : {{ $unitr->unit->brand }} {{ $unitr->unit->unit->sku }}
                                </div>
                                <div class="col-4">
                                    Motor
                                </div>
                                <div class="col-8">
                                    : {{ $unitr->unit->unit->power }}
                                </div>
                                <div class="col-4">
                                    Pressure
                                </div>
                                <div class="col-8">
                                    : {{ $unitr->unit->unit->bar }}
                                </div>
                                <div class="col-4">
                                    FAD
                                </div>
                                <div class="col-8">
                                    : {{ $unitr->unit->unit->air_cap }}
                                </div>
                                <div class="col-4">
                                    Voltage
                                </div>
                                <div class="col-8">
                                    : {{ $unitr->unit->unit->voltage }}
                                </div>
                                <div class="col-4">
                                    Connection
                                </div>
                                <div class="col-8">
                                    : {{ $unitr->unit->unit->connect }}
                                </div>
                                <div class="col-4">
                                    Dimensi
                                </div>
                                <div class="col-8">
                                    : {{ $unitr->unit->unit->dimension }}
                                </div>
                                <div class="col-4">
                                    Weight
                                </div>
                                <div class="col-8">
                                    : {{ $unitr->unit->unit->weight }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6 text-start border-right">
                            <h4 class="onboarding-title text-center text-body border-top border-bottom">Other</h4>
                            <div class="row">
                                @php
                                    switch ($unitr->status) {
                                        case 'On Rental':
                                            $color = 'warning';
                                            $title = 'On Rental';
                                            break;
                                        case 'Ready':
                                            $color = 'primary';
                                            $title = 'Ready';
                                            break;
                                        case 'Service':
                                            $color = 'danger';
                                            $title = 'Service';
                                            break;
                                        case 'Sold':
                                            $color = 'dark';
                                            $title = 'Sold';
                                            break;
                                        default:
                                            break;
                                    }
                                @endphp
                                <div class="col-6 mb-2">
                                    <p class="mb-0">
                                        Status Unit
                                    </p>
                                    <span
                                        class="badge rounded-pill bg-label-{{ $color }} fs-5">{{ $title }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">Kondisi </div>
                                <div class="col-8">: {{ $unitr->status_unit }}</div>
                                <div class="col-4">Harga Unit </div>
                                <div class="col-8">: Rp {{ number_format($unitr->price, 0, '', '.') }}</div>
                                <div class="col-4">Harga Rental </div>
                                <div class="col-8">: Rp {{ number_format($unitr->rental_price, 0, '', '.') }}
                                </div>
                                <div class="col-4">Harga Best </div>
                                <div class="col-8">: Rp {{ number_format($unitr->best_price, 0, '', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="float-end mb-2">
                        <a href="{{$unitr->desc}}" type="button" class="btn btn-primary waves-effect" target="_blank"> Data Sheet </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
