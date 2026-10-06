<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';   
    protected $primaryKey = 'id';  

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'telefono',
        'direccion',
        'rfc',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_cliente', 'id');
    }
}