<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activo extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'software_instalado' => 'array', // Crucial para manejar el JSON de software
        'tiene_camara' => 'boolean',
        'tiene_parlantes' => 'boolean',
        'tiene_estabilizador' => 'boolean',
        'almacenamiento_discos' => 'array',
        'monitores' => 'array',
        'tiene_mouse' => 'boolean',
        'tiene_teclado' => 'boolean',
    ];

    public function relevamiento()
    {
        return $this->belongsTo(Relevamiento::class);
    }

    public function toExportArray(): array
    {
        return $this->toArray();
    }
}
