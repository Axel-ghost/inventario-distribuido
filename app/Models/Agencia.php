<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agencia extends Model
{
    protected $fillable = ['nombre', 'ciudad', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }
}