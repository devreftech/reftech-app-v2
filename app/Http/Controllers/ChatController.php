<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    /**
     * Helper to get user online presence status.
     * - Online (Hijau): Active within last 5 minutes
     * - Away (Orange): Inactive for > 30 minutes
     * - Offline (Putih/Abu-abu): Not active / no recent session
     */
    public static function getUserPresence($userId): array
    {
        $lastActivityTimestamp = Cache::get('user_last_activity_' . $userId);

        if (!$lastActivityTimestamp) {
            return [
                'status' => 'offline',
                'color' => '#a1a5b7',
                'label' => 'Offline',
                'badge_class' => 'rf-status-offline',
                'last_seen_text' => 'Offline',
            ];
        }

        $now = now()->timestamp;
        $diffSeconds = max(0, $now - $lastActivityTimestamp);
        $diffMinutes = (int) round($diffSeconds / 60);

        // 1. Hijau: Online & aktif (<= 5 menit lalu)
        if ($diffMinutes <= 5) {
            return [
                'status' => 'online',
                'color' => '#71dd37',
                'label' => 'Online',
                'badge_class' => 'rf-status-online',
                'last_seen_text' => 'Online',
            ];
        }

        // 2. Orange: Login tapi > 30 menit tidak ada aktivitas (Zzzz)
        if ($diffMinutes > 30 && $diffMinutes <= 720) {
            $hours = floor($diffMinutes / 60);
            $mins = $diffMinutes % 60;
            $timeText = $hours > 0 ? "{$hours} jam lalu" : "{$mins} mnt lalu";
            return [
                'status' => 'away',
                'color' => '#ffab00',
                'label' => 'Zzzz (>30 mnt)',
                'badge_class' => 'rf-status-away',
                'last_seen_text' => 'Zzzz (' . $timeText . ')',
            ];
        }

        if ($diffMinutes > 5 && $diffMinutes <= 30) {
            return [
                'status' => 'away',
                'color' => '#ffab00',
                'label' => 'Zzzz',
                'badge_class' => 'rf-status-away',
                'last_seen_text' => 'Zzzz (' . $diffMinutes . ' mnt lalu)',
            ];
        }

        // 3. Putih / Abu-abu: Offline (> 12 jam)
        $carbonLast = Carbon::createFromTimestamp($lastActivityTimestamp, 'Asia/Jakarta');
        $lastSeenDate = $carbonLast->isToday()
            ? 'Hari ini ' . $carbonLast->format('H:i')
            : ($carbonLast->isYesterday() ? 'Kemarin ' . $carbonLast->format('H:i') : $carbonLast->format('d/m/y H:i'));

        return [
            'status' => 'offline',
            'color' => '#a1a5b7',
            'label' => 'Offline',
            'badge_class' => 'rf-status-offline',
            'last_seen_text' => 'Terakhir online ' . $lastSeenDate,
        ];
    }
    /**
     * Get total unread messages count for the logged-in user.
     */
    public function getUnreadCount()
    {
        $myId = Auth::id();
        if (!$myId) {
            return response()->json(['unread_count' => 0, 'last_id' => 0]);
        }

        $count = ChatMessage::where('receiver_id', $myId)
            ->where('is_read', false)
            ->count();

        $lastId = ChatMessage::where('receiver_id', $myId)->max('id') ?? 0;

        return response()->json([
            'status' => 'success',
            'unread_count' => $count,
            'last_id' => $lastId,
        ]);
    }

    /**
     * Get list of contacts (all active users) with last message and unread count.
     */
    public function getContacts(Request $request)
    {
        $myId = Auth::id();
        if (!$myId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $excludedNames = [
            'Derry',
            'Eri Kurnia',
            'Bre Kautsar',
            'Ervina',
            'Hadi',
            'Nada',
            'Ramdani',
            'User',
            'Prokemas',
        ];

        $excludedIds = [14, 15, 23, 24, 25, 26, 27, 28, 33];

        // Get users except current user and excluded list
        $users = User::where('id', '!=', $myId)
            ->whereNotIn('id', $excludedIds)
            ->whereNotIn('name', $excludedNames)
            ->select('id', 'name', 'email', 'role', 'image', 'active')
            ->get();

        // Get unread counts grouped by sender
        $unreadCounts = ChatMessage::where('receiver_id', $myId)
            ->where('is_read', false)
            ->groupBy('sender_id')
            ->select('sender_id', DB::raw('count(*) as total'))
            ->pluck('total', 'sender_id')
            ->toArray();

        // Get last messages between myId and other users
        $allLatestMessages = ChatMessage::where(function ($q) use ($myId) {
            $q->where('sender_id', $myId)->orWhere('receiver_id', $myId);
        })
        ->orderBy('created_at', 'desc')
        ->get();

        $latestByUser = [];
        foreach ($allLatestMessages as $msg) {
            $otherId = ($msg->sender_id == $myId) ? $msg->receiver_id : $msg->sender_id;
            if (!isset($latestByUser[$otherId])) {
                $latestByUser[$otherId] = $msg;
            }
        }

        $contacts = $users->map(function ($user) use ($unreadCounts, $latestByUser, $myId) {
            $lastMsg = $latestByUser[$user->id] ?? null;
            $unread = $unreadCounts[$user->id] ?? 0;

            // Generate initials
            $names = explode(' ', trim($user->name));
            $initials = '';
            if (count($names) >= 2) {
                $initials = strtoupper(substr($names[0], 0, 1) . substr($names[1], 0, 1));
            } elseif (count($names) == 1 && strlen($names[0]) > 0) {
                $initials = strtoupper(substr($names[0], 0, min(2, strlen($names[0]))));
            } else {
                $initials = 'US';
            }

            // Role styling helpers
            $role = $user->role ?: 'Staff';
            $roleClass = 'rf-role-sales';
            $avatarColor = '#666cff';

            $roleLower = strtolower($role);
            if (str_contains($roleLower, 'tech') || str_contains($roleLower, 'teknisi') || str_contains($roleLower, 'service')) {
                $roleClass = 'rf-role-tech';
                $avatarColor = '#71dd37';
            } elseif (str_contains($roleLower, 'finance') || str_contains($roleLower, 'account')) {
                $roleClass = 'rf-role-finance';
                $avatarColor = '#ffab00';
            } elseif (str_contains($roleLower, 'wh') || str_contains($roleLower, 'warehouse') || str_contains($roleLower, 'gudang') || str_contains($roleLower, 'logistic')) {
                $roleClass = 'rf-role-wh';
                $avatarColor = '#03c3ec';
            } elseif (str_contains($roleLower, 'admin') || str_contains($roleLower, 'developer')) {
                $roleClass = 'rf-role-admin';
                $avatarColor = '#ff3e1d';
            }

            // Avatar URL
            $avatarUrl = null;
            if ($user->image) {
                if (str_starts_with($user->image, 'http://') || str_starts_with($user->image, 'https://')) {
                    $avatarUrl = $user->image;
                } else {
                    $avatarUrl = asset('storage/' . $user->image);
                }
            }

            // Last message snippet
            $lastMsgSnippet = 'Belum ada percakapan';
            $lastMsgTime = '';
            $sortTimestamp = 0;

            if ($lastMsg) {
                if ($lastMsg->message) {
                    $lastMsgSnippet = ($lastMsg->sender_id == $myId ? 'Anda: ' : '') . $lastMsg->message;
                } elseif ($lastMsg->attachment) {
                    $lastMsgSnippet = ($lastMsg->sender_id == $myId ? 'Anda mengirim ' : 'Mengirim ') . ($lastMsg->attachment_type === 'image' ? '🖼️ Gambar' : '📎 Dokumen');
                }

                $createdAt = $lastMsg->created_at->setTimezone('Asia/Jakarta');
                $sortTimestamp = $createdAt->timestamp;
                if ($createdAt->isToday()) {
                    $lastMsgTime = $createdAt->format('H:i');
                } elseif ($createdAt->isYesterday()) {
                    $lastMsgTime = 'Kemarin';
                } else {
                    $lastMsgTime = $createdAt->format('d/m/y');
                }
            }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $role,
                'role_class' => $roleClass,
                'avatar_color' => $avatarColor,
                'avatar_text' => $initials,
                'avatar_url' => $avatarUrl,
                'unread_count' => $unread,
                'last_message' => $lastMsgSnippet,
                'last_message_time' => $lastMsgTime,
                'sort_timestamp' => $sortTimestamp,
                'presence' => self::getUserPresence($user->id),
            ];
        });

        // Sort contacts: highest unread first, then latest message time, then alphabetical name
        $sorted = $contacts->sortByDesc(function ($c) {
            return ($c['unread_count'] > 0 ? 10000000000 : 0) + $c['sort_timestamp'];
        })->values();

        return response()->json([
            'status' => 'success',
            'contacts' => $sorted,
            'my_id' => $myId,
        ]);
    }

    /**
     * Get chat conversation history with a specific user.
     */
    public function getMessages(Request $request, $userId)
    {
        $myId = Auth::id();
        if (!$myId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $targetUser = User::select('id', 'name', 'role')->find($userId);
        if (!$targetUser) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        // Fast mark incoming messages from targetUser as read
        ChatMessage::where('sender_id', $userId)
            ->where('receiver_id', $myId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => Carbon::now('Asia/Jakarta'),
            ]);

        // Fetch last 60 messages between the two users sorted by indexed primary key
        $messages = ChatMessage::where(function ($q) use ($myId, $userId) {
            $q->where('sender_id', $myId)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($myId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $myId);
        })
        ->select('id', 'sender_id', 'receiver_id', 'message', 'attachment', 'attachment_name', 'attachment_type', 'is_read', 'created_at')
        ->orderBy('id', 'desc')
        ->take(60)
        ->get()
        ->reverse()
        ->values();

        $formattedMessages = $messages->map(function ($msg) use ($myId) {
            $created = $msg->created_at ? $msg->created_at->setTimezone('Asia/Jakarta') : null;
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'is_outgoing' => ($msg->sender_id == $myId),
                'message' => $msg->message,
                'attachment_url' => $msg->attachment_url,
                'attachment_name' => $msg->attachment_name,
                'attachment_type' => $msg->attachment_type,
                'is_read' => (bool)$msg->is_read,
                'time' => $created ? $created->format('H:i') : '',
                'date' => $created ? $created->format('Y-m-d') : '',
                'date_label' => $created ? ($created->isToday() ? 'Hari ini' : ($created->isYesterday() ? 'Kemarin' : $created->format('d M Y'))) : '',
            ];
        });

        return response()->json([
            'status' => 'success',
            'target_user' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'role' => $targetUser->role ?: 'Staff',
                'presence' => self::getUserPresence($targetUser->id),
            ],
            'messages' => $formattedMessages,
        ]);
    }

    /**
     * Send a new message to a user (with optional file/image attachment).
     */
    public function sendMessage(Request $request)
    {
        $myId = Auth::id();
        if (!$myId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|max:10240', // max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if (empty($request->message) && !$request->hasFile('attachment')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesan atau lampiran tidak boleh kosong.',
            ], 422);
        }

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $mime = $file->getMimeType();
            $attachmentType = str_starts_with($mime, 'image/') ? 'image' : 'document';
            $attachmentPath = $file->store('chat_attachments', 'public');
        }

        $chatMessage = ChatMessage::create([
            'sender_id' => $myId,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'attachment' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
            'is_read' => false,
        ]);

        $created = $chatMessage->created_at ? $chatMessage->created_at->setTimezone('Asia/Jakarta') : Carbon::now('Asia/Jakarta');

        return response()->json([
            'status' => 'success',
            'message_data' => [
                'id' => $chatMessage->id,
                'sender_id' => $chatMessage->sender_id,
                'receiver_id' => $chatMessage->receiver_id,
                'is_outgoing' => true,
                'message' => $chatMessage->message,
                'attachment_url' => $chatMessage->attachment_url,
                'attachment_name' => $chatMessage->attachment_name,
                'attachment_type' => $chatMessage->attachment_type,
                'is_read' => false,
                'time' => $created->format('H:i'),
                'date' => $created->format('Y-m-d'),
                'date_label' => 'Hari ini',
            ],
        ]);
    }

    /**
     * Mark all unread messages from a specific sender as read.
     */
    public function markAsRead(Request $request, $userId)
    {
        $myId = Auth::id();
        if (!$myId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        ChatMessage::where('sender_id', $userId)
            ->where('receiver_id', $myId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => Carbon::now('Asia/Jakarta'),
            ]);

        $totalUnread = ChatMessage::where('receiver_id', $myId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => 'success',
            'total_unread' => $totalUnread,
        ]);
    }

    /**
     * Delta-polling endpoint to check for incoming new messages.
     */
    public function poll(Request $request)
    {
        $myId = Auth::id();
        if (!$myId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $lastId = (int)$request->input('last_id', 0);
        $activeUserId = $request->input('active_user_id');

        // If last_id is 0 (initial poll upon page load), establish baseline cursor without returning old historical messages as "new"
        if ($lastId === 0) {
            $currentMaxId = ChatMessage::where('receiver_id', $myId)->max('id') ?? 0;
            $totalUnread = ChatMessage::where('receiver_id', $myId)
                ->where('is_read', false)
                ->count();

            $targetUserPresence = null;
            if ($activeUserId) {
                $targetUserPresence = self::getUserPresence($activeUserId);
            }

            return response()->json([
                'status' => 'success',
                'last_id' => $currentMaxId,
                'new_messages' => [],
                'read_message_ids' => [],
                'total_unread' => $totalUnread,
                'target_user_presence' => $targetUserPresence,
            ]);
        }

        // Query new incoming messages arrived after lastId
        $query = ChatMessage::where('receiver_id', $myId)
            ->where('id', '>', $lastId)
            ->orderBy('id', 'asc');

        $newIncoming = $query->get();

        // If user is currently looking at active conversation, mark those specific incoming messages as read
        if ($activeUserId && $newIncoming->count() > 0) {
            $activeIncomingIds = $newIncoming->where('sender_id', $activeUserId)->pluck('id');
            if ($activeIncomingIds->count() > 0) {
                ChatMessage::whereIn('id', $activeIncomingIds)->update([
                    'is_read' => true,
                    'read_at' => Carbon::now('Asia/Jakarta'),
                ]);
            }
        }

        // Query status updates for outgoing messages (e.g. mark as read by receiver)
        $readUpdates = [];
        if ($activeUserId) {
            $readUpdates = ChatMessage::where('sender_id', $myId)
                ->where('receiver_id', $activeUserId)
                ->where('is_read', true)
                ->pluck('id')
                ->toArray();
        }

        $totalUnread = ChatMessage::where('receiver_id', $myId)
            ->where('is_read', false)
            ->count();

        $formatted = $newIncoming->map(function ($msg) use ($myId) {
            $created = $msg->created_at ? $msg->created_at->setTimezone('Asia/Jakarta') : null;
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'is_outgoing' => ($msg->sender_id == $myId),
                'message' => $msg->message,
                'attachment_url' => $msg->attachment_url,
                'attachment_name' => $msg->attachment_name,
                'attachment_type' => $msg->attachment_type,
                'is_read' => (bool)$msg->is_read,
                'time' => $created ? $created->format('H:i') : '',
                'date' => $created ? $created->format('Y-m-d') : '',
                'date_label' => $created ? ($created->isToday() ? 'Hari ini' : ($created->isYesterday() ? 'Kemarin' : $created->format('d M Y'))) : '',
            ];
        });

        $targetUserPresence = null;
        if ($activeUserId) {
            $targetUserPresence = self::getUserPresence($activeUserId);
        }

        return response()->json([
            'status' => 'success',
            'new_messages' => $formatted,
            'read_message_ids' => $readUpdates,
            'total_unread' => $totalUnread,
            'target_user_presence' => $targetUserPresence,
        ]);
    }
}
