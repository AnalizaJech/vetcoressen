<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    use HasFactory;

    protected $table = 'email_logs';

    protected $fillable = [
        'customer_id',
        'tipo_notificacion',
        'correo_destino',
        'status',
        'error_mensaje',
        'fecha_envio',
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
    ];

    /**
     * Get the cliente that owns the EmailLog.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
