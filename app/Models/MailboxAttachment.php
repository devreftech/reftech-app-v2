<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailboxAttachment extends Model
{
    use HasFactory;

    protected $table = 'mailbox_attachments';

    protected $fillable = [
        'mailbox_message_id',
        'filename',
        'file_path',
        'file_size',
        'file_ext',
        'mime_type',
    ];

    public function message()
    {
        return $this->belongsTo(MailboxMessage::class, 'mailbox_message_id');
    }
}
