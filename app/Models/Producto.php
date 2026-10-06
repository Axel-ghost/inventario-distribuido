<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'id_categoria',       
        'id_proveedor',       
        'precio_compra',
        'precio_venta',
        'existencia',
        'stock_minimo',
        'estado',           
    ];

    protected $casts = [
        'estado' => 'boolean',  
    ];

    public function categoria()
{
    return $this->belongsTo(Categoria::class, 'id_categoria', 'id');
}

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }
}