<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = "payment";
    protected $fillable = [
        'id_quotation',
        'id_unit_quotation',
        'file',
        'percent',
        'amount',
        'note',
        'level',
        'type',
        'method',
        'date_confirm',
        'due_date',
        'overdue',
        'tempo',
        'date',
        'cost',
        'pph',
    ];

    public function quotation()
    {
        return $this->belongsTo('App\Models\Quotation', 'id_quotation', 'id');
    }

    public function unitQuotation()
    {
        return $this->belongsTo('App\Models\UnitQuotation', 'id_unit_quotation', 'id');
    }
}
