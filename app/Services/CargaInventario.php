<?php

namespace App\Services;

use App\Models\Categoria;
use App\Models\Proveedor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Compara una carga masiva contra la base y la guarda.
 * Trabaja con DB::table para escribir siempre en la agencia elegida,
 * sin depender del filtro automático por agencia del usuario en sesión.
 */
class CargaInventario
{
    /**
     * Marca cada producto como 'nuevo' o 'actualiza' y calcula la existencia final.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    public static function comparar(array $items, int $agenciaId, string $modo): array
    {
        $existentes = [];

        foreach (array_chunk(array_column($items, 'codigo'), 500) as $codigos) {
            $filas = DB::table('productos')
                ->where('agencia_id', $agenciaId)
                ->whereIn('codigo', $codigos)
                ->get(['codigo', 'existencia']);

            foreach ($filas as $fila) {
                $existentes[$fila->codigo] = $fila;
            }
        }

        foreach ($items as &$item) {
            $actual = $existentes[$item['codigo']] ?? null;

            $item['estado'] = $actual ? 'actualiza' : 'nuevo';
            $item['existencia_actual'] = $actual ? (int) $actual->existencia : null;
            $item['existencia_final'] = $actual && $modo === 'sumar'
                ? (int) $actual->existencia + $item['existencia']
                : $item['existencia'];
        }
        unset($item);

        return $items;
    }

    /**
     * Guarda la carga completa en una sola transacción: o entra todo o no entra nada.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array{creados: int, actualizados: int}
     */
    public static function importar(array $items, int $agenciaId, string $modo, int $stockMinimo): array
    {
        $creados = 0;
        $actualizados = 0;
        $ahora = now();

        DB::transaction(function () use ($items, $agenciaId, $modo, $stockMinimo, $ahora, &$creados, &$actualizados) {
            foreach (array_chunk($items, 500) as $lote) {
                $existentes = [];

                $filas = DB::table('productos')
                    ->where('agencia_id', $agenciaId)
                    ->whereIn('codigo', array_column($lote, 'codigo'))
                    ->lockForUpdate()
                    ->get();

                foreach ($filas as $fila) {
                    $existentes[$fila->codigo] = $fila;
                }

                foreach ($lote as $item) {
                    $actual = $existentes[$item['codigo']] ?? null;

                    if ($actual) {
                        $cambios = [
                            'nombre' => $item['nombre'],
                            'existencia' => $modo === 'sumar'
                                ? (int) $actual->existencia + $item['existencia']
                                : $item['existencia'],
                            'precio_compra' => $item['precio_compra'],
                            'precio_venta' => $item['precio_venta'],
                            'updated_at' => $ahora,
                        ];

                        if ($item['categoria'] !== null) {
                            $cambios['categoria'] = $item['categoria'];
                        }
                        if ($item['proveedor'] !== null) {
                            $cambios['proveedor'] = $item['proveedor'];
                        }
                        if ($item['stock_minimo'] !== null) {
                            $cambios['stock_minimo'] = $item['stock_minimo'];
                        }

                        DB::table('productos')->where('id', $actual->id)->update($cambios);
                        $actualizados++;
                    } else {
                        DB::table('productos')->insert([
                            'codigo' => $item['codigo'],
                            'nombre' => $item['nombre'],
                            'descripcion' => null,
                            'categoria' => $item['categoria'],
                            'proveedor' => $item['proveedor'],
                            'imagen_url' => null,
                            'precio_compra' => $item['precio_compra'],
                            'precio_venta' => $item['precio_venta'],
                            'existencia' => $item['existencia'],
                            'stock_minimo' => $item['stock_minimo'] ?? $stockMinimo,
                            'activo' => true,
                            'agencia_id' => $agenciaId,
                            'created_at' => $ahora,
                            'updated_at' => $ahora,
                        ]);
                        $creados++;
                    }
                }
            }

            // Catálogos de categorías y proveedores (los mismos que llena el alta manual).
            foreach (array_unique(array_filter(array_column($items, 'categoria'))) as $nombre) {
                Categoria::firstOrCreate(['nombre' => $nombre]);
            }
            foreach (array_unique(array_filter(array_column($items, 'proveedor'))) as $nombre) {
                Proveedor::firstOrCreate(['nombre' => $nombre]);
            }
        });

        return ['creados' => $creados, 'actualizados' => $actualizados];
    }

    /* ------------------------------------------------------------------ */
    /*  Borrador entre la vista previa y la confirmación                   */
    /* ------------------------------------------------------------------ */

    /** @param array<string, mixed> $datos */
    public static function guardarBorrador(string $token, array $datos): void
    {
        $disco = Storage::disk('local');

        // Limpieza: borradores de más de un día.
        foreach ($disco->files('cargas') as $archivo) {
            if ($disco->lastModified($archivo) < now()->subDay()->getTimestamp()) {
                $disco->delete($archivo);
            }
        }

        $disco->put("cargas/{$token}.json", json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
    }

    /** Lee el borrador sin tocarlo (para la vista previa). Solo lo ve quien lo subió. */
    public static function leerBorrador(string $token, int $usuarioId): ?array
    {
        $disco = Storage::disk('local');
        $ruta = "cargas/{$token}.json";

        if (! $disco->exists($ruta)) {
            return null;
        }

        $datos = json_decode((string) $disco->get($ruta), true);

        return is_array($datos) && ($datos['usuario_id'] ?? null) === $usuarioId ? $datos : null;
    }

    /**
     * Toma el borrador para importarlo. Si dos clics llegan a la vez, solo uno lo consigue,
     * y así una carga en modo "sumar" nunca se aplica dos veces.
     */
    public static function reclamarBorrador(string $token, int $usuarioId): ?array
    {
        $datos = self::leerBorrador($token, $usuarioId);
        if ($datos === null) {
            return null;
        }

        try {
            $movido = Storage::disk('local')->move("cargas/{$token}.json", "cargas/{$token}.procesando");
        } catch (\Throwable) {
            return null;
        }

        return $movido ? $datos : null;
    }

    /** Devuelve el borrador a su lugar si la importación falló, para poder reintentar. */
    public static function soltarBorrador(string $token): void
    {
        try {
            Storage::disk('local')->move("cargas/{$token}.procesando", "cargas/{$token}.json");
        } catch (\Throwable) {
            // Nada que hacer: el usuario volverá a subir el archivo.
        }
    }

    public static function borrarBorrador(string $token): void
    {
        Storage::disk('local')->delete(["cargas/{$token}.json", "cargas/{$token}.procesando"]);
    }
}