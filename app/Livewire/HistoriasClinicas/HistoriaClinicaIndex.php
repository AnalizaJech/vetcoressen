<?php

namespace App\Livewire\HistoriasClinicas;

use App\Models\MedicalRecord;
use App\Models\Species;
use Flux\Flux;
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
    public string $search = '';

    #[Url]
    public ?string $filtroCliente = '';

    #[Url]
    public ?string $filtroMascota = '';

    #[Url]
    public string $especie_id = '';

    #[Url]
    public ?int $clienteSeleccionadoId = null;
    #[Url]
    public ?int $mascota_id = null;
    #[Url]
    public ?int $mascotaSeleccionadaId = null;
    public ?int $historiaEliminarId = null;

    public function mount(): void
    {
        if (request()->has('mascota_id') && request('mascota_id')) {
            $this->mascota_id = (int) request('mascota_id');
            $this->mascotaSeleccionadaId = (int) request('mascota_id');
            $pet = \App\Models\Pet::find($this->mascota_id);
            if ($pet) {
                $this->clienteSeleccionadoId = (int) $pet->customer_id;
                $this->filtroMascota = $pet->name;
            }
        } elseif (request()->has('mascotaSeleccionadaId') && request('mascotaSeleccionadaId')) {
            $this->mascotaSeleccionadaId = (int) request('mascotaSeleccionadaId');
            $this->mascota_id = (int) request('mascotaSeleccionadaId');
            $pet = \App\Models\Pet::find($this->mascotaSeleccionadaId);
            if ($pet) {
                $this->clienteSeleccionadoId = (int) $pet->customer_id;
                $this->filtroMascota = $pet->name;
            }
        } elseif (request()->has('clienteSeleccionadoId') && request('clienteSeleccionadoId')) {
            $this->clienteSeleccionadoId = (int) request('clienteSeleccionadoId');
        } elseif (request()->has('cliente_id') && request('cliente_id')) {
            $this->clienteSeleccionadoId = (int) request('cliente_id');
        } elseif (request()->has('cliente') && request('cliente')) {
            $this->clienteSeleccionadoId = (int) request('cliente');
        } elseif (request()->has('busqueda') && request('busqueda') && !$this->search) {
            $this->search = (string) request('busqueda');
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroCliente(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroMascota(): void
    {
        $this->resetPage();
    }

    public function updatedEspecieId(): void
    {
        $this->resetPage();
    }

    public function seleccionarCliente(int $id): void
    {
        $this->clienteSeleccionadoId = $id;
        $this->mascotaSeleccionadaId = null;
        $this->mascota_id = null;
    }

    public function volver(): void
    {
        $this->clienteSeleccionadoId = null;
        $this->mascotaSeleccionadaId = null;
        $this->mascota_id = null;
    }

    public function deseleccionarCliente(): void
    {
        $this->volver();
    }

    public function limpiarFiltroMascota(): void
    {
        $this->mascotaSeleccionadaId = null;
        $this->mascota_id = null;
    }

    public function abrirModalEliminar(int $id): void
    {
        $this->historiaEliminarId = $id;
        Flux::modal('confirmar-eliminar')->show();
    }

    // Eliminar historia clínica (soft delete)
    public function eliminar(int $id): void
    {
        $historia = MedicalRecord::findOrFail($id);
        $historia->delete();
        $this->historiaEliminarId = null;
        session()->flash('mensaje', 'Historia clínica eliminada correctamente.');
    }

    public function confirmarEliminar(): void
    {
        if (!$this->historiaEliminarId) {
            return;
        }

        $this->eliminar($this->historiaEliminarId);
        Flux::modal('confirmar-eliminar')->close();
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
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('numero_documento', 'like', $term)
                        ->orWhereHas('mascotas', fn ($m) => $m->where('name', 'like', $term));
                });
            })
            ->when($this->filtroCliente, function ($q) {
                $q->where('id', $this->filtroCliente);
            })
            ->when($this->filtroMascota, function ($q) {
                $q->whereHas('mascotas', fn ($m) => $m->where('name', $this->filtroMascota));
            })
            ->when($this->especie_id, function ($q) {
                $q->whereHas('mascotas', fn ($m) => $m->where('species_id', $this->especie_id));
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
            $targetMascotaId = $this->mascotaSeleccionadaId ?: $this->mascota_id;
            $clienteSeleccionado = \App\Models\Customer::with([
                'mascotas' => function ($q) use ($targetMascotaId) {
                    $q->when($targetMascotaId, fn ($sq) => $sq->where('id', $targetMascotaId))
                      ->with([
                        'historiasClinicas' => function ($q2) {
                            $q2->orderByDesc('date')->with('veterinario');
                        },
                        'especie',
                        'raza'
                    ]);
                }
            ])->find($this->clienteSeleccionadoId);
        }

        $clientesOptions = [['value' => '', 'label' => 'filter.allClients']];
        foreach (\App\Models\Customer::orderBy('first_name')->get() as $c) {
            $clientesOptions[] = ['value' => (string)$c->id, 'label' => $c->nombre_completo];
        }

        $mascotasOptions = [['value' => '', 'label' => 'filter.allPets']];
        $nombresMascotas = \App\Models\Pet::whereNotNull('name')
            ->where('name', '!=', '')
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        foreach ($nombresMascotas as $nombre) {
            $mascotasOptions[] = ['value' => $nombre, 'label' => $nombre];
        }

        $especies = Species::orderBy('name')->get();
        $especiesOptions = [['value' => '', 'label' => 'filter.allSpecies']];
        foreach ($especies as $esp) {
            $especiesOptions[] = ['value' => (string) $esp->id, 'label' => $esp->name];
        }

        return view('livewire.historias-clinicas.historia-clinica-index', compact('clientes', 'clienteSeleccionado', 'clientesOptions', 'mascotasOptions', 'especiesOptions', 'especies'));
    }
}
