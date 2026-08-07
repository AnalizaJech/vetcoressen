<?php

namespace App\Livewire\Inventario;

use App\Models\Product;
use App\Models\Supplier;
use App\Services\InventoryService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Recepcionar Pedido')]
class ProductEntryForm extends Component
{
    public $producto_id = '';
    public $supplier_id = '';
    public $lote = '';
    public $fecha_vencimiento = '';
    public $costo_unitario = '';
    public $precio_venta = '';
    public $cantidad = '';

    protected $rules = [
        'producto_id' => 'required|exists:products,id',
        'supplier_id' => 'nullable|exists:suppliers,id',
        'cantidad' => 'required|integer|min:1',
        'costo_unitario' => 'required|numeric|min:0',
        'precio_venta' => 'nullable|numeric|min:0',
    ];

    public function updatedProductoId()
    {
        $producto = Product::find($this->producto_id);
        if ($producto) {
            $this->precio_venta = $producto->precio_final;
        }
    }

    public function guardar(InventoryService $inventoryService)
    {
        // Proveedor: si es un nombre nuevo, crearlo al vuelo
        if (!empty($this->supplier_id) && !is_numeric($this->supplier_id)) {
            $prov = Supplier::firstOrCreate(['name' => $this->supplier_id]);
            $this->supplier_id = $prov->id;
        }

        $this->validate();

        $producto = Product::find($this->producto_id);
        
        if (in_array($producto->type, ['Medicamento', 'Alimento'])) {
            $this->validate([
                'lote' => 'required|string|max:50',
                'fecha_vencimiento' => 'required|date|after:today',
            ]);
        }

        $inventoryService->registrarEntrada(
            $this->producto_id,
            $this->cantidad,
            $this->costo_unitario,
            $this->lote ?: null,
            $this->fecha_vencimiento ?: null,
            $this->supplier_id ?: null
        );

        // Actualizar el precio de venta si cambió
        if ($this->precio_venta && $this->precio_venta != $producto->precio_final) {
            $pf = (float) $this->precio_venta;
            
            if ($producto->tipo_afectacion_igv === 'Gravado') {
                $base_imponible = $pf / 1.18;
                $igv_monto = $pf - $base_imponible;
            } else {
                $base_imponible = $pf;
                $igv_monto = 0;
            }

            $producto->update([
                'precio_final' => $pf,
                'base_imponible' => $base_imponible,
                'igv_monto' => $igv_monto
            ]);
        }

        session()->flash('mensaje', 'alert.entry_created');
        
        $this->redirect(route('inventario.index'), navigate: true);
    }

    public function render()
    {
        $productos = Product::where('type', '!=', 'Servicio')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $proveedores = Supplier::orderBy('name')->get();

        return view('livewire.inventario.product-entry-form', [
            'productos' => $productos,
            'proveedores' => $proveedores,
        ]);
    }
}
