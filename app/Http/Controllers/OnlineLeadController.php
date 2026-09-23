<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\OnlineLead;
use App\Models\OnlineLeadFollowUp;
use App\Models\OnlineLeadFollowUpItem;
use App\Models\Pic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OnlineLeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isManagerOrAdmin = $user && (in_array($user->role, ['Admin', 'Developer', 'Sales Manager'])
            || (method_exists($user, 'isDeveloper') && $user->isDeveloper()));

        // Base query with permission scope
        $query = OnlineLead::with(['sales', 'client', 'quotation', 'unitQuotation', 'followUps.user'])
            ->visibleTo($user);

        // Sales filter (for managers/admins)
        if ($isManagerOrAdmin && $request->filled('id_sales')) {
            $query->where('id_sales', $request->id_sales);
        }

        // Channel filter
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        // Customer Type (User / Reseller) filter
        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search filter (name, phone, company, product_interest)
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Date range filter
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('date', [$request->date_from, $request->date_to]);
        } elseif ($request->filled('month') || $request->filled('year')) {
            $query->whereYear('date', $year)->whereMonth('date', $month);
        }

        // Calculate KPI summaries based on the filtered query (without pagination)
        $statsQuery = clone $query;
        $allMatching = $statsQuery->get();

        $totalLeads = $allMatching->count();
        $userCount = $allMatching->where('customer_type', 'User')->count();
        $resellerCount = $allMatching->where('customer_type', 'Reseller')->count();
        $convertedCount = $allMatching->whereIn('status', ['deal', 'quoted'])->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedCount / $totalLeads) * 100, 1) : 0;

        // Channel breakdown
        $channelStats = $allMatching->groupBy('channel')->map(function ($items) {
            return [
                'count' => $items->count(),
                'deals' => $items->whereIn('status', ['deal', 'quoted'])->count(),
            ];
        });

        // Paginate results
        $leads = $query->orderByDesc('date')->orderByDesc('id')->paginate(25)->withQueryString();

        // Data for dropdowns
        $channels = OnlineLead::CHANNELS;
        $statuses = OnlineLead::STATUSES;
        $salesList = $isManagerOrAdmin
            ? User::where(function ($q) {
                $q->where('role', 'Sales')->orWhere('id', 16)->orWhere('id', 38);
            })->where('active', '1')->orderBy('name')->get()
            : collect();

        return view('pages.ecommerce.leads.index', compact(
            'leads',
            'totalLeads',
            'userCount',
            'resellerCount',
            'convertedCount',
            'conversionRate',
            'channelStats',
            'channels',
            'statuses',
            'salesList',
            'isManagerOrAdmin',
            'month',
            'year'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'channel' => 'required|string|max:80',
            'customer_type' => 'required|in:User,Reseller',
            'name' => 'required|string|max:255',
            'phone' => OnlineLead::isMarketplaceChannel($request->channel) ? 'nullable|string|max:50' : 'required|string|max:50',
            'company' => 'nullable|string|max:255',
            'product_interest' => 'nullable|string',
            'notes' => 'nullable|string',
            'date' => 'nullable|date',
            'id_sales' => 'nullable|integer|exists:users,id',
        ]);

        $user = Auth::user();
        $idSales = $user?->id ?? 1;

        // If manager/admin specifies sales owner
        if ($user && in_array($user->role, ['Admin', 'Developer', 'Sales Manager']) && $request->filled('id_sales')) {
            $idSales = (int) $request->id_sales;
        }

        $lead = OnlineLead::create([
            'id_sales' => $idSales,
            'channel' => $request->channel,
            'customer_type' => $request->customer_type,
            'name' => $request->name,
            'phone' => $request->phone,
            'company' => $request->company,
            'product_interest' => $request->product_interest,
            'notes' => $request->notes,
            'status' => 'new',
            'date' => $request->date ?: Carbon::today()->toDateString(),
        ]);

        // Kebutuhan pertama (dari chat awal) langsung dicatat sebagai follow-up
        // #1, biar histori kebutuhan lead ini utuh dari awal — bukan cuma yang
        // nyusul lewat "Follow Up & Kebutuhan" doang.
        $this->seedInitialFollowUp($lead);

        return redirect()->back()->with('success', "Lead baru dari {$lead->name} ({$lead->channel}) berhasil dicatat.");
    }

    /**
     * Bikin entri follow-up pertama dari data kebutuhan awal (product_interest/notes)
     * saat lead baru dibuat, supaya jadi baris pertama di timeline "Follow Up & Kebutuhan"
     * — dipanggil sekali saat create, dan juga dari migration backfill data lama.
     */
    private function seedInitialFollowUp(OnlineLead $lead): void
    {
        $itemName = trim($lead->product_interest ?? '');
        if ($itemName === '') {
            return;
        }

        $followUp = OnlineLeadFollowUp::create([
            'id_lead' => $lead->id,
            'id_user' => $lead->id_sales,
            'date' => $lead->date,
            'note' => $lead->notes ?: 'Kebutuhan awal saat chat pertama masuk.',
        ]);

        OnlineLeadFollowUpItem::create([
            'id_follow_up' => $followUp->id,
            'item_name' => $itemName,
            'status' => 'pending',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $lead = OnlineLead::findOrFail($id);
        $user = Auth::user();

        // Authorization check
        $canEdit = !$user || in_array($user->role, ['Admin', 'Developer', 'Sales Manager'])
            || $lead->id_sales === $user->id;

        if (!$canEdit) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah lead ini.');
        }

        $request->validate([
            'channel' => 'required|string|max:80',
            'customer_type' => 'required|in:User,Reseller',
            'name' => 'required|string|max:255',
            'phone' => OnlineLead::isMarketplaceChannel($request->channel) ? 'nullable|string|max:50' : 'required|string|max:50',
            'company' => 'nullable|string|max:255',
            'product_interest' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:new,contacted,quoted,deal,lost',
            'date' => 'required|date',
        ]);

        $lead->update([
            'channel' => $request->channel,
            'customer_type' => $request->customer_type,
            'name' => $request->name,
            'phone' => $request->phone,
            'company' => $request->company,
            'product_interest' => $request->product_interest,
            'notes' => $request->notes,
            'status' => $request->status,
            'date' => $request->date,
        ]);

        return redirect()->back()->with('success', "Data lead #{$lead->id} berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $lead = OnlineLead::findOrFail($id);
        $user = Auth::user();

        $canDelete = !$user || in_array($user->role, ['Admin', 'Developer', 'Sales Manager'])
            || $lead->id_sales === $user->id;

        if (!$canDelete) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus lead ini.');
        }

        $lead->delete();

        return redirect()->back()->with('success', 'Data lead berhasil dihapus.');
    }

    /**
     * Catat 1x Follow Up baru untuk sebuah lead, berikut item-item kebutuhannya.
     * 1x follow-up bisa berisi banyak item, masing-masing status provided/not_provided
     * sendiri-sendiri (misal minta 5 item, yang bisa di-provide cuma 1).
     */
    public function storeFollowUp(Request $request, $id)
    {
        $lead = OnlineLead::findOrFail($id);
        $user = Auth::user();

        $canEdit = !$user || in_array($user->role, ['Admin', 'Developer', 'Sales Manager'])
            || $lead->id_sales === $user->id;

        if (!$canEdit) {
            abort(403, 'Anda tidak memiliki akses untuk mencatat follow-up lead ini.');
        }

        $request->validate([
            'date' => 'nullable|date',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.qty' => 'nullable|string|max:50',
            'items.*.status' => 'required|in:pending,provided,not_provided',
            'items.*.note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $lead, $user) {
            $followUp = OnlineLeadFollowUp::create([
                'id_lead' => $lead->id,
                'id_user' => $user?->id,
                'date' => $request->date ?: Carbon::today()->toDateString(),
                'note' => $request->note,
            ]);

            foreach ($request->input('items', []) as $item) {
                if (empty(trim($item['item_name'] ?? ''))) {
                    continue;
                }
                OnlineLeadFollowUpItem::create([
                    'id_follow_up' => $followUp->id,
                    'item_name' => $item['item_name'],
                    'qty' => $item['qty'] ?? null,
                    'status' => $item['status'] ?? 'pending',
                    'note' => $item['note'] ?? null,
                ]);
            }
        });

        return redirect()->back()->with('success', "Follow-up baru untuk {$lead->name} berhasil dicatat.");
    }

    /**
     * Update status 1 item kebutuhan (provided / not_provided / pending) tanpa
     * perlu buka form follow-up baru — dipakai dari dropdown status di timeline.
     */
    public function updateFollowUpItemStatus(Request $request, $itemId)
    {
        $item = OnlineLeadFollowUpItem::with('followUp.lead')->findOrFail($itemId);
        $lead = $item->followUp->lead;
        $user = Auth::user();

        $canEdit = !$user || in_array($user->role, ['Admin', 'Developer', 'Sales Manager'])
            || ($lead && $lead->id_sales === $user->id);

        if (!$canEdit) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,provided,not_provided',
        ]);

        $item->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'status' => $item->status,
            'status_meta' => $item->status_meta,
        ]);
    }

    /**
     * Convert an Online Lead to a formal Client & PIC record in Reftech master database.
     */
    public function convertToClient(Request $request, $id)
    {
        $lead = OnlineLead::findOrFail($id);
        $user = Auth::user();

        $canConvert = !$user || in_array($user->role, ['Admin', 'Developer', 'Sales Manager'])
            || $lead->id_sales === $user->id;

        if (!$canConvert) {
            abort(403, 'Anda tidak memiliki akses untuk mengonversi lead ini.');
        }

        DB::beginTransaction();
        try {
            // Determine company name (fall back to Person's name if not provided)
            $companyName = !empty($lead->company) ? trim($lead->company) : 'Personal - ' . trim($lead->name);

            // Determine entity info (Reftech or Kojisha) based on channel name
            $entityInfo = str_contains($lead->channel, 'Kojisha') ? 'Kojisha' : 'Reftech';

            // Check if client already exists with this phone (kalau ada) atau company
            $existingClient = Client::where('company', $companyName)
                ->when(!empty($lead->phone), fn ($q) => $q->orWhere('phone', $lead->phone))
                ->first();

            if ($existingClient) {
                $client = $existingClient;
            } else {
                $client = new Client();
                $client->id_sales = $lead->id_sales;
                $client->id_issues = 1; // General Inquiry / Default issue
                $client->id_support = null;
                $client->company = $companyName;
                $client->phone = substr((string) $lead->phone, 0, 15);
                $client->ru = $lead->customer_type; // 'User' or 'Reseller'
                $client->unit = '-';
                $client->image = 'profile.jpg';
                $client->source = substr($lead->channel, 0, 15);
                $client->source_detail = 'Converted from Online Lead #' . $lead->id;
                $client->created_date = Carbon::today()->toDateString();
                $client->role = 'Leads';
                $client->info = $entityInfo;
                $client->address = '-';
                $client->area = 'Online';
                $client->week = (int) ceil(Carbon::today()->day / 7);
                $client->save();
            }

            // Ensure PIC exists for this client
            $pic = Pic::where('id_client', $client->id)
                ->where(function ($q) use ($lead) {
                    $q->where('phone_pic', $lead->phone)->orWhere('name_pic', $lead->name);
                })
                ->first();

            if (!$pic) {
                $pic = Pic::create([
                    'id_client' => $client->id,
                    'name_pic' => substr($lead->name, 0, 255),
                    'phone_pic' => substr((string) $lead->phone, 0, 15),
                    'position' => $lead->customer_type === 'Reseller' ? 'Reseller' : 'Purchasing',
                    'email_pic' => '-',
                ]);
            }

            // Update lead record
            $lead->id_client = $client->id;
            $lead->status = 'deal';
            $lead->converted_at = Carbon::now();
            $lead->save();

            DB::commit();

            // Redirect to quotation creation page with client and pic preselected if available
            $redirectUrl = route('quotation.index');
            return redirect()->route('online-leads.index')->with(
                'success',
                "Lead {$lead->name} berhasil di-convert menjadi Client #{$client->id} ({$client->company}). Data siap digunakan untuk pembuatan penawaran."
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengonversi lead: ' . $e->getMessage());
        }
    }
}
