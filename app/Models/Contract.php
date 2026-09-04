<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "contract";
    protected $dates = [
        'date',
        'signed_at',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_quotation',
        'id_unit_quotation',
        'id_client',
        'no_contract',
        'level',
        'type',
        'date',
        'sign_token',
        'signed_at',
        'customer_signature',
        'customer_signer_name',
        'customer_signer_position',
        'customer_signed_stamp',
        'customer_ip',
    ];

    /**
     * Get or auto-generate a secure token for customer online signature.
     */
    public function getSignTokenAttribute($value)
    {
        if (empty($value)) {
            $newToken = bin2hex(random_bytes(20));
            // Update silently in database
            \Illuminate\Support\Facades\DB::table('contract')
                ->where('id', $this->id)
                ->update(['sign_token' => $newToken]);
            $this->attributes['sign_token'] = $newToken;
            return $newToken;
        }
        return $value;
    }

    /**
     * URL publik untuk customer menandatangani kontrak online.
     */
    public function getSignUrlAttribute(): string
    {
        return url('/contract/sign/' . $this->sign_token);
    }

    /**
     * Cek apakah kontrak sudah ditandatangani oleh customer secara online.
     */
    public function isSignedByCustomer(): bool
    {
        return !empty($this->customer_signature) && !empty($this->signed_at);
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'id_client', 'id');
    }

    public function quotation()
    {
        return $this->belongsTo('App\Models\Quotation', 'id_quotation', 'id');
    }

    public function unitQuotation()
    {
        return $this->belongsTo('App\Models\UnitQuotation', 'id_unit_quotation', 'id');
    }

    public function visitSchedules()
    {
        return $this->hasMany(\App\Models\ContractVisitSchedule::class, 'id_contract', 'id');
    }

    /**
     * Nomor kontrak untuk quotation unit (Smart Quotation), dipisah per (type x PPN/Non-PPN)
     * dan berdiri sendiri dari deret kontrak service.
     *   Selling  -> {seq}/{P|NP}/SELLCTX/RJO/{tahun}
     *   Order    -> {seq}/{P|NP}/CO/KII/{tahun}
     *
     * @return array{
     *   nextSP:string, nextSNP:string, nextCP:string, nextCNP:string,
     *   lastSP:?string, lastSNP:?string, lastCP:?string, lastCNP:?string
     * }
     */
    public static function unitContractNumbers($year): array
    {
        $last = function (string $type, int $tax) use ($year) {
            return static::join('unit_quotation as uq', 'uq.id', '=', 'contract.id_unit_quotation')
                ->whereYear('contract.date', $year)
                ->where('uq.tax', $tax)
                ->where('contract.type', $type)
                ->where('contract.level', '1')
                ->orderByDesc('contract.id')
                ->value('contract.no_contract');
        };

        $next = function (?string $lastNo): string {
            if ($lastNo && preg_match('/^\d{3}/', $lastNo, $m)) {
                return str_pad((int) $m[0] + 1, 3, '0', STR_PAD_LEFT);
            }
            return '001';
        };

        $lastSP  = $last('Selling', 1);
        $lastSNP = $last('Selling', 0);
        $lastCP  = $last('Order', 1);
        $lastCNP = $last('Order', 0);

        return [
            'nextSP'  => $next($lastSP),
            'nextSNP' => $next($lastSNP),
            'nextCP'  => $next($lastCP),
            'nextCNP' => $next($lastCNP),
            'lastSP'  => $lastSP,
            'lastSNP' => $lastSNP,
            'lastCP'  => $lastCP,
            'lastCNP' => $lastCNP,
        ];
    }
}
