<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;
    protected $table = "purchase_request";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'no_pr',
        'id_pending',
        'id_user',
        'id_equivalent',
        'qty',
        'status',
        'purchase_type',
        'cargo',
        'no_resi',
        'purchase_date',
    ];
    public function pending()
    {
        return $this->belongsTo('App\Models\PendingPO', 'id_pending', 'id');
    }
    public function equivalent()
    {
        return $this->belongsTo('App\Models\SerialProduct', 'id_equivalent', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }
}
