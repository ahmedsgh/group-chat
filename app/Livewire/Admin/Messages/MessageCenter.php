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

    public string $additionalGroupIdsString = '';

    public string $groupSearch = '';
    public string $messageContent = '';
    public array $attachments = [];
    public $newAttachments = [];

    public function updatedNewAttachments()
    {
        $this->validate([
            'newAttachments.*' => 'file|max:10240',
        ]);

        $this->attachments = array_merge($this->attachments, is_array($this->newAttachments) ? $this->newAttachments : [$this->newAttachments]);
        $this->newAttachments = [];
    }

    public bool $showReadsModal = false;
    public ?int $readsMessageId = null;
    public array $messageReads = [];

    public int $messagesLimit = 10;

    public function mount(): void
    {
        // Initialize from URL if present
    }

    protected function parseAdditionalIds(): array
    {
        if (empty($this->additionalGroupIdsString)) {
            return [];
        }
        return array_filter(array_map('intval', explode(',', $this->additionalGroupIdsString)));
    }

    public function getAdditionalGroupIdsProperty(): array
    {
        return $this->parseAdditionalIds();
    }

    public function selectGroup(int $groupId): void
    {
        $this->selectedGroupId = $groupId;
        $this->messagesLimit = 10; // Reset pagination when switching groups
        // Remove from additional if it was there
        $additional = $this->parseAdditionalIds();
        $additional = array_diff($additional, [$groupId]);
        $this->additionalGroupIdsString = implode(',', $additional);
    }

    public function loadMoreMessages(): void
    {
        $this->messagesLimit += 10;
    }

    public function toggleAdditionalGroup(int $groupId): void
    {
        if ($groupId === $this->selectedGroupId) {
            return;
        }

        $additional = $this->parseAdditionalIds();
        if (in_array($groupId, $additional)) {
            $additional = array_diff($additional, [$groupId]);
        } else {
            $additional[] = $groupId;
        }
        $this->additionalGroupIdsString = implode(',', $additional);
    }

    public function selectAllGroups(): void
    {
        $groupsQuery = Group::query();

        // If search is active, only select filtered groups
        if ($this->groupSearch) {
            $groupsQuery->where('name', 'like', "%{$this->groupSearch}%");
        }

        $filteredGroupIds = $groupsQuery->pluck('id')->toArray();

        // Current additional groups
        $currentAdditional = $this->parseAdditionalIds();

        // Add filtered groups (except selectedGroupId) to current additional groups
        $groupsToAdd = array_diff($filteredGroupIds, [$this->selectedGroupId]);
        $newAdditional = array_unique(array_merge($currentAdditional, $groupsToAdd));

        $this->additionalGroupIdsString = implode(',', $newAdditional);
    }

    public function clearAdditionalGroups(): void
    {
        // If search is active, only clear groups that match the search
        if ($this->groupSearch) {
            $filteredGroupIds = Group::where('name', 'like', "%{$this->groupSearch}%")
                ->pluck('id')
                ->toArray();

            $currentAdditional = $this->parseAdditionalIds();

            // Remove only the filtered groups from additional
            $newAdditional = array_diff($currentAdditional, $filteredGroupIds);

            $this->additionalGroupIdsString = implode(',', $newAdditional);
        } else {
            // No search, clear all additional groups
            $this->additionalGroupIdsString = '';
        }
    }

    public function sendMessage(): void
    {
        $this->validate([
            'messageContent' => 'required_without:attachments|nullable|string',
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
            'content' => $this->messageContent ?: null,
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
    }

    public function removeAttachment(int $index): void
    {
        if (isset($this->attachments[$index])) {
            unset($this->attachments[$index]);
            // Re-index the array to keep Livewire happy
            $this->attachments = array_values($this->attachments);
        }
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
        $hasMoreMessages = false;
        $totalMessagesCount = 0;

        if ($this->selectedGroupId) {
            $selectedGroup = Group::with('members')->find($this->selectedGroupId);
            if ($selectedGroup) {
                $totalMessagesCount = $selectedGroup->messages()->count();
                $hasMoreMessages = $totalMessagesCount > $this->messagesLimit;

                $messages = $selectedGroup->messages()
                    ->with(['user', 'attachments', 'reads.member'])
                    ->orderBy('created_at', 'desc')
                    ->take($this->messagesLimit)
                    ->get();
            }
        }

        return view('livewire.admin.messages.message-center', [
            'groups' => $groups,
            'selectedGroup' => $selectedGroup,
            'messages' => $messages,
            'additionalGroupIds' => $this->additionalGroupIds,
            'hasMoreMessages' => $hasMoreMessages,
            'totalMessagesCount' => $totalMessagesCount,
        ]);
    }
}
