<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    /**
     * Get the members in this group
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'group_member')
            ->withTimestamps();
    }

    /**
     * Get the messages sent to this group
     */
    public function messages(): BelongsToMany
    {
        return $this->belongsToMany(Message::class, 'message_group')
            ->withTimestamps();
    }

    /**
     * Get the latest message for this group
     */
    public function latestMessage()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Get the member count for this group
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }
}
