<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

// Historia clínica con triaje obligatorio
class MedicalRecord extends Model
{
    use SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'medical_records';

    protected $fillable = [
        'clinic_id', 'pet_id', 'veterinarian_id', 'appointment_id',
        'date', 'reason',
        'weight', 'temperature', 'heart_rate', 'respiratory_rate',
        // Examen físico general
        'examen_mucosas', 'examen_linfonodos', 'condicion_corporal',
        'nivel_dolor', 'nivel_hidratacion',
        // Examen por sistemas
        'examen_piel_pelaje', 'examen_ojos_oidos', 'examen_cardiovascular',
        'examen_respiratorio', 'examen_digestivo', 'examen_musculoesqueletico',
        'examen_neurologico', 'examen_urinario',
        // Diagnóstico y tratamiento
        'anamnesis', 'diagnostico_presuntivo', 'tratamiento_indicaciones',
        'proxima_cita_recomendada',
        'notas_aclaratorias',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'weight' => 'decimal:2',
            'temperature' => 'decimal:1',
            'proxima_cita_recomendada' => 'date',
        ];
    }

    public function clinica(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function mascota(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function veterinario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'veterinarian_id');
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function prescripciones(): HasMany
    {
        return $this->hasMany(Prescription::class, 'medical_record_id');
    }
}
