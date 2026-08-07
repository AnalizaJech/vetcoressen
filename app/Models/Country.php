<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo para países - tabla importada del repositorio country-city-sql-database.
 * Se usa para el selector de ubicación País → Ciudad en formularios de clientes.
 */
class Country extends Model
{
    // La tabla no tiene timestamps
    public $timestamps = false;

    protected $fillable = [
        'country',
        'latitude',
        'longitude',
    ];

    /**
     * Ciudades pertenecientes a este país
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * Accessor para mostrar el nombre del país
     */
    public function getNombreAttribute(): string
    {
        return $this->country;
    }
}
