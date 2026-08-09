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

    #[Url]
    public string $especie_id = '';

    // Resetear paginación al buscar o filtrar
    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatedEspecieId(): void
    {
        $this->resetPage();
    }

    // Eliminar historia clínica (soft delete)
    public function eliminar(int $id): void
    {
        $historia = MedicalRecord::findOrFail($id);
        $historia->delete();
        $this->historiaEliminarId = null;
        session()->flash('mensaje', 'Historia clínica eliminada correctamente.');
    }

    public function render()
    {
        // Traemos a los clientes paginados, solo si tienen mascotas, y cargamos sus mascotas con sus historias clínicas ordenadas
        $clientes = \App\Models\Customer::query()
            ->with([
                'mascotas' => function ($q) {
                    $q->with([
                        'historiasClinicas' => function ($q2) {
                            $q2->orderByDesc('date')->with('veterinario');
                        },
                        'especie',
                        'raza'
                    ]);
                }
            ])
            ->whereHas('mascotas', function ($q) {
                if ($this->especie_id) {
                    $q->where('species_id', $this->especie_id);
                }
            })
            ->when($this->busqueda, function ($q) {
                $q->where(function ($sub) {
                    // Búsqueda por cliente
                    $sub->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->busqueda}%"])
                        ->orWhere('numero_documento', 'like', "%{$this->busqueda}%")
                        // O por nombre de mascota
                        ->orWhereHas('mascotas', fn ($m) => $m->where('name', 'like', "%{$this->busqueda}%"));
                });
            })
            // Ordenar por el que tenga historias clínicas más recientes
            ->orderByDesc(
                \App\Models\MedicalRecord::select('date')
                    ->join('pets', 'medical_records.pet_id', '=', 'pets.id')
                    ->whereColumn('pets.customer_id', 'customers.id')
                    ->orderByDesc('date')
                    ->limit(1)
            )
            ->paginate(12);

        $especies = \App\Models\Species::orderBy('name')->get();

        return view('livewire.historias-clinicas.historia-clinica-index', [
            'clientes' => $clientes,
            'especies' => $especies,
        ]);
    }
}
