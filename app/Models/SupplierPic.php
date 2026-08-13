<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPic extends Model
{
    use HasFactory;

    protected $table = "supplier_pic";
    protected $fillable = [
        'id_supplier',
        'name_pic',
        'position',
        'phone_pic',
        'email_pic',
    ];

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'id_supplier', 'id');
    }
}
