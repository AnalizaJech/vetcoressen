<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Prescripción médica vinculada a historia clínica
class Prescription extends Model
{
    protected $table = 'prescriptions';

    protected $fillable = [
        'clinic_id', 'medical_record_id', 'product_id',
        'medicamento', 'dosage', 'frequency', 'duration',
        'via_administracion', 'duracion_dias', 'indicaciones',
        'cantidad_dispensada', 'dispensado',
    ];

    protected function casts(): array
    {
        return ['dispensado' => 'boolean'];
    }

    public function clinica(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function historiaClinica(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
