<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Autentikasi')</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>

<body class="@yield('body-class')">

    <main>
        @yield('content')
    </main>

    <x-floating-whatsapp />

    <script src="{{ asset('js/script.js') }}"></script>
    @stack('scripts')

</body>
</html>
