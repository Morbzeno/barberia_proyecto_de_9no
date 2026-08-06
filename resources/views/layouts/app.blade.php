<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Machin Barber')</title>

    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght=500;700&family=Poppins:wght=300;400;500;600&display=swap" rel="stylesheet">

    @yield('styles')
</head>
<body>

    @yield('content')

    @yield('scripts')
</body>
</html>