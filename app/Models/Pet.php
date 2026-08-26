<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Pet vinculada a un cliente
class Pet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'clinic_id', 'customer_id', 'name', 'species_id', 'raza_id',
        'gender', 'color', 'birth_date', 'current_weight',
        'foto', 'esterilizado', 'fallecido', 'medical_notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'current_weight' => 'decimal:2',
            'esterilizado' => 'boolean',
            'fallecido' => 'boolean',
        ];
    }

    public function getSexoTextoAttribute(): string
    {
        return match ($this->gender) {
            'M', 'Macho' => 'Macho',
            'H', 'Hembra' => 'Hembra',
            default => $this->gender ?? 'Desconocido',
        };
    }

    public function getWeightAttribute()
    {
        return $this->current_weight;
    }

    public function getEdadTextoAttribute(): string
    {
        if (!$this->birth_date) {
            return 'Edad no especificada';
        }
        $diff = \Carbon\Carbon::parse($this->birth_date)->diff(now());
        if ($diff->y > 0) {
            return $diff->y . ' ' . ($diff->y === 1 ? 'año' : 'años');
        } elseif ($diff->m > 0) {
            return $diff->m . ' ' . ($diff->m === 1 ? 'mes' : 'meses');
        } else {
            return $diff->d . ' ' . ($diff->d === 1 ? 'día' : 'días');
        }
    }

    public function clinica(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function historiasClinicas(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function especie(): BelongsTo
    {
        return $this->belongsTo(Species::class, 'species_id');
    }

    public function species(): BelongsTo
    {
        return $this->especie();
    }

    public function raza(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }
}
