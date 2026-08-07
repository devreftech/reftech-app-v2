<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTemplate extends Model
{
    use HasFactory;
    protected $table = "detail_template";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_subtitle',
        'product',
        'detail',
        'price',
        'qty',
        'disc',
        'info_qty',
        'amount'
    ];

    public function subtitle()
    {
        return $this->belongsTo('App\Models\SubtitleTemplate', 'id_subtitle', 'id');
    }
}
