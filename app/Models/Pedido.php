<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'folio',
        'id_cliente',
        'estado',
        'subtotal',
        'impuesto',
        'total',
        'id_usuario',
    ];

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido', 'id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id');
    }

    public function venta()
    {
        return $this->hasOne(Venta::class, 'id_pedido', 'id');
    }
}