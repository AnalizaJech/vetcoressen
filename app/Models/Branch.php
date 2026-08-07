<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Sucursal / sede de una clínica
class Branch extends Model
{
    use SoftDeletes;

    protected $table = 'branches';

    protected $fillable = [
        'clinic_id', 'name', 'ruc', 'address', 'phone',
        'email', 'codigo_ubigeo', 'principal', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'principal' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function clinica(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
