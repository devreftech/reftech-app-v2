<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailExpense extends Model
{
    use HasFactory;
    protected $table = "detail_expense";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'amount',
        'memo',
    ];
    public function expense()
    {
        return $this->belongsTo('App\Model\Expense', 'id_expense', 'id');
    }
    public function account()
    {
        return $this->belongsTo('App\Models\Account', 'id_account', 'id');
    }
}
