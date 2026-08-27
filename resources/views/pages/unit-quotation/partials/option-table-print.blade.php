{{-- Versi print dari tabel item + summary finansial untuk 1 Opsi (styling beda
     dikit dari partial detail — font lebih kecil, ada page-break-inside:avoid).
     Props: $items (koleksi UnitQuotationDetail), $optTotals (UnitQuotation atau
     UnitQuotationOption — punya subtotal/diskon/diskon_type/tax/tax_amount/shipping/total). --}}
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
        'bar'=>' Bar','air_cap'=>' m³/min',
        'filtration'=>' µm','oil_content'=>' ppm',
        'test_pressure'=>' Bar','inlet_pressure'=>' Bar','outlet_pressure'=>' Bar',
        'inlet_cap'=>' m³/min','outlet_cap'=>' m³/min',
        'weight'=>' Kg','capacity'=>' Liter',
    ];
    $specLabelsOverride = [
        'AIR RECEIVER TANK' => [
            'bar'     => 'Max. Pressure',
            'grade'   => 'T Plate',
            'cooling' => 'Certification',
        ],
        'FILTRATION SYSTEM' => [
            'air_cap'  => 'Flowrate',
            'material' => 'Element',
            'connect'  => 'Drain',
        ],
    ];
    $hasDisc = $items->where('disc', '>', 0)->count() > 0;
@endphp

