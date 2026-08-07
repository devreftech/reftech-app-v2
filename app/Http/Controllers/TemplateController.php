<?php

namespace App\Http\Controllers;

use App\Models\DetaillTemplate;
use App\Models\DetailTemplate;
use App\Models\MachineTemplate;
use App\Models\SubtitleTemplate;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.admin.template.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.template.form');
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
            'brand' => 'required',
            'sku' => 'required',
            'kw' => 'required',
            'hp' => 'required',
        ];
        $message = [
            'brand.required' => 'Field Brand Wajib Diisi',
            'sku.required' => 'Field Machine Wajib Diisi',
            'kw.required' => 'Field KW Wajib Diisi',
            'hp.required' => 'Field HP Wajib Diisi',
        ];
        // dd($request->all());
        $this->validate($request, $rule, $message);
        $template = new MachineTemplate;
        $template->brand = $request->brand;
        $template->sku = $request->sku;
        $template->kw = $request->kw;
        $template->hp = $request->hp;
        $templateSave = $template->save();
        if ($templateSave) {
            return redirect('/template/machine_template/' . $template->id)->with('message', 'data telah di tambahkan');
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
        $machine = MachineTemplate::find($id);
        $subTemplate = SubtitleTemplate::with('detail')->where('id_machine', $id)->get();

        return view('pages.admin.template.edit', compact('machine', 'subTemplate'));
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
        $machines = MachineTemplate::find($id);
        $subtitles = SubtitleTemplate::where('id_machine', $id)->get();

        foreach ($subtitles as $subtitle) {
            $details = DetailTemplate::where('id_subtitle', $subtitle->id)->get();
        }
        $row = 0;
        foreach ($request->subTitle as $item => $subtitleValue) {
            $row++;
            $subtitle = new SubtitleTemplate();
            $subtitle->id_machine = $id;
            $subtitle->subtitle = $subtitleValue; // Menggunakan $subtitleValue langsung
            $subtitleSave = $subtitle->save();

            if (!empty($request->product[$row])) {
                foreach ($request->product[$row] as $key => $productValue) {
                    $detSTemplate = new DetailTemplate();
                    $detSTemplate->id_subtitle = $subtitle->id;
                    $detSTemplate->product = $productValue; // Ambil produk berdasarkan indeks
                    $detSTemplate->detail = $request->detail_product[$row][$key] ?? null; // Pastikan nilai aman
                    $detSTemplate->disc = $request->disc[$row][$key] ?? 0; // Default ke 0 jika kosong
                    $detSTemplate->qty = $request->qty[$row][$key] ?? 0; // Default ke 0
                    $detSTemplate->price = $request->price[$row][$key] ?? 0; // Default ke 0
                    $detSTemplate->info_qty = $request->info_qty[$row][$key] ?? null; // Default null
                    $detSTemplate->amount = $request->amount[$row][$key] ?? 0; // Default ke 0
                    $detSTemplate->save();
                }
            }
        }
        foreach ($subtitles as $sub) {
            foreach ($details as $detail) {
                $detail->delete();
            }
            $sub->delete();
        }
        if ($subtitleSave) {
            return redirect('/template')->with('message', 'data template sudah dibuat');
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
        $machine = MachineTemplate::find($id);
        $subtitles = SubtitleTemplate::where('id_machine', $id)->get();

        foreach ($subtitles as $subtitle) {
            $details = DetailTemplate::where('id_subtitle', $subtitle->id)->get();

            foreach ($details as $detail) {
                $detail->delete();
            }

            $subtitle->delete();
        }

        $machineDel = $machine ? $machine->delete() : false;

        return $machineDel ? 1 : 0;
    }

    public function create_template($id)
    {
        $machine = MachineTemplate::find($id);
        return view('pages.admin.template.form', compact('machine'));
    }

    public function store_template(Request $request, $id)
    {
        // dd($request->all());
        $row = 0;
        foreach ($request->subTitle as $item => $subtitleValue) {
            $row++;
            $subtitle = new SubtitleTemplate();
            $subtitle->id_machine = $id;
            $subtitle->subtitle = $subtitleValue; // Menggunakan $subtitleValue langsung
            $subtitleSave = $subtitle->save();

            if (!empty($request->product[$row])) {
                foreach ($request->product[$row] as $key => $productValue) {
                    $detSTemplate = new DetailTemplate();
                    $detSTemplate->id_subtitle = $subtitle->id;
                    $detSTemplate->product = $productValue; // Ambil produk berdasarkan indeks
                    $detSTemplate->detail = $request->detail_product[$row][$key] ?? null; // Pastikan nilai aman
                    $detSTemplate->disc = $request->disc[$row][$key] ?? 0; // Default ke 0 jika kosong
                    $detSTemplate->qty = $request->qty[$row][$key] ?? 0; // Default ke 0
                    $detSTemplate->price = $request->price[$row][$key] ?? 0; // Default ke 0
                    $detSTemplate->info_qty = $request->info_qty[$row][$key] ?? null; // Default null
                    $detSTemplate->amount = $request->amount[$row][$key] ?? 0; // Default ke 0
                    $detSTemplate->save();
                }
            }
        }
        if ($subtitleSave) {
            return redirect('/template')->with('message', 'data template sudah dibuat');
        }
    }
}
