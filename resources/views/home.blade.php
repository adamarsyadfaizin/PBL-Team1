<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Berlima Guest House</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    
    <!-- External Scripts -->
    <script src="https://upload-widget.cloudinary.com/global/all.js" type="text/javascript"></script>
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    <x-navbar />

    <main>
        <x-hero />
        <x-about />
        <x-features />
    </main>

    <x-footer />

    <!-- Scripts -->
    <script src="{{ asset('js/script.js') }}"></script>

</body>
</html>