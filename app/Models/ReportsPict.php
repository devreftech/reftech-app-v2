<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportsPict extends Model
{
    use HasFactory;
    
    protected $table = "reports_pict";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_reports',
        'picture',
        'keterangan'
    ];

    // Connection Table
    public function reports()
    {
        return $this->belongsTo('App\Models\Reports', 'id_reports', 'id');
    }

    public function getUrlAttribute()
    {
        if (!$this->picture) {
            return null;
        }

        if (str_starts_with($this->picture, 'service-reports/')) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->picture);
        }

        return url('/' . $this->picture);
    }
}
