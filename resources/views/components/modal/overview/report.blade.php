<div class="modal animate__animated animate__fadeIn" id="detailReport{{ $sale['id'] }}" tabindex="-1"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-2">
                        <img src="{{ url('') . '/' . $sale['image'] }}" alt="" srcset=""
                            class="rounded-circle" style="width : 100%; height:100%;">
                    </div>
                    <div class="col-10">
                        <h4 class="badge bg-label-secondary w-100 text-center">Achievement</h4>
                        <h5 class="text-center">Rp {{ number_format($sale['total'], 0, ',', '.') }}</h5>
                    </div>
                </div>
                <div class="row">
                    <ul class="p-0 m-0">
                        @php
                            $bulanMap = [
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ];
                        @endphp
                        @foreach ($sale['jumlah'] as $item)
                            <li class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <div>
                                            <i
                                                class="mdi mdi-48px mdi-alpha-{{ strtolower(substr($bulanMap[$item['bulan']], 0, 1)) }}-circle-outline"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{ $bulanMap[$item['bulan']] }}</h6>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-semibold text-heading">Rp
                                            {{ number_format($item['total'], 0, ',', '.') }}</span>
                                        @php
                                            $persenanSales =
                                                $sale['target'] > 0
                                                    ? round(($item['total'] / $sale['target']) * 100, 2)
                                                    : 0;
                                            if ($persenanSales >= 100) {
                                                $label = 'success';
                                            } elseif ($persenanSales >= 90) {
                                                $label = 'warning';
                                            } else {
                                                $label = 'danger';
                                            }

                                        @endphp
                                        <div class="ms-3 badge bg-label-{{ $label }} rounded-pill">
                                            {{ $persenanSales }}%</div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
