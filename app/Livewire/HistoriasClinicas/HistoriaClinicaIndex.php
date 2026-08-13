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
    public string $filtroCliente = '';

    #[Url]
    public string $filtroMascota = '';

    #[Url]
    public ?int $clienteSeleccionadoId = null;

    public function updatedFiltroCliente(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroMascota(): void
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
            ->when($this->filtroCliente, function ($q) {
                $q->where('id', $this->filtroCliente);
            })
            ->when($this->filtroMascota, function ($q) {
                $q->whereHas('mascotas', fn ($m) => $m->where('id', $this->filtroMascota));
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

        $clientesOptions = [['value' => '', 'label' => 'Todos los clientes']];
        foreach (\App\Models\Customer::orderBy('first_name')->get() as $c) {
            $clientesOptions[] = ['value' => (string)$c->id, 'label' => $c->nombre_completo];
        }

        $mascotasOptions = [['value' => '', 'label' => 'Todas las mascotas']];
        foreach (\App\Models\Pet::orderBy('name')->get() as $m) {
            $mascotasOptions[] = ['value' => (string)$m->id, 'label' => $m->name];
        }

        return view('livewire.historias-clinicas.historia-clinica-index', compact('clientes', 'clienteSeleccionado', 'clientesOptions', 'mascotasOptions'));
    }
}
