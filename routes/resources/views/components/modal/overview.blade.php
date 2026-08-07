<div class="modal animate__animated animate__fadeIn" id="overview-sales-{{ $overview['salesId'] }}" tabindex="-1"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="exampleModalLabel5"> Report
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-4">
                    <h5 class="card-header">Assigned: {{ $overview['sales'] }}</h5>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th>Week 1</th>
                                    <th>Week 2</th>
                                    <th>Week 3</th>
                                    <th>Week 4</th>
                                    <th>Week 5</th>
                                    <th>Total</th>
                                    <th>Presentase</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @if ($overview['salesId'] == 1 || $overview['salesId'] == 2 || $overview['salesId'] == 32)
                                    <tr>
                                        <td>
                                            <strong>New Leads</strong>
                                        </td>
                                        @php
                                            $totalLeadsFullWeek = 0;
                                        @endphp
                                        @foreach ($overview['leads'] as $week)
                                            <td>{{ $week }}</td>
                                            @php
                                                $totalLeadsFullWeek += $week;
                                            @endphp
                                        @endforeach
                                        <td>{{ $totalLeadsFullWeek }}</td>
                                        <td>
                                            {{ round(($totalLeadsFullWeek / ($user->target[0]->leads + $user->target[0]->leads / 4)) * 100) }}
                                            %
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Daily Call</strong>
                                        </td>
                                        @php
                                            $totalDCFullWeek = 0;
                                        @endphp
                                        @foreach ($overview['dc'] as $week)
                                            <td>{{ $week }}</td>
                                            @php
                                                $totalDCFullWeek += $week;
                                            @endphp
                                        @endforeach
                                        <td>{{ $totalDCFullWeek }}</td>
                                        <td>
                                            {{ round(($totalDCFullWeek / ($user->target[0]->dc + $user->target[0]->dc / 4)) * 100) }}
                                            %
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>
                                        <strong>CRM</strong>
                                    </td>
                                    @php
                                        $totalCRMFullWeek = 0;
                                    @endphp
                                    @foreach ($overview['crm'] as $week)
                                        <td>{{ $week }}</td>
                                        @php
                                            $totalCRMFullWeek += $week;
                                        @endphp
                                    @endforeach
                                    <td>{{ $totalCRMFullWeek }}</td>
                                    <td>
                                        @php
                                            if (is_array($dataCRM)) {
                                                $jumlahData = count($dataCRM);
                                            }
                                        @endphp
                                        {{ round(($totalCRMFullWeek / ($user->target[0]->crm + $user->target[0]->crm / 4)) * 100) }}
                                        %
                                    </td>
                                </tr>
                                {{-- @if ($user->detail[0]->area == 'Bekasi' || $user->detail[0]->area == 'Jabodetabek' || $user->detail[0]->area == 'Jawa Barat')
                                    <tr>
                                        <td>
                                            <strong>Visit</strong>
                                        </td>
                                        @php
                                            $totalVisitFullWeek = 0;
                                        @endphp
                                        @foreach ($dataVisit[$user->name] as $week)
                                            <td>{{ $week }}</td>
                                            @php
                                                $totalVisitFullWeek += $week;
                                            @endphp
                                        @endforeach
                                        <td>{{ $totalVisitFullWeek }}</td>
                                        <td>
                                            @php
                                                if (is_array($dataVisit)) {
                                                    $jumlahData = count($dataVisit);
                                                }
                                            @endphp
                                            @if ($jumlahData > 4)
                                                {{ round(($totalVisitFullWeek / ($user->target[0]->visit + $user->target[0]->visit / 4)) * 100) }}
                                                %
                                            @elseif($jumlahData == 4)
                                                {{ round(($totalVisitFullWeek / $user->target[0]->visit) * 100) }} %
                                            @endif
                                        </td>
                                    </tr>
                                @endif --}}
                                <tr>
                                    <td>
                                        <strong>Quotation</strong>
                                    </td>
                                    @php
                                        $totalQuoteFullWeek = 0;
                                    @endphp
                                    @foreach ($overview['quote'] as $week)
                                        <td>{{ $week }}</td>
                                        @php
                                            $totalQuoteFullWeek += $week;
                                        @endphp
                                    @endforeach
                                    <td>{{ $totalQuoteFullWeek }}</td>
                                    <td>
                                        {{ round(($totalQuoteFullWeek / ($user->target[0]->quote + $user->target[0]->quote / 4)) * 100) }}
                                        %
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Purchase Order</strong>
                                    </td>
                                    @php
                                        $totalPoFullWeek = 0;
                                    @endphp
                                    @foreach ($overview['po'] as $week)
                                        <td>{{ $week }}</td>
                                        @php
                                            $totalPoFullWeek += $week;
                                        @endphp
                                    @endforeach
                                    <td>{{ $totalPoFullWeek }}</td>
                                    <td>
                                        {{ round(($totalPoFullWeek / ($user->target[0]->quote + $user->target[0]->quote / 4)) * 100) }}
                                        %
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <p class="fw-semibold m-0"> Total Quotation</p>
                                        <p class="text-muted m-0">{{ $totalQuoteFullWeek }}</p>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <p class="fw-semibold m-0"> Total PO</p>
                                        <p class="text-muted m-0">{{ $totalPoFullWeek }}</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
