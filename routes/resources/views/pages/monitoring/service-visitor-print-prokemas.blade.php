@extends('layouts.sales.app')
@section('title', 'Service reports')

<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
            <div class="mb-xl-0 pb-1">
                <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                    <span class="app-brand-logo demo">
                        <span style="color: var(--bs-primary)">
                            <img class="text-md"
                                src="{{ url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png') }}"
                                alt="" srcset="" width="60%">
                        </span>
                    </span>
                </div>
                <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                <div style="font-size: 10px">
                    <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                    <p class="mb-1">Bandung – Jawa Barat 40218</p>
                    <p class="mb-1">
                        <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                        54417653{{ '  |  ' }}<i
                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                    </p>
                </div>
            </div>
            <div class="text-end">
                <h3 class="fw-bold">DAILY MONITORING</h3>
                <div>
                    <span class="fw-bolder">{{ $machine->unit->unit->unit }}</span>
                </div>
                <div class="mt-1">
                    <span class="text-muted">{{ $machine->unit->brand }} {{ $machine->unit->unit->sku }}</span>
                </div>
                <div class="mt-1">
                    <span class="text-muted">{{ $machine->tag }} - {{ $machine->location }}</span>
                </div>
            </div>
        </div>
        <hr class="my-2">
        {{-- <div class="row mb-3">
            <div class="col-2 fw-medium">
                <p class="mb-1">Customers </p>
                <p class="mb-1">Address </p>
                <p class="mb-1">PIC </p>
            </div>
            <div class="col-4">
                <p class="mb-1">: {{ $service->pic->client->company }}</p>
                <p class="mb-1">: {{ $service->pic->client->area }}</p>
                <p class="mb-1">: {{ $service->pic->name_pic }}</p>
            </div>
            <div class="col-2 fw-medium">
                <p class="mb-1">Unit Type </p>
                <p class="mb-1">Serial Number </p>
                <p class="mb-1">Running & Load </p>
            </div>
            <div class="col-4">
                <p class="mb-1">: {{ $service->machine->unit->brand }} {{ $service->machine->unit->unit->sku }}
                </p>
                <p class="mb-1">: {{ $service->machine->unit->sn }}</p>
                <p class="mb-1">: {{ $service->running }} | {{ $service->load }}</p>
            </div>
        </div> --}}

        <h5>Daily Check</h5>
        <div class="table-responsive text-nowrap">
            @if ($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER')
                <table class="table table-bordered">
                    <thead class="table-light">
                        <th>Date</th>
                        <th>Condition</th>
                        <th>R Hr.</th>
                        <th>L Hr.</th>
                        <th>Press.</th>
                        <th>Temp. (90°C - 100°C)</th>
                        <th>Oil Lvl</th>
                        <th>Kebocoran</th>
                        <th>PIC</th>
                    </thead>
                    <tbody>
                        @foreach ($compressor as $item)
                            <tr>
                                <td>{{ $item['date'] }}</td>
                                <td>{{ $item['condition'] }}</td>
                                <td>{{ $item['running'] }}</td>
                                <td>{{ $item['loading'] }}</td>
                                <td>{{ $item['pressure'] }}</td>
                                <td>
                                    @php
                                        $stringTemp = $item['temp'] ?? '';
                                        $stringTemp = str_replace(',', '.', $stringTemp); // ganti koma jadi titik

                                        $tempNumber = null;

                                        if (preg_match('/\d+(\.\d+)?/', $stringTemp, $matches)) {
                                            $tempNumber = (float) $matches[0];
                                        }
                                    @endphp

                                    @if (!is_null($tempNumber) && $tempNumber >= 100)
                                        <p class="mb-0 fw-bold fs-6 text-danger">
                                            {{ $item['temp'] }}
                                        </p>
                                    @else
                                        {{ $item['temp'] }}
                                    @endif
                                </td>
                                <td>{{ $item['oil_level'] }}</td>
                                <td>{{ $item['leak'] }}</td>
                                <td>{{ $item['pic'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <table class="table table-bordered">
                    <thead class="table-light">
                        <th>Date</th>
                        <th>Condition</th>
                        <th>Temp IN</th>
                        <th>Temp OUT</th>
                        <th>Dew P.</th>
                        <th>Auto Drain</th>
                        <th>Fan Kondenser</th>
                        <th>Kebocoran</th>
                        <th>PIC</th>
                    </thead>
                    <tbody>
                        @foreach ($dryer as $item)
                            <tr>
                                <td>{{ $item['date'] }}</td>
                                <td>{{ $item['condition'] }}</td>
                                <td>{{ $item['temp'] }}</td>
                                <td>{{ $item['temp_out'] }}</td>
                                <td>
                                    @if (!is_null($item['dew']) && $item['dew'] > 10)
                                        <p class="mb-0 fw-bold fs-6 text-danger">
                                            {{ $item['dew'] }}</p>
                                    @else
                                        {{ $item['dew'] }}
                                    @endif
                                </td>
                                <td>{{ $item['drain'] }}</td>
                                <td>{{ $item['fan'] }}</td>
                                <td>{{ $item['leak'] }}</td>
                                <td>{{ $item['pic'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if ($machine->unit->unit->unit == 'REFRIGERANT AIR DRYER')
            <div class="card invoice-preview-card mb-4">
                <div class="card-body">
                    <div class="monthly mb-4">
                        <h5>Monthly Check</h5>
                        <div class="table-responsive text-nowrap mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <th>Date</th>
                                    <th>HP (High Pressure)</th>
                                    <th>LP (Low Pressure)</th>
                                    <th>Strainer</th>
                                </thead>
                                <tbody>
                                    @if ($monthly)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($monthly->date)->format('d-m-Y') }}</td>
                                            <td>{{ $monthly->hp }}</td>
                                            <td>{{ $monthly->lp }}</td>
                                            <td>{{ $monthly->strainer }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        @endif

        <div class="issue mt-5">
            <h5>Issue & Recommendation</h5>
            <div class="table-responsive text-nowrap mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="width:10%;">Date</th>
                            <th>Issue</th>
                            <th>Recommendation</th>
                            <th>Part Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($issue as $issues)
                            <tr>
                                <td>{{ $issues->date ?? 'N/A' }}</td>
                                <td>
                                    <pre class="mb-1"
                                        style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;">{{ $issues->issue }}</pre>
                                </td>
                                <td>{{ $issues->recommendation ?? '-' }}</td>
                                <td>{{ $issues->pn ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- <div class="quote mb-4">
            <div class="table-responsive text-nowrap mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>No. Quote</th>
                            <th>No. PR</th>
                            <th>Title</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quotes as $quote)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{ route('quotation.show', $quote->id) }}" class="text-black">
                                        {{ $quote->no_quote }}
                                    </a>
                                </td>
                                <td>{{ $quote->no_pr }}</td>
                                <td>{{ $quote->title }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum Ada Quote</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div> --}}

        <div class="mainlog mt-5">
            <h5>Maintenance Log</h5>
            <div class="table-responsive text-nowrap mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="width:20%;">Date</th>
                            <th>Maintenance</th>
                            <th style="width:25%;">Pic</th>
                            {{-- <th style="width:10%;">Button</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mainlog as $item)
                            <tr>
                                <td>{{ $item->date ?? 'N/A' }}</td>
                                <td>
                                    <pre class="mb-1"
                                        style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;">{{ $item->desc }}</pre>
                                </td>
                                <td>{{ $item->name }}</td>
                                {{-- @if ($mainlog['id_service'] != null)
                                    <td>
                                        <a class="btn btn-warning waves-effect"
                                            href="{{ route('service-reports.show', $mainlog['id_service']) }}">
                                            <i class="menu-icon tf-icons mdi mdi-eye-outline"></i>
                                        </a>
                                    </td>
                                @elseif($mainlog['id_service'] == null && $mainlog['id_pic'] == Auth::user()->id)
                                    <td>
                                        <a class="btn btn-primary waves-effect"
                                            href="{{ route('create.daily-monitoring-reports', [$mainlog['id'], $mainlog['id_machine']]) }}">
                                            <i class="menu-icon tf-icons mdi mdi-file-plus-outline"></i>
                                        </a>
                                    </td>
                                @else
                                    <td>
                                        Has No Reports
                                    </td>
                                @endif --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-monitoring-print.css" />
    <link rel="stylesheet" href="style.css">
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
    <script>
        window.addEventListener('beforeprint', () => {
            const rows = document.querySelectorAll('table tr');
            rows.forEach((row, index) => {
                const rect = row.getBoundingClientRect();
                if (rect.top > window.innerHeight) {
                    row.style.marginTop = '20mm';
                }
            });
        });
    </script>
@endpush
