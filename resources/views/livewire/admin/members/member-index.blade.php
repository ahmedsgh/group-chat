<div>
    <x-slot name="header">Members</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('admin.members.create') }}"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition-colors shadow-lg shadow-indigo-500/25">
            <x-icon name="plus" class="w-4 h-4 mr-2" />
            Add Member
        </a>
    </x-slot>

    <div class="space-y-6">
        <!-- Search and Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <x-icon name="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Search by name or phone..."
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <select wire:model.live="type"
                    class="pl-4 pr-8 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Types</option>
                    <option value="student">Students</option>
                    <option value="parent">Parents</option>
                </select>
                <select wire:model.live="gender"
                    class="pl-4 pr-8 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                @if($search || $type || $gender)
                    <button wire:click="clearFilters"
                        class="px-4 py-2.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium transition-colors">
                        Clear
                    </button>
                @endif
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div
                class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <!-- Members Table -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Member</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Phone</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Type</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Gender</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Groups</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($members as $member)
                            <tr wire:key="member-{{ $member->id }}"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center flex-shrink-0">
                                            <span
                                                class="text-white font-medium text-sm">{{ substr($member->name, 0, 1) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $member->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300">
                                    {{ $member->phone }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $member->type === 'student' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                        {{ ucfirst($member->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300 capitalize">
                                    {{ $member->gender }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300">
                                    {{ $member->groups->count() }} groups
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('admin.members.show', $member) }}"
                                            class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                            title="View">
                                            <x-icon name="view" class="w-5 h-5" />
                                        </a>
                                        <a href="{{ route('admin.members.edit', $member) }}"
                                            class="p-2 text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors"
                                            title="Edit">
                                            <x-icon name="edit" class="w-5 h-5" />
                                        </a>
                                        <button
                                            wire:click="confirmDelete({{ $member->id }}, '{{ addslashes($member->name) }}')"
                                            class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                            title="Delete">
                                            <x-icon name="delete" class="w-5 h-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <x-icon name="users" class="w-12 h-12 mx-auto mb-4 opacity-50" />
                                    <p class="text-lg font-medium">No members found</p>
                                    <p class="text-sm mt-1">Get started by adding your first member</p>
                                    <a href="{{ route('admin.members.create') }}"
                                        class="inline-flex items-center mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition-colors">
                                        <x-icon name="plus" class="w-4 h-4 mr-2" />
                                        Add Member
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($members->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $members->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div wire:click="cancelDelete" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-6 py-6">
                        <div
                            class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                            <x-icon name="delete" class="w-6 h-6 text-red-600 dark:text-red-400" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center" id="modal-title">
                            Delete Member
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 text-center">
                            Are you sure you want to delete <strong>{{ $memberToDeleteName }}</strong>? This action cannot
                            be undone.
                        </p>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-3">
                        <button wire:click="cancelDelete"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                            Cancel
                        </button>
                        <button wire:click="deleteMember"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>