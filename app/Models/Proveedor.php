<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores'; // <-- AGREGA ESTO
    protected $fillable = ['nombre', 'estado'];
}