<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Machine;
use App\Models\Monitoring;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use File;
use Str;
use Intervention\Image\Facades\Image as Image;

class MachineController extends Controller
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
            'desc' =>
                'required',

            'serial' =>
                'required',
        ];

        $message = [
            'desc.required' => 'Field desc Wajib Diisi',
            'serial.required' => 'Field serial Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        // dd($request->all());
        $machine = new Machine;
        $machine->id_client = $request->id_client;
        $machine->id_unit = $request->unit;
        $machine->serial = $request->serial;
        $machine->desc = $request->desc;
        $machine->tag = $request->tag;
        $machine->location = $request->location;
        $machineSave = $machine->save();
        // dd($machine);
        if ($machineSave) {
            return redirect()->back()->with('message', 'data telah ditambahkan');
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
        $rule = [
            'desc' =>
                'required',

            'serial' =>
                'required',

            'unit' =>
                'required',
        ];

        $message = [
            'desc.required' => 'Field desc Wajib Diisi',
            'serial.required' => 'Field serial Wajib Diisi',
            'unit.required' => 'Field unit Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        $machine = Machine::find($id);
        $machine->id_client = $request->id_client;
        $machine->id_unit = $request->unit;
        $machine->serial = $request->serial;
        $machine->desc = $request->desc;
        $machine->tag = $request->tag;
        $machine->location = $request->location;
        $machineSave = $machine->save();
        if ($machineSave) {
            return redirect('/existing/' . $request->id_client)->with('message', 'data telah ditambahkan');
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
        $machine = Machine::find($id);

        $delMachine = $machine->delete();
        if ($delMachine) {
            return 1;
        } else {
            return 0;
        }
    }
    public function storeTechnician(Request $request)
    {
        $rule = [
            'desc' =>
                'required',

            'serial' =>
                'required',

            'unit' =>
                'required',
        ];

        $message = [
            'desc.required' => 'Field desc Wajib Diisi',
            'serial.required' => 'Field serial Wajib Diisi',
            'unit.required' => 'Field unit Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        $machine = new Machine;
        $machine->id_client = $request->id_client;
        $machine->id_unit = $request->unit;
        $machine->serial = $request->serial;
        $machine->desc = $request->desc;
        $machine->tag = $request->tag;
        $machine->location = $request->location;
        $machineSave = $machine->save();
        if ($machineSave) {
            return redirect('/service-reports/create')->with('message', 'data telah ditambahkan');
        }
    }

    public function indexMonitoring($id)
    {
        $machine = Machine::find($id);
        return view('pages.monitoring.index', compact('machine'));
    }

    public function createMonitoring($id)
    {
        $machine = Machine::find($id);
        return view('pages.monitoring.form', compact('machine'));
    }
    public function storeMonitoring(Request $request, $id)
    {
        // dd($request->all());
        $machine = Machine::find($id);
        $monitoring = new Monitoring();
        $monitoring->id_machine = $id;
        $monitoring->id_pic = Auth::user()->id;
        $monitoring->runing = $request->running . ' Hour';
        $monitoring->load = $request->load . ' Hour';
        $monitoring->pressure = $request->pressure . ' Bar';
        $monitoring->temp = $request->temperature . " °C";
        $monitoring->condition = $request->condition;
        $monitoring->oil_level = $request->oil;
        $monitoring->desc = $request->desc;
        $monitoring->date = \Carbon\Carbon::today();
        if ($request->hasFile('picture')) {
            // $foto = $request->file('picture'); // Akses file sesuai dengan iterasi saat ini
            // // Proses setiap file gambar
            // $picture_ext = $foto->getClientOriginalExtension();
            // $picture_name = Str::random(8);

            // $machine_brand = $machine->brand;
            // $machine_type = $machine->type;
            // $upload_dir = public_path('asset/machines/' . $machine_brand . '-' . $machine_type);

            // if (!file_exists($upload_dir)) {
            //     mkdir($upload_dir, 0777, true);
            // }

            // $picturename = $upload_dir . '/' . $picture_name . '.' . $picture_ext;

            // // Pemrosesan gambar
            // $img = Image::make($foto->path());
            // $img->fit(500, 800, function ($constraint) {
            //     $constraint->aspectRatio();
            // });
            // $img->save($picturename);

            // $monitoring['picture'] = $picturename;

            $foto = $request->file('picture');
            $foto_ext = $foto->getClientOriginalExtension();
            $foto_name = Str::random(8);

            $machine_brand = $machine->brand;
            $machine_type = $machine->type;
            $upload_dir = public_path('asset/machines/' . $machine_brand . '-' . $machine_type);
            $upload_path = 'asset/machines';
            $imagename = $upload_path . '/' . $machine_brand . '-' . $machine_type . '/' . $foto_name . '.' . $foto_ext;

            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $img = Image::make($foto->path());
            $img->fit(500, 500, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($imagename);
            $monitoring['picture'] = $imagename;
        }
        $monitorSave = $monitoring->save();
        if ($monitorSave) {
            return redirect('machine/monitoring/' . $id)->with('message', 'Data telah diibuat');
        }
    }

    public function visitorMonitoring($id)
    {
        $machine = Machine::find($id);
        $client = Client::find($machine->id_client);

        // mengambil dara monitoring bulan ini
        $today = Carbon::today();

        $startOfMonth = $today->copy()->startOfMonth();
        $startOfMonthDate = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $dates = [];
        for ($date = $startOfMonthDate; $date->lte($endOfMonth); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }
        // dd($dates);

        // Ambil data monitoring dari database
        $monitoringData = Monitoring::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('id_machine', $id)
            ->get(); // Format: ['2024-11-01' => '100']

        $monitoringIndexed = $monitoringData->keyBy('date')->map(function ($item) {
            return [
                'runing' => $item->runing ?? '-',
                'load' => $item->load ?? '-',
                'pressure' => $item->pressure ?? '-',
                'temp' => $item->temp ?? '-',
                'condition' => $item->condition ?? '-',
                'oil_level' => $item->oil_level ?? '-',
                'desc' => $item->desc ?? '-',
            ];
        })->toArray();

        // Gabungkan data monitoring dengan daftar tanggal
        $result = [];
        foreach ($dates as $date) {
            $result[] = [
                'date' => $date,
                'running' => $monitoringIndexed[$date]['runing'] ?? '-',
                'load' => $monitoringIndexed[$date]['load'] ?? '-',
                'pressure' => $monitoringIndexed[$date]['pressure'] ?? '-',
                'temp' => $monitoringIndexed[$date]['temp'] ?? '-',
                'condition' => $monitoringIndexed[$date]['condition'] ?? '-',
                'oil_level' => $monitoringIndexed[$date]['oil_level'] ?? '-',
                'desc' => $monitoringIndexed[$date]['desc'] ?? '-',
            ];
        }

        // Return data
        $monitoringThisMonth = response()->json($result);
        // dd($result);   
        return view('pages.monitoring.visitor', compact('machine', 'client', 'result'));
    }

    public function getMachineReftech()
    {
        $unit = Machine::join('serial_product as s', 's.id', '=', 'machine.id_unit')->join('unit as u', 'u.id', '=', 's.id_product')->get();
        // Return data
        return response()->json(['data' => $unit]);
    }
}
