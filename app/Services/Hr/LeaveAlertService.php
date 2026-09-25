<?php

namespace App\Services\Hr;

use App\Models\AppSetting;
use App\Models\HrLeaveRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LeaveAlertService
{
    public const DEFAULT_ROLES = [
        'Finance',
        'Finance Manager',
        'Admin',
        'Developer'
    ];

    /**
     * Ambil konfigurasi penerima notifikasi / alert approval cuti & izin.
     */
    public static function getSettings(): array
    {
        $raw = AppSetting::get('hr_leave_alert_config');
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return [
                    'enabled' => (bool) ($decoded['enabled'] ?? true),
                    'roles' => is_array($decoded['roles'] ?? null) ? $decoded['roles'] : self::DEFAULT_ROLES,
                    'user_ids' => is_array($decoded['user_ids'] ?? null) ? array_map('intval', $decoded['user_ids']) : [],
                ];
            }
        }

        return [
            'enabled' => true,
            'roles' => self::DEFAULT_ROLES,
            'user_ids' => [],
        ];
    }

    /**
     * Simpan konfigurasi penerima notifikasi / alert approval cuti & izin.
     */
    public static function saveSettings(array $data): void
    {
        $config = [
            'enabled' => !empty($data['enabled']),
            'roles' => is_array($data['roles'] ?? null) ? array_values(array_unique(array_filter($data['roles']))) : [],
            'user_ids' => is_array($data['user_ids'] ?? null) ? array_values(array_unique(array_map('intval', array_filter($data['user_ids'])))) : [],
        ];

        AppSetting::set('hr_leave_alert_config', json_encode($config));
    }

    /**
     * Cek apakah user yang login berhak menerima notifikasi & alert modal approval cuti/izin.
     */
    public static function isUserEligible($user): bool
    {
        if (!$user) {
            return false;
        }

        // Akun Developer selalu memiliki akses review/audit
        if (method_exists($user, 'isDeveloper') && $user->isDeveloper()) {
            return true;
        }

        $config = self::getSettings();
        if (!$config['enabled']) {
            return false;
        }

        // Cek ID User spesifik
        if (!empty($config['user_ids']) && in_array((int) $user->id, $config['user_ids'], true)) {
            return true;
        }

        // Cek Role User
        $userRole = $user->role ?? null;
        $rawRole = method_exists($user, 'getRawOriginal') ? $user->getRawOriginal('role') : $userRole;

        foreach ($config['roles'] as $role) {
            if (strcasecmp($role, (string) $userRole) === 0 || strcasecmp($role, (string) $rawRole) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Jumlah pengajuan cuti/izin yang berstatus Pending.
     */
    public static function getPendingCount(): int
    {
        return HrLeaveRequest::where('status', 'Pending')->count();
    }

    /**
     * Daftar pengajuan cuti/izin yang berstatus Pending untuk modal quick review.
     */
    public static function getPendingRequests(int $limit = 20): Collection
    {
        return HrLeaveRequest::with([
            'employee.user',
            'employee.department',
            'leaveType'
        ])
            ->where('status', 'Pending')
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();
    }

    /**
     * Daftar semua role unik yang ada di database.
     */
    public static function getAllAvailableRoles(): array
    {
        $dbRoles = DB::table('users')
            ->select('role')
            ->whereNotNull('role')
            ->where('role', '!=', '')
            ->distinct()
            ->pluck('role')
            ->toArray();

        $standardRoles = [
            'Admin',
            'Finance',
            'Finance Manager',
            'Accounting',
            'Developer',
            'Sales',
            'Technician',
            'ServiceM',
            'Logistic',
            'Support',
            'Project Manager',
            'Client Vendor'
        ];

        return array_values(array_unique(array_merge($standardRoles, $dbRoles)));
    }

    /**
     * Daftar user aktif untuk pilihan penerima alert spesifik per akun.
     */
    public static function getAllEligibleUsers(): Collection
    {
        return User::where(function ($q) {
            $q->where('active', '1')->orWhere('active', 1)->orWhereNull('active');
        })
            ->where('role', '!=', 'Client')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email', 'role']);
    }
}
