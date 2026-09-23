<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "stock_opname";
    protected $dates = [
        'created_at',
        'updated_at',
        'date',
        'completed_at',
    ];
    protected $fillable = [
        'id_user',
        'date',
        'year',
        'periode',
        'status',
        'is_locked',
        'completed_at',
        'id_user_completed',
        'note',
    ];
    
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }

    public function userCompleted()
    {
        return $this->belongsTo('App\Models\User', 'id_user_completed', 'id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\DetailStockOpname', 'id_stock_opname');
    }

    public function isLocked(): bool
    {
        return (bool) ($this->is_locked || $this->status === 'completed');
    }
}
