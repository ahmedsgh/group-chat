<?php

namespace App\Livewire\Member;

use App\Models\Group;
use App\Models\Member;
use App\Models\MessageRead;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.member')]
#[Title('Messages')]
class Messages extends Component
{
    #[Url]
    public ?int $selectedGroupId = null;

    public ?Member $member = null;

    public function mount(): void
    {
        /** @var Member|null $member */
        $member = auth()->guard('member')->user();
        $this->member = $member;

        if ($this->member) {
            $this->member->update(['last_seen_at' => now()]);
        }
    }

    public function selectGroup(int $groupId): void
    {
        $this->selectedGroupId = $groupId;
        $this->markMessagesAsRead();
    }

    protected function markMessagesAsRead(): void
    {
        if (!$this->selectedGroupId || !$this->member) {
            return;
        }

        $group = $this->member->groups()->find($this->selectedGroupId);
        if (!$group) {
            return;
        }

        foreach ($group->messages as $message) {
            MessageRead::firstOrCreate([
                'message_id' => $message->id,
                'member_id' => $this->member->id,
            ], [
                'read_at' => now(),
            ]);
        }
    }

    // Real-time updates handled via wire:poll in the view

    public function render()
    {
        if (!$this->member) {
            return redirect()->route('login');
        }

        // Update last seen on each render
        $this->member->update(['last_seen_at' => now()]);

        $groups = $this->member->groups()
            ->withCount('members')
            ->orderBy('name')
            ->get();

        $selectedGroup = null;
        $messages = collect();

        if ($this->selectedGroupId) {
            $selectedGroup = $groups->firstWhere('id', $this->selectedGroupId);

            if ($selectedGroup) {
                $messages = $selectedGroup->messages()
                    ->with(['user', 'attachments'])
                    ->orderBy('created_at', 'asc')
                    ->get();

                // Mark messages as read
                $this->markMessagesAsRead();
            }
        }

        return view('livewire.member.messages', [
            'groups' => $groups,
            'selectedGroup' => $selectedGroup,
            'messages' => $messages,
        ]);
    }
}
