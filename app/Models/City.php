<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para ciudades - tabla importada del repositorio country-city-sql-database.
 * Se usa para el selector de ubicación País → Ciudad en formularios de clientes.
 */
class City extends Model
{
    // La tabla no tiene timestamps
    public $timestamps = false;

    protected $fillable = [
        'country_id',
        'city',
        'latitude',
        'longitude',
        'population',
    ];

    /**
     * País al que pertenece esta ciudad
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Accessor para mostrar el nombre de la ciudad
     */
    public function getNombreAttribute(): string
    {
        return $this->city;
    }
}
