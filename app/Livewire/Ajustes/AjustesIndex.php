<?php

namespace App\Livewire\Ajustes;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Ajustes')]
class AjustesIndex extends Component
{
    public string $tab = 'perfil';

    public function mount($tab = 'perfil')
    {
        abort_unless(auth()->user()->can('view_settings'), 403);
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.ajustes.ajustes-index');
    }
}
