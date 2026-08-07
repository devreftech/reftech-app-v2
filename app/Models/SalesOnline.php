<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOnline extends Model
{
    use HasFactory;
    protected $table = "sales_online";
    protected $date = [
        'created_at',
        'updated_at',
        'date'
    ];
    protected $fillable = [
        'airend', 'kojisha', 'average', 'product', 'ig', 'tiktok', 'tokped', 'type'
    ];

    public function machine()
    {
        return $this->belongsTo('App\Models\User', 'id_sales', 'id');
    }
}
