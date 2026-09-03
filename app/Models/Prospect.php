<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    use HasFactory, LogsActivity;
    
    protected $table = "prospect";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_support',
        'id_sales',
        'id_quotation',
        'id_pic',
        'kebutuhan',
        'provide',
        'level',
        'date',
    ];

    // Connection Table
    public function quotation()
    {
        return $this->belongsTo('App\Models\Quotation', 'id_quotation', 'id');
    }
    public function smartQuotation()
    {
        return $this->belongsTo('App\Models\UnitQuotation', 'id_quotation', 'id');
    }

    /**
     * Resolusi kolom `id_quotation` yang dipakai bersama oleh dua tabel berbeda:
     * Smart Quote (`unit_quotation`) vs quotation lama (`quotation`). Id kedua tabel
     * itu punya ruang auto-increment sendiri-sendiri, jadi Smart Quote dicocokkan
     * dengan `id_pic` prospek supaya tabrakan id (id sama di kedua tabel) tidak
     * salah resolve. Fallback ke quotation lama kalau tidak ketemu.
     *
     * @return array{0: \Illuminate\Database\Eloquent\Model|null, 1: bool} [$quotation, $isSmart]
     */
    public function resolveQuotation(): array
    {
        if (! $this->id_quotation) {
            return [null, false];
        }

        $smart = \App\Models\UnitQuotation::where('id', $this->id_quotation)
            ->where('id_pic', $this->id_pic)
            ->first();

        if ($smart) {
            return [$smart, true];
        }

        return [\App\Models\Quotation::find($this->id_quotation), false];
    }
    public function sales()
    {
        return $this->belongsTo('App\Models\User', 'id_sales', 'id');
    }
    public function support()
    {
        return $this->belongsTo('App\Models\User', 'id_support', 'id');
    }
    public function pic()
    {
        return $this->belongsTo('App\Models\Pic', 'id_pic', 'id');
    }
    public function Comment()
    {
        return $this->hasMany('App\Models\Comment', 'id_prospect');
    }
}
