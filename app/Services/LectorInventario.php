<?php

namespace App\Services;

use RuntimeException;
use ZipArchive;

/**
 * Lee un inventario desde Excel (.xlsx) o XML y lo deja limpio y validado.
 * No depende de Laravel ni de librerías externas (solo ZipArchive y SimpleXML).
 */
class LectorInventario
{
    /** Encabezados aceptados (ya normalizados: minúsculas, sin acentos ni signos). */
    private const ALIAS = [
        'codigo' => ['no de parte', 'numero de parte', 'num de parte', 'codigo', 'clave', 'sku', 'parte'],
        'nombre' => ['descripcion de la pieza', 'descripcion', 'nombre', 'producto', 'pieza'],
        'existencia' => ['cantidad', 'existencia', 'stock', 'piezas'],
        'precio_compra' => ['precio de compra', 'precio compra', 'costo', 'costo unitario'],
        // OJO: "PRECIO DE VENTA" del Excel de la agencia es compra + IVA, NO el precio al público.
        'precio_venta' => ['precio publico', 'precio venta publico', 'precio venta'],
        'categoria' => ['categoria'],
        'proveedor' => ['proveedor'],
        'stock_minimo' => ['stock minimo', 'minimo'],
    ];

    private const OBLIGATORIAS = ['codigo', 'nombre', 'existencia', 'precio_compra', 'precio_venta'];

    private const ETIQUETAS = [
        'codigo' => 'NO. DE PARTE',
        'nombre' => 'DESCRIPCION DE LA PIEZA',
        'existencia' => 'CANTIDAD',
        'precio_compra' => 'PRECIO DE COMPRA',
        'precio_venta' => 'PRECIO PUBLICO',
    ];

    private const MAX_BYTES_DESCOMPRIMIDOS = 60 * 1024 * 1024;

    /**
     * Lee el archivo, valida fila por fila y junta las partes repetidas.
     *
     * @return array{items: array<int, array<string, mixed>>, errores: array<int, array<string, mixed>>, ignoradas: int, filas_leidas: int}
     */
    public static function procesar(string $ruta, string $extension): array
    {
        $extension = strtolower($extension);

        $filas = match ($extension) {
            'xlsx' => self::filasDeExcel($ruta),
            'xml' => self::filasDeXml($ruta),
            default => throw new RuntimeException('Formato no soportado. Usa un archivo .xlsx o .xml.'),
        };

        return self::consolidar($filas['filas'], $filas['ignoradas']);
    }

    /* ------------------------------------------------------------------ */
    /*  Validación y consolidación                                         */
    /* ------------------------------------------------------------------ */

