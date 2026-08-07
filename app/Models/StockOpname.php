<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;
    protected $table = "stock_opname";
    protected $date = [
        'created_at',
        'updated_at',
        'date',
    ];
    protected $fillable = [
        'note',
        'year',
    ];
    
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }
    public function detail()
    {
        return $this->hasMany('App\Models\DetailStockOpname', 'id_stock_opname');
    }
}
