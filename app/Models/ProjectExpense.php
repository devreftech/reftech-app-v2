<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    use HasFactory;

    protected $table = 'project_expenses';

    protected $fillable = [
        'id_pending',
        'id_user',
        'name',
        'category',
        'amount',
        'date',
        'receipt',
    ];

    protected $dates = ['date'];

    public function pending()
    {
        return $this->belongsTo('App\Models\PendingPO', 'id_pending', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }
}
