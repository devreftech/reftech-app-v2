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
        $user = $user->exists ? $user : User::find(Auth::id());
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
        $users = User::find($id);
        $users->name = $request->name;
        $users->email = $request->email;
        $users->birthday = $request->birthday;
        if ($request->filled('password')) {
            $users->password = Hash::make($request->password);
        }
        $users->address = $request->address;
        $users->phone = '+62' . $request->phone;

        if ($request->hasFile('image')) {
            if ($users->image && $users->image != 'asset/profile/profile.jpg' && File::exists(public_path($users->image))) {
                File::delete(public_path($users->image));
            }

            $foto = $request->file('image');
            $foto_ext = $foto->getClientOriginalExtension();
            $foto_name = Str::random(8);

            $upload_path = 'asset/profile';
            $imagename = $upload_path . '/' . $foto_name . '.' . $foto_ext;
            $foto->move(public_path($upload_path), $foto_name . '.' . $foto_ext);

            $users->image = $imagename;
        }

        if ($request->hasFile('banner')) {
            if ($users->banner && File::exists(public_path($users->banner))) {
                File::delete(public_path($users->banner));
            }

            $banner = $request->file('banner');
            $banner_ext = $banner->getClientOriginalExtension();
            $banner_name = 'banner_' . Str::random(10);

            $upload_path = 'asset/profile/banners';
            if (!File::exists(public_path($upload_path))) {
                File::makeDirectory(public_path($upload_path), 0777, true, true);
            }
            $bannerPath = $upload_path . '/' . $banner_name . '.' . $banner_ext;
            $banner->move(public_path($upload_path), $banner_name . '.' . $banner_ext);

            $users->banner = $bannerPath;
        }

        $status = $users->save();
        if ($status) {
            return redirect('/profile/' . $id)->with('success', 'Data Has been updated');
        }
    }

    /**
     * Update user profile banner photo.
     */
    public function updateBanner(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'banner.required' => 'Silakan pilih foto banner!',
            'banner.image' => 'File harus berupa gambar.',
            'banner.mimes' => 'Format gambar: jpeg, png, jpg, gif, webp.',
            'banner.max' => 'Ukuran maksimal banner 5MB.',
        ]);

        $user = User::findOrFail($id);

        if ($request->hasFile('banner')) {
            if ($user->banner && File::exists(public_path($user->banner))) {
                File::delete(public_path($user->banner));
            }

            $foto = $request->file('banner');
            $foto_ext = $foto->getClientOriginalExtension();
            $foto_name = 'banner_' . Str::random(10);

            $upload_path = 'asset/profile/banners';
            if (!File::exists(public_path($upload_path))) {
                File::makeDirectory(public_path($upload_path), 0777, true, true);
            }
            $imagename = $upload_path . '/' . $foto_name . '.' . $foto_ext;
            $foto->move(public_path($upload_path), $foto_name . '.' . $foto_ext);

            $user->banner = $imagename;
            $user->save();
        }

        return redirect()->back()->with('success', 'Foto Banner profil berhasil diperbarui!');
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
