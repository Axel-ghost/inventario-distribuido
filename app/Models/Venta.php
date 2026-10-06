<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'id_pedido',
        'fecha',
        'total',
        'metodo_pago',
        'estado',
        'id_usuario',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id');
    }
    public function usuario()
    {
    return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
}