<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalNoteTemplate extends Model
{
    use HasFactory;

    protected $table = 'rental_note_templates';

    protected $fillable = [
        'note',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
