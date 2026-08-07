<?php

namespace App\Livewire\Proveedores;

use App\Models\Supplier;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Proveedores')]
class ProveedorIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $busqueda = '';

    public ?int $proveedorEliminarId = null;
    public ?Supplier $proveedorVer = null;

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function confirmDeletion(int $id): void
    {
        $this->proveedorEliminarId = $id;
        Flux::modal('confirmar-eliminacion')->show();
    }

    public function ver(int $id): void
    {
        $this->proveedorVer = Supplier::findOrFail($id);
    }

    public function eliminar(): void
    {
        if ($this->proveedorEliminarId) {
            $proveedor = Supplier::find($this->proveedorEliminarId);
            if ($proveedor) {
                // Verificar si tiene productos asociados (lógica pendiente)
                $proveedor->delete();
                session()->flash('mensaje', 'Proveedor eliminado exitosamente.');
            }
        }
        
        $this->proveedorEliminarId = null;
        Flux::modal('confirmar-eliminacion')->close();
    }

    public function render()
    {
        $proveedores = Supplier::query()
            ->when($this->busqueda, function ($query) {
                $query->where('name', 'like', '%' . $this->busqueda . '%')
                      ->orWhere('ruc', 'like', '%' . $this->busqueda . '%')
                      ->orWhere('phone', 'like', '%' . $this->busqueda . '%')
                      ->orWhere('email', 'like', '%' . $this->busqueda . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.proveedores.proveedor-index', [
            'proveedores' => $proveedores
        ]);
    }
}
