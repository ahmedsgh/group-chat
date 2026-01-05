<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'content',
    ];

    /**
     * Get the admin user who sent this message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the groups this message was sent to
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'message_group')
            ->withTimestamps();
    }

    /**
     * Get the attachments for this message
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * Get the read receipts for this message
     */
    public function reads(): HasMany
    {
        return $this->hasMany(MessageRead::class);
    }

    /**
     * Check if a specific member has read this message
     */
    public function isReadByMember(Member $member): bool
    {
        return $this->reads()->where('member_id', $member->id)->exists();
    }

    /**
     * Get members who have read this message
     */
    public function readByMembers()
    {
        return Member::whereIn('id', $this->reads()->pluck('member_id'));
    }
}
