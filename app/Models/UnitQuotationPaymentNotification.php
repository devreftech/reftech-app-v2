<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitQuotationPaymentNotification extends Model
{
    use HasFactory;
    protected $table = "unit_quotation_payment_notifications";
    protected $fillable = [
        'id_payment',
        'id_invoice',
        'id_unit_quotation',
        'id_user',
        'type',
        'is_read',
    ];
    public function payment()
    {
        return $this->belongsTo('App\Models\Payment', 'id_payment', 'id');
    }
    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice', 'id_invoice', 'id');
    }
    public function unitQuotation()
    {
        return $this->belongsTo('App\Models\UnitQuotation', 'id_unit_quotation', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }
}
