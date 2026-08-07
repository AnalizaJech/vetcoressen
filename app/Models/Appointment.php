<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

// Appointment veterinaria con estados y notificaciones
class Appointment extends Model
{
    use SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'clinic_id', 'customer_id', 'pet_id', 'veterinarian_id',
        'fecha_hora', 'reason', 'status', 'notes',
        'notificado_sms', 'notificado_whatsapp', 'notificado_email',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
            'notificado_sms' => 'boolean',
            'notificado_whatsapp' => 'boolean',
            'notificado_email' => 'boolean',
        ];
    }

    public function clinica(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function mascota(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function veterinario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'veterinarian_id');
    }
}
