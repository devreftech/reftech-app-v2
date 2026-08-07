<?php

namespace App\Http\Controllers;

use App\Models\MentionNotulen;
use App\Models\Notulen;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotulenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::all();
        $notulens = Notulen::join('mention_notulen as m', 'm.id_notulen', '=', 'notulen.id')->join('users as u', 'm.id_mention', '=', 'u.id')->get(['notulen.*', 'u.name', 'm.level']);
        return view('pages.notulen.index', compact('user', 'notulens'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
        $notulen = new Notulen;
        $notulen->id_notuler = Auth::id();
        $notulen->title = $request->title;
        $notulen->desc = $request->desc;
        $notulen->date = Carbon::now();
        $notulenSave = $notulen->save();
        foreach ($request->mention as $key => $value) {
            $mentionTo = new MentionNotulen;
            $mentionTo->id_notulen = $notulen->id;
            $mentionTo->id_mention = $request->mention[$key];
            $mentionTo->level = '0';
            $mentionToSave = $mentionTo->save();
        }
        if ($notulenSave && $mentionToSave) {
            return redirect('/notulen')->with('message', 'data telah di tambahkan');
        } else {
            # code...
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
        $notulen = MentionNotulen::find($id);
        $notulen->level = '1';
        $notulenSave = $notulen->save();
        if ($notulenSave) {
            return 1;
        } else {
            return 0;
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
}
