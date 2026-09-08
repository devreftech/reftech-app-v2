<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "purchase_order";
    protected $dates = [
        'date',
        'vendor_signed_at',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'no_po',
        'no_gr',
        'no_invoice_supplier',
        'invoice_file',
        'category',
        'receipt_status',
        'gr_sent_at',
        'id_purchase_request',
        'attn',
        'mobile',
        'company',
        'email',
        'address',
        'phone',
        'payment',
        'delivery',
        'note',
        'subtotal',
        'vat',
        'delivery_cost',
        'total',
        'sign_token',
        'vendor_signature',
        'vendor_signer_name',
        'vendor_signer_position',
        'vendor_signed_stamp',
        'vendor_signed_at',
        'vendor_ip',
    ];

    /**
     * Get or auto-generate a secure token for vendor online signature.
     */
    public function getSignTokenAttribute($value)
    {
        if (empty($value)) {
            $newToken = bin2hex(random_bytes(20));
            \Illuminate\Support\Facades\DB::table('purchase_order')
                ->where('id', $this->id)
                ->update(['sign_token' => $newToken]);
            $this->attributes['sign_token'] = $newToken;
            return $newToken;
        }
        return $value;
    }

    /**
     * URL publik untuk vendor menandatangani PO online.
     */
    public function getSignUrlAttribute(): string
    {
        return url('/purchase/sign/' . $this->sign_token);
    }

    /**
     * Cek apakah PO sudah ditandatangani oleh vendor secara online.
     */
    public function isSignedByVendor(): bool
    {
        return !empty($this->vendor_signature) && !empty($this->vendor_signed_at);
    }
    public function detail()
    {
        return $this->hasMany('App\Models\DetailPurchaseOrder', 'id_purchase_order');
    }
    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'id_supplier', 'id');
    }
    public function purchaseRequest()
    {
        return $this->belongsTo('App\Models\PurchaseRequest', 'id_purchase_request', 'id');
    }
    public function prAllocations()
    {
        return $this->hasMany('App\Models\PurchaseRequestDetailAllocation', 'id_purchase_order');
    }
}
