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
        'clinic_id', 'name', 'ruc', 'address', 'country', 'state', 'city', 'phone',
        'email', 'codigo_ubigeo', 'principal', 'is_main', 'is_active',
    ];

    protected $appends = ['is_main'];

    protected function casts(): array
    {
        return [
            'principal' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getIsMainAttribute(): bool
    {
        return (bool) ($this->attributes['principal'] ?? false);
    }

    public function setIsMainAttribute($value): void
    {
        $this->attributes['principal'] = (bool) $value;
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
