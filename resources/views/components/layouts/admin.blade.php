<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Dashboard' }} - {{ config('app.name', 'Group Chat') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 {{ isset($fullHeight) && $fullHeight ? 'overflow-hidden' : '' }}">
    <div class="{{ isset($fullHeight) && $fullHeight ? 'h-screen' : 'min-h-screen' }} flex" x-data="{ sidebarOpen: false }">

        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-gray-900/80 lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-indigo-900 via-purple-900 to-indigo-900 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:flex-shrink-0">
            <div class="flex flex-col h-full lg:h-screen lg:sticky lg:top-0">
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-6 border-b border-white/10">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <x-icon name="messages" class="w-6 h-6 text-white" />
                        </div>
                        <span class="text-xl font-bold text-white">GroupChat</span>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-white/80 hover:text-white">
                        <x-icon name="close" class="w-6 h-6" />
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <p class="px-3 text-xs font-semibold text-white/50 uppercase tracking-wider mb-4">Main Menu</p>

                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center px-4 py-3 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-white/15 text-white shadow-lg' : '' }}">
                        <x-icon name="home"
                            class="w-5 h-5 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-indigo-300' : 'text-white/60 group-hover:text-white' }}" />
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a href="{{ route('admin.members.index') }}"
                        class="flex items-center px-4 py-3 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.members.*') ? 'bg-white/15 text-white shadow-lg' : '' }}">
                        <x-icon name="users"
                            class="w-5 h-5 mr-3 {{ request()->routeIs('admin.members.*') ? 'text-indigo-300' : 'text-white/60 group-hover:text-white' }}" />
                        <span class="font-medium">Members</span>
                    </a>

                    <a href="{{ route('admin.groups.index') }}"
                        class="flex items-center px-4 py-3 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.groups.*') ? 'bg-white/15 text-white shadow-lg' : '' }}">
                        <x-icon name="groups"
                            class="w-5 h-5 mr-3 {{ request()->routeIs('admin.groups.*') ? 'text-indigo-300' : 'text-white/60 group-hover:text-white' }}" />
                        <span class="font-medium">Groups</span>
                    </a>

                    <a href="{{ route('admin.messages.index') }}"
                        class="flex items-center px-4 py-3 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.messages.*') ? 'bg-white/15 text-white shadow-lg' : '' }}">
                        <x-icon name="messages"
                            class="w-5 h-5 mr-3 {{ request()->routeIs('admin.messages.*') ? 'text-indigo-300' : 'text-white/60 group-hover:text-white' }}" />
                        <span class="font-medium">Messages</span>
                    </a>
                </nav>

                <!-- User section -->
                <div class="p-4 border-t border-white/10">
                    <div class="flex items-center p-3 bg-white/5 rounded-xl">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center">
                            <span
                                class="text-white font-semibold text-sm">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                            <p class="text-xs text-white/60 truncate">{{ auth()->user()->email ?? '' }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit"
                                class="p-2 text-white/60 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                                <x-icon name="logout" class="w-5 h-5" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0 {{ isset($fullHeight) && $fullHeight ? 'h-screen overflow-hidden' : '' }}">
            <!-- Top header -->
            <header
                class="sticky top-0 z-30 bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true"
                            class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <x-icon name="menu" class="w-6 h-6" />
                        </button>
                        <h1 class="ml-2 lg:ml-0 text-xl font-semibold text-gray-900 dark:text-white">
                            {{ $header ?? 'Dashboard' }}
                        </h1>
                    </div>

                    <div class="flex items-center space-x-3">
                        {{ $headerActions ?? '' }}
                    </div>
                </div>
            </header>

            <!-- Page content -->
            @if(isset($fullHeight) && $fullHeight)
                <main class="flex-1 flex flex-col min-h-0">
                    {{ $slot }}
                </main>
            @else
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            @endif
        </div>
    </div>

    @livewireScripts
</body>

</html>