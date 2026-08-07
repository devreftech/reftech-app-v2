<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\ToolAssignmentTechnician;
use App\Models\ToolMaster;
use App\Models\User;
use App\Services\ToolAssetPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Str;

class ToolAssignmentController extends Controller
{
    protected function guardAdmin()
    {
        if (Auth::user()->role != 'Admin') {
            abort(403, 'Hanya Admin yang bisa mengakses Management Tools.');
        }
    }

    public function index()
    {
        $this->guardAdmin();

        $technicians = User::whereHas('toolAssignmentEntry')
            ->withCount(['toolsAssigned' => function ($q) {
                $q->where('status_tools', 'Aktif');
            }])
            ->orderBy('name')
            ->get();

        $availableUsers = User::whereDoesntHave('toolAssignmentEntry')->orderBy('name')->get();

        return view('pages.technician.tool-assignment.index', compact('technicians', 'availableUsers'));
    }

    public function addTechnician(Request $request)
    {
        $this->guardAdmin();

        $request->validate([
            'user_id' => 'required|exists:users,id|unique:tool_assignment_technicians,user_id',
        ], [
            'user_id.unique' => 'User ini sudah ada di daftar teknisi Tool Assignment.',
        ]);

        $user = User::findOrFail($request->user_id);
        ToolAssignmentTechnician::create(['user_id' => $user->id]);

        return redirect()->back()->with('success', $user->name . ' berhasil ditambahkan ke daftar Tool Assignment.');
    }

    public function removeTechnician($userId)
    {
        $this->guardAdmin();

        $entry = ToolAssignmentTechnician::where('user_id', $userId)->firstOrFail();
        $entry->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus dari daftar Tool Assignment.');
    }

    public function show($technicianId)
    {
        $this->guardAdmin();

        $technician = User::whereHas('toolAssignmentEntry')->findOrFail($technicianId);
        $tools = FixedAsset::where('type', 'Tools')
            ->where('id_pic', $technicianId)
            ->with('toolsMaster')
            ->orderByDesc('tanggal_serah_terima')
            ->get();
        $toolMasters = ToolMaster::where('status_aktif', 1)->orderBy('nama_tools')->get();
        $otherTechnicians = User::whereHas('toolAssignmentEntry')->where('id', '!=', $technicianId)->orderBy('name')->get();

        return view('pages.technician.tool-assignment.show', compact('technician', 'tools', 'toolMasters', 'otherTechnicians'));
    }

    public function store(Request $request, $technicianId)
    {
        $this->guardAdmin();

        $technician = User::whereHas('toolAssignmentEntry')->findOrFail($technicianId);

        $request->validate([
            'id_tools_master' => 'required|exists:tool_master,id',
            'qty' => 'required|integer|min:1',
            'tanggal_serah_terima' => 'required|date',
            'foto_awal' => 'required|image|mimes:jpeg,jpg,png|max:4096',
        ], [
            'id_tools_master.required' => 'Pilih jenis tools dari master.',
            'foto_awal.required' => 'Foto serah-terima wajib diupload.',
        ]);

        $fixed = new FixedAsset();
        $fixed->type = 'Tools';
        $fixed->id_tools_master = $request->id_tools_master;
        $fixed->id_pic = $technician->id;
        $fixed->qty = $request->qty;
        $fixed->tanggal_serah_terima = $request->tanggal_serah_terima;
        $fixed->status_tools = 'Aktif';
        $fixed->status = 1;
        $fixed->desc = $request->desc;

        $fixed->foto_awal = $this->uploadFoto($request->file('foto_awal'), $technician->id);
        $fixed->save();

        return redirect()->back()->with('success', 'Tools berhasil ditambahkan ke ' . $technician->name . '.');
    }

    public function update(Request $request, $id)
    {
        $this->guardAdmin();

        $fixed = FixedAsset::where('type', 'Tools')->findOrFail($id);

        $request->validate([
            'qty' => 'required|integer|min:1',
            'tanggal_serah_terima' => 'required|date',
        ]);

        $fixed->qty = $request->qty;
        $fixed->tanggal_serah_terima = $request->tanggal_serah_terima;
        $fixed->desc = $request->desc;

        if ($request->hasFile('foto_awal')) {
            $fixed->foto_awal = $this->uploadFoto($request->file('foto_awal'), $fixed->id_pic);
        }

        $fixed->save();

        return redirect()->back()->with('success', 'Data tools berhasil diperbarui.');
    }

    public function transfer(Request $request, $id)
    {
        $this->guardAdmin();

        $request->validate([
            'id_pic' => 'required|exists:users,id',
        ]);

        $fixed = FixedAsset::where('type', 'Tools')->findOrFail($id);
        $newTechnician = User::whereHas('toolAssignmentEntry')->findOrFail($request->id_pic);

        $fixed->id_pic = $newTechnician->id;
        $fixed->save();

        return redirect()->back()->with('success', 'Tools berhasil dipindah ke ' . $newTechnician->name . '.');
    }

    public function retire($id)
    {
        $this->guardAdmin();

        $fixed = FixedAsset::where('type', 'Tools')->findOrFail($id);
        $fixed->status_tools = $fixed->status_tools == 'Retired' ? 'Aktif' : 'Retired';
        $fixed->save();

        return redirect()->back()->with('success', $fixed->status_tools == 'Retired' ? 'Tools ditandai Retired.' : 'Tools diaktifkan kembali.');
    }

    protected function uploadFoto($foto, $technicianId)
    {
        $ext = $foto->getClientOriginalExtension();
        $name = Str::random(10);
        $uploadDir = ToolAssetPath::to('asset/tools-instance/' . $technicianId);
        $relativePath = 'asset/tools-instance/' . $technicianId . '/' . $name . '.' . $ext;

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $img = Image::make($foto->path());
        $img->fit(1000, 1000, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save(ToolAssetPath::to($relativePath));

        return $relativePath;
    }
}
