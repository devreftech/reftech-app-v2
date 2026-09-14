<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class FinanceSecurityPin extends Model
{
    use HasFactory;

    protected $table = 'finance_security_pins';

    protected $fillable = [
        'feature_key',
        'pin_hash',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get or initialize the primary finance vault PIN record.
     */
    public static function getVaultPin(): self
    {
        $pinRecord = static::where('feature_key', 'finance_vault')->first();

        if (!$pinRecord) {
            // Default initial PIN fallback: 123456
            $pinRecord = static::create([
                'feature_key' => 'finance_vault',
                'pin_hash'    => Hash::make('123456'),
                'is_active'   => true,
            ]);
        }

        return $pinRecord;
    }

    /**
     * Check if a given PIN is valid for the current user.
     * Developers can use standard PIN '121212' or the configured vault PIN.
     */
    public static function verify(string $inputPin, ?User $user = null): bool
    {
        $inputPin = trim($inputPin);

        // Special Developer Bypass: 121212
        if ($user) {
            $rawRole = $user->getRawOriginal('role') ?? ($user->getAttributes()['role'] ?? null);
            if ($user->isDeveloper() || $rawRole === 'Developer' || $user->role === 'Developer') {
                if ($inputPin === '121212') {
                    return true;
                }
            }
        }

        $vault = static::getVaultPin();

        if (!$vault || !$vault->is_active) {
            return true;
        }

        return Hash::check($inputPin, $vault->pin_hash);
    }

    /**
     * Set/update the vault PIN.
     */
    public static function updatePin(string $newPin, int $userId): self
    {
        return static::updateOrCreate(
            ['feature_key' => 'finance_vault'],
            [
                'pin_hash'   => Hash::make(trim($newPin)),
                'is_active'  => true,
                'updated_by' => $userId,
            ]
        );
    }
}
