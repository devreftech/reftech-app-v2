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
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
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
            ->leftJoin(
                DB::raw('(SELECT id_quotation, SUM(amount) as total_payment 
                  FROM payment 
                  GROUP BY id_quotation) as pay'),
                'quotation.id',
                '=',
                'pay.id_quotation'
            )
            ->where('status', '100')
            ->where('invoice.flag', 'Reftech')
            ->where('quotation.tax', '11')
            ->sum(DB::raw('IFNULL(pay.total_payment, 0)'));
        $sisa = $fullInvoice - $fullPayment;

        $lastPaymentSub = DB::table('payment as p1')
            ->select('p1.id', 'p1.id_quotation', 'p1.amount', 'p1.type', 'p1.due_date', 'p1.method', 'p1.created_at')
            ->join(DB::raw('(SELECT id_quotation, MAX(id) as max_id FROM payment GROUP BY id_quotation) as p2'), 'p1.id', '=', DB::raw('p2.max_id'));
        // dd($lastPaymentSub);
        // dd(DB::select('SELECT p1.* FROM payment p1 INNER JOIN (SELECT id_quotation, MAX(id) as max_id FROM payment GROUP BY id_quotation) p2 ON p1.id = p2.max_id'));
        // dd(DB::select('SELECT id_quotation, SUM(amount) as total_payment FROM payment GROUP BY id_quotation'));
        return view('pages.accounting.payment.ahmad.index-invoice', compact('fullInvoice', 'fullPayment', 'sisa'));
    }
    public function index_invoice_rayi()
    {
        $fullInvoice = Invoice::join('quotation', 'quotation.id', '=', 'invoice.id_quotation')
            ->where('status', '100')
            ->where('invoice.flag', 'Reftech')
            ->where('quotation.tax', '11')
            ->sum('harga_total');
        $fullPayment = Invoice::join('quotation', 'quotation.id', '=', 'invoice.id_quotation')
            ->leftJoin(
                DB::raw('(SELECT id_quotation, SUM(amount) as total_payment 
                  FROM payment 
                  GROUP BY id_quotation) as pay'),
                'quotation.id',
                '=',
                'pay.id_quotation'
            )
            ->where('status', '100')
            ->where('invoice.flag', 'Reftech')
            ->where('quotation.tax', '11')
            ->sum(DB::raw('IFNULL(pay.total_payment, 0)'));
        $sisa = $fullInvoice - $fullPayment;

        $lastPaymentSub = DB::table('payment as p1')
            ->select('p1.id', 'p1.id_quotation', 'p1.amount', 'p1.type', 'p1.due_date', 'p1.method', 'p1.created_at')
            ->join(DB::raw('(SELECT id_quotation, MAX(id) as max_id FROM payment GROUP BY id_quotation) as p2'), 'p1.id', '=', DB::raw('p2.max_id'));
        // dd($lastPaymentSub);
        // dd(DB::select('SELECT p1.* FROM payment p1 INNER JOIN (SELECT id_quotation, MAX(id) as max_id FROM payment GROUP BY id_quotation) p2 ON p1.id = p2.max_id'));
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
        $payment = Payment::where('id_quotation', $quote->id)->get();
        return view('pages.accounting.payment.detail-invoice', compact('invoice', 'quote', 'dQuote', 'payment'));
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
        $payment  = Payment::find($id);
        $activity = ChangeStatus::where('id_payment', $id)->get();

        if ($payment->id_unit_quotation) {
            $quote   = \App\Models\UnitQuotation::with('client')->find($payment->id_unit_quotation);
            $invoice = Invoice::where('id_unit_quotation', $payment->id_unit_quotation)
                ->whereNotNull('no_invoice')->first();
            return view('pages.accounting.payment.detail-payment',
                compact('activity', 'invoice', 'quote', 'payment'))
                ->with('isUnitQuotation', true);
        }

        $quote = Quotation::find($payment->id_quotation);

        if ($payment->type === 'BP') {
            $invoice = Invoice::where('id_quotation', $quote->id)->where('type', 'BP')->first();
        } else {
            $invoice = Invoice::where('id_quotation', $quote->id)->first();
        }
        return view('pages.accounting.payment.detail-payment',
            compact('activity', 'invoice', 'quote', 'payment'))
            ->with('isUnitQuotation', false);
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
    public function confirm_payment($id)
    {
        $payment = Payment::find($id);
        $payment->level = 1;
        $payment->date_confirm = Carbon::now()->toDateString();
        $paymentSave = $payment->save();

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
            return 1;
        } else {
            return 0;
        }
    }
    public function unconfirm_payment($id)
    {
        $payment = Payment::find($id);
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
            return 1;
        } else {
            return 0;
        }
    }
    public function view_payment($id)
    {
        $payment = Payment::findOrFail($id);

        // cek apakah user sudah pernah view dengan kombinasi payment + user + status
        $status = ChangeStatus::where('id_payment', $id)
            ->where('id_user', Auth::id())
            ->where('status', 1)
            ->first();

        if (!$status) {
            // hanya bikin baru kalau belum ada
            ChangeStatus::create([
                'id_user' => Auth::id(),
                'id_payment' => $id,
                'note' => "Payment View By ",
                'status' => 1,
                'date' => Carbon::now(),
            ]);
        }

        // redirect ke file aslinya
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
        // dd($request->all());
        $payment = Payment::find($id);
        $payment->pph = $request->pph;
        $paymentSave = $payment->save();
        if ($paymentSave) {
            return redirect('/payment-detail/payment/' . $id)->with('success', 'PPH berhasil ditambahkan!');
        }
    }

    public function addCost(Request $request, $id)
    {
        // dd($request->all());
        $payment = Payment::find($id);
        $payment->cost = $request->cost;
        $paymentSave = $payment->save();
        if ($paymentSave) {
            return redirect('/payment-detail/payment/' . $id)->with('success', 'Cost berhasil ditambahkan!');
        }
    }

    public function editDate(Request $request, $id)
    {
        // dd($request->all());
        $payment = Payment::find($id);
        $payment->date = $request->date;
        $paymentSave = $payment->save();
        if ($paymentSave) {
            return redirect('/payment-detail/payment/' . $id)->with('success', 'Date Telah Diubah!');
        }
    }
}
