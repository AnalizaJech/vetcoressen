<?php

namespace App\Livewire\Inventario;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

// Listado de productos del inventario con alertas de stock
#[Layout('components.layouts.app')]
#[Title('Inventario')]
class ProductoIndex extends Component
{
    use WithPagination;

    #[Url]
    public ?string $filtroProducto = '';

    #[Url]
    public $filtroStock = '';
    public $filtroTipo = '';

    public bool $soloStockBajo = false;

    public ?int $productoEliminarId = null;
    public ?Product $productoVer = null;

    public function updatedFiltroProducto(): void { $this->resetPage(); }
    public function updatedFiltroTipo(): void { $this->resetPage(); }
    public function updatedSoloStockBajo(): void { $this->resetPage(); }

    public function ver(int $id): void
    {
        $this->productoVer = Product::findOrFail($id);
    }

    public function confirmDeletion(int $id): void
    {
        $this->productoEliminarId = $id;
    }

    public function eliminar(): void
    {
        if (!$this->productoEliminarId) return;
        
        try {
            $producto = Product::findOrFail($this->productoEliminarId);
            $producto->delete();
            session()->flash('mensaje', "alert.product_deleted");
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('mensaje', "No se puede eliminar el producto porque tiene registros asociados (lotes, ventas, etc).");
        } catch (\Exception $e) {
            session()->flash('mensaje', "Ocurrió un error al eliminar el producto.");
        }
    }

    public function render()
    {
        $productos = Product::with('productBatches')
            ->when($this->filtroProducto, fn ($q) =>
                $q->where('id', $this->filtroProducto)
            )
            ->when($this->filtroTipo, fn ($q, $filtro) => $q->where('type', strtoupper($filtro)))
            ->when($this->soloStockBajo, fn ($q) =>
                $q->whereColumn('current_stock', '<=', 'minimum_stock')
            )
            ->orderBy('name')
            ->paginate(20);

        $productosOptions = [['value' => '', 'label' => 'filter.allProducts']];
        foreach (Product::orderBy('name')->get() as $p) {
            $productosOptions[] = ['value' => (string)$p->id, 'label' => $p->name];
        }

        return view('livewire.inventario.producto-index', compact('productos', 'productosOptions'));
    }
}
