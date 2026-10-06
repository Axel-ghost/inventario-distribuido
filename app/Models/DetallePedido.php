<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedido';

    protected $fillable = [
        'id_pedido',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id');
    }
}