<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketplace extends Model
{
    use HasFactory;

    protected $table = 'marketplaces';

    protected $fillable = [
        'name',
        'entity',
        'id_default_bank',
        'fee_percent',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fee_percent' => 'float',
    ];

    public function defaultBank()
    {
        return $this->belongsTo(Bank::class, 'id_default_bank');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'id_marketplace');
    }

    public function settlements()
    {
        return $this->hasMany(MarketplaceSettlement::class, 'id_marketplace');
    }

    public function heldPayments()
    {
        return $this->payments()->where('disbursement_status', 'held');
    }
}
