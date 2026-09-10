<?php

namespace App\Models\Hvac;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HvacProject extends Model
{
    use HasFactory;

    protected $table = 'hvac_projects';

    protected $fillable = [
        'project_code',
        'project_name',
        'id_client',
        'customer_name',
        'id_sales',
        'id_engineer',
        'location',
        'design_outdoor_temp',
        'design_outdoor_rh',
        'status',
        'notes',
    ];

    protected $casts = [
        'design_outdoor_temp' => 'decimal:2',
        'design_outdoor_rh'   => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_sales');
    }

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_engineer');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(HvacRoom::class, 'id_hvac_project');
    }

    public function getCustomerDisplayNameAttribute(): string
    {
        return $this->client ? $this->client->company : ($this->customer_name ?: 'Unknown Customer');
    }

    public static function generateProjectCode(): string
    {
        $yearMonth = date('ym');
        $prefix = 'HVAC-' . $yearMonth . '-';
        $last = self::where('project_code', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        if ($last) {
            $lastNo = substr($last->project_code, strlen($prefix));
            $nextNo = str_pad((int)$lastNo + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNo = '0001';
        }
        return $prefix . $nextNo;
    }
}
