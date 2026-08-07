<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'branch_id',
        'tipo_documento',
        'numero_documento',
        'name',
        'last_name',
        'email',
        'password',
        'phone',
        'dni',
        'address',
        'country',
        'state',
        'city',
        'notes',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ── Relaciones ──

    public function clinica(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // ── Helpers de rol ──

    public function esSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function esVeterinario(): bool
    {
        return $this->hasRole('veterinario');
    }

    public function esRecepcionista(): bool
    {
        return $this->hasRole('recepcionista');
    }
}