    /**
     * @param  array<int, array<string, mixed>>  $filas  cada fila trae 'fila' (número) y los campos crudos
     */
    private static function consolidar(array $filas, int $ignoradas): array
    {
        $items = [];
        $errores = [];

        foreach ($filas as $f) {
            $n = $f['fila'];
            $codigo = trim((string) ($f['codigo'] ?? ''));
            $nombre = trim((string) preg_replace('/\s+/u', ' ', (string) ($f['nombre'] ?? '')));
            $problemas = [];

            if ($codigo === '') {
                $problemas[] = 'Falta el número de parte.';
            } elseif (mb_strlen($codigo) > 50) {
                $problemas[] = 'El número de parte excede 50 caracteres.';
            }

            if ($nombre === '') {
                $problemas[] = 'Falta la descripción.';
            } elseif (mb_strlen($nombre) > 255) {
                $problemas[] = 'La descripción excede 255 caracteres.';
            }

            $existencia = self::numero($f['existencia'] ?? null);
            if ($existencia === null || $existencia === false) {
                $problemas[] = 'La cantidad falta o no es un número.';
            } elseif ($existencia < 0 || abs($existencia - round($existencia)) > 1e-9) {
                $problemas[] = 'La cantidad debe ser un entero de 0 en adelante.';
            }

            $compra = self::numero($f['precio_compra'] ?? null);
            if ($compra === null || $compra === false || $compra < 0) {
                $problemas[] = 'El precio de compra falta o no es válido.';
            }

            $venta = self::numero($f['precio_venta'] ?? null);
            if ($venta === null || $venta === false || $venta < 0) {
                $problemas[] = 'El precio público falta o no es válido.';
            }

            $minimo = null;
            if (isset($f['stock_minimo']) && trim((string) $f['stock_minimo']) !== '') {
                $m = self::numero($f['stock_minimo']);
                if ($m === false || $m === null || $m < 0 || abs($m - round($m)) > 1e-9) {
                    $problemas[] = 'El stock mínimo debe ser un entero de 0 en adelante.';
                } else {
                    $minimo = (int) round($m);
                }
            }

            if ($problemas) {
                $errores[] = ['fila' => $n, 'codigo' => $codigo, 'mensaje' => implode(' ', $problemas)];
                continue;
            }

            $categoria = trim((string) ($f['categoria'] ?? '')) ?: null;
            $proveedor = trim((string) ($f['proveedor'] ?? '')) ?: null;

            $nuevo = [
                'codigo' => $codigo,
                'nombre' => $nombre,
                'existencia' => (int) round($existencia),
                'precio_compra' => round($compra, 2),
                'precio_venta' => round($venta, 2),
                'categoria' => $categoria,
                'proveedor' => $proveedor,
                'stock_minimo' => $minimo,
                'filas' => [$n],
                'avisos' => [],
            ];

            if ($nuevo['precio_venta'] < $nuevo['precio_compra']) {
                $nuevo['avisos'][] = 'El precio público es menor que el precio de compra.';
            }

            if (! isset($items[$codigo])) {
                $items[$codigo] = $nuevo;
                continue;
            }

            // Número de parte repetido en el archivo: se suman las cantidades.
            $base = &$items[$codigo];
            $base['existencia'] += $nuevo['existencia'];
            $base['filas'][] = $n;

            if (self::normalizar($base['nombre']) !== self::normalizar($nuevo['nombre'])) {
                $base['avisos'][] = "Descripción distinta en la fila {$n} («{$nuevo['nombre']}»); se conservó la primera. Revisa si el número de parte es correcto.";
            }
            if (abs($base['precio_compra'] - $nuevo['precio_compra']) > 0.005 || abs($base['precio_venta'] - $nuevo['precio_venta']) > 0.005) {
                $base['avisos'][] = "Precios distintos en la fila {$n}; se conservaron los de la primera.";
            }
            $base['categoria'] ??= $nuevo['categoria'];
            $base['proveedor'] ??= $nuevo['proveedor'];
            $base['stock_minimo'] ??= $nuevo['stock_minimo'];
            unset($base);
        }

        foreach ($items as &$item) {
            if (count($item['filas']) > 1) {
                array_unshift(
                    $item['avisos'],
                    'Repetido en las filas ' . implode(', ', $item['filas']) . ': se sumaron las cantidades.'
                );
            }
        }
        unset($item);

        return [
            'items' => array_values($items),
            'errores' => $errores,
            'ignoradas' => $ignoradas,
            'filas_leidas' => count($filas) + $ignoradas,
        ];
    }

    /** @return float|null|false  null = vacío, false = no es número */
    private static function numero(mixed $v): float|null|false
    {
        if ($v === null || (is_string($v) && trim($v) === '')) {
            return null;
        }
        if (is_bool($v)) {
            return false;
        }
        if (is_int($v) || is_float($v)) {
            return (float) $v;
        }

        $t = str_replace(['$', ',', ' ', "\u{00A0}"], '', (string) $v);

        return is_numeric($t) ? (float) $t : false;
    }

    private static function normalizar(?string $t): string
    {
        $t = mb_strtolower(trim((string) $t));
        $t = strtr($t, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);

        return trim((string) preg_replace('/[^a-z0-9]+/', ' ', $t));
    }

    private static function campoDeEncabezado(string $texto): ?string
    {
        $n = self::normalizar($texto);
        if ($n === '') {
            return null;
        }
        foreach (self::ALIAS as $campo => $alias) {
            if (in_array($n, $alias, true)) {
                return $campo;
            }
        }

        return null;
    }

    /* ------------------------------------------------------------------ */
    /*  Excel (.xlsx)                                                      */
    /* ------------------------------------------------------------------ */

