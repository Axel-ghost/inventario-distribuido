<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'folio',
        'solicitante_id',
        'revisor_id',
        'estado',
        'comentario_admin',
        'fecha_solicitud',
        'fecha_resolucion',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_resolucion' => 'datetime',
    ];

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisor_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }
}