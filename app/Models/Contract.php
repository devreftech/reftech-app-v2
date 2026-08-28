<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "contract";
    protected $date = [
        'date',
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
    ];

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
