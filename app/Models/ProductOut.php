<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOut extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "product_out";
    protected $date = [
        'created_at',
        'updated_at',
        'date'
    ];
    protected $fillable = [
        'id_user',
        'no_product_out',
        'detail_client',
        'invoice',
        'po',
        'no_type',
        'note',
        'vers',
        'flag',
        'total',
    ];
    public function isKojisha(): bool
    {
        return strtolower($this->flag ?? '') === 'kojisha'
            || (is_string($this->no_product_out) && str_contains($this->no_product_out, 'BK-KII'))
            || (is_string($this->invoice) && str_contains($this->invoice, '/KII/'))
            || (is_string($this->po) && str_contains($this->po, 'KII'));
    }
    public function detail()
    {
        return $this->hasMany('App\Models\DetailProductOut', 'id_product_out');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }
    public function pending()
    {
        return $this->hasMany('App\Models\PendingPO', 'id_product_out');
    }
}
