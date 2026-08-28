<?php

namespace App\Services;

use App\Models\MailboxAttachment;
use App\Models\MailboxMessage;
use App\Models\User;
use App\Models\UserMailSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class MailboxService
{
    /**
     * Send email using user's configured SMTP (or fallback to default .env mailer).
     *
     * @param User $user
     * @param array $payload
     * @param array $uploadedFiles
     * @return array
     */
    public function sendEmail(User $user, array $payload, array $uploadedFiles = [])
    {
        $setting = UserMailSetting::where('user_id', $user->id)->where('is_active', true)->first();

        $senderEmail = $user->email ?: 'sales@reftech.id';
        $senderName = $user->name ?: 'Reftech Sales';

        // Check if user has personal SMTP configuration
        $hasCustomSmtp = $setting && !empty($setting->smtp_username) && !empty($setting->decrypted_smtp_password);

        if ($hasCustomSmtp) {
            $host = $setting->smtp_host ?: 'smtp.gmail.com';
            $port = (int) ($setting->smtp_port ?: 587);
            $encryption = strtolower($setting->smtp_encryption ?: 'tls');
            $username = $setting->smtp_username;
            $password = $setting->decrypted_smtp_password;
            $senderEmail = $setting->from_address ?: $username;
            $senderName = $setting->from_name ?: $senderName;

            // Configure dynamic mailer runtime
            Config::set('mail.mailers.user_smtp', [
                'transport' => 'smtp',
                'host' => $host,
                'port' => $port,
                'encryption' => $encryption === 'ssl' ? 'ssl' : ($encryption === 'tls' ? 'tls' : null),
                'username' => $username,
                'password' => $password,
                'timeout' => 20,
                'auth_mode' => null,
            ]);

            $mailer = Mail::mailer('user_smtp');
        } else {
            // Fallback to system default mailer from .env
            $mailer = Mail::mailer();
        }

        $recipientEmail = trim($payload['recipient_email']);
        $recipientName = !empty($payload['recipient_name']) ? $payload['recipient_name'] : explode('@', $recipientEmail)[0];
        $subject = $payload['subject'];
        $bodyText = $payload['body'];
        $signatureHtml = !empty($payload['signature_html']) ? $payload['signature_html'] : ($setting ? $setting->signature_html : '');

        // Compile HTML body
        $bodyHtml = nl2br(e($bodyText));
        if (!empty($payload['include_signature']) && !empty($signatureHtml)) {
            $bodyHtml .= "<br><br>" . $signatureHtml;
        }

        try {
            // Send email
            $mailer->send([], [], function ($message) use ($senderEmail, $senderName, $recipientEmail, $recipientName, $subject, $bodyHtml, $payload, $uploadedFiles) {
                $message->from($senderEmail, $senderName)
                    ->to($recipientEmail, $recipientName)
                    ->subject($subject)
                    ->html($bodyHtml);

                if (!empty($payload['cc'])) {
                    $ccList = array_map('trim', explode(',', $payload['cc']));
                    $message->cc(array_filter($ccList));
                }

                if (!empty($payload['bcc'])) {
                    $bccList = array_map('trim', explode(',', $payload['bcc']));
                    $message->bcc(array_filter($bccList));
                }

                // Handle file attachments
                foreach ($uploadedFiles as $file) {
                    $message->attach($file->getRealPath(), [
                        'as' => $file->getClientOriginalName(),
                        'mime' => $file->getMimeType(),
                    ]);
                }
            });

            $status = 'Delivered';
            $errorMsg = null;
        } catch (\Exception $e) {
            Log::error("Mailbox Send Error: " . $e->getMessage());
            $status = 'Failed';
            $errorMsg = $e->getMessage();
        }

        // Save into mailbox_messages database table
        $savedMessage = MailboxMessage::create([
            'user_id' => $user->id,
            'folder' => 'sent',
            'status' => $status,
            'sender_name' => $senderName,
            'sender_email' => $senderEmail,
            'recipient_name' => $recipientName,
            'recipient_email' => $recipientEmail,
            'cc' => $payload['cc'] ?? null,
            'bcc' => $payload['bcc'] ?? null,
            'subject' => $subject,
            'preview' => substr(strip_tags($bodyText), 0, 95) . '...',
            'body_html' => $bodyHtml,
            'body_text' => $bodyText,
            'tag' => $payload['tag'] ?? 'General / Sales',
            'tag_color' => 'primary',
            'is_read' => true,
            'is_starred' => false,
            'has_attachment' => !empty($uploadedFiles),
            'sent_at' => now(),
        ]);

        // Save attachments records
        if (!empty($uploadedFiles) && $savedMessage) {
            foreach ($uploadedFiles as $file) {
                $path = $file->store('mailbox_attachments', 'public');
                MailboxAttachment::create([
                    'mailbox_message_id' => $savedMessage->id,
                    'filename' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => round($file->getSize() / 1024, 1) . ' KB',
                    'file_ext' => $file->getClientOriginalExtension(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        if ($status === 'Failed') {
            return [
                'success' => false,
                'message' => "Gagal mengirim email: " . $errorMsg,
                'message_id' => $savedMessage->id ?? null,
            ];
        }

        return [
            'success' => true,
            'message' => "Email berhasil dikirim ke {$recipientEmail}!",
            'data' => $savedMessage->load('attachments'),
        ];
    }

    /**
     * Test SMTP connection handshake.
     *
     * @param UserMailSetting $setting
     * @return array
     */
    public function testSmtpConnection(UserMailSetting $setting)
    {
        $host = $setting->smtp_host ?: 'smtp.gmail.com';
        $port = (int) ($setting->smtp_port ?: 587);
        $encryption = strtolower($setting->smtp_encryption ?: 'tls');
        $username = $setting->smtp_username;
        $password = $setting->decrypted_smtp_password;

        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Username / Email dan Password SMTP belum diisi.',
            ];
        }

        try {
            $isTls = ($encryption === 'tls' || $encryption === 'ssl');
            $transport = new EsmtpTransport($host, $port, $isTls);
            $transport->setUsername($username);
            $transport->setPassword($password);

            $transport->start();
            $transport->stop();

            return [
                'success' => true,
                'message' => "Koneksi SMTP Berhasil! Server ({$host}:{$port}) merespons OK dan autentikasi terverifikasi.",
            ];
        } catch (\Exception $e) {
            Log::warning("SMTP Test Connection Failed: " . $e->getMessage());

            $errMsg = $e->getMessage();
            if (str_contains($errMsg, 'Bad credentials') || str_contains($errMsg, '535-5.7.8') || str_contains($errMsg, 'Username and Password not accepted')) {
                $errMsg = "Username atau Password SMTP ditolak. Untuk akun Gmail / Google Workspace, pastikan Anda menggunakan 'Google App Password' 16 digit, bukan password login biasa.";
            } elseif (str_contains($errMsg, 'Connection refused') || str_contains($errMsg, 'timed out')) {
                $errMsg = "Gagal terhubung ke host {$host} pada port {$port}. Pastikan port tidak diblokir firewall.";
            }

            return [
                'success' => false,
                'message' => "Gagal terhubung ke SMTP: " . $errMsg,
            ];
        }
    }

    /**
     * Synchronize BOTH incoming (INBOX) and outgoing (SENT) emails directly from the mail server.
     *
     * @param User $user
     * @return array
     */
    public function syncIncomingEmails(User $user)
    {
        $setting = UserMailSetting::where('user_id', $user->id)->first();

        if (!$setting || empty($setting->smtp_username) || empty($setting->decrypted_smtp_password)) {
            return [
                'success' => false,
                'message' => 'Silakan konfigurasikan email dan password Anda di menu "Setting SMTP/IMAP Akun" terlebih dahulu.',
            ];
        }

        // Determine IMAP configuration
        $host = $setting->imap_host ?: ($setting->smtp_host === 'smtp.gmail.com' ? 'imap.gmail.com' : 'imap.' . explode('@', $setting->smtp_username)[1]);
        $port = (int) ($setting->imap_port ?: 993);
        $encryption = $setting->imap_encryption ?: 'ssl';
        $username = $setting->imap_username ?: $setting->smtp_username;
        $password = $setting->decrypted_imap_password ?: $setting->decrypted_smtp_password;

        $newInboxCount = 0;
        $newSentCount = 0;

        try {
            $client = new ImapSocketClient();
            $client->connect($host, $port, $encryption, 15);
            $client->login($username, $password);

            // 1. SINKRONISASI KOTAK MASUK (INBOX)
            $inboxEmails = $client->fetchRecentEmailsFromFolder('INBOX', 25);
            foreach ($inboxEmails as $item) {
                $exists = MailboxMessage::where('user_id', $user->id)
                    ->where('message_id', $item['message_id'])
                    ->exists();

                if (!$exists) {
                    $tag = 'Inquiry Baru';
                    $tagColor = 'success';
                    $lowerSubj = strtolower($item['subject']);
                    if (str_contains($lowerSubj, 're: ') || str_contains($lowerSubj, 'penawaran') || str_contains($lowerSubj, 'quote')) {
                        $tag = 'Penawaran';
                        $tagColor = 'info';
                    } elseif (str_contains($lowerSubj, 'invoice') || str_contains($lowerSubj, 'tagihan')) {
                        $tag = 'Tagihan';
                        $tagColor = 'danger';
                    }

                    $msg = MailboxMessage::create([
                        'user_id' => $user->id,
                        'folder' => 'inbox',
                        'status' => 'Received',
                        'sender_name' => $item['sender_name'],
                        'sender_email' => $item['sender_email'],
                        'recipient_name' => $item['recipient_name'] ?: $user->name,
                        'recipient_email' => $item['recipient_email'] ?: $user->email,
                        'cc' => $item['cc'] ?? null,
                        'bcc' => $item['bcc'] ?? null,
                        'subject' => $item['subject'],
                        'preview' => substr(strip_tags($item['body_text']), 0, 95) . '...',
                        'body_text' => $item['body_text'],
                        'body_html' => $item['body_html'],
                        'tag' => $tag,
                        'tag_color' => $tagColor,
                        'is_read' => false,
                        'is_starred' => false,
                        'has_attachment' => $item['has_attachment'] ?? false,
                        'message_id' => $item['message_id'],
                        'created_at' => Carbon::createFromTimestamp($item['timestamp']),
                        'received_at' => Carbon::createFromTimestamp($item['timestamp']),
                    ]);

                    $newInboxCount++;
                }
            }

            // 2. SINKRONISASI KOTAK KELUAR (SENT MAIL / TERKIRIM)
            $sentFolder = $client->detectSentFolder();
            if ($sentFolder) {
                $sentEmails = $client->fetchRecentEmailsFromFolder($sentFolder, 20);
                foreach ($sentEmails as $item) {
                    $exists = MailboxMessage::where('user_id', $user->id)
                        ->where('message_id', $item['message_id'])
                        ->exists();

                    if (!$exists) {
                        MailboxMessage::create([
                            'user_id' => $user->id,
                            'folder' => 'sent',
                            'status' => 'Delivered',
                            'sender_name' => $item['sender_name'] ?: $user->name,
                            'sender_email' => $item['sender_email'] ?: $user->email,
                            'recipient_name' => $item['recipient_name'],
                            'recipient_email' => $item['recipient_email'],
                            'cc' => $item['cc'] ?? null,
                            'bcc' => $item['bcc'] ?? null,
                            'subject' => $item['subject'],
                            'preview' => substr(strip_tags($item['body_text']), 0, 95) . '...',
                            'body_text' => $item['body_text'],
                            'body_html' => $item['body_html'],
                            'tag' => 'General / Sales',
                            'tag_color' => 'primary',
                            'is_read' => true,
                            'is_starred' => false,
                            'has_attachment' => $item['has_attachment'] ?? false,
                            'message_id' => $item['message_id'],
                            'created_at' => Carbon::createFromTimestamp($item['timestamp']),
                            'sent_at' => Carbon::createFromTimestamp($item['timestamp']),
                        ]);

                        $newSentCount++;
                    }
                }
            }

            $client->disconnect();
            $setting->update(['last_synced_at' => now()]);

            $totalNew = $newInboxCount + $newSentCount;
            if ($totalNew > 0) {
                $syncMessage = "Sinkronisasi server mail sukses! (+{$newInboxCount} Kotak Masuk, +{$newSentCount} Kotak Keluar).";
            } else {
                $syncMessage = "Mailbox tersinkron sempurna dengan server mail (Kotak Masuk & Kotak Keluar).";
            }

        } catch (\Exception $e) {
            Log::error("IMAP Full Sync Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Gagal menyinkronkan email dari server ({$host}): " . $e->getMessage(),
            ];
        }

        $totalInbox = MailboxMessage::where('user_id', $user->id)->where('folder', 'inbox')->count();
        $unreadInbox = MailboxMessage::where('user_id', $user->id)->where('folder', 'inbox')->where('is_read', false)->count();
        $totalSent = MailboxMessage::where('user_id', $user->id)->where('folder', 'sent')->count();
        $totalStarred = MailboxMessage::where('user_id', $user->id)->where('is_starred', true)->count();
        $totalTrash = MailboxMessage::where('user_id', $user->id)->where('folder', 'trash')->count();

        // Get latest email list for real-time frontend refresh
        $allMessages = MailboxMessage::with('attachments')
            ->where('user_id', $user->id)
            ->orderByRaw('COALESCE(received_at, sent_at, created_at) DESC')
            ->get()
            ->map(function ($msg) {
                $emailDate = ($msg->received_at ?: ($msg->sent_at ?: $msg->created_at))->copy()->setTimezone('Asia/Jakarta');
                return [
                    'id' => $msg->id,
                    'folder' => $msg->folder,
                    'sender_name' => $msg->sender_name,
                    'sender_email' => $msg->sender_email,
                    'recipient_name' => $msg->recipient_name,
                    'recipient_email' => $msg->recipient_email,
                    'subject' => $msg->subject,
                    'preview' => $msg->preview ?: substr(strip_tags($msg->body_text), 0, 80) . '...',
                    'body' => $msg->body_text,
                    'body_html' => $msg->body_html,
                    'tag' => $msg->tag ?: 'General / Sales',
                    'tag_color' => $msg->tag_color ?: 'primary',
                    'date' => $emailDate->translatedFormat('d M Y'),
                    'time' => $emailDate->format('H:i') . ' WIB',
                    'status' => $msg->status ?: ($msg->folder === 'sent' ? 'Delivered' : 'Received'),
                    'status_badge' => $msg->status === 'Delivered' || $msg->folder === 'sent' ? 'bg-success' : ($msg->status === 'Failed' ? 'bg-danger' : 'bg-primary'),
                    'has_attachment' => $msg->has_attachment,
                    'attachments' => $msg->attachments->map(function ($att) {
                        return [
                            'name' => $att->filename,
                            'size' => $att->file_size,
                            'ext' => $att->file_ext,
                            'url' => asset('storage/' . $att->file_path),
                        ];
                    })->toArray(),
                    'is_read' => $msg->is_read,
                    'is_starred' => $msg->is_starred,
                ];
            });

        return [
            'success' => true,
            'message' => $syncMessage,
            'new_inbox' => $newInboxCount,
            'new_sent' => $newSentCount,
            'total_inbox' => $totalInbox,
            'unread_inbox' => $unreadInbox,
            'total_sent' => $totalSent,
            'total_starred' => $totalStarred,
            'total_trash' => $totalTrash,
            'emails' => $allMessages,
            'last_synced' => now()->format('H:i') . ' WIB',
        ];
    }
}
