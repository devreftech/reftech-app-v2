<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineTemplate extends Model
{
    use HasFactory;
    protected $table = "machine_template";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'brand',
        'sku'
    ];
    public function subtitle()
    {
        return $this->hasMany('App\Models\SubtitleTemplate', 'id_machine');
    }
}
