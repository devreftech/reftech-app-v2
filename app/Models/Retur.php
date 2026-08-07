<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    use HasFactory;

    protected $table = "return";
    protected $fillable = [
        "id_pending",
        "id_product_in",
        "no_pending",
        "status"
    ];

    public function pending()
    {
        return $this->belongsTo('App\Models\PendingPO', 'id_pending', 'id');
    }
    public function productIn()
    {
        return $this->belongsTo('App\Models\ProductIn', 'id_product_in', 'id');
    }
    public function detail()
    {
        return $this->hasMany('App\Models\DetailReturn', 'id_retur');
    }
}
