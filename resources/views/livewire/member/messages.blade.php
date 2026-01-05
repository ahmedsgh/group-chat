<div x-data="{ showGroups: {{ !$selectedGroupId ? 'true' : 'false' }} }">
    <div class="h-[calc(100vh-4rem)] flex">
        <!-- Groups Sidebar (Mobile: full screen, Desktop: sidebar) -->
        <div class="w-full md:w-80 flex-shrink-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col"
            :class="{ 'hidden md:flex': {{ $selectedGroupId ? 'true' : 'false' }} && !showGroups }">

            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Your Groups</h2>
            </div>

            <div class="flex-1 overflow-y-auto">
                @forelse($groups as $group)
                    <button wire:click="selectGroup({{ $group->id }})" @click="showGroups = false"
                        class="w-full flex items-center px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-100 dark:border-gray-700 text-left {{ $selectedGroupId == $group->id ? 'bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-l-indigo-500' : '' }}">
                        <div
                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center flex-shrink-0">
                            <x-icon name="groups" class="w-6 h-6 text-white" />
                        </div>
                        <div class="ml-4 flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $group->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $group->members_count }} members</p>
                        </div>
                        <x-icon name="chevron-right" class="w-5 h-5 text-gray-400 flex-shrink-0" />
                    </button>
                @empty
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                        <x-icon name="groups" class="w-12 h-12 mx-auto mb-4 opacity-50" />
                        <p>No groups assigned to you yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 flex flex-col bg-gray-50 dark:bg-gray-900"
            :class="{ 'hidden md:flex': !{{ $selectedGroupId ? 'true' : 'false' }} || showGroups }">

            @if($selectedGroup)
                <!-- Chat Header -->
                <div
                    class="bg-white dark:bg-gray-800 px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center">
                    <button @click="showGroups = true" class="md:hidden p-2 -ml-2 mr-2 text-gray-500">
                        <x-icon name="arrow-left" class="w-5 h-5" />
                    </button>
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center">
                        <x-icon name="groups" class="w-5 h-5 text-white" />
                    </div>
                    <div class="ml-3">
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $selectedGroup->name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $selectedGroup->members->count() }} members
                        </p>
                    </div>
                </div>

                <!-- Messages -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messagesContainer" wire:poll.30s>
                    @foreach($messages as $message)
                        <div wire:key="message-{{ $message->id }}" class="flex justify-start">
                            <div class="max-w-[85%] md:max-w-lg">
                                <div class="bg-white dark:bg-gray-800 px-4 py-3 rounded-2xl rounded-bl-md shadow-sm">
                                    <p class="text-xs font-medium text-indigo-600 dark:text-indigo-400 mb-1">
                                        {{ $message->user->name }}
                                    </p>
                                    <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $message->content }}</p>
                                    @if($message->attachments->count())
                                        <div class="mt-2 space-y-2">
                                            @foreach($message->attachments as $attachment)
                                                @if($attachment->isImage())
                                                    <img src="{{ $attachment->url }}" alt="{{ $attachment->filename }}"
                                                        class="max-w-full rounded-lg">
                                                @else
                                                    <a href="{{ $attachment->url }}" target="_blank"
                                                        class="flex items-center space-x-2 text-indigo-600 dark:text-indigo-400 hover:underline">
                                                        <x-icon name="document" class="w-4 h-4" />
                                                        <span class="text-sm">{{ $attachment->filename }}</span>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-2">
                                    {{ $message->created_at->format('M d, H:i') }}
                                </p>
                            </div>
                        </div>
                    @endforeach

                    @if($messages->isEmpty())
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center text-gray-500 dark:text-gray-400">
                                <x-icon name="messages" class="w-12 h-12 mx-auto mb-4 opacity-50" />
                                <p>No messages in this group yet</p>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <!-- No Group Selected (Mobile shows groups, Desktop shows this) -->
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center text-gray-500 dark:text-gray-400">
                        <x-icon name="messages" class="w-16 h-16 mx-auto mb-4 opacity-50" />
                        <p class="text-lg font-medium">Select a group to view messages</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>