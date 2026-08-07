<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseInvoice extends Model
{
    use HasFactory;

    protected $table = "expense_invoice";
    protected $fillable = [
        'id_invoice',
        'desc',
        'qty',
        'price',
        'total',
    ];
    
    public function quote()
    {
        return $this->belongsTo('App\Models\Invoice', 'id_invoice', 'id');
    }
}
