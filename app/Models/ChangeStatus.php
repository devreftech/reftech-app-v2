<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeStatus extends Model
{
    use HasFactory;
    protected $table = "change_status";
    protected $dates = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_quotation',
        'id_unit_quotation',
        'id_user',
        'id_payment',
        'id_pending',
        'status',
        'note',
        'date',
    ];
    
    public function quotation()
    {
        return $this->belongsTo('App\Models\Quotation', 'id_quotation', 'id');
    }
    public function comment()
    {
        return $this->hasMany('App\Models\Comment', 'id_status');
    }
    public function payment()
    {
        return $this->belongsTo('App\Models\Payment', 'id_payment', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }
}
