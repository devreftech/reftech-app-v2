<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailQuotation extends Model
{
    use HasFactory;
    protected $table = "detail_quotation";
    protected $fillable = [
        'id_quotation',
        'id_equivalent',
        // 'product',
        'detail_product',
        'qty',
        'disc',
        'price',
        'amount',
        'fee',
        'pph',
        'view',
    ];

    
    public function quotation()
    {
        return $this->belongsTo('App\Models\Quotation', 'id_quotation', 'id');
    }
    public function equivalent()
    {
        return $this->belongsTo('App\Models\SerialProduct', 'id_equivalent', 'id');
    }
}
