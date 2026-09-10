<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bast extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'basts';

    protected $fillable = [
        'no_bast',
        'type',
        'id_kanban_task',
        'id_quotation',
        'entity',
        'customer_name',
        'work_title',
        'po_number',
        'work_date',
        'rental_start_date',
        'rental_end_date',
        'test_running_result',
        'sign',
        'created_by',
        'sign_token',
        'customer_signature',
        'customer_signer_name',
        'customer_signer_position',
        'customer_signed_stamp',
        'customer_signed_at',
        'customer_ip',
    ];

    protected $casts = [
        'work_date' => 'date',
        'rental_start_date' => 'date',
        'rental_end_date' => 'date',
        'customer_signed_at' => 'datetime',
    ];

    /**
     * Get or auto-generate a secure token for customer online signature.
     */
    public function getSignTokenAttribute($value)
    {
        if (empty($value)) {
            $newToken = bin2hex(random_bytes(20));
            \Illuminate\Support\Facades\DB::table('basts')
                ->where('id', $this->id)
                ->update(['sign_token' => $newToken]);
            $this->attributes['sign_token'] = $newToken;
            return $newToken;
        }
        return $value;
    }

    /**
     * URL publik untuk customer menandatangani BAST online.
     */
    public function getSignUrlAttribute(): string
    {
        return url('/bast/sign/' . $this->sign_token);
    }

    /**
     * Cek apakah BAST sudah ditandatangani oleh customer secara online.
     */
    public function isSignedByCustomer(): bool
    {
        return !empty($this->customer_signature) && !empty($this->customer_signed_at);
    }

    public function kanbanTask()
    {
        return $this->belongsTo(KanbanTask::class, 'id_kanban_task');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'id_quotation');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function units()
    {
        return $this->hasMany(BastUnit::class, 'id_bast')->orderBy('position');
    }
}
