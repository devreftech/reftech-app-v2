{{-- Tabel item + summary finansial untuk 1 Opsi (dipakai berulang kalau quotation
     punya >1 opsi perbandingan harga, atau 1x aja kalau quotation biasa).
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
        'bar'=>' Bar','air_cap'=>' m³/min','test_pressure'=>' Bar',
        'inlet_pressure'=>' Bar','outlet_pressure'=>' Bar',
        'inlet_cap'=>' m³/min','outlet_cap'=>' m³/min',
        'weight'=>' Kg','capacity'=>' Liter',
    ];
    $hasDisc = $items->where('disc', '>', 0)->count() > 0;
@endphp
<div class="table-responsive rounded border mb-3">
    <table class="table table-bordered items-top-align-table m-0" style="width:100%; font-size:12px;">
        <thead style="font-size:11px; background:#f2f2f2; color:#444444;">
            <tr>
                <th class="text-center py-2" style="width:4%; font-weight:700; border-color:#dddddd;">No.</th>
                <th class="text-center py-2" style="width:{{ $hasDisc ? '44%' : '49%' }}; font-weight:700; border-color:#dddddd;">Item Description</th>
                <th class="text-center py-2" style="width:10%; font-weight:700; border-color:#dddddd;">Qty</th>
                <th class="text-center py-2" style="width:18%; font-weight:700; border-color:#dddddd;">Price (IDR)</th>
                @if ($hasDisc)
                    <th class="text-center py-2" style="width:7%; font-weight:700; border-color:#dddddd;">Disc</th>
                @endif
                <th class="text-center py-2" style="width:{{ $hasDisc ? '17%' : '19%' }}; font-weight:700; border-color:#dddddd;">Total (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $itemNo = 1;
                $headerCount = 0;

                // Subtotal per Head Title (sub-grouping DI DALAM 1 opsi) — semua item
                // di bawah 1 Head Title sampai Head Title berikutnya dijumlah.
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
                    <tr style="background:#f0f0ff;">
                        <td colspan="{{ $hasDisc ? '6' : '5' }}" class="fw-bold text-primary text-uppercase px-3" style="padding: 5px 10px; font-size:11.5px; border-top:1px solid #d0d0ff; border-bottom:1px solid #d0d0ff;">
                            <div class="d-flex align-items-center justify-content-between">
                                <span><i class="mdi mdi-bookmark-outline me-1"></i>{{ $lbl }}</span>
                                @if ($sectionSubtotal > 0)
                                    <span class="text-nowrap" style="font-size:11px;">Subtotal: Rp {{ number_format($sectionSubtotal, 0, ',', '.') }}</span>
                                @endif
                            </div>
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
                                         $subDesc = preg_replace('/^[\s\-\*\•]+/u', '', $item->label);
                                         if (empty($subDesc) || $subDesc === $brandPn) {
                                             $subDesc = optional($item->equivalent->product)->description ?? optional($item->equivalent->product)->name;
                                         }
                                         $prod = optional($item->equivalent)->product;
                                         $stkBdg = (int) ($prod->stock ?? 0);
                                         $stkBks = (int) ($prod->warehouse_stock ?? 0);
                                         $stkPend = (int) ($prod->pending_stock ?? 0);
                                         $totalStk = $stkBdg + $stkBks;
                                         $popoverContent = "<div class='text-start small p-1'><div><span class='badge bg-label-primary me-1'>BDG: $stkBdg</span> Stok Bandung</div><div class='mt-1'><span class='badge bg-label-info me-1'>BKS: $stkBks</span> Stok Bekasi</div><div class='mt-1'><span class='badge bg-label-warning me-1'>Pend: $stkPend</span> Pending PO</div></div>";
                                     @endphp
                                     <div class="d-inline-flex align-items-center flex-wrap gap-1">
                                         <p class="mb-0 fw-bold text-dark" style="font-size: 12px">{{ $brandPn ?: preg_replace('/^[\s\-\*\•]+/u', '', $item->label) }}</p>
                                         @if ($prod)
                                             <span class="badge bg-label-info ms-2 cursor-pointer stock-popover"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   data-bs-html="true"
                                                   title="<b>BDG:</b> {{ $stkBdg }} &nbsp;|&nbsp; <b>BKS:</b> {{ $stkBks }} &nbsp;|&nbsp; <b>Pend:</b> {{ $stkPend }}"
                                                   data-bs-trigger="hover focus"
                                                   style="font-size: 9.5px; font-weight: 600; padding: 2px 6px;">
                                                 <i class="mdi mdi-cube-outline me-1"></i>Stok: {{ $totalStk }}
                                             </span>
                                         @endif
                                     </div>
                                     @if ($subDesc && $subDesc !== $brandPn)
                                         <div style="font-size: 12px; color: #333333; font-weight: 500; margin-top: 2px; line-height: 1.4;">{{ preg_replace('/^[\s\-\*\•]+/u', '', $subDesc) }}</div>
                                     @endif
                                 @else
                                     <p class="mb-0 fw-bold text-dark" style="font-size: 12px">{{ preg_replace('/^[\s\-\*\•]+/u', '', $item->label) }}</p>
                                 @endif
                            @else
                                <p class="mb-0 fw-bold text-dark" style="font-size: 12px">{{ preg_replace('/^[\s\-\*\•]+/u', '', $item->label) }}</p>
                                @if ($item->description)
                                     @php
                                         $descLines = explode("\n", str_replace("\r", "", $item->description));
                                     @endphp
                                     <div class="text-muted" style="font-size:11px; margin-top:3px; line-height:1.4;">
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
                                                         <span style="flex-shrink:0; min-width:14px; color:#696cff; font-weight:600;">{{ $dMatches[1] }}</span>
                                                         <span style="flex:1;">{{ $dMatches[2] }}</span>
                                                     </div>
                                                 @else
                                                     <div style="margin-bottom:2px; font-weight:600; color:#222;">{{ $dLine }}</div>
                                                 @endif
                                             @endif
                                         @endforeach
                                     </div>
                                 @endif
                            @endif
                        </td>
                        <td class="text-center align-top py-2" style="font-size:12px !important;">{{ (int) $item->qty }} {{ $item->info_qty ?? 'Unit' }}</td>
                        <td class="text-end align-top py-2" style="font-size:12px !important;">{{ number_format($item->price, 0, '', '.') }}</td>
                        @if ($hasDisc)
                            <td class="text-center align-top py-2" style="font-size:12px !important;">{{ $item->disc > 0 ? (int) $item->disc . '%' : '-' }}</td>
                        @endif
                        <td class="text-end align-top py-2 fw-semibold" style="font-size:12px !important;">{{ number_format($item->amount, 0, '', '.') }}</td>
                    </tr>
                @endif
            @empty
                <tr><td colspan="{{ $hasDisc ? 6 : 5 }}" class="text-center text-muted py-4">Belum ada item.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Financial Summary (Right Aligned Box) --}}
