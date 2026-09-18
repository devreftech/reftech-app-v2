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
        'no_do',
        'entity',
        'id_invoice',
        'id_suo',
        'id_unit_quotation',
        'customer_name',
        'address',
        'po_number',
        'destination',
        'driver_name',
        'vehicle_no',
        'note',
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
     * Display DO / Surat Jalan Number.
     */
    public function getDoNumberAttribute(): string
    {
        if (!empty($this->no_do)) {
            return $this->no_do;
        }
        if ($this->invoice && !empty($this->invoice->no_invoice)) {
            return $this->invoice->no_invoice;
        }
        if ($this->unitQuotation && !empty($this->unitQuotation->no_quote)) {
            return $this->unitQuotation->no_quote;
        }
        if ($this->suo && !empty($this->suo->no_invoice_booking)) {
            return $this->suo->no_invoice_booking;
        }
        if ($this->suo && !empty($this->suo->no_suo)) {
            return $this->suo->no_suo;
        }
        return 'DO-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Display Customer Name.
     */
    public function getCustomerNameDisplayAttribute(): string
    {
        if (!empty($this->customer_name)) {
            return $this->customer_name;
        }
        if ($this->invoice && $this->invoice->quote && $this->invoice->quote->pic && $this->invoice->quote->pic->client) {
            return $this->invoice->quote->pic->client->company ?? '-';
        }
        if ($this->unitQuotation && $this->unitQuotation->client) {
            return $this->unitQuotation->client->company ?? '-';
        }
        if ($this->suo) {
            return $this->suo->company ?? '-';
        }
        return '-';
    }

    /**
     * Display Delivery Address.
     */
    public function getAddressDisplayAttribute(): string
    {
        if (!empty($this->address)) {
            return $this->address;
        }
        if ($this->invoice && $this->invoice->quote && $this->invoice->quote->pic && $this->invoice->quote->pic->client) {
            $client = $this->invoice->quote->pic->client;
            return ($this->destination == '2' && !empty($client->subAddress)) ? $client->subAddress : ($client->address ?? '-');
        }
        if ($this->unitQuotation && $this->unitQuotation->client) {
            $client = $this->unitQuotation->client;
            return ($this->destination == '2' && !empty($client->subAddress)) ? $client->subAddress : ($client->address ?? '-');
        }
        if ($this->suo) {
            return $this->suo->address ?? '-';
        }
        return '-';
    }

    /**
     * Display PO Number reference.
     */
    public function getPoNumberDisplayAttribute(): string
    {
        if (!empty($this->po_number)) {
            return $this->po_number;
        }
        if ($this->invoice && !empty($this->invoice->no_po)) {
            return $this->invoice->no_po;
        }
        if ($this->unitQuotation && !empty($this->unitQuotation->po_number)) {
            return $this->unitQuotation->po_number;
        }
        return '-';
    }

    /**
     * Display Entity (Reftech / Kojisha).
     */
    public function getEntityDisplayAttribute(): string
    {
        if (!empty($this->entity)) {
            return $this->entity;
        }
        if ($this->invoice && !empty($this->invoice->flag)) {
            return $this->invoice->flag;
        }
        if ($this->unitQuotation && $this->unitQuotation->client) {
            return ($this->unitQuotation->client->info === 'Kojisha') ? 'Kojisha' : 'Reftech';
        }
        return 'Reftech';
    }

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
