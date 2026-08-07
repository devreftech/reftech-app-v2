<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPaymentTemplate extends Model
{
    use HasFactory;

    protected $table = 'sales_payment_templates';

    protected $fillable = [
        'id_sales',
        'id_client',
        'client_ids',
        'name',
        'payment_term',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'client_ids' => 'array',
    ];

    public function sales()
    {
        return $this->belongsTo(User::class, 'id_sales', 'id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id');
    }

    public function getClientsListAttribute()
    {
        $ids = $this->client_ids;
        if (empty($ids)) {
            if ($this->id_client) {
                $ids = [$this->id_client];
            } else {
                return collect([]);
            }
        }
        return Client::whereIn('id', (array) $ids)->get();
    }
}
