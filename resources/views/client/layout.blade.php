<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Machin Barber')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite([
         'resources/css/app.css',
    'resources/css/client.css',
    'resources/js/app.js'
    ])

    @stack('styles')
</head>

<body>

<header>

    <nav class="navbar">

        <div class="logo">
            Machin Barber
        </div>

        <button type="button" class="nav-toggle" id="navToggle" aria-label="Abrir menú">
            ☰
        </button>

        <div class="nav-links" id="navLinks">

            <a href="{{ url('/') }}">
                Inicio
            </a>

            <a href="{{ url('/#services') }}">
                Servicios
            </a>

            <a href="{{ url('/#products') }}">
                Productos
            </a>

            @auth

                <a href="{{ route('appointments.mine') }}">
                    Mis citas
                </a>

                <a href="{{ route('purchases.mine') }}">
                    Mis compras
                </a>

            @endauth

            <a href="{{ route('cart.view') }}">
                Carrito
            </a>

            @guest

                <a href="{{ route('login') }}">
                    Iniciar sesión
                </a>

            @endguest

            @auth

                <form
                    action="{{ route('logout') }}"
                    method="POST"
                    style="display:inline;"
                >
                    @csrf

                    <button
                        type="submit"
                        class="logout-link"
                    >
                        Cerrar sesión
                    </button>

                </form>

            @endauth

        </div>

    </nav>

</header>

<main class="container">

    @yield('content')

</main>

<footer>

    <div class="footer-content">

        <p>
            © {{ date('Y') }} Machin Barber.
            Todos los derechos reservados.
        </p>

    </div>

</footer>

<script>
    (function () {
        var toggle = document.getElementById('navToggle');
        var links = document.getElementById('navLinks');

        if (toggle && links) {
            toggle.addEventListener('click', function () {
                links.classList.toggle('open');
            });
        }
    })();
</script>

@stack('scripts')

</body>

</html>
