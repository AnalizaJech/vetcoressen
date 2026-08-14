<?php

namespace App\Livewire\Clientes;

use App\Models\Customer;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

// Listado de clientes con bÃºsqueda en tiempo real
#[Layout('components.layouts.app')]
#[Title('Clientes')]
class ClienteIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $filtroCliente = '';

    // ID del cliente pendiente de eliminar (modal de confirmaciÃ³n)
    public ?int $clienteEliminarId = null;
    public ?Customer $clienteVer = null;

    // Resetear paginaciÃ³n al filtrar
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
        session()->flash('mensaje', "Cliente Â«{$cliente->nombre_completo}Â» eliminado correctamente.");
        $this->clienteEliminarId = null;
    }

    #[Computed]
    public function opcionesClientes()
    {
        return Customer::select('id', 'name')->orderBy('name')->get();
    }

    #[Computed]
    public function clientes()
    {
        return Customer::query()
            ->when($this->filtroCliente, function ($query) {
                // Now filtroCliente holds the ID instead of text search
                $query->where('id', $this->filtroCliente);
            })
            ->withCount('mascotas')
            ->orderBy('id', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        $clientesOptions = [['value' => '', 'label' => 'filter.allClients']];
        foreach (Customer::orderBy('first_name')->get() as $c) {
            $clientesOptions[] = ['value' => (string)$c->id, 'label' => $c->nombre_completo . ' - ' . $c->numero_documento];
        }
        return view('livewire.clientes.cliente-index', [
            'clientes' => $this->clientes(),
            'clientesOptions' => $clientesOptions
        ]);
    }
}
