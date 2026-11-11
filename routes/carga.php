<?php

use App\Models\Agencia;
use App\Services\CargaInventario;
use App\Services\LectorInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/*
 * Carga masiva de inventario (Excel .xlsx o XML).
 * Flujo: subir archivo -> vista previa con validación -> confirmar.
 * Este archivo se carga desde routes/web.php con: require __DIR__.'/carga.php';
 */

Route::middleware(['auth', 'rol:administrador,almacen'])->group(function () {

    // Formulario para subir el archivo
    Route::get('/inventario/carga', function () {
        $agencias = Auth::user()->agencia_id === null
            ? Agencia::where('activo', true)->orderBy('nombre')->get()
            : collect();

        return view('inventario.carga', compact('agencias'))->with('activePage', 'carga');
    });

    // Plantilla de ejemplo en XML
    Route::get('/inventario/carga/ejemplo.xml', function () {
        return response(LectorInventario::ejemploXml(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla-inventario.xml"',
        ]);
    });

    // Paso 1: leer y validar el archivo, sin guardar nada en el inventario
    Route::post('/inventario/carga', function (Request $request) {
        $esDueno = Auth::user()->agencia_id === null;

        $request->validate([
            'archivo' => ['required', 'file', 'max:2048', 'extensions:xlsx,xml'],
            'modo' => ['required', 'in:reemplazar,sumar'],
            'stock_minimo' => ['required', 'integer', 'min:0', 'max:100000'],
            'agencia_id' => [Rule::requiredIf($esDueno), 'nullable', 'exists:agencias,id'],
        ], [
            'archivo.required' => 'Elige un archivo .xlsx o .xml.',
            'archivo.max' => 'El archivo pesa más de 2 MB. Divídelo en partes más pequeñas.',
            'archivo.extensions' => 'El archivo debe ser .xlsx (Excel) o .xml.',
            'agencia_id.required' => 'Elige la agencia a la que se cargará el inventario.',
        ]);

        $archivo = $request->file('archivo');

        try {
            $resultado = LectorInventario::procesar($archivo->getRealPath(), $archivo->getClientOriginalExtension());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['archivo' => $e->getMessage()])->withInput();
        }

        if (empty($resultado['items'])) {
            $detalle = $resultado['errores'][0]['mensaje'] ?? 'El archivo no tiene productos.';

            return back()->withErrors(['archivo' => 'No encontré productos válidos. ' . $detalle])->withInput();
        }

        $token = (string) Str::uuid();

        CargaInventario::guardarBorrador($token, [
            'usuario_id' => Auth::id(),
            'agencia_id' => $esDueno ? (int) $request->input('agencia_id') : (int) Auth::user()->agencia_id,
            'modo' => $request->input('modo'),
            'stock_minimo' => (int) $request->input('stock_minimo'),
            'archivo' => $archivo->getClientOriginalName(),
            'resultado' => $resultado,
        ]);

        return redirect('/inventario/carga/' . $token);
    });

    // Paso 2: vista previa con lo que se va a crear y actualizar
    Route::get('/inventario/carga/{token}', function (string $token) {
        $borrador = CargaInventario::leerBorrador($token, Auth::id());

        if ($borrador === null) {
            return redirect('/inventario/carga')
                ->withErrors(['archivo' => 'Esa carga ya no está disponible. Sube el archivo de nuevo.']);
        }

        $resultado = $borrador['resultado'];
        $items = CargaInventario::comparar($resultado['items'], $borrador['agencia_id'], $borrador['modo']);

        $resumen = [
            'nuevos' => count(array_filter($items, fn ($i) => $i['estado'] === 'nuevo')),
            'actualizan' => count(array_filter($items, fn ($i) => $i['estado'] === 'actualiza')),
            'errores' => count($resultado['errores']),
            'avisos' => count(array_filter($items, fn ($i) => ! empty($i['avisos']))),
            'unidades' => array_sum(array_column($items, 'existencia')),
            'inversion' => array_sum(array_map(fn ($i) => $i['existencia'] * $i['precio_compra'], $items)),
        ];

        $agencia = Agencia::find($borrador['agencia_id']);
        $errores = $resultado['errores'];

        return view('inventario.carga-vista-previa', compact('token', 'borrador', 'items', 'errores', 'resumen', 'agencia'))
            ->with('activePage', 'carga');
    })->whereUuid('token');

    // Paso 3: confirmar y guardar
    Route::post('/inventario/carga/{token}', function (string $token) {
        $borrador = CargaInventario::reclamarBorrador($token, Auth::id());

        if ($borrador === null) {
            return redirect('/inventario/carga')
                ->withErrors(['archivo' => 'Esa carga ya se aplicó o ya no está disponible. Revisa el inventario antes de repetirla.']);
        }

        try {
            $resultado = CargaInventario::importar(
                $borrador['resultado']['items'],
                $borrador['agencia_id'],
                $borrador['modo'],
                $borrador['stock_minimo']
            );
        } catch (\Throwable $e) {
            report($e);
            CargaInventario::soltarBorrador($token);

            return redirect('/inventario/carga/' . $token)
                ->withErrors(['carga' => 'No se pudo completar la carga y no se guardó nada. Inténtalo de nuevo en un momento.']);
        }

        CargaInventario::borrarBorrador($token);

        return redirect('/inventario')->with(
            'status',
            "Carga completada: {$resultado['creados']} productos nuevos y {$resultado['actualizados']} actualizados."
        );
    })->whereUuid('token');
});