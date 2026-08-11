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

    #[Url]
    public string $filtroDocumento = '';

    #[Url]
    public string $filtroTelefono = '';

    public ?int $clienteSeleccionadoId = null;

    // Resetear paginación al buscar o filtrar
    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatedEspecieId(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroDocumento(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroTelefono(): void
    {
        $this->resetPage();
    }

    public function seleccionarCliente(int $id): void
    {
        $this->clienteSeleccionadoId = $id;
    }

    public function volver(): void
    {
        $this->clienteSeleccionadoId = null;
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
                    $sub->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->busqueda}%"])
                        ->orWhereHas('mascotas', fn ($m) => $m->where('name', 'like', "%{$this->busqueda}%"));
                });
            })
            ->when($this->filtroDocumento, function ($q) {
                $q->where('numero_documento', 'like', "%{$this->filtroDocumento}%");
            })
            ->when($this->filtroTelefono, function ($q) {
                $q->where('phone', 'like', "%{$this->filtroTelefono}%");
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

        $clienteSeleccionado = null;
        if ($this->clienteSeleccionadoId) {
            $clienteSeleccionado = \App\Models\Customer::with([
                'mascotas' => function ($q) {
                    $q->with([
                        'historiasClinicas' => function ($q2) {
                            $q2->orderByDesc('date')->with('veterinario');
                        },
                        'especie',
                        'raza'
                    ]);
                }
            ])->find($this->clienteSeleccionadoId);
        }

        $especies = \App\Models\Species::orderBy('name')->get();

        return view('livewire.historias-clinicas.historia-clinica-index', [
            'clientes' => $clientes,
            'especies' => $especies,
            'clienteSeleccionado' => $clienteSeleccionado
        ]);
    }
}
