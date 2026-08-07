<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use App\Models\Client;
use App\Models\CrmStatus;
use App\Models\Issues;
use Illuminate\Http\Request;

class ActivitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        $leads = Client::where("id", $request->client_id)->first();
        $leads->id_issues = $request->issues;
        if ($request->issues == '4') {
            $leads->role = 'Customers';
        }
        dd($request->all());

        $action = new Activities;
        $action->client_id = $request->client_id;
        $action->name = $request->name;
        $action->status = $request->status;
        $action->date = $request->date;
        $action->follow_up = $request->follow_up;
        $action->save();
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
        //
    }
    public function update_calendar(Request $request)
    {
        $client = Client::where("id", $request->input('client_id'))->first();

        // Update client data
        $client->id_issues = $request->input('issues');
        if ($request->input('issues') == '5') {
            $client->role = 'Customers';
            $status = new CrmStatus();
            $status->id_client = $request->input('client_id');
            $status->status = 2;
            $status->save();
        }
        $client->save();

        // Update activities
        $action = new Activities;
        $action->id_client = $request->input('client_id');
        $action->name = $client->activities != null ? "Follow Up" : "Daily Call";
        $action->status = $request->input('status');
        $action->action = $request->input('action');
        $action->note = $request->input('note');
        $action->date = $request->input('date');
        $action->follow_up = $request->input('follow_up');
        $action->save();

        // Berikan respons ke AJAX
        return response()->json(['success' => true, 'message' => 'Perubahan berhasil disimpan']);
    }
    // if ($isuSave && $activitiesSave || $statSave) {
    //     if ($request->issues == '5') {
    //         return redirect("/existing/" . $request->client_id)->with("success", "Data telah ditambahkan");
    //     } else {
    //         return redirect("/leads/detail/" . $request->client_id)->with("success", "Data telah ditambahkan");
    //     }
    // }
}
