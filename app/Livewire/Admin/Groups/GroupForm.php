<?php

namespace App\Livewire\Admin\Groups;

use App\Models\Group;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class GroupForm extends Component
{
    public ?Group $group = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    // Existing members search and selection
    public string $memberSearch = '';
    public array $searchResults = [];
    public array $selectedMembers = [];

    // New members to create
    public array $newMembers = [];

    // Current members (for edit mode)
    public array $currentMembers = [];

    public function mount(?Group $group = null): void
    {
        if ($group && $group->exists) {
            $this->group = $group;
            $this->name = $group->name;
            $this->currentMembers = $group->members()
                ->get(['members.id', 'name', 'phone', 'type'])
                ->map(fn($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'phone' => $m->phone,
                    'type' => $m->type,
                ])
                ->toArray();
        }
    }

    public function updatedMemberSearch(): void
    {
        if (strlen($this->memberSearch) < 2) {
            $this->searchResults = [];
            return;
        }

        $excludeIds = array_merge(
            array_column($this->selectedMembers, 'id'),
            array_column($this->currentMembers, 'id')
        );

        $this->searchResults = Member::where(function ($q) {
            $q->where('name', 'like', "%{$this->memberSearch}%")
                ->orWhere('phone', 'like', "%{$this->memberSearch}%");
        })
            ->whereNotIn('id', $excludeIds)
            ->limit(10)
            ->get(['id', 'name', 'phone', 'type'])
            ->map(fn($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'phone' => $m->phone,
                'type' => $m->type,
            ])
            ->toArray();
    }

    public function addMember(int $memberId): void
    {
        $member = collect($this->searchResults)->firstWhere('id', $memberId);
        if ($member && !collect($this->selectedMembers)->contains('id', $memberId)) {
            $this->selectedMembers[] = $member;
        }
        $this->memberSearch = '';
        $this->searchResults = [];
    }

    public function removeMember(int $memberId): void
    {
        $this->selectedMembers = array_values(
            array_filter($this->selectedMembers, fn($m) => $m['id'] !== $memberId)
        );
    }

    public function removeCurrentMember(int $memberId): void
    {
        $this->currentMembers = array_values(
            array_filter($this->currentMembers, fn($m) => $m['id'] !== $memberId)
        );
    }

    public function addNewMember(): void
    {
        $tempId = uniqid();
        $this->newMembers[$tempId] = [
            'temp_id' => $tempId,
            'name' => '',
            'phone' => '',
            'type' => 'student',
            'gender' => 'male',
        ];
    }

    public function removeNewMember(string $tempId): void
    {
        unset($this->newMembers[$tempId]);
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'newMembers.*.name' => 'required|string|max:255',
            'newMembers.*.phone' => 'required|string|max:20|distinct|unique:members,phone',
            'newMembers.*.type' => 'required|in:student,parent',
            'newMembers.*.gender' => 'required|in:male,female',
        ], [
            'newMembers.*.name.required' => 'Name is required for new member.',
            'newMembers.*.phone.required' => 'Phone is required for new member.',
            'newMembers.*.phone.unique' => 'This phone number already exists.',
            'newMembers.*.phone.distinct' => 'Phone numbers must be unique.',
        ]);

        DB::transaction(function () {
            if ($this->group) {
                $this->group->update(['name' => $this->name]);
            } else {
                $this->group = Group::create(['name' => $this->name]);
            }

            // Collect all member IDs to sync
            $memberIds = array_merge(
                array_column($this->currentMembers, 'id'),
                array_column($this->selectedMembers, 'id')
            );

            // Create new members and add their IDs
            foreach ($this->newMembers as $memberData) {
                $data = collect($memberData)->except('temp_id')->toArray();
                $member = Member::create($data);
                $memberIds[] = $member->id;
            }

            $this->group->members()->sync($memberIds);
        });

        session()->flash('success', $this->group->wasRecentlyCreated ? 'Group created successfully.' : 'Group updated successfully.');
        $this->redirect(route('admin.groups.index'), navigate: true);
    }

    public function getTitle(): string
    {
        return $this->group ? 'Edit Group' : 'Create Group';
    }

    public function render()
    {
        return view('livewire.admin.groups.group-form', [
            'isEditing' => (bool) $this->group?->exists,
        ])->title($this->getTitle());
    }
}
