<?php

use App\Models\Producto;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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

        if (! Auth::user()->estado) {
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
            $productos = Producto::with('categoria', 'proveedor')->get();
            $categorias = Categoria::orderBy('nombre')->get();
            return view('productos', compact('productos', 'categorias'));

        });

        Route::get('/inventario', function () {
            $productos = Producto::orderBy('existencia', 'asc')->get();

            $agotados = $productos->where('existencia', 0)->count();
            $bajos = $productos->filter(fn ($p) => $p->existencia > 0 && $p->existencia <= $p->stock_minimo)->count();

            return view('inventario', compact('productos', 'agotados', 'bajos'));
        });
    });

    // Alta y edición de productos: administrador y almacén
    Route::middleware('rol:administrador,almacen')->group(function () {

        Route::get('/productos/crear', function () {
            $categorias = Categoria::all();
            $proveedores = Proveedor::all();
            return view('productos.crear', compact('categorias', 'proveedores'));
        });

        Route::post('/productos', function (Request $request) {
            $data = $request->validate([
                'codigo' => ['required', 'string', 'max:50', 'unique:productos,codigo'],
                'nombre' => ['required', 'string', 'max:255'],
                'descripcion' => ['nullable', 'string'],
                'id_categoria' => ['required', 'exists:categorias,id_categoria'],
                'id_proveedor' => ['nullable', 'exists:proveedores,id_proveedor'],
                'precio_compra' => ['required', 'numeric', 'min:0'],
                'precio_venta' => ['required', 'numeric', 'min:0'],
                'existencia' => ['required', 'integer', 'min:0'],
                'stock_minimo' => ['required', 'integer', 'min:0'],
            ]);

            $data['estado'] = true;

            Producto::create($data);

            return redirect('/productos')->with('status', 'Producto registrado correctamente.');
        });

        Route::get('/productos/{producto}/editar', function (Producto $producto) {
            $categorias = Categoria::all();
            $proveedores = Proveedor::all();
            return view('productos.editar', compact('producto', 'categorias', 'proveedores'));
        });

        Route::put('/productos/{producto}', function (Request $request, Producto $producto) {
            $data = $request->validate([
                'codigo' => ['required', 'string', 'max:50', 'unique:productos,codigo,' . $producto->id_producto . ',id_producto'],
                'nombre' => ['required', 'string', 'max:255'],
                'descripcion' => ['nullable', 'string'],
                'id_categoria' => ['required', 'exists:categorias,id_categoria'],
                'id_proveedor' => ['nullable', 'exists:proveedores,id_proveedor'],
                'precio_compra' => ['required', 'numeric', 'min:0'],
                'precio_venta' => ['required', 'numeric', 'min:0'],
                'existencia' => ['required', 'integer', 'min:0'],
                'stock_minimo' => ['required', 'integer', 'min:0'],
            ]);

            $producto->update($data);

            return redirect('/productos')->with('status', 'Producto actualizado correctamente.');
        });
    });

    // Baja lógica de productos: solo administrador
    Route::middleware('rol:administrador')->group(function () {

        Route::patch('/productos/{producto}/estado', function (Producto $producto) {
            $producto->update(['estado' => ! $producto->estado]);

            return back()->with('status', 'Estado del producto actualizado.');
        });
    });

    // Consultar ventas: administrador, vendedor y caja
    Route::middleware('rol:administrador,vendedor,caja')->group(function () {

        Route::get('/ventas', function () {
    $ventas = \App\Models\Venta::with([
            'pedido.detalles.producto',
            'usuario',
        ])
        ->orderByDesc('fecha')
        ->get();

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

    $productos = Producto::where('estado', true)
        ->where(function ($query) use ($termino) {
            $query->where('nombre', 'like', "%{$termino}%")
                ->orWhere('codigo', 'like', "%{$termino}%");
        })
        ->limit(8)
        ->get();

  
    $resultado = [];
    foreach ($productos as $p) {
        $resultado[] = [
            'id_producto'  => (int) $p->id,
            'codigo'       => (string) $p->codigo,
            'nombre'       => (string) $p->nombre,
            'precio_venta' => (float) $p->precio_venta,
            'existencia'   => (int) $p->existencia,
        ];
    }

    return response()->json($resultado);
});

        Route::post('/ventas', function (Request $request) {
            $request->validate([
                'metodo_pago' => ['required', 'in:efectivo,tarjeta,transferencia'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.id_producto' => ['required', 'exists:productos,id'],
                'items.*.cantidad' => ['required', 'integer', 'min:1'],
            ]);

            DB::beginTransaction();

            try {
                // 1. Calcular subtotal
                $subtotal = 0;
                foreach ($request->items as $item) {
                    $producto = Producto::findOrFail($item['id_producto']);

                    if ($producto->existencia < $item['cantidad']) {
                        throw new \Exception("Stock insuficiente de '{$producto->nombre}'. Disponible: {$producto->existencia}");
                    }

                    $subtotal += $producto->precio_venta * $item['cantidad'];
                }

                $impuesto = $subtotal * 0.16;
                $total = $subtotal + $impuesto;

                // 2. Crear pedido (sin cliente)
                $pedido = \App\Models\Pedido::create([
                    'folio' => 'VEN-' . strtoupper(uniqid()),                 
                    'estado' => 'completado',
                    'subtotal' => $subtotal,
                    'impuesto' => $impuesto,
                    'total' => $total,
                    'id_usuario' => Auth::id(),
                ]);

                // 3. Crear venta
                \App\Models\Venta::create([
                    'id_pedido' => $pedido->id,
                    'fecha' => now(),
                    'total' => $total,
                    'metodo_pago' => $request->metodo_pago,
                    'estado' => 'completado',
                    'id_usuario' => Auth::id(),
                ]);

                // 4. Detalles + stock + movimiento
                foreach ($request->items as $item) {
                    $producto = Producto::findOrFail($item['id_producto']);

                    \App\Models\DetallePedido::create([
                        'id_pedido' => $pedido->id,
                        'id_producto' => $producto->id,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $producto->precio_venta,
                        'subtotal' => $producto->precio_venta * $item['cantidad'],
                    ]);

                    $producto->existencia -= $item['cantidad'];
                    $producto->save();

                    \App\Models\InventarioMovimiento::create([
                        'id_producto' => $producto->id,
                        'tipo' => 'salida',
                        'cantidad' => $item['cantidad'],
                        'motivo' => "Venta {$pedido->folio}",
                        'id_usuario' => Auth::id(),
                    ]);
                }

                DB::commit();

                return redirect('/ventas')->with('status', "Venta {$pedido->folio} registrada correctamente.");

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withErrors(['venta' => $e->getMessage()]);
            }
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

            $data['estado'] = true;

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

            $usuario->update(['estado' => ! $usuario->estado]);

            return back()->with('status', 'Estado del usuario actualizado.');
        });
    });

});