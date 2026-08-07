<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\DetailQuotation;
use App\Models\Invoice;
use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\SubtitleQuotation;
use App\Models\UnitQuotation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $requestContract = Contract::where('level', '0')->count();
        $requestInvoice = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->join('users', 'users.id', '=', 'quotation.id_sales')
            ->where('status', '100')
            ->whereNotNull('quotation.po_file')
            ->whereNull('invoice.no_invoice')
            ->count()
            + Invoice::pendingUnitRequest()->count();
        $contracts = Contract::with(['quotation.pic.client', 'unitQuotation.client'])
            ->where('level', '0')
            ->get();
        // dd($contracts->quotation);
        $today = Carbon::now();
        $thisYear = $today->year;
        $numberLastSP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '11')->where('contract.type', 'Selling')->where('contract.level', '1')->groupBy('contract.id')->orderByDesc('contract.id')->first('contract.no_contract');
        $numberLastSNP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '0')->where('contract.type', 'Selling')->where('contract.level', '1')->groupBy('contract.id')->orderByDesc('contract.id')->first('contract.no_contract');
        $numberLastCP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '11')->where('contract.type', 'Order')->where('contract.level', '1')->groupBy('contract.id')->orderByDesc('contract.id')->first('contract.no_contract');
        $numberLastCNP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '0')->where('contract.type', 'Order')->where('contract.level', '1')->groupBy('contract.id')->orderByDesc('contract.id')->first('contract.no_contract');
        // dd($numberLastSNP);
        $numberSP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '11')->where('contract.type', 'Selling')->where('contract.level', '1')->groupBy('contract.id')->get('contract.id');
        $numberSNP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '0')->where('contract.type', 'Selling')->where('contract.level', '1')->groupBy('contract.id')->get('contract.id');
        $numberCP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '11')->where('contract.type', 'Order')->where('contract.level', '1')->groupBy('contract.id')->get('contract.id');
        $numberCNP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '0')->where('contract.type', 'Order')->where('contract.level', '1')->groupBy('contract.id')->get('contract.id');
        $formattedNumberSP  = $this->generateNextContractNumber($numberLastSP, '001');
        $formattedNumberSNP = $this->generateNextContractNumber($numberLastSNP, '001');
        $formattedNumberCP  = $this->generateNextContractNumber($numberLastCP, '001');
        $formattedNumberCNP = $this->generateNextContractNumber($numberLastCNP, '001');
        // Unit selling contract — sequential across ALL selling contracts this year
        $numberLastSC      = Contract::where('type', 'Selling')->where('level', '1')
            ->whereYear('date', $today)->orderByDesc('id')->first('no_contract');
        $formattedNumberSC = $this->generateNextContractNumber($numberLastSC, '001');
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        return view('pages.accounting.contract.index', compact('requestContract','requestInvoice','noSaleProspect', 'contracts', 'thisYear', 'formattedNumberSP', 'formattedNumberSNP', 'formattedNumberCP', 'formattedNumberCNP', 'numberLastSP', 'numberLastSNP', 'numberLastCP', 'numberLastCNP', 'formattedNumberSC'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $requestContract = Contract::where('level', '0')->count();
        $requestInvoice = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->join('users', 'users.id', '=', 'quotation.id_sales')
            ->where('status', '100')
            ->whereNotNull('quotation.po_file')
            ->whereNull('invoice.no_invoice')
            ->count()
            + Invoice::pendingUnitRequest()->count();
        $today = Carbon::now();
        $thisYear = $today->year;
        $numberLastSP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '11')->where('contract.type', 'Selling')->where('contract.level', '1')->groupBy('contract.id')->orderByDesc('contract.id')->first('contract.no_contract');
        $numberLastSNP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '0')->where('contract.type', 'Selling')->where('contract.level', '1')->groupBy('contract.id')->orderByDesc('contract.id')->first('contract.no_contract');
        $numberLastCP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '11')->where('contract.type', 'Order')->where('contract.level', '1')->groupBy('contract.id')->orderByDesc('contract.id')->first('contract.no_contract');
        $numberLastCNP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '0')->where('contract.type', 'Order')->where('contract.level', '1')->groupBy('contract.id')->orderByDesc('contract.id')->first('contract.no_contract');
        // dd($numberLastSNP);
        $numberSP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '11')->where('contract.type', 'Selling')->where('contract.level', '1')->groupBy('contract.id')->get('contract.id');
        $numberSNP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '0')->where('contract.type', 'Selling')->where('contract.level', '1')->groupBy('contract.id')->get('contract.id');
        $numberCP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '11')->where('contract.type', 'Order')->where('contract.level', '1')->groupBy('contract.id')->get('contract.id');
        $numberCNP = Contract::join('quotation as q', 'contract.id_quotation', '=', 'q.id')->whereYear('contract.date', $today)->where('q.tax', '0')->where('contract.type', 'Order')->where('contract.level', '1')->groupBy('contract.id')->get('contract.id');

        $formattedNumberSP = $this->generateNextContractNumber($numberLastSP, '001');
        $formattedNumberSNP = $this->generateNextContractNumber($numberLastSNP, '001');
        $formattedNumberCP = $this->generateNextContractNumber($numberLastCP, '001');
        $formattedNumberCNP = $this->generateNextContractNumber($numberLastCNP, '001');
        // $formattedNumberSP = str_pad($numberSP->count() + 1, 3, '0', STR_PAD_LEFT);
        // $formattedNumberSNP = str_pad($numberSNP->count() + 1, 3, '0', STR_PAD_LEFT);
        // $formattedNumberCP = str_pad($numberCP->count() + 1, 3, '0', STR_PAD_LEFT);
        // $formattedNumberCNP = str_pad($numberCNP->count() + 1, 3, '0', STR_PAD_LEFT);
        $contract       = Contract::find($id);
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $numberLastSC   = Contract::where('type', 'Selling')->where('level', '1')
            ->whereYear('date', $today)->orderByDesc('id')->first('no_contract');
        $formattedNumberSC = $this->generateNextContractNumber($numberLastSC, '001');

        // Unit quotation contract — separate view
        if ($contract->id_unit_quotation) {
            $unitQuote = UnitQuotation::with(['client', 'pic', 'details.unit'])->find($contract->id_unit_quotation);
            return view('pages.accounting.contract.detail-unit', compact(
                'requestContract', 'requestInvoice', 'noSaleProspect',
                'contract', 'unitQuote', 'thisYear',
                'formattedNumberSP', 'formattedNumberSNP', 'formattedNumberSC'
            ));
        }

        // Service / sparepart contract
        $quote = Quotation::where('id', $contract->id_quotation)->first();
        if ($quote->type != 'Sparepart') {
            $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $quote->id)->get();
        }
        $tax = ($quote->subtotal - $quote->diskon) * $quote->tax / 100;
        $dquote = DetailQuotation::where('id_quotation', $quote->id)->get();
        if ($quote->type == 'Sparepart') {
            return view('pages.accounting.contract.detail', compact('requestContract','requestInvoice','noSaleProspect', 'contract', 'quote', 'dquote', 'tax', 'thisYear', 'formattedNumberSP', 'formattedNumberSNP', 'formattedNumberCP', 'formattedNumberCNP', 'numberLastSP', 'numberLastSNP', 'numberLastCP', 'numberLastCNP'));
        } else {
            return view('pages.accounting.contract.detail', compact('requestContract','requestInvoice','subQuote', 'noSaleProspect', 'contract', 'quote', 'dquote', 'tax', 'thisYear', 'formattedNumberSP', 'formattedNumberSNP', 'formattedNumberCP', 'formattedNumberCNP', 'numberLastSP', 'numberLastSNP', 'numberLastCP', 'numberLastCNP'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contract = Contract::find($id);
        $contractDelete = $contract->delete();
        if ($contractDelete) {
            return 1;
        } else {
            return 0;
        }

    }
    public function create_selling_contract(Request $request, $id)
    {
        $sellcon = new Contract;
        $sellcon->id_quotation = $id;
        $sellcon->no_contract = $request->no_contract;
        $sellcon->level = "1";
        $sellcon->type = "Selling";
        $sellcon->date = Carbon::today();
        $sellconSave = $sellcon->save();
        if ($sellconSave) {
            return redirect('contract/' . $sellcon->id);
        } else {

        }
    }

    public function create_confirm_order(Request $request, $id)
    {
        $sellcon = new Contract;
        $sellcon->id_quotation = $id;
        $sellcon->no_contract = $request->no_contract;
        $sellcon->level = "1";
        $sellcon->type = "Order";
        $sellcon->date = Carbon::today();
        $sellconSave = $sellcon->save();
        if ($sellconSave) {
            return redirect('contract/' . $sellcon->id);
        } else {

        }
    }
    public function index_selling()
    {
        return redirect()->route('contract.index');
    }

    public function index_order()
    {
        return redirect()->route('contract.index');
    }
    public function contract_print($id)
    {
        $sellcon = Contract::find($id);

        if ($sellcon->id_unit_quotation) {
            $unitQuote = UnitQuotation::with(['client', 'pic', 'details.unit'])->find($sellcon->id_unit_quotation);
            return view('pages.accounting.contract.detail-print-unit', compact('sellcon', 'unitQuote'));
        }

        $quote = Quotation::where('id', $sellcon->id_quotation)->first();
        if ($quote->type != 'Sparepart') {
            $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $quote->id)->get();
        }
        $tax    = ($quote->subtotal - $quote->diskon) * $quote->tax / 100;
        $dquote = DetailQuotation::where('id_quotation', $quote->id)->get();
        if ($quote->type == 'Sparepart') {
            return view('pages.accounting.contract.detail-print', compact('sellcon', 'quote', 'dquote', 'tax'));
        } else {
            return view('pages.accounting.contract.detail-print', compact('subQuote', 'sellcon', 'quote', 'dquote', 'tax'));
        }
    }
    public function request_selling_contract($id)
    {
        $quote = Quotation::find($id);
        $sellcon = new Contract;
        $sellcon->id_quotation = $id;
        $sellcon->no_contract = $quote->no_quote;
        $sellcon->level = "0";
        $sellcon->type = "Selling";
        $sellcon->date = Carbon::today();
        $sellconSave = $sellcon->save();
        if ($sellconSave) {
            return 1;
        } else {
            return 0;
        }
    }

    public function request_selling_contract_unit($id)
    {
        $quote   = UnitQuotation::findOrFail($id);
        $sellcon = Contract::create([
            'id_unit_quotation' => $id,
            'no_contract'       => $quote->no_quote,
            'level'             => '0',
            'type'              => 'Selling',
            'date'              => Carbon::today(),
        ]);
        return $sellcon ? 1 : 0;
    }

    public function create_selling_contract_unit(Request $request, $id)
    {
        $quote   = UnitQuotation::findOrFail($id);
        $sellcon = Contract::create([
            'id_unit_quotation' => $id,
            'no_contract'       => $request->no_contract,
            'level'             => '1',
            'type'              => 'Selling',
            'date'              => Carbon::today(),
        ]);
        return redirect()->route('contract.show', $sellcon->id)
            ->with('success', 'Selling Contract berhasil dibuat.');
    }

    public function request_confirm_order($id)
    {
        $quote = Quotation::find($id);
        $sellcon = new Contract;
        $sellcon->id_quotation = $id;
        $sellcon->no_contract = $quote->no_quote;
        $sellcon->level = "0";
        $sellcon->type = "Order";
        $sellcon->date = Carbon::today();
        $sellconSave = $sellcon->save();
        if ($sellconSave) {
            return 1;
        } else {
            return 0;
        }
    }

    public function accept_contract(Request $request, $id)
    {
        // Menemukan kontrak berdasarkan ID
        $contract = Contract::find($id);

        // Memeriksa apakah kontrak ditemukan
        if (!$contract) {
            return redirect()->back()->with('error', 'Contract not found');
        }

        // Memperbarui kontrak
        $contract->no_contract = $request->no_contract;
        $contract->level = '1';
        $contractSave = $contract->save();

        // Memeriksa apakah penyimpanan berhasil
        if ($contractSave) {
            return redirect('/contract/' . $id)->with('message', 'Contract Was Accepted');
        } else {
            return redirect()->back()->with('error', 'Failed to accept contract');
        }
    }

    private function generateNextContractNumber($lastContract, $defaultCode)
    {
        if ($lastContract) {
            // Ekstrak 3 digit numerik pertama dari no_Contract
            preg_match('/^\d{3}/', $lastContract->no_contract, $matches);

            if (!empty($matches)) {
                $lastNumber = $matches[0]; // Bagian numerik yang diekstrak, misal "004"
                $newNumber = str_pad((int) $lastNumber + 1, 3, '0', STR_PAD_LEFT); // Increment dan pad angka

                return $newNumber;
            } else {
                // Jika tidak ada bagian numerik yang ditemukan, gunakan default
                return $defaultCode;
            }
        } else {
            // Jika tidak ada invoice sebelumnya, mulai dari awal
            return $defaultCode;
        }
    }
}
