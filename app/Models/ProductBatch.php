<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'lote',
        'fecha_vencimiento',
        'costo_unitario',
        'precio_venta',
        'stock_inicial',
        'stock_actual'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'costo_unitario' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock_inicial' => 'integer',
        'stock_actual' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
