<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;
    protected $table = "contract";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_quotation',
        'id_unit_quotation',
        'id_client',
        'no_contract',
        'level',
        'type',
        'date',
    ];

    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'id_client', 'id');
    }

    public function quotation()
    {
        return $this->belongsTo('App\Models\Quotation', 'id_quotation', 'id');
    }

    public function unitQuotation()
    {
        return $this->belongsTo('App\Models\UnitQuotation', 'id_unit_quotation', 'id');
    }

    public function visitSchedules()
    {
        return $this->hasMany(\App\Models\ContractVisitSchedule::class, 'id_contract', 'id');
    }
}
