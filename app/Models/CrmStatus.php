<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmStatus extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "crm_status";
    protected $fillable = [
        'id_client', 'status'
    ];

    public function activityLogReferenceLabel(): ?string
    {
        $statusLabels = [
            '1' => 'Bangkrupt',
            '2' => 'Aktif',
            '3' => 'Non-Aktif',
        ];
        return 'Status Customer: ' . ($statusLabels[$this->status] ?? $this->status);
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
