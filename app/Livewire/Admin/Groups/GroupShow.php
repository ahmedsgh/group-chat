<?php

namespace App\Livewire\Admin\Groups;

use App\Models\Group;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Group Details')]
class GroupShow extends Component
{
    public Group $group;

    public function mount(Group $group): void
    {
        $this->group = $group->load('members');
    }

    public function render()
    {
        return view('livewire.admin.groups.group-show');
    }
}
