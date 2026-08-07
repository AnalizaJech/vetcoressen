<?php

namespace App\Livewire\Caja;

use App\Models\Sale;
use Livewire\Component;

class Voucher extends Component
{
    public $venta;

    public function mount($id)
    {
        $this->venta = Sale::with(['cliente', 'detalles.producto'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.caja.voucher')->layout('components.layouts.guest');
    }
}
