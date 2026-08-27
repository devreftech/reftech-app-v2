<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "service_order";
    protected $date = [
        'date_schedule',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'SJ',
        'BA',
        'note_doc',
        'note_schedule',
    ];
    public function order()
    {
        return $this->belongsTo('App\Models\PendingPO', 'id_sales_order', 'id');
    }
}
