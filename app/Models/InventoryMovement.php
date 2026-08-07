<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Movimiento de inventario inmutable (Kardex)
class InventoryMovement extends Model
{
    protected $table = 'inventory_movements';

    protected $fillable = [
        'clinic_id', 'product_id', 'user_id',
        'type', 'quantity', 'costo_unitario', 'product_batch_id',
        'reference_document', 'stock_anterior', 'stock_posterior',
        'referencia_tipo', 'referencia_id', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'costo_unitario' => 'decimal:2',
            'fecha_vencimiento' => 'date',
        ];
    }

    public function clinica(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productBatch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
