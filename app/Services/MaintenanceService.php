<?php

namespace App\Services;

class MaintenanceService
{
    protected static function getFilePath(): string
    {
        $dir = storage_path('framework');
        if (!file_exists($dir)) {
            @mkdir($dir, 0777, true);
        }
        return $dir . '/developer_maintenance.json';
    }

    public static function getDefaultState(): array
    {
        return [
            'is_active' => false,
            'message' => 'Sistem sedang dalam proses pemeliharaan & pembaruan dari Staging ke Production.',
            'end_time' => null,
            'started_at' => null,
            'started_by' => null,
            // Planning Maintenance fields
            'is_planned' => false,
            'plan_start_time' => null,
            'plan_end_time' => null,
            'plan_warn_minutes' => 30,
            'plan_message' => 'Pemberitahuan: Sistem akan memasuki masa pemeliharaan (Maintenance). Mohon segera simpan pekerjaan dan transaksi Anda.',
            'auto_activate' => true,
            'planned_by' => null,
            'planned_at' => null,
            // UI Template setting ('light', 'dark', 'animated')
            'template' => 'animated',
            // Background Music (BGM) settings
            'bgm_enabled' => true,
            'bgm_url' => '/assets/audio/Water_Lily.mp3',
            'bgm_title' => 'Lofi Ambient Relaxing - Water Lily',
        ];
    }

    public static function setBgm(bool $enabled, ?string $url = null, ?string $title = null): bool
    {
        $current = self::getDetails();
        $data = array_merge($current, [
            'bgm_enabled' => $enabled,
            'bgm_url' => !empty(trim($url ?? '')) ? trim($url) : ($current['bgm_url'] ?? '/assets/audio/Water_Lily.mp3'),
            'bgm_title' => !empty(trim($title ?? '')) ? trim($title) : ($current['bgm_title'] ?? 'Lofi Ambient Relaxing'),
        ]);

        return (bool) file_put_contents(self::getFilePath(), json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function setTemplate(string $template): bool
    {
        $current = self::getDetails();
        $template = in_array($template, ['dark', 'light', 'animated']) ? $template : 'animated';
        $data = array_merge($current, [
            'template' => $template,
        ]);

        return (bool) file_put_contents(self::getFilePath(), json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function isActive(): bool
    {
        self::checkAutoTransition();
        $details = self::getDetails();
        return (bool) ($details['is_active'] ?? false);
    }

    public static function getDetails(): array
    {
        $file = self::getFilePath();
        if (!file_exists($file)) {
            return self::getDefaultState();
        }

        try {
            $content = file_get_contents($file);
            $data = json_decode($content, true);
            if (is_array($data)) {
                return array_merge(self::getDefaultState(), $data);
            }
        } catch (\Throwable $e) {
            // safe fallback
        }

        return self::getDefaultState();
    }

    public static function activate(string $message = '', ?string $endTime = null, ?string $startedBy = null, ?string $template = null): bool
    {
        $current = self::getDetails();
        $data = array_merge($current, [
            'is_active' => true,
            'message' => !empty(trim($message)) ? trim($message) : 'Sistem sedang dalam proses pemeliharaan & pembaruan dari Staging ke Production.',
            'end_time' => $endTime ? trim($endTime) : null,
            'started_at' => date('Y-m-d H:i:s'),
            'started_by' => $startedBy ?? 'Developer',
            'template' => $template && in_array($template, ['dark', 'light', 'animated']) ? $template : ($current['template'] ?? 'animated'),
            // Deactivate plan once hard maintenance starts
            'is_planned' => false,
        ]);

        return (bool) file_put_contents(self::getFilePath(), json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function deactivate(): bool
    {
        $current = self::getDetails();
        $data = array_merge($current, [
            'is_active' => false,
            'message' => 'Sistem sedang dalam proses pemeliharaan & pembaruan dari Staging ke Production.',
            'end_time' => null,
            'started_at' => null,
            'started_by' => null,
        ]);

        return (bool) file_put_contents(self::getFilePath(), json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function schedulePlan(
        string $startTime,
        ?string $endTime = null,
        int $warnMinutes = 30,
        string $message = '',
        bool $autoActivate = true,
        ?string $plannedBy = null
    ): bool {
        $current = self::getDetails();
        $data = array_merge($current, [
            'is_planned' => true,
            'plan_start_time' => trim($startTime),
            'plan_end_time' => $endTime ? trim($endTime) : null,
            'plan_warn_minutes' => $warnMinutes > 0 ? $warnMinutes : 30,
            'plan_message' => !empty(trim($message)) ? trim($message) : 'Pemberitahuan: Sistem akan memasuki masa pemeliharaan (Maintenance). Mohon segera simpan pekerjaan dan transaksi Anda.',
            'auto_activate' => $autoActivate,
            'planned_by' => $plannedBy ?? 'Developer',
            'planned_at' => date('Y-m-d H:i:s'),
        ]);

        return (bool) file_put_contents(self::getFilePath(), json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function cancelPlan(): bool
    {
        $current = self::getDetails();
        $data = array_merge($current, [
            'is_planned' => false,
            'plan_start_time' => null,
            'plan_end_time' => null,
            'planned_by' => null,
            'planned_at' => null,
        ]);

        return (bool) file_put_contents(self::getFilePath(), json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Check if planned maintenance has reached its start time and should auto-activate.
     */
    protected static function checkAutoTransition(): void
    {
        $file = self::getFilePath();
        if (!file_exists($file)) return;

        try {
            $content = file_get_contents($file);
            $data = json_decode($content, true);
            if (!is_array($data)) return;

            if (
                !empty($data['is_planned']) &&
                !empty($data['auto_activate']) &&
                empty($data['is_active']) &&
                !empty($data['plan_start_time'])
            ) {
                $startTime = self::parseTimestamp($data['plan_start_time']);
                if ($startTime && time() >= $startTime) {
                    // Time has arrived -> auto-activate Hard Maintenance Mode!
                    $data['is_active'] = true;
                    $data['message'] = $data['plan_message'] ?? 'Sistem sedang dalam proses pemeliharaan & pembaruan dari Staging ke Production.';
                    $data['end_time'] = $data['plan_end_time'] ?? null;
                    $data['started_at'] = date('Y-m-d H:i:s');
                    $data['started_by'] = ($data['planned_by'] ?? 'Developer') . ' (Auto-Scheduled)';
                    $data['is_planned'] = false;

                    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }

    /**
     * Helper to parse flexible date/time string into a UNIX timestamp.
     */
    public static function parseTimestamp(string $str): ?int
    {
        $str = trim($str);
        if (empty($str)) return null;

        // 1. If clean ISO or full date (e.g. 2026-08-14 12:00:00)
        $clean = preg_replace('/\s+(WIB|WITA|WIT)/i', '', $str);
        $ts = strtotime($clean);
        if ($ts !== false) return $ts;

        // 2. If range like "11:30 - 12:00", take start time
        if (strpos($str, '-') !== false && !preg_match('/\d{4}-\d{2}/', $str)) {
            $parts = explode('-', $str);
            $str = trim($parts[0]);
        }

        // 3. If time format like "12:00" or "12.00"
        if (preg_match('/(\d{1,2})[:.](\d{2})(?:[:.](\d{2}))?/', $str, $matches)) {
            $h = (int) $matches[1];
            $m = (int) $matches[2];
            $s = isset($matches[3]) ? (int) $matches[3] : 0;
            return mktime($h, $m, $s, (int)date('n'), (int)date('j'), (int)date('Y'));
        }

        return null;
    }
}
