<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryService
{
    /**
     * Registra el ingreso de un nuevo lote de producto.
     */
    public function registrarEntrada(
        int $productId,
        int $quantity,
        float $costoUnitario,
        ?string $lote = null,
        ?string $fechaVencimiento = null,
        ?int $supplierId = null
    ): ProductBatch {
        return DB::transaction(function () use ($productId, $quantity, $costoUnitario, $lote, $fechaVencimiento, $supplierId) {
            $product = Product::findOrFail($productId);
            
            $batch = ProductBatch::create([
                'product_id' => $productId,
                'supplier_id' => $supplierId,
                'lote' => $lote,
                'fecha_vencimiento' => $fechaVencimiento,
                'costo_unitario' => $costoUnitario,
                'stock_inicial' => $quantity,
                'stock_actual' => $quantity,
            ]);

            $stockAnterior = $product->current_stock;
            $product->increment('current_stock', $quantity);

            InventoryMovement::create([
                'clinic_id' => 1,
                'product_id' => $productId,
                'product_batch_id' => $batch->id,
                'user_id' => Auth::id() ?? 1,
                'type' => 'ENTRADA_COMPRA',
                'quantity' => $quantity,
                'costo_unitario' => $costoUnitario,
                'reference_document' => 'COMPRA',
                'stock_anterior' => $stockAnterior,
                'stock_posterior' => $stockAnterior + $quantity,
                'notes' => 'Ingreso de nuevo lote',
            ]);

            return $batch;
        });
    }

    /**
     * Registra la salida de un producto descontando de los lotes usando FIFO.
     */
    public function registrarSalida(
        int $productId,
        int $quantityToDeduct,
        string $referenceDocument = 'VENTA',
        ?string $notes = 'Venta de producto'
    ): void {
        DB::transaction(function () use ($productId, $quantityToDeduct, $referenceDocument, $notes) {
            $product = Product::findOrFail($productId);
            
            if ($product->current_stock < $quantityToDeduct) {
                throw new \Exception("Stock insuficiente para el producto: {$product->name}");
            }

            $remainingToDeduct = $quantityToDeduct;
            $stockAnteriorProduct = $product->current_stock;

            // Obtener lotes disponibles ordenados por fecha de vencimiento (más próximos a vencer primero) o id (más antiguos primero)
            $batches = ProductBatch::where('product_id', $productId)
                ->where('stock_actual', '>', 0)
                ->orderByRaw('fecha_vencimiento IS NULL, fecha_vencimiento ASC')
                ->orderBy('created_at', 'ASC')
                ->get();

            foreach ($batches as $batch) {
                if ($remainingToDeduct <= 0) {
                    break;
                }

                $deductFromBatch = min($batch->stock_actual, $remainingToDeduct);
                
                $batch->decrement('stock_actual', $deductFromBatch);
                
                // Registrar movimiento por este lote
                InventoryMovement::create([
                    'clinic_id' => 1,
                    'product_id' => $productId,
                    'product_batch_id' => $batch->id,
                    'user_id' => Auth::id() ?? 1,
                    'type' => 'SALIDA_VENTA',
                    'quantity' => -$deductFromBatch,
                    'costo_unitario' => $batch->costo_unitario, // Costo real del lote!
                    'reference_document' => $referenceDocument,
                    'stock_anterior' => $stockAnteriorProduct,
                    'stock_posterior' => $stockAnteriorProduct - $deductFromBatch,
                    'notes' => $notes . " (Lote: {$batch->lote})",
                ]);

                $stockAnteriorProduct -= $deductFromBatch;
                $remainingToDeduct -= $deductFromBatch;
            }
            
            // Actualizar stock total del producto
            $product->decrement('current_stock', $quantityToDeduct);
        });
    }
}
