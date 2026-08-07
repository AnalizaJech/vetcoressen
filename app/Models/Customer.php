<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Customer de la clínica (persona o empresa)
class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'clinic_id', 'tipo_documento', 'numero_documento',
        'first_name', 'last_name', 'email', 'phone',
        'country',
        'state',
        'city',
        'address',
        'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    // Nombre completo para mostrar en la UI
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function clinica(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }



    public function mascotas(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
