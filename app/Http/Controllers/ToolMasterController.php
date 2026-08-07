<?php

namespace App\Http\Controllers;

use App\Models\ToolMaster;
use App\Services\ToolAssetPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Str;

class ToolMasterController extends Controller
{
    public function index()
    {
        if (Auth::user()->role != 'Admin') {
            abort(403, 'Hanya Admin yang bisa mengakses Master Tools.');
        }
        return view('pages.technician.tool-master.index');
    }

    public function data()
    {
        $tools = ToolMaster::orderBy('nama_tools')->get();
        return response()->json(['data' => $tools]);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role != 'Admin') {
            abort(403, 'Hanya Admin yang bisa menambah Master Tools.');
        }

        $request->validate([
            'nama_tools' => 'required|string|max:255',
        ], [
            'nama_tools.required' => 'Nama Tools wajib diisi.',
        ]);

        $tool = new ToolMaster();
        $tool->nama_tools = $request->nama_tools;
        $tool->kategori = $request->kategori;
        $tool->spesifikasi = $request->spesifikasi;
        $tool->link_pembelian = $request->link_pembelian;
        $tool->harga_referensi = $request->harga_referensi;
        $tool->status_aktif = $request->status_aktif ?? 1;

        if ($request->hasFile('foto_referensi')) {
            $tool->foto_referensi = $this->uploadFoto($request->file('foto_referensi'));
        }

        $tool->save();

        return redirect()->back()->with('success', 'Master Tools berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role != 'Admin') {
            abort(403, 'Hanya Admin yang bisa mengubah Master Tools.');
        }

        $request->validate([
            'nama_tools' => 'required|string|max:255',
        ], [
            'nama_tools.required' => 'Nama Tools wajib diisi.',
        ]);

        $tool = ToolMaster::findOrFail($id);
        $tool->nama_tools = $request->nama_tools;
        $tool->kategori = $request->kategori;
        $tool->spesifikasi = $request->spesifikasi;
        $tool->link_pembelian = $request->link_pembelian;
        $tool->harga_referensi = $request->harga_referensi;
        $tool->status_aktif = $request->status_aktif ?? 1;

        if ($request->hasFile('foto_referensi')) {
            $tool->foto_referensi = $this->uploadFoto($request->file('foto_referensi'));
        }

        $tool->save();

        return redirect()->back()->with('success', 'Master Tools berhasil diperbarui!');
    }

    public function destroy($id)
    {
        if (Auth::user()->role != 'Admin') {
            abort(403, 'Hanya Admin yang bisa menghapus Master Tools.');
        }

        $tool = ToolMaster::findOrFail($id);
        $tool->delete();

        return response()->json(['success' => true]);
    }

    protected function uploadFoto($foto)
    {
        $ext = $foto->getClientOriginalExtension();
        $name = Str::random(10);
        $uploadDir = ToolAssetPath::to('asset/tools-master');
        $relativePath = 'asset/tools-master/' . $name . '.' . $ext;

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $img = Image::make($foto->path());
        $img->fit(800, 800, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save(ToolAssetPath::to($relativePath));

        return $relativePath;
    }
}
