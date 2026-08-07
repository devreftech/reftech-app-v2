<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubtitleQuotation extends Model
{
    use HasFactory;
    protected $table = "subtitle_quotation";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'subtitle'
    ];

    public function quotation()
    {
        return $this->belongsTo('App\Models\Quotation', 'id_quotation', 'id');
    }
    public function detail()
    {
        return $this->hasMany('App\Models\DetailServiceQuotation', 'id_subtitle');
    }
}
