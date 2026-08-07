<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expanse extends Model
{
    use HasFactory;
    protected $table = "expanse";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'kurir',
        'image',
        'charged',
        'cost',
        'no_track',
        'type',
        'status',
        'id_expense',
        'description',
    ];
    public function pending()
    {
        return $this->belongsTo('App\Models\PendingPO', 'id_pending', 'id');
    }
    public function expense()
    {
        return $this->belongsTo('App\Models\Expense', 'id_expense', 'id');
    }
}
