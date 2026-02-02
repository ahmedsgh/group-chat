<div class="flex-1 flex flex-col min-h-0">
    <x-slot name="header">Messages</x-slot>
    <x-slot name="fullHeight">true</x-slot>

    <div
        class="flex flex-1 min-h-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Groups Sidebar -->
        <div class="w-80 flex-shrink-0 border-r border-gray-100 dark:border-gray-700 flex flex-col">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 space-y-3">
                <div class="relative">
                    <x-icon name="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                    <input type="text" wire:model.live.debounce.300ms="groupSearch" placeholder="Search groups..."
                        class="w-full pl-10 pr-10 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    @if($groupSearch)
                        <button type="button" wire:click="$set('groupSearch', '')"
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 flex items-center justify-center rounded-full bg-gray-200 dark:bg-gray-500 text-gray-600 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-400 transition-colors">
                            <x-icon name="close" class="w-3 h-3" />
                        </button>
                    @endif
                </div>
                <div class="flex gap-2">
                    @php
                        // Calculate how many groups in the current view (search results) are selected
                        $groupIdsInView = $groups->pluck('id')->toArray();
                        $selectedInView = array_filter($groupIdsInView, fn($id) => in_array($id, $additionalGroupIds) || $id == $selectedGroupId);
                        $additionalInView = array_filter($groupIdsInView, fn($id) => in_array($id, $additionalGroupIds));
                        // Total selected count (selectedGroupId + additionalGroupIds)
                        $totalSelectedCount = ($selectedGroupId ? 1 : 0) + count($additionalGroupIds);
                        $totalGroupsCount = \App\Models\Group::count();
                    @endphp
                    <button wire:click="selectAllGroups"
                        class="flex-1 px-3 py-1.5 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded-lg hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition-colors">
                        Select All{{ $groupSearch ? ' (' . count($groups) . ')' : '' }}
                    </button>
                    @if($groupSearch ? count($additionalInView) > 0 : count($additionalGroupIds) > 0)
                        <button wire:click="clearAdditionalGroups"
                            class="flex-1 px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            @if($groupSearch)
                                Clear ({{ count($additionalInView) + (in_array($selectedGroupId, $groupIdsInView) ? 1 : 0) }})
                            @else
                                Clear ({{ count($additionalGroupIds) + ($selectedGroupId ? 1 : 0) }})
                            @endif
                        </button>
                    @endif
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                @foreach($groups as $group)
                    <div wire:key="group-sidebar-{{ $group->id }}"
                        class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-l-4 cursor-pointer
                                                                                                                    {{ $selectedGroupId == $group->id ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : (in_array($group->id, $additionalGroupIds) ? 'border-purple-400 bg-purple-50 dark:bg-purple-900/10' : 'border-transparent') }}">

                        <!-- Checkbox for multi-select -->
                        @if($selectedGroupId != $group->id)
                            <input type="checkbox"
                                wire:key="cb-{{ $group->id }}-{{ in_array($group->id, $additionalGroupIds) ? 'ch' : 'unch' }}"
                                wire:click="toggleAdditionalGroup({{ $group->id }})" @checked(in_array($group->id, $additionalGroupIds))
                                class="w-4 h-4 text-indigo-600 bg-gray-100 dark:bg-gray-600 border-gray-300 dark:border-gray-500 rounded focus:ring-indigo-500 mr-3 transition-colors cursor-pointer">
                        @else
                            <!-- Placeholder for selected item alignment -->
                            <div class="w-4 h-4 mr-3 flex-shrink-0 flex items-center justify-center">
                                <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                            </div>
                        @endif

                        <div wire:click="selectGroup({{ $group->id }})" class="flex items-center flex-1 min-w-0">
                            <div
                                class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center flex-shrink-0">
                                <x-icon name="groups" class="w-5 h-5 text-white" />
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $group->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $group->members_count }} members</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Selection Counter - Bottom Bar --}}
            @if($totalSelectedCount > 0)
                <div
                    class="flex-shrink-0 px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 rounded-lg bg-indigo-600 dark:bg-indigo-500 flex items-center justify-center">
                                <span class="text-sm font-bold text-white">{{ $totalSelectedCount }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $totalSelectedCount == 1 ? 'Group' : 'Groups' }} Selected
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">of {{ $totalGroupsCount }} total</p>
                            </div>
                        </div>
                        @if($groupSearch && count($selectedInView) > 0)
                            <div class="text-right">
                                <p class="text-sm font-semibold text-purple-600 dark:text-purple-400">
                                    {{ count($selectedInView) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">in view</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Messages Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            @if($selectedGroup)
                <!-- Chat Header -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center">
                            <x-icon name="groups" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                {{ $selectedGroup->name }}
                                @if(count($additionalGroupIds) > 0)
                                    <span class="text-indigo-600 dark:text-indigo-400">+{{ count($additionalGroupIds) }}
                                        groups</span>
                                @endif
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $selectedGroup->members->count() }}
                                members</p>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="flex-1 overflow-y-auto p-4 min-h-0 flex flex-col-reverse gap-4" id="messagesContainer"
                    wire:poll.30s>
                    @foreach($messages as $index => $message)
                        @php
                            $nextMessage = $messages[$index + 1] ?? null;
                            $showDateSeparator = !$nextMessage || $nextMessage->created_at->toDateString() !== $message->created_at->toDateString();
                        @endphp

                        <div wire:key="message-{{ $message->id }}">
                            <div
                                class="w-fit max-w-full md:max-w-lg bg-indigo-600 text-white rounded-2xl rounded-br-md shadow-sm overflow-hidden">
                                @if($message->attachments->count())
                                    @php
                                        $images = $message->attachments->filter(fn($a) => $a->isImage());
                                        $files = $message->attachments->reject(fn($a) => $a->isImage());
                                        $imgCount = $images->count();
                                        $gridCols = match (true) {
                                            $imgCount === 1 => 'grid-cols-1',
                                            $imgCount === 2 || $imgCount === 4 => 'grid-cols-2',
                                            default => 'grid-cols-3',
                                        };
                                    @endphp

                                    @if($imgCount > 0)
                                        <div class="grid {{ $gridCols }} gap-0.5">
                                            @foreach($images as $image)
                                                <div class="relative {{ $imgCount > 1 ? 'aspect-square' : '' }} overflow-hidden">
                                                    <img src="{{ $image->url }}" alt="{{ $image->filename }}"
                                                        class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition-opacity"
                                                        @click="$dispatch('open-lightbox', { url: '{{ $image->url }}', filename: '{{ $image->filename }}' })">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($files->count() > 0)
                                        <div class="p-2 space-y-1">
                                            @foreach($files as $attachment)
                                                <div class="flex items-center gap-3 p-3 bg-indigo-700/50 rounded-xl">
                                                    <div
                                                        class="flex-shrink-0 w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center">
                                                        <x-icon name="document" class="w-5 h-5 text-white" />
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-white truncate">{{ $attachment->filename }}</p>
                                                        <p class="text-xs text-indigo-200">
                                                            {{ number_format($attachment->size / 1024, 1) }} KB
                                                        </p>
                                                    </div>
                                                    <a href="{{ $attachment->url }}" download="{{ $attachment->filename }}"
                                                        onclick="event.preventDefault(); downloadFile('{{ $attachment->url }}', '{{ $attachment->filename }}')"
                                                        class="flex-shrink-0 w-8 h-8 bg-indigo-500 hover:bg-indigo-400 rounded-lg flex items-center justify-center transition-colors">
                                                        <x-icon name="download" class="w-5 h-5 text-white" />
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                                @if($message->content)
                                    <p dir="auto" class="whitespace-pre-wrap break-words px-4 py-3">{{ $message->content }}</p>
                                @endif
                            </div>
                            <div class="mt-1 flex items-center gap-2">
                                <span
                                    class="text-xs text-gray-500 dark:text-gray-400">{{ $message->created_at->format('H:i') }}</span>
                                <button wire:click="showMessageReads({{ $message->id }})"
                                    class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-indigo-500 transition-colors">
                                    <x-icon name="info" class="w-3.5 h-3.5" />
                                    <span>{{ $message->reads->count() }} read</span>
                                </button>
                            </div>
                        </div>

                        @if($showDateSeparator)
                            <div class="flex items-center justify-center my-2">
                                <span
                                    class="px-3 py-1 text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-full">
                                    @php
                                        $date = $message->created_at;
                                        $daysDiff = now()->startOfDay()->diffInDays($date->copy()->startOfDay(), false);

                                        if ($date->isToday()) {
                                            echo 'Today';
                                        } elseif ($date->isYesterday()) {
                                            echo 'Yesterday';
                                        } elseif (abs($daysDiff) < 7) {
                                            echo $date->format('l');
                                        } else {
                                            echo $date->format('d/m/Y');
                                        }
                                    @endphp
                                </span>
                            </div>
                        @endif
                    @endforeach

                    @if($hasMoreMessages)
                        <div class="flex justify-center py-2">
                            <button wire:click="loadMoreMessages" wire:loading.attr="disabled" wire:target="loadMoreMessages"
                                class="px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-lg transition-colors disabled:opacity-50">
                                <span wire:loading.remove wire:target="loadMoreMessages">
                                    Load More ({{ $totalMessagesCount - $messagesLimit }} older)
                                </span>
                                <span wire:loading wire:target="loadMoreMessages">
                                    Loading...
                                </span>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Message Input -->
                <form wire:submit="sendMessage"
                    class="flex-shrink-0 px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                    @error('messageContent')
                        <p class="mb-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @if(session('error'))
                        <div class="mb-2 text-sm text-red-600 dark:text-red-400">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(count($attachments) > 0)
                        <div class="mb-3 flex gap-3 overflow-x-auto pb-2">
                            @foreach($attachments as $index => $file)
                                <div wire:key="attachment-preview-{{ $index }}" class="relative group flex-shrink-0 pt-2 pr-2">
                                    @php
                                        $isImage = str_starts_with($file->getMimeType(), 'image/');
                                    @endphp

                                    @if($isImage)
                                        <div
                                            class="w-20 h-20 rounded-xl overflow-hidden shadow-sm transition-transform hover:scale-[1.02]">
                                            <img src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover">
                                            <div
                                                class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                            </div>
                                        </div>
                                    @else
                                        <div
                                            class="h-20 px-4 flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-700/50 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-600 min-w-[120px] max-w-[200px]">
                                            <x-icon name="document" class="w-8 h-8 text-indigo-500 mb-2" />
                                            <span
                                                class="text-[10px] font-medium text-gray-600 dark:text-gray-300 truncate w-full text-center px-1">
                                                {{ $file->getClientOriginalName() }}
                                            </span>
                                            <span class="text-[8px] text-gray-400 uppercase mt-0.5">
                                                {{ pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION) }}
                                            </span>
                                        </div>
                                    @endif

                                    <button type="button" wire:click="removeAttachment({{ $index }})"
                                        class="absolute top-0 right-0 w-6 h-6 flex items-center justify-center rounded-full bg-white dark:bg-gray-800 text-red-500 shadow-md border border-gray-100 dark:border-gray-700 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all z-10">
                                        <x-icon name="close" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <!-- Attachment Button -->
                        <label
                            class="flex-shrink-0 w-10 h-10 flex items-center justify-center text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full cursor-pointer transition-colors relative">
                            <input type="file" wire:model="newAttachments" multiple class="hidden"
                                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                            <div wire:loading wire:target="newAttachments">
                                <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </div>
                            <x-icon name="attachment" class="w-5 h-5" wire:loading.remove wire:target="newAttachments" />
                        </label>

                        <!-- Text Input -->
                        <div class="flex-1 relative min-w-0" x-data="{ text: $wire.entangle('messageContent') }">
                            <textarea x-model="text" wire:model="messageContent" rows="1" placeholder="Type a message..."
                                class="w-full block px-4 py-2.5 bg-gray-100 dark:bg-gray-700 border-0 rounded-2xl text-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 resize-none focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-gray-600 transition-colors break-words @error('messageContent') ring-2 ring-red-500 @enderror"
                                style="field-sizing: content; min-height: 40px; max-height: 150px;"></textarea>
                        </div>

                        <!-- Send Button -->
                        <button type="submit"
                            class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-md hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled" wire:target="sendMessage" @disabled(empty($attachments))
                            x-bind:disabled="!$wire.messageContent?.trim() && {{ count($attachments) }} === 0">
                            <svg wire:loading.remove wire:target="sendMessage" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                <path
                                    d="M3.478 2.404a.75.75 0 0 0-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z" />
                            </svg>
                            <svg wire:loading wire:target="sendMessage" class="w-5 h-5 animate-spin"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </form>
            @else
                <!-- No Group Selected -->
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <div
                            class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <x-icon name="messages" class="w-12 h-12 text-gray-400" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Select a group</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Choose a group from the sidebar to start messaging
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Read Receipts Modal -->
    @if($showReadsModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div wire:click="closeReadsModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Read by</h3>
                    </div>
                    <div class="px-6 py-4 max-h-80 overflow-y-auto">
                        @if(count($messageReads) > 0)
                            <ul class="space-y-3">
                                @foreach($messageReads as $read)
                                    <li class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $read['name'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $read['phone'] }}</p>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $read['read_at'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">No one has read this message yet</p>
                        @endif
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end">
                        <button wire:click="closeReadsModal"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 rounded-xl font-medium transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Image Lightbox Modal --}}
    <div x-data="{ open: false, imageUrl: '', filename: '' }"
        x-on:open-lightbox.window="open = true; imageUrl = $event.detail.url; filename = $event.detail.filename"
        x-show="open" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/90" @click="open = false"></div>

        {{-- Close button --}}
        <button @click="open = false"
            class="absolute top-4 right-4 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors">
            <x-icon name="close" class="w-6 h-6" />
        </button>

        {{-- Download button --}}
        <a :href="imageUrl" :download="filename" @click.prevent="downloadFile(imageUrl, filename)"
            class="absolute top-4 left-4 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors">
            <x-icon name="download" class="w-5 h-5" />
        </a>

        {{-- Image --}}
        <img :src="imageUrl" :alt="filename"
            class="relative max-w-[90vw] max-h-[90vh] object-contain rounded-lg shadow-2xl" @click.stop>
    </div>

    {{-- Download helper script --}}
    <script>
        function downloadFile(url, filename) {
            // Use fetch + blob for cross-browser/WebView compatibility
            fetch(url)
                .then(response => response.blob())
                .then(blob => {
                    const blobUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = blobUrl;
                    a.download = filename || 'download';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(blobUrl);
                })
                .catch(() => {
                    // Fallback: open in new tab
                    window.open(url, '_blank');
                });
        }
    </script>
</div>