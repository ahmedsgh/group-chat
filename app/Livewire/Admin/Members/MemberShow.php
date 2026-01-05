<?php

namespace App\Livewire\Admin\Members;

use App\Models\Member;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Member Details')]
class MemberShow extends Component
{
    public Member $member;

    public function mount(Member $member): void
    {
        $this->member = $member->load('groups');
    }

    public function render()
    {
        return view('livewire.admin.members.member-show');
    }
}
