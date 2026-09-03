<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'attachment',
        'attachment_name',
        'attachment_type',
        'is_read',
        'read_at',
        'is_edited',
        'edited_at',
        'original_message',
        'is_deleted',
        'deleted_at',
        'deleted_by',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'read_at' => 'datetime',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'attachment_url',
        'time_formatted',
        'date_formatted',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function getAttachmentUrlAttribute()
    {
        if (!$this->attachment) {
            return null;
        }

        if (str_starts_with($this->attachment, 'http://') || str_starts_with($this->attachment, 'https://')) {
            return $this->attachment;
        }

        return asset('storage/' . $this->attachment);
    }

    public function getTimeFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->setTimezone('Asia/Jakarta')->format('H:i') : '';
    }

    public function getDateFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->setTimezone('Asia/Jakarta')->isoFormat('D MMMM Y') : '';
    }
}
