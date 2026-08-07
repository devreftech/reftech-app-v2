<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailStockOpname extends Model
{
    use HasFactory;
    protected $table = "detail_stock_opname";
    protected $date = [
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'stock_sistem',
        'stock_gudang',
        'selisih',
        'note',
    ];
    
    public function opname()
    {
        return $this->belongsTo('App/Models/StockOpname', 'id', 'id_stock_opname');
    }
        public function product(){
            return $this->belongsTo('App\Models\DetailProduct', 'id_product', 'id');
        }
}
