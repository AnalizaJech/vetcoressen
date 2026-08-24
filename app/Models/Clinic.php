<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Clínica veterinaria - tenant principal del sistema
class Clinic extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'ruc', 'razon_social', 'address',
        'phone', 'email', 'logo', 'sitio_web', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function getSimboloMonedaAttribute(): string
    {
        return ($this->moneda_principal ?? 'PEN') === 'USD' ? '$' : 'S/';
    }

    public function sucursales(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function clientes(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function mascotas(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
