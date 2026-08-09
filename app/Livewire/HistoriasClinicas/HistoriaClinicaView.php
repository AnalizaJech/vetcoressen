<?php

namespace App\Livewire\HistoriasClinicas;

use App\Models\MedicalRecord;
use Livewire\Component;

class HistoriaClinicaView extends Component
{
    public $historia;

    public function mount($id)
    {
        $this->historia = MedicalRecord::with(['pet.cliente', 'veterinario'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.historias-clinicas.historia-clinica-view')->layout('components.layouts.app');
    }
}
