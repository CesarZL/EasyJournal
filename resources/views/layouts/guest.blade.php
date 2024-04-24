<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EasyJournal') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.cdnfonts.com/css/sf-pro-display" rel="stylesheet">
        <script src="https://kit.fontawesome.com/b871c9bab3.js" crossorigin="anonymous"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body>
        <div class="font-sans text-gray-900 dark:text-gray-100 antialiased bg-gray-100 dark:bg-gray-90">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
