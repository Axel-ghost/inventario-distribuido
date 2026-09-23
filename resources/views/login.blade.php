<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CoreBuild | Login</title>

    @vite(['resources/css/app.css'])
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
            </apan>
        </div>

        <div class="login-heading">
            <h2>Bienvenidos</h2>

            <p>
                Sistema de inventario y ventas
            </p>
        </div>

        <form>

            <div class="input-group">
                <label for="username">
                    Usuario
                </label>

                <input 
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Ingresa tu usuario"
                    >
            </div>

            <div class="input-group">
                <label for="password">
                    Contraseña
                </label>

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
            
        </form>

        <p class="system-status">
             ▫️Sistema seguro
        </p>

    </section>

</main>