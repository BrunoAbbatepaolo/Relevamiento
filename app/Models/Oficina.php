<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Oficina extends Model
{
    protected $connection = 'external_db';
    protected $table = 'admin.oficinas';
    public $timestamps = false;

    public function relevamientos(): HasMany
    {
        return $this->hasMany(Relevamiento::class, 'oficina_id');
    }
}
