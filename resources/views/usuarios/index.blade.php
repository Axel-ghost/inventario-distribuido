<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild | Usuarios</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <div class="dashboard">

        <x-sidebar active-page="usuarios" />

        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>Usuarios</h2>
                    <p>Administración de usuarios y roles</p>
                </div>

                <x-profile-card />
            </header>

            @if (session('status'))
                <p style="color: #22c55e; margin-bottom: 16px;">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <p style="color: #ef4444; margin-bottom: 16px;">{{ $errors->first() }}</p>
            @endif

            <a href="/usuarios/crear" class="btn-process"
               style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; max-width: 200px; margin-bottom: 20px;">
                <span class="btn-label">+ Nuevo usuario</span>
            </a>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th></th>
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
                                @if ($u->estado)
                                    <span class="badge badge-ok">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
    {{-- Editar (azul) --}}
    <a href="/usuarios/{{ $u->id }}/editar" class="accion-link">
        Editar
    </a>

    {{-- Activar (verde) / Desactivar (rojo) --}}
    @if ($u->id !== Auth::id())
        <form method="POST" action="/usuarios/{{ $u->id }}/estado" style="display: inline;">
            @csrf
            @method('PATCH')
            <button type="submit" class="accion-link {{ $u->estado ? 'accion-danger' : 'accion-ok' }}">
                {{ $u->estado ? 'Desactivar' : 'Activar' }}
            </button>
        </form>
    @endif
</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </main>

    </div>

</body>

</html>