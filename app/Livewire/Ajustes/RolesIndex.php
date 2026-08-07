<?php

namespace App\Livewire\Ajustes;

use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Roles y Permisos')]
class RolesIndex extends Component
{
    use WithPagination;

    public string $busqueda = '';
    public ?int $roleEliminarId = null;

    protected $listeners = ['rolEliminado' => '$refresh'];

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function eliminar()
    {
        $role = Role::findOrFail($this->roleEliminarId);
        
        // No se puede eliminar super_admin por seguridad
        if ($role->name === 'super_admin') {
            session()->flash('mensaje_error', 'No puedes eliminar el rol principal de Super Admin.');
            $this->roleEliminarId = null;
            return;
        }

        $role->delete();
        session()->flash('mensaje', 'Rol eliminado correctamente.');
        $this->roleEliminarId = null;
    }

    public function render()
    {
        $roles = Role::with('permissions')
            ->where('name', 'like', '%' . $this->busqueda . '%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.ajustes.roles-index', [
            'roles' => $roles,
        ]);
    }
}
