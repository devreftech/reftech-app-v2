<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\DetailQuotation;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Termncon;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function archive_quotation()
    {
        return view('pages.sales.quotation.archive.index');
    }

    public function unarchive_quotation($id)
    {
        $quotation = Quotation::find($id);
        $quotes = Quotation::where('primary_id', $quotation->primary_id)->get();

        foreach ($quotes as $quote) {
            $quote->is_primary = '0';
            $quote->save();
        }

        $quotation->is_primary = '1';
        $quotation->level = '1';
        $delQuote = $quotation->save();

        if ($delQuote) {
            return 1;
        } else {
            return 0;
        }
    }
    
    public function delete_archive_quotation($id){
        $quotation = Quotation::find($id);
        $detailQuote = DetailQuotation::where('id_quotation', $id)->get();
        $contract = Contract::where('id_quotation', $id)->get();
        $termncon = Termncon::where('id_quotation', $id)->get();
        $invoice = Invoice::where('id_quotation', $id)->get();

        $delQuote = $quotation->delete();

        foreach ($detailQuote as $dQuote) {
            $delDetQuote = $dQuote->delete();
        }
        foreach ($contract as $contracts) {
            $delContract = $contracts->delete();
        }
        foreach ($termncon as $termncons) {
            $deltermncon = $termncons->delete();
        }
        foreach ($invoice as $invoices) {
            $delinvoice = $invoices->delete();
        }

        if ($delQuote || $delDetQuote || $delContract || $deltermncon || $delinvoice) {
            return 1;
        } else {
            return 0;
        }
    }
}
