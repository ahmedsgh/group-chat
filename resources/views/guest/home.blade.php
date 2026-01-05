<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'GroupChat') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="antialiased bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 min-h-screen">
    <div class="min-h-screen flex flex-col items-center justify-center px-6 py-12">
        <!-- Logo -->
        <div
            class="w-24 h-24 md:w-28 md:h-28 bg-white/20 backdrop-blur-xl rounded-3xl flex items-center justify-center shadow-2xl mb-8 animate-pulse">
            <x-icon name="messages" class="w-12 h-12 md:w-14 md:h-14 text-white" />
        </div>

        <!-- App Name -->
        <h1 class="text-4xl md:text-5xl font-extrabold text-white text-center mb-3 tracking-tight">
            GroupChat
        </h1>

        <!-- Tagline -->
        <p class="text-lg md:text-xl text-white/80 text-center max-w-xs md:max-w-sm mb-12">
            Stay connected with your school community
        </p>

        <!-- Get Started Button -->
        <a href="{{ route('login') }}"
            class="w-full max-w-xs px-8 py-4 bg-white text-indigo-600 text-lg font-bold rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 text-center">
            Get Started
        </a>

        <!-- Subtle footer -->
        <p class="mt-12 text-white/50 text-sm">
            &copy; {{ date('Y') }} {{ config('app.name', 'GroupChat') }}
        </p>
    </div>
</body>

</html>