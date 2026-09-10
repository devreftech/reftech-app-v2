<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "delivery";
    protected $dates = [
        'created_at',
        'updated_at',
        'date',
        'customer_signed_at',
    ];

    protected $casts = [
        'date' => 'date',
        'customer_signed_at' => 'datetime',
    ];

    protected $fillable = [
        'id_invoice',
        'id_suo',
        'id_unit_quotation',
        'destination',
        'date',
        'type',
        'code',
        'sign',
        'sign_token',
        'customer_signature',
        'customer_signer_name',
        'customer_signer_position',
        'customer_signed_stamp',
        'customer_signed_at',
        'customer_ip',
    ];

    /**
     * Get or auto-generate a secure token for customer online signature.
     */
    public function getSignTokenAttribute($value)
    {
        if (empty($value)) {
            $newToken = bin2hex(random_bytes(20));
            \Illuminate\Support\Facades\DB::table('delivery')
                ->where('id', $this->id)
                ->update(['sign_token' => $newToken]);
            $this->attributes['sign_token'] = $newToken;
            return $newToken;
        }
        return $value;
    }

    /**
     * URL publik untuk customer menandatangani Surat Jalan online.
     */
    public function getSignUrlAttribute(): string
    {
        return url('/delivery/sign/' . $this->sign_token);
    }

    /**
     * Cek apakah Surat Jalan sudah ditandatangani oleh penerima secara online.
     */
    public function isSignedByCustomer(): bool
    {
        return !empty($this->customer_signature) && !empty($this->customer_signed_at);
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice', 'id_invoice', 'id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\DetailDelivery', 'id_delivery');
    }

    public function unitQuotation()
    {
        return $this->belongsTo('App\Models\UnitQuotation', 'id_unit_quotation', 'id');
    }

    public function suo()
    {
        return $this->belongsTo('App\Models\Suo', 'id_suo', 'id');
    }
}
