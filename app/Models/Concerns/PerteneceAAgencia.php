<?php

namespace App\Models\Concerns;

use App\Models\Agencia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait PerteneceAAgencia
{
    protected static function bootPerteneceAAgencia(): void
    {
        static::addGlobalScope('agencia', function (Builder $query) {
            $agenciaId = Auth::user()?->agencia_id;

            if ($agenciaId) {
                $query->where($query->getModel()->getTable() . '.agencia_id', $agenciaId);
            }
        });

        static::creating(function ($modelo) {
            if (empty($modelo->agencia_id)) {
                $modelo->agencia_id = Auth::user()?->agencia_id;
            }
        });
    }

    public function agencia()
    {
        return $this->belongsTo(Agencia::class);
    }
}