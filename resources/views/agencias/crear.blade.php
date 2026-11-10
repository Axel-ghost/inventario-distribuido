<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild | Nueva agencia</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <div class="dashboard">

        <x-sidebar active-page="agencias" />

        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>Nueva agencia</h2>
                    <p>Registra una sucursal con sus propios datos</p>
                </div>

                <x-profile-card />
            </header>

            @if ($errors->any())
                <div style="color: #ef4444; margin-bottom: 16px;">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="/agencias" class="form-card">
                @csrf

                <div class="input-group">
                    <label for="nombre">Nombre de la agencia</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                </div>

                <div class="input-group">
                    <label for="ciudad">Ciudad</label>
                    <input type="text" id="ciudad" name="ciudad" value="{{ old('ciudad') }}">
                </div>

                <button type="submit" class="btn-process">
                    <span class="btn-label">Registrar agencia</span>
                </button>

            </form>

        </main>

    </div>

</body>

</html>