<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailWarehouse extends Model
{
    protected $table = "detail_warehouse";
    protected $fillable = [
        'id_warehouse',
        'id_replacement',
        'qty',
        'warehouse1',
        'warehouse2',
    ];
    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'id_warehouse', 'id');
    }
    public function replacement()
    {
        return $this->belongsTo('App\Models\DetailProduct', 'id_replacement', 'id');
    }
}
