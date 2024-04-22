<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EasyJournal') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.cdnfonts.com/css/sf-pro-display" rel="stylesheet">
        <script src="https://kit.fontawesome.com/b871c9bab3.js" crossorigin="anonymous"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/editor.js'])
        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        {{-- <x-banner /> --}}

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">

            <button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" aria-controls="default-sidebar" type="button" class="inline-flex items-center p-2 mt-2 ml-3 text-sm text-gray-500 rounded-md sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                <span class="sr-only">Open sidebar</span>
                <svg class="flex-shrink-0 w-5 h-5 text-gray-600 transition duration-75 dark:text-gray-600 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                   <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                </svg>
             </button>

            @livewire('navigation-menu')

            <!-- Page Content -->
            <main class="p-4 sm:ml-64">
                    {{ $slot }}
            </main>

            @include('sweetalert::alert')


        </div>
        

        @stack('modals')

        @livewireScripts
    </body>
</html>
