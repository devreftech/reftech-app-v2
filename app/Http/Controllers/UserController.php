<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("pages.sales.user.create-acc");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $rule = [
            'name => required',
            'email => required',
            'area => required',
            'image => required',
            'phone => required',
        ];
        $customMessages = [
            'name.required' => 'Field Nama Wajib Diisi!',
            'email.required' => 'Field EMail Wajib Diisi',
            'image.required' => 'Field Foto Wajib Diisi',
            'area.required' => 'Field Area Wajib Diisi',
            'phone.required' => 'Field phone Wajib Diisi!',
        ];

        $this->validate($request, $rule, $customMessages);
        $users = new User;
        $users->name = $request->name;
        $users->email = $request->email;
        $users->area = $request->area;
        $users->code = $request->code;
        $users->active = $request->active;
        $users->role = $request->role;
        $users->phone = '+62' . $request->phone;
        $users->password = Hash::make($request->password);
        if ($request->hasFile('image')) {
            if ($users->image != 'asset/profile/profile.jpg') {
                File::delete($users->image);
            }

            $foto = $request->file('image');
            $foto_ext = $foto->getClientOriginalExtension();
            $foto_name = Str::random(8);

            $upload_path = 'asset/profile';
            $imagename = $upload_path . '/' . $foto_name . '.' . $foto_ext;
            $request->file('image')->move($upload_path, $imagename);

            $users['image'] = $imagename;
        } else {
            $users->image = 'asset/profile/profile.jpg';
        }
        $status = $users->save();
        if ($status) {
            return redirect('/profile' . '/' . Auth::user()->id)->with('success', 'Data Has been created');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user = User::find(Auth::user()->id);
        $overview = User::where("role", "sales")->get();
        return view('pages.sales.user.profile', compact('user', 'overview'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('pages.sales.user.setting', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $rule = [
            'name => required',
            'email => required',
            'image => required',
            'phone => required',
        ];
        $customMessages = [
            'name.required' => 'Field Nama Wajib Diisi!',
            'email.required' => 'Field EMail Wajib Diisi',
            'image.required' => 'Field Foto Wajib Diisi',
            'phone.required' => 'Field phone Wajib Diisi!',
        ];

        $this->validate($request, $rule, $customMessages);
        // dd($request);
        $users = User::find($id);
        $users->name = $request->name;
        $users->email = $request->email;
        $users->birthday = $request->birthday;
        $users->password = Hash::make($request->password);
        $users->address = $request->address;
        if (Auth::user()->role == 'Admin') {
            // $users->nip = $request->nip;
            // $users->code = $request->code;
            // $users->active = $request->active;
        }
        $users->phone = '+62' . $request->phone;
        if ($request->hasFile('image')) {
            if ($users->image != 'asset/profile/profile.jpg') {
                File::delete($users->image);
            }

            $foto = $request->file('image');
            $foto_ext = $foto->getClientOriginalExtension();
            $foto_name = Str::random(8);

            $upload_path = 'asset/profile';
            $imagename = $upload_path . '/' . $foto_name . '.' . $foto_ext;
            $request->file('image')->move($upload_path, $imagename);

            $users['image'] = $imagename;
        }
        $status = $users->save();
        if ($status) {
            return redirect('/profile' . '/' . $id)->with('success', 'Data Has been updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        abort(404);
    }
}
