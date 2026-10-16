<?php

use App\Models\DetallePedido;
use App\Models\Pedido;
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
        return redirect(Auth::user()->rol === 'cliente' ? '/tienda' : '/dashboard');
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

        $destinoDefault = Auth::user()->rol === 'cliente' ? '/tienda' : '/dashboard';

        return redirect()->intended($destinoDefault);
    }

    return back()->withErrors(['usuario' => 'Usuario o contraseña incorrectos.']);
})->name('login.attempt');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

/* ---------- TIENDA PÚBLICA (sin necesidad de cuenta) ---------- */

Route::get('/tienda', function () {
    $termino = request('q');

    $query = Producto::where('activo', true)->where('existencia', '>', 0);

    if ($termino) {
        $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('categoria', 'like', "%{$termino}%");
        });
    }

    $productos = $query->orderBy('nombre')->get();

    $categorias = Producto::where('activo', true)
        ->whereNotNull('categoria')
        ->distinct()
        ->pluck('categoria');

    return view('tienda.index', compact('productos', 'categorias'));
})->name('tienda.index');

Route::get('/tienda/carrito', function () {
    $carrito = session('carrito', []);
    $total = collect($carrito)->sum(fn ($item) => $item['precio'] * $item['cantidad']);

    return view('tienda.carrito', compact('carrito', 'total'));
})->name('tienda.carrito');

Route::post('/tienda/carrito/agregar', function (Request $request) {
    $producto = Producto::where('activo', true)->findOrFail($request->input('producto_id'));

    $carrito = session('carrito', []);
    $cantidadActual = $carrito[$producto->id]['cantidad'] ?? 0;

    $carrito[$producto->id] = [
        'producto_id' => $producto->id,
        'codigo' => $producto->codigo,
        'nombre' => $producto->nombre,
        'precio' => (float) $producto->precio_venta,
        'imagen_url' => $producto->imagen_url,
        'cantidad' => min($cantidadActual + 1, $producto->existencia),
    ];

    session(['carrito' => $carrito]);

    return back()->with('status', $producto->nombre . ' se agregó al carrito.');
})->name('tienda.carrito.agregar');

Route::post('/tienda/carrito/actualizar', function (Request $request) {
    $carrito = session('carrito', []);
    $id = $request->input('producto_id');

    if (isset($carrito[$id])) {
        $producto = Producto::find($id);
        $cantidad = max(1, (int) $request->input('cantidad', 1));

        $carrito[$id]['cantidad'] = $producto ? min($cantidad, $producto->existencia) : $cantidad;
        session(['carrito' => $carrito]);
    }

    return back();
})->name('tienda.carrito.actualizar');

Route::post('/tienda/carrito/quitar', function (Request $request) {
    $carrito = session('carrito', []);
    unset($carrito[$request->input('producto_id')]);
    session(['carrito' => $carrito]);

    return back()->with('status', 'Producto quitado del carrito.');
})->name('tienda.carrito.quitar');

Route::get('/tienda/registro', function () {
    if (Auth::check()) {
        return redirect('/tienda/checkout');
    }

    return view('tienda.registro');
})->name('tienda.registro');

Route::post('/tienda/registro', function (Request $request) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'apellidos' => ['required', 'string', 'max:255'],
        'usuario' => ['required', 'string', 'max:50', 'unique:users,usuario'],
        'email' => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $nuevoCliente = User::create([
        'name' => $data['name'],
        'apellidos' => $data['apellidos'],
        'usuario' => $data['usuario'],
        'email' => $data['email'],
        'password' => $data['password'],
        'rol' => 'cliente',
        'activo' => true,
    ]);

    Auth::login($nuevoCliente);
    $request->session()->regenerate();

    return redirect()->intended('/tienda/checkout');
})->name('tienda.registro.guardar');

/* ---------- RUTAS PROTEGIDAS (requieren sesión) ---------- */

