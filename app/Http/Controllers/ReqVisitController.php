<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Machine;
use App\Models\Pic;
use App\Models\Reports;
use App\Models\ReportsPict;
use App\Models\ReqVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as Image;

class ReqVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $rule = [
            'machine' =>
                'required',

            'date' =>
                'required',

            'note' =>
                'required',
        ];

        $message = [
            'machine.required' => 'Field Machine Wajib Diisi',
            'date.required' => 'Field Date Wajib Diisi',
            'note.required' => 'Field Note Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        $visit = new ReqVisit();
        $visit->id_machine = $request->machine;
        $visit->id_service = null;
        $visit->date = null;
        $visit->visit_date = null;
        $visit->req_date = $request->date;
        $visit->note = $request->note;
        $visit->visit_note = null;
        $visit->desc = null;
        $visit->status = "Waiting";
        $visitSave = $visit->save();
        $machine = Machine::find($request->machine);
        if ($visitSave) {
            return redirect('/existing/' . $machine->id_client)->with('message', 'data telah dibuat');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $clients = Client::all();
        $visit = ReqVisit::find($id);
        $dateNow = Carbon::now();
        $numberS = Reports::whereYear('date', $dateNow)->where('id_technician', Auth::user()->id)->count();
        $formattedNumberS = str_pad($numberS + 1, 3, '0', STR_PAD_LEFT);
        $monthNow = $dateNow->month;
        $formattedMonthNow = $this->convertToRoman($monthNow);
        $pic = Pic::where('id_client', $visit->machine->id_client)->get();
        // dd($visit);
        return view('pages.coordinator.visit.form', compact('visit', 'pic', 'formattedNumberS', 'formattedMonthNow', 'clients'));
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
        $rule = [
            'date' =>
                'required',

            'desc' =>
                'required',
        ];

        $message = [
            'date.required' => 'Field Date Wajib Diisi',
            'desc.required' => 'Field desc Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        $visit = ReqVisit::find($id);
        $visit->date = $request->date;
        $visit->desc = $request->desc;
        if ($visit->date == $visit->req_date) {
            $visit->status = "On Process";
        } else {
            $visit->status = "Pending";
        }
        $visitSave = $visit->save();
        if ($visitSave) {
            return redirect('/')->with('message', 'data telah dibuat');
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

    public function reportsWithRequest(Request $request, $id)
    {
        $rule = [
            'no_service => required',
            'running => required',
            'load => required',
            'jobdesc => required',
            'desc => required',
        ];
        $customMessages = [
            'no_service.required' => 'Field No Service Wajib Diisi!',
            'running.required' => 'Field Running Wajib Diisi!',
            'load.required' => 'Field Load Wajib Diisi!',
            'jobdesc.required' => 'Field Jobdesc Wajib Diisi!',
            'desc.required' => 'Field desc Wajib Diisi!',
        ];

        $this->validate($request, $rule, $customMessages);
        // dd($request);
        $visit = ReqVisit::find($id);
        // Masukan Data ke Service Reports
        $reports = new Reports();
        $reports->id_technician = $request->technician;
        $reports->id_pic = $request->id_pic;
        $reports->id_machine = $visit->id_machine;
        $reports->no_service = $request->no_service;
        $reports->type = $request->type;
        $reports->running = $request->running;
        $reports->load = $request->load;
        $reports->jobdesc = $request->jobdesc;
        $reports->desc = $request->desc;
        $reports->recomendation = $request->recomendation;
        $reports->date = $request->date;
        $reports->sign_client = NULL;
        $status = $reports->save();
        // dd($reports);
        $visit->id_service = $reports->id;
        $visit->status = 'Finish';
        $visit->save();

        // masukan data ke image reports
        foreach ($request->description as $item => $value) {
            $photo = new ReportsPict();
            $photo->id_reports = $reports->id;
            $photo->keterangan = $value; // Gunakan $value, bukan $request->description

            if ($request->hasFile('image')) {
                $foto = $request->file('image')[$item]; // Akses file sesuai dengan iterasi saat ini
                // Proses setiap file gambar
                $image_ext = $foto->getClientOriginalExtension();
                $image_name = Str::random(8);

                $upload_path = 'asset/reports';
                $imagename = $upload_path . '/' . $image_name . '.' . $image_ext;

                // Pemrosesan gambar
                $img = Image::make($foto->path());
                $img->fit(500, 800, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save($imagename);

                $photo['picture'] = $imagename;
            }

            $status = $photo->save(); // Simpan objek setelah pemrosesan setiap file
        }
        if ($status) {
            return redirect('service-reports/' . $reports->id)->with('success', 'Data Has been created');
        }
    }
    public function visited(Request $request, $id)
    {
        $rule = [
            'date' =>
                'required',

            'note' =>
                'required',
        ];

        $message = [
            'date.required' => 'Field Date Wajib Diisi',
            'note.required' => 'Field note Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        $visit = ReqVisit::find($id);
        $visit->visit_date = $request->date;
        $visit->visit_note = $request->note;
        $visit->status = "Finish";
        $visitSave = $visit->save();
        if ($visitSave) {
            return redirect('/')->with('message', 'data telah dibuat');
        }
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
