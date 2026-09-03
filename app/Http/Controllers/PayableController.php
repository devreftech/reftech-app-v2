<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use App\Models\DetailPayable;
use App\Models\DetailProductIn;
use App\Models\Payable;
use App\Models\ProductIn;
use App\Models\Retur;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayableController extends Controller
{

    public function index_invoice()
    {
        $base = ProductIn::whereNotNull('invoice');
        $totalCount = $base->count();
        $totalAmount = $base->sum('total');
        $paidCount = $base->clone()->where('accept', '1')->count();
        $paidAmount = $base->clone()->where('accept', '1')->sum('total');
        $unpaidCount = $base->clone()->where('accept', '0')->count();
        $unpaidAmount = $base->clone()->where('accept', '0')->sum('total');

        return view('pages.finance.payable.index-invoice', compact(
            'totalCount',
            'totalAmount',
            'paidCount',
            'paidAmount',
            'unpaidCount',
            'unpaidAmount'
        ));
    }
    public function show_invoice($id)
    {
        $product = ProductIn::find($id);
        $detProduct = DetailProductIn::where('id_product_in', $id)->get();
        $return = Retur::where('id_product_in', $id)->get();
        // dd($return);
        return view('pages.finance.payable.detail-invoice', compact('product', 'detProduct', 'return'));
    }
    public function index_aging()
    {
        $base = ProductIn::where('accept', '0');

        $unpaid = $base->clone()->get();
        $bucketCurrent = $base->clone()->whereRaw('DATEDIFF(CURDATE(), date) BETWEEN 0 AND 30')->get();
        $bucket31to60 = $base->clone()->whereRaw('DATEDIFF(CURDATE(), date) BETWEEN 31 AND 60')->get();
        $bucket61to90 = $base->clone()->whereRaw('DATEDIFF(CURDATE(), date) BETWEEN 61 AND 90')->get();
        $bucket90plus = $base->clone()->whereRaw('DATEDIFF(CURDATE(), date) > 90')->get();

        return view('pages.finance.payable.index-aging', compact(
            'unpaid',
            'bucketCurrent',
            'bucket31to60',
            'bucket61to90',
            'bucket90plus'
        ));
    }
    public function show_aging($id)
    {
        $product = ProductIn::find($id);
        $detProduct = DetailProductIn::where('id_product_in', $id)->get();
        $today = Carbon::today();
        $diffDue = $today->diffInDays($product->date, false);
        // dd($detProduct);
        return view('pages.finance.payable.detail-aging', compact('product', 'detProduct', 'diffDue'));
    }
    public function index_receipt()
    {
        $base = ProductIn::whereNotNull('invoice');
        $totalCount = $base->count();
        $receipt = $base->sum('total');
        $paidCount = $base->clone()->where('accept', '1')->count();
        $paid = $base->clone()->where('accept', '1')->sum('total');
        $unpaidCount = $base->clone()->where('accept', '0')->count();
        $unpaid = $base->clone()->where('accept', '0')->sum('total');

        return view('pages.finance.payable.index-receipt', compact(
            'totalCount',
            'receipt',
            'paidCount',
            'paid',
            'unpaidCount',
            'unpaid'
        ));
    }
    public function show_receipt($id)
    {
        $product = ProductIn::findOrFail($id);

        $receipt = ProductIn::where('id', $id)
            ->selectRaw("
            CONCAT(
                '#PAY-',
                LPAD(
                    (
                        SELECT COUNT(*)
                        FROM product_in pi2
                        WHERE YEAR(pi2.date) = YEAR(product_in.date)
                          AND pi2.id <= product_in.id
                    ),
                    3,
                    '0'
                ),
                '-',
                RIGHT(YEAR(product_in.date), 2)
            ) as no_receipt
        ")
            ->value('no_receipt'); // ambil string saja

        $detProduct = DetailProductIn::where('id_product_in', $id)->get();

        return view(
            'pages.finance.payable.detail-receipt',
            compact('receipt', 'product', 'detProduct')
        );

    }
    public function storePayable(Request $request)
    {
        // dd($request->all());
        $bank = Bank::find($request->bank);
        $payable = new Payable;
        $payable->id_bank = $request->bank;
        $payable->no_voucher = $request->no_voucher;
        $payable->no_cheque = $request->no_cheque;
        $payable->memo = $request->detail;
        $payable->payee = $request->payee;
        $payable->date = $request->date;
        $payable->amount = $request->total;
        $payableSave = $payable->save();
        if ($payableSave) {
            foreach ($request->account as $item => $value) {
                $dpayable = new DetailPayable();
                $dpayable->id_payable = $payable->id;
                $dpayable->id_account = $request->account[$item];
                $dpayable->memo = $request->memo[$item];
                $dpayable->amount = $request->amount[$item];
                $dpayableSave = $dpayable->save();
            }
        }
        $bank->saldo -= $request->total;
        $bank->save();
        if ($payableSave && $dpayableSave) {
            return redirect('payable')->with('success', 'Data berhasil disimpan');
        }
    }
    public function showPayable($id)
    {
        $payable = Payable::find($id);
        $detailPayable = DetailPayable::where('id_payable', $id)->get();
        $terbilang = $this->capitalizeWords(
            trim($this->terbilang($payable->amount))
        );
        return view('pages.finance.payable.detail', compact('detailPayable', 'payable', 'terbilang'));
    }
    public function showPayablePrint($id)
    {
        $payable = Payable::find($id);
        $detailPayable = DetailPayable::where('id_payable', $id)->get();
        $terbilang = $this->capitalizeWords(
            trim($this->terbilang($payable->amount))
        );
        return view('pages.finance.payable.detail-print', compact('detailPayable', 'payable', 'terbilang'));
    }
    public function deletePayable($id)
    {
        $payable = Payable::find($id);
        $bank = Bank::find($payable->id_bank);
        $detailPayable = DetailPayable::where('id_payable', $id)->get();
        foreach ($detailPayable as $key) {
            $key->delete();
        }
        $bank->saldo += $payable->amount;
        $bank->save();
        $payableDel = $payable->delete();
        if ($payableDel) {
            return 1;
        } else {
            return 0;
        }
    }
    public function addPph(Request $request, $id)
    {
        // dd($request->all());
        $payment = ProductIn::find($id);
        $payment->pph = $request->pph;
        $paymentSave = $payment->save();
        if ($paymentSave) {
            return redirect('/payable/receipt/'. $id)->with('success', 'PPH berhasil ditambahkan!');
        }
    }

    public function editDate(Request $request, $id)
    {
        // dd($request->all());
        $payment = ProductIn::find($id);
        $payment->date_payment = $request->date;
        $paymentSave = $payment->save();
        if ($paymentSave) {
            return redirect('/payable/receipt/' . $id)->with('success', 'Date Telah Diubah!');
        }
    }

    public function confirmReceipt(Request $request, $id)
    {
        $product = ProductIn::findOrFail($id);
        $product->accept = '1';
        if ($request->filled('date_payment')) {
            $product->date_payment = $request->date_payment;
        } elseif (!$product->date_payment) {
            $product->date_payment = Carbon::now()->toDateString();
        }
        $product->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran berhasil dikonfirmasi (PAID).'
            ]);
        }

        return redirect()->route('payable.show_receipt', $id)->with('success', 'Pembayaran berhasil dikonfirmasi (PAID).');
    }

    public function unconfirmReceipt(Request $request, $id)
    {
        $product = ProductIn::findOrFail($id);
        $product->accept = '0';
        $product->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Konfirmasi pembayaran berhasil dibatalkan (UNPAID).'
            ]);
        }

        return redirect()->route('payable.show_receipt', $id)->with('success', 'Konfirmasi pembayaran berhasil dibatalkan (UNPAID).');
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
