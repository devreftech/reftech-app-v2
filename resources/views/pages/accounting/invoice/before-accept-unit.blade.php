@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.sales.app')
@section('title', 'Request Invoice — Unit Quotation')
@section('content')
    <div class="row invoice-preview">
        {{-- Invoice Preview --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body p-4">
                    {{-- Header --}}
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column mb-0">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="" width="60%">
                                    </span>
                                </span>
                            </div>
                            <p class="mb-1 fw-bolder" style="font-size: 15px">PT Reftech Jaya Optima</p>
                            <div style="font-size: 12px; color: #555;">
                                <p class="mb-0">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                                <p class="mb-0">Bandung – Jawa Barat 40218</p>
                                <p class="mb-0"><i class="mdi mdi-phone-outline me-1" style="font-size:11px;"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1" style="font-size:11px;"></i>accounting@reftech.id &nbsp;|&nbsp; <i class="mdi mdi-web me-1" style="font-size:11px;"></i>www.reftech.id</p>
                                <p class="mb-0 mt-1" style="font-size:10.5px; color:#444; font-weight:500;">
                                    <i class="mdi mdi-certificate-outline me-1 text-primary"></i><span class="fw-bold" style="color:#696cff;">ISO Certified:</span> ISO 9001:2015 &nbsp;|&nbsp; ISO 14001:2015 &nbsp;|&nbsp; ISO 45001:2018
                                </p>
                            </div>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold mb-1" style="letter-spacing:2px; color:#696cff;">INVOICE REQUEST</h3>
                            <p class="mb-1 fw-semibold" style="font-size:14px;">#{{ $quote->no_quote }}</p>
                            <div class="mb-1">
                                <span class="badge bg-warning px-3 py-1 fs-6">Unit Quotation</span>
                            </div>
                            <p class="mb-0 text-muted" style="font-size:12px;">{{ $quote->date?->format('d F Y') }}</p>
                            @if ($quote->no_pr)
                                <p class="mb-0 text-muted" style="font-size:11px;">No. PR: {{ $quote->no_pr }}</p>
                            @endif
                            @if ($quote->type || $quote->week)
                                <p class="mb-0 text-muted" style="font-size:11px;">
                                    Type: {{ $quote->type }}{{ $quote->type && $quote->week ? ' | ' : '' }}{{ $quote->week ? 'Week ' . $quote->week : '' }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Accent Divider --}}
                    <div style="height:3px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:14px 0 16px;"></div>

                    {{-- Quote To + Prepared By Box --}}
                    <div style="display:flex !important; align-items:stretch !important; gap:12px; margin-bottom:16px; font-size:12px;">
                        <div style="flex:1; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                            <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Quote To</p>
                            <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;">{{ $quote->client?->company ?? '-' }}</p>
                            @php
                                $contactParts = [];
                                if ($quote->pic?->name_pic) {
                                    $contactParts[] = '<i class="mdi mdi-account-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($quote->pic->name_pic) . '</span>';
                                }
                                if ($quote->pic?->phone_pic) {
                                    $contactParts[] = '<i class="mdi mdi-phone-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($quote->pic->phone_pic) . '</span>';
                                }
                                if ($quote->client?->email) {
                                    $contactParts[] = '<i class="mdi mdi-email-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($quote->client->email) . '</span>';
                                }
                            @endphp
                            @if (count($contactParts) > 0)
                                <p class="mb-1" style="font-size:11.5px; color:#333;">
                                    {!! implode(' &nbsp;|&nbsp; ', $contactParts) !!}
                                </p>
                            @endif
                            @if ($quote->address || $quote->plant)
                                <p class="mb-0" style="font-size:11.5px; color:#222;">
                                    <i class="mdi mdi-map-marker-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;">{{ $quote->address ?? $quote->plant?->address }} {{ $quote->plant ? '(' . $quote->plant->name . ')' : '' }}</span>
                                </p>
                            @endif
                        </div>
                        <div style="min-width:240px; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                            <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Prepared By</p>
                            <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;">{{ $quote->sales?->name ?? 'Sales Engineer' }}</p>
                            <p class="mb-1 fw-medium" style="font-size:11.5px; color:#444;">
                                <i class="mdi mdi-briefcase-outline me-1" style="font-size:11px; color:#444;"></i>{{ $quote->sales?->title ?? 'Sales Engineer' }}
                            </p>
                            @if ($quote->sales?->email || $quote->sales?->phone)
                                <p class="mb-0" style="font-size:11.5px; color:#222;">
                                    @if ($quote->sales?->phone)
                                        <i class="mdi mdi-phone-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;">{{ $quote->sales->phone }}</span>
                                    @endif
                                    @if ($quote->sales?->phone && $quote->sales?->email) &nbsp;|&nbsp; @endif
                                    @if ($quote->sales?->email)
                                        <i class="mdi mdi-email-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;">{{ $quote->sales->email }}</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Items Table --}}
                    @php
                        $specLabels = [
                            'brand'=>'Brand','model'=>'Model','type_unit'=>'Type',
                            'bar'=>'Max Pressure','air_cap'=>'Air Capacity','power'=>'Motor Power',
                            'voltage'=>'Voltage','connect'=>'Drive','cooling'=>'Cooling Method',
                            'exhaust'=>'Connection','refrigerant_type'=>'Refrigerant Type','pdp'=>'PDP',
                            'filtration'=>'Filtration','oil_content'=>'Oil Content','grade'=>'Grade',
                            'capacity'=>'Capacity','material'=>'Material','test_pressure'=>'Test Pressure',
                            'inlet_pressure'=>'Inlet Pressure','outlet_pressure'=>'Outlet Pressure',
                            'inlet_cap'=>'Inlet Capacity (LP)','outlet_cap'=>'Outlet Capacity (HP)',
                            'dimension'=>'Dimension','weight'=>'Weight',
                        ];
                        $specUnits = [
                            'bar'=>' Bar','air_cap'=>' m³/min','test_pressure'=>' Bar',
                            'inlet_pressure'=>' Bar','outlet_pressure'=>' Bar',
                            'inlet_cap'=>' m³/min','outlet_cap'=>' m³/min',
                            'weight'=>' Kg','capacity'=>' Liter',
                        ];
                        $hasDisc = $quote->details->where('disc', '>', 0)->count() > 0;
                    @endphp
                    <div class="table-responsive rounded border mb-3">
                        <table class="table table-bordered m-0" style="width:100%; font-size:12px;">
                            <thead style="font-size:11px; background:#eeeeff; color:#3d3d8f;">
                                <tr>
                                    <th class="text-center py-2" style="width:4%; font-weight:700; border-color:#d0d0ff;">No.</th>
                                    <th class="text-center py-2" style="width:{{ $hasDisc ? '44%' : '49%' }}; font-weight:700; border-color:#d0d0ff;">DESKRIPSI</th>
                                    <th class="text-center py-2" style="width:10%; font-weight:700; border-color:#d0d0ff;">Qty</th>
                                    <th class="text-center py-2" style="width:18%; font-weight:700; border-color:#d0d0ff;">HARGA (IDR)</th>
                                    @if ($hasDisc)
                                        <th class="text-center py-2" style="width:7%; font-weight:700; border-color:#d0d0ff;">Disc</th>
                                    @endif
                                    <th class="text-center py-2" style="width:{{ $hasDisc ? '17%' : '19%' }}; font-weight:700; border-color:#d0d0ff;">TOTAL HARGA (IDR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $itemNo = 1;
                                    $headerCount = 0;
                                @endphp
                                @foreach ($quote->details as $item)
                                    @if ($item->type === 'header' || $item->type === 'heading')
                                        @php
                                            $lbl = trim($item->label ?? '');
                                            if (!preg_match('/^[A-Z0-9][\.\)]/i', $lbl)) {
                                                $lbl = chr(65 + ($headerCount % 26)) . '. ' . $lbl;
                                            }
                                            $headerCount++;
                                        @endphp
                                        <tr style="background:#f0f0ff;">
                                            <td colspan="{{ $hasDisc ? '6' : '5' }}" class="fw-bold text-primary text-uppercase px-3" style="padding: 5px 10px; font-size:11.5px; border-top:1px solid #d0d0ff; border-bottom:1px solid #d0d0ff;">
                                                <i class="mdi mdi-bookmark-outline me-1"></i>{{ $lbl }}
                                            </td>
                                        </tr>
                                    @else
                                        <tr style="font-size: 12px">
                                            <td class="text-center align-top py-2">{{ $itemNo++ }}</td>
                                            <td class="align-top py-2">
                                                @if ($item->type === 'unit' && $item->unit)
                                                    <p class="mb-1 fw-semibold" style="font-size: 12px">
                                                        {{ $item->label ?: ($item->unit->brand . ' ' . $item->unit->sku . ($item->unit->model ? ' — ' . $item->unit->model : '')) }}
                                                    </p>
                                                    @php $specs = $item->getSpecVisibleArray(); @endphp
                                                    @if (!empty($specs))
                                                        <div style="font-size:11px; color:#777; margin-top:4px;">
                                                            @foreach ($specs as $field)
                                                                @if ($field === 'unit') @continue @endif
                                                                @php $val = $item->unit->$field ?? null; @endphp
                                                                @if ($val && isset($specLabels[$field]))
                                                                    <div style="display:flex; padding:1px 0;">
                                                                        <span style="min-width:110px; flex-shrink:0;">{{ $specLabels[$field] }}</span>
                                                                        <span>: {{ $val }}{{ $specUnits[$field] ?? '' }}</span>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @elseif ($item->type === 'equivalent' || $item->type === 'sparepart' || $item->id_equivalent || $item->equivalent)
                                                     @if ($item->equivalent)
                                                         @php
                                                             $brandPn = trim(($item->equivalent->brand ?? '') . ($item->equivalent->pn ? ' - ' . $item->equivalent->pn : ''));
                                                             $subDesc = $item->label;
                                                             if (empty($subDesc) || $subDesc === $brandPn) {
                                                                 $subDesc = optional($item->equivalent->product)->description ?? optional($item->equivalent->product)->name;
                                                             }
                                                         @endphp
                                                         <p class="mb-0 fw-bold text-dark" style="font-size: 12px">{{ $brandPn ?: $item->label }}</p>
                                                         @if ($subDesc && $subDesc !== $brandPn)
                                                             <div style="font-size: 12px; color: #333333; font-weight: 500; margin-top: 2px; line-height: 1.4;">{{ $subDesc }}</div>
                                                         @endif
                                                     @else
                                                         <p class="mb-0 fw-bold text-dark" style="font-size: 12px">{{ $item->label }}</p>
                                                     @endif
                                                @else
                                                    <p class="mb-0 fw-bold text-dark" style="font-size: 12px">{{ $item->label }}</p>
                                                @endif
                                                @if ($item->description)
                                                    <div class="text-muted" style="font-size:11px; white-space:pre-line; margin-top:2px;">{{ $item->description }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center align-top py-2">{{ (float) $item->qty }} {{ $item->info_qty ?? 'Unit' }}</td>
                                            <td class="text-end align-top py-2">{{ number_format($item->price, 0, '', '.') }}</td>
                                            @if ($hasDisc)
                                                <td class="text-center align-top py-2">{{ $item->disc > 0 ? (float) $item->disc . '%' : '-' }}</td>
                                            @endif
                                            <td class="text-end align-top py-2 fw-semibold">{{ number_format($item->amount, 0, '', '.') }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Financial Summary Box --}}
                    @php
                        $afterDisc = $quote->diskon > 0
                            ? $quote->subtotal - $quote->discount_amount
                            : $quote->subtotal;
                    @endphp
                    <div class="d-flex justify-content-end mb-3">
                        <div style="min-width:270px; font-size:12px; border:1px solid #d0d0ff; border-left:4px solid #696cff; border-radius:6px; overflow:hidden; background:#fff;">
                            <table style="width:100%; border-collapse:collapse;">
                                <tr>
                                    <td style="padding:6px 16px 6px 14px; color:#555;">Subtotal</td>
                                    <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($quote->subtotal, 0, '', '.') }}</td>
                                </tr>
                                @if ($quote->diskon > 0)
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;">Discount{{ $quote->discount_label ? ' ' . $quote->discount_label : '' }}</td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#dc3545;">- Rp {{ number_format($quote->discount_amount, 0, '', '.') }}</td>
                                    </tr>
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;">After Discount</td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($afterDisc, 0, '', '.') }}</td>
                                    </tr>
                                @endif
                                <tr style="border-top:1px solid #eeeeff;">
                                    <td style="padding:6px 16px 6px 14px; color:#555;">Tax {{ $quote->tax ? '(11%)' : '' }}</td>
                                    <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">
                                        {{ $quote->tax ? 'Rp ' . number_format($quote->tax_amount, 0, '', '.') : '-' }}
                                    </td>
                                </tr>
                                @if ($quote->shipping > 0)
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;">Shipping Cost</td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($quote->shipping, 0, '', '.') }}</td>
                                    </tr>
                                @endif
                                <tr style="border-top:2px solid #d0d0ff; background:#f0f0ff;">
                                    <td style="padding:9px 16px 9px 14px; font-weight:700; font-size:13px; color:#3d3d8f;">TOTAL PRICE</td>
                                    <td style="padding:9px 14px 9px 0; text-align:right; font-weight:700; font-size:13px; color:#696cff;">Rp {{ number_format($quote->total, 0, '', '.') }}</td>
                                </tr>
                                @php
                                    $rawType   = $invoice->type ?: ($quote->payment_method ?? 'Payment');
                                    $typeLabel = match($rawType) {
                                        'Balance Payment', 'BP' => 'BP',
                                        'Down Payment', 'DP'    => 'DP',
                                        'Down Payment 2'        => 'DP 2',
                                        'Down Payment 3'        => 'DP 3',
                                        'Cash / Full Payment', 'CT' => 'CT',
                                        default => str_replace(['Balance Payment', 'Down Payment'], ['BP', 'DP'], $rawType)
                                    };
                                @endphp
                                @if ($quote->payment_method === 'DP & BP' || $invoice->percent < 100 || $invoice->type)
                                    @php
                                        $pct = floatval($invoice->percent ?? 100);
                                        $amt = round($quote->total * $pct / 100);
                                    @endphp
                                    <tr style="border-top:1.5px solid #696cff; background:#e8ebff;">
                                        <td style="padding:8px 16px 8px 14px; font-weight:700; font-size:12px; color:#3d3d8f;">
                                            TAGIHAN {{ strtoupper($typeLabel) }} ({{ $pct }}%)
                                        </td>
                                        <td style="padding:8px 14px 8px 0; text-align:right; font-weight:700; font-size:12px; color:#696cff;">Rp {{ number_format($amt, 0, '', '.') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Sidebar --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">

            {{-- Accept Form --}}
            <div class="card mb-3">
                <div class="card-header fw-semibold">Terbitkan Invoice</div>
                <div class="card-body">
                    @php
                        $invPercent = floatval($invoice->percent ?? 100);
                        $invAmount  = round($quote->total * $invPercent / 100);
                    @endphp

                    {{-- Invoice Amount Preview --}}
                    <div class="mb-3 p-2 rounded" style="background: #f0f4ff;">
                        <div class="small text-muted">
                            {{ $typeLabel }} ({{ $invPercent }}%)
                        </div>
                        <div class="fw-bold">Rp {{ number_format($invAmount, 0, ',', '.') }}</div>
                    </div>

                    <form action="{{ route('accept.unit', $invoice->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Tanggal Invoice</label>
                            <input type="date" name="invoice_date" class="form-control form-control-sm"
                                   value="{{ now()->toDateString() }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Term of Payment</label>
                            <input type="text" name="term" class="form-control form-control-sm"
                                   placeholder="misal: Cash, NET 30, NET 60..." required>
                        </div>

                        @foreach ($allInvoices as $inv)
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">
                                    No. Invoice
                                    @if ($quote->payment_method === 'DP & BP' || $allInvoices->count() > 1)
                                        <span class="badge {{ $inv->type === 'DP' ? 'bg-warning text-dark' : 'bg-info' }} ms-1">{{ $inv->type }}</span>
                                    @endif
                                </label>
                                <input type="text" name="no_invoice_{{ $inv->id }}"
                                       class="form-control form-control-sm"
                                       value="{{ $nextNumbers[$inv->id] ?? '' }}" required>
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary d-grid w-100 waves-effect mb-2">
                            Terbitkan Invoice
                        </button>
                    </form>
                    <button type="button" class="btn btn-outline-danger d-grid w-100 waves-effect mb-2"
                            data-bs-toggle="modal" data-bs-target="#rejectInvoiceModal">
                        Reject
                    </button>
                    <button class="btn btn-outline-secondary d-grid w-100 waves-effect" id="backButton">
                        Kembali
                    </button>
                </div>
            </div>

            {{-- Info PO --}}
            <div class="card mb-3">
                <div class="card-body">
                    <p class="mb-1 text-muted fw-semibold small">No. PO</p>
                    <p class="mb-3 fw-bold">{{ $quote->po_number }}</p>
                    <p class="mb-1 text-muted fw-semibold small">Payment Method</p>
                    <p class="mb-3 fw-bold">{{ $typeLabel }} {{ floatval($invoice->percent ?? 100) }}%</p>
                    @if ($quote->po_file)
                        <a href="{{ Storage::url($quote->po_file) }}" target="_blank"
                           class="btn btn-outline-primary d-grid w-100 waves-effect">
                            <i class="mdi mdi-file-pdf-box me-1"></i> Lihat File PO
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectInvoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('reject.unit', $invoice->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Pengajuan Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">Pengajuan invoice untuk quote <strong>{{ $quote->no_quote }}</strong>
                            akan di-reject dan tidak akan muncul lagi di halaman Request Invoice.</p>
                        <div class="mb-3">
                            <label class="form-label">Alasan Reject (opsional)</label>
                            <textarea name="reason" class="form-control" rows="3"
                                placeholder="Contoh: PO tidak sesuai, data belum lengkap, dll."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
@endpush

@push('script')
<script>
    $('#backButton').click(function () {
        window.history.back();
    });
</script>
@endpush
