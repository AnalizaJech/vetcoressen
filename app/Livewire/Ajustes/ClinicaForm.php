<?php

namespace App\Livewire\Ajustes;

use App\Models\Clinic;
use Livewire\Component;

class ClinicaForm extends Component
{
    public string $name = '';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public string $ruc = '';
    public string $razon_social = '';
    public string $sitio_web = '';
    public string $moneda_principal = 'PEN';

    public function mount()
    {
        $clinica = Clinic::first();
        if ($clinica) {
            $this->name = $clinica->name;
            $this->address = $clinica->address ?? '';
            $this->phone = $clinica->phone ?? '';
            $this->email = $clinica->email ?? '';
            $this->ruc = $clinica->ruc ?? '';
            $this->razon_social = $clinica->razon_social ?? '';
            $this->sitio_web = $clinica->sitio_web ?? '';
            $this->moneda_principal = $clinica->moneda_principal ?? 'PEN';
        }
    }

    public function actualizarClinica()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'ruc' => 'nullable|string|max:20',
            'razon_social' => 'nullable|string|max:255',
            'sitio_web' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'moneda_principal' => 'required|string|size:3',
        ]);

        $clinica = Clinic::first();
        if ($clinica) {
            $clinica->update([
                'name' => $this->name,
                'address' => $this->address,
                'phone' => $this->phone,
                'email' => $this->email,
                'ruc' => $this->ruc,
                'razon_social' => $this->razon_social,
                'sitio_web' => $this->sitio_web,
                'moneda_principal' => $this->moneda_principal,
            ]);
        } else {
            Clinic::create([
                'name' => $this->name,
                'address' => $this->address,
                'phone' => $this->phone,
                'email' => $this->email,
                'ruc' => $this->ruc,
                'razon_social' => $this->razon_social,
                'sitio_web' => $this->sitio_web,
                'moneda_principal' => $this->moneda_principal,
            ]);
        }

        session()->flash('clinica_mensaje', 'Datos de la clínica actualizados correctamente.');
        
        $this->dispatch('clinic-updated', [
            'name' => $this->name
        ]);
    }

    public function render()
    {
        return view('livewire.ajustes.clinica-form');
    }
}
