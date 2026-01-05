<?php

namespace App\Livewire\Admin\Members;

use App\Models\Group;
use App\Models\Member;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class MemberForm extends Component
{
    public ?Member $member = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:20')]
    public string $phone = '';

    #[Validate('required|in:student,parent')]
    public string $type = '';

    #[Validate('required|in:male,female')]
    public string $gender = '';

    // Group search and selection
    public string $groupSearch = '';
    public array $groupSearchResults = [];
    public array $selectedGroups = [];

    public function mount(?Member $member = null): void
    {
        if ($member && $member->exists) {
            $this->member = $member;
            $this->name = $member->name;
            $this->phone = $member->phone;
            $this->type = $member->type;
            $this->gender = $member->gender;
            $this->selectedGroups = $member->groups()
                ->get(['groups.id', 'name'])
                ->map(fn($g) => ['id' => $g->id, 'name' => $g->name])
                ->toArray();
        }
    }

    public function updatedGroupSearch(): void
    {
        if (strlen($this->groupSearch) < 2) {
            $this->groupSearchResults = [];
            return;
        }

        $excludeIds = array_column($this->selectedGroups, 'id');

        $this->groupSearchResults = Group::where('name', 'like', "%{$this->groupSearch}%")
            ->whereNotIn('id', $excludeIds)
            ->limit(10)
            ->get(['id', 'name'])
            ->map(fn($g) => ['id' => $g->id, 'name' => $g->name])
            ->toArray();
    }

    public function addGroup(int $groupId): void
    {
        $group = collect($this->groupSearchResults)->firstWhere('id', $groupId);
        if ($group && !collect($this->selectedGroups)->contains('id', $groupId)) {
            $this->selectedGroups[] = $group;
        }
        $this->groupSearch = '';
        $this->groupSearchResults = [];
    }

    public function removeGroup(int $groupId): void
    {
        $this->selectedGroups = array_values(
            array_filter($this->selectedGroups, fn($g) => $g['id'] !== $groupId)
        );
    }

    public function rules(): array
    {
        $phoneRule = 'required|string|max:20|unique:members,phone';
        if ($this->member) {
            $phoneRule .= ',' . $this->member->id;
        }

        return [
            'name' => 'required|string|max:255',
            'phone' => $phoneRule,
            'type' => 'required|in:student,parent',
            'gender' => 'required|in:male,female',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $groupIds = array_column($this->selectedGroups, 'id');

        if ($this->member) {
            $this->member->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'type' => $this->type,
                'gender' => $this->gender,
            ]);
            $this->member->groups()->sync($groupIds);
            session()->flash('success', 'Member updated successfully.');
        } else {
            $member = Member::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'type' => $this->type,
                'gender' => $this->gender,
            ]);
            $member->groups()->sync($groupIds);
            session()->flash('success', 'Member created successfully.');
        }

        $this->redirect(route('admin.members.index'), navigate: true);
    }

    #[Title('Add New Member')]
    public function getTitle(): string
    {
        return $this->member ? 'Edit Member' : 'Add New Member';
    }

    public function render()
    {
        return view('livewire.admin.members.member-form', [
            'isEditing' => (bool) $this->member,
        ])->title($this->getTitle());
    }
}
