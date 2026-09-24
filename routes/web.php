<?php

use Illuminate\Support\Facades\Route;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use illuminate\Support\Facades\Storage;

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