<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    use HasFactory, LogsActivity;

    protected $table = "return";
    protected $fillable = [
        "id_pending",
        "id_product_in",
        "id_quotation",
        "id_unit_quotation",
        "id_sales",
        "no_return",
        "status",
        "reason_category",
        "reason_note",
        "resolution",
        "bank_name",
        "bank_account",
        "bank_holder",
        "total_amount",
        "date",
        "done_date",
    ];

    public function pending()
    {
        return $this->belongsTo('App\Models\PendingPO', 'id_pending', 'id');
    }
    public function quotation()
    {
        return $this->belongsTo('App\Models\Quotation', 'id_quotation', 'id');
    }
    public function unitQuotation()
    {
        return $this->belongsTo('App\Models\UnitQuotation', 'id_unit_quotation', 'id');
    }
    public function sales()
    {
        return $this->belongsTo('App\Models\User', 'id_sales', 'id');
    }
    public function productIn()
    {
        return $this->belongsTo('App\Models\ProductIn', 'id_product_in', 'id');
    }
    public function detail()
    {
        return $this->hasMany('App\Models\DetailReturn', 'id_retur');
    }
}
