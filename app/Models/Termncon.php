<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Termncon extends Model
{
    use HasFactory;
    protected $table = "termncon";
    protected $fillable = [
        "id_quotation",
        "validity",
        "pricing",
        "warranty",
        "delivery_process",
        "payment"
    ];

    public function quotation()
    {
        return $this->belongsTo('App/Models/Quotation', 'id_quotation', 'id');
    }
}
