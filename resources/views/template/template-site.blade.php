<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title') - Handgeev</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/icon.png') }}">

        <!-- Meta Tags -->
        <meta name="description" content="@yield('description', 'HandGeev - Crie e gerencie suas APIs de forma simples')">
        <meta name="keywords" content="api, workspace, json, handgeev, desenvolvimento">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        @vite('resources/css/app.css')
        @vite('resources/views/template/css/dashboard.css')
        
        @vite('resources/js/app.js')

        {{-- jQuery --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        {{-- jQuery Mask Plugin --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

        {{-- Flowbite JS --}}
        <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
    </head>
    <body class="font-sans antialiased text-white">
        {{-- Stack para estilos --}}
        @stack('style')
        
        @yield('content_site')

        {{-- Stack para scripts --}}
        @stack('scripts')
    </body>
</html>