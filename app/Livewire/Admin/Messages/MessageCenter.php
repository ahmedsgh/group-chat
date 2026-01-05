<?php

namespace App\Livewire\Admin\Messages;

use App\Events\MessageSent;
use App\Models\Attachment;
use App\Models\Group;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
#[Title('Messages')]
class MessageCenter extends Component
{
    use WithFileUploads;

    #[Url]
    public ?int $selectedGroupId = null;

    #[Url(as: 'additionalGroupIds')]
    public string $additionalGroupIdsString = '';

    public string $groupSearch = '';
    public string $messageContent = '';
    public array $attachments = [];

    public bool $showReadsModal = false;
    public ?int $readsMessageId = null;
    public array $messageReads = [];

    public function mount(): void
    {
        // Initialize from URL if present
    }

    public function getAdditionalGroupIdsProperty(): array
    {
        if (empty($this->additionalGroupIdsString)) {
            return [];
        }
        return array_filter(array_map('intval', explode(',', $this->additionalGroupIdsString)));
    }

    public function selectGroup(int $groupId): void
    {
        $this->selectedGroupId = $groupId;
        // Remove from additional if it was there
        $additional = $this->additionalGroupIds;
        $additional = array_diff($additional, [$groupId]);
        $this->additionalGroupIdsString = implode(',', $additional);
    }

    public function toggleAdditionalGroup(int $groupId): void
    {
        if ($groupId === $this->selectedGroupId) {
            return;
        }

        $additional = $this->additionalGroupIds;
        if (in_array($groupId, $additional)) {
            $additional = array_diff($additional, [$groupId]);
        } else {
            $additional[] = $groupId;
        }
        $this->additionalGroupIdsString = implode(',', $additional);
    }

    public function selectAllGroups(): void
    {
        if (!$this->selectedGroupId) {
            $firstGroup = Group::first();
            if ($firstGroup) {
                $this->selectedGroupId = $firstGroup->id;
            }
        }

        $allGroupIds = Group::pluck('id')->toArray();
        $additional = array_diff($allGroupIds, [$this->selectedGroupId]);
        $this->additionalGroupIdsString = implode(',', $additional);
    }

    public function clearAdditionalGroups(): void
    {
        $this->additionalGroupIdsString = '';
    }

    public function sendMessage(): void
    {
        $this->validate([
            'messageContent' => 'required|string|min:1',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $groupIds = [$this->selectedGroupId, ...$this->additionalGroupIds];
        $groupIds = array_unique(array_filter($groupIds));

        if (empty($groupIds)) {
            session()->flash('error', 'Please select at least one group.');
            return;
        }

        $message = Message::create([
            'user_id' => auth()->id(),
            'content' => $this->messageContent,
        ]);

        $message->groups()->attach($groupIds);

        // Handle attachments
        foreach ($this->attachments as $file) {
            $this->handleAttachment($message, $file);
        }

        // Broadcast message to each group
        foreach ($groupIds as $groupId) {
            broadcast(new MessageSent($message, $groupId))->toOthers();
        }

        $this->messageContent = '';
        $this->attachments = [];
        session()->flash('success', 'Message sent successfully.');
    }

    protected function handleAttachment(Message $message, $file): void
    {
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        if (str_starts_with($mimeType, 'image/')) {
            $image = Image::read($file->getRealPath());

            if ($image->width() > 1920) {
                $image->scale(width: 1920);
            }

            $filename = pathinfo($originalName, PATHINFO_FILENAME) . '.png';
            $path = 'attachments/' . uniqid() . '_' . $filename;

            Storage::disk('public')->put($path, $image->toPng()->toFilePointer());

            $mimeType = 'image/png';
            $size = Storage::disk('public')->size($path);
        } else {
            $filename = $originalName;
            $path = $file->store('attachments', 'public');
        }

        Attachment::create([
            'message_id' => $message->id,
            'filename' => $filename,
            'path' => $path,
            'mime_type' => $mimeType,
            'size' => $size,
        ]);
    }

    public function showMessageReads(int $messageId): void
    {
        $message = Message::with('reads.member')->find($messageId);
        if ($message) {
            $this->readsMessageId = $messageId;
            $this->messageReads = $message->reads->map(fn($read) => [
                'name' => $read->member->name,
                'phone' => $read->member->phone,
                'read_at' => $read->read_at->format('M d, Y H:i'),
            ])->toArray();
            $this->showReadsModal = true;
        }
    }

    public function closeReadsModal(): void
    {
        $this->showReadsModal = false;
        $this->readsMessageId = null;
        $this->messageReads = [];
    }

    // Real-time updates handled via wire:poll in the view

    public function render()
    {
        $groupsQuery = Group::withCount('members')->orderBy('name');

        if ($this->groupSearch) {
            $groupsQuery->where('name', 'like', "%{$this->groupSearch}%");
        }

        $groups = $groupsQuery->get();

        $selectedGroup = null;
        $messages = collect();

        if ($this->selectedGroupId) {
            $selectedGroup = Group::with('members')->find($this->selectedGroupId);
            if ($selectedGroup) {
                $messages = $selectedGroup->messages()
                    ->with(['user', 'attachments', 'reads.member'])
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        return view('livewire.admin.messages.message-center', [
            'groups' => $groups,
            'selectedGroup' => $selectedGroup,
            'messages' => $messages,
            'additionalGroupIds' => $this->additionalGroupIds,
        ]);
    }
}
