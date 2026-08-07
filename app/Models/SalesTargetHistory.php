<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTargetHistory extends Model
{
    protected $table = 'sales_target_histories';

    protected $fillable = ['user_id', 'year', 'target_annual', 'set_by'];

    public function sales()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function setBy()
    {
        return $this->belongsTo(User::class, 'set_by');
    }
}
