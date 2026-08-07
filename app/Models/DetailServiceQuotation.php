<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailServiceQuotation extends Model
{
    use HasFactory;
    protected $table = "detail_service_quotation";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_subtitle',
        'product',
        'detail',
        'price',
        'qty',
        'disc',
        'info_qty',
        'amount'
    ];

    public function subtitle()
    {
        return $this->belongsTo('App\Models\SubtitleQuotation', 'id_subtitle', 'id');
    }
    public function pending()
    {
        return $this->hasMany('App\Models\DetailPendingPO', 'id_detail_service');
    }
}
