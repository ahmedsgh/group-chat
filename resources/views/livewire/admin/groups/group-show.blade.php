<div>
    <x-slot name="header">{{ $group->name }}</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('admin.messages.index', ['selectedGroupId' => $group->id]) }}" wire:navigate
            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition-colors">
            <x-icon name="send" class="w-4 h-4 mr-2" />
            Send Message
        </a>
    </x-slot>

    <div class="space-y-6">
        <!-- Group Info -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center">
                        <x-icon name="groups" class="w-7 h-7 text-white" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $group->name }}</h2>
                        <p class="text-gray-500 dark:text-gray-400">{{ $group->members->count() }} members</p>
                    </div>
                </div>
                <a href="{{ route('admin.groups.edit', $group) }}" wire:navigate
                    class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-medium transition-colors">
                    <x-icon name="edit" class="w-4 h-4 mr-2" />
                    Edit Group
                </a>
            </div>
        </div>

        <!-- Members List -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white">Group Members</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($group->members as $member)
                    <div
                        class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                                <span class="text-white font-medium text-sm">{{ substr($member->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $member->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $member->phone }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $member->type === 'student' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                {{ ucfirst($member->type) }}
                            </span>
                            @if($member->isOnline())
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        No members in this group yet
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Back Link -->
        <div>
            <a href="{{ route('admin.groups.index') }}" wire:navigate
                class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                Back to Groups
            </a>
        </div>
    </div>
</div>