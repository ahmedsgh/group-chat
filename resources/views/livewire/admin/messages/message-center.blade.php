<div>
    <x-slot name="header">Messages</x-slot>

    <div
        class="flex h-[calc(100vh-12rem)] bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <!-- Groups Sidebar -->
        <div class="w-80 flex-shrink-0 border-r border-gray-100 dark:border-gray-700 flex flex-col">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 space-y-3">
                <div class="relative">
                    <x-icon name="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                    <input type="text" wire:model.live.debounce.300ms="groupSearch" placeholder="Search groups..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex gap-2">
                    <button wire:click="selectAllGroups"
                        class="flex-1 px-3 py-1.5 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded-lg hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition-colors">
                        Select All
                    </button>
                    @if(count($additionalGroupIds) > 0)
                        <button wire:click="clearAdditionalGroups"
                            class="flex-1 px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Clear ({{ count($additionalGroupIds) + ($selectedGroupId ? 1 : 0) }})
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
        </div>

        <!-- Messages Area -->
        <div class="flex-1 flex flex-col">
            {{-- Debug Info --}}
            <div
                class="bg-yellow-100 dark:bg-yellow-900/30 p-2 text-xs font-mono text-yellow-800 dark:text-yellow-200 border-b border-yellow-200 dark:border-yellow-800">
                <strong>DEBUG state:</strong>
                <span class="ml-2">Selected: [{{ $selectedGroupId }}]</span>
                <span class="ml-4">Additional: [{{ implode(', ', $additionalGroupIds) }}]</span>
            </div>
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
                <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messagesContainer" wire:poll.30s>
                    @foreach($messages as $message)
                        <div wire:key="message-{{ $message->id }}" class="flex justify-end">
                            <div class="max-w-lg">
                                <div class="bg-indigo-600 text-white px-4 py-3 rounded-2xl rounded-br-md shadow-sm cursor-pointer"
                                    wire:click="showMessageReads({{ $message->id }})">
                                    <p class="whitespace-pre-wrap">{{ $message->content }}</p>
                                    @if($message->attachments->count())
                                        <div class="mt-2 space-y-1">
                                            @foreach($message->attachments as $attachment)
                                                @if($attachment->isImage())
                                                    <img src="{{ $attachment->url }}" alt="{{ $attachment->filename }}"
                                                        class="max-w-xs rounded-lg">
                                                @else
                                                    <a href="{{ $attachment->url }}" target="_blank"
                                                        class="flex items-center space-x-2 text-indigo-100 hover:text-white">
                                                        <x-icon name="document" class="w-4 h-4" />
                                                        <span class="text-sm">{{ $attachment->filename }}</span>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center justify-end mt-1 space-x-2">
                                    <span
                                        class="text-xs text-gray-500 dark:text-gray-400">{{ $message->created_at->format('H:i') }}</span>
                                    <span class="text-xs text-gray-400">• {{ $message->reads->count() }} read</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Message Input -->
                <form wire:submit="sendMessage" class="p-4 border-t border-gray-100 dark:border-gray-700">
                    @if(session('success'))
                        <div class="mb-3 text-sm text-emerald-600 dark:text-emerald-400">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-3 text-sm text-red-600 dark:text-red-400">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex items-end space-x-3">
                        <div class="flex-1">
                            <textarea wire:model="messageContent" rows="2" placeholder="Type your message..."
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-gray-900 dark:text-white resize-none focus:ring-2 focus:ring-indigo-500 @error('messageContent') ring-2 ring-red-500 @enderror"></textarea>
                            @error('messageContent')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <label class="p-3 text-gray-400 hover:text-gray-600 cursor-pointer">
                            <input type="file" wire:model="attachments" multiple class="hidden"
                                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                            <x-icon name="attachment" class="w-6 h-6" />
                        </label>
                        <button type="submit"
                            class="p-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-lg shadow-indigo-500/25 disabled:opacity-50"
                            wire:loading.attr="disabled" wire:target="sendMessage">
                            <x-icon name="send" class="w-6 h-6" wire:loading.remove wire:target="sendMessage" />
                            <span wire:loading wire:target="sendMessage" class="block w-6 h-6">...</span>
                        </button>
                    </div>

                    @if(count($attachments) > 0)
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($attachments as $index => $file)
                                <span
                                    class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-lg text-xs text-gray-600 dark:text-gray-400">
                                    {{ $file->getClientOriginalName() }}
                                </span>
                            @endforeach
                        </div>
                    @endif
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
</div>