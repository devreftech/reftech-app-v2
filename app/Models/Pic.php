<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pic extends Model
{
    use HasFactory, LogsActivity;

    protected $table = "pic";
    protected $fillable = [
        'id_client',
        'name_pic',
        'position',
        'email_pic',
        'phone_pic',
    ];

    public function activityLogReferenceLabel(): ?string
    {
        return $this->name_pic . ($this->position ? ' (' . $this->position . ')' : '');
    }

    public function activityLogExtraProperties(): array
    {
        return [
            'id_client' => $this->id_client,
        ];
    }

    
    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'id_client', 'id');
    }
    
    public function quotation()
    {
        return $this->hasMany('App\Models\Quotation', 'id_pic');
    }
}
