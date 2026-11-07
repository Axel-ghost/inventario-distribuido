<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild | @yield('title', 'Sistema')</title>
 
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
            background-color: #f4f7f9; 
        }

        [data-theme="dark"] html {
            background-color: #0f172a;
        }
 
        body {
            margin: 0;
            min-height: 100vh;
            background-color: transparent; 
            transition: background-color 0.3s ease, color 0.3s ease;
        }
 
        /* Fondo en modo claro: SIN CAPA BLANCA */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none; 
            background: var(--bg-image) center / cover no-repeat;
            /* Si notas que el texto no se lee, puedes agregar una capa MUY leve aquí: */
            /* background: linear-gradient(rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.1)), var(--bg-image) center / cover no-repeat; */
        }
 
        /* Fondo en modo oscuro: mantenemos tu capa oscura original para que el texto resalte */
        [data-theme="dark"] body::before {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)), var(--bg-image) center / cover no-repeat;
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
 
                <!-- Tarjeta de perfil del usuario -->
                <x-profile-card />
            </header>
 
            <!-- Mensajes de éxito -->
            @if (session('status'))
                <p style="color: #22c55e; margin-bottom: 16px; font-weight: 500;">{{ session('status') }}</p>
            @endif
 
            <!-- AQUÍ SE INYECTA EL CONTENIDO DE CADA VISTA -->
            @yield('content')
 
        </main>
    </div>
 
    <!-- Scripts inyectados desde las vistas -->
    @stack('scripts')
 
</body>
</html>