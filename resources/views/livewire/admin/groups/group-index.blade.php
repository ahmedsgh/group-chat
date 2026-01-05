<div>
    <x-slot name="header">Groups</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('admin.groups.create') }}" wire:navigate
            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition-colors shadow-lg shadow-indigo-500/25">
            <x-icon name="plus" class="w-4 h-4 mr-2" />
            Create Group
        </a>
    </x-slot>

    <div class="space-y-6">
        <!-- Search -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <div class="flex gap-4">
                <div class="flex-1 relative">
                    <x-icon name="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search groups..."
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                </div>
                @if($search)
                    <button wire:click="$set('search', '')"
                        class="px-4 py-2.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium transition-colors">
                        Clear
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div
                class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <!-- Groups Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($groups as $group)
                <div wire:key="group-{{ $group->id }}"
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center">
                                    <x-icon name="groups" class="w-6 h-6 text-white" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $group->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $group->members_count }} members
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <a href="{{ route('admin.messages.index', ['selectedGroupId' => $group->id]) }}" wire:navigate
                            class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 font-medium">
                            Send Message
                        </a>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.groups.show', $group) }}" wire:navigate
                                class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                <x-icon name="view" class="w-5 h-5" />
                            </a>
                            <a href="{{ route('admin.groups.edit', $group) }}" wire:navigate
                                class="p-2 text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                                <x-icon name="edit" class="w-5 h-5" />
                            </a>
                            <button wire:click="confirmDelete({{ $group->id }}, '{{ addslashes($group->name) }}')"
                                class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                <x-icon name="delete" class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                    <x-icon name="groups" class="w-12 h-12 mx-auto mb-4 text-gray-400" />
                    <p class="text-lg font-medium text-gray-900 dark:text-white">No groups yet</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create your first group to start messaging</p>
                    <a href="{{ route('admin.groups.create') }}" wire:navigate
                        class="inline-flex items-center mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition-colors">
                        <x-icon name="plus" class="w-4 h-4 mr-2" />
                        Create Group
                    </a>
                </div>
            @endforelse
        </div>

        @if($groups->hasPages())
            <div class="mt-6">{{ $groups->links() }}</div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div wire:click="cancelDelete" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-6 py-6">
                        <div
                            class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                            <x-icon name="delete" class="w-6 h-6 text-red-600 dark:text-red-400" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center" id="modal-title">
                            Delete Group
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 text-center">
                            Are you sure you want to delete <strong>{{ $groupToDeleteName }}</strong>? This action cannot be
                            undone.
                        </p>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-3">
                        <button wire:click="cancelDelete"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                            Cancel
                        </button>
                        <button wire:click="deleteGroup"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>