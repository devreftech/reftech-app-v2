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
        return view('pages.finance.account.index', compact('account', 'prim'));
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
        $account->currency = $request->currency;
        $account->saldo = $request->saldo;
        $account->level = @$request->parent ? 2 : 1;
        $accountSave = $account->save();
        if ($accountSave) {
            return redirect('/expense-acount')->with('success', 'data telah dibuat');
        }
    }
    public function updateAccount(Request $request, $id)
    {
        $account = Account::find($id);
        $account->id_parents = $request->parent ?? 0;
        $account->code = $request->code;
        $account->name = $request->name;
        $account->category = $request->category;
        $account->currency = $request->currency;
        $account->saldo = $request->saldo;
        $account->level = @$request->parent ? 2 : 1;
        $accountSave = $account->save();
        if ($accountSave) {
            return redirect('/expense-acount')->with('success', 'data telah dibuat');
        }
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

    public function indexExpense()
    {
        return view('pages.finance.expense.index');
    }
    public function indexExpenseUmum()
    {
        return view('pages.finance.expense.index-umum');
    }
    public function indexInvenAdj()
    {
        return view('pages.finance.expense.index-inventory');
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
            foreach ($request->replacement as $item => $value) {
                $replacement = DetailProduct::where('id', $request->replacement[$item])->first();
                if ($request->warehouse[$item] == "BDG") {
                    $replacement->stock -= $request->qty[$item];
                } else {
                    $replacement->warehouse_stock -= $request->qty[$item];
                }
                $replacement->save();
                $inventory = new DetailInventoryAdj();
                $inventory->id_detail_expense = $dExpense->id;
                $inventory->id_product = $request->replacement[$item];
                $inventory->qty = $request->qty[$item];
                $inventory->warehouse = $request->warehouse[$item];
                $inventory->price = $request->price[$item];
                $inventory->amount = $request->amount[$item];
                $inventorysave = $inventory->save();
            }
        }
        if ($expenseSave && $dExpenseSave && $inventorysave) {
            return redirect('expense-inventory')->with('success', 'Data berhasil disimpan');
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
        $bank = Bank::all();
        $account = Account::all();
        return view('pages.finance.expense.index-ongkir', compact('bank', 'account'));
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
        $expense->id_bank = $request->bank ?? null;
        $expense->no_expense = $request->no_expense;
        $expense->no_invoice = $request->no_invoice;
        $expense->no_cheque = $request->no_cheque;
        $expense->memo = $request->detail;
        $expense->date = $request->date;
        $expense->amount = $request->total;
        $expenseSave = $expense->save();
        if ($expenseSave) {
            foreach ($request->account as $item => $value) {
                $dExpense = new DetailExpense();
                $dExpense->id_Expense = $expense->id;
                $dExpense->id_account = $request->account[$item];
                $dExpense->memo = $request->memo[$item];
                $dExpense->amount = $request->amount[$item];
                $dExpenseSave = $dExpense->save();
            }
        }
        if ($expenseSave && $dExpenseSave) {
            if (@$request->bank) {
                return redirect('expense')->with('success', 'Data berhasil disimpan');
            } else {
                return redirect('expense-umum')->with('success', 'Data berhasil disimpan');
            }

        }
    }
    public function showExpense($id)
    {
        $expense = Expense::find($id);
        $detailExpense = DetailExpense::where('id_expense', $id)->get();
        $terbilang = $this->capitalizeWords(
            trim($this->terbilang($expense->amount))
        );
        return view('pages.finance.expense.detail', compact('detailExpense', 'expense', 'terbilang'));
    }
    public function showExpensePrint($id)
    {
        $expense = Expense::find($id);
        $detailExpense = DetailExpense::where('id_expense', $id)->get();
        $terbilang = $this->capitalizeWords(
            trim($this->terbilang($expense->amount))
        );
        return view('pages.finance.expense.detail-print', compact('detailExpense', 'expense', 'terbilang'));
    }
    public function deleteExpense($id)
    {
        $expense = Expense::find($id);
        $bank = Bank::find($expense->id_bank);
        $detailExpense = DetailExpense::where('id_expense', $id)->get();
        foreach ($detailExpense as $key) {
            $key->delete();
        }
        $bank->saldo += $expense->amount;
        $bank->save();
        $expenseDel = $expense->delete();
        if ($expenseDel) {
            return 1;
        } else {
            return 0;
        }
    }

    public function indexIncome()
    {

        $currentYear = date('Y');

        $years = [];
        for ($i = $currentYear - 5; $i <= $currentYear + 5; $i++) {
            $years[] = $i;
        }
        $start = Carbon::now()->subYear()->startOfYear();
        $end = Carbon::now()->endOfYear();

        $months = collect();
        $cursor = $start->copy();

        while ($cursor <= $end) {
            $months->push([
                'month' => $cursor->month,      // 1–12
                'year' => $cursor->year,
                'label' => $cursor->translatedFormat('F Y'), // Januari 2024
            ]);

            $cursor->addMonth();
        }
        // dd($months);
        return view('pages.finance.income.index', compact('years', 'months'));
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
    public function printBulan($month, $year)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = Carbon::today();
        $quotation = Quotation::whereBetween('po_date', [$startDate, $endDate])->where('status', '100')->where('level', '1')->where('is_primary', '1')->get();
        $poSum = $quotation->sum('nett');
        $modalSum = Quotation::join('detail_quotation', 'quotation.id', '=', 'detail_quotation.id_quotation')
            ->join('serial_product', 'detail_quotation.id_equivalent', '=', 'serial_product.id')
            ->whereBetween('quotation.po_date', [$startDate, $endDate])
            ->where('quotation.status', '100')
            ->where('quotation.level', '1')
            ->where('quotation.is_primary', '1')
            ->sum('serial_product.price');
        $allExpense = detailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')->whereBetween('e.date', [$startDate, $endDate])->groupBy('detail_expense.id')->get();
        $expenseSum = $allExpense->sum('amount');
        $allIncome = LabaRugi::whereBetween('date', [$startDate, $endDate])->where('type', 'Pendapatan Lain')->get();
        $incomeSum = $allIncome->sum('amount');
        $allCharge = LabaRugi::whereBetween('date', [$startDate, $endDate])->where('type', 'Beban Lain')->get();
        $chargeSum = $allCharge->sum('amount');
        $startStringYear = $start->translatedFormat('j M');
        $startString = $start->translatedFormat('j M Y');
        $endString = $end->translatedFormat('j M Y');
        return view('pages.finance.income.print', compact(
            'startDate',
            'endDate',
            'startString',
            'startStringYear',
            'endString',
            'poSum',
            'modalSum',
            'allExpense',
            'allCharge',
            'allIncome',
            'expenseSum',
            'incomeSum',
            'chargeSum'
        ));
    }
    public function printTahun($year)
    {
        $startDate = Carbon::create($year, 1, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, 12, 1)->endOfMonth()->toDateString();
        $start = Carbon::create($year, 1, 1)->startOfMonth();
        $end = Carbon::today();
        $quotation = Quotation::whereBetween('po_date', [$startDate, $endDate])->where('status', '100')->where('level', '1')->where('is_primary', '1')->get();
        $poSum = $quotation->sum('nett');
        $modalSum = Quotation::join('detail_quotation', 'quotation.id', '=', 'detail_quotation.id_quotation')
            ->join('serial_product', 'detail_quotation.id_equivalent', '=', 'serial_product.id')
            ->whereBetween('quotation.po_date', [$startDate, $endDate])
            ->where('quotation.status', '100')
            ->where('quotation.level', '1')
            ->where('quotation.is_primary', '1')
            ->sum('serial_product.price');
        $allExpense = detailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')->whereBetween('e.date', [$startDate, $endDate])->groupBy('detail_expense.id')->get();
        $expenseSum = $allExpense->sum('amount');
        $allIncome = LabaRugi::whereBetween('date', [$startDate, $endDate])->where('type', 'Pendapatan Lain')->get();
        $incomeSum = $allIncome->sum('amount');
        $allCharge = LabaRugi::whereBetween('date', [$startDate, $endDate])->where('type', 'Beban Lain')->get();
        $chargeSum = $allCharge->sum('amount');
        $startStringYear = $start->translatedFormat('j M');
        $startString = $start->translatedFormat('j M Y');
        $endString = $end->translatedFormat('j M Y');
        return view('pages.finance.income.print', compact(
            'startDate',
            'endDate',
            'startString',
            'startStringYear',
            'endString',
            'poSum',
            'modalSum',
            'allExpense',
            'allCharge',
            'allIncome',
            'expenseSum',
            'incomeSum',
            'chargeSum'
        ));
    }

    public function indexBalance()
    {

        $currentYear = date('Y');

        $years = [];
        for ($i = $currentYear - 5; $i <= $currentYear + 5; $i++) {
            $years[] = $i;
        }
        $start = Carbon::now()->subYear()->startOfYear();
        $end = Carbon::now()->endOfYear();

        $months = collect();
        $cursor = $start->copy();

        while ($cursor <= $end) {
            $months->push([
                'month' => $cursor->month,      // 1–12
                'year' => $cursor->year,
                'label' => $cursor->translatedFormat('F Y'), // Januari 2024
            ]);

            $cursor->addMonth();
        }
        // dd($months);
        return view('pages.finance.balance.index', compact('years', 'months'));
    }
    /**
     * Kumpulan data Balance Statement (dipakai bareng oleh halaman print & detail,
     * baik mode bulanan maupun tahunan) supaya kalkulasinya cuma ada di satu tempat.
     */
    private function hitungDataBalance($year, $month = null)
    {
        $bank = Bank::where('bank', 'BCA')->first();

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

        $currentYear = date('Y');

        $years = [];
        for ($i = $currentYear - 5; $i <= $currentYear + 5; $i++) {
            $years[] = $i;
        }
        $start = Carbon::now()->subYear()->startOfYear();
        $end = Carbon::now()->endOfYear();

        $months = collect();
        $cursor = $start->copy();

        while ($cursor <= $end) {
            $months->push([
                'month' => $cursor->month,      // 1–12
                'year' => $cursor->year,
                'label' => $cursor->translatedFormat('F Y'), // Januari 2024
            ]);

            $cursor->addMonth();
        }
        // dd($months);
        return view('pages.finance.equity.index', compact('years', 'months'));
    }
    /**
     * Kumpulan data Equity Statement (dipakai bareng oleh halaman print & detail,
     * baik mode bulanan maupun tahunan) supaya kalkulasinya cuma ada di satu tempat.
     */
    private function hitungDataEquity($year, $month = null)
    {
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

        $currentYear = date('Y');

        $years = [];
        for ($i = $currentYear - 5; $i <= $currentYear + 5; $i++) {
            $years[] = $i;
        }
        $start = Carbon::now()->subYear()->startOfYear();
        $end = Carbon::now()->endOfYear();

        $months = collect();
        $cursor = $start->copy();

        while ($cursor <= $end) {
            $months->push([
                'month' => $cursor->month,      // 1–12
                'year' => $cursor->year,
                'label' => $cursor->translatedFormat('F Y'), // Januari 2024
            ]);

            $cursor->addMonth();
        }
        // Ringkasan bulan & tahun berjalan buat preview di halaman index (biar gak "blind pick" periode)
        $now = Carbon::now();
        $ringkasanBulan = $this->ringkasanCashflowDari($this->hitungDataCashflow($now->year, $now->month));
        $ringkasanTahun = $this->ringkasanCashflowDari($this->hitungDataCashflow($now->year));
        $ringkasanBulanLabel = $now->translatedFormat('F Y');
        $ringkasanTahunLabel = $now->format('Y');

        // dd($months);
        return view('pages.finance.cashflow.index', compact(
            'years',
            'months',
            'ringkasanBulan',
            'ringkasanTahun',
            'ringkasanBulanLabel',
            'ringkasanTahunLabel'
        ));
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
            'month'
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
