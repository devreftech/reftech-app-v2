<form action="{{ route('sales-order.dokumentasi', $schedule->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="dokumentasi-{{ $schedule->id }}" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content">
                        <h4 class="onboarding-title text-center mb-4"> Dokumentasi
                            {{ @$schedule->order->quote->invoice[0]->no_po ?? @$schedule->order->quote->pic->client->customer }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    id="suratJalanSwitch" name="SJ" {{ $schedule->SJ == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="suratJalanSwitch">Surat
                                                    Jalan</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    id="beritaAcaraSwitch" name="BA" {{ $schedule->BA == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="beritaAcaraSwitch">Berita
                                                    Acara</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-floating form-floating-outline">
                                                <textarea class="form-control h-100" id="exampleFormControlTextarea1" name="note" placeholder="Note Schedule here...">{{ @$schedule->note_doc }}</textarea>
                                                <label for="exampleFormControlTextarea1">Note Document</label>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <select class="select2 form-select" id="selectAddress"
                                            aria-label="Default select example" name="destination"
                                            data-allow-clear="true">
                                            <option value="1"
                                                {{ old('address', $schedule->SJ) == '1' ? 'selected' : '' }}>
                                                Surat Jalan
                                            </option>
                                            <option value="2"
                                                {{ old('address', $schedule->BA) == '2' ? 'selected' : '' }}>
                                                Berita Acara
                                            </option>
                                        </select> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
