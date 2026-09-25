<?php

use Illuminate\Support\Facades\Route;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }

    return view('login');
})->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    // Muestra el formulario de nueva venta
Route::get('/ventas/crear', function () {
    return view('ventas.crear');
});

// Busca productos en tiempo real (para el buscador)
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

// Procesa la venta (por ahora, simulado - sin guardar en BD todavía)
Route::post('/ventas', function (Request $request) {
    $data = $request->validate([
        'items' => ['required', 'array', 'min:1'],
    ]);

    // TEMPORAL: aquí solo mostramos lo que se habría guardado.
    // Cuando existan las tablas ventas/detalle_ventas, esto se
    // reemplaza por Venta::create(...) + los detalles.
    dd($data);
});

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

    Route::post('/perfil/foto', function (Request $request) {
    $request->validate([
        'foto' => ['required', 'image', 'max:2048'],
    ]);

    $user = Auth::user();

    if ($user->foto) {
        Storage::disk('public')->delete($user->foto);
    }

    $ruta = $request->file('foto')->store('perfiles', 'public');

    $user->update(['foto' => $ruta]);

    return back()->with('status', 'Foto actualizada correctamente.');
});

Route::get('/inventario', function () {
    $productos = Producto::orderBy('existencia', 'asc')->get();

    $agotados = $productos->where('existencia', 0)->count();
    $bajos = $productos->filter(fn ($p) => $p->existencia > 0 && $p->existencia <= $p->stock_minimo)->count();

    return view('inventario', compact('productos', 'agotados', 'bajos'));
});

Route::get('/productos', function () {
    $productos = Producto::all();
    return view('productos', compact('productos'));
    });
});


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