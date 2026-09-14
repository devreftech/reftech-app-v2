<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerStatementController extends Controller
{
    /**
     * Display the Customer Statement of Account (Kartu Piutang).
     */
    public function index(Request $request)
    {
        $selectedClientId = $request->get('client_id');
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $selectedClient = null;
        $client = null;
        $openingBalance = 0;
        $transactions = collect();
        $totalDebit = 0;
        $totalCredit = 0;
        $endingBalance = 0;

        if ($selectedClientId) {
            $selectedClient = Client::with('sales')->find($selectedClientId);
            if ($selectedClient) {
                $client = $selectedClient;

                $data = $this->getDataForClient($selectedClientId, $startDate, $endDate);
                $openingBalance = $data['openingBalance'];
                $transactions = $data['transactions'];
                $totalDebit = $data['totalDebit'];
                $totalCredit = $data['totalCredit'];
                $endingBalance = $data['endingBalance'];
            }
        }

        return view('pages.accounting.payment.customer-statement', compact(
            'selectedClientId',
            'selectedClient',
            'client',
            'startDate',
            'endDate',
            'openingBalance',
            'transactions',
            'totalDebit',
            'totalCredit',
            'endingBalance'
        ));
    }

    /**
     * Search clients via AJAX for Select2 with minimum 2 characters.
     * Only displays clients that have invoice transactions,
     * and excludes any client who has ever used Escrow payment.
     */
    public function searchClients(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $clients = Client::with('sales')
            ->where(function ($query) {
                // Must have invoice from quotation (sparepart) or unit_quotation (unit)
                $query->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('invoice')
                        ->join('quotation', 'quotation.id', '=', 'invoice.id_quotation')
                        ->join('pic', 'pic.id', '=', 'quotation.id_pic')
                        ->whereColumn('pic.id_client', 'client.id')
                        ->whereNotNull('invoice.no_invoice');
                })
                ->orWhereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('invoice')
                        ->join('unit_quotation', 'unit_quotation.id', '=', 'invoice.id_unit_quotation')
                        ->whereColumn('unit_quotation.id_client', 'client.id')
                        ->whereNotNull('invoice.no_invoice');
                });
            })
            ->whereNotExists(function ($sub) {
                // Exclude clients who have used Escrow payment on quotation (sparepart)
                $sub->select(DB::raw(1))
                    ->from('payment')
                    ->join('quotation', 'quotation.id', '=', 'payment.id_quotation')
                    ->join('pic', 'pic.id', '=', 'quotation.id_pic')
                    ->whereColumn('pic.id_client', 'client.id')
                    ->where(function ($p) {
                        $p->where('payment.method', 'Escrow')
                          ->orWhereNotNull('payment.escrow_channel');
                    });
            })
            ->whereNotExists(function ($sub) {
                // Exclude clients who have used Escrow payment on unit_quotation (unit)
                $sub->select(DB::raw(1))
                    ->from('payment')
                    ->join('unit_quotation', 'unit_quotation.id', '=', 'payment.id_unit_quotation')
                    ->whereColumn('unit_quotation.id_client', 'client.id')
                    ->where(function ($p) {
                        $p->where('payment.method', 'Escrow')
                          ->orWhereNotNull('payment.escrow_channel');
                    });
            })
            ->where(function ($sub) use ($q) {
                $sub->where('company', 'LIKE', "%{$q}%")
                    ->orWhere('ru', 'LIKE', "%{$q}%")
                    ->orWhereHas('sales', function ($sq) use ($q) {
                        $sq->where('name', 'LIKE', "%{$q}%");
                    });
            })
            ->orderBy('company')
            ->limit(30)
            ->get(['id', 'company', 'ru', 'id_sales'])
            ->map(function ($c) {
                $salesName = $c->sales ? $c->sales->name : 'No Sales';
                $companyLabel = $c->company . ($c->ru ? ' (' . $c->ru . ')' : '');
                return [
                    'id' => $c->id,
                    'text' => '[' . $salesName . '] ' . $companyLabel,
                    'sales' => $salesName,
                    'company' => $companyLabel,
                ];
            });

        return response()->json($clients);
    }

    /**
     * Print statement of account for customer.
     */
    public function print(Request $request, $id)
    {
        $selectedClient = Client::findOrFail($id);
        $client = $selectedClient;
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $data = $this->getDataForClient($id, $startDate, $endDate);

        return view('pages.accounting.payment.customer-statement-print', array_merge($data, [
            'selectedClient' => $selectedClient,
            'client' => $client,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]));
    }

    /**
     * Export statement of account to CSV/Excel.
     */
    public function exportExcel(Request $request, $id)
    {
        $selectedClient = Client::findOrFail($id);
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $data = $this->getDataForClient($id, $startDate, $endDate);

        $filename = 'SOA_Piutang_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $selectedClient->company) . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($selectedClient, $startDate, $endDate, $data) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($handle, ['STATEMENT OF ACCOUNT (KARTU PIUTANG PELANGGAN)']);
            fputcsv($handle, ['Pelanggan:', $selectedClient->company]);
            fputcsv($handle, ['Alamat:', $selectedClient->address ?: '-']);
            fputcsv($handle, ['Periode:', Carbon::parse($startDate)->format('d/m/Y') . ' s/d ' . Carbon::parse($endDate)->format('d/m/Y')]);
            fputcsv($handle, ['Tanggal Cetak:', date('d/m/Y H:i:s')]);
            fputcsv($handle, []);

            fputcsv($handle, ['Tanggal', 'Tipe', 'No. Referensi', 'No. PO', 'Keterangan', 'Tagihan / Debit (Rp)', 'Pembayaran / Kredit (Rp)', 'Saldo Piutang (Rp)']);

            fputcsv($handle, [
                Carbon::parse($startDate)->format('d/m/Y'),
                'SALDO AWAL',
                '-',
                '-',
                'Saldo Piutang Awal Periode',
                0,
                0,
                $data['openingBalance'],
            ]);

            foreach ($data['transactions'] as $row) {
                fputcsv($handle, [
                    Carbon::parse($row->date)->format('d/m/Y'),
                    $row->type,
                    $row->ref,
                    $row->po,
                    $row->description,
                    $row->debit,
                    $row->credit,
                    $row->balance,
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', 'TOTAL', '', $data['totalDebit'], $data['totalCredit'], $data['endingBalance']]);
            fputcsv($handle, ['', '', '', 'SALDO AKHIR PIUTANG', '', '', '', $data['endingBalance']]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper to compute statement data for a client and period.
     */
    protected function getDataForClient($clientId, $startDate, $endDate)
    {
        // 1. Calculate opening balance before start_date
        $prevSpInvoices = (float) Invoice::join('quotation', 'quotation.id', '=', 'invoice.id_quotation')
            ->join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->where('pic.id_client', $clientId)
            ->whereNotNull('invoice.no_invoice')
            ->where('invoice.date', '<', $startDate)
            ->sum('quotation.harga_total');

        $prevUqInvoices = (float) Invoice::join('unit_quotation', 'unit_quotation.id', '=', 'invoice.id_unit_quotation')
            ->where('unit_quotation.id_client', $clientId)
            ->whereNotNull('invoice.no_invoice')
            ->where('invoice.date', '<', $startDate)
            ->sum('unit_quotation.total');

        $prevSpPayments = (float) Payment::join('quotation', 'quotation.id', '=', 'payment.id_quotation')
            ->join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->where('pic.id_client', $clientId)
            ->where('payment.level', 1)
            ->where('payment.date', '<', $startDate)
            ->sum('payment.amount');

        $prevUqPayments = (float) Payment::join('unit_quotation', 'unit_quotation.id', '=', 'payment.id_unit_quotation')
            ->where('unit_quotation.id_client', $clientId)
            ->where('payment.level', 1)
            ->where('payment.date', '<', $startDate)
            ->sum('payment.amount');

        $openingBalance = max(0, ($prevSpInvoices + $prevUqInvoices) - ($prevSpPayments + $prevUqPayments));

        // 2. Invoices in date range
        $spInvoices = Invoice::join('quotation', 'quotation.id', '=', 'invoice.id_quotation')
            ->join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->where('pic.id_client', $clientId)
            ->whereNotNull('invoice.no_invoice')
            ->whereBetween('invoice.date', [$startDate, $endDate])
            ->select([
                'invoice.id',
                'invoice.no_invoice',
                'invoice.no_po',
                'invoice.date',
                'quotation.harga_total as amount',
                'quotation.no_quote',
            ])
            ->get()
            ->map(function ($inv) {
                return [
                    'date' => $inv->date,
                    'type' => 'INVOICE',
                    'badge_class' => 'bg-label-primary',
                    'ref' => $inv->no_invoice,
                    'po' => $inv->no_po ?: '-',
                    'description' => 'Penagihan Sales Invoice (Ref: ' . ($inv->no_quote ?: '-') . ')',
                    'debit' => (float) $inv->amount,
                    'credit' => 0,
                    'link' => route('payment_detail.invoice', $inv->id),
                ];
            });

        $uqInvoices = Invoice::join('unit_quotation', 'unit_quotation.id', '=', 'invoice.id_unit_quotation')
            ->where('unit_quotation.id_client', $clientId)
            ->whereNotNull('invoice.no_invoice')
            ->whereBetween('invoice.date', [$startDate, $endDate])
            ->select([
                'invoice.id',
                'invoice.no_invoice',
                'invoice.no_po',
                'invoice.date',
                'unit_quotation.total as amount',
                'unit_quotation.no_quote',
            ])
            ->get()
            ->map(function ($inv) {
                return [
                    'date' => $inv->date,
                    'type' => 'INVOICE',
                    'badge_class' => 'bg-label-primary',
                    'ref' => $inv->no_invoice,
                    'po' => $inv->no_po ?: '-',
                    'description' => 'Penagihan Sales Invoice Unit (Ref: ' . ($inv->no_quote ?: '-') . ')',
                    'debit' => (float) $inv->amount,
                    'credit' => 0,
                    'link' => route('payment_detail.invoice', $inv->id),
                ];
            });

        // 3. Payments in date range
        $spPayments = Payment::join('quotation', 'quotation.id', '=', 'payment.id_quotation')
            ->join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->leftJoin('bank', 'bank.id', '=', 'payment.id_bank')
            ->where('pic.id_client', $clientId)
            ->where('payment.level', 1)
            ->whereBetween('payment.date', [$startDate, $endDate])
            ->select([
                'payment.id',
                'payment.amount',
                'payment.date',
                'payment.method',
                'payment.type',
                'payment.note',
                'bank.bank as bank_name',
            ])
            ->get()
            ->map(function ($pay) {
                $bank = $pay->bank_name ? ' via ' . $pay->bank_name : '';
                $method = $pay->method ?: ($pay->type ?: 'Payment');
                return [
                    'date' => $pay->date,
                    'type' => 'PAYMENT',
                    'badge_class' => 'bg-label-success',
                    'ref' => '#RCPT-' . $pay->id,
                    'po' => '-',
                    'description' => 'Penerimaan Pembayaran (' . $method . ')' . $bank . ($pay->note ? ' - ' . $pay->note : ''),
                    'debit' => 0,
                    'credit' => (float) $pay->amount,
                    'link' => route('payment_detail.payment', $pay->id),
                ];
            });

        $uqPayments = Payment::join('unit_quotation', 'unit_quotation.id', '=', 'payment.id_unit_quotation')
            ->leftJoin('bank', 'bank.id', '=', 'payment.id_bank')
            ->where('unit_quotation.id_client', $clientId)
            ->where('payment.level', 1)
            ->whereBetween('payment.date', [$startDate, $endDate])
            ->select([
                'payment.id',
                'payment.amount',
                'payment.date',
                'payment.method',
                'payment.type',
                'payment.note',
                'bank.bank as bank_name',
            ])
            ->get()
            ->map(function ($pay) {
                $bank = $pay->bank_name ? ' via ' . $pay->bank_name : '';
                $method = $pay->method ?: ($pay->type ?: 'Payment');
                return [
                    'date' => $pay->date,
                    'type' => 'PAYMENT',
                    'badge_class' => 'bg-label-success',
                    'ref' => '#RCPT-U-' . $pay->id,
                    'po' => '-',
                    'description' => 'Penerimaan Pembayaran Unit (' . $method . ')' . $bank . ($pay->note ? ' - ' . $pay->note : ''),
                    'debit' => 0,
                    'credit' => (float) $pay->amount,
                    'link' => route('payment_detail.payment', $pay->id),
                ];
            });

        $rawTransactions = $spInvoices->concat($uqInvoices)
            ->concat($spPayments)
            ->concat($uqPayments)
            ->sortBy(function ($t) {
                return $t['date'] . '_' . ($t['type'] === 'INVOICE' ? '0' : '1');
            })
            ->values();

        $running = $openingBalance;
        $totalDebit = 0;
        $totalCredit = 0;

        $transactions = $rawTransactions->map(function ($item) use (&$running, &$totalDebit, &$totalCredit) {
            $totalDebit += $item['debit'];
            $totalCredit += $item['credit'];
            $running = $running + $item['debit'] - $item['credit'];
            $item['balance'] = $running;
            return (object) $item;
        });

        return [
            'openingBalance' => $openingBalance,
            'transactions' => $transactions,
            'ledger' => $transactions,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'endingBalance' => $running,
            'closingBalance' => $running,
        ];
    }
}
