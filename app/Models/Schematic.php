<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schematic extends Model
{
    use HasFactory;

    protected $table = 'schematics';

    protected $fillable = [
        'schematic_number',
        'title',
        'client_id',
        'project_name',
        'diagram_type',
        'canvas_data',
        'preview_image',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function generateNumber()
    {
        $prefix = 'SCH-' . date('Ymd') . '-';
        $last = self::where('schematic_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $seq = intval(substr($last->schematic_number, -4)) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
