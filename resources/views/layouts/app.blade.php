<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Berlima Guest House')</title>

    <!-- FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- GLOBAL CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- CSS KHUSUS HALAMAN -->
    @stack('styles')
</head>

<body class="@yield('body-class')">

    <!-- NAVBAR (GLOBAL) -->
    <x-navbar />

    <!-- ISI HALAMAN -->
    <main>
        @yield('content')
    </main>

    <!-- FOOTER (GLOBAL) -->
    <x-footer />

    <!-- GLOBAL JS -->
    <script src="{{ asset('js/script.js') }}"></script>

    <!-- JS KHUSUS HALAMAN -->
    @stack('scripts')

</body>
</html>