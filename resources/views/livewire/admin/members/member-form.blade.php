<div>
    <x-slot name="header">{{ $isEditing ? 'Edit Member' : 'Add New Member' }}</x-slot>

    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <form wire:submit="save" class="p-6 space-y-6">
            <!-- Name & Phone Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                    <input type="text" wire:model.blur="name" id="name"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 @error('name') ring-2 ring-red-500 @enderror"
                        placeholder="Enter member name">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone
                        Number</label>
                    <input type="text" wire:model.blur="phone" id="phone"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 @error('phone') ring-2 ring-red-500 @enderror"
                        placeholder="+1234567890">
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Type & Gender Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Type -->
                <div>
                    <label for="type"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                    <select wire:model.blur="type" id="type"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 @error('type') ring-2 ring-red-500 @enderror">
                        <option value="">Select type</option>
                        <option value="student">Student</option>
                        <option value="parent">Parent</option>
                    </select>
                    @error('type')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gender</label>
                    <select wire:model.blur="gender" id="gender"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 @error('gender') ring-2 ring-red-500 @enderror">
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    @error('gender')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Groups -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assign to
                    Groups</label>

                <div class="space-y-4">
                    <!-- Search Groups -->
                    <div class="relative">
                        <x-icon name="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                        <input type="text" wire:model.live.debounce.300ms="groupSearch" placeholder="Search groups..."
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">

                        <!-- Search Results Dropdown -->
                        @if(count($groupSearchResults) > 0)
                            <div
                                class="absolute z-10 w-full mt-2 bg-white dark:bg-gray-700 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 max-h-48 overflow-y-auto">
                                @foreach($groupSearchResults as $group)
                                    <button type="button" wire:click="addGroup({{ $group['id'] }})"
                                        class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 text-left transition-colors">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $group['name'] }}</p>
                                        <x-icon name="plus" class="w-4 h-4 text-gray-400" />
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Selected Groups -->
                    <div class="space-y-2">
                        @forelse($selectedGroups as $group)
                            <div wire:key="group-{{ $group['id'] }}"
                                class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 px-4 py-3 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center flex-shrink-0">
                                        <x-icon name="groups" class="w-4 h-4 text-white" />
                                    </div>
                                    <span class="text-gray-900 dark:text-white font-medium">{{ $group['name'] }}</span>
                                </div>
                                <button type="button" wire:click="removeGroup({{ $group['id'] }})"
                                    class="text-gray-400 hover:text-red-500 transition-colors">
                                    <x-icon name="close" class="w-5 h-5" />
                                </button>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm py-2">No groups assigned</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.members.index') }}" wire:navigate
                    class="px-6 py-3 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-lg shadow-indigo-500/25 disabled:opacity-50"
                    wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove
                        wire:target="save">{{ $isEditing ? 'Update Member' : 'Create Member' }}</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>