<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceSettlementItem extends Model
{
    use HasFactory;

    protected $table = 'marketplace_settlement_items';

    protected $fillable = [
        'id_marketplace_settlement',
        'id_payment',
        'amount',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function settlement()
    {
        return $this->belongsTo(MarketplaceSettlement::class, 'id_marketplace_settlement');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'id_payment');
    }
}
