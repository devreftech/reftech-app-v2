<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPlant extends Model
{
    use HasFactory, LogsActivity;

    protected $table = "client_plants";
    protected $fillable = [
        'id_client',
        'name',
        'address',
    ];

    public function activityLogReferenceLabel(): ?string
    {
        return $this->name;
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
}
