<x-layouts.admin>
    <x-slot name="header">Dashboard</x-slot>

    <div class="space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Members -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Members</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ number_format($stats['members']) }}</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                        <x-icon name="users" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-emerald-500">{{ $stats['students'] }} students</span>
                    <span class="mx-2 text-gray-300 dark:text-gray-600">•</span>
                    <span class="text-purple-500">{{ $stats['parents'] }} parents</span>
                </div>
            </div>

            <!-- Total Groups -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Groups</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ number_format($stats['groups']) }}</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                        <x-icon name="groups" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
                <a href="{{ route('admin.groups.create') }}"
                    class="mt-4 inline-flex items-center text-sm text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300">
                    <x-icon name="plus" class="w-4 h-4 mr-1" />
                    Create new group
                </a>
            </div>

            <!-- Total Messages -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Messages Sent</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ number_format($stats['messages']) }}</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                        <x-icon name="messages" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
                <a href="{{ route('admin.messages.index') }}"
                    class="mt-4 inline-flex items-center text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300">
                    <x-icon name="send" class="w-4 h-4 mr-1" />
                    Send message
                </a>
            </div>

            <!-- Quick Action -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/80">Quick Action</p>
                        <p class="text-xl font-bold text-white mt-1">Add Member</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <x-icon name="plus" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <a href="{{ route('admin.members.create') }}"
                    class="mt-4 inline-flex items-center justify-center w-full px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl text-sm font-medium transition-colors backdrop-blur-sm">
                    Add New Member
                </a>
            </div>
        </div>

        <!-- Recent Data Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Members -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Recent Members</h3>
                    <a href="{{ route('admin.members.index') }}"
                        class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">View
                        all</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentMembers as $member)
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
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $member->type === 'student' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                {{ ucfirst($member->type) }}
                            </span>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No members yet. <a href="{{ route('admin.members.create') }}"
                                class="text-indigo-600 dark:text-indigo-400 hover:underline">Add your first member</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Groups -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Recent Groups</h3>
                    <a href="{{ route('admin.groups.index') }}"
                        class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">View
                        all</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentGroups as $group)
                        <a href="{{ route('admin.groups.show', $group) }}"
                            class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors block">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center">
                                    <x-icon name="groups" class="w-5 h-5 text-white" />
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $group->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $group->members_count }} members
                                    </p>
                                </div>
                            </div>
                            <x-icon name="chevron-right" class="w-5 h-5 text-gray-400" />
                        </a>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No groups yet. <a href="{{ route('admin.groups.create') }}"
                                class="text-indigo-600 dark:text-indigo-400 hover:underline">Create your first group</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>