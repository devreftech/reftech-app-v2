<?php

namespace App\Http\Controllers;

use App\Models\ChangeStatus;
use App\Models\DetailQuotation;
use App\Models\Expanse;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\Reminder;
use App\Models\Resi;
use App\Services\PurchaseRequestService;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

use App\Models\SubtitleQuotation;

class PaymentController extends Controller
{
    protected PurchaseRequestService $prService;

    public function __construct(PurchaseRequestService $prService)
    {
        $this->prService = $prService;
    }

    public function index_invoice()
    {
        $salesUsers = \App\Models\User::where('role', 'Sales')->orderBy('name', 'asc')->get();
        return view('pages.accounting.payment.index-invoice', compact('salesUsers'));
    }
    public function index_invoice_ahmad()
    {
        $fullInvoice = Invoice::join('quotation', 'quotation.id', '=', 'invoice.id_quotation')
            ->where('status', '100')
            ->where('invoice.flag', 'Reftech')
            ->where('quotation.tax', '11')
            ->sum('harga_total');
        $fullPayment = Invoice::join('quotation', 'quotation.id', '=', 'invoice.id_quotation')
            ->where('status', '100')
            ->where('invoice.flag', 'Reftech')
            ->where('quotation.tax', '11')
            ->sum('payment.amount');
        $sisa = $fullInvoice - $fullPayment;
        // dd(DB::select('SELECT id_quotation, SUM(amount) as total_payment FROM payment GROUP BY id_quotation'));
        return view('pages.accounting.payment.ahmad.index-invoice', compact('fullInvoice', 'fullPayment', 'sisa'));
    }
    public function index_invoice_rayi()
    {
        $fullInvoice = Invoice::join('quotation', 'quotation.id', '=', 'invoice.id_quotation')
            ->where('status', '100')
            ->where('invoice.flag', 'Kojisha')
            ->where('quotation.tax', '11')
            ->sum('harga_total');
        $fullPayment = Invoice::join('quotation', 'quotation.id', '=', 'invoice.id_quotation')
            ->where('status', '100')
            ->where('invoice.flag', 'Kojisha')
            ->where('quotation.tax', '11')
            ->sum('payment.amount');
        $sisa = $fullInvoice - $fullPayment;
        // dd(DB::select('SELECT id_quotation, SUM(amount) as total_payment FROM payment GROUP BY id_quotation'));
        return view('pages.accounting.payment.rayi.index-invoice', compact('fullInvoice', 'fullPayment', 'sisa'));
    }
    public function detail_invoice($id)
    {
        $invoice = Invoice::find($id);

        if ($invoice->id_unit_quotation) {
            return redirect()->route('invoice.show_unit', $id);
        }

        $quote = Quotation::find($invoice->id_quotation);
        $dQuote = DetailQuotation::where('id_quotation', $quote->id)->get();
        $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $quote->id)->get();
        $payment = Payment::where('id_quotation', $quote->id)->get();
        return view('pages.accounting.payment.detail-invoice', compact('invoice', 'quote', 'dQuote', 'subQuote', 'payment'));
    }
    public function index_payment()
    {
        $salesUsers = \App\Models\User::where('role', 'Sales')->orderBy('name', 'asc')->get();
        $receipt = Payment::sum('amount');
        $confirm = Payment::where('level', 1)->sum('amount');
        $unconfirm = Payment::where('level', 0)->sum('amount');
        return view('pages.accounting.payment.index-payment', compact('receipt', 'confirm', 'unconfirm', 'salesUsers'));
    }
    public function detail_payment($id)
    {
        $payment  = Payment::with('bank')->findOrFail($id);
        $activity = ChangeStatus::with('user')->where('id_payment', $id)->orderBy('date', 'desc')->get();
        $banks    = \App\Models\Bank::where('is_active', 1)->orderBy('bank', 'asc')->get();

        $clientInfo = 'Reftech';
        $tax = 0;
        $isUnitQuotation = false;

        if ($payment->id_unit_quotation) {
            $isUnitQuotation = true;
            $quote   = \App\Models\UnitQuotation::with('client')->find($payment->id_unit_quotation);
            $invoice = Invoice::where('id_unit_quotation', $payment->id_unit_quotation)
                ->whereNotNull('no_invoice')->first();
            $clientInfo = $quote?->client?->info ?? 'Reftech';
            $tax = (float) ($quote?->tax ?? 0);
        } else {
            $quote = Quotation::with('pic.client')->find($payment->id_quotation);
            $clientInfo = $quote?->pic?->client?->info ?? 'Reftech';
            $tax = (float) ($quote?->tax ?? 0);

            if ($payment->type === 'BP') {
                $invoice = Invoice::where('id_quotation', $quote?->id)->where('type', 'BP')->first();
            } else {
                $invoice = Invoice::where('id_quotation', $quote?->id)->first();
            }
        }

        // Tentukan Rekening Bank Default yang Sinkron dengan Invoice
        $defaultBankId = $payment->id_bank;
        $suggestedReason = '';

        if (!$defaultBankId) {
            if (strtolower($clientInfo) === 'kojisha') {
                if ($tax > 0) {
                    $defaultBank = \App\Models\Bank::where('no_rek', '5223876543')->first();
                    $suggestedReason = 'KOJISHA - PPN (PT. KOJISHA INNOTIV INDONESIA)';
                } else {
                    $defaultBank = \App\Models\Bank::where('no_rek', '1560239137')->first();
                    $suggestedReason = 'KOJISHA - Non-PPN (REGITA DWI MELINDA)';
                }
            } else {
                if ($tax > 0) {
                    $defaultBank = \App\Models\Bank::where('no_rek', 'like', '%008%6289%789%')->orWhere('id', 2)->first();
                    $suggestedReason = 'REFTECH - PPN (PT. REFTECH JAYA OPTIMA)';
                } else {
                    $defaultBank = \App\Models\Bank::where('no_rek', 'like', '%166%2242%271%')->first();
                    $suggestedReason = 'REFTECH - Non-PPN (ARIEP RACHMAN)';
                }
            }
            $defaultBankId = $defaultBank?->id;
        }

        return view('pages.accounting.payment.detail-payment',
            compact('activity', 'invoice', 'quote', 'payment', 'banks', 'defaultBankId', 'suggestedReason', 'clientInfo', 'tax'))
            ->with('isUnitQuotation', $isUnitQuotation);
    }
    public function index_aging()
    {
        // Sparepart quotation aging
        $spBase = Payment::join('quotation as q', 'q.id', '=', 'payment.id_quotation')
            ->join('users as u', 'u.id', '=', 'q.id_sales')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')
            ->join('client as c', 'p.id_client', '=', 'c.id')
            ->where('payment.type', 'Tempo');

        // Unit quotation aging
        $uqBase = Payment::join('unit_quotation as uq', 'uq.id', '=', 'payment.id_unit_quotation')
            ->join('users as u', 'u.id', '=', 'uq.id_sales')
            ->join('client as c', 'c.id', '=', 'uq.id_client')
            ->where('payment.type', 'Tempo');

        $invoice = $spBase->clone()
            ->where('payment.level', 0)->whereNotNull('payment.due_date')
            ->groupBy('payment.id')
            ->select('payment.*', 'q.harga_total', 'q.tax', 'c.info', 'u.id as id_sales')->get()
            ->merge(
                $uqBase->clone()
                    ->where('payment.level', 0)->whereNotNull('payment.due_date')
                    ->groupBy('payment.id')
                    ->select('payment.*', 'uq.total as harga_total', 'uq.tax', 'c.info', 'u.id as id_sales')->get()
            );

        $confirm = Payment::where('type', 'Tempo')->where('level', 1)->get();

        $unconfirm = $spBase->clone()
            ->where('payment.level', 0)
            ->groupBy('payment.id')
            ->select('payment.*', 'q.harga_total', 'c.info', 'u.id as id_sales')->get()
            ->merge(
                $uqBase->clone()
                    ->where('payment.level', 0)
                    ->groupBy('payment.id')
                    ->select('payment.*', 'uq.total as harga_total', 'c.info', 'u.id as id_sales')->get()
            );

        $overdue = $spBase->clone()
            ->where('payment.level', 0)->whereNotNull('payment.due_date')
            ->whereDate('payment.due_date', '<=', Carbon::today())
            ->groupBy('payment.id')
            ->select('payment.*', 'q.harga_total', 'q.tax', 'c.info', 'u.id as id_sales')->get()
            ->merge(
                $uqBase->clone()
                    ->where('payment.level', 0)->whereNotNull('payment.due_date')
                    ->whereDate('payment.due_date', '<=', Carbon::today())
                    ->groupBy('payment.id')
                    ->select('payment.*', 'uq.total as harga_total', 'uq.tax', 'c.info', 'u.id as id_sales')->get()
            );

        $ondue = $spBase->clone()
            ->where('payment.level', 0)->whereNotNull('payment.due_date')
            ->whereDate('payment.due_date', '>', Carbon::today())
            ->groupBy('payment.id')
            ->select('payment.*', 'q.harga_total', 'q.tax', 'c.info', 'u.id as id_sales')->get()
            ->merge(
                $uqBase->clone()
                    ->where('payment.level', 0)->whereNotNull('payment.due_date')
                    ->whereDate('payment.due_date', '>', Carbon::today())
                    ->groupBy('payment.id')
                    ->select('payment.*', 'uq.total as harga_total', 'uq.tax', 'c.info', 'u.id as id_sales')->get()
            );

        $nodueCount = Payment::where('type', 'Tempo')->whereNull('due_date')->count();

        $salesUsers = \App\Models\User::where('role', 'Sales')->orderBy('name', 'asc')->get();

        return view('pages.accounting.payment.index-aging', compact('invoice', 'confirm', 'nodueCount', 'unconfirm', 'overdue', 'ondue', 'salesUsers'));
    }
    public function detail_aging($id)
    {
        $payment = Payment::find($id);
        $today = Carbon::today();
        $diffDue = $today->diffInDays($payment->due_date, false);
        $reminder = Reminder::where('id_payment', $id)->get()->sortByDesc('created_at')->values();

        if ($payment->id_unit_quotation) {
            $quote = $payment->unitQuotation;
            $invoice = Invoice::where('id_unit_quotation', $payment->id_unit_quotation)->whereNotNull('no_invoice')->first();
            $isUnitQuotation = true;
        } else {
            $quote = Quotation::find($payment->id_quotation);
            $invoice = Invoice::where('id_quotation', $quote->id)->first();
            $isUnitQuotation = false;
        }

        return view('pages.accounting.payment.detail-aging', compact('reminder', 'diffDue', 'invoice', 'payment', 'quote', 'isUnitQuotation'));
    }
    public function confirm_payment(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->level = 1;
        $payment->date_confirm = Carbon::now()->toDateString();
        
        $bankId = $request->input('id_bank') ?: $payment->id_bank;
        if (!$bankId) {
            $defaultBank = \App\Models\Bank::first();
            $bankId = $defaultBank ? $defaultBank->id : null;
        }
        $payment->id_bank = $bankId;
        $paymentSave = $payment->save();

        // Increment bank balance
        if ($payment->id_bank && $payment->amount > 0) {
            \App\Models\Bank::find($payment->id_bank)?->increment('saldo', $payment->amount);
        }

        if ($payment->id_unit_quotation) {
            Invoice::where('id_unit_quotation', $payment->id_unit_quotation)
                ->whereNotNull('no_invoice')
                ->update(['status_p' => 1]);
        }

        $activity = new ChangeStatus();
        $activity->id_user = Auth::user()->id;
        $activity->id_payment = $payment->id;
        $activity->note = "Payment Verif By ";
        $activity->status = 2;
        $activity->date = Carbon::now();
        $activity->save();

        if ($paymentSave) {
            $this->prService->evaluatePaymentGate($payment, Auth::id());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Pembayaran berhasil diverifikasi & saldo bank bertambah.']);
            }
            return 1;
        } else {
            return 0;
        }
    }

    public function unconfirm_payment(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        
        // Decrement bank balance if previously incremented
        if ($payment->level == 1 && $payment->id_bank && $payment->amount > 0) {
            \App\Models\Bank::find($payment->id_bank)?->decrement('saldo', $payment->amount);
        }

        $payment->level = 0;
        $payment->date_confirm = null;
        $paymentSave = $payment->save();

        if ($payment->id_unit_quotation) {
            Invoice::where('id_unit_quotation', $payment->id_unit_quotation)
                ->whereNotNull('no_invoice')
                ->update(['status_p' => 0]);
        }

        $activity = new ChangeStatus();
        $activity->id_user = Auth::user()->id;
        $activity->id_payment = $payment->id;
        $activity->note = "Unconfirmed By ";
        $activity->status = 3;
        $activity->date = Carbon::now();
        $activity->save();

        if ($paymentSave) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Verifikasi pembayaran dibatalkan & saldo bank dikoreksi.']);
            }
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Customer Statement of Account (SOA Client / Kartu Piutang)
     */
    public function customerStatement(Request $request)
    {
        $clients = \App\Models\Client::orderBy('company', 'asc')->get();
        $selectedClientId = $request->get('client_id');
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $selectedClient = null;
        $openingBalance = 0;
        $transactions = collect();
        $totalDebit = 0;
        $totalCredit = 0;
        $endingBalance = 0;

        if ($selectedClientId) {
            $selectedClient = \App\Models\Client::find($selectedClientId);

            // Previous invoices (debit) before start date
            $prevQuoteInvoices = Quotation::whereHas('pic', function($q) use ($selectedClientId) {
                $q->where('id_client', $selectedClientId);
            })->whereDate('created_at', '<', $startDate)->sum('harga_total');

            $prevUnitInvoices = \App\Models\UnitQuotation::where('id_client', $selectedClientId)
                ->where(function($q) use ($startDate) {
                    $q->whereDate('date', '<', $startDate)
                      ->orWhere(function($sub) use ($startDate) {
                          $sub->whereNull('date')->whereDate('created_at', '<', $startDate);
                      });
                })->sum('total');

            $prevTotalInvoiced = (float) $prevQuoteInvoices + (float) $prevUnitInvoices;

            // Previous payments (credit) before start date
            $prevPayments = (float) Payment::where('level', 1)
                ->where(function($q) use ($startDate) {
                    $q->whereDate('date', '<', $startDate)
                      ->orWhere(function($sub) use ($startDate) {
                          $sub->whereNull('date')->whereDate('created_at', '<', $startDate);
                      });
                })
                ->where(function($q) use ($selectedClientId) {
                    $q->whereHas('quotation.pic', function($sub) use ($selectedClientId) {
                        $sub->where('id_client', $selectedClientId);
                    })->orWhereHas('unitQuotation', function($sub) use ($selectedClientId) {
                        $sub->where('id_client', $selectedClientId);
                    });
                })->sum('amount');

            $openingBalance = max(0, $prevTotalInvoiced - $prevPayments);

            // Current transactions in date range
            $quotationInvoices = Quotation::whereHas('pic', function($q) use ($selectedClientId) {
                $q->where('id_client', $selectedClientId);
            })->whereDate('created_at', '>=', $startDate)
              ->whereDate('created_at', '<=', $endDate)
              ->get()->map(function($q) {
                return [
                    'date' => $q->created_at ? $q->created_at->toDateString() : $q->po_date,
                    'type' => 'INVOICE',
                    'badge_class' => 'bg-label-primary',
                    'ref' => $q->no_quote ?: ('#QT-' . $q->id),
                    'description' => 'Penjualan Sparepart / Jasa (' . ($q->title ?: 'Quotation') . ')',
                    'debit' => (float) $q->harga_total,
                    'credit' => 0,
                    'link' => route('quotation.show', $q->id),
                ];
            });

            $unitInvoices = \App\Models\UnitQuotation::where('id_client', $selectedClientId)
                ->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])
                      ->orWhere(function($sub) use ($startDate, $endDate) {
                          $sub->whereNull('date')->whereBetween('created_at', [$startDate, $endDate]);
                      });
                })->get()->map(function($uq) {
                    return [
                        'date' => $uq->date ?: ($uq->created_at ? $uq->created_at->toDateString() : Carbon::now()->toDateString()),
                        'type' => 'INVOICE UNIT',
                        'badge_class' => 'bg-label-info',
                        'ref' => $uq->no_quote ?: ('#UQ-' . $uq->id),
                        'description' => 'Penjualan Unit Mesin (' . ($uq->project ?? 'Unit') . ')',
                        'debit' => (float) $uq->total,
                        'credit' => 0,
                        'link' => route('unit-quotation.show', $uq->id),
                    ];
                });

            $currentPayments = Payment::where('level', 1)
                ->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])
                      ->orWhere(function($sub) use ($startDate, $endDate) {
                          $sub->whereNull('date')->whereBetween('created_at', [$startDate, $endDate]);
                      });
                })
                ->where(function($q) use ($selectedClientId) {
                    $q->whereHas('quotation.pic', function($sub) use ($selectedClientId) {
                        $sub->where('id_client', $selectedClientId);
                    })->orWhereHas('unitQuotation', function($sub) use ($selectedClientId) {
                        $sub->where('id_client', $selectedClientId);
                    });
                })->with('bank')->get()->map(function($pay) {
                    $bankName = $pay->bank ? $pay->bank->nama_bank : ($pay->method ?: 'Bank Transfer');
                    return [
                        'date' => $pay->date ?: ($pay->created_at ? $pay->created_at->toDateString() : Carbon::now()->toDateString()),
                        'type' => 'PAYMENT',
                        'badge_class' => 'bg-label-success',
                        'ref' => '#PAY-AR-' . $pay->id,
                        'description' => 'Pembayaran Masuk via ' . $bankName . ($pay->note ? ' (' . $pay->note . ')' : ''),
                        'debit' => 0,
                        'credit' => (float) $pay->amount,
                        'link' => route('payment_detail.payment', $pay->id),
                    ];
                });

            $all = $quotationInvoices->concat($unitInvoices)->concat($currentPayments)->sortBy('date')->values();

            $running = $openingBalance;
            $transactions = $all->map(function ($item) use (&$running, &$totalDebit, &$totalCredit) {
                $totalDebit += $item['debit'];
                $totalCredit += $item['credit'];
                $running = $running + $item['debit'] - $item['credit'];
                $item['balance'] = $running;
                return (object) $item;
            });

            $endingBalance = $running;
            $closingBalance = $running;
            $ledger = $transactions;
            $client = $selectedClient;
        } else {
            $closingBalance = 0;
            $ledger = collect();
            $client = null;
        }

        return view('pages.accounting.payment.customer-statement', compact(
            'clients',
            'selectedClientId',
            'selectedClient',
            'client',
            'startDate',
            'endDate',
            'openingBalance',
            'transactions',
            'ledger',
            'totalDebit',
            'totalCredit',
            'endingBalance',
            'closingBalance'
        ));
    }

    /**
     * Print Customer Statement of Account
     */
    public function customerStatementPrint(Request $request, $id)
    {
        $selectedClient = \App\Models\Client::findOrFail($id);
        $client = $selectedClient;
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $prevQuoteInvoices = Quotation::whereHas('pic', function($q) use ($id) {
            $q->where('id_client', $id);
        })->whereDate('created_at', '<', $startDate)->sum('harga_total');

        $prevUnitInvoices = \App\Models\UnitQuotation::where('id_client', $id)
            ->where(function($q) use ($startDate) {
                $q->whereDate('date', '<', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('date')->whereDate('created_at', '<', $startDate);
                  });
            })->sum('total');

        $prevTotalInvoiced = (float) $prevQuoteInvoices + (float) $prevUnitInvoices;

        $prevPayments = (float) Payment::where('level', 1)
            ->where(function($q) use ($startDate) {
                $q->whereDate('date', '<', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('date')->whereDate('created_at', '<', $startDate);
                  });
            })
            ->where(function($q) use ($id) {
                $q->whereHas('quotation.pic', function($sub) use ($id) {
                    $sub->where('id_client', $id);
                })->orWhereHas('unitQuotation', function($sub) use ($id) {
                    $sub->where('id_client', $id);
                });
            })->sum('amount');

        $openingBalance = max(0, $prevTotalInvoiced - $prevPayments);

        $quotationInvoices = Quotation::whereHas('pic', function($q) use ($id) {
            $q->where('id_client', $id);
        })->whereDate('created_at', '>=', $startDate)
          ->whereDate('created_at', '<=', $endDate)
          ->get()->map(function($q) {
            return [
                'date' => $q->created_at ? $q->created_at->toDateString() : $q->po_date,
                'type' => 'DEBIT',
                'ref_no' => $q->no_quote ?: ('#QT-' . $q->id),
                'po_number' => $q->po_number ?? '',
                'description' => 'Penjualan Sparepart / Jasa (' . ($q->title ?: 'Quotation') . ')',
                'bank_name' => '',
                'debit' => (float) $q->harga_total,
                'credit' => 0,
            ];
        });

        $unitInvoices = \App\Models\UnitQuotation::where('id_client', $id)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('date')->whereBetween('created_at', [$startDate, $endDate]);
                  });
            })->get()->map(function($uq) {
                return [
                    'date' => $uq->date ?: ($uq->created_at ? $uq->created_at->toDateString() : Carbon::now()->toDateString()),
                    'type' => 'DEBIT',
                    'ref_no' => $uq->no_quote ?: ('#UQ-' . $uq->id),
                    'po_number' => $uq->po_number ?? '',
                    'description' => 'Penjualan Unit Mesin (' . ($uq->project ?? 'Unit') . ')',
                    'bank_name' => '',
                    'debit' => (float) $uq->total,
                    'credit' => 0,
                ];
            });

        $currentPayments = Payment::where('level', 1)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('date')->whereBetween('created_at', [$startDate, $endDate]);
                  });
            })
            ->where(function($q) use ($id) {
                $q->whereHas('quotation.pic', function($sub) use ($id) {
                    $sub->where('id_client', $id);
                })->orWhereHas('unitQuotation', function($sub) use ($id) {
                    $sub->where('id_client', $id);
                });
            })->with('bank')->get()->map(function($pay) {
                $bankName = $pay->bank ? $pay->bank->nama_bank : ($pay->method ?: 'Bank Transfer');
                return [
                    'date' => $pay->date ?: ($pay->created_at ? $pay->created_at->toDateString() : Carbon::now()->toDateString()),
                    'type' => 'CREDIT',
                    'ref_no' => '#PAY-AR-' . $pay->id,
                    'po_number' => '',
                    'description' => 'Pembayaran Masuk via ' . $bankName . ($pay->note ? ' (' . $pay->note . ')' : ''),
                    'bank_name' => $bankName,
                    'debit' => 0,
                    'credit' => (float) $pay->amount,
                ];
            });

        $all = $quotationInvoices->concat($unitInvoices)->concat($currentPayments)->sortBy('date')->values();

        $totalDebit = 0;
        $totalCredit = 0;
        $running = $openingBalance;
        $ledger = $all->map(function ($item) use (&$running, &$totalDebit, &$totalCredit) {
            $totalDebit += $item['debit'];
            $totalCredit += $item['credit'];
            $running = $running + $item['debit'] - $item['credit'];
            $item['running_balance'] = $running;
            return $item;
        });

        $closingBalance = $running;

        return view('pages.accounting.payment.customer-statement-print', compact(
            'client',
            'startDate',
            'endDate',
            'openingBalance',
            'ledger',
            'totalDebit',
            'totalCredit',
            'closingBalance'
        ));
    }

    /**
     * Export AR Aging Report to Excel/CSV.
     */
    public function exportAgingExcel(Request $request)
    {
        $payments = Payment::where('level', 0)
            ->with(['quotation.pic.client', 'unitQuotation.client', 'quotation.sales', 'unitQuotation.sales'])
            ->get();

        $filename = 'AR_Aging_Report_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($payments) {
            $output = fopen('php://output', 'w');
            fputs($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'No. Tagihan / Ref',
                'No. PO Customer',
                'Customer / Klien',
                'Sales Person',
                'Tipe Pembayaran',
                'Jatuh Tempo',
                'Nominal Tagihan (Rp)',
                'Keterlambatan (Hari)',
                'Status Jatuh Tempo',
            ]);

            $today = Carbon::today();
            foreach ($payments as $p) {
                $clientName = '-';
                $poNumber = '-';
                $salesName = '-';
                $refNo = '#RCPT-' . $p->id;

                if ($p->unitQuotation) {
                    $clientName = $p->unitQuotation->client?->company ?? '-';
                    $poNumber = $p->unitQuotation->po_number ?? '-';
                    $salesName = $p->unitQuotation->sales?->name ?? '-';
                    $refNo = $p->unitQuotation->no_quote ?: $refNo;
                } elseif ($p->quotation) {
                    $clientName = $p->quotation->pic?->client?->company ?? '-';
                    $poNumber = $p->quotation->po_number ?? '-';
                    $salesName = $p->quotation->sales?->name ?? '-';
                    $refNo = $p->quotation->no_quote ?: $refNo;
                }

                $dueDate = $p->due_date ? Carbon::parse($p->due_date) : null;
                $daysDiff = $dueDate ? $today->diffInDays($dueDate, false) : 0;
                
                $status = 'BELUM DISETTING';
                if ($dueDate) {
                    if ($today->gt($dueDate)) {
                        $status = 'OVERDUE (' . abs($daysDiff) . ' Hari)';
                    } elseif ($daysDiff <= 7) {
                        $status = 'DUE SOON (' . $daysDiff . ' Hari Lagi)';
                    } else {
                        $status = 'CURRENT / ON DUE';
                    }
                }

                fputcsv($output, [
                    $refNo,
                    $poNumber,
                    $clientName,
                    $salesName,
                    $p->type ?: 'Tempo',
                    $dueDate ? $dueDate->format('d/m/Y') : 'Tanpa Jatuh Tempo',
                    (float) $p->amount,
                    $dueDate && $today->gt($dueDate) ? abs($daysDiff) : 0,
                    $status,
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Customer SOA (Kartu Piutang) to Excel/CSV.
     */
    public function exportCustomerStatementExcel(Request $request, $id)
    {
        $selectedClient = \App\Models\Client::findOrFail($id);
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $prevQuoteInvoices = Quotation::whereHas('pic', function($q) use ($id) {
            $q->where('id_client', $id);
        })->whereDate('created_at', '<', $startDate)->sum('harga_total');

        $prevUnitInvoices = \App\Models\UnitQuotation::where('id_client', $id)
            ->where(function($q) use ($startDate) {
                $q->whereDate('date', '<', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('date')->whereDate('created_at', '<', $startDate);
                  });
            })->sum('total');

        $prevTotalInvoiced = (float) $prevQuoteInvoices + (float) $prevUnitInvoices;

        $prevPayments = (float) Payment::where('level', 1)
            ->where(function($q) use ($startDate) {
                $q->whereDate('date', '<', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('date')->whereDate('created_at', '<', $startDate);
                  });
            })
            ->where(function($q) use ($id) {
                $q->whereHas('quotation.pic', function($sub) use ($id) {
                    $sub->where('id_client', $id);
                })->orWhereHas('unitQuotation', function($sub) use ($id) {
                    $sub->where('id_client', $id);
                });
            })->sum('amount');

        $openingBalance = max(0, $prevTotalInvoiced - $prevPayments);

        $quotationInvoices = Quotation::whereHas('pic', function($q) use ($id) {
            $q->where('id_client', $id);
        })->whereDate('created_at', '>=', $startDate)
          ->whereDate('created_at', '<=', $endDate)
          ->get()->map(function($q) {
            return [
                'date' => $q->created_at ? $q->created_at->toDateString() : $q->po_date,
                'type' => 'INVOICE (DEBIT)',
                'ref_no' => $q->no_quote ?: ('#QT-' . $q->id),
                'description' => 'Penjualan Sparepart / Jasa (' . ($q->title ?: 'Quotation') . ')',
                'debit' => (float) $q->harga_total,
                'credit' => 0,
            ];
        });

        $unitInvoices = \App\Models\UnitQuotation::where('id_client', $id)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('date')->whereBetween('created_at', [$startDate, $endDate]);
                  });
            })->get()->map(function($uq) {
                return [
                    'date' => $uq->date ?: ($uq->created_at ? $uq->created_at->toDateString() : Carbon::now()->toDateString()),
                    'type' => 'INVOICE (DEBIT)',
                    'ref_no' => $uq->no_quote ?: ('#UQ-' . $uq->id),
                    'description' => 'Penjualan Unit Reftech / Kojisha (PO: ' . ($uq->po_number ?: '-') . ')',
                    'debit' => (float) $uq->total,
                    'credit' => 0,
                ];
            });

        $payments = Payment::where('level', 1)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('date')->whereBetween('created_at', [$startDate, $endDate]);
                  });
            })
            ->where(function($q) use ($id) {
                $q->whereHas('quotation.pic', function($sub) use ($id) {
                    $sub->where('id_client', $id);
                })->orWhereHas('unitQuotation', function($sub) use ($id) {
                    $sub->where('id_client', $id);
                });
            })
            ->with(['bank', 'unitQuotation', 'quotation'])
            ->get()->map(function($p) {
                $quoteNo = $p->unitQuotation?->no_quote ?: ($p->quotation?->no_quote ?: ('#PAY-' . $p->id));
                $bankName = $p->bank?->bank ?: 'Kas/Bank';
                return [
                    'date' => $p->date ?: ($p->created_at ? $p->created_at->toDateString() : Carbon::now()->toDateString()),
                    'type' => 'PAYMENT (CREDIT)',
                    'ref_no' => $quoteNo,
                    'description' => 'Penerimaan Pembayaran via ' . $bankName . ($p->note ? ' (' . $p->note . ')' : ''),
                    'debit' => 0,
                    'credit' => (float) $p->amount,
                ];
            });

        $all = $quotationInvoices->concat($unitInvoices)->concat($payments)->sortBy('date')->values();
        $cleanClientName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $selectedClient->company ?: $selectedClient->name);
        $filename = 'Kartu_Piutang_' . $cleanClientName . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($selectedClient, $startDate, $endDate, $openingBalance, $all) {
            $output = fopen('php://output', 'w');
            fputs($output, "\xEF\xBB\xBF");

            fputcsv($output, ['KARTU PIUTANG CUSTOMER (STATEMENT OF ACCOUNT)']);
            fputcsv($output, ['Customer / Perusahaan', $selectedClient->company ?: $selectedClient->name]);
            fputcsv($output, ['Periode', Carbon::parse($startDate)->format('d/m/Y') . ' s/d ' . Carbon::parse($endDate)->format('d/m/Y')]);
            fputcsv($output, ['Saldo Awal Piutang', $openingBalance]);
            fputcsv($output, []);

            fputcsv($output, ['Tanggal', 'Tipe Mutasi', 'No. Referensi / Quote', 'Keterangan Transaksi', 'Debit / Tagihan Penjualan (Rp)', 'Kredit / Pembayaran Diterima (Rp)', 'Saldo Piutang (Rp)']);

            $running = $openingBalance;
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($all as $item) {
                $totalDebit += $item['debit'];
                $totalCredit += $item['credit'];
                $running = $running + $item['debit'] - $item['credit'];

                fputcsv($output, [
                    Carbon::parse($item['date'])->format('d/m/Y'),
                    $item['type'],
                    $item['ref_no'],
                    $item['description'],
                    $item['debit'] > 0 ? $item['debit'] : 0,
                    $item['credit'] > 0 ? $item['credit'] : 0,
                    $running,
                ]);
            }

            fputcsv($output, ['TOTAL', '', '', '', $totalDebit, $totalCredit, $running]);
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Digital Official Receipt / Kwitansi
     */
    public function showKwitansi($id)
    {
        $payment = Payment::with(['bank', 'quotation.pic.client', 'unitQuotation.client'])->findOrFail($id);
        
        $client = null;
        $clientName = '-';
        $noInvoice = '-';
        $invoiceNumber = '-';
        $poNumber = '-';

        if ($payment->id_unit_quotation && $payment->unitQuotation) {
            $client = $payment->unitQuotation->client;
            $clientName = $client?->company ?? '-';
            $poNumber = $payment->unitQuotation->po_number ?? '-';
            $invoice = Invoice::where('id_unit_quotation', $payment->id_unit_quotation)->first();
            $noInvoice = $invoice?->no_invoice ?? '-';
            $invoiceNumber = $noInvoice;
        } elseif ($payment->id_quotation && $payment->quotation) {
            $client = $payment->quotation->pic?->client;
            $clientName = $client?->company ?? '-';
            $poNumber = $payment->quotation->po_number ?? '-';
            $invoice = Invoice::where('id_quotation', $payment->id_quotation)->first();
            $noInvoice = $invoice?->no_invoice ?? '-';
            $invoiceNumber = $noInvoice;
        }

        $amount = (float) $payment->amount;
        $kwitansiNumber = 'KWT-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT) . '/' . Carbon::parse($payment->date ?? $payment->created_at)->format('m/Y');
        $paymentType = $payment->type ?: 'Pelunasan Tagihan';

        $terbilang = $this->capitalizeWords(
            trim($this->terbilang($amount))
        );

        return view('pages.accounting.payment.kwitansi', compact(
            'payment',
            'client',
            'clientName',
            'noInvoice',
            'invoiceNumber',
            'poNumber',
            'amount',
            'kwitansiNumber',
            'paymentType',
            'terbilang'
        ));
    }

    public function view_payment($id)
    {
        $payment = Payment::findOrFail($id);

        $status = ChangeStatus::where('id_payment', $id)
            ->where('id_user', Auth::id())
            ->where('status', 1)
            ->first();

        if (!$status) {
            ChangeStatus::create([
                'id_user' => Auth::id(),
                'id_payment' => $id,
                'note' => "Payment View By ",
                'status' => 1,
                'date' => Carbon::now(),
            ]);
        }

        return redirect(url($payment->file));
    }

    public function reminder_payment(Request $request, $id)
    {
        $reminder = new Reminder;
        $remCount = Reminder::where('id_payment', $id)->get()->count();
        $reminder->id_user = Auth::user()->id;
        $reminder->id_payment = $id;
        $reminder->reminder = $request->reminder;
        $reminder->date_fu = $request->date_fu;
        $reminder->date = Carbon::now();
        $reminder->status = $remCount + 1;
        $reminderSave = $reminder->save();
        if ($reminderSave) {
            return redirect('/payment-detail/aging/' . $id)->with("success", "data telah di buat");
        }
    }

    public function reminder_calendar(Request $request)
    {
        $reminder = new Reminder;
        $remCount = Reminder::where('id_payment', $request->input('id_payment'))->get()->count();
        $reminder->id_payment = $request->input('id_payment');
        $reminder->id_user = Auth::user()->id;
        $reminder->reminder = $request->input('note');
        $reminder->date_fu = $request->input('follow_up');
        $reminder->date = Carbon::now();
        $reminder->status = $remCount + 1;
        $reminderSave = $reminder->save();
        if ($reminderSave) {
            return response()->json(['success' => true, 'message' => 'Perubahan berhasil disimpan']);
        }
    }

    public function addPph(Request $request, $id)
    {
        $payment = Payment::find($id);
        $payment->pph = $request->pph;
        $paymentSave = $payment->save();
        if ($paymentSave) {
            return redirect('/payment-detail/payment/' . $id)->with('success', 'PPH berhasil ditambahkan!');
        }
    }

    public function addCost(Request $request, $id)
    {
        $payment = Payment::find($id);
        $payment->cost = $request->cost;
        $paymentSave = $payment->save();
        if ($paymentSave) {
            return redirect('/payment-detail/payment/' . $id)->with('success', 'Cost berhasil ditambahkan!');
        }
    }

    public function editDate(Request $request, $id)
    {
        $payment = Payment::find($id);
        $payment->date = $request->date;
        $paymentSave = $payment->save();
        if ($paymentSave) {
            return redirect('/payment-detail/payment/' . $id)->with('success', 'Date Telah Diubah!');
        }
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
