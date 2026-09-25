<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Bank;
use App\Models\DetailExpense;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\HrSemesterBonus;
use App\Models\HrSemesterBonusItem;
use App\Models\HrSemesterBonusRecipient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BonusController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $semester = $request->input('semester');
        $status = $request->input('status');

        $query = HrSemesterBonus::with(['generator', 'approver', 'expense.bank'])
            ->withCount('recipients');

        if ($year) {
            $query->where('year', $year);
        }

        if ($semester) {
            $query->where('semester', (int) $semester);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $bonuses = $query->orderByDesc('year')
            ->orderByDesc('semester')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        // Summary statistics
        $stats = [
            'total_batches' => HrSemesterBonus::where('year', $year)->count(),
            'total_net_amount' => (float) HrSemesterBonus::where('year', $year)->sum('total_net_amount'),
            'total_paid_amount' => (float) HrSemesterBonus::where('year', $year)->where('status', 'Paid')->sum('total_net_amount'),
            'total_recipients' => (int) HrSemesterBonusRecipient::whereHas('bonus', function ($q) use ($year) {
                $q->where('year', $year);
            })->count(),
        ];

        // Available years
        $availableYears = HrSemesterBonus::select('year')->distinct()->pluck('year')->toArray();
        $availableYears = array_unique(array_merge([(int)date('Y'), (int)date('Y') + 1, (int)date('Y') - 1], $availableYears));
        rsort($availableYears);

        // Active employees count
        $activeEmployeesCount = Employee::where('employment_status', '!=', 'Resign')->count();

        return view('pages.hr.bonuses.index', compact(
            'bonuses',
            'stats',
            'year',
            'semester',
            'status',
            'availableYears',
            'activeEmployeesCount'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'semester' => 'required|in:1,2',
            'year' => 'required|integer|min:2020|max:2099',
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'include_all_employees' => 'nullable|boolean',
        ]);

        $semester = (int) $validated['semester'];
        $year = (int) $validated['year'];
        $baseCode = "BNS-SEM{$semester}-{$year}";

        // Cek keunikan kode
        $code = $baseCode;
        $counter = 1;
        while (HrSemesterBonus::where('code', $code)->exists()) {
            $counter++;
            $code = "{$baseCode}-v{$counter}";
        }

        DB::beginTransaction();
        try {
            $bonus = HrSemesterBonus::create([
                'code' => $code,
                'title' => $validated['title'],
                'semester' => $semester,
                'year' => $year,
                'status' => 'Draft',
                'total_addition' => 0,
                'total_deduction' => 0,
                'total_net_amount' => 0,
                'total_recipients_count' => 0,
                'generated_by' => Auth::id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            // Jika user memilih untuk menyertakan semua karyawan aktif
            if ($request->boolean('include_all_employees', true)) {
                $employees = Employee::where('employment_status', '!=', 'Resign')
                    ->orderBy('id', 'asc')
                    ->get();

                foreach ($employees as $emp) {
                    $recipient = HrSemesterBonusRecipient::create([
                        'bonus_id' => $bonus->id,
                        'employee_id' => $emp->id,
                        'total_addition' => 0,
                        'total_deduction' => 0,
                        'net_bonus' => 0,
                        'payment_status' => 'Pending',
                    ]);

                    // Buat 3 default placeholder komponen dinamis (bisa diedit namanya atau nominalnya di UI)
                    HrSemesterBonusItem::create([
                        'recipient_id' => $recipient->id,
                        'name' => 'Bonus Capaian Penjualan',
                        'type' => 'addition',
                        'amount' => 0,
                        'order' => 1,
                    ]);

                    HrSemesterBonusItem::create([
                        'recipient_id' => $recipient->id,
                        'name' => 'Insentif Evaluasi Kinerja',
                        'type' => 'addition',
                        'amount' => 0,
                        'order' => 2,
                    ]);

                    HrSemesterBonusItem::create([
                        'recipient_id' => $recipient->id,
                        'name' => 'Insentif Kedisiplinan & Presensi',
                        'type' => 'addition',
                        'amount' => 0,
                        'order' => 3,
                    ]);
                }

                $bonus->recalculateTotals();
            }

            DB::commit();
            return redirect()->route('hr.bonuses.show', $bonus->id)
                ->with('success', "Batch Perhitungan Bonus Semester {$semester} {$year} ({$bonus->code}) berhasil dibuat.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat batch bonus: ' . $e->getMessage());
        }
    }

    public function show(HrSemesterBonus $bonus)
    {
        $bonus->load([
            'generator',
            'approver',
            'expense.bank',
            'recipients.employee.user',
            'recipients.employee.department',
            'recipients.employee.position',
            'recipients.items'
        ]);

        $existingEmployeeIds = $bonus->recipients->pluck('employee_id')->toArray();
        $availableEmployees = Employee::with(['user', 'department'])
            ->where('employment_status', '!=', 'Resign')
            ->whereNotIn('id', $existingEmployeeIds)
            ->orderBy('id', 'asc')
            ->get();

        $banks = Bank::orderBy('bank')->get();
        $accounts = Account::where('category', 'Expense')->orderBy('code')->get();

        return view('pages.hr.bonuses.show', compact('bonus', 'availableEmployees', 'banks', 'accounts'));
    }

    public function updateStatus(Request $request, HrSemesterBonus $bonus)
    {
        $validated = $request->validate([
            'status' => 'required|in:Draft,Confirmed,Approved,Paid',
        ]);

        $status = $validated['status'];
        $updates = ['status' => $status];

        if ($status === 'Approved') {
            $updates['approved_by'] = Auth::id();
            $updates['approved_at'] = Carbon::now();
        }

        $bonus->update($updates);

        return redirect()->back()->with('success', "Status batch bonus diubah menjadi '{$status}'.");
    }

    public function addEmployee(Request $request, HrSemesterBonus $bonus)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        if ($bonus->status === 'Paid') {
            return redirect()->back()->with('error', 'Tidak dapat menambah karyawan pada batch bonus yang sudah dibayar (Paid).');
        }

        $exists = HrSemesterBonusRecipient::where('bonus_id', $bonus->id)
            ->where('employee_id', $validated['employee_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('info', 'Karyawan ini sudah terdaftar dalam batch bonus.');
        }

        DB::beginTransaction();
        try {
            $recipient = HrSemesterBonusRecipient::create([
                'bonus_id' => $bonus->id,
                'employee_id' => $validated['employee_id'],
                'total_addition' => 0,
                'total_deduction' => 0,
                'net_bonus' => 0,
                'payment_status' => 'Pending',
            ]);

            HrSemesterBonusItem::create([
                'recipient_id' => $recipient->id,
                'name' => 'Bonus Capaian Penjualan',
                'type' => 'addition',
                'amount' => 0,
                'order' => 1,
            ]);

            HrSemesterBonusItem::create([
                'recipient_id' => $recipient->id,
                'name' => 'Insentif Evaluasi Kinerja',
                'type' => 'addition',
                'amount' => 0,
                'order' => 2,
            ]);

            $bonus->recalculateTotals();

            DB::commit();
            return redirect()->back()->with('success', 'Karyawan berhasil ditambahkan ke dalam daftar penerima bonus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan karyawan: ' . $e->getMessage());
        }
    }

    public function removeRecipient(HrSemesterBonusRecipient $recipient)
    {
        $bonus = $recipient->bonus;
        if ($bonus->status === 'Paid') {
            return redirect()->back()->with('error', 'Tidak dapat menghapus karyawan pada batch bonus yang sudah dibayar.');
        }

        $recipient->delete();
        $bonus->recalculateTotals();

        return redirect()->back()->with('success', 'Karyawan berhasil dihapus dari daftar penerima bonus.');
    }

    public function updateRecipientItems(Request $request, HrSemesterBonusRecipient $recipient)
    {
        $bonus = $recipient->bonus;
        if ($bonus->status === 'Paid') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Batch sudah dibayar (Paid), tidak dapat mengubah komponen.'], 422);
            }
            return redirect()->back()->with('error', 'Batch bonus sudah berstatus Paid.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|integer',
            'items.*.name' => 'required|string|max:255',
            'items.*.type' => 'required|in:addition,deduction',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $recipient->notes = $validated['notes'] ?? null;
            $recipient->save();

            $existingItemIds = $recipient->items()->pluck('id')->toArray();
            $updatedItemIds = [];

            $itemsData = $validated['items'] ?? [];
            foreach ($itemsData as $index => $itemData) {
                $itemId = $itemData['id'] ?? null;
                $data = [
                    'recipient_id' => $recipient->id,
                    'name' => $itemData['name'],
                    'type' => $itemData['type'],
                    'amount' => (float) $itemData['amount'],
                    'notes' => $itemData['notes'] ?? null,
                    'order' => $index + 1,
                ];

                if ($itemId && in_array($itemId, $existingItemIds)) {
                    $item = HrSemesterBonusItem::find($itemId);
                    if ($item && $item->recipient_id === $recipient->id) {
                        $item->update($data);
                        $updatedItemIds[] = $item->id;
                    }
                } else {
                    $newItem = HrSemesterBonusItem::create($data);
                    $updatedItemIds[] = $newItem->id;
                }
            }

            // Hapus item yang dibuang dari UI
            $toDelete = array_diff($existingItemIds, $updatedItemIds);
            if (!empty($toDelete)) {
                HrSemesterBonusItem::whereIn('id', $toDelete)->delete();
            }

            $recipient->recalculateTotals();

            DB::commit();

            if ($request->ajax()) {
                $recipient->load('items', 'employee.user');
                return response()->json([
                    'success' => true,
                    'message' => 'Komponen bonus berhasil diperbarui!',
                    'recipient' => [
                        'id' => $recipient->id,
                        'total_addition' => $recipient->total_addition,
                        'total_deduction' => $recipient->total_deduction,
                        'net_bonus' => $recipient->net_bonus,
                        'total_addition_formatted' => number_format($recipient->total_addition, 0, ',', '.'),
                        'total_deduction_formatted' => number_format($recipient->total_deduction, 0, ',', '.'),
                        'net_bonus_formatted' => number_format($recipient->net_bonus, 0, ',', '.'),
                        'items' => $recipient->items,
                    ],
                    'bonus' => [
                        'total_addition' => $bonus->total_addition,
                        'total_deduction' => $bonus->total_deduction,
                        'total_net_amount' => $bonus->total_net_amount,
                        'total_addition_formatted' => number_format($bonus->total_addition, 0, ',', '.'),
                        'total_deduction_formatted' => number_format($bonus->total_deduction, 0, ',', '.'),
                        'total_net_amount_formatted' => number_format($bonus->total_net_amount, 0, ',', '.'),
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Rincian komponen bonus karyawan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal memperbarui komponen bonus: ' . $e->getMessage());
        }
    }

    public function postToExpense(Request $request, HrSemesterBonus $bonus)
    {
        if ($bonus->expense_id) {
            return redirect()->back()->with('error', 'Batch bonus ini sudah pernah dibukukan ke Finance Expense (No. Voucher: ' . $bonus->expense?->no_expense . ').');
        }

        $validated = $request->validate([
            'id_bank' => 'required|exists:bank,id',
            'id_account' => 'required|exists:account,id',
            'payment_date' => 'required|date',
            'memo' => 'nullable|string|max:500',
        ]);

        $bank = Bank::findOrFail($validated['id_bank']);
        $totalAmount = (float) $bonus->total_net_amount;

        if ($totalAmount <= 0) {
            return redirect()->back()->with('error', 'Total bonus bernilai Rp 0. Tidak dapat diposting ke Finance Expense.');
        }

        if ($bank->saldo < $totalAmount) {
            return redirect()->back()->with('error', 'Saldo bank ' . $bank->bank . ' tidak mencukupi (Tersedia: Rp ' . number_format($bank->saldo, 0, ',', '.') . ', Dibutuhkan: Rp ' . number_format($totalAmount, 0, ',', '.') . ').');
        }

        DB::beginTransaction();
        try {
            // 1. Generate nomor voucher expense otomatis: EXP001-IX-2026
            $date = Carbon::parse($validated['payment_date']);
            $year = $date->year;
            $month = $date->month;
            $romanMonths = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $roman = $romanMonths[$month] ?? 'I';

            $count = Expense::whereYear('date', $year)->count();
            $noExpense = 'EXP' . str_pad($count + 1, 3, '0', STR_PAD_LEFT) . '-' . $roman . '-' . $year;

            // 2. Potong saldo Bank
            $bank->saldo -= $totalAmount;
            $bank->save();

            // 3. Simpan Voucher Expense Header
            $memo = $validated['memo'] ?: "Pembayaran Bonus Semester Karyawan - {$bonus->title} ({$bonus->code})";
            $expense = new Expense();
            $expense->id_bank = $bank->id;
            $expense->no_expense = $noExpense;
            $expense->no_invoice = $bonus->code;
            $expense->memo = $memo;
            $expense->date = $validated['payment_date'];
            $expense->amount = $totalAmount;
            $expense->save();

            // 4. Simpan Detail Jurnal Beban COA (Biaya Bonus & Insentif Karyawan)
            $detailExpense = new DetailExpense();
            $detailExpense->id_expense = $expense->id;
            $detailExpense->id_account = $validated['id_account'];
            $detailExpense->memo = "Bonus Karyawan {$bonus->title}";
            $detailExpense->amount = $totalAmount;
            $detailExpense->save();

            // 5. Update status Bonus menjadi Paid & hubungkan expense_id
            $bonus->update([
                'expense_id' => $expense->id,
                'status' => 'Paid',
                'payment_date' => $validated['payment_date'],
            ]);

            // 6. Tandai seluruh rincian bonus karyawan telah dibayar (Paid)
            $bonus->recipients()->update([
                'payment_status' => 'Paid',
                'paid_at' => Carbon::now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', "Batch Bonus Semester berhasil dibukukan ke Finance Expense dengan voucher #{$noExpense}! Saldo bank {$bank->bank} dipotong sebesar Rp " . number_format($totalAmount, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memposting bonus ke Finance Expense: ' . $e->getMessage());
        }
    }

    public function slip(HrSemesterBonusRecipient $recipient)
    {
        $recipient->load([
            'bonus.generator',
            'bonus.approver',
            'employee.user',
            'employee.department',
            'employee.position',
            'items'
        ]);

        return view('pages.hr.bonuses.slip', compact('recipient'));
    }

    public function destroy(HrSemesterBonus $bonus)
    {
        if ($bonus->status === 'Paid') {
            return redirect()->back()->with('error', 'Tidak dapat menghapus batch bonus yang sudah dibayar (Paid).');
        }

        $bonus->delete();
        return redirect()->route('hr.bonuses.index')->with('success', 'Batch bonus berhasil dihapus.');
    }
}
