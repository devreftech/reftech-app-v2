<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitQuotation extends Model
{
    protected $table = 'unit_quotation';

    protected $fillable = [
        'root_id',
        'revision_number',
        'is_latest',
        'id_client',
        'id_pic',
        'id_plant',
        'address',
        'id_sales',
        'id_support',
        'no_quote',
        'attn',
        'no_pr',
        'date',
        'expired_date',
        'title',
        'hide_title',
        'type',
        'unit_condition',
        'week',
        'subtotal',
        'diskon',
        'diskon_type',
        'tax',
        'tax_amount',
        'shipping',
        'total',
        'fee',
        'fee_note',
        'fee_bank_name',
        'fee_bank_account',
        'fee_bank_holder',
        'fee_bank_branch',
        'fee_payment_status',
        'fee_transfer_date',
        'fee_transfer_proof',
        'fee_transfer_note',
        'fee_paid_by',
        'id_source_bank',
        'note',
        'validity',
        'pricing',
        'warranty',
        'delivery_process',
        'payment',
        'status',
        'cancel_request',
        'po_number',
        'po_file',
        'po_received',
        'payment_method',
    ];

    protected $casts = [
        'date'              => 'date',
        'expired_date'      => 'date',
        'fee_transfer_date' => 'datetime',
        'tax'               => 'boolean',
        'hide_title'        => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    /**
     * Entitas penerbit dokumen mengikuti client-nya: 'Kojisha' kalau client.info = 'Kojisha',
     * selain itu Reftech. Dipakai untuk routing kontrak (Selling Contract vs Confirm Order)
     * & branding dokumen.
     */
    public function isKojisha(): bool
    {
        return optional($this->client)->info === 'Kojisha';
    }

    public function pic()
    {
        return $this->belongsTo(Pic::class, 'id_pic');
    }

    public function plant()
    {
        return $this->belongsTo(ClientPlant::class, 'id_plant');
    }

    public function sales()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_sales');
    }

    public function feePaidBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'fee_paid_by');
    }

    public function sourceBank()
    {
        return $this->belongsTo(Bank::class, 'id_source_bank');
    }

    public function details()
    {
        return $this->hasMany(UnitQuotationDetail::class, 'id_unit_quotation')->orderBy('sort_order');
    }

    public function options()
    {
        return $this->hasMany(UnitQuotationOption::class, 'id_unit_quotation')->orderBy('sort_order');
    }

    /** True kalau quotation ini punya lebih dari 1 opsi perbandingan harga yang belum diputuskan. */
    public function getHasMultipleOptionsAttribute(): bool
    {
        return $this->options()->count() > 1;
    }

    public function statusHistory()
    {
        return $this->hasMany(UnitQuotationStatusHistory::class, 'id_unit_quotation')->orderBy('created_at', 'desc');
    }

    public function comments()
    {
        return $this->hasMany(UnitQuotationComment::class, 'id_unit_quotation')->orderBy('created_at', 'desc');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'id_unit_quotation');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'id_unit_quotation');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'id_unit_quotation');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'id_unit_quotation');
    }

    public function suo()
    {
        return $this->hasOne(Suo::class, 'id_unit_quotation');
    }

    public function getHargaTotalAttribute()
    {
        return $this->total;
    }

    /** Nominal discount in Rupiah, regardless of whether it was entered as % or Rp */
    public function getDiscountAmountAttribute()
    {
        if (($this->diskon_type ?? 'percent') === 'amount') {
            return (float) $this->diskon;
        }
        return round($this->subtotal * $this->diskon / 100);
    }

    /**
     * Discount rate descriptor, e.g. "10%" for a percent discount.
     * Empty for a flat Rupiah discount — the amount is already shown
     * next to it, so repeating it here would just be redundant.
     */
    public function getDiscountLabelAttribute()
    {
        if (($this->diskon_type ?? 'percent') === 'amount') {
            return '';
        }
        return $this->diskon . '%';
    }

    /**
     * Kebijakan Pajak Fee 2026:
     * - < 1.500.000: 0% (tidak ada potongan pajak)
     * - 1.500.000 - 5.000.000: potongan 3.68%
     * - > 5.000.000: potongan 10%
     */
    public function getFeeTaxDataAttribute()
    {
        $fee = floatval($this->fee ?? 0);
        if ($fee < 1500000) {
            $taxRate = 0;
            $taxRateLabel = '0%';
        } elseif ($fee <= 5000000) {
            $taxRate = 0.0368;
            $taxRateLabel = '3.68%';
        } else {
            $taxRate = 0.10;
            $taxRateLabel = '10%';
        }
        $taxAmount = round($fee * $taxRate);
        $netFee = $fee - $taxAmount;

        return (object) [
            'gross_fee'      => $fee,
            'tax_rate'       => $taxRate,
            'tax_rate_label' => $taxRateLabel,
            'tax_amount'     => $taxAmount,
            'net_fee'        => $netFee,
        ];
    }

    public function getFeeCalculationAttribute()
    {
        return $this->fee_tax_data;
    }

    /** All revisions in the same group (including original) */
    public function allVersions()
    {
        $rootId = $this->root_id ?? $this->id;
        return UnitQuotation::where(function ($q) use ($rootId) {
            $q->where('id', $rootId)->orWhere('root_id', $rootId);
        })->orderBy('revision_number')->get(['id', 'no_quote', 'revision_number']);
    }

    /**
     * Ringkasan Status Pembayaran Quotation dari Customer ke Perusahaan (Lunas, DP, Credit/Tempo, Unpaid)
     */
    public function getCustomerPaymentSummaryAttribute()
    {
        $totalQuote = floatval($this->total ?? 0);
        $payments = $this->payments ?? collect();
        $invoices = $this->invoices ?? collect();

        $confirmedPayments = $payments->where('level', 1);
        $totalPaidConfirmed = $confirmedPayments->sum('amount');
        
        $unconfirmedPayments = $payments->where('level', 0);
        $totalPaidUnconfirmed = $unconfirmedPayments->sum('amount');

        $hasEscrow = $payments->contains('method', 'Escrow') || $invoices->contains('type', 'Escrow');
        $hasTempo = $payments->contains(function($p) {
            return in_array(strtolower($p->type ?? ''), ['tempo', 'credit', 'kredit', 'top', 'ct']);
        }) || $invoices->contains(function($i) {
            return in_array(strtolower($i->type ?? ''), ['tempo', 'credit', 'kredit', 'top', 'ct']);
        }) || in_array(strtolower($this->payment_method ?? ''), ['tempo', 'credit', 'kredit', 'top', 'ct']);

        $hasPaidInvoice = $invoices->where('status_p', 1)->isNotEmpty();
        $hasUnpaidInvoice = $invoices->where(function($i) {
            return empty($i->status_p) && !empty($i->no_invoice);
        })->isNotEmpty();

        $percentPaid = $totalQuote > 0 ? min(100, round(($totalPaidConfirmed / $totalQuote) * 100)) : 0;
        $remaining = max(0, $totalQuote - $totalPaidConfirmed);

        // 1. Escrow
        if ($hasEscrow) {
            return (object) [
                'status'       => 'escrow',
                'label'        => 'Escrow / Marketplace',
                'badge_class'  => 'bg-label-info',
                'icon'         => 'mdi-store-outline',
                'is_lunas'     => true,
                'total_paid'   => $totalPaidConfirmed ?: $totalQuote,
                'remaining'    => 0,
                'percent'      => 100,
                'detail_text'  => 'Pembayaran via Rekening Bersama / Escrow',
            ];
        }

        // 2. Lunas / Full Payment
        if (($totalPaidConfirmed >= ($totalQuote - 1000) && $totalQuote > 0) || ($hasPaidInvoice && !$hasUnpaidInvoice && $totalPaidConfirmed > 0)) {
            return (object) [
                'status'       => 'paid',
                'label'        => 'Full Payment (Lunas)',
                'badge_class'  => 'bg-label-success',
                'icon'         => 'mdi-check-decagram-outline',
                'is_lunas'     => true,
                'total_paid'   => $totalPaidConfirmed ?: $totalQuote,
                'remaining'    => 0,
                'percent'      => 100,
                'detail_text'  => 'Lunas 100% (Rp ' . number_format($totalPaidConfirmed ?: $totalQuote, 0, ',', '.') . ')',
            ];
        }

        // 3. DP / Terbayar Sebagian
        if ($totalPaidConfirmed > 0 && $remaining > 0) {
            if ($hasTempo) {
                return (object) [
                    'status'       => 'partial_tempo',
                    'label'        => 'Credit / Tempo (' . $percentPaid . '%)',
                    'badge_class'  => 'bg-label-warning',
                    'icon'         => 'mdi-calendar-clock-outline',
                    'is_lunas'     => false,
                    'total_paid'   => $totalPaidConfirmed,
                    'remaining'    => $remaining,
                    'percent'      => $percentPaid,
                    'detail_text'  => 'Terbayar Rp ' . number_format($totalPaidConfirmed, 0, ',', '.') . ' • Sisa Rp ' . number_format($remaining, 0, ',', '.'),
                ];
            }

            return (object) [
                'status'       => 'dp',
                'label'        => 'DP (' . $percentPaid . '%)',
                'badge_class'  => 'bg-label-warning',
                'icon'         => 'mdi-cash-clock',
                'is_lunas'     => false,
                'total_paid'   => $totalPaidConfirmed,
                'remaining'    => $remaining,
                'percent'      => $percentPaid,
                'detail_text'  => 'DP Rp ' . number_format($totalPaidConfirmed, 0, ',', '.') . ' • Sisa Rp ' . number_format($remaining, 0, ',', '.'),
            ];
        }

        // 4. Tempo / Credit Murni (Belum ada pembayaran masuk)
        if ($hasTempo) {
            return (object) [
                'status'       => 'tempo',
                'label'        => 'Credit / Tempo',
                'badge_class'  => 'bg-label-secondary',
                'icon'         => 'mdi-calendar-clock-outline',
                'is_lunas'     => false,
                'total_paid'   => 0,
                'remaining'    => $totalQuote,
                'percent'      => 0,
                'detail_text'  => 'Termin Kredit (Belum ada pembayaran masuk)',
            ];
        }

        // 5. Menunggu Konfirmasi Accounting
        if ($totalPaidUnconfirmed > 0) {
            return (object) [
                'status'       => 'pending_confirm',
                'label'        => 'Menunggu Konfirmasi',
                'badge_class'  => 'bg-label-primary',
                'icon'         => 'mdi-clock-outline',
                'is_lunas'     => false,
                'total_paid'   => $totalPaidUnconfirmed,
                'remaining'    => $totalQuote,
                'percent'      => 0,
                'detail_text'  => 'Bukti bayar customer menunggu verifikasi Accounting',
            ];
        }

        // 6. Belum Dibayar (Unpaid)
        return (object) [
            'status'       => 'unpaid',
            'label'        => 'Belum Dibayar',
            'badge_class'  => 'bg-label-danger',
            'icon'         => 'mdi-alert-circle-outline',
            'is_lunas'     => false,
            'total_paid'   => 0,
            'remaining'    => $totalQuote,
            'percent'      => 0,
            'detail_text'  => 'Customer belum melakukan pembayaran tagihan',
        ];
    }
}
