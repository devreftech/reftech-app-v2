<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suo extends Model
{
    use HasFactory;

    protected $table = 'suo';

    protected $fillable = [
        'no_suo',
        'company',
        'pic',
        'address',
        'notes',
        'id_sales',
        'status',
        'no_invoice_booking',
        'id_quotation',
        'id_unit_quotation',
        'confirmed_by',
        'confirmed_at',
        'approved_by',
        'approved_at',
    ];

    protected $dates = ['confirmed_at', 'approved_at'];

    public function confirmedBy()
    {
        return $this->belongsTo('App\Models\User', 'confirmed_by', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo('App\Models\User', 'approved_by', 'id');
    }

    public function sales()
    {
        return $this->belongsTo('App\Models\User', 'id_sales', 'id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\SuoDetail', 'id_suo');
    }

    public function quotation()
    {
        return $this->belongsTo('App\Models\Quotation', 'id_quotation', 'id');
    }

    public function unitQuotation()
    {
        return $this->belongsTo('App\Models\UnitQuotation', 'id_unit_quotation', 'id');
    }

    public function deliveries()
    {
        return $this->hasMany('App\Models\Delivery', 'id_suo');
    }
}