Route::middleware(['auth'])->group(function () {

    // Todos los roles internos
    Route::get('/dashboard', function () {
         if (Auth::user()->rol === 'cliente') {
            return redirect('/tienda');
        }
    // KPIs
    $totalProductos = \App\Models\Producto::where('estado', true)->count();

    $stockBajo = \App\Models\Producto::where('estado', true)
        ->whereColumn('existencia', '<=', 'stock_minimo')
        ->where('existencia', '>', 0)
        ->count();
    
    $productosAgotados = \App\Models\Producto::where('estado', true)
        ->where('existencia', 0)
        ->count();

    $ventasDelMes = \App\Models\Venta::whereMonth('fecha', now()->month)
        ->whereYear('fecha', now()->year)
        ->sum('total');

    $pedidosPendientes = \App\Models\Pedido::where('estado', '!=', 'completado')->count();

    
    $productosStockBajo = \App\Models\Producto::with('categoria')
        ->where('estado', true)
        ->whereColumn('existencia', '<=', 'stock_minimo')
        ->where('existencia', '>', 0)
        ->orderBy('existencia', 'asc')
        ->take(3)
        ->get();

    // Operaciones recientes (últimos movimientos de inventario)
    $operacionesRecientes = \App\Models\InventarioMovimiento::with('producto')
        ->orderByDesc('created_at')
        ->take(3)
        ->get();

    return view('dashboard', compact(
        'totalProductos',
        'stockBajo',
        'productosAgotados',
        'ventasDelMes',
        'pedidosPendientes',
        'productosStockBajo',
        'operacionesRecientes',
    ));
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

    // Checkout: cualquier usuario logueado (pensado para clientes)
    Route::get('/tienda/checkout', function () {
        $carrito = session('carrito', []);

        if (empty($carrito)) {
            return redirect('/tienda')->with('status', 'Tu carrito está vacío.');
        }

        $total = collect($carrito)->sum(fn ($item) => $item['precio'] * $item['cantidad']);

        return view('tienda.checkout', compact('carrito', 'total'));
    })->name('tienda.checkout');

    Route::post('/tienda/checkout', function (Request $request) {
        $carrito = session('carrito', []);

        if (empty($carrito)) {
            return redirect('/tienda');
        }

        $total = collect($carrito)->sum(fn ($item) => $item['precio'] * $item['cantidad']);

        session(['ultimo_pedido_cliente' => [
            'folio' => 'PED-' . now()->format('YmdHis'),
            'fecha' => now()->format('d/m/Y H:i'),
            'cliente' => Auth::user()->name . ' ' . Auth::user()->apellidos,
            'items' => array_values($carrito),
            'total' => $total,
        ]]);

        session()->forget('carrito');

        return redirect('/tienda/confirmacion');
    });

    Route::get('/tienda/confirmacion', function () {
        $pedido = session('ultimo_pedido_cliente');

        if (! $pedido) {
            return redirect('/tienda');
        }

        return view('tienda.confirmacion', compact('pedido'));
    })->name('tienda.confirmacion');

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
                'id_categoria' => ['required', 'exists:categorias,id'],
                'id_proveedor' => ['nullable', 'exists:proveedores,id'],
                'imagen_url' => ['nullable', 'url', 'max:500'],
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
                'codigo' => ['required', 'string', 'max:50', 'unique:productos,codigo,' . $producto->id . ',id'],
                'nombre' => ['required', 'string', 'max:255'],
                'descripcion' => ['nullable', 'string'],
                'id_categoria' => ['required', 'exists:categorias,id'],
                'id_proveedor' => ['nullable', 'exists:proveedores,id'],
                'imagen_url' => ['nullable', 'url', 'max:500'],
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

    // Pedidos (requisiciones de reabasto): administrador y almacén
    Route::middleware('rol:administrador,almacen')->group(function () {

        Route::get('/pedidos', function () {
            $query = Pedido::with('solicitante', 'detalles.producto')->orderByDesc('created_at');

            if (Auth::user()->rol === 'almacen') {
                $query->where('solicitante_id', Auth::id());
            }

            $pedidos = $query->get();

            return view('pedidos.index', compact('pedidos'));
        });

        Route::get('/pedidos/crear', function (Request $request) {
            $sugerido = null;

            if ($request->filled('producto_id')) {
                $producto = Producto::find($request->query('producto_id'));

                if ($producto) {
                    $sugerido = [
                        'id' => $producto->id,
                        'codigo' => $producto->codigo,
                        'nombre' => $producto->nombre,
                        'cantidad' => max($producto->stock_minimo * 2 - $producto->existencia, 1),
                    ];
                }
            }

            return view('pedidos.crear', compact('sugerido'));
        });

        Route::post('/pedidos', function (Request $request) {
            $data = $request->validate([
                'items' => ['required', 'array', 'min:1'],
                'items.*.producto_id' => ['required', 'exists:productos,id'],
                'items.*.cantidad' => ['required', 'integer', 'min:1'],
            ]);

            $pedido = Pedido::create([
                'folio' => 'REQ-' . now()->format('YmdHis'),
                'solicitante_id' => Auth::id(),
                'estado' => 'solicitado',
                'fecha_solicitud' => now(),
            ]);

            foreach ($data['items'] as $item) {
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad_solicitada' => $item['cantidad'],
                ]);
            }

            return redirect('/pedidos/' . $pedido->id)->with('status', 'Requisición enviada correctamente.');
        });

        Route::get('/pedidos/{pedido}', function (Pedido $pedido) {
            if (Auth::user()->rol === 'almacen' && $pedido->solicitante_id !== Auth::id()) {
                abort(403);
            }

            $pedido->load('solicitante', 'revisor', 'detalles.producto');

            return view('pedidos.show', compact('pedido'));
        });

        Route::patch('/pedidos/{pedido}/finalizar', function (Pedido $pedido) {
            if ($pedido->estado !== 'aprobado') {
                return back()->withErrors(['pedido' => 'Solo se puede finalizar un pedido ya aprobado.']);
            }

            foreach ($pedido->detalles as $detalle) {
                $detalle->producto->increment('existencia', $detalle->cantidad_solicitada);
            }

            $pedido->update(['estado' => 'finalizado']);

            return back()->with('status', 'Pedido marcado como finalizado. Inventario actualizado.');
        });
    });

    // Revisar, aprobar y rechazar pedidos: solo administrador
    Route::middleware('rol:administrador')->group(function () {

        Route::patch('/pedidos/{pedido}/revisar', function (Pedido $pedido) {
            $pedido->update([
                'estado' => 'en_revision',
                'revisor_id' => Auth::id(),
            ]);

            return back()->with('status', 'Pedido puesto en revisión.');
        });

        Route::patch('/pedidos/{pedido}/aprobar', function (Pedido $pedido) {
            $pedido->update([
                'estado' => 'aprobado',
                'revisor_id' => Auth::id(),
                'fecha_resolucion' => now(),
            ]);

            return back()->with('status', 'Pedido aprobado.');
        });

        Route::patch('/pedidos/{pedido}/rechazar', function (Request $request, Pedido $pedido) {
            $request->validate(['comentario_admin' => ['required', 'string', 'max:500']]);

            $pedido->update([
                'estado' => 'rechazado',
                'revisor_id' => Auth::id(),
                'comentario_admin' => $request->comentario_admin,
                'fecha_resolucion' => now(),
            ]);

            return back()->with('status', 'Pedido rechazado.');
        });
    });

    // Consultar ventas: administrador, vendedor y caja
    Route::middleware('rol:administrador,vendedor,caja')->group(function () {

        Route::get('/ventas', function () {
    $ventas = \App\Models\Venta::with([
            'detalles.producto',
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
    });

    // Búsqueda de productos: administrador, vendedor y almacén (ventas Y pedidos la usan)
    Route::middleware('rol:administrador,vendedor,almacen')->group(function () {

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

        // 2. Crear la venta (sin pedido)
        $venta = \App\Models\Venta::create([
            'fecha' => now(),
            'total' => $total,
            'metodo_pago' => $request->metodo_pago,
            'estado' => 'completado',
            'id_usuario' => Auth::id(),
        ]);

        // 3. Crear detalles + descontar stock + movimiento de inventario
        foreach ($request->items as $item) {
            $producto = Producto::findOrFail($item['id_producto']);

            \App\Models\VentaDetalle::create([
                'id_venta' => $venta->id,
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
                'motivo' => "Venta #{$venta->id}",
                'id_usuario' => Auth::id(),
            ]);
        }

        DB::commit();

        return redirect('/ventas')->with('status', "Venta #{$venta->id} registrada correctamente.");

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
                'rol' => ['required', 'in:administrador,vendedor,caja,almacen,cliente'],
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
                'rol' => ['required', 'in:administrador,vendedor,caja,almacen,cliente'],
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