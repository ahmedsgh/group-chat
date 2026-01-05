<?php

namespace App\Livewire\Admin\Groups;

use App\Models\Group;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Groups')]
class GroupIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public bool $showDeleteModal = false;
    public ?int $groupToDelete = null;
    public string $groupToDeleteName = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id, string $name): void
    {
        $this->groupToDelete = $id;
        $this->groupToDeleteName = $name;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->groupToDelete = null;
        $this->groupToDeleteName = '';
    }

    public function deleteGroup(): void
    {
        if ($this->groupToDelete) {
            Group::find($this->groupToDelete)?->delete();
            session()->flash('success', 'Group deleted successfully.');
        }
        $this->cancelDelete();
    }

    public function render()
    {
        $query = Group::withCount('members');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $groups = $query->latest()->paginate(12);

        return view('livewire.admin.groups.group-index', [
            'groups' => $groups,
        ]);
    }
}
