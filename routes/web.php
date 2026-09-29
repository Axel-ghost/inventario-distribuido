<?php

use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }

    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'usuario' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    if (Auth::attempt(['usuario' => $credentials['usuario'], 'password' => $credentials['password']])) {
        $request->session()->regenerate();

        if (! Auth::user()->activo) {
            Auth::logout();
            return back()->withErrors(['usuario' => 'Tu cuenta está desactivada.']);
        }

        return redirect('/dashboard');
    }

    return back()->withErrors(['usuario' => 'Usuario o contraseña incorrectos.']);
})->name('login.attempt');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

/* ---------- RUTAS PROTEGIDAS (requieren sesión) ---------- */

Route::middleware(['auth'])->group(function () {

    // Todos los roles
    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    Route::post('/perfil/foto', function (Request $request) {
        $request->validate([
            'foto' => ['required', 'image', 'max:2048'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }

        $ruta = $request->file('foto')->store('perfiles', 'public');
        $user->update(['foto' => $ruta]);

        return back()->with('status', 'Foto actualizada correctamente.');
    });

    // Productos e Inventario: administrador, vendedor y almacén
    Route::middleware('rol:administrador,vendedor,almacen')->group(function () {

        Route::get('/productos', function () {
            $productos = Producto::all();
            return view('productos', compact('productos'));
        });

        Route::get('/inventario', function () {
            $productos = Producto::orderBy('existencia', 'asc')->get();

            $agotados = $productos->where('existencia', 0)->count();
            $bajos = $productos->filter(fn ($p) => $p->existencia > 0 && $p->existencia <= $p->stock_minimo)->count();

            return view('inventario', compact('productos', 'agotados', 'bajos'));
        });
    });

    // Consultar ventas: administrador, vendedor y caja
    Route::middleware('rol:administrador,vendedor,caja')->group(function () {

        Route::get('/ventas', function () {
            $ventas = [
                [
                    'id' => 1,
                    'fecha_venta' => '2026-09-20 10:15',
                    'usuario' => 'Axel Admin',
                    'items' => [
                        ['producto' => 'Teclado inalámbrico', 'cantidad' => 2, 'precio_unitario' => 350, 'subtotal' => 700],
                        ['producto' => 'Mouse óptico', 'cantidad' => 1, 'precio_unitario' => 180, 'subtotal' => 180],
                    ],
                    'total' => 880,
                ],
                [
                    'id' => 2,
                    'fecha_venta' => '2026-09-21 16:40',
                    'usuario' => 'Axel Admin',
                    'items' => [
                        ['producto' => 'Monitor 24"', 'cantidad' => 1, 'precio_unitario' => 2800, 'subtotal' => 2800],
                    ],
                    'total' => 2800,
                ],
                [
                    'id' => 3,
                    'fecha_venta' => '2026-09-22 09:05',
                    'usuario' => 'Axel Admin',
                    'items' => [
                        ['producto' => 'Silla gamer', 'cantidad' => 1, 'precio_unitario' => 3200, 'subtotal' => 3200],
                        ['producto' => 'Mouse óptico', 'cantidad' => 2, 'precio_unitario' => 180, 'subtotal' => 360],
                    ],
                    'total' => 3560,
                ],
            ];

            return view('ventas', compact('ventas'));
        });
    });

    // Registrar ventas: solo administrador y vendedor
    Route::middleware('rol:administrador,vendedor')->group(function () {

        Route::get('/ventas/crear', function () {
            return view('ventas.crear');
        });

        Route::get('/api/productos/buscar', function (Request $request) {
            $termino = $request->query('q', '');

            $productos = Producto::where('activo', true)
                ->where(function ($query) use ($termino) {
                    $query->where('nombre', 'like', "%{$termino}%")
                          ->orWhere('codigo', 'like', "%{$termino}%");
                })
                ->limit(8)
                ->get(['id', 'codigo', 'nombre', 'precio_venta', 'existencia']);

            return response()->json($productos);
        });

        Route::post('/ventas', function (Request $request) {
            $data = $request->validate([
                'items' => ['required', 'array', 'min:1'],
            ]);

            // TEMPORAL hasta que existan las tablas ventas / detalle_ventas
            dd($data);
        });
    });

    // Usuarios y permisos: solo administrador
    Route::middleware('rol:administrador')->group(function () {

        Route::get('/usuarios', function () {
            $usuarios = User::orderBy('name')->get();
            return view('usuarios.index', compact('usuarios'));
        });

        Route::get('/usuarios/crear', function () {
            return view('usuarios.crear');
        });

        Route::post('/usuarios', function (Request $request) {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'apellidos' => ['required', 'string', 'max:255'],
                'usuario' => ['required', 'string', 'max:50', 'unique:users,usuario'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
                'rol' => ['required', 'in:administrador,vendedor,caja,almacen'],
            ]);

            $data['activo'] = true;

            User::create($data);

            return redirect('/usuarios')->with('status', 'Usuario registrado correctamente.');
        });

        Route::get('/usuarios/{usuario}/editar', function (User $usuario) {
    return view('usuarios.editar', compact('usuario'));
});

Route::put('/usuarios/{usuario}', function (Request $request, User $usuario) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'apellidos' => ['required', 'string', 'max:255'],
        'usuario' => ['required', 'string', 'max:50', 'unique:users,usuario,' . $usuario->id],
        'email' => ['required', 'email', 'unique:users,email,' . $usuario->id],
        'rol' => ['required', 'in:administrador,vendedor,caja,almacen'],
        'password' => ['nullable', 'string', 'min:8'],
    ]);

    if (empty($data['password'])) {
        unset($data['password']);
    }

    $usuario->update($data);

    return redirect('/usuarios')->with('status', 'Usuario actualizado correctamente.');
});

        Route::patch('/usuarios/{usuario}/estado', function (User $usuario) {
            if ($usuario->id === Auth::id()) {
                return back()->withErrors(['estado' => 'No puedes desactivar tu propia cuenta.']);
            }

            $usuario->update(['activo' => ! $usuario->activo]);

            return back()->with('status', 'Estado del usuario actualizado.');
        });
    });
});