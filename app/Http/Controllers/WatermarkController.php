<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class WatermarkController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'photos'   => 'required|array|min:1',
            'photos.*' => 'required|image|mimes:jpeg,jpg,png,webp|max:8192',
        ], [
            'photos.required' => 'Silakan pilih minimal 1 foto.',
            'photos.*.mimes'  => 'Format gambar hanya boleh JPG, JPEG, PNG, atau WEBP.',
            'photos.*.max'    => 'Ukuran gambar maksimal 8MB per file.',
        ]);

        $sessionId = session()->getId();
        $tempDir = storage_path("app/temp-watermarked/{$sessionId}/");
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $wmPath = public_path('asset/WM-REFTECH.png');
        if (!file_exists($wmPath) && file_exists('/home/u877155683/public_html/asset/WM-REFTECH.png')) {
            $wmPath = '/home/u877155683/public_html/asset/WM-REFTECH.png';
        }

        foreach ($request->file('photos') as $photo) {
            $image = Image::make($photo);
            $image->resize(1920, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            if (file_exists($wmPath)) {
                $watermark = Image::make($wmPath)->opacity(60);
                $image->insert($watermark, 'center');
            }

            $cleanName = preg_replace('/[^a-zA-Z0-9._-]/', '', $photo->getClientOriginalName());
            $filename = time() . '_' . Str::random(6) . '_' . $cleanName;
            $image->save($tempDir . $filename, 75);
        }

        return response()->json(['message' => 'Photo(s) uploaded and watermarked!']);
    }
    public function downloadAll()
    {
        $sessionId = session()->getId();
        $tempDir = storage_path("app/temp-watermarked/{$sessionId}/");

        if (!file_exists($tempDir)) {
            return back()->with('error', 'No photos to download.');
        }

        $zipPath = storage_path("app/temp-watermarked/{$sessionId}.zip");

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach (glob($tempDir . '*') as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        return new StreamedResponse(function () use ($zipPath, $tempDir) {
            readfile($zipPath); // Kirim isi file ke browser

            // Setelah dikirim, hapus ZIP dan folder
            @unlink($zipPath);
            \File::deleteDirectory($tempDir);
        }, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="watermarked_photos.zip"',
        ]);
    }

    public function reset(Request $request)
    {
        $sessionId = session()->getId();
        $tempDir = storage_path("app/temp-watermarked/{$sessionId}/");

        if (file_exists($tempDir)) {
            $files = glob($tempDir . '*'); // semua file di folder
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file); // hapus file
                }
            }
            rmdir($tempDir); // hapus folder setelah kosong
        }

        return response()->json(['success' => true, 'message' => 'Temporary files deleted.']);
    }
}
