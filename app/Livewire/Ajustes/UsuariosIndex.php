<?php

namespace App\Livewire\Ajustes;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Usuarios')]
class UsuariosIndex extends Component
{
    use WithPagination;

    public string $busqueda = '';
    public ?int $usuarioEliminarId = null;
    public ?User $usuarioVer = null;

    protected $listeners = ['usuarioEliminado' => '$refresh'];

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function eliminar()
    {
        $user = User::findOrFail($this->usuarioEliminarId);
        if ($user->getKey() === (int) \Illuminate\Support\Facades\Auth::id()) {
            session()->flash('mensaje_error', 'No puedes eliminar tu propio usuario.');
            return;
        }

        $user->delete();
        session()->flash('mensaje', 'Usuario eliminado correctamente.');
        $this->usuarioEliminarId = null;
    }

    public function ver(int $id)
    {
        $this->usuarioVer = User::findOrFail($id);
    }

    public function render()
    {
        $usuarios = User::where('name', 'like', '%' . $this->busqueda . '%')
            ->orWhere('last_name', 'like', '%' . $this->busqueda . '%')
            ->orWhere('email', 'like', '%' . $this->busqueda . '%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.ajustes.usuarios-index', [
            'usuarios' => $usuarios,
        ]);
    }
}
