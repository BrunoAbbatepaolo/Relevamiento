<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Relevamiento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'area_id',
        'oficina_id',
        'responsable_nombre',
        'estado',
        'user_id',
        'tiene_impresora',
        'impresoras',
        'tiene_modem',
        'modems',
    ];

    protected $casts = [
        'tiene_impresora' => 'boolean',
        'impresoras' => 'array',
        'tiene_modem' => 'boolean',
        'modems' => 'array',
    ];

    // Relaciones con los modelos externos
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
    public function oficina(): BelongsTo
    {
        return $this->belongsTo(Oficina::class);
    }

    // Relaciones locales
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function servidor(): HasOne
    {
        return $this->hasOne(Servidor::class);
    }
    public function activos(): HasMany
    {
        return $this->hasMany(Activo::class);
    }
    public function equiposAdicionales(): HasOne
    {
        return $this->hasOne(EquipoAdicional::class);
    }

    // Método para exportar a JSON (usado en el Wizard)
    public function toExportArray(): array
    {
        return [
            'datos_generales' => [
                'area' => $this->area?->nombre,
                'oficina' => $this->oficina?->nombre,
                'responsable' => $this->responsable_nombre,
            ],
            'servidor' => $this->servidor?->toExportArray(),
            'activos' => $this->activos->map->toExportArray()->toArray(),
            'equipos_adicionales' => $this->equiposAdicionales?->toExportArray(),
        ];
    }
}
