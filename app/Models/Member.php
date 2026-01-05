<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'type',
        'gender',
        'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    /**
     * Get the password for the member (not used, but required by Authenticatable)
     */
    public function getAuthPassword()
    {
        return null; // We use OTP, not passwords
    }

    /**
     * Check if the member is currently online (last seen within 10 seconds)
     */
    public function isOnline(): bool
    {
        if (!$this->last_seen_at) {
            return false;
        }

        return $this->last_seen_at->diffInSeconds(now()) < 10;
    }

    /**
     * Get the groups this member belongs to
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_member')
            ->withTimestamps();
    }

    /**
     * Get message read records for this member
     */
    public function messageReads(): HasMany
    {
        return $this->hasMany(MessageRead::class);
    }

    /**
     * Get OTPs for this member
     */
    public function otps(): HasMany
    {
        return $this->hasMany(MemberOtp::class, 'phone', 'phone');
    }
}
