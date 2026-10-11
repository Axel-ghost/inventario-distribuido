<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild Gaming | Crear cuenta</title>
    @vite(['resources/css/app.css'])
</head>

<body class="tienda-body">

    <x-tienda-nav />

    <main class="tienda-main" style="display: flex; justify-content: center;">

        <div>
            <h2 style="margin-bottom: 20px;">Crea tu cuenta</h2>

            @if ($errors->any())
                <div style="color: #ef4444; margin-bottom: 16px;">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="/tienda/registro" class="form-card">
                @csrf

                <div class="input-group">
                    <label for="name">Nombre</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                </div>

                <div class="input-group">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos') }}" required>
                </div>

                <div class="input-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" value="{{ old('usuario') }}" required>
                </div>

                <div class="input-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="input-group">
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn-process">
                    <span class="btn-label">Crear cuenta</span>
                </button>

                <p style="margin-top: 16px; text-align: center; color: var(--muted);">
                    ¿Ya tienes cuenta? <a href="/" style="color: var(--accent);">Inicia sesión</a>
                </p>
            </form>
        </div>

    </main>

</body>

</html>