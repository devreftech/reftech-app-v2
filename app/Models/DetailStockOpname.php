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
        'id_stock_opname',
        'id_product',
        'stock_sistem',
        'stock_gudang',
        'stock_bdg',
        'id_user_bdg',
        'stock_bks',
        'id_user_bks',
        'selisih',
        'note',
    ];
    
    public function opname()
    {
        return $this->belongsTo(StockOpname::class, 'id_stock_opname', 'id');
    }

    public function product()
    {
        return $this->belongsTo(DetailProduct::class, 'id_product', 'id');
    }

    public function userBdg()
    {
        return $this->belongsTo(User::class, 'id_user_bdg', 'id');
    }

    public function userBks()
    {
        return $this->belongsTo(User::class, 'id_user_bks', 'id');
    }
}
