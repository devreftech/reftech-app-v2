<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignPict extends Model
{
    use HasFactory;

    protected $table = "sign_pict";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_reports',
        'picture',
    ];

    // Connection Table
    public function reports()
    {
        return $this->belongsTo('App\Models\Reports', 'id_reports', 'id');
    }
}
