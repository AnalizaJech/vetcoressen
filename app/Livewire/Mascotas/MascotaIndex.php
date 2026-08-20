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
    public string $filtroMascota = '';

    #[Url]
    public string $filtroCliente = '';

    // ID de la mascota pendiente de eliminar (modal de confirmación)
    public ?int $mascotaEliminarId = null;
    public ?Pet $mascotaVer = null;

    public function updatedFiltroMascota(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroCliente(): void
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
            ->when($this->filtroMascota, function ($q) {
                $q->where('name', $this->filtroMascota);
            })
            ->when($this->filtroCliente, function ($q) {
                $q->where('customer_id', $this->filtroCliente);
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        $mascotasOptions = [['value' => '', 'label' => 'filter.allPets']];
        $nombresMascotas = Pet::whereNotNull('name')
            ->where('name', '!=', '')
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        foreach ($nombresMascotas as $nombre) {
            $mascotasOptions[] = ['value' => $nombre, 'label' => $nombre];
        }

        $clientesOptions = [['value' => '', 'label' => 'filter.allClients']];
        foreach (Customer::orderBy('first_name')->get() as $c) {
            $clientesOptions[] = ['value' => (string)$c->id, 'label' => $c->nombre_completo];
        }

        return view('livewire.mascotas.mascota-index', compact('mascotas', 'mascotasOptions', 'clientesOptions'));
    }
}
