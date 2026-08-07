<?php

namespace App\Livewire\HistoriasClinicas;

use App\Models\MedicalRecord;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

// Listado de historias clínicas con búsqueda por mascota o cliente
#[Layout('components.layouts.app')]
#[Title('Historias Clínicas')]
class HistoriaClinicaIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $busqueda = '';

    // ID pendiente de eliminar (modal de confirmación)
    public ?int $historiaEliminarId = null;

    // Resetear paginación al buscar
    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    // Eliminar historia clínica (soft delete)
    public function eliminar(int $id): void
    {
        $historia = MedicalRecord::findOrFail($id);
        $historia->delete();
        session()->flash('mensaje', 'Historia clínica eliminada correctamente.');
    }

    public function render()
    {
        $historias = MedicalRecord::query()
            ->with(['mascota.cliente', 'veterinario'])
            ->when($this->busqueda, function ($q) {
                $q->where(function ($sub) {
                    // Búsqueda por mascota, cliente o diagnóstico
                    $sub->whereHas('mascota', fn ($m) =>
                        $m->where('name', 'like', "%{$this->busqueda}%")
                    )
                    ->orWhereHas('mascota.cliente', fn ($c) =>
                        $c->where('first_name', 'like', "%{$this->busqueda}%")
                          ->orWhere('last_name', 'like', "%{$this->busqueda}%")
                    )
                    ->orWhere('diagnostico_presuntivo', 'like', "%{$this->busqueda}%")
                    ->orWhere('reason', 'like', "%{$this->busqueda}%");
                });
            })
            ->orderByDesc('date')
            ->paginate(15);

        return view('livewire.historias-clinicas.historia-clinica-index', [
            'historias' => $historias,
        ]);
    }
}
