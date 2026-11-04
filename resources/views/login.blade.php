<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CoreBuild | Login</title>

    <!-- 1. Agregamos dark.css y app.js -->
    @vite(['resources/css/app.css', 'resources/css/dark.css', 'resources/js/app.js'])

    <!-- 2. Script anti-flash para evitar parpadeo blanco -->
    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = saved || (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>

<body>

<main class="login-page">

    <div class="background-glow glow-one"></div>
    <div class="background-glow glow-two"></div>

    <section class="login-card">

        <div class="brand">
            <span class="brand-line"></span>

            <h1>CoreBuild</h1>

            <span class="brand-subtitle">
                GAMING
            </span>
        </div>

        <div class="login-heading">
            <h2>Bienvenidos</h2>

            <p>
                Sistema de inventario y ventas
            </p>
        </div>

    <form method="POST" action="/login">
    @csrf

    @if ($errors->any())
        <p style="color: #ef4444; margin-bottom: 16px;">
            {{ $errors->first() }}
        </p>
    @endif

    <div class="input-group">
        <label for="usuario">Usuario</label>
        <input
            type="text"
            id="usuario"
            name="usuario"
            placeholder="Ingresa tu usuario"
            value="{{ old('usuario') }}"
        >
    </div>

    <div class="input-group">
        <label for="password">Contraseña</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Ingresa tu contraseña"
        >
    </div>

    <button type="submit" class="login-button">
        INICIAR SESION
    </button>

    <a href="{{ route('tienda.index') }}" class="catalog-back-button">
        &larr; VOLVER AL CATÁLOGO
    </a>

    <p style="margin-top: 16px; text-align: center; color: var(--muted); font-size: 13px;">
    ¿Eres cliente nuevo? <a href="/tienda/registro" style="color: var(--accent);">Crea tu cuenta aquí</a>
    </p>

    </form>

        <p class="system-status">
             ▫️Sistema seguro
        </p>

    </section>

    <!-- 3. Botón flotante para el modo oscuro -->
    <button id="theme-toggle" class="action-btn" type="button" title="Cambiar tema" style="position: absolute; top: 20px; right: 20px; z-index: 100; padding: 10px 15px; background: var(--card);">
        <span id="theme-icon">☁︎</span>
    </button>

</main>

</body>
</html>