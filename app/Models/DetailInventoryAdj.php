<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailInventoryAdj extends Model
{
    use HasFactory;
    protected $table = "detail_inventory_adj";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'qty',
        'warehouse',
        'amount',
    ];
    public function expense()
    {
        return $this->belongsTo('App\Model\DetailExpense', 'id_expense', 'id');
    }
}
