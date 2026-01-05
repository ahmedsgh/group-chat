<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberOtp extends Model
{
    protected $fillable = [
        'phone',
        'otp',
        'expires_at',
        'used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    /**
     * Check if the OTP is valid (not expired and not used)
     */
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    /**
     * Check if the OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Mark the OTP as used
     */
    public function markAsUsed(): void
    {
        $this->update(['used' => true]);
    }

    /**
     * Generate a new OTP code
     */
    public static function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Check if a phone can request a new OTP (rate limiting)
     */
    public static function canRequestNewOtp(string $phone): bool
    {
        $lastOtp = static::where('phone', $phone)
            ->where('used', false)
            ->latest()
            ->first();

        if (!$lastOtp) {
            return true;
        }

        // Allow new OTP request 5 minutes after the last one
        return $lastOtp->created_at->addMinutes(5)->isPast();
    }

    /**
     * Get remaining seconds until new OTP can be requested
     */
    public static function getRemainingCooldown(string $phone): int
    {
        $lastOtp = static::where('phone', $phone)
            ->where('used', false)
            ->latest()
            ->first();

        if (!$lastOtp) {
            return 0;
        }

        $canRequestAt = $lastOtp->created_at->addMinutes(5);

        if ($canRequestAt->isPast()) {
            return 0;
        }

        return (int) now()->diffInSeconds($canRequestAt);
    }
}