<table class="table table-bordered items-top-align-table m-0 mb-3" style="width:100%; font-size:12px;">
    <thead style="font-size:11px; background:#f2f2f2; color:#444444;">
        <tr>
            <th class="text-center" style="width:4%; padding:10px 8px; font-weight:700; border-color:#dddddd;">No.</th>
            <th class="text-center" style="width:{{ $hasDisc ? '43%' : '48%' }}; padding:10px 10px; font-weight:700; border-color:#dddddd;">Item Description</th>
            <th class="text-center" style="width:9%; padding:10px 8px; font-weight:700; border-color:#dddddd;">Qty</th>
            <th class="text-center" style="width:18%; padding:10px 10px; font-weight:700; border-color:#dddddd;">Price (IDR)</th>
            @if ($hasDisc)
                <th class="text-center" style="width:6%; padding:10px 6px; font-weight:700; border-color:#dddddd;">Disc</th>
            @endif
            <th class="text-center" style="width:{{ $hasDisc ? '20%' : '21%' }}; padding:10px 10px; font-weight:700; border-color:#dddddd;">Total (IDR)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $itemNo = 1;
            $headerCount = 0;

            // Subtotal per Head Title (sub-grouping DI DALAM 1 opsi).
            $sectionSubtotals = [];
            $currentHeaderId = null;
            foreach ($items as $d) {
                if ($d->type === 'header' || $d->type === 'heading') {
                    $currentHeaderId = $d->id;
                    $sectionSubtotals[$currentHeaderId] = 0;
                } elseif ($currentHeaderId !== null) {
                    $sectionSubtotals[$currentHeaderId] += (float) ($d->amount ?? 0);
                }
            }
        @endphp
        @forelse ($items as $i => $item)
            @if ($item->type === 'header' || $item->type === 'heading')
                @php
                    $lbl = trim($item->label ?? '');
                    if (!preg_match('/^[A-Z0-9][\.\)]/i', $lbl)) {
                        $lbl = chr(65 + ($headerCount % 26)) . '. ' . $lbl;
                    }
                    $headerCount++;
                    $sectionSubtotal = $sectionSubtotals[$item->id] ?? 0;
                @endphp
                <tr class="table-section-header" style="background:#f4f4fe;">
                    <td colspan="{{ $hasDisc ? 6 : 5 }}" style="padding:7px 12px; font-weight:700; font-size:11px; color:#3d3d8f; border-color:#d0d0ff; text-transform:uppercase; letter-spacing:.5px;">
                        <div style="display:flex; align-items:center; justify-content:space-between;">
                            <span><i class="mdi mdi-bookmark-outline me-1"></i>{{ $lbl }}</span>
                            @if ($sectionSubtotal > 0)
                                <span style="font-size:10px; text-transform:none; letter-spacing:normal;">Subtotal: Rp {{ number_format($sectionSubtotal, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @else
                <tr style="background:#fff; color:#111;">
                    <td class="align-top text-center" style="padding:8px 8px; color:#111; font-weight:600;">{{ $itemNo++ }}</td>
                    <td class="align-top" style="padding:8px 10px;">
                        @if ($item->type === 'unit' && $item->unit)
                            <p class="mb-1 fw-bold" style="font-size:12px; color:#111;">
                                {{ $item->label ?: ($item->unit->brand . ' ' . $item->unit->sku . ($item->unit->model ? ' — ' . $item->unit->model : '')) }}
                            </p>
                            @php
                                $specs = $item->getSpecVisibleArray();
                                $category = $item->unit->unit ?? '';
                                $catOverride = $specLabelsOverride[$category] ?? [];
                            @endphp
                            @if (!empty($specs))
                                <div style="font-size:10px; color:#222; margin-top:3px;">
                                    @foreach ($specs as $field)
                                        @if ($field === 'unit') @continue @endif
                                        @php
                                            $val = $item->unit->$field ?? null;
                                            $label = $catOverride[$field] ?? $specLabels[$field] ?? $field;
                                        @endphp
                                        @if ($val && isset($specLabels[$field]))
                                            <div style="display:flex; padding:1px 0;">
                                                <span style="color:#444; font-weight:600; min-width:110px; flex-shrink:0;">{{ $label }}</span>
                                                <span style="color:#111; font-weight:500;">: {{ $val }}{{ $specUnits[$field] ?? '' }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @elseif ($item->id_equivalent && $item->equivalent)
                            <p class="mb-1 fw-bold" style="font-size:12px; color:#111;">
                                {{ preg_replace('/^[\s\-\*\•]+/u', '', trim(($item->equivalent->brand ?? '') . ($item->equivalent->pn ? ' — ' . $item->equivalent->pn : '')) ?: $item->label) }}
                            </p>
                            @if (optional($item->equivalent->product)->description)
                                <p class="mb-0" style="font-size:10px; color:#333;">{{ preg_replace('/^[\s\-\*\•]+/u', '', $item->equivalent->product->description) }}</p>
                            @endif
                        @else
                            <p class="mb-0 fw-bold" style="font-size:12px; color:#111;">{{ preg_replace('/^[\s\-\*\•]+/u', '', $item->label) }}</p>
                            @if ($item->description)
                                @php
                                    $descLines = explode("\n", str_replace("\r", "", $item->description));
                                @endphp
                                <div style="font-size:10px; color:#333; margin-top:3px; line-height:1.4;">
                                    @foreach ($descLines as $dLine)
                                        @php
                                            $trimmedDLine = trim($dLine);
                                        @endphp
                                        @if (empty($trimmedDLine))
                                            <div style="height:2px;"></div>
                                        @else
                                            @php
                                                $hasBullet = preg_match('/^([•\-\*]|\d+[\.\)])\s*(.*)/u', $trimmedDLine, $dMatches);
                                            @endphp
                                            @if ($hasBullet && !empty($dMatches[1]) && !empty($dMatches[2]))
                                                <div style="display:flex; align-items:flex-start; margin-bottom:2px;">
                                                    <span style="flex-shrink:0; min-width:14px; color:#555; font-weight:600;">{{ $dMatches[1] }}</span>
                                                    <span style="flex:1;">{{ $dMatches[2] }}</span>
                                                </div>
                                            @else
                                                <div style="margin-bottom:2px; font-weight:600; color:#111;">{{ $dLine }}</div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </td>
                    <td class="align-top text-center" style="padding:8px 8px; color:#222; font-weight:500; font-size:12px !important;">{{ (int) $item->qty }} {{ $item->info_qty ?? 'Unit' }}</td>
                    <td class="align-top text-end" style="padding:8px 10px; color:#111; font-weight:500; font-size:12px !important;">{{ number_format($item->price, 0, '', '.') }}</td>
                    @if ($hasDisc)
                        <td class="align-top text-center" style="padding:8px 6px; color:#111; font-size:12px !important;">{{ $item->disc > 0 ? (int) $item->disc . '%' : '-' }}</td>
                    @endif
                    <td class="align-top text-end" style="padding:8px 10px; font-weight:700; color:#111; font-size:12px !important;">{{ number_format($item->amount, 0, '', '.') }}</td>
                </tr>
            @endif
        @empty
            <tr><td colspan="{{ $hasDisc ? 6 : 5 }}" class="text-center text-muted py-4">Belum ada item.</td></tr>
        @endforelse
    </tbody>
</table>

@php
    $afterDisc = $optTotals->diskon > 0
        ? $optTotals->subtotal - $optTotals->discount_amount
        : $optTotals->subtotal;
@endphp
<div class="d-flex justify-content-end mb-3" style="page-break-inside: avoid !important; break-inside: avoid !important;">
    <div style="min-width:270px; font-size:12px; border:1px solid #d0d0ff; border-left:4px solid #696cff; border-radius:6px; overflow:hidden; background:#fff;">
            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                <tr>
                    <td style="padding:5px 14px 5px 12px; color:#555;">Subtotal</td>
                    <td style="padding:5px 12px 5px 0; text-align:right; font-weight:600; color:#222;">Rp {{ number_format($optTotals->subtotal, 0, '', '.') }}</td>
                </tr>
                @if ($optTotals->diskon > 0)
                    <tr style="border-top:1px solid #eeeeff;">
                        <td style="padding:5px 14px 5px 12px; color:#555;">Discount{{ $optTotals->discount_label ? ' ' . $optTotals->discount_label : '' }}</td>
                        <td style="padding:5px 12px 5px 0; text-align:right; font-weight:600; color:#dc3545;">- Rp {{ number_format($optTotals->discount_amount, 0, '', '.') }}</td>
                    </tr>
                    <tr style="border-top:1px solid #eeeeff;">
                        <td style="padding:5px 14px 5px 12px; color:#555;">After Discount</td>
                        <td style="padding:5px 12px 5px 0; text-align:right; font-weight:600; color:#222;">Rp {{ number_format($afterDisc, 0, '', '.') }}</td>
                    </tr>
                @endif
                <tr style="border-top:1px solid #eeeeff;">
                    <td style="padding:5px 14px 5px 12px; color:#555;">Tax {{ $optTotals->tax ? '(12%)' : '' }}</td>
                    <td style="padding:5px 12px 5px 0; text-align:right; font-weight:600; color:#222;">
                        {{ $optTotals->tax ? 'Rp ' . number_format($optTotals->tax_amount, 0, '', '.') : '-' }}
                    </td>
                </tr>
                @if ($optTotals->shipping > 0)
                    <tr style="border-top:1px solid #eeeeff;">
                        <td style="padding:5px 14px 5px 12px; color:#555;">Shipping Cost</td>
                        <td style="padding:5px 12px 5px 0; text-align:right; font-weight:600; color:#222;">Rp {{ number_format($optTotals->shipping, 0, '', '.') }}</td>
                    </tr>
                @endif
                <tr style="border-top:2px solid #d0d0ff; background:#f0f0ff;">
                    <td style="padding:7px 14px 7px 12px; font-weight:700; font-size:13px !important; color:#3d3d8f;">TOTAL PRICE</td>
                    <td style="padding:7px 12px 7px 0; text-align:right; font-weight:700; font-size:13px !important; color:#696cff;">Rp {{ number_format($optTotals->total, 0, '', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>
