<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EasyJournal') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.cdnfonts.com/css/sf-pro-display" rel="stylesheet">
    <script src="https://kit.fontawesome.com/b871c9bab3.js" crossorigin="anonymous"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css','resources/js/app.js', 'resources/js/codex-editor.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

</head>
<body class="antialiased bg-gray-100">
    <main>
        @yield('content')
    </main>  
</body>

</html>
