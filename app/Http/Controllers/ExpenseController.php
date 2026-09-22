<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use App\Models\DetailExpense;
use App\Models\DetailInventoryAdj;
use App\Models\DetailProduct;
use App\Models\Expanse;
use App\Models\Expense;
use App\Models\FixedAsset;
use App\Models\LabaRugi;
use App\Models\Payment;
use App\Models\ProductIn;
use App\Models\Quotation;
use App\Models\SerialProduct;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class Expensecontroller extends Controller
{
    public function indexAccount()
    {
        $account = Account::where('level', 1)->get();
        $prim = Account::where('level', 1)->get();

        // KPI Metrics for Account COA
        $totalAccounts = Account::count();
        $headerAccounts = Account::where('level', 1)->count();
        $detailAccounts = Account::where('level', '!=', 1)->count();
        $categoriesCount = Account::distinct('category')->whereNotNull('category')->count('category');
        $availableCategories = Account::select('category')->whereNotNull('category')->groupBy('category')->orderBy('category')->pluck('category');

        return view('pages.finance.account.index', compact(
            'account',
            'prim',
            'totalAccounts',
            'headerAccounts',
            'detailAccounts',
            'categoriesCount',
            'availableCategories'
        ));
    }
    public function getAccount($id)
    {
        $account = Account::find($id);

        if (!$account) {
            return response()->json([
                'message' => 'Data stock account tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'id' => $account->id ?? 1,
            'code' => $account->code ?? '',
            'name' => $account->name ?? '',
            'category' => $account->category ?? '',
            'currency' => $account->currency ?? '',
            'saldo' => $account->saldo ?? '',
            'parent' => $account->id_parents ?? '',
        ]);
    }
    public function storeAccount(Request $request)
    {
        $account = new Account();
        $account->id_parents = $request->parent ?? 0;
        $account->code = $request->code;
        $account->name = $request->name;
        $account->category = $request->category;
        $account->currency = $request->currency ?? 'IDR';
        $account->saldo = $request->saldo;
        $account->level = @$request->parent ? 2 : 1;
        $accountSave = $account->save();
        if ($accountSave) {
            return redirect()->route('expense-account.index')->with('success', 'Akun COA berhasil dibuat');
        }
        return redirect()->back()->with('error', 'Gagal membuat akun COA');
    }
    public function updateAccount(Request $request, $id)
    {
        $account = Account::find($id);
        if (!$account) {
            return redirect()->route('expense-account.index')->with('error', 'Akun tidak ditemukan');
        }
        $account->id_parents = $request->parent ?? 0;
        $account->code = $request->code;
        $account->name = $request->name;
        $account->category = $request->category;
        $account->currency = $request->currency ?? 'IDR';
        $account->saldo = $request->saldo;
        $account->level = @$request->parent ? 2 : 1;
        $accountSave = $account->save();
        if ($accountSave) {
            return redirect()->route('expense-account.index')->with('success', 'Akun COA berhasil diperbarui');
        }
        return redirect()->back()->with('error', 'Gagal memperbarui akun COA');
    }
    public function deleteAccount($id)
    {
        $account = Account::find($id);
        $delAccount = $account->delete();
        if ($delAccount) {
            return 1;
        } else {
            return 0;
        }
    }

    public function indexExpense(Request $request)
    {
        $selectedYear = $request->get('year', date('Y'));
        $selectedMonth = $request->get('month', date('m'));
        $selectedBank = $request->get('bank_id', '');

        // --- 1. Tab Expense Bank Metrics ---
        $totalThisMonth = Expense::whereNotNull('id_bank')
            ->whereYear('date', date('Y'))
            ->whereMonth('date', date('m'))
            ->sum('amount');

        $countThisMonth = Expense::whereNotNull('id_bank')
            ->whereYear('date', date('Y'))
            ->whereMonth('date', date('m'))
            ->count();

        $totalThisYear = Expense::whereNotNull('id_bank')
            ->whereYear('date', $selectedYear)
            ->sum('amount');

        $countThisYear = Expense::whereNotNull('id_bank')
            ->whereYear('date', $selectedYear)
            ->count();

        $avgVoucher = $countThisMonth > 0 ? round($totalThisMonth / $countThisMonth) : 0;

        $banks = Bank::orderBy('bank')->get();

        $availableYears = Expense::whereNotNull('id_bank')
            ->selectRaw('YEAR(date) as yr')
            ->distinct()
            ->orderByDesc('yr')
            ->pluck('yr')
            ->filter()
            ->values()
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int)date('Y')];
        }
        if (!in_array((int)date('Y'), $availableYears)) {
            array_unshift($availableYears, (int)date('Y'));
        }

        // --- 2. Tab Kas Umum Metrics ---
        $totalKasUmum = Expense::whereNull('id_bank')->sum('amount');
        $countKasUmum = Expense::whereNull('id_bank')->count();
        $currentMonthKas = Expense::whereNull('id_bank')
            ->whereYear('date', date('Y'))
            ->whereMonth('date', date('m'))
            ->sum('amount');
        $currentMonthCount = Expense::whereNull('id_bank')
            ->whereYear('date', date('Y'))
            ->whereMonth('date', date('m'))
            ->count();
        $avgKasUmum = $countKasUmum > 0 ? round($totalKasUmum / $countKasUmum) : 0;
        $availableYearsKas = Expense::whereNull('id_bank')
            ->whereNotNull('date')
            ->selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');
        if ($availableYearsKas->isEmpty()) {
            $availableYearsKas = collect([date('Y')]);
        }

        // --- 3. Tab Inventory Adjustment Metrics ---
        $totalAdjValue = Expense::join('detail_expense as de', 'de.id_expense', '=', 'expense.id')
            ->join('detail_inventory_adj as da', 'da.id_detail_expense', '=', 'de.id')
            ->whereNull('expense.id_bank')
            ->sum('expense.amount');
        $totalAdjCount = Expense::join('detail_expense as de', 'de.id_expense', '=', 'expense.id')
            ->join('detail_inventory_adj as da', 'da.id_detail_expense', '=', 'de.id')
            ->whereNull('expense.id_bank')
            ->distinct('expense.id')
            ->count('expense.id');
        $totalItemsAdjusted = Expense::join('detail_expense as de', 'de.id_expense', '=', 'expense.id')
            ->join('detail_inventory_adj as da', 'da.id_detail_expense', '=', 'de.id')
            ->whereNull('expense.id_bank')
            ->sum('da.qty');
        $currentMonthValue = Expense::join('detail_expense as de', 'de.id_expense', '=', 'expense.id')
            ->join('detail_inventory_adj as da', 'da.id_detail_expense', '=', 'de.id')
            ->whereNull('expense.id_bank')
            ->whereYear('expense.date', date('Y'))
            ->whereMonth('expense.date', date('m'))
            ->sum('expense.amount');
        $availableYearsInventory = Expense::join('detail_expense as de', 'de.id_expense', '=', 'expense.id')
            ->join('detail_inventory_adj as da', 'da.id_detail_expense', '=', 'de.id')
            ->whereNull('expense.id_bank')
            ->whereNotNull('expense.date')
            ->selectRaw('YEAR(expense.date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');
        if ($availableYearsInventory->isEmpty()) {
            $availableYearsInventory = collect([date('Y')]);
        }

        // --- 4. Tab Ongkir Logistik Metrics ---
        $totalResi = Expanse::where('type', 'Resi')->where('charged', '1')->count();
        $pendingCount = Expanse::where('type', 'Resi')->where('charged', '1')->where('status', 'pending')->count();
        $pendingCost = Expanse::where('type', 'Resi')->where('charged', '1')->where('status', 'pending')->sum('cost');
        $postedCount = Expanse::where('type', 'Resi')->where('charged', '1')->where('status', 'posted')->count();
        $postedCost = Expanse::where('type', 'Resi')->where('charged', '1')->where('status', 'posted')->sum('cost');
        $couriers = Expanse::where('type', 'Resi')->where('charged', '1')->select('kurir')->whereNotNull('kurir')->distinct()->pluck('kurir');
        $account = Account::all();

        return view('pages.finance.expense.index', compact(
            'totalThisMonth',
            'countThisMonth',
            'totalThisYear',
            'countThisYear',
            'avgVoucher',
            'banks',
            'availableYears',
            'selectedYear',
            'selectedMonth',
            'selectedBank',
            'totalKasUmum',
            'countKasUmum',
            'currentMonthKas',
            'currentMonthCount',
            'avgKasUmum',
            'availableYearsKas',
            'totalAdjValue',
            'totalAdjCount',
            'totalItemsAdjusted',
            'currentMonthValue',
            'availableYearsInventory',
            'totalResi',
            'pendingCount',
            'pendingCost',
            'postedCount',
            'postedCost',
            'couriers',
            'account'
        ));
    }
    public function indexExpenseUmum(Request $request)
    {
        return redirect('/expense#tab-expense-umum');
    }

    public function indexInvenAdj(Request $request)
    {
        return redirect('/expense#tab-expense-inventory');
    }
    public function createExpenseInventory()
    {
        $bank = Bank::all();
        $expense = Expense::all();
        $account = Account::all();
        $product = SerialProduct::join('product', 'serial_product.id_product', '=', 'product.id')->get('serial_product.*');
        return view('pages.finance.expense.form-inventory', compact('bank', 'expense', 'account', 'product'));
    }
    public function storeExpenseInventory(Request $request)
    {
        // dd($request->all());
        $expense = new Expense;
        $expense->id_bank = null;
        $expense->no_invoice = $request->no_invoice;
        $expense->no_cheque = null;
        $expense->memo = $request->detail;
        $expense->date = $request->date;
        $expense->amount = $request->total;
        $expenseSave = $expense->save();
        if ($expenseSave) {
            $dExpense = new DetailExpense();
            $dExpense->id_Expense = $expense->id;
            $dExpense->id_account = $request->account;
            $dExpense->memo = null;
            $dExpense->amount = null;
            $dExpenseSave = $dExpense->save();
            $replacements = $request->replacement;
            $warehouses = $request->warehouse;
            $qtys = $request->qty;
            $prices = $request->price;
            $amounts = $request->amount;

            if (empty($replacements) && $request->has('group-a')) {
                $replacements = [];
                $warehouses = [];
                $qtys = [];
                $prices = [];
                $amounts = [];
                foreach ($request->input('group-a') as $row) {
                    if (!empty($row['replacement'])) {
                        $replacements[] = is_array($row['replacement']) ? ($row['replacement'][0] ?? null) : $row['replacement'];
                        $warehouses[] = is_array($row['warehouse'] ?? null) ? ($row['warehouse'][0] ?? 'BDG') : ($row['warehouse'] ?? 'BDG');
                        $qtys[] = is_array($row['qty'] ?? null) ? ($row['qty'][0] ?? 1) : ($row['qty'] ?? 1);
                        $prices[] = is_array($row['price'] ?? null) ? ($row['price'][0] ?? 0) : ($row['price'] ?? 0);
                        $amounts[] = is_array($row['amount'] ?? null) ? ($row['amount'][0] ?? 0) : ($row['amount'] ?? 0);
                    }
                }
            }

            $inventorysave = false;
            if (is_array($replacements)) {
                foreach ($replacements as $item => $value) {
                    $replacement = DetailProduct::where('id', $replacements[$item])->first();
                    if ($replacement) {
                        $warehouseVal = $warehouses[$item] ?? 'BDG';
                        $qtyVal = (float) ($qtys[$item] ?? 0);
                        if ($warehouseVal == "BDG") {
                            $replacement->stock -= $qtyVal;
                        } else {
                            $replacement->warehouse_stock -= $qtyVal;
                        }
                        $replacement->save();

                        $inventory = new DetailInventoryAdj();
                        $inventory->id_detail_expense = $dExpense->id;
                        $inventory->id_product = $replacements[$item];
                        $inventory->qty = $qtyVal;
                        $inventory->warehouse = $warehouseVal;
                        $inventory->price = (float) ($prices[$item] ?? 0);
                        $inventory->amount = (float) ($amounts[$item] ?? 0);
                        $inventorysave = $inventory->save();
                    }
                }
            }
        }
        if ($expenseSave && $dExpenseSave && $inventorysave) {
            return redirect('/expense#tab-expense-inventory')->with('success', 'Data berhasil disimpan');
        }
    }

    public function deleteExpenseInventory($id)
    {
        $expense = Expense::find($id);
        $detailExpense = DetailExpense::where('id_expense', $id)->first();
        $inventory = DetailInventoryAdj::where('id_detail_expense', $detailExpense->id)->get();

        foreach ($inventory as $adj) {
            $replacement = DetailProduct::find($adj->id_product);

            if ($adj->warehouse == "BDG") {
                $replacement->stock += $adj->qty;
            } else {
                $replacement->warehouse_stock += $adj->qty; // fix typo juga
            }

            $replacement->save();
            $adj->delete();
        }

        // delete SEKALI saja
        $detailExpense->delete();
        $expenseDel = $expense->delete();

        return $expenseDel ? 1 : 0;
    }

    public function indexOngkir()
    {
        return redirect('/expense#tab-expense-ongkir');
    }

    public function postOngkir(Request $request, $id)
    {
        $expanse = Expanse::find($id);
        if (!$expanse || $expanse->status !== 'pending') {
            return response()->json(['error' => 'Data ongkir tidak ditemukan atau sudah diposting'], 404);
        }

        $bank = Bank::find($request->id_bank);
        $bank->saldo -= $expanse->cost;
        $bank->save();

        $expense = new Expense;
        $expense->id_bank = $bank->id;
        $expense->no_expense = $this->generateNoExpense();
        $expense->no_invoice = $expanse->no_track;
        $expense->memo = $request->memo ?? ('Ongkir Pending PO #' . $expanse->id_pending . ' (' . $expanse->kurir . ')');
        $expense->date = Carbon::today();
        $expense->amount = $expanse->cost;
        $expenseSave = $expense->save();

        if ($expenseSave) {
            $dExpense = new DetailExpense();
            $dExpense->id_Expense = $expense->id;
            $dExpense->id_account = $request->id_account;
            $dExpense->memo = $expense->memo;
            $dExpense->amount = $expanse->cost;
            $dExpense->save();

            $expanse->status = 'posted';
            $expanse->id_expense = $expense->id;
            $expanse->save();
        }

        return redirect()->back()->with('success', 'Ongkir berhasil diposting ke Finance');
    }

    public function createExpense()
    {
        $bank = Bank::all();
        $expense = Expense::all();
        $account = Account::all();
        $noExpense = $this->generateNoExpense();
        return view('pages.finance.expense.form', compact('bank', 'expense', 'account', 'noExpense'));
    }

    private function generateNoExpense()
    {
        $romans = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        $now = \Carbon\Carbon::now();
        $roman = $romans[$now->month - 1];
        $year = $now->year;
        $count = Expense::where('no_expense', 'LIKE', "EXP%-{$roman}-{$year}")->count();
        return 'EXP' . str_pad($count + 1, 3, '0', STR_PAD_LEFT) . '-' . $roman . '-' . $year;
    }
    public function createExpenseUmum()
    {
        $expense = Expense::all();
        $account = Account::all();
        return view('pages.finance.expense.form-umum', compact('expense', 'account'));
    }
    public function storeExpense(Request $request)
    {
        // dd($request->all());
        if (@$request->bank) {
            # code...
            $bank = Bank::find($request->bank);
            $bank->saldo -= $request->total;
            $bank->save();
        }
        $expense = new Expense;
        $expense->id_bank = $request->filled('bank') ? $request->bank : null;
        $expense->no_expense = $request->no_expense;
        $expense->no_invoice = $request->no_invoice;
        $expense->no_cheque = $request->no_cheque;
        $expense->memo = $request->detail;
        $expense->date = $request->date;
        $expense->amount = $request->total;
        $expenseSave = $expense->save();
        if ($expenseSave) {
            $accounts = $request->account;
            $memos = $request->memo;
            $amounts = $request->amount;

            if (empty($accounts) && $request->has('group-a')) {
                $accounts = [];
                $memos = [];
                $amounts = [];
                foreach ($request->input('group-a') as $row) {
                    if (isset($row['account'])) {
                        $accounts[] = is_array($row['account']) ? ($row['account'][0] ?? null) : $row['account'];
                        $memos[] = is_array($row['memo'] ?? null) ? ($row['memo'][0] ?? null) : ($row['memo'] ?? null);
                        $amounts[] = is_array($row['amount'] ?? null) ? ($row['amount'][0] ?? null) : ($row['amount'] ?? 0);
                    }
                }
            }

            $dExpenseSave = false;
            if (is_array($accounts)) {
                foreach ($accounts as $item => $value) {
                    $dExpense = new DetailExpense();
                    $dExpense->id_Expense = $expense->id;
                    $dExpense->id_account = $accounts[$item] ?? null;
                    $dExpense->memo = $memos[$item] ?? null;
                    $dExpense->amount = $amounts[$item] ?? 0;
                    $dExpenseSave = $dExpense->save();
                }
            }
        }
        if ($expenseSave && $dExpenseSave) {
            if (@$request->bank) {
                return redirect('expense')->with('success', 'Data berhasil disimpan');
            } else {
                return redirect('/expense#tab-expense-umum')->with('success', 'Data berhasil disimpan');
            }

        }
    }
    public function showExpense($id)
    {
        $expense = Expense::find($id);
        if (!$expense) {
            return redirect()->route('expense.index')->with('error', 'Expense tidak ditemukan');
        }
        $detailExpense = DetailExpense::where('id_expense', $id)->get();
        $terbilang = $this->capitalizeWords(
            trim($this->terbilang($expense->amount ?? 0))
        );
        return view('pages.finance.expense.detail', compact('detailExpense', 'expense', 'terbilang'));
    }
    public function showExpensePrint($id)
    {
        $expense = Expense::find($id);
        if (!$expense) {
            return redirect()->route('expense.index')->with('error', 'Expense tidak ditemukan');
        }
        $detailExpense = DetailExpense::where('id_expense', $id)->get();
        $terbilang = $this->capitalizeWords(
            trim($this->terbilang($expense->amount ?? 0))
        );
        return view('pages.finance.expense.detail-print', compact('detailExpense', 'expense', 'terbilang'));
    }
    public function deleteExpense($id)
    {
        $expense = Expense::find($id);
        if (!$expense) {
            return 0;
        }

        $detailExpense = DetailExpense::where('id_expense', $id)->get();
        foreach ($detailExpense as $key) {
            $key->delete();
        }

        if ($expense->id_bank) {
            $bank = Bank::find($expense->id_bank);
            if ($bank) {
                $bank->saldo += $expense->amount;
                $bank->save();
            }
        }

        $expenseDel = $expense->delete();
        if ($expenseDel) {
            return 1;
        } else {
            return 0;
        }
    }

    public function indexStatement(Request $request)
    {
        $activeTab = $request->get('tab', 'income');
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');

        $years = [];
        for ($i = $currentYear - 5; $i <= $currentYear + 2; $i++) {
            $years[] = $i;
        }

        $start = Carbon::now()->subYear()->startOfYear();
        $end = Carbon::now()->endOfYear();

        $months = collect();
        $cursor = $start->copy();

        while ($cursor <= $end) {
            $months->push([
                'month' => $cursor->month,
                'year' => $cursor->year,
                'label' => $cursor->translatedFormat('F Y'),
            ]);

            $cursor->addMonth();
        }

        $selectedYear = (int) $request->get('year', $currentYear);
        $selectedMonth = (int) $request->get('month', $currentMonth);

        // --- 1. Data Income Statement (Laba Rugi) ---
        $incomeData = $this->hitungDataIncome($selectedYear, $selectedMonth);
        $incomeYear = $this->hitungDataIncome($selectedYear);

        $poYear = $incomeYear['poSum'];
        $modalYear = $incomeYear['modalSum'];
        $grossProfitYear = $incomeYear['subtotal'];
        $expenseYear = $incomeYear['expenseSum'];
        $incomeYearVal = $incomeYear['incomeSum'];
        $chargeYear = $incomeYear['chargeSum'];
        $netProfitYear = $incomeYear['total'];
        $grossMarginPct = $incomeYear['grossMarginPct'];
        $netMarginPct = $incomeYear['netMarginPct'];

        // --- 2. Data Balance Statement (Neraca) ---
        $balanceData = $this->hitungDataBalance($currentYear, $currentMonth);
        $balanceYear = $this->hitungDataBalance($currentYear);

        $bankSaldo = optional($balanceData['bank'])->saldo ?? 0;
        $piutang = $balanceData['piutang'] ?? 0;
        $persediaan = $balanceData['asset'] ?? 0;
        $asetLancar = $bankSaldo + $piutang + $persediaan;

        $totalFixed = $balanceData['totalFixed'] ?? 0;
        $penyusutan = $balanceData['grandTotalPenyusutan'] ?? 0;
        $asetTetapBersih = max(0, $totalFixed - $penyusutan);

        $totalAset = $asetLancar + $asetTetapBersih;

        $labaTahunLalu = $balanceData['labaTahunLalu'] ?? 0;
        $labaBulanIni = $balanceData['labaBulanIni'] ?? 0;
        $prive = $balanceData['prive'] ?? 0;
        $totalEkuitas = $labaTahunLalu + $labaBulanIni - $prive;

        // --- 3. Data Equity Statement (Perubahan Modal) ---
        $equityData = $this->hitungDataEquity($currentYear, $currentMonth);
        $equityYear = $this->hitungDataEquity($currentYear);

        $modalAwal = $equityData['labaTahunLalu'] ?? 0;
        $labaBersih = $equityData['labaBulanIni'] ?? 0;
        $modalAkhir = $modalAwal + $labaBersih - $prive;

        $modalAwalYear = $equityYear['labaTahunLalu'] ?? 0;
        $labaBersihYear = $equityYear['labaTahunIni'] ?? 0;
        $modalAkhirYear = $modalAwalYear + $labaBersihYear - $prive;

        // --- 4. Data Cashflow Statement (Arus Kas) ---
        $now = Carbon::now();
        $ringkasanBulan = $this->ringkasanCashflowDari($this->hitungDataCashflow($now->year, $now->month));
        $ringkasanTahun = $this->ringkasanCashflowDari($this->hitungDataCashflow($now->year));
        $ringkasanBulanLabel = $now->translatedFormat('F Y');
        $ringkasanTahunLabel = $now->format('Y');

        return view('pages.finance.statement.index', compact(
            'activeTab',
            'years',
            'months',
            'currentYear',
            'currentMonth',
            'selectedYear',
            'selectedMonth',
            'incomeData',
            'incomeYear',
            'poYear',
            'modalYear',
            'grossProfitYear',
            'expenseYear',
            'incomeYear',
            'chargeYear',
            'netProfitYear',
            'grossMarginPct',
            'netMarginPct',
            'balanceData',
            'balanceYear',
            'bankSaldo',
            'piutang',
            'persediaan',
            'asetLancar',
            'totalFixed',
            'penyusutan',
            'asetTetapBersih',
            'totalAset',
            'totalEkuitas',
            'labaTahunLalu',
            'labaBulanIni',
            'equityData',
            'equityYear',
            'modalAwal',
            'labaBersih',
            'prive',
            'modalAkhir',
            'modalAwalYear',
            'labaBersihYear',
            'modalAkhirYear',
            'ringkasanBulan',
            'ringkasanTahun',
            'ringkasanBulanLabel',
            'ringkasanTahunLabel'
        ));
    }

    public function indexIncome()
    {
        return redirect()->to(route('finance.statement.index') . '#tab-income');
    }
    public function storeIncome(Request $request)
    {
        $income = new LabaRugi;
        $income->desc = $request->desc;
        $income->type = $request->type;
        $income->amount = $request->price;
        $income->date = Carbon::today();
        $incomeSave = $income->save();
        if ($incomeSave) {
            return redirect()->back()->with('success', 'berhasil ditambahkan!');
        }
    }
    public function hitungDataIncome($year, $month = null)
    {
        if ($month && (int)$year < 100 && (int)$month > 1900) {
            [$year, $month] = [(int)$month, (int)$year];
        }

        if ($month) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = Carbon::create($year, $month, 1)->endOfMonth();
            $periodLabel = $start->translatedFormat('F Y');
        } else {
            $startDate = Carbon::create($year, 1, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::create($year, 12, 31)->endOfMonth()->toDateString();
            $start = Carbon::create($year, 1, 1)->startOfMonth();
            $end = Carbon::create($year, 12, 31)->endOfMonth();
            $periodLabel = 'Tahun ' . $year;
        }

        $quotation = Quotation::whereBetween('po_date', [$startDate, $endDate])
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->get();
        $poSum = $quotation->sum('nett');

        $modalSum = Quotation::join('detail_quotation', 'quotation.id', '=', 'detail_quotation.id_quotation')
            ->join('serial_product', 'detail_quotation.id_equivalent', '=', 'serial_product.id')
            ->whereBetween('quotation.po_date', [$startDate, $endDate])
            ->where('quotation.status', '100')
            ->where('quotation.level', '1')
            ->where('quotation.is_primary', '1')
            ->sum('serial_product.price');

        $allExpense = DetailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->whereBetween('e.date', [$startDate, $endDate])
            ->groupBy('detail_expense.id')
            ->get();
        $expenseSum = $allExpense->sum('amount');

        $allIncome = LabaRugi::whereBetween('date', [$startDate, $endDate])
            ->where('type', 'Pendapatan Lain')
            ->get();
        $incomeSum = $allIncome->sum('amount');

        $allCharge = LabaRugi::whereBetween('date', [$startDate, $endDate])
            ->where('type', 'Beban Lain')
            ->get();
        $chargeSum = $allCharge->sum('amount');

        $startString = $start->translatedFormat('j M Y');
        $startStringYear = $start->translatedFormat('j M');
        $endString = $end->translatedFormat('j M Y');

        $subtotal = $poSum - $modalSum;
        $operatingProfit = $subtotal - $expenseSum;
        $incomeCharge = $incomeSum - $chargeSum;
        $total = $operatingProfit + $incomeCharge;
        $grossMarginPct = $poSum > 0 ? round(($subtotal / $poSum) * 100, 1) : 0;
        $netMarginPct = $poSum > 0 ? round(($total / $poSum) * 100, 1) : 0;

        return compact(
            'year',
            'month',
            'startDate',
            'endDate',
            'startString',
            'startStringYear',
            'endString',
            'periodLabel',
            'poSum',
            'modalSum',
            'allExpense',
            'allCharge',
            'allIncome',
            'expenseSum',
            'incomeSum',
            'chargeSum',
            'subtotal',
            'operatingProfit',
            'incomeCharge',
            'total',
            'grossMarginPct',
            'netMarginPct'
        );
    }

    public function detailBulanIncome($year, $month)
    {
        return view('pages.finance.income.detail', $this->hitungDataIncome($year, $month));
    }

    public function detailTahunIncome($year)
    {
        return view('pages.finance.income.detail', $this->hitungDataIncome($year));
    }

    public function printBulan($month, $year)
    {
        return view('pages.finance.income.print', $this->hitungDataIncome($year, $month));
    }

    public function printTahun($year)
    {
        return view('pages.finance.income.print', $this->hitungDataIncome($year));
    }

    public function apiIncomeKpi(Request $request)
    {
        $year = (int) $request->get('year', date('Y'));
        $month = $request->filled('month') ? (int) $request->get('month') : null;

        $data = $this->hitungDataIncome($year, $month);

        return response()->json([
            'success' => true,
            'periodLabel' => $data['periodLabel'],
            'poSum' => $data['poSum'],
            'poSumFormatted' => 'Rp ' . number_format($data['poSum'], 0, ',', '.'),
            'modalSum' => $data['modalSum'],
            'modalSumFormatted' => 'Rp ' . number_format($data['modalSum'], 0, ',', '.'),
            'grossProfit' => $data['subtotal'],
            'grossProfitFormatted' => 'Rp ' . number_format($data['subtotal'], 0, ',', '.'),
            'grossMarginPct' => $data['grossMarginPct'] . '%',
            'netProfit' => $data['total'],
            'netProfitFormatted' => ($data['total'] < 0 ? '- ' : '') . 'Rp ' . number_format(abs($data['total']), 0, ',', '.'),
            'netMarginPct' => $data['netMarginPct'] . '% Net',
            'isNetProfitPositive' => $data['total'] >= 0,
        ]);
    }

    public function indexBalance()
    {
        return redirect()->to(route('finance.statement.index') . '?tab=balance#tab-balance');
    }
    /**
     * Kumpulan data Balance Statement (dipakai bareng oleh halaman print & detail,
     * baik mode bulanan maupun tahunan) supaya kalkulasinya cuma ada di satu tempat.
     */
    private function hitungDataBalance($year, $month = null)
    {
        if ($month && (int)$year < 100 && (int)$month > 1900) {
            [$year, $month] = [(int)$month, (int)$year];
        }

        $bank = Bank::where('bank', 'BCA')->where(function($q) {
            $q->whereNull('branch')->orWhere('branch', 'NOT LIKE', '%palembang%');
        })->first();
        if (!$bank) {
            $bank = Bank::where('bank', 'BCA')->first();
        }

        $allBanks = Bank::where('is_active', 1)->get();
        $palembangBanks = Bank::where('is_active', 1)
            ->where(function($q) {
                $q->where('branch', 'LIKE', '%Palembang%')
                  ->orWhere('description', 'LIKE', '%Palembang%');
            })->get();

        $capPalembang = (float) ($palembangBanks->firstWhere('no_rek', '841-002-4250')->saldo ?? 425000000);
        $modPalembang = (float) ($palembangBanks->firstWhere('no_rek', '841-003-5750')->saldo ?? 575000000);
        $totalKasBank = ($bank ? (float)$bank->saldo : 0) + $capPalembang + $modPalembang;

        if ($month) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
            $start = Carbon::create($year, $month, 1)->startOfMonth();
        } else {
            $startDate = Carbon::create($year, 1, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::create($year, 12, 1)->endOfMonth()->toDateString();
            $start = Carbon::create($year, 1, 1)->startOfMonth();
        }
        $end = Carbon::today();

        $piutang = Payment::join('quotation as q', 'q.id', '=', 'payment.id_quotation')
            ->join('users as u', 'u.id', '=', 'q.id_sales')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')->join('client as c', 'p.id_client', '=', 'c.id')
            ->whereBetween('po_date', [$startDate, $endDate])
            ->where('payment.type', 'Tempo')
            ->where('payment.level', 0)
            ->whereNotNULL('payment.due_date')
            ->groupBy('payment.id')
            ->sum('payment.amount');
        $replace = DetailProduct::all();
        $asset = $replace->sum(function ($replacement) {
            return $replacement->modal * $replacement->stock;
        });
        $pIn = ProductIn::where('tax', '11')->whereBetween('date', [$startDate, $endDate])->sum('total');
        $ppnMas = $pIn * 11 / 100;
        $totalFixed = FixedAsset::sum('total');
        $fixedAsset = FixedAsset::select('type', DB::raw('SUM(total) as total_amount'))
            ->groupBy('type')
            ->get();
        $penyusutan = FixedAsset::all()->groupBy('type')->map(function ($assets, $type) {
            $total = 0;
            foreach ($assets as $asset) {
                $bulan = min(
                    Carbon::parse($asset->beli)->diffInMonths(now()),
                    $asset->umur
                );

                $total += (($asset->total * 0.25) / 12) * $bulan;
            }
            return [
                'type' => $type,
                'total_penyusutan' => $total
            ];
        });
        $grandTotalPenyusutan = $penyusutan->sum('total_penyusutan');
        $quotation = Quotation::whereBetween('po_date', [$startDate, $endDate])->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $ppnKel = $quotation * 11 / 100;
        $prive = DetailExpense::where('id_account', 51)->sum('amount');

        $labaTahunLalu = $this->hitungLabaTahunan($year - 1);

        $startStringYear = $start->translatedFormat('j M');
        $startString = $start->translatedFormat('j M Y');
        $endString = $end->translatedFormat('j M Y');

        $data = compact(
            'bank',
            'allBanks',
            'palembangBanks',
            'capPalembang',
            'modPalembang',
            'totalKasBank',
            'startDate',
            'endDate',
            'startString',
            'startStringYear',
            'endString',
            'piutang',
            'asset',
            'ppnMas',
            'ppnKel',
            'totalFixed',
            'fixedAsset',
            'penyusutan',
            'quotation',
            'prive',
            'labaTahunLalu',
            'grandTotalPenyusutan',
            'month'
        );

        if ($month) {
            $data['labaBulanIni'] = $this->hitungLabaBulanan($year, $month);
            $data['labaTahunTahun'] = $this->hitungLabaTahunSebelumnya($year, $month);
        } else {
            $data['labaTahunIni'] = $this->hitungLabaTahunan($year);
            $data['labaTahunTahun'] = $this->hitungLabaTahunSebelumnya($year, month: 12);
        }

        return $data;
    }

    public function printBulanBalance($year, $month)
    {
        return view('pages.finance.balance.print', $this->hitungDataBalance($year, $month));
    }

    public function printTahunBalance($year)
    {
        return view('pages.finance.balance.print', $this->hitungDataBalance($year));
    }

    public function detailBulanBalance($year, $month)
    {
        return view('pages.finance.balance.detail', $this->hitungDataBalance($year, $month));
    }

    public function detailTahunBalance($year)
    {
        return view('pages.finance.balance.detail', $this->hitungDataBalance($year));
    }

    public function indexEquity()
    {
        return redirect()->to(route('finance.statement.index') . '?tab=equity#tab-equity');
    }
    /**
     * Kumpulan data Equity Statement (dipakai bareng oleh halaman print & detail,
     * baik mode bulanan maupun tahunan) supaya kalkulasinya cuma ada di satu tempat.
     */
    private function hitungDataEquity($year, $month = null)
    {
        if ($month && (int)$year < 100 && (int)$month > 1900) {
            [$year, $month] = [(int)$month, (int)$year];
        }

        if ($month) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
            $start = Carbon::create($year, $month, 1)->startOfMonth();
        } else {
            $startDate = Carbon::create($year, 1, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::create($year, 12, 1)->endOfMonth()->toDateString();
            $start = Carbon::create($year, 1, 1)->startOfMonth();
        }
        $end = Carbon::today();

        $prive = DetailExpense::where('id_account', 51)->sum('amount');
        $labaTahunLalu = $this->hitungLabaTahunan($year - 1);

        $startStringYear = $start->translatedFormat('j M');
        $startString = $start->translatedFormat('j M Y');
        $endString = $end->translatedFormat('j M Y');

        $data = compact(
            'startDate',
            'endDate',
            'startString',
            'startStringYear',
            'endString',
            'prive',
            'labaTahunLalu',
            'month',
            'year'
        );

        if ($month) {
            $data['labaBulanIni'] = $this->hitungLabaBulanan($year, $month);
            $data['labaTahunTahun'] = $this->hitungLabaTahunSebelumnya($year, $month);
        } else {
            $data['labaTahunIni'] = $this->hitungLabaTahunan($year);
            $data['labaTahunTahun'] = $this->hitungLabaTahunSebelumnya($year, month: 12);
        }

        return $data;
    }

    public function printBulanEquity($year, $month)
    {
        return view('pages.finance.equity.print', $this->hitungDataEquity($year, $month));
    }

    public function printTahunEquity($year)
    {
        return view('pages.finance.equity.print', $this->hitungDataEquity($year));
    }

    public function detailBulanEquity($year, $month)
    {
        return view('pages.finance.equity.detail', $this->hitungDataEquity($year, $month));
    }

    public function detailTahunEquity($year)
    {
        return view('pages.finance.equity.detail', $this->hitungDataEquity($year));
    }
    public function indexCashflow()
    {
        return redirect()->to(route('finance.statement.index') . '?tab=cashflow#tab-cashflow');
    }

    /**
     * Ringkas hasil hitungDataCashflow() jadi 4 angka summary (Kas Masuk/Keluar Operasi,
     * Net Investasi, Net Pendanaan) buat ditampilkan sebagai stat card di halaman index.
     */
    private function ringkasanCashflowDari(array $data)
    {
        return [
            'kasMasuk' => $data['quotation'] + $data['income'],
            'kasKeluar' => $data['expenseSum'] + $data['outcome'],
            'netInvestasi' => $data['disposalProceeds'] - $data['assetPurchase'],
            'netPendanaan' => -$data['prive'],
        ];
    }

    /**
     * Kumpulan data Cashflow Statement (dipakai bareng oleh halaman print & detail,
     * baik mode bulanan maupun tahunan) supaya kalkulasinya cuma ada di satu tempat.
     */
    private function hitungDataCashflow($year, $month = null)
    {
        if ($month && (int)$year < 100 && (int)$month > 1900) {
            [$year, $month] = [(int)$month, (int)$year];
        }

        if ($month) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
            $start = Carbon::create($year, $month, 1)->startOfMonth();
        } else {
            $startDate = Carbon::create($year, 1, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::create($year, 12, 1)->endOfMonth()->toDateString();
            $start = Carbon::create($year, 1, 1)->startOfMonth();
        }
        $end = Carbon::today();

        $quotation = Quotation::whereBetween('po_date', [$startDate, $endDate])->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $pendapatan = LabaRugi::whereBetween('date', [$start, $end])
            ->where('type', 'Pendapatan Lain')
            ->get();
        $income = $pendapatan->sum('amount');
        $biaya = LabaRugi::whereBetween('date', [$start, $end])
            ->where('type', 'Biaya Lain')
            ->get();
        $outcome = $biaya->sum('amount');
        $expensePerAccount = DB::table('detail_expense')
            ->join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->join('account', 'account.id', '=', 'detail_expense.id_account')
            ->whereBetween('e.date', [$startDate, $endDate])
            ->select(
                'account.name',
                DB::raw('SUM(detail_expense.amount) as total_amount')
            )
            ->groupBy('detail_expense.id_account', 'account.name')
            ->get();
        $expenseSum = $expensePerAccount->sum('total_amount');
        $piutang = Payment::join('quotation as q', 'q.id', '=', 'payment.id_quotation')
            ->join('users as u', 'u.id', '=', 'q.id_sales')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')->join('client as c', 'p.id_client', '=', 'c.id')
            ->whereBetween('po_date', [$startDate, $endDate])
            ->where('payment.type', 'Tempo')
            ->where('payment.level', 0)
            ->whereNotNULL('payment.due_date')
            ->groupBy('payment.id')
            ->sum('payment.amount');
        $replace = DetailProduct::all();
        $asset = $replace->sum(function ($replacement) {
            return $replacement->modal * $replacement->stock;
        });
        $pIn = ProductIn::where('tax', '11')->whereBetween('date', [$startDate, $endDate])->sum('total');
        $ppnMas = $pIn * 11 / 100;
        $totalFixed = FixedAsset::sum('total');
        $fixedAsset = FixedAsset::select('type', DB::raw('SUM(total) as total_amount'))
            ->groupBy('type')
            ->get();
        $penyusutan = FixedAsset::all()->groupBy('type')->map(function ($assets, $type) {
            $total = 0;
            foreach ($assets as $asset) {
                $bulan = min(
                    Carbon::parse($asset->beli)->diffInMonths(now()),
                    $asset->umur
                );

                $total += (($asset->total * 0.25) / 12) * $bulan;
            }
            return [
                'type' => $type,
                'total_penyusutan' => $total
            ];
        });
        $grandTotalPenyusutan = $penyusutan->sum('total_penyusutan');
        $ppnKel = $quotation * 11 / 100;
        $prive = DetailExpense::where('id_account', 51)->sum('amount');

        $startStringYear = $start->translatedFormat('j M');
        $startString = $start->translatedFormat('j M Y');
        $endString = $end->translatedFormat('j M Y');

        $investasi = $this->hitungCashflowInvestasi($startDate, $endDate, $start, $end);

        return compact(
            'startDate',
            'endDate',
            'startString',
            'startStringYear',
            'endString',
            'piutang',
            'asset',
            'income',
            'ppnMas',
            'ppnKel',
            'totalFixed',
            'fixedAsset',
            'penyusutan',
            'quotation',
            'pendapatan',
            'expensePerAccount',
            'expenseSum',
            'biaya',
            'outcome',
            'prive',
            'grandTotalPenyusutan',
            'month',
            'year'
        ) + $investasi;
    }

    public function printBulanCashflow($year, $month)
    {
        return view('pages.finance.cashflow.print', $this->hitungDataCashflow($year, $month));
    }

    public function printTahunCashflow($year)
    {
        return view('pages.finance.cashflow.print', $this->hitungDataCashflow($year));
    }

    public function detailBulanCashflow($year, $month)
    {
        return view('pages.finance.cashflow.detail', $this->hitungDataCashflow($year, $month));
    }

    public function detailTahunCashflow($year)
    {
        return view('pages.finance.cashflow.detail', $this->hitungDataCashflow($year));
    }

    /**
     * Data Aktivitas Investasi untuk Cashflow Statement: pembelian aset tetap, hasil
     * penjualan/disposal, laba-rugi disposal, dan penyusutan yang dibebankan HANYA
     * di periode laporan (bukan akumulasi sepanjang umur aset).
     */
    private function hitungCashflowInvestasi($startDate, $endDate, $start, $end)
    {
        $assetPurchase = FixedAsset::whereBetween('beli', [$startDate, $endDate])->sum('total');

        $disposedAssets = FixedAsset::where('is_disposed', true)
            ->whereBetween('tanggal_disposal', [$startDate, $endDate])
            ->get();
        $disposalProceeds = $disposedAssets->sum('harga_jual_final');
        $labaRugiDisposal = $disposedAssets->sum(function ($a) {
            return $a->harga_jual_final - $a->nilai_buku_disposal;
        });

        $penyusutanPeriode = FixedAsset::all()->sum(function ($asset) use ($start, $end) {
            $assetStart = Carbon::parse($asset->beli);
            $assetEnd = $assetStart->copy()->addMonths($asset->umur);
            $overlapStart = $assetStart->greaterThan($start) ? $assetStart : $start;
            $overlapEnd = $assetEnd->lessThan($end) ? $assetEnd : $end;
            if ($overlapEnd->lessThanOrEqualTo($overlapStart)) {
                return 0;
            }
            return (($asset->total * 0.25) / 12) * $overlapStart->diffInMonths($overlapEnd);
        });

        return compact('assetPurchase', 'disposalProceeds', 'labaRugiDisposal', 'penyusutanPeriode');
    }

    private function hitungLabaTahunan($year)
    {
        $start = Carbon::create($year, 1, 1)->startOfYear();
        $end = Carbon::create($year, 12, 31)->endOfYear();

        $po = Quotation::whereBetween('po_date', [$start, $end])
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->sum('nett');

        $modal = Quotation::join('detail_quotation', 'quotation.id', '=', 'detail_quotation.id_quotation')
            ->join('serial_product', 'detail_quotation.id_equivalent', '=', 'serial_product.id')
            ->whereBetween('quotation.po_date', [$start, $end])
            ->where('quotation.status', '100')
            ->where('quotation.level', '1')
            ->where('quotation.is_primary', '1')
            ->sum('serial_product.price');

        $expense = detailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->whereBetween('e.date', [$start, $end])
            ->sum('detail_expense.amount');

        $income = LabaRugi::whereBetween('date', [$start, $end])
            ->where('type', 'Pendapatan Lain')
            ->sum('amount');

        $charge = LabaRugi::whereBetween('date', [$start, $end])
            ->where('type', 'Beban Lain')
            ->sum('amount');

        return $po - $modal - $expense + $income - $charge;
    }
    private function hitungLabaBulanan($year, $month)
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = Carbon::create($year, $month, 31)->endOfMonth();

        $po = Quotation::whereBetween('po_date', [$start, $end])
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->sum('nett');

        $modal = Quotation::join('detail_quotation', 'quotation.id', '=', 'detail_quotation.id_quotation')
            ->join('serial_product', 'detail_quotation.id_equivalent', '=', 'serial_product.id')
            ->whereBetween('quotation.po_date', [$start, $end])
            ->where('quotation.status', '100')
            ->where('quotation.level', '1')
            ->where('quotation.is_primary', '1')
            ->sum('serial_product.price');

        $expense = detailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->whereBetween('e.date', [$start, $end])
            ->sum('detail_expense.amount');

        $income = LabaRugi::whereBetween('date', [$start, $end])
            ->where('type', 'Pendapatan Lain')
            ->sum('amount');

        $charge = LabaRugi::whereBetween('date', [$start, $end])
            ->where('type', 'Beban Lain')
            ->sum('amount');

        return $po - $modal - $expense + $income - $charge;
    }
    private function hitungLabaTahunSebelumnya($year, $month)
    {
        $start = Carbon::create(2020, 1, 1)->startOfYear();
        $end = Carbon::create($year, $month, 31)->endOfYear();

        $po = Quotation::whereBetween('po_date', [$start, $end])
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->sum('nett');

        $modal = Quotation::join('detail_quotation', 'quotation.id', '=', 'detail_quotation.id_quotation')
            ->join('serial_product', 'detail_quotation.id_equivalent', '=', 'serial_product.id')
            ->whereBetween('quotation.po_date', [$start, $end])
            ->where('quotation.status', '100')
            ->where('quotation.level', '1')
            ->where('quotation.is_primary', '1')
            ->sum('serial_product.price');

        $expense = detailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->whereBetween('e.date', [$start, $end])
            ->sum('detail_expense.amount');

        $income = LabaRugi::whereBetween('date', [$start, $end])
            ->where('type', 'Pendapatan Lain')
            ->sum('amount');

        $charge = LabaRugi::whereBetween('date', [$start, $end])
            ->where('type', 'Beban Lain')
            ->sum('amount');

        return $po - $modal - $expense + $income - $charge;
    }
    private function hitungLabaBulanSebelumnya($year, $month)
    {
        $start = Carbon::create($year, 1, 1)->startOfMonth();
        $end = Carbon::create($year, $month, 31)->endOfMonth();

        $po = Quotation::whereBetween('po_date', [$start, $end])
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->sum('nett');

        $modal = Quotation::join('detail_quotation', 'quotation.id', '=', 'detail_quotation.id_quotation')
            ->join('serial_product', 'detail_quotation.id_equivalent', '=', 'serial_product.id')
            ->whereBetween('quotation.po_date', [$start, $end])
            ->where('quotation.status', '100')
            ->where('quotation.level', '1')
            ->where('quotation.is_primary', '1')
            ->sum('serial_product.price');

        $expense = detailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->whereBetween('e.date', [$start, $end])
            ->sum('detail_expense.amount');

        $income = LabaRugi::whereBetween('date', [$start, $end])
            ->where('type', 'Pendapatan Lain')
            ->sum('amount');

        $charge = LabaRugi::whereBetween('date', [$start, $end])
            ->where('type', 'Beban Lain')
            ->sum('amount');

        return $po - $modal - $expense + $income - $charge;
    }

    private function terbilang($number)
    {
        $number = abs($number);
        $words = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];

        if ($number < 12)
            return " " . $words[$number];
        if ($number < 20)
            return $this->terbilang($number - 10) . " belas";
        if ($number < 100)
            return $this->terbilang(floor($number / 10)) . " puluh" . $this->terbilang($number % 10);
        if ($number < 200)
            return " seratus" . $this->terbilang($number - 100);
        if ($number < 1000)
            return $this->terbilang(floor($number / 100)) . " ratus" . $this->terbilang($number % 100);
        if ($number < 2000)
            return " seribu" . $this->terbilang($number - 1000);
        if ($number < 1000000)
            return $this->terbilang(floor($number / 1000)) . " ribu" . $this->terbilang($number % 1000);
        if ($number < 1000000000)
            return $this->terbilang(floor($number / 1000000)) . " juta" . $this->terbilang($number % 1000000);
        if ($number < 1000000000000)
            return $this->terbilang(floor($number / 1000000000)) . " miliar" . $this->terbilang($number % 1000000000);

        return "";
    }

    private function capitalizeWords($str)
    {
        return ucwords($str);
    }

}
