<?php

namespace App\Livewire\Clientes;

use App\Models\Customer;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

// Listado de clientes con búsqueda en tiempo real
#[Layout('components.layouts.app')]
#[Title('Clientes')]
class ClienteIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $filtroCliente = '';

    // ID del cliente pendiente de eliminar (modal de confirmación)
    public ?int $clienteEliminarId = null;
    public ?Customer $clienteVer = null;

    // Resetear paginación al filtrar
    public function updatedFiltroCliente(): void
    {
        $this->resetPage();
    }

    public function ver(int $id): void
    {
        $this->clienteVer = Customer::with('mascotas')->findOrFail($id);
        Flux::modal('ver-cliente')->show();
    }

    // Eliminar cliente (soft delete)
    public function eliminar(): void
    {
        if (!$this->clienteEliminarId) return;
        
        $cliente = Customer::findOrFail($this->clienteEliminarId);
        $cliente->delete();
        session()->flash('mensaje', "Cliente «{$cliente->nombre_completo}» eliminado correctamente.");
        $this->clienteEliminarId = null;
    }

    public function render()
    {
        $clientes = Customer::query()
            ->when($this->filtroCliente, function ($q) {
                $q->where('id', $this->filtroCliente);
            })
            ->withCount('mascotas')
            ->orderByDesc('created_at')
            ->paginate(15);

        $clientesOptions = [['value' => '', 'label' => 'Todos los clientes']];
        foreach (Customer::orderBy('first_name')->get() as $c) {
            $clientesOptions[] = ['value' => (string)$c->id, 'label' => $c->nombre_completo . ' - ' . $c->numero_documento];
        }

        return view('livewire.clientes.cliente-index', compact('clientes', 'clientesOptions'));
    }
}
