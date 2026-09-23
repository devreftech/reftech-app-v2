<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailReturn extends Model
{
    protected $table = "detail_return";
    protected $fillable = [
        'id_retur',
        'id_replacement',
        'id_detail_quotation',
        'id_unit_quotation_detail',
        'item_name',
        'qty',
        'price',
        'amount',
        'note',
        'date',
        'status',
    ];

    public function return()
    {
        return $this->belongsTo('App\Models\Retur', 'id_retur', 'id');
    }
    public function replacement()
    {
        return $this->belongsTo('App\Models\DetailProduct', 'id_replacement', 'id');
    }
    public function detailQuotation()
    {
        return $this->belongsTo('App\Models\DetailQuotation', 'id_detail_quotation', 'id');
    }
    public function unitQuotationDetail()
    {
        return $this->belongsTo('App\Models\UnitQuotationDetail', 'id_unit_quotation_detail', 'id');
    }
}
