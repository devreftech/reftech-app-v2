<?php

namespace App\Http\Controllers;

use App\Models\KanbanBoard;
use App\Models\KanbanColumn;
use App\Models\KanbanTask;
use App\Models\KanbanTaskComment;
use App\Models\KanbanTaskActivity;
use App\Models\KanbanChecklist;
use App\Models\KanbanChecklistItem;
use App\Models\KanbanTaskAttachment;
use App\Models\KanbanTaskDeleteRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\PurchaseRequest;
use App\Models\ProjectExpense;
use App\Models\Expanse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KanbanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'Admin') {
            $boards = KanbanBoard::with('creator')->orderBy('title')->get();
        } else {
            $boards = $user->kanbanBoards()->with('creator')->orderBy('title')->get();
        }

        // Fetch all active users for board creation members select
        $users = User::where('active', '1')->orderBy('name')->get();

        return view('pages.kanban.index', compact('boards', 'users'));
    }

    public function storeBoard(Request $request)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Hanya Admin yang bisa membuat papan Kanban.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
            'columns' => 'required|array|min:1',
            'columns.*' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $board = KanbanBoard::create([
                'title' => $request->title,
                'description' => $request->description,
                'created_by' => Auth::id(),
            ]);

            // Sync members
            if ($request->has('member_ids')) {
                $board->members()->sync($request->member_ids);
            }

            // Create columns
            foreach ($request->columns as $index => $colTitle) {
                KanbanColumn::create([
                    'board_id' => $board->id,
                    'title' => $colTitle,
                    'position' => $index,
                ]);
            }
        });

        return redirect()->route('kanban.index')->with('success', 'Papan Kanban berhasil dibuat!');
    }

    public function showBoard($id)
    {
        $user = Auth::user();
        $board = KanbanBoard::with(['columns', 'members'])->findOrFail($id);

        if ($board->type === 'monitoring') {
            return redirect()->route('kanban.monitoring-document');
        }

        // Check permission: Admin can see all, members can see their boards
        if ($user->role !== 'Admin' && !$board->members->contains($user->id)) {
            abort(403, 'Anda tidak memiliki akses ke papan Kanban ini.');
        }

        // Fetch all active users for settings member select
        $users = User::where('active', '1')->orderBy('name')->get();
        $salesUsers = collect();
        $accountingUsers = collect();
        if ($board->type === 'monitoring') {
            $salesUsers = User::where('role', 'Sales')->where('active', '1')->orderBy('name')->get();
            $accountingUsers = User::where('role', 'Accounting')->where('active', '1')->orderBy('name')->with('handledSales')->get();
        }

        // Fetch user boards for board switcher dropdown
        if ($user->role === 'Admin') {
            $myBoards = KanbanBoard::orderBy('title')->get();
        } else {
            $myBoards = $user->kanbanBoards()->orderBy('title')->get();
        }

        return view('pages.kanban.board', compact('board', 'users', 'myBoards', 'salesUsers', 'accountingUsers'));
    }

    public function updateBoard(Request $request, $id)
    {
        if (Auth::user()->role !== 'Admin' && Auth::user()->role !== 'Accounting') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $board = KanbanBoard::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
            'columns' => 'required|array|min:1',
            'columns.*.id' => 'nullable|integer',
            'columns.*.title' => 'required|string|max:255',
            'labels' => 'nullable|array',
            'labels.*' => 'nullable|string|max:100',
            'notification_sound' => 'nullable|file|max:5120',
        ]);

        DB::transaction(function () use ($request, $board) {
            $updateData = [
                'title' => $request->title,
                'description' => $request->description,
                'labels' => $request->labels,
            ];

            if ($request->hasFile('notification_sound')) {
                $file = $request->file('notification_sound');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/kanban_sounds'), $filename);
                
                if ($board->notification_sound && file_exists(public_path($board->notification_sound))) {
                    @unlink(public_path($board->notification_sound));
                }
                
                $updateData['notification_sound'] = 'uploads/kanban_sounds/' . $filename;
            }

            $board->update($updateData);

            // Sync members
            $board->members()->sync($request->member_ids ?? []);

            // Handle Columns update/create/delete
            $submittedColumnIds = collect($request->columns)->pluck('id')->filter()->toArray();

            // 1. Delete columns not in the request
            $board->columns()->whereNotIn('id', $submittedColumnIds)->delete();

            // 2. Create or Update columns
            foreach ($request->columns as $index => $colData) {
                if (isset($colData['id']) && $colData['id']) {
                    KanbanColumn::where('id', $colData['id'])->update([
                        'title' => $colData['title'],
                        'position' => $index,
                    ]);
                } else {
                    KanbanColumn::create([
                        'board_id' => $board->id,
                        'title' => $colData['title'],
                        'position' => $index,
                    ]);
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Papan Kanban berhasil diperbarui!']);
    }

    public function destroyBoard($id)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Hanya Admin yang bisa menghapus papan Kanban.');
        }

        $board = KanbanBoard::findOrFail($id);
        $board->delete();

        return redirect()->route('kanban.index')->with('success', 'Papan Kanban berhasil dihapus!');
    }

    public function getBoardData($id)
    {
        $user = Auth::user();
        $board = KanbanBoard::with([
            'columns.tasks.assignees',
            'columns.tasks.checklists.items',
            'columns.tasks.pendingPo.quote.sales',
            'columns.tasks.pendingPo.quote.invoice',
            'columns.tasks.pendingPo.quote.pic.client',
            'columns.tasks.pendingPo.unitQuotation.sales',
            'columns.tasks.pendingPo.unitQuotation.invoices',
            'columns.tasks.pendingPo.unitQuotation.client',
            'columns.tasks.unitQuotation.sales',
            'columns.tasks.unitQuotation.invoices',
            'columns.tasks.unitQuotation.client',
        ])->findOrFail($id);

        if ($user->role !== 'Admin' && !$board->members->contains($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $hiddenForServiceM = ['INVOICE', 'CANCEL PO', 'PO MENYUSUL'];
        $data = [];
        foreach ($board->columns as $column) {
            if ($board->type === 'monitoring' && $user->role === 'ServiceM') {
                $colUpper = strtoupper(trim($column->title));
                $shouldHide = false;
                foreach ($hiddenForServiceM as $hiddenKeyword) {
                    if (str_contains($colUpper, $hiddenKeyword)) {
                        $shouldHide = true;
                        break;
                    }
                }
                if ($shouldHide) {
                    continue;
                }
            }

            $items = [];
            foreach ($column->tasks as $task) {
                $taskAssignees = $task->assignees->map(function ($u) {
                    return [
                        'name' => $u->name,
                        'avatar' => $u->image ? '/' . $u->image : null,
                    ];
                });

                $totalChecklistItems = 0;
                $completedChecklistItems = 0;
                foreach ($task->checklists as $checklist) {
                    $totalChecklistItems += $checklist->items->count();
                    $completedChecklistItems += $checklist->items->where('is_completed', true)->count();
                }

                $nettValue = 0;
                $idSales = null;
                $salesName = null;
                $entityType = null;
                $noPo = null;
                $company = null;

                if ($task->pendingPo || $task->unitQuotation) {
                    $po = $task->pendingPo;
                    // Kartu bisa lahir dari PO (pendingPo) atau langsung dari tombol "Post
                    // to Kanban" di halaman quotation (id_unit_quotation, tanpa PO/SO) —
                    // dua-duanya diperlakukan sebagai quotation Unit.
                    $isUnit = $po ? (bool) $po->id_unit_quotation : true;
                    $quoteRef = $po ? ($isUnit ? $po->unitQuotation : $po->quote) : $task->unitQuotation;
                    if ($quoteRef) {
                        $nettValue = $user->role === 'ServiceM' ? 0 : (float) ($isUnit ? ($quoteRef->total ?? 0) : ($quoteRef->nett ?? 0));
                        $idSales = $quoteRef->id_sales;
                        $salesName = $quoteRef->sales ? $quoteRef->sales->name : null;
                        $company = $isUnit ? ($quoteRef->client->company ?? null) : ($quoteRef->pic->client->company ?? null);
                    }
                    if ($po) {
                        $noPo = $po->no_po ?: $po->no_pending;
                    }

                    // Check invoice flag / invoice number
                    $invoices = $isUnit
                        ? ($quoteRef ? $quoteRef->invoices : collect())
                        : ($quoteRef ? $quoteRef->invoice : collect());

                    $firstInvoice = $invoices ? ($invoices->whereNotNull('no_invoice')->where('no_invoice', '!=', '')->first() ?: $invoices->first()) : null;
                    if ($firstInvoice) {
                        if ($firstInvoice->flag === 'Kojisha' || ($firstInvoice->no_invoice && str_contains($firstInvoice->no_invoice, '/KII/'))) {
                            $entityType = 'KII';
                        } elseif ($firstInvoice->flag === 'Reftech' || ($firstInvoice->no_invoice && str_contains($firstInvoice->no_invoice, '/RJO/'))) {
                            $entityType = 'RJO';
                        }
                    }

                    // Fallback to quotation flag / client info
                    if (!$entityType && $quoteRef) {
                        $client = $isUnit
                            ? ($quoteRef->client ?? null)
                            : (($quoteRef->pic) ? $quoteRef->pic->client : null);
                        $flag = $quoteRef->flag ?? ($client ? $client->info : null);
                        if ($flag === 'Kojisha') {
                            $entityType = 'KII';
                        } elseif ($flag === 'Reftech') {
                            $entityType = 'RJO';
                        }
                    }

                    // Default to RJO for monitoring board tasks if not specified
                    if (!$entityType && $board->type === 'monitoring') {
                        $entityType = 'RJO';
                    }
                }

                $items[] = [
                    'id' => (string) $task->id,
                    'title' => $task->title,
                    'description' => $task->description ?? '',
                    'due_date' => $task->due_date ? $task->due_date : '',
                    'assignees' => $taskAssignees,
                    'labels' => $task->labels ?? [],
                    'total_checklists' => $totalChecklistItems,
                    'completed_checklists' => $completedChecklistItems,
                    'priority' => $task->priority ?? 'medium',
                    'nett' => $nettValue,
                    'id_sales' => $idSales,
                    'sales_name' => $salesName,
                    'entity_type' => $entityType,
                    'no_po' => $noPo,
                    'company' => $company,
                ];
            }
            $data[] = [
                'id' => 'column_' . $column->id,
                'title' => $column->title,
                'item' => $items
            ];
        }

        return response()->json($data);
    }

    public function moveTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:kanban_tasks,id',
            'target_column_id' => 'required|string',
            'new_position' => 'required|integer',
        ]);

        $columnId = (int) str_replace('column_', '', $request->target_column_id);
        $taskId = $request->task_id;
        $newPos = $request->new_position;

        $task = KanbanTask::findOrFail($taskId);
        $oldColumnId = $task->column_id;
        $oldPos = $task->position;
        $oldCol = KanbanColumn::find($oldColumnId);
        $newCol = KanbanColumn::find($columnId);

        DB::transaction(function () use ($task, $oldColumnId, $oldPos, $columnId, $newPos) {
            if ($oldColumnId == $columnId) {
                // Moving inside the same column
                if ($oldPos < $newPos) {
                    KanbanTask::where('column_id', $columnId)
                        ->where('position', '>', $oldPos)
                        ->where('position', '<=', $newPos)
                        ->decrement('position');
                } else if ($oldPos > $newPos) {
                    KanbanTask::where('column_id', $columnId)
                        ->where('position', '>=', $newPos)
                        ->where('position', '<', $oldPos)
                        ->increment('position');
                }
            } else {
                // Moving to a different column
                // 1. Decrement positions in source column for tasks after the old position
                KanbanTask::where('column_id', $oldColumnId)
                    ->where('position', '>', $oldPos)
                    ->decrement('position');

                // 2. Increment positions in target column for tasks at or after the new position
                KanbanTask::where('column_id', $columnId)
                    ->where('position', '>=', $newPos)
                    ->increment('position');
            }

            // 3. Update task column and position
            $task->update([
                'column_id' => $columnId,
                'position' => $newPos,
            ]);
        });

        if ($oldColumnId != $columnId) {
            $this->logActivity($task->id, 'move', [
                'from' => $oldCol ? $oldCol->title : 'Unknown',
                'to' => $newCol ? $newCol->title : 'Unknown'
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function storeTask(Request $request)
    {
        $request->validate([
            'board_id' => 'required|exists:kanban_boards,id',
            'column_id' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|string|in:high,medium,low',
            'pending_po_id' => 'nullable|integer',
        ]);

        $columnId = (int) str_replace('column_', '', $request->column_id);

        // Get max position in this column
        $maxPos = KanbanTask::where('column_id', $columnId)->max('position');
        $position = is_null($maxPos) ? 0 : $maxPos + 1;

        $task = KanbanTask::create([
            'board_id' => $request->board_id,
            'column_id' => $columnId,
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'due_date' => $request->due_date,
            'position' => $position,
            'priority' => $request->priority ?? 'medium',
            'pending_po_id' => $request->pending_po_id,
        ]);

        // Log creation activity
        $this->logActivity($task->id, 'create');

        if ($request->assigned_to) {
            $task->assignees()->sync([$request->assigned_to]);
        }

        $taskLoad = KanbanTask::with('assignees')->find($task->id);

        $taskAssignees = $taskLoad->assignees->map(function ($u) {
            return [
                'name' => $u->name,
                'avatar' => $u->image ? '/' . $u->image : null,
            ];
        });

        return response()->json([
            'success' => true,
            'task' => [
                'id' => (string) $taskLoad->id,
                'title' => $taskLoad->title,
                'description' => $taskLoad->description ?? '',
                'due_date' => $taskLoad->due_date ? $taskLoad->due_date : '',
                'assignees' => $taskAssignees,
                'priority' => $taskLoad->priority ?? 'medium',
            ]
        ]);
    }

    public function updateTask(Request $request, $id)
    {
        $task = KanbanTask::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'due_date' => 'nullable|date',
            'column_id' => 'nullable|string',
            'priority' => 'nullable|string|in:high,medium,low',
            'service_report_id' => 'nullable|integer',
        ]);

        DB::transaction(function () use ($request, $task) {
            $oldTitle = $task->title;
            $oldDesc = $task->description;
            $oldDueDate = $task->due_date;
            $oldColumnId = $task->column_id;
            $oldPriority = $task->priority ?? 'medium';
            
            $columnId = $request->column_id ? (int) str_replace('column_', '', $request->column_id) : $oldColumnId;

            // If column has changed
            if ($columnId != $oldColumnId) {
                // 1. Decrement positions in source column for tasks after the old position
                KanbanTask::where('column_id', $oldColumnId)
                    ->where('position', '>', $task->position)
                    ->decrement('position');

                // 2. Get max position in new column
                $maxPos = KanbanTask::where('column_id', $columnId)->max('position');
                $newPos = is_null($maxPos) ? 0 : $maxPos + 1;

                $task->update([
                    'column_id' => $columnId,
                    'position' => $newPos,
                ]);

                $oldCol = KanbanColumn::find($oldColumnId);
                $newCol = KanbanColumn::find($columnId);
                $this->logActivity($task->id, 'move', [
                    'from' => $oldCol ? $oldCol->title : 'Unknown',
                    'to' => $newCol ? $newCol->title : 'Unknown'
                ]);
            }

            $newAssigneesIds = $request->assignees ?? [];
            $oldAssigneesIds = $task->assignees()->pluck('users.id')->toArray();

            sort($oldAssigneesIds);
            sort($newAssigneesIds);

            if ($oldAssigneesIds !== $newAssigneesIds) {
                $task->assignees()->sync($newAssigneesIds);
                
                $oldNames = User::whereIn('id', $oldAssigneesIds)->pluck('name')->toArray();
                $newNames = User::whereIn('id', $newAssigneesIds)->pluck('name')->toArray();
                
                $this->logActivity($task->id, 'update_assignee', [
                    'old' => implode(', ', $oldNames) ?: 'tidak ditugaskan',
                    'new' => implode(', ', $newNames) ?: 'tidak ditugaskan'
                ]);
            }

            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'assigned_to' => !empty($newAssigneesIds) ? $newAssigneesIds[0] : null,
                'due_date' => $request->due_date,
                'priority' => $request->priority ?? $task->priority,
                'service_report_id' => array_key_exists('service_report_id', $request->all()) ? $request->service_report_id : $task->service_report_id,
            ]);

            // Log other changes
            if ($oldTitle !== $request->title) {
                $this->logActivity($task->id, 'update_title', ['old' => $oldTitle, 'new' => $request->title]);
            }
            if ($oldDesc !== $request->description) {
                $this->logActivity($task->id, 'update_description');
            }
            if ($oldDueDate != $request->due_date) {
                $this->logActivity($task->id, 'update_due_date', ['old' => $oldDueDate, 'new' => $request->due_date]);
            }
            if ($oldPriority !== $request->priority) {
                $priorityNames = ['high' => 'Tinggi', 'medium' => 'Sedang', 'low' => 'Rendah'];
                $oldName = $priorityNames[$oldPriority] ?? ($oldPriority ?? 'Sedang');
                $newName = $priorityNames[$request->priority] ?? ($request->priority ?? 'Sedang');
                $this->logActivity($task->id, 'update_priority', ['old' => $oldName, 'new' => $newName]);
            }
        });

        $taskLoad = KanbanTask::with(['assignees', 'column'])->find($task->id);

        return response()->json([
            'success' => true,
            'task' => [
                'id' => (string) $taskLoad->id,
                'title' => $taskLoad->title,
                'description' => $taskLoad->description ?? '',
                'due_date' => $taskLoad->due_date ? $taskLoad->due_date : '',
                'assignees' => $taskLoad->assignees->pluck('id')->toArray(),
                'column_id' => $taskLoad->column_id,
                'column_title' => $taskLoad->column->title,
                'priority' => $taskLoad->priority ?? 'medium',
            ]
        ]);
    }

    public function destroyTask($id)
    {
        $task = KanbanTask::findOrFail($id);
        
        DB::transaction(function () use ($task) {
            // Decrement positions of tasks after this task in the same column
            KanbanTask::where('column_id', $task->column_id)
                ->where('position', '>', $task->position)
                ->decrement('position');

            $task->delete();
        });

        return response()->json(['success' => true]);
    }

    public function getTaskDetails($id)
    {
        $task = KanbanTask::with([
            'board',
            'column',
            'assignees',
            'comments.user',
            'activities.user',
            'checklists.items',
            'attachments.user',
            'pendingPo.quote.pic.client',
            'pendingPo.quote.sales',
            'pendingPo.unitQuotation.client',
            'pendingPo.unitQuotation.sales',
            'unitQuotation.client',
            'unitQuotation.sales',
            'bast'
        ])->findOrFail($id);

        // Format comments
        $comments = $task->comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'type' => 'comment',
                'actor' => $comment->user->name,
                'actor_avatar' => $comment->user->image ? '/' . $comment->user->image : null,
                'actor_initials' => strtoupper(substr($comment->user->name, 0, 1)),
                'text' => $comment->comment,
                'created_at_raw' => $comment->created_at,
                'created_at' => $comment->created_at->format('M d, Y, g:i A'),
                'created_at_human' => $comment->created_at->diffForHumans(),
                'user_id' => $comment->user_id,
            ];
        });

        // Format activities
        $activities = $task->activities->map(function ($activity) {
            $actor = $activity->user ? $activity->user->name : 'System';
            $text = '';
            $data = $activity->activity_data;

            switch ($activity->activity_type) {
                case 'create':
                    $text = 'membuat tugas ini';
                    break;
                case 'move':
                    $from = isset($data['from']) ? $data['from'] : 'Unknown';
                    $to = isset($data['to']) ? $data['to'] : 'Unknown';
                    $text = "memindahkan tugas dari kolom \"{$from}\" ke \"{$to}\"";
                    break;
                case 'update_title':
                    $old = isset($data['old']) ? $data['old'] : '';
                    $new = isset($data['new']) ? $data['new'] : '';
                    $text = "mengubah judul tugas dari \"{$old}\" menjadi \"{$new}\"";
                    break;
                case 'update_description':
                    $text = 'memperbarui deskripsi tugas';
                    break;
                case 'update_assignee':
                    $old = isset($data['old']) && $data['old'] ? $data['old'] : 'belum ditugaskan';
                    $new = isset($data['new']) && $data['new'] ? $data['new'] : 'tidak ditugaskan';
                    $text = "mengubah penugasan dari \"{$old}\" ke \"{$new}\"";
                    break;
                case 'update_due_date':
                    $old = isset($data['old']) && $data['old'] ? $data['old'] : 'tanpa batas waktu';
                    $new = isset($data['new']) && $data['new'] ? $data['new'] : 'dihapus';
                    $text = "mengubah tanggal jatuh tempo dari \"{$old}\" ke \"{$new}\"";
                    break;
                case 'upload_attachment':
                    $fileName = isset($data['file_name']) ? $data['file_name'] : 'file';
                    $text = "mengunggah lampiran \"{$fileName}\"";
                    break;
                case 'delete_attachment':
                    $fileName = isset($data['file_name']) ? $data['file_name'] : 'file';
                    $text = "menghapus lampiran \"{$fileName}\"";
                    break;
                case 'update_priority':
                    $old = isset($data['old']) ? $data['old'] : 'Sedang';
                    $new = isset($data['new']) ? $data['new'] : 'Sedang';
                    $text = "mengubah prioritas dari \"{$old}\" menjadi \"{$new}\"";
                    break;
                case 'request_delete':
                    $text = 'mengajukan penghapusan tugas ini kepada Admin';
                    break;
                default:
                    $text = 'memperbarui tugas ini';
                    break;
            }

            return [
                'id' => $activity->id,
                'type' => 'activity',
                'actor' => $actor,
                'actor_avatar' => $activity->user && $activity->user->image ? '/' . $activity->user->image : null,
                'actor_initials' => $activity->user ? strtoupper(substr($activity->user->name, 0, 1)) : 'S',
                'text' => $text,
                'created_at_raw' => $activity->created_at,
                'created_at' => $activity->created_at->format('M d, Y, g:i A'),
                'created_at_human' => $activity->created_at->diffForHumans(),
            ];
        });

        // Combine chronologically
        $feed = $comments->concat($activities)->sortByDesc('created_at_raw')->values()->map(function ($item) {
            unset($item['created_at_raw']);
            return $item;
        });

        // Format checklists
        $checklists = $task->checklists->map(function ($checklist) {
            $total = $checklist->items->count();
            $completed = $checklist->items->where('is_completed', true)->count();
            $percent = $total > 0 ? round(($completed / $total) * 100) : 0;

            return [
                'id' => $checklist->id,
                'title' => $checklist->title,
                'percent' => $percent,
                'items' => $checklist->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'is_completed' => (bool)$item->is_completed,
                    ];
                }),
            ];
        });

        // Format attachments
        $attachments = $task->attachments->map(function ($att) {
            return [
                'id' => $att->id,
                'file_name' => $att->file_name,
                'file_path' => '/' . $att->file_path,
                'file_size' => $att->file_size ? round($att->file_size / 1024, 1) . ' KB' : 'Unknown size',
                'file_type' => $att->file_type,
                'uploaded_by' => $att->user ? $att->user->name : 'Unknown',
                'uploaded_at' => $att->created_at->format('M d, Y, g:i A'),
            ];
        });

        // Ringkasan Kesehatan Keuangan — sama persis rumusnya kayak
        // ProjectMonitoringController::show(), cuma dipanggil di sini biar kartu
        // ringkasannya kelihatan langsung dari task Kanban tanpa pindah halaman.
        $computeFinancialHealth = function ($pendingId, $revenue) {
            if (!$pendingId) {
                return null;
            }
            $materialCost = PurchaseRequest::where('id_pending', $pendingId)
                ->where('status', '3')
                ->with('details')
                ->get()
                ->flatMap->details
                ->sum('amount');
            $generalCost = ProjectExpense::where('id_pending', $pendingId)->sum('amount');
            $shippingCost = Expanse::where('id_pending', $pendingId)->where('type', 'Resi')->sum('cost');
            $totalCost = $materialCost + $generalCost + $shippingCost;
            $profit = $revenue - $totalCost;
            $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

            return [
                'revenue' => $revenue,
                'total_cost' => $totalCost,
                'profit' => $profit,
                'margin' => round($margin, 1),
            ];
        };

        $soDetails = null;
        if ($task->pendingPo || $task->unitQuotation) {
            if ($task->pendingPo) {
                $po = $task->pendingPo;
                $isUnit = (bool) $po->id_unit_quotation;
                $quoteRef = $isUnit ? $po->unitQuotation : $po->quote;
                $quoteIdCol = $isUnit ? 'id_unit_quotation' : 'id_quotation';
                $quoteRefId = $isUnit ? $po->id_unit_quotation : $po->id_quotation;

                $invoicePo = \App\Models\Invoice::where($quoteIdCol, $quoteRefId)->whereNotNull('no_po')->where('no_po', '!=', '')->value('no_po');
                $poNumber = $invoicePo ?: ($po->no_pending ?? 'No PO');
                $companyName = 'Unknown Client';
                $clientAddress = '';
                $clientId = null;
                $bastEntity = 'Reftech';
                $client = $isUnit
                    ? ($quoteRef->client ?? null)
                    : (($quoteRef && $quoteRef->pic) ? $quoteRef->pic->client : null);
                if ($client) {
                    $companyName = $client->company;
                    $clientAddress = $client->address;
                    $clientId = $client->id;
                    $bastEntity = ($client->info == 'Reftech') ? 'Reftech' : 'Kojisha';
                }

                // Get Invoices and associate their payments sequentially
                $invoices = \App\Models\Invoice::where($quoteIdCol, $quoteRefId)->orderBy('id')->get();
                $payments = \App\Models\Payment::where($quoteIdCol, $quoteRefId)->orderBy('id')->get();
                
                $invoiceList = [];
                foreach ($invoices as $index => $invoice) {
                    $payment = $payments->get($index);
                    $status = 'Unpaid';
                    if ($payment) {
                        $status = ($payment->level == 1) ? 'Paid' : 'Pending Confirmation';
                    }
                    
                    $typeLabel = 'Invoice';
                    switch ($invoice->type) {
                        case 'CT':
                            $typeLabel = 'Cash Before Delivery';
                            break;
                        case 'DP':
                            $typeLabel = 'Down Payment';
                            break;
                        case 'BP':
                            $typeLabel = 'Balance Payment / Tempo';
                            break;
                    }
                    $percentVal = $invoice->percent !== null ? (int)$invoice->percent : 100;
                    $termName = ($invoice->no_invoice && $invoice->term) ? $invoice->term : "{$typeLabel} ({$percentVal}%)";
                    
                    $invoiceList[] = [
                        'id' => $invoice->id,
                        'no_invoice' => $invoice->no_invoice ?: 'Draft Invoice #' . $invoice->id,
                        'term_name' => $termName,
                        'status' => $status,
                        'link' => url('/invoice/' . $invoice->id)
                    ];
                }

                // Get Delivery / Surat Jalan records for all invoices of this quotation
                $invoiceIds = $invoices->pluck('id')->toArray();
                $deliveries = \App\Models\Delivery::whereIn('id_invoice', $invoiceIds)->get()->map(function($d) {
                    return [
                        'id' => $d->id,
                        'destination' => $d->destination ?: 'No Destination',
                        'link' => url('/delivery/' . $d->id),
                    ];
                });

                // Get available service reports for this client
                $availableReports = [];
                if ($clientId) {
                    $availableReports = \App\Models\Reports::whereHas('pic', function($q) use ($clientId) {
                        $q->where('id_client', $clientId);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($r) {
                        return [
                            'id' => $r->id,
                            'jobdesc' => ($r->no_service ?: 'No SR') . ' - ' . ($r->jobdesc ?: 'Service Report #' . $r->id) . ' (' . $r->created_at->format('d-m-Y') . ')',
                        ];
                    });
                }

                $activeReport = null;
                if ($task->service_report_id) {
                    $rep = \App\Models\Reports::find($task->service_report_id);
                    if ($rep) {
                        $activeReport = [
                            'id' => $rep->id,
                            'jobdesc' => ($rep->no_service ?: 'No SR') . ' - ' . ($rep->jobdesc ?: 'Service Report #' . $rep->id),
                            'date' => $rep->created_at->format('d-m-Y'),
                            'link' => url('/service-reports/' . $rep->id),
                        ];
                    }
                }

                $entityType = null;
                $firstInvoice = $invoices->whereNotNull('no_invoice')->where('no_invoice', '!=', '')->first() ?: $invoices->first();
                if ($firstInvoice) {
                    if ($firstInvoice->flag === 'Kojisha' || ($firstInvoice->no_invoice && str_contains($firstInvoice->no_invoice, '/KII/'))) {
                        $entityType = 'KII';
                    } elseif ($firstInvoice->flag === 'Reftech' || ($firstInvoice->no_invoice && str_contains($firstInvoice->no_invoice, '/RJO/'))) {
                        $entityType = 'RJO';
                    }
                }
                if (!$entityType && $quoteRef) {
                    $flag = $quoteRef->flag ?? ($client ? $client->info : null);
                    if ($flag === 'Kojisha') {
                        $entityType = 'KII';
                    } elseif ($flag === 'Reftech') {
                        $entityType = 'RJO';
                    }
                }
                if (!$entityType) {
                    $entityType = ($bastEntity === 'Kojisha') ? 'KII' : 'RJO';
                }

                $soDetails = [
                    'id' => $po->id,
                    'no_po' => $poNumber,
                    'company' => $companyName,
                    'address' => $clientAddress,
                    'entity_type' => $entityType,
                    'sales_name' => $quoteRef && $quoteRef->sales ? $quoteRef->sales->name : 'N/A',
                    'quote_id' => $quoteRefId,
                    'quote_no' => $quoteRef ? $quoteRef->no_quote : 'N/A',
                    'quote_link' => $isUnit ? route('unit-quotation.show', $quoteRefId) : route('quotation.show', $quoteRefId),
                    'quote_nett' => $quoteRef ? number_format($isUnit ? $quoteRef->total : $quoteRef->nett, 2, ',', '.') : '0',
                    'project_monitoring_link' => route('project-monitoring.show', $po->id),
                    'financial_health' => $computeFinancialHealth($po->id, (float) ($quoteRef ? ($isUnit ? $quoteRef->total : $quoteRef->nett) : 0)),
                    'type' => $po->type,
                    'date' => $po->date ? $po->date : '',
                    'invoices' => $invoiceList,
                    'deliveries' => $deliveries,
                    'available_reports' => $availableReports,
                    'service_report_id' => $task->service_report_id,
                    'active_report' => $activeReport,
                    'bast_prefill' => [
                        'id_quotation' => $quoteRefId,
                        'entity' => $bastEntity,
                        'customer_name' => $companyName,
                        'work_title' => $quoteRef ? $quoteRef->title : '',
                        'po_number' => $poNumber,
                    ],
                    'bast' => $task->bast ? [
                        'id' => $task->bast->id,
                        'no_bast' => $task->bast->no_bast,
                        'show_link' => route('bast.show', $task->bast->id),
                        'print_link' => route('bast.print', $task->bast->id),
                    ] : null,
                ];
            } elseif ($task->unitQuotation) {
                // Kartu lahir langsung dari tombol "Post to Kanban" di halaman quotation
                // (belum ada PO/SO) — panel SO Details tetap ditampilkan tapi versi
                // ringkas, yang penting link balik ke quotation-nya kelihatan.
                $quoteRef = $task->unitQuotation;
                $client = $quoteRef->client;
                $bastEntity = ($client && $client->info == 'Reftech') ? 'Reftech' : 'Kojisha';
                $entityType = $bastEntity === 'Kojisha' ? 'KII' : 'RJO';

                // Kartu ini gak punya pending_po_id, tapi bisa aja PendingPO-nya udah dibuat
                // terpisah (mis. dari upload-po) — kalau ada, ikutan tautkan Project Monitoring
                // & Ringkasan Kesehatan Keuangan-nya.
                $relatedPo = \App\Models\PendingPO::where('id_unit_quotation', $quoteRef->id)->first();

                $soDetails = [
                    'id' => null,
                    'no_po' => 'Belum ada PO',
                    'company' => $client ? $client->company : 'Unknown Client',
                    'address' => $client ? $client->address : '',
                    'entity_type' => $entityType,
                    'sales_name' => $quoteRef->sales ? $quoteRef->sales->name : 'N/A',
                    'quote_id' => $quoteRef->id,
                    'quote_no' => $quoteRef->no_quote,
                    'quote_link' => route('unit-quotation.show', $quoteRef->id),
                    'quote_nett' => number_format($quoteRef->total, 2, ',', '.'),
                    // Fallback ke halaman quotation kalau belum ada PendingPO sama sekali.
                    'project_monitoring_link' => $relatedPo
                        ? route('project-monitoring.show', $relatedPo->id)
                        : route('unit-quotation.show', $quoteRef->id),
                    'financial_health' => $computeFinancialHealth($relatedPo?->id, (float) $quoteRef->total),
                    'type' => $quoteRef->type,
                    'date' => $quoteRef->date ?: '',
                    'invoices' => [],
                    'deliveries' => [],
                    'available_reports' => [],
                    'service_report_id' => null,
                    'active_report' => null,
                    'bast_prefill' => [
                        'id_quotation' => $quoteRef->id,
                        'entity' => $bastEntity,
                        'customer_name' => $client ? $client->company : '',
                        'work_title' => $quoteRef->title,
                        'po_number' => 'Belum ada PO',
                    ],
                    'bast' => null,
                ];
            }
        }

        $user = Auth::user();
        if ($soDetails && $user && $user->role === 'ServiceM') {
            $soDetails['quote_nett'] = null;
            $soDetails['quote_link'] = null;
            $soDetails['invoices'] = [];
            $soDetails['financial_health'] = null;
            if ($soDetails['project_monitoring_link'] && str_contains($soDetails['project_monitoring_link'], 'unit-quotation')) {
                $soDetails['project_monitoring_link'] = null;
            }
        }

        return response()->json([
            'success' => true,
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description ?? '',
                'due_date' => $task->due_date,
                'assignees' => $task->assignees->pluck('id')->toArray(),
                'column_id' => $task->column_id,
                'column_title' => $task->column->title,
                'labels' => $task->labels ?? [],
                'priority' => $task->priority ?? 'medium',
                'pending_po_id' => $task->pending_po_id,
                'service_report_id' => $task->service_report_id,
            ],
            'feed' => $feed,
            'checklists' => $checklists,
            'attachments' => $attachments,
            'so_details' => $soDetails,
        ]);
    }

    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $task = KanbanTask::findOrFail($id);

        $comment = KanbanTaskComment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        // Parse mentions
        $activeUsers = User::where('active', '1')->get();
        foreach ($activeUsers as $user) {
            if (stripos($request->comment, '@' . $user->name) !== false) {
                $comment->mentions()->attach($user->id);
            }
        }

        return response()->json(['success' => true]);
    }

    public function updateComment(Request $request, $id)
    {
        $comment = KanbanTaskComment::findOrFail($id);
        
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment->update([
            'comment' => $request->comment,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroyComment($id)
    {
        $comment = KanbanTaskComment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['success' => true]);
    }

    public function updateLabels(Request $request, $id)
    {
        $task = KanbanTask::findOrFail($id);

        $request->validate([
            'labels' => 'nullable|array',
        ]);

        $task->update([
            'labels' => $request->labels,
        ]);

        return response()->json(['success' => true]);
    }

    public function storeChecklist(Request $request, $taskId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $checklist = KanbanChecklist::create([
            'task_id' => $taskId,
            'title' => $request->title,
        ]);

        return response()->json(['success' => true, 'checklist' => $checklist]);
    }

    public function destroyChecklist($id)
    {
        $checklist = KanbanChecklist::findOrFail($id);
        $checklist->delete();

        return response()->json(['success' => true]);
    }

    public function storeChecklistItem(Request $request, $checklistId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $item = KanbanChecklistItem::create([
            'checklist_id' => $checklistId,
            'title' => $request->title,
            'is_completed' => false,
        ]);

        return response()->json(['success' => true, 'item' => $item]);
    }

    public function toggleChecklistItem(Request $request, $id)
    {
        $item = KanbanChecklistItem::findOrFail($id);
        $item->update([
            'is_completed' => !$item->is_completed,
        ]);

        return response()->json(['success' => true, 'is_completed' => $item->is_completed]);
    }

    public function destroyChecklistItem($id)
    {
        $item = KanbanChecklistItem::findOrFail($id);
        $item->delete();

        return response()->json(['success' => true]);
    }

    public function uploadAttachment(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $task = KanbanTask::findOrFail($id);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            
            // Store file in public/uploads/kanban
            $file->move(public_path('uploads/kanban'), $filename);
            $filePath = 'uploads/kanban/' . $filename;

            $attachment = KanbanTaskAttachment::create([
                'task_id' => $task->id,
                'user_id' => Auth::id(),
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientOriginalExtension(),
            ]);

            // Log activity
            $this->logActivity($task->id, 'upload_attachment', ['file_name' => $attachment->file_name]);

            return response()->json(['success' => true, 'attachment' => $attachment]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function destroyAttachment($id)
    {
        $attachment = KanbanTaskAttachment::findOrFail($id);

        // Delete from local disk
        $fullPath = public_path($attachment->file_path);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        // Log activity
        $this->logActivity($attachment->task_id, 'delete_attachment', ['file_name' => $attachment->file_name]);

        $attachment->delete();

        return response()->json(['success' => true]);
    }

    public function requestDeleteTask(Request $request, $id)
    {
        $task = KanbanTask::findOrFail($id);
        $user = Auth::user();

        // Check if there is already a pending delete request for this task
        $exists = KanbanTaskDeleteRequest::where('task_id', $task->id)
            ->where('status', 'pending')
            ->exists();

        if (!$exists) {
            KanbanTaskDeleteRequest::create([
                'board_id' => $task->board_id,
                'task_id' => $task->id,
                'requested_by' => $user->id,
                'status' => 'pending',
            ]);

            // Optional: log request activity
            $this->logActivity($task->id, 'request_delete', ['requested_by' => $user->name]);
        }

        return response()->json(['success' => true]);
    }

    public function getDeleteRequests($boardId)
    {
        $board = KanbanBoard::findOrFail($boardId);
        $user = Auth::user();

        // Only board creator/owner or admin user can view delete requests
        if ($user->role !== 'Admin' && $board->created_by !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $requests = KanbanTaskDeleteRequest::with(['task', 'requester'])
            ->where('board_id', $boardId)
            ->where('status', 'pending')
            ->get()
            ->map(function ($req) {
                return [
                    'id' => $req->id,
                    'task_id' => $req->task_id,
                    'task_title' => $req->task ? $req->task->title : 'Unknown Task',
                    'requested_by_name' => $req->requester ? $req->requester->name : 'Unknown',
                    'requested_by_avatar' => $req->requester && $req->requester->image ? '/' . $req->requester->image : null,
                    'created_at' => $req->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'requests' => $requests,
        ]);
    }

    public function approveDeleteRequest($id)
    {
        $req = KanbanTaskDeleteRequest::with('board')->findOrFail($id);
        $user = Auth::user();

        // Only board creator/owner or admin user can approve delete requests
        if ($user->role !== 'Admin' && $req->board->created_by !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::transaction(function () use ($req) {
            $task = KanbanTask::find($req->task_id);
            if ($task) {
                // Decrement positions of tasks after this task in the same column
                KanbanTask::where('column_id', $task->column_id)
                    ->where('position', '>', $task->position)
                    ->decrement('position');

                $task->delete();
            }

            // Remove all requests for this task
            KanbanTaskDeleteRequest::where('task_id', $req->task_id)->delete();
        });

        return response()->json(['success' => true]);
    }

    public function rejectDeleteRequest($id)
    {
        $req = KanbanTaskDeleteRequest::with('board')->findOrFail($id);
        $user = Auth::user();

        // Only board creator/owner or admin user can reject delete requests
        if ($user->role !== 'Admin' && $req->board->created_by !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Just delete/dismiss the request record
        $req->delete();

        return response()->json(['success' => true]);
    }

    public function monitoringDocument()
    {
        $user = Auth::user();
        $board = KanbanBoard::with('members')->where('type', 'monitoring')->first();

        $isMember = $board && $board->members->contains($user->id);
        if ($user->role !== 'Admin' && $user->role !== 'Accounting' && $user->role !== 'Finance Manager' && !$isMember) {
            abort(403, 'Akses ditolak.');
        }

        if (!$board) {
            $board = KanbanBoard::create([
                'title' => 'Monitoring Document',
                'description' => 'Papan Kanban khusus untuk memantau dokumen PO/Sales Order.',
                'type' => 'monitoring',
                'created_by' => $user->id,
            ]);

            $defaultColumns = ['PO REFTECH', 'PO E-COMMERCE', 'Draft / SPK', 'Invoice Sent', 'Paid / Completed'];
            foreach ($defaultColumns as $index => $colTitle) {
                KanbanColumn::create([
                    'board_id' => $board->id,
                    'title' => $colTitle,
                    'position' => $index,
                ]);
            }
        } else {
            // Auto-cleanup orphaned tasks (kartu yang pending_po_id nya sudah terhapus di database)
            $orphanedTasks = KanbanTask::where('board_id', $board->id)
                ->whereNotNull('pending_po_id')
                ->whereDoesntHave('pendingPo')
                ->get();

            foreach ($orphanedTasks as $task) {
                foreach ($task->attachments as $attachment) {
                    $fullPath = public_path($attachment->file_path);
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                }
                $task->delete();
            }
        }

        $activePos = \App\Models\PendingPO::where('status', '<', 6)
            ->where('created_at', '>=', '2026-07-17 00:00:00')
            ->where(function ($outer) {
                $outer->where(function ($sub) {
                    // Quotation lama (id_quotation)
                    $sub->whereNotNull('id_quotation')
                        ->whereHas('quote.invoice', function ($q) {
                            $q->whereNotNull('no_invoice')->where('no_invoice', '!=', '');
                        })
                        ->whereHas('quote.payment', function ($q) {
                            $q->where('method', '!=', 'Escrow');
                        });
                })->orWhere(function ($sub) {
                    // Unit Quotation (id_unit_quotation)
                    $sub->whereNotNull('id_unit_quotation')
                        ->whereHas('unitQuotation.invoices', function ($q) {
                            $q->whereNotNull('no_invoice')->where('no_invoice', '!=', '');
                        })
                        ->whereHas('unitQuotation.payments', function ($q) {
                            $q->where('method', '!=', 'Escrow');
                        });
                });
            })
            ->with('quote.pic.client', 'unitQuotation.client')
            ->get();

        // Auto-update existing tasks titles to match new PO format if needed
        $existingTasks = KanbanTask::where('board_id', $board->id)
            ->whereNotNull('pending_po_id')
            ->with(['pendingPo.quote.pic.client', 'pendingPo.unitQuotation.client', 'assignees'])
            ->get();

        // Batch semua lookup yang tadinya di-query per-PO di dalam loop (N+1)
        $quotationIds = $activePos->pluck('id_quotation')
            ->merge($existingTasks->pluck('pendingPo.id_quotation'))
            ->filter()
            ->unique();

        $unitQuotationIds = $activePos->pluck('id_unit_quotation')
            ->merge($existingTasks->pluck('pendingPo.id_unit_quotation'))
            ->filter()
            ->unique();

        $invoicePoByQuotation = \App\Models\Invoice::whereIn('id_quotation', $quotationIds)
            ->whereNotNull('no_po')->where('no_po', '!=', '')
            ->pluck('no_po', 'id_quotation');

        $invoicePoByUnitQuotation = \App\Models\Invoice::whereIn('id_unit_quotation', $unitQuotationIds)
            ->whereNotNull('no_po')->where('no_po', '!=', '')
            ->pluck('no_po', 'id_unit_quotation');

        $existingTaskPoIds = KanbanTask::where('board_id', $board->id)
            ->whereNotNull('pending_po_id')
            ->pluck('pending_po_id')
            ->flip();

        $columnsByTitle = KanbanColumn::where('board_id', $board->id)
            ->whereIn('title', ['PO E-COMMERCE', 'PO REFTECH'])
            ->get()
            ->keyBy('title');

        $positionByColumn = [];
        foreach ($columnsByTitle as $column) {
            $positionByColumn[$column->id] = KanbanTask::where('column_id', $column->id)->count();
        }

        foreach ($activePos as $po) {
            if (!$existingTaskPoIds->has($po->id)) {
                $isUnitNew = (bool) $po->id_unit_quotation;
                $idSales = $isUnitNew
                    ? ($po->unitQuotation ? $po->unitQuotation->id_sales : null)
                    : ($po->quote ? $po->quote->id_sales : null);
                $targetColTitle = ($idSales == 16) ? 'PO E-COMMERCE' : 'PO REFTECH';

                $column = $columnsByTitle->get($targetColTitle);

                if ($column) {
                    $poNumber = $isUnitNew
                        ? ($invoicePoByUnitQuotation->get($po->id_unit_quotation) ?: ($po->no_pending ?? 'No PO'))
                        : ($invoicePoByQuotation->get($po->id_quotation) ?: ($po->no_pending ?? 'No PO'));
                    $companyName = 'Unknown Client';
                    if ($isUnitNew && $po->unitQuotation && $po->unitQuotation->client) {
                        $companyName = $po->unitQuotation->client->company;
                    } elseif (!$isUnitNew && $po->quote && $po->quote->pic && $po->quote->pic->client) {
                        $companyName = $po->quote->pic->client->company;
                    }

                    $taskTitle = "[$poNumber] - $companyName";

                    $newTask = KanbanTask::create([
                        'board_id' => $board->id,
                        'column_id' => $column->id,
                        'pending_po_id' => $po->id,
                        'title' => $taskTitle,
                        'description' => null,
                        'position' => $positionByColumn[$column->id]++,
                    ]);

                    if ($idSales) {
                        $newTask->assignees()->sync([$idSales]);
                    }
                }
            }
        }

        foreach ($existingTasks as $task) {
            $po = $task->pendingPo;
            if ($po) {
                $isUnit = (bool) $po->id_unit_quotation;
                $quoteRef = $isUnit ? $po->unitQuotation : $po->quote;
                $client = $isUnit
                    ? ($quoteRef->client ?? null)
                    : (($quoteRef && $quoteRef->pic) ? $quoteRef->pic->client : null);

                $poNumber = $isUnit
                    ? ($invoicePoByUnitQuotation->get($po->id_unit_quotation) ?: ($po->no_pending ?? 'No PO'))
                    : ($invoicePoByQuotation->get($po->id_quotation) ?: ($po->no_pending ?? 'No PO'));
                $companyName = $client ? $client->company : 'Unknown Client';

                $expectedTitle = "[$poNumber] - $companyName";
                if ($task->title !== $expectedTitle) {
                    $task->title = $expectedTitle;
                    $task->save();
                }

                if ($task->assignees->isEmpty() && $quoteRef && $quoteRef->id_sales) {
                    $task->assignees()->sync([$quoteRef->id_sales]);
                }
            }
        }

        $users = User::where('active', '1')->orderBy('name')->get();
        $salesUsers = User::where('role', 'Sales')->where('active', '1')->orderBy('name')->get();
        $accountingUsers = User::where('role', 'Accounting')->where('active', '1')->orderBy('name')->with('handledSales')->get();

        if ($user->role === 'Admin') {
            $myBoards = KanbanBoard::where('type', 'dynamic')->orderBy('title')->get();
        } else {
            $myBoards = $user->kanbanBoards()->where('type', 'dynamic')->orderBy('title')->get();
        }

        return view('pages.kanban.board', compact('board', 'users', 'myBoards', 'salesUsers', 'accountingUsers'));
    }

    public function updateAccountingSalesMapping(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'Admin' && $user->role !== 'Accounting') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'mappings' => 'array',
            'mappings.*.id_accounting' => 'required|exists:users,id',
            'mappings.*.sales_ids' => 'array',
            'mappings.*.sales_ids.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->input('mappings', []) as $mapping) {
                $accounting = User::find($mapping['id_accounting']);
                $accounting->handledSales()->sync($mapping['sales_ids'] ?? []);
            }
        });

        return response()->json(['success' => true, 'message' => 'Mapping Accounting-Sales berhasil disimpan!']);
    }

    public function getAvailablePOs()
    {
        $user = Auth::user();
        $board = KanbanBoard::with('members')->where('type', 'monitoring')->first();
        $isMember = $board && $board->members->contains($user->id);
        if ($user->role !== 'Admin' && $user->role !== 'Accounting' && $user->role !== 'Finance Manager' && !$isMember) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $existingPoIds = $board ? KanbanTask::where('board_id', $board->id)->whereNotNull('pending_po_id')->pluck('pending_po_id')->toArray() : [];

        $pos = \App\Models\PendingPO::where('status', '<', 6)
            ->whereNotIn('id', $existingPoIds)
            ->where('created_at', '>=', '2026-07-17 00:00:00')
            ->where(function ($outer) {
                $outer->where(function ($sub) {
                    $sub->whereNotNull('id_quotation')
                        ->whereHas('quote.invoice', function($q) {
                            $q->whereNotNull('no_invoice')->where('no_invoice', '!=', '');
                        })
                        ->whereHas('quote.payment', function($q) {
                            $q->where('method', '!=', 'Escrow');
                        });
                })->orWhere(function ($sub) {
                    $sub->whereNotNull('id_unit_quotation')
                        ->whereHas('unitQuotation.invoices', function($q) {
                            $q->whereNotNull('no_invoice')->where('no_invoice', '!=', '');
                        })
                        ->whereHas('unitQuotation.payments', function($q) {
                            $q->where('method', '!=', 'Escrow');
                        });
                });
            })
            ->with(['quote.pic.client', 'quote.sales', 'quote.invoice', 'unitQuotation.client', 'unitQuotation.sales', 'unitQuotation.invoices'])
            ->get();

        $invoicePoByQuotation = \App\Models\Invoice::whereIn('id_quotation', $pos->pluck('id_quotation')->filter()->unique())
            ->whereNotNull('no_po')->where('no_po', '!=', '')
            ->pluck('no_po', 'id_quotation');

        $invoicePoByUnitQuotation = \App\Models\Invoice::whereIn('id_unit_quotation', $pos->pluck('id_unit_quotation')->filter()->unique())
            ->whereNotNull('no_po')->where('no_po', '!=', '')
            ->pluck('no_po', 'id_unit_quotation');

        $pos = $pos->map(function ($po) use ($invoicePoByQuotation, $invoicePoByUnitQuotation) {
            $isUnit = (bool) $po->id_unit_quotation;
            $quoteRef = $isUnit ? $po->unitQuotation : $po->quote;

            $poNumber = $isUnit
                ? ($invoicePoByUnitQuotation->get($po->id_unit_quotation) ?: ($po->no_pending ?? 'No PO'))
                : ($invoicePoByQuotation->get($po->id_quotation) ?: ($po->no_pending ?? 'No PO'));

            $companyName = 'Unknown Client';
            if ($isUnit && $po->unitQuotation && $po->unitQuotation->client) {
                $companyName = $po->unitQuotation->client->company;
            } elseif (!$isUnit && $po->quote && $po->quote->pic && $po->quote->pic->client) {
                $companyName = $po->quote->pic->client->company;
            }

            $salesName = $quoteRef && $quoteRef->sales ? $quoteRef->sales->name : 'N/A';

            $entityType = null;
            $invoices = $isUnit
                ? ($quoteRef ? $quoteRef->invoices : collect())
                : ($quoteRef ? $quoteRef->invoice : collect());

            $firstInvoice = $invoices ? ($invoices->whereNotNull('no_invoice')->where('no_invoice', '!=', '')->first() ?: $invoices->first()) : null;
            if ($firstInvoice) {
                if ($firstInvoice->flag === 'Kojisha' || ($firstInvoice->no_invoice && str_contains($firstInvoice->no_invoice, '/KII/'))) {
                    $entityType = 'KII';
                } elseif ($firstInvoice->flag === 'Reftech' || ($firstInvoice->no_invoice && str_contains($firstInvoice->no_invoice, '/RJO/'))) {
                    $entityType = 'RJO';
                }
            }

            if (!$entityType && $quoteRef) {
                $client = $isUnit
                    ? ($quoteRef->client ?? null)
                    : (($quoteRef->pic) ? $quoteRef->pic->client : null);
                $flag = $quoteRef->flag ?? ($client ? $client->info : null);
                if ($flag === 'Kojisha') {
                    $entityType = 'KII';
                } elseif ($flag === 'Reftech') {
                    $entityType = 'RJO';
                }
            }

            if (!$entityType) {
                $entityType = 'RJO';
            }

            return [
                'id' => $po->id,
                'no_po' => $poNumber,
                'company' => $companyName,
                'sales' => $salesName,
                'entity_type' => $entityType,
            ];
        });

        return response()->json([
            'success' => true,
            'pos' => $pos,
        ]);
    }

    public function checkNewCards($lastTaskId)
    {
        $user = Auth::user();
        $board = KanbanBoard::with('members')->where('type', 'monitoring')->first();
        if (!$board) {
            return response()->json(['success' => true, 'new_cards' => 0]);
        }

        $isMember = $board->members->contains($user->id);
        if ($user->role !== 'Admin' && $user->role !== 'Accounting' && $user->role !== 'Finance Manager' && !$isMember) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $newCardsCount = KanbanTask::where('board_id', $board->id)
            ->where('id', '>', $lastTaskId)
            ->count();

        return response()->json([
            'success' => true,
            'new_cards' => $newCardsCount,
        ]);
    }

    private function logActivity($taskId, $type, $data = null)
    {
        KanbanTaskActivity::create([
            'task_id' => $taskId,
            'user_id' => Auth::id(),
            'activity_type' => $type,
            'activity_data' => $data,
        ]);
    }
}
