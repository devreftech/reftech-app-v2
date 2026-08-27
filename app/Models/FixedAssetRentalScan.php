<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixedAssetRentalScan extends Model
{
    protected $table = 'fixed_asset_rental_scans';

    protected $fillable = [
        'id_fixed_asset',
        'action',
        'id_client',
        'id_pic_internal',
        'scanned_by',
        'note',
    ];

    public function fixedAsset()
    {
        return $this->belongsTo(FixedAsset::class, 'id_fixed_asset', 'id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id');
    }

    public function picInternal()
    {
        return $this->belongsTo(User::class, 'id_pic_internal', 'id');
    }

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by', 'id');
    }
}
