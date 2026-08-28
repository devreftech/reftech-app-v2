<?php

namespace App\Http\Controllers;

use App\Models\MailboxMessage;
use App\Models\UserMailSetting;
use App\Services\MailboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MailboxController extends Controller
{
    protected $mailboxService;

    public function __construct(MailboxService $mailboxService)
    {
        $this->mailboxService = $mailboxService;
    }

    /**
     * Display the Webmail & Signature Studio Hub for the logged-in user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userName = $user ? $user->name : 'Sales Executive';
        $userEmail = $user ? $user->email : 'sales@reftech.id';
        $userPhone = $user && isset($user->phone) ? $user->phone : '+62 812-8888-9999';
        $userTitle = 'Senior Sales Executive';

        // Load user mail settings (only if configured with credentials or developer)
        $mailSetting = null;
        if ($user) {
            $mailSetting = UserMailSetting::where('user_id', $user->id)
                ->whereNotNull('smtp_password')
                ->where('smtp_password', '!=', '')
                ->first();

            if (!$mailSetting && !$user->isDeveloper()) {
                abort(403, 'Akses Mailbox belum dikonfigurasi untuk akun Anda. Silakan hubungi Developer / IT Administrator.');
            }
            if (!$mailSetting && $user->isDeveloper()) {
                $mailSetting = new UserMailSetting([
                    'user_id' => $user->id,
                    'mail_driver' => 'smtp',
                    'smtp_host' => 'srv162.niagahoster.com',
                    'smtp_port' => 465,
                    'smtp_encryption' => 'ssl',
                    'smtp_username' => $userEmail,
                    'imap_host' => 'srv162.niagahoster.com',
                    'imap_port' => 993,
                    'imap_encryption' => 'ssl',
                    'imap_username' => $userEmail,
                    'from_name' => $userName,
                    'from_address' => $userEmail,
                    'signature_layout' => 'sig_corporate',
                    'signature_color' => '#696cff',
                ]);
            }
        }

        // Load messages from database
        $dbMessages = $user
            ? MailboxMessage::with('attachments')
                ->where('user_id', $user->id)
                ->orderByRaw('COALESCE(received_at, sent_at, created_at) DESC')
                ->get()
            : collect([]);

        // Format for frontend
        $emails = $dbMessages->map(function ($msg) {
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
                'status' => $msg->status ?: 'Received',
                'status_badge' => $msg->status === 'Delivered' ? 'bg-success' : ($msg->status === 'Failed' ? 'bg-danger' : 'bg-primary'),
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
        })->toArray();

        // Calculate statistics
        $stats = [
            'total_inbox' => $dbMessages->where('folder', 'inbox')->count(),
            'unread_inbox' => $dbMessages->where('folder', 'inbox')->where('is_read', false)->count(),
            'total_sent' => $dbMessages->where('folder', 'sent')->count(),
            'total_starred' => $dbMessages->where('is_starred', true)->count(),
            'total_draft' => $dbMessages->where('folder', 'draft')->count(),
            'total_trash' => $dbMessages->where('folder', 'trash')->count(),
        ];

        // Business Email Templates
        $templates = [
            [
                'id' => 'blank',
                'name' => '✉️ Tulis Pesan Kosong / Bebas',
                'badge' => 'Manual',
                'badge_color' => 'secondary',
                'subject' => '',
                'body' => "Halo Bapak/Ibu,\n\n[Tulis isi pesan Anda di sini]",
            ],
            [
                'id' => 'intro_server',
                'name' => '🏢 Perkenalan Solusi Server & Hardware Refurbished',
                'badge' => 'Leads Intro',
                'badge_color' => 'primary',
                'subject' => 'Perkenalan Solusi Server, Storage & Hardware Bergaransi Resmi - PT Reftech Indonesia',
                'body' => "Yth. {client_name},\n{company_name}\n\nSalam hangat dari PT Reftech Indonesia.\n\nPerkenalkan saya {sales_name} dari Reftech. Kami adalah penyedia solusi IT Hardware terkemuka yang berfokus pada Enterprise Server (Dell EMC, HPE ProLiant, Lenovo ThinkSystem), Storage, Workstation, serta PC Refurbished Berkualitas Grade-A dengan Garansi Resmi hingga 1 Tahun.\n\nMelalui solusi kami, banyak mitra perusahaan dapat menghemat anggaran pengadaan IT hingga 40-60% tanpa mengorbankan performa dan keandalan sistem.\n\nApakah kami dapat menjadwalkan sesi diskusi singkat / demo unit selama 15 menit minggu ini untuk membahas kebutuhan infrastruktur IT di {company_name}?\n\nTerima kasih atas perhatian Bapak/Ibu.",
            ],
            [
                'id' => 'quotation_send',
                'name' => '💼 Pengiriman Surat Penawaran Harga (Quotation)',
                'badge' => 'Penawaran',
                'badge_color' => 'info',
                'subject' => 'Penawaran Harga Resmi Pengadaan Server & Hardware - PT Reftech Indonesia',
                'body' => "Yth. {client_name},\n{company_name}\n\nMenindaklanjuti komunikasi dan kebutuhan yang telah disampaikan sebelumnya, bersama email ini kami lampirkan dokumen Resmi Penawaran Harga (Quotation) untuk kebutuhan perangkat IT di {company_name}.\n\nRingkasan Penawaran:\n- Unit terlampir telah melalui proses QC 24 Tahap (Full Stress Test Hardware)\n- Garansi Unit: 1 Tahun Full Replacement & On-Site Support\n- Estimasi Pengiriman: Ready Stock (1-3 hari kerja setelah PO diterbitkan)\n\nMohon dapat memeriksa dokumen penawaran pada lampiran email ini. Jika ada spesifikasi yang ingin disesuaikan atau dinegosiasikan, jangan ragu untuk menghubungi kami.\n\nSalam sukses,",
            ],
            [
                'id' => 'invoice_billing',
                'name' => '💳 Penagihan Invoice / Pengingat Jatuh Tempo',
                'badge' => 'Tagihan & Invoice',
                'badge_color' => 'danger',
                'subject' => 'Pemberitahuan Invoice & Pengingat Pembayaran - PT Reftech Indonesia',
                'body' => "Yth. Departemen Finance / {client_name},\n{company_name}\n\nSemoga email ini menjumpai Anda dalam keadaan baik.\n\nKami menginformasikan bahwa Invoice terkait pengadaan perangkat IT di {company_name} akan segera jatuh tempo dalam waktu dekat. Bersama email ini kami lampirkan salinan invoice dan nomor rekening resmi PT Reftech Indonesia.\n\nDetail Pembayaran:\n- Bank: BCA / Mandiri\n- Atas Nama: PT Reftech Indonesia\n\nApabila pembayaran telah dilakukan, mohon dapat membalas email ini dengan melampirkan bukti transfer untuk segera kami verifikasi.\n\nTerima kasih atas kerja sama yang baik.",
            ],
            [
                'id' => 'followup_leads',
                'name' => '⏳ Follow-Up Prospek / Penawaran Sebelumnya',
                'badge' => 'Follow-Up',
                'badge_color' => 'warning',
                'subject' => 'Follow-Up Penawaran Solusi Hardware IT - PT Reftech Indonesia',
                'body' => "Halo {client_name},\n\nSemoga aktivitas hari ini berjalan lancar.\n\nSaya ingin menindaklanjuti email dan penawaran yang kami kirimkan beberapa hari lalu mengenai kebutuhan pengadaan perangkat IT di {company_name}.\n\nApakah dokumen penawaran tersebut sudah sempat ditinjau oleh tim internal atau manajemen? Jika ada pertanyaan tambahan mengenai spesifikasi, SLA pengiriman, atau skema pembayaran, saya siap membantu kapan saja.\n\nTerima kasih banyak!",
            ],
        ];

        // Quick contacts
        $quickContacts = [
            ['name' => 'Michael Chen', 'company' => 'PT Global Mega Teknologi', 'email' => 'm.chen@globalmegatech.co.id'],
            ['name' => 'Budi Santoso', 'company' => 'PT Mitra Solusi Integrasi', 'email' => 'budi.santoso@msi-corp.id'],
            ['name' => 'Siti Rahmawati', 'company' => 'CV Cahaya Abadi Network', 'email' => 'siti.rahmawati@cahaya-abadi.com'],
            ['name' => 'David Pratama', 'company' => 'PT Surya Digital Pratama', 'email' => 'david.p@suryadigital.com'],
        ];

        return view('pages.sales.mailbox.index', compact(
            'emails',
            'templates',
            'stats',
            'userName',
            'userEmail',
            'userPhone',
            'userTitle',
            'quickContacts',
            'mailSetting'
        ));
    }

    /**
     * Send email via AJAX using dynamic SMTP.
     */
    public function send(Request $request)
    {
        $request->validate([
            'recipient_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak terautentikasi.'], 401);
        }

        $files = $request->hasFile('attachments') ? $request->file('attachments') : [];

        $payload = [
            'recipient_email' => $request->input('recipient_email'),
            'recipient_name' => $request->input('recipient_name'),
            'subject' => $request->input('subject'),
            'body' => $request->input('body'),
            'cc' => $request->input('cc'),
            'bcc' => $request->input('bcc'),
            'signature_html' => $request->input('signature_html'),
            'include_signature' => $request->boolean('include_signature', true),
            'tag' => $request->input('tag', 'General / Sales'),
        ];

        $result = $this->mailboxService->sendEmail($user, $payload, $files);

        return response()->json($result);
    }

    /**
     * Save user's custom SMTP, IMAP & Signature settings.
     */
    public function saveSettings(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak terautentikasi.'], 401);
        }

        $data = [
            'mail_driver' => 'smtp',
            'smtp_host' => $request->input('smtp_host', 'smtp.gmail.com'),
            'smtp_port' => (int) $request->input('smtp_port', 587),
            'smtp_encryption' => $request->input('smtp_encryption', 'tls'),
            'smtp_username' => $request->input('smtp_username'),
            'from_name' => $request->input('from_name', $user->name),
            'from_address' => $request->input('from_address', $user->email),
            'signature_layout' => $request->input('signature_layout', 'sig_corporate'),
            'signature_color' => $request->input('signature_color', '#696cff'),
            'signature_html' => $request->input('signature_html'),
            'is_active' => true,
        ];

        // Only update password if provided
        if ($request->filled('smtp_password')) {
            $data['smtp_password'] = $request->input('smtp_password');
        }

        if ($request->filled('imap_username')) {
            $data['imap_username'] = $request->input('imap_username');
            $data['imap_host'] = $request->input('imap_host', 'imap.gmail.com');
            $data['imap_port'] = (int) $request->input('imap_port', 993);
            $data['imap_encryption'] = $request->input('imap_encryption', 'ssl');
        }

        if ($request->filled('imap_password')) {
            $data['imap_password'] = $request->input('imap_password');
        }

        $setting = UserMailSetting::updateOrCreate(['user_id' => $user->id], $data);

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi SMTP dan Signature berhasil disimpan dengan enkripsi aman!',
            'data' => $setting,
        ]);
    }

    /**
     * Test SMTP connection handshake.
     */
    public function testConnection(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak terautentikasi.'], 401);
        }

        $setting = UserMailSetting::firstOrNew(['user_id' => $user->id]);
        $setting->smtp_host = $request->input('smtp_host', $setting->smtp_host ?: 'smtp.gmail.com');
        $setting->smtp_port = (int) $request->input('smtp_port', $setting->smtp_port ?: 587);
        $setting->smtp_encryption = $request->input('smtp_encryption', $setting->smtp_encryption ?: 'tls');
        $setting->smtp_username = $request->input('smtp_username', $setting->smtp_username);

        if ($request->filled('smtp_password')) {
            $setting->smtp_password = $request->input('smtp_password');
        }

        $result = $this->mailboxService->testSmtpConnection($setting);

        return response()->json($result);
    }

    /**
     * Sync mailbox messages.
     */
    public function sync(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak terautentikasi.'], 401);
        }

        $result = $this->mailboxService->syncIncomingEmails($user);

        return response()->json($result);
    }

    /**
     * Toggle starred status of a message.
     */
    public function toggleStar(Request $request)
    {
        $user = Auth::user();
        $message = MailboxMessage::where('id', $request->input('id'))->where('user_id', $user->id)->first();

        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Pesan tidak ditemukan.'], 404);
        }

        $message->is_starred = !$message->is_starred;
        $message->save();

        return response()->json(['success' => true, 'is_starred' => $message->is_starred]);
    }

    /**
     * Mark message as read.
     */
    public function markRead(Request $request)
    {
        $user = Auth::user();
        $message = MailboxMessage::where('id', $request->input('id'))->where('user_id', $user->id)->first();

        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Pesan tidak ditemukan.'], 404);
        }

        $message->is_read = true;
        $message->save();

        return response()->json(['success' => true, 'is_read' => true]);
    }

    /**
     * Delete / move message to trash.
     */
    public function deleteMessage(Request $request)
    {
        $user = Auth::user();
        $message = MailboxMessage::where('id', $request->input('id'))->where('user_id', $user->id)->first();

        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Pesan tidak ditemukan.'], 404);
        }

        if ($message->folder === 'trash') {
            $message->delete();
            return response()->json(['success' => true, 'message' => 'Pesan dihapus permanen.']);
        } else {
            $message->folder = 'trash';
            $message->status = 'Trash';
            $message->save();
            return response()->json(['success' => true, 'message' => 'Pesan dipindahkan ke Sampah.']);
        }
    }

    /**
     * Seed initial sample mailbox messages for new user demo.
     */
    protected function seedInitialMessagesForUser($userId, $userName, $userEmail)
    {
        MailboxMessage::create([
            'user_id' => $userId,
            'folder' => 'inbox',
            'status' => 'Received',
            'sender_name' => 'Michael Chen',
            'sender_email' => 'm.chen@globalmegatech.co.id',
            'recipient_name' => $userName,
            'recipient_email' => $userEmail,
            'subject' => 'Permintaan Informasi Sewa & Beli Server HP ProLiant Gen10',
            'preview' => 'Halo tim sales Reftech, kami sedang membutuhkan penawaran pengadaan 4 unit Server HP ProLiant DL380...',
            'body_text' => "Halo tim sales Reftech,\n\nKami dari PT Global Mega Teknologi sedang mencari vendor penyedia server enterprise untuk data center kantor cabang baru kami di Cikarang.\n\nKebutuhan spesifikasi kami:\n- 4 Unit Server HP ProLiant DL380 Gen10 (Dual Xeon Gold, RAM 128GB, 4x 1.2TB SAS 10K)\n- Layanan garansi SLA 4 jam penggantian spare part\n\nMohon dikirimkan surat penawaran resmi beserta opsi skema sewa dan beli putus. Kami tunggu kabarnya paling lambat hari Jumat ini.\n\nTerima kasih,\nMichael Chen\nIT Infrastructure Head - PT Global Mega Teknologi",
            'tag' => 'Inquiry Baru',
            'tag_color' => 'success',
            'is_read' => false,
            'is_starred' => true,
            'has_attachment' => false,
            'created_at' => now()->subMinutes(35),
        ]);

        MailboxMessage::create([
            'user_id' => $userId,
            'folder' => 'inbox',
            'status' => 'Received',
            'sender_name' => 'Budi Santoso',
            'sender_email' => 'budi.santoso@msi-corp.id',
            'recipient_name' => $userName,
            'recipient_email' => $userEmail,
            'subject' => 'Re: Penawaran Harga Pengadaan Switch Cisco Catalyst & Server Dell R740',
            'preview' => 'Selamat siang Pak. Kami sudah meninjau dokumen penawaran yang bapak kirimkan...',
            'body_text' => "Selamat siang Pak,\n\nKami sudah meninjau dokumen penawaran harga yang Bapak kirimkan kemarin. Secara spesifikasi teknis sudah sesuai dengan kebutuhan data center kami.\n\nApakah ada diskon tambahan untuk pembayaran tempo 30 hari (TOP 30) jika kami PO sekaligus 6 unit?\n\nMohon update dokumen penawarannya ya Pak agar bisa segera kami ajukan ke Direksi Keuangan.\n\nSalam,\nBudi Santoso\nProcurement Manager - PT Mitra Solusi Integrasi",
            'tag' => 'Penawaran',
            'tag_color' => 'info',
            'is_read' => true,
            'is_starred' => false,
            'has_attachment' => false,
            'created_at' => now()->subHours(3),
        ]);
    }
}
