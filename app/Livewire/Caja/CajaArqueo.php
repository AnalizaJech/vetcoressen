<?php

namespace App\Livewire\Caja;

use App\Models\CashRegister;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Flux\Flux;

class CajaArqueo extends Component
{
    public $activeRegister;
    public $opening_amount = 0;
    
    public $real_amount = 0;
    public $notes = '';

    public function mount()
    {
        $this->activeRegister = CashRegister::where('user_id', auth()->id())
            ->where('status', 'ABIERTA')
            ->first();
    }

    public function abrirCaja()
    {
        $this->validate([
            'opening_amount' => 'required|numeric|min:0'
        ]);

        $this->activeRegister = CashRegister::create([
            'user_id' => auth()->id(),
            'opened_at' => now(),
            'opening_amount' => $this->opening_amount,
            'status' => 'ABIERTA'
        ]);

        session()->flash('mensaje', 'Caja abierta exitosamente.');
        Flux::modal('abrir-caja')->close();
    }

    public function cerrarCaja()
    {
        $this->validate([
            'real_amount' => 'required|numeric|min:0'
        ]);

        // Calculate sales sum for this register
        $calculated = Sale::where('cash_register_id', $this->activeRegister->id)
            ->where('status', 'PAGADO')
            ->sum('total');

        $expected = $this->activeRegister->opening_amount + $calculated;
        $difference = $this->real_amount - $expected;

        $this->activeRegister->update([
            'closed_at' => now(),
            'calculated_amount' => $calculated,
            'real_amount' => $this->real_amount,
            'difference' => $difference,
            'notes' => $this->notes,
            'status' => 'CERRADA'
        ]);

        session()->flash('mensaje', 'Caja cerrada exitosamente. Desfase: S/ ' . number_format($difference, 2));
        $this->activeRegister = null;
        $this->real_amount = 0;
        $this->notes = '';
        Flux::modal('cerrar-caja')->close();
    }

    public function render()
    {
        // To show current status
        $calculated = 0;
        $expected = 0;
        $sales = collect();

        if ($this->activeRegister) {
            $calculated = Sale::where('cash_register_id', $this->activeRegister->id)
                ->where('status', 'PAGADO')
                ->sum('total');
            $expected = $this->activeRegister->opening_amount + $calculated;
            $sales = Sale::where('cash_register_id', $this->activeRegister->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $registers = CashRegister::where('user_id', auth()->id())
            ->orderBy('opened_at', 'desc')
            ->take(10)
            ->get();

        return view('livewire.caja.caja-arqueo', [
            'calculated' => $calculated,
            'expected' => $expected,
            'sales' => $sales,
            'registers' => $registers,
        ])->layout('components.layouts.app');
    }
}
