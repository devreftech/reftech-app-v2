<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipingRab extends Model
{
    use HasFactory;

    protected $table = 'piping_rabs';

    protected $fillable = [
        'no_rab',
        'id_client',
        'id_pic',
        'id_sales',
        'id_admin',
        'project_name',
        'location_plant',
        'rab_date',
        'revision_number',
        'root_id',
        'is_latest',
        'status',
        'total_hpp',
        'total_margin',
        'total_selling_price',
        'converted_quotation_id',
        'notes',
    ];

    protected $casts = [
        'rab_date'            => 'date',
        'is_latest'           => 'boolean',
        'total_hpp'           => 'decimal:2',
        'total_margin'        => 'decimal:2',
        'total_selling_price' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    public function pic()
    {
        return $this->belongsTo(Pic::class, 'id_pic');
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'id_sales');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin');
    }

    public function sections()
    {
        return $this->hasMany(PipingRabSection::class, 'id_piping_rab')->orderBy('sort_order', 'asc');
    }

    public function convertedQuotation()
    {
        return $this->belongsTo(UnitQuotation::class, 'converted_quotation_id');
    }

    public function revisions()
    {
        $rootId = $this->root_id ?: $this->id;
        return self::where(function ($query) use ($rootId) {
            $query->where('id', $rootId)->orWhere('root_id', $rootId);
        })->with([
            'client',
            'pic',
            'sales',
            'admin',
            'convertedQuotation',
            'sections.items.supplier',
            'sections.items.material.vendorPrices.supplier'
        ])->orderBy('revision_number', 'asc')->get();
    }

    public static function generateNoRab(?User $user = null): string
    {
        $user = $user ?: \Illuminate\Support\Facades\Auth::user();
        $userInitial = 'REF';
        if ($user) {
            $userInitial = !empty($user->code) ? strtoupper(trim($user->code)) : strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $user->name), 0, 3));
        }

        $year = date('Y');
        $month = (int)date('m');
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $monthRoman = $romanMonths[$month] ?? 'I';

        // Cari nomor urut terakhir pada tahun berjalan
        $lastRab = self::where(function ($q) use ($year) {
            $q->where('no_rab', 'like', "%/{$year}")
              ->orWhere('no_rab', 'like', "%/{$year}-R%");
        })->orderBy('id', 'desc')->first();

        $nextNumber = 1;
        if ($lastRab && preg_match('/^(\d+)-RAB\//i', $lastRab->no_rab, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            $count = self::whereYear('created_at', $year)->count();
            $nextNumber = $count + 1;
        }

        $numPadded = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        return "{$numPadded}-RAB/{$userInitial}/{$monthRoman}/{$year}";
    }
}
