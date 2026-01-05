<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Messages' }} - {{ config('app.name', 'GroupChat') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <x-icon name="messages" class="w-6 h-6 text-white" />
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">GroupChat</span>
                    </div>

                    @auth('member')
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ $member->name ?? '' }}</span>
                            <form method="POST" action="{{ route('member.logout') }}">
                                @csrf
                                <button type="submit"
                                    class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                    <x-icon name="logout" class="w-5 h-5" />
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>

</html>