<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Línea de detalle de una venta
class SaleDetail extends Model
{
    protected $table = 'sale_details';

    protected $fillable = [
        'sale_id', 'product_id', 'description',
        'quantity', 'precio_final_unitario', 'base_imponible', 'igv_monto', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'precio_final_unitario' => 'decimal:2',
            'base_imponible' => 'decimal:2',
            'igv_monto' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
