<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = ['folio', 'usuario_id', 'total', 'fecha_venta'];

    protected $casts = ['fecha_venta' => 'datetime'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }
}