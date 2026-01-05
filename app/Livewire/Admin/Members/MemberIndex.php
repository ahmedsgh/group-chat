<?php

namespace App\Livewire\Admin\Members;

use App\Models\Member;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Members')]
class MemberIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $type = '';

    #[Url]
    public string $gender = '';

    public bool $showDeleteModal = false;
    public ?int $memberToDelete = null;
    public string $memberToDeleteName = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function updatingGender(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->type = '';
        $this->gender = '';
        $this->resetPage();
    }

    public function confirmDelete(int $id, string $name): void
    {
        $this->memberToDelete = $id;
        $this->memberToDeleteName = $name;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->memberToDelete = null;
        $this->memberToDeleteName = '';
    }

    public function deleteMember(): void
    {
        if ($this->memberToDelete) {
            Member::find($this->memberToDelete)?->delete();
            session()->flash('success', 'Member deleted successfully.');
        }
        $this->cancelDelete();
    }

    public function render()
    {
        $query = Member::with('groups');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%");
            });
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->gender) {
            $query->where('gender', $this->gender);
        }

        $members = $query->latest()->paginate(15);

        return view('livewire.admin.members.member-index', [
            'members' => $members,
        ]);
    }
}
