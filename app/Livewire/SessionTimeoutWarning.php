<?php

namespace App\Livewire;

use Livewire\Component;

class SessionTimeoutWarning extends Component
{
    public function ping()
    {
        // Esta petición automáticamente extiende la sesión en Laravel
    }

    public function render()
    {
        return view('livewire.session-timeout-warning');
    }
}