@php
    $sumItemsAmount = $items->whereNotIn('type', ['header', 'heading'])->sum('amount');
    $optSubtotal = floatval($optTotals->subtotal ?? 0);
    if ($optSubtotal <= 0 && $sumItemsAmount > 0) {
        $optSubtotal = $sumItemsAmount;
    }

    $optDiskon = floatval($optTotals->diskon ?? 0);
    $optDiskonType = $optTotals->diskon_type ?? 'percent';
    $optDiscountAmount = 0;
    if ($optDiskon > 0) {
        $optDiscountAmount = ($optDiskonType === 'amount')
            ? $optDiskon
            : ($optSubtotal * $optDiskon / 100);
    } elseif (isset($optTotals->discount_amount) && $optTotals->discount_amount > 0) {
        $optDiscountAmount = (float) $optTotals->discount_amount;
    }

    $afterDisc = max(0, $optSubtotal - $optDiscountAmount);
    $optTax = (bool) ($optTotals->tax ?? false);
    $optTaxAmount = floatval($optTotals->tax_amount ?? 0);
    if ($optTax && $optTaxAmount <= 0) {
        $optTaxAmount = round($afterDisc * 0.11);
    }

    $optShipping = floatval($optTotals->shipping ?? 0);
    $optTotal = floatval($optTotals->total ?? 0);
    if ($optTotal <= 0) {
        $optTotal = $afterDisc + $optTaxAmount + $optShipping;
    }
@endphp
<div class="d-flex justify-content-end mb-3">
    <div style="min-width:270px; font-size:12px; border:1px solid #d0d0ff; border-left:4px solid #696cff; border-radius:6px; overflow:hidden; background:#fff;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="padding:6px 16px 6px 14px; color:#555;">Subtotal</td>
                <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($optSubtotal, 0, '', '.') }}</td>
            </tr>
            @if ($optDiscountAmount > 0)
                <tr style="border-top:1px solid #eeeeff;">
                    <td style="padding:6px 16px 6px 14px; color:#555;">Discount{{ !empty($optTotals->discount_label) ? ' ' . $optTotals->discount_label : '' }}</td>
                    <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#dc3545;">- Rp {{ number_format($optDiscountAmount, 0, '', '.') }}</td>
                </tr>
                <tr style="border-top:1px solid #eeeeff;">
                    <td style="padding:6px 16px 6px 14px; color:#555;">After Discount</td>
                    <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($afterDisc, 0, '', '.') }}</td>
                </tr>
            @endif
            <tr style="border-top:1px solid #eeeeff;">
                <td style="padding:6px 16px 6px 14px; color:#555;">Tax {{ $optTax ? '(11%)' : '' }}</td>
                <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">
                    {{ $optTax ? 'Rp ' . number_format($optTaxAmount, 0, '', '.') : '-' }}
                </td>
            </tr>
            @if ($optShipping > 0)
                <tr style="border-top:1px solid #eeeeff;">
                    <td style="padding:6px 16px 6px 14px; color:#555;">Shipping Cost</td>
                    <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($optShipping, 0, '', '.') }}</td>
                </tr>
            @endif
            <tr style="border-top:2px solid #d0d0ff; background:#f0f0ff;">
                <td style="padding:9px 16px 9px 14px; font-weight:700; font-size:13px !important; color:#3d3d8f;">TOTAL PRICE</td>
                <td style="padding:9px 14px 9px 0; text-align:right; font-weight:700; font-size:13px !important; color:#696cff;">Rp {{ number_format($optTotal, 0, '', '.') }}</td>
            </tr>
        </table>
    </div>
</div>
