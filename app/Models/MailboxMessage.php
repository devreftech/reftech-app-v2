<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailboxMessage extends Model
{
    use HasFactory;

    protected $table = 'mailbox_messages';

    protected $fillable = [
        'user_id',
        'folder',
        'status',
        'sender_name',
        'sender_email',
        'recipient_name',
        'recipient_email',
        'cc',
        'bcc',
        'subject',
        'preview',
        'body_html',
        'body_text',
        'tag',
        'tag_color',
        'is_read',
        'is_starred',
        'has_attachment',
        'message_id',
        'sent_at',
        'received_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_starred' => 'boolean',
        'has_attachment' => 'boolean',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(MailboxAttachment::class, 'mailbox_message_id');
    }
}