    /** @return array{filas: array<int, array<string, mixed>>, ignoradas: int} */
    private static function filasDeExcel(string $ruta): array
    {
        $matriz = self::matrizDeXlsx($ruta);

        // 1) Buscar la fila de encabezados (en las primeras 30 filas).
        $mapa = null;
        $filaEncabezado = null;
        $mejor = ['mapa' => [], 'fila' => null];

        $contadas = 0;
        foreach ($matriz as $numero => $celdas) {
            if (++$contadas > 30) {
                break;
            }
            $candidato = [];
            foreach ($celdas as $col => $valor) {
                $campo = self::campoDeEncabezado((string) $valor);
                if ($campo !== null && ! in_array($campo, $candidato, true)) {
                    $candidato[$col] = $campo;
                }
            }
            if (count($candidato) > count($mejor['mapa'])) {
                $mejor = ['mapa' => $candidato, 'fila' => $numero];
            }
            if (! array_diff(self::OBLIGATORIAS, array_values($candidato))) {
                $mapa = $candidato;
                $filaEncabezado = $numero;
                break;
            }
        }

        if ($mapa === null) {
            $faltan = array_diff(self::OBLIGATORIAS, array_values($mejor['mapa']));
            $nombres = implode(', ', array_map(fn ($c) => self::ETIQUETAS[$c], $faltan));
            throw new RuntimeException(
                'No encontré los encabezados del inventario en la primera hoja. Faltan estas columnas: ' . $nombres . '.'
            );
        }

        // 2) Leer los datos debajo del encabezado.
        $filas = [];
        $ignoradas = 0;

        foreach ($matriz as $numero => $celdas) {
            if ($numero <= $filaEncabezado) {
                continue;
            }

            $f = ['fila' => $numero];
            foreach ($mapa as $col => $campo) {
                $f[$campo] = $celdas[$col] ?? null;
            }

            $codigo = trim((string) ($f['codigo'] ?? ''));
            $nombre = trim((string) ($f['nombre'] ?? ''));

            // Encabezado repetido a mitad de la hoja.
            if ($codigo !== '' && self::campoDeEncabezado($codigo) === 'codigo') {
                $ignoradas++;
                continue;
            }

            // Fila vacía (sin número de parte ni descripción).
            if ($codigo === '' && $nombre === '') {
                $ignoradas++;
                continue;
            }

            $filas[] = $f;
        }

        return ['filas' => $filas, 'ignoradas' => $ignoradas];
    }

    /**
     * Devuelve la primera hoja como [número de fila => [índice de columna => valor]].
     *
     * @return array<int, array<int, mixed>>
     */
    private static function matrizDeXlsx(string $ruta): array
    {
        $zip = new ZipArchive();
        if ($zip->open($ruta) !== true) {
            throw new RuntimeException('No pude abrir el archivo. ¿Es un Excel .xlsx válido?');
        }

        try {
            $workbook = self::xmlDeZip($zip, 'xl/workbook.xml');
            if ($workbook === null) {
                throw new RuntimeException('El archivo no parece un Excel .xlsx válido.');
            }

            // Ubicar la primera hoja visible.
            $namespaces = $workbook->getNamespaces(true);
            $rid = null;
            foreach ($workbook->sheets->sheet as $hoja) {
                $estado = (string) ($hoja['state'] ?? 'visible');
                if ($estado !== 'visible') {
                    continue;
                }
                $atributos = isset($namespaces['r']) ? $hoja->attributes($namespaces['r']) : null;
                $rid = $atributos ? (string) $atributos['id'] : null;
                break;
            }

            $rutaHoja = 'xl/worksheets/sheet1.xml';
            if ($rid) {
                $rels = self::xmlDeZip($zip, 'xl/_rels/workbook.xml.rels');
                if ($rels !== null) {
                    foreach ($rels->Relationship as $rel) {
                        if ((string) $rel['Id'] === $rid) {
                            $destino = (string) $rel['Target'];
                            $rutaHoja = str_starts_with($destino, '/') ? ltrim($destino, '/') : 'xl/' . $destino;
                            break;
                        }
                    }
                }
            }

            // Textos compartidos.
            $textos = [];
            $compartidos = self::xmlDeZip($zip, 'xl/sharedStrings.xml');
            if ($compartidos !== null) {
                foreach ($compartidos->si as $si) {
                    $textos[] = self::textoDe($si);
                }
            }

            $hojaXml = self::xmlDeZip($zip, $rutaHoja);
            if ($hojaXml === null) {
                throw new RuntimeException('No pude leer la primera hoja del Excel.');
            }

            $matriz = [];
            foreach ($hojaXml->sheetData->row as $row) {
                $numero = (int) $row['r'];
                $celdas = [];

                foreach ($row->c as $c) {
                    $referencia = (string) $c['r'];
                    $col = self::indiceColumna($referencia);
                    $tipo = (string) $c['t'];

                    if ($tipo === 's') {
                        $valor = $textos[(int) $c->v] ?? null;
                    } elseif ($tipo === 'inlineStr') {
                        $valor = self::textoDe($c->is);
                    } elseif ($tipo === 'b') {
                        $valor = ((string) $c->v) === '1';
                    } elseif ($tipo === 'e') {
                        $valor = null;
                    } elseif ($tipo === 'str') {
                        $valor = (string) $c->v;
                    } else {
                        $v = (string) $c->v;
                        $valor = $v === '' ? null : (is_numeric($v) ? $v + 0 : $v);
                    }

                    $celdas[$col] = $valor;
                }

                $matriz[$numero] = $celdas;
            }

            ksort($matriz);

            return $matriz;
        } finally {
            $zip->close();
        }
    }

