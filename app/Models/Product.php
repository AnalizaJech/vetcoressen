<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Product o servicio del inventario
class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'clinic_id', 'type', 'principio_activo', 'presentacion', 'weight', 'requiere_receta',
        'categoria', 'name', 'codigo_barras',
        'precio_final', 'base_imponible', 'igv_monto', 'tipo_afectacion_igv', 'margen_ganancia',
        'current_stock', 'minimum_stock', 'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'precio_final' => 'decimal:2',
            'base_imponible' => 'decimal:2',
            'igv_monto' => 'decimal:2',
            'margen_ganancia' => 'decimal:2',
            'requiere_receta' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // Alerta: stock actual <= stock mínimo
    public function getStockBajoAttribute(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function clinica(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function kardexMovimientos()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function productBatches()
    {
        return $this->hasMany(ProductBatch::class);
    }
}
