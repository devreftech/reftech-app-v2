<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReports extends Model
{
    use HasFactory;
    protected $table = "sales_reports";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'semester',
        'year',
        'target',
    ];
}
