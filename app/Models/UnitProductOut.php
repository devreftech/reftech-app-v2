<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitProductOut extends Model
{
    protected $table = 'unit_product_out';

    protected $fillable = [
        'no_transaksi',
        'date',
        'customer',
        'note',
        'created_by',
    ];

    public function detail()
    {
        return $this->hasMany(DetailUnitProductOut::class, 'id_unit_product_out', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
