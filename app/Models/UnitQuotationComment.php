<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitQuotationComment extends Model
{
    protected $table = 'unit_quotation_comments';

    protected $fillable = [
        'id_unit_quotation',
        'user_id',
        'comment',
    ];

    public function unitQuotation()
    {
        return $this->belongsTo(UnitQuotation::class, 'id_unit_quotation');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
