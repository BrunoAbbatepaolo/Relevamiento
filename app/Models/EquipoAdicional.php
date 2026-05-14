<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipoAdicional extends Model
{
    protected $table = 'equipos_adicionales';
    protected $guarded = ['id'];
    protected $casts = [
        'camaras_vigilancia_ids' => 'array',
        'otros_dispositivos' => 'array',
    ];

    public function relevamiento()
    {
        return $this->belongsTo(Relevamiento::class);
    }
}
