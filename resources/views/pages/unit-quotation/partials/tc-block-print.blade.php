{{--
    Blok Note + Ketentuan Rental + Term & Condition — dipakai di print.blade.php,
    dipisah jadi partial biar bisa dirender sekali (global, quotation 1 opsi) atau
    per-opsi (quotation >1 opsi, tiap opsi punya T&C sendiri).
    Vars: $tcNote, $tcRentalTerms, $tcValidity, $tcPricing, $tcDeliveryProcess,
    $tcPayment, $tcWarranty
--}}
{{-- Note (full-width, di bawah financial summary) --}}
@if ($tcNote)
<div style="border:1px solid #e0e0e0; border-left:3px solid #696cff; border-radius:6px; padding:10px 14px; font-size:11px; color:#333; margin-bottom:14px; background:#fafafa; page-break-inside: avoid !important; break-inside: avoid !important;">
    <p class="mb-1 fw-semibold" style="font-size:10px; color:#888; text-transform:uppercase; letter-spacing:.5px;">Remarks</p>
    @php
        $noteLines = explode("\n", str_replace("\r", "", $tcNote));
    @endphp
    <div style="font-size:11px; color:#222; line-height:1.5;">
        @foreach ($noteLines as $line)
            @php
                $trimmed = trim($line);
            @endphp
            @if (empty($trimmed))
                <div style="height:3px;"></div>
            @else
                @php
                    $hasBullet = preg_match('/^([•\-\*]|\d+[\.\)])\s*(.*)/u', $trimmed, $matches);
                @endphp
                @if ($hasBullet && !empty($matches[1]) && !empty($matches[2]))
                    <div style="display:flex; align-items:flex-start; margin-bottom:3px;">
                        <span style="flex-shrink:0; min-width:20px; color:#696cff; font-weight:600;">{{ $matches[1] }}</span>
                        <span style="flex:1;">{{ $matches[2] }}</span>
                    </div>
                @else
                    <div style="margin-bottom:3px;">{{ $line }}</div>
                @endif
            @endif
        @endforeach
    </div>
</div>
@endif

{{-- Ketentuan Rental Unit Kompresor (Khusus Tipe Rental jika ada isinya) --}}
@if (!empty($tcRentalTerms))
<div style="border:1px solid #ffe0b2; border-left:3px solid #ff9800; border-radius:6px; padding:10px 14px; font-size:11px; color:#333; margin-bottom:14px; background:#fffdf8; page-break-inside: avoid !important; break-inside: avoid !important;">
    <p class="mb-1 fw-semibold" style="font-size:10px; color:#e65100; text-transform:uppercase; letter-spacing:.5px;">Ketentuan Rental Unit Kompresor</p>
    @php
        $rentalLines = explode("\n", str_replace("\r", "", $tcRentalTerms));
    @endphp
    <div style="font-size:11px; color:#222; line-height:1.5;">
        @foreach ($rentalLines as $line)
            @php
                $trimmed = trim($line);
            @endphp
            @if (empty($trimmed))
                <div style="height:3px;"></div>
            @else
                @php
                    $hasBullet = preg_match('/^([•\-\*]|\d+[\.\)])\s*(.*)/u', $trimmed, $matches);
                @endphp
                @if ($hasBullet && !empty($matches[1]) && !empty($matches[2]))
                    <div style="display:flex; align-items:flex-start; margin-bottom:3px;">
                        <span style="flex-shrink:0; min-width:20px; color:#ff9800; font-weight:600;">{{ $matches[1] }}</span>
                        <span style="flex:1;">{{ $matches[2] }}</span>
                    </div>
                @else
                    <div style="margin-bottom:3px;">{{ $line }}</div>
                @endif
            @endif
        @endforeach
    </div>
</div>
@endif

{{-- T&C --}}
<div style="border:1px solid #e0e0e0; border-radius:6px; padding:12px 16px; font-size:11px; background:#fff; margin-bottom:16px; page-break-inside: avoid !important; break-inside: avoid !important;">
    <p class="mb-2 fw-semibold" style="font-size:10px; text-transform:uppercase; letter-spacing:.5px; color:#888;">Term &amp; Condition</p>
    <table style="width:100%; border-collapse:collapse; font-size:11px;">
        <tr>
            <td style="width:150px; padding:3px 0; color:#555; vertical-align:top;">Validity of Quotation</td>
            <td style="padding:3px 0; color:#222; vertical-align:top;">: {{ $tcValidity ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px 0; color:#555; vertical-align:top;">Price</td>
            <td style="padding:3px 0; color:#222; vertical-align:top;">: {{ $tcPricing ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px 0; color:#555; vertical-align:top;">Payment</td>
            <td style="padding:3px 0; color:#222; vertical-align:top;">: {{ $tcPayment ?? '-' }}</td>
        </tr>
        @if ($tcWarranty)
            <tr>
                <td style="padding:3px 0; color:#555; vertical-align:top;">Warranty</td>
                <td style="padding:3px 0; color:#222; vertical-align:top;">: {{ $tcWarranty }}</td>
            </tr>
        @endif
        @php
            $deliveryLines = array_filter(preg_split('/\r?\n/', $tcDeliveryProcess ?? '-'), fn($l) => trim($l) !== '');
            $deliveryText = count($deliveryLines) > 1
                ? implode("\n", array_map(fn($l) => '• ' . trim($l), $deliveryLines))
                : ($tcDeliveryProcess ?? '-');
        @endphp
        <tr>
            <td style="padding:3px 0; color:#555; vertical-align:top;">Delivery Process</td>
            <td style="padding:3px 0; color:#222; vertical-align:top;">
                <div style="display:flex; align-items:flex-start;">
                    <span style="flex-shrink:0;">:&nbsp;</span>
                    <span style="white-space:pre-line;">{{ $deliveryText }}</span>
                </div>
            </td>
        </tr>
    </table>
</div>
