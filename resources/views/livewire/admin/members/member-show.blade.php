<div>
    <x-slot name="header">Member Details</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('admin.members.edit', $member) }}" wire:navigate
            class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-medium transition-colors">
            <x-icon name="edit" class="w-4 h-4 mr-2" />
            Edit
        </a>
    </x-slot>

    <div class="space-y-6">
        <!-- Member Info Card -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6">
                <div class="flex items-start space-x-4">
                    <div
                        class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-2xl">{{ substr($member->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $member->name }}</h2>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $member->phone }}</p>
                        <div class="flex items-center space-x-2 mt-3">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $member->type === 'student' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                {{ ucfirst($member->type) }}
                            </span>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                {{ ucfirst($member->gender) }}
                            </span>
                            @if($member->isOnline())
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                    Online
                                </span>
                            @elseif($member->last_seen_at)
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    Last seen {{ $member->last_seen_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Created</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            {{ $member->created_at->format('M d, Y') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Last Updated</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            {{ $member->updated_at->format('M d, Y') }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Assigned Groups -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white">Assigned Groups ({{ $member->groups->count() }})
                </h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($member->groups as $group)
                    <a href="{{ route('admin.groups.show', $group) }}" wire:navigate
                        class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors block">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center">
                                <x-icon name="groups" class="w-5 h-5 text-white" />
                            </div>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $group->name }}</span>
                        </div>
                        <x-icon name="chevron-right" class="w-5 h-5 text-gray-400" />
                    </a>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        Not assigned to any groups yet
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Back Link -->
        <div>
            <a href="{{ route('admin.members.index') }}" wire:navigate
                class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                Back to Members
            </a>
        </div>
    </div>
</div>