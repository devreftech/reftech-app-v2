<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class UserMailSetting extends Model
{
    use HasFactory;

    protected $table = 'user_mail_settings';

    protected $fillable = [
        'user_id',
        'mail_driver',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'smtp_username',
        'smtp_password',
        'imap_host',
        'imap_port',
        'imap_encryption',
        'imap_username',
        'imap_password',
        'from_name',
        'from_address',
        'signature_layout',
        'signature_color',
        'signature_html',
        'is_active',
        'last_synced_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    /**
     * Mutator to encrypt SMTP password automatically.
     */
    public function setSmtpPasswordAttribute($value)
    {
        $this->attributes['smtp_password'] = !empty($value) ? Crypt::encryptString($value) : null;
    }

    /**
     * Accessor to decrypt SMTP password.
     */
    public function getDecryptedSmtpPasswordAttribute()
    {
        if (empty($this->smtp_password)) {
            return null;
        }

        try {
            return Crypt::decryptString($this->smtp_password);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Mutator to encrypt IMAP password.
     */
    public function setImapPasswordAttribute($value)
    {
        $this->attributes['imap_password'] = !empty($value) ? Crypt::encryptString($value) : null;
    }

    /**
     * Accessor to decrypt IMAP password.
     */
    public function getDecryptedImapPasswordAttribute()
    {
        if (empty($this->imap_password)) {
            return null;
        }

        try {
            return Crypt::decryptString($this->imap_password);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
