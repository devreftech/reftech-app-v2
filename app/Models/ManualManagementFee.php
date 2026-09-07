<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualManagementFee extends Model
{
    use HasFactory;

    protected $table = 'manual_management_fees';

    protected $fillable = [
        'client_id',
        'custom_company_name',
        'date',
        'title',
        'reference_no',
        'gross_fee',
        'fee_bank_name',
        'fee_bank_branch',
        'fee_bank_account',
        'fee_bank_holder',
        'fee_payment_status',
        'fee_transfer_date',
        'fee_transfer_proof',
        'fee_transfer_note',
        'fee_paid_by',
        'id_source_bank',
        'created_by',
    ];

    protected $casts = [
        'date'              => 'date',
        'fee_transfer_date' => 'datetime',
        'gross_fee'         => 'float',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function feePaidBy()
    {
        return $this->belongsTo(User::class, 'fee_paid_by');
    }

    public function sourceBank()
    {
        return $this->belongsTo(Bank::class, 'id_source_bank');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Nama company yang ditampilkan (mengambil dari Client atau fallback custom)
     */
    public function getCompanyNameAttribute()
    {
        if ($this->client) {
            return $this->client->company ?: ($this->client->name ?? 'Customer');
        }
        return $this->custom_company_name ?: '-';
    }

    /**
     * Kebijakan Pajak Fee 2026:
     * - < 1.5 Juta        : Pajak 0% (Bebas Pajak)
     * - 1.5 Juta - 5 Juta : Pajak 3.68%
     * - > 5 Juta          : Pajak 10%
     */
    public function getFeeTaxDataAttribute()
    {
        $fee = floatval($this->gross_fee ?? 0);
        if ($fee < 1500000) {
            $taxRate = 0.00;
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
}
