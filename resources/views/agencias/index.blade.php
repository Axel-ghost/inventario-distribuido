<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild | Agencias</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <div class="dashboard">

        <x-sidebar active-page="agencias" />

        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>Agencias</h2>
                    <p>Sucursales con sus propios datos</p>
                </div>

                <x-profile-card />
            </header>

            @if (session('status'))
                <p style="color: #22c55e; margin-bottom: 16px;">{{ session('status') }}</p>
            @endif

            <a href="/agencias/crear" class="btn-process" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; max-width: 200px; margin-bottom: 20px;">
                <span class="btn-label">+ Nueva agencia</span>
            </a>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Agencia</th>
                        <th>Ciudad</th>
                        <th>Usuarios</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($agencias as $agencia)
                        <tr>
                            <td>{{ $agencia->nombre }}</td>
                            <td>{{ $agencia->ciudad ?? '—' }}</td>
                            <td>{{ $agencia->usuarios_count }}</td>
                            <td>
                                @if ($agencia->activo)
                                    <span class="badge badge-ok">Activa</span>
                                @else
                                    <span class="badge badge-danger">Inactiva</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Todavía no hay agencias registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </main>

    </div>

</body>

</html>