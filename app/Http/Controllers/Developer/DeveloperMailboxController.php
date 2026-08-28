<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMailSetting;
use App\Models\MailboxMessage;
use App\Services\MailboxService;
use App\Services\ImapSocketClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class DeveloperMailboxController extends Controller
{
    protected MailboxService $mailboxService;

    public function __construct(MailboxService $mailboxService)
    {
        $this->mailboxService = $mailboxService;
    }

    /**
     * Display developer mailbox management page.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isDeveloper()) {
            abort(403, 'Akses ditolak. Modul ini hanya untuk akun Developer.');
        }

        // 1. Fetch only users who have been configured for mailbox management
        $configuredSettings = UserMailSetting::whereNotNull('smtp_password')
            ->where('smtp_password', '!=', '')
            ->with(['user', 'user.mailboxMessages' => function ($q) {
                $q->select('id', 'user_id', 'folder', 'is_read');
            }])
            ->get();

        $configuredUsers = $configuredSettings
            ->filter(fn($s) => $s->user !== null)
            ->map(function ($s) {
                $u = $s->user;
                $inboxCount = $u->mailboxMessages->where('folder', 'inbox')->count();
                $sentCount = $u->mailboxMessages->where('folder', 'sent')->count();
                $unreadCount = $u->mailboxMessages->where('folder', 'inbox')->where('is_read', false)->count();

                $isConfigured = !empty($s->smtp_username) && !empty($s->imap_username);

                return [
                    'id' => $u->id,
                    'nip' => $u->nip,
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => $u->getRawOriginal('role'),
                    'image' => $u->image ? asset('storage/' . $u->image) : null,
                    'is_configured' => $isConfigured,
                    'is_active' => (bool)($s->is_active ?? true),
                    'smtp_host' => $s->smtp_host ?: 'srv162.niagahoster.com',
                    'smtp_port' => $s->smtp_port ?: 465,
                    'smtp_encryption' => $s->smtp_encryption ?: 'ssl',
                    'smtp_username' => $s->smtp_username ?: null,
                    'imap_host' => $s->imap_host ?: 'srv162.niagahoster.com',
                    'imap_port' => $s->imap_port ?: 993,
                    'imap_encryption' => $s->imap_encryption ?: 'ssl',
                    'imap_username' => $s->imap_username ?: null,
                    'from_name' => $s->from_name ?: $u->name,
                    'from_address' => $s->from_address ?: $u->email,
                    'has_password' => !empty($s->smtp_password),
                    'last_synced_at' => $s->last_synced_at ? $s->last_synced_at->timezone('Asia/Jakarta')->format('d M Y H:i') . ' WIB' : null,
                    'inbox_count' => $inboxCount,
                    'sent_count' => $sentCount,
                    'unread_count' => $unreadCount,
                    'total_messages' => $inboxCount + $sentCount,
                ];
            })
            ->values();

        // 2. Fetch all active users for the "Tambah Akun" dropdown selector
        $availableUsers = User::where('active', '1')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'nip' => $u->nip,
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => $u->getRawOriginal('role'),
                    'is_already_configured' => UserMailSetting::where('user_id', $u->id)->whereNotNull('smtp_username')->exists(),
                ];
            });

        $stats = [
            'total_configured' => $configuredUsers->count(),
            'active_configured' => $configuredUsers->where('is_active', true)->count(),
            'inactive_configured' => $configuredUsers->where('is_active', false)->count(),
            'total_employees' => $availableUsers->count(),
            'total_messages_db' => MailboxMessage::count(),
            'server_host' => 'srv162.niagahoster.com',
        ];

        return view('pages.developer.mailbox.index', compact('configuredUsers', 'availableUsers', 'stats'));
    }

    /**
     * Get single user setting JSON for edit modal.
     */
    public function getUserSetting($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isDeveloper()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::with('mailSetting')->find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }

        $setting = $user->mailSetting;
        $plainPassword = '';
        if ($setting && !empty($setting->smtp_password)) {
            try {
                $plainPassword = Crypt::decryptString($setting->smtp_password);
            } catch (\Exception $e) {
                $plainPassword = '';
            }
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRawOriginal('role'),
            ],
            'setting' => [
                'from_name' => $setting?->from_name ?: $user->name,
                'from_address' => $setting?->from_address ?: $user->email,
                'smtp_host' => $setting?->smtp_host ?: 'srv162.niagahoster.com',
                'smtp_port' => $setting?->smtp_port ?: 465,
                'smtp_encryption' => $setting?->smtp_encryption ?: 'ssl',
                'smtp_username' => $setting?->smtp_username ?: $user->email,
                'smtp_password' => $plainPassword,
                'imap_host' => $setting?->imap_host ?: 'srv162.niagahoster.com',
                'imap_port' => $setting?->imap_port ?: 993,
                'imap_encryption' => $setting?->imap_encryption ?: 'ssl',
                'imap_username' => $setting?->imap_username ?: $user->email,
                'signature_layout' => $setting?->signature_layout ?: 'sig_corporate',
                'signature_color' => $setting?->signature_color ?: '#696cff',
            ]
        ]);
    }

    /**
     * Save or update user mailbox configuration.
     */
    public function saveSetting(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isDeveloper()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'from_name' => 'required|string|max:100',
            'from_address' => 'required|email|max:100',
            'smtp_host' => 'required|string|max:150',
            'smtp_port' => 'required|numeric',
            'smtp_encryption' => 'required|in:ssl,tls',
            'smtp_username' => 'required|string|max:150',
            'imap_host' => 'required|string|max:150',
            'imap_port' => 'required|numeric',
            'imap_encryption' => 'required|in:ssl,tls',
            'imap_username' => 'required|string|max:150',
        ]);

        $user = User::findOrFail($request->input('user_id'));

        $setting = UserMailSetting::firstOrNew(['user_id' => $user->id]);
        $setting->from_name = $request->input('from_name');
        $setting->from_address = $request->input('from_address');
        $setting->mail_driver = 'smtp';
        $setting->smtp_host = $request->input('smtp_host');
        $setting->smtp_port = $request->input('smtp_port');
        $setting->smtp_encryption = $request->input('smtp_encryption');
        $setting->smtp_username = $request->input('smtp_username');

        if ($request->filled('smtp_password')) {
            $setting->smtp_password = Crypt::encryptString($request->input('smtp_password'));
        }

        $setting->imap_host = $request->input('imap_host');
        $setting->imap_port = $request->input('imap_port');
        $setting->imap_encryption = $request->input('imap_encryption');
        $setting->imap_username = $request->input('imap_username');

        if ($request->filled('imap_password')) {
            $setting->imap_password = Crypt::encryptString($request->input('imap_password'));
        } elseif ($request->filled('smtp_password')) {
            $setting->imap_password = Crypt::encryptString($request->input('smtp_password'));
        }

        if ($request->filled('signature_layout')) {
            $setting->signature_layout = $request->input('signature_layout');
        }
        if ($request->filled('signature_color')) {
            $setting->signature_color = $request->input('signature_color');
        }

        $setting->save();

        return response()->json([
            'success' => true,
            'message' => "Konfigurasi Mailbox untuk {$user->name} ({$setting->from_address}) berhasil disimpan!"
        ]);
    }

    /**
     * Test handshake connection before saving.
     */
    public function testConnection(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isDeveloper()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|numeric',
            'smtp_encryption' => 'required|in:ssl,tls',
            'smtp_username' => 'required|string',
            'smtp_password' => 'required|string',
            'imap_host' => 'required|string',
            'imap_port' => 'required|numeric',
            'imap_encryption' => 'required|in:ssl,tls',
            'imap_username' => 'required|string',
        ]);

        $smtpHost = $request->input('smtp_host');
        $smtpPort = (int)$request->input('smtp_port');
        $smtpEnc = $request->input('smtp_encryption');
        $smtpUser = $request->input('smtp_username');
        $smtpPass = $request->input('smtp_password');

        $imapHost = $request->input('imap_host');
        $imapPort = (int)$request->input('imap_port');
        $imapEnc = $request->input('imap_encryption');
        $imapUser = $request->input('imap_username');
        $imapPass = $request->input('smtp_password');

        // 1. Test SMTP
        $smtpResult = $this->mailboxService->testSmtpConnection($smtpHost, $smtpPort, $smtpEnc, $smtpUser, $smtpPass);
        if (!$smtpResult['success']) {
            return response()->json([
                'success' => false,
                'message' => "SMTP Error: " . $smtpResult['message']
            ], 422);
        }

        // 2. Test IMAP
        try {
            $imapClient = new ImapSocketClient();
            $imapClient->connect($imapHost, $imapPort, $imapEnc, 10);
            $imapClient->login($imapUser, $imapPass);
            $sentFolder = $imapClient->detectSentFolder();
            $imapClient->disconnect();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "IMAP Error: " . $e->getMessage()
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Koneksi SMTP & IMAP Berhasil! Handshake sukses terhubung ke {$smtpHost}:{$smtpPort} dan {$imapHost}:{$imapPort}."
        ]);
    }

    /**
     * Trigger sync for specific user mailbox directly from developer panel.
     */
    public function syncUser(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isDeveloper()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        $res = $this->mailboxService->syncIncomingEmails($user);

        return response()->json($res);
    }

    /**
     * Delete/reset mailbox setting for a user.
     */
    public function deleteSetting(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isDeveloper()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        if ($user->mailSetting) {
            $user->mailSetting->delete();
        }

        return response()->json([
            'success' => true,
            'message' => "Konfigurasi mailbox untuk {$user->name} berhasil di-reset!"
        ]);
    }

    /**
     * Toggle active/inactive mailbox status for a specific user.
     */
    public function toggleActive(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isDeveloper()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        $setting = UserMailSetting::firstOrNew(['user_id' => $user->id]);

        if (!$setting->exists) {
            $setting->mail_driver = 'smtp';
            $setting->smtp_host = 'srv162.niagahoster.com';
            $setting->smtp_port = 465;
            $setting->smtp_encryption = 'ssl';
            $setting->smtp_username = $user->email;
            $setting->imap_host = 'srv162.niagahoster.com';
            $setting->imap_port = 993;
            $setting->imap_encryption = 'ssl';
            $setting->imap_username = $user->email;
            $setting->from_name = $user->name;
            $setting->from_address = $user->email;
            $setting->is_active = true;
        } else {
            $setting->is_active = !$setting->is_active;
        }
        $setting->save();

        $statusText = $setting->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json([
            'success' => true,
            'is_active' => (bool)$setting->is_active,
            'message' => "Menu Mailbox untuk {$user->name} berhasil {$statusText}!"
        ]);
    }
}
