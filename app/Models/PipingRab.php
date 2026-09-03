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
        })->orderBy('revision_number', 'asc')->get();
    }

    public static function generateNoRab(): string
    {
        $yearMonth = date('Ym');
        $prefix = 'RAB-PIP/' . $yearMonth . '/';
        $last = self::where('no_rab', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        if ($last) {
            $lastNo = substr($last->no_rab, strlen($prefix));
            $nextNo = str_pad((int)$lastNo + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNo = '0001';
        }
        return $prefix . $nextNo;
    }
}
