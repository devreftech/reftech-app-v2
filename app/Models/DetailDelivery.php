<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailDelivery extends Model
{
    use HasFactory;
    protected $table = "detail_delivery";
    protected $date = [
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'id_delivery',
        'id_unit_quotation_detail',
        'id_pn',
        'type',
        'qty',
        'desc',
        'info_qty',
        'view',
    ];

    public function delivery()
    {
        return $this->belongsTo('App\Models\Delivery', 'id_delivery', 'id');
    }
    public function pn()
    {
        return $this->belongsTo('App\Models\SerialProduct', 'id_pn', 'id');
    }
    public function unitQuotationDetail()
    {
        return $this->belongsTo(UnitQuotationDetail::class, 'id_unit_quotation_detail');
    }
}
