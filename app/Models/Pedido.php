<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'folio',
        'id_proveedor',
        'id_cliente',
        'estado',
        'subtotal',
        'impuesto',
        'total',
        'id_usuario',
    ];

    public function detalles()
    {
        return $this->hasMany(PedidoDetalle::class, 'id_pedido', 'id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
}