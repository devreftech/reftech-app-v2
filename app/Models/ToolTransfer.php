<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolTransfer extends Model
{
    use HasFactory;

    protected $table = 'tool_transfers';

    protected $fillable = [
        'transfer_number',
        'id_fixed_asset',
        'id_from_user',
        'id_to_user',
        'status',
        'catatan_pengirim',
        'catatan_penerima',
        'foto_kondisi',
        'requested_at',
        'responded_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function fixedAsset()
    {
        return $this->belongsTo(FixedAsset::class, 'id_fixed_asset');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'id_from_user');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'id_to_user');
    }
}
