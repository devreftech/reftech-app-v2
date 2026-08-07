<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractVisitSchedule extends Model
{
    use HasFactory;

    protected $table = 'contract_visit_schedule';

    protected $fillable = [
        'id_contract',
        'visit_number',
        'planned_date',
        'estimated_revenue',
        'status',
        'description',
    ];

    protected $dates = [
        'planned_date',
        'created_at',
        'updated_at',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'id_contract', 'id');
    }
}
