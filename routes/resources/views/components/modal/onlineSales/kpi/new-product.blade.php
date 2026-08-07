<div class="modal-onboarding modal modal-xl fade animate__animated" id="newProduct" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="onboarding-content mb-0">
                    <h4 class="onboarding-title text-body">Link Product</h4>
                    <div class="table-responsive text-nowrap mb-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Qty</th>
                                    <th>Link 1</th>
                                    <th>Link 2</th>
                                    <th>Link 3</th>
                                    <th>Link 4</th>
                                    <th>Link 5</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($onSale as $item)
                                    <tr>
                                        <td>{{ $item['date'] }}</td>
                                        <td>{{ $item['count'] ?? 0 }}</td>
                                        <td>
                                            <a href="{{ $item['link']['product'][0] ?? '#' }}"
                                                class="btn btn-label-secondary waves-effect {{ !empty($item['link']['product'][0]) ? 'text-success' : 'text-danger' }} p-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="tooltip-dark" title="{{ $item['link']['desc_product'][0] ?? 'Belum Diisi' }}">
                                                <i
                                                    class="menu-icon tf-icons mdi mdi-{{ !empty($item['link']['product'][0]) ? 'check' : 'window-close' }} m-0 fs-5"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ $item['link']['product'][1] ?? '#' }}"
                                                class="btn btn-label-secondary waves-effect {{ !empty($item['link']['product'][1]) ? 'text-success' : 'text-danger' }} p-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="tooltip-dark" title="{{ $item['link']['desc_product'][1] ?? 'Belum Diisi' }}">
                                                <i
                                                    class="menu-icon tf-icons mdi mdi-{{ !empty($item['link']['product'][1]) ? 'check' : 'window-close' }} m-0 fs-5"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ $item['link']['product'][2] ?? '#' }}"
                                                class="btn btn-label-secondary waves-effect {{ !empty($item['link']['product'][2]) ? 'text-success' : 'text-danger' }} p-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="tooltip-dark" title="{{ $item['link']['desc_product'][2] ?? 'Belum Diisi' }}">
                                                <i
                                                    class="menu-icon tf-icons mdi mdi-{{ !empty($item['link']['product'][2]) ? 'check' : 'window-close' }} m-0 fs-5"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ $item['link']['product'][3] ?? '#' }}"
                                                class="btn btn-label-secondary waves-effect {{ !empty($item['link']['product'][3]) ? 'text-success' : 'text-danger' }} p-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="tooltip-dark" title="{{ $item['link']['desc_product'][3] ?? 'Belum Diisi' }}">
                                                <i
                                                    class="menu-icon tf-icons mdi mdi-{{ !empty($item['link']['product'][3]) ? 'check' : 'window-close' }} m-0 fs-5"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ $item['link']['product'][4] ?? '#' }}"
                                                class="btn btn-label-secondary waves-effect {{ !empty($item['link']['product'][4]) ? 'text-success' : 'text-danger' }} p-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="tooltip-dark" title="{{ $item['link']['desc_product'][4] ?? 'Belum Diisi' }}">
                                                <i
                                                    class="menu-icon tf-icons mdi mdi-{{ !empty($item['link']['product'][4]) ? 'check' : 'window-close' }} m-0 fs-5"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- <div class="modal-footer border-0">
                <button type="button" class="btn btn-label-secondary waves-effect waves-effect" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="submit" class="btn btn-p 
                      data-bs-toggle="tooltip"
                      data-bs-placement="top"
                      data-bs-custom-class="tooltip-dark"
                      title="{{ $item['link']['desc_product'][0] ?? 'Belum Diisi' }}"rimary waves-effect waves-light">Submit</button>
            </div> --}}
        </div>
    </div>
</div>
