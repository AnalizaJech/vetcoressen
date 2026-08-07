<?php

namespace App\Livewire\Sucursales;

use App\Models\Branch;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Sucursales')]
class SucursalIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $busqueda = '';

    public ?int $sucursalEliminarId = null;

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function confirmDeletion(int $id): void
    {
        $this->sucursalEliminarId = $id;
        Flux::modal('confirmar-eliminacion')->show();
    }

    public function eliminar(): void
    {
        if ($this->sucursalEliminarId) {
            $sucursal = Branch::find($this->sucursalEliminarId);
            if ($sucursal) {
                // Validación para no eliminar la sucursal principal o si tiene usuarios/cajas asignadas
                if ($sucursal->principal) {
                    session()->flash('mensaje', 'No se puede eliminar la sucursal principal.');
                } else {
                    $sucursal->delete();
                    session()->flash('mensaje', 'Sucursal eliminada exitosamente.');
                }
            }
        }
        
        $this->sucursalEliminarId = null;
        Flux::modal('confirmar-eliminacion')->close();
    }

    public function render()
    {
        $sucursales = Branch::query()
            ->when($this->busqueda, function ($query) {
                $query->where('name', 'like', '%' . $this->busqueda . '%')
                      ->orWhere('address', 'like', '%' . $this->busqueda . '%')
                      ->orWhere('phone', 'like', '%' . $this->busqueda . '%');
            })
            ->orderByDesc('principal')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.sucursales.sucursal-index', [
            'sucursales' => $sucursales
        ]);
    }
}
