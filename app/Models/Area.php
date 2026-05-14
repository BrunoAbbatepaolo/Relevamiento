<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    // Indicamos que use la conexión externa definida en config/database.php
    protected $connection = 'external_db';
    protected $table = 'admin.areas';
    public $timestamps = false; // Normalmente estas tablas externas no usan los timestamps de Laravel

    public function relevamientos(): HasMany
    {
        // Relación con tu tabla local
        return $this->hasMany(Relevamiento::class, 'area_id');
    }
}
