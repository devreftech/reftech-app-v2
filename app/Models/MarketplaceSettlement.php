<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceSettlement extends Model
{
    use HasFactory;

    protected $table = 'marketplace_settlements';

    protected $fillable = [
        'settlement_number',
        'id_marketplace',
        'id_bank',
        'settlement_date',
        'reference_no',
        'gross_amount',
        'fee_amount',
        'net_amount',
        'proof_file',
        'status',
        'note',
        'created_by',
    ];

    protected $casts = [
        'settlement_date' => 'date',
        'gross_amount' => 'float',
        'fee_amount' => 'float',
        'net_amount' => 'float',
    ];

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class, 'id_marketplace');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'id_bank');
    }

    public function items()
    {
        return $this->hasMany(MarketplaceSettlementItem::class, 'id_marketplace_settlement');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
