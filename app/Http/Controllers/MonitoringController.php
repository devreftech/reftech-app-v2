<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Machine;
use App\Models\Mainlog;
use App\Models\Monitoring;
use App\Models\MonitoringActivities;
use App\Models\MonitoringMonthly;
use App\Models\MonitoringWeekly;
use App\Models\Pic;
use App\Models\Quotation;
use App\Models\Reports;
use App\Models\StatusMonitoring;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Str;

class MonitoringController extends Controller
{
    // button
    public function button($id)
    {
        $machine = Machine::find($id);
        $hasMonitoringToday = Monitoring::where('id_machine', $id)
            ->whereDate('created_at', Carbon::today())
            ->exists();
        return view('pages.monitoring.button', compact('machine', 'hasMonitoringToday'));
    }

    // monitoring daily
    public function indexDaily($id)
    {
        $machine = Machine::find($id);
        // dd($machine->unit);
        return view('pages.monitoring.index', compact('machine'));
    }
    public function createDaily($id)
    {
        $machine = Machine::find($id);
        $monitoring = Monitoring::where('id_machine', $id)->whereDate('created_at', Carbon::today())->first();
        // dd($monitoring);
        return view('pages.monitoring.form', compact('machine', 'monitoring'));
    }
    public function createDailyReports($monitoring, $id)
    {
        $machine = Machine::find($id);
        $monitoring = Monitoring::where('id_machine', $id)->orderByDesc('date')->first();

        $runningRaw = str_replace('.', '', $monitoring->running);
        $loadingRaw = str_replace('.', '', $monitoring->loading);

        // Ambil angka menggunakan regex
        preg_match('/\d+/', $runningRaw, $runningMatches);
        $runningNumericValue = $runningMatches[0] ?? ''; // Hasil: 1869

        preg_match('/\d+/', $loadingRaw, $loadingMatches);
        $loadingNumericValue = $loadingMatches[0] ?? ''; // Hasil: 6244
        $clients = Client::all();
        $dateNow = Carbon::now();
        $numberS = Reports::whereYear('date', $dateNow)->where('id_technician', Auth::user()->id)->count();
        $formattedNumberS = str_pad($numberS + 1, 3, '0', STR_PAD_LEFT);
        // dd($formattedNumberS);
        $monthNow = $dateNow->month;
        $formattedMonthNow = $this->convertToRoman($monthNow);
        $pic = Pic::join('client as c', 'c.id', '=', 'pic.id_client')->where('c.role', '=', 'Customers')->where('c.id', '=', '1277')->select('pic.*')->get();
        return view('pages.monitoring.form-service-reports', compact('machine', 'monitoring', 'pic', 'formattedNumberS', 'formattedMonthNow', 'runningNumericValue', 'loadingNumericValue'));
    }
    public function storeDaily(Request $request, $id)
    {
        if ($request->condition != "Off") {
            $rule = [
                'leak' => 'required',
            ];
            $message = [
                'leak.required' => 'Cek Kebocoran Wajib Dipilih!',
            ];
            $this->validate($request, $rule, $message);
        }
        if ($id == 495 || $id == 496) {
            $maksimal = 100;
        } else {
            $maksimal = 94;
        }

        $dew = floatval(str_replace(',', '.', $request->dew));
        // dd($request->all());
        $machine = Machine::find($id);
        // dd($machine->unit->unit->unit);
        $monitoring = new Monitoring();
        $monitoring->id_machine = $id;
        $monitoring->id_pic = Auth::user()->id;
        $monitoring->condition = $request->condition;
        if ($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER') {
            if ($request->condition == 'Running') {
                $monitoring->leak = $request->leak;
                $monitoring->running = number_format($request->running, 0, ',', '.') . ' Hour';
                $monitoring->loading = number_format($request->loading, 0, ',', '.') . ' Hour';
                if (is_numeric($request->pressure) && strpos($request->pressure, ',') === false) {
                    $monitoring->pressure = $request->pressure . ',0' . ' Bar';
                } else {
                    $monitoring->pressure = $request->pressure . ' Bar';
                }
                $monitoring->oil_level = $request->oil;
                $monitoring->temp = $request->temperature . " °C";
                if ($request->issue == null) {
                    if ($request->temperature <= $maksimal) {
                        $monitoring->issue = null;
                    } else {
                        $monitoring->issue = 'High Temperature : ' . $request->temperature . " °C";
                    }
                } else {
                    if ($request->issue != null) {
                        if ($request->temperature <= $maksimal) {
                            $monitoring->issue = $request->issue;
                        } else {
                            $monitoring->issue = $request->issue . ', High Temperature : ' . $request->temperature . " °C";
                        }
                    }
                }
            } elseif ($request->condition == 'Stand By') {
                $monitoring->leak = $request->leak;
                $monitoring->running = number_format($request->running, 0, ',', '.') . ' Hour';
                $monitoring->loading = number_format($request->loading, 0, ',', '.') . ' Hour';
                if (is_numeric($request->pressure) && strpos($request->pressure, ',') === false) {
                    $monitoring->pressure = $request->pressure . ',0' . ' Bar';
                } else {
                    $monitoring->pressure = $request->pressure . ' Bar';
                }
                $monitoring->oil_level = $request->oil;
                $monitoring->temp = $request->temperature . " °C";
                if ($request->issue == null) {
                    if ($request->temperature <= $maksimal) {
                        $monitoring->issue = 'Stand By';
                    } else {
                        $monitoring->issue = 'Stand By: High Temperature : ' . $request->temperature . " °C";
                    }
                } else {
                    if ($request->issue != null) {
                        if ($request->temperature <= $maksimal) {
                            $monitoring->issue = 'Stand By : ' . $request->issue;
                        } else {
                            $monitoring->issue = 'Stand By : ' . $request->issue . ', High Temperature : ' . $request->temperature . " °C";
                        }
                    }
                }
            } elseif ($request->condition == 'Off') {
                $monitoring->leak = 'Tidak Ada';
                $monitoring->running = '-';
                $monitoring->loading = '-';
                if (is_numeric($request->pressure) && strpos($request->pressure, ',') === false) {
                    $monitoring->pressure = '-';
                } else {
                    $monitoring->pressure = '-';
                }
                $monitoring->oil_level = '-';
                $monitoring->temp = "-";
                if ($request->issue == null) {
                    if ($request->temperature <= $maksimal) {
                        $monitoring->issue = 'Off';
                    } else {
                        $monitoring->issue = 'Off: High Temperature : ' . $request->temperature . " °C";
                    }
                } else {
                    if ($request->temperature <= $maksimal) {
                        $monitoring->issue = 'Off : ' . $request->issue;
                    } else {
                        $monitoring->issue = 'Off : ' . $request->issue . ', High Temperature : ' . $request->temperature . " °C";
                    }
                }
            }
        } else {
            if ($request->condition == 'Running') {
                $monitoring->dew = $request->dew;
                $monitoring->fan = $request->fan;
                $monitoring->drain = $request->drain;
                $monitoring->leak = $request->leak;
                $monitoring->temp = $request->temperature_in . " °C";
                $monitoring->temp_out = $request->temperature_out . " °C";
                if ($request->issue == null) {
                    if ($dew <= 10) {
                        $monitoring->issue = null;
                    } else {
                        $monitoring->issue = 'High dew';
                    }
                } else {
                    if ($request->issue != null) {
                        if ($dew <= 10) {
                            $monitoring->issue = $request->issue;
                        } else {
                            $monitoring->issue = $request->issue . ', High dew';
                        }
                    }
                }
            } elseif ($request->condition == 'Stand By') {
                $monitoring->dew = $request->dew;
                $monitoring->fan = $request->fan;
                $monitoring->drain = $request->drain;
                $monitoring->leak = $request->leak;
                $monitoring->temp = $request->temperature_in . " °C";
                $monitoring->temp_out = $request->temperature_out . " °C";
                if ($request->issue == null) {
                    if ($dew <= 10) {
                        $monitoring->issue = 'Stand By';
                    } else {
                        $monitoring->issue = 'Stand By: High dew';
                    }
                } else {
                    if ($dew <= 10) {
                        $monitoring->issue = 'Stand By : ' . $request->issue;
                    } else {
                        $monitoring->issue = 'Stand By : ' . $request->issue . ', High dew';
                    }
                }
            } elseif ($request->condition == 'Off') {
                $monitoring->dew = '-';
                $monitoring->fan = '-';
                $monitoring->drain = '-';
                $monitoring->leak = 'Tidak Ada';
                $monitoring->temp = "-";
                $monitoring->temp_out = "-";
            }
        }
        $monitoring->recommendation = '-';
        if (Auth::user()->code == 'RMD') {
            $monitoring->date = $request->date;
        } else {
            $monitoring->date = Carbon::today();
        }
        if ($request->hasFile('picture')) {
            $foto = $request->file('picture');
            $foto_ext = $foto->getClientOriginalExtension();
            $foto_name = Str::random(8);

            $machine_brand = $machine->unit->brand;
            $machine_type = $machine->unit->type;

            // Direktori upload
            $upload_dir = public_path('asset/machines/' . $machine_brand . '-' . $machine_type);
            $upload_path = 'asset/machines';
            $imagename = $upload_path . '/' . $machine_brand . '-' . $machine_type . '/' . $foto_name . '.' . $foto_ext;

            // Cek apakah direktori sudah ada
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Proses manipulasi dan simpan gambar
            $img = Image::make($foto->path());
            $img->fit(500, 500, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($upload_dir . '/' . $foto_name . '.' . $foto_ext);

            // Simpan path ke database atau variabel monitoring
            $monitoring['picture'] = $imagename;

        }
        $monitoring->created_at = Carbon::now()->addHour(7);
        $monitorSave = $monitoring->save();
        if ($monitorSave) {
            $statMonitoring = new StatusMonitoring();
            $statMonitoring->id_monitoring = $monitoring->id;
            $statMonitoring->id_pic = Auth::user()->id;
            $statMonitoring->status = '0';
            $statMonitoring->desc = 'Monitoring Issues Created';
            $statMonitoring->date = Carbon::today();
            $statMonitoringSave = $statMonitoring->save();
        }
        $month = Carbon::now()->month;
        // dd($monitoring);
        if ($monitorSave) {
            if (auth::user()->level == 1) {
                return redirect('/monitoring/daily/' . $id)->with('message', 'Data telah di buat');
            } else {
                return redirect('/service-manager-daily/' . $id . '/' . $month)->with('message', 'Data telah di buat');
            }

        } else {

        }
    }
    public function updateDaily(Request $request, $id)
    {
        if ($request->condition != "Off") {
            $rule = [
                'leak' => 'required',
            ];
            $message = [
                'leak.required' => 'Cek Kebocoran Wajib Dipilih!',
            ];
            $this->validate($request, $rule, $message);
        }
        $dew = floatval(str_replace(',', '.', $request->dew));
        // dd($request->all());
        $machine = Machine::find($id);
        $monitoring = Monitoring::where('id_machine', $id)->whereDate('created_at', Carbon::today())->first();
        // dd($machine->unit->unit->unit);$machine->monitoring()->whereDate('created_at', Carbon\Carbon::today())->exists()
        // $monitoring = new Monitoring();
        if ($id == 495 || $id == 496) {
            $maksimal = 100;
        } else {
            $maksimal = 94;
        }
        $monitoring->id_machine = $id;
        $monitoring->id_pic = Auth::user()->id;
        $monitoring->condition = $request->condition;
        if ($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER') {
            if ($request->condition == 'Running') {
                $monitoring->leak = $request->leak;
                $monitoring->running = number_format($request->running, 0, ',', '.') . ' Hour';
                $monitoring->loading = number_format($request->loading, 0, ',', '.') . ' Hour';
                if (is_numeric($request->pressure) && strpos($request->pressure, ',') === false) {
                    $monitoring->pressure = $request->pressure . ',0' . ' Bar';
                } else {
                    $monitoring->pressure = $request->pressure . ' Bar';
                }
                $monitoring->oil_level = $request->oil;
                $monitoring->temp = $request->temperature . " °C";
                if ($request->issue == null) {
                    if ($request->temperature <= $maksimal) {
                        $monitoring->issue = null;
                    } else {
                        $monitoring->issue = 'High Temperature : ' . $request->temperature . " °C";
                    }
                } else {
                    if ($request->issue != null) {
                        if ($request->temperature <= $maksimal) {
                            $monitoring->issue = $request->issue;
                        } else {
                            $monitoring->issue = $request->issue . ', High Temperature : ' . $request->temperature . " °C";
                        }
                    }
                }
            } elseif ($request->condition == 'Stand By') {
                $monitoring->leak = $request->leak;
                $monitoring->running = number_format($request->running, 0, ',', '.') . ' Hour';
                $monitoring->loading = number_format($request->loading, 0, ',', '.') . ' Hour';
                if (is_numeric($request->pressure) && strpos($request->pressure, ',') === false) {
                    $monitoring->pressure = $request->pressure . ',0' . ' Bar';
                } else {
                    $monitoring->pressure = $request->pressure . ' Bar';
                }
                $monitoring->oil_level = $request->oil;
                $monitoring->temp = $request->temperature . " °C";
                if ($request->issue == null) {
                    if ($request->temperature <= $maksimal) {
                        $monitoring->issue = 'Stand By';
                    } else {
                        $monitoring->issue = 'Stand By: High Temperature : ' . $request->temperature . " °C";
                    }
                } else {
                    if ($request->issue != null) {
                        if ($request->temperature <= $maksimal) {
                            $monitoring->issue = 'Stand By : ' . $request->issue;
                        } else {
                            $monitoring->issue = 'Stand By : ' . $request->issue . ', High Temperature : ' . $request->temperature . " °C";
                        }
                    }
                }
            } elseif ($request->condition == 'Off') {
                $monitoring->leak = 'Tidak Ada';
                $monitoring->running = '-';
                $monitoring->loading = '-';
                if (is_numeric($request->pressure) && strpos($request->pressure, ',') === false) {
                    $monitoring->pressure = '-';
                } else {
                    $monitoring->pressure = '-';
                }
                $monitoring->oil_level = '-';
                $monitoring->temp = "-";
                if ($request->issue == null) {
                    if ($request->temperature <= $maksimal) {
                        $monitoring->issue = 'Off';
                    } else {
                        $monitoring->issue = 'Off: High Temperature : ' . $request->temperature . " °C";
                    }
                } else {
                    if ($request->temperature <= $maksimal) {
                        $monitoring->issue = 'Off : ' . $request->issue;
                    } else {
                        $monitoring->issue = 'Off : ' . $request->issue . ', High Temperature : ' . $request->temperature . " °C";
                    }
                }
            }
        } else {
            if ($request->condition == 'Running') {
                $monitoring->dew = $request->dew;
                $monitoring->fan = $request->fan;
                $monitoring->drain = $request->drain;
                $monitoring->leak = $request->leak;
                $monitoring->temp = $request->temperature_in . " °C";
                $monitoring->temp_out = $request->temperature_out . " °C";
                if ($request->issue == null) {
                    if ($dew <= 10) {
                        $monitoring->issue = null;
                    } else {
                        $monitoring->issue = 'High dew';
                    }
                } else {
                    if ($request->issue != null) {
                        if ($dew <= 10) {
                            $monitoring->issue = $request->issue;
                        } else {
                            $monitoring->issue = $request->issue . ', High dew';
                        }
                    }
                }
            } elseif ($request->condition == 'Stand By') {
                $monitoring->dew = $request->dew;
                $monitoring->fan = $request->fan;
                $monitoring->drain = $request->drain;
                $monitoring->leak = $request->leak;
                $monitoring->temp = $request->temperature_in . " °C";
                $monitoring->temp_out = $request->temperature_out . " °C";
                if ($request->issue == null) {
                    if ($dew <= 10) {
                        $monitoring->issue = 'Stand By';
                    } else {
                        $monitoring->issue = 'Stand By: High dew';
                    }
                } else {
                    if ($dew <= 10) {
                        $monitoring->issue = 'Stand By : ' . $request->issue;
                    } else {
                        $monitoring->issue = 'Stand By : ' . $request->issue . ', High dew';
                    }
                }
            } elseif ($request->condition == 'Off') {
                $monitoring->dew = '-';
                $monitoring->fan = '-';
                $monitoring->drain = '-';
                $monitoring->leak = 'Tidak Ada';
                $monitoring->temp = "-";
                $monitoring->temp_out = "-";
            }
        }
        $monitoring->recommendation = '-';
        if (Auth::user()->code == 'RMD') {
            $monitoring->date = $request->date;
        } else {
            $monitoring->date = Carbon::today();
        }
        if ($request->hasFile('picture')) {
            $foto = $request->file('picture');
            $foto_ext = $foto->getClientOriginalExtension();
            $foto_name = Str::random(8);

            $machine_brand = $machine->unit->brand;
            $machine_type = $machine->unit->type;

            // Direktori upload
            $upload_dir = public_path('asset/machines/' . $machine_brand . '-' . $machine_type);
            $upload_path = 'asset/machines';
            $imagename = $upload_path . '/' . $machine_brand . '-' . $machine_type . '/' . $foto_name . '.' . $foto_ext;

            // Cek apakah direktori sudah ada
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Proses manipulasi dan simpan gambar
            $img = Image::make($foto->path());
            $img->fit(500, 500, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($upload_dir . '/' . $foto_name . '.' . $foto_ext);

            // Simpan path ke database atau variabel monitoring
            $monitoring['picture'] = $imagename;

        }
        $monitoring->created_at = Carbon::now()->addHour(7);
        $monitorSave = $monitoring->save();
        if ($monitorSave) {
            $statMonitoring = new StatusMonitoring();
            $statMonitoring->id_monitoring = $monitoring->id;
            $statMonitoring->id_pic = Auth::user()->id;
            $statMonitoring->status = '0';
            $statMonitoring->desc = 'Monitoring Issues Created';
            $statMonitoring->date = Carbon::today();
            $statMonitoringSave = $statMonitoring->save();
        }
        $month = Carbon::now()->month;
        // dd($monitoring);
        if ($monitorSave) {
            if (auth::user()->level == 1) {
                return redirect('/monitoring/daily/' . $id)->with('message', 'Data telah di buat');
            } else {
                return redirect('/service-manager-daily/' . $id . '/' . $month)->with('message', 'Data telah di buat');
            }

        } else {

        }
    }
    public function storeService(Request $request, $monitoring, $id)
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
        // Masukan Data ke Service Reports
        $reports = new Reports();
        $reports->id_technician = Auth::user()->id;
        $reports->id_pic = $request->id_pic;
        $reports->id_machine = $id;
        $reports->id_monitoring = $monitoring;
        $reports->no_service = $request->no_service;
        $reports->type = $request->type;
        $reports->running = $request->running;
        $reports->load = $request->load;
        $reports->date = $request->date;
        $reports->jobdesc = $request->jobdesc;
        $reports->desc = $request->desc;
        $reports->recomendation = $request->recomendation;
        $reports->sign_client = NULL;
        $status = $reports->save();
        // dd($reports);
        if ($status) {
            return redirect('service-reports/' . $reports->id)->with('success', 'Data Has been created');
        }
    }

    public function storeMainLog(Request $request, $id)
    {
        // dd($monitoring);
        $monitoring = Monitoring::find($id);
        // dd($monitoring->id_machine);
        $mainlog = new Mainlog();
        $mainlog->id_machine = $monitoring->id_machine;
        $mainlog->id_teknisi = auth::user()->id;
        $mainlog->desc = $request->main_desc;
        $mainlog->next = $request->main_next;
        $mainlog->date = Carbon::today();
        $monitorSave = $mainlog->save();
        if ($monitorSave) {
            return redirect('/monitoring-service-create/' . $id . '/' . $monitoring->id_machine)->with('message', 'Data telah di save');
        }
    }

    public function storeMainLogService(Request $request, $id)
    {
        // dd($monitoring);
        // dd($monitoring->id_machine);
        $mainlog = new Mainlog();
        $mainlog->id_machine = $id;
        $mainlog->id_teknisi = $request->teknisi;
        $mainlog->desc = $request->main_desc;
        $mainlog->next = $request->main_next;
        $mainlog->date = $request->date;
        $monitorSave = $mainlog->save();
        if ($monitorSave) {
            return redirect('/service-manager-daily/' . $id . '/' . Carbon::today()->format('m'))->with('message', 'Data telah di save');
        }
    }
    public function deleteMainlog($id)
    {
        $mainlog = Mainlog::find($id);
        $mainlogDel = $mainlog->delete();
        if ($mainlogDel) {
            return 1;
        } else {
            return 0;
        }

    }
    public function visitorDailyMonth($id, $month)
    {
        $machine = Machine::find($id);
        $client = Client::find($machine->id_client);

        $today = Carbon::today()->setMonth($month)
            // ->subMonth(1)
        ;

        $startOfMonth = $today->copy()->startOfMonth();
        $startOfMonthDate = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $dates = [];
        for ($date = $startOfMonthDate; $date->lte($endOfMonth); $date->addDay()) {
            $dates[] = $date->format('d-m-Y');
        }
        // dd($dates);

        // Ambil data monitoring dari database
        $monitoringData = Monitoring::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('id_machine', $id)
            ->whereYear('date', $today->year)
            ->join('users as u', 'u.id', '=', 'monitoring.id_pic')
            ->get(['monitoring.*', 'u.name'])
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date)->format('d-m-Y'); // Format tanggal
                return $item;
            });

        $compressorIndexed = $monitoringData->keyBy('date')->map(function ($item) {
            return [
                'id' => $item->id ?? '-',
                'running' => $item->running ?? '-',
                'loading' => $item->loading ?? '-',
                'pressure' => $item->pressure ?? '-',
                'temp' => $item->temp ?? '-',
                'leak' => $item->leak ?? '-',
                'condition' => $item->condition ?? '-',
                'oil_level' => $item->oil_level ?? '-',
                'issue' => $item->issue ?? '-',
                'pic' => $item->name ?? '-',
            ];
        })->toArray();

        // Gabungkan data monitoring dengan daftar tanggal
        $compressor = [];
        foreach ($dates as $date) {
            $compressor[] = [
                'date' => $date,
                'id' => $compressorIndexed[$date]['id'] ?? '-',
                'running' => $compressorIndexed[$date]['running'] ?? '-',
                'loading' => $compressorIndexed[$date]['loading'] ?? '-',
                'pressure' => $compressorIndexed[$date]['pressure'] ?? '-',
                'leak' => $compressorIndexed[$date]['leak'] ?? '-',
                'temp' => $compressorIndexed[$date]['temp'] ?? '-',
                'condition' => $compressorIndexed[$date]['condition'] ?? '-',
                'oil_level' => $compressorIndexed[$date]['oil_level'] ?? '-',
                'issue' => $compressorIndexed[$date]['issue'] ?? '-',
                'pic' => $compressorIndexed[$date]['pic'] ?? '-',
            ];
        }

        $dryerIndexed = $monitoringData->keyBy('date')->map(function ($item) {
            return [
                'id' => $item->id ?? '-',
                'temp' => $item->temp ?? '-',
                'temp_out' => $item->temp_out ?? '-',
                'dew' => $item->dew ?? '-',
                'drain' => $item->drain ?? '-',
                'leak' => $item->leak ?? '-',
                'fan' => $item->fan ?? '-',
                'condition' => $item->condition ?? '-',
                'oil_level' => $item->oil_level ?? '-',
                'issue' => $item->issue ?? '-',
                'pic' => $item->name ?? '-',
            ];
        })->toArray();

        // Gabungkan data monitoring dengan daftar tanggal
        $dryer = [];
        foreach ($dates as $date) {
            $dryer[] = [
                'date' => $date,
                'id' => $dryerIndexed[$date]['id'] ?? '-',
                'temp' => $dryerIndexed[$date]['temp'] ?? '-',
                'temp_out' => $dryerIndexed[$date]['temp_out'] ?? '-',
                'dew' => $dryerIndexed[$date]['dew'] ?? '-',
                'drain' => $dryerIndexed[$date]['drain'] ?? '-',
                'condition' => $dryerIndexed[$date]['condition'] ?? '-',
                'oil_level' => $dryerIndexed[$date]['oil_level'] ?? '-',
                'issue' => $dryerIndexed[$date]['issue'] ?? '-',
                'leak' => $dryerIndexed[$date]['leak'] ?? '-',
                'fan' => $dryerIndexed[$date]['fan'] ?? '-',
                'pic' => $dryerIndexed[$date]['pic'] ?? '-',
            ];
        }
        // dd($compressor);

        // Return data
        $monitoringThisMonth = response()->json($compressor);

        $weeks = [1, 2, 3, 4, 5];
        $weeksoy = collect($weeks)->map(function ($week) use ($id, $today, $month) {
            $data = MonitoringWeekly::join('users as u', 'u.id', '=', 'monitoring_weekly.id_pic')
                ->where('id_machine', $id)
                ->where('week', $week)
                ->whereMonth('monitoring_weekly.date', $month)
                ->whereYear('monitoring_weekly.date', $today->year)
                ->select('monitoring_weekly.*', 'u.name')
                ->first();

            return $data ?? [
                'id_pic' => '-',
                'id_machine' => '-',
                'condition' => '-',
                'voltage' => '-',
                'ampere' => '-',
                'vibration' => '-',
                'idle' => '-',
                'week' => '-',
                'drain' => '-',
                'pre' => '-',
                'cooler' => 0,
                'coupling' => 2,
                'area' => 0,
                'condensor' => 0,
                'after' => '-',
                'desc' => '-',
                'type' => '-',
                'date' => '-',
                'name' => '-',
            ];
        })->toArray();

        $issue = Monitoring::leftJoin('pn_monitoring as pn', 'pn.id_monitoring', '=', 'monitoring.id')
            ->join('users as u', 'u.id', '=', 'monitoring.id_pic')
            ->where('id_machine', $id)
            ->whereNot('issue', '-')
            ->whereNot('issue', 'normal')
            ->whereNot('issue', 'Normal')
            ->whereNotNull('issue')
            ->whereMonth('monitoring.date', $month)
            ->whereYear('monitoring.date', $today->year)
            ->select(
                'monitoring.*',
                'u.name',
                DB::raw("IFNULL(GROUP_CONCAT(pn.pn SEPARATOR ' | '), '-') as pn")
            )
            ->groupBy('monitoring.id')
            ->get();

        $mainlog = Mainlog::join('users as u', 'u.id', '=', 'main_log.id_teknisi')
            ->where('id_machine', $id)
            ->whereMonth('date', $month)
            ->whereYear('date', $today->year)
            ->whereNotNull('desc')
            ->select('main_log.*', 'u.name')->get();
        $quotes = Quotation::join('detail_quotation as d', 'd.id_quotation', '=', 'quotation.id')
            ->leftJoin('serial_product as sp', 'sp.id', '=', 'd.id_equivalent')
            ->join('machine as m', 'm.id_unit', '=', 'sp.id')
            ->where('m.id_client', 1277)
            ->where('m.id', $id)
            ->where('quotation.is_primary', 1)
            ->whereMonth('estimated_date', $month)
            ->whereYear('estimated_date', $today->year)->get();
        $monthly = MonitoringMonthly::whereMonth('date', $month)->whereYear('date', $today->year)->where('id_machine', $id)->first();

        return view('pages.monitoring.visitor-change', compact('quotes', 'machine', 'client', 'compressor', 'dryer', 'weeksoy', 'issue', 'mainlog', 'monthly')); 
        // return view('pages.under-maintenance', compact('quotes', 'machine', 'client', 'compressor', 'dryer', 'weeksoy', 'issue', 'mainlog', 'monthly')); FajarPaper
    }
    public function visitorDaily($id)
    {
        $machine = Machine::find($id);
        $client = Client::find($machine->id_client);

        $today = Carbon::today()
            // ->subMonth(1)
        ;

        $startOfMonth = $today->copy()->startOfMonth();
        $startOfMonthDate = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $dates = [];
        for ($date = $startOfMonthDate; $date->lte($endOfMonth); $date->addDay()) {
            $dates[] = $date->format('d-m-Y');
        }
        // dd($dates);

        // Ambil data monitoring dari database
        $monitoringData = Monitoring::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('id_machine', $id)
            ->join('users as u', 'u.id', '=', 'monitoring.id_pic')
            ->get(['monitoring.*', 'u.name'])
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date)->format('d-m-Y'); // Format tanggal
                return $item;
            });

        $compressorIndexed = $monitoringData->keyBy('date')->map(function ($item) {
            return [
                'id' => $item->id ?? '-',
                'running' => $item->running ?? '-',
                'loading' => $item->loading ?? '-',
                'pressure' => $item->pressure ?? '-',
                'temp' => $item->temp ?? '-',
                'leak' => $item->leak ?? '-',
                'condition' => $item->condition ?? '-',
                'oil_level' => $item->oil_level ?? '-',
                'issue' => $item->issue ?? '-',
                'pic' => $item->name ?? '-',
            ];
        })->toArray();

        // Gabungkan data monitoring dengan daftar tanggal
        $compressor = [];
        foreach ($dates as $date) {
            $compressor[] = [
                'date' => $date,
                'id' => $compressorIndexed[$date]['id'] ?? '-',
                'running' => $compressorIndexed[$date]['running'] ?? '-',
                'loading' => $compressorIndexed[$date]['loading'] ?? '-',
                'pressure' => $compressorIndexed[$date]['pressure'] ?? '-',
                'leak' => $compressorIndexed[$date]['leak'] ?? '-',
                'temp' => $compressorIndexed[$date]['temp'] ?? '-',
                'condition' => $compressorIndexed[$date]['condition'] ?? '-',
                'oil_level' => $compressorIndexed[$date]['oil_level'] ?? '-',
                'issue' => $compressorIndexed[$date]['issue'] ?? '-',
                'pic' => $compressorIndexed[$date]['pic'] ?? '-',
            ];
        }

        $dryerIndexed = $monitoringData->keyBy('date')->map(function ($item) {
            return [
                'id' => $item->id ?? '-',
                'temp' => $item->temp ?? '-',
                'temp_out' => $item->temp_out ?? '-',
                'dew' => $item->dew ?? '-',
                'drain' => $item->drain ?? '-',
                'leak' => $item->leak ?? '-',
                'fan' => $item->fan ?? '-',
                'condition' => $item->condition ?? '-',
                'oil_level' => $item->oil_level ?? '-',
                'issue' => $item->issue ?? '-',
                'pic' => $item->name ?? '-',
            ];
        })->toArray();

        // Gabungkan data monitoring dengan daftar tanggal
        $dryer = [];
        foreach ($dates as $date) {
            $dryer[] = [
                'date' => $date,
                'id' => $dryerIndexed[$date]['id'] ?? '-',
                'temp' => $dryerIndexed[$date]['temp'] ?? '-',
                'temp_out' => $dryerIndexed[$date]['temp_out'] ?? '-',
                'dew' => $dryerIndexed[$date]['dew'] ?? '-',
                'drain' => $dryerIndexed[$date]['drain'] ?? '-',
                'condition' => $dryerIndexed[$date]['condition'] ?? '-',
                'oil_level' => $dryerIndexed[$date]['oil_level'] ?? '-',
                'issue' => $dryerIndexed[$date]['issue'] ?? '-',
                'leak' => $dryerIndexed[$date]['leak'] ?? '-',
                'fan' => $dryerIndexed[$date]['fan'] ?? '-',
                'pic' => $dryerIndexed[$date]['pic'] ?? '-',
            ];
        }
        // dd($compressor);

        // Return data
        $monitoringThisMonth = response()->json($compressor);

        $weeks = [1, 2, 3, 4, 5];
        $weeksoy = collect($weeks)->map(function ($week) use ($id, $today) {
            $data = MonitoringWeekly::join('users as u', 'u.id', '=', 'monitoring_weekly.id_pic')
                ->where('id_machine', $id)
                ->where('week', $week)
                ->whereMonth('monitoring_weekly.date', $today->month)
                ->whereYear('monitoring_weekly.date', $today->year)
                ->select('monitoring_weekly.*', 'u.name')
                ->first();

            return $data ?? [
                'id_pic' => '-',
                'id_machine' => '-',
                'condition' => '-',
                'voltage' => '-',
                'ampere' => '-',
                'vibration' => '-',
                'idle' => '-',
                'week' => '-',
                'drain' => '-',
                'pre' => '-',
                'cooler' => 0,
                'coupling' => 2,
                'area' => 0,
                'condensor' => 0,
                'after' => '-',
                'desc' => '-',
                'type' => '-',
                'date' => '-',
                'name' => '-',
            ];
        })->toArray();

        $issue = Monitoring::leftJoin('pn_monitoring as pn', 'pn.id_monitoring', '=', 'monitoring.id')
            ->join('users as u', 'u.id', '=', 'monitoring.id_pic')
            ->where('id_machine', $id)
            ->whereNot('issue', '-')
            ->whereNot('issue', 'normal')
            ->whereNot('issue', 'Normal')
            ->whereNotNull('issue')
            ->whereMonth('monitoring.date', $today->month)
            ->select(
                'monitoring.*',
                'u.name',
                DB::raw("IFNULL(GROUP_CONCAT(pn.pn SEPARATOR ' | '), '-') as pn")
            )
            ->groupBy('monitoring.id')
            ->get();

        $mainlog = Mainlog::join('users as u', 'u.id', '=', 'main_log.id_teknisi')->where('id_machine', $id)->whereMonth('date', $today->month)->whereNotNull('desc')->select('main_log.*', 'u.name')->orderBy('main_log.date', 'asc')->get();
        $quotes = Quotation::join('detail_quotation as d', 'd.id_quotation', '=', 'quotation.id')
            ->leftJoin('serial_product as sp', 'sp.id', '=', 'd.id_equivalent')
            ->join('machine as m', 'm.id_unit', '=', 'sp.id')
            ->where('m.id_client', 1277)
            ->where('m.id', $id)
            ->where('quotation.is_primary', 1)
            ->whereMonth('estimated_date', $today->month)->get();

        $monthly = MonitoringMonthly::whereMonth('date', $today->month)->where('id_machine', $id)->first();
        //return view('pages.monitoring.visitor', compact('quotes', 'machine', 'client', 'compressor', 'dryer', 'weeksoy', 'issue', 'mainlog', 'monthly')); 
        return view('pages.under-maintenance', compact('quotes', 'machine', 'client', 'compressor', 'dryer', 'weeksoy', 'issue', 'mainlog', 'monthly'));
    }

    public function logDaily($id)
    {
        $machine = Machine::find($id);
        $client = Client::find($machine->id_client);

        $today = Carbon::today();

        $monitoring = Monitoring::whereNotNULL('main_desc')->where('main_desc', '!=', '-')->whereMonth('date', $today->month)->where('id_machine', $id)->get();
        $issue = Monitoring::whereNotNULL('issue')->where('issue', '!=', '-')->whereMonth('date', $today->month)->where('id_machine', $id)->get();
        // dd($monitoring);

        return view('pages.monitoring.maintenance-log', compact('machine', 'client', 'monitoring', 'issue'));
    }

    // monitoring daily
    public function indexWeekly($id)
    {
        $machine = Machine::find($id);
        // dd($machine->unit);
        return view('pages.monitoring.index-weekly', compact('machine'));
    }
    // Monitoring Weekly
    public function createWeekly($id)
    {
        $today = Carbon::now(); // Hari ini
        $weekNumber = $today->weekOfYear;
        $machine = Machine::find($id);
        // dd($weekNumber);
        return view('pages.monitoring.form-weekly', compact('machine', 'weekNumber'));
    }
    public function editWeekly($id)
    {
        $today = Carbon::now(); // Hari ini
        $weekly = MonitoringWeekly::find($id);
        $machine = Machine::find($weekly->id_machine);
        // dd($weekNumber);
        return view('pages.monitoring.form-weekly', compact('machine', 'weekly'));
    }
    public function storeWeekly(Request $request, $id)
    {
        // dd($request->all());
        $machine = Machine::find($id);
        // dd($machine->unit->unit->unit);
        $monitoring = new MonitoringWeekly();
        $monitoring->id_machine = $id;
        $monitoring->id_pic = Auth::user()->id;
        $monitoring->week = $request->week;
        if ($request->condition != 'Off') {
            $monitoring->condition = $request->condition;
            $monitoring->ampere = $request->ampere . ' A';
            $monitoring->voltage = $request->voltage . ' V';
            if ($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER') {
                $monitoring->drain = $request->drain;
                $monitoring->vibration = $request->v . '/' . $request->h . '/' . $request->a;
                if ($request->cooler == 1) {
                    $monitoring->cooler = 1;
                } else {
                    $monitoring->cooler = 0;
                }
                if ($request->coupling == 1) {
                    $monitoring->coupling = 1;
                } else {
                    $monitoring->coupling = 0;
                }
                if ($request->area == 1) {
                    $monitoring->area = 1;
                } else {
                    $monitoring->area = 0;
                }
            } else {
                $monitoring->dew = $request->dew;
                $monitoring->drain = $request->drain;
                $monitoring->pre = $request->pre;
                $monitoring->after = $request->after;
                if ($request->condensor == 1) {
                    $monitoring->condensor = 1;
                } else {
                    $monitoring->condensor = 0;
                }
            }
        } else {
            $monitoring->condition = $request->condition;
            $monitoring->ampere = '-';
            $monitoring->voltage = '-';
            if ($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER') {
                $monitoring->idle = '-';
                $monitoring->vibration = '-';
                $monitoring->area = 0;
                $monitoring->coupling = 0;
                $monitoring->cooler = 0;
            } else {
                $monitoring->dew = '-';
                $monitoring->drain = '-';
                $monitoring->pre = '-';
                $monitoring->after = '-';
                $monitoring->condensor = 0;
            }
        }
        $monitoring->desc = $request->desc;
        $monitoring->date = Carbon::today();
        $monitorSave = $monitoring->save();
        if ($monitorSave) {
            return redirect('/monitoring/weekly/' . $id)->with('message', 'Data telah diibuat');
        }
    }
    public function updateWeekly(Request $request, $id)
    {
        // dd($request->condition);
        // dd($machine->unit->unit->unit);
        $monitoring = MonitoringWeekly::find($id);
        if ($request->condition != 'Off') {
            $monitoring->condition = $request->condition;
            $monitoring->ampere = $request->ampere . ' A';
            $monitoring->voltage = $request->voltage . ' V';
            if ($monitoring->machine->unit->unit->unit != 'REFRIGERANT AIR DRYER') {
                $monitoring->drain = $request->drain;
                $monitoring->vibration = $request->vibration;
                if ($request->cooler == 1) {
                    $monitoring->cooler = 1;
                } else {
                    $monitoring->cooler = 0;
                }
                if ($request->coupling == 1) {
                    $monitoring->coupling = 1;
                } else {
                    $monitoring->coupling = 0;
                }
                if ($request->area == 1) {
                    $monitoring->area = 1;
                } else {
                    $monitoring->area = 0;
                }
            } else {
                $monitoring->drain = $request->drain;
                $monitoring->pre = $request->pre;
                $monitoring->after = $request->after;
                if ($request->condensor == 1) {
                    $monitoring->condensor = 1;
                } else {
                    $monitoring->condensor = 0;
                }
            }
        } else {
            $monitoring->condition = $request->condition;
            $monitoring->ampere = '-';
            $monitoring->voltage = '-';
            if ($monitoring->machine->unit->unit->unit != 'REFRIGERANT AIR DRYER') {
                $monitoring->idle = '-';
                $monitoring->vibration = '-';
                $monitoring->area = 0;
                $monitoring->coupling = 0;
                $monitoring->cooler = 0;
            } else {
                $monitoring->dew = '-';
                $monitoring->drain = '-';
                $monitoring->pre = '-';
                $monitoring->after = '-';
                $monitoring->condensor = 0;
            }
        }
        $monitoring->desc = $request->desc;
        $date = $monitoring->date;
        $monitorSave = $monitoring->save();
        if ($monitorSave) {
            return redirect('/service-manager-daily/' . $monitoring->id_machine . '/' . Carbon::parse($date)->month)->with('message', 'Data telah dibuat');
        }
    }
    public function visitorWeekly($id)
    {
        $machine = Machine::find($id);
        $client = Client::find($machine->id_client);
        $machineC = Client::join('machine as m', 'client.id', '=', 'm.id_client')->groupBy('client.id')->whereNotBetween('m.id', [472, 481])->get('m.*');
        $monitoringAC = Machine::leftJoin('monitoring_weekly as mw', 'mw.id_machine', '=', 'machine.id')
            ->join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->join('unit as u', 'u.id', '=', 'sp.id_product')
            ->leftJoin('users as us', 'us.id', '=', 'mw.id_pic')
            ->whereNotBetween('machine.id', [472, 481])
            ->where('u.unit', 'AIR COMPRESSOR SCREW')
            ->where('machine.id_client', $client->id)
            ->select('machine.*', 'mw.*', 'us.name', 'machine.id as idM')
            ->groupBy('machine.id')
            ->get();
        $monitoringDRYER = Machine::leftJoin('monitoring_weekly as mw', 'mw.id_machine', '=', 'machine.id')
            ->join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->join('unit as u', 'u.id', '=', 'sp.id_product')
            ->leftJoin('users as us', 'us.id', '=', 'mw.id_pic')
            ->whereNotBetween('machine.id', [472, 481])
            ->where('u.unit', 'REFRIGERANT AIR DRYER')
            ->where('machine.id_client', $client->id)
            ->select('machine.*', 'mw.*', 'us.name', 'machine.id as idM')
            ->groupBy('machine.id')
            ->get();
        // dd($monitoringDRYER);


        $startDate = Carbon::now()->startOfWeek()->format('d-m-Y');
        $endDate = Carbon::now()->endOfWeek()->format('d-m-Y');

        return view('pages.monitoring.visitor-weekly', compact('startDate', 'endDate', 'machine', 'client', 'monitoringAC', 'monitoringDRYER'));
    }

    public function createMonthly($id)
    {
        $today = Carbon::now(); // Hari ini
        $machine = Machine::find($id);
        // dd($weekNumber);
        return view('pages.monitoring.form-monthly', compact('machine'));
    }
    public function storeMonthly(Request $request, $id)
    {
        // dd($request->all());
        $machine = Machine::find($id);
        // dd($machine->unit->unit->unit);
        $monitoring = new MonitoringMonthly();
        $monitoring->id_machine = $id;
        $monitoring->id_pic = Auth::user()->id;
        if ($request->condition == "Off") {
            $monitoring->condition = $request->condition;
            $monitoring->strainer = '-';
            $monitoring->lp = '-';
            $monitoring->hp = '-';
            $monitoring->date = Carbon::today();
        } else {
            $monitoring->condition = $request->condition;
            $monitoring->strainer = $request->strainer;
            $monitoring->lp = $request->lp;
            $monitoring->hp = $request->hp;
            $monitoring->date = Carbon::today();
        }
        $monitorSave = $monitoring->save();
        if ($monitorSave) {
            return redirect('/service-manager')->with('message', 'Data telah dibuat');
        }
    }
    // get data monitoring
    public function getMonitoringCompressorThisMonth($id)
    {
        $today = Carbon::today();
        $machine = Machine::find($id);

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
                'id' => $item->id ?? '-',
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
                'id' => $monitoringIndexed[$date]['id'] ?? '-',
                'runing' => $monitoringIndexed[$date]['runing'] ?? '-',
                'load' => $monitoringIndexed[$date]['load'] ?? '-',
                'pressure' => $monitoringIndexed[$date]['pressure'] ?? '-',
                'temp' => $monitoringIndexed[$date]['temp'] ?? '-',
                'condition' => $monitoringIndexed[$date]['condition'] ?? '-',
                'oil_level' => $monitoringIndexed[$date]['oil_level'] ?? '-',
                'desc' => $monitoringIndexed[$date]['desc'] ?? '-',
            ];
        }

        // Return data
        return response()->json(['data' => $result]);
    }
    public function getMonitoringDryerThisMonth($id)
    {
        $today = Carbon::today();
        $machine = Machine::find($id);

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
                'id' => $item->id ?? '-',
                'temp' => $item->temp ?? '-',
                'temp_out' => $item->temp_out ?? '-',
                'dew' => $item->dew ?? '-',
                'drain' => $item->drain ?? '-',
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
                'id' => $monitoringIndexed[$date]['id'] ?? '-',
                'temp' => $monitoringIndexed[$date]['temp'] ?? '-',
                'temp_out' => $monitoringIndexed[$date]['temp_out'] ?? '-',
                'dew' => $monitoringIndexed[$date]['dew'] ?? '-',
                'drain' => $monitoringIndexed[$date]['drain'] ?? '-',
                'condition' => $monitoringIndexed[$date]['condition'] ?? '-',
                'oil_level' => $monitoringIndexed[$date]['oil_level'] ?? '-',
                'desc' => $monitoringIndexed[$date]['desc'] ?? '-',
            ];
        }

        // Return data
        return response()->json(['data' => $result]);
    }

    // service Manager
    public function indexServiceProkemas()
    {
        // $today = Carbon::now()->toDateString();
        // $commodity = Product::count();
        // $dproduct = DetailProduct::count();
        // $sproduct = SerialProduct::count();
        // $user = User::find('25');
        // $monitoring = MonitoringActivities::whereDate('date', $today)->first();

        // $visits = ReqVisit::whereNull('date')->get();
        // $visited = ReqVisit::whereNotNull('date')->whereNull('visit_date')->get();
        return view(
            "pages.monitoring.service-index-prokemas"
        );
    }

    public function indexServiceM()
    {
        $allPlant = Machine::where('id_client', 1277)->whereNotBetween('id', [472, 481])->count();
        $allPlantMonitoring = Machine::where('id_client', 1277)->whereNotBetween('id', [472, 481])
            ->whereHas('monitoring', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();
        $GT = Machine::where('id_client', 1277)->where('location', 'GT 1-2')->whereNotBetween('id', [472, 481])->count();
        $GTMonitoring = Machine::where('id_client', 1277)->where('location', 'GT 1-2')->whereNotBetween('id', [472, 481])
            ->whereHas('monitoring', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();
        $GT3 = Machine::where('id_client', 1277)->where('location', 'GT3/BOILER')->whereNotBetween('id', [472, 481])->count();
        $GT3Monitoring = Machine::where('id_client', 1277)->where('location', 'GT3/BOILER')->whereNotBetween('id', [472, 481])
            ->whereHas('monitoring', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();
        $INC = Machine::where('id_client', 1277)->where('location', 'INC')->whereNotBetween('id', [472, 481])->count();
        $INCMonitoring = Machine::where('id_client', 1277)->where('location', 'INC')->whereNotBetween('id', [472, 481])
            ->whereHas('monitoring', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();
        $PM12 = Machine::where('id_client', 1277)->where('location', 'BM 1-2')->whereNotBetween('id', [472, 481])->count();
        $PM12Monitoring = Machine::where('id_client', 1277)->where('location', 'BM 1-2')->whereNotBetween('id', [472, 481])
            ->whereHas('monitoring', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();
        $PM35 = Machine::where('id_client', 1277)->whereBetween('location', ['BM 3', 'BM 5'])->whereNotBetween('id', [472, 481])->count();
        $PM35Monitoring = Machine::where('id_client', 1277)->whereBetween('location', ['BM 3', 'BM 5'])->whereNotBetween('id', [472, 481])
            ->whereHas('monitoring', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();
        $PM78 = Machine::where('id_client', 1277)->whereBetween('location', ['BM 7', 'BM 8'])->whereNotBetween('id', [472, 481])->count();
        $PM78Monitoring = Machine::where('id_client', 1277)->whereBetween('location', ['BM 7', 'BM 8'])->whereNotBetween('id', [472, 481])
            ->whereHas('monitoring', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();


        $day = Carbon::today();
        $month = $day->month;
        $year = $day->year;

        $machines = Machine::with([
            'unit',
            'unit.unit',
            'monitoring' => function ($query) use ($month, $year) {
                $query->whereMonth('date', $month)
                    ->whereYear('date', $year);
            },
            'mainlog' => function ($query) use ($month, $year) {
                $query->whereMonth('date', $month)
                    ->whereYear('date', $year);
            }
        ])->where('id_client', 1277)->whereNotBetween('id', [472, 481])->get();

        $result = $machines->filter(function ($machine) {
            return $machine->monitoring->isNotEmpty();
        })->map(function ($machine) {
            return [
                'machine' => $machine->unit->brand . ' ' . $machine->unit->unit->sku . ' - ' . $machine->tag . ' - ' . $machine->location,
                'id' => $machine->id,
                'log' => $machine->monitoring->filter(function ($log) {
                    return !is_null($log->desc) && $log->desc !== '-' && $log->desc !== 'normal' && $log->desc !== 'Normal';
                })->map(function ($log) {
                    return [
                        'log' => $log->desc,
                        'date' => \Carbon\Carbon::parse($log->date)->format('d'),
                        'pic' => $log->pic->name
                    ];
                }),
                'mainlog' => $machine->mainlog->filter(function ($mainlog) {
                    return !is_null($mainlog->desc) && $mainlog->desc !== '-';
                })->map(function ($mainlog) {
                    return [
                        'id' => $mainlog->id,
                        'id_pic' => $mainlog->id_teknisi,
                        'id_machine' => $mainlog->machine->id,
                        'id_service' => optional($mainlog->issue?->reports->first())->id,
                        'log' => $mainlog->desc,
                        'date' => \Carbon\Carbon::parse($mainlog->date)->format('d'),
                        'technician' => $mainlog->teknisi->name
                    ];
                }),
            ];
        });
        return view('pages.monitoring.service-index', compact('month', 'year', 'allPlant', 'allPlantMonitoring', 'GT', 'GTMonitoring', 'GT3', 'GT3Monitoring', 'INC', 'INCMonitoring', 'PM12', 'PM12Monitoring', 'PM35', 'PM35Monitoring', 'PM78', 'PM78Monitoring', 'result'));
    }
    public function showServiceM($id)
    {
        $machine = Machine::find($id);
        return view('pages.monitoring.service-detail', compact('machine'));
    }
    public function visitorDailyServiceProkemas($id, $month)
    {
        $machine = Machine::find($id);
        $months = $month;
        // dd($months);
        $client = Client::find($machine->id_client);

        $setday = Carbon::today();
        $today = $setday->setMonth($month);
        $year = $today->year;

        $startOfMonth = $today->copy()->startOfMonth();
        $startOfMonthDate = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $dates = [];
        for ($date = $startOfMonthDate; $date->lte($endOfMonth); $date->addDay()) {
            $dates[] = $date->format('d-m-Y');
        }
        // dd($dates);

        // Ambil data monitoring dari database
        $monitoringData = Monitoring::whereBetween('monitoring.date', [$startOfMonth, $endOfMonth])
            ->where('id_machine', $id)
            ->join('users as u', 'u.id', '=', 'monitoring.id_pic')
            ->join('machine as m', 'm.id', '=', 'monitoring.id_machine')
            ->leftJoin('serial_product as sp', 'sp.id', '=', 'm.id_unit')
            ->leftJoin('unit as un', 'un.id', '=', 'sp.id_product')
            ->get(['monitoring.*', 'u.name', 'm.id as id_machine', 'un.unit'])
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date)->format('d-m-Y'); // Format tanggal
                return $item;
            });
        // dd($monitoringData);

        $compressorIndexed = $monitoringData->keyBy('date')->map(function ($item) {
            return [
                'id' => $item->id ?? '-',
                'id_machine' => $item->id_machine ?? '-',
                'unit' => $item->unit ?? '-',
                'running' => $item->running ?? '-',
                'loading' => $item->loading ?? '-',
                'pressure' => $item->pressure ?? '-',
                'dew' => $item->dew ?? '-',
                'drain' => $item->drain ?? '-',
                'temp' => $item->temp ?? '-',
                'temp_out' => $item->temp_out ?? '-',
                'condition' => $item->condition ?? '-',
                'oil_level' => $item->oil_level ?? '-',
                'fan' => $item->fan ?? '-',
                'leak' => $item->leak ?? '-',
                'pic' => $item->name ?? '-',
                'issue' => $item->issue ?? '-',
                'main_desc' => $item->main_desc ?? '-',
            ];
        })->toArray();
        // dd($compressorIndexed);

        // Gabungkan data monitoring dengan daftar tanggal
        $compressor = [];
        foreach ($dates as $date) {
            $compressor[] = [
                'date' => $date,
                'id' => $compressorIndexed[$date]['id'] ?? '-',
                'id_machine' => $compressorIndexed[$date]['id_machine'] ?? '-',
                'unit' => $compressorIndexed[$date]['unit'] ?? '-',
                'running' => $compressorIndexed[$date]['running'] ?? '-',
                'loading' => $compressorIndexed[$date]['loading'] ?? '-',
                'pressure' => $compressorIndexed[$date]['pressure'] ?? '-',
                'temp' => $compressorIndexed[$date]['temp'] ?? '-',
                'temp_out' => $compressorIndexed[$date]['temp_out'] ?? '-',
                'dew' => $compressorIndexed[$date]['dew'] ?? '-',
                'drain' => $compressorIndexed[$date]['drain'] ?? '-',
                'condition' => $compressorIndexed[$date]['condition'] ?? '-',
                'oil_level' => $compressorIndexed[$date]['oil_level'] ?? '-',
                'leak' => $compressorIndexed[$date]['leak'] ?? '-',
                'pic' => $compressorIndexed[$date]['pic'] ?? '-',
                'fan' => $compressorIndexed[$date]['fan'] ?? '-',
                'issue' => $compressorIndexed[$date]['issue'] ?? '-',
                'main_desc' => $compressorIndexed[$date]['main_desc'] ?? '-',
            ];
        }
        // dd($compressor);

        $dryerIndexed = $monitoringData->keyBy('date')->map(function ($item) {
            return [
                'id' => $item->id ?? '-',
                'id_machine' => $item->id_machine ?? '-',
                'temp' => $item->temp ?? '-',
                'temp_out' => $item->temp_out ?? '-',
                'dew' => $item->dew ?? '-',
                'drain' => $item->drain ?? '-',
                'condition' => $item->condition ?? '-',
                'oil_level' => $item->oil_level ?? '-',
                'leak' => $item->leak ?? '-',
                'fan' => $item->fan ?? '-',
                'issue' => $item->issue ?? '-',
                'main_desc' => $item->main_desc ?? '-',
                'pic' => $item->name ?? '-',
            ];
        })->toArray();

        // Gabungkan data monitoring dengan daftar tanggal
        $dryer = [];
        foreach ($dates as $date) {
            $dryer[] = [
                'date' => $date,
                'id' => $dryerIndexed[$date]['id'] ?? '-',
                'id_machine' => $compressorIndexed[$date]['id_machine'] ?? '-',
                'temp' => $dryerIndexed[$date]['temp'] ?? '-',
                'temp_out' => $dryerIndexed[$date]['temp_out'] ?? '-',
                'dew' => $dryerIndexed[$date]['dew'] ?? '-',
                'drain' => $dryerIndexed[$date]['drain'] ?? '-',
                'condition' => $dryerIndexed[$date]['condition'] ?? '-',
                'leak' => $dryerIndexed[$date]['leak'] ?? '-',
                'fan' => $dryerIndexed[$date]['fan'] ?? '-',
                'oil_level' => $dryerIndexed[$date]['oil_level'] ?? '-',
                'issue' => $dryerIndexed[$date]['issue'] ?? '-',
                'main_desc' => $dryerIndexed[$date]['main_desc'] ?? '-',
                'pic' => $dryerIndexed[$date]['pic'] ?? '-',
            ];
        }
        // dd($compressor);

        // Return data
        $monitoringThisMonth = response()->json($compressor);

        $issue = Monitoring::leftJoin('pn_monitoring as pn', 'pn.id_monitoring', '=', 'monitoring.id')
            ->join('users as u', 'u.id', '=', 'monitoring.id_pic')
            ->where('id_machine', $id)
            ->whereNot('issue', '-')
            ->whereNot('issue', 'normal')
            ->whereNot('issue', 'Normal')
            ->whereNotNull('issue')
            ->whereMonth('monitoring.date', $month)
            ->select(
                'monitoring.*',
                'u.name',
                DB::raw("IFNULL(GROUP_CONCAT(pn.pn SEPARATOR ' | '), '-') as pn")
            )
            ->groupBy('monitoring.id')
            ->get();

        // test weekly
        $weeks = [1, 2, 3, 4, 5];
        $weeksoy = collect($weeks)->map(function ($week) use ($id, $month, $today) {
            $data = MonitoringWeekly::join('users as u', 'u.id', '=', 'monitoring_weekly.id_pic')
                ->where('id_machine', $id)
                ->where('week', $week)
                ->whereMonth('monitoring_weekly.date', $month)
                ->whereYear('monitoring_weekly.date', $today->year)
                ->select('monitoring_weekly.*', 'u.name')
                ->first();

            return $data ?? [
                'id' => '-',
                'id_pic' => '-',
                'id_machine' => '-',
                'condition' => '-',
                'voltage' => '-',
                'ampere' => '-',
                'vibration' => '-',
                'idle' => '-',
                'week' => '-',
                'drain' => '-',
                'pre' => '-',
                'cooler' => 0,
                'coupling' => 2,
                'area' => 0,
                'condensor' => 0,
                'after' => '-',
                'desc' => '-',
                'type' => '-',
                'date' => '-',
                'name' => '-',
            ];
        })->toArray();
        // dd($weeksoy);
        // $mainlog = Mainlog::join('users as u', 'u.id', '-', 'main_log.id_teknisi')->where('id_monitoring', $id)->whereMonth('date', $month)->get();
        $mainlog = Mainlog::join('users as u', 'u.id', '=', 'main_log.id_teknisi')->where('id_machine', $id)->whereMonth('date', $today->month)->whereNotNull('desc')->select('main_log.*', 'u.name')->orderBy('main_log.date', 'asc')->get();
        // dd($compressor);
        $weekly = MonitoringWeekly::where('id_machine', $id)->whereMonth('date', $month)->whereYear('date', $today->year)->get();
        $reports = Reports::where('id_machine', $id)->whereMonth('date', $month)->whereYear('date', $today->year)->orderBy('date')->get();

        $startDate = Carbon::create($today->year, $month, 1)->startOfWeek(Carbon::SUNDAY);
        $endDate = Carbon::create($today->year, $month, 1)->endOfMonth();
        $weeks = [];
        while ($startDate->lte($endDate)) {
            $weekNumber = $startDate->format('W'); // Nomor minggu dalam tahun
            $weeks[$weekNumber] = [
                // 'minggu' => "$weekNumber",
                'week' => '-',
                'condition' => '-',
                'voltage' => '-',
                'ampere' => '-',
                'idle' => '-',
                'vibration' => '-',
                'dew' => '-',
                'drain' => '-',
                'cooler' => '-',
                'coupling' => '-',
                'condensor' => '-',
                'area' => '-',
                'pre' => '-',
                'after' => '-',
                'desc' => '-',
                'date' => '-',
                'name' => '-'
            ];
            $startDate->addWeek();
        }

        // Isi data yang ada ke dalam minggu
        foreach ($weekly as $item) {
            $weekNumber = Carbon::parse($item->date)->format('W');
            $weeks[$weekNumber]['week'] = $item->week;
            $weeks[$weekNumber]['condition'] = $item->condition;
            $weeks[$weekNumber]['voltage'] = $item->voltage;
            $weeks[$weekNumber]['ampere'] = $item->ampere;
            $weeks[$weekNumber]['idle'] = $item->idle;
            $weeks[$weekNumber]['vibration'] = $item->vibration;
            $weeks[$weekNumber]['dew'] = $item->dew;
            $weeks[$weekNumber]['cooler'] = $item->cooler;
            $weeks[$weekNumber]['coupling'] = $item->coupling;
            $weeks[$weekNumber]['area'] = $item->area;
            $weeks[$weekNumber]['condensor'] = $item->condensor;
            $weeks[$weekNumber]['drain'] = $item->drain;
            $weeks[$weekNumber]['pre'] = $item->pre;
            $weeks[$weekNumber]['after'] = $item->after;
            $weeks[$weekNumber]['date'] = $item->date;
            $weeks[$weekNumber]['desc'] = $item->desc;
            $weeks[$weekNumber]['name'] = $item->pic->name;
        }
        // dd($weeks);

        $monthly = MonitoringMonthly::whereMonth('date', $month)->where('id_machine', $id)->first();
        $quotes = Quotation::join('detail_quotation as d', 'd.id_quotation', '=', 'quotation.id')->where('d.id_equivalent', $id)->whereMonth('estimated_date', $month)->get();
        // dd($monthly);

        $teknisi = User::get();
        // dd($teknisi);

        return view('pages.monitoring.service-visitor-prokemas', compact('teknisi', 'quotes', 'monthly', 'machine', 'client', 'compressor', 'dryer', 'months', 'issue', 'mainlog', 'weekly', 'reports', 'weeksoy'));
    }
    public function visitorDailyService($id, $month)
    {
        $machine = Machine::find($id);
        $months = $month;
        // dd($months);
        $client = Client::find($machine->id_client);

        $setday = Carbon::today();
        $today = $setday->setMonth($month);
        $year = $today->year;

        $startOfMonth = $today->copy()->startOfMonth();
        $startOfMonthDate = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();
        // dd($today);

        $dates = [];
        for ($date = $startOfMonthDate; $date->lte($endOfMonth); $date->addDay()) {
            $dates[] = $date->format('d-m-Y');
        }
        // dd($dates);

        // Ambil data monitoring dari database
        $monitoringData = Monitoring::whereBetween('monitoring.date', [$startOfMonth, $endOfMonth])
            ->where('id_machine', $id)
            ->join('users as u', 'u.id', '=', 'monitoring.id_pic')
            ->join('machine as m', 'm.id', '=', 'monitoring.id_machine')
            ->leftJoin('serial_product as sp', 'sp.id', '=', 'm.id_unit')
            ->leftJoin('unit as un', 'un.id', '=', 'sp.id_product')
            ->get(['monitoring.*', 'u.name', 'm.id as id_machine', 'un.unit'])
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date)->format('d-m-Y'); // Format tanggal
                return $item;
            });
        // dd($monitoringData);

        $compressorIndexed = $monitoringData->keyBy('date')->map(function ($item) {
            return [
                'id' => $item->id ?? '-',
                'id_machine' => $item->id_machine ?? '-',
                'unit' => $item->unit ?? '-',
                'running' => $item->running ?? '-',
                'loading' => $item->loading ?? '-',
                'pressure' => $item->pressure ?? '-',
                'dew' => $item->dew ?? '-',
                'drain' => $item->drain ?? '-',
                'temp' => $item->temp ?? '-',
                'temp_out' => $item->temp_out ?? '-',
                'condition' => $item->condition ?? '-',
                'oil_level' => $item->oil_level ?? '-',
                'fan' => $item->fan ?? '-',
                'leak' => $item->leak ?? '-',
                'pic' => $item->name ?? '-',
                'issue' => $item->issue ?? '-',
                'main_desc' => $item->main_desc ?? '-',
            ];
        })->toArray();
        // dd($compressorIndexed);

        // Gabungkan data monitoring dengan daftar tanggal
        $compressor = [];
        foreach ($dates as $date) {
            $compressor[] = [
                'date' => $date,
                'id' => $compressorIndexed[$date]['id'] ?? '-',
                'id_machine' => $compressorIndexed[$date]['id_machine'] ?? '-',
                'unit' => $compressorIndexed[$date]['unit'] ?? '-',
                'running' => $compressorIndexed[$date]['running'] ?? '-',
                'loading' => $compressorIndexed[$date]['loading'] ?? '-',
                'pressure' => $compressorIndexed[$date]['pressure'] ?? '-',
                'temp' => $compressorIndexed[$date]['temp'] ?? '-',
                'temp_out' => $compressorIndexed[$date]['temp_out'] ?? '-',
                'dew' => $compressorIndexed[$date]['dew'] ?? '-',
                'drain' => $compressorIndexed[$date]['drain'] ?? '-',
                'condition' => $compressorIndexed[$date]['condition'] ?? '-',
                'oil_level' => $compressorIndexed[$date]['oil_level'] ?? '-',
                'leak' => $compressorIndexed[$date]['leak'] ?? '-',
                'pic' => $compressorIndexed[$date]['pic'] ?? '-',
                'fan' => $compressorIndexed[$date]['fan'] ?? '-',
                'issue' => $compressorIndexed[$date]['issue'] ?? '-',
                'main_desc' => $compressorIndexed[$date]['main_desc'] ?? '-',
            ];
        }
        // dd($compressor);

        $dryerIndexed = $monitoringData->keyBy('date')->map(function ($item) {
            return [
                'id' => $item->id ?? '-',
                'id_machine' => $item->id_machine ?? '-',
                'temp' => $item->temp ?? '-',
                'temp_out' => $item->temp_out ?? '-',
                'dew' => $item->dew ?? '-',
                'drain' => $item->drain ?? '-',
                'condition' => $item->condition ?? '-',
                'oil_level' => $item->oil_level ?? '-',
                'leak' => $item->leak ?? '-',
                'fan' => $item->fan ?? '-',
                'issue' => $item->issue ?? '-',
                'main_desc' => $item->main_desc ?? '-',
                'pic' => $item->name ?? '-',
            ];
        })->toArray();

        // Gabungkan data monitoring dengan daftar tanggal
        $dryer = [];
        foreach ($dates as $date) {
            $dryer[] = [
                'date' => $date,
                'id' => $dryerIndexed[$date]['id'] ?? '-',
                'id_machine' => $compressorIndexed[$date]['id_machine'] ?? '-',
                'temp' => $dryerIndexed[$date]['temp'] ?? '-',
                'temp_out' => $dryerIndexed[$date]['temp_out'] ?? '-',
                'dew' => $dryerIndexed[$date]['dew'] ?? '-',
                'drain' => $dryerIndexed[$date]['drain'] ?? '-',
                'condition' => $dryerIndexed[$date]['condition'] ?? '-',
                'leak' => $dryerIndexed[$date]['leak'] ?? '-',
                'fan' => $dryerIndexed[$date]['fan'] ?? '-',
                'oil_level' => $dryerIndexed[$date]['oil_level'] ?? '-',
                'issue' => $dryerIndexed[$date]['issue'] ?? '-',
                'main_desc' => $dryerIndexed[$date]['main_desc'] ?? '-',
                'pic' => $dryerIndexed[$date]['pic'] ?? '-',
            ];
        }
        // dd($compressor);

        // Return data
        $monitoringThisMonth = response()->json($compressor);

        $issue = Monitoring::leftJoin('pn_monitoring as pn', 'pn.id_monitoring', '=', 'monitoring.id')
            ->join('users as u', 'u.id', '=', 'monitoring.id_pic')
            ->where('id_machine', $id)
            ->whereNot('issue', '-')
            ->whereNot('issue', 'normal')
            ->whereNot('issue', 'Normal')
            ->whereNotNull('issue')
            ->whereMonth('monitoring.date', $month)
            ->whereYear('monitoring.date', $today->year)
            ->select(
                'monitoring.*',
                'u.name',
                DB::raw("IFNULL(GROUP_CONCAT(pn.pn SEPARATOR ' | '), '-') as pn")
            )
            ->groupBy('monitoring.id')
            ->get();

        // test weekly
        $weeks = [1, 2, 3, 4, 5];
        $weeksoy = collect($weeks)->map(function ($week) use ($id, $month, $today) {
            $data = MonitoringWeekly::join('users as u', 'u.id', '=', 'monitoring_weekly.id_pic')
                ->where('id_machine', $id)
                ->where('week', $week)
                ->whereMonth('monitoring_weekly.date', $month)
                ->whereYear('monitoring_weekly.date', $today->year)
                ->select('monitoring_weekly.*', 'u.name')
                ->first();

            return $data ?? [
                'id' => '-',
                'id_pic' => '-',
                'id_machine' => '-',
                'condition' => '-',
                'voltage' => '-',
                'ampere' => '-',
                'vibration' => '-',
                'idle' => '-',
                'week' => '-',
                'drain' => '-',
                'pre' => '-',
                'cooler' => 0,
                'coupling' => 2,
                'area' => 0,
                'condensor' => 0,
                'after' => '-',
                'desc' => '-',
                'type' => '-',
                'date' => '-',
                'name' => '-',
            ];
        })->toArray();
        // dd($weeksoy);
        // $mainlog = Mainlog::join('users as u', 'u.id', '-', 'main_log.id_teknisi')->where('id_monitoring', $id)->whereMonth('date', $month)->get();
        $mainlog = Mainlog::join('users as u', 'u.id', '=', 'main_log.id_teknisi')->where('id_machine', $id)->whereMonth('date', $today->month)->whereYear('date', $today->year)->whereNotNull('desc')->select('main_log.*', 'u.name')->orderBy('main_log.date', 'asc')->get();
        // dd($compressor);
        $weekly = MonitoringWeekly::where('id_machine', $id)->whereMonth('date', $month)->whereYear('date', $today->year)->get();
        $reports = Reports::where('id_machine', $id)->whereMonth('date', $month)->whereYear('date', $today->year)->orderBy('date')->get();

        $startDate = Carbon::create($today->year, $month, 1)->startOfWeek(Carbon::SUNDAY);
        $endDate = Carbon::create($today->year, $month, 1)->endOfMonth();
        $weeks = [];
        while ($startDate->lte($endDate)) {
            $weekNumber = $startDate->format('W'); // Nomor minggu dalam tahun
            $weeks[$weekNumber] = [
                // 'minggu' => "$weekNumber",
                'week' => '-',
                'condition' => '-',
                'voltage' => '-',
                'ampere' => '-',
                'idle' => '-',
                'vibration' => '-',
                'dew' => '-',
                'drain' => '-',
                'cooler' => '-',
                'coupling' => '-',
                'condensor' => '-',
                'area' => '-',
                'pre' => '-',
                'after' => '-',
                'desc' => '-',
                'date' => '-',
                'name' => '-'
            ];
            $startDate->addWeek();
        }

        // Isi data yang ada ke dalam minggu
        foreach ($weekly as $item) {
            $weekNumber = Carbon::parse($item->date)->format('W');
            $weeks[$weekNumber]['week'] = $item->week;
            $weeks[$weekNumber]['condition'] = $item->condition;
            $weeks[$weekNumber]['voltage'] = $item->voltage;
            $weeks[$weekNumber]['ampere'] = $item->ampere;
            $weeks[$weekNumber]['idle'] = $item->idle;
            $weeks[$weekNumber]['vibration'] = $item->vibration;
            $weeks[$weekNumber]['dew'] = $item->dew;
            $weeks[$weekNumber]['cooler'] = $item->cooler;
            $weeks[$weekNumber]['coupling'] = $item->coupling;
            $weeks[$weekNumber]['area'] = $item->area;
            $weeks[$weekNumber]['condensor'] = $item->condensor;
            $weeks[$weekNumber]['drain'] = $item->drain;
            $weeks[$weekNumber]['pre'] = $item->pre;
            $weeks[$weekNumber]['after'] = $item->after;
            $weeks[$weekNumber]['date'] = $item->date;
            $weeks[$weekNumber]['desc'] = $item->desc;
            $weeks[$weekNumber]['name'] = $item->pic->name;
        }
        // dd($weeks);

        $monthly = MonitoringMonthly::whereMonth('date', $month)->where('id_machine', $id)->first();
        $quotes = Quotation::join('detail_quotation as d', 'd.id_quotation', '=', 'quotation.id')->where('d.id_equivalent', $id)->whereMonth('estimated_date', $month)->get();
        // dd($monthly);

        $teknisi = User::get();
        // dd($teknisi);

        return view('pages.monitoring.service-visitor', compact('teknisi', 'quotes', 'monthly', 'machine', 'client', 'compressor', 'dryer', 'months', 'issue', 'mainlog', 'weekly', 'reports', 'weeksoy'));
    }
    public function visitorDailyServicePrintProkemas($id, $month)
    {
        $machine = Machine::find($id);
        $client = Client::find($machine->id_client);

        $setday = Carbon::today();
        $today = $setday->setMonth($month);

        $startOfMonth = $today->copy()->startOfMonth();
        $startOfMonthDate = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $dates = [];
        for ($date = $startOfMonthDate; $date->lte($endOfMonth); $date->addDay()) {
            $dates[] = $date->format('d-m-Y');
        }
        // dd($dates);

        // Ambil data monitoring dari database
        $monitoringData = Monitoring::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('id_machine', $id)
            ->join('users as u', 'u.id', '=', 'monitoring.id_pic')
            ->get(['monitoring.*', 'u.name'])
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date)->format('d-m-Y'); // Format tanggal
                return $item;
            });
        // dd($monitoringData);

        $compressorIndexed = $monitoringData->keyBy('date')->map(function ($item) {
            return [
                'id' => $item->id ?? '-',
                'id_machine' => $item->id_machine ?? '-',
                'running' => $item->running ?? '-',
                'loading' => $item->loading ?? '-',
                'pressure' => $item->pressure ?? '-',
                'temp' => $item->temp ?? '-',
                'condition' => $item->condition ?? '-',
                'oil_level' => $item->oil_level ?? '-',
                'leak' => $item->leak ?? '-',
                'pic' => $item->name ?? '-',
                'desc' => $item->desc ?? '-',
                'main_desc' => $item->main_desc ?? '-',
            ];
        })->toArray();
        // dd($compressorIndexed);

        // Gabungkan data monitoring dengan daftar tanggal
        $compressor = [];
        foreach ($dates as $date) {
            $compressor[] = [
                'date' => $date,
                'id' => $compressorIndexed[$date]['id'] ?? '-',
                'id_machine' => $compressorIndexed[$date]['id_machine'] ?? '-',
                'running' => $compressorIndexed[$date]['running'] ?? '-',
                'loading' => $compressorIndexed[$date]['loading'] ?? '-',
                'pressure' => $compressorIndexed[$date]['pressure'] ?? '-',
                'temp' => $compressorIndexed[$date]['temp'] ?? '-',
                'condition' => $compressorIndexed[$date]['condition'] ?? '-',
                'oil_level' => $compressorIndexed[$date]['oil_level'] ?? '-',
                'leak' => $compressorIndexed[$date]['leak'] ?? '-',
                'pic' => $compressorIndexed[$date]['pic'] ?? '-',
                'desc' => $compressorIndexed[$date]['desc'] ?? '-',
                'main_desc' => $compressorIndexed[$date]['main_desc'] ?? '-',
            ];
        }
        // dd($compressor);

        $dryerIndexed = $monitoringData->keyBy('date')->map(function ($item) {
            return [
                'id' => $item->id ?? '-',
                'id_machine' => $item->id_machine ?? '-',
                'temp' => $item->temp ?? '-',
                'temp_out' => $item->temp_out ?? '-',
                'dew' => $item->dew ?? '-',
                'drain' => $item->drain ?? '-',
                'condition' => $item->condition ?? '-',
                'oil_level' => $item->oil_level ?? '-',
                'leak' => $item->leak ?? '-',
                'fan' => $item->fan ?? '-',
                'desc' => $item->desc ?? '-',
                'main_desc' => $item->main_desc ?? '-',
                'pic' => $item->name ?? '-',
            ];
        })->toArray();

        // Gabungkan data monitoring dengan daftar tanggal
        $dryer = [];
        foreach ($dates as $date) {
            $dryer[] = [
                'date' => $date,
                'id' => $dryerIndexed[$date]['id'] ?? '-',
                'id_machine' => $compressorIndexed[$date]['id_machine'] ?? '-',
                'temp' => $dryerIndexed[$date]['temp'] ?? '-',
                'temp_out' => $dryerIndexed[$date]['temp_out'] ?? '-',
                'dew' => $dryerIndexed[$date]['dew'] ?? '-',
                'drain' => $dryerIndexed[$date]['drain'] ?? '-',
                'condition' => $dryerIndexed[$date]['condition'] ?? '-',
                'leak' => $dryerIndexed[$date]['leak'] ?? '-',
                'fan' => $dryerIndexed[$date]['fan'] ?? '-',
                'oil_level' => $dryerIndexed[$date]['oil_level'] ?? '-',
                'desc' => $dryerIndexed[$date]['desc'] ?? '-',
                'main_desc' => $dryerIndexed[$date]['main_desc'] ?? '-',
                'pic' => $dryerIndexed[$date]['pic'] ?? '-',
            ];
        }

        // Return data
        $monitoringThisMonth = response()->json($compressor);


        $issue = Monitoring::leftJoin('pn_monitoring as pn', 'pn.id_monitoring', '=', 'monitoring.id')
            ->join('users as u', 'u.id', '=', 'monitoring.id_pic')
            ->where('id_machine', $id)
            ->whereNot('issue', '-')
            ->whereNot('issue', 'normal')
            ->whereNot('issue', 'Normal')
            ->whereNotNull('issue')
            ->whereMonth('monitoring.date', $month)
            ->select(
                'monitoring.*',
                'u.name',
                DB::raw("IFNULL(GROUP_CONCAT(pn.pn SEPARATOR ' | '), '-') as pn")
            )
            ->groupBy('monitoring.id')
            ->get();
        // dd($id);
        // $mainlog = Monitoring::join('users as u', 'u.id', '=', 'monitoring.id_pic')->where('id_machine', $id)->whereMonth('date', $month)->whereNot('main_desc', '-')->whereNot('main_desc', 'normal')->whereNot('main_desc', 'Normal')->whereNotNull('main_desc')->select('monitoring.*', 'u.name')->get();
        $mainlog = Mainlog::join('users as u', 'u.id', '=', 'main_log.id_teknisi')->where('id_machine', $id)->whereMonth('date', $today->month)->whereNotNull('desc')->select('main_log.*', 'u.name')->get();

        $weekly = MonitoringWeekly::where('id_machine', $id)->whereMonth('date', $month)->whereYear('date', $today->year)->get();
        $reports = Reports::where('id_machine', $id)->whereMonth('date', $month)->whereYear('date', $today->year)->orderBy('date')->get();

        $startDate = Carbon::create($today->year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $weeks = [];
        while ($startDate->lte($endDate)) {
            $weekNumber = $startDate->format('W'); // Nomor minggu dalam tahun
            $weeks[$weekNumber] = [
                'minggu' => "$weekNumber",
                'week' => '-',
                'condition' => '-',
                'voltage' => '-',
                'ampere' => '-',
                'idle' => '-',
                'vibration' => '-',
                'cooler' => '-',
                'coupling' => '-',
                'condensor' => '-',
                'area' => '-',
                'dew' => '-',
                'drain' => '-',
                'pre' => '-',
                'after' => '-',
                'desc' => '-',
                'name' => '-'
            ];
            $startDate->addWeek();
        }

        // Isi data yang ada ke dalam minggu
        foreach ($weekly as $item) {
            $weekNumber = Carbon::parse($item->date)->format('W');
            $weeks[$weekNumber]['week'] = $item->week;
            $weeks[$weekNumber]['condition'] = $item->condition;
            $weeks[$weekNumber]['voltage'] = $item->voltage;
            $weeks[$weekNumber]['ampere'] = $item->ampere;
            $weeks[$weekNumber]['idle'] = $item->idle;
            $weeks[$weekNumber]['vibration'] = $item->vibration;
            $weeks[$weekNumber]['dew'] = $item->dew;
            $weeks[$weekNumber]['drain'] = $item->drain;
            $weeks[$weekNumber]['cooler'] = $item->cooler;
            $weeks[$weekNumber]['coupling'] = $item->coupling;
            $weeks[$weekNumber]['area'] = $item->area;
            $weeks[$weekNumber]['condensor'] = $item->condensor;
            $weeks[$weekNumber]['pre'] = $item->pre;
            $weeks[$weekNumber]['after'] = $item->after;
            $weeks[$weekNumber]['desc'] = $item->desc;
            $weeks[$weekNumber]['name'] = $item->pic->name;
        }
        // dd($weeks);
        $monthly = MonitoringMonthly::whereMonth('date', $month)->where('id_machine', $id)->first();
        $quotes = Quotation::where('id_monitoring', $id)->get();

        return view('pages.monitoring.service-visitor-print-prokemas', compact('quotes', 'monthly', 'machine', 'client', 'compressor', 'dryer', 'issue', 'mainlog', 'weekly', 'reports', 'weeks'));
    }
    public function visitorDailyServicePrint($id, $month)
    {
        $machine = Machine::find($id);
        $client = Client::find($machine->id_client);

        $setday = Carbon::today();
        $today = $setday->setMonth($month);

        $startOfMonth = $today->copy()->startOfMonth();
        $startOfMonthDate = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $dates = [];
        for ($date = $startOfMonthDate; $date->lte($endOfMonth); $date->addDay()) {
            $dates[] = $date->format('d-m-Y');
        }
        // dd($dates);

        // Ambil data monitoring dari database
        $monitoringData = Monitoring::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('id_machine', $id)
            ->join('users as u', 'u.id', '=', 'monitoring.id_pic')
            ->get(['monitoring.*', 'u.name'])
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date)->format('d-m-Y'); // Format tanggal
                return $item;
            });
        // dd($monitoringData);

        $compressorIndexed = $monitoringData->keyBy('date')->map(function ($item) {
            return [
                'id' => $item->id ?? '-',
                'id_machine' => $item->id_machine ?? '-',
                'running' => $item->running ?? '-',
                'loading' => $item->loading ?? '-',
                'pressure' => $item->pressure ?? '-',
                'temp' => $item->temp ?? '-',
                'condition' => $item->condition ?? '-',
                'oil_level' => $item->oil_level ?? '-',
                'leak' => $item->leak ?? '-',
                'pic' => $item->name ?? '-',
                'desc' => $item->desc ?? '-',
                'main_desc' => $item->main_desc ?? '-',
            ];
        })->toArray();
        // dd($compressorIndexed);

        // Gabungkan data monitoring dengan daftar tanggal
        $compressor = [];
        foreach ($dates as $date) {
            $compressor[] = [
                'date' => $date,
                'id' => $compressorIndexed[$date]['id'] ?? '-',
                'id_machine' => $compressorIndexed[$date]['id_machine'] ?? '-',
                'running' => $compressorIndexed[$date]['running'] ?? '-',
                'loading' => $compressorIndexed[$date]['loading'] ?? '-',
                'pressure' => $compressorIndexed[$date]['pressure'] ?? '-',
                'temp' => $compressorIndexed[$date]['temp'] ?? '-',
                'condition' => $compressorIndexed[$date]['condition'] ?? '-',
                'oil_level' => $compressorIndexed[$date]['oil_level'] ?? '-',
                'leak' => $compressorIndexed[$date]['leak'] ?? '-',
                'pic' => $compressorIndexed[$date]['pic'] ?? '-',
                'desc' => $compressorIndexed[$date]['desc'] ?? '-',
                'main_desc' => $compressorIndexed[$date]['main_desc'] ?? '-',
            ];
        }
        // dd($compressor);

        $dryerIndexed = $monitoringData->keyBy('date')->map(function ($item) {
            return [
                'id' => $item->id ?? '-',
                'id_machine' => $item->id_machine ?? '-',
                'temp' => $item->temp ?? '-',
                'temp_out' => $item->temp_out ?? '-',
                'dew' => $item->dew ?? '-',
                'drain' => $item->drain ?? '-',
                'condition' => $item->condition ?? '-',
                'oil_level' => $item->oil_level ?? '-',
                'leak' => $item->leak ?? '-',
                'fan' => $item->fan ?? '-',
                'desc' => $item->desc ?? '-',
                'main_desc' => $item->main_desc ?? '-',
                'pic' => $item->name ?? '-',
            ];
        })->toArray();

        // Gabungkan data monitoring dengan daftar tanggal
        $dryer = [];
        foreach ($dates as $date) {
            $dryer[] = [
                'date' => $date,
                'id' => $dryerIndexed[$date]['id'] ?? '-',
                'id_machine' => $compressorIndexed[$date]['id_machine'] ?? '-',
                'temp' => $dryerIndexed[$date]['temp'] ?? '-',
                'temp_out' => $dryerIndexed[$date]['temp_out'] ?? '-',
                'dew' => $dryerIndexed[$date]['dew'] ?? '-',
                'drain' => $dryerIndexed[$date]['drain'] ?? '-',
                'condition' => $dryerIndexed[$date]['condition'] ?? '-',
                'leak' => $dryerIndexed[$date]['leak'] ?? '-',
                'fan' => $dryerIndexed[$date]['fan'] ?? '-',
                'oil_level' => $dryerIndexed[$date]['oil_level'] ?? '-',
                'desc' => $dryerIndexed[$date]['desc'] ?? '-',
                'main_desc' => $dryerIndexed[$date]['main_desc'] ?? '-',
                'pic' => $dryerIndexed[$date]['pic'] ?? '-',
            ];
        }

        // Return data
        $monitoringThisMonth = response()->json($compressor);


        $issue = Monitoring::leftJoin('pn_monitoring as pn', 'pn.id_monitoring', '=', 'monitoring.id')
            ->join('users as u', 'u.id', '=', 'monitoring.id_pic')
            ->where('id_machine', $id)
            ->whereNot('issue', '-')
            ->whereNot('issue', 'normal')
            ->whereNot('issue', 'Normal')
            ->whereNotNull('issue')
            ->whereMonth('monitoring.date', $month)
            ->select(
                'monitoring.*',
                'u.name',
                DB::raw("IFNULL(GROUP_CONCAT(pn.pn SEPARATOR ' | '), '-') as pn")
            )
            ->groupBy('monitoring.id')
            ->get();
        // dd($id);
        // $mainlog = Monitoring::join('users as u', 'u.id', '=', 'monitoring.id_pic')->where('id_machine', $id)->whereMonth('date', $month)->whereNot('main_desc', '-')->whereNot('main_desc', 'normal')->whereNot('main_desc', 'Normal')->whereNotNull('main_desc')->select('monitoring.*', 'u.name')->get();
        $mainlog = Mainlog::join('users as u', 'u.id', '=', 'main_log.id_teknisi')->where('id_machine', $id)->whereMonth('date', $today->month)->whereNotNull('desc')->select('main_log.*', 'u.name')->get();

        $weekly = MonitoringWeekly::where('id_machine', $id)->whereMonth('date', $month)->whereYear('date', $today->year)->get();
        $reports = Reports::where('id_machine', $id)->whereMonth('date', $month)->whereYear('date', $today->year)->orderBy('date')->get();

        $startDate = Carbon::create($today->year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $weeks = [];
        while ($startDate->lte($endDate)) {
            $weekNumber = $startDate->format('W'); // Nomor minggu dalam tahun
            $weeks[$weekNumber] = [
                'minggu' => "$weekNumber",
                'week' => '-',
                'condition' => '-',
                'voltage' => '-',
                'ampere' => '-',
                'idle' => '-',
                'vibration' => '-',
                'cooler' => '-',
                'coupling' => '-',
                'condensor' => '-',
                'area' => '-',
                'dew' => '-',
                'drain' => '-',
                'pre' => '-',
                'after' => '-',
                'desc' => '-',
                'name' => '-'
            ];
            $startDate->addWeek();
        }

        // Isi data yang ada ke dalam minggu
        foreach ($weekly as $item) {
            $weekNumber = Carbon::parse($item->date)->format('W');
            $weeks[$weekNumber]['week'] = $item->week;
            $weeks[$weekNumber]['condition'] = $item->condition;
            $weeks[$weekNumber]['voltage'] = $item->voltage;
            $weeks[$weekNumber]['ampere'] = $item->ampere;
            $weeks[$weekNumber]['idle'] = $item->idle;
            $weeks[$weekNumber]['vibration'] = $item->vibration;
            $weeks[$weekNumber]['dew'] = $item->dew;
            $weeks[$weekNumber]['drain'] = $item->drain;
            $weeks[$weekNumber]['cooler'] = $item->cooler;
            $weeks[$weekNumber]['coupling'] = $item->coupling;
            $weeks[$weekNumber]['area'] = $item->area;
            $weeks[$weekNumber]['condensor'] = $item->condensor;
            $weeks[$weekNumber]['pre'] = $item->pre;
            $weeks[$weekNumber]['after'] = $item->after;
            $weeks[$weekNumber]['desc'] = $item->desc;
            $weeks[$weekNumber]['name'] = $item->pic->name;
        }
        // dd($weeks);
        $monthly = MonitoringMonthly::whereMonth('date', $month)->where('id_machine', $id)->first();
        $quotes = Quotation::where('id_monitoring', $id)->get();

        return view('pages.monitoring.service-visitor-print', compact('quotes', 'monthly', 'machine', 'client', 'compressor', 'dryer', 'issue', 'mainlog', 'weekly', 'reports', 'weeks'));
    }
    public function visitorWeeklyService($id, $week)
    {
        $machine = Machine::find($id);
        $client = Client::find($machine->id_client);
        $machineC = Client::join('machine as m', 'client.id', '=', 'm.id_client')->groupBy('client.id')->whereNotBetween('m.id', [473, 481])->get('m.*');
        $monitoringAC = Machine::leftJoin('monitoring_weekly as mw', 'mw.id_machine', '=', 'machine.id')
            ->join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->join('unit as u', 'u.id', '=', 'sp.id_product')
            ->leftJoin('users as us', 'us.id', '=', 'mw.id_pic')
            ->whereNotBetween('machine.id', [472, 481])
            ->where('u.unit', 'AIR COMPRESSOR SCREW')
            ->where(function ($query) use ($week) {
                $query->where('mw.week', $week)
                    ->orWhereNull('mw.week'); // Tetap tampilkan jika mw.week kosong
            })
            ->where('machine.id_client', $client->id)
            ->select('machine.*', 'mw.*', 'us.name', 'machine.id as idM')
            ->groupBy('machine.id')
            ->get();
        $monitoringDRYER = Machine::leftJoin('monitoring_weekly as mw', 'mw.id_machine', '=', 'machine.id')
            ->join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->join('unit as u', 'u.id', '=', 'sp.id_product')
            ->leftJoin('users as us', 'us.id', '=', 'mw.id_pic')
            ->whereNotBetween('machine.id', [472, 481])
            ->where('u.unit', 'REFRIGERANT AIR DRYER')
            ->where(function ($query) use ($week) {
                $query->where('mw.week', $week)
                    ->orWhereNull('mw.week'); // Tetap tampilkan jika mw.week kosong
            })
            ->where('machine.id_client', $client->id)
            ->select('machine.*', 'mw.*', 'us.name', 'machine.id as idM')
            ->groupBy('machine.id')
            ->get();

        // $dateMonitoring = $monitoringAC->date;
        // $year = Carbon::createFromFormat('Y', time: $dateMonitoring);

        $startDate = Carbon::now()->setISODate(2025, $week)->startOfWeek()->format('d-m-Y');
        $endDate = Carbon::now()->setISODate(2025, $week)->endOfWeek()->format('d-m-Y');
        // dd($startDate);
        return view('pages.monitoring.service-visitor-weekly', compact('machine', 'client', 'monitoringAC', 'monitoringDRYER', 'startDate', 'endDate'));
    }
    public function visitorWeeklyServicePrint($id, $week)
    {
        $machine = Machine::find($id);
        $client = Client::find($machine->id_client);
        $machineC = Client::join('machine as m', 'client.id', '=', 'm.id_client')->groupBy('client.id')->whereNotBetween('m.id', [472, 481])->get('m.*');
        $monitoringAC = Machine::leftJoin('monitoring_weekly as mw', 'mw.id_machine', '=', 'machine.id')
            ->join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->join('unit as u', 'u.id', '=', 'sp.id_product')
            ->leftJoin('users as us', 'us.id', '=', 'mw.id_pic')
            ->whereNotBetween('machine.id', [472, 481])
            ->where('u.unit', 'AIR COMPRESSOR SCREW')
            ->where(function ($query) use ($week) {
                $query->where('mw.week', $week)
                    ->orWhereNull('mw.week'); // Tetap tampilkan jika mw.week kosong
            })
            ->where('machine.id_client', $client->id)
            ->select('machine.*', 'mw.*', 'us.name', 'machine.id as idM')
            ->groupBy('machine.id')
            ->get();
        $monitoringDRYER = Machine::leftJoin('monitoring_weekly as mw', 'mw.id_machine', '=', 'machine.id')
            ->join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->join('unit as u', 'u.id', '=', 'sp.id_product')
            ->leftJoin('users as us', 'us.id', '=', 'mw.id_pic')
            ->whereNotBetween('machine.id', [472, 481])
            ->where('u.unit', 'REFRIGERANT AIR DRYER')
            ->where(function ($query) use ($week) {
                $query->where('mw.week', $week)
                    ->orWhereNull('mw.week'); // Tetap tampilkan jika mw.week kosong
            })
            ->where('machine.id_client', $client->id)
            ->select('machine.*', 'mw.*', 'us.name', 'machine.id as idM')
            ->groupBy('machine.id')
            ->get();
        // dd($monitoringAC);
        return view('pages.monitoring.service-visitor-print-weekly', compact('machine', 'client', 'monitoringAC', 'monitoringDRYER'));
    }
    public function visitorMonthlyService($id, $month)
    {
        $today = Carbon::today();
        $thisMonth = $today->setMonth($month)->format('F');
        $machine = Machine::find($id);
        $client = Client::find($machine->id_client);
        $machineC = Client::join('machine as m', 'client.id', '=', 'm.id_client')->groupBy('client.id')->whereNotBetween('m.id', [472, 481])->get('m.*');
        // $monitoringAC = Machine::leftJoin('monitoring_monthly as mm', 'mm.id_machine', '=', 'machine.id')
        //     ->join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
        //     ->join('unit as u', 'u.id', '=', 'sp.id_product')
        //     ->leftJoin('users as us', 'us.id', '=', 'mm.id_pic')
        //     ->whereNotBetween('machine.id', [472, 481])
        //     ->where('u.unit', 'AIR COMPRESSOR SCREW')
        //     ->where('machine.id_client', $client->id)
        //     ->select('machine.*', 'mm.*', 'us.name', 'machine.id as idM')
        //     ->groupBy('machine.id')
        //     ->get();
        $monitoringDRYER = Machine::leftJoin('monitoring_monthly as mm', 'mm.id_machine', '=', 'machine.id')
            ->leftJoin('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->leftJoin('unit as u', 'u.id', '=', 'sp.id_product')
            ->leftJoin('users as us', 'us.id', '=', 'mm.id_pic')
            ->whereNotBetween('machine.id', [472, 481])
            ->where('u.unit', 'REFRIGERANT AIR DRYER')
            ->where('machine.id_client', $client->id)
            ->where(function ($query) use ($month) {
                $query->whereMonth('mm.date', $month)->orWhereNull('mm.date');
            })
            ->select('machine.*', 'mm.*', 'us.name', 'machine.id as idM')
            ->groupBy('machine.id')
            ->get();

        // $dateMonitoring = $monitoringAC->date;
        // $year = Carbon::createFromFormat('Y', time: $dateMonitoring);

        // $startDate = Carbon::now()->setISODate(2025, $week)->startOfWeek()->format('d-m-Y');
        // $endDate = Carbon::now()->setISODate(2025, $week)->endOfWeek()->format('d-m-Y');
        // dd($monitoringDRYER);
        return view('pages.monitoring.service-visitor-monthly', compact('machine', 'client', 'monitoringDRYER', 'thisMonth'));
    }
    public function visitorMonthlyServicePrint($id, $month)
    {
        $today = Carbon::today();
        $thisMonth = $today->setMonth($month)->format('F');
        $machine = Machine::find($id);
        $client = Client::find($machine->id_client);
        $machineC = Client::join('machine as m', 'client.id', '=', 'm.id_client')->groupBy('client.id')->whereNotBetween('m.id', [472, 481])->get('m.*');
        // $monitoringAC = Machine::leftJoin('monitoring_monthly as mm', 'mm.id_machine', '=', 'machine.id')
        //     ->join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
        //     ->join('unit as u', 'u.id', '=', 'sp.id_product')
        //     ->leftJoin('users as us', 'us.id', '=', 'mm.id_pic')
        //     ->whereNotBetween('machine.id', [472, 481])
        //     ->where('u.unit', 'AIR COMPRESSOR SCREW')
        //     ->where('machine.id_client', $client->id)
        //     ->select('machine.*', 'mm.*', 'us.name', 'machine.id as idM')
        //     ->groupBy('machine.id')
        //     ->get();
        $monitoringDRYER = Machine::leftJoin('monitoring_monthly as mm', 'mm.id_machine', '=', 'machine.id')
            ->leftJoin('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->leftJoin('unit as u', 'u.id', '=', 'sp.id_product')
            ->leftJoin('users as us', 'us.id', '=', 'mm.id_pic')
            ->whereNotBetween('machine.id', [472, 481])
            ->where('u.unit', 'REFRIGERANT AIR DRYER')
            ->where('machine.id_client', $client->id)
            ->where(function ($query) use ($month) {
                $query->whereMonth('mm.date', $month)->orWhereNull('mm.date');
            })
            ->select('machine.*', 'mm.*', 'us.name', 'machine.id as idM')
            ->groupBy('machine.id')
            ->get();

        // $dateMonitoring = $monitoringAC->date;
        // $year = Carbon::createFromFormat('Y', time: $dateMonitoring);

        // $startDate = Carbon::now()->setISODate(2025, $week)->startOfWeek()->format('d-m-Y');
        // $endDate = Carbon::now()->setISODate(2025, $week)->endOfWeek()->format('d-m-Y');
        // dd($monitoringDRYER);
        return view('pages.monitoring.service-visitor-print-monthly', compact('machine', 'client', 'monitoringDRYER', 'thisMonth'));
    }
    public function issueMachine($date)
    {
        $day = Carbon::createFromFormat('d-m-Y', time: $date);
        $month = $day->month;
        $year = $day->year;

        $machines = Machine::with([
            'unit',
            'unit.unit',
            'monitoring' => function ($query) use ($month, $year) {
                $query->whereMonth('date', $month)
                    ->whereYear('date', $year);
            },
            'mainlog' => function ($query) use ($month, $year) {
                $query->whereMonth('date', $month)
                    ->whereYear('date', $year);
            }
        ])
            ->whereNotBetween('machine.id', [472, 481])
            ->where('id_client', 1277)
            ->orderBy('machine.location')
            ->get();

        $result = $machines->filter(function ($machine) {
            return $machine->monitoring->isNotEmpty();
        })->map(function ($machine) {
            return [
                'machine' => $machine->unit->brand . ' ' . $machine->unit->unit->sku . ' - ' . $machine->tag . ' - ' . $machine->location,
                'id' => $machine->id,
                'log' => $machine->monitoring->filter(function ($log) {
                    return !is_null($log->issue) && $log->issue !== '-' && $log->issue !== 'normal' && $log->issue !== 'Normal';
                })->map(function ($log) {
                    return [
                        'log' => $log->issue,
                        'date' => \Carbon\Carbon::parse($log->date)->format('d'),
                        'pic' => $log->pic->name
                    ];
                }),
                'mainlog' => $machine->mainlog->filter(function ($mainlog) {
                    return !is_null($mainlog->desc) && $mainlog->desc !== '-';
                })->map(function ($mainlog) {
                    return [
                        'id' => $mainlog->id,
                        'id_pic' => $mainlog->id_teknisi,
                        'id_machine' => $mainlog->machine->id,
                        'id_service' => optional($mainlog->issue?->reports->first())->id,
                        'log' => $mainlog->desc,
                        'date' => \Carbon\Carbon::parse($mainlog->date)->format('d'),
                        'technician' => $mainlog->teknisi->name
                    ];
                }),
            ];
        });

        // dd($machines);

        // $mainlog = $machinesmain->filter(function ($machine) {
        //     return $machine->monitoring->isNotEmpty();
        // })->map(function ($machine) {
        //     return [
        //         'machine' => $machine->unit->brand . ' ' . $machine->unit->unit->sku . ' - ' . $machine->tag . ' - ' . $machine->location,
        //         'log' => $machine->monitoring->map(function ($log) {
        //             return ['mainlog' => $log->main_desc, 'date' => \Carbon\Carbon::parse($log->date)->format('d'), 'technician' => $log->technician];
        //         })
        //     ];
        // });
        // dd($result);
        $dateRec = $day->format('d-m-Y');
        return view('pages.monitoring.issue', compact('result', 'month', 'year', 'day', 'dateRec'));
    }

    public function recapDay($month, $year)
    {
        $tanggal = $month . ' ' . $year;
        return view('pages.monitoring.recap-day', compact('tanggal'));
    }
    public function recapWeek($month, $year)
    {
        $tanggal = $month . ' ' . $year;
        return view('pages.monitoring.recap-week', compact('tanggal'));
    }
    public function recapM($date)
    {
        try {
            $date = Carbon::createFromFormat('Y-m-d', $date);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format, expected Y-m-d'], 400);
        }
        $date = $date ? Carbon::parse($date) : Carbon::today();
        $allPlant = Machine::where('id_client', 1277)->whereNotBetween('machine.id', [472, 481])->count();
        $allPlantMonitoring = Machine::where('id_client', 1277)->whereNotBetween('machine.id', [472, 481])
            ->whereHas('monitoring', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->count();
        $GT = Machine::where('id_client', 1277)->where('location', 'GT 1-2')->whereNotBetween('machine.id', [472, 481])->count();
        $GTMonitoring = Machine::where('id_client', 1277)->where('location', 'GT 1-2')->whereNotBetween('machine.id', [472, 481])
            ->whereHas('monitoring', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->count();
        $GT3 = Machine::where('id_client', 1277)->where('location', 'GT3/BOILER')->whereNotBetween('machine.id', [472, 481])->count();
        $GT3Monitoring = Machine::where('id_client', 1277)->where('location', 'GT3/BOILER')->whereNotBetween('machine.id', [472, 481])
            ->whereHas('monitoring', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->count();
        $INC = Machine::where('id_client', 1277)->where('location', 'INC')->whereNotBetween('machine.id', [472, 481])->count();
        $INCMonitoring = Machine::where('id_client', 1277)->where('location', 'INC')->whereNotBetween('machine.id', [472, 481])
            ->whereHas('monitoring', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->count();
        $PM12 = Machine::where('id_client', 1277)->where('location', 'BM 1-2')->whereNotBetween('machine.id', [472, 481])->count();
        $PM12Monitoring = Machine::where('id_client', 1277)->where('location', 'BM 1-2')->whereNotBetween('machine.id', [472, 481])
            ->whereHas('monitoring', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->count();
        $PM35 = Machine::where('id_client', 1277)->whereBetween('location', ['BM 3', 'BM 5'])->whereNotBetween('machine.id', [472, 481])->count();
        $PM35Monitoring = Machine::where('id_client', 1277)->whereBetween('location', ['BM 3', 'BM 5'])->whereNotBetween('machine.id', [472, 481])
            ->whereHas('monitoring', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->count();
        $PM78 = Machine::where('id_client', 1277)->whereBetween('location', ['BM 7', 'BM 8'])->whereNotBetween('machine.id', [472, 481])->count();
        $PM78Monitoring = Machine::where('id_client', 1277)->whereBetween('location', ['BM 7', 'BM 8'])->whereNotBetween('machine.id', [472, 481])
            ->whereHas('monitoring', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->count();
        // $mesin = Machine::where('id_client', 1277)->rightJoin('monitoring as m', 'm.id_machine', '=' ,'machine.id')->whereDate('m.date', $date)->get();
        $mesinDryer = Machine::leftJoin('monitoring as m', 'machine.id', '=', 'm.id_machine')
            ->join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->join('unit as u', 'u.id', '=', 'sp.id_product')
            ->leftJoin('users as us', 'us.id', '=', 'm.id_pic')
            ->where('machine.id_client', 1277)
            ->where('u.unit', 'REFRIGERANT AIR DRYER')
            ->whereDate('m.date', $date)
            ->orderBy('machine.location')
            ->select(
                DB::raw("CONCAT(sp.brand, ' ', u.sku) as brand_type"),
                'm.*',
                'machine.*',
                'us.name'
            )
            ->get();
        // dd($mesinDryer);
        return view('pages.monitoring.recap', compact('mesinDryer', 'allPlant', 'allPlantMonitoring', 'GT', 'GTMonitoring', 'GT3', 'GT3Monitoring', 'INC', 'INCMonitoring', 'PM12', 'PM12Monitoring', 'PM35', 'PM35Monitoring', 'PM78', 'PM78Monitoring', 'date'));
    }
    public function recapMW($week, $date)
    {
        try {
            $date = Carbon::createFromFormat('Y-m-d', $date);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format, expected Y-m-d'], 400);
        }
        $date = $date ? Carbon::parse($date) : Carbon::today();
        $month = $date->month;
        $year = $date->year;
        $allPlant = Machine::where('id_client', 1277)->whereNotBetween('machine.id', [472, 481])->count();
        $allPlantMonitoring = Machine::where('id_client', 1277)->whereNotBetween('machine.id', [472, 481])
            ->withCount([
                'monitoringW as monitoring_count' => function ($query) use ($week, $month, $year) {
                    $query->where('week', $week)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $year);
                }
            ])
            ->get()
            ->sum('monitoring_count');
        $GT = Machine::where('id_client', 1277)->where('location', 'GT 1-2')->whereNotBetween('machine.id', [472, 481])->count();
        $GTMonitoring = Machine::where('id_client', 1277)->where('location', 'GT 1-2')->whereNotBetween('machine.id', [472, 481])
            ->withCount([
                'monitoringW as monitoring_count' => function ($query) use ($week, $month, $year) {
                    $query->where('week', $week)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $year);
                }
            ])
            ->get()
            ->sum('monitoring_count');
        $GT3 = Machine::where('id_client', 1277)->where('location', 'GT3/BOILER')->whereNotBetween('machine.id', [472, 481])->count();
        $GT3Monitoring = Machine::where('id_client', 1277)->where('location', 'GT3/BOILER')->whereNotBetween('machine.id', [472, 481])
            ->withCount([
                'monitoringW as monitoring_count' => function ($query) use ($week, $month, $year) {
                    $query->where('week', $week)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $year);
                }
            ])
            ->get()
            ->sum('monitoring_count');
        $INC = Machine::where('id_client', 1277)->where('location', 'INC')->whereNotBetween('machine.id', [472, 481])->count();
        $INCMonitoring = Machine::where('id_client', 1277)->where('location', 'INC')->whereNotBetween('machine.id', [472, 481])
            ->withCount([
                'monitoringW as monitoring_count' => function ($query) use ($week, $month, $year) {
                    $query->where('week', $week)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $year);
                }
            ])
            ->get()
            ->sum('monitoring_count');
        $PM12 = Machine::where('id_client', 1277)->where('location', 'BM 1-2')->whereNotBetween('machine.id', [472, 481])->count();
        $PM12Monitoring = Machine::where('id_client', 1277)->where('location', 'BM 1-2')->whereNotBetween('machine.id', [472, 481])
            ->withCount([
                'monitoringW as monitoring_count' => function ($query) use ($week, $month, $year) {
                    $query->where('week', $week)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $year);
                }
            ])
            ->get()
            ->sum('monitoring_count');
        $PM35 = Machine::where('id_client', 1277)->whereBetween('location', ['BM 3', 'BM 5'])->whereNotBetween('machine.id', [472, 481])->count();
        $PM35Monitoring = Machine::where('id_client', 1277)->whereBetween('location', ['BM 3', 'BM 5'])->whereNotBetween('machine.id', [472, 481])
            ->withCount([
                'monitoringW as monitoring_count' => function ($query) use ($week, $month, $year) {
                    $query->where('week', $week)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $year);
                }
            ])
            ->get()
            ->sum('monitoring_count');
        $PM78 = Machine::where('id_client', 1277)->whereBetween('location', ['BM 7', 'BM 8'])->whereNotBetween('machine.id', [472, 481])->count();
        $PM78Monitoring = Machine::where('id_client', 1277)->whereBetween('location', ['BM 7', 'BM 8'])->whereNotBetween('machine.id', [472, 481])
            ->withCount([
                'monitoringW as monitoring_count' => function ($query) use ($week, $month, $year) {
                    $query->where('week', $week)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $year);
                }
            ])
            ->get()
            ->sum('monitoring_count');
        $mesinDryer = Machine::leftJoin('monitoring_weekly as m', function ($join) use ($week, $month, $year) {
            $join->on('machine.id', '=', 'm.id_machine')
                ->where('m.week', $week)
                ->where(function ($q) use ($month, $year) {
                    $q->whereNull('m.date') // kalau null, mesin tetap muncul
                        ->orWhere(function ($q2) use ($month, $year) {
                            $q2->whereMonth('m.date', $month)
                                ->whereYear('m.date', $year);
                        });
                });
        })
            ->join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->join('unit as u', 'u.id', '=', 'sp.id_product')
            ->leftJoin('users as us', 'us.id', '=', 'm.id_pic')
            ->where('machine.id_client', 1277)
            ->where('u.unit', 'REFRIGERANT AIR DRYER')
            ->orderBy('machine.location')
            ->select(
                'machine.*',
                DB::raw("CONCAT(sp.brand, ' ', u.sku) as brand_type"),
                'm.*',
                'us.name'
            )
            ->get();
        // dd($mesinDryer);
        return view('pages.monitoring.recapWeek', compact('mesinDryer', 'allPlant', 'allPlantMonitoring', 'GT', 'GTMonitoring', 'GT3', 'GT3Monitoring', 'INC', 'INCMonitoring', 'PM12', 'PM12Monitoring', 'PM35', 'PM35Monitoring', 'PM78', 'PM78Monitoring', 'date'));
    }
    public function recapClient()
    {
        $date = Carbon::today();
        $allPlant = Machine::where('id_client', 1277)->whereNotBetween('machine.id', [472, 481])->count();
        $allPlantMonitoring = Machine::where('id_client', 1277)->whereNotBetween('machine.id', [472, 481])
            ->whereHas('monitoring', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->count();
        $GT = Machine::where('id_client', 1277)->where('location', 'GT 1-2')->whereNotBetween('machine.id', [472, 481])->count();
        $GTMonitoring = Machine::where('id_client', 1277)->where('location', 'GT 1-2')->whereNotBetween('machine.id', [472, 481])
            ->whereHas('monitoring', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->count();
        $GT3 = Machine::where('id_client', 1277)->where('location', 'GT3/BOILER')->whereNotBetween('machine.id', [472, 481])->count();
        $GT3Monitoring = Machine::where('id_client', 1277)->where('location', 'GT3/BOILER')->whereNotBetween('machine.id', [472, 481])
            ->whereHas('monitoring', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->count();
        $INC = Machine::where('id_client', 1277)->where('location', 'INC')->whereNotBetween('machine.id', [472, 481])->count();
        $INCMonitoring = Machine::where('id_client', 1277)->where('location', 'INC')->whereNotBetween('machine.id', [472, 481])
            ->whereHas('monitoring', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->count();
        $PM12 = Machine::where('id_client', 1277)->where('location', 'BM 1-2')->whereNotBetween('machine.id', [472, 481])->count();
        $PM12Monitoring = Machine::where('id_client', 1277)->where('location', 'BM 1-2')->whereNotBetween('machine.id', [472, 481])
            ->whereHas('monitoring', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->count();
        $PM35 = Machine::where('id_client', 1277)->whereBetween('location', ['BM 3', 'BM 5'])->whereNotBetween('machine.id', [472, 481])->count();
        $PM35Monitoring = Machine::where('id_client', 1277)->whereBetween('location', ['BM 3', 'BM 5'])->whereNotBetween('machine.id', [472, 481])
            ->whereHas('monitoring', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->count();
        $PM78 = Machine::where('id_client', 1277)->whereBetween('location', ['BM 7', 'BM 8'])->whereNotBetween('machine.id', [472, 481])->count();
        $PM78Monitoring = Machine::where('id_client', 1277)->whereBetween('location', ['BM 7', 'BM 8'])->whereNotBetween('machine.id', [472, 481])
            ->whereHas('monitoring', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->count();
        // $mesin = Machine::where('id_client', 1277)->rightJoin('monitoring as m', 'm.id_machine', '=' ,'machine.id')->whereDate('m.date', $date)->get();
        $mesinCompressor = Machine::whereNotBetween('machine.id', [472, 481])
            ->leftJoin('monitoring as m', function ($join) use ($date) {
                $join->on('machine.id', '=', 'm.id_machine')
                    ->whereDate('m.date', '=', $date); // Menyaring berdasarkan tanggal monitoring
            })
            ->join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->join('unit as u', 'u.id', '=', 'sp.id_product')
            ->leftJoin('users as us', 'us.id', '=', 'm.id_pic')
            ->where('machine.id_client', 1277)
            ->where('u.unit', 'AIR COMPRESSOR SCREW')
            ->select(
                'machine.*',
                DB::raw("CONCAT(sp.brand, ' ', u.sku) as brand_type"),
                'm.*',
                'u.unit',
                'us.name'
            )
            ->get();
        $mesinDryer = Machine::whereNotBetween('machine.id', [472, 481])
            ->leftJoin('monitoring as m', function ($join) use ($date) {
                $join->on('machine.id', '=', 'm.id_machine')
                    ->whereDate('m.date', '=', $date); // Menyaring berdasarkan tanggal monitoring
            })
            ->join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->join('unit as u', 'u.id', '=', 'sp.id_product')
            ->leftJoin('users as us', 'us.id', '=', 'm.id_pic')
            ->where('machine.id_client', 1277)
            ->where('u.unit', 'REFRIGERANT AIR DRYER')
            ->select(
                'machine.*',
                DB::raw("CONCAT(sp.brand, ' ', u.sku) as brand_type"),
                'm.*',
                'us.name'
            )
            ->get();
        // dd($mesinDryer);
        return view('pages.monitoring.recap', compact('allPlant', 'allPlantMonitoring', 'GT', 'GTMonitoring', 'GT3', 'GT3Monitoring', 'INC', 'INCMonitoring', 'PM12', 'PM12Monitoring', 'PM35', 'PM35Monitoring', 'PM78', 'PM78Monitoring', 'date'));
    }

    public function issueUpdate(Request $request, $id, $month)
    {
        if ($request->condition != "Off") {
            $rule = [
                'leak' => 'required',
            ];
            $message = [
                'leak.required' => 'Cek Kebocoran Wajib Dipilih!',
            ];
            $this->validate($request, $rule, $message);
        }

        // dd($request->all());
        // dd($machine->unit->unit->unit);
        if ($id == 495 || $id == 496) {
            $maksimal = 100;
        } else {
            $maksimal = 94;
        }
        $monitoring = Monitoring::find($id);
        $machine = Machine::find($monitoring->id_machine);
        $monitoring->condition = $request->condition;
        if ($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER') {
            if ($request->condition == 'Running') {
                $monitoring->leak = $request->leak;
                $monitoring->running = number_format($request->running, 0, ',', '.') . ' Hour';
                $monitoring->loading = number_format($request->loading, 0, ',', '.') . ' Hour';
                if (is_numeric($request->pressure) && strpos($request->pressure, ',') === false) {
                    $monitoring->pressure = $request->pressure . ',0' . ' Bar';
                } else {
                    $monitoring->pressure = $request->pressure . ' Bar';
                }
                $monitoring->oil_level = $request->oil;
                $monitoring->temp = $request->temperature . " °C";
                if ($request->issue == null) {
                    if ($request->temperature <= $maksimal) {
                        $monitoring->issue = null;
                    } else {
                        $monitoring->issue = 'High Temperature : ' . $request->temperature . " °C";
                    }
                } else {
                    if ($request->temperature <= $maksimal) {
                        $monitoring->issue = $request->issue;
                    } else {
                        $monitoring->issue = $request->issue . ', High Temperature : ' . $request->temperature . " °C";
                    }
                }
            } elseif ($request->condition == 'Stand By') {
                $monitoring->leak = $request->leak;
                $monitoring->running = number_format($request->running, 0, ',', '.') . ' Hour';
                $monitoring->loading = number_format($request->loading, 0, ',', '.') . ' Hour';
                if (is_numeric($request->pressure) && strpos($request->pressure, ',') === false) {
                    $monitoring->pressure = $request->pressure . ',0' . ' Bar';
                } else {
                    $monitoring->pressure = $request->pressure . ' Bar';
                }
                $monitoring->oil_level = $request->oil;
                $monitoring->temp = $request->temperature . " °C";
                if ($request->issue == null) {
                    if ($request->temperature <= $maksimal) {
                        $monitoring->issue = 'Stand By';
                    } else {
                        $monitoring->issue = 'Stand By: High Temperature : ' . $request->temperature . " °C";
                    }
                } else {
                    if ($request->temperature <= $maksimal) {
                        $monitoring->issue = 'Stand By : ' . $request->issue;
                    } else {
                        $monitoring->issue = 'Stand By : ' . $request->issue . ', High Temperature : ' . $request->temperature . " °C";
                    }
                }
            } elseif ($request->condition == 'Off') {
                $monitoring->leak = $request->leak;
                $monitoring->running = '-';
                $monitoring->loading = '-';
                if (is_numeric(value: $request->pressure) && strpos($request->pressure, ',') === false) {
                    $monitoring->pressure = '-';
                } else {
                    $monitoring->pressure = '-';
                }
                $monitoring->oil_level = $request->oil;
                $monitoring->temp = "-";
                if ($request->issue == null) {
                    if ($request->temperature <= $maksimal) {
                        $monitoring->issue = 'Off';
                    } else {
                        $monitoring->issue = 'Off: High Temperature : ' . $request->temperature . " °C";
                    }
                } else {
                    if ($request->temperature <= $maksimal) {
                        $monitoring->issue = $request->issue;
                    } else {
                        $monitoring->issue = $request->issue . ', High Temperature : ' . $request->temperature . " °C";
                    }
                }
            }
        } else {
            if ($request->condition == 'Running') {
                $monitoring->fan = $request->fan;
                $monitoring->dew = $request->dew;
                $monitoring->drain = $request->drain;
                $monitoring->leak = $request->leak;
                $monitoring->temp = $request->temperature_in . " °C";
                $monitoring->temp_out = $request->temperature_out . " °C";
                if ($request->issue == null) {
                    if ($request->dew <= 10) {
                        $monitoring->issue = null;
                    } else {
                        $monitoring->issue = 'High dew';
                    }
                } else {
                    if ($request->dew <= 10) {
                        $monitoring->issue = $request->issue;
                    } else {
                        $monitoring->issue = $request->issue . ', High dew';
                    }
                }
            } elseif ($request->condition == 'Stand By') {
                $monitoring->fan = $request->fan;
                $monitoring->drain = $request->drain;
                $monitoring->dew = $request->dew;
                $monitoring->leak = $request->leak;
                $monitoring->temp = $request->temperature_in . " °C";
                $monitoring->temp_out = $request->temperature_out . " °C";
                if ($request->issue == null) {
                    if ($request->dew <= 10) {
                        $monitoring->issue = 'Stand By';
                    } else {
                        $monitoring->issue = 'Stand By: High dew';
                    }
                } else {
                    if ($request->dew <= 10) {
                        $monitoring->issue = 'Stand By : ' . $request->issue;
                    } else {
                        $monitoring->issue = 'Stand By : ' . $request->issue . ', High dew';
                    }
                }
            } elseif ($request->condition == 'Off') {
                $monitoring->fan = '-';
                $monitoring->drain = '-';
                $monitoring->dew = '-';
                $monitoring->leak = $request->leak;
                $monitoring->temp = "-";
                $monitoring->temp_out = "-";
                if ($request->issue == null) {
                    if ($request->dew <= 10) {
                        $monitoring->issue = 'Off';
                    } else {
                        $monitoring->issue = 'Off: High dew';
                    }
                } else {
                    if ($request->dew <= 10) {
                        $monitoring->issue = $request->issue;
                    } else {
                        $monitoring->issue = $request->issue . ', High dew';
                    }
                }
            }
        }
        $monitoring->recommendation = '-';
        $monitorSave = $monitoring->save();
        // dd($monitoring);
        // if ($monitorSave) {
        //     $statMonitoring = new StatusMonitoring();
        //     $statMonitoring->id_monitoring = $monitoring->id;
        //     $statMonitoring->id_pic = Auth::user()->id;
        //     $statMonitoring->status = '0';
        //     $statMonitoring->desc = 'Monitoring Issues Created';
        //     $statMonitoring->date = Carbon::today();
        //     $statMonitoringSave = $statMonitoring->save();
        // }
        // dd($monitoring);
        if ($monitorSave) {
            return redirect('/service-manager-daily/' . $monitoring->id_machine . '/' . $month)->with('massage', 'data telah dibuat');
        }
    }
    public function mainlogUpdate(Request $request, $id, $month)
    {
        $mainlog = Mainlog::find($id);
        // dd($request->all());
        $mainlog->desc = $request->desc;
        $mainlogSave = $mainlog->save();
        if ($mainlogSave) {
            return redirect('/service-manager-daily/' . $mainlog->id_machine . '/' . $month)->with('massage', 'data telah dibuat');
        }
    }

    public function getAllMachine($date)
    {
        $day = Carbon::createFromFormat('d-m-Y', $date);
        $month = $day->month;
        $year = $day->year;

        $startOfMonth = $day->copy()->startOfMonth();
        $endOfMonth = $day->copy()->endOfMonth();

        $startDate = $day->copy()->startOfMonth();
        $endDate = $day->copy()->endOfMonth();

        $machines = Machine::with([
            'unit',
            'monitoring' => function ($query) use ($month, $year) {
                $query->whereMonth('date', $month)
                    ->whereYear('date', $year);
            },
            'monitoringW' => function ($query) use ($month, $year) {
                $query->whereMonth('date', $month)
                    ->whereYear('date', $year);
            },
            'reports' => function ($query) use ($month, $year) {
                $query->whereMonth('date', $month)
                    ->whereYear('date', $year);
            },
            'mainlog' => function ($query) use ($month, $year) {
                $query->whereMonth('date', $month)
                    ->whereYear('date', $year);
            },
        ])
            // ->join('unit as u', 'machine.id_unit', '=', 'u.id')
            ->whereNotBetween('machine.id', [472, 481])
            ->where('machine.id_client', 1277)
            // ->orderBy('u.unit')
            ->orderBy('machine.location')
            ->get();

        // dd($machines);

        $result = $machines->filter(function ($machine) {
            return $machine->monitoring->isNotEmpty();
        })->map(function ($machine) use ($startOfMonth, $endOfMonth, $endDate, $date) {
            $dates = [];
            $dateCursor = $startOfMonth->copy(); // Pastikan startOfMonth tidak berubah
            $dateWeek = $startOfMonth->copy(); // Pastikan startOfMonth tidak berubah

            // Inisialisasi semua hari dalam bulan
            while ($dateCursor->lte($endOfMonth)) {
                $dateNumber = $dateCursor->format('d');
                $dates[$dateNumber] = [
                    'Hari' => "$dateNumber",
                    'running' => '-',
                    'loading' => '-',
                    'pressure' => '-',
                    'temp' => '-',
                    'temp_out' => '-',
                    'condition' => '-',
                    'oil_level' => '-',
                    'dew' => '-',
                    'drain' => '-',
                    'cleaning' => '-',
                    'issue' => '-',
                    'main_desc' => '-',
                    'leak' => '-',
                    'fan' => '-',
                    'technician' => '-',
                    'pic' => '-',
                    'date' => '-',
                ];
                $dateCursor->addDay(); // Tambahkan satu hari
            }

            // Masukkan data dari monitoring ke dalam tanggal yang sesuai
            foreach ($machine->monitoring as $item) {
                $dateNumber = Carbon::parse($item->date)->format('d');
                if (isset($dates[$dateNumber])) {
                    $dates[$dateNumber]['date'] = $item->date;
                    $dates[$dateNumber]['running'] = $item->running;
                    $dates[$dateNumber]['loading'] = $item->loading;
                    $dates[$dateNumber]['pressure'] = $item->pressure;
                    $dates[$dateNumber]['temp'] = $item->temp;
                    $dates[$dateNumber]['temp_out'] = $item->temp_out;
                    $dates[$dateNumber]['condition'] = $item->condition;
                    $dates[$dateNumber]['oil_level'] = $item->oil_level;
                    $dates[$dateNumber]['dew'] = $item->dew;
                    $dates[$dateNumber]['drain'] = $item->drain;
                    $dates[$dateNumber]['cleaning'] = $item->cleaning;
                    $dates[$dateNumber]['issue'] = $item->issue;
                    $dates[$dateNumber]['main_desc'] = $item->main_desc;
                    $dates[$dateNumber]['leak'] = $item->leak;
                    $dates[$dateNumber]['fan'] = $item->fan;
                    $dates[$dateNumber]['pic'] = $item->pic->name;
                    $dates[$dateNumber]['technician'] = $item->technician;
                }
            }

            // $weeks = [];
            // while ($dateWeek->lte($endOfMonth)) {
            //     $weekNumber = $dateWeek->format('W');
            //     $weeks[$weekNumber] = [
            //         'minggu' => "$weekNumber",
            //         'week' => '-',
            //         'condition' => '-',
            //         'voltage' => '-',
            //         'ampere' => '-',
            //         'idle' => '-',
            //         'vibration' => '-',
            //         'cooler' => '-',
            //         'coupling' => '-',
            //         'area' => '-',
            //         'dew' => '-',
            //         'drain' => '-',
            //         'pre' => '-',
            //         'after' => '-',
            //         'condensor' => '-',
            //         'desc' => '-',
            //         'date' => '-',
            //         'name' => '-'
            //     ];
            //     $dateWeek->addWeek();
            // }
            $today = Carbon::createFromFormat('d-m-Y', $date);

            // dd($today);
            $weeks = [1, 2, 3, 4, 5];
            $weeksoy = collect($weeks)->map(function ($week) use ($today) {
                $data = MonitoringWeekly::join('users as u', 'u.id', '=', 'monitoring_weekly.id_pic')
                    ->where('week', $week)
                    ->whereMonth('monitoring_weekly.date', $today->month)
                    ->whereYear('monitoring_weekly.date', $today->year)
                    ->select('monitoring_weekly.*', 'u.name')
                    ->first();

                return $data ?? [
                    'id_pic' => '-',
                    'id_machine' => '-',
                    'condition' => '-',
                    'voltage' => '-',
                    'ampere' => '-',
                    'vibration' => '-',
                    'idle' => '-',
                    'week' => '-',
                    'drain' => '-',
                    'pre' => '-',
                    'cooler' => 0,
                    'coupling' => 2,
                    'area' => 0,
                    'condensor' => 0,
                    'after' => '-',
                    'desc' => '-',
                    'type' => '-',
                    'date' => '-',
                    'name' => '-',
                ];
            })->toArray();

            // Isi data yang ada ke dalam minggu
            // foreach ($machine->monitoringW as $item) {
            //     $weekNumber = Carbon::parse($item->date)->format('W');
            //     $weeks[$weekNumber]['week'] = $item->week;
            //     $weeks[$weekNumber]['condition'] = $item->condition;
            //     $weeks[$weekNumber]['voltage'] = $item->voltage;
            //     $weeks[$weekNumber]['ampere'] = $item->ampere;
            //     $weeks[$weekNumber]['idle'] = $item->idle;
            //     $weeks[$weekNumber]['vibration'] = $item->vibration;
            //     $weeks[$weekNumber]['cooler'] = $item->cooler;
            //     $weeks[$weekNumber]['coupling'] = $item->coupling;
            //     $weeks[$weekNumber]['area'] = $item->area;
            //     $weeks[$weekNumber]['dew'] = $item->dew;
            //     $weeks[$weekNumber]['drain'] = $item->drain;
            //     $weeks[$weekNumber]['pre'] = $item->pre;
            //     $weeks[$weekNumber]['after'] = $item->after;
            //     $weeks[$weekNumber]['condensor'] = $item->condensor;
            //     $weeks[$weekNumber]['desc'] = $item->desc;
            //     $weeks[$weekNumber]['date'] = is_string($item->date) ? date('d-m-Y', strtotime($item->date)) : Carbon::parse($item->date)->format('d-m-Y');
            //     $weeks[$weekNumber]['name'] = $item->pic->name;
            // }

            return [
                'machine' => $machine->unit->brand . ' ' . $machine->unit->unit->sku . ' - ' . $machine->tag,
                'plant' => $machine->location,
                'unit' => $machine->unit->unit->unit,
                'id' => $machine->id,
                'daily' => $dates,
                'weekly' => $weeksoy,
                'log' => $machine->monitoring->filter(function ($log) {
                    return !is_null($log->issue) && $log->issue !== '-' && strtolower($log->issue) !== 'normal';
                })->map(function ($log) {
                    return [
                        'log' => $log->issue,
                        'date' => Carbon::parse($log->date)->format('d-m-y'),
                        'pic' => optional($log->pic)->name
                    ];
                }),
                'mainlog' => $machine->mainlog->filter(function ($mainlog) {
                    return !is_null($mainlog->desc) && $mainlog->desc !== '-';
                })->map(function ($mainlog) {
                    return [
                        'id' => $mainlog->id,
                        'id_pic' => $mainlog->id_teknisi,
                        'id_machine' => $mainlog->machine->id,
                        'id_service' => optional($mainlog->issue?->reports->first())->id,
                        'log' => $mainlog->desc,
                        'date' => \Carbon\Carbon::parse($mainlog->date)->format('d'),
                        'technician' => $mainlog->teknisi->name
                    ];
                }),
                'reports' => $machine->reports->map(function ($report) {
                    return [
                        'id' => $report->id,
                        'no_service' => $report->no_service,
                        'type' => $report->type,
                        'running' => $report->running,
                        'load' => $report->load,
                        'jobdesc' => $report->jobdesc,
                        'desc' => $report->desc,
                        'recomendation' => $report->recomendation,
                        'sign_client' => $report->sign_client,
                        'company' => $report->pic->client->company,
                        'area' => $report->pic->client->area,
                        'pic' => $report->pic->name_pic,
                        'technician' => $report->technician->name,
                        'technician_sign' => $report->technician->sign,
                        'tag' => $report->machine->tag,
                        'location' => $report->machine->location,
                        'unit' => $report->machine->unit->brand . ' ' . $report->machine->unit->unit->sku,
                        'date' => is_string($report->date) ? date('d-m-Y', strtotime($report->date)) : Carbon::parse($report->date)->format('d-m-Y'),
                        'picture' => $report->picture
                    ];
                }),
                'monthly' => collect($machine->monitoringM)->map(function ($monitoringM) {
                    return [
                        'condition' => $monitoringM->condition,
                        'hp' => $monitoringM->hp,
                        'lp' => $monitoringM->lp,
                        'strainer' => $monitoringM->strainer,
                        'date' => is_string($monitoringM->date) ? date('d-m-Y', strtotime($monitoringM->date)) : Carbon::parse($monitoringM->date)->format('d-m-Y'),
                        'pic' => $monitoringM->pic->name,
                    ];
                }),
            ];
        });
        // dd($result);
        return view('pages.monitoring.allMachine', compact('result'));
    }

    public function summaryMainlog($month)
    {
        $today = Carbon::today();
        $month = $month;
        $mainlog = Mainlog::join('machine', 'main_log.id_machine', '=', 'machine.id')
            ->join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->join('unit as u', 'u.id', '=', 'sp.id_product')
            ->where('machine.id_client', 1277)
            ->whereNotBetween('machine.id', [472, 481])
            ->orderBy('machine.location')
            ->whereMonth('main_log.date', $month)->whereYear('main_log.date', $today->year)
            ->select('main_log.*', DB::raw("CONCAT(sp.brand, ' ', u.sku) as brand_type"), 'machine.location', 'machine.serial', 'machine.tag')->get();
        // dd($mainlog);
        return view('pages.monitoring.summary', compact('mainlog', 'month'));
    }
    public function summaryMainlogPrint($month)
    {
        $today = Carbon::today();
        $month = $month;
        $mainlog = Mainlog::join('machine', 'main_log.id_machine', '=', 'machine.id')
            ->join('serial_product as sp', 'sp.id', '=', 'machine.id_unit')
            ->join('unit as u', 'u.id', '=', 'sp.id_product')
            ->where('machine.id_client', 1277)
            ->whereNotBetween('machine.id', [472, 481])
            ->orderBy('machine.location')
            ->whereMonth('main_log.date', $month)->whereYear('main_log.date', $today->year)
            ->select('main_log.*', DB::raw("CONCAT(sp.brand, ' ', u.sku) as brand_type"), 'machine.location', 'machine.serial', 'machine.tag')->get();
        // dd($mainlog);
        return view('pages.monitoring.summary-print', compact('mainlog', 'month'));
    }
    public function kosongan()
    {
        return view('pages.kosongan');
    }
    public function checkPlanning(Request $request)
    {
        $value = $request->input('planing');
        $today = Carbon::now()->toDateString();

        $monitoring = MonitoringActivities::whereDate('date', $today)->first();

        if ($monitoring) {
            $monitoring->planning = $value;
            $monitoring->save();
        } else {
            $monitAct = new MonitoringActivities();
            $monitAct->date = $today;
            $monitAct->planning = $value;
            $monitAct->save();
        }

        return response()->json(['status' => 'success', 'planing' => $value]);
    }
    public function checkSync(Request $request)
    {
        $value = $request->input('sync');
        $today = Carbon::now()->toDateString();

        $monitoring = MonitoringActivities::whereDate('date', $today)->first();

        if ($monitoring) {
            $monitoring->sync = $value;
            $monitoring->save();
        } else {
            $monitAct = new MonitoringActivities();
            $monitAct->date = $today;
            $monitAct->sync = $value;
            $monitAct->save();
        }

        return response()->json(['status' => 'success', 'sync' => $value]);
    }
    public function checkAbnormal(Request $request)
    {
        $value = $request->input('abnormal');
        $today = Carbon::now()->toDateString();

        $monitoring = MonitoringActivities::whereDate('date', $today)->first();

        if ($monitoring) {
            $monitoring->abnormal = $value;
            $monitoring->save();
        } else {
            $monitAct = new MonitoringActivities();
            $monitAct->date = $today;
            $monitAct->planning = $value;
            $monitAct->save();
        }

        return response()->json(['status' => 'success', 'abnormal' => $value]);
    }
    public function checkLog(Request $request)
    {
        $value = $request->input('log');
        $today = Carbon::now()->toDateString();

        $monitoring = MonitoringActivities::whereDate('date', $today)->first();

        if ($monitoring) {
            $monitoring->log = $value;
            $monitoring->save();
        } else {
            $monitAct = new MonitoringActivities();
            $monitAct->date = $today;
            $monitAct->planning = $value;
            $monitAct->save();
        }

        return response()->json(['status' => 'success', 'log' => $value]);
    }
    public function checkTimeline(Request $request)
    {
        $value = $request->input('timeline');
        $today = Carbon::now()->toDateString();

        $monitoring = MonitoringActivities::whereDate('date', $today)->first();

        if ($monitoring) {
            $monitoring->timeline = $value;
            $monitoring->save();
        } else {
            $monitAct = new MonitoringActivities();
            $monitAct->date = $today;
            $monitAct->planning = $value;
            $monitAct->save();
        }

        return response()->json(['status' => 'success', 'timeline' => $value]);
    }
    public function checkPreventive(Request $request)
    {
        $value = $request->input('preventive');
        $today = Carbon::now()->toDateString();

        $monitoring = MonitoringActivities::whereDate('date', $today)->first();

        if ($monitoring) {
            $monitoring->preventive = $value;
            $monitoring->save();
        } else {
            $monitAct = new MonitoringActivities();
            $monitAct->date = $today;
            $monitAct->planning = $value;
            $monitAct->save();
        }

        return response()->json(['status' => 'success', 'preventive' => $value]);
    }
    public function activity()
    {
        $today = Carbon::now()->toDateString();
        $user = User::find('25');
        $monitoring = MonitoringActivities::whereDate('date', $today)->first();
        return view('pages.monitoring.activities.kpi', compact('user', 'monitoring'));
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
    protected function getWeekNumberFromDate($date)
    {
        // Ubah tanggal ke format Carbon
        $carbonDate = Carbon::parse($date);

        // Dapatkan hari pertama dalam bulan ini
        $firstDayOfMonth = $carbonDate->copy()->startOfMonth();

        // Hitung minggu keberapa
        $weekNumber = ceil($carbonDate->diffInDays($firstDayOfMonth) / 7);

        return $weekNumber;
    }
    
    public function exportDailyCSV(Request $request, $id, $month)
    {
        $today = Carbon::today()->setMonth($month);
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();
    
        $dates = [];
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $dates[] = $date->format('d-m-Y');
        }
    
        $monitoringData = Monitoring::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('id_machine', $id)
            ->whereYear('date', $today->year)
            ->join('users as u', 'u.id', '=', 'monitoring.id_pic')
            ->get(['monitoring.*', 'u.name'])
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date)->format('d-m-Y');
                return $item;
            });
    
        $indexed = $monitoringData->keyBy('date');
    
        $rows = [];
        $rows[] = "date,running,loading,pressure,temp,leak,condition,oil_level,issue,pic";
    
        foreach ($dates as $date) {
            $d = $indexed[$date] ?? null;
            $rows[] = implode(',', [
                $date,
                $d->running ?? '-',
                $d->loading ?? '-',
                $d->pressure ?? '-',
                $d->temp ?? '-',
                $d->leak ?? '-',
                $d->condition ?? '-',
                $d->oil_level ?? '-',
                $d->issue ?? '-',
                $d->name ?? '-',
            ]);
        }
    
        return response(implode("\n", $rows))
            ->header('Content-Type', 'text/plain');
    }
}
