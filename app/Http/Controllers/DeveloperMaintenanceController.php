<?php

namespace App\Http\Controllers;

use App\Services\MaintenanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeveloperMaintenanceController extends Controller
{
    /**
     * Display the public maintenance page for blocked users/guests.
     */
    public function showMaintenancePage(Request $request)
    {
        $details = MaintenanceService::getDetails();

        // Always render view directly (prevents any redirect ping-pong loops)
        return view('errors.maintenance', compact('details'));
    }

    /**
     * Public JSON endpoint for client-side polling / status checking.
     */
    public function status()
    {
        return response()->json(MaintenanceService::getDetails());
    }

    /**
     * Developer-only: Display the maintenance control settings.
     */
    public function index()
    {
        if (!Auth::user()?->isDeveloper()) {
            abort(403, 'Akses khusus role Developer.');
        }

        $details = MaintenanceService::getDetails();
        return view('pages.developer.maintenance', compact('details'));
    }

    /**
     * Developer-only: Toggle maintenance mode ON or OFF.
     */
    public function toggle(Request $request)
    {
        if (!Auth::user()?->isDeveloper()) {
            abort(403, 'Akses khusus role Developer.');
        }

        $action = $request->input('action'); // 'activate', 'deactivate', 'schedule', 'cancel_plan'
        $startedBy = Auth::user()->name . ' (' . Auth::user()->email . ')';

        if ($action === 'activate') {
            $message = $request->input('message', 'Sistem sedang dalam proses pemeliharaan & pembaruan dari Staging ke Production.');
            $endTime = $request->input('end_time');
            $template = $request->input('template');

            MaintenanceService::activate($message, $endTime, $startedBy, $template);

            return redirect()->back()->with('success', 'Maintenance Mode berhasil DIAKTIFKAN. Hanya role Developer yang dapat mengakses sistem.');
        } elseif ($action === 'deactivate') {
            MaintenanceService::deactivate();

            return redirect()->back()->with('success', 'Maintenance Mode berhasil DINONAKTIFKAN. Seluruh user dapat mengakses sistem kembali.');
        } elseif ($action === 'change_template') {
            $template = $request->input('template', 'animated');
            MaintenanceService::setTemplate($template);

            $names = [
                'animated' => 'Template 3 (Light Dynamic & Motion Animated)',
                'light'    => 'Template 2 (Light Clean & Elegant)',
                'dark'     => 'Template 1 (Dark Tech & Glassmorphism)',
            ];
            $templateName = $names[$template] ?? $template;
            return redirect()->back()->with('success', "Tema tampilan halaman maintenance berhasil diubah menjadi: {$templateName}");
        } elseif ($action === 'schedule') {
            $startTime = $request->input('plan_start_time');
            if (empty($startTime)) {
                return redirect()->back()->with('error', 'Waktu mulai maintenance wajib diisi.');
            }

            $endTime = $request->input('plan_end_time');
            $warnMinutes = (int) $request->input('plan_warn_minutes', 30);
            $message = $request->input('plan_message', 'Pemberitahuan: Sistem akan memasuki masa pemeliharaan (Maintenance). Mohon segera simpan pekerjaan dan transaksi Anda.');
            $autoActivate = $request->has('auto_activate');

            MaintenanceService::schedulePlan($startTime, $endTime, $warnMinutes, $message, $autoActivate, $startedBy);

        } elseif ($action === 'update_bgm') {
            $bgmEnabled = $request->has('bgm_enabled');
            $bgmTitle = $request->input('bgm_title', 'Lofi Ambient Relaxing');
            $bgmUrl = $request->input('bgm_url');

            // 1. Check if Base64 encoded audio was sent from client
            if ($request->filled('bgm_base64')) {
                $base64Data = $request->input('bgm_base64');
                if (preg_match('/^data:audio\/(\w+);base64,/', $base64Data, $matches) || preg_match('/^data:([^;]+);base64,/', $base64Data, $matches)) {
                    $mime = $matches[1];
                    $ext = 'mp3';
                    if (str_contains($mime, 'wav')) $ext = 'wav';
                    elseif (str_contains($mime, 'ogg')) $ext = 'ogg';
                    elseif (str_contains($mime, 'm4a') || str_contains($mime, 'mp4')) $ext = 'm4a';

                    $data = substr($base64Data, strpos($base64Data, ',') + 1);
                    $decoded = base64_decode($data);
                    if ($decoded !== false && strlen($decoded) > 0) {
                        $rawName = $request->input('bgm_title') ?: 'custom_bgm';
                        $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $rawName);
                        $filename = 'bgm_' . time() . '_' . substr($cleanName, 0, 30) . '.' . $ext;
                        $targetDir = public_path('assets/audio');
                        if (!file_exists($targetDir)) {
                            mkdir($targetDir, 0777, true);
                        }
                        file_put_contents($targetDir . '/' . $filename, $decoded);
                        $bgmUrl = '/assets/audio/' . $filename;
                        $bgmTitle = $rawName;
                    }
                }
            }
            // 2. Standard multipart upload fallback
            elseif ($request->hasFile('bgm_file') && $request->file('bgm_file')->isValid()) {
                $file = $request->file('bgm_file');
                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'aac'])) {
                    return redirect()->back()->with('error', 'Format file harus berupa audio (.mp3, .wav, .ogg, .m4a).');
                }

                $rawName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $rawName);
                $filename = 'bgm_' . time() . '_' . substr($cleanName, 0, 30) . '.' . $ext;
                $targetDir = public_path('assets/audio');
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $file->move($targetDir, $filename);
                $bgmUrl = '/assets/audio/' . $filename;

                if (empty(trim($request->input('bgm_title') ?? '')) || $request->input('bgm_title') === 'Water Lily - Lofi Piano' || $request->input('bgm_title') === 'Lofi Ambient Relaxing - Water Lily') {
                    $bgmTitle = $rawName;
                }
            } elseif (isset($_FILES['bgm_file']) && $_FILES['bgm_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                // If standard upload threw INI_SIZE and no base64 was sent
                $errCode = $_FILES['bgm_file']['error'];
                if ($errCode === UPLOAD_ERR_INI_SIZE || $errCode === UPLOAD_ERR_FORM_SIZE) {
                    return redirect()->back()->with('error', 'Ukuran file audio melebihi batas server.');
                }
            }

            MaintenanceService::setBgm($bgmEnabled, $bgmUrl, $bgmTitle);

            return redirect()->back()->with('success', "Pengaturan Background Music (BGM) berhasil diperbarui: {$bgmTitle}");
        } elseif ($action === 'cancel_plan') {
            MaintenanceService::cancelPlan();

            return redirect()->back()->with('success', 'Jadwal Pemeliharaan berhasil DIBATALKAN.');
        }

        return redirect()->back();
    }

    public function uploadChunk(Request $request)
    {
        if (!Auth::user()?->isDeveloper()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $fileUuid = $request->input('file_uuid');
        $chunkIndex = (int) $request->input('chunk_index', 0);
        $totalChunks = (int) $request->input('total_chunks', 1);
        $originalName = $request->input('filename', 'custom_audio.mp3');
        $bgmTitle = $request->input('bgm_title');

        if (empty($fileUuid) || !$request->hasFile('chunk_file')) {
            return response()->json(['success' => false, 'message' => 'Invalid chunk payload'], 400);
        }

        $tempDir = storage_path('app/temp_audio/' . preg_replace('/[^a-zA-Z0-9_-]/', '', $fileUuid));
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        // Save chunk
        $chunkFile = $request->file('chunk_file');
        $chunkFile->move($tempDir, 'chunk_' . $chunkIndex);

        // If this is the last chunk, merge all chunks
        if ($chunkIndex === $totalChunks - 1) {
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION)) ?: 'mp3';
            if (!in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'aac'])) {
                $ext = 'mp3';
            }

            $rawName = pathinfo($originalName, PATHINFO_FILENAME);
            $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $rawName);
            $finalFilename = 'bgm_' . time() . '_' . substr($cleanName, 0, 35) . '.' . $ext;
            $targetDir = public_path('assets/audio');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $finalPath = $targetDir . '/' . $finalFilename;
            $out = fopen($finalPath, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $tempDir . '/chunk_' . $i;
                if (file_exists($chunkPath)) {
                    $in = fopen($chunkPath, 'rb');
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                    fclose($in);
                    @unlink($chunkPath);
                }
            }
            fclose($out);
            @rmdir($tempDir);

            $bgmUrl = '/assets/audio/' . $finalFilename;
            $finalTitle = !empty(trim($bgmTitle ?? '')) ? trim($bgmTitle) : $rawName;

            MaintenanceService::setBgm(true, $bgmUrl, $finalTitle);

            return response()->json([
                'success' => true,
                'message' => "Audio berhasil diunggah dan disimpan: {$finalTitle}",
                'bgm_url' => $bgmUrl,
                'bgm_title' => $finalTitle,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Chunk {$chunkIndex} of {$totalChunks} received",
            'chunk_index' => $chunkIndex,
            'percent' => round((($chunkIndex + 1) / $totalChunks) * 100),
        ]);
    }
}