    private static function xmlDeZip(ZipArchive $zip, string $nombre): ?\SimpleXMLElement
    {
        $info = $zip->statName($nombre);
        if ($info === false) {
            return null;
        }
        if ($info['size'] > self::MAX_BYTES_DESCOMPRIMIDOS) {
            throw new RuntimeException('El archivo es demasiado grande para procesarlo.');
        }

        $contenido = $zip->getFromName($nombre);
        if ($contenido === false) {
            return null;
        }

        return self::cargarXml($contenido);
    }

    private static function textoDe(?\SimpleXMLElement $nodo): string
    {
        if ($nodo === null) {
            return '';
        }
        if (isset($nodo->t)) {
            return (string) $nodo->t;
        }

        $texto = '';
        foreach ($nodo->r as $r) {
            $texto .= (string) $r->t;
        }

        return $texto;
    }

    /** "AB12" → 27 (A=0). */
    private static function indiceColumna(string $referencia): int
    {
        preg_match('/^[A-Z]+/', strtoupper($referencia), $m);
        $letras = $m[0] ?? 'A';
        $n = 0;
        foreach (str_split($letras) as $letra) {
            $n = $n * 26 + (ord($letra) - 64);
        }

        return $n - 1;
    }

    /* ------------------------------------------------------------------ */
    /*  XML                                                                */
    /* ------------------------------------------------------------------ */

    /** @return array{filas: array<int, array<string, mixed>>, ignoradas: int} */
    private static function filasDeXml(string $ruta): array
    {
        $contenido = file_get_contents($ruta);
        if ($contenido === false || trim($contenido) === '') {
            throw new RuntimeException('El archivo XML está vacío.');
        }

        $xml = self::cargarXml($contenido);
        if ($xml === null) {
            throw new RuntimeException('El XML no es válido o está mal formado.');
        }

        $filas = [];
        $n = 0;

        foreach ($xml->children() as $producto) {
            $n++;
            $f = ['fila' => $n];

            foreach ($producto->children() as $campo) {
                $destino = self::campoDeEncabezado($campo->getName());
                if ($destino !== null) {
                    $f[$destino] = trim((string) $campo);
                }
            }

            $filas[] = $f;
        }

        if (! $filas) {
            throw new RuntimeException('No encontré productos en el XML. Cada producto debe ir dentro de un elemento <producto>.');
        }

        return ['filas' => $filas, 'ignoradas' => 0];
    }

    /** Carga XML sin permitir DTD ni entidades externas. */
    private static function cargarXml(string $contenido): ?\SimpleXMLElement
    {
        if (preg_match('/<!(DOCTYPE|ENTITY)/i', $contenido)) {
            throw new RuntimeException('El archivo contiene declaraciones XML no permitidas.');
        }

        $previo = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($contenido, \SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOCDATA);
        libxml_clear_errors();
        libxml_use_internal_errors($previo);

        return $xml === false ? null : $xml;
    }

    public static function ejemploXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<inventario>
    <producto>
        <codigo>JG541041</codigo>
        <nombre>JUNTA DE CARTER CENTRAL</nombre>
        <existencia>5</existencia>
        <precio_compra>139.75</precio_compra>
        <precio_venta>275</precio_venta>
        <!-- Opcionales: categoria, proveedor, stock_minimo -->
        <categoria>Motor</categoria>
        <proveedor>Bajaj Auto</proveedor>
        <stock_minimo>2</stock_minimo>
    </producto>
    <producto>
        <codigo>JR161210</codigo>
        <nombre>CABLE DE ACELERADOR</nombre>
        <existencia>5</existencia>
        <precio_compra>96.66</precio_compra>
        <precio_venta>200</precio_venta>
    </producto>
</inventario>
XML;
    }
}