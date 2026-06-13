<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Perpustakaan SMP 11 Jember') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-8">
            <a href="/" class="mb-6 flex flex-col items-center gap-3 text-center">
                <x-application-logo class="h-20 w-20 rounded-lg bg-white p-2 shadow-sm ring-1 ring-slate-200" />
                <div>
                    <span class="block text-xl font-extrabold text-brand-navy">Perpustakaan Digital</span>
                    <span class="block text-sm font-semibold text-slate-500">SMP Negeri 11 Jember</span>
                </div>
            </a>

            <div class="w-full max-w-md rounded-lg border border-slate-200 bg-white/95 p-6 shadow-sm">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
