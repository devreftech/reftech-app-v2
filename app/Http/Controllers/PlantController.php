<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientPlant;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    public function store(Request $request, $id)
    {
        $rule = [
            'namePlant' => 'required',
            'addressPlant' => 'required',
        ];

        $message = [
            'namePlant.required' => 'Field Nama Plant Wajib Diisi',
            'addressPlant.required' => 'Field Alamat Plant Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);

        $plant = new ClientPlant;
        $plant->id_client = $id;
        $plant->name = $request->namePlant;
        $plant->address = $request->addressPlant;
        $plantsave = $plant->save();

        if ($plantsave) {
            return redirect('/existing/' . $id)->with('message', 'Plant telah ditambahkan');
        }
    }

    public function update(Request $request, $id)
    {
        $rule = [
            'namePlant' => 'required',
            'addressPlant' => 'required',
        ];

        $message = [
            'namePlant.required' => 'Field Nama Plant Wajib Diisi',
            'addressPlant.required' => 'Field Alamat Plant Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);

        $plant = ClientPlant::find($id);
        $client = Client::where('id', $plant->id_client)->first('id');
        $plant->name = $request->namePlant;
        $plant->address = $request->addressPlant;
        $plantsave = $plant->save();

        if ($plantsave) {
            return redirect('/existing/' . $client->id)->with('message', 'Plant telah diubah');
        }
    }

    public function destroy($id)
    {
        $plant = ClientPlant::find($id);
        $delPlant = $plant->delete();

        if ($delPlant) {
            return 1;
        } else {
            return 0;
        }
    }
}
