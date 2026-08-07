<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubtitleTemplate extends Model
{
    use HasFactory;
    protected $table = "subtitle_template";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'subtitle'
    ];

    public function machine()
    {
        return $this->belongsTo('App\Models\MachineTemplate', 'id_machine', 'id');
    }
    public function detail()
    {
        return $this->hasMany('App\Models\DetailTemplate', 'id_subtitle');
    }
}
