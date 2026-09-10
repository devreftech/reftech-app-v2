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
        'filtration'=>' µm','oil_content'=>' ppm',
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
    $afterDisc = $optTotals->diskon > 0
        ? $optTotals->subtotal - $optTotals->discount_amount
        : $optTotals->subtotal;

    // Subtotal per Head Title
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
    $itemNo = 1;
    $headerCount = 0;
@endphp

<table class="items-table">
    <thead>
        <tr>
            <th style="width: 4%; text-align: center;">No</th>
            <th style="width: {{ $hasDisc ? '46%' : '52%' }};">Item Description</th>
            <th style="width: 10%; text-align: center;">Qty</th>
            <th style="width: 17%; text-align: right;">Price (IDR)</th>
            @if ($hasDisc)
                <th style="width: 6%; text-align: center;">Disc</th>
            @endif
            <th style="width: 17%; text-align: right;">Amount (IDR)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($items as $item)
            @if ($item->type === 'header' || $item->type === 'heading')
                @php
                    $lbl = trim($item->label ?? '');
                    if (!preg_match('/^[A-Z0-9][\.\)]/i', $lbl)) {
                        $lbl = chr(65 + ($headerCount % 26)) . '. ' . $lbl;
                    }
                    $headerCount++;
                    $sectionSubtotal = $sectionSubtotals[$item->id] ?? 0;
                @endphp
                <tr class="table-section-header">
                    <td colspan="{{ $hasDisc ? 6 : 5 }}">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span><i class="mdi mdi-bookmark-outline me-1"></i>{{ $lbl }}</span>
                            @if ($sectionSubtotal > 0)
                                <span style="font-size: 10px; font-weight: 600; color: #475569; text-transform: none; letter-spacing: normal;">
                                    Subtotal: Rp {{ number_format($sectionSubtotal, 0, '', '.') }}
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
            @else
                <tr>
                    <td style="text-align: center; color: #64748b; font-weight: 500;">{{ $itemNo++ }}</td>
                    <td>
                        @if ($item->type === 'unit' && $item->unit)
                            <div class="item-title">{{ $item->label ?: ($item->unit->brand . ' ' . $item->unit->sku . ($item->unit->model ? ' — ' . $item->unit->model : '')) }}</div>
                            @php
                                $specs = $item->getSpecVisibleArray();
                                $category = $item->unit->unit ?? '';
                                $catOverride = $specLabelsOverride[$category] ?? [];
                            @endphp
                            @if (!empty($specs))
                                <div class="spec-grid">
                                    @foreach ($specs as $field)
                                        @if ($field === 'unit') @continue @endif
                                        @php
                                            $val = $item->unit->$field ?? null;
                                            $lbl = $catOverride[$field] ?? $specLabels[$field] ?? $field;
                                        @endphp
                                        @if ($val && isset($specLabels[$field]))
                                            <div>
                                                <span style="color:#64748b;">{{ $lbl }}:</span>
                                                <strong>{{ $val }}{{ $specUnits[$field] ?? '' }}</strong>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @elseif ($item->id_equivalent && $item->equivalent)
                            <div class="item-title">
                                {{ preg_replace('/^[\s\-\*\•]+/u', '', trim(($item->equivalent->brand ?? '') . ($item->equivalent->pn ? ' — ' . $item->equivalent->pn : '')) ?: $item->label) }}
                            </div>
                            @if (optional($item->equivalent->product)->description)
                                <div class="item-desc">{{ preg_replace('/^[\s\-\*\•]+/u', '', $item->equivalent->product->description) }}</div>
                            @endif
                        @else
                            <div class="item-title">{{ preg_replace('/^[\s\-\*\•]+/u', '', $item->label) }}</div>
                            @if ($item->description && $item->description !== $item->label)
                                @php
                                    $descLines = explode("\n", str_replace("\r", "", $item->description));
                                @endphp
                                <div class="item-desc">
                                    @foreach ($descLines as $dLine)
                                        @php $trimmedDLine = trim($dLine); @endphp
                                        @if (empty($trimmedDLine))
                                            <div style="height: 2px;"></div>
                                        @else
                                            @php $hasBullet = preg_match('/^([•\-\*]|\d+[\.\)])\s*(.*)/u', $trimmedDLine, $dMatches); @endphp
                                            @if ($hasBullet && !empty($dMatches[1]) && !empty($dMatches[2]))
                                                <div style="display:flex; align-items:flex-start; margin-bottom:1px;">
                                                    <span style="flex-shrink:0; min-width:14px; color:#64748b; font-weight:600;">{{ $dMatches[1] }}</span>
                                                    <span style="flex:1;">{{ $dMatches[2] }}</span>
                                                </div>
                                            @else
                                                <div style="margin-bottom:1px;">{{ $dLine }}</div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: 600;">
                        {{ (float)$item->qty == (int)$item->qty ? (int)$item->qty : $item->qty }} {{ $item->info_qty ?? 'Unit' }}
                    </td>
                    <td style="text-align: right;">
                        {{ number_format($item->price, 0, '', '.') }}
                    </td>
                    @if ($hasDisc)
                        <td style="text-align: center; color: #dc2626; font-weight: 500;">
                            {{ $item->disc > 0 ? (int)$item->disc . '%' : '-' }}
                        </td>
                    @endif
                    <td style="text-align: right; font-weight: 700;">
                        {{ number_format($item->amount, 0, '', '.') }}
                    </td>
                </tr>
            @endif
        @empty
            <tr><td colspan="{{ $hasDisc ? 6 : 5 }}" style="text-align: center; color: #94a3b8; padding: 16px;">Belum ada item.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- Totals Section (Right Aligned) --}}
<div class="totals-section">
    <table class="totals-table">
        <tr>
            <td class="label">Subtotal:</td>
            <td class="val">Rp {{ number_format($optTotals->subtotal, 0, '', '.') }}</td>
        </tr>
        @if ($optTotals->diskon > 0)
            <tr>
                <td class="label" style="color: #dc2626;">Discount {{ $optTotals->discount_label ? '(' . $optTotals->discount_label . ')' : '' }}:</td>
                <td class="val" style="color: #dc2626;">- Rp {{ number_format($optTotals->discount_amount, 0, '', '.') }}</td>
            </tr>
            <tr>
                <td class="label">After Discount:</td>
                <td class="val">Rp {{ number_format($afterDisc, 0, '', '.') }}</td>
            </tr>
        @endif
        @if ($optTotals->tax)
            <tr>
                <td class="label">VAT / PPN (11%):</td>
                <td class="val">Rp {{ number_format($optTotals->tax_amount, 0, '', '.') }}</td>
            </tr>
        @endif
        @if ($optTotals->shipping > 0)
            <tr>
                <td class="label">Shipping:</td>
                <td class="val">Rp {{ number_format($optTotals->shipping, 0, '', '.') }}</td>
            </tr>
        @endif
        <tr class="grand-total-row">
            <td class="label">{{ $optTotals->tax ? 'TOTAL (INC PPN):' : 'TOTAL (EXC PPN):' }}</td>
            <td class="val">Rp {{ number_format($optTotals->total, 0, '', '.') }}</td>
        </tr>
    </table>
</div>
