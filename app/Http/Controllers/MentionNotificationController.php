<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\MentionComment;
use App\Models\PrDiscussion;
use App\Models\PrDiscussionMention;
use App\Models\UnitQuotation;
use App\Models\UnitQuotationComment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MentionNotificationController extends Controller
{
    /**
     * Polling endpoint untuk mendapatkan semua unread mentions
     * across: Smart Quotation, Prospect, dan Purchase Request
     */
    public function unreadMentions()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'count' => 0,
                'unread_count' => 0,
                'mentions' => [],
            ], 401);
        }

        $userId = Auth::id();
        $items = collect();

        try {
            // 1. Smart Quotation Mentions
            if (Schema::hasTable('unit_quotation_comment_mentions')) {
                $sqMentions = DB::table('unit_quotation_comment_mentions as m')
                    ->join('unit_quotation_comments as c', 'm.comment_id', '=', 'c.id')
                    ->join('unit_quotation as uq', 'c.id_unit_quotation', '=', 'uq.id')
                    ->leftJoin('client as cl', 'uq.id_client', '=', 'cl.id')
                    ->join('users as u', 'c.user_id', '=', 'u.id')
                    ->where('m.user_id', $userId)
                    ->where('m.is_read', false)
                    ->where('c.user_id', '!=', $userId)
                    ->select([
                        'm.id as mention_id',
                        'm.comment_id',
                        'm.created_at as mention_created_at',
                        'c.comment',
                        'c.created_at as comment_created_at',
                        'uq.id as quotation_id',
                        'uq.no_quote',
                        'uq.title as quote_title',
                        'cl.company as client_company',
                        'u.id as author_id',
                        'u.name as author_name',
                        'u.image as author_image',
                        'u.role as author_role',
                    ])
                    ->orderByDesc('m.created_at')
                    ->take(15)
                    ->get();

                foreach ($sqMentions as $m) {
                    $quoteNo = $m->no_quote ?: 'SQ #' . $m->quotation_id;
                    $company = $m->client_company ?: ($m->quote_title ?: 'Unknown Client');
                    $targetTitle = "[$quoteNo] $company";

                    $items->push([
                        'id' => 'sq_mention_' . $m->mention_id,
                        'raw_id' => $m->mention_id,
                        'comment_id' => $m->comment_id,
                        'type' => 'smart_quotation',
                        'module_name' => 'Smart Quotation',
                        'module_badge' => 'primary',
                        'icon' => 'mdi-file-document-edit-outline',
                        'target_id' => $m->quotation_id,
                        'target_title' => $targetTitle,
                        'author_name' => $m->author_name,
                        'author_avatar' => $m->author_image ? url($m->author_image) : null,
                        'author_role' => $m->author_role,
                        'comment' => Str::limit(strip_tags($m->comment), 110),
                        'go_url' => route('notifications.mentions.go', ['module' => 'smart_quotation', 'id' => $m->mention_id]),
                        'read_url' => route('notifications.mentions.read', ['module' => 'smart_quotation', 'id' => $m->mention_id]),
                        'created_at' => Carbon::parse($m->mention_created_at ?: $m->comment_created_at)->diffForHumans(),
                        'created_timestamp' => Carbon::parse($m->mention_created_at ?: $m->comment_created_at)->timestamp,
                    ]);
                }
            }

            // 2. Prospect Mentions
            if (Schema::hasTable('mention_comment')) {
                $prospectMentions = DB::table('mention_comment as m')
                    ->join('comment as c', 'm.id_comment', '=', 'c.id')
                    ->join('prospect as p', 'c.id_prospect', '=', 'p.id')
                    ->leftJoin('pic', 'p.id_pic', '=', 'pic.id')
                    ->leftJoin('client as cl', 'pic.id_client', '=', 'cl.id')
                    ->join('users as u', 'c.id_user', '=', 'u.id')
                    ->where('m.id_mention', $userId)
                    ->where('m.level', '0')
                    ->where('c.id_user', '!=', $userId)
                    ->select([
                        'm.id as mention_id',
                        'm.id_comment as comment_id',
                        'm.created_at as mention_created_at',
                        'c.comment',
                        'c.date as comment_date',
                        'c.created_at as comment_created_at',
                        'p.id as prospect_id',
                        'p.kebutuhan as prospect_kebutuhan',
                        'cl.company as client_company',
                        'u.id as author_id',
                        'u.name as author_name',
                        'u.image as author_image',
                        'u.role as author_role',
                    ])
                    ->orderByDesc('m.id')
                    ->take(15)
                    ->get();

                foreach ($prospectMentions as $m) {
                    $company = $m->client_company ?: ('Prospek #' . $m->prospect_id);
                    $targetTitle = "Prospek: $company";

                    $dateVal = $m->mention_created_at ?: ($m->comment_created_at ?: $m->comment_date);
                    $items->push([
                        'id' => 'prospect_mention_' . $m->mention_id,
                        'raw_id' => $m->mention_id,
                        'comment_id' => $m->comment_id,
                        'type' => 'prospect',
                        'module_name' => 'Prospect',
                        'module_badge' => 'warning',
                        'icon' => 'mdi-account-search-outline',
                        'target_id' => $m->prospect_id,
                        'target_title' => $targetTitle,
                        'author_name' => $m->author_name,
                        'author_avatar' => $m->author_image ? url($m->author_image) : null,
                        'author_role' => $m->author_role,
                        'comment' => Str::limit(strip_tags($m->comment), 110),
                        'go_url' => route('notifications.mentions.go', ['module' => 'prospect', 'id' => $m->mention_id]),
                        'read_url' => route('notifications.mentions.read', ['module' => 'prospect', 'id' => $m->mention_id]),
                        'created_at' => Carbon::parse($dateVal ?: now())->diffForHumans(),
                        'created_timestamp' => Carbon::parse($dateVal ?: now())->timestamp,
                    ]);
                }
            }

            // 3. Purchase Request Mentions
            if (Schema::hasTable('pr_discussion_mention')) {
                $prMentions = DB::table('pr_discussion_mention as m')
                    ->join('pr_discussion as d', 'm.id_discussion', '=', 'd.id')
                    ->join('pending_po as po', 'd.id_pending', '=', 'po.id')
                    ->leftJoin('quotation as q', 'po.id_quotation', '=', 'q.id')
                    ->leftJoin('unit_quotation as uq', 'po.id_unit_quotation', '=', 'uq.id')
                    ->leftJoin('client as uq_cl', 'uq.id_client', '=', 'uq_cl.id')
                    ->leftJoin('pic as q_pic', 'q.id_pic', '=', 'q_pic.id')
                    ->leftJoin('client as q_cl', 'q_pic.id_client', '=', 'q_cl.id')
                    ->join('users as u', 'd.id_user', '=', 'u.id')
                    ->where('m.id_user_mention', $userId)
                    ->where('m.level', '0')
                    ->where('d.id_user', '!=', $userId)
                    ->select([
                        'm.id as mention_id',
                        'm.id_discussion as discussion_id',
                        'm.created_at as mention_created_at',
                        'd.message as comment',
                        'd.created_at as comment_created_at',
                        'po.id as pending_id',
                        'po.no_pending',
                        'po.title as pending_title',
                        'uq_cl.company as uq_company',
                        'q_cl.company as q_company',
                        'u.id as author_id',
                        'u.name as author_name',
                        'u.image as author_image',
                        'u.role as author_role',
                    ])
                    ->orderByDesc('m.id')
                    ->take(15)
                    ->get();

                foreach ($prMentions as $m) {
                    $prNo = $m->no_pending ?: 'PR #' . $m->pending_id;
                    $company = $m->uq_company ?: ($m->q_company ?: ($m->pending_title ?: 'Purchase Request'));
                    $targetTitle = "[$prNo] $company";

                    $items->push([
                        'id' => 'pr_mention_' . $m->mention_id,
                        'raw_id' => $m->mention_id,
                        'comment_id' => $m->discussion_id,
                        'type' => 'purchase_request',
                        'module_name' => 'Purchase Request',
                        'module_badge' => 'info',
                        'icon' => 'mdi-cart-arrow-down',
                        'target_id' => $m->pending_id,
                        'target_title' => $targetTitle,
                        'author_name' => $m->author_name,
                        'author_avatar' => $m->author_image ? url($m->author_image) : null,
                        'author_role' => $m->author_role,
                        'comment' => Str::limit(strip_tags($m->comment), 110),
                        'go_url' => route('notifications.mentions.go', ['module' => 'purchase_request', 'id' => $m->mention_id]),
                        'read_url' => route('notifications.mentions.read', ['module' => 'purchase_request', 'id' => $m->mention_id]),
                        'created_at' => Carbon::parse($m->mention_created_at ?: $m->comment_created_at)->diffForHumans(),
                        'created_timestamp' => Carbon::parse($m->mention_created_at ?: $m->comment_created_at)->timestamp,
                    ]);
                }
            }

            // 4. Kanban Task Mentions
            if (Schema::hasTable('kanban_task_comment_mentions')) {
                $kanbanMentions = DB::table('kanban_task_comment_mentions as m')
                    ->join('kanban_task_comments as c', 'm.comment_id', '=', 'c.id')
                    ->join('kanban_tasks as t', 'c.task_id', '=', 't.id')
                    ->join('kanban_boards as b', 't.board_id', '=', 'b.id')
                    ->join('users as u', 'c.user_id', '=', 'u.id')
                    ->where('m.user_id', $userId)
                    ->where('m.is_read', false)
                    ->where('c.user_id', '!=', $userId)
                    ->select([
                        'm.comment_id as mention_id',
                        'm.comment_id',
                        'm.created_at as mention_created_at',
                        'c.comment',
                        'c.created_at as comment_created_at',
                        't.id as task_id',
                        't.title as task_title',
                        'b.id as board_id',
                        'b.title as board_title',
                        'u.id as author_id',
                        'u.name as author_name',
                        'u.image as author_image',
                        'u.role as author_role',
                    ])
                    ->orderByDesc('c.created_at')
                    ->take(15)
                    ->get();

                foreach ($kanbanMentions as $m) {
                    $boardTitle = $m->board_title ?: 'Kanban Board';
                    $taskTitle = $m->task_title ?: ('Task #' . $m->task_id);
                    $targetTitle = "[$boardTitle] $taskTitle";

                    $items->push([
                        'id' => 'kanban_mention_' . $m->comment_id,
                        'raw_id' => $m->comment_id,
                        'comment_id' => $m->comment_id,
                        'type' => 'kanban',
                        'module_name' => 'Kanban',
                        'module_badge' => 'success',
                        'icon' => 'mdi-view-dashboard-outline',
                        'target_id' => $m->task_id,
                        'target_title' => $targetTitle,
                        'author_name' => $m->author_name,
                        'author_avatar' => $m->author_image ? url($m->author_image) : null,
                        'author_role' => $m->author_role,
                        'comment' => Str::limit(strip_tags($m->comment), 110),
                        'go_url' => route('notifications.mentions.go', ['module' => 'kanban', 'id' => $m->comment_id]),
                        'read_url' => route('notifications.mentions.read', ['module' => 'kanban', 'id' => $m->comment_id]),
                        'created_at' => Carbon::parse($m->mention_created_at ?: $m->comment_created_at)->diffForHumans(),
                        'created_timestamp' => Carbon::parse($m->mention_created_at ?: $m->comment_created_at)->timestamp,
                    ]);
                }
            }

            // Sort merged items by newest first
            $sortedItems = $items->sortByDesc('created_timestamp')->values();

            return response()->json([
                'success' => true,
                'count' => $sortedItems->count(),
                'unread_count' => $sortedItems->count(),
                'mentions' => $sortedItems,
                'items' => $sortedItems,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'count' => 0,
                'unread_count' => 0,
                'mentions' => [],
                'items' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tandai mention sebagai dibaca dan redirect ke target URL
     */
    public function goMention($module, $id)
    {
        $userId = Auth::id();

        if ($module === 'smart_quotation') {
            try {
                DB::table('unit_quotation_comment_mentions')
                    ->where('id', $id)
                    ->where('user_id', $userId)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                        'updated_at' => now(),
                    ]);
            } catch (\Throwable $e) {}

            $mention = DB::table('unit_quotation_comment_mentions as m')
                ->join('unit_quotation_comments as c', 'm.comment_id', '=', 'c.id')
                ->where('m.id', $id)
                ->select('c.id_unit_quotation')
                ->first();

            if ($mention && $mention->id_unit_quotation) {
                return redirect()->to(url('smart-quote/' . $mention->id_unit_quotation . '#activity-feed'));
            }

            return redirect()->to(url('smart-quote'));
        }

        if ($module === 'prospect') {
            try {
                MentionComment::where('id', $id)
                    ->where('id_mention', $userId)
                    ->update(['level' => '1']);
            } catch (\Throwable $e) {}

            $mention = DB::table('mention_comment as m')
                ->join('comment as c', 'm.id_comment', '=', 'c.id')
                ->where('m.id', $id)
                ->select('c.id_prospect')
                ->first();

            if ($mention && $mention->id_prospect) {
                return redirect()->to(url('prospect/' . $mention->id_prospect . '#viewComment'));
            }

            return redirect()->to(url('prospect'));
        }

        if ($module === 'purchase_request') {
            try {
                PrDiscussionMention::where('id', $id)
                    ->where('id_user_mention', $userId)
                    ->update(['level' => '1']);
            } catch (\Throwable $e) {}

            $mention = DB::table('pr_discussion_mention as m')
                ->join('pr_discussion as d', 'm.id_discussion', '=', 'd.id')
                ->where('m.id', $id)
                ->select('d.id_pending')
                ->first();

            if ($mention && $mention->id_pending) {
                return redirect()->route('purchase-request.show', $mention->id_pending)->withFragment('diskusi');
            }

            return redirect()->to(url('purchase-request'));
        }

        if ($module === 'kanban') {
            try {
                DB::table('kanban_task_comment_mentions')
                    ->where('comment_id', $id)
                    ->where('user_id', $userId)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                        'updated_at' => now(),
                    ]);
            } catch (\Throwable $e) {}

            $mention = DB::table('kanban_task_comments as c')
                ->join('kanban_tasks as t', 'c.task_id', '=', 't.id')
                ->where('c.id', $id)
                ->select('t.board_id', 't.id as task_id')
                ->first();

            if ($mention) {
                return redirect()->route('kanban.boards.show', ['id' => $mention->board_id, 'task' => $mention->task_id]);
            }

            return redirect()->route('kanban.index');
        }

        return redirect()->back();
    }

    /**
     * Tandai mention sebagai dibaca via AJAX
     */
    public function markMentionRead($module, $id)
    {
        $userId = Auth::id();

        try {
            if ($module === 'smart_quotation') {
                DB::table('unit_quotation_comment_mentions')
                    ->where('id', $id)
                    ->where('user_id', $userId)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                        'updated_at' => now(),
                    ]);
            } elseif ($module === 'prospect') {
                MentionComment::where('id', $id)
                    ->where('id_mention', $userId)
                    ->update(['level' => '1']);
            } elseif ($module === 'purchase_request') {
                PrDiscussionMention::where('id', $id)
                    ->where('id_user_mention', $userId)
                    ->update(['level' => '1']);
            } elseif ($module === 'kanban') {
                DB::table('kanban_task_comment_mentions')
                    ->where('comment_id', $id)
                    ->where('user_id', $userId)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
