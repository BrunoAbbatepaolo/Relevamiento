<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servidor extends Model
{
    protected $table = 'servidores';
    protected $guarded = ['id']; // Permite asignación masiva de todo menos el ID
    protected $casts = [
        'tiene_servidor' => 'boolean',
        'almacenamiento_discos' => 'array',
    ];

    public function relevamiento()
    {
        return $this->belongsTo(Relevamiento::class);
    }

    public function toExportArray(): array
    {
        return $this->toArray(); // O personalizar según el formato JSON deseado
    }
}
