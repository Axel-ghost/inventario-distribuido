<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioMovimiento extends Model
{
    protected $table = 'inventario_movimiento';

    protected $fillable = [
        'id_producto',
        'tipo',
        'cantidad',
        'motivo',
        'id_usuario',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id');
    }
}