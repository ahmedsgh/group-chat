<div>
    <x-slot name="header">{{ $isEditing ? 'Edit Group' : 'Create Group' }}</x-slot>

    <div class="space-y-6">
        <!-- Group Name -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Group Name</label>
            <input type="text" wire:model.blur="name" id="name"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 @error('name') ring-2 ring-red-500 @enderror"
                placeholder="e.g., Class 10-A, Parent Group">
            @error('name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Section 1: Add Existing Members -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add Existing Members</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Search and add members that are already in the
                system</p>

            <div class="relative mb-4">
                <x-icon name="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <input type="text" wire:model.live.debounce.300ms="memberSearch"
                    placeholder="Search by name or phone..."
                    class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">

                <!-- Search Results Dropdown -->
                @if(count($searchResults) > 0)
                    <div
                        class="absolute z-10 w-full mt-2 bg-white dark:bg-gray-700 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 max-h-48 overflow-y-auto">
                        @foreach($searchResults as $member)
                            <button type="button" wire:click="addMember({{ $member['id'] }})"
                                class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 text-left transition-colors">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $member['name'] }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $member['phone'] }}</p>
                                </div>
                                <span
                                    class="text-xs px-2 py-1 rounded-full {{ $member['type'] === 'student' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                    {{ $member['type'] }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Selected Members -->
            <div class="space-y-2">
                @forelse($selectedMembers as $member)
                    <div wire:key="selected-{{ $member['id'] }}"
                        class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 px-4 py-3 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ substr($member['name'], 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $member['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member['phone'] }}</p>
                            </div>
                        </div>
                        <button type="button" wire:click="removeMember({{ $member['id'] }})"
                            class="text-gray-400 hover:text-red-500 transition-colors">
                            <x-icon name="close" class="w-5 h-5" />
                        </button>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 py-2">No members selected from search</p>
                @endforelse
            </div>
        </div>

        <!-- Section 2: Create New Members -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Create New Members</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Create new members directly while creating this
                group
            </p>

            <div class="space-y-4">
                @foreach($newMembers as $tempId => $newMember)
                    <div wire:key="new-member-{{ $tempId }}" class="flex items-center">
                        <div class="flex-1 bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <input type="text" wire:model="newMembers.{{ $tempId }}.name" placeholder="Name"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 @error('newMembers.' . $tempId . '.name') ring-2 ring-red-500 @enderror">
                                    @error('newMembers.' . $tempId . '.name')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <input type="text" wire:model="newMembers.{{ $tempId }}.phone" placeholder="Phone"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 @error('newMembers.' . $tempId . '.phone') ring-2 ring-red-500 @enderror">
                                    @error('newMembers.' . $tempId . '.phone')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <select wire:model="newMembers.{{ $tempId }}.type"
                                    class="px-3 py-2 bg-white dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                    <option value="student">Student</option>
                                    <option value="parent">Parent</option>
                                </select>
                                <select wire:model="newMembers.{{ $tempId }}.gender"
                                    class="px-3 py-2 bg-white dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" wire:click="removeNewMember('{{ $tempId }}')"
                            class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                            title="Remove member">
                            <x-icon name="delete" class="w-5 h-5" />
                        </button>
                    </div>
                @endforeach
            </div>

            <button type="button" wire:click="addNewMember"
                class="mt-4 inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 text-gray-700 dark:text-white rounded-xl text-sm font-medium transition-colors">
                <x-icon name="plus" class="w-4 h-4 mr-2" />
                Create New Member
            </button>
        </div>

        <!-- Section 3: Current Members (Edit Only) -->
        @if($isEditing && count($currentMembers) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Members
                    ({{ count($currentMembers) }})</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Members currently assigned to this group</p>

                <div class="space-y-2">
                    @foreach($currentMembers as $member)
                        <div wire:key="current-{{ $member['id'] }}"
                            class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 px-4 py-3 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">{{ substr($member['name'], 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $member['name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member['phone'] }}</p>
                                </div>
                            </div>
                            <button type="button" wire:click="removeCurrentMember({{ $member['id'] }})"
                                class="text-gray-400 hover:text-red-500 transition-colors" title="Remove from group">
                                <x-icon name="close" class="w-5 h-5" />
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('admin.groups.index') }}" wire:navigate
                class="px-6 py-3 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium transition-colors">
                Cancel
            </a>
            <button type="button" wire:click="save"
                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-lg shadow-indigo-500/25 disabled:opacity-50"
                wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Update Group' : 'Create Group' }}</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </div>
</div>