<?php

namespace App\Http\Controllers;

use App\Models\DetailUser;
use App\Models\Target;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::all();
        return view('pages.admin.employee.user.index', compact('user'));
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
            'nip'      => 'required',
            'name'     => 'required',
            'email'    => 'required',
            'password' => 'required',
            'area'     => 'required',
            'address'  => 'required',
            'position' => 'required',
            'code'     => 'required',
            'image'    => 'required',
            'phone'    => 'required',
        ];
        $customMessages = [
            'nip.required' => 'Field NIP Wajib Diisi!',
            'name.required' => 'Field Nama Wajib Diisi!',
            'email.required' => 'Field EMail Wajib Diisi',
            'password.required' => 'Field passwprd Wajib Diisi',
            'image.required' => 'Field Foto Wajib Diisi',
            'area.required' => 'Field Area Wajib Diisi',
            'address.required' => 'Field address Wajib Diisi',
            'position.required' => 'Field position Wajib Diisi',
            'code.required' => 'Field code Wajib Diisi',
            'phone.required' => 'Field phone Wajib Diisi!',
        ];
        // dd($request->all());

        $this->validate($request, $rule, $customMessages);
        $users = new User;
        $users->nip = $request->nip;
        $users->name = $request->name;
        $users->birthday = $request->birthday;
        $users->date_in = $request->date_in;
        $users->address = $request->address;
        $users->sign = NULL;
        $users->code = $request->code;
        $users->active = $request->active;
        $users->role = $request->role;
        $users->email = $request->email;
        $users->password = Hash::make($request->password);
        $users->phone = '+62' . $request->phone;
        if ($request->hasFile('image')) {
            if ($users->image != 'asset/profile/profile.jpg') {
                File::delete($users->image);
            }

            $foto = $request->file('image');
            $foto_ext = $foto->getClientOriginalExtension();
            $foto_name = Str::random(8);

            $upload_path = 'asset/profile';
            $foto_filename = $foto_name . '.' . $foto_ext;
            $imagename = $upload_path . '/' . $foto_filename;
            $request->file('image')->move($upload_path, $foto_filename);

            $users['image'] = $imagename;
        } else {
            $users->image = 'asset/profile/profile.jpg';
        }
        $status = $users->save();
        if ($request->role == 'Sales') {
            $target = new Target;
            $target->id_sales = $users->id;
            $target->dc = $request->dc;
            $target->crm = $request->crm;
            if(isset($request->visit)){
                $target->visit = $request->visit;
            }else{
                $target->visit = 1;
            }
            $target->quote = $request->quote;
            $target->leads = $request->leads;
            $target->total = $request->total;
            $target->save();
        }
        $detail = new DetailUser;
        $detail->id_users = $users->id;
        $detail->position = $request->position;
        $detail->roles = $request->role;
        $detail->area = $request->area;
        $detail->date = Carbon::today();
        $dSave = $detail->save();
        if ($status && $dSave) {
            return redirect('/employee')->with('success', 'Data Has been created');
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
        $users = User::findOrFail($id);
        $detail = DetailUser::where('id_users', $id)->orderByDesc('id')->get();
        // dd($detail);
        return view('pages.admin.employee.user.detail', compact('users', 'detail'));
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
            'nip => required',
            'name => required',
            'email => required',
            'address => required',
            'code => required',
            'image => required',
            'phone => required',
        ];
        $customMessages = [
            'nip.required' => 'Field NIP Wajib Diisi!',
            'name.required' => 'Field Nama Wajib Diisi!',
            'email.required' => 'Field EMail Wajib Diisi',
            'image.required' => 'Field Foto Wajib Diisi',
            'address.required' => 'Field address Wajib Diisi',
            'code.required' => 'Field code Wajib Diisi',
            'phone.required' => 'Field phone Wajib Diisi!',
        ];
        // dd($request->all());

        $this->validate($request, $rule, $customMessages);
        $users = User::find($id);
        $users->nip = $request->nip;
        $users->name = $request->name;
        $users->birthday = $request->birthday;
        $users->address = $request->address;
        $users->sign = NULL;
        $users->code = $request->code;
        $users->active = $request->active;
        $users->email = $request->email;
        $users->password = $request->password ? Hash::make($request->password) : $users->password;
        // $users->phone = '+62' . $request->phone;
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
            return redirect('/employee/' . $id)->with('success', 'Data Has been created');
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
        $user = User::find($id);
        $detail = DetailUser::where('id_users', $id)->get();
        $target = Target::where('id_sales', $id)->first();
        $userDel = $user->delete();
        if ($detail != NULL) {
            foreach ($detail as $details) {
                $detDel = $details->delete();
            }
        }
        if ($target != NULL) {
            $target->delete();
        }
        if ($userDel || $detDel) {
            return 1;
        } else {
            return 0;
        }
    }

    public function newPosition($id, Request $request)
    {
        $user = User::find($id);

        $detail = new DetailUser;
        $detail->id_users = $id;
        $detail->area = $request->area;
        $detail->date = $request->date;
        $detail->position = $request->position;
        $detail->roles = $request->role;
        $detailSaved = $detail->save();

        if ($user->role == "Sales") {
            if ($request->role == "Sales") {
                $target = Target::where('id_sales', $id)->first();
                $target->dc = $request->dc;
                $target->crm = $request->crm;
                if(isset($request->visit)){
                    $target->visit = $request->visit;
                }else{
                    $request->visit = 0;
                }
                $target->quote = $request->quote;
                $target->leads = $request->leads;
                $target->total = $request->total;
                $target->save();
                $user->role = $request->role;
                $user->save();
            } else {
                $target = Target::where('id_sales', $id)->first();
                $target->delete();
                $user->role = $request->role;
                $user->save();
            }
        } else {
            if ($request->role == "Sales") {
                $target = new Target;
                $target->id_sales = $id;
                $target->dc = $request->dc;
                $target->crm = $request->crm;
                if(isset($request->visit)){
                    $target->visit = $request->visit;
                }else{
                    $request->visit = NULL;
                }
                $target->quote = $request->quote;
                $target->leads = $request->leads;
                $target->total = $request->total;
                $target->save();
                $user->role = $request->role;
                $user->save();
            } else {
                $user->role = $request->role;
                $user->save();
            }
        }
        if ($detailSaved) {
            return redirect('/employee/'. $id)->with('message', 'Data telah Dikirim');
        }
    }

    public function updateTarget(Request $request, $id){
        $rule = [
            'dc => required',
            'crm => required',
            'quote => required',
            'po => required',
            // 'total => required',
        ];
        $customMessages = [
            'dc.required' => 'Field dc Wajib Diisi!',
            'crm.required' => 'Field crm Wajib Diisi!',
            'quote.required' => 'Field quote Wajib Diisi',
            'po.required' => 'Field po Wajib Diisi',
            // 'total.required' => 'Field code Wajib Diisi',
        ];
        $this->validate($request, $rule, $customMessages);
        $sales = User::find($id);
        $lastDetail = $sales->detail->last();
        // dd($lastDetail);
        $target = Target::where('id_sales', $id)->first();
        $target->dc = $request->dc;
        $target->crm = $request->crm;
        if ($lastDetail->area == "Bekasi" || $lastDetail->area == "Jabodetabek" || $lastDetail->area == "Jawa Barat") {
            $target->visit = $request->visit;
        }
        $target->quote = $request->quote;
        $target->leads = $request->leads;
        $target->total = $request->semuanya;
        $status = $target->save();
        if ($status) {
            return redirect('/employee/' . $id)->with('message', 'Target telah diubah');
        }
    }
}
