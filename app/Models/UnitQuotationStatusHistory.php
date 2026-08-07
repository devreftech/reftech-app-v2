<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitQuotationStatusHistory extends Model
{
    protected $table = 'unit_quotation_status_history';

    protected $fillable = ['id_unit_quotation', 'status', 'note'];

    public function unitQuotation()
    {
        return $this->belongsTo(UnitQuotation::class, 'id_unit_quotation');
    }
}
