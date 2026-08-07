<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Sale / comprobante de pago
class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'clinic_id', 'customer_id', 'cajero_id',
        'tipo_comprobante', 'subtotal', 'igv', 'total',
        'payment_method',
        'status',
        'notes',
        'nubefact_enlace_pdf',
        'nubefact_enlace_xml',
        'nubefact_enlace_cdr',
        'nubefact_sunat_ticket_numero',
        'nubefact_error',
        'cash_register_id',
        'parent_sale_id',
        'is_credit_note',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'igv' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function clinica(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function cajero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cajero_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }
}
