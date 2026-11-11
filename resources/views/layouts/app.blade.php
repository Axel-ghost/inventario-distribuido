<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAJAJ | @yield('title', 'Sistema')</title>

    <!-- 1. Script anti-flash para el tema (antes de cargar los estilos) -->
    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = saved || (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    @vite([
        'resources/css/app.css',
        'resources/css/dark.css',
        'resources/js/app.js'
    ])

    <!-- 2. Fondo en el HEAD para evitar flashazos -->
        <style>
        :root {
            --bg-image: url('{{ asset('images/moto.png') }}');
        }

        html {
            scrollbar-gutter: stable;
            background-color: transparent !important; 
        }

        /* 1. La imagen de fondo va en el HTML (no en el body) para que cubra todo */
        html::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -2; /* Detrás de todo */
            pointer-events: none;
            background: var(--bg-image) center / cover no-repeat;
        }

        /* 2. Capa oscura para el modo oscuro */
        html::after {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1; /* Entre la imagen y el contenido */
            pointer-events: none;
            background: transparent;
            transition: background 0.3s ease;
        }

        html[data-theme="dark"]::after {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8));
        }

        body {
            margin: 0;
            min-height: 100vh;
            background-color: transparent !important; /* Importante: transparente para que se vea la moto */
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>
</head>

<body>

    <div class="dashboard">

        <!-- Sidebar dinámico según la página activa -->
        <x-sidebar :active-page="$activePage ?? 'dashboard'" />

        <!-- Contenedor central que se reemplaza con JavaScript. -->
        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>@yield('header_title')</h2>
                    <p class="page-subtitle">@yield('header_subtitle')</p>
                </div>

                <!-- Tarjeta de perfil del usuario (con el botón de tema integrado) -->
                <x-profile-card />
            </header>

            <!-- Mensajes de éxito -->
            @if (session('status'))
                <p style="color: #22c55e; margin-bottom: 16px; font-weight: 500;">{{ session('status') }}</p>
            @endif

            <!-- AQUÍ SE INYECTA EL CONTENIDO DE CADA VISTA -->
            @yield('content')

            <!-- Scripts de las vistas (dentro de .dashboard-content para que el SPA los ejecute) -->
            @stack('scripts')

        </main>
    </div>

</body>
</html>