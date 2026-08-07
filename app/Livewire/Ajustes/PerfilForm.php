<?php

namespace App\Livewire\Ajustes;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class PerfilForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function actualizarPerfil()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . (int) auth()->id(),
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        session()->flash('perfil_mensaje', 'Perfil actualizado correctamente.');
    }

    public function actualizarPassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('password_mensaje', 'Contraseña actualizada correctamente.');
    }

    public function render()
    {
        return view('livewire.ajustes.perfil-form');
    }
}
