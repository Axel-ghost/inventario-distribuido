<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild | Usuarios</title>
    
    @vite(['resources/css/app.css', 'resources/css/dark.css', 'resources/js/app.js'])

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

    <div class="dashboard">

        <x-sidebar active-page="usuarios" />

        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>Usuarios</h2>
                    <p class="page-subtitle">Administración de usuarios y roles</p>
                </div>

                <x-profile-card />
            </header>

            @if (session('status'))
                <p style="color: #22c55e; margin-bottom: 16px; font-weight: 500;">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <p style="color: #dc2626; margin-bottom: 16px; font-weight: 500;">{{ $errors->first() }}</p>
            @endif

            <div class="table-actions-top">
                <a href="/usuarios/crear" class="btn-process" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; max-width: 200px; height: 44px; border-radius: 8px;">
                    <span class="btn-label">+ Nuevo usuario</span>
                </a>
            </div>

            <div class="table-container">
                <table class="data-table-clean">
                    <thead>
                        <tr>
                            <th>NOMBRE</th>
                            <th>USUARIO</th>
                            <th>CORREO</th>
                            <th>ROL</th>
                            <th>ESTADO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $u)
                            <tr>
                                <td>{{ $u->name }} {{ $u->apellidos }}</td>
                                <td>{{ $u->usuario }}</td>
                                <td>{{ $u->email }}</td>
                                <td>{{ ucfirst($u->rol) }}</td>
                                <td>
                                    @if ($u->activo)
                                        <span class="status-badge status-success">Activo</span>
                                    @else
                                        <span class="status-badge status-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="actions-wrapper">
                                        <a href="/usuarios/{{ $u->id }}/editar" class="btn-action btn-link">
                                            Editar
                                        </a>

                                        @if ($u->id !== Auth::id())
                                            <form method="POST" action="/usuarios/{{ $u->id }}/estado" style="margin: 0; display: flex;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-action btn-link">
                                                    {{ $u->activo ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </main>

    </div>

</body>

</html>