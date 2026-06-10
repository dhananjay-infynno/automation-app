<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Todo App') — {{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -left-32 top-0 h-96 w-96 rounded-full bg-indigo-600/20 blur-3xl"></div>
        <div class="absolute -right-32 bottom-0 h-96 w-96 rounded-full bg-violet-600/20 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        @yield('content')
    </div>
</body>
</html>
