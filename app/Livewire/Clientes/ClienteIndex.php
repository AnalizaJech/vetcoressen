<?php

namespace App\Livewire\Clientes;

use App\Models\Customer;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

// Listado de clientes con filtros select por nombre, documento y teléfono
#[Layout('components.layouts.app')]
#[Title('Clientes')]
class ClienteIndex extends Component
{
    use WithPagination;

    #[Url]
    public ?string $filtroCliente = '';

    #[Url]
    public ?string $filtroDocumento = '';

    #[Url]
    public ?string $filtroTelefono = '';

    // ID del cliente pendiente de eliminar (modal de confirmación)
    public ?int $clienteEliminarId = null;
    public ?Customer $clienteVer = null;

    // Resetear paginación al cambiar cualquier filtro
    public function updatedFiltroCliente(): void
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

    public function clientes()
    {
        return Customer::query()
            ->when($this->filtroCliente, function ($query) {
                $query->where('id', $this->filtroCliente);
            })
            ->when($this->filtroDocumento, function ($query) {
                $query->where('numero_documento', $this->filtroDocumento);
            })
            ->when($this->filtroTelefono, function ($query) {
                $query->where('phone', $this->filtroTelefono);
            })
            ->withCount('mascotas')
            ->orderBy('id', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        // 1. Opciones de clientes (solo nombres limpios sin números)
        $clientesOptions = [['value' => '', 'label' => 'filter.allClients']];
        foreach (Customer::orderBy('first_name')->get() as $c) {
            $clientesOptions[] = [
                'value' => (string)$c->id, 
                'label' => $c->nombre_completo
            ];
        }

        // 2. Opciones de documentos de identidad / DNI
        $documentosOptions = [['value' => '', 'label' => 'filter.allDocuments']];
        $docs = Customer::whereNotNull('numero_documento')
            ->where('numero_documento', '!=', '')
            ->select('numero_documento', 'tipo_documento')
            ->distinct()
            ->orderBy('numero_documento')
            ->get();
        foreach ($docs as $d) {
            $documentosOptions[] = [
                'value' => $d->numero_documento,
                'label' => ($d->tipo_documento ? $d->tipo_documento . ': ' : '') . $d->numero_documento
            ];
        }

        // 3. Opciones de teléfonos
        $telefonosOptions = [['value' => '', 'label' => 'filter.allPhones']];
        $phones = Customer::whereNotNull('phone')
            ->where('phone', '!=', '')
            ->select('phone')
            ->distinct()
            ->orderBy('phone')
            ->pluck('phone');
        foreach ($phones as $p) {
            $telefonosOptions[] = [
                'value' => $p,
                'label' => $p
            ];
        }

        return view('livewire.clientes.cliente-index', [
            'clientes' => $this->clientes(),
            'clientesOptions' => $clientesOptions,
            'documentosOptions' => $documentosOptions,
            'telefonosOptions' => $telefonosOptions,
        ]);
    }
}
