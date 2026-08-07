<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\DetailAudit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.technician.audit-tools.index');
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
        $audit = Audit::where('id', $id)->first();
        $tools = DetailAudit::where('id_audit', $id)->get();
        // dd($audit);
        return view('pages.technician.audit-tools.detail', compact('audit', 'tools'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $formattedMonthNow = $this->convertToRoman($monthNow);
        $audit = Audit::where('id', $id)->first();
        $tools = DetailAudit::where('id_audit', $id)->get();
        return view('pages.technician.audit-tools.form', compact('formattedMonthNow', 'audit', 'tools'));
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
        $audit = Audit::where('id', $id)->first();
        $rule = [
            'no_audit' => 'required',
            'date' => 'required',
            'noteD' => 'required',
        ];
        $message = [
            'no_audit.required' => 'Field No audit Wajib Diisi',
            'date.required' => 'Wajib isi Date',
            'noteD.required' => 'Field Detail Note Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);

        $auditS = new Audit();
        $auditS->id_technician = $audit->id_technician;
        $auditS->no_audit = $request->no_audit;
        $auditS->status = $request->status;
        $auditS->note = $request->noteD;
        $auditS->date = $request->date;
        $status = $audit->save();

        foreach ($request->tools as $item => $value) {
            $Daudit = new Audit();
            $Daudit->id_detail_audit = $auditS->id;
            $Daudit->tools = $request->tools[$item];
            $Daudit->qty = $request->qty[$item];
            $Daudit->desc = $request->desc[$item];
            $Daudit->assesment = $request->assesment[$item];
            $Daudit->note = $request->note[$item];
            $status = $Daudit->save();
        }

        if($status){
            return redirect('audit-tools')->with("success", "Data Tools Telah Ditambahkan");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function print_audit($id)
    {
        $audit = Audit::where('id', $id)->first();
        $tools = DetailAudit::where('id_audit', $id)->get();
        // dd($termncon);
        return view("pages.technician.audit-tools.detail-print", compact('audit', 'tools'));
    }
    protected function convertToRoman($month)
    {
        $romanMonth = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $romanMonth[$month];
    }
}
