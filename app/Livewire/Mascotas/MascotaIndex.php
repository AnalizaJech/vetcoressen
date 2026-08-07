<?php

namespace App\Livewire\Mascotas;

use App\Models\Customer;
use App\Models\Pet;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

// Listado de mascotas con filtro por cliente
#[Layout('components.layouts.app')]
#[Title('Mascotas')]
class MascotaIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $busqueda = '';

    // ID de la mascota pendiente de eliminar (modal de confirmación)
    public ?int $mascotaEliminarId = null;
    public ?Pet $mascotaVer = null;

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function ver(int $id): void
    {
        $this->mascotaVer = Pet::with(['cliente', 'especie', 'raza'])->findOrFail($id);
        /** @phpstan-ignore-next-line */
        \Flux::modal('ver-mascota')->show();
    }

    public function eliminar(): void
    {
        if (!$this->mascotaEliminarId) return;
        
        $mascota = Pet::findOrFail($this->mascotaEliminarId);
        $mascota->delete();
        session()->flash('mensaje', "Mascota «{$mascota->name}» eliminada correctamente.");
        $this->mascotaEliminarId = null;
    }

    public function render()
    {
        $mascotas = Pet::with(['cliente', 'clinica', 'especie', 'raza'])
            ->when($this->busqueda, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->busqueda}%")
                        ->orWhereHas('especie', function ($e) {
                            $e->where('name', 'like', "%{$this->busqueda}%");
                        })
                        ->orWhereHas('raza', function ($r) {
                            $r->where('name', 'like', "%{$this->busqueda}%");
                        })
                        ->orWhereHas('cliente', function ($c) {
                            $c->where('first_name', 'like', "%{$this->busqueda}%")
                              ->orWhere('last_name', 'like', "%{$this->busqueda}%");
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.mascotas.mascota-index', [
            'mascotas' => $mascotas,
        ]);
    }
}
