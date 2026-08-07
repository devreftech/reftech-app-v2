<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOut extends Model
{
    use HasFactory;
    protected $table = "product_out";
    protected $date = [
        'created_at',
        'updated_at',
        'date'
    ];
    protected $fillable = [
        'id_user',
        'detail_client',
        'invoice',
        'po',
        'no_type',
        'note',
        'vers',
        'total',
    ];
    public function detail()
    {
        return $this->hasMany('App\Models\DetailProductOut', 'id_product_out');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }
    public function pending()
    {
        return $this->hasMany('App\Models\PendingPO', 'id_product_out');
    }
}
